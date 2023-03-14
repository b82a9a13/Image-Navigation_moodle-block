<?php
/**
 * @package     block_imagenav
 * @author      Robert
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */
namespace block_imagenav;

use stdClass;

class lib{
    //Used for creating and updating the setup configuration
    public function setup_nav_config($total, $width, $height, $aspectratio, $role, $files, $links){
        //section is used for creating a setting record in the imagenav_settings database
        global $DB;
        $settings = new stdClass();
        $settings->role = $role;
        $settings->width = $width;
        $settings->height = $height;
        $settings->aspectratio = 0;
        if($aspectratio === 'aspectratio'){
            $settings->aspectratio = 1;
        }
        if($DB->record_exists('imagenav_settings', [$DB->sql_compare_text('role') => $role])){
            $id = $DB->get_record_sql('SELECT id FROM {imagenav_settings} WHERE role = ?',[$role])->id;
            $settings->id = $id;
            $DB->update_record('imagenav_settings', $settings, false);
            \block_imagenav\event\updated_settings_record::create(array('contextid' => 1, 'contextlevel' => 10))->trigger();
        } else {
            $DB->insert_record('imagenav_settings', $settings);
            \block_imagenav\event\created_settings_record::create(array('contextid' => 1, 'contextlevel' => 10))->trigger();
        }
        //section is used to add image records to the imagenav_images database
        for($i = 0; $i < $total; $i++){
            $settingsid = $DB->get_record_sql('SELECT id FROM {imagenav_settings} WHERE role = ?',[$role])->id;
            $image = new stdClass();
            $image->position = $i;
            $image->url = $links[$i];
            $image->image = $files[$i];
            $image->settingid = $settingsid;
            if($DB->record_exists('imagenav_images', [$DB->sql_compare_text('position') => $i, $DB->sql_compare_text('settingid') => $settingsid])){
                $id = $DB->get_record_sql('SELECT id FROM {imagenav_images} WHERE position = ? AND settingid = ?',[$i, $settingsid])->id;
                $image->id = $id;
                $DB->update_record('imagenav_images', $image, false);
                \block_imagenav\event\updated_images_record::create(array('contextid' => 1, 'contextlevel' => 10))->trigger();
            } else {
                $DB->insert_record('imagenav_images', $image);
                \block_imagenav\event\created_images_record::create(array('contextid' => 1, 'contextlevel' => 10))->trigger();
            }
        }
    }

    //used to get the settings for a select navigation
    public function get_nav_config($string){
        global $DB;
        return $DB->get_record_sql('SELECT role, width, height, aspectratio FROM {imagenav_settings} WHERE role = ?',[$string]);
    }

    //used to get the images for a select navigation
    public function get_nav_images($string){
        global $DB;
        $setting = $DB->get_record_sql('SELECT id, aspectratio FROM {imagenav_settings} WHERE role = ?',[$string]);
        $aspectratio = '';
        if($setting->aspectratio == 1){
            $aspectratio = 'object-fit:contain;';
        }
        $records = $DB->get_records_sql('SELECT * FROM {imagenav_images} WHERE settingid = ?',[$setting->id]);
        $array = [];
        foreach($records as $record){
            array_push($array, [$record->position+1, $record->url, $record->image, $aspectratio]);
        }
        return $array;
    }

    //Used to update the configuration, images and links
    public function update_nav_config($width, $height, $aspectratio, $role, $files, $links){
        global $DB;
        $settings = new stdClass();
        $changeSetting = false;
        if($width != null){
            $settings->width = $width;
            $changeSetting = true;
        }
        if($height != null){
            $settings->height = $height;
            $changeSetting = true;
        }
        if($aspectratio != null){
            if($aspectratio == 'false'){
                $settings->aspectratio = 0;
            } elseif($aspectratio == 'true'){
                $settings->aspectratio = 1;
            }
            $changeSetting = true;
        }
        if($changeSetting === true){
            $settings->id = $DB->get_record_sql('SELECT id FROM {imagenav_settings} WHERE role = ?',[$role])->id;
            $settings->role = $role;
            $DB->update_record('imagenav_settings', $settings, false);
            \block_imagenav\event\updated_settings_record::create(array('contextid' => 1, 'contextlevel' => 10))->trigger();
        }
        $settingid = $DB->get_record_sql('SELECT id FROM {imagenav_settings} WHERE role = ?',[$role])->id;
        $fileTotal = count($files);
        if($fileTotal > 0){
            for($i = 0; $i < $fileTotal; $i++){
                $record = new stdClass();
                $files[$i][0] = $files[$i][0] - 1;
                $record->image = $files[$i][1];
                $record->position = $files[$i][0];
                if($DB->record_exists('imagenav_images', [$DB->sql_compare_text('settingid') => $settingid, $DB->sql_compare_text('position') => $files[$i][0]])){
                    $record->id = $DB->get_record_sql('SELECT id FROM {imagenav_images} WHERE settingid = ? AND position = ?',[$settingid, $files[$i][0]])->id;
                    $DB->update_record('imagenav_images', $record, false);
                    \block_imagenav\event\updated_images_record::create(array('contextid' => 1, 'contextlevel' => 10))->trigger();
                } else {
                    $record->settingid = $settingid;
                    $DB->insert_record('imagenav_images', $record);
                    \block_imagenav\event\created_images_record::create(array('contextid' => 1, 'contextlevel' => 10))->trigger();
                }
            }
        }
        $linkTotal = count($links);
        if($linkTotal > 0){
            for($i = 0; $i < $linkTotal; $i++){
                $record = new stdClass();
                $links[$i][0] = $links[$i][0] - 1;
                $record->position = $links[$i][0];
                $record->url = $links[$i][1];
                if($DB->record_exists('imagenav_images', [$DB->sql_compare_text('settingid') => $settingid, $DB->sql_compare_text('position') => $links[$i][0]])){
                    $record->id = $DB->get_record_sql('SELECT id FROM {imagenav_images} WHERE settingid = ? AND position = ?',[$settingid, $links[$i][0]])->id;
                    $DB->update_record('imagenav_images', $record, false);
                    \block_imagenav\event\updated_images_record::create(array('contextid' => 1, 'contextlevel' => 10))->trigger();
                } else {
                    $record->settingid = $settingid;
                    $DB->insert_record('imagenav_images', $record);
                    \block_imagenav\event\created_images_record::create(array('contextid' => 1, 'contextlevel' => 10))->trigger();
                }
            }
        }
    }

    //Get current users highest role
    public function role(){
        global $DB;
        global $USER;
        $user = $USER->id;
        $assignments = $DB->get_records_sql('SELECT * FROM {role_assignments} WHERE userid = ?',[$user]);
        $role = [false, false, false];
        foreach($assignments as $assignment){
            if($assignment->roleid == 1){
                $role[0] = true;
            } elseif($assignment->roleid == 4 || $assignment->roleid == 3){
                $role[1] = true;
            } elseif($assignment->roleid == 5){
                $role[2] = true;
            }
        }
        if($role[0] === true){
            return 'admin';
        } elseif($role[1] === true){
            return 'coach';
        } elseif($role[2] === true){
            return 'learner';
        }
    }

    //Get a course where the user has the provided role - WORK IN PROGRESS
    public function course_enrolled($type){
        global $USER;
        $userid = $USER->id;
        global $DB;
        $roleid = [];
        if($type == 'coach'){
            $roleid = [3, 4];
        } elseif($type == 'learner'){
            $roleid = [5];
        }
        $userEnrolments = $DB->get_records_sql('SELECT enrolid, status FROM {user_enrolments} WHERE userid = ? AND status = ?',[$userid, 0]);
        $enrolTable = $DB->get_records('enrol');
        $courseids = [];
        foreach($userEnrolments as $userEnrol){
            foreach($enrolTable as $enrolTab){
                if($enrolTab->id == $userEnrol->enrolid){
                    array_push($courseids, [$enrolTab->courseid]);
                }
            }
        }
        $temp = [];
        $contexts = $DB->get_records('context');
        $roleAssignments = $DB->get_records_sql('SELECT * FROM {role_assignments} WHERE userid = ?',[$userid]);
        foreach($contexts as $context){
            foreach($roleAssignments as $roleAssign){
                if($roleAssign->contextid == $context->id && ($roleAssign->roleid = $roleid[0] || ($roleAssign->roleid = $roleid[1] && count($roleid) == 2))){
                    array_push($temp, [$context->instanceid]);
                }
            }
        }
        $temp2 = [];
        foreach($temp as $tem){
            foreach($courseids as $courseid){
                array_push($temp2, $courseid);
            }
        }
        return $temp2[0][0];
    }

    //Create content for navigation based on role
    public function create_content($role){
        $data = $this->get_nav_images($role);
        $settings = $this->get_nav_config($role);
        $text = '<div class="text-center">';
        $dataLength = count($data);
        for($i = 0; $i < $dataLength; $i++){
            $text .= '<img src="'.$data[$i][2].'" style="cursor:pointer;width:'.$settings->width.'px;height:'.$settings->height.'px;'.$data[$i][3].'" onclick="window.location.href=`'.$data[$i][1].'`">';
        }
        $text .= '</div>';
        return $text;
    }
}
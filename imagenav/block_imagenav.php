<?php
use block_imagenav\lib;
class block_imagenav extends block_base{
    public function init(){
        $context = context_system::instance();
        $lib = new lib();
        $role = $lib->role();
        if($role == 'admin' || has_capability('block/imagenav:admin', $context)){
            $this->title = 'Admin Navigation';
        } elseif($role == 'coach'){
            $courseid = $lib->course_enrolled($role);
            $context = context_course::instance($courseid);
            require_capability('block/imagenav:coach', $context);
            $this->title = 'Coach Navigation';
        } elseif($role == 'learner'){
            $courseid = $lib->course_enrolled($role);
            $context = context_course::instance($courseid);
            require_capability('block/imagenav:learner',  $context);
            $this->title = 'Learner Navigation';
        }else {
            $this->title = 'error Navigation';
        }
    }
    public function get_content(){
        $this->content = new stdClass();
        $context = context_system::instance();
        $lib = new lib();
        $role = $lib->role();
        if($role == 'admin' || has_capability('block/imagenav:admin', $context)){
            $context = context_system::instance();
            require_capability('block/imagenav:admin', $context);
            $this->content->text = $lib->create_content('admin');
        } elseif($role == 'coach'){
            $courseid = $lib->course_enrolled($role);
            $context = context_course::instance($courseid);
            require_capability('block/imagenav:coach', $context);
            $this->content->text = $lib->create_content($role);
        } elseif($role == 'learner'){
            $courseid = $lib->course_enrolled($role);
            $context = context_course::instance($courseid);
            require_capability('block/imagenav:learner',  $context);
            $this->content->text = $lib->create_content($role);
        } else {
            $this->content->text = 'error';
        }
    }
}
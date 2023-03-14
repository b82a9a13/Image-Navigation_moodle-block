<?php
// This file is part of Image Nav Block Plugin
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     block_imagenav
 * @author      Robert
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
 */

namespace block_imagenav\event;

use core\event\base;

defined('MOODLE_INTERNAL') || die();

class updated_images_record extends base {
    protected function init(){
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'imagenav_images';
    }
    public static function get_name(){
        return 'Updated a record in imagenav_images database';
    }
    public function get_description(){
        return "The user with id '".$this->userid."' updated a record in the database imagenav_images.";
    }
    public function get_url(){
        return new \moodle_url('/blocks/imagenav/configuration.php');
    }
    public function get_id(){
        return $this->objectid;
    }
}
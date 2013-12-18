<?php
/* Copyright Panopto 2009 - 2013 / With contributions from Spenser Jones (sjones@ambrose.edu) and Jay Briers (jay@jrbriers.co.uk)
 * 
 * This file is part of the Panopto Activity Module for Moodle adapted from the Panopto Block for Moodle.
 * 
 * The Panopto Activity Module for Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The Panopto plugin for Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with the Panopto plugin for Moodle.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *
 * @package    mod
 * @subpackage panopto
 * @copyright  2013 onwards Jay Briers, Lancaster University {@link http://jrbriers.co.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once("config.php");

require_once("mod_panopto_lib.php");
require_once("mod_panopto_soap_client.php");

class mod_panopto_data {
    var $instancename;

    var $moodle_course_id;

    var $servername;
    var $applicationkey;

    var $soap_client;

    var $sessiongroup_id;

    var $url_prefix;

    var $username;

    var $email;
    var $firstName;
    var $lastName;

    function __construct($userdetails) {
        // Fetch global settings from DB
        $this->instancename = INSTANCE_NAME;
        $this->servername = SERVER_NAME;
        $this->username = $userdetails->userName;
        $this->email = $userdetails->email;
        $this->firstName = $userdetails->firstName;
        $this->lastName = $userdetails->lastName;
        //$this->applicationkey = $config->applicationkey;
        $this->url_prefix = $this->servername."/Panopto/Pages/Viewer/Default.aspx?id=";

        // Compute web service credentials for current user.
        $apiuser_userkey = mod_panopto_decorate_username($this->username);
        $apiuser_authcode = mod_panopto_generate_auth_code($apiuser_userkey . "@" . $this->servername);

        // Instantiate our SOAP client.
        $this->soap_client = new mod_panopto_soap_client($this->servername, $apiuser_userkey, $apiuser_authcode);
        
    }

    // returns SystemInfo
    function get_system_info() {
        return $this->soap_client->GetSystemInfo();
    }

    function panopto_provision_course($courseid){
        Global $CFG;

        $panopto_data = new mod_panopto_data(null);
        $panopto_data->moodle_course_id = $courseid;
        $provisioning_data = $panopto_data->get_provisioning_info();
        $provisioned_data  = $panopto_data->provision_course($provisioning_data);
    }

    // Create the Panopto course and populate its ACLs.
    function provision_course($provisioning_info) {
        $course_info = $this->soap_client->ProvisionCourse($provisioning_info);

        //var_dump($course_info);
        /*if(!empty($course_info) && !empty($course_info->PublicID)) {
            mod_panopto_data::set_panopto_course_id($this->moodle_course_id, $course_info->PublicID);
        }*/

        return $course_info;
    }

    // Fetch course name and membership info from DB in preparation for provisioning operation.
    function get_provisioning_info() {
        global $DB;
        $provisioning_info = new stdClass();
        $provisioning_info->ShortName = "Private Folder";
        $provisioning_info->LongName = $this->username;
        $provisioning_info->ExternalCourseID = $this->instancename . ":" . "this->moodle_course_id";
         
        // moodle/course:update capability will include admins along with teachers, course creators, etc.
        // Could also use moodle/legacy:teacher, moodle/legacy:editingteacher, etc. if those turn out to be more appropriate.
        $provisioning_info->Instructors = array();
        $instructor_info = new stdClass;
        $instructor_info->UserKey = $this->panopto_decorate_username($this->username);
        $instructor_info->FirstName = $this->firstName;
        $instructor_info->LastName = $this->lastName;
        $instructor_info->Email = $this->email;
        $instructor_info->MailLectureNotifications = true;

        array_push($provisioning_info->Instructors, $instructor_info);


        


        return $provisioning_info;
    }

    // Get courses visible to the current user.
    function get_courses() {
        $courses_result = $this->soap_client->GetCourses();
        $courses = array();
        if(!empty($courses_result->CourseInfo)) {
            $courses = $courses_result->CourseInfo;
            // Single-element return set comes back as scalar, not array (?)
            if(!is_array($courses)) {
                $courses = array($courses);
            }
        }
        	
        return $courses;
    }

    // Get info about the currently mapped course.
    function get_course() {
        return $this->soap_client->GetCourse($this->sessiongroup_id);
    }

    // Get ongoing Panopto sessions for the currently mapped course.
    function get_live_sessions() {
        $live_sessions_result = $this->soap_client->GetLiveSessions($this->sessiongroup_id);

        $live_sessions = array();
        if(!empty($live_sessions_result->SessionInfo)) {
            $live_sessions = $live_sessions_result->SessionInfo;
            // Single-element return set comes back as scalar, not array (?)
            if(!is_array($live_sessions)) {
                $live_sessions = array($live_sessions);
            }
        }

        return $live_sessions;
    }

    // Get recordings available to view for the currently mapped course.
    function get_completed_deliveries($folderID = '-1') {
        if($folderID=='-1')$folderID = $this->sessiongroup_id;
        //echo "folder id = '".$folderID."'";
        $completed_deliveries_result = $this->soap_client->GetCompletedDeliveries($folderID);

        $completed_deliveries = array();
        if(!empty($completed_deliveries_result->DeliveryInfo)) {
            $completed_deliveries = $completed_deliveries_result->DeliveryInfo;
            // Single-element return set comes back as scalar, not array (?)
            if(!is_array($completed_deliveries)) {
                $completed_deliveries = array($completed_deliveries);
            }
        }

        return $completed_deliveries;
    }

    // Instance method caches Moodle instance name from DB (vs. block_panopto_lib version).
    function panopto_decorate_username($moodle_username) {
        return ($this->instancename . "\\" . $moodle_username);
    }

    // We need to retrieve the current course mapping in the constructor, so this must be static.
    static function get_panopto_course_id($moodle_course_id) {
        global $DB;
        return $DB->get_field('panopto_foldermap', 'panopto_id', array('moodleid' => $moodle_course_id));
    }

    // Called by Moodle block instance config save method, so must be static.
    static function set_panopto_course_id($moodle_course_id, $sessiongroup_id) {
        global $DB;
        if($DB->get_records('panopto_foldermap', array('moodleid' => $moodle_course_id))) {
            return $DB->set_field('panopto_foldermap', 'panopto_id', $sessiongroup_id, array('moodleid' => $moodle_course_id));
        } else {
            $row = (object) array('moodleid' => $moodle_course_id, 'panopto_id' => $sessiongroup_id);
            return $DB->insert_record('panopto_foldermap', $row);
        }
    }

    function get_course_options() {
        $courses_by_access_level = array("Creator" => array(), "Viewer" => array(), "Public" => array());

        $panopto_courses = $this->get_courses();
        if(!empty($panopto_courses)) {
            foreach($panopto_courses as $course_info) {
                array_push($courses_by_access_level[$course_info->Access], $course_info);
            }

            $options = array();
            foreach(array_keys($courses_by_access_level) as $access_level) {
                $courses = $courses_by_access_level[$access_level];
                $group = array();
                foreach($courses as $course_info) {
                    $display_name = ($course_info->DisplayName);
                    $group[$course_info->PublicID] = $display_name;
                }
                $options[$access_level] = $group;
            }
        }
        else if(isset($panopto_courses)) {
            $options = array('Error' => array('-- No Courses Available --'));
        } else {
            $options = array('Error' => array('!! Unable to retrieve course list !!'));
        }

        return array('courses' => $options, 'selected' => $this->sessiongroup_id);
    }

    
}
/* End of file panopto_data.php */
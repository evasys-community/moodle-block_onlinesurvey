<?php
/*
EvaSys Online Surveys - Moodle Block
Copyright (C) 2018 Soon Systems GmbH on behalf of Electric Paper Evaluationssysteme GmbH

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

Contact:
Soon-Systems GmbH
Syrlinstr. 5
89073 Ulm
Deutschland

E-Mail: info@soon-systems.de
*/

require_once('../../config.php');

require_once('lib.php');


// Block settings.
$config = get_config("block_onlinesurvey");
$error = '';
$debugmode = false;

if (isset($config)) {
    
    $connectiontype = $config->connectiontype;
    $debugmode = $config->survey_debug;
    
    // Session information
    global $USER;
    if ($moodleuserid = $USER->id) {
        $moodleusername = $USER->username;
        $moodleemail = $USER->email;
        $context = null;
        $course = null;
        
        $contextid = optional_param('ctxid', 2, PARAM_INT);
        if(!empty($contextid)){
            $context = context::instance_by_id($contextid);
        }
        
        // lti_regard_coursecontext is not yet supported by EvaSys LTI provider
        if(isset($config->lti_regard_coursecontext) && !empty($config->lti_regard_coursecontext)){
            $courseid = optional_param('cid', 1, PARAM_INT);
            if(!empty($courseid)){
                $course = get_course($courseid);
            }
        }
        
        if(!empty($course)){
            $course = get_course(1);
        }

        if($connectiontype == 'SOAP'){
            $url = $CFG->wwwroot.'/blocks/onlinesurvey/style/iframe_soap.css';
            
            echo '<head>';
            echo '<link rel="stylesheet" href="' . $url . '">';
            echo '</head>';
            
            get_soap_content($config, $moodleusername, $moodleemail);
        }
        else if($connectiontype == 'LTI'){
            get_lti_content($config, $context, $course);
        }
    } else {
        $error = get_string('userid_not_found', 'block_onlinesurvey').'<br>';
    }
} else {
    $error = get_string('config_not_accessible', 'block_onlinesurvey');
}

if(!empty($error)){
    $context = context_system::instance();
    if (has_capability('moodle/site:config', $context)) {
        if ($error) {
            echo  get_string('error_occured', 'block_onlinesurvey', $error);
        }
    } else if ($debugmode) {
        echo  get_string('error_occured', 'block_onlinesurvey', $error);
    }
}

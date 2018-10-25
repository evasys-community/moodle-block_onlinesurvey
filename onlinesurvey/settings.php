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

defined('MOODLE_INTERNAL') || die;

require_once $CFG->dirroot . '/blocks/onlinesurvey/lib.php';

if ($ADMIN->fulltree) {
    
    /* Block title */
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurvey/blocktitle',
                    get_string('blocktitle', 'block_onlinesurvey'),
                    get_string('blocktitle_description', 'block_onlinesurvey'),
                    get_string('pluginname', 'block_onlinesurvey')
            )
    );
    
    $communicationoptions = array();
    $communicationoptions["SOAP"] = get_string('soap', 'block_onlinesurvey');
    $communicationoptions["LTI"] = get_string('lti', 'block_onlinesurvey');
    $settings->add(
            new admin_setting_configselect('block_onlinesurvey/connectiontype', get_string('communication_interface', 'block_onlinesurvey'),
                    get_string('communication_interface_description', 'block_onlinesurvey'),
                    "LTI", $communicationoptions
            )
    );
    unset($communicationoptions);
    
    $userdataoptions = array();
    $userdataoptions["email"] = get_string('email');
    $userdataoptions["username"] = get_string('username');
    $settings->add(
            new admin_setting_configselect('block_onlinesurvey/useridentifier', get_string('useridentifier', 'block_onlinesurvey'),
                    get_string('useridentifier_description', 'block_onlinesurvey'),
                    "email", $userdataoptions
            )
    );
    unset($userdataoptions);
    
    $customfieldidoptions = array();
    $customfieldnr = get_string('customfieldnumber', 'block_onlinesurvey');
    $customfieldidoptions[1] = $customfieldnr." 1";
    $customfieldidoptions[2] = $customfieldnr." 2";
    $customfieldidoptions[3] = $customfieldnr." 3";
    $settings->add(
            new admin_setting_configselect('block_onlinesurvey/customfieldnumber', get_string('customfieldnumberinevasys', 'block_onlinesurvey'),
                    get_string('customfieldnumberinevasys_description', 'block_onlinesurvey'),
                    "1", $customfieldidoptions
            )
    );
    unset($customfieldidoptions);
    
    
    // #8984
    $presentationoptions = array();
    $presentationoptions["brief"] = get_string('presentation_brief', 'block_onlinesurvey');
    $presentationoptions["detailed"] = get_string('presentation_detailed', 'block_onlinesurvey');
    $settings->add(new admin_setting_configselect('block_onlinesurvey/presentation', get_string('presentation', 'block_onlinesurvey'),
            get_string('presentation_description', 'block_onlinesurvey'),
            "brief", $presentationoptions));
    unset($presentationoptions);
    // END #8984
    
    // #8977
    $settings->add(
            new admin_setting_configcheckbox(
                    'block_onlinesurvey/survey_hide_empty',
                    get_string('survey_hide_empty', 'block_onlinesurvey'),
                    get_string('survey_hide_empty_description', 'block_onlinesurvey'),
                    0
            )
    );
    // END #8977
    
    $settings->add(
            new admin_setting_configcheckbox(
                    'block_onlinesurvey/survey_show_popupinfo',
                    get_string('survey_show_popupinfo', 'block_onlinesurvey'),
                    get_string('survey_show_popupinfo_description', 'block_onlinesurvey'),
                    0
            )
    );
    
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurvey/survey_timeout',
                    get_string('survey_timeout', 'block_onlinesurvey'),
                    get_string('survey_timeout_description', 'block_onlinesurvey'),
                    0,
                    PARAM_INT
            )
    );
    
    $settings->add(
            new admin_setting_configcheckbox(
                    'block_onlinesurvey/survey_debug',
                    get_string('survey_debug', 'block_onlinesurvey'),
                    get_string('survey_debug_description', 'block_onlinesurvey'),
                    0
            )
    );
    
    // Addition CSS for iframe content
    $settings->add(
            new admin_setting_configtextarea(
                    'block_onlinesurvey/additionalcss',
                    get_string('additionalcss', 'block_onlinesurvey'),
                    get_string('additionalcss_description', 'block_onlinesurvey'),
                    '',
                    PARAM_RAW,
                    50,
                    6
            )
    );
    
    // Add SOAP heading.
    $settings->add(
            new admin_setting_heading('block_onlinesurvey/generalheadingsoap',
                    get_string('generalheadingsoap', 'block_onlinesurvey'),
                    get_string('soap_general_information', 'block_onlinesurvey')
            )
    );
    
    /* SOAP settings */
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurvey/survey_server',
                    get_string('survey_server', 'block_onlinesurvey'),
                    get_string('survey_server_description', 'block_onlinesurvey'),
                    '',
                    PARAM_RAW,
                    80
            )
    );
    
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurvey/survey_login',
                    get_string('survey_login', 'block_onlinesurvey'),
                    get_string('survey_login_description', 'block_onlinesurvey'),
                    '',
                    PARAM_RAW,
                    80
            )
    );
    
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurvey/survey_user',
                    get_string('survey_user', 'block_onlinesurvey'),
                    get_string('survey_user_description', 'block_onlinesurvey'),
                    '',
                    PARAM_RAW
            )
    );
    
    $settings->add(
            new admin_setting_configpasswordunmask(
                    'block_onlinesurvey/survey_pwd',
                    get_string('survey_pwd', 'block_onlinesurvey'),
                    get_string('survey_pwd_description', 'block_onlinesurvey'),
                    ''
            )
    );
    
    // #8983
    $settings->add(
            new admin_setting_configcheckbox(
                    'block_onlinesurvey/soap_request_eachtime',
                    get_string('soap_request_eachtime', 'block_onlinesurvey'),
                    get_string('soap_request_eachtime_description', 'block_onlinesurvey'),
                    0
            )
    );
    
    // Add LTI heading.
    $settings->add(
            new admin_setting_heading('block_onlinesurvey/generalheadinglti',
                    get_string('generalheadinglti', 'block_onlinesurvey'),
                    get_string('lti_general_information', 'block_onlinesurvey')
            )
    );
    
    /* LTI settings */
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurvey/lti_url',
                    get_string('survey_lti_url', 'block_onlinesurvey'),
                    get_string('survey_lti_url_description', 'block_onlinesurvey'),
                    '',
                    PARAM_RAW,
                    80
            )
    );
    // Hide "consumer key" for LTI in configurationen -> currently not evaluated in EvaSys
//     $settings->add(
//             new admin_setting_configtext(
//                             'block_onlinesurvey/lti_resourcekey',
//                             get_string('survey_lti_resourcekey', 'block_onlinesurvey'),
//                             get_string('survey_lti_resourcekey_description', 'block_onlinesurvey'),
//                             ''
//             )
//     );
    
    $settings->add(
            new admin_setting_configpasswordunmask(
                    'block_onlinesurvey/lti_password',
                    get_string('survey_lti_password', 'block_onlinesurvey'),
                    get_string('survey_lti_password_description', 'block_onlinesurvey'),
                    ''
            )
    );
    
    // lti custom parameters
    $settings->add(
            new admin_setting_configtextarea(
                    'block_onlinesurvey/lti_customparameters',
                    get_string('lti_customparameters', 'block_onlinesurvey'),
                    get_string('lti_customparameters_description', 'block_onlinesurvey'),
                    '',
                    PARAM_RAW,
                    50,
                    6
            )
    );
    
    // lti role mapping Instructor
    $choices = array();
    // Get some basic data we are going to need.
    $roles = get_all_roles();
    $systemcontext = context_system::instance();
    $rolenames = role_fix_names($roles, $systemcontext, ROLENAME_ORIGINAL);
    if(!empty($rolenames)){
        foreach ($rolenames as $key => $role) {
            if(!array_key_exists($role->id, $choices)){
                $choices[$role->id] = $role->localname;
            }
        }
    }
    $settings->add(
            new admin_setting_configmultiselect(
                    'block_onlinesurvey/lti_instructormapping',
                    get_string('lti_instructormapping', 'block_onlinesurvey'),
                    get_string('lti_instructormapping_description', 'block_onlinesurvey'),
                    array(3,4),
                    $choices
            )
    );
    // lti role mapping Learner
    $settings->add(
            new admin_setting_configmultiselect(
                    'block_onlinesurvey/lti_learnermapping',
                    get_string('lti_learnermapping', 'block_onlinesurvey'),
                    get_string('lti_learnermapping_description', 'block_onlinesurvey'),
                    array(5),
                    $choices
            )
    );
    unset($roles);
    unset($rolenames);
    unset($choices);
    
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurvey/lti_regex_learner',
                    get_string('lti_regex_learner', 'block_onlinesurvey'),
                    get_string('lti_regex_learner_description', 'block_onlinesurvey'),
                    BLOCK_ONLINESURVEY_LTI_REGEX_LEARNER_DEFAULT,
                    PARAM_RAW,
                    80
            )
    );
    
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurvey/lti_regex_instructor',
                    get_string('lti_regex_instructor', 'block_onlinesurvey'),
                    get_string('lti_regex_instructor_description', 'block_onlinesurvey'),
                    BLOCK_ONLINESURVEY_LTI_REGEX_INSTRUCTOR_DEFAULT,
                    PARAM_RAW,
                    80
            )
    );
    /* END LTI settings */
}

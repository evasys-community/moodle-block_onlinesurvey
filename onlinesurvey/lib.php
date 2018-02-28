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


define('COMM_SOAP', "SOAP");
define('COMM_LTI', "LTI");

define('LTI_REGEX_LEARNER_DEFAULT', '/<(div){1}[\s]{1,}(class=){1}["|\']{1}[a-z]{0,}[\s]{0,}(participate){1}[\s]{0,}[a-z]{0,}[\s]{0,}["|\']{1}>/');


/**
 * Request surveys for the current user according to email or username and displays the result.
 * @param string $config block settings of "block_onlinesurvey"
 * @param string $moodleusername username for SOAP request
 * @param string $moodleemail email for SOAP request
 */
function get_soap_content($config = null, $moodleusername = '', $moodleemail = ''){
    global $SESSION;
    
    $survey_url = 'indexstud.php?type=html&user_tan=';
    
    if(empty($config)){
        $config = get_config("block_onlinesurvey");
    }
    
    $connectiontype = $config->connectiontype;
    $survey_url = $config->survey_login.$survey_url;
    $wsdl = $config->survey_server;
    $soapuser = $config->survey_user;
    $soappassword = $config->survey_pwd;
    $debugmode = $config->survey_debug;
    
    $timeout = $config->survey_timeout;
    if (!$timeout) {
        $timeout = 3;
    }
    
    // Parse wsdlnamespace from the wsdl url.
    preg_match('/\/([^\/]+\.wsdl)$/', $wsdl, $matches);
    
    $soap_content_str = '';
    
    if (count($matches) == 2) {
        $wsdlnamespace = $matches[1];
        
        $soap_config_obj = new stdClass();
        $soap_config_obj->connectiontype = $connectiontype;
        $soap_config_obj->wsdl = $wsdl;
        $soap_config_obj->timeout = $timeout;
        $soap_config_obj->debugmode = $debugmode;
        $soap_config_obj->soapuser = $soapuser;
        $soap_config_obj->soappassword = $soappassword;
        $soap_config_obj->wsdlnamespace = $wsdlnamespace;
        $soap_config_obj->useridentifier = $config->useridentifier;
        $soap_config_obj->moodleemail = $moodleemail;
        $soap_config_obj->moodleusername = $moodleusername;
        $soap_config_obj->customfieldnumber = $config->customfieldnumber;
        $soap_config_obj->coursecode = '';
        
        $result = null;
        
        // Get surveys if no surveys in SESSION or debug mode for the block is enabled
        if (!isset($SESSION->block_onlinesurvey_surveykeys) || $debugmode) {
            $result = get_surveys($soap_config_obj);
            $SESSION->block_onlinesurvey_surveykeys = $result->surveys;
        }
        
        if (is_object($SESSION->block_onlinesurvey_surveykeys)) {
            if (!is_array($SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys)) {
                $SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys = array(
                    $SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys
                );
            }
        
            $count = count($SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys);
            $surveys_found = false;
            foreach ($SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys as $surveykey) {
                if(!empty($surveykey->TransactionNumber) && ($surveykey->TransactionNumber != null && $surveykey->TransactionNumber !== 'null')){
                    $surveys_found = true;
                    break;
                }
            }
            
            // Surveys found
            if ($count && $surveys_found) {
                $soap_content_str .= '<div class="survey_list table">';
                
                $cnt = 0;
                foreach ($SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys as $surveykey) {
                    if($surveykey->TransactionNumber !== 'null'){
                        $cnt++;
                        
                        $soap_content_str .=  '<div class="row">';
                        $soap_content_str .=     '<div class="cell survey">';
                        $soap_content_str .=         "<a id=\"surveylink_button_".$cnt."\" ".
                                                        "href=\"$survey_url" . "{$surveykey->TransactionNumber}\" ".
                                                        "target=\"_blank\">$surveykey->CourseName</a>";
                         $soap_content_str .=    '</div>';
                        $soap_content_str .=  '</div>';
                        
                    }
                }
                $soap_content_str .= '</div>';
                
                if(!empty($config->survey_show_popupinfo)){
                    $soap_content_str .=  '<script language="JavaScript">if (typeof window.parent.evasys_generate_popupinfo == "function"){ window.parent.evasys_generate_popupinfo(); }</script>';
                }
            }
            else{
                $soap_content_str =  '<div class="block_onlinesurvey_info">'.get_string('no_surveys', 'block_onlinesurvey').'</div>';
            }
        }
        else if(empty($SESSION->block_onlinesurvey_surveykeys)){
            $soap_content_str = '<div class="block_onlinesurvey_info">'.get_string('no_surveys', 'block_onlinesurvey').'</div>';
        }
        
        if ($debugmode && isset($result->error) && !empty($result->error)) {
            $soap_content_str = get_string('error_occured', 'block_onlinesurvey', $result->error) ."<br>" . $soap_content_str;
        }
        
        if ($debugmode && isset($result->warning) && !empty($result->warning)) {
            $soap_content_str = get_string('warning_message', 'block_onlinesurvey', $result->warning) ."<br>" . $soap_content_str;
        }
    } else {
        if($debugmode)$soap_content_str = get_string('wsdl_namespace', 'block_onlinesurvey');
    }
    
    echo $soap_content_str;
}

/**
 * Perform SOAP request for surveys of a user according to user email or username.
 * 
 * @param unknown $soap_config_obj Object containing data for SOAP request.
 * @return unknown|stdClass Object containing surveys if present and errors or warnings of the onlinesurvey_soap_client
 */
function get_surveys($soap_config_obj) {
    $retval = new stdClass();
    $retval->error = null;
    $retval->warning = null;
    $retval->surveys = false;
    try {
        // Check connectiontype for SOAP
        if($soap_config_obj->connectiontype == 'SOAP'){
            require_once('onlinesurvey_soap_client.php');
            $client = new onlinesurvey_soap_client( $soap_config_obj->wsdl,
                    array(
                                    'trace' => 1,
                                    'feature' => SOAP_SINGLE_ELEMENT_ARRAYS,
                                    'connection_timeout' => $soap_config_obj->timeout),
                    $soap_config_obj->timeout,
                    $soap_config_obj->debugmode
            );
            
            $header = array(
                            'Login' => $soap_config_obj->soapuser,
                            'Password' => $soap_config_obj->soappassword
            );
            
            if (is_object($client)) {
                if ($client->haswarning) {
                    $retval->warning = $client->warnmessage;
                }
                
                $soapheader = new SoapHeader($soap_config_obj->wsdlnamespace, 'Header', $header);
                $client->__setSoapHeaders($soapheader);
            } else {
                $retval->error = handle_error("SOAP client configuration error");
                return $result;
            }
            
            if(!empty($soap_config_obj->useridentifier)){
                if($soap_config_obj->useridentifier == 'email'){
                    if($soap_config_obj->moodleemail){
                        $retval->surveys = $client->GetPswdsByParticipant($soap_config_obj->moodleemail);
                    }
                }
                else if($soap_config_obj->useridentifier == 'username'){
                    $retval->surveys = $client->GetPswdsByParticipant($soap_config_obj->moodleusername, $soap_config_obj->coursecode, $soap_config_obj->customfieldnumber);
                }
            }
        }
    } catch (Exception $e) {
        $retval->error = handle_error($e);
        return $retval;
    }
    return $retval;
}

/**
 * Helper function that returns an error string
 * @param unknown $err
 * @return Ambigous <string, unknown>
 */
function handle_error($err) {
    $error = '';
    if (is_array($err)) {
        // Configuration validation error.
        if (!$err[0]) {
            $error = $err[1];
        }
    } else if (is_string($err)) {
        // Simple error message.
        $error = $err;
    } else {
        // Error should be an exception.
        $error = print_exceptions($err);
    }
    return $error;
}

/**
 * Helper function for exceptions
 * @param unknown $e should be an exception
 * @return string formatted error message of the excetion
 */
function print_exceptions($e) {
    $msg = '';
    if (get_class($e) == "SoapFault") {
        $detail = '';
//         if(isset($e->detail) && !empty($e->detail)){
//             $detail = $e->detail;
//             if(is_object($detail) && isset($detail->tSoapfault)){
//                 $detail = $detail->tSoapfault;
//                 if(isset($detail->sDetails)){
//                     $detail = $detail->sDetails;
//                 }
//             }
//         }
        
        $msg = "{$e->faultstring}";
    } else {
        $msg = $e->getMessage();
    }
    return $msg;
}

/**
 * Request surveys via LTI for the current user according to email or username and displays the result.
 * This functions uses functions of '/mod/lti/locallib.php'.
 * Performs a second request via curl to check the result for learner content in order to include code to display popupinfo dialog - if option is selected in the settings. 
 * @param string $config block settings of "block_onlinesurvey"
 * @param string $context context for LTI request - not yet supported by LTI provider
 * @param string $course course for LTI request - not yet supported by LTI provider
 */
function get_lti_content($config = null, $context = null, $course = null){
    global $CFG, $SESSION;
    
    require_once $CFG->dirroot.'/mod/lti/locallib.php';
    
    $courseid = (!empty($course->id)) ? $course->id : 1;
    
    list($endpoint, $parameter) = get_launch_data($config, $context, $course);
    
    $debuglaunch = $config->survey_debug;
    $content = lti_post_launch_html($parameter, $endpoint, $debuglaunch);

    // Check for learner content in LTI result
    if(!isset($SESSION->block_onlinesurvey_curl_checked) && !empty($config->survey_show_popupinfo)){
        
        $content2 = lti_post_launch_html_curl($parameter, $endpoint, $debuglaunch);
        
        // Search in $content2 for e.g.: <div class="cell participate centered">
        // If match found and survey_show_popupinfo is set, add code to generate popup 
        if(!empty($content2)){
            if(isset($config->lti_regex_learner) && !empty($config->lti_regex_learner)){
                $re = $config->lti_regex_learner;
            }
            // No regex in config -> use default regex
            else{
                $re = LTI_REGEX_LEARNER_DEFAULT;
            }
            
            if(!empty($re)){
                preg_match_all($re, $content2, $matches, PREG_SET_ORDER, 0);
                
                $SESSION->block_onlinesurvey_curl_checked = true;
                
                if(!empty($matches)){
                    echo '<script language="JavaScript">if (typeof window.parent.evasys_generate_popupinfo == "function"){ window.parent.evasys_generate_popupinfo(); }</script>';
                }
            }
        }
    }
    
    echo $content;
}

/**
 * Return the endpoint and parameter for lti request based on the block settings.
 * This function uses '/mod/lti/locallib.php'.
 * @param string $config block settings of "block_onlinesurvey"
 * @param string $context optional context for LTI request - not yet supported by LTI provider
 * @param string $course optional course for LTI request - not yet supported by LTI provider
 * @return multitype:string 
 */
function get_launch_data($config = null, $context = null, $course = null) {
    global $CFG, $PAGE;

    require_once $CFG->dirroot.'/mod/lti/locallib.php';
    
    if(empty($config)){
        $config = get_config("block_onlinesurvey");
    }
    // Default the organizationid if not specified.
    if (empty($config->lti_tool_consumer_instance_guid)) {
        $urlparts = parse_url($CFG->wwwroot);
        $config->lti_tool_consumer_instance_guid = $urlparts['host'];
    }

    if (isset($config->proxyid)) {
        // No proxy support
    }
    else {
        $toolproxy = null;
        if (!empty($config->lti_resourcekey)) {
            $key = $config->lti_resourcekey;
        } else if (is_array($config) && !empty($config['lti_resourcekey'])) {
            $key = $config['lti_resourcekey'];
        } else {
            $key = '';
        }
        if (!empty($config->lti_password)) {
            $secret = $config->lti_password;
        } else if (is_array($config) && !empty($config['lti_password'])) {
            $secret = $config['lti_password'];
        } else {
            $secret = '';
        }
    }
    
    $endpoint = !empty($config->lti_url) ? $config->lti_url : $config['lti_url'];
    $endpoint = trim($endpoint);

    // If the current request is using SSL and a secure tool URL is specified, use it.
    if (lti_request_is_using_ssl() && !empty($config->securetoolurl)) {
        $endpoint = trim($config->securetoolurl);
    }

    // If SSL is forced, use the secure tool url if specified. Otherwise, make sure https is on the normal launch URL.
    if (isset($config->forcessl) && ($config->forcessl == '1')) {
        if (!empty($config->securetoolurl)) {
            $endpoint = trim($config->securetoolurl);
        }

        $endpoint = lti_ensure_url_is_https($endpoint);
    } else {
        if (!strstr($endpoint, '://')) {
            $endpoint = 'http://' . $endpoint;
        }
    }

    $orgid = $config->lti_tool_consumer_instance_guid;

    if(empty($course)){
        $course = $PAGE->course;
    }

    $islti2 = isset($config->proxyid);
    
    $allparams = build_request_lti($config, $course);
    
    if(!isset($config->id)){
        $config->id = null;
    }
    $requestparams = $allparams;
    $requestparams = array_merge($requestparams, lti_build_standard_request($config, $orgid, $islti2));
    $customstr = '';
    if (isset($config->lti_customparameters)) {
        $customstr = $config->lti_customparameters;
    }

    // The function 'lti_build_custom_parameters' expects some parameters that are not part of the block setting - so we build "dummys"
    $toolproxy = new stdClass();
    $tool = new stdClass();
    $tool->parameter = '';
    $tool->enabledcapability = array();
    $instance = null;
    $instructorcustomstr = null;
    
    $requestparams = array_merge($requestparams, lti_build_custom_parameters($toolproxy, $tool, $instance, $allparams, $customstr, $instructorcustomstr, $islti2));

    $target = 'iframe';
    if (!empty($target)) {
        $requestparams['launch_presentation_document_target'] = $target;
    }

    // Consumer key currently not used -> $key can be '' --> check "(true or !empty(key))"
    if ((true or !empty($key)) && !empty($secret)) {
        $parms = lti_sign_parameters($requestparams, $endpoint, "POST", $key, $secret);

        $endpointurl = new \moodle_url($endpoint);
        $endpointparams = $endpointurl->params();

        // Strip querystring params in endpoint url from $parms to avoid duplication.
        if (!empty($endpointparams) && !empty($parms)) {
            foreach (array_keys($endpointparams) as $paramname) {
                if (isset($parms[$paramname])) {
                    unset($parms[$paramname]);
                }
            }
        }

    } else {
        // If no key and secret, do the launch unsigned.
        $returnurlparams['unsigned'] = '1';
        $parms = $requestparams;
    }

    return array($endpoint, $parms);
}

/**
 * Builds array of parameters for the LTI request
 * @param unknown $config block settings of "block_onlinesurvey"
 * @param unknown $course course that is used for some context attributes
 * @return multitype:string NULL 
 */
function build_request_lti($config, $course) {
    global $USER;

    $roles = get_ims_roles($USER, $config);

    $intro = '';
    $requestparams = array(
                    'user_id' => $USER->id,
                    'lis_person_sourcedid' => $USER->idnumber,
                    'roles' => $roles,
                    'context_id' => $course->id,
                    'context_label' => $course->shortname,
                    'context_title' => $course->fullname,
    );
    if ($course->format == 'site') {
        $requestparams['context_type'] = 'Group';
    } else {
        $requestparams['context_type'] = 'CourseSection';
        $requestparams['lis_course_section_sourcedid'] = $course->idnumber;
    }

    // E-mail address is evaluated in EVERY case, even if it is decided to use the Username instead
    $requestparams['lis_person_contact_email_primary'] = $USER->email;

    if (strpos($roles, 'Learner') !== false) {
        if($config->useridentifier == 'email'){
            $requestparams['custom_learner_lms_identifier'] = 'lis_person_contact_email_primary';
            $requestparams['lis_person_contact_email_primary'] = $USER->email;
        }
        else if($config->useridentifier == 'username'){
            $requestparams['custom_learner_lms_identifier'] = 'ext_user_username';
            $requestparams['ext_user_username'] = $USER->username;
            $requestparams['custom_learner_provider_identifier'] = "custom".$config->customfieldnumber;
        }
    }
    if (strpos($roles, 'Instructor') !== false) {
        //$requestparams['custom_instructor_lms_identifier'] = 'ext_user_username';
        //$requestparams['ext_user_username'] = $USER->username;
        
        if($config->useridentifier == 'email'){
            $requestparams['custom_instructor_lms_identifier'] = 'lis_person_contact_email_primary';
            $requestparams['lis_person_contact_email_primary'] = $USER->email;
        }
        else if($config->useridentifier == 'username'){
            $requestparams['custom_instructor_lms_identifier'] = 'ext_user_username';
            $requestparams['ext_user_username'] = $USER->username;
            //$requestparams['custom_instructor_provider_identifier'] = "custom".$config->customfieldnumber;
        }
    }

    return $requestparams;
}


/**
 * Gets the LTI role string for the specified user according to lti rolemappings
 *
 * @param unknown $user user object
 * @param unknown $config block settings of "block_onlinesurvey"
 * @return string A role string suitable for passing with an LTI launch
 */
function get_ims_roles($user, $config) {
    global $DB;

    $roles = array();

    // Check if user has "mapped" roles
    $isinstructor = false;
    $lti_mapping = $config->lti_instructormapping;
    if(!empty($lti_mapping)){
        try {
            $isinstructor = $DB->record_exists_select('role_assignments', "userid = $user->id and roleid in ($lti_mapping)", array());
        } catch (Exception $e) {
            error_log("error check user roles for 'instructor': ".$e->getMessage());
        }
    }
    $islearner = false;
    $lti_mapping = $config->lti_learnermapping;
    if(!empty($lti_mapping)){
        try {
            $islearner = $DB->record_exists_select('role_assignments', "userid = $user->id and roleid in ($lti_mapping)", array());
        } catch (Exception $e) {
            error_log("error check user roles for 'learner': ".$e->getMessage());
        }
    }


    if (!empty($isinstructor)) {
        array_push($roles, 'Instructor');
    }
    if (!empty($islearner)) {
        array_push($roles, 'Learner');
    }

    if (is_siteadmin($user)) {
        array_push($roles, 'urn:lti:sysrole:ims/lis/Administrator', 'urn:lti:instrole:ims/lis/Administrator');
    }

    return join(',', $roles);
}

    
/**
 * @param unknown $parameter parameter array for LTI request
 * @param unknown $endpoint endpoint for LTI request
 * @return string|Ambigous <string, unknown> result of the curl LTI request
 */
function lti_post_launch_html_curl($parameter, $endpoint) {

    $retval = '';
    
    $url = $endpoint;
    
    // Set POST variables
    $fields = array();
    
    // Contruct html for the launch parameters.
    foreach ($parameter as $key => $value) {
        $key = htmlspecialchars($key);
        $value = htmlspecialchars($value);
        if ( $key == "ext_submit" ) {
            //
        } else {
            $fields[$key] = urlencode($value);
        }
    }
    // url-ify the data for the POST
    $fields_string = '';
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');

    
    $ch = curl_init($url);
    // Set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    
    $xmlstr = curl_exec($ch);
    
    if ($error_number = curl_errno($ch)) {
        $error_msg_str = curl_error($ch);
        curl_close($ch);
        $retval = $error_msg_str;
        return $retval;
    }
    
    curl_close($ch);
    
    if (empty($xmlstr) or !$xmlstr or !trim($xmlstr)) {
        return $retval;
    }
    else{
        $retval = $xmlstr;
    }

    return $retval;
}

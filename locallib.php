<?php
// This file is part of Moodle - http://moodle.org/
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
 * Plugin "Evaluations (evasys)" - Local library
 *
 * @package    block_onlinesurvey
 * @copyright  2018 Soon Systems GmbH on behalf of evasys GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('BLOCK_ONLINESURVEY_COMM_SOAP', "SOAP");
define('BLOCK_ONLINESURVEY_COMM_LTI', "LTI");
define('BLOCK_ONLINESURVEY_DEFAULT_TIMEOUT', 15);

define('BLOCK_ONLINESURVEY_LTI_REGEX_LEARNER_DEFAULT', '/<(p){1}(.){0,}[\s]{0,}(data-participated="false"){1}[\s]{0,}/');
define('BLOCK_ONLINESURVEY_LTI_REGEX_INSTRUCTOR_DEFAULT',
        '/<(div){1}[\s]{1,}(class=){1}["|\']{1}[a-z]{0,}[\s]{0,}(response-box){1}[\s]{0,}[a-z]{0,}[\s]{0,}["|\']{1}>/');

define('BLOCK_ONLINESURVEY_PRESENTATION_BRIEF', "brief");
define('BLOCK_ONLINESURVEY_PRESENTATION_DETAILED', "detailed");

require_once($CFG->dirroot . '/mod/lti/locallib.php');
require_once(__DIR__ . '/classes/logger.php');

/**
 * Request surveys for the current user according to email or username and displays the result.
 * @param string $config block settings of "block_onlinesurvey"
 * @param string $moodleusername username for SOAP request
 * @param string $moodleemail email for SOAP request
 * @param int $modalzoom indicates if the modal list popup is open or not
 * @return string
 */
function block_onlinesurvey_get_soap_content($config = null, $moodleusername = '', $moodleemail = '', $modalzoom = 0) {
    global $SESSION;

    $surveyurl = 'indexstud.php?type=html&user_tan=';

    if (empty($config)) {
        $config = get_config("block_onlinesurvey");
    }

    $connectiontype = $config->connectiontype;
    $surveyurl = $config->survey_login.$surveyurl;
    $wsdl = $config->survey_server;
    $soapuser = $config->survey_user;
    $soappassword = $config->survey_pwd;
    $debugmode = $config->survey_debug;

    $hideempty = $config->survey_hide_empty;
    $offerzoom = $config->offer_zoom;

    $timeout = isset($config->survey_timeout) ? $config->survey_timeout : BLOCK_ONLINESURVEY_DEFAULT_TIMEOUT;

    // Parse wsdlnamespace from the wsdl url.
    preg_match('/\/([^\/]+\.wsdl)$/', $wsdl, $matches);

    $soapcontentstr = '';

    if (count($matches) == 2) {
        $wsdlnamespace = $matches[1];

        $soapconfigobj = new stdClass();
        $soapconfigobj->connectiontype = $connectiontype;
        $soapconfigobj->wsdl = $wsdl;
        $soapconfigobj->timeout = $timeout;
        $soapconfigobj->debugmode = $debugmode;
        $soapconfigobj->soapuser = $soapuser;
        $soapconfigobj->soappassword = $soappassword;
        $soapconfigobj->wsdlnamespace = $wsdlnamespace;
        $soapconfigobj->useridentifier = $config->useridentifier;
        $soapconfigobj->moodleemail = $moodleemail;
        $soapconfigobj->moodleusername = $moodleusername;
        $soapconfigobj->customfieldnumber = $config->customfieldnumber;
        $soapconfigobj->coursecode = '';

        $result = new stdClass();

        $soaprequesteachtime = $config->soap_request_eachtime;

        // Get surveys if no surveys in SESSION or debug mode for the block is enabled.
        if (!isset($SESSION->block_onlinesurvey_surveykeys) || $debugmode || $soaprequesteachtime) {
            $result = block_onlinesurvey_get_surveys($soapconfigobj);
            $SESSION->block_onlinesurvey_surveykeys = $result->surveys;

            $SESSION->block_onlinesurvey_error = $result->error;
        }

        if (isset($SESSION->block_onlinesurvey_error)) {
            $result->error = $SESSION->block_onlinesurvey_error;
        }

        if (is_object($SESSION->block_onlinesurvey_surveykeys)) {
            if (!is_array($SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys)) {
                $SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys = array(
                                $SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys
                );
            }

            $count = count($SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys);

            $count2 = 0;

            $surveysfound = false;
            foreach ($SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys as $surveykey) {
                if (!empty($surveykey->TransactionNumber) && ($surveykey->TransactionNumber != null &&
                        $surveykey->TransactionNumber !== 'null')) {
                    $surveysfound = true;

                    $count2++;
                }
            }

            if ($hideempty && $count2 > 0) {
                $soapcontentstr .= block_onlinesurvey_viewscript();
            }

            if (!$offerzoom && $count2 > 0 && !$modalzoom) {
                $soapcontentstr .= block_onlinesurvey_surveybuttonscript();
            }

            if ($count2 > 0 && !$modalzoom) {
                $soapcontentstr .= block_onlinesurvey_highlightscript($count2);
            } else if ($count2 == 0 && !$modalzoom) {
                $soapcontentstr .= block_onlinesurvey_donthighlightscript();
            }

            if ($config->presentation == BLOCK_ONLINESURVEY_PRESENTATION_BRIEF && !$modalzoom) {

                $soapcontentstr .= block_onlinesurvey_createsummary($count2);

                // Surveys found.
                if ($count2 && $surveysfound) {
                    if (!empty($config->survey_show_popupinfo)) {
                        $soapcontentstr .= '<script language="JavaScript">'.
                                'if (typeof window.parent.evasysGeneratePopupinfo == "function") { '.
                                'window.parent.evasysGeneratePopupinfo(); }</script>';
                    }
                }

            } else {

                // Surveys found.
                if ($count && $surveysfound) {
                    $soapcontentstr .= '<ul class="block_onlinesurvey_survey_list">';

                    $cnt = 0;
                    foreach ($SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys as $surveykey) {
                        if ($surveykey->TransactionNumber !== 'null') {
                            $cnt++;

                            $soapcontentstr .= '<li class="survey">';
                            $soapcontentstr .= "<a id=\"surveylink_".$cnt."\" ".
                                    "href=\"$surveyurl" . "{$surveykey->TransactionNumber}\" ".
                                    "target=\"_blank\">$surveykey->CourseName</a>";
                            $soapcontentstr .= '</li>';

                        }
                    }
                    $soapcontentstr .= '</ul>';

                    if (!empty($config->survey_show_popupinfo)) {
                        $soapcontentstr .= '<script language="JavaScript">'.
                                'if (typeof window.parent.evasysGeneratePopupinfo == "function") { '.
                                'window.parent.evasysGeneratePopupinfo(); }</script>';
                    }
                } else {
                    $soapcontentstr = '<div class="block_onlinesurvey_info">'.
                            get_string('surveys_exist_not', 'block_onlinesurvey').'</div>';
                }
            }
        } else if (empty($SESSION->block_onlinesurvey_surveykeys)) {
            $soapcontentstr = '<div class="block_onlinesurvey_info">'.get_string('surveys_exist_not', 'block_onlinesurvey').
                    '</div>';
        }

        if (isset($result->error) && !empty($result->error)) {
            $soapcontentstr = get_string('error_occured', 'block_onlinesurvey', $result->error);
        }

        // TODO: Check, was hier angezeigt werden soll.
        if ($debugmode && isset($result->warning) && !empty($result->warning)) {
            $soapcontentstr = get_string('error_warning_message', 'block_onlinesurvey', $result->warning) ."<br>" . $soapcontentstr;
        }
    } else {
        if ($debugmode) {
            $soapcontentstr = get_string('error_wsdl_namespace', 'block_onlinesurvey');
        }
    }

    echo $soapcontentstr;
}

/**
 * Returns a string with HTML code for the compact view.
 *
 * @param int $surveycount number of surveys
 * @return string
 */
function block_onlinesurvey_createsummary($surveycount) {
    $offerzoom = get_config('block_onlinesurvey', 'offer_zoom');
    if ($surveycount == 0 && $offerzoom == false) {
        $contentstr = "<div id=\"block_onlinesurvey_area\" class=\"block_onlinesurvey_area\">";

        $contentstr .= "<div class=\"block_onlinesurvey_circle\" >";
        $contentstr .= "<span class=\"block_onlinesurvey_number\">";
        $contentstr .= "<i class=\"fa fa-check\"></i>";
        $contentstr .= "</span>";
        $contentstr .= "</div>";

        $contentstr .= '<div class="block_onlinesurvey_text">' . get_string('surveys_exist_not', 'block_onlinesurvey') . '</div>';

        $contentstr .= "</div>";
    } else if ($surveycount == 0 && $offerzoom == true) {
        $contentstr = "<div id=\"block_onlinesurvey_area\" class=\"block_onlinesurvey_area block_onlinesurvey_offerzoom\" ".
            "onClick=\"parent.document.getElementById('block_onlinesurvey_surveys_content').click(parent.document);\">";

        $contentstr .= "<div class=\"block_onlinesurvey_circle\" >";
        $contentstr .= "<span class=\"block_onlinesurvey_number\">";
        $contentstr .= "<i class=\"fa fa-check\"></i>";
        $contentstr .= "</span>";
        $contentstr .= "<div class=\"block_onlinesurvey_compact_magnifier\">";
        $contentstr .= "<i class=\"fa fa-search-plus\"></i>";
        $contentstr .= "</div>";
        $contentstr .= "</div>";

        $contentstr .= '<div class="block_onlinesurvey_text">' . get_string('surveys_exist_not', 'block_onlinesurvey') . '</div>';

        $contentstr .= "</div>";
    } else {
        if ($surveycount > 0 && $surveycount <= 3) {
            $surveycountclass = 'block_onlinesurvey_surveycount_'.$surveycount;
        }
        if ($surveycount > 3) {
            $surveycountclass = 'block_onlinesurvey_surveycount_gt3';
        }

        $contentstr = "<div id=\"block_onlinesurvey_area\" ".
                "class=\"block_onlinesurvey_area block_onlinesurvey_surveysexist ".$surveycountclass."\" ".
                "onClick=\"parent.document.getElementById('block_onlinesurvey_surveys_content').click(parent.document);\">";

        $contentstr .= "<div class=\"block_onlinesurvey_circle\" >";
        $contentstr .= "<span class=\"block_onlinesurvey_number\">";
        $contentstr .= $surveycount;
        $contentstr .= "</span>";
        $contentstr .= "<div class=\"block_onlinesurvey_compact_magnifier\">";
        $contentstr .= "<i class=\"fa fa-search-plus\"></i>";
        $contentstr .= "</div>";
        $contentstr .= "</div>";

        $contentstr .= '<div class="block_onlinesurvey_text">' . get_string('surveys_exist', 'block_onlinesurvey') . '</div>';

        $contentstr .= "</div>";
    }

    return $contentstr;
}

/**
 * Returns a string with a <script> tag which shows the previously hidden block.
 *
 * @return string
 */
function block_onlinesurvey_viewscript() {
    return '<script language="JavaScript">'."\n".
            '   var hiddenelements = parent.document.getElementsByClassName("block_onlinesurvey");'."\n".
            '   for (var i = 0; i < hiddenelements.length; i++) {'."\n".
            '       hiddenelements[i].style.display = "block";'."\n".
            '   }'."\n".
            '</script>';
}

/**
 * Returns a string with a <script> tag which shows the previously hidden 'zoom survey list' button.
 *
 * @return string
 */
function block_onlinesurvey_surveybuttonscript() {
    return '<script language="JavaScript">'."\n".
            '   var hiddenelements = parent.document.getElementsByClassName("block_onlinesurvey_allsurveys");'."\n".
            '   for (var i = 0; i < hiddenelements.length; i++) {'."\n".
            '       hiddenelements[i].style.display = "block";'."\n".
            '   }'."\n".
            '</script>';
}

/**
 * Returns a string with a <script> tag which adds a class to indicate that surveys exist.
 *
 * @param int $surveycount The number of open surveys.
 * @return string
 */
function block_onlinesurvey_highlightscript($surveycount) {
    if ($surveycount > 0 && $surveycount <= 3) {
        $surveycountclass = 'block_onlinesurvey_surveycount_'.$surveycount;
    }
    if ($surveycount > 3) {
        $surveycountclass = 'block_onlinesurvey_surveycount_gt3';
    }

    return '<script language="JavaScript">'."\n".
            '   var parentelements = parent.document.getElementsByClassName("block_onlinesurvey");'."\n".
            '   for (var i = 0; i < parentelements.length; i++) {'."\n".
            '       parentelements[i].classList.add("block_onlinesurvey_surveysexist");'."\n".
            '       parentelements[i].classList.add("'.$surveycountclass.'");'."\n".
            '   }'."\n".
            '</script>';
}

/**
 * Returns a string with a <script> tag which removes a class to indicate that no surveys exist.
 *
 * @return string
 */
function block_onlinesurvey_donthighlightscript() {
    return '<script language="JavaScript">'."\n".
            '   var parentelements = parent.document.getElementsByClassName("block_onlinesurvey");'."\n".
            '   for (var i = 0; i < parentelements.length; i++) {'."\n".
            '       parentelements[i].classList.remove("block_onlinesurvey_surveysexist");'."\n".
            '   }'."\n".
            '</script>';
}

/**
 * Perform SOAP request for surveys of a user according to user email or username.
 *
 * @param object $soapconfigobj Object containing data for SOAP request.
 * @return object Object containing surveys if present and errors or warnings of the onlinesurvey_soap_client
 */
function block_onlinesurvey_get_surveys($soapconfigobj) {
    $retval = new stdClass();
    $retval->error = null;
    $retval->warning = null;
    $retval->surveys = false;
    try {
        // Check connectiontype for SOAP.
        if ($soapconfigobj->connectiontype == 'SOAP') {
            require_once('onlinesurvey_soap_client.php');

            $client = new onlinesurvey_soap_client( $soapconfigobj->wsdl,
                    array(
                                    'trace' => 1,
                                    'feature' => SOAP_SINGLE_ELEMENT_ARRAYS,
                                    'connection_timeout' => $soapconfigobj->timeout),
                    $soapconfigobj->timeout,
                    $soapconfigobj->debugmode
                    );

            $header = array(
                            'Login' => $soapconfigobj->soapuser,
                            'Password' => $soapconfigobj->soappassword
            );

            if (is_object($client)) {
                if ($client->haswarning) {
                    $retval->warning = $client->warnmessage;
                }

                $soapheader = new SoapHeader($soapconfigobj->wsdlnamespace, 'Header', $header);
                $client->__setSoapHeaders($soapheader);
            } else {
                $retval->error = block_onlinesurvey_handle_error("SOAP client configuration error");
                return $retval;
            }

            if (!empty($soapconfigobj->useridentifier)) {
                if ($soapconfigobj->useridentifier == 'email') {
                    if ($soapconfigobj->moodleemail) {
                        $retval->surveys = $client->GetPswdsByParticipant($soapconfigobj->moodleemail);
                    }
                } else if ($soapconfigobj->useridentifier == 'username') {
                    $retval->surveys = $client->GetPswdsByParticipant($soapconfigobj->moodleusername,
                            $soapconfigobj->coursecode, $soapconfigobj->customfieldnumber);
                }
            }
        }
    } catch (Exception $e) {
        $retval->error = block_onlinesurvey_handle_error($e);
        return $retval;
    }
    return $retval;
}

/**
 * Helper function that returns an error string
 * @param Array|object|string $err
 * @return string human readable representation of an error
 */
function block_onlinesurvey_handle_error($err) {
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
        $error = block_onlinesurvey_print_exceptions($err);
    }
    return $error;
}

/**
 * Helper function for exceptions
 * @param object $e should be an exception
 * @return string formatted error message of the excetion
 */
function block_onlinesurvey_print_exceptions($e) {
    if (get_class($e) == "SoapFault") {
        $msg = "{$e->faultstring}";

        $context = context_system::instance();
        if (has_capability('block/onlinesurvey:view_debugdetails', $context)) {
            $detail = '';
            if (isset($e->detail) && !empty($e->detail)) {
                $detail = $e->detail;
                if (is_object($detail) && isset($detail->tSoapfault)) {
                    $detail = $detail->tSoapfault;
                    if (isset($detail->sDetails)) {
                        $detail = $detail->sDetails;
                    }
                }

                $msg .= "<br>".$detail;
            }
        }
    } else {
        $msg = $e->getMessage();
    }

    return $msg;
}

/**
 * Request surveys via LTI for the current user according to email or username and displays the result.
 * This functions uses functions of '/mod/lti/locallib.php'.
 * Performs a second request via curl to check the result for learner content in order to include code to display popupinfo dialog -
 * if option is selected in the settings.
 * @param string $config block settings of "block_onlinesurvey"
 * @param string $context context for LTI request - not yet supported by LTI provider
 * @param string $course course for LTI request - not yet supported by LTI provider
 * @param int $modalzoom indicates if the modal list popup is open or not
 * @return string
 */
function block_onlinesurvey_get_lti_content($config = null, $context = null, $course = null, $modalzoom = 0, $foruserid = 0) {
    global $CFG, $SESSION;
    require_once(__DIR__ . '/classes/logger.php');
    $logger = new \block_onlinesurvey\Logger('block_onlinesurvey_get_lti_content.txt');
    $logger->log('inside block_onlinesurvey_get_lti_content with these settings:');
    $logger->log('config:', $config);
    $logger->log('context:', $context);
    $logger->log('course:', $course);
    $logger->log('modalzoom:', $modalzoom);
    require_once($CFG->dirroot.'/mod/lti/locallib.php');

    if (empty($config)) {
        $logger->log('config is empty, so about to call block_onlinesurvey_get_launch_config');
        $config = block_onlinesurvey_get_launch_config();
    }

    $courseid = (!empty($course->id)) ? $course->id : 1;
    $logger->log('about to get launch data');
    list($endpoint, $parameter) = block_onlinesurvey_lti_get_launch_data($config, '', '', $foruserid);
    $logger->log('inside block_onlinesurvey_get_lti_content, after calling block_onlinesurvey_lti_get_launch_data');
    $logger->log('got endpoint: ', $endpoint);
    $logger->log('got parameter: ', $parameter);
    $debuglaunch = $config->survey_debug;

    $surveycount = 0;

    // Check for learner content in LTI result.
    try {
        $content2 = block_onlinesurvey_lti_post_launch_html_curl($parameter, $endpoint, $config);
    } catch (Exception $e) {
        $lticontentstr = $e->getMessage();
        $logger->log('got error in block_onlinesurvey_get_lti_content(): ' . $e->getMessage());
        echo $lticontentstr;
        return '';
    }

    // Search in $content2 for e.g.: <div class="cell participate centered">.
    // If match found and survey_show_popupinfo is set, add code to generate popup.
    if (!empty($content2)) {
        if (isset($config->lti_regex_learner) && !empty($config->lti_regex_learner)) {
            $re = $config->lti_regex_learner;

            // No regex in config -> use default regex.
        } else {
            $re = BLOCK_ONLINESURVEY_LTI_REGEX_LEARNER_DEFAULT;
        }

        if (!empty($re)) {
            $surveycount = preg_match_all($re, $content2, $matches, PREG_SET_ORDER, 0);
            $logger->log('got surveycount: ', $surveycount);
            $logger->log('got matches: ', $matches);
            $logger->log('$config->survey_show_popupinfo: ', $config->survey_show_popupinfo);
            $SESSION->block_onlinesurvey_curl_checked = true;

            if (!empty($matches) && !empty($config->survey_show_popupinfo)) {
                // Check to display dialog is (also) done in JS function "evasysGeneratePopupinfo".
                echo '<script language="JavaScript">if (typeof window.parent.evasysGeneratePopupinfo == "function") { '.
                        'window.parent.evasysGeneratePopupinfo(); }</script>';
            }
        }

        if (isset($config->lti_regex_instructor) && !empty($config->lti_regex_instructor)) {
            $reinstructor = $config->lti_regex_instructor;

            // No regex in config -> use default regex.
        } else {
            $reinstructor = BLOCK_ONLINESURVEY_LTI_REGEX_INSTRUCTOR_DEFAULT;
        }
        if (!empty($reinstructor)) {
            $surveycount += preg_match_all($reinstructor, $content2, $matches, PREG_SET_ORDER, 0);
        }
    }

    $lticontentstr = '';

    if ($config->survey_hide_empty && $surveycount > 0 && !$modalzoom) {
        $lticontentstr .= block_onlinesurvey_viewscript();
    }

    if (!$config->offer_zoom && $surveycount > 0 && !$modalzoom) {
        $lticontentstr .= block_onlinesurvey_surveybuttonscript();
    }

    if ($surveycount > 0 && !$modalzoom) {
        $lticontentstr .= block_onlinesurvey_highlightscript($surveycount);
    } else if ($surveycount == 0 && !$modalzoom) {
        $lticontentstr .= block_onlinesurvey_donthighlightscript();
    }

    if ($config->presentation == BLOCK_ONLINESURVEY_PRESENTATION_BRIEF && !$modalzoom) {
        $logger->log('creating summary');
        $lticontentstr .= block_onlinesurvey_createsummary($surveycount);
    } else {
        $logger->log('not creating summary. $config->presentation:', $config->presentation);
        $logger->log('$modalzoom:', $modalzoom);
        if (empty($context)) {
            $context = context_system::instance();
        }
        if (empty($debuglaunch) || has_capability('block/onlinesurvey:view_debugdetails', $context)) {
            $lticontentstr .= lti_post_launch_html($parameter, $endpoint, $debuglaunch);

            if ($debuglaunch && has_capability('block/onlinesurvey:view_debugdetails', $context)) {
                $debuglaunch = false;
                // $lti_content_str2 = lti_post_launch_html($parameter, $endpoint, $debuglaunch);
                // echo "$lti_content_str2 <br><br>";
            }
        } else {
            $lticontentstr = get_string('error_debugmode_missing_capability', 'block_onlinesurvey');
        }
    }

    echo $lticontentstr;
}

/**
 * Return the endpoint and parameter for lti request based on the block settings.
 * This function uses '/mod/lti/locallib.php'.
 * @param string $config block settings of "block_onlinesurvey"
 * @param string $course optional course for LTI request - not yet supported by LTI provider
 * @param string $nonce the nonce value to use (applies to LTI 1.3 only)
 * @param string $messagetype LTI Message Type for this launch
 * @return array the endpoint URL and parameters (including the signature)
 */
function block_onlinesurvey_lti_get_launch_data($config = null, $nonce = '', $messagetype = 'basic-lti-launch-request', $foruserid = 0) {
    global $CFG, $PAGE, $USER, $DB;

    require_once($CFG->dirroot.'/mod/lti/locallib.php');
    $logger = new \block_onlinesurvey\Logger('block_onlinesurvey_lti_get_launch_data.txt');
    $logger->log('called block_onlinesurvey_lti_get_launch_data with config:', $config);
    if (empty($config)) {
        $config = get_config("block_onlinesurvey");
    }

    $ltiversion = $config->connectiontype;
    $typeid = $config->typeid;
    if (empty($config->lti_clientid)) {
        $config->lti_clientid = block_onlinesurvey_get_clientid($typeid);
    }
    /*
    ICTODO: check if we need to get the type id from somewhere
    lti_get_launch_data does it so:
    $tool = lti_get_instance_type($instance); // gets it from table {lti_types} - we don't have that here though
    if ($tool) {
        $typeid = $tool->id;
        $ltiversion = $tool->ltiversion;
    } else {
        $typeid = null;
        $ltiversion = LTI_VERSION_1;
    }
    */


    // Default the organizationid if not specified.
    if (empty($config->lti_tool_consumer_instance_guid)) {
        $urlparts = parse_url($CFG->wwwroot);
        $config->lti_tool_consumer_instance_guid = $urlparts['host'];
    }

    if ($ltiversion === LTI_VERSION_1P3) {
        $key = $config->lti_clientid;
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

    $endpoint = !empty($config->lti_url) ? $config->lti_url : $config['lti_url'];
    $endpoint = trim($endpoint);

    $logger->log('in line ' . __LINE__ );
    // If the current request is using SSL and a secure tool URL is specified, use it.
    if (lti_request_is_using_ssl() && !empty($config->securetoolurl)) {
        $logger->log('in line ' . __LINE__ );
        $endpoint = trim($config->securetoolurl);
    }
    $logger->log('in line ' . __LINE__ );
    // If SSL is forced, use the secure tool url if specified. Otherwise, make sure https is on the normal launch URL.
    if (isset($config->forcessl) && ($config->forcessl == '1')) {
        if (!empty($config->securetoolurl)) {
            $endpoint = trim($config->securetoolurl);
        }
        $logger->log('about to call lti_ensure_url_is_https, in line ' . __LINE__ );
        $endpoint = lti_ensure_url_is_https($endpoint);
        $logger->log('called lti_ensure_url_is_https, in line ' . __LINE__ );
    } else {
        if (!strstr($endpoint, '://')) {
            $endpoint = 'http://' . $endpoint;
        }
    }
    $logger->log('in line ' . __LINE__ );
    $orgid = $config->lti_tool_consumer_instance_guid;

    if (empty($course)) {
        $course = $PAGE->course;
    }
    $logger->log('about to call block_onlinesurvey_build_request_lti, in line ' . __LINE__ );
    $allparams = block_onlinesurvey_build_request_lti($config, $course, $messagetype, $foruserid); // analog to lti/locallib.php line 560
    $logger->log('called block_onlinesurvey_build_request_lti, got allparams:', $allparams );
    if (!isset($config->id)) {
        $config->id = null;
    }
    $requestparams = $allparams;
    $requestparams = array_merge($requestparams, lti_build_standard_message($config, $orgid, false, $messagetype));
    $customstr = '';
    if (isset($config->lti_customparameters)) {
        $customstr = $config->lti_customparameters;
    }

    // The function 'lti_build_custom_parameters' expects some parameters that are not part of the block setting -
    // so we build "dummys".
    $toolproxy = new stdClass();
    $tool = new stdClass();
//    $tool->ltiversion = LTI_VERSION_1; // ICTODO: this shouldn't be hardcoded - we use LTI_VERSION_1P3
    $tool->ltiversion = $ltiversion;
    $tool->parameter = '';
    $tool->enabledcapability = array();
    $instance = null;
    $instructorcustomstr = null;

    $requestparams = array_merge($requestparams, lti_build_custom_parameters($toolproxy, $tool, $instance, $allparams, $customstr,
            $instructorcustomstr, false));

    $target = 'iframe';
    if (!empty($target)) {
        $requestparams['launch_presentation_document_target'] = $target;
    }
    $basicoutcome = new \stdClass();
    $servicesalt = $DB->get_field('lti_types_config', 'value', ['typeid'=>$typeid, 'name'=>'servicesalt']);
    $basicoutcome->lis_result_sourcedid = json_encode(lti_build_sourcedid($typeid,
        $USER->id,
        $servicesalt,
        $typeid));
    $serviceurl = new \moodle_url('/mod/lti/service.php');
    $serviceurl = $serviceurl->out();
    $forcessl = false;
    if (!empty($CFG->mod_lti_forcessl)) {
        $forcessl = true;
    }
    if ((isset($toolconfig['forcessl']) && ($toolconfig['forcessl'] == '1')) or $forcessl) {
        $serviceurl = lti_ensure_url_is_https($serviceurl);
    }
    $basicoutcome->lis_outcome_service_url = $serviceurl;
    if ($foruserid) {
        $requestparams['for_user_id'] = $foruserid;
    }
    $requestparams['https://purl.imsglobal.org/spec/lti-bo/claim/basicoutcome'] = $basicoutcome;
    $requestparams['resource_link_id'] = block_onlinesurvey_get_lti_typeid();

    // Consumer key currently not used -> $key can be '' -> check "(true or !empty(key))".
    if ((!empty($key) && !empty($secret)) || ($ltiversion === LTI_VERSION_1P3)) { // ICNOTICE: matches mod/lti/locallib.php, lines 632ff
        if ($ltiversion !== LTI_VERSION_1P3) {
            $logger->log('not lti version 1p3. current ltiversion:', $ltiversion);
            $logger->log('about to call lti_sign_parameters');
            $parms = lti_sign_parameters($requestparams, $endpoint, 'POST', $key, $secret);
            $logger->log('called lti_sign_parameters and got parms:', $parms);
        } else {
            $requestparams['https://purl.imsglobal.org/spec/lti/claim/version'] = '1.3.0';
            $requestparams['lti_version'] = '1.3.0';
            $logger->log('about to call lti_sign_jwt');
            $logger->log('with requestparams:', $requestparams);
            $logger->log('with endpoint:', $endpoint);
            $logger->log('with key:', $key);
            $logger->log('with typeid:', $typeid);
            $logger->log('with nonce:', $nonce);

//            $requestparams = block_onlinesurvey_get_dummy_request(); // only use for testing purposes
            $parms = lti_sign_jwt($requestparams, $endpoint, $key, $typeid, $nonce);
            $logger->log('called lti_sign_jwt and got $parms: ', $parms);
        }

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
        $logger->log('secret was empty, had to skip the lti_sign procedure');
        // If no key and secret, do the launch unsigned.
        $returnurlparams['unsigned'] = '1';
        $parms = $requestparams;
    }

    return array($endpoint, $parms);
}

/**
 * Builds array of parameters for the LTI request
 * @param object $config block settings of "block_onlinesurvey"
 * @param object $course course that is used for some context attributes
 * @param string $messagetype LTI Message Type for this launch
 * @return multitype:string NULL
 */
function block_onlinesurvey_build_request_lti($config, $course, $messagetype, $foruserid = 0) {
    global $USER;

    $roles = block_onlinesurvey_get_ims_roles($USER, $config);

    $requestparams = array(
                    'user_id' => $USER->id,
                    'lis_person_sourcedid' => $USER->idnumber,
                    'roles' => $roles,
                    'context_id' => $course->id,
                    'context_label' => $course->shortname,
                    'context_title' => $course->fullname,
    );
    if ($messagetype) {
        $requestparams['lti_message_type'] = $messagetype;
    }
    if ($course->format == 'site') {
        $requestparams['context_type'] = 'Group';
    } else {
        $requestparams['context_type'] = 'CourseSection';
        $requestparams['lis_course_section_sourcedid'] = $course->idnumber;
    }

    // E-mail address is evaluated in EVERY case, even if it is decided to use the Username instead.
    $requestparams['lis_person_contact_email_primary'] = $USER->email;
    $requestparams['lis_person_name_given'] = $USER->firstname;
    $requestparams['lis_person_name_family'] = $USER->lastname;
    $requestparams['lis_person_name_full'] = fullname($USER);
    $requestparams['ext_user_username'] = $USER->username;

    // the data below will later be stored/converted in:
//    $requestparams["https://purl.imsglobal.org/spec/lti/claim/resource_link"] = [
//        "title"=> "Evasys Online Survey",
//        "description" => "",
//        "id"=> "1",
//    ];
    $requestparams['resource_link_title'] = $config->blocktitle;
    $requestparams['resource_link_description'] = $config->blocktitle;
    $requestparams['resource_link_id'] = block_onlinesurvey_get_lti_typeid();


    $requestparams['ext_lms'] = 'moodle-2';
    $requestparams["https://purl.imsglobal.org/spec/lti/claim/ext"] = [
        "user_username"=> $USER->username,
        "lms"=>  "moodle-2",
    ];

    $requestparams["email"]= $USER->email;
    if ($foruserid) {
        $requestparams['for_user_id'] = $foruserid;
    }
    $requestparams["https://purl.imsglobal.org/spec/lti/claim/version"] = '1.3.0';

    if (strpos($roles, 'Learner') !== false) {
        if ($config->useridentifier == 'email') {
            $requestparams['custom_learner_lms_identifier'] = 'lis_person_contact_email_primary';
            $requestparams['lis_person_contact_email_primary'] = $USER->email;
        } else if ($config->useridentifier == 'username') {
            $requestparams['custom_learner_lms_identifier'] = 'ext_user_username';
            $requestparams['ext_user_username'] = $USER->username;
            $requestparams['custom_learner_provider_identifier'] = "custom".$config->customfieldnumber;
        }
    }
    if (strpos($roles, 'Instructor') !== false) {
        // $requestparams['custom_instructor_lms_identifier'] = 'ext_user_username';
        // $requestparams['ext_user_username'] = $USER->username;

        if ($config->useridentifier == 'email') {
            $requestparams['custom_instructor_lms_identifier'] = 'lis_person_contact_email_primary';
            $requestparams['lis_person_contact_email_primary'] = $USER->email;
        } else if ($config->useridentifier == 'username') {
            $requestparams['custom_instructor_lms_identifier'] = 'ext_user_username';
            $requestparams['ext_user_username'] = $USER->username;
            // $requestparams['custom_instructor_provider_identifier'] = "custom".$config->customfieldnumber;
        }
    }

    return $requestparams;
}

/**
 * Gets the LTI role string for the specified user according to lti rolemappings
 *
 * @param object $user user object
 * @param object $config block settings of "block_onlinesurvey"
 * @return string A role string suitable for passing with an LTI launch
 */
function block_onlinesurvey_get_ims_roles($user, $config) {
    global $DB;

    $roles = array();

    // Check if user has "mapped" roles.
    $isinstructor = false;
    $ltimapping = $config->lti_instructormapping;
    if (!empty($ltimapping)) {
        try {
            $ltimapping = explode(',', $ltimapping);
            list($sql, $params) = $DB->get_in_or_equal($ltimapping, SQL_PARAMS_NAMED, 'lti_mapping');
            $params['userid'] = $user->id;
            $isinstructor = $DB->record_exists_select('role_assignments', "userid = :userid and roleid $sql", $params);
        } catch (Exception $e) {
            error_log("error check user roles for 'instructor': ".$e->getMessage());
        }
    }
    $islearner = false;
    $ltimapping = $config->lti_learnermapping;
    if (!empty($ltimapping)) {
        try {
            $ltimapping = explode(',', $ltimapping);
            list($sql, $params) = $DB->get_in_or_equal($ltimapping, SQL_PARAMS_NAMED, 'lti_mapping');
            $params['userid'] = $user->id;
            $islearner = $DB->record_exists_select('role_assignments', "userid = :userid and roleid $sql", $params);
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

    // User has NO role in moodle -> use role mapping for learner.
    if (empty($roles)) {
        array_push($roles, 'Learner');
    }

    if (is_siteadmin($user)) {
        array_push($roles, 'urn:lti:sysrole:ims/lis/Administrator', 'urn:lti:instrole:ims/lis/Administrator');
    }

    return join(',', $roles);
}

/**
 * Fetches the LTI content on the server for analyzing the survey list server-side.
 *
 * @param array $parameter parameter for LTI request
 * @param string $endpoint endpoint for LTI request
 * @param object $config the plugin configuration
 * @return string result of the curl LTI request
 */
function block_onlinesurvey_lti_post_launch_html_curl($parameter, $endpoint, $config) {
    $logger = new \block_onlinesurvey\Logger('block_onlinesurvey_launch_via_curl.txt', \block_onlinesurvey\Logger::LEVEL_VERY_VERBOSE);
    $logger->log('using curl to launch LTI');
    // Set POST variables.
    $fields = array();

    // Construct html for the launch parameters.
    foreach ($parameter as $key => $value) {
        $key = htmlspecialchars($key);
        $value = htmlspecialchars($value);
        if ( $key != "ext_submit" ) {
            $fields[$key] = urlencode($value);
        }
    }
    // Url-ify the data for the POST.
    $fieldsstring = '';
    foreach ($fields as $key => $value) {
        $fieldsstring .= $key.'='.$value.'&';
    }
    $fieldsstring = rtrim($fieldsstring, '&');

    $curl = new curl;
    $timeout = isset($config->survey_timeout) ? $config->survey_timeout : BLOCK_ONLINESURVEY_DEFAULT_TIMEOUT;
    $curloptions = array(
        'RETURNTRANSFER' => 1,
        'FRESH_CONNECT' => true,
        'TIMEOUT' => $timeout,
    );
    $logger->log('about to call curl with endpoint:', $endpoint);
    $logger->log('and $fieldsstring:', $fieldsstring);
    $ret = $curl->post($endpoint, $fieldsstring, $curloptions);
    $logger->log('curl called, still running');

    if ($errornumber = $curl->get_errno()) {
        $msgoutput = get_string('error_survey_curl_timeout_msg', 'block_onlinesurvey');
        $logger->log('got curl error', $curl->get_info());
        $logger->log('error number: ', $errornumber);
        $logger->log('response', $curl->get_raw_response());
        $context = context_system::instance();
        if (has_capability('block/onlinesurvey:view_debugdetails', $context)) {
            if (!empty($msgoutput)) {
                $msgoutput .= "<br><br>"."curl_errno $errornumber: $ret"; // Variable $ret now contains the error string.
            }
        }

        if (in_array($errornumber, array(CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED))) {
            throw new Exception("$msgoutput");
        }
    } else {
        $logger->log('No errors in curl call. Got response:', $ret);
    }

    return $ret;
}
function block_onlinesurvey_lti_initiate_login($config, $messagetype = 'basic-lti-launch-request',
                                               $title = '', $text = '', $foruserid = 0) {
    global $SESSION;
    $logger = new \block_onlinesurvey\Logger('block_onlinesurvey_lti_initiate_login.txt');
    $logger->log('about to call block_onlinesurvey_lti_build_login_request with the following parameters:');
    $logger->log('config:', $config);
    $logger->log('messagetype:', $messagetype);
    $logger->log('foruserid:', $foruserid);
    $logger->log('title:', $title);
    $logger->log('text:', $text);
    $params = block_onlinesurvey_lti_build_login_request($config, $messagetype, $foruserid, $title, $text);
    $logger->log('function called, received params:', $params);
    $r = "<form action=\"" . $config->lti_initiatelogin .
        "\" name=\"ltiInitiateLoginForm\" id=\"ltiInitiateLoginForm\" method=\"post\" " .
        "encType=\"application/x-www-form-urlencoded\">\n";

    foreach ($params as $key => $value) {
        $key = htmlspecialchars($key, ENT_COMPAT);
        $value = htmlspecialchars($value, ENT_COMPAT);
        $r .= "  <input type=\"hidden\" name=\"{$key}\" value=\"{$value}\"/>\n";
    }
    $r .= "</form>\n";

    $r .= "<script type=\"text/javascript\">\n" .
        "//<![CDATA[\n" .
        "document.ltiInitiateLoginForm.submit();\n" .
        "//]]>\n" .
        "</script>\n";

    return $r;
}

function block_onlinesurvey_lti_initiate_login_via_curl($config, $messagetype = 'basic-lti-launch-request',
                                                        $title = '', $text = '', $foruserid = 0) {
    $endpoint = $config->lti_initiatelogin;
    $params = block_onlinesurvey_lti_build_login_request($config, $messagetype, $foruserid, $title, $text);
    $content2 = block_onlinesurvey_lti_post_launch_html_curl($params, $endpoint, $config);
    return $content2;
}


/**
 * Prepares an LTI 1.3 login request
 *
 * @param stdClass       $config    Tool type configuration
 * @param string         $messagetype   LTI message type
 * @param int            $foruserid Id of the user targeted by the launch
 * @param string         $title     Title of content item
 * @param string         $text      Description of content item
 * @return array Login request parameters
 */
function block_onlinesurvey_lti_build_login_request($config, $messagetype, $foruserid=0, $title = '', $text = '') {
    global $USER, $CFG, $SESSION;
    $ltihint = [];

    $endpoint = $config->lti_url;
    $logger = new \block_onlinesurvey\Logger('block_onlinesurvey_lti_build_login_request.txt');

//    if (($messagetype === 'ContentItemSelectionRequest') && !empty($config->lti_toolurl_ContentItemSelectionRequest)) {
//        $endpoint = $config->lti_toolurl_ContentItemSelectionRequest;
//    }
    $launchid = "ltilaunch_$messagetype".rand();
    $SESSION->$launchid =
        "{$messagetype},{$foruserid}," . base64_encode($title) . ',' . base64_encode($text);
    $logger->log('\$launchid:', $launchid);
    $logger->log('\$SESSION->\$launchid:', $SESSION->$launchid);

    $endpoint = trim($endpoint);

    $ltihint['launchid'] = $launchid;
    // If SSL is forced make sure https is on the normal launch URL.

    $endpoint = lti_ensure_url_is_https($endpoint);

    $params = array();
    $params['iss'] = $CFG->wwwroot;
    $params['target_link_uri'] = $endpoint;
    $params['login_hint'] = $USER->id;
    $params['lti_message_hint'] = json_encode($ltihint);
    $params['client_id'] = $config->lti_clientid;
    $params['lti_deployment_id'] = block_onlinesurvey_get_lti_typeid();
    $logger->log('returning params:', $params);
    return $params;
}

function block_onlinesurvey_settings_updated($arg) {
    $config = get_config('block_onlinesurvey');
    if (!isset($config->typeid)) {
        $typeid = block_onlinesurvey_create_lti_type();
        set_config('typeid', $typeid, 'block_onlinesurvey');

    }
    block_onlinesurvey_update_lti_type();
    $clientid = block_onlinesurvey_get_clientid($config->typeid);
    set_config('lti_clientid', $clientid, 'block_onlinesurvey');
}

function block_onlinesurvey_get_clientid($typeid) {
    global $DB;
    $clientid = $DB->get_field('lti_types', 'clientid', ['id' => $typeid]);
    return $clientid;
}

function block_onlinesurvey_create_lti_type() {
    global $DB, $USER;
    list($ltitype, $configparams) = block_onlinesurvey_get_params();
    $id = lti_add_type($ltitype, $configparams);
    return $id;
}

function block_onlinesurvey_update_lti_type() {
    global $DB;
    list($ltitype, $configparams) = block_onlinesurvey_get_params();
    lti_update_type($ltitype, $configparams);
    $config = get_config('block_onlinesurvey');
    $publickeyset = $DB->get_field('lti_types_config',  'value', ['typeid' => $config->typeid, 'name' => 'publickeyset']);
    set_config('block_onlinesurvey/lti_publickeyset', $publickeyset);
}

function block_onlinesurvey_get_publickeyset() {
    global $DB;
    $config = get_config('block_onlinesurvey');
    if (isset($config->lti_publickeyset) && !empty($config->lti_publickeyset)) {
        return $config->lti_publickeyset;
    }
    $publickeyset = $DB->get_field('lti_types_config',  'value', ['typeid' => $config->typeid, 'name' => 'publickeyset']);
    set_config('block_onlinesurvey/lti_publickeyset', $publickeyset);
    return $publickeyset;
}

function block_onlinesurvey_get_params() {
    global $USER;
    $ltitype = [
        'name' => 'block_onlinesurvey',
        'baseurl' => '',
        'tooldomain' => '',
        'state' => '1',
        'course' => '0',
        'coursevisible' => '1',
        'ltiversion' => '1.3.0',
        'clientid' => '',
        'toolproxyid' => '',
        'enabledcapability' => '',
        'parameter' => '',
        'icon' => '',
        'secureicon' => '',
        'createdby' => $USER->id,
        'timecreated' => time(),
        'timemodified' => time(),
        'description' => time(),
    ];
    $settings2config = [
        'lti_clientid',
        'lti_keytype',
        'lti_publickey',
        'lti_publickeyset',
        'lti_accesstoken',
        'lti_authrequest',
        'lti_initiatelogin',
        'lti_redirectionuris'
    ];
    $config = get_config('block_onlinesurvey');
    foreach($ltitype as $key => $value) {
        if (isset($config->$key)) {
            $ltitype[$key] = $config->$key;
        }
        if (isset($config->{'lti_'.$key})) {
            $ltitype[$key] = $config->{'lti_'.$key};
        }
    }
    if (isset($config->lti_url)) {
        $ltitype['baseurl'] = $config->lti_url;
    }
    if (isset($config->typeid)) {
        $ltitype['id'] = $config->typeid;
        $type = lti_get_type($config->typeid);
        $urls = block_onlinesurvey_get_tool_type_urls($type);
        set_config('lti_publickeyset', $urls['publickeyset'], 'block_onlinesurvey');
        set_config('lti_authrequest', $urls['authrequest'], 'block_onlinesurvey');
        set_config('lti_accesstoken', $urls['accesstoken'], 'block_onlinesurvey');
        $config->lti_publickeyset = $urls['publickeyset'];
        $config->lti_authrequest = $urls['authrequest'];
        $config->lti_accesstoken = $urls['accesstoken'];
    }
    $configparams = [];
    foreach($settings2config as $key) {
        if (isset($config->$key)) {
            $configparams[$key] = $config->$key;
            $configkeyname = str_replace('lti_', '', $key);
            $configparams[$configkeyname] = $config->$key;
        }
    }

    $ltitype = (object)$ltitype;
    $configparams = (object)$configparams;
    return [$ltitype, $configparams];
}

function block_onlinesurvey_get_lti_typeid() {
    return get_config('block_onlinesurvey', 'typeid');
}

function block_onlinesurvey_get_lti_type() {
    $typeid = block_onlinesurvey_get_lti_typeid();
    if (empty($typeid)) {
        return null;
    }
    return lti_get_type($typeid);
}

function block_onlinesurvey_get_lti_type_config() {
    global $DB;
    $typeid = block_onlinesurvey_get_lti_typeid();
    if (empty($typeid)) {
        return null;
    }
    $config = $DB->get_records('lti_types_config', ['typeid' => $typeid], '', 'name,value');
    $return = [];
    foreach($config as $key => $value) {
        $return[$key] = $value->value;
    }
    return $return;
}
function block_onlinesurvey_get_launch_config() {
    $config = get_config("block_onlinesurvey");
    if (empty($config->lti_publickeyset)) {
        $config->lti_publickeyset = block_onlinesurvey_get_publickeyset();
    }
    if (empty($config->lti_accesstoken)) {
        $config->lti_accesstoken = block_onlinesurvey_get_accesstoken($config->typeid);
    }
    if (empty($config->lti_authrequest)) {
        $config->lti_authrequest = block_onlinesurvey_get_authrequest($config->typeid);
    }
    if (empty($config->lti_clientid)) {
        $config->lti_clientid = block_onlinesurvey_get_clientid($config->typeid);
    }
    if (empty($config->lti_deploymentid)) {
        $config->lti_deploymentid = $config->typeid;
    }
    return $config;
}
function block_onlinesurvey_get_accesstoken($typeid) {
    $type = lti_get_type($typeid);
    $urls = block_onlinesurvey_get_tool_type_urls($type);
    set_config('lti_accesstoken', $urls['accesstoken'], 'block_onlinesurvey');
    return $urls['accesstoken'];
}
function block_onlinesurvey_get_authrequest($typeid) {
    $type = lti_get_type($typeid);
    $urls = block_onlinesurvey_get_tool_type_urls($type);
    set_config('lti_authrequest', $urls['authrequest'], 'block_onlinesurvey');
    return $urls['authrequest'];
}

/**
 * Returns the icon and edit urls for the tool type and the course url if it is a course type.
 *
 * @param stdClass $type The tool type
 *
 * @return array The urls of the tool type
 */
function block_onlinesurvey_get_tool_type_urls(\stdClass $type) {
    $urls = array(
        'icon' => get_tool_type_icon_url($type),
        'edit' => get_tool_type_edit_url($type),
    );

    $url = new moodle_url('/blocks/onlinesurvey/certs.php');
    $urls['publickeyset'] = $url->out();
    $url = new moodle_url('/blocks/onlinesurvey/token.php');
    $urls['accesstoken'] = $url->out();
    $url = new moodle_url('/blocks/onlinesurvey/auth.php');
    $urls['authrequest'] = $url->out();

    return $urls;
}

function block_onlinesurvey_get_dummy_request() {
    $dummy = [
        "user_id"=>"211",
        "lis_person_sourcedid"=>"",
        "roles"=>"Learner",
        "context_id"=>"145",
        "context_label"=>"evasys",
        "context_title"=>"Evasys-Test",
        "lti_message_type"=>"basic-lti-launch-request",
        "resource_link_title"=>"Evasys Online Survey",
        "resource_link_description"=>"",
        "resource_link_id"=>"1",
        "context_type"=>"CourseSection",
        "lis_course_section_sourcedid"=>"",
        "lis_result_sourcedid"=>
            '{"data":{"instanceid":"1","userid":"211","typeid":"2","launchid":30951083},"hash":"1cd07f2d4c2ca3f4fa9b2b5188a0f4c70f50c1eac7c2fa9d30dd4354ed409822"}',
        "lis_outcome_service_url"=>"https://lms.iconnewmedia.de/mod/lti/service.php",
        "lis_person_name_given"=>"Johnson",
        "lis_person_name_family"=>"JEXT0001",
        "lis_person_name_full"=>"Johnson JEXT0001",
        "ext_user_username"=>"tjohnson@example.com",
        "lis_person_contact_email_primary"=>"tjohnson@example.com",
        "launch_presentation_locale"=>"de",
        "ext_lms"=>"moodle-2",
        "tool_consumer_info_product_family_code"=>"moodle",
        "tool_consumer_info_version"=>"2022112805",
        "oauth_callback"=>"about:blank",
        "lti_version" => "1.3.0",
        "tool_consumer_instance_guid" => "f2064102633512210d43340631a1d219",
        "tool_consumer_instance_name" => "ICON Trainings",
        "tool_consumer_instance_description" => "ICON Trainings",
        "launch_presentation_document_target" => "iframe",
        "launch_presentation_return_url" => "https://lms.iconnewmedia.de/mod/lti/return.php?course=145&launch_container=2&instanceid=1&sesskey=HLB3XBRtWu"
    ];
    return $dummy;
}
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
 * Plugin "Evaluations (EvaSys)"
 *
 * @package    block_onlinesurvey
 * @copyright  2018 Soon Systems GmbH on behalf of Electric Paper Evaluationssysteme GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/*************************/
/* General.
/*************************/

$string['pluginname'] = 'Evaluations (EvaSys)';
$string['lti'] = 'LTI';
$string['soap'] = 'SOAP';


/*************************/
/* Appearance settings.
/*************************/

$string['setting_heading_appearance'] = 'Appearance';
$string['setting_heading_appearance_desc'] = 'The settings in this section define how the EvaSys block will be displayed.';

$string['setting_blocktitle'] = 'Title';
$string['setting_blocktitle_desc'] = 'The text entered here is used as the block title.';

$string['setting_presentation'] = 'Display mode';
$string['setting_presentation_desc'] = 'In compact mode, the EvaSys Block displays the number of open surveys by means of a graphic. In this mode, an enlarged list view can be opened as soon as the user has at least one open survey by clicking on the graphic.<br />In detailed mode, the EvaSys Block displays the list of available surveys directly. In this mode, but only when using a SOAP connection, an enlarged list view can be opened as soon as the user has at least one open survey by clicking a button below the list.';
$string['setting_presentation_brief'] = 'Compact';
$string['setting_presentation_detailed'] = 'Detailed';

$string['setting_survey_hide_empty'] = 'Hide empty block';
$string['setting_survey_hide_empty_desc'] = 'If activated, the EvaSys block is hidden when the user has no surveys. If it is not activated, in the compact view a graphic with the text “No open evaluations available” is displayed and in the detailed view an empty list is presented.<br /><em>Please note: If the LTI template you are using is configured in a way that participants are allowed to see and/or access results for surveys they have taken part in, you may not want to hide the block. Otherwise, the participants would no longer be able to access the results.</em>';

$string['setting_offer_zoom'] = 'Always offer enlarged list view';
$string['setting_offer_zoom_desc'] = 'If activated, the user will always be able to open the enlarged list view. If not activated, the use will only be able to open the enlarged list view if he has open surveys.<br /><em>Please note: If the LTI template you are using is configured in a way that participants are allowed to see and/or access results for surveys they have taken part in, you will want to enable this setting. Otherwise, the participants would no longer be able to access the results.</em>';

$string['setting_show_spinner'] = 'Show spinner';
$string['setting_show_spinner_desc'] = 'If activated, a spinner icon will be shown in the block until the open surveys are loaded from EvaSys.';

$string['setting_survey_show_popupinfo'] = 'Pop-up info active';
$string['setting_survey_show_popupinfo_desc'] = 'If activated, a pop-up with an information about open online surveys (if existing) is displayed every time a student logs in to Moodle.';

$string['setting_survey_popupinfo_title'] = 'Pop-up title';
$string['setting_survey_popupinfo_title_desc'] = 'If needed, the title of the pop-up can be modified with this setting.';
$string['setting_survey_popupinfo_title_default'] = 'Open evaluations';

$string['setting_survey_popupinfo_content'] = 'Pop-up content';
$string['setting_survey_popupinfo_content_desc'] = 'If needed, the content which is presented in the pop-up can be modified with this setting.';
$string['setting_survey_popupinfo_content_default'] = '<p>Dear student,</p>
<p>there are currently one or more open online surveys available for the courses you have visited. Your participation helps us improve our offers.<br />
The survey links are displayed in the block "Evaluations".</p>
<p>Thank you for your support!<br />
Your evaluation team</p>';


/*************************/
/* Communication settings.
/*************************/

$string['setting_heading_communication'] = 'Communication';
$string['setting_heading_communication_desc'] = 'The settings in this section define how the EvaSys block will communicate with EvaSys.';

$string['setting_communication_interface'] = 'Communication protocol';
$string['setting_communication_interface_desc'] = 'Here you can define whether Moodle should communicate with EvaSys via SOAP or LTI. <br /><em>Depending on the communication protocol selected here, please do your further settings in the corresponding protocol section below.</em>';

$string['setting_useridentifier'] = 'User identifier';
$string['setting_useridentifier_desc'] = 'Select whether a user\'s email address or username should be used as unique user identifier.';

$string['setting_customfieldnumberinevasys'] = 'Custom field in EvaSys';
$string['setting_customfieldnumberinevasys_desc'] = 'If the username is selected as user identifier, one of the first three custom fields in EvaSys can be used for authentication.<br /><em>Please note: This setting is only relevant for learners. If you decide to use the username for instructors, the username must be stored in EvaSys in the field "External ID" of the user settings.</em>';
$string['setting_customfieldnumber'] = 'Custom field No.';

$string['setting_survey_timeout'] = 'Connection timeout';
$string['setting_survey_timeout_desc'] = 'Maximum response time (in seconds) of the EvaSys server. If the EvaSys server didn\'t answer within this time, the request is aborted and the surveys are not shown to the user.';


/*************************/
/* SOAP settings.
/*************************/

$string['setting_heading_soap'] = 'SOAP settings';
$string['setting_heading_soap_desc'] = 'The settings in this section define how the EvaSys block will communicate with EvaSys.<br /><em>These settings are only required if you selected "SOAP" in the "Communication protocol" setting.</em>';

$string['setting_survey_server'] = 'EvaSys SOAP WSDL URL';
$string['setting_survey_server_desc'] = 'URL of the web service description file on the EvaSys server (https://[SERVERNAME]/evasys/services/soapserver-v61.wsdl).<br /><em>Please note: If EvaSys is operated with several servers (dual server option), the backend server on which users and administrators work, must be specified here. This prevents a too high load on the online survey server.</em>';

$string['setting_survey_login'] = 'EvaSys SOAP path for online surveys';
$string['setting_survey_login_desc'] = 'URL of the EvaSys online survey login (https://[SERVERNAME]/evasys/).';

$string['setting_survey_user'] = 'EvaSys SOAP username';
$string['setting_survey_user_desc'] = 'User name of the EvaSys SOAP user.';

$string['setting_survey_pwd'] = 'EvaSys SOAP password';
$string['setting_survey_pwd_desc'] = 'Password of the EvaSys SOAP user.';

$string['setting_soap_request_eachtime'] = 'Request SOAP data on every rendering';
$string['setting_soap_request_eachtime_desc'] = 'If activated, the data which is rendered in the EvaSys block will be requested from EvaSys each time the block is rendered. If not activated, the data is only requested once per session (i.e. only once a user logged into Moodle).';


/*************************/
/* LTI settings.
/*************************/

$string['setting_heading_lti'] = 'LTI settings';
$string['setting_heading_lti_desc'] = 'The settings in this section define how the EvaSys block will communicate with EvaSys.<br /><em>These settings are only required if you selected "LTI" in the "Communication protocol" setting.</em>';

$string['setting_survey_lti_url'] = 'EvaSys LTI provider URL';
$string['setting_survey_lti_url_desc'] = 'URL of the LTI provider PHP file on the EvaSys server (https://[SERVERNAME]/customer/lti/lti_provider.php).';

$string['setting_survey_lti_password'] = 'EvaSys LTI password';
$string['setting_survey_lti_password_desc'] = 'Password of the EvaSys LTI interface.';

$string['setting_lti_customparameters'] = 'EvaSys LTI Custom parameter';
$string['setting_lti_customparameters_desc'] = 'Here the custom parameters are stored, which can be used to define settings for displaying the surveys, e.g. whether the student view should also display completed surveys (learner_show_completed_surveys=1) or whether the reports of the surveys can also be called up in the instructor view (instructor_show_report=1). Each parameter has to be added on a separate line. For detailed information on the available parameters, please consult the EvaSys LTI manual.';

$string['setting_lti_instructormapping'] = 'LTI Role mapping "Instructor"';
$string['setting_lti_instructormapping_desc'] = 'Defines which Moodle roles should correspond to the LTI role "Instructor" who will see the EvaSys block content as instructors.';

$string['setting_lti_learnermapping'] = 'LTI Role mapping "Learner"';
$string['setting_lti_learnermapping_desc'] = 'Defines which Moodle roles should correspond to the LTI role "Learner" who will see the EvaSys block content as students.';


/*************************/
/* Expert settings.
/*************************/

$string['setting_heading_expert'] = 'Expert settings';
$string['setting_heading_expert_desc'] = 'The settings in this section normally don\'t need any modification and are provided for special usage scenarios.';

$string['setting_survey_debug'] = 'Debug mode';
$string['setting_survey_debug_desc'] = 'If activated, debugging and error messages are shown within the EvaSys block.';

$string['setting_additionalcss'] = 'Additional CSS for iframe';
$string['setting_additionalcss_desc'] = 'Here, you can add CSS code which will be added to the page which is loaded in the EvaSys block. You can use this setting to re-style the EvaSys block content according to your needs.<br /><em>Please note: This setting is used in compact mode for LTI and SOAP connections as well as in detailed mode for SOAP connections. It is not used in detailed mode for LTI connections - if you need to add custom styles in this mode, please change your LTI template in EvaSys.</em>';

$string['setting_lti_regex_learner'] = 'LTI - Learner regular expression';
$string['setting_lti_regex_learner_desc'] = 'Regular expression which searches the content of the LTI-Response for open online surveys. This only needs to be adjusted if customized templates have been created or modified in a way that the functions differ from the standard templates.<br /><em>Please note: This setting is only processed if you selected "LTI" in the "Communication protocol" setting.</em>';

$string['setting_lti_regex_instructor'] = 'LTI - Instructor regular expression';
$string['setting_lti_regex_instructor_desc'] = 'Regular expression which searches the content of the LTI-Response for open online surveys. This only needs to be adjusted if customized templates have been created or modified in a way that the functions differ from the standard templates.<br /><em>Please note: This setting is only processed if you selected "LTI" in the "Communication protocol" setting.</em>';


/*************************/
/* Capabilities.
/*************************/

$string['onlinesurvey:addinstance'] = 'Add instance of the Evaluations (EvaSys) block';
$string['onlinesurvey:myaddinstance'] = 'Add instance of the Evaluations (EvaSys) block to my page';
$string['onlinesurvey:view'] = 'View Evaluations (EvaSys) block';
$string['onlinesurvey:view_debugdetails'] = 'View debug details';


/*************************/
/* Block content.
/*************************/

$string['surveys_exist'] = 'Open surveys available';
$string['surveys_exist_not'] = 'No open surveys available';
$string['allsurveys'] = 'All surveys';
$string['zoomsurveylist'] = 'Zoom survey list';


/*************************/
/* Block error messages.
/*************************/

$string['error_config_not_accessible'] = 'Configuration not accessible';
$string['error_debugmode_missing_capability'] = 'The block is in debug mode. You do not have permission to view content.';
$string['error_lti_learnermapping_missing'] = 'Learner role mapping missing';
$string['error_lti_password_missing'] = 'LTI Consumer key missing';
$string['error_lti_settings_error'] = 'LTI settings error';
$string['error_lti_url_missing'] = 'URL for LTI provider missing';
$string['error_occured'] = '<b>An error has occured:</b><br />{$a}<br />';
$string['error_soap_settings_error'] = 'SOAP settings error';
$string['error_survey_curl_timeout_msg'] = 'The surveys could not be queried.';
$string['error_survey_login_missing'] = 'Path for online surveys missing';
$string['error_survey_pwd_missing'] = 'SOAP password missing';
$string['error_survey_server_missing'] = 'URL for EvaSys server missing';
$string['error_survey_user_missing'] = 'SOAP user missing';
$string['error_userid_not_found'] = 'User ID not found';
$string['error_warning_message'] = '<b>Warning:</b><br />{$a}<br />';
$string['error_wsdl_namespace'] = 'WSDL namespace parse error<br />';


/*************************/
/* Privacy.
/*************************/

$string['privacy:metadata:block_onlinesurvey'] = 'The EvaSys block plugin does not store any personal data, but does transmit user data from Moodle to the connected EvaSys instance.';
$string['privacy:metadata:block_onlinesurvey:email'] = 'The user\'s email sent to EvaSys to check for existing surveys.';
$string['privacy:metadata:block_onlinesurvey:username'] = 'The user\'s username value sent to EvaSys to check for existing surveys.';


/*************************/
/* Misc.
/*************************/

$string['setting_blocktitle_multilangnote'] = 'You can define more than one language (e.g. English and German) by using the Moodle multilanguage filter syntax (see https://docs.moodle.org/en/Multi-language_content_filter for details).';

/*************************/
/* Update notices.
/*************************/

$string['upgrade_notice_2020010900'] = 'The recommended version for the Evasys SOAP API was changed from version 51 to version 61. Thus, the plugin settings where automatically modified during the plugin update.<br />The Evasys SOAP WSDL URL was up to now: {$a->old}<br />The Evasys SOAP WSDL URL is now: {$a->new}<br />Please verify that the automatically modified URL is correct.';

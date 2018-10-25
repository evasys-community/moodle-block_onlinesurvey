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

$string['pluginname'] = 'Evaluations (EvaSys)';

// settings page - general
$string['blocktitle'] = 'Title';
$string['blocktitle_description'] = '';

$string['communication_interface'] = 'Communication channel';
$string['communication_interface_description'] = 'Moodle and EvaSys can communicate via LTI or via SOAP. Depending on the communication channel selected here, please do your further settings in the corresponding section below.';
$string['soap'] = 'SOAP';
$string['lti'] = 'LTI';

// #8984
$string['presentation'] = 'Display mode';
$string['presentation_description'] = 'In compact mode, the EvaSys Block displays the number of open surveys by means of a graphic. In detailed mode, a list of available surveys is displayed. In both modes, an enlarged list view can be opened upon mouse click.';
$string['presentation_brief'] = 'compact';
$string['presentation_detailed'] = 'detailed';
// END #8984

// #8977
$string['survey_hide_empty'] = 'Hide empty block';
$string['survey_hide_empty_description'] = 'If activated, the EvaSys block is hidden when the user has no open surveys.';
// END #8977

$string['useridentifier'] = 'User Identifier';
$string['useridentifier_description'] = 'You can either transmit the user login name or email address as unique identifier.';

$string['customfieldnumberinevasys'] = 'Custom field in EvaSys';
$string['customfieldnumberinevasys_description'] = 'When using the login name as identifier you can specifiy, which of the first three custom fields in EvaSys should be used for authentication.';
$string['customfieldnumber'] = 'Custom field No.';

$string['survey_show_popupinfo'] = 'Pop-up-Info active';
$string['survey_show_popupinfo_description'] = 'If activated, a pop-up message is displayed upon login of a user, giving notice of open surveys.';

$string['survey_timeout'] = 'Connection timeout in seconds';
$string['survey_timeout_description'] = '';

$string['survey_debug'] = 'DEBUG';
$string['survey_debug_description'] = '';

$string['additionalcss'] = 'Additional CSS for iframe';
$string['additionalcss_description'] = 'Content here will be added as CSS to the bottom of HEAD in iframe.';
// END: settings page - general

// settings page - SOAP
$string['generalheadingsoap'] = 'SOAP';
$string['soap_general_information'] = 'The following information is required only if you selected "SOAP" for communication.';

$string['survey_server'] = 'EvaSys server (SOAP)';
$string['survey_server_description'] = '';

$string['survey_login'] = 'EvaSys path for online surveys (SOAP)';
$string['survey_login_description'] = '';

$string['survey_user'] = 'EvaSys SOAP user';
$string['survey_user_description'] = '';

$string['survey_pwd'] = 'EvaSys SOAP password';
$string['survey_pwd_description'] = '';

$string['soap_request_eachtime'] = 'SOAP request at pageview';
$string['soap_request_eachtime_description'] = 'If activated, the content of the EvaSys Block will be updated each time the user opens the starting page. If not activated, the block is only updated upon login / at the beginning of the session.';
// END: settings page - SOAP

// settings page - LTI
$string['generalheadinglti'] = 'LTI';
$string['lti_general_information'] = 'The following information is required only if you selected "LTI" for communication.';

$string['survey_lti_url'] = 'URL of the LTI Provider';
$string['survey_lti_url_description'] = '';

// "survey_lti_resourcekey" currently not used -> kept for future
$string['survey_lti_resourcekey'] = 'Consumer key';
$string['survey_lti_resourcekey_description'] = '';

$string['survey_lti_password'] = 'LTI password';
$string['survey_lti_password_description'] = '';

$string['lti_customparameters'] = 'Custom parameters';
$string['lti_customparameters_description'] = 'Custom parameters are settings used by the tool provider. For example, a custom parameter may be used to display
a specific resource from the provider. Each parameter should be entered on a separate line using a format of "name=value"; for example, "learner_show_completed_surveys=1". For further information please refer to the EvaSys LTI Manual.';

// lti_regard_coursecontext is not yet supported by EvaSys LTI provider -> kept for future
$string['regard_coursecontext'] = 'Consider course context';
$string['regard_coursecontext_description'] = 'Consider course context: If selected, only surveys of the current course are shown.';

$string['lti_instructormapping'] = 'Role mapping "Instructor"';
$string['lti_instructormapping_description'] = 'Here you can define which Moodle roles shall be mapped on the LTI role "instructor".';

$string['lti_learnermapping'] = 'Role mapping "Learner"';
$string['lti_learnermapping_description'] = 'Here you can define which Moodle roles shall be mapped on the LTI role "learner".';

$string['lti_regex_learner'] = 'Learner regular expression';
$string['lti_regex_learner_description'] = 'Regular expression to search for open online surveys for "learners" in the LTI result.';

$string['lti_regex_instructor'] = 'Instructor regular expression';
$string['lti_regex_instructor_description'] = 'Regular expression to search for open online surveys for "instructor" in the LTI result.';
// END: settings page - LTI

// capabilities
$string['onlinesurvey:addinstance'] = 'Add instance of the Evaluations (EvaSys) block';
$string['onlinesurvey:myaddinstance'] = 'Add instance of the Evaluations (EvaSys) block to my page';
$string['onlinesurvey:view'] = 'View Evaluations (EvaSys) block';
$string['onlinesurvey:view_debugdetails'] = 'View debug details';
// END: capabilities

// Block content
$string['tech_error'] = 'A technical problem occured while connecting to EvaSys.<p>';
$string['conn_works'] = 'Connection to EvaSys server tested successfully.<p>';
// #8977
$string['no_surveys'] = 'No open surveys available';
$string['surveys_exist'] = 'Open surveys available';
// END #8977
$string['popupinfo_dialog_title'] = 'Open evaluations';
$string['popupinfo'] = 'Dear student,<br />
<br />
there are currently one or more open online surveys available for the courses you have visited. Your participation helps us improve our offers.<br />
The survey links are displayed in the block "Evaluations". <br />
<br />
Thank you for your support!<br />
Your evaluation team';

$string['survey_list_header'] = '';

$string['soap_settings_error'] = 'SOAP settings error';
$string['survey_server_missing'] = 'URL for EvaSys server missing';
$string['survey_login_missing'] = 'Path for online surveys missing';
$string['survey_user_missing'] = 'SOAP user missing';
$string['survey_pwd_missing'] = 'SOAP password missing';

$string['lti_settings_error'] = 'LTI settings error';
$string['lti_url_missing'] = 'URL for LTI provider missing';
$string['lti_resourcekey_missing'] = 'Consumer key missing';
$string['lti_password_missing'] = 'LTI Consumer key missing';
$string['lti_learnermapping_missing'] = 'Learner role mapping missing';
$string['userid_not_found'] = 'User ID not found';
$string['config_not_accessible'] = 'Configuration not accessible';
$string['error_occured'] = '<b>An error has occured:</b><br />{$a}<br />';
$string['warning_message'] = '<b>Warning:</b><br />{$a}<br />';
$string['wsdl_namespace'] = 'WSDL namespace parse error<br />';

$string['debugmode_missing_capability'] = 'The block is in debug mode. You do not have permission to view content.';
// END: Block content

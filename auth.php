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
 * This file responds to a login authentication request
 *
 * @package    mod_lti
 * @copyright  2019 Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');
require_once(__DIR__ . '/locallib.php');
global $_POST, $_SERVER;

if (!isloggedin() && empty($_POST['repost'])) {
    header_remove("Set-Cookie");
    $PAGE->set_pagelayout('popup');
    $PAGE->set_context(context_system::instance());
    $output = $PAGE->get_renderer('mod_lti');
    $page = new \mod_lti\output\repost_crosssite_page($_SERVER['REQUEST_URI'], $_POST);
    echo $output->header();
    echo $output->render($page);
    echo $output->footer();
    return;
}
$PAGE->set_context(context_system::instance());
$scope = optional_param('scope', '', PARAM_TEXT);
$responsetype = optional_param('response_type', '', PARAM_TEXT);
$clientid = optional_param('client_id', '', PARAM_TEXT);
$redirecturi = optional_param('redirect_uri', '', PARAM_URL);
$loginhint = optional_param('login_hint', '', PARAM_TEXT);
$ltimessagehintenc = optional_param('lti_message_hint', '', PARAM_TEXT);
$ltimessagehintenc = htmlspecialchars_decode(urldecode($ltimessagehintenc));
$state = optional_param('state', '', PARAM_TEXT);
$responsemode = optional_param('response_mode', '', PARAM_TEXT);
$nonce = optional_param('nonce', '', PARAM_TEXT);
$prompt = optional_param('prompt', '', PARAM_TEXT);
$typeid = block_onlinesurvey_get_lti_typeid();

$ok = !empty($scope) && !empty($responsetype) && !empty($clientid) &&
      !empty($redirecturi) && !empty($loginhint) &&
      !empty($nonce);

if (!$ok) {
    $error = 'invalid_request';
}
$ltimessagehint = json_decode($ltimessagehintenc);
$ok = $ok && isset($ltimessagehint->launchid);
if (!$ok) {
    $error = 'invalid_request';
    $desc = 'No launch id in LTI hint';
}
if ($ok && ($scope !== 'openid')) {
    $ok = false;
    $error = 'invalid_scope';
}
if ($ok && ($responsetype !== 'id_token')) {
    $ok = false;
    $error = 'unsupported_response_type';
}
if ($ok) {
    $launchid = $ltimessagehint->launchid;
    list($messagetype, $foruserid, $titleb64, $textb64) = explode(',', $SESSION->$launchid, 7);
    unset($SESSION->$launchid);
    $config = lti_get_type_type_config($typeid);
    $ok = ($clientid === $config->lti_clientid);
    if (!$ok) {
        $error = 'unauthorized_client';
    }
}
if ($ok && ($loginhint !== $USER->id)) {
    $ok = false;
    $error = 'access_denied';
}

// If we're unable to load up config; we cannot trust the redirect uri for POSTing to.
if (empty($config)) {
    throw new moodle_exception('invalidrequest', 'error');
} else {
    $uris = array_map("trim", explode("\n", $config->lti_redirectionuris));
    if (!in_array($redirecturi, $uris)) {
        throw new moodle_exception('invalidrequest', 'error');
    }
}
if ($ok) {
    if (isset($responsemode)) {
        $ok = ($responsemode === 'form_post');
        if (!$ok) {
            $error = 'invalid_request';
            $desc = 'Invalid response_mode';
        }
    } else {
        $ok = false;
        $error = 'invalid_request';
        $desc = 'Missing response_mode';
    }
}
if ($ok && !empty($prompt) && ($prompt !== 'none')) {
    $ok = false;
    $error = 'invalid_request';
    $desc = 'Invalid prompt';
}
$config = get_config('block_onlinesurvey');
if ($config->presentation == BLOCK_ONLINESURVEY_PRESENTATION_BRIEF) {
    include(__DIR__ . '/auth_compactmode.php');
} else {
    include(__DIR__ . '/auth_detailedmode.php');
}
//$r = '<form action="' . $redirecturi . "\" name=\"ltiAuthForm\" id=\"ltiAuthForm\" " .
//     "method=\"post\" enctype=\"application/x-www-form-urlencoded\">\n";
//if (!empty($params)) {
//    foreach ($params as $key => $value) {
//        $key = htmlspecialchars($key, ENT_COMPAT);
//        $value = htmlspecialchars($value, ENT_COMPAT);
//        $r .= "  <input type=\"hidden\" name=\"{$key}\" value=\"{$value}\"/>\n";
//    }
//}
//$r .= "</form>\n";
//$r .= "<script type=\"text/javascript\">\n" .
//    "//<![CDATA[\n" .
//    "document.ltiAuthForm.submit();\n" .
//    "//]]>\n" .
//    "</script>\n";
//echo $r;

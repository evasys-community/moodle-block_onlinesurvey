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
require_once(__DIR__ . '/classes/logger.php');
require_once(__DIR__ . '/locallib.php');
$logger = new \block_onlinesurvey\Logger('block_onlinesurvey_auth.txt', \block_onlinesurvey\Logger::LEVEL_VERY_VERBOSE);
global $_POST, $_SERVER;
$logger->log('called blocks/onlinesurvey/auth.php', \block_onlinesurvey\Logger::LEVEL_VERBOSE);
if (!isloggedin() && empty($_POST['repost'])) {
    header_remove("Set-Cookie");
    $PAGE->set_pagelayout('popup');
    $PAGE->set_context(context_system::instance());
    $output = $PAGE->get_renderer('mod_lti');
    $page = new \mod_lti\output\repost_crosssite_page($_SERVER['REQUEST_URI'], $_POST);
    echo $output->header();
    echo $output->render($page);
    echo $output->footer();
    $logger->log('in blocks/onlinesurvey/auth.php: not logged in or empty repost');
    $logger->log('is logged in?', isloggedin());
    $logger->log('empty repost?', empty($_POST['repost']));
    return;
}

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
$logger->log('ltimessagehint:', $ltimessagehint, \block_onlinesurvey\Logger::LEVEL_VERBOSE);
$ok = $ok && isset($ltimessagehint->launchid);
if (!$ok) {
    $error = 'invalid_request';
    $desc = 'No launch id in LTI hint';
    $logger->log($error . ' ' . $desc,
        ['scope' => $scope,
        'responsetype' => $responsetype,
        'lti_message_hint' => $ltimessagehintenc,
        'clientid' => $clientid,
        'redirecturi' => $redirecturi,
        'loginhint' => $loginhint,
        'nonce' => $nonce],
        \block_onlinesurvey\Logger::LEVEL_VERBOSE
    );
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
    $logger->log('line ' . __LINE__ . ', launchid:', $launchid);
    $logger->log('line ' . __LINE__ . ', session launchid:', $SESSION->$launchid);
    list($messagetype, $foruserid, $titleb64, $textb64) = explode(',', $SESSION->$launchid, 7);
    $logger->log('got messagetype: ', $messagetype);
    $logger->log('got foruserid: ', $foruserid);
    $logger->log('got titleb64: ', $titleb64);
    $logger->log('got textb64: ', $textb64);
    unset($SESSION->$launchid);
    $config = lti_get_type_type_config($typeid);
    $ok = ($clientid === $config->lti_clientid);
    if (!$ok) {
        $error = 'unauthorized_client';
        $logger->log('clientid didn\'t match. client id given: ', $clientid);
        $logger->log('client id expected: ', $config->lti_clientid);
    }
}
if ($ok && ($loginhint !== $USER->id)) {
    $ok = false;
    $error = 'access_denied';
    $logger->log('access denied for user ' . $USER->id);
    $logger->log('loginhint: ', $loginhint);
}

// If we're unable to load up config; we cannot trust the redirect uri for POSTing to.
if (empty($config)) {
    $logger->log('error, empty config');
    throw new moodle_exception('invalidrequest', 'error');
} else {
    $uris = array_map("trim", explode("\n", $config->lti_redirectionuris));
    if (!in_array($redirecturi, $uris)) {
        $logger->log('error, redirecturi not in valid uris');
        $logger->log('redirecturi:', $redirecturi);
        $logger->log('uris:', $uris);
        throw new moodle_exception('invalidrequest', 'error');
    }
}
if ($ok) {
    if (isset($responsemode)) {
        $ok = ($responsemode === 'form_post');
        if (!$ok) {
            $error = 'invalid_request';
            $desc = 'Invalid response_mode';
            $logger->log('error: ' . $error . ' - ' . $desc);
        }
    } else {
        $ok = false;
        $error = 'invalid_request';
        $desc = 'Missing response_mode';
        $logger->log('error: ' . $error . ' - ' . $desc);
    }
}
if ($ok && !empty($prompt) && ($prompt !== 'none')) {
    $ok = false;
    $error = 'invalid_request';
    $desc = 'Invalid prompt';
    $logger->log('error: ' . $error . ' - ' . $desc);
}
if (isset($state)) {
    $logger->log('got state from optional_param, setting $SESSION->state to the same value:', $state);
    $logger->log('but did we also have a conficting value in $SESSION->state?:', $SESSION->state);
    $SESSION->state = $state;
} else {
    $params['state'] = $SESSION->state;
}
if ($ok) {
    $config = get_config('block_onlinesurvey');
    $logger->log('all okay, about to call require_login');
    require_login();
//    if ($id) {
        $context = context_system::instance();
        $lti = get_config('block_onlinesurvey');
        $logger->log('about to call block_onlinesurvey_lti_get_launch_data');
        list($endpoint, $params) = block_onlinesurvey_lti_get_launch_data($lti, $nonce, $messagetype, $foruserid);
        $params['state'] = $state;
        setcookie('state', $state, ['samesite' => 'None']);
        setcookie('lti1p3_' . $state, $state, ['samesite' => 'None', 'path' => '/']);
        $logger->log('called block_onlinesurvey_lti_get_launch_data and got endpoint:', $endpoint);
        $logger->log('and got params:', $params);
   /* } else {
        require_login($course);
        $context = context_course::instance($courseid);
        require_capability('moodle/course:manageactivities', $context);
        require_capability('mod/lti:addcoursetool', $context);
        // Set the return URL. We send the launch container along to help us avoid frames-within-frames when the user returns.
        $returnurlparams = [
            'course' => $courseid,
            'id' => $typeid,
            'sesskey' => sesskey()
        ];
        $returnurl = new \moodle_url('/mod/lti/contentitem_return.php', $returnurlparams);
        // Prepare the request.
        $title = base64_decode($titleb64);
        $text = base64_decode($textb64);
        $request = lti_build_content_item_selection_request($typeid, $course, $returnurl, $title, $text,
                                                            [], [], false, true, false, false, false, $nonce);
        $endpoint = $request->url;
        $params = $request->params;
    }*/
} else {
    $params['error'] = $error;
    if (!empty($desc)) {
        $params['error_description'] = $desc;
    }
    $logger->log('not ok, got error and error:', $error);
    $logger->log('and error description:', $desc);
}

$params['lti1p3_' . $SESSION->state] = $SESSION->state;
if (isset($SESSION->state)) {
    setcookie('lti1p3_' . $SESSION->state, $SESSION->state);
}
unset($SESSION->lti_message_hint);
$r = '<form action="' . $redirecturi . "\" name=\"ltiAuthForm\" id=\"ltiAuthForm\" " .
     "method=\"post\" enctype=\"application/x-www-form-urlencoded\">\n";
if (!empty($params)) {
    foreach ($params as $key => $value) {
        $key = htmlspecialchars($key, ENT_COMPAT);
        $value = htmlspecialchars($value, ENT_COMPAT);
        $r .= "  <input type=\"hidden\" name=\"{$key}\" value=\"{$value}\"/>\n";
    }
}
$r .= "</form>\n";
$r .= "<script type=\"text/javascript\">\n" .
    "//<![CDATA[\n" .
    "document.ltiAuthForm.submit();\n" .
    "//]]>\n" .
    "</script>\n";
echo $r;

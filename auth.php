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
if (isset($state)) {
    $SESSION->lti_state = $state;
} else {
    $state = $SESSION->lti_state;
}

if ($ok) {
    $config = get_config('block_onlinesurvey');
    require_login();
    $context = context_system::instance();
    $lti = get_config('block_onlinesurvey');

    list($endpoint, $params) = block_onlinesurvey_lti_get_launch_data($lti, $nonce, $messagetype, $foruserid);
    die('so far so good ' . __FILE__ . ' ' . __LINE__); // ICUNDO!
    $params['state'] = $state;
    setcookie('state', $state, ['samesite' => 'None']);
    setcookie('lti1p3_' . $state, $state, ['samesite' => 'None', 'path' => '/']);
} else {
    $params['error'] = $error;
    if (!empty($desc)) {
        $params['error_description'] = $desc;
    }
}

$params['lti1p3_' . $SESSION->lti_state] = $SESSION->lti_state;

if (isset($SESSION->lti_state)) {
    setcookie('lti1p3_' . $SESSION->lti_state, $SESSION->lti_state);
    block_onlinesurvey_remove_outdated_cookies($SESSION->lti_state);
}
unset($SESSION->lti_message_hint);
$config = block_onlinesurvey_get_launch_config();

$return = block_onlinesurvey_lti_post_launch_html_curl($params, $redirecturi, $config);

if ($config->presentation == BLOCK_ONLINESURVEY_PRESENTATION_BRIEF) {
    if (isset($SESSION->modalzoom)) {
        $modalzoom = $SESSION->modalzoom;
        unset($SESSION->modalzoom);
    } else {
        $modalzoom = optional_param('modalZoom', 0, PARAM_INT);
    }
    if (!$modalzoom) {
        $return = block_onlinesurvey_get_summary($return, $config, $modalzoom, $foruserid);
        $return .= '<link rel="stylesheet" href="' .  $CFG->wwwroot . '/blocks/onlinesurvey/style/block_onlinesurvey_iframe_compact.css">';
        $return .= '<link rel="stylesheet" href="' . $CFG->wwwroot . '/blocks/onlinesurvey/lib/fonts/font-awesome-4.7.0/css/font-awesome.min.css">';
    }
}
if ($modalzoom || $config->presentation != BLOCK_ONLINESURVEY_PRESENTATION_BRIEF) {
    $pathinfo = pathinfo($config->lti_url);
    $base = $pathinfo['dirname'];
    if (strpos($return, '<head>') !== false) {
//        $return = str_replace('<head>', '<head><base href="' . $base . '/" />', $return); // ICUNDO
    } else {
//        $return = str_replace('<html>', '<html><head><base href="' . $base . '/" /></head>', $return); // ICUNDO
    }
    $return .= '<script>
// make iframe height match its content
        var block_onlinesurvey_iframe_height = document.documentElement.offsetHeight + 40; 
        window.parent.parent.document.getElementById(\'block_onlinesurvey_contentframe\').style.height = block_onlinesurvey_iframe_height + \'px\';
</script>';
}
file_put_contents($CFG->dataroot . '/block_onlinesurvey_auth_output.txt', $return, FILE_APPEND); // ICUNDO!
send_headers('text/html; charset=utf-8'); // ICUNDO!

echo "TEST!"; // ICUNDO
//echo $return; // ICUNDO!
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

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
global $ok, $state, $DB, $CFG, $courseid, $id, $nonce, $messagetype, $foruserid, $typeid, $titleb64, $textb64, $error, $SESSION, $redirecturi;
file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
if (isset($state)) {
    $SESSION->lti_state = $state;
} else {
    $state = $SESSION->lti_state;
}
if ($ok) {
    file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
    $config = get_config('block_onlinesurvey');
    require_login();
    $context = context_system::instance();
    $lti = get_config('block_onlinesurvey');
    list($endpoint, $params) = block_onlinesurvey_lti_get_launch_data($lti, $nonce, $messagetype, $foruserid);
    $params['state'] = $state;
    setcookie('state', $state, ['samesite' => 'None', 'path' => '/', 'expires' => time() + 30 * 24 * 3600]);
    setcookie('lti1p3_' . $state, $state, ['samesite' => 'None', 'path' => '/', 'expires' => time() + 30 * 24 * 3600]);
    file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
} else {
    $params['error'] = $error;
    file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\nERROR: " . $error . "\r\n", FILE_APPEND);
    if (!empty($desc)) {
        $params['error_description'] = $desc;
    }
}
file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
$params['lti1p3_' . $SESSION->lti_state] = $SESSION->lti_state;
if (isset($SESSION->lti_state)) {
    file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
    setcookie('lti1p3_' . $SESSION->lti_state, $SESSION->lti_state, time() + 30 * 24 * 3600, '/');
    block_onlinesurvey_remove_outdated_cookies($SESSION->lti_state);
    file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
}
file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
unset($SESSION->lti_message_hint);
$config = block_onlinesurvey_get_launch_config();
file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
$return = block_onlinesurvey_lti_post_launch_html_curl($params, $redirecturi, $config, $state);
$modalzoom = 0;
file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
if ($config->presentation == BLOCK_ONLINESURVEY_PRESENTATION_BRIEF) {
    file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
    if (isset($SESSION->modalzoom)) {
        $modalzoom = $SESSION->modalzoom;
        unset($SESSION->modalzoom);
    } else {
        $modalzoom = optional_param('modalZoom', 0, PARAM_INT);
    }
    file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
    if (!$modalzoom) {
        file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
        $return = block_onlinesurvey_get_summary($return, $config, $modalzoom, $foruserid);
        $return .= '<link rel="stylesheet" href="' .  $CFG->wwwroot . '/blocks/onlinesurvey/style/block_onlinesurvey_iframe_compact.css">';
        $return .= '<link rel="stylesheet" href="' . $CFG->wwwroot . '/blocks/onlinesurvey/lib/fonts/font-awesome-4.7.0/css/font-awesome.min.css">';
        file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
    }
}
if ($modalzoom || $config->presentation != BLOCK_ONLINESURVEY_PRESENTATION_BRIEF) {
    file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
    $pathinfo = pathinfo($config->lti_url);
    $base = $pathinfo['dirname'];
    if (strpos($return, '<head>') !== false) {
        $return = str_replace('<head>', '<head><base href="' . $base . '/" />', $return);
    } else {
        $return = str_replace('<html>', '<html><head><base href="' . $base . '/" /></head>', $return);
    }
    if ($modalzoom) {
        $cssselector = '#block_onlinesurvey_custom-modal_contentframe';
    } else {
        $cssselector = '#block_onlinesurvey_contentframe';
    }
    file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
    $return .= '<script>
// make iframe height match its content
 var hassurveys = document.querySelectorAll(\'.cell.survey\').length > 0;
if (hassurveys) {
    window.parent.parent.document.querySelector(\'body\').classList.add(\'evasys_has_surveys\');
}
var content_height = document.documentElement.offsetHeight;
if (content_height > 0) {
    var block_onlinesurvey_iframe_height = content_height + 40;
    window.parent.parent.document.querySelector(\'' . $cssselector . '\').style.height = block_onlinesurvey_iframe_height + \'px\';
}
</script>';
}
file_put_contents($CFG->dataroot . '/lti_logs/auth_compactmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
echo $return;
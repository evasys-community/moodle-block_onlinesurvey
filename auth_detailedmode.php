<?php
global $ok, $DB, $courseid, $id, $nonce, $messagetype, $foruserid, $typeid, $titleb64, $textb64, $error, $SESSION, $redirecturi;
file_put_contents($CFG->dataroot . '/lti_logs/auth_detailedmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
if ($ok) {
    $config = get_config('block_onlinesurvey');
    require_login();
    $context = context_system::instance();
    $lti = get_config('block_onlinesurvey');
    list($endpoint, $params) = block_onlinesurvey_lti_get_launch_data($lti, $nonce, $messagetype, $foruserid);
} else {
    $params['error'] = $error;
    if (!empty($desc)) {
        $params['error_description'] = $desc;
    }
}
file_put_contents($CFG->dataroot . '/lti_logs/auth_detailedmode.txt', "\r\n" . __LINE__ . "\r\n", FILE_APPEND);
if (isset($state)) {
    $params['state'] = $state;
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

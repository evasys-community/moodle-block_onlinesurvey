<?php
namespace block_onlinesurvey;
class Logger {
    function log($message, $obj = null) {
        global $CFG;
        if (!is_null($obj)) {
            ob_start();
            var_dump($obj);
            $message .= "\r\n" . ob_get_clean();
        }
        file_put_contents(
            $CFG->dataroot . '/log_lti.txt',
            "\r\n### " . date('d.m.Y H:i:s') . "\r\n" . $message,
        FILE_APPEND
        );
    }
}
<?php
namespace block_onlinesurvey;
class Logger {
    protected $filename;
    function __construct($filename = '') {
        if (!empty($filename)) {
            $this->filename = $filename;
        } else {
            $this->filename = 'log_lti.txt';
        }
    }
    function log($message, $obj = null) {
        global $CFG;
        if (!is_null($obj)) {
            ob_start();
            var_dump($obj);
            $message .= "\r\n" . ob_get_clean();
        }
        file_put_contents(
            $CFG->dataroot . '/' . $this->filename,
            "\r\n### " . date('d.m.Y H:i:s') . "\r\n" . $message,
        FILE_APPEND
        );
    }
}
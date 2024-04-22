<?php
namespace block_onlinesurvey;
class Logger {
    protected $filename;
    protected $level;
    public const LEVEL_NORMAL = '1';
    public const LEVEL_VERBOSE = '2';
    public const LEVEL_VERY_VERBOSE = '3';
    function __construct($filename = '', $level = self::LEVEL_NORMAL) {
        $this->level = $level;
        if (!empty($filename)) {
            $this->filename = $filename;
        } else {
            $this->filename = 'log_lti.txt';
        }
        $this->log('############################
' . date('d.m.Y H:i:s') . ' - initiated logger from ' .
            debug_backtrace()[1]['file'] . ',' . debug_backtrace()[1]['function'] . ', ' . debug_backtrace()[1]['line'] . '
##########################
');
    }
    function log($message, $obj = null, $level = self::LEVEL_VERY_VERBOSE) {
        global $CFG;
        if ($level > $this->level) {
            return;
        }
        if (!is_null($obj)) {
            ob_start();
            var_dump($obj);
            $message .= "\r\n" . ob_get_clean();
        }
        $message = "------ " . debug_backtrace()[1]['file'] . ',' . debug_backtrace()[1]['function'] . ', ' . debug_backtrace()[1]['line'] . "\r\n" . $message;
        file_put_contents(
            $CFG->dataroot . '/' . $this->filename,
            "\r\n### " . date('d.m.Y H:i:s') . "\r\n" . $message,
        FILE_APPEND
        );
    }
}
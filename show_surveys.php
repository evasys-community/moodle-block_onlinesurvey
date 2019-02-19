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

define('BLOCK_ONLINESURVEY_PRESENTATION_BRIEF', "brief");
define('BLOCK_ONLINESURVEY_PRESENTATION_DETAILED', "detailed");

require_once('../../config.php');

require_once('lib.php');

// Block settings.
$config = get_config("block_onlinesurvey");
$error = '';
$debugmode = false;

$css = array();

if (isset($config)) {

    $connectiontype = $config->connectiontype;
    $debugmode = $config->survey_debug;

    // Session information.
    global $USER;
    if ($moodleuserid = $USER->id) {
        $moodleusername = $USER->username;
        $moodleemail = $USER->email;
        $context = null;
        $course = null;

        $contextid = optional_param('ctxid', 2, PARAM_INT);
        if (!empty($contextid)) {
            $context = context::instance_by_id($contextid);
        }

        $modalzoom = optional_param('modalZoom', 0, PARAM_INT);

        if ($config->presentation == BLOCK_ONLINESURVEY_PRESENTATION_BRIEF && !$modalzoom) {
            $css[] = $CFG->wwwroot.'/blocks/onlinesurvey/style/block_onlinesurvey_iframe_compact.css';
        }

        if ($connectiontype == 'SOAP') {
            $css[] = $CFG->wwwroot.'/blocks/onlinesurvey/style/block_onlinesurvey_iframe_soap.css';
        } else if ($connectiontype == 'LTI') {
            // Nothing to prepare.
        }
    } else {
        $error = get_string('userid_not_found', 'block_onlinesurvey').'<br>';
    }
} else {
    $error = get_string('config_not_accessible', 'block_onlinesurvey');
}

$title = get_string('pluginname', 'block_onlinesurvey');
if (isset($config) && !empty($config)) {

    // Get block title from block setting.
    if (!empty($config->blocktitle)) {
        $context = context_system::instance();
        $PAGE->set_context($context);
        $title = format_string($config->blocktitle);
    }
}

echo '<html>'."\n".
        '<head>'."\n".
        '<title>'.$title.'</title>';
foreach ($css as $file) {
    echo '<link rel="stylesheet" href="' . $file . '">'."\n";
}
if (!empty($config->additionalcss)) {
    echo "\n"."<style>".$config->additionalcss."</style>";
}

echo '</head>'."\n".
        '<body>';

if (!empty($error)) {
    $context = context_system::instance();
    if (has_capability('moodle/site:config', $context)) {
        if ($error) {
            echo  get_string('error_occured', 'block_onlinesurvey', $error);
        }
    } else if ($debugmode) {
        echo  get_string('error_occured', 'block_onlinesurvey', $error);
    }
} else {
    if ($connectiontype == 'SOAP') {
        block_onlinesurvey_get_soap_content($config, $moodleusername, $moodleemail, $modalzoom);
    } else if ($connectiontype == 'LTI') {
        block_onlinesurvey_get_lti_content($config, $context, $course, $modalzoom);
    }
}
echo '</body>';
echo '</html>';

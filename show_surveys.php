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
 * Plugin "Evaluations (evasys)"
 *
 * @package    block_onlinesurvey
 * @copyright  2018 Soon Systems GmbH on behalf of evasys GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->dirroot .'/mod/lti/locallib.php');
$PAGE->set_context(context_system::instance());
if (isset($SESSION->lti_state)) {
    block_onlinesurvey_remove_outdated_cookies($SESSION->lti_state);
}
require_login();
try {
    $systemcontext = context_system::instance();
    require_capability('block/onlinesurvey:view', $systemcontext);
    $action = optional_param('action', '', PARAM_TEXT);
    $foruserid = optional_param('user', 0, PARAM_INT);
    global $USER, $PAGE;

// Block settings.
    $config = block_onlinesurvey_get_launch_config();
    $error = '';

    $css = array();

    if (isset($config)) {

        $connectiontype = $config->connectiontype;
        $debugmode = $config->survey_debug;

        // Session information.
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
                $css[] = ['file' => $CFG->wwwroot . '/blocks/onlinesurvey/style/block_onlinesurvey_iframe_compact.css'];
            }

            if ($connectiontype == 'SOAP') {
                $css[] = ['file' => $CFG->wwwroot . '/blocks/onlinesurvey/style/block_onlinesurvey_iframe_detail_soap.css'];
            }

            $css[] = ['file' => $CFG->wwwroot . '/blocks/onlinesurvey/lib/fonts/font-awesome-4.7.0/css/font-awesome.min.css'];
        } else {
            $error = get_string('error_userid_not_found', 'block_onlinesurvey') . '<br>';
        }
    } else {
        $error = get_string('error_config_not_accessible', 'block_onlinesurvey');
    }
    if ($error) {
        // log the error if you like
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
    $data = [];
    $data['title'] = $title;

    $data['additionalcss'] = $config->additionalcss;

    $bodyclasses = array();
    if (isset($PAGE->theme->name)) {
        $bodyclasses[] = 'theme_' . $PAGE->theme->name;
    }
    if ($modalzoom) {
        $bodyclasses[] = 'zoom_modal';
    } else {
        $bodyclasses[] = 'zoom_block';
    }

    if (!empty($error)) {
        $context = context_system::instance();
        if (has_capability('moodle/site:config', $context) || $debugmode) {
            $data['error'] = $error;
        }
    } else {
        if ($connectiontype == 'SOAP') {
            $data['content'] =  block_onlinesurvey_get_soap_content($config, $moodleusername, $moodleemail, $modalzoom);
        } else if ($connectiontype == 'LTI' || $connectiontype == LTI_VERSION_1P3) {
            if ($connectiontype === LTI_VERSION_1P3) {
                if (!isset($SESSION->lti_initiatelogin_status)) {
                    $msgtype = 'basic-lti-launch-request';
                    if ($action === 'gradeReport') {
                        $msgtype = 'LtiSubmissionReviewRequest';
                    }
                    echo block_onlinesurvey_lti_initiate_login($config, $msgtype, '', '', $foruserid);
                    exit;
                } else {
                    unset($SESSION->lti_initiatelogin_status);
                }
            }
            $data['content'] = block_onlinesurvey_get_lti_content($config, $context, $course, $modalzoom);
        }
    }
    global $OUTPUT, $surveysfound;
    $css[] = ['file' => $CFG->wwwroot . '/blocks/onlinesurvey/style/block_onlinesurvey_modal-zoom.css'];
    $data['css'] = $css;
    $data['script'] = '';
    if ($surveysfound) {
        $bodyclasses[] = 'evasys_has_surveys';
        $data['script'] = '<script type="text/javascript">parent.document.getElementsByTagName("body")[0].classList.add("evasys_has_surveys");</script>';
    } else {
        $bodyclasses[] = 'evasys_no_surveys';
        $data['script'] = '<script type="text/javascript">parent.document.getElementsByTagName("body")[0].classList.add("evasys_no_surveys");</script>';
    }
    $data['bodyclasses'] = implode(' ', $bodyclasses);
    echo $OUTPUT->render_from_template('block_onlinesurvey/show_surveys', $data);
} catch(Exception $e) {
    // nothing here yet - log the exception if you like, or output a message
}
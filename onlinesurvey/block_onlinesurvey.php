<?php
/*
EvaSys Online Surveys - Moodle Block
Copyright (C) 2016  Electric Paper Evaluationssysteme GmbH

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

Contact:
Electric Paper
Evaluationssysteme GmbH
Konrad-Zuse-Allee 13
21337 Lüneburg
Deutschland

E-Mail: info@evasys.de
*/

class block_onlinesurvey extends block_base {
    const SURVEY_URL = 'indexstud.php?type=html&user_tan=';

    private $debugmode;
    private $hideblock;
    private $isconfigured;
    private $warning;
    private $error;
    private $connectionok;

    private $surveyurl;

    private $wsdl;
    private $soapuser;
    private $soappassword;
    private $timeout;
    private $wsdlnamespace;

    private $moodleuserid;
    private $moodleusername;
    private $moodleemail;

    public function init() {
        global $CFG;

        $this->title = get_string('pluginname', 'block_onlinesurvey');

        if (isset($CFG)) {
            // Block settings.
            $this->debugmode = $CFG->block_onlinesurvey_survey_debug == 1;
            $this->hideblock = $CFG->block_onlinesurvey_survey_hide_block == 1;
            $this->surveyurl = $CFG->block_onlinesurvey_survey_login;
            $this->wsdl = $CFG->block_onlinesurvey_survey_server;
            $this->soapuser = $CFG->block_onlinesurvey_survey_user;
            $this->soappassword = $CFG->block_onlinesurvey_survey_pwd;
            $this->timeout = $CFG->block_onlinesurvey_survey_timeout;
            if (!$this->timeout) {
                $this->timeout = 3;
            }

            // Session information.
            global $USER;
            if ($this->moodleuserid = $USER->id) {
                $this->moodleusername = $USER->username;
                $this->moodleemail = $USER->email;

                // Parse wsdlnamespace from the wsdl url.
                preg_match('/\/([^\/]+\.wsdl)$/', $this->wsdl, $matches);

                if (count($matches) == 2) {
                    $this->wsdlnamespace = $matches[1];
                    $this->isconfigured = true;
                } else {
                    $this->isconfigured = false;
                    $this->handle_error("WSDL namespace parse error");
                }
            } else {
                $this->isconfigured = false;
                $this->handle_error("User ID not found");
            }
        } else {
            $this->handle_error("Configuration not accessible");
            $this->isconfigured = false;
        }
    }

    public function get_content() {
        global $SESSION;

        if ($this->moodleuserid && $this->isconfigured) {
            $this->content = new stdClass();

            if (!isset($SESSION->block_onlinesurvey_surveykeys) || $this->debugmode) {
                $SESSION->block_onlinesurvey_surveykeys = $this->get_surveys();
            }

            if ($SESSION->block_onlinesurvey_surveykeys === false && !$this->debugmode) {
                if ($this->hideblock) {
                    $this->content->text = '';
                } else {
                    $this->content->text = get_string('no_surveys', 'block_onlinesurvey');
                }

                return;
            }

            $context = context_system::instance();
            if (has_capability('moodle/site:config', $context)) {
                if ($this->connectionok) {
                    $this->content->text = get_string('conn_works', 'block_onlinesurvey');
                }
            } else if (is_object($SESSION->block_onlinesurvey_surveykeys)) {
                if (!is_array($SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys)) {
                    $SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys = array(
                        $SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys
                    );
                }

                $count = count($SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys);
                if ($count) {
                    foreach ($SESSION->block_onlinesurvey_surveykeys->OnlineSurveyKeys as $surveykey) {
                        $this->content->text .= "<a href=\"{$this->surveyurl}" . self::SURVEY_URL .
                                                "{$surveykey->TransactionNumber}\" target=\"_blank\">".
                                                "{$surveykey->CourseName}</a><br>";
                    }
                }
            }
        }

        $context = context_system::instance();
        if (has_capability('moodle/site:config', $context)) {
            if ($this->debugmode && $this->error && !$this->connectionok) {
                $this->content->text = "<b>An error has occured:</b><br />{$this->error}<br />" . $this->content->text;
            }
        } else if ($this->debugmode && $this->error) {
            $this->content->text = "<b>An error has occured:</b><br />{$this->error}<br />" . $this->content->text;
        }

        if ($this->debugmode && $this->warning) {
            $this->content->text = "<b>Warning:</b><br />{$this->warning}<hr />" . $this->content->text;
        }
    }

    private function get_surveys() {
        try {
            require_once('onlinesurvey_soap_client.php');
            $client = new onlinesurvey_soap_client( $this->wsdl,
                array(
                    'trace' => 1,
                    'feature' => SOAP_SINGLE_ELEMENT_ARRAYS,
                    'connection_timeout' => $this->timeout),
                $this->timeout,
                $this->debugmode
            );

            $header = array(
                'Login' => $this->soapuser,
                'Password' => $this->soappassword
            );

            if (is_object($client)) {
                if ($client->haswarning) {
                    $this->warning = $client->warnmessage;
                }

                $soapheader = new SoapHeader($this->wsdlnamespace, 'Header', $header);
                $client->__setSoapHeaders($soapheader);
            } else {
                $this->handle_error("SOAP client configuration error");
                return false;
            }

            $this->connectionok = true;
            return $client->GetPswdsByParticipant($this->moodleemail);
        } catch (Exception $e) {
            $this->handle_error($e);
            return false;
        }
    }

    private function handle_error($err) {
        if (is_array($err)) {
            // Configuration validation error.
            if (!$err[0]) {
                $this->log_error($err[1]);
            }
        } else if (is_string($err)) {
            // Simple error message.
            $this->log_error($err);
        } else {
            // Error should be an exception.
            $this->log_error($this->pretty_print_exceptions($err));
        }
    }

    public function has_config() {
        return true;
    }

    public function config_save($data) {
        foreach ($data as $name => $value) {
            set_config($name, $value);
        }

        return true;
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function hide_header() {
        return false;
    }

    public function applicable_formats() {
        $context = context_system::instance();
        if (has_capability('moodle/site:config', $context)) {
            return array('all' => true);
        } else {
            return array(
                'all' => false,
                'admin' => true
            );
        }
    }

    private function pretty_print_exceptions($e) {
        $msg = '';
        if (get_class($e) == "SoapFault") {
            $msg = "{$e->faultstring}: {$e->detail}";
        } else {
            $msg = $e->getMessage();
        }

        return $msg;
    }

    private function log_error($error) {
        $this->error = $error;
    }
}

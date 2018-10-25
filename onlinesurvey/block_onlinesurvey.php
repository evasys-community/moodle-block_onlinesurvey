<?php
/*
EvaSys Online Surveys - Moodle Block
Copyright (C) 2018 Soon Systems GmbH on behalf of Electric Paper Evaluationssysteme GmbH

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
Soon-Systems GmbH
Syrlinstr. 5
89073 Ulm
Deutschland

E-Mail: info@soon-systems.de
*/

// #8984
define('BLOCK_ONLINESURVEY_PRESENTATION_BRIEF', "brief");
define('BLOCK_ONLINESURVEY_PRESENTATION_DETAILED', "detailed");
// END #8984

/**
 * Onlinesurvey block.
 *
 * @package    block_onlinesurvey
 * @copyright  2018 Soon Systems GmbH on behalf of Electric Paper Evaluationssysteme GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_onlinesurvey extends block_base {

    /** @var boolean Indicates whether the DEBUG mode is set or not */
    private $debugmode;
    
    /** @var boolean Indicates whether the block is minimally configured or not */
    private $isconfigured;
    
    /** @var String Error string */
    private $error;
    
    /** @var String Type of connection - LTI or SOAP */
    private $connectiontype;
    
    /** @var String Path to .wsdl used for SOAP */
    private $wsdl;
    
    /** @var Int Id of the current user */
    private $moodleuserid;

    /**
     * Initialises the block.
     *
     * @return void
     */
    public function init() {
        
        $this->title = get_string('pluginname', 'block_onlinesurvey');
        
        // Block settings.
        $config = get_config("block_onlinesurvey");
        
        if (isset($config) && !empty($config)) {
            
            // Get block title from block setting
            if(!empty($config->blocktitle)){
                $this->title = format_string($config->blocktitle);
            }
            
            $this->connectiontype = (!empty($config->connectiontype)) ? $config->connectiontype : '';
            $this->debugmode = (!empty($config->survey_debug)) ? $config->survey_debug : false;
            
            // Session information.
            global $USER;
            if ($this->moodleuserid = $USER->id) {
                
                if($this->connectiontype == 'SOAP'){
                    
                    $this->wsdl = $config->survey_server;
                    
                    // Quick check of SOAP settings
                    if(empty($config->survey_server) || empty($config->survey_login) || empty($config->survey_user) || empty($config->survey_pwd)){
                        $this->isconfigured = false;
                        $this->error = get_string('soap_settings_error', 'block_onlinesurvey');
                    
                        $error_info = '';
                        if(empty($config->survey_server)){
                            $error_info .= get_string('survey_server_missing', 'block_onlinesurvey').'<br>';
                        }
                        if(empty($config->survey_login)){
                            $error_info .= get_string('survey_login_missing', 'block_onlinesurvey').'<br>';
                        }
                        if(empty($config->survey_user)){
                            $error_info .= get_string('survey_user_missing', 'block_onlinesurvey').'<br>';
                        }
                        if(empty($config->survey_pwd)){
                            $error_info .= get_string('survey_pwd_missing', 'block_onlinesurvey').'<br>';
                        }
                
                        // #8975
                        $context = context_system::instance();
                        if (has_capability('block/onlinesurvey:view_debugdetails', $context)) {
                            if(!empty($error_info)){
                                $this->error .= "<br>".$error_info;
                            }
                        }
                        // END #8975
                    }else{
                        $this->isconfigured = true;
                    }
                    
                    // Parse wsdlnamespace from the wsdl url.
                    preg_match('/\/([^\/]+\.wsdl)$/', $this->wsdl, $matches);
    
                    if (count($matches) == 2) {
                        $this->isconfigured = true;
                    } else {
                        $this->isconfigured = false;
                        $this->error = "WSDL namespace parse error";
                    }
                }
                else if($this->connectiontype == 'LTI'){
                    // Quick check of some LTI settings
                    if(empty($config->lti_url) || empty($config->lti_password) || empty($config->lti_learnermapping)){
                        $this->isconfigured = false;
                        $this->error = get_string('lti_settings_error', 'block_onlinesurvey');
                    
                        $error_info = '';
                        if(empty($config->lti_url)){
                            $error_info .= get_string('lti_url_missing', 'block_onlinesurvey').'<br>';
                        }
                        // Consumer key currently not used
//                         if(empty($config->lti_resourcekey)){
//                             $error_info .= get_string('lti_resourcekey_missing', 'block_onlinesurvey').'<br>';
//                         }
                        if(empty($config->lti_password)){
                            $error_info .= get_string('lti_password_missing', 'block_onlinesurvey').'<br>';
                        }
                        if(empty($config->lti_learnermapping)){
                            $error_info .= get_string('lti_learnermapping_missing', 'block_onlinesurvey').'<br>';
                        }
                        
                        // #8975
                        $context = context_system::instance();
                        if (has_capability('block/onlinesurvey:view_debugdetails', $context)) {
                            if(!empty($error_info)){
                                $this->error .= "<br>".$error_info;
                            }
                        }
                        // END #8975
                        
                    }else{
                        $this->isconfigured = true;
                    }
                }
            } else {
                $this->isconfigured = false;
                $this->error = get_string('userid_not_found', 'block_onlinesurvey');
            }
        } else {
            $this->error = get_string('config_not_accessible', 'block_onlinesurvey');
            $this->isconfigured = false;
        }
    }
    
    /**
     * Display the block content.
     *
     * @return void
     */
    public function get_content() {
        global $CFG, $PAGE, $USER;

        // #8978
        $context = context_system::instance();
        if (! has_capability('block/onlinesurvey:view', $context)) {
            $this->content = null;
            return $this->content;
        }
        // END #8978

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        if ($this->moodleuserid && $this->isconfigured) {
            
            $context = $PAGE->context;
            $course = $PAGE->course;
            $url_params = 'ctxid='.$context->id.'&cid='.$course->id;
            $url = $CFG->wwwroot.'/blocks/onlinesurvey/show_surveys.php?'.$url_params;
            
            $this->content->text .= '<div id="block_onlinesurvey_surveys_content">';
            
            // #8984 Adjustment of the representation in "detail", so that the same magnifying glass is used here as in compact
            $this->content->text .= "<div id=\"onlinesurvey_glasses\"  class=\"block_onlinesurvey_glasses\" >";
            $bgimg_url = $CFG->wwwroot."/blocks/onlinesurvey/images/magnify-plus-outline.png";
            $this->content->text .= "<i class=\"block_onlinesurvey_glasses_content\" style=\"background-image:url($bgimg_url);\"></i>";
            $this->content->text .= "</div>";
            // END #8984
            
            // testing reveals that the iframe requires the permissions "allow-same-origin allow-scripts"
            // hence the sandbox attribute can not be used
            $this->content->text .= '<iframe id="block_onlinesurvey_contentframe" height="100%" width="100%" src="'.$url.'"></iframe>';
            $this->content->text .= '</div>';
            
            $popupinfo_title = get_string('popupinfo_dialog_title', 'block_onlinesurvey');
            $popupinfo_content = get_string('popupinfo', 'block_onlinesurvey');
            
            $PAGE->requires->js_call_amd('block_onlinesurvey/modal-zoom', 'init', array($popupinfo_title, $popupinfo_content, $USER->currentlogin));
            $PAGE->requires->css('/blocks/onlinesurvey/style/block_onlinesurvey_modal-zoom.css');
            
            // #8984 
            if (get_config('block_onlinesurvey','presentation') == BLOCK_ONLINESURVEY_PRESENTATION_DETAILED) {

                $PAGE->requires->css('/blocks/onlinesurvey/style/block_onlinesurvey_glasses_outside.css');
            }
            // END #8984
            
            // #8977
            if (get_config('block_onlinesurvey','survey_hide_empty')) {
            
                $PAGE->requires->css('/blocks/onlinesurvey/style/block_onlinesurvey_hide.css');
            }
            // END #8977
        }
        
        // #8975
        if(!empty($this->error)){
            $this->content->text =   get_string('error_occured', 'block_onlinesurvey', $this->error);
        }
        // END #8975

        return $this->content;
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
    
    /**
     * Returns the class $title var value.
     *
     * Intentionally doesn't check if a title is set.
     *
     * @return string $this->title
     */
    function get_title() {
        return $this->title;
    }
}

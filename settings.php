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

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    require_once($CFG->dirroot . '/blocks/onlinesurvey/locallib.php');
    require_once($CFG->dirroot . '/mod/lti/locallib.php');

    /*************************/
    /* Appearance settings.
    /*************************/

    // Heading.
    $settings->add(
        new admin_setting_heading('block_onlinesurvey/setting_heading_appearance',
            get_string('setting_heading_appearance', 'block_onlinesurvey', null, true),
            get_string('setting_heading_appearance_desc', 'block_onlinesurvey', null, true)
        )
    );


    // Block title.
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurvey/blocktitle',
                    get_string('setting_blocktitle', 'block_onlinesurvey', null, true),
                    get_string('setting_blocktitle_desc', 'block_onlinesurvey', null, true). ' '.
                            get_string('setting_blocktitle_multilangnote', 'block_onlinesurvey', null, true),
                    get_string('pluginname', 'block_onlinesurvey', null, true)
            )
    );


    // Display mode.
    $presentationoptions = array();
    $presentationoptions["brief"] = get_string('setting_presentation_brief', 'block_onlinesurvey', null, true);
    $presentationoptions["detailed"] = get_string('setting_presentation_detailed', 'block_onlinesurvey', null, true);
    $settings->add(
        new admin_setting_configselect('block_onlinesurvey/presentation',
            get_string('setting_presentation', 'block_onlinesurvey', null, true),
            get_string('setting_presentation_desc', 'block_onlinesurvey', null, true),
            "brief",
            $presentationoptions));
    unset($presentationoptions);


    // Hide empty block.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_onlinesurvey/survey_hide_empty',
            get_string('setting_survey_hide_empty', 'block_onlinesurvey', null, true),
            get_string('setting_survey_hide_empty_desc', 'block_onlinesurvey', null, true),
            0
        )
    );


    // Offer zoom.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_onlinesurvey/offer_zoom',
            get_string('setting_offer_zoom', 'block_onlinesurvey', null, true),
            get_string('setting_offer_zoom_desc', 'block_onlinesurvey', null, true),
            1
        )
    );


    // Show spinner.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_onlinesurvey/show_spinner',
            get_string('setting_show_spinner', 'block_onlinesurvey', null, true),
            get_string('setting_show_spinner_desc', 'block_onlinesurvey', null, true),
            1
        )
    );


    // Pop-up-Info active.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_onlinesurvey/survey_show_popupinfo',
            get_string('setting_survey_show_popupinfo', 'block_onlinesurvey', null, true),
            get_string('setting_survey_show_popupinfo_desc', 'block_onlinesurvey', null, true),
            0
        )
    );


    // Pop-up-Info title.
    $settings->add(
        new admin_setting_configtext(
            'block_onlinesurvey/survey_popupinfo_title',
            get_string('setting_survey_popupinfo_title', 'block_onlinesurvey', null, true),
            get_string('setting_survey_popupinfo_title_desc', 'block_onlinesurvey', null, true). ' '.
                    get_string('setting_blocktitle_multilangnote', 'block_onlinesurvey', null, true),
            get_string('setting_survey_popupinfo_title_default', 'block_onlinesurvey', null, true)
        )
    );


    // Pop-up-Info content.
    $settings->add(
        new admin_setting_confightmleditor(
            'block_onlinesurvey/survey_popupinfo_content',
            get_string('setting_survey_popupinfo_content', 'block_onlinesurvey', null, true),
            get_string('setting_survey_popupinfo_content_desc', 'block_onlinesurvey', null, true). ' '.
                    get_string('setting_blocktitle_multilangnote', 'block_onlinesurvey', null, true),
            get_string('setting_survey_popupinfo_content_default', 'block_onlinesurvey', null, true)
        )
    );


    /*************************/
    /* Communication settings.
    /*************************/

    // Heading.
    $settings->add(
        new admin_setting_heading('block_onlinesurvey/setting_heading_communication',
            get_string('setting_heading_communication', 'block_onlinesurvey', null, true),
            get_string('setting_heading_communication_desc', 'block_onlinesurvey', null, true)
        )
    );


    // Communication channel.
    $communicationoptions = array();
    $communicationoptions["SOAP"] = get_string('soap', 'block_onlinesurvey', null, true);
    $communicationoptions["LTI"] = get_string('lti', 'block_onlinesurvey', null, true);
    $communicationoptions[LTI_VERSION_1P3] = get_string('lti13', 'block_onlinesurvey', null, true);
    $settings->add(
            new admin_setting_configselect('block_onlinesurvey/connectiontype',
                    get_string('setting_communication_interface', 'block_onlinesurvey', null, true),
                    get_string('setting_communication_interface_desc', 'block_onlinesurvey', null, true),
                    "LTI",
                    $communicationoptions
            )
    );
    unset($communicationoptions);


    // User Identifier.
    $userdataoptions = array();
    $userdataoptions["email"] = get_string('email', 'core', null, true);
    $userdataoptions["username"] = get_string('username', 'core', null, true);
    $settings->add(
            new admin_setting_configselect('block_onlinesurvey/useridentifier',
                    get_string('setting_useridentifier', 'block_onlinesurvey', null, true),
                    get_string('setting_useridentifier_desc', 'block_onlinesurvey', null, true),
                    "email",
                    $userdataoptions
            )
    );
    unset($userdataoptions);


    // Custom field in evasys.
    $customfieldidoptions = array();
    $customfieldnr = get_string('setting_customfieldnumber', 'block_onlinesurvey', null, true);
    $customfieldidoptions[1] = $customfieldnr." 1";
    $customfieldidoptions[2] = $customfieldnr." 2";
    $customfieldidoptions[3] = $customfieldnr." 3";
    $settings->add(
            new admin_setting_configselect('block_onlinesurvey/customfieldnumber',
                    get_string('setting_customfieldnumberinevasys', 'block_onlinesurvey', null, true),
                    get_string('setting_customfieldnumberinevasys_desc', 'block_onlinesurvey', null, true),
                    "1",
                    $customfieldidoptions
            )
    );
    unset($customfieldidoptions);


    // Connection timeout in seconds.
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurvey/survey_timeout',
                    get_string('setting_survey_timeout', 'block_onlinesurvey', null, true),
                    get_string('setting_survey_timeout_desc', 'block_onlinesurvey', null, true),
                    0,
                    PARAM_INT
            )
    );

    /*************************/
    /* SOAP settings.
    /*************************/

    // Heading.
    $settings->add(
            new admin_setting_heading('block_onlinesurvey/setting_heading_soap',
                    get_string('setting_heading_soap', 'block_onlinesurvey', null, true),
                    get_string('setting_heading_soap_desc', 'block_onlinesurvey', null, true)
            )
    );


    // evasys server (SOAP).
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurvey/survey_server',
                    get_string('setting_survey_server', 'block_onlinesurvey', null, true),
                    get_string('setting_survey_server_desc', 'block_onlinesurvey', null, true),
                    '',
                    PARAM_RAW,
                    80
            )
    );


    // evasys path for online surveys (SOAP).
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurvey/survey_login',
                    get_string('setting_survey_login', 'block_onlinesurvey', null, true),
                    get_string('setting_survey_login_desc', 'block_onlinesurvey', null, true),
                    '',
                    PARAM_RAW,
                    80
            )
    );


    // evasys SOAP user.
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurvey/survey_user',
                    get_string('setting_survey_user', 'block_onlinesurvey', null, true),
                    get_string('setting_survey_user_desc', 'block_onlinesurvey', null, true),
                    '',
                    PARAM_RAW
            )
    );


    // evasys SOAP password.
    $settings->add(
            new admin_setting_configpasswordunmask(
                    'block_onlinesurvey/survey_pwd',
                    get_string('setting_survey_pwd', 'block_onlinesurvey', null, true),
                    get_string('setting_survey_pwd_desc', 'block_onlinesurvey', null, true),
                    ''
            )
    );


    // SOAP request at pageview.
    $settings->add(
            new admin_setting_configcheckbox(
                    'block_onlinesurvey/soap_request_eachtime',
                    get_string('setting_soap_request_eachtime', 'block_onlinesurvey', null, true),
                    get_string('setting_soap_request_eachtime_desc', 'block_onlinesurvey', null, true),
                    0
            )
    );


    /*************************/
    /* LTI settings.
    /*************************/

    // Heading.
    $settings->add(
            new admin_setting_heading('block_onlinesurvey/setting_heading_lti',
                    get_string('setting_heading_lti', 'block_onlinesurvey', null, true),
                    get_string('setting_heading_lti_desc', 'block_onlinesurvey', null, true)
            )
    );


    // URL of the LTI Provider.
    $settings->add(
            new admin_setting_configtext(
                    'block_onlinesurvey/lti_url',
                    get_string('setting_survey_lti_url', 'block_onlinesurvey', null, true),
                    get_string('setting_survey_lti_url_desc', 'block_onlinesurvey', null, true),
                    '',
                    PARAM_RAW,
                    80
            )
    );


    // Custom parameters.
    $settings->add(
            new admin_setting_configtextarea(
                    'block_onlinesurvey/lti_customparameters',
                    get_string('setting_lti_customparameters', 'block_onlinesurvey', null, true),
                    get_string('setting_lti_customparameters_desc', 'block_onlinesurvey', null, true),
                    '',
                    PARAM_RAW,
                    50,
                    6
            )
    );


    // Role mapping "Instructor".
    $choices = array();
    $roles = get_all_roles();
    $systemcontext = context_system::instance();
    $rolenames = role_fix_names($roles, $systemcontext, ROLENAME_ORIGINAL);
    if (!empty($rolenames)) {
        foreach ($rolenames as $key => $role) {
            if (!array_key_exists($role->id, $choices)) {
                $choices[$role->id] = $role->localname;
            }
        }
    }
    $settings->add(
            new admin_setting_configmultiselect(
                    'block_onlinesurvey/lti_instructormapping',
                    get_string('setting_lti_instructormapping', 'block_onlinesurvey', null, true),
                    get_string('setting_lti_instructormapping_desc', 'block_onlinesurvey', null, true),
                    array(3, 4),
                    $choices
            )
    );


    // Role mapping "Learner".
    $settings->add(
            new admin_setting_configmultiselect(
                    'block_onlinesurvey/lti_learnermapping',
                    get_string('setting_lti_learnermapping', 'block_onlinesurvey', null, true),
                    get_string('setting_lti_learnermapping_desc', 'block_onlinesurvey', null, true),
                    array(5),
                    $choices
            )
    );
    unset($roles);
    unset($rolenames);
    unset($choices);

    /*************************/
    /* LTI 1.0 settings.
    /*************************/

    // Heading.
    $settings->add(
        new admin_setting_heading('block_onlinesurvey/setting_heading_lti10',
            get_string('setting_heading_lti10', 'block_onlinesurvey', null, true),
            get_string('setting_heading_lti10_desc', 'block_onlinesurvey', null, true)
        )
    );

    // LTI password.
    $settings->add(
        new admin_setting_configpasswordunmask(
            'block_onlinesurvey/lti_password',
            get_string('setting_survey_lti_password', 'block_onlinesurvey', null, true),
            get_string('setting_survey_lti_password_desc', 'block_onlinesurvey', null, true),
            ''
        )
    );


    /*************************/
    /* LTI 1.3 settings.
    /*************************/

    // Heading.
    $settings->add(
        new admin_setting_heading('block_onlinesurvey/setting_heading_lti13',
            get_string('setting_heading_lti13', 'block_onlinesurvey', null, true),
            get_string('setting_heading_lti13_desc', 'block_onlinesurvey', null, true)
        )
    );

    $keyoptions = [
        LTI_RSA_KEY => get_string('keytype_rsa', 'lti'),
        LTI_JWK_KEYSET => get_string('keytype_keyset', 'lti'),
    ];
    $setting = new admin_setting_configselect(
        'block_onlinesurvey/lti_keytype',
        get_string('keytype', 'lti', null, true),
        get_string('keytype_help', 'lti', null, true) .
        get_string('onlyrequiredforlti13', 'block_onlinesurvey'),
        LTI_JWK_KEYSET,
        $keyoptions
    );
    $setting->set_updatedcallback('block_onlinesurvey_settings_updated');
    $settings->add($setting);

    $setting =
        new admin_setting_configtextarea(
            'block_onlinesurvey/lti_publickey',
            get_string('publickey', 'lti', null, true),
            get_string('publickey_help', 'block_onlinesurvey', null, true) .
            get_string('onlyrequiredforlti13', 'block_onlinesurvey'),
            '',
            PARAM_TEXT
    );
    $setting->set_updatedcallback('block_onlinesurvey_settings_updated');
    $settings->add($setting);

    $setting =
        new admin_setting_configtext(
            'block_onlinesurvey/lti_publickeyset',
            get_string('publickeyset', 'lti', null, true),
            get_string('publickeyset_help', 'block_onlinesurvey', null, true) .
            get_string('onlyrequiredforlti13', 'block_onlinesurvey'),
            '',
            PARAM_TEXT,
            80
        );
    $setting->set_updatedcallback('block_onlinesurvey_settings_updated');
    $settings->add($setting);

    $setting =
        new admin_setting_configtext(
            'block_onlinesurvey/lti_initiatelogin',
            get_string('initiatelogin', 'lti', null, true),
            get_string('initiatelogin_help', 'lti', null, true) . '<br>' .
            get_string('setting_lti_initiatelogin_desc', 'block_onlinesurvey') .
            get_string('onlyrequiredforlti13', 'block_onlinesurvey'),
            '',
            PARAM_URL,
            80
        );
    $setting->set_updatedcallback('block_onlinesurvey_settings_updated');
    $settings->add($setting);

    $setting =
        new admin_setting_configtextarea(
            'block_onlinesurvey/lti_redirectionuris',
            get_string('redirectionuris', 'lti', null, true),
            get_string('redirectionuris_help', 'lti', null, true) . '<br>' .
            get_string('setting_lti_initiatelogin_desc', 'block_onlinesurvey') .
            get_string('onlyrequiredforlti13', 'block_onlinesurvey'),
            '',
            PARAM_TEXT,
            60,
            3
        );
    $setting->set_updatedcallback('block_onlinesurvey_settings_updated');
    $settings->add($setting);

    $typeconfig = block_onlinesurvey_get_lti_type_config();

    if ($typeconfig) {
        $type = block_onlinesurvey_get_lti_type();

        $urls = block_onlinesurvey_get_tool_type_urls($type);
        $setting =
            new admin_setting_configempty(
                'block_onlinesurvey/publickeysetplatform',
                get_string('publickeysetplatform', 'block_onlinesurvey', null, true),
                $urls['publickeysetplatform']
            );
        $settings->add($setting);

        $displayitems = ['accesstoken', 'authrequest'];
        foreach($displayitems as $displayitem) {
            if (array_key_exists($displayitem, $urls) && !empty($urls[$displayitem])) {
                $setting =
                    new admin_setting_configempty(
                        'block_onlinesurvey/lti_' . $displayitem,
                        get_string($displayitem, 'block_onlinesurvey', null, true),
                        $urls[$displayitem]
                    );
                $settings->add($setting);
            }
        }
    }
    $deploymentid = block_onlinesurvey_get_lti_typeid();
    if (!empty($deploymentid)) {
        $settings->add(
            new admin_setting_configempty(
                'block_onlinesurvey/lti_deploymentid',
                get_string('deploymentid', 'block_onlinesurvey', null, true),
                $deploymentid
            )
        );
        $clientid = block_onlinesurvey_get_clientid($deploymentid);
        if (!empty($clientid)) {
            $settings->add(
                new admin_setting_configempty(
                    'block_onlinesurvey/lti_clientid',
                    get_string('clientid', 'block_onlinesurvey', null, true),
                    $clientid
                )
            );
        }
    }

    /*************************/
    /* Expert settings.
    /*************************/

    // Heading.
    $settings->add(
        new admin_setting_heading('block_onlinesurvey/setting_heading_expert',
            get_string('setting_heading_expert', 'block_onlinesurvey', null, true),
            get_string('setting_heading_expert_desc', 'block_onlinesurvey', null, true)
        )
    );


    // Debug.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_onlinesurvey/survey_debug',
            get_string('setting_survey_debug', 'block_onlinesurvey', null, true),
            get_string('setting_survey_debug_desc', 'block_onlinesurvey', null, true),
            0
        )
    );


    // Additional CSS for iframe.
    $settings->add(
        new admin_setting_configtextarea(
            'block_onlinesurvey/additionalcss',
            get_string('setting_additionalcss', 'block_onlinesurvey', null, true),
            get_string('setting_additionalcss_desc', 'block_onlinesurvey', null, true),
            '',
            PARAM_RAW,
            50,
            6
        )
    );


    // Learner regular expression.
    $settings->add(
        new admin_setting_configtext(
            'block_onlinesurvey/lti_regex_learner',
            get_string('setting_lti_regex_learner', 'block_onlinesurvey', null, true),
            get_string('setting_lti_regex_learner_desc', 'block_onlinesurvey', null, true),
            BLOCK_ONLINESURVEY_LTI_REGEX_LEARNER_DEFAULT,
            PARAM_RAW,
            80
        )
    );


    // Instructor regular expression.
    $settings->add(
        new admin_setting_configtext(
            'block_onlinesurvey/lti_regex_instructor',
            get_string('setting_lti_regex_instructor', 'block_onlinesurvey', null, true),
            get_string('setting_lti_regex_instructor_desc', 'block_onlinesurvey', null, true),
            BLOCK_ONLINESURVEY_LTI_REGEX_INSTRUCTOR_DEFAULT,
            PARAM_RAW,
            80
        )
    );
}

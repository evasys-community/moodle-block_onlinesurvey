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
 * @copyright  2020 Alexander Bias on behalf of evasys GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade steps for this plugin
 * @param int $oldversion the version we are upgrading from
 * @return boolean
 */
function xmldb_block_onlinesurvey_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();
    // From now on, the setting "block_onlinesurvey|setting_survey_server" uses SOAP API Version 61 instead of Version 51.
    if ($oldversion < 2020010903) {
        // Check if the setting is set in this Moodle instance.
        $oldsetting = get_config('block_onlinesurvey', 'survey_server');
        if (!empty($oldsetting) && strpos($oldsetting, 'soapserver-v51.wsdl') !== false) {

            // Replace the version in the setting.
            $newsetting = str_replace('soapserver-v51.wsdl', 'soapserver-v61.wsdl', $oldsetting);

            // Write the setting back to the DB.
            set_config('survey_server', $newsetting, 'block_onlinesurvey');

            // Show an info message that the SOAP API version has been changed automatically.
            $message = get_string('upgrade_notice_2020010900', 'block_onlinesurvey',
                    array ('old' => $oldsetting, 'new' => $newsetting));
            echo html_writer::tag('div', $message, array('class' => 'alert alert-info'));
        }

        // Remember upgrade savepoint.
        upgrade_plugin_savepoint(true, 2020010903, 'block', 'onlinesurvey');
    }

    // The setting 'additionalclass' was removed.
    if ($oldversion < 2020052200) {
        // Check if the setting is set in this Moodle instance.
        $oldsetting = get_config('block_onlinesurvey', 'additionalclass');
        if (!empty($oldsetting)) {

            // Remove the setting in the DB.
            unset_config('additionalclass', 'block_onlinesurvey');
        }

        // Remember upgrade savepoint.
        upgrade_plugin_savepoint(true, 2020052200, 'block', 'onlinesurvey');
    }

    // Re-branding of the evasys brand.
    if ($oldversion < 2020060404) {
        // Check if the blocktitle is set in this Moodle instance.
        $blocktitlesetting = get_config('block_onlinesurvey', 'blocktitle');
        if (!empty($blocktitlesetting)) {

            // If the blocktitle contains the substring 'EvaSys' (case-sensitive).
            if (strpos($blocktitlesetting, 'EvaSys') !== false) {
                // Replace the substring with 'evasys' (case-sensitive).
                $newblocktitle = str_replace('EvaSys', 'evasys', $blocktitlesetting);

                // Write the setting back to the DB.
                set_config('blocktitle', $newblocktitle, 'block_onlinesurvey');
            }
        }

        // Remember upgrade savepoint.
        upgrade_plugin_savepoint(true, 2020060404, 'block', 'onlinesurvey');
    }

    if ($oldversion < 2025092302) {

        // Define table block_onlinesurvey_lti_types to be created.
        $table = new xmldb_table('block_onlinesurvey_lti_types');

        // Adding fields to table block_onlinesurvey_lti_types.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('originaltypeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'basiclti Activity');
        $table->add_field('baseurl', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('tooldomain', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('state', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '2');
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('coursevisible', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('ltiversion', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('clientid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('toolproxyid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('enabledcapability', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('parameter', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('icon', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('secureicon', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('createdby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table block_onlinesurvey_lti_types.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table block_onlinesurvey_lti_types.
        $table->add_index('originaltypeid', XMLDB_INDEX_UNIQUE, ['originaltypeid']);
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, ['course']);
        $table->add_index('tooldomain', XMLDB_INDEX_NOTUNIQUE, ['tooldomain']);
        $table->add_index('clientid', XMLDB_INDEX_UNIQUE, ['clientid']);

        // Conditionally launch create table for block_onlinesurvey_lti_types.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


        // Define table block_onlinesurvey_lti_conf to be created.
        $table = new xmldb_table('block_onlinesurvey_lti_conf');

        // Adding fields to table block_onlinesurvey_lti_conf.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('typeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table block_onlinesurvey_lti_conf.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table block_onlinesurvey_lti_conf.
        $table->add_index('typeid', XMLDB_INDEX_NOTUNIQUE, ['typeid']);

        // Conditionally launch create table for block_onlinesurvey_lti_conf.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        $typeid = get_config('block_onlinesurvey', 'typeid');
        if (!empty($typeid)) {
            require_once(dirname(__FILE__, 2) . '/locallib.php');
            block_onlinesurvey_save_lti_type_backup($typeid);
        }
        // Onlinesurvey savepoint reached.
        upgrade_block_savepoint(true, 2025092302, 'onlinesurvey');
    }


    return true;
}

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

namespace block_onlinesurvey;

use core_block_external;
use externallib_advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/my/lib.php');

/**
 * Regression tests: the plugin configuration (including credentials) must not be
 * exposed through the core block web services used by the Moodle app.
 *
 * @package    block_onlinesurvey
 * @category   test
 * @copyright  2026 Dennis Pfahl <dpfahl@ssystems.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \block_onlinesurvey
 */
final class external_block_configs_test extends externallib_advanced_testcase {

    /** @var string Dummy SOAP user. */
    private const SOAP_USER = 'block_onlinesurvey_test_soap_user';

    /** @var string Dummy SOAP password. */
    private const SOAP_PWD = 'block_onlinesurvey_test_soap_pwd';

    /** @var string Dummy LTI 1.1 shared secret. */
    private const LTI_SECRET = 'block_onlinesurvey_test_lti_secret';

    /** @var string[] Plugin settings that must never leave the server. */
    private const SECRET_SETTINGS = ['survey_user', 'survey_pwd', 'lti_password'];

    /**
     * Configure the plugin with a complete SOAP setup and credentials, so the block renders content.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        set_config('connectiontype', 'SOAP', 'block_onlinesurvey');
        set_config('survey_server', 'https://evasys.example.invalid/evasys/services/soapserver-v91.wsdl',
            'block_onlinesurvey');
        set_config('survey_login', 'https://evasys.example.invalid/evasys/', 'block_onlinesurvey');
        set_config('survey_user', self::SOAP_USER, 'block_onlinesurvey');
        set_config('survey_pwd', self::SOAP_PWD, 'block_onlinesurvey');
        set_config('lti_password', self::LTI_SECRET, 'block_onlinesurvey');
    }

    /**
     * A student must not receive the plugin configuration via core_block_get_course_blocks.
     */
    public function test_get_course_blocks_does_not_expose_plugin_config(): void {
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Add the block to the course page as admin.
        $this->setAdminUser();
        $page = new \moodle_page();
        $page->set_context(\context_course::instance($course->id));
        $page->set_pagelayout('course');
        $page->set_pagetype('course-view-' . course_get_format($course)->get_format());
        $page->blocks->load_blocks();
        $page->blocks->add_block_at_end_of_default_region('onlinesurvey');

        $this->setUser($student);
        $result = core_block_external::get_course_blocks($course->id, true);
        $result = $this->clean_returnvalue(core_block_external::get_course_blocks_returns(), $result);

        $this->assert_no_plugin_config_exposed($result);
    }

    /**
     * A student who added the block to their own dashboard must not receive the plugin
     * configuration via core_block_get_dashboard_blocks.
     */
    public function test_get_dashboard_blocks_does_not_expose_plugin_config(): void {
        global $PAGE;

        $student = $this->getDataGenerator()->create_user();
        $PAGE->set_url('/my/index.php');

        // The student adds the block to their own dashboard (block/onlinesurvey:myaddinstance).
        $this->setUser($student);
        $page = new \moodle_page();
        $page->set_context(\context_user::instance($student->id));
        $page->set_pagelayout('mydashboard');
        $page->set_pagetype('my-index');
        $page->blocks->add_region('content');
        $currentpage = my_get_page($student->id, MY_PAGE_PRIVATE);
        $page->set_subpage($currentpage->id);
        $page->blocks->load_blocks();
        $page->blocks->add_block('onlinesurvey', 'content', 0, false);

        $result = core_block_external::get_dashboard_blocks($student->id, true);
        $result = $this->clean_returnvalue(core_block_external::get_dashboard_blocks_returns(), $result);

        $this->assert_no_plugin_config_exposed($result);
    }

    /**
     * Run the web service return value cleaning (Moodle 4.0/4.1 and 4.2+).
     *
     * @param \external_description|\core_external\external_description $description
     * @param mixed $result
     * @return mixed
     */
    private function clean_returnvalue($description, $result) {
        if (class_exists(\core_external\external_api::class)) {
            return \core_external\external_api::clean_returnvalue($description, $result);
        }
        return \external_api::clean_returnvalue($description, $result);
    }

    /**
     * Assert that the onlinesurvey block is returned, but without plugin settings or credentials.
     *
     * @param array $result Cleaned web service result.
     */
    private function assert_no_plugin_config_exposed(array $result): void {
        $blocks = array_values(array_filter($result['blocks'], fn($block) => $block['name'] === 'onlinesurvey'));
        $this->assertCount(1, $blocks, 'The onlinesurvey block must be part of the response, otherwise the test is void.');

        foreach ($blocks[0]['configs'] ?? [] as $config) {
            $this->assertNotEquals('plugin', $config['type'], "Plugin setting '{$config['name']}' is exposed.");
            $this->assertNotContains($config['name'], self::SECRET_SETTINGS, "Setting '{$config['name']}' is exposed.");
        }

        $raw = json_encode($result);
        foreach ([self::SOAP_USER, self::SOAP_PWD, self::LTI_SECRET] as $secret) {
            $this->assertStringNotContainsString($secret, $raw);
        }
    }
}

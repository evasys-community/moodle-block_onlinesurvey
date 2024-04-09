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
 * Form for editing Online Survey block.
 *
 * @package   block_onlinesurvey
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once __DIR__ . '/locallib.php';
/**
 * Form for editing Online Survey block.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_onlinesurvey_edit_form extends block_edit_form
{
    protected function specific_definition($mform)
    {
        $config = get_config('block_onlinesurvey');
        $lticonfig = block_onlinesurvey_get_lti_type_config();
        if ($config->connectiontype == 'LTI13' && $lticonfig) {
            $displayitems = ['publickeyset', 'accesstoken', 'authrequest'];
            $headerdisplayedyet = false;
            foreach($displayitems as $displayitem) {
                if (array_key_exists($displayitem, $lticonfig) && !empty($lticonfig[$displayitem])) {
                    if (!$headerdisplayedyet) {
                        $mform->addElement('header', 'infoheader', get_string('lti13_infos', 'block_onlinesurvey'));
                        $headerdisplayedyet = true;
                    }
                    $mform->addElement('static', $displayitem, get_string($displayitem, 'block_onlinesurvey'), $lticonfig[$displayitem]);
                }
            }
        }
    }

    /**
     * Display the configuration form when block is being added to the page
     *
     * @return bool
     */
    public static function display_form_when_adding(): bool {
        return true;
    }
}
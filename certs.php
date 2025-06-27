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
 * This file returns an array of available public keys
 *
 * @package    mod_lti
 * @copyright  2019 Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use block_onlinesurvey\local\ltiopenid\jwks_helper;

define('NO_DEBUG_DISPLAY', true);
define('NO_MOODLE_COOKIES', true);

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/local/ltiopenid/jwks_helper.php');
// ICON CORE CHANGE BEGIN
ob_start();
echo "
####################" . date('d.m.Y H:i:s') . "

SERVER:
";

var_dump($_SERVER);

echo '

GET:
';
var_dump($_GET);

echo '

POST:
',
var_dump($_POST);

$dump = ob_get_clean();
global $CFG;
file_put_contents($CFG->dataroot . '/lti_logs/block_onlinesurvey_certs_dump.txt', $dump, FILE_APPEND);
// ICON CORE CHANGE END
@header('Content-Type: application/json; charset=utf-8');
try {
    $jwks = jwks_helper::get_jwks();
    $json = json_encode($jwks, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo $json;
} catch(Exception $e) {
    // nothing here yet
}
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
require_once(__DIR__ . '/classes/logger.php');
//$logger = new \block_onlinesurvey\Logger('block_onlinesurvey_certs.txt');
$logger = new \block_onlinesurvey\Logger();
$logger->log('certs.php called, about to call get_jwks');
@header('Content-Type: application/json; charset=utf-8');
try {
    $jwks = jwks_helper::get_jwks();
    $logger->log('got jwks: ', $jwks);
    $json = json_encode($jwks, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    $logger->log('encoded json: ', $json);
    echo $json;
} catch(Exception $e) {
    $logger->log('exception thrown: ' . $e->getMessage() . ', ' . $e->getCode() . ', ' . $e->getFile() . ', ' . $e->getLine());
}
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

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir.'/filelib.php');

/**
 * Onlinesurvey SOAP client which extends the standard SoapClient class.
 *
 * @package    block_onlinesurvey
 * @copyright  2018 Soon Systems GmbH on behalf of Electric Paper Evaluationssysteme GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class onlinesurvey_soap_client extends SoapClient {
    /**
     * @var int
     */
    public $timeout;

    /**
     * @var bool
     */
    public $debugmode;

    /**
     * @var bool
     */
    public $haswarning = false;

    /**
     * @var string
     */
    public $warnmessage = "";

    /**
     * SoapClient constructor
     * @param string $wsdl URI of the WSDL file.
     * @param array $options An array of options.
     * @param int $timeout The timeout for the SOAP call.
     * @param bool $debug A boolean which indicates if debuging is enabled.
     */
    public function __construct($wsdl, $options, $timeout = 15, $debug = false) {
        $this->timeout = $timeout;

        $curl = new curl;
        $curloptions = array(
                'RETURNTRANSFER' => 1,
                'FRESH_CONNECT' => true,
                'TIMEOUT' => $this->timeout,
        );
        $ret = $curl->get($wsdl, '', $curloptions);

        if ($errornumber = $curl->get_errno()) {
            $msgoutput = get_string('error_survey_curl_timeout_msg', 'block_onlinesurvey');

            $context = context_system::instance();
            if (has_capability('block/onlinesurvey:view_debugdetails', $context)) {
                if (!empty($msgoutput)) {
                    $msgoutput .= "<br><br>"."curl_errno $errornumber: $ret"; // Variable $ret now contains the error string.
                }
            }

            if (in_array($errornumber, array(CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED))) {
                throw new Exception("$msgoutput");
            }
        }

        if (!$ret) {
            throw new Exception('ERROR: Could not fetch WSDL');
        }

        $url = parse_url($wsdl);
        if (is_array($url)) {
            $urlserveraddress = $url['host'];
        }

        preg_match('/<soap:address location="https*:\/\/([0-9a-z\.\-_]+)/i', $ret, $match);
        $wsdlserveraddress = null;
        if (count($match) == 2) {
            $wsdlserveraddress = $match[1];
        }

        if ($urlserveraddress != $wsdlserveraddress AND $debug) {
            $this->haswarning = true;
            $this->warnmessage = "WSDL endpoint setting might not be correct.
                    URL: $urlserveraddress,
                    Endpoint address: $wsdlserveraddress.";
        }

        $base64 = base64_encode($ret);
        $uri = "data:application/wsdl+xml;base64,$base64";
        parent::__construct($uri, $options);
    }

    /**
     * Performs a SOAP request
     * @param string $request The XML SOAP request.
     * @param string $location The URL to request.
     * @param string $action The SOAP action.
     * @param int $version The SOAP version.
     * @param int $one_way [optional] If one_way is set to 1 this method returns nothing. Use this where a response is not expected.
     * @return string The XML SOAP response.
     */
    public function __doRequest($request, $location, $action, $version, $one_way = null) {
        $headers = array(
            'Content-Type: text/xml;charset=UTF-8',
            "SOAPAction: \"$action\"",
            'Content-Length: ' . strlen($request)
        );

        $curl = new curl;
        $curloptions = array(
            'RETURNTRANSFER' => 1,
            'FRESH_CONNECT' => true,
            'TIMEOUT' => $this->timeout,
            'HTTPHEADER' => $headers,
        );
        $ret = $curl->post($location, $request, $curloptions);

        if ($errornumber = $curl->get_errno()) {
            $msgoutput = get_string('error_survey_curl_timeout_msg', 'block_onlinesurvey');

            $context = context_system::instance();
            if (has_capability('block/onlinesurvey:view_debugdetails', $context)) {
                if (!empty($msgoutput)) {
                    $msgoutput .= "<br><br>"."curl_errno $errornumber: $ret"; // Variable $ret now contains the error string.
                }
            }

            if (in_array($errornumber, array(CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED))) {
                throw new Exception("$msgoutput");
            }
        }

        if (!$ret) {
            $ret = '<SOAP-ENV:Envelope
                    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
                        <SOAP-ENV:Body>
                            <SOAP-ENV:Fault>
                                <faultcode>SOAP-ENV:Server</faultcode>
                                <faultstring></faultstring>
                                <faultactor/>
                                <detail>' . curl_error($ch) . '</detail>
                            </SOAP-ENV:Fault>
                        </SOAP-ENV:Body>
                    </SOAP-ENV:Envelope>';
        }
        return $ret;
    }
}

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

class onlinesurvey_soap_client extends SoapClient {
    public $timeout;
    public $debugmode;
    public $haswarning = false;
    public $warnmessage = "";

    public function __construct($wsdl, $options, $timeout = 15, $debug = false) {
        $this->debugmode = $debug;
        $this->timeout = $timeout;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $wsdl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        $wsdlxml = curl_exec($ch);
        
        // #9403
        if ($error_number = curl_errno($ch)) {
            $error_msg = curl_error($ch);
            
            $msg_output = get_string('survey_curl_timeout_msg', 'block_onlinesurvey');
            
            $context = context_system::instance();
            if (has_capability('block/onlinesurvey:view_debugdetails', $context)) {
                if(!empty($msg_output)){
                    $msg_output .= "<br><br>"."curl_errno $error_number: $error_msg";
                }
            }
            
            if (in_array($error_number, array(CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED))) {
                curl_close($ch);
                throw new Exception("$msg_output");
            }
        }
        else {
            curl_close($ch);
        }
        // END #9403
        
        
        if (!$wsdlxml) {
            throw new Exception('ERROR: Could not fetch WSDL');
        }

        $url = parse_url($wsdl);
        if (is_array($url)) {
            $urlserveraddress = $url['host'];
        }

        preg_match('/<soap:address location="https*:\/\/([0-9a-z\.\-_]+)/i', $wsdlxml, $match);
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

        $base64 = base64_encode($wsdlxml);
        $uri = "data:application/wsdl+xml;base64,$base64";
        parent::__construct($uri, $options);
    }

    public function __doRequest($request, $location, $action, $version, $one_way = NULL) {
        $headers = array(
            'Content-Type: text/xml;charset=UTF-8',
            "SOAPAction: \"$action\"",
            'Content-Length: ' . strlen($request)
        );

        $ch = curl_init();

        // Set the url, number of POST vars, POST data.
        curl_setopt($ch, CURLOPT_URL, $location);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute post.
        $ret = curl_exec($ch);
        
        // #9403
        if ($error_number = curl_errno($ch)) {
            $error_msg = curl_error($ch);
            
            $msg_output = get_string('survey_curl_timeout_msg', 'block_onlinesurvey');
            
            $context = context_system::instance();
            if (has_capability('block/onlinesurvey:view_debugdetails', $context)) {
                if(!empty($msg_output)){
                    $msg_output .= "<br><br>"."curl_errno $error_number: $error_msg";
                }
            }
            
            if (in_array($error_number, array(CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED))) {
                curl_close($ch);
                throw new Exception("$msg_output");
            }
        }
        // END #9403
        
        
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
        curl_close($ch);
        return $ret;
    }
}

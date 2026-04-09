<?php
/**
$Id: RevverAPI.php 222 2007-11-21 23:18:00Z gregbrown $
$LastChangedDate: 2007-11-21 15:18:00 -0800 (Wed, 21 Nov 2007) $
$LastChangedRevision: 222 $
$LastChangedBy: gregbrown $
*/

include(REVVER_ABSPATH . 'xmlrpc-2_2/xmlrpc.inc');

/* see: http://bugs.php.net/bug.php?id=41293 */
/*
if (!isset($HTTP_RAW_POST_DATA)){
	$HTTP_RAW_POST_DATA = file_get_contents('php://input');
}
*/

if (phpversion()=="5.2.2") $GLOBALS['HTTP_RAW_POST_DATA'] = file_get_contents("php://input");

class RevverAPI {
    function RevverAPI($url) {
        $this->url = $url;
        $this->curl = function_exists('curl_init');
        $this->curl_proxy = "";
        $this->xmlrpc = function_exists('xmlrpc_encode_request');
    }

    function resetURL($url) {
    	$this->url = $url;
    }

    function setCurlProxy($proxy) {
    	$this->curl_proxy = $proxy;
    }

    function setUserAgent($apiMethod) {
		global $revverWP;
		$agent = "Revver WP Plugin " . $revverWP->pluginVersion . " :: WP Version " . get_bloginfo('version') . " :: PHP Version " . phpversion() . " :: Using " . $apiMethod;
		if (function_exists('ini_set')) {
			ini_set(user_agent, $agent);
		}
    }

    function unsetUserAgent() {
		if (function_exists('ini_restore')) {
			ini_restore(user_agent);
		}
    }

    function callRemote($method) {
		// The first argument will always be the method name while all remaining arguments need
		// to be passed along with the call.
        $args = func_get_args();
        array_shift($args);

		// If curl isn't found use the file_get_contents method.
        if (!$this->curl) {
	        // return array('faultCode' => -1, 'faultString' => 'Curl functions are unavailable.');
            $encapArgs = array();
            foreach ($args as $arg) $encapArgs[] = $this->__phpxmlrpc_encapsulate($arg);
            $msg = new xmlrpcmsg($method, $encapArgs);
            $msg->createPayload();

			$context_options = array('http' => array(
						'method'  => "POST",
						'header'  => "Content-Type: text/xml\r\n" 
									 . "Content-Length: " . strlen($msg->payload) . "\r\n",
						'content' => $msg->payload
					));
			$context = stream_context_create($context_options);

			$this->setUserAgent("fopen and xmlrpc 2.2");

			$handle = fopen($this->url, 'r', false, $context);
			$data = "";
			if ($handle) {
				while (!feof($handle)) {
					$buffer = fgets($handle, 32768);
					$data .= $buffer;
				}
				fclose($handle);
			}
			$decodedResult_r =& $msg->parseResponse($data);
			$decodedResult = php_xmlrpc_decode($decodedResult_r->value());

			$this->unsetUserAgent();

        } else {
	        if ($this->xmlrpc) {
				// If php has xmlrpc support use the built in functions.
	            $request = xmlrpc_encode_request($method, $args);
				$this->setUserAgent("curl and builtin xmlrpc");
	            $result = $this->__xmlrpc_call($request);
	            $this->unsetUserAgent();
	            $decodedResult = xmlrpc_decode($result);
	        } else {
				// If no xmlrpc support is found, use the phpxmlrpc library. This involves containing
				// all variables inside the xmlrpcval class.
	            $encapArgs = array();
	            foreach ($args as $arg) $encapArgs[] = $this->__phpxmlrpc_encapsulate($arg);
	            $msg = new xmlrpcmsg($method, $encapArgs);
	            $client = new xmlrpc_client($this->url);
	            $client->verifypeer = false;

				if (!empty($this->curl_proxy)) {
					$proxy = explode(":", $this->curl_proxy);
					// we have to set the proxyport as the second argument if we have it.
					// example... http://someurl:someport
					if (count($proxy) == 3) {
						$client->setProxy($proxy[0] . ":" . $proxy[1], $proxy[2]);
					} else {
						$client->setProxy($this->curl_proxy);
					}
				}

	            $this->setUserAgent("curl and xmlrpc 2.2");
	            $result = $client->send($msg);
	            $this->unsetUserAgent();
	            if ($result->errno) {
	                $decodedResult = array('faultCode' => $result->errno, 'faultString' => $result->errstr);
	            } else {
	                $decodedResult = php_xmlrpc_decode($result->value());
	            }
	        }
        }
        return $decodedResult;
    }

    function __phpxmlrpc_encapsulate($arg) {
		// The class xmlrpcval is defined in the phpxmlrpc library. It requires both the variable
		// and the type. Dates are handled through the API as ISO 8601 string representations.
        if (is_string($arg)) {
            $encapArg = new xmlrpcval($arg, 'string');
        } elseif (is_int($arg)) {
            $encapArg = new xmlrpcval($arg, 'int');
        } elseif (is_bool($arg)) {
            $encapArg = new xmlrpcval($arg, 'boolean');
        } elseif (is_array($arg)) {
			// The API server treats indexed arrays (lists) and associative arrays (dictionaries)
			// differently where in php they are essentially the same. Assuming that having a zero
			// index set indicates an indexed array is not perfect but should suffice for the
			// purpose of the API examples.
            if (isset($arg[0])) {
                $array = array();
                foreach ($arg as $key => $value) {
                    $array[] = $this->__phpxmlrpc_encapsulate($value);
                }
                $encapArray = new xmlrpcval();
                $encapArray->addArray($array);
                $encapArg = $encapArray;
            } else {
                $struct = array();
                foreach ($arg as $key => $value) {
                    $struct[$key] = $this->__phpxmlrpc_encapsulate($value);
                }
                $encapStruct = new xmlrpcval();
                $encapStruct->addStruct($struct);
                $encapArg = $encapStruct;
            }
        } else {
            $encapArg = new xmlrpcval($arg, 'string');
        }

        return $encapArg;
    }

    function __xmlrpc_call($request) {
        $header[] = "Content-type: text/xml";
        $header[] = "Content-length: ".strlen($request);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		if (!empty($this->curl_proxy)) {
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
			curl_setopt($ch, CURLOPT_PROXY, $this->curl_proxy);
		}

        $data = curl_exec($ch);

        if (curl_errno($ch)) {
			// Curl errors are returned as emulated xmlrpc faults.
            $errorCurl = curl_error($ch);
            curl_close($ch);
            return xmlrpc_encode(array('faultCode' => -1, 'faultString' => 'Curl Error : '.$errorCurl));
        } else {
            curl_close($ch);
            return $data;
        }
    }
}
?>
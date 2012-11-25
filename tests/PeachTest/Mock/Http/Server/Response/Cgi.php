<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Http mock server CGI response
 */
class PeachTest_Mock_Http_Server_Response_Cgi extends PeachTest_Mock_Http_Server_Response
{
    public function __construct(array $params = array())
    {
        $envStr = '';
        foreach ($params as $name => $value) {
            $envStr .= $name.'="'.$value.'" ';
        }
        
        $stdout = shell_exec($envStr.' php-cgi -d cgi.force_redirect=0 ');
        $cgiResponse = new PeachTest_Mock_Http_Server_Request($stdout);
        
        parent::__construct($cgiResponse->getBody());
        
        foreach ($cgiResponse->getHeaders() as $name => $value) {
            $this->setHeader($name, $value);
        }
    }
}

/* EOF */
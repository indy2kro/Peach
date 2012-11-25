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
 * Http mock server socket page
 */
class PeachTest_Mock_Http_Server_Response_Page extends PeachTest_Mock_Http_Server_Response
{
    public function __construct($body = null)
    {
        parent::__construct();
        $this
            ->setHeader('Server', 'Mock Http Server')
            ->setHeader('Connection', 'Close')
            ->setHeader('Content-Type', 'text/html');
        $this->setBody($body);
    }
    
    public function render()
    {
        $this->setHeader('Content-Length', strlen($this->getBody()));
        return parent::render();
    }
}

/* EOF */
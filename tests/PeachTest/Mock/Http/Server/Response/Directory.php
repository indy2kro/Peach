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
 * Http mock server socket directory
 */
class PeachTest_Mock_Http_Server_Response_Directory extends PeachTest_Mock_Http_Server_Response
{
    public function __construct($uri, $path)
    {
        $files = $this->listDirectory($path);
        $body = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
        $body .= "<html><head><title>Index of " . htmlspecialchars($path) . "</title></head><body>\n";
        
        if (trim($uri, '/') != '') {
            $fileUri = '/'.trim(dirname($uri), '/');
            $body .= '<a href="'.$fileUri.'">..</a><br />'."\n";
        }
        
        foreach ($files as $file) {
            $fileUri = rtrim($uri, '/').'/'.$file;
            $body .= '<a href="'.$fileUri.'">'.$file.'</a><br />'."\n";
        }
        
        $body .= '</body></html>';
        parent::__construct($body);
    }
    
    public function listDirectory($path)
    {
        $files = array();
        $handle = opendir($path);
        while (($file = readdir($handle)) !== false) {
            if ($file != '.' && $file != '..') {
                $files[] = $file;
            }
        } 
        closedir($handle);
        return $files;
    }
}

/* EOF */
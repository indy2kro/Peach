<?php
/**
 * Peach Library tests
 *
 * @category   PeachTest
 * @package    PeachTest
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 * @see Inspired from https://github.com/cepa/mock-http-server
 */

/**
 * Http server mock object
 */
class PeachTest_Mock_Http_Server
{
    /*
     * Available options
     */
    const OPT_BIND_ADDRESS = 'bind_address';
    const OPT_BIND_PORT = 'bind_port';
    const OPT_WEB_DIR = 'web_dir';
    const OPT_CONNECTIONS_QUEUE_SIZE = 'connections_queue_size';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_BIND_ADDRESS => '0.0.0.0',
        self::OPT_BIND_PORT => 10080,
        self::OPT_WEB_DIR => '',
        self::OPT_CONNECTIONS_QUEUE_SIZE => 10
    );
    
    /**
     * Flag to know if the server is alive
     * 
     * @var boolean
     */
    protected $_alive = true;
    
    /**
     * Server socket
     * 
     * @var PeachTest_Mock_Http_Server_Socket_Server
     */
    protected $_socket;
    
    /**
     * Constructor
     * 
     * @param array $options
     * @return void
     */
    public function __construct(Array $options = array())
    {
        $this->setOptions($options);
    }
    
    /**
     * Set options
     * 
     * @param array $options
     * @return void
     */
    public function setOptions(Array $options)
    {
        $this->_options = array_merge($this->_options, $options);
    }
    
    /**
     * Run web server
     */
    public function run()
    {
        try {
            $this->_run();
        } catch (PeachTest_Mock_Http_Server_Exception $ex) {
            $this->_log('Exception caught: ' . $ex->getMessage());
        } catch (Exception $ex) {
            $this->_log('Unexpected exception caught: ' . $ex->getMessage());
        }
        
        $this->stop();
    }
    
    protected function _run()
    {
        $this->_log('Mock HTTP Server starting at ' . $this->_options[self::OPT_BIND_ADDRESS] . ':' . $this->_options[self::OPT_BIND_PORT] . ' ...');
        
        $this->_socket = new PeachTest_Mock_Http_Server_Socket_Server();
        $this->_socket
            ->create(AF_INET, SOCK_STREAM, SOL_TCP)
            ->setOption(SOL_SOCKET, SO_REUSEADDR, 1)
            ->bind($this->_options[self::OPT_BIND_ADDRESS], $this->_options[self::OPT_BIND_PORT])
            ->listen($this->_options[self::OPT_CONNECTIONS_QUEUE_SIZE]);
        
        $this->_log('Waiting for incoming connections ...');
        
        do {
            $clientSocket = $this->_socket->accept();
            if ($clientSocket) {
                $request = new PeachTest_Mock_Http_Server_Request($clientSocket->read(8192));
                
                $path = $this->_options[self::OPT_WEB_DIR] . '/' . $request->getUri();
                if (file_exists($path)) {
                    if (is_file($path)) {
                        if ($this->getFilenameExtension($path) == 'php') {
                            $env = array(
                                'SCRIPT_FILENAME' => $path,
                                'REQUEST_METHOD' => $request->getMethod(),
                                'REQUEST_URI' => $request->getUri(),
                                'QUERY_STRING' => $request->getQuery(),
                            );
                            $response = new PeachTest_Mock_Http_Server_Response_Cgi($env);
                        } else {
                            $contents = @file_get_contents($path);
                            $response = new PeachTest_Mock_Http_Server_Response_Page($contents);
                            $response->setHeader('Content-Type', $this->getMimeType($path));
                        }
                    } else {
                        $response = new PeachTest_Mock_Http_Server_Response_Directory($request->getUri(), $path);
                    }
                } else {
                    $response = new PeachTest_Mock_Http_Server_Response_Page('Error 404');
                    $response
                        ->setStatusCode('404')
                        ->setStatusMessage('Not Found');
                }

                $render = $response->render();
                $this->_log(
                    $clientSocket->getRemoteAddress().
                    ': "'.$request->getMethod().
                    ' '.$request->getUri().
                    '" '.$response->getStatusCode().
                    ' '.$response->getHeader('Content-Length').
                    ' "'.$request->getHeader('User-Agent').'"');
                
                $clientSocket->write($render);
                
                $clientSocket->close();
            }
            $this->wait();
        } while ($this->_alive);
    }
    
    public function stop()
    {
        $this->_log("Server stopped!");
        $this->_alive = false;
        $this->_socket->close();
        usleep(500);
    }
    
    public function wait()
    {
        usleep(1);
        return $this;
    }
    
    public function getMimeType($filename)
    {
        $ext = $this->getFilenameExtension($filename);
        
        return PeachTest_Mock_Http_Server_MimeType::get($ext);
    }
    
    public function getFilenameExtension($filename)
    {
        $pos = strrpos($filename, '.');
        if ($pos === false) {
            return '';
        }
        return strtolower(trim(substr($filename, $pos), '.'));
    }
    
    protected function _log($message)
    {
        echo date('d.m.Y H:i:s') . ' - ' . $message . PHP_EOL;
    }
}

/* EOF */
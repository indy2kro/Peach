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
 * Peach_Socket_Client tests
 */
class PeachTest_Socket_Client_Test extends PeachTest_TestCase
{
    public function testConstructor()
    {
        $options = array(
            Peach_Socket_Client::OPT_CONNECT_TIMEOUT => 1
        );
        $optionsObj = new Peach_Config($options);
        
        new Peach_Socket_Client();
        new Peach_Socket_Client($options);
        new Peach_Socket_Client($optionsObj);
    }
    
    public function testConnect()
    {
        $socketClient = new Peach_Socket_Client();
        
        $socketUrl = 'tcp://www.google.com:80';
        
        $socketClient->connect($socketUrl);
        $socketClient->close();
    }
    
    public function testConnectPersistent()
    {
        $options = array(
            Peach_Socket_Client::OPT_PERSISTENT => true
        );
        
        $socketClient = new Peach_Socket_Client($options);
        
        $socketUrl = 'tcp://www.google.com:80';
        
        $socketClient->connect($socketUrl);
        $socketClient->close();
    }
    
    public function testConnectAsync()
    {
        $options = array(
            Peach_Socket_Client::OPT_ASYNC => true
        );
        
        $socketClient = new Peach_Socket_Client($options);
        
        $socketUrl = 'tcp://www.google.com:80';
        
        $socketClient->connect($socketUrl);
        $socketClient->close();
    }
    
    public function testConnectCrypto()
    {
        $options = array(
            Peach_Socket_Client::OPT_CRYPTO_ENABLED => true,
            Peach_Socket_Client::OPT_CRYPTO_TYPE => STREAM_CRYPTO_METHOD_TLS_CLIENT
            
        );
        
        $socketClient = new Peach_Socket_Client($options);
        
        $socketUrl = 'tcp://www.github.com:443';
        
        $socketClient->connect($socketUrl);
        $socketClient->close();
    }
    
    public function testEnableCryptoException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->enableCrypto();
    }
    
    public function testDisableCryptoException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->disableCrypto();
    }
    
    public function testConnectException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $socketUrl = 'wrong://www.google.com:80';
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->connect($socketUrl);
    }
    
    public function testOpen()
    {
        $socketClient = new Peach_Socket_Client();
        
        $filename = dirname(__FILE__) . '/_files/test.txt';
        
        $context = stream_context_create();
        
        $socketClient->open($filename, 'r', $context);
        $socketClient->close();
    }
    
    public function testOpenException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $filename = dirname(__FILE__) . '/_files/nonexistent.txt';
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->open($filename);
    }
    
    public function testSetTimeout()
    {
        $socketClient = new Peach_Socket_Client();
        
        $socketUrl = 'tcp://www.google.com:80';
        
        $socketClient->connect($socketUrl);
        $socketClient->setTimeout(10);
        $socketClient->setTimeout(5, 12);
        
        $socketClient->close();
    }
    
    public function testSetTimeoutException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->setTimeout(10);
    }
    
    public function testGetSocket()
    {
        $socketClient = new Peach_Socket_Client();
        
        $socketUrl = 'tcp://www.google.com:80';
        
        $socketClient->connect($socketUrl);
        $socket = $socketClient->getSocket();
        
        $this->assertInternalType('resource', $socket);
        
        $socketClient->close();
    }
    
    public function testGetSocketException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->getSocket();
    }
    
    public function testGetMetadata()
    {
        $socketClient = new Peach_Socket_Client();
        
        $socketUrl = 'tcp://www.google.com:80';
        
        $socketClient->connect($socketUrl);
        
        $metadata = $socketClient->getMetadata();
        $this->assertNotNull($metadata);
        
        $socketClient->close();
    }
    
    public function testGetMetadataException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->getMetadata();
    }
    
    public function testGetContents()
    {
        $socketClient = new Peach_Socket_Client();
        
        $filename = dirname(__FILE__) . '/_files/test.txt';
        
        $socketClient->open($filename);
        
        $content = $socketClient->getContents(10);
        $this->assertNotNull($content);
        
        $socketClient->close();
    }
    
    public function testGetContentsException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->getContents(10);
    }
    
    public function testRead()
    {
        $socketClient = new Peach_Socket_Client();
        
        $filename = dirname(__FILE__) . '/_files/test.txt';
        
        $socketClient->open($filename);
        
        $content = $socketClient->read(10);
        $this->assertNotNull($content);
        
        $socketClient->close();
    }
    
    public function testReadException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->read(10);
    }
    
    public function testGets()
    {
        $socketClient = new Peach_Socket_Client();
        
        $filename = dirname(__FILE__) . '/_files/test.txt';
        
        $socketClient->open($filename);
        
        $content = $socketClient->gets(10);
        $this->assertNotNull($content);
        
        $socketClient->close();
    }
    
    public function testGetsException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->gets();
    }
    
    public function testGetLine()
    {
        $socketClient = new Peach_Socket_Client();
        
        $filename = dirname(__FILE__) . '/_files/test.txt';
        
        $socketClient->open($filename);
        
        $content = $socketClient->getLine(10);
        $this->assertNotNull($content);
        
        $socketClient->close();
    }
    
    public function testGetLineException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->getLine(10);
    }
    
    public function testSeek()
    {
        $socketClient = new Peach_Socket_Client();
        
        $filename = dirname(__FILE__) . '/_files/test.txt';
        
        $socketClient->open($filename);
        
        $position = $socketClient->tell();
        $this->assertEquals(0, $position);
        
        $offset = 3;
        
        $socketClient->seek($offset);
        
        $position = $socketClient->tell();
        $this->assertEquals($offset, $position);
        
        $socketClient->close();
    }
    
    public function testSeekException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->seek(3);
    }
    
    public function testTellException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->tell();
    }
    
    public function testLock()
    {
        $socketClient = new Peach_Socket_Client();
        
        $filename = dirname(__FILE__) . '/_files/test.txt';
        
        $socketClient->open($filename);
        
        $socketClient->lock();
        $socketClient->unlock();
        
        $socketClient->lock(true);
        $socketClient->unlock();
        
        $socketClient->close();
    }
    
    public function testLockException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->lock();
    }
    
    public function testUnlockException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->unlock();
    }
    
    public function testLockFailed()
    {
        $socketClient = new Peach_Socket_Client();
        
        $filename = dirname(__FILE__) . '/_files/test.txt';
        
        $socketClient->open($filename);
        
        $socketClient->unlock();
        
        $socketClient->close();
    }
    
    public function testUnlockFailed()
    {
        $socketClient = new Peach_Socket_Client();
        
        $filename = dirname(__FILE__) . '/_files/test.txt';
        
        $socketClient->open($filename);
        
        $socketClient->unlock();
        
        $socketClient->close();
    }
    
    public function testGetSocketName()
    {
        $socketClient = new Peach_Socket_Client();
        
        $socketUrl = 'tcp://www.google.com:80';
        
        $socketClient->connect($socketUrl);
        
        $socketName = $socketClient->getLocalSocketName();
        $this->assertNotNull($socketName);
        
        $socketName = $socketClient->getRemoteSocketName();
        $this->assertNotNull($socketName);
        
        $socketClient->close();
    }
    
    public function testGetSocketNameException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->getLocalSocketName();
    }
    
    public function testSetBlockingException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->setBlocking();
    }
    
    public function testSetReadBuffer()
    {
        $socketClient = new Peach_Socket_Client();
        
        $filename = dirname(__FILE__) . '/_files/test.txt';
        
        $socketClient->open($filename);
        
        $socketClient->setReadBuffer(100);
        
        $socketClient->close();
    }
    
    public function testSetReadBufferException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->setReadBuffer(100);
    }
    
    public function testTruncate()
    {
        $socketClient = new Peach_Socket_Client();
        
        $filename = dirname(__FILE__) . '/_files/out.txt';
        
        $socketClient->open($filename, 'w');
        
        $socketClient->truncate(0);
        
        $socketClient->close();
    }
    
    public function testTruncateException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->truncate(0);
    }
    
    public function testSetWriteBufferException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->setWriteBuffer(100);
    }
    
    public function testEof()
    {
        $socketClient = new Peach_Socket_Client();
        
        $filename = dirname(__FILE__) . '/_files/test.txt';
        
        $socketClient->open($filename);
        
        $this->assertFalse($socketClient->eof());
        $socketClient->getContents();
        $this->assertTrue($socketClient->eof());
        
        $socketClient->close();
    }
    
    public function testEofException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->eof();
    }
    
    public function testFlush()
    {
        $socketClient = new Peach_Socket_Client();
        
        $filename = dirname(__FILE__) . '/_files/out.txt';
        
        $socketClient->open($filename, 'w');
        
        $socketClient->flush();
        
        $socketClient->close();
    }
    
    public function testFlushException()
    {
        $socketClient = new Peach_Socket_Client();
        
        $this->setExpectedException('Peach_Socket_Client_Exception');
        $socketClient->flush();
    }
}

/* EOF */
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
 * Peach_Cache tests
 */
class PeachTest_Cache_Adapter_File_Test extends PeachTest_TestCase
{
    public function testConstructor()
    {
        new Peach_Cache_Adapter_File();
    }
    
    public function testSetOptions()
    {
        $cache = new Peach_Cache_Adapter_File();
        
        $options = array(
            Peach_Cache_Adapter_File::OPT_LIFETIME => 1000
        );
        $optionsObj = new Peach_Config($options);
        
        $cache->setOptions($options);
        $cache->setOptions($optionsObj);
    }
    
    public function testLoad()
    {
        $options = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        $cache = new Peach_Cache_Adapter_File($options);
        
        $data = 'Cached data';
        $id = 'cache-id';
        
        $this->assertNull($cache->save($data, $id));
        $this->assertEquals($data, $cache->load($id));
        $this->assertNull($cache->remove($id));
    }
    
    public function testLoadNotExisting()
    {
        $options = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        $cache = new Peach_Cache_Adapter_File($options);
        
        $id = 'cache-id';
        
        $this->assertNull($cache->load($id));
    }
    
    public function testSaveNotExistingPermsCheck()
    {
        $options = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_filesNotExisting/',
            Peach_Cache_Adapter_File::OPT_CHECK_PERMISSIONS => true
        );
        $cache = new Peach_Cache_Adapter_File($options);
        
        $data = 'Cached data';
        $id = 'cache-id';
        
        $this->setExpectedException('Peach_Cache_Exception');
        $cache->save($data, $id);
    }
    
    public function testSaveNotExistingNoPermsCheck()
    {
        $options = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_filesNotExisting/',
            Peach_Cache_Adapter_File::OPT_CHECK_PERMISSIONS => false
        );
        $cache = new Peach_Cache_Adapter_File($options);
        
        $data = 'Cached data';
        $id = 'cache-id';
        
        $this->setExpectedException('Peach_Cache_Exception');
        $cache->save($data, $id);
    }
    
    public function testLoadExpired()
    {
        $lifetime = 100;
        
        $options = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/',
            Peach_Cache_Adapter_File::OPT_LIFETIME => $lifetime
        );
        $cache = new Peach_Cache_Adapter_File($options);
        
        $data = 'Cached data';
        $id = 'cache-id';
        
        $this->assertNull($cache->save($data, $id));
        $this->assertNull($cache->touch($id, (time() - $lifetime - 1)));
        $this->assertNull($cache->load($id));
    }
    
    public function testToken()
    {
        $options = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        $cache = new Peach_Cache_Adapter_File($options);
        
        $data = 'Cached data';
        $id = 'cache-id';
        
        $tokens = array(
            Peach_Cache_Adapter_Abstract::TOKEN_METHOD_NONE,
            Peach_Cache_Adapter_Abstract::TOKEN_METHOD_MD5,
            Peach_Cache_Adapter_Abstract::TOKEN_METHOD_CRC32,
            Peach_Cache_Adapter_Abstract::TOKEN_METHOD_ADLER32,
            Peach_Cache_Adapter_Abstract::TOKEN_METHOD_CLEAN_FILE
        );
        
        foreach ($tokens as $token) {
            $tokenOptions = array(
                Peach_Cache_Adapter_Abstract::OPT_TOKEN_METHOD => $token
            );
            $cache->setOptions($tokenOptions);
            $this->assertNull($cache->save($data, $id));
            $this->assertEquals($data, $cache->load($id));
            $this->assertNull($cache->remove($id));
        }
    }
    
    public function testTokenException()
    {
        $options = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/',
            Peach_Cache_Adapter_File::OPT_TOKEN_METHOD => 'not-existing-method'
        );
        
        $cache = new Peach_Cache_Adapter_File($options);
        
        $data = 'Cached data';
        $id = 'cache-id';
        
        $this->setExpectedException('Peach_Cache_Exception');
        $cache->save($data, $id);
    }
    
    public function testNoLock()
    {
        $options = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/',
            Peach_Cache_Adapter_File::OPT_FILE_LOCKING => false
        );
        $cache = new Peach_Cache_Adapter_File($options);
        
        $data = 'Cached data';
        $id = 'cache-id';
        
        $this->assertNull($cache->remove($id));
        $this->assertNull($cache->save($data, $id));
        $this->assertEquals($data, $cache->load($id));
        $this->assertNull($cache->remove($id));
    }
    
    public function testLock()
    {
        $options = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/',
            Peach_Cache_Adapter_File::OPT_FILE_LOCKING => true
        );
        $cache = new Peach_Cache_Adapter_File($options);
        
        $data = 'Cached data';
        $id = 'cache-id';
        
        $this->assertNull($cache->remove($id));
        $this->assertNull($cache->save($data, $id));
        $this->assertEquals($data, $cache->load($id));
        $this->assertNull($cache->remove($id));
    }
    
    public function testCleanException()
    {
        $cache = new Peach_Cache_Adapter_File();
        $this->setExpectedException('Peach_Cache_Exception');
        $cache->clean('non-existing');
    }
    
    public function testCleanAll()
    {
        $cache = new Peach_Cache_Adapter_File();
        $this->assertNull($cache->clean(Peach_Cache::CLEANING_MODE_ALL));
    }
    
    public function testCleanOld()
    {
        $cache = new Peach_Cache_Adapter_File();
        $this->assertNull($cache->clean(Peach_Cache::CLEANING_MODE_OLD));
    }
    
    public function testCleanMatchingTag()
    {
        $cache = new Peach_Cache_Adapter_File();
        $this->setExpectedException('Peach_Cache_Exception');
        $cache->clean(Peach_Cache::CLEANING_MODE_MATCHING_TAG);
    }
    
    public function testCleanNotMatchingTag()
    {
        $cache = new Peach_Cache_Adapter_File();
        $this->setExpectedException('Peach_Cache_Exception');
        $cache->clean(Peach_Cache::CLEANING_MODE_NOT_MATCHING_TAG);
    }
    
    public function testCleanMatchingAnyTag()
    {
        $cache = new Peach_Cache_Adapter_File();
        $this->setExpectedException('Peach_Cache_Exception');
        $cache->clean(Peach_Cache::CLEANING_MODE_MATCHING_ANY_TAG);
    }
    
    public function testTest()
    {
        $options = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        $cache = new Peach_Cache_Adapter_File($options);
        
        $data = 'Testing. This should match the cache.';
        $id = 'cache-id';
        
        $this->assertFalse($cache->test($id));
        $this->assertNull($cache->save($data, $id));
        $this->assertTrue($cache->test($id));
        $this->assertNull($cache->remove($id));
    }
    
    public function testTouch()
    {
        $options = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        $cache = new Peach_Cache_Adapter_File($options);
        
        $data = 'Cached data';
        $id = 'cache-id';
        
        $this->assertNull($cache->remove($id));
        $this->assertNull($cache->save($data, $id));
        $this->assertNull($cache->touch($id));
        $this->assertNull($cache->touch($id, time()));
        $this->assertNull($cache->remove($id));
    }
}

/* EOF */
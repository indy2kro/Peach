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
class PeachTest_Cache_Test extends PeachTest_TestCase
{
    public function testConstructor()
    {
        $options = array(
            Peach_Cache::OPT_ENABLED => true
        );
        
        $adapterOptions = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        
        new Peach_Cache();
        new Peach_Cache(Peach_Cache::ADAPTER_FILE);
        new Peach_Cache(Peach_Cache::ADAPTER_FILE, $options);
        new Peach_Cache(Peach_Cache::ADAPTER_FILE, $options, $adapterOptions);
    }
    
    public function testSetOptions()
    {
        $cache = new Peach_Cache();
        
        $options = array(
            Peach_Cache::OPT_ENABLED => true
        );
        $optionsObj = new Peach_Config($options);
        
        $cache->setOptions($options);
        $cache->setOptions($optionsObj);
    }
    
    public function testGetAdapter()
    {
        $cache = new Peach_Cache();
        
        $adapter = $cache->getAdapter();
        
        $this->assertInstanceOf('Peach_Cache_Adapter_Abstract', $adapter);
    }
    
    public function testSetFilters()
    {
        $cache = new Peach_Cache();
        $filter1 = new Peach_Cache_Filter_Gzip();
        $filter2 = new Peach_Cache_Filter_Serialize();
        
        $cache->setFilters(array());
        
        $this->assertCount(0, $cache->getFilters());
        
        $cache->addFilter($filter1);
        
        $this->assertCount(1, $cache->getFilters());
        
        $cache->addFilter($filter2);
        
        $this->assertCount(2, $cache->getFilters());
    }
    
    public function testLoad()
    {
        $options = array(
            Peach_Cache::OPT_ENABLED => true
        );
        
        $adapterOptions = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        
        $cache = new Peach_Cache(Peach_Cache::ADAPTER_FILE, $options, $adapterOptions);
        
        $data = 'Testing. This should match the cache.';
        $id = 'cache-id';
        
        $cache->save($data, $id);
        $this->assertEquals($data, $cache->load($id));
        $cache->remove($id);
    }
    
    public function testLoadNotEnabled()
    {
        $options = array(
            Peach_Cache::OPT_ENABLED => false
        );
        
        $adapterOptions = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        
        $cache = new Peach_Cache(Peach_Cache::ADAPTER_FILE, $options, $adapterOptions);
        
        $data = 'Testing. This should match the cache.';
        $id = 'cache-id';
        
        $cache->save($data, $id);
        $this->assertFalse($cache->load($id));
        $cache->remove($id);
    }
    
    public function testLoadNotInCache()
    {
        $options = array(
            Peach_Cache::OPT_ENABLED => true
        );
        
        $adapterOptions = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        
        $cache = new Peach_Cache(Peach_Cache::ADAPTER_FILE, $options, $adapterOptions);
        
        $id = 'cache-id';
        
        $this->assertFalse($cache->load($id));
    }
    
    public function testSerialize()
    {
        $options = array(
            Peach_Cache::OPT_ENABLED => true,
            Peach_Cache::OPT_AUTOMATIC_SERIALIZATION => true
        );
        
        $adapterOptions = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        
        $data = 'Testing. This should match the cache.';
        $id = 'cache-id2';
        
        $cache = new Peach_Cache(Peach_Cache::ADAPTER_FILE, $options, $adapterOptions);
        
        $cache->save($data, $id);
        $this->assertEquals($data, $cache->load($id));
        $cache->remove($id);
    }
    
    public function testMemoryLeakSave()
    {
        $options = array(
        );
        
        $adapterOptions = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        
        $data = 'Testing. This should match the cache.';
        $id = 'cache-id-save';
        
        $cache = new Peach_Cache(Peach_Cache::ADAPTER_FILE, $options, $adapterOptions);
        
        $iterations = 10;
        
        $client = null;
        $memoryUsed = null;
        $previousMemoryUsed = null;
        
        for ($counter = 0; $counter < $iterations; $counter++) {
            $cache->save($data, $id);
            
            $previousMemoryUsed = $memoryUsed;
            $memoryUsed = memory_get_usage();
            
            if (!is_null($previousMemoryUsed) && $memoryUsed > $previousMemoryUsed) {
                $this->fail('Memory leak detected! Current memory usage: '
                        . $memoryUsed . ' (' . $this->_friendlySize($memoryUsed) . ')' . ', previous: '
                        . $previousMemoryUsed . ' (' . $this->_friendlySize($previousMemoryUsed) . ')');
            }
        }
        
        $cache->remove($id);
    }
    
    public function testMemoryLeakLoad()
    {
        $options = array(
        );
        
        $adapterOptions = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        
        $data = 'Testing. This should match the cache.';
        $id = 'cache-id-load';
        
        $cache = new Peach_Cache(Peach_Cache::ADAPTER_FILE, $options, $adapterOptions);
        $cache->save($data, $id);
        
        $iterations = 10;
        
        $client = null;
        $memoryUsed = null;
        $previousMemoryUsed = null;
        
        for ($counter = 0; $counter < $iterations; $counter++) {
            $this->assertEquals($data, $cache->load($id));
            
            $previousMemoryUsed = $memoryUsed;
            $memoryUsed = memory_get_usage();
            
            if (!is_null($previousMemoryUsed) && $memoryUsed > $previousMemoryUsed) {
                $this->fail('Memory leak detected! Current memory usage: '
                        . $memoryUsed . ' (' . $this->_friendlySize($memoryUsed) . ')' . ', previous: '
                        . $previousMemoryUsed . ' (' . $this->_friendlySize($previousMemoryUsed) . ')');
            }
        }
        
        $cache->remove($id);
    }
    
    public function testTest()
    {
        $options = array(
            Peach_Cache::OPT_ENABLED => true
        );
        
        $adapterOptions = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        
        $cache = new Peach_Cache(Peach_Cache::ADAPTER_FILE, $options, $adapterOptions);
        
        $data = 'Testing. This should match the cache.';
        $id = 'cache-id';
        
        $this->assertFalse($cache->test($id));
        $cache->save($data, $id);
        $this->assertTrue($cache->test($id));
        $cache->remove($id);
    }
    
    public function testTestNotEnabled()
    {
        $options = array(
            Peach_Cache::OPT_ENABLED => false
        );
        
        $adapterOptions = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        
        $cache = new Peach_Cache(Peach_Cache::ADAPTER_FILE, $options, $adapterOptions);
        
        $data = 'Testing. This should match the cache.';
        $id = 'cache-id';
        
        $this->assertFalse($cache->test($id));
        $cache->save($data, $id);
        $this->assertFalse($cache->test($id));
        $cache->remove($id);
    }
    
    public function testSaveIgnoreUserAbort()
    {
        $options = array(
            Peach_Cache::OPT_ENABLED => true,
            Peach_Cache::OPT_IGNORE_USER_ABORT => true
        );
        
        $adapterOptions = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        
        $cache = new Peach_Cache(Peach_Cache::ADAPTER_FILE, $options, $adapterOptions);
        
        $data = 'Testing. This should match the cache.';
        $id = 'cache-id';
        
        $cache->save($data, $id);
        $cache->remove($id);
    }
    
    public function testFilters()
    {
        $options = array(
            Peach_Cache::OPT_ENABLED => true,
            Peach_Cache::OPT_IGNORE_USER_ABORT => true
        );
        
        $adapterOptions = array(
            Peach_Cache_Adapter_File::OPT_CACHE_DIR => dirname(__FILE__) . '/_files/'
        );
        
        $cache = new Peach_Cache(Peach_Cache::ADAPTER_FILE, $options, $adapterOptions);
        
        $filter1 = new Peach_Cache_Filter_Serialize();
        $filter2 = new Peach_Cache_Filter_Gzip();
        
        $cache->addFilter($filter1);
        $cache->addFilter($filter2);
        
        $data = 'Testing. This should match the cache.';
        $id = 'cache-id';
        
        $cache->save($data, $id);
        $this->assertEquals($data, $cache->load($id));
        $cache->remove($id);
    }
}

/* EOF */
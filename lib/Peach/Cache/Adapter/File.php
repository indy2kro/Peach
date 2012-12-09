<?php
/**
 * Peach Library
 *
 * @category   Peach
 * @package    Peach_Cache
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Cache file adapter
 */
class Peach_Cache_Adapter_File extends Peach_Cache_Adapter_Abstract
{
    /*
     * Available options
     */
    const OPT_FILE_LOCKING = 'file_locking';
    const OPT_CACHE_DIR = 'cache_dir';
    const OPT_EXTENSION = 'extension';
    const OPT_CHECK_PERMISSIONS = 'check_permissions';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_LIFETIME => 3600,
        self::OPT_TOKEN_METHOD => self::TOKEN_METHOD_MD5,
        self::OPT_FILE_LOCKING => true,
        self::OPT_CACHE_DIR => '',
        self::OPT_EXTENSION => '.cache',
        self::OPT_CHECK_PERMISSIONS => true
    );
    
    /**
     * Load a cache item
     *
     * @param string $id Cache id
     * @return string|null Cached data
     */
    public function load($id)
    {
        // build cache file path
        $cachePath = $this->_buildPath($id);
        
        // check if cache file exists
        if (!file_exists($cachePath)) {
            return null;
        }
        
        // check if the cache is still available
        $cacheTest = $this->_test($cachePath);

        if (!$cacheTest) {
            return null;
        }
        
        // load cache data
        $cacheData = $this->_load($cachePath);

        return $cacheData;
    }
    
    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param string $id cache id
     * @return boolean
     */
    public function test($id)
    {
        // build cache file path
        $cachePath = $this->_buildPath($id);
        
        // check if cache file exists
        if (!file_exists($cachePath)) {
            return false;
        }
        
        // check if the cache is still available
        $cacheTest = $this->_test($cachePath);
        
        return $cacheTest;
    }

    /**
     * Save some string data into a cache record
     *
     * @param string $data Data to cache
     * @param string $id   Cache id
     * @param array  $tags Array of strings, the cache record will be tagged by each string entry
     * @return void
     */
    public function save($data, $id, Array $tags = array())
    {
        // build cache file path
        $cachePath = $this->_buildPath($id);
        
        // check if the cache is still available
        $this->_save($data, $cachePath);
    }

    /**
     * Remove a cache record
     *
     * @param string $id Cache id
     * @return boolean True if no problem
     */
    public function remove($id)
    {
        // build cache file path
        $cachePath = $this->_buildPath($id);
        
        // remove cache file
        $this->_remove($cachePath);
    }

    /**
     * Clean some cache records
     *
     * Available modes are :
     * Peach_Cache::CLEANING_MODE_ALL (default)    => remove all cache entries ($tags is not used)
     * Peach_Cache::CLEANING_MODE_OLD              => remove too old cache entries ($tags is not used)
     * Peach_Cache::CLEANING_MODE_MATCHING_TAG     => remove cache entries matching all given tags
     * Peach_Cache::CLEANING_MODE_NOT_MATCHING_TAG => remove cache entries not matching one of the given tags
     * Peach_Cache::CLEANING_MODE_MATCHING_ANY_TAG => remove cache entries matching any given tags
     *
     * @param string $mode Clean mode
     * @param array  $tags Array of tags
     * @return void
     * @throws Peach_Cache_Exception
     */
    public function clean($mode = Peach_Cache::CLEANING_MODE_ALL, Array $tags = array())
    {
        switch ($mode) {
            case Peach_Cache::CLEANING_MODE_ALL:
                // TODO
                break;
            
            case Peach_Cache::CLEANING_MODE_OLD:
                // TODO
                break;
            
            case Peach_Cache::CLEANING_MODE_MATCHING_TAG:     // intentionally omitted break
            case Peach_Cache::CLEANING_MODE_NOT_MATCHING_TAG: // intentionally omitted break
            case Peach_Cache::CLEANING_MODE_MATCHING_ANY_TAG:
                throw new Peach_Cache_Exception('Tags are not supported for this adapter');
                break;
            
            default:
                throw new Peach_Cache_Exception('Unknown cleaning mode: "' . $mode . '"');
                break;
        }
    }
    
    /**
     * Test if a cache is available or not
     *
     * @param string $cachePath
     * @return boolean
     */
    protected function _test($cachePath)
    {
        $lastModifiedTime = $this->_getLastModifiedTime($cachePath);
        $expireTime = $this->_computeExpireTime();

        if ($lastModifiedTime <= $expireTime) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Save cache to path
     * 
     * @param string $data
     * @param string $cachePath
     * @return void
     * @throws Peach_Cache_Exception
     */
    protected function _save($data, $cachePath)
    {
        Peach_Error_Handler::start();
        $handle = fopen($cachePath, 'wb+');
        $error = Peach_Error_Handler::stop();

        if (!is_null($error)) {
            throw new Peach_Cache_Exception('Failed to open cache file "' . $cachePath . '" for writing: ' . $error);
        }
        
        if (false === $handle) {
            throw new Peach_Cache_Exception('Failed to open cache file "' . $cachePath . '" for writing');
        }
        
        if ($this->_options[self::OPT_FILE_LOCKING]) {
            // lock file
            Peach_Error_Handler::start();
            $lockResult = flock($handle, LOCK_EX);
            $error = Peach_Error_Handler::stop();

            if (!is_null($error)) {
                throw new Peach_Cache_Exception('Failed to lock cache file "' . $cachePath . '": ' . $error);
            }

            if (false === $lockResult) {
                throw new Peach_Cache_Exception('Failed to lock cache file "' . $cachePath . '"');
            }

            // write data to cache file
            Peach_Error_Handler::start();
            $writeResult = fwrite($handle, $data);
            $error = Peach_Error_Handler::stop();
            
            if (!is_null($error)) {
                Peach_Error_Handler::start();
                flock($handle, LOCK_UN);
                fclose($handle);
                Peach_Error_Handler::stop();
                
                throw new Peach_Cache_Exception('Failed to write to cache file "' . $cachePath . '": ' . $error);
            }

            if (false === $writeResult) {
                Peach_Error_Handler::start();
                flock($handle, LOCK_UN);
                fclose($handle);
                Peach_Error_Handler::stop();
                
                throw new Peach_Cache_Exception('Failed to write to cache file "' . $cachePath . '"');
            }

            // unlock file
            Peach_Error_Handler::start();
            $unlockResult = flock($handle, LOCK_UN);
            $error = Peach_Error_Handler::stop();

            if (!is_null($error)) {
                throw new Peach_Cache_Exception('Failed to unlock cache file "' . $cachePath . '": ' . $error);
            }

            if (false === $unlockResult) {
                throw new Peach_Cache_Exception('Failed to unlock cache file "' . $cachePath . '"');
            }
        } else {
            $writeResult = file_put_contents($handle);
        }
        
        if (false === $writeResult) {
            throw new Peach_Cache_Exception('Failed to read from the cache file "' . $cachePath . '"');
        }
        
        // close file handle
        Peach_Error_Handler::start();
        fclose($handle);
        Peach_Error_Handler::stop();
    }
    
    /**
     * Load cache from path
     * 
     * @param string $cachePath
     * @return string
     * @throws Peach_Cache_Exception
     */
    protected function _load($cachePath)
    {
        if (!is_file($cachePath)) {
            throw new Peach_Cache_Exception('Cache file "' . $cachePath . '" does not exist');
        }
        
        Peach_Error_Handler::start();
        $handle = fopen($cachePath, 'rb');
        $error = Peach_Error_Handler::stop();

        if (!is_null($error)) {
            throw new Peach_Cache_Exception('Failed to open cache file "' . $cachePath . '" for writing: ' . $error);
        }
        
        if (false === $handle) {
            throw new Peach_Cache_Exception('Failed to open cache file "' . $cachePath . '" for writing');
        }
        
        if ($this->_options[self::OPT_FILE_LOCKING]) {
            // lock file
            Peach_Error_Handler::start();
            $lockResult = flock($handle, LOCK_EX);
            $error = Peach_Error_Handler::stop();

            if (!is_null($error)) {
                throw new Peach_Cache_Exception('Failed to lock cache file "' . $cachePath . '": ' . $error);
            }

            if (false === $lockResult) {
                throw new Peach_Cache_Exception('Failed to lock cache file "' . $cachePath . '"');
            }

            // read cache contents
            $cache = stream_get_contents($handle);
            
            // unlock file
            Peach_Error_Handler::start();
            $unlockResult = flock($handle, LOCK_UN);
            $error = Peach_Error_Handler::stop();

            if (!is_null($error)) {
                throw new Peach_Cache_Exception('Failed to unlock cache file "' . $cachePath . '": ' . $error);
            }

            if (false === $unlockResult) {
                throw new Peach_Cache_Exception('Failed to unlock cache file "' . $cachePath . '"');
            }
        } else {
            $cache = stream_get_contents($handle);
        }
        
        if (false === $cache) {
            throw new Peach_Cache_Exception('Failed to read from the cache file "' . $cachePath . '"');
        }
        
        // close file handle
        Peach_Error_Handler::start();
        fclose($handle);
        Peach_Error_Handler::stop();
        
        return $cache;
    }
    
    /**
     * Compute expire timestamp
     * 
     * @return integer
     */
    protected function _computeExpireTime()
    {
        $expireTime = time() - $this->_options[self::OPT_LIFETIME];
        
        return $expireTime;
    }
    
    /**
     * Get last modified time
     * 
     * @param string $cachePath
     * @return integer
     * @throws Peach_Cache_Exception
     */
    protected function _getLastModifiedTime($cachePath)
    {
        if ($this->_options[self::OPT_CHECK_PERMISSIONS]) {
            if (!file_exists($cachePath)) {
                throw new Peach_Cache_Exception('Cache file "' . $cachePath . '" does not exist');
            }
        }

        Peach_Error_Handler::start();
        // get last modified time
        $lastModifiedTime = filemtime($cachePath);
        $error = Peach_Error_Handler::stop();

        if (!is_null($error)) {
            throw new Peach_Cache_Exception('Failed to check last modified time for cache file "' . $cachePath . '": ' . $error);
        }
        
        if (false === $lastModifiedTime) {
            throw new Peach_Cache_Exception('Failed to retrieve last modified time for cache file "' . $cachePath . '"');
        }
        
        return $lastModifiedTime;
    }
    
    /**
     * Remove cache file
     * 
     * @param string $cachePath
     * @return void
     * @throws Peach_Cache_Exception
     */
    protected function _remove($cachePath)
    {
        if (!file_exists($cachePath)) {
            return null;
        }

        Peach_Error_Handler::start();
        // remove cache file
        $removeResult = unlink($cachePath);
        $error = Peach_Error_Handler::stop();

        if (!is_null($error)) {
            throw new Peach_Cache_Exception('Failed to delete cache file "' . $cachePath . '": ' . $error);
        }
        
        if (false === $removeResult) {
            throw new Peach_Cache_Exception('Failed to delete cache file "' . $cachePath . '"');
        }
    }
    
    /**
     * Build file path for a cache file
     * 
     * @param string $id
     * @return string
     * @throws Peach_Cache_Exception
     */
    protected function _buildPath($id)
    {
        $path = $this->_options[self::OPT_CACHE_DIR];
        
        if ($this->_options[self::OPT_CHECK_PERMISSIONS]) {
            if (!is_dir($path)) {
                throw new Peach_Cache_Exception('Cache directory "' . $path . '" does not exist');
            }

            if (!is_writable($path)) {
                throw new Peach_Cache_Exception('Cache directory "' . $path . '" is not writable');
            }
        }
        
        // get token based on id
        $token = $this->_getToken($id);
        
        $path .= $token . $this->_options[self::OPT_EXTENSION];
        
        return $path;
    }
}

/* EOF */
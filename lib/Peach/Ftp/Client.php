<?php
/**
 * Peach Library
 *
 * @category   Peach
 * @package    Peach_Ftp
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Peach FTP client
 */
class Peach_Ftp_Client
{
    /*
     * Allowed options 
     */
    const OPT_TIMEOUT = 'timeout';
    const OPT_MODE = 'mode';
    const OPT_HOST = 'host';
    const OPT_PORT = 'port';
    const OPT_USERNAME = 'username';
    const OPT_PASSWORD = 'password';
    const OPT_SSL = 'ssl';
    const OPT_PASV = 'pasv';
    const OPT_USE_TMP_FILE = 'use_tmp_file';
    const OPT_TMP_FILE_EXTENSION = 'tmp_file_extension';
    const OPT_OVERWRITE = 'overwrite';
    const OPT_REMOVE_UPLOADED = 'remove_uploaded';
    const OPT_LOG = 'logger';
    const OPT_RESUME = 'resume';
    const OPT_RETRY_ENABLE = 'retry_enable';
    const OPT_RETRY_COUNT = 'retry_count';
    const OPT_RETRY_SLEEP = 'retry_sleep';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_TIMEOUT => 90,
        self::OPT_MODE => FTP_BINARY,
        self::OPT_HOST => null,
        self::OPT_PORT => 21,
        self::OPT_USERNAME => null,
        self::OPT_PASSWORD => null,
        self::OPT_SSL => false,
        self::OPT_PASV => null,
        self::OPT_USE_TMP_FILE => false,
        self::OPT_TMP_FILE_EXTENSION => '.tmp',
        self::OPT_OVERWRITE => false,
        self::OPT_REMOVE_UPLOADED => false,
        self::OPT_LOG => null,
        self::OPT_RESUME => false,
        self::OPT_RETRY_ENABLE => false,
        self::OPT_RETRY_COUNT => 3,
        self::OPT_RETRY_SLEEP => 1
    );

    /**
     * Connection resource
     * 
     * @var resource
     */
    protected $_connection;
    
    /**
     * Remote directory
     *
     * @var string
     */
    protected $_remoteDirectory = '/';

    /**
     * Constructor
     *
     * @param array|Peach_Config $options Options to set
     * @return void
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }
    
    /**
     * Set options
     * 
     * @param array|Peach_Config $options Options to set
     * @return void
     */
    public function setOptions($options = array())
    {
        if ($options instanceof Peach_Config) {
            $options = $options->toArray();
        }
        
        $this->_options = array_merge($this->_options, $options);
    }
    
    /**
     * Set timeout
     * 
     * @param integer $timeout Timeout value in seconds
     * @return boolean
     */
    public function setTimeout($timeout)
    {
        $this->_options[self::OPT_TIMEOUT] = $timeout;
        
        if (!$this->isConnected()) {
            return true;
        }
        
        // set server option
        $setOption = $this->setServerOption(FTP_TIMEOUT_SEC, $timeout);
        
        return $setOption;
    }

    /**
     * Connect to ftp server
     *
     * @throws Peach_Ftp_Client_Exception
     */
    public function connect()
    {
        if ($this->isConnected()) {
            // already connected, nothing to do
            return null;
        }
        
        $connected = false;
        $currentTry = 0;
        
        do {
            $currentTry++;
            
            try {
                $this->_log('Try to connect to FTP server', Peach_Log::DEBUG);
                $this->_connect();
                $connected = true;
            } catch (Peach_Ftp_Client_Exception $ex) {
                if ($this->_options[self::OPT_RETRY_ENABLE] && ($currentTry <= $this->_options[self::OPT_RETRY_COUNT])) {
                    $this->_log('Failed to connect to FTP server, sleep before retry', Peach_Log::NOTICE);
                    sleep($this->_options[self::OPT_RETRY_SLEEP]);
                }
            }
        } while (!$connected && $this->_options[self::OPT_RETRY_ENABLE] && ($currentTry <= $this->_options[self::OPT_RETRY_COUNT]));
        
        // rethrow exception
        if (!$connected) {
            throw $ex;
        }
        
        // set passive mode
        if (!is_null($this->_options[self::OPT_PASV])) {
            $this->setPasv($this->_options[self::OPT_PASV]);
        }

        $this->_log('Connected to FTP server ' . $this->_options[self::OPT_HOST] . ':' . $this->_options[self::OPT_PORT], Peach_Log::INFO);
    }
    
    /**
     * Check if connection is established with FTP server
     * 
     * @return boolean
     */
    public function isConnected()
    {
        return !is_null($this->_connection);
    }
     
    /**
     * Get the FTP connection
     * 
     * @return resource
     */
    public function getConnection()
    {
        $this->connect();
         
        return $this->_connection;
    }
    
    /**
     * Set server option
     * 
     * @param integer $optionName  Options name
     * @param mixed   $optionValue Options value
     * @throws Peach_Ftp_Client_Exception
     * @return boolean
     */
    public function setServerOption($optionName, $optionValue)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        $result = @ftp_set_option($this->_connection, $optionName, $optionValue);
        
        if (false === $result) {
            $this->_log('Failed to set option ' . $optionName . ' to value ' . $optionValue, Peach_Log::NOTICE);
        }
        
        return $result;
    }
    
    /**
     * Get server option
     * 
     * @param integer $optionName Options name
     * @throws Peach_Ftp_Client_Exception
     * @return boolean
     */
    public function getServerOption($optionName)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        $result = @ftp_get_option($this->_connection, $optionName);
        
        if (false === $result) {
            $this->_log('Failed to get option ' . $optionName, Peach_Log::NOTICE);
        }
        
        return $result;
    }
    
    /**
     * Move one directory up
     * 
     * @throws Peach_Ftp_Client_Exception 
     * @return boolean
     */
    public function cdUp()
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        // run cdup command
        $result = @ftp_cdup($this->_connection);
        
        return $result;
    }
     
    /**
     * Set passive mode
     * 
     * @param boolean $pasv Passive mode
     * @throws Peach_Ftp_Client_Exception 
     * @return boolean
     */
    public function setPasv($pasv = true)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        $pasvText = $pasv ? 'TRUE' : 'FALSE';
        
        $this->_log('Set passive mode to ' . $pasvText, Peach_Log::DEBUG);
        
        $result = @ftp_pasv($this->_connection, $pasv);
        
        return $result;
    }

    /**
     * Close ftp connection
     *
     * @throws Peach_Ftp_Client_Exception
     * @return void
     */
    public function close()
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        $result = @ftp_close($this->_connection);

        if (!$result) {
            throw new Peach_Ftp_Client_Exception('Failed to disconnect from FTP server.');
        }

        $this->_log('Closed connection to FTP server', Peach_Log::INFO);

        $this->_connection = null;
    }
    
    /**
     * Get current directory name
     * 
     * @throws Peach_Ftp_Client_Exception 
     * @return string 
     */
    public function getPwd()
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        $currentDirectory = @ftp_pwd($this->_connection);
        
        if (false === $currentDirectory) {
            $this->_log('Failed to get current directory name', Peach_Log::NOTICE);
        }
        
        return $currentDirectory;
    }

    /**
     * Upload a file to ftp server
     *
     * @param string             $localFile         Local file path
     * @param string             $remoteFile        Remote file path
     * @param array|Peach_Config $contextualOptions Contextual options
     * @throws Peach_Ftp_Client_Exception
     * @return boolean
     */
    public function uploadFile($localFile, $remoteFile, $contextualOptions = array())
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        // set contextual options
        $existingOptions = $this->_options;
        $this->setOptions($contextualOptions);
        
        if (!file_exists($localFile)) {
            $this->_log('Local file does not exist: ' . $localFile, Peach_Log::NOTICE);

            // reset options
            $this->_options = $existingOptions;
            return false;
        }

        $remoteFileName = basename($remoteFile);
        $remoteDirectory = $this->_dirname($remoteFile);

        // create remote directory if it doesn't exist
        $this->createRemoteDirectory($remoteDirectory);

        $chdirResult = $this->chdir($remoteDirectory);

        if (!$chdirResult) {
            $this->_log('Failed to change directory to '. $remoteDirectory, Peach_Log::NOTICE);
            
            // reset options
            $this->_options = $existingOptions;
            return false;
        }

        // check if file already exists
        $fileExists = $this->fileExists($remoteFileName);

        if ($fileExists) {
            if ($this->_options[self::OPT_OVERWRITE]) {
                $this->_log('File ' . $remoteFile . ' exists, overwriting', Peach_Log::INFO);

                $deleteResult = $this->deleteFile($remoteFile);

                if (!$deleteResult) {
                    $this->_log('Failed to delete remote file ' . $remoteFile . ', abort copy', Peach_Log::NOTICE);
                    
                    // reset options
                    $this->_options = $existingOptions;
                    return false;
                }
            } elseif ($this->_options[self::OPT_RESUME]) {
                $localSize = filesize($localFile);
                $remoteSize = $this->getFileSize($remoteFile);
                
                if (false === $remoteSize) {
                    $this->_log('Failed to get file size for remote file: ' . $remoteFile, Peach_Log::NOTICE);
                    
                    // reset options
                    $this->_options = $existingOptions;
                    return false;
                }
                
                // nothing to do, file size matches
                if ($localSize == $remoteSize) {
                    $this->_log('Remote file already exists and has the correct size: ' . $remoteFile, Peach_Log::DEBUG);
                    
                    // reset options
                    $this->_options = $existingOptions;
                    return true;
                }
                
                // resume upload
                $this->_log('Resume upload for file: ' . $remoteFile . ' from offset ' . $remoteSize, Peach_Log::DEBUG);
                $uploadResult = $this->_uploadFile($localFile, $remoteFile, $remoteSize);
                
                // reset options
                $this->_options = $existingOptions;
                return $uploadResult;
            } else {
                $this->_log('Can not upload ' . $localFile . ', destination already exists and overwrite is disabled', Peach_Log::DEBUG);
                
                // reset options
                $this->_options = $existingOptions;
                return false;
            }
        }

        if ($this->_options[self::OPT_USE_TMP_FILE]) {
            // build the remote file name
            $remoteFileNameFull = $remoteFile . $this->_options[self::OPT_TMP_FILE_EXTENSION];

            // upload the file with extension, always overwrite the partial file
            $existingOptionsPartial = $this->_options;
            $contextualOptionsPartial = array(
                self::OPT_OVERWRITE => true
            );
            $this->setOptions($contextualOptionsPartial);
            
            // upload file
            $uploadResult = $this->_uploadFile($localFile, $remoteFileNameFull);
            
            // reset existing options
            $this->_options = $existingOptionsPartial;

            if ($uploadResult) {
                // rename file to final name
                $renameResult = $this->rename($remoteFileNameFull, $remoteFile);

                if ($renameResult) {
                    $this->_log('Successfully renamed ' . $remoteFileNameFull . ' to ' . $remoteFile, Peach_Log::DEBUG);
                } else {
                    $this->_log('Failed to rename ' . $remoteFileNameFull . ' to ' . $remoteFile, Peach_Log::NOTICE);
                }

                $uploadResult = $renameResult;
            }
        } else {
            // upload the file to the final location
            $uploadResult = $this->_uploadFile($localFile, $remoteFile);
        }

        if (!$uploadResult) {
            $this->_log('Failed to upload file: '. $localFile, Peach_Log::NOTICE);
            
            // reset options
            $this->_options = $existingOptions;
            return false;
        } else {
            if ($this->_options[self::OPT_REMOVE_UPLOADED]) {
                // upload was successful, remove the local file
                $unlinkResult = unlink($localFile);

                if ($unlinkResult) {
                    $this->_log('Removed local file: ' . $localFile . ' after successful upload', Peach_Log::DEBUG);
                } else {
                    $this->_log('Failed to remove local file: ' . $localFile . ' after successful upload', Peach_Log::NOTICE);
                }
            }
        }

        $this->_log('Successfully uploaded file: '. $remoteFile, Peach_Log::INFO);
        
        // reset options
        $this->_options = $existingOptions;
        return true;
    }
    
    /**
     * Download a file
     * 
     * @param string             $localFile         Local file path
     * @param string             $remoteFile        Remote file path
     * @param array|Peach_Config $contextualOptions Contextual options
     * @throws Peach_Ftp_Client_Exception
     * @return boolean
     */
    public function downloadFile($localFile, $remoteFile, $contextualOptions = array())
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        // set contextual options
        $existingOptions = $this->_options;
        $this->setOptions($contextualOptions);
        
        if (file_exists($localFile)) {
            if (!$this->_options[self::OPT_OVERWRITE] && !$this->_options[self::OPT_RESUME]) {
                $this->_log('Local file already exists and overwrite is disabled: ' . $localFile, Peach_Log::DEBUG);
                    
                // reset options
                $this->_options = $existingOptions;
                return false;
            }
            
            if ($this->_options[self::OPT_RESUME] && !$this->_options[self::OPT_OVERWRITE]) {
                $localSize = filesize($localFile);
                $remoteSize = $this->getFileSize($remoteFile);
                
                if (false === $remoteSize) {
                    $this->_log('Failed to get file size for remote file: ' . $remoteFile, Peach_Log::NOTICE);
                    
                    // reset options
                    $this->_options = $existingOptions;
                    return false;
                }
                
                // nothing to do, file size matches
                if ($localSize == $remoteSize) {
                    $this->_log('Local file already exists and has the correct size: ' . $localFile, Peach_Log::DEBUG);
                    
                    // reset options
                    $this->_options = $existingOptions;
                    return true;
                }
                
                // resume download
                $this->_log('Resume download for file: ' . $remoteFile . ' from offset ' . $localSize, Peach_Log::DEBUG);
                $downloadResult = $this->_downloadFile($localFile, $remoteFile, $localSize);
                
                // reset options
                $this->_options = $existingOptions;
                return $downloadResult;
            }
        }
        
        if ($this->_options[self::OPT_USE_TMP_FILE]) {
            // build the remote file name
            $localFileFull = $localFile . $this->_options[self::OPT_TMP_FILE_EXTENSION];

            // download the file with extension, always overwrite the partial file
            $downloadResult = $this->_downloadFile($localFileFull, $remoteFile);

            if ($downloadResult) {
                // rename file to final name
                $renameResult = rename($localFileFull, $localFile);

                if ($renameResult) {
                    $this->_log('Successfully renamed ' . $localFileFull . ' to ' . $localFile, Peach_Log::DEBUG);
                } else {
                    $this->_log('Failed to rename ' . $localFileFull . ' to ' . $localFile, Peach_Log::NOTICE);
                }

                $downloadResult = $renameResult;
            }
        } else {
            // download the file
            $downloadResult = $this->_downloadFile($localFile, $remoteFile);
        }

        if (!$downloadResult) {
            $this->_log('Failed to download file: '. $remoteFile, Peach_Log::NOTICE);
            
            // reset options
            $this->_options = $existingOptions;
            return false;
        }

        $this->_log('Successfully downloaded file: '. $remoteFile, Peach_Log::INFO);
        
        // reset options
        $this->_options = $existingOptions;
        return true;
    }
    
    /**
     * Download directory
     * 
     * @param string             $localDirectory    Local directory
     * @param string             $remoteDirectory   Remote directory
     * @param array|Peach_Config $contextualOptions Contextual options
     * @return boolean
     * @throws Peach_Ftp_Client_Exception 
     */
    public function downloadDirectory($localDirectory, $remoteDirectory, $contextualOptions = array())
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        // set contextual options
        $existingOptions = $this->_options;
        $this->setOptions($contextualOptions);
        
        $localDirectory = rtrim($localDirectory, '/') . '/';
        $remoteDirectory = rtrim($remoteDirectory, '/') . '/';

        $this->_log('Download directory: ' . $remoteDirectory, Peach_Log::INFO);

        if (!$this->directoryExists($remoteDirectory)) {
            $this->_log('Remote directory does not exist: ' . $remoteDirectory, Peach_Log::NOTICE);

            // reset options
            $this->_options = $existingOptions;
            return false;
        }
        
        $directoryName = basename($remoteDirectory);
        
        // build local path
        $localPath = $localDirectory . $directoryName . '/';
        
        if (!is_dir($localPath)) {
            // create local path
            $mkdirResult = mkdir($localPath);

            if (!$mkdirResult) {
                $this->_log('Failed to create local directory: ' . $localPath, Peach_Log::NOTICE);

                // reset options
                $this->_options = $existingOptions;
                return false;
            }
        }
        
        $filesList = $this->getFilesList($remoteDirectory);
        
        $downloadResult = true;
        
        foreach ($filesList as $file) {
            $remotePath = $remoteDirectory . $file;
            
            if ($this->fileExists($remotePath)) {
                $downloadResult &= $this->downloadFile($localPath . $file, $remotePath);
            } else {
                $remotePath .= '/';

                $downloadResult &= $this->downloadDirectory($localPath, $remotePath);
            }
        }
        
        // reset options
        $this->_options = $existingOptions;
        return $downloadResult;    
    }

    /**
     * Download list of files/directories
     * 
     * @param array              $filesList         List of files and/or directories
     * @param string             $localDirectory    Local directory
     * @param array|Peach_Config $contextualOptions Contextual options
     * @throws Peach_Ftp_Client_Exception 
     * @return boolean
     */
    public function downloadList(Array $filesList, $localDirectory, $contextualOptions = array())
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        // set contextual options
        $existingOptions = $this->_options;
        $this->setOptions($contextualOptions);
        
        $downloadResult = true;
        
        foreach ($filesList as $remotePath) {
            $fileName = basename($remotePath);

            if ($this->directoryExists($remotePath)) {
                $downloadResult &= $this->downloadDirectory($remotePath, $localDirectory . $fileName . '/');
            } else {
                $downloadResult &= $this->downloadFile($remotePath, $localDirectory . $fileName);
            }
        }
        
        // reset options
        $this->_options = $existingOptions;
        return $downloadResult;
    }

    /**
     * Change remote directory
     *
     * @param string $remoteDirectory Remote directory
     * @return boolean
     */
    public function chdir($remoteDirectory)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        if ($this->_remoteDirectory == $remoteDirectory || '.' == $remoteDirectory) {
            // already in that directory, no need to chdir
            return true;
        }

        $this->_log('Chdir to ' . $remoteDirectory, Peach_Log::DEBUG);

        // change remote directory
        $chdirResult = @ftp_chdir($this->_connection, $remoteDirectory);

        if ($chdirResult) {
            $this->_remoteDirectory = $remoteDirectory;
        } else {
            $this->_log('Failed to change directory: ' . $remoteDirectory, Peach_Log::NOTICE);
        }

        return $chdirResult;
    }

    /**
     * Upload a directory recursive to ftp server
     *
     * @param string             $localDirectory    Local directory
     * @param string             $remoteDirectory   Remote directory
     * @param array|Peach_Config $contextualOptions Contextual options
     * @throws Peach_Ftp_Client_Exception 
     * @return boolean
     */
    public function uploadDirectory($localDirectory, $remoteDirectory, $contextualOptions = array())
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        // set contextual options
        $existingOptions = $this->_options;
        $this->setOptions($contextualOptions);
        
        $localDirectory = rtrim($localDirectory, '/') . '/';
        $remoteDirectory = rtrim($remoteDirectory, '/') . '/';

        $this->_log('Upload directory: ' . $localDirectory, Peach_Log::INFO);

        $handle = opendir($localDirectory);

        if (!$handle) {
            $this->_log('Failed to open local directory: ' . $localDirectory, Peach_Log::NOTICE);
            
            // reset options
            $this->_options = $existingOptions;
            return false;
        }

        // create remote directory if it doesn't exist
        $this->createRemoteDirectory($remoteDirectory);

        do {
            $fileName = readdir($handle);
            
            if ('.' == $fileName || '..' == $fileName) {
                continue;
            }

            if (!$fileName) {
                break;
            }

            $filePath = $localDirectory . $fileName;

            if (is_dir($filePath)) {
                $uploadResult = $this->uploadDirectory($filePath . '/', $remoteDirectory . $fileName . '/');

                if ($uploadResult && $this->_options[self::OPT_REMOVE_UPLOADED]) {
                    // upload was successful, remove the local directory
                    $rmdirResult = rmdir($filePath);

                    if ($rmdirResult) {
                        $this->_log('Removed local directory: ' . $filePath . ' after successful upload', Peach_Log::DEBUG);
                    } else {
                        $this->_log('Failed to remove local directory: ' . $filePath . ' after successful upload', Peach_Log::NOTICE);
                    }
                }
            } else {
                $this->uploadFile($filePath, $remoteDirectory . $fileName);
            }
        } while ($fileName);
            
        closedir($handle);

        if ($this->_options[self::OPT_REMOVE_UPLOADED]) {
            // upload was successful, remove the local directory
            $rmdirResult = rmdir($localDirectory);

            if ($rmdirResult) {
                $this->_log('Removed local directory: ' . $localDirectory . ' after successful upload', Peach_Log::DEBUG);
            } else {
                $this->_log('Failed to remove local directory: ' . $localDirectory . ' after successful upload', Peach_Log::NOTICE);
            }
        }

        // reset options
        $this->_options = $existingOptions;
        return true;
    }

    /**
     * Check if a file exists on the server in the current remote directory
     *
     * @param string $remoteFileName Remote file name
     * @throws Peach_Ftp_Client_Exception 
     * @return boolean
     */
    public function fileExists($remoteFileName)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        $this->_log('Check if file exists: ' . $remoteFileName, Peach_Log::DEBUG);

        $sizeResult = $this->getFileSize($remoteFileName);

        if (-1 === $sizeResult) {
            return false;
        }

        return true;
    }
    
    /**
     * Get file size
     * 
     * @param string $remoteFileName Remote file name
     * @return integer|false
     * @throws Peach_Ftp_Client_Exception 
     */
    public function getFileSize($remoteFileName)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        $sizeResult = @ftp_size($this->_connection, $remoteFileName);

        return $sizeResult;
        
    }

    /**
     * Get last modified timestamp
     * 
     * @param string $remoteFileName Remote file name
     * @return integer|false
     * @throws Peach_Ftp_Client_Exception 
     */
    public function getLastModified($remoteFileName)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        $sizeResult = @ftp_mdtm($this->_connection, $remoteFileName);

        return $sizeResult;
    }

    /**
     * Check if a file exists on the server in the current remote directory
     *
     * @param string $remoteDirectory Remote directory path
     * @return boolean
     */
    public function directoryExists($remoteDirectory)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        $this->_log('Check if directory exists: ' . $remoteDirectory, Peach_Log::DEBUG);

        if (!@ftp_chdir($this->_connection, $remoteDirectory)) {
            return false;
        }

        // change the directory back
        @ftp_chdir($this->_connection, $this->_remoteDirectory);

        return true;
    }
    
    /**
     * Delete a remote file - full file path can be given
     * 
     * @param string $remoteFile Remote file
     * @return boolean
     */
    public function deleteFile($remoteFile)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        $this->_log('Delete remote file: ' . $remoteFile, Peach_Log::DEBUG);

        $deleteResult = @ftp_delete($this->_connection, $remoteFile);

        if (!$deleteResult) {
             $this->_log('Failed to delete remote file: '. $remoteFile, Peach_Log::NOTICE);
        }

        return $deleteResult;
    }

    /**
     * Delete an empty remote directory - full file path can be given
     * 
     * @param string  $remoteDirectory Remote directory
     * @return boolean
     */
    public function deleteDirectory($remoteDirectory)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }
        
        if (empty($remoteDirectory) || ('/' == $remoteDirectory) || ('.' == $remoteDirectory)) {
            return false;
        }

        $this->_log('Delete remote directory: ' . $remoteDirectory, Peach_Log::DEBUG);

        $deleteResult = @ftp_rmdir($this->_connection, $remoteDirectory);

        if (!$deleteResult) {
             $this->_log('Failed to delete remote directory: '. $remoteDirectory, Peach_Log::NOTICE);
        }

        return $deleteResult;
    }
    
    /**
     * Delete a path on the server
     * 
     * @param string $remotePath Remote path
     * @return boolean
     * @throws Peach_Ftp_Client_Exception 
     */
    public function deletePath($remotePath)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        $this->_log('Delete remote path: ' . $remotePath, Peach_Log::DEBUG);

        if ($this->directoryExists($remotePath)) {
            $filesList = $this->getFilesList($remotePath);
            
            if (false === $filesList) {
                $this->_log('Failed to delete remote path: ' . $remotePath, Peach_Log::NOTICE);
                return false;
            }
            
            $deleteResult = true;
            
            foreach ($filesList as $file) {
                // delete all subfolders and files
                $deletePath = rtrim($remotePath, '/') . '/' . $file;
                
                $deleteResult &= $this->deletePath($deletePath);
            }
            
            if ($deleteResult) {
                // delete parent directory
                $deleteResult = $this->deleteDirectory($remotePath);
            }
            
            return $deleteResult;
        }
        
        if ($this->fileExists($remotePath)) {
            $deleteResult = $this->deleteFile($remotePath);
            return $deleteResult;
        }
        
        $this->_log('Remote path does not exist: ' . $remotePath, Peach_Log::NOTICE);

        return false;
    }
    
    /**
     * Rename/move a remote file or folder
     *
     * @param string $source      Source file or folder
     * @param string $destination Destination file or folder
     * @throws Peach_Ftp_Client_Exception 
     * @return boolean
     */
    public function rename($source, $destination)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        $this->_log('Rename remote file: ' . $source . ' to ' . $destination, Peach_Log::DEBUG);

        $renameResult = @ftp_rename($this->_connection, $source, $destination);

        if (!$renameResult) {
             $this->_log('Failed to rename remote file: ' . $source . ' to ' . $destination, Peach_Log::NOTICE);
        }

        return $renameResult;
    }

    /**
     * Create a remote directory if it doesn't exist
     *
     * @param string $remoteDirectory Remote directory
     * @throws Peach_Ftp_Client_Exception 
     * @return boolean
     */
    public function createRemoteDirectory($remoteDirectory)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        if (empty($remoteDirectory) || '.' == $remoteDirectory || '/' == $remoteDirectory) {
            // can't create root directory
            return true;
        }

        if ($this->directoryExists($remoteDirectory)) {
            // no need to create directory if it already exists
            return true;
        }
        
        $this->_log('Create remote directory: ' . $remoteDirectory, Peach_Log::DEBUG);

        $mkdirResult = @ftp_mkdir($this->_connection, $remoteDirectory);

        if (!$mkdirResult) {
            $this->_log('Failed to create remote directory: ' . $remoteDirectory, Peach_Log::NOTICE);
        }

        return $mkdirResult;
    }

    /**
     * Upload a list of files/directories
     *
     * @param array              $filesList         Files or directories list
     * @param string             $remoteDirectory   Remote directory where to upload
     * @param array|Peach_Config $contextualOptions Contextual options
     * @throws Peach_Ftp_Client_Exception 
     * @return boolean
     */
    public function uploadList(Array $filesList, $remoteDirectory, $contextualOptions = array())
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        // set contextual options
        $existingOptions = $this->_options;
        $this->setOptions($contextualOptions);
        
        $uploadResult = true;
        
        foreach ($filesList as $localFile) {
            $fileName = basename($localFile);

            if (is_dir($localFile)) {
                $uploadResult &= $this->uploadDirectory($localFile, $remoteDirectory . $fileName . '/');
            } else {
                $uploadResult &= $this->uploadFile($localFile, $remoteDirectory . $fileName);
            }
        }
        
        // reset options
        $this->_options = $existingOptions;
        return $uploadResult;
    }
    
    /**
     * Get files list
     * 
     * @param string $remoteDirectory Remote directory
     * @return array
     * @throws Peach_Ftp_Client_Exception 
     */
    public function getFilesList($remoteDirectory)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        $files = @ftp_nlist($this->_connection, $remoteDirectory);
        
        if (false === $files) {
            $this->_log('Failed to get files list: ' . $remoteDirectory, Peach_Log::NOTICE);
        }
        
        return $files;
    }

    /**
     * Get raw files list
     * 
     * @param string $remoteDirectory Remote directory
     * @return array
     * @throws Peach_Ftp_Client_Exception 
     */
    public function getRawFilesList($remoteDirectory)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        $rawList = @ftp_rawlist($this->_connection, $remoteDirectory);
        
        if (false === $rawList) {
            $this->_log('Failed to get files list: ' . $remoteDirectory, Peach_Log::NOTICE);
        }

        // format raw files list
        $files = $this->_formatRawFilesList($rawList);
        
        return $files;
    }
    
    /**
     * Get system type
     * 
     * @return string
     * @throws Peach_Ftp_Client_Exception 
     */
    public function getSysType()
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }

        $sysType = @ftp_systype($this->_connection);
        
        if (false === $sysType) {
            $this->_log('Failed to retrieve system type', Peach_Log::NOTICE);
        }
        
        return $sysType;
    }
    
    /**
     * Change permissions
     * 
     * @param string  $path Remote path
     * @param integer $mode Mode to set
     * @return boolean
     * @throws Peach_Ftp_Client_Exception 
     */
    public function chmod($path, $mode)
    {
        if (!$this->isConnected()) {
            throw new Peach_Ftp_Client_Exception('Not connected to FTP server.');
        }
        
        $this->_log('Chmod ' . sprintf('%o', $mode) . ' path: ' . $path, Peach_Log::DEBUG);

        $result = @ftp_chmod($this->_connection, $mode, $path);
        
        if (false === $result) {
            $this->_log('Failed to chmod path: ' . $path, Peach_Log::NOTICE);
            return false;
        }
        
        return true;
    }

    /**
     * Execute arbitrary command
     * 
     * @param string $command Command to execute
     * @return mixed
     */
    public function exec($command)
    {
        $result = @ftp_exec($this->_connection, $command);
        
        return $result;
    }
    
    /**
     * Execute raw command
     * 
     * @param string $command Command to execute
     * @return mixed
     */
    public function raw($command)
    {
        $result = @ftp_raw($this->_connection, $command);
        
        return $result;
    }
    
    /**
     * Connect to FTP server
     * 
     * @throws Peach_Ftp_Client_Exception 
     */
    protected function _connect()
    {
        $host = $this->_options[self::OPT_HOST];
        $port = $this->_options[self::OPT_PORT];
        $username = $this->_options[self::OPT_USERNAME];
        $password = $this->_options[self::OPT_PASSWORD];
        
        // connect to ftp server
        if ($this->_options[self::OPT_SSL]) {
            $this->_connection = @ftp_ssl_connect($host, $port, $this->_options[self::OPT_TIMEOUT]);
        } else {
            $this->_connection = @ftp_connect($host, $port, $this->_options[self::OPT_TIMEOUT]);
        }

        if (!$this->_connection) {
            throw new Peach_Ftp_Client_Exception('Could not connect to FTP server ' . $host . ':' . $port);
        }

        // log in with username and password
        $loginResult = @ftp_login($this->_connection, $username, $password);

        if (!$loginResult) {
            throw new Peach_Ftp_Client_Exception('Could not login to FTP server ' . $host . ':' . $port . ' using ' . $username . '/***');
        }
    }
    
    /**
     * Upload a local file to the server
     *
     * @param string  $localFile     Local file
     * @param string  $remoteFile    Remote file
     * @param integer $startPosition Start position
     * @return boolean
     */
    protected function _uploadFile($localFile, $remoteFile, $startPosition = 0)
    {
        $this->_log('Starting file upload: ' . $remoteFile, Peach_Log::DEBUG);

        // start the upload
        $putResult = @ftp_put($this->_connection, $remoteFile, $localFile, $this->_options[self::OPT_MODE], $startPosition);

        return $putResult;
    }

    /**
     * Download a remote file
     *
     * @param string  $localFile     Local file
     * @param string  $remoteFile    Remote file
     * @param integer $startPosition Start position
     * @return boolean
     */
    protected function _downloadFile($localFile, $remoteFile, $startPosition = 0)
    {
        $this->_log('Starting file download: ' . $remoteFile, Peach_Log::DEBUG);

        // start the upload
        $getResult = @ftp_get($this->_connection, $localFile, $remoteFile, $this->_options[self::OPT_MODE], $startPosition);

        return $getResult;
    }

    /**
     * Return the dirname from a path
     *
     * @param string $path Path string
     * @return string
     */
    protected function _dirname($path)
    {
        $dirname = dirname($path);

        $dirname = '/' . ltrim($dirname, './');

        return $dirname;
    }
    
    /**
     * Format raw files list
     * 
     * @param array $rawList Raw files list
     * @return array 
     */
    protected function _formatRawFilesList($rawList)
    {
        $items = array();

        foreach ($rawList as $item) {
            $struct = array();
            $current = preg_split('/[\s]+/', $item, 9);
            
            $struct['raw']    = $item;
            $struct['perms']  = $current[0];
            $struct['permsn'] = $this->_chmodNumeric($current[0]);
            $struct['number'] = $current[1];
            $struct['owner']  = $current[2];
            $struct['group']  = $current[3];
            $struct['size']   = $current[4];
            $struct['month']  = $current[5];
            $struct['day']    = $current[6];
            $struct['year']   = $current[7];
            $struct['name']   = str_replace('//', '', $current[8]);
            
            $items[] = $struct;
         }
        
        return $items;
    }
    
    /**
     * Return chmod string in numeric format
     * 
     * @param string $mode Chmod string
     * @return string
     */
    protected function _chmodNumeric($mode)
    {
       $realMode = '';
       $legal = array('', 'w', 'r', 'x', '-');
       $attArray = preg_split('//', $mode);
       
       for ($i = 0; $i < count($attArray); $i++) {
           $key = array_search($attArray[$i], $legal);
           
           if (!$key) {
               continue;
           }
           
           $realMode .= $legal[$key];
       }
       
       $mode = str_pad($realMode, 9, '-');
       $translation = array(
           '-' => '0',
           'r' => '4',
           'w' => '2',
           'x' => '1'
       );
       $mode = strtr($mode, $translation);
       
       $newMode = '';
       $newMode .= $mode[0] + $mode[1] + $mode[2];
       $newMode .= $mode[3] + $mode[4] + $mode[5];
       $newMode .= $mode[6] + $mode[7] + $mode[8];
       
       return $newMode;
    }

    /**
     * Log message
     * 
     * @param string  $message  Message to log
     * @param integer $priority Log priority
     * @return void
     */
    protected function _log($message, $priority)
    {
        if (is_null($this->_options[self::OPT_LOG])) {
            return null;
        }
        
        // make sure the option is a valid log object
        if ($this->_options[self::OPT_LOG] instanceof Peach_Log) {
            throw new Peach_Ftp_Client_Exception('Log object must be an instance of Peach_Log.');
        }
        
        $this->_options[self::OPT_LOG]->log($message, $priority);
    }
}

/*EOF*/
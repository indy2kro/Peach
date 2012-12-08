<?php
/**
 * Peach Library
 *
 * @category   Peach
 * @package    Peach_Service_Buxfer
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 */

/**
 * Buxfer service
 */
class Peach_Service_Buxfer
{
    /*
     * API version
     */
    const VERSION = '1.0';
    
    /*
     * Available options
     */
    const OPT_USER_AGENT = 'user_agent';
    const OPT_TIMEOUT = 'timeout';
    const OPT_LOGIN_USERNAME = 'login_username';
    const OPT_LOGIN_PASSWORD = 'login_password';
    const OPT_LOG = 'log';
    const OPT_TOKEN = 'token';
    const OPT_HTTP_CLIENT = 'http_client';
    
    /*
     * Buxfer API url
     */
    const API_URL = 'https://www.buxfer.com/api/';
   
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_USER_AGENT => 'BuxferApi/1.0',
        self::OPT_TIMEOUT => 30,
        self::OPT_LOGIN_USERNAME => null,
        self::OPT_LOGIN_PASSWORD => null,
        self::OPT_LOG => null,
        self::OPT_TOKEN => null,
        self::OPT_HTTP_CLIENT => null
    );
    
    /**
     * Service adapter
     * 
     * @var Peach_Service_Buxfer_Adapter_Abstract
     */
    protected $_adapter;
    
    /**
     * Last transactions count
     * 
     * @var integer
     */
    protected $_lastTransactionsCount;
    
    /**
     * Last report image url
     * 
     * @var string
     */
    protected $_lastReportImageUrl;
    
    /**
     * Last report buxfer image url
     * 
     * @var string
     */
    protected $_lastReportBuxferImageUrl;
    
    /**
     * Last report breadcrumb
     * 
     * @var string
     */
    protected $_lastReportBreadcrumb;
    
    /**
     * Last upload balance
     * 
     * @var integer
     */
    protected $_lastUploadBalance;
    
    /**
     * Constructor
     *  
     * @param array|Peach_Config $options
     * @return void
     */
    public function __construct($options = array())
    {
        // set options
        $this->setOptions($options);
        
        // initialize http client
        $this->_initHttpClient();
    }
    
    /**
     * Merge options with incoming values
     * 
     * @param array|Peach_Config $options
     * @return void
     */
    public function setOptions($options)
    {
        if ($options instanceof Peach_Config) {
            $options = $options->toArray();
        }
        
        $this->_options = array_merge($this->_options, $options);
        
        if (!is_null($this->_adapter)) {
            // set options for the adapter as well
            $this->_adapter->setOptions($options);
        }
    }
    
    /**
     * Get token
     * 
     * @return string
     */
    public function getToken()
    {
        $token = $this->_options[self::OPT_TOKEN];
        
        return $token;
    }
    
    /**
     * Get API version
     * 
     * @return float
     */
    public function getVersion()
    {
        return self::VERSION;
    }
    
    /**
     * List transactions
     * 
     * @param array $options Options
     * @return array
     */
    public function getTransactions(Array $options = array())
    {
        // build command
        $cmd = new Peach_Service_Buxfer_Command_Transactions();
        
        // run command
        $result = $this->_runCmd($cmd, $options);
        
        // store last count
        $this->_lastTransactionsCount = $cmd->getLastCount();

        return $result;
    }
    
    /**
     * Get last transactions count
     * 
     * @param array $options Options
     * @return integer
     */
    public function getLastTransactionsCount()
    {
        return $this->_lastTransactionsCount;
    }
    
    /**
     * Count transactions
     * 
     * @param array $options Options
     * @return array
     */
    public function countTransactions(Array $options = array())
    {   
        // get transactions
        $this->getTransactions($options);
        
        // get last count
        $count = $this->getLastTransactionsCount();

        return $count;
    }
    
    /**
     * Get report
     * 
     * @param array $options Options
     * @return array
     */
    public function getReport(Array $options = array())
    {
        // build command
        $cmd = new Peach_Service_Buxfer_Command_Reports();
        
        // run command
        $response = $this->_runCmd($cmd, $options);
        
        // store last values
        $this->_lastReportImageUrl = $cmd->getLastImageUrl();
        $this->_lastReportBuxferImageUrl = $cmd->getLastBuxferImageUrl();
        $this->_lastReportBreadcrumb = $cmd->getLastBreadcrumb();

        return $response;
    }
    
    /**
     * Get last report image url
     * 
     * @return string
     */
    public function getLastReportImageUrl()
    {
        return $this->_lastReportImageUrl;
    }
    
    /**
     * Get last report buxfer image url
     * 
     * @return string
     */
    public function getLastReportBuxferImageUrl()
    {
        return $this->_lastReportBuxferImageUrl;
    }
    
    /**
     * Get last report breadcrumb
     * 
     * @return string
     */
    public function getLastReportBreadcrumb()
    {
        return $this->_lastReportBreadcrumb;
    }
    
    /**
     * Get tags
     * 
     * @return array
     */
    public function getTags()
    {
        // build command
        $cmd = new Peach_Service_Buxfer_Command_Tags();
        
        // run command
        $response = $this->_runCmd($cmd);
        
        return $response;
    }
    
    /**
     * Get accounts
     * 
     * @return array
     */
    public function getAccounts()
    {
        // build command
        $cmd = new Peach_Service_Buxfer_Command_Accounts();
        
        // run command
        $response = $this->_runCmd($cmd);
        
        return $response;
    }
    
    /**
     * Get contacts
     * 
     * @return array
     */
    public function getContacts()
    {
        // build command
        $cmd = new Peach_Service_Buxfer_Command_Contacts();
        
        // run command
        $response = $this->_runCmd($cmd);
        
        return $response;
    }
    
    /**
     * Get groups
     * 
     * @return array
     */
    public function getGroups()
    {
        // build command
        $cmd = new Peach_Service_Buxfer_Command_Groups();
        
        // run command
        $response = $this->_runCmd($cmd);
        
        return $response;
    }
    
    /**
     * Get reminders
     * 
     * @return array
     */
    public function getReminders()
    {
        // build command
        $cmd = new Peach_Service_Buxfer_Command_Reminders();
        
        // run command
        $response = $this->_runCmd($cmd);
        
        return $response;
    }
    
    /**
     * Get loans
     * 
     * @return array
     */
    public function getLoans()
    {
        // build command
        $cmd = new Peach_Service_Buxfer_Command_Loans();
        
        // run command
        $response = $this->_runCmd($cmd);
        
        return $response;
    }
    
    /**
     * Get budgets
     * 
     * @return array
     */
    public function getBudgets()
    {
        // build command
        $cmd = new Peach_Service_Buxfer_Command_Budgets();
        
        // run command
        $response = $this->_runCmd($cmd);
        
        return $response;
    }
    
    /**
     * Upload statement
     * 
     * @param integer $accountId  Account id
     * @param string  $statement  Statement text
     * @param string  $dateFormat Date format
     * @return boolean
     */
    public function uploadStatement($accountId, $statement, $dateFormat = Peach_Service_Buxfer_Command_UploadStatement::DATE_FORMAT_MM_DD_YYYY)
    {
        // build command
        $cmd = new Peach_Service_Buxfer_Command_UploadStatement();
        
        $options = array(
            Peach_Service_Buxfer_Command_UploadStatement::PARAM_ACCOUNT_ID => $accountId,
            Peach_Service_Buxfer_Command_UploadStatement::PARAM_STATEMENT => $statement,
            Peach_Service_Buxfer_Command_UploadStatement::PARAM_DATE_FORMAT => $dateFormat
        );
        
        // run command
        $response = $this->_runCmd($cmd, $options);
        
        // store last upload balance
        $this->_lastUploadBalance = $cmd->getLastBalance();
        
        return $response;
    }
    
    /**
     * Get last upload balance
     * 
     * @return integer
     */
    public function getLastUploadBalance()
    {
        return $this->_lastUploadBalance;
    }
    
    /**
     * Add transaction
     * 
     * @param float  $value        Transaction value, positive for income, negative for expense
     * @param string $description  Transaction description
     * @param array  $accountNames Accounts list, usually one for income/expense, two for transfer
     * @param array  $tags         List of tags
     * @param string $date         Transaction date
     * @param string $status       Transaction status (Cleared, Pending, Reconciled)
     * @param array  $participants Participants to the transaction
     * @return boolean
     */
    public function addTransaction($value, $description, Array $accountNames = array(), Array $tags = array(), $date = null, $status = null, Array $participants = array())
    {
        $text = '';
        
        // build command
        $text .= $description . ' ' . $value;
        
        if (!empty($tags)) {
            $text .= ' tags:' . implode(',', $tags);
        }
        
        if (!empty($accountNames)) {
            $text .= ' acct:' . implode(',', $accountNames);
        }
        
        if (!empty($date)) {
            $text .= ' date:' . $date;
        }
        
        if (!empty($status)) {
            $text .= ' status:' . $status;
        }
        
        if (!empty($participants)) {
            $text .= ' with:' . implode(' ', $participants);
        }
        
        $options = array(
            Peach_Service_Buxfer_Command_AddTransaction::PARAM_FORMAT => Peach_Service_Buxfer_Command_AddTransaction::FORMAT_SMS,
            Peach_Service_Buxfer_Command_AddTransaction::PARAM_TEXT => $text
        );
        
        // build command
        $cmd = new Peach_Service_Buxfer_Command_AddTransaction();
        
        // run command
        $response = $this->_runCmd($cmd, $options);
        
        return $response;
    }
    
    /**
     * Add expense
     * 
     * @param float  $value        Transaction value
     * @param string $description  Transaction description
     * @param string $accountName  Account name
     * @param array  $tags         Tags
     * @param string $date         Transaction date
     * @param string $status       Transaction status
     * @param array  $participants Participants to the transaction
     * @return boolean
     */
    public function addExpense($value, $description, $accountName = null, Array $tags = array(), $date = null, $status = null, Array $participants = array())
    {
        if ($value > 0) {
            $value = (-1) * $value;
        }
        
        $response = $this->addTransaction($value, $description, array($accountName), $tags, $date, $status, $participants);
        
        return $response;
    }
    
    /**
     * Add income
     * 
     * @param float  $value        Transaction value
     * @param string $description  Transaction description
     * @param string $accountName  Account name
     * @param array  $tags         Tags
     * @param string $date         Transaction date
     * @param string $status       Transaction status
     * @param array  $participants Participants to the transaction
     * @return boolean
     */
    public function addIncome($value, $description, $accountName = null, Array $tags = array(), $date = null, $status = null, Array $participants = array())
    {
        if ($value < 0) {
            $value = (-1) * $value;
        }
        
        $response = $this->addTransaction($value, $description, array($accountName), $tags, $date, $status, $participants);
        
        return $response;
    }
    
    /**
     * Add income
     * 
     * @param float  $value           Transaction value
     * @param string $description     Transaction description
     * @param string $fromAccountName From account name
     * @param string $toAccountName   To account name
     * @param string $date            Transaction date
     * @param string $status          Transaction status
     * @param array  $participants    Participants to the transaction
     * @return boolean
     */
    public function addTransfer($value, $description, $fromAccountName, $toAccountName = null, Array $tags = array(), $date = null, $status = null, Array $participants = array())
    {
        if ($value < 0) {
            $value = (-1) * $value;
        }
        
        $response = $this->addTransaction($value, $description, array($fromAccountName, $toAccountName), $tags, $date, $status, $participants);
        
        return $response;
    }
    
    /**
     * Login
     * 
     * @param array|null $options Options for login, if not provided, the global options are used
     * @return boolean
     */
    public function login($options = null)
    {
        // login options can be provided in global options as well
        if (is_null($options)) {
            $options = array(
                Peach_Service_Buxfer_Command_Login::PARAM_USER_ID => $this->_options[self::OPT_LOGIN_USERNAME],
                Peach_Service_Buxfer_Command_Login::PARAM_PASSWORD => $this->_options[self::OPT_LOGIN_PASSWORD]
            );
        }
        
        // build command
        $cmd = new Peach_Service_Buxfer_Command_Login();
        
        // run command
        $response = $this->_runCmd($cmd, $options);
        
        // store token for future requests
        $this->_options[self::OPT_TOKEN] = $cmd->getLastToken();
        
        return $response;
    }
    
    /**
     * Get http client
     * 
     * @return Peach_Http_Client
     */
    public function getHttpClient()
    {
        return $this->_options[self::OPT_HTTP_CLIENT];
    }
    
    /**
     * Run command
     * 
     * @param Peach_Service_Buxfer_Command_Abstract $cmd    Command object
     * @param array                           $params Parameters for command
     * @throws Peach_Service_Buxfer_Exception
     * @return array
     */
    protected function _runCmd(Peach_Service_Buxfer_Command_Abstract $cmd, Array $params = array())
    {
        $this->_log('Run API command ' . $cmd->getCmd());
        
        if ($cmd->getCmd() != Peach_Service_Buxfer_Command_Abstract::CMD_LOGIN) {
            // set token parameter
            $params[Peach_Service_Buxfer_Command_Abstract::PARAM_TOKEN] = $this->_options[self::OPT_TOKEN];
        }

        // set final url
        $url = $this->_buildUrl($cmd);
        
        $httpClient = $this->_options[self::OPT_HTTP_CLIENT];
        $httpClient->setUri($url);
        
        // get HTTP method
        $httpMethod = $cmd->getHttpMethod();
        
        switch ($httpMethod) {
            case Peach_Http_Request::METHOD_GET:
                foreach ($params as $paramName => $paramValue) {
                    $httpClient->setQueryParameter($paramName, $paramValue);
                }
                break;
                
            case Peach_Http_Request::METHOD_POST:
                foreach ($params as $paramName => $paramValue) {
                    $httpClient->setPostParameter($paramName, $paramValue);
                }
                break;
            
            default:
                throw new Peach_Service_Buxfer_Exception('Invalid HTTP method provided: ' . $httpMethod);
                break;
        }
        
        // set http method
        $httpClient->setMethod($httpMethod);
        
        // perform request
        $httpResponse = $httpClient->request();
        
        // log request
        $this->_log('HTTP request:', Peach_Log::DEBUG);
        $this->_log($httpClient->getRequest(), Peach_Log::DEBUG);
        
        // log response
        $this->_log('HTTP response:', Peach_Log::DEBUG);
        $this->_log($httpClient->getResponse(), Peach_Log::DEBUG);
        
        // initialize response
        $response = new Peach_Service_Buxfer_Response($httpResponse);
        $responseArray = $response->toArray();
        
        // format result
        $result = $cmd->format($responseArray);
        
        return $result;
    }
    
    /**
     * Initialize http client
     * 
     * @return void
     */
    protected function _initHttpClient()
    {
        // initialize the http client only if it was not injected as a parameter
        if (!is_null($this->_options[self::OPT_HTTP_CLIENT])) {
            return null;
        }
        
        $httpClientConfig = array(
            Peach_Http_Client::OPT_USER_AGENT => $this->_options[self::OPT_USER_AGENT],
            Peach_Http_Client::OPT_TIMEOUT => $this->_options[self::OPT_TIMEOUT]
        );
        
        $this->_options[self::OPT_HTTP_CLIENT] = new Peach_Http_Client(null, $httpClientConfig);
    }
    
    /**
     * Build url
     * 
     * @param Peach_Service_Buxfer_Command_Abstract $cmd Command object
     * @return string
     */
    protected function _buildUrl(Peach_Service_Buxfer_Command_Abstract $cmd)
    {
        // set final url
        $url = self::API_URL . $cmd->getCmd() . '.xml';
        
        return $url;
    }
    
    /**
     * Log message
     * 
     * @param string  $message  Message text
     * @param integer $priority Priority
     * @return void
     */
    protected function _log($message, $priority = Peach_Log::INFO)
    {
        if (is_null($this->_options[self::OPT_LOG])) {
            return null;
        }
        
        $this->_options[self::OPT_LOG]->log($message, $priority);
    }
}

/* EOF */
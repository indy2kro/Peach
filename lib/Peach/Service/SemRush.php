<?php
/**
 * Peach Framework
 *
 * @category   Peach
 * @package    Peach_Service_SemRush
 * @author     Cristi RADU <indy2kro@yahoo.com>
 * @copyright  Copyright (c) 2012 Peach Library
 * @see        http://www.semrush.com/api.html
 */

/**
 * SemRush API service
 */
class Peach_Service_SemRush
{
    /*
     * API version implementation
     */
    const VERSION = '3.0';
    
    /*
     * Available APIs
     */
    const API_BASE = 'Base';
    const API_FULL_SEARCH = 'FullSearch';
    const API_SERP_SOURCE = 'SerpSource';
    const API_UPS_AND_DOWNS = 'UpsDowns';
    const API_DOMAINS_VS_DOMAINS = 'DomainsVsDomains';
    
    /*
     * Databases available for lookup
     */
    const DB_GOOGLE_COM = 'us';
    const DB_GOOGLE_CO_UK = 'uk';
    const DB_GOOGLE_CA = 'ca';
    const DB_GOOGLE_RU = 'ru';
    const DB_GOOGLE_DE = 'de';
    const DB_GOOGLE_FR = 'fr';
    const DB_GOOGLE_ES = 'es';
    const DB_GOOGLE_IT = 'it';
    const DB_GOOGLE_COM_BR = 'br';
    const DB_GOOGLE_COM_AU = 'au';
    const DB_BING_COM = 'us.bing';
    
    /*
     * Available fields
     */
    const FIELD_ACTION = 'action';
    const FIELD_TYPE = 'type';
    const FIELD_KEY = 'key';
    const FIELD_DISPLAY_LIMIT = 'display_limit';
    const FIELD_DISPLAY_OFFSET = 'display_offset';
    const FIELD_EXPORT = 'export';
    const FIELD_EXPORT_COLUMNS = 'export_columns';
    const FIELD_EXPORT_ESCAPE = 'export_escape';
    const FIELD_DISPLAY_SORT = 'display_sort';
    const FIELD_DISPLAY_FILTERS = 'display_filters';
    const FIELD_PHRASE = 'phrase';
    const FIELD_DOMAINS = 'domains';
    
    /*
     * Available actions
     */
    const ACTION_REPORT = 'report';
    
    /*
     * Available export types
     */
    const EXPORT_API = 'api';
    
    /*
     * Report types
     */
    const REPORT_TYPE_DOMAIN_RANK = 'domain_rank';
    const REPORT_TYPE_DOMAIN_ORGANIC = 'domain_organic';
    const REPORT_TYPE_DOMAIN_ADWORDS = 'domain_adwords';
    const REPORT_TYPE_DOMAIN_ORGANIC_ORGANIC = 'domain_organic_organic';
    const REPORT_TYPE_DOMAIN_ADWORDS_ADWORDS = 'domain_adwords_adwords';
    const REPORT_TYPE_DOMAIN_ORGANIC_ADWORDS = 'domain_organic_adwords';
    const REPORT_TYPE_DOMAIN_ADWORDS_ORGANIC = 'domain_adwords_organic';
    const REPORT_TYPE_DOMAIN_PIVOT = 'domain_pivot';
    const REPORT_TYPE_PHRASE_THIS = 'phrase_this';
    const REPORT_TYPE_PHRASE_RELATED = 'phrase_related';
    const REPORT_TYPE_URL_ORGANIC = 'url_organic';
    const REPORT_TYPE_DOMAIN_RANK_HISTORY = 'domain_rank_history';
    
    /*
     * Request types
     */
    const REQUEST_TYPE_DOMAIN = 'domain';
    const REQUEST_TYPE_PHRASE = 'phrase';
    const REQUEST_TYPE_URL = 'url';
    
    /*
     * Domain vs domains API
     */
    const TYPE_DOMAIN_DOMAINS = 'domain_domains';
    
    /*
     * Full search API
     */
    const TYPE_PHRASE_FULLSEARCH = 'phrase_fullsearch';
    
    /*
     * SERP source API
     */
    const TYPE_PHRASE_SOURCE = 'phrase_source';
    
    /*
     * Ups & Downs API
     */
    const TYPE_RANK_DIFFERENCE = 'rank_difference';
    
    /*
     * Available options
     */
    const OPT_API_KEY = 'api_key';
    const OPT_TIMEOUT = 'timeout';
    
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array(
        self::OPT_API_KEY => null,
        self::OPT_TIMEOUT => 10
    );
    
    /**
     * Http client
     * 
     * @var Peach_Http_Client
     */
    protected $_httpClient;
    
    /**
     * Constructor
     *  
     * @param array|Peach_Config $options
     * @param Peach_Http_Client  $httpClient
     * @return void
     */
    public function __construct($options = array(), Peach_Http_Client $httpClient = null)
    {
        // set options
        $this->setOptions($options);
        
        if (!is_null($httpClient)) {
            // use provided http client
            $this->_httpClient = $httpClient;
        } else {
            // create new http client
            $this->_httpClient = new Peach_Http_Client();
            
        }
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
    }
    
    /**
     * Get API version
     * 
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }
    
    /**
     * Get HTTP client
     * 
     * @return Peach_Http_Client
     */
    public function getHttpClient()
    {
        return $this->_httpClient;
    }
    
    /**
     * Perform a generic request
     * 
     * @param string $url
     * @param array  $params
     * @return array
     */
    public function request($url, Array $params = array())
    {
        // set http options
        $httpOptions = array(
            Peach_Http_Client::OPT_TIMEOUT => $this->_options[self::OPT_TIMEOUT]
        );
        $this->_httpClient->setOptions($httpOptions);
        
        // set url
        $this->_httpClient->setUri($url);
        
        //set parameters
        $this->_httpClient->setQueryParameters($params);
        
        // perform request
        $response = $this->_httpClient->request();
        
        // format the response
        return $this->_formatResponse($response->getBody());
    }
    
    /**
     * Base API request
     * 
     * @param string  $request
     * @param string  $db
     * @param string  $reportType
     * @param string  $requestType
     * @param integer $limit
     * @param integer $offset
     * @param array   $exportColumns
     * @param string  $displaySort
     * @param string  $displayFilters
     * @return array
     */
    public function baseRequest($request, $db = self::DB_GOOGLE_COM, $reportType = self::REPORT_TYPE_DOMAIN_RANK,
            $requestType = self::REQUEST_TYPE_DOMAIN, $limit = 10, $offset = 0, $exportColumns = array(),
            $displaySort = null, $displayFilters = '')
    {
        $url = 'http://' . $db . '.api.semrush.com/';
        
        $params = array(
            self::FIELD_KEY => $this->_options[self::OPT_API_KEY],
            self::FIELD_EXPORT => self::EXPORT_API,
            self::FIELD_ACTION => self::ACTION_REPORT,
            self::FIELD_TYPE => $reportType,
            $requestType => $request,
            self::FIELD_DISPLAY_LIMIT => $limit,
            self::FIELD_DISPLAY_OFFSET => $offset
        );
        
        if (!empty($exportColumns)) {
            $params[self::FIELD_EXPORT_COLUMNS] = $this->_formatExportColumns($exportColumns);
        }
        
        if (!is_null($displaySort)) {
            $params[self::FIELD_DISPLAY_SORT] = $displaySort;
        }
        
        if (!empty($displayFilters)) {
            $params[self::FIELD_DISPLAY_FILTERS] = $displayFilters;
        }
        
        return $this->request($url, $params);
    }
    
    /**
     * Full search request
     * 
     * @param string  $request
     * @param string  $db
     * @param integer $limit
     * @param integer $offset
     * @param array   $exportColumns
     * @return array
     */
    public function fullSearchRequest($request, $db = self::DB_GOOGLE_COM, $limit = 10, $offset = 0, $exportColumns = array())
    {
        $url = 'http://' . $db . '.fullsearch-api.semrush.com/';
        
        $params = array(
            self::FIELD_KEY => $this->_options[self::OPT_API_KEY],
            self::FIELD_EXPORT => self::EXPORT_API,
            self::FIELD_ACTION => self::ACTION_REPORT,
            self::FIELD_TYPE => self::TYPE_PHRASE_FULLSEARCH,
            self::FIELD_PHRASE => $request,
            self::FIELD_DISPLAY_LIMIT => $limit,
            self::FIELD_DISPLAY_OFFSET => $offset
        );
        
        if (!empty($exportColumns)) {
            $params[self::FIELD_EXPORT_COLUMNS] = $this->_formatExportColumns($exportColumns);
        }
        
        return $this->request($url, $params);
    }
    
    /**
     * SERP source request
     * 
     * @param string $request
     * @param string $db
     * @return array
     */
    public function serpSourceRequest($request, $db = self::DB_GOOGLE_COM)
    {
        $url = 'http://sources.' . $db . '.api.semrush.com/';
        
        $params = array(
            self::FIELD_KEY => $this->_options[self::OPT_API_KEY],
            self::FIELD_ACTION => self::ACTION_REPORT,
            self::FIELD_TYPE => self::TYPE_PHRASE_SOURCE,
            self::FIELD_PHRASE => $request
        );
        
        return $this->request($url, $params);
    }
    
    /**
     * Ups & downs request
     * 
     * @param string  $db
     * @param integer $limit
     * @param integer $offset
     * @param array   $exportColumns
     * @param string  $displaySort
     * @return array
     */
    public function upsDownsRequest($db = self::DB_GOOGLE_COM, $limit = 10, $offset = 0, $exportColumns = array(), $displaySort = null)
    {
        $url = 'http://' . $db . '.api.semrush.com/';
        
        $params = array(
            self::FIELD_KEY => $this->_options[self::OPT_API_KEY],
            self::FIELD_ACTION => self::ACTION_REPORT,
            self::FIELD_TYPE => self::TYPE_RANK_DIFFERENCE,
            self::FIELD_DISPLAY_LIMIT => $limit,
            self::FIELD_DISPLAY_OFFSET => $offset
        );
        
        if (!empty($exportColumns)) {
            $params[self::FIELD_EXPORT_COLUMNS] = $this->_formatExportColumns($exportColumns);
        }
        
        if (!is_null($displaySort)) {
            $params[self::FIELD_DISPLAY_SORT] = $displaySort;
        }
        
        return $this->request($url, $params);
    }
    
    /**
     * Domain vs domains  request
     * 
     * @param string  $domains
     * @param string  $db
     * @param integer $limit
     * @param integer $offset
     * @return array
     */
    public function domainsVsDomainsRequest($domains, $db = self::DB_GOOGLE_COM, $limit = 10, $offset = 0)
    {
        $url = 'http://' . $db . '.api.semrush.com/';
        
        $params = array(
            self::FIELD_KEY => $this->_options[self::OPT_API_KEY],
            self::FIELD_EXPORT => self::EXPORT_API,
            self::FIELD_ACTION => self::ACTION_REPORT,
            self::FIELD_TYPE => self::TYPE_DOMAIN_DOMAINS,
            self::FIELD_DISPLAY_LIMIT => $limit,
            self::FIELD_DISPLAY_OFFSET => $offset,
            self::FIELD_DOMAINS => $domains
        );
        
        return $this->request($url, $params);
    }
    
    /**
     * Format export columns
     * 
     * @param array $columns
     * @return string
     */
    protected function _formatExportColumns(Array $columns)
    {
        $result = implode(',', $columns);
        
        return $result;
    }
        
    /**
     * Format CSV response as an array
     *
     * @param string $result
     * @return array
     * @throws Peach_Service_SemRush_Exception
     */
    protected function _formatResponse($result)
    {
        // query returned an error
        if (preg_match('/^ERROR\s([0-9]+)\s::/i', $result, $regs)) {
            $errorCode = $regs[1];
            $errorString = Peach_Service_SemRush_ErrorTranslator::translate($errorCode);
            
            if (is_null($errorString)) {
                $errorString = 'UNKNOWN ERROR';
            }
            
            throw new Peach_Service_SemRush_Exception('SemRush API Exception: Error #' . $errorCode . ' : ' . $errorString);
        }

        $formattedResponse = array();

        // get the result headers and data
        $lines = explode("\n", $result);
        $headers = explode(';', array_shift($lines));
        
        // trim all values
        $headers = array_map('trim', $headers);

        foreach ($lines as $line) {
            // empty line
            if (empty($line)) {
                continue;
            }

            // items are separated by ;
            $items = explode(';', $line);

            $formattedItem = array();
            foreach ($items as $index => $item) {
                $formattedItem[ $headers[$index] ] = $item;
            }

            $formattedResponse[] = $formattedItem;
        }
        
        return $formattedResponse;
    }
}

/* EOF */
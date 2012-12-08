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
 * Buxfer service response xml adapter
 */
class Peach_Service_Buxfer_Response_Adapter_Xml extends Peach_Service_Buxfer_Response_Adapter_Abstract
{
    /**
     * Convert XML to array
     * 
     * @throws Peach_Service_Buxfer_Response_Exception
     * @return void
     */
    protected function _toArray()
    {
        $array = array();
        
        // get input XML from the body of the request
        $inputXml = $this->_httpResponse->getBody();
        
        // build DOM object
        $dom = new DOMDocument('1.0', 'UTF-8');
        
        // load the input XML
        Peach_Error_Handler::start();
        $parsed = $dom->loadXML($inputXml);
        $error = Peach_Error_Handler::stop();
        
        if (!is_null($error)) {
            throw new Peach_Service_Buxfer_Response_Exception('Error parsing the XML string: ' . $error);
        }

        if (!$parsed) {
            throw new Peach_Service_Buxfer_Response_Exception('Error parsing the XML string.');
        }
        
		$array[$dom->documentElement->tagName] = $this->_convert($dom->documentElement);

        $this->_structure = $array;
    }
    
    /**
     * Convert a DOM document to array
     * 
     * @param DOMElement|DOMText $node Start node
     * @return array
     */
    protected function _convert($node)
    {
        $output = array();

		switch ($node->nodeType) {
			case XML_CDATA_SECTION_NODE: // intentionally omitted break
			case XML_TEXT_NODE:
				$output = trim($node->textContent);
				break;

			case XML_ELEMENT_NODE:
				// for each child node, call the covert function recursively
				for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
					$child = $node->childNodes->item($i);
					$v = $this->_convert($child);
					if(isset($child->tagName)) {
						$t = $child->tagName;

						// assume more nodes of same kind are coming
						if (!isset($output[$t])) {
							$output[$t] = array();
						}
						$output[$t][] = $v;
					} else {
						//check if it is not an empty text node
						if($v !== '') {
							$output = $v;
						}
					}
				}

				if (is_array($output)) {
					// if only one node of its kind, assign it directly instead if array($value);
					foreach ($output as $t => $v) {
						if(is_array($v) && count($v)==1) {
							$output[$t] = $v[0];
						}
					}
					if(empty($output)) {
						//for empty nodes
						$output = '';
					}
				}

				// loop through the attributes and collect them
				if($node->attributes->length) {
					$a = array();
					foreach($node->attributes as $attrName => $attrNode) {
						$a[$attrName] = (string) $attrNode->value;
					}
                    
					// if its an leaf node, store the value in @value instead of directly storing it.
					if(!is_array($output)) {
						$output = array('@value' => $output);
					}
					$output['@attributes'] = $a;
				}
				break;
		}
        
		return $output;
    }
}

/* EOF */
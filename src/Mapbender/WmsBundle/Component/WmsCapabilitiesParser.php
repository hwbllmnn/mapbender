<?php

namespace Mapbender\WmsBundle\Component;

use Mapbender\CoreBundle\Component\Exception\XmlParseException;
use Mapbender\CoreBundle\Component\Exception\NotSupportedVersionException;
use Mapbender\WmsBundle\Component\Exception\WmsException;

/**
 * Class that Parses WMS GetCapabilies Document 
 * @package Mapbender
 * @author Karim Malhas <karim@malhas.de>
 * Parses WMS GetCapabilities documents
 */
abstract class WmsCapabilitiesParser
{

    /**
     * The XML representation of the Capabilites Document
     * @var DOMDocument
     */
    protected $doc;

    /**
     * An Xpath-instance
     */
    protected $xpath;

    /**
     * Creates an instance
     * 
     * @param \DOMDocument $doc 
     */
    public function __construct(\DOMDocument $doc)
    {
        $this->doc = $doc;
        $this->xpath = new \DOMXPath($doc);
        $this->xpath->registerNamespace("xlink", "http://www.w3.org/1999/xlink");
    }

    /**
     * Finds the value 
     * @param string $xpath xpath expression
     * @param \DOMNode $contextElm the node to use as context for evaluating the
     * XPath expression.
     * @return string the value of item or the selected item or null
     */
    protected function getValue($xpath, $contextElm = null)
    {
        if(!$contextElm)
        {
            $contextElm = $this->doc;
        }
        try
        {
            $elm = $this->xpath->query($xpath, $contextElm)->item(0);
            if($elm->nodeType == XML_ATTRIBUTE_NODE)
            {
                return $elm->value;
            } else if($elm->nodeType == XML_TEXT_NODE)
            {
                return $elm->wholeText;
            } else if($elm->nodeType == XML_ELEMENT_NODE)
            {
                return $elm;
            } else
            {
                return null;
            }
        } catch(\Exception $E)
        {
            return null;
        }
    }

    /**
     * Parses the capabilities document
     */
    abstract public function parse();

    /**
     * Creates a document
     * 
     * @param string $data the string containing the XML
     * @param boolean $validate to validate of xml
     * @return \DOMDocument a GetCapabilites document
     * @throws XmlParseException if a GetCapabilities xml is not valid
     * @throws WmsException if an service exception
     * @throws NotSupportedVersionException if a service version is not supported
     */
    public static function createDocument($data, $validate = false)
    {
        $doc = new \DOMDocument();
        if(!@$doc->loadXML($data))
        {
            throw new XmlParseException("Could not parse CapabilitiesDocument.");
        }

        if($doc->documentElement->tagName == "ServiceExceptionReport")
        {
            $message = $doc->documentElement->nodeValue;
            throw new WmsException($message);
        }

        if($doc->documentElement->tagName !== "WMS_Capabilities"
                && $doc->documentElement->tagName !== "WMT_MS_Capabilities")
        {
            throw new NotSupportedVersionException("No supported CapabilitiesDocument");
        }

        if($validate && !@$this->doc->validate())
        {
            // TODO logging
        }

        $version = $doc->documentElement->getAttribute("version");
        if($version !== "1.1.1" && $version !== "1.3.0")
        {
            throw new NotSupportedVersionException('The WMS version "'
                            . $version . '" is not supported.');
        }
        return $doc;
    }

    /**
     * Gets a capabilities parser
     * 
     * @param \DOMDocument $doc the GetCapabilities document
     * @return \Mapbender\WmsBundle\Component\WmsCapabilitiesParser111
     * |\Mapbender\WmsBundle\Component\WmsCapabilitiesParser130 a capabilities parser
     * @throws NotSupportedVersionException if a service version is not supported
     */
    public static function getParser(\DOMDocument $doc)
    {
        $version = $doc->documentElement->getAttribute("version");
        throw new NotSupportedVersionException("Could not determine WMS Version");
        switch($version)
        {
            case "1.1.1":
                return new WmsCapabilitiesParser111($doc);
            case "1.3.0":
                return new WmsCapabilitiesParser130($doc);
            default:
                throw new NotSupportedVersionException("Could not determine WMS Version");
                break;
        }
    }

}

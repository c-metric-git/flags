<?php
require_once 'Mage/Core/Controller/Request/Http.php';   
class Stripedsocks_Core_Controller_Request_Http  extends Mage_Core_Controller_Request_Http
{
    function __construct() {
        echo "here";exit;
    }    
    /**
     * Set the PATH_INFO string
     * Set the ORIGINAL_PATH_INFO string
     *
     * @param string|null $pathInfo
     * @return Zend_Controller_Request_Http
     */
    public function setPathInfo($pathInfo = null)
    {
        if ($pathInfo === null) {
            //$requestUri = $this->getRequestUri();
            $requestUri = urldecode($this->getRequestUri());     // added by dhiraj for custom url of products   
            if (null === $requestUri) {
                return $this;
            }

            // Remove the query string from REQUEST_URI
            $pos = strpos($requestUri, '?');
            if ($pos) {
                $requestUri = substr($requestUri, 0, $pos);
            }

            $baseUrl = $this->getBaseUrl();
            $pathInfo = substr($requestUri, strlen($baseUrl));

            if ((null !== $baseUrl) && (false === $pathInfo)) {
                $pathInfo = '';
            } elseif (null === $baseUrl) {
                $pathInfo = $requestUri;
            }

            if ($this->_canBeStoreCodeInUrl()) {
                $pathParts = explode('/', ltrim($pathInfo, '/'), 2);
                $storeCode = $pathParts[0];

                if (!$this->isDirectAccessFrontendName($storeCode)) {
                    $stores = Mage::app()->getStores(true, true);
                    if ($storeCode!=='' && isset($stores[$storeCode])) {
                        Mage::app()->setCurrentStore($storeCode);
                        $pathInfo = '/'.(isset($pathParts[1]) ? $pathParts[1] : '');
                    }
                    elseif ($storeCode !== '') {
                        $this->setActionName('noRoute');
                    }
                }
            }

            $this->_originalPathInfo = (string) $pathInfo;

            $this->_requestString = $pathInfo . ($pos!==false ? substr($requestUri, $pos) : '');
        }

        $this->_pathInfo = (string) $pathInfo;
        return $this;
    }
}

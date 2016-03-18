<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Magethrow
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Domainpolicy
{
    /**
     * X-Frame-Options allow (header is absent)
     */
    const FRAME_POLICY_ALLOW = 1;

    /**
     * X-Frame-Options SAMEORIGIN
     */
    const FRAME_POLICY_ORIGIN = 2;

    /**
     * Path to backend domain policy settings
     */
    const XML_DOMAIN_POLICY_BACKEND = 'admin/security/domain_policy_backend';

    /**
     * Path to frontend domain policy settings
     */
    const XML_DOMAIN_POLICY_FRONTEND = 'admin/security/domain_policy_frontend';

    /**
     * Current store
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    public function __construct($options = array())
    {
        $this->_store = isset($options['store']) ? $options['store'] : Mage::app()->getStore();
    }

    /**
     * Add X-Frame-Options header to response, depends on config settings
     *
     * @var Varien_Object $observer
     * @return $this
     */
    public function addDomainPolicyHeader($observer)
    {
        /** @var Mage_Core_Controller->getCurrentAreaDomainPolicy_Varien_Action $action */
        $action = $observer->getControllerAction();
        $policy = null;

        if ('adminhtml' == $action->getLayout()->getArea()) {
            $policy = $this->getBackendPolicy();
        } elseif('frontend' == $action->getLayout()->getArea()) {
            $policy = $this->getFrontendPolicy();
        }

        if ($policy) {
            /** @var Mage_Core_Controller_Response_Http $response */
            $response = $action->getResponse();
            $response->setHeader('X-Frame-Options', $policy, true);
        }

        return $this;
    }

    /**
     * Get backend policy
     *
     * @return string|null
     */
    public function getBackendPolicy()
    {
        return $this->_getDomainPolicyByCode((int)(string)$this->_store->getConfig(self::XML_DOMAIN_POLICY_BACKEND));
    }

    /**
     * Get frontend policy
     *
     * @return string|null
     */
    public function getFrontendPolicy()
    {
        return $this->_getDomainPolicyByCode((int)(string)$this->_store->getConfig(self::XML_DOMAIN_POLICY_FRONTEND));
    }



    /**
     * Return string representation for policy code
     *
     * @param $policyCode
     * @return string|null
     */
    protected function _getDomainPolicyByCode($policyCode)
    {
        switch($policyCode) {
            case self::FRAME_POLICY_ALLOW:
                $policy = null;
                break;
            default:
                $policy = 'SAMEORIGIN';
        }

        return $policy;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Page cache observer model
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PageCache_Model_Observer
{
    const XML_NODE_ALLOWED_CACHE = 'frontend/cache/allowed_requests';

    /**
     * Check if full page cache is enabled
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return Mage::helper('pagecache')->isEnabled();
    }

    /**
     * Check when cache should be disabled
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_PageCache_Model_Observer
     */
    public function processPreDispatch(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $action = $observer->getEvent()->getControllerAction();
        $request = $action->getRequest();
        $needCaching = true;

        if ($request->isPost()) {
            $needCaching = false;
        }

        $configuration = Mage::getConfig()->getNode(self::XML_NODE_ALLOWED_CACHE);

        if (!$configuration) {
            $needCaching = false;
        }

        $configuration = $configuration->asArray();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if (!isset($configuration[$module])) {
            $needCaching = false;
        }

        if (isset($configuration[$module]['controller']) && $configuration[$module]['controller'] != $controller) {
            $needCaching = false;
        }

        if (isset($configuration[$module]['action']) && $configuration[$module]['action'] != $action) {
            $needCaching = false;
        }

        if (!$needCaching) {
            Mage::helper('pagecache')->setNoCacheCookie();
        }

        return $this;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Page cache data helper
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PageCache_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Pathes to external cache config options
     */
    const XML_PATH_EXTERNAL_CACHE_ENABLED  = 'system/external_page_cache/enabled';
    const XML_PATH_EXTERNAL_CACHE_LIFETIME = 'system/external_page_cache/cookie_lifetime';
    const XML_PATH_EXTERNAL_CACHE_CONTROL  = 'system/external_page_cache/control';

    /**
     * Path to external cache controls
     */
    const XML_PATH_EXTERNAL_CACHE_CONTROLS = 'global/external_cache/controls';

    /**
     * Cookie name for disabling external caching
     *
     * @var string
     */
    const NO_CACHE_COOKIE = 'external_no_cache';

    /**
     * Check whether external cache is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_EXTERNAL_CACHE_ENABLED);
    }

    /**
     * Return all available external cache controls
     *
     * @return array
     */
    public function getCacheControls()
    {
        $controls = Mage::app()->getConfig()->getNode(self::XML_PATH_EXTERNAL_CACHE_CONTROLS);
        return $controls->asCanonicalArray();
    }

    /**
     * Initialize proper external cache control model
     *
     * @throws Mage_Core_Exception
     * @return Mage_PageCache_Model_Control_Interface
     */
    public function getCacheControlInstance()
    {
        $usedControl = Mage::getStoreConfig(self::XML_PATH_EXTERNAL_CACHE_CONTROL);
        if ($usedControl) {
            foreach ($this->getCacheControls() as $control => $info) {
                if ($control == $usedControl && !empty($info['class'])) {
                    return Mage::getSingleton($info['class']);
                }
            }
        }
        Mage::throwException($this->__('Failed to load external cache control'));
    }

    /**
     * Disable caching on external storage side by setting special cookie
     *
     * @return void
     */
    public function setNoCacheCookie()
    {
        $cookie   = Mage::getSingleton('core/cookie');
        $lifetime = Mage::getStoreConfig(self::XML_PATH_EXTERNAL_CACHE_LIFETIME);
        $noCache  = $cookie->get(self::NO_CACHE_COOKIE);

        if ($noCache) {
            $cookie->renew(self::NO_CACHE_COOKIE, $lifetime);
        } else {
            $cookie->set(self::NO_CACHE_COOKIE, 1, $lifetime);
        }
    }

    /**
     * Returns a lifetime of cookie for external cache
     *
     * @return string Time in seconds
     */
    public function getNoCacheCookieLifetime()
    {
        return Mage::getStoreConfig(self::XML_PATH_EXTERNAL_CACHE_LIFETIME);
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Persistent Observer
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Model_Observer
{
    /**
     * Whether set quote to be persistent in workflow
     *
     * @var bool
     */
    protected $_setQuotePersistent = true;

    /**
     * Apply persistent data
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Persistent_Model_Observer
     */
    public function applyPersistentData($observer)
    {
        if (!Mage::helper('persistent')->canProcess($observer)
            || !$this->_getPersistentHelper()->isPersistent() || Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this;
        }
        Mage::getModel('persistent/persistent_config')
            ->setConfigFilePath(Mage::helper('persistent')->getPersistentConfigFilePath())
            ->fire();
        return $this;
    }

    /**
     * Apply persistent data to specific block
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Persistent_Model_Observer
     */
    public function applyBlockPersistentData($observer)
    {
        if (!$this->_getPersistentHelper()->isPersistent() || Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this;
        }

        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getEvent()->getBlock();

        if (!$block) {
            return $this;
        }

        $xPath = '//instances/blocks/*[block_type="' . get_class($block) . '"]';
        $configFilePath = $observer->getEvent()->getConfigFilePath();

        /** @var $persistentConfig Mage_Persistent_Model_Persistent_Config */
        $persistentConfig = Mage::getModel('persistent/persistent_config')
            ->setConfigFilePath(
                $configFilePath ? $configFilePath : Mage::helper('persistent')->getPersistentConfigFilePath()
            );

        foreach ($persistentConfig->getXmlConfig()->xpath($xPath) as $persistentConfigInfo) {
            $persistentConfig->fireOne($persistentConfigInfo->asArray(), $block);
        }

        return $this;
    }

    /**
     * Emulate 'welcome' block with persistent data
     *
     * @param Mage_Core_Block_Abstract $block
     * @return Mage_Persistent_Model_Observer
     */
    public function emulateWelcomeBlock($block)
    {
        $block->setWelcome(
            Mage::helper('persistent')->__('Welcome, %s!', Mage::helper('core')->escapeHtml($this->_getPersistentCustomer()->getName(), null))
        );

        $this->_applyAccountLinksPersistentData();
        $block->setAdditionalHtml(Mage::app()->getLayout()->getBlock('header.additional')->toHtml());

        return $this;
    }

    /**
     * Emulate 'account links' block with persistent data
     */
    protected function _applyAccountLinksPersistentData()
    {
        if (!Mage::app()->getLayout()->getBlock('header.additional')) {
            Mage::app()->getLayout()->addBlock('persistent/header_additional', 'header.additional');
        }
    }

    /**
     * Emulate 'account links' block with persistent data
     *
     * @param Mage_Core_Block_Abstract $block
     */
    public function emulateAccountLinks($block)
    {
        $this->_applyAccountLinksPersistentData();
        $block->getCacheKeyInfo();
        $block->addLink(
            Mage::helper('persistent')->getPersistentName(),
            Mage::helper('persistent')->getUnsetCookieUrl(),
            Mage::helper('persistent')->getPersistentName(),
            false,
            array(),
            110
        );
        $block->removeLinkByUrl(Mage::helper('customer')->getRegisterUrl());
        $block->removeLinkByUrl(Mage::helper('customer')->getLoginUrl());
    }

    /**
     * Emulate 'top links' block with persistent data
     *
     * @param Mage_Core_Block_Abstract $block
     */
    public function emulateTopLinks($block)
    {
        $this->_applyAccountLinksPersistentData();
    }

    /**
     * Emulate quote by persistent data
     *
     * @param Varien_Event_Observer $observer
     */
    public function emulateQuote($observer)
    {
        $stopActions = array(
            'persistent_index_saveMethod',
            'customer_account_createpost'
        );

        if (!Mage::helper('persistent')->canProcess($observer)
            || !$this->_getPersistentHelper()->isPersistent() || Mage::getSingleton('customer/session')->isLoggedIn()) {
            return;
        }

        /** @var $action Mage_Checkout_OnepageController */
        $action = $observer->getEvent()->getControllerAction();
        $actionName = $action->getFullActionName();

        if (in_array($actionName, $stopActions)) {
            return;
        }

        /** @var $checkoutSession Mage_Checkout_Model_Session */
        $checkoutSession = Mage::getSingleton('checkout/session');
        if ($this->_isShoppingCartPersist()) {
            $checkoutSession->setCustomer($this->_getPersistentCustomer());
            if (!$checkoutSession->hasQuote()) {
                $checkoutSession->getQuote();
            }
        }
    }

    /**
     * Set persistent data into quote
     *
     * @param Varien_Event_Observer $observer
     */
    public function setQuotePersistentData($observer)
    {
        if (!$this->_isPersistent()) {
            return;
        }

        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();
        if (!$quote) {
            return;
        }

        if ($this->_isGuestShoppingCart() && $this->_setQuotePersistent) {
            //Quote is not actual customer's quote, just persistent
            $quote->setIsActive(false)->setIsPersistent(true);
        }
    }

    /**
     * Set quote to be loaded even if not active
     *
     * @param Varien_Event_Observer $observer
     */
    public function setLoadPersistentQuote($observer)
    {
        if (!$this->_isGuestShoppingCart()) {
            return;
        }

        /** @var $checkoutSession Mage_Checkout_Model_Session */
        $checkoutSession = $observer->getEvent()->getCheckoutSession();
        if ($checkoutSession) {
            $checkoutSession->setLoadInactive();
        }
    }

    /**
     * Prevent clear checkout session
     *
     * @param Varien_Event_Observer $observer
     */
    public function preventClearCheckoutSession($observer)
    {
        $action = $this->_checkClearCheckoutSessionNecessity($observer);

        if ($action) {
            $action->setClearCheckoutSession(false);
        }
    }

    /**
     * Make persistent quote to be guest
     *
     * @param Varien_Event_Observer $observer
     */
    public function makePersistentQuoteGuest($observer)
    {
        if (!$this->_checkClearCheckoutSessionNecessity($observer)) {
            return;
        }

        $this->setQuoteGuest(true);
    }

    /**
     * Check if checkout session should NOT be cleared
     *
     * @param Varien_Event_Observer $observer
     * @return bool|Mage_Persistent_IndexController
     */
    protected function _checkClearCheckoutSessionNecessity($observer)
    {
        if (!$this->_isGuestShoppingCart()) {
            return false;
        }

        /** @var $action Mage_Persistent_IndexController */
        $action = $observer->getEvent()->getControllerAction();
        if ($action instanceof Mage_Persistent_IndexController) {
            return $action;
        }

        return false;
    }

    /**
     * Reset session data when customer re-authenticates
     *
     * @param Varien_Event_Observer $observer
     */
    public function customerAuthenticatedEvent($observer)
    {
        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('customer/session');
        $customerSession->setCustomerId(null)->setCustomerGroupId(null);

        if (Mage::app()->getRequest()->getParam('context') != 'checkout') {
            $this->_expirePersistentSession();
            return;
        }

        $this->setQuoteGuest();
    }

    /**
     * Unset persistent cookie and make customer's quote as a guest
     *
     * @param Varien_Event_Observer $observer
     */
    public function removePersistentCookie($observer)
    {
        if (!Mage::helper('persistent')->canProcess($observer) || !$this->_isPersistent()) {
            return;
        }

        $this->_getPersistentHelper()->getSession()->removePersistentCookie();
        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn()) {
            $customerSession->setCustomerId(null)->setCustomerGroupId(null);
        }

        $this->setQuoteGuest();
    }

    /**
     * Disable guest checkout if we are in persistent mode
     *
     * @param Varien_Event_Observer $observer
     */
    public function disableGuestCheckout($observer)
    {
        if ($this->_getPersistentHelper()->isPersistent()) {
            $observer->getEvent()->getResult()->setIsAllowed(false);
        }
    }

    /**
     * Prevent express checkout with PayPal Express checkout
     *
     * @param Varien_Event_Observer $observer
     */
    public function preventExpressCheckout($observer)
    {
        if (!$this->_isLoggedOut()) {
            return;
        }

        /** @var $controllerAction Mage_Core_Controller_Front_Action */
        $controllerAction = $observer->getEvent()->getControllerAction();
        if (method_exists($controllerAction, 'redirectLogin')) {
            Mage::getSingleton('core/session')->addNotice(
                Mage::helper('persistent')->__('To proceed to Checkout, please log in using your email address.')
            );
            $controllerAction->redirectLogin();
            if ($controllerAction instanceof Mage_Paypal_Controller_Express_Abstract) {
                Mage::getSingleton('customer/session')
                    ->setBeforeAuthUrl(Mage::getUrl('persistent/index/expressCheckout'));
            }
        }
    }

    /**
     * Retrieve persistent customer instance
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getPersistentCustomer()
    {
        return Mage::getModel('customer/customer')->load(
            $this->_getPersistentHelper()->getSession()->getCustomerId()
        );
    }

    /**
     * Retrieve persistent helper
     *
     * @return Mage_Persistent_Helper_Session
     */
    protected function _getPersistentHelper()
    {
        return Mage::helper('persistent/session');
    }

    /**
     * Return current active quote for persistent customer
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        $quote = Mage::getModel('sales/quote');
        $quote->loadByCustomer($this->_getPersistentCustomer());
        return $quote;
    }

    /**
     * Check whether shopping cart is persistent
     *
     * @return bool
     */
    protected function _isShoppingCartPersist()
    {
        return Mage::helper('persistent')->isShoppingCartPersist();
    }

    /**
     * Check whether persistent mode is running
     *
     * @return bool
     */
    protected function _isPersistent()
    {
        return $this->_getPersistentHelper()->isPersistent();
    }

    /**
     * Check if persistent mode is running and customer is logged out
     *
     * @return bool
     */
    protected function _isLoggedOut()
    {
        return $this->_isPersistent() && !Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /**
     * Check if shopping cart is guest while persistent session and user is logged out
     *
     * @return bool
     */
    protected function _isGuestShoppingCart()
    {
        return $this->_isLoggedOut() && !Mage::helper('persistent')->isShoppingCartPersist();
    }

    /**
     * Make quote to be guest
     *
     * @param bool $checkQuote Check quote to be persistent (not stolen)
     */
    public function setQuoteGuest($checkQuote = false)
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote && $quote->getId()) {
            if ($checkQuote && !Mage::helper('persistent')->isShoppingCartPersist() && !$quote->getIsPersistent()) {
                Mage::getSingleton('checkout/session')->unsetAll();
                return;
            }

            $quote->getPaymentsCollection()->walk('delete');
            $quote->getAddressesCollection()->walk('delete');
            $this->_setQuotePersistent = false;
            $quote
                ->setIsActive(true)
                ->setCustomerId(null)
                ->setCustomerEmail(null)
                ->setCustomerFirstname(null)
                ->setCustomerLastname(null)
                ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID)
                ->setIsPersistent(false)
                ->removeAllAddresses();
            //Create guest addresses
            $quote->getShippingAddress();
            $quote->getBillingAddress();
            $quote->collectTotals()->save();
        }

        $this->_getPersistentHelper()->getSession()->removePersistentCookie();
    }

    /**
     * Check and clear session data if persistent session expired
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkExpirePersistentQuote(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('persistent')->canProcess($observer)) {
            return;
        }

        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('customer/session');

        if (Mage::helper('persistent')->isEnabled()
            && !$this->_isPersistent()
            && !$customerSession->isLoggedIn()
            && Mage::getSingleton('checkout/session')->getQuoteId()
            && !($observer->getControllerAction() instanceof Mage_Checkout_OnepageController)
            // persistent session does not expire on onepage checkout page to not spoil customer group id
        ) {
            Mage::dispatchEvent('persistent_session_expired');
            $this->_expirePersistentSession();
            $customerSession->setCustomerId(null)->setCustomerGroupId(null);
        }
    }
    /**
     * Active Persistent Sessions
     */
    protected function _expirePersistentSession()
    {
        /** @var $checkoutSession Mage_Checkout_Model_Session */
        $checkoutSession = Mage::getSingleton('checkout/session');

        $quote = $checkoutSession->setLoadInactive()->getQuote();
        if ($quote->getIsActive() && $quote->getCustomerId()) {
            $checkoutSession->setCustomer(null)->unsetAll();
        } else {
            $quote
                ->setIsActive(true)
                ->setIsPersistent(false)
                ->setCustomerId(null)
                ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        }
    }

    /**
     * Clear expired persistent sessions
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @return Mage_Persistent_Model_Observer_Cron
     */
    public function clearExpiredCronJob(Mage_Cron_Model_Schedule $schedule)
    {
        $websiteIds = Mage::getResourceModel('core/website_collection')->getAllIds();
        if (!is_array($websiteIds)) {
            return $this;
        }

        foreach ($websiteIds as $websiteId) {
            Mage::getModel('persistent/session')->deleteExpired($websiteId);
        }

        return $this;
    }

    /**
     * Create handle for persistent session if persistent cookie and customer not logged in
     *
     * @param Varien_Event_Observer $observer
     */
    public function createPersistentHandleLayout(Varien_Event_Observer $observer)
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = $observer->getEvent()->getLayout();
        if (Mage::helper('persistent')->canProcess($observer) && $layout && Mage::helper('persistent')->isEnabled()
            && Mage::helper('persistent/session')->isPersistent()
        ) {
            $handle = (Mage::getSingleton('customer/session')->isLoggedIn())
                ? Mage_Persistent_Helper_Data::LOGGED_IN_LAYOUT_HANDLE
                : Mage_Persistent_Helper_Data::LOGGED_OUT_LAYOUT_HANDLE;
            $layout->getUpdate()->addHandle($handle);
        }
    }

    /**
     * Update customer id and customer group id if user is in persistent session
     *
     * @param Varien_Event_Observer $observer
     */
    public function updateCustomerCookies(Varien_Event_Observer $observer)
    {
        if (!$this->_isPersistent()) {
            return;
        }

        $customerCookies = $observer->getEvent()->getCustomerCookies();
        if ($customerCookies instanceof Varien_Object) {
            $persistentCustomer = $this->_getPersistentCustomer();
            $customerCookies->setCustomerId($persistentCustomer->getId());
            $customerCookies->setCustomerGroupId($persistentCustomer->getGroupId());
        }
    }

    /**
     * Set persistent data to customer session
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Persistent_Model_Observer
     */
    public function emulateCustomer($observer)
    {
        if (!Mage::helper('persistent')->canProcess($observer)
            || !$this->_isShoppingCartPersist()
        ) {
            return $this;
        }

        if ($this->_isLoggedOut()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer')->load(
                $this->_getPersistentHelper()->getSession()->getCustomerId()
            );
            Mage::getSingleton('customer/session')
                ->setCustomerId($customer->getId())
                ->setCustomerGroupId($customer->getGroupId());
        }
        return $this;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Persistent Shopping Cart Data Helper
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Helper_Data extends Mage_Core_Helper_Data
{
    const XML_PATH_ENABLED = 'persistent/options/enabled';
    const XML_PATH_LIFE_TIME = 'persistent/options/lifetime';
    const XML_PATH_LOGOUT_CLEAR = 'persistent/options/logout_clear';
    const XML_PATH_REMEMBER_ME_ENABLED = 'persistent/options/remember_enabled';
    const XML_PATH_REMEMBER_ME_DEFAULT = 'persistent/options/remember_default';
    const XML_PATH_PERSIST_SHOPPING_CART = 'persistent/options/shopping_cart';

    const LOGGED_IN_LAYOUT_HANDLE = 'customer_logged_in_psc_handle';
    const LOGGED_OUT_LAYOUT_HANDLE = 'customer_logged_out_psc_handle';

    /**
     * Name of config file
     *
     * @var string
     */
    protected $_configFileName = 'persistent.xml';

    /**
     * Checks whether Persistence Functionality is enabled
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }

    /**
     * Checks whether "Remember Me" enabled
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isRememberMeEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_REMEMBER_ME_ENABLED, $store);
    }

    /**
     * Is "Remember Me" checked by default
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isRememberMeCheckedDefault($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_REMEMBER_ME_DEFAULT, $store);
    }

    /**
     * Is shopping cart persist
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isShoppingCartPersist($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PERSIST_SHOPPING_CART, $store);
    }

    /**
     * Get Persistence Lifetime
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return int
     */
    public function getLifeTime($store = null)
    {
        $lifeTime = intval(Mage::getStoreConfig(self::XML_PATH_LIFE_TIME, $store));
        return ($lifeTime < 0) ? 0 : $lifeTime;
    }

    /**
     * Check if set `Clear on Logout` in config settings
     *
     * @return bool
     */
    public function getClearOnLogout()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_LOGOUT_CLEAR);
    }

    /**
     * Retrieve url for unset long-term cookie
     *
     * @return string
     */
    public function getUnsetCookieUrl()
    {
        return $this->_getUrl('persistent/index/unsetCookie');
    }

    /**
     * Retrieve name of persistent customer
     *
     * @return string
     */
    public function getPersistentName()
    {
        return $this->__('(Not %s?)', $this->escapeHtml(Mage::helper('persistent/session')->getCustomer()->getName()));
    }

    /**
     * Retrieve path for config file
     *
     * @return string
     */
    public function getPersistentConfigFilePath()
    {
        return Mage::getConfig()->getModuleDir('etc', $this->_getModuleName()) . DS . $this->_configFileName;
    }

    /**
     * Check whether specified action should be processed
     *
     * @param Varien_Event_Observer $observer
     * @return bool
     */
    public function canProcess($observer)
    {
        $action = $observer->getEvent()->getAction();
        $controllerAction = $observer->getEvent()->getControllerAction();

        if ($action instanceof Mage_Core_Controller_Varien_Action) {
            return !$action->getFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_START_SESSION);
        }
        if ($controllerAction instanceof Mage_Core_Controller_Varien_Action) {
            return !$controllerAction->getFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_START_SESSION);
        }
        return true;
    }

    /**
     * Get create account url depends on checkout
     *
     * @param  $url string
     * @return string
     */
    public function getCreateAccountUrl($url)
    {
        if (Mage::helper('checkout')->isContextCheckout()) {
            $url = Mage::helper('core/url')->addRequestParam($url, array('context' => 'checkout'));
        }
        return $url;
    }

}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Persistent Shopping Cart Data Helper
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Helper_Session extends Mage_Core_Helper_Data
{
    /**
     * Instance of Session Model
     *
     * @var null|Mage_Persistent_Model_Session
     */
    protected $_sessionModel;

    /**
     * Persistent customer
     *
     * @var null|Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Is "Remember Me" checked
     *
     * @var null|bool
     */
    protected $_isRememberMeChecked;

    /**
     * Get Session model
     *
     * @return Mage_Persistent_Model_Session
     */
    public function getSession()
    {
        if (is_null($this->_sessionModel)) {
            $this->_sessionModel = Mage::getModel('persistent/session');
            $this->_sessionModel->loadByCookieKey();
        }
        return $this->_sessionModel;
    }

    /**
     * Force setting session model
     *
     * @param Mage_Persistent_Model_Session $sessionModel
     * @return Mage_Persistent_Model_Session
     */
    public function setSession($sessionModel)
    {
        $this->_sessionModel = $sessionModel;
        return $this->_sessionModel;
    }

    /**
     * Check whether persistent mode is running
     *
     * @return bool
     */
    public function isPersistent()
    {
        return $this->getSession()->getId() && Mage::helper('persistent')->isEnabled();
    }

    /**
     * Check if "Remember Me" checked
     *
     * @return bool
     */
    public function isRememberMeChecked()
    {
        if (is_null($this->_isRememberMeChecked)) {
            //Try to get from checkout session
            $isRememberMeChecked = Mage::getSingleton('checkout/session')->getRememberMeChecked();
            if (!is_null($isRememberMeChecked)) {
                $this->_isRememberMeChecked = $isRememberMeChecked;
                Mage::getSingleton('checkout/session')->unsRememberMeChecked();
                return $isRememberMeChecked;
            }

            /** @var $helper Mage_Persistent_Helper_Data */
            $helper = Mage::helper('persistent');
            return $helper->isEnabled() && $helper->isRememberMeEnabled() && $helper->isRememberMeCheckedDefault();
        }

        return (bool)$this->_isRememberMeChecked;
    }

    /**
     * Set "Remember Me" checked or not
     *
     * @param bool $checked
     */
    public function setRememberMeChecked($checked = true)
    {
        $this->_isRememberMeChecked = $checked;
    }

    /**
     * Return persistent customer
     *
     * @return Mage_Customer_Model_Customer|bool
     */
    public function getCustomer()
    {
        if (is_null($this->_customer)) {
            $customerId = $this->getSession()->getCustomerId();
            $this->_customer = Mage::getModel('customer/customer')->load($customerId);
        }
        return $this->_customer;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Persistent Session Model
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Model_Session extends Mage_Core_Model_Abstract
{
    const KEY_LENGTH = 50;
    const COOKIE_NAME = 'persistent_shopping_cart';

    /**
     * Fields which model does not save into `info` db field
     *
     * @var array
     */
    protected $_unserializableFields = array('persistent_id', 'key', 'customer_id', 'website_id', 'info', 'updated_at');

    /**
     * If model loads expired sessions
     *
     * @var bool
     */
    protected $_loadExpired = false;

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('persistent/session');
    }

    /**
     * Set if load expired persistent session
     *
     * @param bool $loadExpired
     * @return Mage_Persistent_Model_Session
     */
    public function setLoadExpired($loadExpired = true)
    {
        $this->_loadExpired = $loadExpired;
        return $this;
    }

    /**
     * Get if model loads expired sessions
     *
     * @return bool
     */
    public function getLoadExpired()
    {
        return $this->_loadExpired;
    }

    /**
     * Get date-time before which persistent session is expired
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return string
     */
    public function getExpiredBefore($store = null)
    {
        return gmdate('Y-m-d H:i:s', time() - Mage::helper('persistent')->getLifeTime($store));
    }

    /**
     * Serialize info for Resource Model to save
     * For new model check and set available cookie key
     *
     * @return Mage_Persistent_Model_Session
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        // Setting info
        $info = array();
        foreach ($this->getData() as $index => $value) {
            if (!in_array($index, $this->_unserializableFields)) {
                $info[$index] = $value;
            }
        }
        $this->setInfo(Mage::helper('core')->jsonEncode($info));

        if ($this->isObjectNew()) {
            $this->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            // Setting cookie key
            do {
                $this->setKey(Mage::helper('core')->getRandomString(self::KEY_LENGTH));
            } while (!$this->getResource()->isKeyAllowed($this->getKey()));
        }

        return $this;
    }

    /**
     * Set model data from info field
     *
     * @return Mage_Persistent_Model_Session
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $info = Mage::helper('core')->jsonDecode($this->getInfo());
        if (is_array($info)) {
            foreach ($info as $key => $value) {
                $this->setData($key, $value);
            }
        }
        return $this;
    }

    /**
     * Get persistent session by cookie key
     *
     * @param string $key
     * @return Mage_Persistent_Model_Session
     */
    public function loadByCookieKey($key = null)
    {
        if (is_null($key)) {
            $key = Mage::getSingleton('core/cookie')->get(Mage_Persistent_Model_Session::COOKIE_NAME);
        }
        if ($key) {
            $this->load($key, 'key');
        }

        return $this;
    }

    /**
     * Load session model by specified customer id
     *
     * @param int $id
     * @return Mage_Core_Model_Abstract
     */
    public function loadByCustomerId($id)
    {
        return $this->load($id, 'customer_id');
    }

    /**
     * Delete customer persistent session by customer id
     *
     * @param int $customerId
     * @param bool $clearCookie
     * @return Mage_Persistent_Model_Session
     */
    public function deleteByCustomerId($customerId, $clearCookie = true)
    {
        if ($clearCookie) {
            $this->removePersistentCookie();
        }
        $this->getResource()->deleteByCustomerId($customerId);
        return $this;
    }

    /**
     * Remove persistent cookie
     *
     * @return Mage_Persistent_Model_Session
     */
    public function removePersistentCookie()
    {
        Mage::getSingleton('core/cookie')->delete(Mage_Persistent_Model_Session::COOKIE_NAME);
        return $this;
    }

    /**
     * Delete expired persistent sessions for the website
     *
     * @param null|int $websiteId
     * @return Mage_Persistent_Model_Session
     */
    public function deleteExpired($websiteId = null)
    {
        if (is_null($websiteId)) {
            $websiteId = Mage::app()->getStore()->getWebsiteId();
        }

        $lifetime = Mage::getConfig()->getNode(
            Mage_Persistent_Helper_Data::XML_PATH_LIFE_TIME,
            'website',
            intval($websiteId)
        );

        if ($lifetime) {
            $this->getResource()->deleteExpired(
                $websiteId,
                gmdate('Y-m-d H:i:s', time() - $lifetime)
            );
        }

        return $this;
    }

    /**
     * Delete 'persistent' cookie
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterDeleteCommit() {
        Mage::getSingleton('core/cookie')->delete(Mage_Persistent_Model_Session::COOKIE_NAME);
        return parent::_afterDeleteCommit();
    }

    /**
     * Set `updated_at` to be always changed
     *
     * @return Mage_Persistent_Model_Session
     */
    public function save()
    {
        $this->setUpdatedAt(gmdate('Y-m-d H:i:s'));
        return parent::save();
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Persistent Session Resource Model
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Model_Resource_Session extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Use is object new method for object saving
     *
     * @var boolean
     */
    protected $_useIsObjectNew = true;

    /**
     * Initialize connection and define main table and primary key
     */
    protected function _construct()
    {
        $this->_init('persistent/session', 'persistent_id');
    }

    /**
     * Add expiration date filter to select
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Persistent_Model_Session $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if (!$object->getLoadExpired()) {
            $tableName = $this->getMainTable();
            $select->join(array('customer' => $this->getTable('customer/entity')),
                'customer.entity_id = ' . $tableName . '.customer_id'
            )->where($tableName . '.updated_at >= ?', $object->getExpiredBefore());
        }

        return $select;
    }

    /**
     * Delete customer persistent session by customer id
     *
     * @param int $customerId
     * @return Mage_Persistent_Model_Resource_Session
     */
    public function deleteByCustomerId($customerId)
    {
        $this->_getWriteAdapter()->delete($this->getMainTable(), array('customer_id = ?' => $customerId));
        return $this;
    }

    /**
     * Check if such session key allowed (not exists)
     *
     * @param string $key
     * @return bool
     */
    public function isKeyAllowed($key)
    {
        $sameSession = Mage::getModel('persistent/session')->setLoadExpired();
        $sameSession->loadByCookieKey($key);
        return !$sameSession->getId();
    }

    /**
     * Delete expired persistent sessions
     *
     * @param  $websiteId
     * @param  $expiredBefore
     * @return Mage_Persistent_Model_Resource_Session
     */
    public function deleteExpired($websiteId, $expiredBefore)
    {
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            array(
                'website_id = ?' => $websiteId,
                'updated_at < ?' => $expiredBefore,
            )
        );
        return $this;
    }
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Persistent Session Observer
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Model_Observer_Session
{
    /**
     * Create/Update and Load session when customer log in
     *
     * @param Varien_Event_Observer $observer
     */
    public function synchronizePersistentOnLogin(Varien_Event_Observer $observer)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getEvent()->getCustomer();
        // Check if customer is valid (remove persistent cookie for invalid customer)
        if (!$customer || !$customer->getId() || !Mage::helper('persistent/session')->isRememberMeChecked()) {
            Mage::getModel('persistent/session')->removePersistentCookie();
            return;
        }

        $persistentLifeTime = Mage::helper('persistent')->getLifeTime();
        // Delete persistent session, if persistent could not be applied
        if (Mage::helper('persistent')->isEnabled() && ($persistentLifeTime <= 0)) {
            // Remove current customer persistent session
            Mage::getModel('persistent/session')->deleteByCustomerId($customer->getId());
            return;
        }

        /** @var $sessionModel Mage_Persistent_Model_Session */
        $sessionModel = Mage::helper('persistent/session')->getSession();

        // Check if session is wrong or not exists, so create new session
        if (!$sessionModel->getId() || ($sessionModel->getCustomerId() != $customer->getId())) {
            $sessionModel = Mage::getModel('persistent/session')
                ->setLoadExpired()
                ->loadByCustomerId($customer->getId());
            if (!$sessionModel->getId()) {
                $sessionModel = Mage::getModel('persistent/session')
                    ->setCustomerId($customer->getId())
                    ->save();
            }

            Mage::helper('persistent/session')->setSession($sessionModel);
        }

        // Set new cookie
        if ($sessionModel->getId()) {
            Mage::getSingleton('core/cookie')->set(
                Mage_Persistent_Model_Session::COOKIE_NAME,
                $sessionModel->getKey(),
                $persistentLifeTime
            );
        }
    }

    /**
     * Unload persistent session (if set in config)
     *
     * @param Varien_Event_Observer $observer
     */
    public function synchronizePersistentOnLogout(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('persistent')->isEnabled() || !Mage::helper('persistent')->getClearOnLogout()) {
            return;
        }

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getEvent()->getCustomer();
        // Check if customer is valid
        if (!$customer || !$customer->getId()) {
            return;
        }

        Mage::getModel('persistent/session')->removePersistentCookie();

        // Unset persistent session
        Mage::helper('persistent/session')->setSession(null);
    }

    /**
     * Synchronize persistent session info
     *
     * @param Varien_Event_Observer $observer
     */
    public function synchronizePersistentInfo(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('persistent')->isEnabled() || !Mage::helper('persistent/session')->isPersistent()) {
            return;
        }

        /** @var $sessionModel Mage_Persistent_Model_Session */
        $sessionModel = Mage::helper('persistent/session')->getSession();

        /** @var $request Mage_Core_Controller_Request_Http */
        $request = $observer->getEvent()->getFront()->getRequest();

        // Quote Id could be changed only by logged in customer
        if (Mage::getSingleton('customer/session')->isLoggedIn()
            || ($request && $request->getActionName() == 'logout' && $request->getControllerName() == 'account')
        ) {
            $sessionModel->save();
        }
    }

    /**
     * Set Checked status of "Remember Me"
     *
     * @param Varien_Event_Observer $observer
     */
    public function setRememberMeCheckedStatus(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('persistent')->canProcess($observer)
            || !Mage::helper('persistent')->isEnabled() || !Mage::helper('persistent')->isRememberMeEnabled()
        ) {
            return;
        }

        /** @var $controllerAction Mage_Core_Controller_Varien_Action */
        $controllerAction = $observer->getEvent()->getControllerAction();
        if ($controllerAction) {
            $rememberMeCheckbox = $controllerAction->getRequest()->getPost('persistent_remember_me');
            Mage::helper('persistent/session')->setRememberMeChecked((bool)$rememberMeCheckbox);
            if (
                $controllerAction->getFullActionName() == 'checkout_onepage_saveBilling'
                    || $controllerAction->getFullActionName() == 'customer_account_createpost'
            ) {
                Mage::getSingleton('checkout/session')->setRememberMeChecked((bool)$rememberMeCheckbox);
            }
        }
    }

    /**
     * Renew persistent cookie
     *
     * @param Varien_Event_Observer $observer
     */
    public function renewCookie(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('persistent')->canProcess($observer)
            || !Mage::helper('persistent')->isEnabled() || !Mage::helper('persistent/session')->isPersistent()
        ) {
            return;
        }

        /** @var $controllerAction Mage_Core_Controller_Front_Action */
        $controllerAction = $observer->getEvent()->getControllerAction();

        if (Mage::getSingleton('customer/session')->isLoggedIn()
            || $controllerAction->getFullActionName() == 'customer_account_logout'
        ) {
            Mage::getSingleton('core/cookie')->renew(
                Mage_Persistent_Model_Session::COOKIE_NAME,
                Mage::helper('persistent')->getLifeTime()
            );
        }
    }
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Cron_Type_Abstract
{
    const MAX_MEMORY_LIMIT = 1024;

    private $previousStoreId = NULL;

    private $lockItem = NULL;
    private $operationHistory = NULL;

    private $initiator = Ess_M2ePro_Helper_Data::INITIATOR_UNKNOWN;

    //####################################

    public function process()
    {
        if ($this->isDisabledByDeveloper()) {
            return false;
        }

        $this->initialize();
        $this->updateLastAccess();

        if (!$this->isPossibleToRun()) {
            $this->deInitialize();
            return true;
        }

        $this->updateLastRun();
        $this->beforeStart();

        $result = true;

        try {

            // local tasks
            $result = !$this->processTask(Ess_M2ePro_Model_Cron_Task_LogsClearing::NICK) ? false : $result;

            // request tasks
            $result = !$this->processTask(Ess_M2ePro_Model_Cron_Task_Servicing::NICK) ? false : $result;
            $result = !$this->processTask(Ess_M2ePro_Model_Cron_Task_Processing::NICK) ? false : $result;
            $result = !$this->processTask(Ess_M2ePro_Model_Cron_Task_Synchronization::NICK) ? false : $result;

        } catch (Exception $exception) {

            $result = false;

            Mage::helper('M2ePro/Module_Exception')->process($exception);

            $this->getOperationHistory()->setContentData('exception', array(
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'trace'   => $exception->getTraceAsString(),
            ));
        }

        $this->afterEnd();
        $this->deInitialize();

        return $result;
    }

    protected function processTask($task)
    {
        $task = str_replace('_',' ',$task);
        $task = str_replace(' ','',ucwords($task));

        /** @var $task Ess_M2ePro_Model_Cron_Task_Abstract **/
        $task = Mage::getModel('M2ePro/Cron_Task_'.trim($task));

        $task->setInitiator($this->getInitiator());
        $task->setParentLockItem($this->getLockItem());
        $task->setParentOperationHistory($this->getOperationHistory());

        $result = $task->process();

        return is_null($result) || $result;
    }

    // -----------------------------------

    abstract protected function getType();

    //####################################

    public function setInitiator($value)
    {
        $this->initiator = (int)$value;
    }

    public function getInitiator()
    {
        return $this->initiator;
    }

    //####################################

    protected function isDisabledByDeveloper()
    {
        return false;
    }

    protected function initialize()
    {
        $this->previousStoreId = Mage::app()->getStore()->getId();
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        Mage::helper('M2ePro/Client')->setMemoryLimit(self::MAX_MEMORY_LIMIT);
        Mage::helper('M2ePro/Module_Exception')->setFatalErrorHandler();
    }

    protected function deInitialize()
    {
        if (!is_null($this->previousStoreId)) {
            Mage::app()->setCurrentStore($this->previousStoreId);
            $this->previousStoreId = NULL;
        }
    }

    //####################################

    protected function updateLastAccess()
    {
        $currentDateTime = Mage::helper('M2ePro')->getCurrentGmtDate();
        Mage::helper('M2ePro/Module_Cron')->setLastAccess($currentDateTime);
    }

    protected function isPossibleToRun()
    {
        $helper = Mage::helper('M2ePro/Module_Cron');

        return $this->getType() == $helper->getType() &&
               $helper->isModeEnabled() &&
               $helper->isReadyToRun() &&
               !$this->getLockItem()->isExist();
    }

    protected function updateLastRun()
    {
        $currentDateTime = Mage::helper('M2ePro')->getCurrentGmtDate();
        Mage::helper('M2ePro/Module_Cron')->setLastRun($currentDateTime);
    }

    // -----------------------------------

    protected function beforeStart()
    {
        $this->getLockItem()->create();
        $this->getLockItem()->makeShutdownFunction();

        $this->getOperationHistory()->cleanOldData();

        $this->getOperationHistory()->start('cron',NULL,$this->getInitiator());
        $this->getOperationHistory()->makeShutdownFunction();
    }

    protected function afterEnd()
    {
        $this->getOperationHistory()->stop();
        $this->getLockItem()->remove();
    }

    //####################################

    protected function getLockItem()
    {
        if (is_null($this->lockItem)) {
            $this->lockItem = Mage::getModel('M2ePro/LockItem');
            $this->lockItem->setNick('cron');
        }
        return $this->lockItem;
    }

    protected function getOperationHistory()
    {
        if (is_null($this->operationHistory)) {
            $this->operationHistory = Mage::getModel('M2ePro/OperationHistory');
        }
        return $this->operationHistory;
    }

    //####################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

final class Ess_M2ePro_Model_Cron_Type_Service extends Ess_M2ePro_Model_Cron_Type_Abstract
{
    const MAX_INACTIVE_TIME = 300;

    private $requestAuthKey = NULL;
    private $requestConnectionId = NULL;

    //####################################

    protected function getType()
    {
        return Ess_M2ePro_Helper_Module_Cron::TYPE_SERVICE;
    }

    //####################################

    public function setRequestAuthKey($value)
    {
        $this->requestAuthKey = $value;
    }

    public function getRequestAuthKey()
    {
        return $this->requestAuthKey;
    }

    // -----------------------------------

    public function setRequestConnectionId($value)
    {
        $this->requestConnectionId = $value;
    }

    public function getRequestConnectionId()
    {
        return $this->requestConnectionId;
    }

    // -----------------------------------

    public function resetTasksStartFrom()
    {
        $this->resetTaskStartFrom('processing');
        $this->resetTaskStartFrom('servicing');
        $this->resetTaskStartFrom('synchronization');
    }

    //####################################

    protected function isDisabledByDeveloper()
    {
        return (bool)(int)Mage::helper('M2ePro/Module')->getConfig()
                              ->getGroupValue('/cron/service/','disabled');
    }

    protected function initialize()
    {
        parent::initialize();

        $helper = Mage::helper('M2ePro/Module_Cron');

        if (!$helper->isTypeService()) {

            $helper->setType(Ess_M2ePro_Helper_Module_Cron::TYPE_SERVICE);
            $helper->setLastTypeChange(Mage::helper('M2ePro')->getCurrentGmtDate());

            $this->resetTasksStartFrom();

        } else {
            $helper->isLastAccessMoreThan(self::MAX_INACTIVE_TIME) &&
                $this->resetTasksStartFrom();
        }
    }

    protected function isPossibleToRun()
    {
        return !is_null($this->getAuthKey()) &&
               !is_null($this->getRequestAuthKey()) &&
               !is_null($this->getRequestConnectionId()) &&
               $this->getAuthKey() == $this->getRequestAuthKey() &&
               parent::isPossibleToRun();
    }

    // -----------------------------------

    protected function beforeStart()
    {
        parent::beforeStart();
        $this->getOperationHistory()->setContentData('connection_id',$this->getRequestConnectionId());
    }

    //####################################

    private function getAuthKey()
    {
        return Mage::helper('M2ePro/Module')->getConfig()
                    ->getGroupValue('/cron/service/','auth_key');
    }

    private function resetTaskStartFrom($taskName)
    {
        $config = Mage::helper('M2ePro/Module')->getConfig();

        $startDate = new DateTime(Mage::helper('M2ePro')->getCurrentGmtDate(), new DateTimeZone('UTC'));
        $shift = 60 + rand(0,(int)$config->getGroupValue('/cron/task/'.$taskName.'/','interval'));
        $startDate->modify('+'.$shift.' seconds');

        $config->setGroupValue('/cron/task/'.$taskName.'/','start_from',$startDate->format('Y-m-d H:i:s'));
    }

    //####################################
}

/*
 * @copyright  Copyright (c) 2014 by  ESS-UA.
 */

class Ess_M2ePro_Helper_Data extends Mage_Core_Helper_Abstract
{
    const STATUS_ERROR      = 1;
    const STATUS_WARNING    = 2;
    const STATUS_SUCCESS    = 3;

    const INITIATOR_UNKNOWN   = 0;
    const INITIATOR_USER      = 1;
    const INITIATOR_EXTENSION = 2;
    const INITIATOR_DEVELOPER = 3;

    const CUSTOM_IDENTIFIER = 'm2epro_extension';

    // ########################################

    public function __()
    {
        $args = func_get_args();
        return Mage::helper('M2ePro/Module_Translation')->translate($args);
    }

    // ########################################

    /**
     * @param $modelName
     * @param array $params
     * @return Ess_M2ePro_Model_Abstract
     */
    public function getModel($modelName, $params = array())
    {
        return Mage::getModel('M2ePro/'.$modelName,$params);
    }

    public function getHelper($helperName = NULL)
    {
        is_string($helperName) && $helperName = '/'.$helperName;
        return Mage::helper('M2ePro'.(string)$helperName);
    }

    //-----------------------------------------

    /**
     * @param string $modelName
     * @param mixed $value
     * @param null|string $field
     * @return Ess_M2ePro_Model_Abstract
     */
    public function getObject($modelName, $value, $field = NULL)
    {
        return $this->getModel($modelName)->loadInstance($value, $field);
    }

    /**
     * @param string $modelName
     * @param mixed $value
     * @param null|string $field
     * @param array $tags
     * @return Ess_M2ePro_Model_Abstract
     */
    public function getCachedObject($modelName, $value, $field = NULL, array $tags = array())
    {
        if (Mage::helper('M2ePro/Module')->isDevelopmentEnvironment()) {
            return $this->getObject($modelName,$value,$field);
        }

        $cacheKey = strtoupper($modelName.'_data_'.$field.'_'.$value);
        $cacheData = Mage::helper('M2ePro/Data_Cache_Permanent')->getValue($cacheKey);

        if ($cacheData !== false) {
            return $cacheData;
        }

        $tags[] = $modelName;

        if (strpos($modelName,'_') !== false) {

            $allComponents = Mage::helper('M2ePro/Component')->getComponents();
            $modelNameComponent = substr($modelName,0,strpos($modelName,'_'));

            if (in_array(strtolower($modelNameComponent),array_map('strtolower',$allComponents))) {
                $modelNameOnlyModel = substr($modelName,strpos($modelName,'_')+1);
                $tags[] = $modelNameComponent;
                $tags[] = $modelNameOnlyModel;
            }
        }

        $tags = array_unique($tags);
        $tags = array_map('strtolower',$tags);

        $cacheData = $this->getObject($modelName,$value,$field);

        if (!empty($cacheData)) {
            Mage::helper('M2ePro/Data_Cache_Permanent')->setValue($cacheKey,$cacheData,$tags,60*60*24);
        }

        return $cacheData;
    }

    // ########################################

    public function getCurrentGmtDate($returnTimestamp = false, $format = NULL)
    {
        if ($returnTimestamp) {
            return (int)Mage::getModel('core/date')->gmtTimestamp();
        }
        return Mage::getModel('core/date')->gmtDate($format);
    }

    public function getCurrentTimezoneDate($returnTimestamp = false, $format = NULL)
    {
        if ($returnTimestamp) {
            return (int)Mage::getModel('core/date')->timestamp();
        }
        return Mage::getModel('core/date')->date($format);
    }

    //-----------------------------------------

    public function getDate($date, $returnTimestamp = false, $format = NULL)
    {
        if (is_numeric($date)) {
            $result = (int)$date;
        } else {
            $result = strtotime($date);
        }

        if (is_null($format)) {
            $format = 'Y-m-d H:i:s';
        }

        $result = date($format, $result);

        if ($returnTimestamp) {
            return strtotime($result);
        }

        return $result;
    }

    //-----------------------------------------

    public function gmtDateToTimezone($dateGmt, $returnTimestamp = false, $format = NULL)
    {
        if ($returnTimestamp) {
            return (int)Mage::getModel('core/date')->timestamp($dateGmt);
        }
        return Mage::getModel('core/date')->date($format,$dateGmt);
    }

    public function timezoneDateToGmt($dateTimezone, $returnTimestamp = false, $format = NULL)
    {
        if ($returnTimestamp) {
            return (int)Mage::getModel('core/date')->gmtTimestamp($dateTimezone);
        }
        return Mage::getModel('core/date')->gmtDate($format,$dateTimezone);
    }

    // ########################################

    public function escapeJs($string)
    {
        return str_replace(array("\\"  , "\n"  , "\r" , "\""  , "'"),
                           array("\\\\", "\\n" , "\\r", "\\\"", "\\'"),
                           $string);
    }

    public function escapeHtml($data, $allowedTags = null, $flags = ENT_COMPAT)
    {
        if (is_array($data)) {
            $result = array();
            foreach ($data as $item) {
                $result[] = $this->escapeHtml($item, $allowedTags, $flags);
            }
        } else {
            // process single item
            if (strlen($data)) {
                if (is_array($allowedTags) and !empty($allowedTags)) {
                    $allowed = implode('|', $allowedTags);
                    $result = preg_replace('/<([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)>/si', '##$1$2$3##', $data);
                    $result = htmlspecialchars($result, $flags);
                    $result = preg_replace('/##([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)##/si', '<$1$2$3>', $result);
                } else {
                    $result = htmlspecialchars($data, $flags);
                }
            } else {
                $result = $data;
            }
        }
        return $result;
    }

    // ########################################

    public function reduceWordsInString($string, $neededLength, $longWord = 6, $minWordLen = 2, $atEndOfWord = '.')
    {
        if (strlen($string) <= $neededLength) {
            return $string;
        }

        $longWords = array();
        foreach (explode(' ', $string) as $word) {
            if (strlen($word) >= $longWord && !preg_match('/[0-9]/', $word)) {
                $longWords[$word] = strlen($word) - $minWordLen;
            }
        }

        $canBeReduced = 0;
        foreach ($longWords as $canBeReducedForWord) {
            $canBeReduced += $canBeReducedForWord;
        }

        $needToBeReduced = strlen($string) - $neededLength + (count($longWords) * strlen($atEndOfWord));

        if ($canBeReduced < $needToBeReduced) {
            return $string;
        }

        $weightOfOneLetter = $needToBeReduced / $canBeReduced;
        foreach($longWords as $word => $canBeReducedForWord) {

            $willReduced = ceil($weightOfOneLetter * $canBeReducedForWord);
            $reducedWord = substr($word, 0, strlen($word) - $willReduced) . $atEndOfWord;

            $string = str_replace($word, $reducedWord, $string);

            if (strlen($string) <= $neededLength) {
                break;
            }
        }

        return $string;
    }

    public function convertStringToSku($title)
    {
        $skuVal = strtolower($title);
        $skuVal = str_replace(array(" ", ":", ",", ".", "?", "*", "+", "(", ")", "&", "%", "$", "#", "@",
                                    "!", '"', "'", ";", "\\", "|", "/", "<", ">"), "-", $skuVal);

        return $skuVal;
    }

    public function stripInvisibleTags($text)
    {
        $text = preg_replace(
            array(
                // Remove invisible content
                '/<head[^>]*?>.*?<\/head>/siu',
                '/<style[^>]*?>.*?<\/style>/siu',
                '/<script[^>]*?.*?<\/script>/siu',
                '/<object[^>]*?.*?<\/object>/siu',
                '/<embed[^>]*?.*?<\/embed>/siu',
                '/<applet[^>]*?.*?<\/applet>/siu',
                '/<noframes[^>]*?.*?<\/noframes>/siu',
                '/<noscript[^>]*?.*?<\/noscript>/siu',
                '/<noembed[^>]*?.*?<\/noembed>/siu',

                // Add line breaks before & after blocks
                '/<((br)|(hr))/iu',
                '/<\/?((address)|(blockquote)|(center)|(del))/iu',
                '/<\/?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))/iu',
                '/<\/?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))/iu',
                '/<\/?((table)|(th)|(td)|(caption))/iu',
                '/<\/?((form)|(button)|(fieldset)|(legend)|(input))/iu',
                '/<\/?((label)|(select)|(optgroup)|(option)|(textarea))/iu',
                '/<\/?((frameset)|(frame)|(iframe))/iu',
            ),
            array(
                ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
                "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
                "\n\$0", "\n\$0",
            ),
            $text);

        return $text;
    }

    public function arrayReplaceRecursive($base, $replacements)
    {
        $args = func_get_args();
        foreach (array_slice($args, 1) as $replacements) {

            $bref_stack = array(&$base);
            $head_stack = array($replacements);

            do {
                end($bref_stack);

                $bref = &$bref_stack[key($bref_stack)];
                $head = array_pop($head_stack);

                unset($bref_stack[key($bref_stack)]);

                foreach (array_keys($head) as $key) {

                    if (isset($key, $bref, $bref[$key]) && is_array($bref[$key]) && is_array($head[$key])) {
                        $bref_stack[] = &$bref[$key];
                        $head_stack[] = $head[$key];
                    } else {
                        $bref[$key] = $head[$key];
                    }

                }
            } while(count($head_stack));
        }

        return $base;
    }

    // ########################################

    public function makeBackUrlParam($backIdOrRoute, array $backParams = array())
    {
        $paramsString = count($backParams) > 0 ? '|'.http_build_query($backParams,'','&') : '';
        return base64_encode($backIdOrRoute.$paramsString);
    }

    public function getBackUrlParam($defaultBackIdOrRoute = 'index',
                                    array $defaultBackParams = array())
    {
        $requestParams = Mage::app()->getRequest()->getParams();
        return isset($requestParams['back'])
            ? $requestParams['back'] : $this->makeBackUrlParam($defaultBackIdOrRoute,$defaultBackParams);
    }

    //------------------------------------------

    public function getBackUrl($defaultBackIdOrRoute = 'index',
                               array $defaultBackParams = array(),
                               array $extendedRoutersParams = array())
    {
        $back = base64_decode($this->getBackUrlParam($defaultBackIdOrRoute,$defaultBackParams));

        $route = '';
        $params = array();

        if (strpos($back,'|') !== false) {
            $route = substr($back,0,strpos($back,'|'));
            parse_str(substr($back,strpos($back,'|')+1),$params);
        } else {
            $route = $back;
        }

        $extendedRoutersParamsTemp = array();
        foreach ($extendedRoutersParams as $extRouteName => $extParams) {
            if ($route == $extRouteName) {
                $params = array_merge($params,$extParams);
            } else {
                $extendedRoutersParamsTemp[$route] = $params;
            }
        }
        $extendedRoutersParams = $extendedRoutersParamsTemp;

        $route == 'index' && $route = '*/*/index';
        $route == 'list' && $route = '*/*/index';
        $route == 'edit' && $route = '*/*/edit';
        $route == 'view' && $route = '*/*/view';

        foreach ($extendedRoutersParams as $extRouteName => $extParams) {
            if ($route == $extRouteName) {
                $params = array_merge($params,$extParams);
            }
        }

        return Mage::helper('adminhtml')->getUrl($route,$params);
    }

    // ########################################

    public function getClassConstantAsJson($class)
    {
        if (stripos($class,'Ess_M2ePro_') === false) {
            throw new Exception('Class name must begin with "Ess_M2ePro"');
        }

        $reflectionClass = new ReflectionClass($class);
        $tempConstants = $reflectionClass->getConstants();

        $constants = array();
        foreach ($tempConstants as $key => $value) {
            $constants[] = array(strtoupper($key), $value);
        }

        return json_encode($constants);
    }

    public function getControllerActions($controllerClass, array $params = array())
    {
        $controllerClass = Mage::helper('M2ePro/View_Development_Controller')->loadControllerAndGetClassName(
            $controllerClass
        );

        $route = str_replace('Ess_M2ePro_','',$controllerClass);
        $route = preg_replace('/Controller$/','',$route);
        $route = explode('_',$route);

        foreach ($route as &$part) {
            $part{0} = strtolower($part{0});
        }
        unset($part);

        $route = implode('_',$route) . '/';

        $reflectionClass = new ReflectionClass($controllerClass);

        $actions = array();
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {

            if (!preg_match('/Action$/',$method->name)) {
                continue;
            }

            $methodName = preg_replace('/Action$/','',$method->name);

            $actions[$route . $methodName] = Mage::helper('adminhtml')->getUrl('M2ePro/'.$route.$methodName, $params);
        }

        return $actions;
    }

    // ########################################

    public function generateUniqueHash($strParam = NULL, $maxLength = NULL)
    {
        $hash = sha1(rand(1,1000000).microtime(true).(string)$strParam);
        (int)$maxLength > 0 && $hash = substr($hash,0,(int)$maxLength);
        return $hash;
    }

    public function theSameItemsInData($data, $keysToCheck)
    {
        if (count($data) > 200) {
            return false;
        }

        $preparedData = array();

        foreach ($keysToCheck as $key) {
            $preparedData[$key] = array();
        }

        foreach ($data as $item) {
            foreach ($keysToCheck as $key) {
                $preparedData[$key][] = $item[$key];
            }
        }

        foreach ($keysToCheck as $key) {
            $preparedData[$key] = array_unique($preparedData[$key]);
            if (count($preparedData[$key]) > 1) {
                return false;
            }
        }

        return true;
    }

    public function getMainStatus($statuses)
    {
        foreach (array(self::STATUS_ERROR, self::STATUS_WARNING) as $status) {
            if (in_array($status, $statuses)) {
                return $status;
            }
        }

        return self::STATUS_SUCCESS;
    }

    // ########################################

    public function isISBN($string)
    {
        return $this->isISBN10($string) || $this->isISBN13($string);
    }

    //-----------------------------------------

    public function isISBN10($string)
    {
        if (strlen($string) != 10) {
            return false;
        }

        $a = 0;
        for ($i = 0; $i < 10; $i++) {
            if ($string[$i] == "X" || $string[$i] == "x") {
                $a += 10 * intval(10 - $i);
            } else if (is_numeric($string[$i])) {
                $a += intval($string[$i]) * intval(10 - $i);
            } else {
                return false;
            }
        }
        return ($a % 11 == 0);
    }

    public function isISBN13($string)
    {
        if (strlen($string) != 13) {
            return false;
        }

        if (substr($string,0,3) != '978') {
            return false;
        }

        $check = 0;
        for ($i = 0; $i < 13; $i += 2) $check += (int)substr($string, $i, 1);
        for ($i = 1; $i < 12; $i += 2) $check += 3 * substr($string, $i, 1);

        return $check % 10 == 0;
    }

    // ########################################

    public function isUPC($upc)
    {
        return $this->isWorldWideId($upc,'UPC');
    }

    public function isEAN($ean)
    {
        return $this->isWorldWideId($ean,'EAN');
    }

    //-----------------------------------------

    private function isWorldWideId($worldWideId,$type)
    {
        $adapters = array(
            'UPC' => array(
                '12' => 'Upca'
            ),
            'EAN' => array(
                '13' => 'Ean13'
            )
        );

        $length = strlen($worldWideId);

        if (!isset($adapters[$type],$adapters[$type][$length])) {
            return false;
        }

        try {
            $validator = new Zend_Validate_Barcode($adapters[$type][$length]);
            $result = $validator->isValid($worldWideId);
        } catch (Zend_Validate_Exception $e) {
            return false;
        }

        return $result;
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Helper_Module extends Mage_Core_Helper_Abstract
{
    const SERVER_LOCK_NO = 0;
    const SERVER_LOCK_YES = 1;

    const SERVER_MESSAGE_TYPE_NOTICE = 0;
    const SERVER_MESSAGE_TYPE_ERROR = 1;
    const SERVER_MESSAGE_TYPE_WARNING = 2;
    const SERVER_MESSAGE_TYPE_SUCCESS = 3;

    const WIZARD_MIGRATION_NICK = 'migrationToV6';

    const ENVIRONMENT_PRODUCTION = 'production';
    const ENVIRONMENT_DEVELOPMENT = 'development';
    const ENVIRONMENT_TESTING = 'testing';

    const DEVELOPMENT_MODE_COOKIE_KEY = 'm2epro_development_mode';

    // ########################################

    /**
     * @return Ess_M2ePro_Model_Config_Module
     */
    public function getConfig()
    {
        return Mage::getSingleton('M2ePro/Config_Module');
    }

    /**
     * @return Ess_M2ePro_Model_Config_Cache
     */
    public function getCacheConfig()
    {
        return Mage::getSingleton('M2ePro/Config_Cache');
    }

    /**
     * @return Ess_M2ePro_Model_Config_Synchronization
     */
    public function getSynchronizationConfig()
    {
        return Mage::getSingleton('M2ePro/Config_Synchronization');
    }

    // ########################################

    public function getName()
    {
        return 'm2epro';
    }

    public function getVersion()
    {
        $version = (string)Mage::getConfig()->getNode('modules/Ess_M2ePro/version');
        $version = strtolower($version);

        if (Mage::helper('M2ePro/Data_Cache_Permanent')->getValue('MODULE_VERSION_UPDATER') === false) {
            Mage::helper('M2ePro/Primary')->getConfig()->setGroupValue(
                '/modules/',$this->getName(),$version.'.r'.$this->getRevision()
            );
            Mage::helper('M2ePro/Data_Cache_Permanent')->setValue('MODULE_VERSION_UPDATER',array(),array(),60*60*24);
        }

        return $version;
    }

    public function getRevision()
    {
        $revision = '8915';

        if ($revision == str_replace('|','#','|REVISION|')) {
            $revision = (int)exec('svnversion');
            $revision == 0 && $revision = 'N/A';
            $revision .= '-dev';
        }

        return $revision;
    }

    //----------------------------------------

    public function getVersionWithRevision()
    {
        return $this->getVersion().'r'.$this->getRevision();
    }

    // ########################################

    public function getInstallationKey()
    {
        return Mage::helper('M2ePro/Primary')->getConfig()->getGroupValue(
            '/'.$this->getName().'/server/', 'installation_key'
        );
    }

    public function isMigrationWizardFinished()
    {
        return Mage::helper('M2ePro/Module_Wizard')->isFinished(
            self::WIZARD_MIGRATION_NICK
        );
    }

    // ########################################

    public function isLockedByServer()
    {
        $lock = (int)Mage::helper('M2ePro/Primary')->getConfig()->getGroupValue(
            '/'.$this->getName().'/server/', 'lock'
        );

        $validValues = array(self::SERVER_LOCK_NO, self::SERVER_LOCK_YES);

        if (in_array($lock,$validValues)) {
            return $lock;
        }

        return self::SERVER_LOCK_NO;
    }

    // -------------------------------------------

    public function getServerMessages()
    {
        $messages = Mage::helper('M2ePro/Primary')->getConfig()->getGroupValue(
            '/'.$this->getName().'/server/', 'messages'
        );

        $messages = (!is_null($messages) && $messages != '') ?
                    (array)json_decode((string)$messages,true) :
                    array();

        $messages = array_filter($messages,array($this,'getServerMessagesFilterModuleMessages'));
        !is_array($messages) && $messages = array();

        return $messages;
    }

    public function getServerMessagesFilterModuleMessages($message)
    {
        if (!isset($message['text']) || !isset($message['type'])) {
            return false;
        }

        return true;
    }

    // ########################################

    public function getFoldersAndFiles()
    {
        $paths = array(
            'app/code/community/Ess/',
            'app/code/community/Ess/M2ePro/*',

            'app/locale/*/Ess_M2ePro.csv',
            'app/etc/modules/Ess_M2ePro.xml',
            'app/design/adminhtml/default/default/layout/M2ePro.xml',

            'js/M2ePro/*',
            'skin/adminhtml/default/default/M2ePro/*',
            'skin/adminhtml/default/enterprise/M2ePro/*',
            'app/design/adminhtml/default/default/template/M2ePro/*'
        );

        return $paths;
    }

    public function getRequirementsInfo()
    {
        $clientPhpData = Mage::helper('M2ePro/Client')->getPhpSettings();

        $requirements = array (

            'php_version' => array(
                'title' => Mage::helper('M2ePro')->__('PHP Version'),
                'condition' => array(
                    'sign' => '>=',
                    'value' => '5.3.0'
                ),
                'current' => array(
                    'value' => Mage::helper('M2ePro/Client')->getPhpVersion(),
                    'status' => true
                )
            ),

            'memory_limit' => array(
                'title' => Mage::helper('M2ePro')->__('Memory Limit'),
                'condition' => array(
                    'sign' => '>=',
                    'value' => '256 MB'
                ),
                'current' => array(
                    'value' => (int)$clientPhpData['memory_limit'] . ' MB',
                    'status' => true
                )
            ),

            'magento_version' => array(
                'title' => Mage::helper('M2ePro')->__('Magento Version'),
                'condition' => array(
                    'sign' => '>=',
                    'value' => (Mage::helper('M2ePro/Magento')->isGoEdition()           ? '1.9.0.0' :
                               (Mage::helper('M2ePro/Magento')->isEnterpriseEdition()   ? '1.7.0.0' :
                               (Mage::helper('M2ePro/Magento')->isProfessionalEdition() ? '1.7.0.0' : '1.4.1.0')))
                ),
                'current' => array(
                    'value' => Mage::helper('M2ePro/Magento')->getVersion(false),
                    'status' => true
                )
            ),

            'max_execution_time' => array(
                'title' => Mage::helper('M2ePro')->__('Max Execution Time'),
                'condition' => array(
                    'sign' => '>=',
                    'value' => '360 sec'
                ),
                'current' => array(
                    'value' => is_null($clientPhpData['max_execution_time'])
                               ? 'unknown' : $clientPhpData['max_execution_time'] . ' sec',
                    'status' => true
                )
            )
        );

        foreach ($requirements as $key => &$requirement) {

            // max execution time is unlimited or fcgi handler
            if ($key == 'max_execution_time' &&
                ($clientPhpData['max_execution_time'] == 0 || is_null($clientPhpData['max_execution_time']))) {
                continue;
            }

            $requirement['current']['status'] = version_compare(
                $requirement['current']['value'],
                $requirement['condition']['value'],
                $requirement['condition']['sign']
            );
        }

        return $requirements;
    }

    // ########################################

    public function getUnWritableDirectories()
    {
        $directoriesForCheck = array();
        foreach ($this->getFoldersAndFiles() as $item) {

            $fullDirPath = Mage::getBaseDir().DS.$item;

            if (preg_match('/\*.*$/',$item)) {
                $fullDirPath = preg_replace('/\*.*$/', '', $fullDirPath);
                $directoriesForCheck = array_merge($directoriesForCheck, $this->getDirectories($fullDirPath));
            }

            $directoriesForCheck[] = dirname($fullDirPath);
            is_dir($fullDirPath) && $directoriesForCheck[] = rtrim($fullDirPath, '/\\');
        }
        $directoriesForCheck = array_unique($directoriesForCheck);

        $unWritableDirs = array();
        foreach ($directoriesForCheck as $directory) {
            !is_dir_writeable($directory) && $unWritableDirs[] = $directory;
        }

        return $unWritableDirs;
    }

    private function getDirectories($dirPath)
    {
        $directoryIterator = new RecursiveDirectoryIterator($dirPath, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::SELF_FIRST);

        $directories = array();
        foreach($iterator as $path) {
            $path->isDir() && $directories[] = rtrim($path->getPathname(),'/\\');
        }

        return $directories;
    }

    // ########################################

    public function isDevelopmentMode()
    {
        return Mage::app()->getCookie()->get(self::DEVELOPMENT_MODE_COOKIE_KEY);
    }

    public function isProductionMode()
    {
        return !$this->isDevelopmentMode();
    }

    public function setDevelopmentModeMode($value)
    {
        $value ? Mage::app()->getCookie()->set(self::DEVELOPMENT_MODE_COOKIE_KEY, 'true', 60*60*24*31*12)
               : Mage::app()->getCookie()->delete(self::DEVELOPMENT_MODE_COOKIE_KEY);
    }

    // ----------------------------------------

    public function isProductionEnvironment()
    {
        return (string)getenv('M2EPRO_ENV') == self::ENVIRONMENT_PRODUCTION ||
               (!$this->isDevelopmentEnvironment() && !$this->isTestingEnvironment());
    }

    public function isDevelopmentEnvironment()
    {
        return (string)getenv('M2EPRO_ENV') == self::ENVIRONMENT_DEVELOPMENT;
    }

    public function isTestingEnvironment()
    {
        return (string)getenv('M2EPRO_ENV') == self::ENVIRONMENT_TESTING;
    }

    // ########################################

    public function clearConfigCache()
    {
        $this->getCacheConfig()->clear();
    }

    public function clearCache()
    {
        Mage::helper('M2ePro/Data_Cache_Permanent')->removeAllValues();
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Abstract extends Mage_Core_Model_Abstract
{
    const SETTING_FIELD_TYPE_JSON          = 'json';
    const SETTING_FIELD_TYPE_SERIALIZATION = 'serialization';

    // ########################################

    /**
     * @param int $id
     * @param null|string $field
     * @return Ess_M2ePro_Model_Abstract
     * @throws LogicException
     */
    public function loadInstance($id, $field = NULL)
    {
        $this->load($id,$field);

        if (is_null($this->getId())) {
            throw new LogicException('Instance does not exist.');
        }

        return $this;
    }

    /**
     * @return bool
     * @throws LogicException
     */
    public function isLocked()
    {
        if (is_null($this->getId())) {
            throw new LogicException('Method require loaded instance first');
        }

        if ($this->isLockedObject(NULL)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     * @throws LogicException
     */
    public function deleteInstance()
    {
        if (is_null($this->getId())) {
            throw new LogicException('Method require loaded instance first');
        }

        if ($this->isLocked()) {
            return false;
        }

        $this->delete();
        return true;
    }

    // ########################################

    public function delete()
    {
        if (is_null($this->getId())) {
            throw new LogicException('Method require loaded instance first');
        }

        $this->deleteObjectLocks();
        return parent::delete();
    }

    public function deleteProcessingRequests()
    {
        $processingRequestsHashes = array();
        foreach ($this->getObjectLocks() as $lockedObject) {
            $processingRequestsHashes[] = $lockedObject->getRelatedHash();
        }

        /** @var $collection Mage_Core_Model_Mysql4_Collection_Abstract */
        $collection = Mage::getModel('M2ePro/Processing_Request')->getCollection();
        $collection->addFieldToFilter('hash', array('in'=>array_unique($processingRequestsHashes)));

        foreach ($collection->getItems() as $processingRequest) {
            /** @var $processingRequest Ess_M2ePro_Model_Processing_Request */
            //->__('Request was deleted during object deleting.')
            $processingRequest->getResponserRunner()->complete('Request was deleted during object deleting.');
        }
    }

    // ########################################

    public function addObjectLock($tag = NULL, $relatedHash = NULL, $description = NULL)
    {
        if (is_null($this->getId())) {
            throw new LogicException('Method require loaded instance first');
        }

        if ($this->isLockedObject($tag,$relatedHash)) {
            return;
        }

        $model = Mage::getModel('M2ePro/LockedObject');

        $dataForAdd = array(
            'model_name' => $this->_resourceName,
            'object_id' => $this->getId(),
            'related_hash' => $relatedHash,
            'tag' => $tag,
            'description' => $description
        );

        $model->setData($dataForAdd)->save();
    }

    public function deleteObjectLocks($tag = false, $relatedHash = false)
    {
        if (is_null($this->getId())) {
            throw new LogicException('Method require loaded instance first');
        }

        $lockedObjects = $this->getObjectLocks($tag,$relatedHash);
        foreach ($lockedObjects as $lockedObject) {
            /** @var $lockedObject Ess_M2ePro_Model_LockedObject */
            $lockedObject->deleteInstance();
        }
    }

    //-----------------------------------------

    public function isLockedObject($tag = false, $relatedHash = false)
    {
        if (is_null($this->getId())) {
            throw new LogicException('Method require loaded instance first');
        }

        return count($this->getObjectLocks($tag,$relatedHash)) > 0;
    }

    public function getObjectLocks($tag = false, $relatedHash = false)
    {
        if (is_null($this->getId())) {
            throw new LogicException('Method require loaded instance first');
        }

        $lockedCollection = Mage::getModel('M2ePro/LockedObject')->getCollection();

        $lockedCollection->addFieldToFilter('model_name',$this->_resourceName);
        $lockedCollection->addFieldToFilter('object_id',$this->getId());

        is_null($tag) && $tag = array('null'=>true);
        $tag !== false && $lockedCollection->addFieldToFilter('tag',$tag);
        $relatedHash !== false && $lockedCollection->addFieldToFilter('related_hash',$relatedHash);

        return $lockedCollection->getItems();
    }

    // ########################################

    /**
     * @param string $modelName
     * @param string $fieldName
     * @param bool $asObjects
     * @param array $filters
     * @param array $sort
     * @return array|Ess_M2ePro_Model_Abstract[]
     * @throws LogicException
     */
    protected function getRelatedSimpleItems($modelName, $fieldName, $asObjects = false,
                                             array $filters = array(), array $sort = array())
    {
        if (is_null($this->getId())) {
            throw new LogicException('Method require loaded instance first');
        }

        $tempModel = Mage::getModel('M2ePro/'.$modelName);

        if (is_null($tempModel) || !($tempModel instanceof Ess_M2ePro_Model_Abstract)) {
            return array();
        }

        return $this->getRelatedItems($tempModel,$fieldName,$asObjects,$filters,$sort);
    }

    /**
     * @param Ess_M2ePro_Model_Abstract $model
     * @param string $fieldName
     * @param bool $asObjects
     * @param array $filters
     * @param array $sort
     * @return array|Ess_M2ePro_Model_Abstract[]
     * @throws LogicException
     */
    protected function getRelatedItems(Ess_M2ePro_Model_Abstract $model, $fieldName, $asObjects = false,
                                       array $filters = array(), array $sort = array())
    {
        if (is_null($this->getId())) {
            throw new LogicException('Method require loaded instance first');
        }

        /** @var $tempCollection Mage_Core_Model_Mysql4_Collection_Abstract */
        $tempCollection = $model->getCollection();
        $tempCollection->addFieldToFilter($fieldName, $this->getId());

        foreach ($filters as $field=>$filter) {

            if ($filter instanceof Zend_Db_Expr) {
                $tempCollection->getSelect()->where((string)$filter);
                continue;
            }

            $tempCollection->addFieldToFilter('`'.$field.'`', $filter);
        }

        foreach ($sort as $field => $order) {
            $order = strtoupper(trim($order));
            if ($order != Varien_Data_Collection::SORT_ORDER_ASC &&
                $order != Varien_Data_Collection::SORT_ORDER_DESC) {
                continue;
            }
            $tempCollection->setOrder($field,$order);
        }

        if ((bool)$asObjects) {
            return $tempCollection->getItems();
        }

        $tempArray = $tempCollection->toArray();
        return $tempArray['items'];
    }

    // ########################################

    /**
     * @param string $fieldName
     * @param string $encodeType
     *
     * @return array
     *
     * @throws LogicException
     */
    public function getSettings($fieldName, $encodeType = self::SETTING_FIELD_TYPE_JSON)
    {
        $settings = $this->getData((string)$fieldName);

        if (is_null($settings)) {
            return array();
        }

        if ($encodeType == self::SETTING_FIELD_TYPE_JSON) {
            $settings = @json_decode($settings, true);
        } else if ($encodeType == self::SETTING_FIELD_TYPE_SERIALIZATION) {
            $settings = @unserialize($settings);
        } else {
            throw new LogicException(Mage::helper('M2ePro')->__(
                'Encoding type "%encode_type%" is not supported.',
                $encodeType
                ));
        }

        return is_array($settings) ? $settings : array();
    }

    /**
     * @param string       $fieldName
     * @param string|array $settingNamePath
     * @param mixed        $defaultValue
     * @param string       $encodeType
     *
     * @return mixed|null
     */
    public function getSetting($fieldName,
                               $settingNamePath,
                               $defaultValue = NULL,
                               $encodeType = self::SETTING_FIELD_TYPE_JSON)
    {
        if (empty($settingNamePath)) {
            return $defaultValue;
        }

        $settings = $this->getSettings($fieldName, $encodeType);

        !is_array($settingNamePath) && $settingNamePath = array($settingNamePath);

        foreach ($settingNamePath as $pathPart) {
            if (!isset($settings[$pathPart])) {
                return $defaultValue;
            }

            $settings = $settings[$pathPart];
        }

        if (is_numeric($settings)) {
            $settings = ctype_digit($settings) ? (int)$settings : $settings;
        }

        return $settings;
    }

    //----------------------------------------

    /**
     * @param string $fieldName
     * @param array  $settings
     * @param string $encodeType
     *
     * @return Ess_M2ePro_Model_Abstract
     *
     * @throws LogicException
     */
    public function setSettings($fieldName, array $settings = array(), $encodeType = self::SETTING_FIELD_TYPE_JSON)
    {
        if ($encodeType == self::SETTING_FIELD_TYPE_JSON) {
            $settings = json_encode($settings);
        } else if ($encodeType == self::SETTING_FIELD_TYPE_SERIALIZATION) {
            $settings = serialize($settings);
        } else {
            throw new LogicException(Mage::helper('M2ePro')->__(
                'Encoding type "%encode_type%" is not supported.',
                $encodeType
            ));
        }

        $this->setData((string)$fieldName, $settings);

        return $this;
    }

    /**
     * @param string       $fieldName
     * @param string|array $settingNamePath
     * @param mixed        $settingValue
     * @param string       $encodeType
     *
     * @return Ess_M2ePro_Model_Abstract
     */
    public function setSetting($fieldName,
                               $settingNamePath,
                               $settingValue,
                               $encodeType = self::SETTING_FIELD_TYPE_JSON)
    {
        if (empty($settingNamePath)) {
            return $this;
        }

        $settings = $this->getSettings($fieldName, $encodeType);
        $target = &$settings;

        !is_array($settingNamePath) && $settingNamePath = array($settingNamePath);

        $currentPathNumber = 0;
        $totalPartsNumber = count($settingNamePath);

        foreach ($settingNamePath as $pathPart) {
            $currentPathNumber++;

            if (!array_key_exists($pathPart, $settings) && $currentPathNumber != $totalPartsNumber) {
                $target[$pathPart] = array();
            }

            if ($currentPathNumber != $totalPartsNumber) {
                $target = &$target[$pathPart];
                continue;
            }

            $target[$pathPart] = $settingValue;
        }

        $this->setSettings($fieldName, $settings, $encodeType);

        return $this;
    }

    // ########################################

    public function getDataSnapshot()
    {
        $data = $this->getData();

        foreach ($data as &$value) {
            !is_null($value) && !is_array($value) && $value = (string)$value;
        }

        return $data;
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_Config_Abstract extends Ess_M2ePro_Model_Abstract
{
    const SORT_NONE = 0;
    const SORT_KEY_ASC = 1;
    const SORT_KEY_DESC = 2;
    const SORT_VALUE_ASC = 3;
    const SORT_VALUE_DESC = 4;

    // ########################################

    private $_ormConfig = '';
    private $_cacheData = array();

    // ########################################

    public function __construct()
    {
        $args = func_get_args();
        empty($args[0]) && $args[0] = array();
        $params = $args[0];

        if (empty($params['orm'])) {
            throw new Exception('ORM for config is not defined.');
        }

        $this->_ormConfig = $params['orm'];
        parent::__construct();
    }

    // ########################################

    public function getGlobalValue($key)
    {
        return $this->getValue(NULL, $key);
    }

    public function setGlobalValue($key, $value)
    {
        return $this->setValue(NULL, $key, $value);
    }

    public function deleteGlobalValue($key)
    {
        return $this->deleteValue(NULL, $key);
    }

    //----------------------------------------

    public function getAllGlobalValues($sort = self::SORT_NONE)
    {
        return $this->getAllValues(NULL,$sort);
    }

    public function deleteAllGlobalValues()
    {
        return $this->deleteAllValues(NULL);
    }

    // ########################################

    public function getGroupValue($group, $key)
    {
        $group = $this->prepareGroup($group);
        return $this->getValue($group, $key);
    }

    public function setGroupValue($group, $key, $value)
    {
        $group = $this->prepareGroup($group);
        return $this->setValue($group, $key, $value);
    }

    public function deleteGroupValue($group, $key)
    {
        $group = $this->prepareGroup($group);
        return $this->deleteValue($group, $key);
    }

    //----------------------------------------

    public function getAllGroupValues($group, $sort = self::SORT_NONE)
    {
        $group = $this->prepareGroup($group);
        return $this->getAllValues($group,$sort);
    }

    public function deleteAllGroupValues($group)
    {
        $group = $this->prepareGroup($group);
        return $this->deleteAllValues($group);
    }

    //----------------------------------------

    public function clear()
    {
        $tableName = $this->getResource()->getMainTable();
        Mage::getSingleton('core/resource')->getConnection('core_write')->delete($tableName);

        $this->_cacheData = array();
        $this->updatePermanentCacheData();
    }

    // ########################################

    private function getValue($group, $key)
    {
        $this->loadCacheData();

        if (!is_null($group) && empty($group)) {
            return NULL;
        }

        if (empty($key)) {
            return NULL;
        }

        return $this->getCacheValue($group, $key);
    }

    private function setValue($group, $key, $value)
    {
        $this->loadCacheData();

        if (!is_null($group) && empty($group)) {
            return false;
        }

        if (empty($key)) {
            return false;
        }

        $temp = $this->getCollection();

        if (is_null($group)) {
            $temp->addFieldToFilter('`group`', array('null' => true));
        } else {
            $temp->addFieldToFilter('`group`', $group);
        }

        $temp->addFieldToFilter('`key`', $key);
        $temp = $temp->toArray();

        if (count($temp['items']) > 0) {

            $existItem = $temp['items'][0];

            Mage::getModel($this->_ormConfig)
                         ->load($existItem['id'])
                         ->addData(array('value'=>$value))
                         ->save();
        } else {

            Mage::getModel($this->_ormConfig)
                         ->setData(array('group'=>$group,'key'=>$key,'value'=>$value))
                         ->save();
        }

        return $this->setCacheValue($group,$key,$value);
    }

    private function deleteValue($group, $key)
    {
        $this->loadCacheData();

        if (!is_null($group) && empty($group)) {
            return false;
        }

        if (empty($key)) {
            return false;
        }

        $temp = $this->getCollection();

        if (is_null($group)) {
            $temp->addFieldToFilter('`group`', array('null' => true));
        } else {
            $temp->addFieldToFilter('`group`', $group);
        }

        $temp->addFieldToFilter('`key`', $key);
        $temp = $temp->toArray();

        if (count($temp['items']) <= 0) {
            return false;
        }

        $existItem = $temp['items'][0];
        Mage::getModel($this->_ormConfig)->setId($existItem['id'])->delete();

        return $this->deleteCacheValue($existItem['group'], $existItem['key']);
    }

    // ----------------------------------------

    private function getAllValues($group = NULL, $sort = self::SORT_NONE)
    {
        $this->loadCacheData();

        if (!is_null($group) && empty($group)) {
            return array();
        }

        $result = array();

        $temp = $this->getCollection();

        if (is_null($group)) {
            $temp->addFieldToFilter('`group`', array('null' => true));
        } else {
            $temp->addFieldToFilter('`group`', $group);
        }

        $temp = $temp->toArray();

        foreach ($temp['items'] as $item) {
            $result[$item['key']] = $item['value'];
        }

        $this->sortResult($result,$sort);

        return $result;
    }

    private function deleteAllValues($group = NULL)
    {
        $this->loadCacheData();

        if (!is_null($group) && empty($group)) {
            return false;
        }

        $temp = $this->getCollection();

        if (is_null($group)) {
            $temp->addFieldToFilter('`group`', array('null' => true));
        } else {
            $temp->addFieldToFilter('`group`', array("like"=>$group.'%'));
        }

        $temp = $temp->toArray();

        foreach ($temp['items'] as $item) {
            Mage::getModel($this->_ormConfig)->setId($item['id'])->delete();
            $this->deleteCacheValue($item['group'], $item['key']);
        }

        return true;
    }

    // ########################################

    private function prepareGroup($group = NULL)
    {
        if (is_null($group)) {
            return NULL;
        }

        if (empty($group)) {
            return false;
        }

        return '/'.trim($group,'/').'/';
    }

    private function sortResult(&$array, $sort)
    {
        switch ($sort)
        {
            case self::SORT_KEY_ASC:
                ksort($array);
                break;

            case self::SORT_KEY_DESC:
                krsort($array);
                break;

            case self::SORT_VALUE_ASC:
                asort($array);
                break;

            case self::SORT_VALUE_DESC:
                arsort($array);
                break;
        }
    }

    //-----------------------------------------

    private function getCacheValue($group = NULL, $key)
    {
        empty($group) && $group = 'global';

        if (empty($key)) {
            return NULL;
        }

        $group = strtolower($group);
        $key = strtolower($key);

        if (isset($this->_cacheData[$group][$key])) {
            return $this->_cacheData[$group][$key];
        }

        return NULL;
    }

    private function setCacheValue($group = NULL, $key, $value)
    {
        empty($group) && $group = 'global';

        if (empty($key)) {
            return false;
        }

        $group = strtolower($group);
        $key = strtolower($key);

        if (!isset($this->_cacheData[$group])) {
            $this->_cacheData[$group] = array();
        }

        $this->_cacheData[$group][$key] = $value;
        $this->updatePermanentCacheData();

        return true;
    }

    private function deleteCacheValue($group = NULL, $key)
    {
        empty($group) && $group = 'global';

        if (empty($key)) {
            return false;
        }

        $group = strtolower($group);
        $key = strtolower($key);

        unset($this->_cacheData[$group][$key]);
        $this->updatePermanentCacheData();

        return true;
    }

    //-----------------------------------------

    private function loadCacheData()
    {
        if (!empty($this->_cacheData)) {
            return;
        }

        $key = $this->_ormConfig.'_data';
        $this->_cacheData = Mage::helper('M2ePro/Data_Cache_Permanent')->getValue($key);

        if ($this->_cacheData === false || Mage::helper('M2ePro/Module')->isDevelopmentEnvironment()) {
            $this->_cacheData = $this->buildCacheData();
            $this->updatePermanentCacheData();
        }
    }

    private function buildCacheData()
    {
        $tempData = $this->getCollection()->toArray();

        $newCache = array();
        foreach ($tempData['items'] as $item) {

            if (empty($item['group'])) {
                $item['group'] = 'global';
            }

            $item['group'] = strtolower($item['group']);
            $item['key'] = strtolower($item['key']);

            if (!isset($newCache[$item['group']])) {
                $newCache[$item['group']] = array();
            }

            $newCache[$item['group']][$item['key']] = $item['value'];
        }

        return $newCache;
    }

    private function updatePermanentCacheData()
    {
        $key = $this->_ormConfig.'_data';
        Mage::helper('M2ePro/Data_Cache_Permanent')->setValue($key,$this->_cacheData,array(),60*60);
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_Config_Module extends Ess_M2ePro_Model_Config_Abstract
{
    // ########################################

    public function __construct()
    {
        $args = func_get_args();
        empty($args[0]) && $args[0] = array();
        $params = $args[0];

        $params['orm'] = 'M2ePro/Config_Module';

        parent::__construct($params);
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/Config_Module');
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2014 by  ESS-UA.
 */

abstract class Ess_M2ePro_Helper_Data_Cache_Abstract extends Mage_Core_Helper_Abstract
{
    // ##########################################################

    abstract public function getValue($key);

    abstract public function setValue($key, $value, array $tags = array(), $lifetime = null);

    // ##########################################################

    abstract public function removeValue($key);

    abstract public function removeTagValues($tag);

    abstract public function removeAllValues();

    // ##########################################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Helper_Data_Cache_Permanent extends Ess_M2ePro_Helper_Data_Cache_Abstract
{
    // ########################################

    public function getValue($key)
    {
        $cacheKey = Ess_M2ePro_Helper_Data::CUSTOM_IDENTIFIER.'_'.$key;
        $value = Mage::app()->getCache()->load($cacheKey);
        $value !== false && $value = unserialize($value);
        return $value;
    }

    public function setValue($key, $value, array $tags = array(), $lifeTime = NULL)
    {
        if (is_null($lifeTime) || (int)$lifeTime <= 0) {
            $lifeTime = 60*60*24*365*5;
        }

        $cacheKey = Ess_M2ePro_Helper_Data::CUSTOM_IDENTIFIER.'_'.$key;

        $preparedTags = array(Ess_M2ePro_Helper_Data::CUSTOM_IDENTIFIER.'_main');
        foreach ($tags as $tag) {
            $preparedTags[] = Ess_M2ePro_Helper_Data::CUSTOM_IDENTIFIER.'_'.$tag;
        }

        Mage::app()->getCache()->save(serialize($value), $cacheKey, $preparedTags, (int)$lifeTime);
    }

    // ########################################

    public function removeValue($key)
    {
        $cacheKey = Ess_M2ePro_Helper_Data::CUSTOM_IDENTIFIER.'_'.$key;
        Mage::app()->getCache()->remove($cacheKey);
    }

    public function removeTagValues($tag)
    {
        $mode = Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG;
        $tags = array(Ess_M2ePro_Helper_Data::CUSTOM_IDENTIFIER.'_'.$tag);
        Mage::app()->getCache()->clean($mode,$tags);
    }

    public function removeAllValues()
    {
        $this->removeTagValues('main');
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Helper_Client extends Mage_Core_Helper_Abstract
{
    const API_APACHE_HANDLER = 'apache2handler';

    // ########################################

    public function getHost()
    {
        $domain = $this->getDomain();
        return $domain == '' ? $this->getIp() : $domain;
    }

    //-----------------------------------------

    public function getDomain()
    {
        $domain = Mage::helper('M2ePro/Module')->getCacheConfig()->getGroupValue('/location_info/', 'domain');
        if (is_null($domain) && isset($_SERVER['HTTP_HOST'])) {
            $domain = $_SERVER['HTTP_HOST'];
        }

        if (!is_null($domain)) {
            strpos($domain,'www.') === 0 && $domain = substr($domain,4);
            return strtolower(trim($domain));
        }

        throw new Exception('Server domain is not defined');
    }

    public function getIp()
    {
        $backupIp = Mage::helper('M2ePro/Module')->getCacheConfig()->getGroupValue('/location_info/', 'ip');

        if (!is_null($backupIp)) {
            return strtolower(trim($backupIp));
        }

        $serverIp = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : NULL;
        is_null($serverIp) && $serverIp = isset($_SERVER['LOCAL_ADDR']) ? $_SERVER['LOCAL_ADDR'] : NULL;

        if (!is_null($serverIp)) {
            return strtolower(trim($serverIp));
        }

        throw new Exception('Server IP is not defined');
    }

    public function getBaseDirectory()
    {
        $backupDirectory = Mage::helper('M2ePro/Module')->getCacheConfig()
                                    ->getGroupValue('/location_info/', 'directory');

        if (!is_null($backupDirectory)) {
            return $backupDirectory;
        }

        return Mage::getBaseDir();
    }

    public function isBrowserIE()
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
            return true;
        }
        return false;
    }

    //-----------------------------------------

    public function updateBackupConnectionData($forceUpdate = false)
    {
        $dateLastCheck = Mage::helper('M2ePro/Module')->getCacheConfig()
                                ->getGroupValue('/location_info/', 'date_last_check');

        if (is_null($dateLastCheck)) {
            $dateLastCheck = Mage::helper('M2ePro')->getCurrentGmtDate(true)-60*60*365;
        } else {
            $dateLastCheck = strtotime($dateLastCheck);
        }

        if (!$forceUpdate && Mage::helper('M2ePro')->getCurrentGmtDate(true) < $dateLastCheck + 60*60*24) {
            return;
        }

        $domainBackup = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '127.0.0.1';
        strpos($domainBackup,'www.') === 0 && $domainBackup = substr($domainBackup,4);
        Mage::helper('M2ePro/Module')->getCacheConfig()
            ->setGroupValue('/location_info/', 'domain', $domainBackup);

        $ipBackup = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : NULL;
        is_null($ipBackup) && $ipBackup = isset($_SERVER['LOCAL_ADDR']) ? $_SERVER['LOCAL_ADDR'] : '127.0.0.1';
        Mage::helper('M2ePro/Module')->getCacheConfig()
            ->setGroupValue('/location_info/', 'ip', $ipBackup);

        $directoryBackup = Mage::getBaseDir();
        Mage::helper('M2ePro/Module')->getCacheConfig()
            ->setGroupValue('/location_info/', 'directory', $directoryBackup);

        Mage::helper('M2ePro/Module')->getCacheConfig()->setGroupValue(
            '/location_info/', 'date_last_check', Mage::helper('M2ePro')->getCurrentGmtDate()
        );
    }

    // ########################################

    public function getSystem()
    {
        return php_uname();
    }

    // ----------------------------------------

    public function getPhpVersion()
    {
        return @phpversion();
    }

    public function getPhpApiName()
    {
        return @php_sapi_name();
    }

    // ----------------------------------------

    public function isPhpApiApacheHandler()
    {
        return $this->getPhpApiName() == self::API_APACHE_HANDLER;
    }

    public function isPhpApiFastCgi()
    {
        return !$this->isPhpApiApacheHandler();
    }

    // ----------------------------------------

    public function getPhpSettings()
    {
        return array(
            'memory_limit' => $this->getMemoryLimit(),
            'max_execution_time' => $this->isPhpApiApacheHandler() ? @ini_get('max_execution_time') : null,
            'phpinfo' => $this->getPhpInfoArray()
        );
    }

    public function getPhpInfoArray()
    {
        try {

            ob_start(); phpinfo(INFO_ALL);

            $pi = preg_replace(
            array(
                '#^.*<body>(.*)</body>.*$#m', '#<h2>PHP License</h2>.*$#ms',
                '#<h1>Configuration</h1>#',  "#\r?\n#", "#</(h1|h2|h3|tr)>#", '# +<#',
                "#[ \t]+#", '#&nbsp;#', '#  +#', '# class=".*?"#', '%&#039;%',
                '#<tr>(?:.*?)" src="(?:.*?)=(.*?)" alt="PHP Logo" /></a><h1>PHP Version (.*?)</h1>(?:\n+?)</td></tr>#',
                '#<h1><a href="(?:.*?)\?=(.*?)">PHP Credits</a></h1>#',
                '#<tr>(?:.*?)" src="(?:.*?)=(.*?)"(?:.*?)Zend Engine (.*?),(?:.*?)</tr>#',
                "# +#", '#<tr>#', '#</tr>#'),
            array(
                '$1', '', '', '', '</$1>' . "\n", '<', ' ', ' ', ' ', '', ' ',
                '<h2>PHP Configuration</h2>'."\n".'<tr><td>PHP Version</td><td>$2</td></tr>'.
                "\n".'<tr><td>PHP Egg</td><td>$1</td></tr>',
                '<tr><td>PHP Credits Egg</td><td>$1</td></tr>',
                '<tr><td>Zend Engine</td><td>$2</td></tr>' . "\n" .
                '<tr><td>Zend Egg</td><td>$1</td></tr>', ' ', '%S%', '%E%'
            ), ob_get_clean()
            );

            $sections = explode('<h2>', strip_tags($pi, '<h2><th><td>'));
            unset($sections[0]);

            $pi = array();
            foreach ($sections as $section) {
                $n = substr($section, 0, strpos($section, '</h2>'));
                preg_match_all(
                    '#%S%(?:<td>(.*?)</td>)?(?:<td>(.*?)</td>)?(?:<td>(.*?)</td>)?%E%#',
                    $section,
                    $askapache,
                    PREG_SET_ORDER
                );
                foreach ($askapache as $m) {
                    if (!isset($m[0]) || !isset($m[1]) || !isset($m[2])) {
                        continue;
                    }
                    $pi[$n][$m[1]]=(!isset($m[3])||$m[2]==$m[3])?$m[2]:array_slice($m,2);
                }
            }

        } catch (Exception $exception) {
            return array();
        }

        return $pi;
    }

    // ----------------------------------------

    public function getMysqlVersion()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_read')->getServerVersion();
    }

    public function getMysqlApiName()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read')->getConnection();
        return $connection instanceof PDO ? $connection->getAttribute(PDO::ATTR_CLIENT_VERSION) : 'N/A';
    }

    public function getMysqlSettings()
    {
        $sqlQuery = "SHOW VARIABLES
                     WHERE `Variable_name` IN ('connect_timeout','wait_timeout')";

        $settingsArray = Mage::getSingleton('core/resource')
                            ->getConnection('core_read')
                            ->fetchAll($sqlQuery);

        $settings = array();
        foreach ($settingsArray as $settingItem) {
            $settings[$settingItem['Variable_name']] = $settingItem['Value'];
        }

        $phpInfo = $this->getPhpInfoArray();
        $settings = array_merge($settings,isset($phpInfo['mysql'])?$phpInfo['mysql']:array());

        return $settings;
    }

    public function getMysqlTotals()
    {
        $moduleTables = Mage::helper('M2ePro/Module_Database_Structure')->getMySqlTables();
        $magentoTables = Mage::helper('M2ePro/Magento')->getMySqlTables();

        /** @var $connRead Varien_Db_Adapter_Pdo_Mysql */
        $connRead = Mage::getSingleton('core/resource')->getConnection('core_read');

        $totalRecords = 0;
        foreach ($moduleTables as $moduleTable) {
            $moduleTable = Mage::getSingleton('core/resource')->getTableName($moduleTable);

            if (!in_array($moduleTable, $magentoTables)) {
                continue;
            };
            $dbSelect = $connRead->select()->from($moduleTable,new Zend_Db_Expr('COUNT(*)'));
            $totalRecords += (int)$connRead->fetchOne($dbSelect);
        }

        return array(
            'magento_tables' => count($magentoTables),
            'module_tables' => count($moduleTables),
            'module_records' => $totalRecords
        );
    }

    // ########################################

    public function getMemoryLimit($inMegabytes = true)
    {
        $memoryLimit = trim(ini_get('memory_limit'));

        if ($memoryLimit == '') {
            return 0;
        }

        $lastMemoryLimitLetter = strtolower(substr($memoryLimit, -1));
        switch($lastMemoryLimitLetter) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
        }

        if ($inMegabytes) {
            $memoryLimit /= 1024 * 1024;
        }

        return $memoryLimit;
    }

    public function setMemoryLimit($maxSize = 512)
    {
        $minSize = 32;
        $currentMemoryLimit = $this->getMemoryLimit();

        if ($maxSize < $minSize || (int)$currentMemoryLimit >= $maxSize) {
            return false;
        }

        for ($i=$minSize; $i<=$maxSize; $i*=2) {

            if (@ini_set('memory_limit',"{$i}M") === false) {
                if ($i == $minSize) {
                    return false;
                } else {
                    return $i/2;
                }
            }
        }

        return true;
    }

    // ----------------------------------------

    public function updateMySqlConnection()
    {
        /** @var $connRead Varien_Db_Adapter_Pdo_Mysql */
        $connRead = Mage::getSingleton('core/resource')->getConnection('core_read');

        try {
            $connRead->query('SELECT 1');
        } catch (Exception $exception) {
            $connRead->closeConnection();
        }
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Helper_Module_Exception extends Mage_Core_Helper_Abstract
{
    const FILTER_TYPE_TYPE    = 1;
    const FILTER_TYPE_INFO    = 2;
    const FILTER_TYPE_MESSAGE = 3;

    // ########################################

    public function process(Exception $exception)
    {
        try {

            $temp = Mage::helper('M2ePro/Data_Global')->getValue('send_exception_to_server');
            if (!empty($temp)) {
                return;
            }
            Mage::helper('M2ePro/Data_Global')->setValue('send_exception_to_server', true);

            if ((bool)(int)Mage::helper('M2ePro/Module')->getConfig()
                                ->getGroupValue('/debug/exceptions/','send_to_server')) {

                $type = get_class($exception);

                $info = $this->getExceptionInfo($exception, $type);
                $info .= $this->getExceptionStackTraceInfo($exception);
                $info .= $this->getCurrentUserActionInfo();
                $info .= Mage::helper('M2ePro/Module_Support_Form')->getSummaryInfo();

                if ($this->isExceptionFiltered($info, $exception->getMessage(), $type)) {
                    return;
                }

                $this->send($info, $exception->getMessage(), $type);
            }

            Mage::helper('M2ePro/Data_Global')->unsetValue('send_exception_to_server');

        } catch (Exception $exceptionTemp) {}
    }

    public function processFatal($error, $traceInfo)
    {
        try {

            $temp = Mage::helper('M2ePro/Data_Global')->getValue('send_exception_to_server');
            if (!empty($temp)) {
                return;
            }
            Mage::helper('M2ePro/Data_Global')->setValue('send_exception_to_server', true);

            if ((bool)(int)Mage::helper('M2ePro/Module')->getConfig()
                                ->getGroupValue('/debug/fatal_error/','send_to_server')) {

                $type = 'Fatal Error';

                $info = $this->getFatalInfo($error, $type);
                $info .= $traceInfo;
                $info .= $this->getCurrentUserActionInfo();
                $info .= Mage::helper('M2ePro/Module_Support_Form')->getSummaryInfo();

                if ($this->isExceptionFiltered($info, $error['message'], $type)) {
                    return;
                }

                $this->send($info, $error['message'], $type);
            }

            Mage::helper('M2ePro/Data_Global')->unsetValue('send_exception_to_server');

        } catch (Exception $exceptionTemp) {}
    }

    //-----------------------------------------

    public function setFatalErrorHandler()
    {
        $temp = Mage::helper('M2ePro/Data_Global')->getValue('set_fatal_error_handler');

        if (!empty($temp)) {
            return;
        }

        Mage::helper('M2ePro/Data_Global')->setValue('set_fatal_error_handler', true);

        $functionCode = '$error = error_get_last();

                         if (is_null($error)) {
                             return;
                         }

                         $fatalErrors = array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR);

                         if (in_array((int)$error[\'type\'], $fatalErrors)) {
                             $trace = @debug_backtrace(false);
                             $traceInfo = Mage::helper(\'M2ePro/Module_Exception\')->getFatalStackTraceInfo($trace);
                             Mage::helper(\'M2ePro/Module_Exception\')->processFatal($error,$traceInfo);
                         }';

        $shutdownFunction = create_function('', $functionCode);
        register_shutdown_function($shutdownFunction);
    }

    public function getUserMessage(Exception $exception)
    {
        return Mage::helper('M2ePro')->__('Fatal error occurred').': "'.$exception->getMessage().'".';
    }

    // ########################################

    private function getExceptionInfo(Exception $exception, $type)
    {
        $additionalData = $exception instanceof Ess_M2ePro_Model_Exception ? $exception->getAdditionalData()
                                                                           : '';

        is_array($additionalData) && $additionalData = print_r($additionalData, true);

        $exceptionInfo = <<<EXCEPTION
-------------------------------- EXCEPTION INFO ----------------------------------
Type: {$type}
File: {$exception->getFile()}
Line: {$exception->getLine()}
Code: {$exception->getCode()}
Message: {$exception->getMessage()}
Additional Data: {$additionalData}

EXCEPTION;

        return $exceptionInfo;
    }

    private function getExceptionStackTraceInfo(Exception $exception)
    {
        $stackTraceInfo = <<<TRACE
-------------------------------- STACK TRACE INFO --------------------------------
{$exception->getTraceAsString()}


TRACE;

        return $stackTraceInfo;
    }

    //-----------------------------------------

    private function getFatalInfo($error, $type)
    {
        $exceptionInfo = <<<FATAL
-------------------------------- FATAL ERROR INFO --------------------------------
Type: {$type}
File: {$error['file']}
Line: {$error['line']}
Message: {$error['message']}


FATAL;

        return $exceptionInfo;
    }

    public function getFatalStackTraceInfo($stackTrace)
    {
        if (!is_array($stackTrace)) {
            $stackTrace = array();
        }

        $stackTrace = array_reverse($stackTrace);
        $info = '';

        if (count($stackTrace) > 1) {
            foreach ($stackTrace as $key => $trace) {
                $info .= "#{$key} {$trace['file']}({$trace['line']}):";
                $info .= " {$trace['class']}{$trace['type']}{$trace['function']}(";

                if (count($trace['args'])) {
                    foreach ($trace['args'] as $key => $arg) {
                        $key != 0 && $info .= ',';

                        if (is_object($arg)) {
                            $info .= get_class($arg);
                        } else {
                            $info .= $arg;
                        }
                    }
                }
                $info .= ")\n";
            }
        }

        if ($info == '') {
            $info = 'Unavailable';
        }

        $stackTraceInfo = <<<TRACE
-------------------------------- STACK TRACE INFO --------------------------------
{$info}


TRACE;

        return $stackTraceInfo;
    }

    //-----------------------------------------

    private function getCurrentUserActionInfo()
    {
        $server = isset($_SERVER) ? print_r($_SERVER, true) : '';
        $get = isset($_GET) ? print_r($_GET, true) : '';
        $post = isset($_POST) ? print_r($_POST, true) : '';

        $actionInfo = <<<ACTION
-------------------------------- ACTION INFO -------------------------------------
SERVER: {$server}
GET: {$get}
POST: {$post}

ACTION;

        return $actionInfo;
    }

    // ########################################

    private function send($info, $message, $type)
    {
        $dispatcherObject = Mage::getModel('M2ePro/Connector_M2ePro_Dispatcher');
        $connectorObj = $dispatcherObject->getVirtualConnector('exception','add','entity',
                                                               array('info'    => $info,
                                                                     'message' => $message,
                                                                     'type'    => $type));

        $dispatcherObject->process($connectorObj);
    }

    private function isExceptionFiltered($info, $message, $type)
    {
        if (!(bool)(int)Mage::helper('M2ePro/Module')->getConfig()
                            ->getGroupValue('/debug/exceptions/','filters_mode')) {
            return false;
        }

        $exceptionFilters = Mage::getModel('M2ePro/Registry')->load('/exceptions_filters/', 'key')
                                                             ->getValueFromJson();

        foreach ($exceptionFilters as $exceptionFilter) {

            try {

                $searchSubject = '';
                $exceptionFilter['type'] == self::FILTER_TYPE_TYPE    && $searchSubject = $type;
                $exceptionFilter['type'] == self::FILTER_TYPE_MESSAGE && $searchSubject = $message;
                $exceptionFilter['type'] == self::FILTER_TYPE_INFO    && $searchSubject = $info;

                $tempResult = preg_match($exceptionFilter['preg_match'], $searchSubject);

            } catch (Exception $exception) {
                return false;
            }

            if ($tempResult) {
                return true;
            }
        }

        return false;
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Helper_Data_Global extends Mage_Core_Helper_Abstract
{
    // ########################################

    public function getValue($key)
    {
        $globalKey = Ess_M2ePro_Helper_Data::CUSTOM_IDENTIFIER.'_'.$key;
        return Mage::registry($globalKey);
    }

    public function setValue($key, $value)
    {
        $globalKey = Ess_M2ePro_Helper_Data::CUSTOM_IDENTIFIER.'_'.$key;
        Mage::register($globalKey,$value,!Mage::helper('M2ePro/Module')->isDevelopmentEnvironment());
    }

    // ########################################

    public function unsetValue($key)
    {
        $globalKey = Ess_M2ePro_Helper_Data::CUSTOM_IDENTIFIER.'_'.$key;
        Mage::unregister($globalKey);
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Helper_Module_Cron extends Mage_Core_Helper_Abstract
{
    const TYPE_MAGENTO = 'magento';
    const TYPE_SERVICE = 'service';

    const STATE_IN_PROGRESS = 0;
    const STATE_COMPLETED   = 1;
    const STATE_NOT_FOUND   = 2;

    // ########################################

    public function isModeEnabled()
    {
        return (bool)$this->getConfigValue('mode');
    }

    public function isReadyToRun()
    {
        return Mage::helper('M2ePro/Module')->isMigrationWizardFinished() &&
               (
                   Mage::helper('M2ePro/View_Ebay')->isInstallationWizardFinished() ||
                   Mage::helper('M2ePro/View_Common')->isInstallationWizardFinished()
               );
    }

    // ########################################

    public function getType()
    {
        return $this->getConfigValue('type');
    }

    public function setType($value)
    {
        return $this->setConfigValue('type', $value);
    }

    // ----------------------------------------

    public function isTypeMagento()
    {
        return $this->getType() == self::TYPE_MAGENTO;
    }

    public function isTypeService()
    {
        return $this->getType() == self::TYPE_SERVICE;
    }

    // ########################################

    public function getLastTypeChange()
    {
        return $this->getConfigValue('last_type_change');
    }

    public function setLastTypeChange($value)
    {
        $this->setConfigValue('last_type_change', $value);
    }

    // ----------------------------------------

    public function isLastTypeChangeMoreThan($interval, $isHours = false)
    {
        $isHours && $interval *= 3600;
        $lastTypeChange = $this->getLastTypeChange();

        if (is_null($lastTypeChange)) {

            $tempTimeCacheKey = 'cron_start_time_of_checking_last_type_change';
            $lastTypeChange = Mage::helper('M2ePro/Data_Cache_Permanent')->getValue($tempTimeCacheKey);

            if (empty($lastTypeChange)) {
                $lastTypeChange = Mage::helper('M2ePro')->getCurrentGmtDate();
                Mage::helper('M2ePro/Data_Cache_Permanent')->setValue($tempTimeCacheKey,$lastTypeChange,array('cron'));
            }
        }

        return Mage::helper('M2ePro')->getCurrentGmtDate(true) > strtotime($lastTypeChange) + $interval;
    }

    // ########################################

    public function getLastAccess()
    {
        return $this->getConfigValue('last_access');
    }

    public function setLastAccess($value)
    {
        return $this->setConfigValue('last_access',$value);
    }

    // ----------------------------------------

    public function isLastAccessMoreThan($interval, $isHours = false)
    {
        $isHours && $interval *= 3600;
        $lastAccess = $this->getLastAccess();

        if (is_null($lastAccess)) {

            $tempTimeCacheKey = 'cron_start_time_of_checking_last_access';
            $lastAccess = Mage::helper('M2ePro/Data_Cache_Permanent')->getValue($tempTimeCacheKey);

            if (empty($lastAccess)) {
                $lastAccess = Mage::helper('M2ePro')->getCurrentGmtDate();
                Mage::helper('M2ePro/Data_Cache_Permanent')->setValue($tempTimeCacheKey,$lastAccess,array('cron'));
            }
        }

        return Mage::helper('M2ePro')->getCurrentGmtDate(true) > strtotime($lastAccess) + $interval;
    }

    // ########################################

    public function getLastRun()
    {
        return $this->getConfigValue('last_run');
    }

    public function setLastRun($value)
    {
        return $this->setConfigValue('last_run',$value);
    }

    // ----------------------------------------

    public function isLastRunMoreThan($interval, $isHours = false)
    {
        $isHours && $interval *= 3600;
        $lastRun = $this->getLastRun();

        if (is_null($lastRun)) {

            $tempTimeCacheKey = 'cron_start_time_of_checking_last_run';
            $lastRun = Mage::helper('M2ePro/Data_Cache_Permanent')->getValue($tempTimeCacheKey);

            if (empty($lastRun)) {
                $lastRun = Mage::helper('M2ePro')->getCurrentGmtDate();
                Mage::helper('M2ePro/Data_Cache_Permanent')->setValue($tempTimeCacheKey,$lastRun,array('cron'));
            }
        }

        return Mage::helper('M2ePro')->getCurrentGmtDate(true) > strtotime($lastRun) + $interval;
    }

    // ########################################

    private function getConfig()
    {
        return Mage::helper('M2ePro/Module')->getConfig();
    }

    // ----------------------------------------

    private function getConfigValue($key)
    {
        return $this->getConfig()->getGroupValue('/cron/', $key);
    }

    private function setConfigValue($key, $value)
    {
        return $this->getConfig()->setGroupValue('/cron/', $key, $value);
    }

    // ########################################
}
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Date conversion model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Date
{
    /**
     * Current config offset in seconds
     *
     * @var int
     */
    private $_offset = 0;

    /**
     * Current system offset in seconds
     *
     * @var int
     */
    private $_systemOffset = 0;

    /**
     * Init offset
     *
     */
    public function __construct()
    {
        $this->_offset = $this->calculateOffset($this->_getConfigTimezone());
        $this->_systemOffset = $this->calculateOffset();
    }

    /**
     * Gets the store config timezone
     *
     * @return string
     */
    protected function _getConfigTimezone()
    {
        return Mage::app()->getStore()->getConfig('general/locale/timezone');
    }

    /**
     * Calculates timezone offset
     *
     * @param  string $timezone
     * @return int offset between timezone and gmt
     */
    public function calculateOffset($timezone = null)
    {
        $result = true;
        $offset = 0;

        if (!is_null($timezone)){
            $oldzone = @date_default_timezone_get();
            $result = date_default_timezone_set($timezone);
        }

        if ($result === true) {
            $offset = (int)date('Z');
        }

        if (!is_null($timezone)){
            date_default_timezone_set($oldzone);
        }

        return $offset;
    }

    /**
     * Forms GMT date
     *
     * @param  string $format
     * @param  int|string $input date in current timezone
     * @return string
     */
    public function gmtDate($format = null, $input = null)
    {
        if (is_null($format)) {
            $format = 'Y-m-d H:i:s';
        }

        $date = $this->gmtTimestamp($input);

        if ($date === false) {
            return false;
        }

        $result = date($format, $date);
        return $result;
    }

    /**
     * Converts input date into date with timezone offset
     * Input date must be in GMT timezone
     *
     * @param  string $format
     * @param  int|string $input date in GMT timezone
     * @return string
     */
    public function date($format = null, $input = null)
    {
        if (is_null($format)) {
            $format = 'Y-m-d H:i:s';
        }

        $result = date($format, $this->timestamp($input));
        return $result;
    }

    /**
     * Forms GMT timestamp
     *
     * @param  int|string $input date in current timezone
     * @return int
     */
    public function gmtTimestamp($input = null)
    {
        if (is_null($input)) {
            return gmdate('U');
        } else if (is_numeric($input)) {
            $result = $input;
        } else {
            $result = strtotime($input);
        }

        if ($result === false) {
            // strtotime() unable to parse string (it's not a date or has incorrect format)
            return false;
        }

        $date      = Mage::app()->getLocale()->date($result);
        $timestamp = $date->get(Zend_Date::TIMESTAMP) - $date->get(Zend_Date::TIMEZONE_SECS);

        unset($date);
        return $timestamp;

    }

    /**
     * Converts input date into timestamp with timezone offset
     * Input date must be in GMT timezone
     *
     * @param  int|string $input date in GMT timezone
     * @return int
     */
    public function timestamp($input = null)
    {
        if (is_null($input)) {
            $result = $this->gmtTimestamp();
        } else if (is_numeric($input)) {
            $result = $input;
        } else {
            $result = strtotime($input);
        }

        $date      = Mage::app()->getLocale()->date($result);
        $timestamp = $date->get(Zend_Date::TIMESTAMP) + $date->get(Zend_Date::TIMEZONE_SECS);

        unset($date);
        return $timestamp;
    }

    /**
     * Get current timezone offset in seconds/minutes/hours
     *
     * @param  string $type
     * @return int
     */
    public function getGmtOffset($type = 'seconds')
    {
        $result = $this->_offset;
        switch ($type) {
            case 'seconds':
            default:
                break;

            case 'minutes':
                $result = $result / 60;
                break;

            case 'hours':
                $result = $result / 60 / 60;
                break;
        }
        return $result;
    }

    /**
     * Deprecated since 1.1.7
     */
    public function checkDateTime($year, $month, $day, $hour = 0, $minute = 0, $second = 0)
    {
        if (!checkdate($month, $day, $year)) {
            return false;
        }
        foreach (array('hour' => 23, 'minute' => 59, 'second' => 59) as $var => $maxValue) {
            $value = (int)$$var;
            if (($value < 0) || ($value > $maxValue)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Deprecated since 1.1.7
     */
    public function parseDateTime($dateTimeString, $dateTimeFormat)
    {
        // look for supported format
        $isSupportedFormatFound = false;

        $formats = array(
            // priority is important!
            '%m/%d/%y %I:%M' => array(
                '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2})/',
                array('y' => 3, 'm' => 1, 'd' => 2, 'h' => 4, 'i' => 5)
            ),
            'm/d/y h:i' => array(
                '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2})/',
                array('y' => 3, 'm' => 1, 'd' => 2, 'h' => 4, 'i' => 5)
            ),
            '%m/%d/%y' => array('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,2})/', array('y' => 3, 'm' => 1, 'd' => 2)),
            'm/d/y' => array('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{1,2})/', array('y' => 3, 'm' => 1, 'd' => 2)),
        );

        foreach ($formats as $supportedFormat => $regRule) {
            if (false !== strpos($dateTimeFormat, $supportedFormat, 0)) {
                $isSupportedFormatFound = true;
                break;
            }
        }
        if (!$isSupportedFormatFound) {
            Mage::throwException(Mage::helper('core')->__('Date/time format "%s" is not supported.', $dateTimeFormat));
        }

        // apply reg rule to found format
        $regex = array_shift($regRule);
        $mask  = array_shift($regRule);
        if (!preg_match($regex, $dateTimeString, $matches)) {
            Mage::throwException(Mage::helper('core')->__('Specified date/time "%1$s" do not match format "%2$s".', $dateTimeString, $dateTimeFormat));
        }

        // make result
        $result = array();
        foreach (array('y', 'm', 'd', 'h', 'i', 's') as $key) {
            $value = 0;
            if (isset($mask[$key]) && isset($matches[$mask[$key]])) {
                $value = (int)$matches[$mask[$key]];
            }
            $result[] = $value;
        }

        // make sure to return full year
        if ($result[0] < 100) {
            $result[0] = 2000 + $result[0];
        }

        return $result;
    }
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Mysql4_Abstract
    extends Mage_Core_Model_Mysql4_Abstract
{
    // ########################################

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $origData = $object->getOrigData();

        if (empty($origData)) {
            $object->setData('create_date',Mage::helper('M2ePro')->getCurrentGmtDate());
        }

        $object->setData('update_date',Mage::helper('M2ePro')->getCurrentGmtDate());

        $result = parent::_beforeSave($object);

        // fix for \Varien_Db_Adapter_Pdo_Mysql::prepareColumnValue
        // an empty string cannot be saved -> NULL is saved instead
        // for Magento version > 1.6.x.x
        foreach ($object->getData() as $key => $value) {
            $value === '' && $object->setData($key,new Zend_Db_Expr("''"));
        }

        return $result;
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        // fix for \Varien_Db_Adapter_Pdo_Mysql::prepareColumnValue
        // an empty string cannot be saved -> NULL is saved instead
        // for Magento version > 1.6.x.x
        foreach ($object->getData() as $key => $value) {
            if ($value instanceof Zend_Db_Expr && $value->__toString() === '\'\'') {
                $object->setData($key,'');
            }
        }

        return parent::_afterSave($object);
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_Mysql4_Config_Module
    extends Ess_M2ePro_Model_Mysql4_Abstract
{
    // ########################################

    public function _construct()
    {
        $this->_init('M2ePro/Config_Module', 'id');
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Mysql4_Collection_Abstract
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    // ########################################

    protected function _toOptionArray($valueField = 'id', $labelField = 'title', $additional = array())
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    protected function _toOptionHash($valueField = 'id', $labelField = 'title')
    {
        return parent::_toOptionHash($valueField, $labelField);
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_Mysql4_Config_Module_Collection
    extends Ess_M2ePro_Model_Mysql4_Collection_Abstract
{
    // ########################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/Config_Module');
    }

    // ########################################
}

/**
 * Event observer main model
 *
 * @author Ebizmarts Team <info@ebizmarts.com>
 */
class Ebizmarts_SweetMonkey_Model_Observer
{

    public function saveConfig($observer)
    {
        if (!Mage::helper('core')->isModuleEnabled('TBT_Common')) {
            if (Mage::app()->getRequest()->getParam('store')) {
                $scope = 'store';
            } elseif (Mage::app()->getRequest()->getParam('website')) {
                $scope = 'website';
            } else {
                $scope = 'default';
            }

            $store = is_null($observer->getEvent()->getStore()) ? Mage::app()->getDefaultStoreView()->getCode() : $observer->getEvent()->getStore();
            $config = Mage::getModel('core/config');
            $config->saveConfig('sweetmonkey/general/active', false, $scope, $store);
            Mage::getConfig()->cleanCache();
            $message = Mage::helper('sweetmonkey')->__('To activate Sweet Monkey you need to have <a href=https://www.sweettoothrewards.com/features/magento/>Sweet Tooth Rewards</a> enabled');
            Mage::getSingleton('adminhtml/session')->addWarning($message);
        }
        return $observer;
    }

    /**
     * Sende merge vars after customer logs in
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function customerLogin($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        Mage::helper('sweetmonkey')->pushVars($customer);
        return $this;
    }

    /**
     * Sende merge vars after Rewards/Customer saves
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function customerRewardSave($observer)
    {
        $obj = $observer->getEvent()->getObject();
        if ($obj instanceof TBT_Rewards_Model_Customer) {
            Mage::helper('sweetmonkey')->pushVars($obj);
        }

        return $this;
    }

    /**
     * Sende merge vars on new points transfer
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function pointsEvent($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if (!$customer) {
            return $this;
        }

        Mage::helper('sweetmonkey')->pushVars($customer);

        return $this;
    }

    /**
     * Add points merge vars to MailChimp MergeVars struct
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function attachTbtMergeVars($observer)
    {

        $holder = $observer->getEvent()->getNewvars();
        $helper = Mage::helper('sweetmonkey');
//        $customerHelper = Mage::helper('customer');
        $customer = $observer->getEvent()->getCustomer();
        if ($helper->enabled() && ($customer->getId())) {


            $merge = unserialize($helper->config('merge_vars'));

            if (count($merge)) {

                $tbtVars = array();
                foreach ($merge as $varTag) {
                    $tbtVars [$varTag['var_code']] = '-';
                }

                $tbtCustomer = Mage::getModel('rewards/customer')->load($customer->getId());

//                if(array_key_exists('REFERRALURL', $tbtVars)) {
//                    $tbtVars['REFERRALURL'] = Mage::helper('rewardssocial/referral_share')->getReferralUrl($customer);
//                }

                //Point balance
                if (array_key_exists('PTS', $tbtVars)) {
                    $tbtVars['PTS'] = $tbtCustomer->getPointsSummary();
                }

                if (array_key_exists('POINTS', $tbtVars)) {
                    $tbtVars['POINTS'] = $tbtCustomer->getUsablePointsBalance(1);
                }

                //Earn and Spent points
                $existEarn = array_key_exists('PTSEARN', $tbtVars);
                $existSpent = array_key_exists('PTSSPENT', $tbtVars);

                if ($existEarn || $existSpent) {

                    $lastTransfers = $tbtCustomer->getTransfers()
                        ->selectOnlyActive()
                        ->addOrder('last_update_ts', Varien_Data_Collection::SORT_ORDER_DESC);

                    $spent = $earn = null;

                    if ($lastTransfers->getSize()) {
                        list($spent,$earn) = $this->spentAndErn($lastTransfers);
                    }

                    if ($existEarn && $earn) {
                        $tbtVars['PTSEARN'] = $earn;
                    }
                    if ($existSpent && $spent) {
                        $tbtVars['PTSSPENT'] = $spent;
                    }


                }

                $tbtVars = $this->expirationPoints($tbtVars,$tbtCustomer);
//                //Expiration Points
//                if (array_key_exists('PTSEXP', $tbtVars)) {
//                    $val = Mage::getSingleton('rewards/expiry')
//                        ->getExpiryDate($tbtCustomer);
//                    if ($val) {
//                        $val = date_format(date_create_from_format('d/m/Y', $val), 'Y-m-d');
//                        $tbtVars['PTSEXP'] = $val;
//                    }
//                }
//                foreach ($tbtVars as $key => $var) {
//                    $aux = str_replace('points', '', strtolower($var));
//                    $tbtVars[$key] = str_replace('no', 0, $aux);
//
//                }

                $tbtVars = array_filter($tbtVars);
                //Add data to MailChimp merge vars
                $holder->setData($tbtVars);
            }

        }
        return $this;
    }
    private function spentAndErn($lastTransfers)
    {
        $spent = $earn = null;
        foreach ($lastTransfers as $transfer) {

            if (is_null($earn) && $transfer->getQuantity() > 0) {
                $earn = date_format(date_create_from_format('Y-m-d H:i:s', $transfer->getEffectiveStart()), 'Y-m-d');
            } else if (is_null($spent) && $transfer->getQuantity() < 0) {
                $spent = date_format(date_create_from_format('Y-m-d H:i:s', $transfer->getEffectiveStart()), 'Y-m-d');
            }

            if (!is_null($spent) && !is_null($earn)) {
                break;
            }

        }
        return array($spent,$earn);
    }

    private function expirationPoints($tbtVars,$tbtCustomer)
    {
        if (array_key_exists('PTSEXP', $tbtVars)) {
            $val = Mage::getSingleton('rewards/expiry')
                ->getExpiryDate($tbtCustomer);
            if ($val) {
                $val = date_format(date_create_from_format('d/m/Y', $val), 'Y-m-d');
                $tbtVars['PTSEXP'] = $val;
            }
        }
        foreach ($tbtVars as $key => $var) {
            $aux = str_replace('points', '', strtolower($var));
            $tbtVars[$key] = str_replace('no', 0, $aux);

        }
        return $tbtVars;
    }
    /**
     * Gets a date in format YYYY-MM-DD HH:m:s and returns MM/DD/YYYY
     *
     * @param string Date in format YYYY-MM-DD
     * @return string MM/DD/YYYY
     */
    protected function _formatDateMerge($date)
    {
        return preg_replace("/(\d+)\D+(\d+)\D+(\d+)\D+(\d+)\D+(\d+)\D+(\d+)/", "$2/$3/$1", $date);
    }

}

/*
* @copyright  Copyright (c) 2013 by  ESS-UA.
*/

class Ess_M2ePro_Helper_Module_Wizard extends Mage_Core_Helper_Abstract
{
    const STATUS_NOT_STARTED = 0;
    const STATUS_ACTIVE      = 1;
    const STATUS_COMPLETED   = 2;
    const STATUS_SKIPPED     = 3;

    const KEY_VIEW     = 'view';
    const KEY_STATUS   = 'status';
    const KEY_STEP     = 'step';
    const KEY_PRIORITY = 'priority';
    const KEY_TYPE     = 'type';

    const TYPE_SIMPLE  = 0;
    const TYPE_BLOCKER = 1;

    private $cache = null;

    // ########################################

    /**
     * Wizards Factory
     * @param string $nick
     * @return Ess_M2ePro_Model_Wizard
     */
    public function getWizard($nick)
    {
        return Mage::getSingleton('M2ePro/Wizard_'.ucfirst($nick));
    }

    // ########################################

    public function isNotStarted($nick)
    {
        return $this->getStatus($nick) == self::STATUS_NOT_STARTED &&
               $this->getWizard($nick)->isActive();
    }

    public function isActive($nick)
    {
        return $this->getStatus($nick) == self::STATUS_ACTIVE &&
               $this->getWizard($nick)->isActive();
    }

    public function isCompleted($nick)
    {
        return $this->getStatus($nick) == self::STATUS_COMPLETED;
    }

    public function isSkipped($nick)
    {
        return $this->getStatus($nick) == self::STATUS_SKIPPED;
    }

    public function isFinished($nick)
    {
        return $this->isCompleted($nick) || $this->isSkipped($nick);
    }

    // ########################################

    private function getConfigValue($nick, $key)
    {
        Mage::helper('M2ePro/Module')->isDevelopmentEnvironment() && $this->loadCache();

        if (!is_null($this->cache)) {
            return $this->cache[$nick][$key];
        }

        if (($this->cache = Mage::helper('M2ePro/Data_Cache_Permanent')->getValue('wizard')) !== false) {
            $this->cache = json_decode($this->cache, true);
            return $this->cache[$nick][$key];
        }

        $this->loadCache();

        return $this->cache[$nick][$key];
    }

    private function setConfigValue($nick, $key, $value)
    {
        (is_null($this->cache) || Mage::helper('M2ePro/Module')->isDevelopmentEnvironment()) && $this->loadCache();

        $this->cache[$nick][$key] = $value;

        Mage::helper('M2ePro/Data_Cache_Permanent')->setValue('wizard',
                                                    json_encode($this->cache),
                                                    array('wizard'),
                                                    60*60);

        /** @var $connWrite Varien_Db_Adapter_Pdo_Mysql */
        $connWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tableName = Mage::getSingleton('core/resource')->getTableName('m2epro_wizard');

        $connWrite->update(
            $tableName,
            array($key => $value),
            array('nick = ?' => $nick)
        );

        return $this;
    }

    // --------------------------------------------

    public function getView($nick)
    {
        return $this->getConfigValue($nick, self::KEY_VIEW);
    }

    public function getStatus($nick)
    {
        return $this->getConfigValue($nick, self::KEY_STATUS);
    }

    public function setStatus($nick, $status = self::STATUS_NOT_STARTED)
    {
        $this->setConfigValue($nick, self::KEY_STATUS, $status);
    }

    public function getStep($nick)
    {
        return $this->getConfigValue($nick, self::KEY_STEP);
    }

    public function setStep($nick, $step = NULL)
    {
        $this->setConfigValue($nick, self::KEY_STEP, $step);
    }

    public function getPriority($nick)
    {
        return $this->getConfigValue($nick, self::KEY_PRIORITY);
    }

    public function getType($nick)
    {
        return $this->getConfigValue($nick, self::KEY_TYPE);
    }

    // ########################################

    /**
     * @param string $view
     * @return null|Ess_M2ePro_Model_Wizard
     */
    public function getActiveWizard($view)
    {
        $wizards = $this->getAllWizards($view);

        /** @var $wizard Ess_M2ePro_Model_Wizard */
        foreach ($wizards as $wizard) {
            if ($this->isNotStarted($this->getNick($wizard)) || $this->isActive($this->getNick($wizard))) {
                return $wizard;
            }
        }

        return null;
    }

    // ------------------------------------------------------

    private function getAllWizards($view)
    {
        (is_null($this->cache) || Mage::helper('M2ePro/Module')->isDevelopmentEnvironment()) && $this->loadCache();

        $wizards = array();
        foreach ($this->cache as $nick => $wizard) {
            if ($wizard['view'] != '*' && $wizard['view'] != $view) {
                continue;
            }

            $wizards[] = $this->getWizard($nick);
        }

        return $wizards;
    }

    // ########################################

    /**
     * @param string $block
     * @param string $nick
     * @return Mage_Core_Block_Abstract
     * */

    public function createBlock($block,$nick = '')
    {
        return Mage::getSingleton('core/layout')->createBlock(
            'M2ePro/adminhtml_wizard_'.$nick.'_'.$block,
            null,
            array('nick' => $nick)
        );
    }

    // ########################################

    public function addWizardHandlerJs()
    {
        Mage::getSingleton('core/layout')->getBlock('head')->addJs(
            'M2ePro/WizardHandler.js'
        );
    }

    // ########################################

    public function getNick($wizard)
    {
        $parts = explode('_',get_class($wizard));
        $nick = array_pop($parts);
        $nick{0} = strtolower($nick{0});
        return $nick;
    }

    // ########################################

    private function loadCache()
    {
        /** @var $connRead Varien_Db_Adapter_Pdo_Mysql */
        $connRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tableName = Mage::getSingleton('core/resource')->getTableName('m2epro_wizard');

        $this->cache = $connRead->fetchAll(
            $connRead->select()->from($tableName,'*')
        );

        $sortFunction = <<<FUNCTION
if (\$a['type'] != \$b['type']) {
    return \$a['type'] == Ess_M2ePro_Helper_Module_Wizard::TYPE_BLOCKER ? - 1 : 1;
}

if (\$a['priority'] == \$b['priority']) {
    return 0;
}

return \$a['priority'] > \$b['priority'] ? 1 : -1;
FUNCTION;

        $sortFunction = create_function('$a,$b',$sortFunction);

        usort($this->cache, $sortFunction);

        foreach ($this->cache as $id => $wizard) {
            $this->cache[$wizard['nick']] = $wizard;
            unset($this->cache[$id]);
        }

        Mage::helper('M2ePro/Data_Cache_Permanent')->setValue('wizard',
                                                    json_encode($this->cache),
                                                    array('wizard'),
                                                    60*60);
    }

    // ########################################

    public function getActiveBlockerWizard($view)
    {
        $wizards = $this->getAllWizards($view);

        /** @var $wizard Ess_M2ePro_Model_Wizard */
        foreach ($wizards as $wizard) {

            if ($this->getType($this->getNick($wizard)) != self::TYPE_BLOCKER) {
                continue;
            }

            if ($this->isNotStarted($this->getNick($wizard)) || $this->isActive($this->getNick($wizard))) {
                return $wizard;
            }
        }

        return null;
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Helper_View_Ebay extends Mage_Core_Helper_Abstract
{
    // M2ePro_TRANSLATIONS
    // Sell On eBay

    const NICK  = 'ebay';
    const TITLE = 'Sell On eBay';

    const WIZARD_INSTALLATION_NICK = 'installationEbay';
    const MENU_ROOT_NODE_NICK = 'm2epro_ebay';

    const MODE_SIMPLE = 'simple';
    const MODE_ADVANCED = 'advanced';

    // ########################################

    public function getMenuRootNodeLabel()
    {
        return Mage::helper('M2ePro')->__(self::TITLE);
    }

    // ########################################

    public function getPageNavigationPath($pathNick, $tabName = NULL, $additionalEnd = NULL)
    {
        $resultPath = array();

        $rootMenuNode = Mage::getConfig()->getNode('adminhtml/menu/m2epro_ebay');
        $menuLabel = Mage::helper('M2ePro/View')->getMenuPath($rootMenuNode, $pathNick, $this->getMenuRootNodeLabel());

        if (!$menuLabel) {
            return '';
        }

        $resultPath['menu'] = $menuLabel;

        if ($tabName) {
            $resultPath['tab'] = $tabName . ' ' . Mage::helper('M2ePro')->__('Tab');
        }

        if ($additionalEnd) {
            $resultPath['additional'] = $additionalEnd;
        }

        return join($resultPath, ' > ');
    }

    // ########################################

    public function getWizardInstallationNick()
    {
        return self::WIZARD_INSTALLATION_NICK;
    }

    public function isInstallationWizardFinished()
    {
        return Mage::helper('M2ePro/Module_Wizard')->isFinished(
            $this->getWizardInstallationNick()
        );
    }

    // ########################################

    public function getMode()
    {
        return Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/view/ebay/', 'mode');
    }

    public function setMode($mode)
    {
        $mode = strtolower($mode);
        if (!in_array($mode,array(self::MODE_SIMPLE,self::MODE_ADVANCED))) {
            return;
        }
        Mage::helper('M2ePro/Module')->getConfig()->setGroupValue('/view/ebay/', 'mode', $mode);
    }

    //-----------------------------------------

    public function isSimpleMode()
    {
        return $this->getMode() == self::MODE_SIMPLE;
    }

    public function isAdvancedMode()
    {
        return $this->getMode() == self::MODE_ADVANCED;
    }

    // ########################################

    public function getDocumentationUrl()
    {
        return Mage::helper('M2ePro/Module')->getConfig()
                    ->getGroupValue('/view/ebay/support/', 'documentation_url');
    }

    public function getVideoTutorialsUrl()
    {
        return Mage::helper('M2ePro/Module')->getConfig()
                    ->getGroupValue('/view/ebay/support/', 'video_tutorials_url');
    }

    // ########################################

    public function prepareMenu(array $menuArray)
    {
        if (!Mage::getSingleton('admin/session')->isAllowed(self::MENU_ROOT_NODE_NICK)) {
            return $menuArray;
        }

        if (count(Mage::helper('M2ePro/View_Ebay_Component')->getActiveComponents()) <= 0) {
            unset($menuArray[self::MENU_ROOT_NODE_NICK]);
            return $menuArray;
        }

        $tempTitle = $this->getMenuRootNodeLabel();
        !empty($tempTitle) && $menuArray[self::MENU_ROOT_NODE_NICK]['label'] = $tempTitle;

        // Add wizard menu item
        //---------------------------------
        /* @var $wizardHelper Ess_M2ePro_Helper_Module_Wizard */
        $wizardHelper = Mage::helper('M2ePro/Module_Wizard');

        $activeBlocker = $wizardHelper->getActiveBlockerWizard(Ess_M2ePro_Helper_View_Ebay::NICK);

        if (!$activeBlocker) {
            return $menuArray;
        }

        unset($menuArray[self::MENU_ROOT_NODE_NICK]['children']);
        unset($menuArray[self::MENU_ROOT_NODE_NICK]['click']);

        $menuArray[self::MENU_ROOT_NODE_NICK]['url'] = Mage::helper('adminhtml')->getUrl(
            'M2ePro/adminhtml_wizard_'.$wizardHelper->getNick($activeBlocker).'/index'
        );
        $menuArray[self::MENU_ROOT_NODE_NICK]['last'] = true;

        return $menuArray;
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Helper_View_Common extends Mage_Core_Helper_Abstract
{
    // M2ePro_TRANSLATIONS
    // Sell On Multi-Channels

    const NICK  = 'common';
    const TITLE = 'Sell On Multi-Channels';

    const WIZARD_INSTALLATION_NICK = 'installationCommon';
    const MENU_ROOT_NODE_NICK = 'm2epro_common';

    // ########################################

    public function getMenuRootNodeLabel()
    {
        $activeComponents = $this->getActiveComponentsLabels();

        if (count($activeComponents) <= 0 || count($activeComponents) > 1) {
            return Mage::helper('M2ePro')->__(self::TITLE);
        }

        return array_shift($activeComponents);
    }

    // ########################################

    public function getActiveComponentsLabels()
    {
        $labels = array();

        if (Mage::helper('M2ePro/Component_Amazon')->isActive()) {
            $labels[] = Mage::helper('M2ePro/Component_Amazon')->getTitle();
        }

        if (Mage::helper('M2ePro/Component_Buy')->isActive()) {
            $labels[] = Mage::helper('M2ePro/Component_Buy')->getTitle();
        }

        return $labels;
    }

    // ########################################

    public function getPageNavigationPath($pathNick, $tabName = NULL, $channel = NULL, $additionalEnd = NULL,
                                          $params = array())
    {
        $pathParts = array();

        $rootMenuNode = Mage::getConfig()->getNode('adminhtml/menu/m2epro_common');
        $menuLabel = Mage::helper('M2ePro/View')->getMenuPath($rootMenuNode, $pathNick, $this->getMenuRootNodeLabel());

        if (!$menuLabel) {
            return '';
        }

        $pathParts['menu'] = $menuLabel;

        if ($tabName) {
            $pathParts['tab'] = $tabName . ' ' . Mage::helper('M2ePro')->__('Tab');
        } else {
            $pathParts['tab'] = NULL;
        }

        $channelLabel = '';
        if ($channel) {

            $components = $this->getActiveComponentsLabels();

            if ($channel == 'any') {
                if (count($components) > 1) {
                    if (isset($params['any_channel_as_label']) && $params['any_channel_as_label'] === true) {
                        $channelLabel = Mage::helper('M2ePro')->__('Any Channel');
                    } else {
                        $channelLabel = '[' . join($components, '/') . ']';
                    }
                }

            } elseif ($channel == 'all') {
                if (count($components) > 1) {
                    $channelLabel = Mage::helper('M2ePro')->__('All Channels');
                }
            } else {

                if (!Mage::helper('M2ePro/Component_' . ucfirst($channel))->isActive()) {
                    throw new Exception('Channel is not Active!');
                }

                if (count($components) > 1) {
                    $channelLabel = Mage::helper('M2ePro/Component_' . ucfirst($channel))->getTitle();
                }
            }
        }

        $pathParts['channel'] = $channelLabel;

        $pathParts['additional'] = $additionalEnd;

        $resultPath = array();

        $resultPath['menu'] = $pathParts['menu'];
        if (isset($params['reverse_tab_and_channel']) && $params['reverse_tab_and_channel'] === true) {
            $resultPath['channel'] = $pathParts['channel'];
            $resultPath['tab'] = $pathParts['tab'];
        } else {
            $resultPath['tab'] = $pathParts['tab'];
            $resultPath['channel'] = $pathParts['channel'];
        }
        $resultPath['additional'] = $pathParts['additional'];

        $resultPath = array_diff($resultPath, array(''));

        return join($resultPath, ' > ');
    }

    // ########################################

    public function getWizardInstallationNick()
    {
        return self::WIZARD_INSTALLATION_NICK;
    }

    public function isInstallationWizardFinished()
    {
        return Mage::helper('M2ePro/Module_Wizard')->isFinished(
            $this->getWizardInstallationNick()
        );
    }

    // ########################################

    public function getAutocompleteMaxItems()
    {
        $temp = (int)Mage::helper('M2ePro/Module')->getConfig()
                        ->getGroupValue('/view/common/autocomplete/','max_records_quantity');
        return $temp <= 0 ? 100 : $temp;
    }

    // ########################################

    public function getDocumentationUrl()
    {
        return Mage::helper('M2ePro/Module')->getConfig()
                    ->getGroupValue('/view/common/support/', 'documentation_url');
    }

    public function getVideoTutorialsUrl()
    {
        return Mage::helper('M2ePro/Module')->getConfig()
                    ->getGroupValue('/view/common/support/', 'video_tutorials_url');
    }

    // ########################################

    public function prepareMenu(array $menuArray)
    {
        if (!Mage::getSingleton('admin/session')->isAllowed(self::MENU_ROOT_NODE_NICK)) {
            return $menuArray;
        }

        if (count(Mage::helper('M2ePro/View_Common_Component')->getActiveComponents()) <= 0) {
            unset($menuArray[self::MENU_ROOT_NODE_NICK]);
            return $menuArray;
        }

        $tempTitle = $this->getMenuRootNodeLabel();
        !empty($tempTitle) && $menuArray[self::MENU_ROOT_NODE_NICK]['label'] = $tempTitle;

        // Add wizard menu item
        //---------------------------------
        /* @var $wizardHelper Ess_M2ePro_Helper_Module_Wizard */
        $wizardHelper = Mage::helper('M2ePro/Module_Wizard');

        $activeBlocker = $wizardHelper->getActiveBlockerWizard(Ess_M2ePro_Helper_View_Common::NICK);

        if ($activeBlocker) {

            unset($menuArray[self::MENU_ROOT_NODE_NICK]['children']);
            unset($menuArray[self::MENU_ROOT_NODE_NICK]['click']);

            $menuArray[self::MENU_ROOT_NODE_NICK]['url'] = Mage::helper('adminhtml')->getUrl(
                'M2ePro/adminhtml_wizard_'.$wizardHelper->getNick($activeBlocker).'/index'
            );
            $menuArray[self::MENU_ROOT_NODE_NICK]['last'] = true;

            return $menuArray;
        }
        //---------------------------------

        // Set documentation redirect url
        //---------------------------------
        if (isset($menuArray[self::MENU_ROOT_NODE_NICK]['children']['help']['children']['doc'])) {
            $menuArray[self::MENU_ROOT_NODE_NICK]['children']['help']['children']['doc']['click'] =
                "window.open(this.href, '_blank'); return false;";
            $menuArray[self::MENU_ROOT_NODE_NICK]['children']['help']['children']['doc']['url'] =
                $this->getDocumentationUrl();
        }
        //---------------------------------

        // Set video tutorials redirect url
        //---------------------------------
        if (isset($menuArray[self::MENU_ROOT_NODE_NICK]['children']['help']['children']['tutorial'])) {
            $menuArray[self::MENU_ROOT_NODE_NICK]['children']['help']['children']['tutorial']['click'] =
                "window.open(this.href, '_blank'); return false;";
            $menuArray[self::MENU_ROOT_NODE_NICK]['children']['help']['children']['tutorial']['url'] =
                $this->getVideoTutorialsUrl();
        }
        //---------------------------------

        return $menuArray;
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_LockItem extends Ess_M2ePro_Model_Abstract
{
    private $nick = 'undefined';
    private $maxInactiveTime = 1800; // 30 min

    //####################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/LockItem');
    }

    //####################################

    public function setNick($value)
    {
        $this->nick = $value;
    }

    public function getNick()
    {
        return $this->nick;
    }

    // -----------------------------------

    public function setMaxInactiveTime($value)
    {
        $this->maxInactiveTime = (int)$value;
    }

    public function getMaxInactiveTime()
    {
        return $this->maxInactiveTime;
    }

    //####################################

    public function create($parentId = NULL)
    {
        $data = array(
            'nick' => $this->nick,
            'parent_id' => $parentId
        );

        Mage::getModel('M2ePro/LockItem')->setData($data)->save();

        return true;
    }

    public function remove()
    {
        /** @var $lockModel Ess_M2ePro_Model_LockItem **/
        $lockModel = Mage::getModel('M2ePro/LockItem')->load($this->nick,'nick');

        if (!$lockModel->getId()) {
            return false;
        }

        $childrenCollection = Mage::getModel('M2ePro/LockItem')->getCollection();
        $childrenCollection->addFieldToFilter('parent_id', $lockModel->getId());

        foreach ($childrenCollection->getItems() as $childLockModel) {
            /** @var $childLockModel Ess_M2ePro_Model_LockItem **/
            $childLockModel = Mage::getModel('M2ePro/LockItem')->load($childLockModel->getId());
            $childLockModel->setNick($childLockModel->getData('nick'));
            $childLockModel->getId() && $childLockModel->remove();
        }

        $lockModel->delete();

        return true;
    }

    //-----------------------------------

    public function isExist()
    {
        /** @var $lockModel Ess_M2ePro_Model_LockItem **/
        $lockModel = Mage::getModel('M2ePro/LockItem')->load($this->nick,'nick');

        if (!$lockModel->getId()) {
            return false;
        }

        $currentTimestamp = Mage::helper('M2ePro')->getCurrentGmtDate(true);
        $updateTimestamp = strtotime($lockModel->getData('update_date'));

        if ($updateTimestamp < $currentTimestamp - $this->getMaxInactiveTime()) {
            $lockModel->delete();
            return false;
        }

        return true;
    }

    public function activate()
    {
        /** @var $lockModel Ess_M2ePro_Model_LockItem **/
        $lockModel = Mage::getModel('M2ePro/LockItem')->load($this->nick,'nick');

        if (!$lockModel->getId()) {
            return false;
        }

        $parentId = $lockModel->getData('parent_id');

        if (!is_null($parentId)) {
            /** @var $parentLockModel Ess_M2ePro_Model_LockItem **/
            $parentLockModel = Mage::getModel('M2ePro/LockItem')->load($parentId);
            $parentLockModel->setNick($parentLockModel->getData('nick'));
            $parentLockModel->getId() && $parentLockModel->activate();
        }

        if ($lockModel->getData('kill_now')) {
            $this->remove();
            exit('kill now.');
        }

        $lockModel->setData('data',$lockModel->getData('data'))->save();

        return true;
    }

    //####################################

    public function getRealId()
    {
        /** @var $lockModel Ess_M2ePro_Model_LockItem **/
        $lockModel = Mage::getModel('M2ePro/LockItem')->load($this->nick,'nick');
        return $lockModel->getId() ? $lockModel->getId() : NULL;
    }

    //-----------------------------------

    public function addContentData($key, $value)
    {
        /** @var $lockModel Ess_M2ePro_Model_LockItem **/
        $lockModel = Mage::getModel('M2ePro/LockItem')->load($this->nick,'nick');

        if (!$lockModel->getId()) {
            return false;
        }

        $data = $lockModel->getData('data');
        if (!empty($data)) {
            $data = json_decode($data, true);
        } else {
            $data = array();
        }

        $data[$key] = $value;

        $lockModel->setData('data', json_encode($data));
        $lockModel->save();

        return true;
    }

    public function setContentData(array $data)
    {
        /** @var $lockModel Ess_M2ePro_Model_LockItem **/
        $lockModel = Mage::getModel('M2ePro/LockItem')->load($this->nick,'nick');

        if (!$lockModel->getId()) {
            return false;
        }

        $lockModel->setData('data',json_encode($data))->save();

        return true;
    }

    //-----------------------------------

    public function getContentData($key = NULL)
    {
        /** @var $lockModel Ess_M2ePro_Model_LockItem **/
        $lockModel = Mage::getModel('M2ePro/LockItem')->load($this->nick,'nick');

        if (!$lockModel->getId()) {
            return NULL;
        }

        if ($lockModel->getData('data') == '') {
            return NULL;
        }

        $data = json_decode($lockModel->getData('data'),true);

        if (is_null($key)) {
            return $data;
        }

        if (isset($data[$key])) {
            return $data[$key];
        }

        return NULL;
    }

    //####################################

    public function makeShutdownFunction()
    {
        if (!$this->isExist()) {
            return false;
        }

        $functionCode = "\$object = Mage::getModel('M2ePro/LockItem');
                         \$object->setNick('".$this->nick."');
                         \$object->remove();";

        $shutdownDeleteFunction = create_function('', $functionCode);
        register_shutdown_function($shutdownDeleteFunction);

        return true;
    }

    //####################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_Mysql4_LockItem
    extends Ess_M2ePro_Model_Mysql4_Abstract
{
    // ########################################

    public function _construct()
    {
        $this->_init('M2ePro/LockItem', 'id');
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_OperationHistory extends Ess_M2ePro_Model_Abstract
{
    const MAX_LIFETIME_INTERVAL = 864000; // 10 days

    /**
     * @var Ess_M2ePro_Model_OperationHistory
     */
    private $object = NULL;

    //####################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/OperationHistory');
    }

    //####################################

    public function setObject($value)
    {
        if (is_object($value)) {
            $this->object = $value;
        } else {
            $this->object = Mage::getModel('M2ePro/OperationHistory')->load($value);
            !$this->object->getId() && $this->object = NULL;
        }

        return $this;
    }

    /**
     * @return Ess_M2ePro_Model_OperationHistory
     */
    public function getObject()
    {
        return $this->object;
    }

    //####################################

    public function start($nick, $parentId = NULL, $initiator = Ess_M2ePro_Helper_Data::INITIATOR_UNKNOWN)
    {
        $data = array(
            'nick' => $nick,
            'parent_id' => $parentId,
            'initiator' => $initiator,
            'start_date' => Mage::helper('M2ePro')->getCurrentGmtDate()
        );

        $this->object = Mage::getModel('M2ePro/OperationHistory')->setData($data)->save();

        return true;
    }

    public function stop()
    {
        if (is_null($this->object) || $this->object->getData('end_date')) {
            return false;
        }

        $this->object->setData('end_date',Mage::helper('M2ePro')->getCurrentGmtDate())->save();

        return true;
    }

    //####################################

    public function setContentData($key, $value)
    {
        if (is_null($this->object)) {
            return false;
        }

        $data = array();
        if ($this->object->getData('data') != '') {
            $data = json_decode($this->object->getData('data'),true);
        }

        $data[$key] = $value;
        $this->object->setData('data',json_encode($data))->save();

        return true;
    }

    public function getContentData($key)
    {
        if (is_null($this->object)) {
            return NULL;
        }

        if ($this->object->getData('data') == '') {
            return NULL;
        }

        $data = json_decode($this->object->getData('data'),true);

        if (isset($data[$key])) {
            return $data[$key];
        }

        return NULL;
    }

    //####################################

    public function cleanOldData()
    {
        $minDate = new DateTime('now', new DateTimeZone('UTC'));
        $minDate->modify('-'.self::MAX_LIFETIME_INTERVAL.' seconds');

        Mage::getSingleton('core/resource')->getConnection('core_write')
                ->delete(
                    Mage::getSingleton('core/resource')->getTableName('m2epro_operation_history'),
                    array(
                        '`create_date` <= ?' => $minDate->format('Y-m-d H:i:s')
                    )
            );
    }

    public function makeShutdownFunction()
    {
        if (is_null($this->object)) {
            return false;
        }

        $functionCode =
            '$object = Mage::getModel(\'M2ePro/OperationHistory\');
             $object->setObject('.$this->object->getId().');

             if(!$object->stop()) {
                return;
             }

             $collection = $object->getCollection()
                     ->addFieldToFilter(\'parent_id\', '.$this->object->getId().');

             if ($collection->getSize()) {
                return;
             }

             $error = error_get_last();

             if (is_null($error)) {
                 return;
             }

             if (in_array((int)$error[\'type\'], array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR))) {
                 $stackTrace = @debug_backtrace(false);
                 $object->setContentData(\'fatal_error\',array(
                    \'message\' => $error[\'message\'],
                    \'file\' => $error[\'file\'],
                    \'line\' => $error[\'line\'],
                    \'trace\' => Mage::helper(\'M2ePro/Module_Exception\')->getFatalStackTraceInfo($stackTrace)
                 ));
             }';

        $shutdownDeleteFunction = create_function('', $functionCode);
        register_shutdown_function($shutdownDeleteFunction);

        return true;
    }

    //####################################

    public function getDataInfo($nestingLevel = 0)
    {
        if (is_null($this->object)) {
            return NULL;
        }

        $offset = str_repeat(' ', $nestingLevel * 7);
        $separationLine = str_repeat('#',80 - strlen($offset));

        $nick = strtoupper($this->getObject()->getData('nick'));

        $contentData = (array)json_decode($this->getObject()->getData('data'),true);
        $contentData = preg_replace('/^/m', "{$offset}", print_r($contentData, true));

        return <<<INFO
{$offset}{$nick}
{$offset}Start Date: {$this->getObject()->getData('start_date')}
{$offset}End Date: {$this->getObject()->getData('end_date')}
{$offset}Total Time: {$this->getTotalTime()}

{$offset}{$separationLine}
{$contentData}
{$offset}{$separationLine}

INFO;
    }

    public function getFullDataInfo($nestingLevel = 0)
    {
        if (is_null($this->object)) {
            return NULL;
        }

        $dataInfo = $this->getDataInfo($nestingLevel);

        $childObjects = $this->getCollection()
                             ->addFieldToFilter('parent_id', $this->getObject()->getId())
                             ->setOrder('start_date', 'ASC');

        $childObjects->getSize() > 0 && $nestingLevel++;

        foreach ($childObjects as $item) {

            $object = Mage::getModel('M2ePro/OperationHistory');
            $object->setObject($item);

            $dataInfo .= $object->getFullDataInfo($nestingLevel);
        }

        return $dataInfo;
    }

    //------------------------------------

    protected function getTotalTime()
    {
        $totalTime = strtotime($this->getObject()->getData('end_date')) -
                     strtotime($this->getObject()->getData('start_date'));

        if ($totalTime < 0) {
            return 'n/a';
        }

        $minutes = (int)($totalTime / 60);
        $minutes < 10 && $minutes = '0'.$minutes;

        $seconds = $totalTime - $minutes * 60;
        $seconds < 10 && $seconds = '0'.$seconds;

        return "{$minutes}:{$seconds}";
    }

    //####################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_Mysql4_OperationHistory
    extends Ess_M2ePro_Model_Mysql4_Abstract
{
    // ########################################

    public function _construct()
    {
        $this->_init('M2ePro/OperationHistory', 'id');
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Cron_Task_Abstract
{
    private $lockItem = NULL;
    private $operationHistory = NULL;

    private $parentLockItem = NULL;
    private $parentOperationHistory = NULL;

    private $initiator = Ess_M2ePro_Helper_Data::INITIATOR_UNKNOWN;

    //####################################

    public function process()
    {
        $this->initialize();
        $this->updateLastAccess();

        if (!$this->isPossibleToRun()) {
            return true;
        }

        $this->updateLastRun();
        $this->beforeStart();

        $result = true;

        try {

            $tempResult = $this->performActions();

            if (!is_null($tempResult) && !$tempResult) {
                $result = false;
            }

            $this->getLockItem()->activate();

        } catch (Exception $exception) {

            $result = false;

            Mage::helper('M2ePro/Module_Exception')->process($exception);

            $this->getOperationHistory()->setContentData('exception', array(
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'trace'   => $exception->getTraceAsString(),
            ));
        }

        $this->afterEnd();

        return $result;
    }

    // ----------------------------------

    abstract protected function getNick();

    abstract protected function getMaxMemoryLimit();

    // ----------------------------------

    abstract protected function performActions();

    //####################################

    public function setParentLockItem(Ess_M2ePro_Model_LockItem $object)
    {
        $this->parentLockItem = $object;
    }

    /**
     * @return Ess_M2ePro_Model_LockItem
     */
    public function getParentLockItem()
    {
        return $this->parentLockItem;
    }

    // -----------------------------------

    public function setParentOperationHistory(Ess_M2ePro_Model_OperationHistory $object)
    {
        $this->parentOperationHistory = $object;
    }

    /**
     * @return Ess_M2ePro_Model_OperationHistory
     */
    public function getParentOperationHistory()
    {
        return $this->parentOperationHistory;
    }

    // -----------------------------------

    public function setInitiator($value)
    {
        $this->initiator = (int)$value;
    }

    public function getInitiator()
    {
        return $this->initiator;
    }

    //####################################

    protected function initialize()
    {
        Mage::helper('M2ePro/Client')->setMemoryLimit($this->getMaxMemoryLimit());
        Mage::helper('M2ePro/Module_Exception')->setFatalErrorHandler();
    }

    protected function updateLastAccess()
    {
        $this->setConfigValue('last_access',Mage::helper('M2ePro')->getCurrentGmtDate());
    }

    protected function isPossibleToRun()
    {
        $currentTimeStamp = Mage::helper('M2ePro')->getCurrentGmtDate(true);

        $startFrom = $this->getConfigValue('start_from');
        $startFrom = !empty($startFrom) ? strtotime($startFrom) : $currentTimeStamp;

        return $this->isModeEnabled() &&
               $startFrom <= $currentTimeStamp &&
               $this->isIntervalExceeded() &&
               !$this->getLockItem()->isExist();
    }

    protected function updateLastRun()
    {
        $this->setConfigValue('last_run',Mage::helper('M2ePro')->getCurrentGmtDate());
    }

    // -----------------------------------

    protected function beforeStart()
    {
        $lockItemParentId = $this->getParentLockItem() ? $this->getParentLockItem()->getRealId() : NULL;
        $this->getLockItem()->create($lockItemParentId);
        $this->getLockItem()->makeShutdownFunction();

        $operationHistoryParentId = $this->getParentOperationHistory() ?
                $this->getParentOperationHistory()->getObject()->getId() : NULL;
        $this->getOperationHistory()->start('cron_'.$this->getNick(),
                                            $operationHistoryParentId,
                                            $this->getInitiator());
        $this->getOperationHistory()->makeShutdownFunction();
    }

    protected function afterEnd()
    {
        $this->getOperationHistory()->stop();
        $this->getLockItem()->remove();
    }

    //####################################

    protected function getLockItem()
    {
        if (is_null($this->lockItem)) {
            $this->lockItem = Mage::getModel('M2ePro/LockItem');
            $this->lockItem->setNick('cron_'.$this->getNick());
        }
        return $this->lockItem;
    }

    protected function getOperationHistory()
    {
        if (is_null($this->operationHistory)) {
            $this->operationHistory = Mage::getModel('M2ePro/OperationHistory');
        }
        return $this->operationHistory;
    }

    // -----------------------------------

    protected function isModeEnabled()
    {
        return (bool)$this->getConfigValue('mode');
    }

    protected function isIntervalExceeded()
    {
        $lastRun = $this->getConfigValue('last_run');

        if (is_null($lastRun)) {
            return true;
        }

        $interval = (int)$this->getConfigValue('interval');
        $currentTimeStamp = Mage::helper('M2ePro')->getCurrentGmtDate(true);

        return $currentTimeStamp > strtotime($lastRun) + $interval;
    }

    //####################################

    private function getConfig()
    {
        return Mage::helper('M2ePro/Module')->getConfig();
    }

    private function getConfigGroup()
    {
        return '/cron/task/'.$this->getNick().'/';
    }

    // ----------------------------------------

    private function getConfigValue($key)
    {
        return $this->getConfig()->getGroupValue($this->getConfigGroup(), $key);
    }

    private function setConfigValue($key, $value)
    {
        return $this->getConfig()->setGroupValue($this->getConfigGroup(), $key, $value);
    }

    //####################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

final class Ess_M2ePro_Model_Cron_Task_LogsClearing extends Ess_M2ePro_Model_Cron_Task_Abstract
{
    const NICK = 'logs_clearing';
    const MAX_MEMORY_LIMIT = 128;

    //####################################

    protected function getNick()
    {
        return self::NICK;
    }

    protected function getMaxMemoryLimit()
    {
        return self::MAX_MEMORY_LIMIT;
    }

    //####################################

    protected function performActions()
    {
        /** @var $tempModel Ess_M2ePro_Model_Log_Clearing */
        $tempModel = Mage::getModel('M2ePro/Log_Clearing');

        $tempModel->clearOldRecords(Ess_M2ePro_Model_Log_Clearing::LOG_LISTINGS);
        $tempModel->clearOldRecords(Ess_M2ePro_Model_Log_Clearing::LOG_OTHER_LISTINGS);
        $tempModel->clearOldRecords(Ess_M2ePro_Model_Log_Clearing::LOG_SYNCHRONIZATIONS);
        $tempModel->clearOldRecords(Ess_M2ePro_Model_Log_Clearing::LOG_ORDERS);

        return true;
    }

    //####################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

final class Ess_M2ePro_Model_Cron_Task_Servicing extends Ess_M2ePro_Model_Cron_Task_Abstract
{
    const NICK = 'servicing';

    //####################################

    protected function getNick()
    {
        return self::NICK;
    }

    protected function getMaxMemoryLimit()
    {
        return Ess_M2ePro_Model_Servicing_Dispatcher::MAX_MEMORY_LIMIT;
    }

    //####################################

    protected function performActions()
    {
        return Mage::getModel('M2ePro/Servicing_Dispatcher')->process();
    }

    //####################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

final class Ess_M2ePro_Model_Servicing_Dispatcher
{
    const DEFAULT_INTERVAL = 3600;
    const MAX_MEMORY_LIMIT = 256;

    private $params = array();
    private $forceTasksRunning = false;

    // ########################################

    public function getForceTasksRunning()
    {
        return $this->forceTasksRunning;
    }

    public function setForceTasksRunning($value)
    {
        $this->forceTasksRunning = (bool)$value;
    }

    // ----------------------------------------

    public function getParams()
    {
        return $this->params;
    }

    public function setParams(array $params = array())
    {
        $this->params = $params;
    }

    // ########################################

    public function process($minInterval = NULL)
    {
        $timeLastUpdate = $this->getLastUpdateTimestamp();

        if (!is_null($minInterval) &&
            $timeLastUpdate + (int)$minInterval > Mage::helper('M2ePro')->getCurrentGmtDate(true)) {
            return false;
        }

        $this->setLastUpdateDateTime();
        return $this->processTasks($this->getRegisteredTasks());
    }

    // ----------------------------------------

    public function processTask($allowedTask)
    {
        return $this->processTasks(array($allowedTask));
    }

    public function processTasks(array $allowedTasks = array())
    {
        Mage::helper('M2ePro/Client')->setMemoryLimit(self::MAX_MEMORY_LIMIT);
        Mage::helper('M2ePro/Module_Exception')->setFatalErrorHandler();

        $dispatcherObject = Mage::getModel('M2ePro/Connector_M2ePro_Dispatcher');
        $connectorObj = $dispatcherObject->getVirtualConnector('servicing','update','data',
                                                               $this->getRequestData($allowedTasks));

        $responseData = $dispatcherObject->process($connectorObj);

        if (!is_array($responseData)) {
            return false;
        }

        $this->dispatchResponseData($responseData,$allowedTasks);

        return true;
    }

    // ########################################

    private function getRequestData(array $allowedTasks = array())
    {
        $requestData = array();

        foreach ($this->getRegisteredTasks() as $taskName) {

            if (!in_array($taskName,$allowedTasks)) {
                continue;
            }

            /** @var $taskModel Ess_M2ePro_Model_Servicing_Task */
            $taskModel = Mage::getModel('M2ePro/Servicing_Task_'.ucfirst($taskName));
            $taskModel->setParams($this->getParams());

            if (!$this->getForceTasksRunning() && !$taskModel->isAllowed()) {
                continue;
            }

            $requestData[$taskModel->getPublicNick()] = $taskModel->getRequestData();
        }

        return $requestData;
    }

    private function dispatchResponseData(array $responseData, array $allowedTasks = array())
    {
        foreach ($this->getRegisteredTasks() as $taskName) {

            if (!in_array($taskName,$allowedTasks)) {
                continue;
            }

            /** @var $taskModel Ess_M2ePro_Model_Servicing_Task */
            $taskModel = Mage::getModel('M2ePro/Servicing_Task_'.ucfirst($taskName));
            $taskModel->setParams($this->getParams());

            if (!isset($responseData[$taskModel->getPublicNick()]) ||
                !is_array($responseData[$taskModel->getPublicNick()])) {
                continue;
            }

            $taskModel->processResponseData($responseData[$taskModel->getPublicNick()]);
        }
    }

    // ########################################

    private function getRegisteredTasks()
    {
        return array(
            'license',
            'messages',
            'settings',
            'backups',
            'exceptions',
            'marketplaces',
            'cron'
        );
    }

    // ----------------------------------------

    private function getLastUpdateTimestamp()
    {
        $lastUpdateDate = Mage::helper('M2ePro/Module')->getCacheConfig()
                            ->getGroupValue('/servicing/','last_update_time');

        if (is_null($lastUpdateDate)) {
            return Mage::helper('M2ePro')->getCurrentGmtDate(true) - 3600*24*30;
        }

        return Mage::helper('M2ePro')->getDate($lastUpdateDate,true);
    }

    private function setLastUpdateDateTime()
    {
        Mage::helper('M2ePro/Module')->getCacheConfig()
            ->setGroupValue('/servicing/', 'last_update_time',
                            Mage::helper('M2ePro')->getCurrentGmtDate());
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

final class Ess_M2ePro_Model_Cron_Task_Processing extends Ess_M2ePro_Model_Cron_Task_Abstract
{
    const NICK = 'processing';

    //####################################

    protected function getNick()
    {
        return self::NICK;
    }

    protected function getMaxMemoryLimit()
    {
        return Ess_M2ePro_Model_Processing_Dispatcher::MAX_MEMORY_LIMIT;
    }

    //####################################

    protected function performActions()
    {
        $dispatcher = Mage::getModel('M2ePro/Processing_Dispatcher');

        $dispatcher->setInitiator($this->getInitiator());
        $dispatcher->setParentLockItem($this->getLockItem());
        $dispatcher->setParentOperationHistory($this->getOperationHistory());

        return $dispatcher->process();
    }

    //####################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

final class Ess_M2ePro_Model_Processing_Dispatcher
{
    const MAX_MEMORY_LIMIT = 512;

    const MAX_REQUESTS_PER_ONE_TIME = 3;
    const MAX_PROCESSING_IDS_PER_REQUEST = 100;

    private $lockItem = NULL;
    private $operationHistory = NULL;

    private $parentLockItem = NULL;
    private $parentOperationHistory = NULL;

    private $initiator = Ess_M2ePro_Helper_Data::INITIATOR_UNKNOWN;

    // ########################################

    public function process()
    {
        Mage::helper('M2ePro/Client')->setMemoryLimit(self::MAX_MEMORY_LIMIT);
        Mage::helper('M2ePro/Module_Exception')->setFatalErrorHandler();

        if ($this->getLockItem()->isExist()) {
            return false;
        }

        $lockItemParentId = $this->getParentLockItem() ? $this->getParentLockItem()->getRealId() : NULL;
        $this->getLockItem()->create($lockItemParentId);
        $this->getLockItem()->makeShutdownFunction();

        $this->getOperationHistory()->cleanOldData();

        $operationHistoryParentId = $this->getParentOperationHistory() ?
                $this->getParentOperationHistory()->getObject()->getId() : NULL;
        $this->getOperationHistory()->start('processing',
                                            $operationHistoryParentId,
                                            $this->getInitiator());
        $this->getOperationHistory()->makeShutdownFunction();

        $result = true;

        try {

            $this->clearOldProcessingRequests();
            $this->processProcessingRequests();

        } catch (Exception $exception) {

            $result = false;

            Mage::helper('M2ePro/Module_Exception')->process($exception);

            $this->getOperationHistory()->setContentData('exception', array(
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'trace'   => $exception->getTraceAsString(),
            ));
        }

        $this->getOperationHistory()->stop();
        $this->getLockItem()->remove();

        return $result;
    }

    // ########################################

    public function setInitiator($value)
    {
        $this->initiator = (int)$value;
    }

    public function getInitiator()
    {
        return $this->initiator;
    }

    // -----------------------------------

    public function setParentLockItem(Ess_M2ePro_Model_LockItem $object)
    {
        $this->parentLockItem = $object;
    }

    /**
     * @return Ess_M2ePro_Model_LockItem
     */
    public function getParentLockItem()
    {
        return $this->parentLockItem;
    }

    // -----------------------------------

    public function setParentOperationHistory(Ess_M2ePro_Model_OperationHistory $object)
    {
        $this->parentOperationHistory = $object;
    }

    /**
     * @return Ess_M2ePro_Model_OperationHistory
     */
    public function getParentOperationHistory()
    {
        return $this->parentOperationHistory;
    }

    // ########################################

    protected function getLockItem()
    {
        if (is_null($this->lockItem)) {
            $this->lockItem = Mage::getModel('M2ePro/LockItem');
            $this->lockItem->setNick('processing');
        }
        return $this->lockItem;
    }

    protected function getOperationHistory()
    {
        if (is_null($this->operationHistory)) {
            $this->operationHistory = Mage::getModel('M2ePro/OperationHistory');
        }
        return $this->operationHistory;
    }

    // ########################################

    private function clearOldProcessingRequests()
    {
        $currentDateTime = Mage::helper('M2ePro')->getCurrentGmtDate();

        /** @var $collection Mage_Core_Model_Mysql4_Collection_Abstract */
        $collection = Mage::getModel('M2ePro/Processing_Request')->getCollection();
        $collection->getSelect()->where("expiration_date < '{$currentDateTime}'");

        $this->executeFailedProcessingRequests($collection->getItems());
    }

    private function processProcessingRequests()
    {
        $collection = Mage::getModel('M2ePro/Processing_Request')->getCollection();
        $collection->addFieldToSelect('component');
        $collection->getSelect()->distinct();
        $collection->load();

        foreach ($collection->getItems() as $component) {

            $component = $component->getData('component');

            /** @var $collection Mage_Core_Model_Mysql4_Collection_Abstract */
            $collection = Mage::getModel('M2ePro/Processing_Request')->getCollection();
            $collection->addFieldToFilter('component',$component);
            $processingRequests = $collection->getItems();

            $processingSingleObjects = array();
            $processingPartialObjects = array();

            foreach ($processingRequests as $processingRequest) {
                /** @var $processingRequest Ess_M2ePro_Model_Processing_Request */
                if ($processingRequest->isPerformTypeSingle()) {
                    $processingSingleObjects[] = $processingRequest;
                } else {
                    $processingPartialObjects[] = $processingRequest;
                }
            }

            $processingIds = array();
            $processingObjects = array();

            foreach ($processingSingleObjects as $processingRequest) {
                /** @var $processingRequest Ess_M2ePro_Model_Processing_Request */
                $processingIds[] = $processingRequest->getProcessingHash();
                if (!isset($processingObjects[$processingRequest->getProcessingHash()])) {
                    $processingObjects[$processingRequest->getProcessingHash()] = array();
                }
                $processingObjects[$processingRequest->getProcessingHash()][] = $processingRequest;
            }

            $this->processSingleProcessingRequests($component,array_unique($processingIds),$processingObjects);

            $processingIds = array();
            $processingObjects = array();

            foreach ($processingPartialObjects as $processingRequest) {
                /** @var $processingRequest Ess_M2ePro_Model_Processing_Request */
                $processingIds[] = $processingRequest->getProcessingHash();
                if (!isset($processingObjects[$processingRequest->getProcessingHash()])) {
                    $processingObjects[$processingRequest->getProcessingHash()] = array();
                }
                $processingObjects[$processingRequest->getProcessingHash()][] = $processingRequest;
            }

            $this->processPartialProcessingRequests($component,array_unique($processingIds),$processingObjects);
        }
    }

    // ########################################

    private function processSingleProcessingRequests($component, array $processingIds, array $processingObjects)
    {
        if (count($processingIds) <= 0) {
            return;
        }

        $processingIdsParts = array_chunk($processingIds,self::MAX_PROCESSING_IDS_PER_REQUEST);

        foreach ($processingIdsParts as $processingIds) {

            if (count($processingIds) <= 0) {
                continue;
            }

            // send parts to the server
            $dispatcherObject = Mage::getModel('M2ePro/Connector_'.ucfirst($component).'_Dispatcher');
            $connectorObj = $dispatcherObject->getVirtualConnector('processing','get','results',
                                                                   array('processing_ids'=>$processingIds),
                                                                   'results', NULL, NULL);

            $results = $dispatcherObject->process($connectorObj);

            if (empty($results)) {
                continue;
            }

            // process results
            foreach ($processingIds as $processingId) {

                if (!isset($results[$processingId]) || !isset($results[$processingId]['status']) ||
                    $results[$processingId]['status'] == Ess_M2ePro_Model_Processing_Request::STATUS_NOT_FOUND) {
                    $this->executeFailedProcessingRequests($processingObjects[$processingId]);
                    continue;
                }

                if ($results[$processingId]['status'] != Ess_M2ePro_Model_Processing_Request::STATUS_COMPLETE) {
                    continue;
                }

                !isset($results[$processingId]['data']) && $results[$processingId]['data'] = array();
                !isset($results[$processingId]['messages']) && $results[$processingId]['messages'] = array();

                $this->executeCompletedProcessingRequests($processingObjects[$processingId],
                                                          (array)$results[$processingId]['data'],
                                                          (array)$results[$processingId]['messages']);
            }
        }

        $this->getLockItem()->activate();
    }

    //----------------------------------------

    private function processPartialProcessingRequests($component, array $processingIds, array $processingObjects)
    {
        if (count($processingIds) <= 0) {
            return;
        }

        foreach ($processingIds as $processingId) {

            $nextPart = NULL;

            foreach ($processingObjects[$processingId] as $key => $processingRequest) {

                /** @var $processingRequest Ess_M2ePro_Model_Processing_Request */

                if (is_null($processingRequest->getNextPart())) {
                    unset($processingObjects[$processingId][$key]);
                    continue;
                }

                $tempNextPart = (int)$processingRequest->getNextPart();

                if (is_null($nextPart) || $tempNextPart < $nextPart) {
                    $nextPart = $tempNextPart;
                }
            }

            if (empty($processingObjects[$processingId])) {
                continue;
            }

            $this->processPartialProcessingRequestsNextPart(
                $component, $processingId, array_values($processingObjects[$processingId]), $nextPart, 1
            );
        }
    }

    private function processPartialProcessingRequestsNextPart($component, $processingId, array $processingRequests,
                                                              $necessaryPart, $countCycles = 1)
    {
        $params = array(
            'processing_id' => $processingId,
            'necessary_parts' => array(
                $processingId => (int)$necessaryPart
            )
        );

        // send parts to the server
        $dispatcherObject = Mage::getModel('M2ePro/Connector_'.ucfirst($component).'_Dispatcher');
        $connectorObj = $dispatcherObject->getVirtualConnector('processing','get','results',
                                                               $params, 'results', NULL, NULL);

        $results = $dispatcherObject->process($connectorObj);

        if (empty($results)) {
            return;
        }

        if (!isset($results[$processingId]) || !isset($results[$processingId]['status']) ||
            $results[$processingId]['status'] == Ess_M2ePro_Model_Processing_Request::STATUS_NOT_FOUND) {
            $this->executeFailedProcessingRequests($processingRequests);
            return;
        }

        if ($results[$processingId]['status'] != Ess_M2ePro_Model_Processing_Request::STATUS_COMPLETE) {
            return;
        }

        !isset($results[$processingId]['data']) && $results[$processingId]['data'] = array();
        !isset($results[$processingId]['messages']) && $results[$processingId]['messages'] = array();

        $nextPart = NULL;
        if (isset($results[$processingId]['next_part']) && (int)$results[$processingId]['next_part'] >= 2) {
            $nextPart = (int)$results[$processingId]['next_part'];
        }

        $nextProcessingRequests = array();
        foreach ($processingRequests as $processingRequest) {

            /** @var $processingRequest Ess_M2ePro_Model_Processing_Request */

            $responserRunner = $processingRequest->getResponserRunner();

            if ((int)$processingRequest->getNextPart() == $necessaryPart) {

                $results[$processingId]['data']['next_part'] = $nextPart;

                $processResult = $responserRunner->process(
                    (array)$results[$processingId]['data'], (array)$results[$processingId]['messages']
                );

                if (!$processResult) {
                    continue;
                }

                $processingRequest->setData('next_part', $nextPart)->save();
            }

            $this->getLockItem()->activate();

            if (is_null($nextPart)) {
                $responserRunner->complete();
            } else {
                $nextProcessingRequests[] = $processingRequest;
            }
        }

        if (is_null($nextPart) || empty($nextProcessingRequests)) {
            return;
        }

        if ($countCycles >= self::MAX_REQUESTS_PER_ONE_TIME) {
            return;
        }

        unset($results, $dispatcherObject, $processingRequests);

        $this->processPartialProcessingRequestsNextPart(
            $component, $processingId,
            $nextProcessingRequests,
            $nextPart, $countCycles + 1
        );
    }

    // ########################################

    private function executeCompletedProcessingRequests($processingRequests, array $data, array $messages = array())
    {
        if (!is_array($processingRequests)) {
            $processingRequests = array($processingRequests);
        }

        foreach ($processingRequests as $processingRequest) {

            if (!($processingRequest instanceof Ess_M2ePro_Model_Processing_Request)) {
                continue;
            }

            $responserRunner = $processingRequest->getResponserRunner();

            $responserRunner->process($data, $messages) && $responserRunner->complete();
        }
    }

    private function executeFailedProcessingRequests($processingRequests)
    {
        if (!is_array($processingRequests)) {
            $processingRequests = array($processingRequests);
        }

        $message = 'Request wait timeout exceeded.';

        foreach ($processingRequests as $processingRequest) {

            if (!($processingRequest instanceof Ess_M2ePro_Model_Processing_Request)) {
                continue;
            }

            $processingRequest->getResponserRunner()->complete($message);
        }
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_Processing_Request extends Ess_M2ePro_Model_Abstract
{
    const PERFORM_TYPE_SINGLE  = 1;
    const PERFORM_TYPE_PARTIAL = 2;

    const STATUS_NOT_FOUND  = 'not_found';
    const STATUS_COMPLETE   = 'completed';
    const STATUS_PROCESSING = 'processing';

    const MAX_LIFE_TIME_INTERVAL = 86400; // 1 day

    //####################################

    /** @var Ess_M2ePro_Model_Connector_ResponserRunner $responserRunner */
    private $responserRunner = null;

    //####################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/Processing_Request');
    }

    //####################################

    public function getComponent()
    {
        return $this->getData('component');
    }

    public function getPerformType()
    {
        return (int)$this->getData('perform_type');
    }

    public function getNextPart()
    {
        return $this->getData('next_part');
    }

    //------------------------------------

    public function getHash()
    {
        return $this->getData('hash');
    }

    public function getProcessingHash()
    {
        return $this->getData('processing_hash');
    }

    //------------------------------------

    public function getRequestBody()
    {
        return $this->getData('request_body');
    }

    public function getResponserModel()
    {
        return $this->getData('responser_model');
    }

    public function getResponserParams()
    {
        return $this->getData('responser_params');
    }

    //------------------------------------

    public function isPerformTypeSingle()
    {
        return $this->getPerformType() == self::PERFORM_TYPE_SINGLE;
    }

    public function isPerformTypePartial()
    {
        return $this->getPerformType() == self::PERFORM_TYPE_PARTIAL;
    }

    //####################################

    public function getDecodedRequestBody()
    {
        return @json_decode($this->getRequestBody(),true);
    }

    public function getDecodedResponserParams()
    {
        return @json_decode($this->getResponserParams(),true);
    }

    //####################################

    /**
     * @return Ess_M2ePro_Model_Connector_ResponserRunner
     */
    public function getResponserRunner()
    {
        if (!is_null($this->responserRunner)) {
            return $this->responserRunner;
        }

        $this->responserRunner = Mage::getModel('M2ePro/Connector_ResponserRunner');
        $this->responserRunner->setProcessingRequest($this);

        return $this->responserRunner;
    }

    //####################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_Mysql4_Processing_Request
    extends Ess_M2ePro_Model_Mysql4_Abstract
{
    // ########################################

    public function _construct()
    {
        $this->_init('M2ePro/Processing_Request', 'id');
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_Mysql4_Processing_Request_Collection
    extends Ess_M2ePro_Model_Mysql4_Collection_Abstract
{
    // ########################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/Processing_Request');
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_Mysql4_LockItem_Collection
    extends Ess_M2ePro_Model_Mysql4_Collection_Abstract
{
    // ########################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/LockItem');
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_LockedObject extends Ess_M2ePro_Model_Abstract
{
    //####################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/LockedObject');
    }

    //####################################

    public function getModelName()
    {
        return $this->getData('model_name');
    }

    public function getObjectId()
    {
        return (int)$this->getData('object_id');
    }

    public function getRelatedHash()
    {
        return $this->getData('related_hash');
    }

    public function getTag()
    {
        return $this->getData('tag');
    }

    public function getDescription()
    {
        return $this->getData('description');
    }

    //####################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_Mysql4_LockedObject
    extends Ess_M2ePro_Model_Mysql4_Abstract
{
    // ########################################

    public function _construct()
    {
        $this->_init('M2ePro/LockedObject', 'id');
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

class Ess_M2ePro_Model_Mysql4_LockedObject_Collection
    extends Ess_M2ePro_Model_Mysql4_Collection_Abstract
{
    // ########################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/LockedObject');
    }

    // ########################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

final class Ess_M2ePro_Model_Cron_Task_Synchronization extends Ess_M2ePro_Model_Cron_Task_Abstract
{
    const NICK = 'synchronization';

    //####################################

    protected function getNick()
    {
        return self::NICK;
    }

    protected function getMaxMemoryLimit()
    {
        return Ess_M2ePro_Model_Synchronization_Dispatcher::MAX_MEMORY_LIMIT;
    }

    //####################################

    protected function performActions()
    {
        /** @var $dispatcher Ess_M2ePro_Model_Synchronization_Dispatcher */
        $dispatcher = Mage::getModel('M2ePro/Synchronization_Dispatcher');

        $dispatcher->setParentLockItem($this->getLockItem());
        $dispatcher->setParentOperationHistory($this->getOperationHistory());

        $dispatcher->setAllowedComponents(array(
            Ess_M2ePro_Helper_Component_Ebay::NICK,
            Ess_M2ePro_Helper_Component_Amazon::NICK,
            Ess_M2ePro_Helper_Component_Buy::NICK
        ));

        $dispatcher->setAllowedTasksTypes(array(
            Ess_M2ePro_Model_Synchronization_Task::DEFAULTS,
            Ess_M2ePro_Model_Synchronization_Task::TEMPLATES,
            Ess_M2ePro_Model_Synchronization_Task::ORDERS,
            Ess_M2ePro_Model_Synchronization_Task::FEEDBACKS,
            Ess_M2ePro_Model_Synchronization_Task::OTHER_LISTINGS
        ));

        $dispatcher->setInitiator($this->getInitiator());
        $dispatcher->setParams(array());

        return $dispatcher->process();
    }

    //####################################
}

/*
 * @copyright  Copyright (c) 2013 by  ESS-UA.
 */

final class Ess_M2ePro_Model_Synchronization_Dispatcher
{
    const MAX_MEMORY_LIMIT = 512;

    private $allowedComponents = array();
    private $allowedTasksTypes = array();

    private $lockItem = NULL;
    private $operationHistory = NULL;

    private $parentLockItem = NULL;
    private $parentOperationHistory = NULL;

    private $log = NULL;
    private $params = array();
    private $initiator = Ess_M2ePro_Helper_Data::INITIATOR_UNKNOWN;

    //####################################

    public function process()
    {
        $this->initialize();
        $this->updateLastAccess();

        if (!$this->isPossibleToRun()) {
            return true;
        }

        $this->updateLastRun();
        $this->beforeStart();

        $result = true;

        try {

            // global tasks
            $result = !$this->processTask('Synchronization_Task_Defaults') ? false : $result;

            // components tasks
            $result = !$this->processComponent(Ess_M2ePro_Helper_Component_Ebay::NICK) ? false : $result;
            $result = !$this->processComponent(Ess_M2ePro_Helper_Component_Amazon::NICK) ? false : $result;
            $result = !$this->processComponent(Ess_M2ePro_Helper_Component_Buy::NICK) ? false : $result;

        } catch (Exception $exception) {

            $result = false;

            Mage::helper('M2ePro/Module_Exception')->process($exception);

            $this->getOperationHistory()->setContentData('exception', array(
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'trace'   => $exception->getTraceAsString(),
            ));

            $this->getLog()->addMessage(
                Mage::helper('M2ePro')->__($exception->getMessage()),
                Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH
            );
        }

        $this->afterEnd();

        return $result;
    }

    // ----------------------------------

    protected function processComponent($component)
    {
        if (!in_array($component,$this->getAllowedComponents())) {
            return false;
        }

        return $this->processTask(ucfirst($component).'_Synchronization_Launcher');
    }

    protected function processTask($taskPath)
    {
        $result = $this->makeTask($taskPath)->process();
        return is_null($result) || $result;
    }

    protected function makeTask($taskPath)
    {
        /** @var $task Ess_M2ePro_Model_Synchronization_Task **/
        $task = Mage::getModel('M2ePro/'.$taskPath);

        $task->setParentLockItem($this->getLockItem());
        $task->setParentOperationHistory($this->getOperationHistory());

        $task->setAllowedTasksTypes($this->getAllowedTasksTypes());

        $task->setLog($this->getLog());
        $task->setInitiator($this->getInitiator());
        $task->setParams($this->getParams());

        return $task;
    }

    //####################################

    public function setAllowedComponents(array $components)
    {
        $this->allowedComponents = $components;
    }

    public function getAllowedComponents()
    {
        return $this->allowedComponents;
    }

    // -----------------------------------

    public function setAllowedTasksTypes(array $types)
    {
        $this->allowedTasksTypes = $types;
    }

    public function getAllowedTasksTypes()
    {
        return $this->allowedTasksTypes;
    }

    // -----------------------------------

    public function setParams(array $params)
    {
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }

    // -----------------------------------

    public function setInitiator($value)
    {
        $this->initiator = (int)$value;
    }

    public function getInitiator()
    {
        return $this->initiator;
    }

    // -----------------------------------

    public function setParentLockItem(Ess_M2ePro_Model_LockItem $object)
    {
        $this->parentLockItem = $object;
    }

    /**
     * @return Ess_M2ePro_Model_LockItem
     */
    public function getParentLockItem()
    {
        return $this->parentLockItem;
    }

    // -----------------------------------

    public function setParentOperationHistory(Ess_M2ePro_Model_OperationHistory $object)
    {
        $this->parentOperationHistory = $object;
    }

    /**
     * @return Ess_M2ePro_Model_OperationHistory
     */
    public function getParentOperationHistory()
    {
        return $this->parentOperationHistory;
    }

    //####################################

    protected function initialize()
    {
        Mage::helper('M2ePro/Client')->setMemoryLimit(self::MAX_MEMORY_LIMIT);
        Mage::helper('M2ePro/Module_Exception')->setFatalErrorHandler();
    }

    protected function updateLastAccess()
    {
        $currentDateTime = Mage::helper('M2ePro')->getCurrentGmtDate();
        $this->setConfigValue(NULL,'last_access',$currentDateTime);
    }

    protected function isPossibleToRun()
    {
        return (bool)(int)$this->getConfigValue(NULL,'mode') &&
               !$this->getLockItem()->isExist();
    }

    protected function updateLastRun()
    {
        $currentDateTime = Mage::helper('M2ePro')->getCurrentGmtDate();
        $this->setConfigValue(NULL,'last_run',$currentDateTime);
    }

    // -----------------------------------

    protected function beforeStart()
    {
        $lockItemParentId = $this->getParentLockItem() ? $this->getParentLockItem()->getRealId() : NULL;
        $this->getLockItem()->create($lockItemParentId);
        $this->getLockItem()->makeShutdownFunction();

        $this->getOperationHistory()->cleanOldData();

        $operationHistoryParentId = $this->getParentOperationHistory() ?
                $this->getParentOperationHistory()->getObject()->getId() : NULL;
        $this->getOperationHistory()->start('synchronization',
                                            $operationHistoryParentId,
                                            $this->getInitiator());
        $this->getOperationHistory()->makeShutdownFunction();

        $this->getLog()->setOperationHistoryId($this->getOperationHistory()->getObject()->getId());

        $this->checkAndPrepareProductChange();

        if (in_array(Ess_M2ePro_Model_Synchronization_Task::ORDERS, $this->getAllowedTasksTypes())) {
            Mage::dispatchEvent('m2epro_synchronization_before_start', array());
        }
    }

    protected function afterEnd()
    {
        if (in_array(Ess_M2ePro_Model_Synchronization_Task::ORDERS, $this->getAllowedTasksTypes())) {
            Mage::dispatchEvent('m2epro_synchronization_after_end', array());
        }

        Mage::getModel('M2ePro/ProductChange')->clearLastProcessed(
            $this->getOperationHistory()->getObject()->getData('start_date'),
            (int)$this->getConfigValue('/settings/product_change/', 'max_count_per_one_time')
        );

        $this->getOperationHistory()->stop();
        $this->getLockItem()->remove();
    }

    //####################################

    /**
     * @return Ess_M2ePro_Model_Synchronization_LockItem
     */
    protected function getLockItem()
    {
        if (is_null($this->lockItem)) {
            $this->lockItem = Mage::getModel('M2ePro/Synchronization_LockItem');
        }
        return $this->lockItem;
    }

    /**
     * @return Ess_M2ePro_Model_Synchronization_OperationHistory
     */
    public function getOperationHistory()
    {
        if (is_null($this->operationHistory)) {
            $this->operationHistory = Mage::getModel('M2ePro/Synchronization_OperationHistory');
        }
        return $this->operationHistory;
    }

    /**
     * @return Ess_M2ePro_Model_Synchronization_Log
     */
    protected function getLog()
    {
        if (is_null($this->log)) {
            $this->log = Mage::getModel('M2ePro/Synchronization_Log');
            $this->log->setInitiator($this->getInitiator());
            $this->log->setSynchronizationTask(Ess_M2ePro_Model_Synchronization_Log::TASK_UNKNOWN);
        }
        return $this->log;
    }

    // -----------------------------------

    protected function checkAndPrepareProductChange()
    {
        Mage::getModel('M2ePro/ProductChange')->clearOutdated(
            $this->getConfigValue('/settings/product_change/', 'max_lifetime')
        );
        Mage::getModel('M2ePro/ProductChange')->clearExcessive(
            (int)$this->getConfigValue('/settings/product_change/', 'max_count')
        );

        $startDate = $this->getOperationHistory()->getObject()->getData('start_date');
        $maxCountPerOneTime = (int)$this->getConfigValue('/settings/product_change/', 'max_count_per_one_time');

        $functionCode = "Mage::getModel('M2ePro/ProductChange')
                                ->clearLastProcessed('{$startDate}',{$maxCountPerOneTime});";

        $shutdownFunction = create_function('', $functionCode);
        register_shutdown_function($shutdownFunction);
    }

    //####################################

    private function getConfig()
    {
        return Mage::helper('M2ePro/Module')->getSynchronizationConfig();
    }

    // ----------------------------------------

    private function getConfigValue($group, $key)
    {
        return $this->getConfig()->getGroupValue($group, $key);
    }

    private function setConfigValue($group, $key, $value)
    {
        return $this->getConfig()->setGroupValue($group, $key, $value);
    }

    //####################################
}
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Reward Points + Referral program
 * @version   1.1.2
 * @build     928
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Rewards_Model_Observer_Abstract
{
    public function getConfig()
    {
        return Mage::getSingleton('rewards/config');
    }
}
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Reward Points + Referral program
 * @version   1.1.2
 * @build     928
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Rewards_Model_Observer_Order extends Mirasvit_Rewards_Model_Observer_Abstract
{
    protected function refreshPoints($quote)
    {
        if ($quote->getIsPurchaseSave()) {
            return;
        }

        if (!$purchase = Mage::helper('rewards/purchase')->getByQuote($quote)) {
            return;
        }

        if (!(Mage::getModel('customer/session')->isLoggedIn() && Mage::getModel('customer/session')->getId())) {
            $purchase->setSpendPoints(0);
        }
        $purchase->refreshPointsNumber();
        $purchase->save();

        Mage::helper('rewards/referral')->rememberReferal($quote);
    }

    /**
     * we fire this method only in  the backend.
     */
    public function quoteAfterSaveBackend($observer)
    {
        if (Mage::getSingleton('rewards/config')->getQuoteSaveFlag()) {
            return;
        }
        if (!$quote = $observer->getQuote()) {
            return;
        }
        Mage::getSingleton('rewards/config')->setQuoteSaveFlag(true);
        $this->refreshPoints($quote);
        Mage::getSingleton('rewards/config')->setQuoteSaveFlag(false);
    }

    /**
     * we fire this method only in  the frontend.
     */
    public function quoteAfterSave()
    {
        if (!$quote = Mage::getModel('checkout/cart')->getQuote()) {
            return;
        }

        $this->refreshPoints($quote);
    }

    /**
     * we fire this method only in  the frontend.
     */
    public function actionPredispatch($observer)
    {
        $uri = $observer->getControllerAction()->getRequest()->getRequestUri();
        if (strpos($uri, 'checkout') === false) {
            return;
        }
        if (!$quote = Mage::getModel('checkout/cart')->getQuote()) {
            return;
        }
        //this does not calculate quote correctly
        if (strpos($uri, '/checkout/cart/add/') !== false) {
            return;
        }

        //this does not calculate quote correctly with firecheckout
        if (strpos($uri, '/firecheckout/') !== false) {
            return;
        }

        //this does not calculate quote correctly with gomage
        if (strpos($uri, '/gomage_checkout/onepage/save/') !== false) {
            return;
        }
        $this->refreshPoints($quote);
    }

    public function orderPlaceAfter($observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($this->_isOrderPaidNow($order)) {
            if ($order->getCustomerId()) {
                ;
                Mage::helper('rewards/balance_order')->spendOrderPoints($order);
            }
        }
    }

    public function orderCancelAfter($observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($this->_isOrderPaidNow($order)) {
            if ($order->getCustomerId()) {
                Mage::helper('rewards/balance_order')->restoreSpendPoints($order);
            }
        }
    }

    public function checkoutSuccess($observer)
    {
        $session = Mage::getSingleton('checkout/type_onepage')->getCheckout();
        $orderId = $session->getLastOrderId();
        if (!$session->getLastSuccessQuoteId() || !$orderId) {
            return;
        }
        $order = Mage::getModel('sales/order')->load($orderId);
        $this->addPointsNotifications($order);
    }

    public function addPointsNotifications($order)
    {
        if (!$order->getCustomerId()) {
            return;
        }

        $quote = Mage::getModel('sales/quote')->getCollection()
                ->addFieldToFilter('entity_id', $order->getQuoteId())
                ->getFirstItem(); //we need this for correct work if we create orders via backend
        $totalEarnedPoints = Mage::helper('rewards/balance_earn')->getPointsEarned($quote);
        $purchase = Mage::helper('rewards/purchase')->getByOrder($order);
        $totalSpendPoints = $purchase->getPointsNumber();

        if ($totalEarnedPoints && $totalSpendPoints) {
            $this->addNotificationMessage(Mage::helper('rewards')->__('You earned %s and spent %s for this order.',
                Mage::helper('rewards')->formatPoints($totalEarnedPoints),
                Mage::helper('rewards')->formatPoints($totalSpendPoints)));
        } elseif ($totalSpendPoints) {
            $this->addNotificationMessage(Mage::helper('rewards')->__('You spent %s for this order.',
                Mage::helper('rewards')->formatPoints($totalSpendPoints)));
        } elseif ($totalEarnedPoints) {
            $this->addNotificationMessage(Mage::helper('rewards')->__('You earned %s for this order.',
                Mage::helper('rewards')->formatPoints($totalEarnedPoints)));
        }
        if ($totalEarnedPoints) {
            $this->addNotificationMessage(Mage::helper('rewards')->__('Earned points will be enrolled to your account after we finish processing your order.'));
        }
    }

    private function addNotificationMessage($message)
    {
        $message = Mage::getSingleton('core/message')->success($message);
        Mage::getSingleton('core/session')->addMessage($message);
    }

    protected function _isOrderPaidNow($order)
    {
        if (!Mage::registry('mst_ordercompleted_done')) {
            Mage::register('mst_ordercompleted_done', true);

            return true;
        }

        return false;
    }

    public function afterInvoiceSave($observer)
    {
        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        if ($invoice->getState() != Mage_Sales_Model_Order_Invoice::STATE_PAID) {
            return;
        }

        if ($order && $this->getConfig()->getGeneralIsEarnAfterInvoice()) {
            $this->earnOrderPoints($order);
        }
    }

    public function afterShipmentSave($observer)
    {
        $object = $observer->getObject();
        if (!($object && ($object instanceof Mage_Sales_Model_Order_Shipment))) {
            return $this;
        }

        $order = Mage::getModel('sales/order')->load((int) $object->getOrderId());

        if ($order && $this->getConfig()->getGeneralIsEarnAfterShipment()) {
            $this->earnOrderPoints($order);
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function orderSaveAfter($observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        if (!$order = $observer->getEvent()->getOrder()) {
            return;
        }
        $status = $order->getStatus();

        if (in_array($status, $this->getConfig()->getGeneralEarnInStatuses())) {
            $this->earnOrderPoints($order);
        }
    }

    protected function earnOrderPoints($order)
    {
        if ($order->getCustomerId()) {
            Mage::helper('rewards/balance_order')->earnOrderPoints($order);
        }
        Mage::helper('rewards/referral')->processReferralOrder($order);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function afterRefundSave($observer)
    {
        /** @var Mage_Sales_Model_Order_Creditmemo $creditMemo */
        if (!$creditMemo = $observer->getEvent()->getCreditmemo()) {
            return;
        }
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($creditMemo->getOrderId());
        if ($this->getConfig()->getGeneralIsCancelAfterRefund()) {
            Mage::helper('rewards/balance_order')->cancelEarnedPoints($order, $creditMemo);
        }

        if ($this->getConfig()->getGeneralIsRestoreAfterRefund()) {
            Mage::helper('rewards/balance_order')->restoreSpendPoints($order, $creditMemo);
        }
    }
}
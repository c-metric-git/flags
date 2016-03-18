<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleTrustedStore
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Mage_GoogleTrustedStore_Model_Observer
{
    /**
     * Adds to checkout session all order ids.
     * This is done, because in native behavior such data is cleared after native usage on success page
     *
     * @param Varien_Event_Observer $observer
     */
    public function collectMultishippingOrderIds(Varien_Event_Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        Mage::getModel('checkout/session')->setData('multishipping_order_ids', $orderIds);
    }

    /**
     * Adds cancellation reason to order from request
     * Adds cancellation reason to order history comments
     *
     * @param Varien_Event_Observer $observer
     */
    public function addCancellationReasonToOrder(Varien_Event_Observer $observer)
    {
        $reasonCode = $this->_getCancellationReason();
        $order = $observer->getEvent()->getDataObject();
        if ($reasonCode && $this->_getConfig()->isEnabled($order->getStoreId())) {
            if ($order && $order->isCanceled()) {
                $origState = $order->getOrigData('state');
                if ($origState != Mage_Sales_Model_Order::STATE_CANCELED) {
                    $order->setCancellationReason($reasonCode);
                    $reasonDescription = Mage::getSingleton('googletrustedstore/source_orderCancellationReason')
                        ->getDescriptionByCode($reasonCode);
                    $order->addStatusHistoryComment(Mage::helper('googletrustedstore')->__(
                        'Order was canceled because of next reason: %s',
                        $reasonDescription
                    ));
                }
            }
        }
    }

    /**
     * Adds cancellation reason from request to session
     *
     * @param Varien_Event_Observer $observer
     */
    public function addCancellationReasonToSession(Varien_Event_Observer $observer)
    {
        $reasonCode = $this->_getCancellationReason();
        if ($reasonCode) {
            $order = $observer->getEvent()->getOrder();
            if ($order && !$order->getReordered()) { // then order edited
                $this->_getSession()->setCancellationReason($reasonCode);
            }
        }
    }

    /**
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Adds last created from admin panel order id into admin session for further use
     *
     * @param Varien_Event_Observer $observer
     */
    public function collectAdminOrderId(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order) {
            $this->_getSession()->setLastAdminOrderId($order->getId());
        }
    }

    /**
     * Add observer to specific event
     *
     * @param type $area
     * @param type $eventName
     * @param type $observerName
     * @param type $observerClass
     * @param type $observerMethod
     * @return Mage_GoogleTrustedStore_Model_Observer
     */
    protected function _addObserver($area, $eventName, $observerName, $observerClass, $observerMethod)
    {
        $eventConfig = Mage::getConfig()->getEventConfig($area, $eventName);
        if (!$eventConfig) {
            $eventConfig = Mage::getConfig()->getNode($area)->events->addChild($eventName);
        }
        if (isset($eventConfig->observers)) {
            $eventObservers = $eventConfig->observers;
        } else {
            $eventObservers = $eventConfig->addChild('observers');
        }
        $observer = $eventObservers->addChild($observerName);
        $observer->addChild('class', $observerClass);
        $observer->addChild('method', $observerMethod);

        return $this;
    }

    /**
     * Dynamically add adminhtml_block_html_before event observer for adminhtml_sales_order_index action
     * observes controller_action_predispatch_adminhtml_sales_order_index event
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_GoogleTrustedStore_Model_Observer
     */
    public function addOrderGridBlocksRenderingObserver(Varien_Event_Observer $observer)
    {
        $this->_addObserver('adminhtml',
            'adminhtml_block_html_before',
            'googletrustedstore_order_grid_add_cancellation_reasons',
            'googletrustedstore/observer',
            'orderGridAddCancellationReasons'
        );

        return $this;
    }

    /**
     * Add cancellation reasons selector to the 'Cancel' action
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_GoogleTrustedStore_Model_Observer
     */
    public function orderGridAddCancellationReasons(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract) {
            $item = $block->getItem('cancel_order');
            if (!$item instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction_Item) {
                return $this;
            }
            $source = Mage::getSingleton('googletrustedstore/source_orderCancellationReason');
            $item->setAdditionalActionBlock(
                array(
                    $block->getHtmlId() . '_' . 'cancellation_reason' => array(
                         'name' => 'cancellation_reason',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('googletrustedstore')->__('Cancellation Reason'),
                         'values' => $source->toOptionArray(),
                         'value'  => $source->getDefaultCode(),
                    )
                )
            );
        }
        return $this;
    }

    /**
     * Return cancellation reason code from request or session if cannot get from request
     *
     * @return string|null
     */
    protected function _getCancellationReason()
    {
        $reason = Mage::app()->getRequest()->getParam('cancellation_reason');
        if (!$reason && $this->_getSession()->hasCancellationReason()) {
            $reason = $this->_getSession()->getCancellationReason();
        }

        return $reason;
    }

    /**
     * Saves current GTS config in the registry
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_GoogleTrustedStore_Model_Observer
     */
    public function saveOldSettings(Varien_Event_Observer $observer)
    {
        $request = $observer
            ->getEvent()
            ->getControllerAction()
            ->getRequest();
        $stores = Mage::app()->getStores();
        $currentConfig = array();
        $section = $request->getParam('section');
        if ($section == 'google') {
            foreach ($stores as $store) {
                $currentConfig[$store->getCode()] = $this->_getFeedConfig($store);
            }
            Mage::register('googletrustedstore_config',$currentConfig);
        }
        return $this;
    }

    /**
     * Verifies FTP User/password, Filenames are the same as in those other scopes with the same Merchant ID
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_GoogleTrustedStore_Model_Observer
     */
    public function verifySettings(Varien_Event_Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        $website = $observer->getEvent()->getWebsite();
        if ($oldConfig = Mage::registry('googletrustedstore_config')) {
            $stores = array();
            if ($store) {
                $stores[] = Mage::app()->getStore($store);
            } elseif ($website) {
                $stores = Mage::app()->getWebsite($website)->getStores();
            } else {
                $stores = Mage::app()->getStores();
            }
            $settingsIssueFound = false;
            foreach ($stores as $store) {
                $storeOldFeedConfig = $oldConfig[$store->getCode()];
                $storeCurrentFeedConfig = $this->_getFeedConfig($store);
                $changedConfig = array_diff_assoc($storeOldFeedConfig, $storeCurrentFeedConfig);
                if (!empty($changedConfig)) {
                    foreach (Mage::app()->getStores() as $s) {
                        if ($s->getCode() != $store->getCode()) {
                            $sConfig = $this->_getFeedConfig($s);
                            if ($sConfig['id'] == $storeCurrentFeedConfig['id'] &&
                                array_diff_assoc($storeCurrentFeedConfig, $sConfig)) {
                                    Mage::getSingleton('adminhtml/session')
                                        ->addNotice(Mage::helper('googletrustedstore')
                                        ->__('One or more of your Google Trusted Stores settings below do not match the account settings in other scopes.  Please verify that FTP username, password, Cancel file name, and Shipment file name are the same within a given Google Trusted Store Merchant ID.'));
                                    return $this;
                            }
                        }
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Returns feed config
     *
     * @param mixed $store
     * @return array
     */
    protected function _getFeedConfig($store)
    {
        return array(
            'id' => $this->_getConfig()->getAccountId($store),
            'user' => $this->_getConfig()->getFtpUserName($store),
            'password' => $this->_getConfig()->getFtpPassword($store),
            'shipment' => $this->_getConfig()->getFtpShipmentTargetFileName($store),
            'cancellation' => $this->_getConfig()->getFtpCancellationTargetFileName($store)
        );
    }

    /**
     * Returns GTS config model
     *
     * @return Mage_GoogleTrustedStore_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('googletrustedstore/config');
    }
}
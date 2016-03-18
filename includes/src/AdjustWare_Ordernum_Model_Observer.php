<?php
/**
 * Custom Order Number Pro
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Ordernum
 * @version      5.1.5
 * @license:     d0NyTkcYcW64yuyl9Cf2M6q3gBilLUVMAwQSumkwPP
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Ordernum_Model_Observer extends Varien_Object
{
    private function _initScopeData($request)
    {
        $website = $this->_getWebsite($request);
        $store   = $this->_getStore($request);
        switch(true)
        {
            case(!isset($website) && !isset($store)):
                $scope = 'default';
                $scopeId = '0';
                break;
            case(isset($store) && isset($website)):
                $scope = 'stores';
                $scopeId = Mage::app()->getStore($store)->getId();
                break;
            case(isset($website) && !isset($store)):
                $scope = 'websites';
                $scopeId = Mage::app()->getWebsite($website)->getId();
                break;
            default:
                $scope = 'unknown';
                $scopeId = '0';
                //Mage::throwException('Illegal scope');
        }
        $this->setScope($scope);
        $this->setScopeId($scopeId);
    }

    private function _detectCustomState($groups)
    {
        foreach($groups as $groupName => $group)
        {
            foreach ($group['fields'] as $fieldName => $field )
            {
                $path = Mage::helper('adjordernum/path')->compilePath($groupName, $fieldName);
                $confValue = Mage::getModel('core/config_data')->getCollection()
                    ->addFieldToFilter('scope', $this->getScope())
                    ->addFieldToFilter('scope_id', $this->getScopeId())
                    ->addFieldToFilter('path', $path)
                    ->getFirstItem()->getValue();

                $formInherit = isset($field['inherit']) ? $field['inherit'] : null ;
                $formValue = isset($field['value']) ? $field['value'] : null ;
                if ($formInherit && $formValue)
                {
                   Mage::throwException('Invalid form');
                }
                if(($formInherit == '1' && $confValue !== null) || (!empty($formValue) && $confValue == null))
                {
                    return true;
                }
            }
        }
        return false;
    }

    private function _changeState($stateName)
    { 
        $configState = Mage::getModel('adjordernum/config_state');
        switch ($stateName){
            case('custom'):
                return $configState->setCustom();
            case('global'):
                return $configState->setGlobal();
            case('websites'):
                return $configState->setWebsites();
            case('stores'):
                return $configState->setStoreviews();
            default :
                Mage::throwException('Change '.__CLASS__.'::'.__METHOD__);
        }
    }

    private function _getStore($request)
    {
        $code = $request->getParam('store');
        if (isset($code) && !is_array($code) && $code != 'undefined')
        {
            return Mage::app()->getStore($code);
        }
        return null;        
    }
    
    private function _getWebsite($request)
    {
        $code = $request->getParam('website');
        if (isset($code) && !is_array($code))
        {
            return Mage::app()->getWebsite($code);
        }
        return null;  
    }

    public function predispatchAdminhtml($observer)
    {
        $request = $observer->getEvent()->getData('controller_action')->getRequest();
        $groups = $request->getPost('groups');
        $section = $request->getParam('section');
        $action = $request->getActionName();
        $validSection = Mage::helper('adjordernum/path')->getSection();

        if(($section !== $validSection) || ($action !== 'save') || !isset($groups))
        {
            return;
        }

        $this->_initScopeData($request);

        if(($this->getScope() == 'unknown'))
        {
            return;
        }
        elseif($this->getScope() == 'default')
        {
            $configState = $groups['general']['fields']['settings_level']['value'];
            $this->_changeState($configState);
        }
        else
        {
            $customState = $this->_detectCustomState($groups);
            if($customState) {
                $this->_changeState('custom');
            }
        }
    }

    public function orderSaveBefore($observer)
    {
        $order = $observer->getOrder();

        if ($order->getIncrementId() && !Mage::app()->getRequest()->getParam('order_id'))
        {
            $_order = Mage::getModel('sales/order')->loadByIncrementId($order->getIncrementId());

            if ($_order->getId() && $_order->getState() && $order->getId()!=$_order->getId())
            {
                $order->setIncrementId(null);
            }
        }
    }
}
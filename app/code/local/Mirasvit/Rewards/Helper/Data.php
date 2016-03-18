<?php
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



class Mirasvit_Rewards_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_currentStore;

    /**
     * Sets current store for translation.
     *
     * @param Mage_Core_Model_Store $store
     */
    public function setCurrentStore($store)
    {
        $this->_currentStore = $store;
    }

    /**
     * Returns current store.
     *
     * @return Mage_Core_Model_Store
     */
    public function getCurrentStore()
    {
        if (!$this->_currentStore) {
            $this->_currentStore = Mage::app()->getStore();
        }

        return $this->_currentStore;
    }

    public function getCoreStoreOptionArray()
    {
        $arr = Mage::getModel('core/store')->getCollection()->toArray();
        foreach ($arr['items'] as $value) {
            $result[$value['store_id']] = $value['name'];
        }

        return $result;
    }

    /************************/

    /**
     * Translates backend messages independently from backend locale.
     *
     * @param  string   Message to translate
     * @param  string[] Infinite number of params for vsprintf
     *
     * @return string
     */
    public function ____()
    {
        $args = func_get_args();
        $locale = Mage::getStoreConfig('general/locale/code', $this->getCurrentStore()->getId());
        $localeCsv = Mage::getBaseDir('locale').'/'.$locale.'/'.'Mirasvit_Rewards.csv';
        if (!file_exists($localeCsv)) {
            return call_user_func_array(array('Mirasvit_Rewards_Helper_Data', '__'), $args);
        }

        $translator = new Zend_Translate(
            array(
                'adapter' => 'csv',
                'content' => $localeCsv,
                'locale' => substr($locale, 0, 2),
                'delimiter' => ',',
                )
            );
        $msg = $translator->_($args[0]);
        unset($args[0]);

        return vsprintf($msg, $args);
    }

    public function getConfig()
    {
        return Mage::getSingleton('rewards/config');
    }

    public function getPointsName()
    {
        $unit = $this->getConfig()->getGeneralPointUnitName();
        $unit = str_replace(array('(', ')'), '', $unit);

        return $unit;
    }

    public function formatPoints($points)
    {
        $unit = $this->getConfig()->getGeneralPointUnitName($this->getCurrentStore()->getId());
        if ($points == 1) {
            $unit = preg_replace("/\([^)]+\)/", '', $unit);
        } else {
            $unit = str_replace(array('(', ')'), '', $unit);
        }

        return $points.' '.$unit;
    }

    public function formatCurrency($value)
    {
        return Mage::helper('core')->formatCurrency($value);
    }

    public function isMultiship($quote = null)
    {
        return false;
    }

    public function getWebsiteId($storeId)
    {
        return Mage::getModel('core/store')->load($storeId)->getWebsiteId();
    }

    public function isAdmin()
    {
        return Mage::app()->getStore()->isAdmin() || Mage::getDesign()->getArea() == 'adminhtml';
    }
}

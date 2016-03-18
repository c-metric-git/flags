<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Coupongenerator
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Coupongenerator_Model_Version_Adaptee_Magento5
{
    /**
     * @return string
     */
    public function getByPercentSalesRuleAction()
    {
        return Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION;
    }

    /**
     * @return string
     */
    public function getByFixedSalesRuleAction()
    {
        return Mage_SalesRule_Model_Rule::BY_FIXED_ACTION;
    }

    /**
     * @return string
     */
    public function getCartFixedSalesRuleAction()
    {
        return Mage_SalesRule_Model_Rule::CART_FIXED_ACTION;
    }

    /**
     * @return string
     */
    public function getXYSalesRuleAction()
    {
        return Mage_SalesRule_Model_Rule::BUY_X_GET_Y_ACTION;
    }

    /**
     * @param $storeId int
     *
     * @return Varien_Object
     */
    public function startEnvironmentEmulation($storeId)
    {
        $initialInfo = Mage::getSingleton('core/app_emulation')
            ->startEnvironmentEmulation($storeId, Mage_Core_Model_App_Area::AREA_ADMINHTML);
        return $initialInfo;
    }

    /**
     * @param $initialInfo Varien_Object
     *
     * @return $this
     */
    public function stopEnvironmentEmulation($initialInfo)
    {
        Mage::getSingleton('core/app_emulation')->stopEnvironmentEmulation($initialInfo);
        return $this;
    }
}
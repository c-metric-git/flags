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

class AW_Coupongenerator_Model_Version_Adapter extends Varien_Object
{
    /**
     * @var array
     */
    protected $_methodsMap = array(
        'magento4' => array(
            'addColumnsToCouponsGrid',
            'updateCouponUsageLimit',
            'getRuleSaveAndContinueButtonId',
            'getByPercentSalesRuleAction',
            'getByFixedSalesRuleAction',
            'getCartFixedSalesRuleAction',
            'getXYSalesRuleAction',
            'getFormDependenciesBlock',
            'updateCouponTypeFormDependencies',
            'startEnvironmentEmulation',
            'stopEnvironmentEmulation',
            'getCouponCharset',
            'setSalesRuleValidationFilter'
        ),
        'magento5' => array(
            'getByPercentSalesRuleAction',
            'getByFixedSalesRuleAction',
            'getCartFixedSalesRuleAction',
            'getXYSalesRuleAction',
            'startEnvironmentEmulation',
            'stopEnvironmentEmulation'
        ),
        'magento7' => array(
            'addColumnsToCouponsGrid',
            'updateCouponUsageLimit',
            'getRuleSaveAndContinueButtonId',
            'getFormDependenciesBlock',
            'updateCouponTypeFormDependencies',
            'getCouponCharset',
            'setSalesRuleValidationFilter'
        ),
        'magento11' => array(
            'addColumnsToCouponsGrid',
            'updateCouponUsageLimit',
            'getRuleSaveAndContinueButtonId',
            'getFormDependenciesBlock',
            'updateCouponTypeFormDependencies',
            'getCouponCharset',
            'setSalesRuleValidationFilter',
            'addBannersBlock'
        ),
        'magento12' => array(
            'addColumnsToCouponsGrid',
            'updateCouponUsageLimit',
            'getRuleSaveAndContinueButtonId',
            'getFormDependenciesBlock',
            'updateCouponTypeFormDependencies',
            'getCouponCharset',
            'setSalesRuleValidationFilter'
        )
    );

    /**
     * @param $methodName string
     * @param array $params array
     *
     * @return bool|mixed
     */
    public function call($methodName, $params = array())
    {
        $versionInfo = Mage::getVersionInfo();
        for ($v = $versionInfo['minor']; $v > 3; $v--) {
            if (array_key_exists("magento{$v}", $this->_methodsMap)
                && in_array($methodName, $this->_methodsMap["magento{$v}"])
            ) {
                $adaptee = Mage::getSingleton("coupongenerator/version_adaptee_magento{$v}");
                return call_user_func_array(array($adaptee, $methodName), $params);
            }
        }
        return false;
    }
}
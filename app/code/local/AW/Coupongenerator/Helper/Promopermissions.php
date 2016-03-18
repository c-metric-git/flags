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


class AW_Coupongenerator_Helper_Promopermissions extends Enterprise_PromotionPermissions_Helper_Data
{
    const RULES_COUPONGENERATOR_PROMO_ACL_PATH = 'promo/coupongenerator/coupongenerator_rules';

    /**
     * Check if admin has permissions to edit sales rules
     *
     * @return boolean
     */
    public function getCanAdminEditSalesRules()
    {
        if ('awqcg_rules' == Mage::app()->getRequest()->getControllerName()) {
            $path = self::RULES_COUPONGENERATOR_PROMO_ACL_PATH;
        } else {
            $path = self::EDIT_PROMO_SALESRULE_ACL_PATH;
        }
        return (boolean) Mage::getSingleton('admin/session')->isAllowed($path);
    }
}

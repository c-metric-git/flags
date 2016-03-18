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

class AW_Coupongenerator_Block_Adminhtml_Coupons_Grid_Column_Renderer_Customer
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        if ($customerId = $row->getCustomerId()) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $customerUrl = Mage::getSingleton('adminhtml/url')->getUrl(
                'adminhtml/customer/edit', array('id' => $customerId)
            );
            // $customer->getName() is not suitable because of filtration
            // see AW_Coupongenerator_Block_Adminhtml_Coupons_Grid::_filterBySentTo()
            $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            return "<a href='{$customerUrl}' title='{$customer->getName()}'>{$customerName}</a><br />"
                . parent::render($row);
        }
        return parent::render($row);
    }
}

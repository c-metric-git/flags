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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer address edit block
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
//class Mage_Customer_Block_Address_Edit extends Mage_Directory_Block_Data
class QS_Addressvalidation_Block_Rewrite_Customer_Address_Edit extends Mage_Customer_Block_Address_Edit
{
    protected function _prepareLayout()
    {
        Mage_Directory_Block_Data::_prepareLayout();
        $this->_address = Mage::getModel('customer/address');

        // Init address object
        if ($id = $this->getRequest()->getParam('id')) {
            $this->_address->load($id);
            if ($this->_address->getCustomerId() != Mage::getSingleton('customer/session')->getCustomerId()) {
                $this->_address->setData(array());
            }
        }
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->getTitle());
        }
        if ($postedData = Mage::getSingleton('customer/session')->getAddressFormData(true)) {
//QS salo: fixed empty id for form with errors returning
//            $this->_address->setData($postedData);
//			mage::log($postedData,null,'addressValidation.log');
            $this->_address->addData($postedData);
        }
    }
}

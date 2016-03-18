<?php
/**
 * Address Validation
 * USPS Address Validation
 *
 * @category   QS
 * @package    QS_Addressvalidation
 * @author     Quart-soft Magento Team <magento@quart-soft.com> 
 * @copyright  Copyright (c) 2011 Quart-Soft Ltd http://quart-soft.com
 */
class QS_Addressvalidation_Model_Config_Source_Shipping_Allowedcarriers
    extends QS_Addressvalidation_Model_Config_Source_Shipping_Allcarriers
{
    public function toOptionArray($isActiveOnlyFlag = true)
    {
        return parent::toOptionArray(true);
    }
}

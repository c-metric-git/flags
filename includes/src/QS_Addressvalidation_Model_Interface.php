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
interface QS_Addressvalidation_Model_Interface
{

    /**
     * Check if carrier has address validation option available
     *
     * @return boolean
     */
    public function isValidationAvailable();
	
	/**
     * Perform address verification request
     *
     * @return Varien_Object
     */
	public function getAddressNormalized($address);
    
}

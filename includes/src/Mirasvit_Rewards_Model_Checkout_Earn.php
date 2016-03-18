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


class Mirasvit_Rewards_Model_Checkout_Earn extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	public function __construct()
    {
		$this->setCode('earn');
	}

    protected function getPurchase() {
        return Mage::helper('rewards/purchase')->getPurchase();
    }

    public function getEarnPoints()
    {
        if (!$this->getPurchase()) {
            return 0;
        }
        return $this->getPurchase()->getEarnPoints();
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING) {
            return $this;
        }
        $address->addTotal(
            array(
                'code'  => $this->getCode(),
                'title' => Mage::helper('rewards')->__('You Earn'),
                'value' => $this->getEarnPoints() //will be used in some modifications of iwd onestepcheckout
            )
        );
        return $this;
    }

	public function collect(Mage_Sales_Model_Quote_Address $address)
    {
		if (Mage::helper('rewards' )->isMultiship($address)) {
			return $this;
		}

		return $this;
	}

	private function _getRewardsSess()
    {
		return Mage::getSingleton('rewards/session');
	}
}
<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
$this->startSetup();

Mage::app()->getConfig()->loadDb();

$methods = Mage::getSingleton('adminhtml/system_config_source_shipping_allmethods')
    ->toOptionArray();

$flags = Mage::getResourceModel('amflags/flag_collection');
foreach ($flags as $flag) {
    $shipping = $flag->getData('apply_shipping');
    if ($shipping){
        $appliedMethods = explode(',', $shipping);

        $values = array();
        foreach ($appliedMethods as $appliedMethod) {
            if (isset($methods[$appliedMethod])) {

                $methodValues = array();
                foreach ($methods[$appliedMethod]['value'] as $value) {
                    $methodValues[] = $value['value'];
                }

                $values[] = implode(',', $methodValues);
            }
        }

        $flag->setData('apply_shipping', implode(',', $values));
        $flag->save();
    }
}


$this->endSetup();
<?php
$installer = $this;
$installer->startSetup();

$status = Mage::getModel('sales/order_status');

$status->setStatus('manual_review')->setLabel('Manual Review Required')
    ->assignState(Mage_Sales_Model_Order::STATE_HOLDED)
    ->save();

$installer->endSetup();
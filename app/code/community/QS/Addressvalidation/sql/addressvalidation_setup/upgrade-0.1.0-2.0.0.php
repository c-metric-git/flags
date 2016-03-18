<?php
/**
 * Created by JetBrains PhpStorm.
 * User: inknex
 * Date: 8/28/12
 * Time: 8:25 PM
 * To change this template use File | Settings | File Templates.
 */

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

// Add reset password link token attribute
$installer->addAttribute('customer_address', 'validation_flag', array(
    'type'     => 'int',
    'input'    => 'hidden',
    'visible'  => false,
    'required' => false
));

$installer->endSetup();
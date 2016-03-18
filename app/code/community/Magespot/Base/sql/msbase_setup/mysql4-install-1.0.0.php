<?php
 /**
 * Magespot
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magespot License.
 * It is available through the world-wide-web at this URL:
 * http://magespot.com/license.html
 * If you need receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contacts@magespot.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.magespot.com/ for more information.
 *
 * @category   Magespot
 * @package    Magespot_Base
 * @copyright  Copyright (c) 2013 Magespot Crew (http://www.magespot.com/)
 * @author     Magespot Crew <contacts@magespot.com>
 * @license    http://magespot.com/license.html  Magespot License
 */
$this->startSetup();

$v = Mage::getStoreConfig('msbase/feed/installed');
if (!$v){
    Mage::getModel('core/config_data')
        ->setScope('default')
        ->setPath('msbase/feed/installed')
        ->setValue(time())
        ->save();     
}

$feedData = array();
$feedData[] = array(
    'severity'      => 4,
    'date_added'    => gmdate('Y-m-d H:i:s', time()),
    'title'         => 'The installation for Magespot extension has been completed. Please, flush all cache, run compilation process log-out and log back in to admin panel.',
    'description'   => 'To check current version of your extensions follow `My extensions` tab. You are free to set notifications which you will get from Magepot.com',
    'url'           => 'http://magespot.com/'
);

Mage::getModel('adminnotification/inbox')->parse($feedData);

$this->endSetup();
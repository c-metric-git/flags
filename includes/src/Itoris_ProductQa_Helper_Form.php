<?php 
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_PRODUCTQA
 * @copyright  Copyright (c) 2012 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

class Itoris_ProductQa_Helper_Form {

	public function getStoreSelectOptions() {
		/* @var $storeModel Mage_Adminhtml_Model_System_Store */
		$storeModel = Mage::getSingleton('adminhtml/system_store');

        $options = array();

        foreach ($storeModel->getWebsiteCollection() as $website) {
            $websiteShow = false;
            foreach ($storeModel->getGroupCollection() as $group) {
                if ($group->getWebsiteId() != $website->getId()) {
                    continue;
                }
                $groupShow = false;
                foreach ($storeModel->getStoreCollection() as $store) {
                    if ($store->getGroupId() != $group->getId()) {
                        continue;
                    }
					$groupId;
                    if (!$websiteShow) {
                        $websiteShow = true;
						$groupId = 'website_' . $website->getCode();
                        $options['website_' . $website->getCode()] = array(
                            'label'    => $website->getName(),
                            'value' => array(),
                        );
                    }
                    $options[$groupId]['value'][] = array(
						'value' => $store->getId(),
                        'label'    => $store->getName(),
						'title'   => $store->getName(),
                    );
                }
            }
        }

        return $options;
    }
}
?>
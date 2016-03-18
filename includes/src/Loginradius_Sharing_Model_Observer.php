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
 *  Sharing observer model
 *
 * @category    Loginradius
 * @package     Loginradius_Sharing
 * @author      LoginRadius Team
 */

/**
 * Class Loginradius_Sharing_Model_Observer responsible for LoginRadius api keys verification!
 */
class Loginradius_Sharing_Model_Observer extends Mage_Core_Helper_Abstract
{
    /**
     * @throws Exception while api keys are not valid!
     */
    public function validateLoginradiusKeys()
    {

        $post = Mage::app()->getRequest()->getPost();
        $result['message'] = '';
        $result['status'] = 'Success';
    }

    public function addCustomLayoutHandle(Varien_Event_Observer $observer)
    {
        $controllerAction = $observer->getEvent()->getAction();
        $layout = $observer->getEvent()->getLayout();
        if ($controllerAction && $layout && $controllerAction instanceof Mage_Adminhtml_System_ConfigController) {
            if ($controllerAction->getRequest()->getParam('section') == 'sharing_options') {
                $layout->getUpdate()->addHandle('sharing_custom_handle');
            }
        }
        return $this;
    }
}

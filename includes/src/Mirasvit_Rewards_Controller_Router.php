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


class Mirasvit_Rewards_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
    public function addUrlsRouter($observer)
    {
        $front = $observer->getEvent()->getFront();
        $urlsRouter = new Mirasvit_Rewards_Controller_Router();
        $front->addRouter('rewards', $urlsRouter);
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $pathInfo = $request->getPathInfo();
        $identifier = trim($pathInfo, '/');
        $parts      = explode('/', $identifier);
        if (count($parts) == 2 && $parts[0] == 'r') {
            $request
                ->setRouteName('rewards')
                ->setModuleName('rewards')
                ->setControllerName('referral')
                ->setActionName('referralVisit')
                ->setParams(array('customer_id' => (int)$parts[1]))
                ->setAlias(
                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                    'rewards'
                );
            return true;
        }
        return false;
    }
}

<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Coupongenerator
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Coupongenerator_Adminhtml_Awqcg_CouponsController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->_title($this->__('Promotions'))
            ->_title($this->__('Coupon Code Generator'))
            ->_title($this->__('Generated coupons'))
        ;
        return $this->loadLayout()->_setActiveMenu('promo/items');
    }

    protected function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/coupongenerator/generated_coupons');
    }

    protected function deleteAction()
    {
        if ($this->getRequest()->getParam('id')) {
            $coupon = Mage::getModel('salesrule/coupon')->load($this->getRequest()->getParam('id'));
            if ($coupon->getData()) {
                if (!$coupon->getIsPrimary()) {
                    $this->_getSession()->addSuccess(
                        Mage::helper('coupongenerator')->__('Coupon "%s" has been successfully removed', $coupon->getCode())
                    );
                    $coupon->delete();
                } else {
                    $this->_getSession()->addError(Mage::helper('coupongenerator')->__('Couldn\'t remove primary coupon'));
                }
            } else {
                $this->_getSession()->addError(Mage::helper('coupongenerator')->__('Couldn\'t load coupon by given ID'));
            }
        } else {
            $this->_getSession()->addError(Mage::helper('coupongenerator')->__('ID isn\'t specified'));
        }

        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        try {
            $couponIds = $this->getRequest()->getParam('coupons');
            $countDeletedCoupons = 0;
            if ( ! is_array($couponIds)) {
                throw new Mage_Core_Exception($this->__('Invalid coupon ids'));
            }
            foreach ($couponIds as $couponId) {
                $coupon = Mage::getModel('salesrule/coupon')->load($couponId);
                if ( ! $coupon->getIsPrimary()) {
                    $coupon->delete();
                    $countDeletedCoupons++;
                } else {
                    $this->_getSession()->addWarning(
                        Mage::helper('coupongenerator')->__('Couldn\'t remove primary coupon %s', $coupon->getCode())
                    );
                }
            }
            if ($countDeletedCoupons > 0) {
                $this->_getSession()->addSuccess(
                    $this->__('%d coupon(s) have been successfully deleted', $countDeletedCoupons)
                );
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/index');
    }

    public function massExpireAction()
    {
        try {
            $couponIds = $this->getRequest()->getParam('coupons');
            $countDeactivatedCoupons = 0;
            if ( ! is_array($couponIds)) {
                throw new Mage_Core_Exception($this->__('Invalid coupon ids'));
            }
            foreach ($couponIds as $couponId) {
                $coupon = Mage::getModel('salesrule/coupon')->load($couponId);
                $coupon->setExpirationDate(Mage::getSingleton('core/date')->gmtDate())->save();
                $countDeactivatedCoupons++;
            }
            if ($countDeactivatedCoupons > 0) {
                $this->_getSession()->addSuccess(
                    $this->__('%d coupon(s) have been successfully deactivated', $countDeactivatedCoupons)
                );
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/index');
    }
    
    public function expireAction()
    {
        if ($id = $this->getRequest()->getParam('id', false)) {
            $coupon = Mage::getModel('salesrule/coupon')->load($id);
            if ($coupon->getData()) {
                    $coupon->setExpirationDate(Mage::getSingleton('core/date')->gmtDate())->save();
                    $this->_getSession()->addSuccess(
                        Mage::helper('coupongenerator')->__('Coupon "%s" has expired', $coupon->getCode())
                    );
            } else {
                $this->_getSession()->addError(Mage::helper('coupongenerator')->__('Couldn\'t load coupon by given ID'));
            }
        } else {
            $this->_getSession()->addError(Mage::helper('coupongenerator')->__('ID isn\'t specified'));
        }

        $this->_redirect('*/*/index');
    }

    public function generateAction()
    {
        $ruleId = $this->getRequest()->getParam('coupon_generation_rule', false);
        $notificationFlag = $this->getRequest()->getParam('coupon_generation_notification', false);
        if ( ! $ruleId) {
            $this->_getSession()->addError($this->__('Please select a rule'));
        } else {
            $emails = explode(',', $this->_getRequestEmail());
            foreach ($emails as $email) {
                $email = trim($email);
                if ( (bool) $email &&  ! Zend_Validate::is($email, 'EmailAddress')) {
                    $this->_getSession()->addError($this->__("%s email address is not valid", $email));
                    continue;
                } elseif( ! (bool) $email && count($emails)>1) {
                    continue;
                }
                try {
                    $this->getRequest()->setParam('coupon_generation_email', $email);
                    $coupon = Mage::getModel('coupongenerator/salesrule_coupon')->createNewOne($ruleId);
                    if ($coupon->getCode()) {
                        $message = $this->__('Coupon "%s" has been successfully created.', $coupon->getCode());
                        if ('true' === $notificationFlag && (bool) $email) {
                            Mage::getModel('coupongenerator/notifications')->send($this->_getRequestData($coupon));
                            $message .= ' ' . $this->__('Notification has been sent');
                        }
                        $this->_getSession()->addSuccess($message);
                    }
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Coupon generation, saving and notification if required
     * AJAX request
     */
    public function generateAjaxAction()
    {
        $response = array(
            'success' => false,
            'msg'     => $this->__('Ooops, something went wrong')
        );

        $ruleId = $this->getRequest()->getParam('coupon_generation_rule', false);
        $notificationFlag = $this->getRequest()->getParam('coupon_generation_notification', false);
        $expirationFlag = $this->getRequest()->getParam('coupon_generation_expiration', false);

        if ( ! $ruleId) {
            $response['msg'] = $this->__('Please select a rule');
        } elseif ($this->_getRequestEmail() &&  ! Zend_Validate::is($this->_getRequestEmail(), 'EmailAddress')) {
            $response['msg'] = $this->__('The email address is not valid');
        } else {
            try {
                $coupon = Mage::getModel('coupongenerator/salesrule_coupon')->createNewOne($ruleId);
                if ($coupon->getCode()) {
                    $response = array(
                        'success' => true,
                        'msg'     => $this->__('Coupon "%s" has been successfully created.', $coupon->getCode())
                    );
                    if ('true' === $expirationFlag &&  ! is_null($coupon->getExpirationDate())) {
                        $response['msg'] .= ' ' . $this->__('Expiration date: %s.', $coupon->getLocaleExpirationDate());
                    }
                    if ('true' === $notificationFlag) {
                        Mage::getModel('coupongenerator/notifications')->send($this->_getRequestData($coupon));
                        $response['msg'] .= ' ' . $this->__('Notification has been sent.');
                    }
                }
            } catch (Exception $e) {
                $response = array(
                    'success' => false,
                    'msg'     => $this->__($e->getMessage())
                );
            }
        }

        $this->getResponse()->setBody(Zend_Json::encode($response));
    }

    public function emailautocompleteAjaxAction()
    {
        $search = $this->getRequest()->getParam('search', '');
        $data = array();
        $collection = Mage::helper('coupongenerator')->getCustomerCollectionByEmail($search, 10);
        foreach ($collection as $customer) {
            $data[] = array(
                'email' => $customer->getEmail(),
                'name' => $customer->getFirstname() . ' ' . $customer->getLastname()
            );
        }
        $response = "<ul>";
        foreach ($data as $item) {
            $response .= sprintf(
                "<li data-email='%s' data-name='%s'>%s <br /> &lt;%s&gt;</li>",
                $item['email'], $item['name'], $item['name'], $item['email']
            );
        }
        $response .= "</ul>";
        $this->getResponse()->setBody($response);
    }

    protected function _getRequestData($coupon)
    {
        $requestData = array(
            'customer' => array(
                'email' => $this->_getRequestEmail(),
                'name' => $this->_getCustomerName()
            ),
            'store_id' => $this->_getStoreId(),
            'coupon' => $coupon
        );
        return $requestData;
    }

    protected function _getRequestEmail()
    {
        return $this->getRequest()->getParam('coupon_generation_email', false);
    }

    protected function _getRequestCustomerId()
    {
        return $this->getRequest()->getParam('customer_id', false);
    }

    protected function _getCustomerName()
    {
        if ($customer = Mage::helper('coupongenerator')->getCustomer(
            $this->_getRequestCustomerId(), $this->_getRequestEmail()
        )) {
            return $customer->getName();
        }
        return Mage::helper('coupongenerator')->__('customer');
    }

    protected function _getStoreId()
    {
        if ($customer = Mage::helper('coupongenerator')->getCustomer(
            $this->_getRequestCustomerId(), $this->_getRequestEmail()
        )) {
            return $customer->getStoreId();
        }
        return Mage::app()->getDefaultStoreView()->getId();
    }

}

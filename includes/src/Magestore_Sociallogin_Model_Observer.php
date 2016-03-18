<?php

class Magestore_Sociallogin_Model_Observer {
		
	public function customer_edit($observer){
		try{
			$customerId = Mage::getSingleton('core/session')->getCustomerIdSocialLogin();
			if ($customerId){
				Mage::getSingleton('customer/session')->getCustomer()->setEmail(' ');			
			}
			Mage::getSingleton('core/session')->setCustomerIdSocialLogin();
		} catch(Exception $e){		
		}
	}
	
	public function controller_action_predispatch_adminhtml($observer)
	{
		$controller = $observer->getControllerAction();
		if($controller->getRequest()->getControllerName() != 'system_config'
			|| $controller->getRequest()->getActionName() != 'edit')
			return;
		$section = $controller->getRequest()->getParam('section');
		if($section != 'sociallogin')
			return;
		$magenotificationHelper = Mage::helper('magenotification');
		if(!$magenotificationHelper->checkLicenseKey('Sociallogin')){
			$message = $magenotificationHelper->getInvalidKeyNotice();
			echo $message;die();
		}elseif((int)$magenotificationHelper->getCookieLicenseType() == Magestore_Magenotification_Model_Keygen::TRIAL_VERSION){
			Mage::getSingleton('adminhtml/session')->addNotice($magenotificationHelper->__('You are using a trial version of Social Login extension. It will be expired on %s.',
														 $magenotificationHelper->getCookieData('expired_time')
											));
		}
	}		
}
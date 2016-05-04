<?php
/**
 * @category   Apptrian
 * @package    Apptrian_Minify
 * @author     Apptrian
 * @copyright  Copyright (c) 2015 Apptrian (http://www.apptrian.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Apptrian_Minify_Adminhtml_Apptrian_MinifyController extends Mage_Adminhtml_Controller_Action
{
	
	public function processAction()
	{
	
		$helper = Mage::helper('apptrian_minify');
	
		try {
				
			$helper->process();
				
			$message = $this->__('Minification operations completed successfully.');
			Mage::getSingleton('adminhtml/session')->addSuccess($message);
				
		} catch (Exception $e) {
				
			$message = $this->__('Minification failed.');
			Mage::getSingleton('adminhtml/session')->addError($message);
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				
		}
	
		$url = Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/section/apptrian_minify');
		Mage::app()->getResponse()->setRedirect($url);
	
	}
	
}
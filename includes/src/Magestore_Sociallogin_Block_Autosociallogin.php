<?php
class Magestore_Sociallogin_Block_Autosociallogin extends Magestore_Sociallogin_Block_Sociallogin
{	
	public function getShownPositions()
	{
		$shownpositions = Mage::getStoreConfig('sociallogin/general/position',Mage::app()->getStore()->getId());
		$shownpositions = explode(',',$shownpositions);
		//Zend_debug::dump($this->getBlockPosition());
		//Zend_debug::dump($shownpositions);die();
		return $shownpositions;
	}
	
	public function isShow()
	{	
		if(in_array($this->getBlockPosition(),$this->getShownPositions())){
			return true;
		}
		return false;
	}
	
	protected function _beforeToHtml()
	{
		if(!Mage::helper('magenotification')->checkLicenseKey('Sociallogin')){
			$this->setTemplate(null);
		}
		
		if(!$this->isShow()){
			$this->setTemplate(null);
		}		
		return parent::_beforeToHtml();
	}
}
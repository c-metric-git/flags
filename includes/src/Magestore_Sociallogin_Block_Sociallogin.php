<?php
class Magestore_Sociallogin_Block_Sociallogin extends Mage_Core_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('sociallogin/sociallogin_buttons.phtml');
	}
	
	public function isShowFaceBookButton()
	{
		return (int) Mage::getStoreConfig('sociallogin/fblogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowGmailButton()
	{
		return (int) Mage::getStoreConfig('sociallogin/gologin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowTwitterButton()
	{
		return (int) Mage::getStoreConfig('sociallogin/twlogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowYahooButton()
	{
		return (int) Mage::getStoreConfig('sociallogin/yalogin/is_active',Mage::app()->getStore()->getId());
	}		  
	
	public function getDirection()
	{
		return Mage::getStoreConfig('sociallogin/general/direction',Mage::app()->getStore()->getId());
	}
	
	public function getIsActive()
	{
		return (int) Mage::getStoreConfig('sociallogin/general/is_active',Mage::app()->getStore()->getId());
	}	
	
	public function getFacebookButton()
	{
		return $this->getLayout()->createBlock('sociallogin/fblogin')
					->setTemplate('sociallogin/bt_fblogin.phtml')->toHtml();
		
	}
	
	public function getGmailButton()
	{
		return $this->getLayout()->createBlock('sociallogin/gologin')
					->setTemplate('sociallogin/bt_gologin.phtml')->toHtml();
	
	}

	public function getTwitterButton()
	{
		return $this->getLayout()->createBlock('sociallogin/twlogin')
					->setTemplate('sociallogin/bt_twlogin.phtml')->toHtml();
		
	}

	public function getYahooButton()
	{
		return $this->getLayout()->createBlock('sociallogin/yalogin')
					->setTemplate('sociallogin/bt_yalogin.phtml')->toHtml();
	}	

	public function isShowOpenButton()
    {
        return (int) Mage::getStoreConfig('sociallogin/openlogin/is_active',Mage::app()->getStore()->getId());
    }
	
	public function getOpenButton()
	{
		return $this->getLayout()->createBlock('sociallogin/openlogin')
					->setTemplate('sociallogin/bt_openlogin.phtml')->toHtml();
	}	
	
	public function isShowLjButton()
    {
        return (int) Mage::getStoreConfig('sociallogin/ljlogin/is_active',Mage::app()->getStore()->getId());
    }
	
	public function getLjButton()
	{
		return $this->getLayout()->createBlock('sociallogin/ljlogin')
					->setTemplate('sociallogin/bt_ljlogin.phtml')->toHtml();
	}	

	
	public function getLinkedButton()
	{
		return $this->getLayout()->createBlock('sociallogin/linkedlogin')
					->setTemplate('sociallogin/bt_linkedlogin.phtml')->toHtml();
	}	
	
	public function isShowLinkedButton(){
		return (int) Mage::getStoreConfig('sociallogin/linklogin/is_active',Mage::app()->getStore()->getId());
	}
	// by Hai.Ta
	public function isShowAolButton()
    {
        return (int) Mage::getStoreConfig('sociallogin/aollogin/is_active',Mage::app()->getStore()->getId());
    }
    
    public function isShowWpButton()
    {
        return (int) Mage::getStoreConfig('sociallogin/wplogin/is_active',Mage::app()->getStore()->getId());
    }
	
	public function isShowCalButton()
	{
		return (int) Mage::getStoreConfig('sociallogin/callogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowOrgButton()
	{
		return (int) Mage::getStoreConfig('sociallogin/orglogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowFqButton()
	{
		return (int) Mage::getStoreConfig('sociallogin/fqlogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowLiveButton()
	{
		return (int) Mage::getStoreConfig('sociallogin/livelogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowMpButton()
	{
		return (int) Mage::getStoreConfig('sociallogin/mplogin/is_active',Mage::app()->getStore()->getId());
	}
	
    public function getAolButton()
    {        
        return $this->getLayout()->createBlock('sociallogin/aollogin')
                ->setTemplate('sociallogin/bt_aollogin.phtml')->toHtml();
    }
    
    public function getWpButton()
    {
        return $this->getLayout()->createBlock('sociallogin/wplogin')
                ->setTemplate('sociallogin/bt_wplogin.phtml')->toHtml();
    }
    
    public function getAuWp()
    {        
        return $this->getLayout()->createBlock('sociallogin/wplogin')
                ->setTemplate('sociallogin/au_wp.phtml')->toHtml();
    }
    
	public function getCalButton()
    {
        return $this->getLayout()->createBlock('sociallogin/callogin')
                ->setTemplate('sociallogin/bt_callogin.phtml')->toHtml();
    }
	
	public function getAuCal()
    {        
        return $this->getLayout()->createBlock('sociallogin/calllogin')
                ->setTemplate('sociallogin/au_cal.phtml')->toHtml();
    }
	
	public function getOrgButton()
    {
        return $this->getLayout()->createBlock('sociallogin/orglogin')
                ->setTemplate('sociallogin/bt_orglogin.phtml')->toHtml();
    }
	
	public function getFqButton()
	{
		return $this->getLayout()->createBlock('sociallogin/fqlogin')
				->setTemplate('sociallogin/bt_fqlogin.phtml')->toHtml();
	}
    
    public function getLiveButton()
	{
		return $this->getLayout()->createBlock('sociallogin/livelogin')
				->setTemplate('sociallogin/bt_livelogin.phtml')->toHtml();
	}
	
	public function getMpButton()
	{	
		return $this->getLayout()->createBlock('sociallogin/mplogin')
				->setTemplate('sociallogin/bt_mplogin.phtml')->toHtml();	
	}
	//end Hai.Ta
	//by Chun
	public function isShowPerButton()
	{
		return (int) Mage::getStoreConfig('sociallogin/perlogin/is_active',Mage::app()->getStore()->getId());
	}
	public function getPerButton()
	{	
		return $this->getLayout()->createBlock('sociallogin/perlogin')
				->setTemplate('sociallogin/bt_perlogin.phtml')->toHtml();	
	}
	public function isShowSeButton()
	{
		return (int) Mage::getStoreConfig('sociallogin/selogin/is_active',Mage::app()->getStore()->getId());
	}
	public function getSeButton()
	{	
		return $this->getLayout()->createBlock('sociallogin/selogin')
				->setTemplate('sociallogin/bt_selogin.phtml')->toHtml();	
	}
	//end Chun
	
    protected function _beforeToHtml()
	{
		if(!$this->getIsActive()){
			$this->setTemplate(null);
		}
		
		if(!Mage::helper('magenotification')->checkLicenseKey('Sociallogin')){
			$this->setTemplate(null);
		}			
		
		if(Mage::getSingleton('customer/session')->isLoggedIn()){
			$this->setTemplate(null);
		}
                if($this->getTemplate()=='sociallogin/toplinks/toplinks.phtml'){
                    if(Mage::registry('shown_sociallogin_button')){
                            $this->setTemplate(null);
                    } elseif($this->getTemplate()){
                            Mage::register('shown_sociallogin_button',true);
                    }
                }
		
		return parent::_beforeToHtml();
	}	
	
	public function sortOrderFaceBook()
	{
		return (int) Mage::getStoreConfig('sociallogin/fblogin/sort_order');
	}
	
	public function sortOrderGmail()
	{
		return (int) Mage::getStoreConfig('sociallogin/gologin/sort_order');
	}
	
	public function sortOrderTwitter()
	{
		return (int) Mage::getStoreConfig('sociallogin/twlogin/sort_order');
	}
	
	public function sortOrderYahoo()
	{
		return (int) Mage::getStoreConfig('sociallogin/yalogin/sort_order');
	}	
	
	public function sortOrderOpen()
    {
        return (int) Mage::getStoreConfig('sociallogin/openlogin/sort_order');
    }
	
	public function sortOrderLj()
    {
        return (int) Mage::getStoreConfig('sociallogin/ljlogin/sort_order');
    }
	
	public function sortOrderLinked(){
		return (int) Mage::getStoreConfig('sociallogin/linklogin/sort_order');
	}
	
	public function sortOrderAol()
    {
        return (int) Mage::getStoreConfig('sociallogin/aollogin/sort_order',Mage::app()->getStore()->getId());
    }
    
    public function sortOrderWp()
    {
        return (int) Mage::getStoreConfig('sociallogin/wplogin/sort_order',Mage::app()->getStore()->getId());
    }
	
	public function sortOrderCal()
	{
		return (int) Mage::getStoreConfig('sociallogin/callogin/sort_order',Mage::app()->getStore()->getId());
	}
	
	public function sortOrderOrg()
	{
		return (int) Mage::getStoreConfig('sociallogin/orglogin/sort_order',Mage::app()->getStore()->getId());
	}
	
	public function sortOrderFq()
	{
		return (int) Mage::getStoreConfig('sociallogin/fqlogin/sort_order',Mage::app()->getStore()->getId());
	}
	
	public function sortOrderLive()
	{
		return (int) Mage::getStoreConfig('sociallogin/livelogin/sort_order',Mage::app()->getStore()->getId());
	}
	
	public function sortOrderMp()
	{
		return (int) Mage::getStoreConfig('sociallogin/mplogin/sort_order',Mage::app()->getStore()->getId());
	}
	public function sortOrderPer()
	{
		return (int) Mage::getStoreConfig('sociallogin/perlogin/sort_order',Mage::app()->getStore()->getId());
	}
	public function sortOrderSe()
	{
		return (int) Mage::getStoreConfig('sociallogin/selogin/sort_order',Mage::app()->getStore()->getId());
	}
	
	// by King140115
	public function isShowVkButton()
	{
		return (int) Mage::getStoreConfig('sociallogin/vklogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function getVkButton()
	{	
		return $this->getLayout()->createBlock('sociallogin/vklogin')
				->setTemplate('sociallogin/bt_vklogin.phtml')->toHtml();	
	}
	
	public function sortOrderVk()
	{
		return (int) Mage::getStoreConfig('sociallogin/vklogin/sort_order',Mage::app()->getStore()->getId());
	}
	//end King140115
        
        
	public function isShowInsButton(){
		return (int) Mage::getStoreConfig('sociallogin/instalogin/is_active',Mage::app()->getStore()->getId());
	}
	public function sortOrderIns(){
		return (int) Mage::getStoreConfig('sociallogin/instalogin/sort_order',Mage::app()->getStore()->getId());
	}
	public function getInsButton()
	{
		return $this->getLayout()->createBlock('sociallogin/inslogin')
					->setTemplate('sociallogin/bt_inslogin.phtml')->toHtml();
		
	}
	
	public function isShowAmazonButton(){
		return (int) Mage::getStoreConfig('sociallogin/amazonlogin/is_active',Mage::app()->getStore()->getId())&&Mage::helper('sociallogin')->getAmazonId();
	}
	public function sortOrderAmazon(){
		return (int) Mage::getStoreConfig('sociallogin/amazonlogin/sort_order',Mage::app()->getStore()->getId());
	}
	public function getAmazonButton()
	{
		return $this->getLayout()->createBlock('sociallogin/amazon')
					->setTemplate('sociallogin/bt_amazonlogin.phtml')->toHtml();
		
	}
	
	public function makeArrayButton(){
		$buttonArray = array();
		  if ($this->isShowAmazonButton())
			$buttonArray[] = array(
			'button'=>$this->getAmazonButton(),
			'check' =>$this->isShowAmazonButton(),
			'id'	=> 'bt-loginamazon',
			'sort'  => $this->sortOrderAmazon()
			);
                if ($this->isShowInsButton())
			$buttonArray[] = array(
			'button'=>$this->getInsButton(),
			'check' =>$this->isShowInsButton(),
			'id'	=> 'bt-loginins',
			'sort'  => $this->sortOrderIns()
			);
        if ($this->isShowFaceBookButton())
			$buttonArray[] = array(
			'button'=>$this->getFacebookButton(),
			'check' =>$this->isShowFaceBookButton(),
			'id'	=> 'bt-loginfb',
			'sort'  => $this->sortOrderFaceBook()
			);
        if ($this->isShowGmailButton())
			$buttonArray[] = array(
			'button'=>$this->getGmailButton(),
			'check'=>$this->isShowGmailButton(),
			'id'	=> 'bt-logingo',
			'sort'=> $this->sortOrderGmail()
			);
        if ($this->isShowTwitterButton())
			$buttonArray[] = array(
			'button'=>$this->getTwitterButton(),
			'check'=>$this->isShowTwitterButton(),
			'id'	=> 'bt-logintw',
			'sort'=>$this->sortOrderTwitter()
			);
        if ($this->isShowYahooButton())
			$buttonArray[] = array(
			'button'=>$this->getYahooButton(),
			'check'=>$this->isShowYahooButton(),
			'id'	=> 'bt-loginya',
			'sort'=>$this->sortOrderYahoo()
			);
        if ($this->isShowAolButton())
			$buttonArray[] = array(
			'button'=>$this->getAolButton(),
			'check'=>$this->isShowAolButton(),
			'id'	=> 'bt-loginaol',
			'sort'=>$this->sortOrderAol()
			);
        if ($this->isShowWpButton())
			$buttonArray[] = array(
			'button'=>$this->getWpButton(),
			'check'=>$this->isShowWpButton(),
			'id'	=> 'bt-loginwp',
			'sort'=>$this->sortOrderWp()
			);
        if ($this->isShowCalButton())
			$buttonArray[] = array(
			'button'=>$this->getCalButton(),
			'check'=>$this->isShowCalButton(),
			'id'	=> 'bt-logincal',
			'sort'=>$this->sortOrderCal()
			);
        if ($this->isShowOrgButton())
			$buttonArray[] = array(
			'button'=>$this->getOrgButton(),
			'check'=>$this->isShowOrgButton(),
			'id'	=> 'bt-loginorg',
			'sort'=>$this->sortOrderOrg()
			);
        if ($this->isShowFqButton())
			$buttonArray[] = array(
			'button'=>$this->getFqButton(),
			'check'=>$this->isShowFqButton(),
			'id'	=> 'bt-loginfq',
			'sort'=>$this->sortOrderFq()
			);
        if ($this->isShowLiveButton())
			$buttonArray[] = array(
			'button'=>$this->getLiveButton(),
			'check'=>$this->isShowLiveButton(),
			'id'	=> 'bt-loginlive',
			'sort'=>$this->sortOrderLive()
			);
        if ($this->isShowMpButton())
			$buttonArray[] = array(
			'button'=>$this->getMpButton(),
			'check'=>$this->isShowMpButton(),
			'id'	=> 'bt-loginmp',
			'sort'=>$this->sortOrderMp()
			);
        if ($this->isShowLinkedButton())
			$buttonArray[] = array(
			'button'=>$this->getLinkedButton(),
			'check'=>$this->isShowLinkedButton(),
			'id'	=> 'bt-loginlinked',
			'sort'=>$this->sortOrderLinked()
			);
        if ($this->isShowOpenButton())
			$buttonArray[] = array(
			'button'=>$this->getOpenButton(),
			'check'=>$this->isShowOpenButton(),
			'id'	=> 'bt-loginopen',
			'sort'=>$this->sortOrderOpen()
			);
        if ($this->isShowLjButton())
			$buttonArray[] = array(
			'button'=>$this->getLjButton(),
			'check'=>$this->isShowLjButton(),
			'id'	=> 'bt-loginlj',
			'sort'=>$this->sortOrderLj()
			);
		if ($this->isShowPerButton())
			
			$buttonArray[] = array(
			'button'=>$this->getPerButton(),
			'check'=>$this->isShowPerButton(),
			'id'	=> 'bt-loginper',
			'sort'=>$this->sortOrderPer()
			);
		if ($this->isShowSeButton())			
			$buttonArray[] = array(
			'button'=>$this->getSeButton(),
			'check'=>$this->isShowSeButton(),
			'id'	=> 'bt-loginse',
			'sort'=>$this->sortOrderSe()
			);
		if ($this->isShowVkButton())			
			$buttonArray[] = array(
			'button'=>$this->getVkButton(),
			'check'=>$this->isShowVkButton(),
			'id'	=> 'bt-loginvk',
			'sort'=>$this->sortOrderVk()
			);
		usort($buttonArray, array($this, 'compareSortOrder'));
		return $buttonArray;
	}
	
	public function compareSortOrder($a, $b) {
		if ($a['sort'] == $b['sort']) return 0;
		return $a['sort'] < $b['sort'] ? -1 : 1;
	}
	
	public function getNumberShow(){
		return (int) Mage::getStoreConfig('sociallogin/general/number_show',Mage::app()->getStore()->getId());
	}
}
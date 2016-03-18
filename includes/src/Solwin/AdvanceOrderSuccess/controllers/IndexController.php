<?php
class Solwin_AdvanceOrderSuccess_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("advanceorder"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("advanceorder", array(
                "label" => $this->__("advanceorder"),
                "title" => $this->__("advanceorder")
		   ));

      $this->renderLayout(); 
	  
    }
}
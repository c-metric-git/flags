<?php
class Chapagain_ClearPrint_Checkout_CartController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {						
		$this->loadLayout();		
		$this->renderLayout();
    }
}

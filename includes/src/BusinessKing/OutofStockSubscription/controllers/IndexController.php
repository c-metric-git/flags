<?php

/**
 * Out of Stock Subscription index controller
 *
 * @category    BusinessKing
 * @package     BusinessKing_OutofStockSubscription
 */
class BusinessKing_OutofStockSubscription_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{ 
		$productId = $this->getRequest()->getPost('product');
		$email = $this->getRequest()->getPost('subscription_email');
		$result=array();
		if ($email && $productId) {
			
			Mage::getModel('outofstocksubscription/info')->saveSubscrition($productId, $email);
			
			//$this->_getSession()->addSuccess($this->__('Subscription added successfully.'));
						
			$product = Mage::getModel('catalog/product')->load($productId);
			//$product->getProductUrl();
			$url = $product->getData('url_path');
			//$this->_redirect('catalog/product/view', array('id'=>$productId));
			//$this->_redirect($url);
            $result['r']='success';
            $result['message']='Subscription added successfully.';
            $this->getResponse()->setBody(json_encode($result));
		}
		else {
			$this->_redirect('');
		}		
	}
	
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    protected function testAction()
    {
    	$url=getimagesize('http://flagsrus.stripedsocks.com/media/magebuzz/featuredcategories/resized/120x120/cd1445mm.jpg');
       if(is_array($url))
       {
       	echo "true";
       }
       else
       {
       	echo 'false';
       }
    }
}
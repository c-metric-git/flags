<?php
class Chapagain_ClearPrint_Catalog_ProductController extends Mage_Core_Controller_Front_Action
{
    public function viewAction()
    {	
		$productId = $this->getRequest()->getParam('id');
		$product = Mage::getModel('catalog/product')->load($productId);
        Mage::register('product', $product);
        Mage::register('current_product', $product);
        			
		#$this->loadLayout();     
		$update = $this->getLayout()->getUpdate();
		$update->addHandle('default');
		$this->addActionLayoutHandles();
		$update->addHandle('clearprint_PRODUCT_TYPE_'.$product->getTypeId());
		$this->loadLayoutUpdates();
		$this->generateLayoutXml();
		$this->generateLayoutBlocks();
		$this->_isLayoutLoaded = true;
		
		$this->renderLayout();
    }
}

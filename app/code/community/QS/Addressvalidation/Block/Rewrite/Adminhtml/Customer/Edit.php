<?php
/**
 * Customer edit block
 *
 * @category   
 * @package    
 * @author     
 */
class QS_Addressvalidation_Block_Rewrite_Adminhtml_Customer_Edit  extends Mage_Adminhtml_Block_Customer_Edit {
    
    public function __construct() {
        parent::__construct();
        $this->setTemplate('addressvalidation/widget/form/container.phtml');
    }    
    
}
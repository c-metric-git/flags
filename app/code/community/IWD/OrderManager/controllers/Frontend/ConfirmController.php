<?php
class IWD_OrderManager_Frontend_ConfirmController extends Mage_Core_Controller_Front_Action
{
    /** http://site.com/iwd_order_manager/confirm/edit/action/confirm/pid/000000000000000 **/
    /** http://site.com/iwd_order_manager/confirm/edit/action/cancel/pid/000000000000000 **/
    public function editAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');

        $cms_block = $this->_confirm();
        $this->getLayout()->getBlock('content')->insert($cms_block,'iwd_ordermanager_confirm');

        $this->renderLayout();
    }

    protected function _confirm(){
        $action = $this->getRequest()->getParam('action');
        $pid = $this->getRequest()->getParam('pid');

        /** error **/
        if(empty($action) || empty($pid)){
            return $this->getLayout()->createBlock('cms/block')->setBlockId('iwd_ordermanager_confirm_error');
        }
        /** confirm **/
        if($action == 'confirm'){
            $status = Mage::getModel('iwd_ordermanager/confirm_operations')->confirmByPid($pid);
            if ($status) {
                return $this->getLayout()->createBlock('cms/block')->setBlockId('iwd_ordermanager_confirm_success');
            }
        }

        /** cancel **/
        else if($action == 'cancel'){
            $status = Mage::getModel('iwd_ordermanager/confirm_operations')->cancelConfirmByPid($pid);
            if ($status) {
                return $this->getLayout()->createBlock('cms/block')->setBlockId('iwd_ordermanager_confirm_cancel');
            }
        }

        return $this->getLayout()->createBlock('cms/block')->setBlockId('iwd_ordermanager_confirm_error');
    }
}
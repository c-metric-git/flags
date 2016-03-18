<?php
class IWD_OrderManager_Adminhtml_Sales_CommentController extends Mage_Adminhtml_Controller_Action
{
    public function deleteAction()
    {
        $type = $this->getRequest()->getParam('type');
        $comment_id = $this->getRequest()->getParam('comment_id');

        $result['status'] =  Mage::getModel('iwd_ordermanager/comment')->deleteComment($type, $comment_id);

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function updateAction()
    {
        $type = $this->getRequest()->getParam('type');
        $comment_id = $this->getRequest()->getParam('comment_id');
        $comment_text = $this->getRequest()->getParam('comment_text');

        $result['comment'] = Mage::getModel('iwd_ordermanager/comment')->updateComment($type, $comment_id, $comment_text);

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function getCommentAction()
    {
        $type = $this->getRequest()->getParam('type');
        $comment_id = $this->getRequest()->getParam('comment_id');

        $comment = Mage::getModel('iwd_ordermanager/comment')->getComment($type, $comment_id);

        $result['comment'] = $this->getLayout()
            ->createBlock('iwd_ordermanager/adminhtml_sales_order_comment_form')
            ->setData('comment', $comment)
            ->setData('comment_id', $comment_id)
            ->toHtml();

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _isAllowed()
    {
        $action = $this->getRequest()->getActionName();
        $action = strtolower($action);

        if($action == 'getcomment' || $action == 'update'){
            return Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/order/actions/edit_comment');
        }
        if($action == 'delete'){
            return Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/order/actions/delete_comment');
        }

        return false;
    }
}
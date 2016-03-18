<?php
class IWD_OrderManager_Model_Comment extends Mage_Core_Model_Abstract
{

    public function getComment($type, $comment_id)
    {
        if (isset($type)) {
            switch ($type) {
                case "order":
                    return Mage::getModel('sales/order_status_history')->load($comment_id)->getComment();
                case "creditmemo":
                    return Mage::getModel('sales/order_creditmemo_comment')->load($comment_id)->getComment();
                case "invoice":
                    return Mage::getModel('sales/order_invoice_comment')->load($comment_id)->getComment();
                case "shipment":
                    return Mage::getModel('sales/order_shipment_comment')->load($comment_id)->getComment();
            }
        }
        return "";
    }

    public function updateComment($type, $id, $comment_text)
    {

        if (isset($type)) {
            switch ($type) {
                case "order":
                    return $this->_editComment($id, "order_status_history", $comment_text, $type);
                case "creditmemo":
                    return $this->_editComment($id, "order_creditmemo_comment", $comment_text, $type);
                case "invoice":
                    return $this->_editComment($id, "order_invoice_comment", $comment_text, $type);
                case "shipment":
                    return $this->_editComment($id, "order_shipment_comment", $comment_text, $type);
            }
        }
        return null;
    }

    public function deleteComment($type, $comment_id)
    {
        if ($type) {
            switch ($type) {
                case "order":
                    return $this->_deleteComment($comment_id, "order_status_history", $type);
                case "creditmemo":
                    return $this->_deleteComment($comment_id, "order_creditmemo_comment", $type);
                case "invoice":
                    return $this->_deleteComment($comment_id, "order_invoice_comment", $type);
                case "shipment":
                    return $this->_deleteComment($comment_id, "order_shipment_comment", $type);
            }
        }

        return 0;
    }


    protected function _editComment($id, $model, $new_comment, $type)
    {

        try {
            $comment = Mage::getModel('sales/' . $model)->load($id);
            Mage::dispatchEvent('iwd_ordermanager_sales_comment_update_after', array('comment' => $comment, 'type' => $type));

            $new_comment = Mage::helper('core')->escapeHtml($new_comment, array('b', 'br', 'strong', 'i', 'u'));
            $comment->setComment($new_comment);
            $comment->save();

            Mage::dispatchEvent('iwd_ordermanager_sales_comment_update_before', array('comment' => $comment, 'type' => $type));
            return $comment->getComment();
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            return null;
        }
    }

    protected function _deleteComment($id, $model, $type)
    {
        try {
            $comment = Mage::getModel('sales/' . $model)->load($id);
            Mage::dispatchEvent('iwd_ordermanager_sales_comment_delete_after', array('comment' => $comment, 'type' => $type));

            $comment->delete();

            Mage::dispatchEvent('iwd_ordermanager_sales_comment_delete_before', array('comment' => $comment, 'type' => $type));
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            return 0;
        }
        return 1;
    }
}
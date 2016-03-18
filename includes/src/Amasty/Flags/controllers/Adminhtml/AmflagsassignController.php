<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Adminhtml_AmflagsassignController extends Mage_Adminhtml_Controller_Action
{
    public function setFlagAction()
    {
        $orderId  = $this->getRequest()->getParam('orderId');
        $flagId   = $this->getRequest()->getParam('flagId');
        $columnId = $this->getRequest()->getParam('columnId');
        $comment  = $this->getRequest()->getParam('comment');
        if ($orderId)
        {
            try
            {
                $orderFlags = Mage::getModel('amflags/order_flag')->getCollection();
                $orderFlags->getSelect()->where('order_id = ?', $orderId)->where('column_id = ?', $columnId);
                if ($orderFlags->getSize() > 0)
                {
                    foreach ($orderFlags as $orderFlag)
                        if (0 == $flagId)
                        {
                            // removing flag
                            $orderFlag->delete();
                        } else
                        {
                            $orderFlag->setOrderId($orderId);
                            $orderFlag->setFlagId($flagId);
                            $orderFlag->setColumnId($columnId);
                            $orderFlag->setComment($comment);
                            $orderFlag->save();
                        }
                } else
                {
                    $data['order_id']  = $orderId;
                    $data['flag_id']   = $flagId;
                    $data['column_id'] = $columnId;
                    $data['comment']   = $comment;
                    Mage::getModel('amflags/order_flag')->setData($data)->save();
                }
            } catch (Exception $e)
            {
                $this->_getSession()->addException($e, Mage::helper('amflags')->__('An error occurred while setting order flag.'));
            }
        }
        return true;
    }

    public function massApplyAction()
    {
        $orderIds = Mage::app()->getRequest()->getPost('order_ids'); // sometimes 'order_ids' = Mage::app()->getRequest()->getPost('massaction_prepare_key')
        if (is_array($orderIds) && !empty($orderIds))
        {
            $columnId = Mage::app()->getRequest()->getParam('column');
            $flagId = Mage::app()->getRequest()->getPost('flags_' . $columnId);

            if (strlen($columnId))
            {
                if ('0' === $columnId)
                {
                    // remove flags
                    $all = false;
                    if ('0' === $flagId)
                    {
                        $all = true;
                        $columnCollection = Mage::getModel('amflags/column')->getCollection();
                    }
                    foreach ($orderIds as $orderId)
                    {
                        try
                        {
                            if ($all)
                            {
                                // remove flags from all columns
                                foreach ($columnCollection as $column)
                                {
                                    $orderFlag = Mage::getModel('amflags/order_flag')->loadByColumnIdAndOrderId($orderId, $column->getEntityId());
                                    $orderFlag->delete();
                                }
                            } else
                            {
                                $orderFlag = Mage::getModel('amflags/order_flag')->loadByColumnIdAndOrderId($orderId, $flagId);
                                $orderFlag->delete();
                            }
                        } catch (Exception $e)
                        {
                            Mage::getSingleton('adminhtml/session')->addException($e, Mage::helper('amflags')->__('An error occurred while removing flags.'));
                            $this->_redirect('adminhtml/sales_order');
                            return;
                        }
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amflags')->__('The flags have been removed.'));
                } else
                {
                    foreach ($orderIds as $orderId)
                    {
                        try
                        {
                            $orderFlag = Mage::getModel('amflags/order_flag')->loadByColumnIdAndOrderId($orderId, $columnId);
                            $orderFlag->setOrderId($orderId);
                            $orderFlag->setFlagId($flagId);
                            $orderFlag->setColumnId($columnId);
                            $orderFlag->save();
                        } catch (Exception $e)
                        {
                            Mage::getSingleton('adminhtml/session')->addException($e, Mage::helper('amflags')->__('An error occurred while setting order flags.'));
                            $this->_redirect('adminhtml/sales_order');
                            return;
                        }
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amflags')->__('The flags have been applied.'));
                }
                $this->_redirect('adminhtml/sales_order/');
                return;
            } else
            {
                Mage::getSingleton('adminhtml/session')->addError($this->__('No action specified.'));
                $this->_redirect('adminhtml/sales_order');
                return;
            }
        } else
        {
            Mage::getSingleton('adminhtml/session')->addError($this->__('No orders selected.'));
            $this->_redirect('adminhtml/sales_order');
            return;
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/assign_flags');
    }
}
<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Adminhtml_AmflagsflagController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/amflags')
            ->_addBreadcrumb(Mage::helper('amflags')->__('Sales'), Mage::helper('amflags')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('amflags')->__('Flags'), Mage::helper('amflags')->__('Flags'))
        ;
        return $this;
    }
    
    public function indexAction()
    {
        $this->_title($this->__('Flags'));
             
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('amflags/adminhtml_flag'))
            ->renderLayout();
    }
    
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }
    
    public function editAction()
    {
        $this->_title($this->__('Flags'))->_title($this->__('Edit Flag'));
        
        $id   = $this->getRequest()->getParam('flag_id');
        $flag = Mage::getModel('amflags/flag');
        if ($id) 
        {
            $flag->load($id);
            if (!$flag->getId()) 
            {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amflags')->__('This flag no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        
        // Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) 
        {
            $flag->setData($data);
        }
        
        Mage::register('amflags_flag', $flag);
             
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('amflags/adminhtml_flag_edit'))
            ->_addLeft($this->getLayout()->createBlock('amflags/adminhtml_flag_edit_tabs'))
            ->renderLayout();
    }
    
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) 
        {
            $flagId = $this->getRequest()->getParam('flag_id');
            $model  = Mage::getModel('amflags/flag');
            if ($flagId) {
                $model->load($flagId);
            }
            if (isset($data['apply_status'])) {
                $data['apply_status'] = implode(',', $data['apply_status']);
            } else {
				$data['apply_status'] = '';
			}
            if (isset($data['apply_shipping'])) {
                $data['apply_shipping'] = implode(',', $data['apply_shipping']);
            } else {
				$data['apply_shipping'] = '';
			}
            if (isset($data['apply_payment'])) {
                $data['apply_payment'] = implode(',', $data['apply_payment']);
            } else {
				$data['apply_payment'] = '';
			}
            $model->setData($data);
            try 
            {
                $model->save();
                $flagId = $model->getId();
                
                // check if flag applied to column
                $column = Mage::getModel('amflags/column')->load($data['apply_column']);
                $appliedFlags = explode(',', $column->getApplyFlag());
                if (!in_array($flagId, $appliedFlags)) {
                    $appliedFlags[] = $flagId;
                    $column->setData('apply_flag', implode(',', $appliedFlags));
                    $column->save();
                }
                
                if (isset($_FILES['flag_image']['error']) && UPLOAD_ERR_OK == $_FILES['flag_image']['error'])
                {
                    // trying to upload image
                    $uploader = new Varien_File_Uploader('flag_image');
                    $uploader->setFilesDispersion(false);
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->save(Amasty_Flags_Model_Flag::getUploadDir(), $model->getId() . '.jpg');
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amflags')->__('The flag has been saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                $this->_redirect('*/*/');
                return;
                
            } catch (Exception $e) 
            {
                $this->_getSession()->addException($e, Mage::helper('amflags')->__('An error occurred while saving the flag: ') . $e->getMessage());
            }
            
            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('flag_id' => $flagId));
            return;
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('flag_id')) 
        {
            try 
            {
                $model = Mage::getModel('amflags/flag');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amflags')->__('The flag has been deleted.'));
                $this->_redirect('*/*/');
                return;
                
            } catch (Exception $e) 
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('flag_id' => $id));
                return;
            }
        }
    }

    /*public function setFlagAction()
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
    }*/

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/amflags/flags');
    }
}
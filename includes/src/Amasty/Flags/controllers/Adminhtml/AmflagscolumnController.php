<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Adminhtml_AmflagscolumnController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/amflags')
            ->_addBreadcrumb(Mage::helper('amflags')->__('Sales'),   Mage::helper('amflags')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('amflags')->__('Columns'), Mage::helper('amflags')->__('Columns'))
        ;
        return $this;
    }
    
    public function indexAction()
    {
        $this->_title($this->__('Columns'));
             
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('amflags/adminhtml_column'))
            ->renderLayout();
    }
    
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }
    
     public function editAction()
    {
        $this->_title($this->__('Columns'))->_title($this->__('Edit Column'));
        
        $id   = $this->getRequest()->getParam('column_id');
        $column = Mage::getModel('amflags/column');
        if ($id) 
        {
            $column->load($id);
            if (!$column->getId()) 
            {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amflags')->__('This column no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        
        // Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) 
        {
            $column->setData($data);
        }
        
        Mage::register('amflags_column', $column);
             
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('amflags/adminhtml_column_edit'))
            ->_addLeft($this->getLayout()->createBlock('amflags/adminhtml_column_edit_tabs'))
            ->renderLayout();
    }
    
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) 
        {
            $columnId = $this->getRequest()->getParam('column_id');
            $model  = Mage::getModel('amflags/column');
            if ($columnId) 
            {
                $model->load($columnId);
            }
            if (isset($data['apply_flag']))
            {
                $data['apply_flag'] = implode(',', $data['apply_flag']);
            }
            $model->setData($data);
            try 
            {
                $model->save();
                $columnId = $model->getId();
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amflags')->__('The column has been saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                $this->_redirect('*/*/');
                return;
                
            } catch (Exception $e) 
            {
                $this->_getSession()->addException($e, Mage::helper('amflags')->__('An error occurred while saving the column: ') . $e->getMessage());
            }
            
            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('column_id' => $columnId));
            return;
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('column_id')) 
        {
            try 
            {
                $model = Mage::getModel('amflags/column');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amflags')->__('The column has been deleted.'));
                $this->_redirect('*/*/');
                return;
                
            } catch (Exception $e) 
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('column_id' => $id));
                return;
            }
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/amflags/columns');
    }
}
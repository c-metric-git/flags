<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Model_Observer
{
    protected $_controllerNames = array('sales_', 'orderspro_');
    protected $_exportActions = array('exportCsv', 'exportExcel');
    protected $_permissibleActions = array('index', 'grid');

    static $collectionModified = false;

    public function onSalesOrderSaveAfter($observer)
    {
        $order = $observer->getOrder();

        if ($order->getOrigData('status') != $order->getData('status'))
        {
            $collection = Mage::getModel('amflags/flag')->getCollection();
            $collection->addFieldToFilter('apply_status', array('finset' => $order->getData('status')));
            $collection->setOrder('priority', 'DESC');
            if ($collection->getSize() > 0)
            {
                foreach ($collection as $applyFlag)
                {
                    $orderFlag = Mage::getModel('amflags/order_flag')->loadByColumnIdAndOrderId($order->getId(), $applyFlag->getApplyColumn());
                    $orderFlag->setOrderId($order->getId());
                    $orderFlag->setFlagId($applyFlag->getEntityId());
                    $orderFlag->setComment($applyFlag->getComment());
                    $orderFlag->setColumnId($applyFlag->getApplyColumn());
                    $orderFlag->save();
                }
            }
        }

        if (!$order->getOrigData('entity_id'))
        {
            $collection = Mage::getModel('amflags/flag')->getCollection();
            $collection->addFieldToFilter(
                'apply_shipping',
                array('finset' => $order->getData('shipping_method'))
            );
            $collection->setOrder('priority', 'DESC');
            if ($collection->getSize() > 0)
            {
                foreach ($collection as $applyFlag)
                {
                    $orderFlag = Mage::getModel('amflags/order_flag')->loadByColumnIdAndOrderId($order->getId(), $applyFlag->getApplyColumn());
                    $orderFlag->setOrderId($order->getId());
                    $orderFlag->setFlagId($applyFlag->getEntityId());
                    $orderFlag->setComment($applyFlag->getComment());
                    $orderFlag->setColumnId($applyFlag->getApplyColumn());
                    $orderFlag->save();
                }
            }
        }

        if (!$order->getOrigData('entity_id'))
        {
            $collection = Mage::getModel('amflags/flag')->getCollection();
            $paymentMethod = $order->getPayment()->getMethod();
            $collection->addFieldToFilter('apply_payment', array('finset' => $paymentMethod));
            $collection->setOrder('priority', 'DESC');
            if ($collection->getSize() > 0)
            {
                foreach ($collection as $applyFlag)
                {
                    $orderFlag = Mage::getModel('amflags/order_flag')->loadByColumnIdAndOrderId($order->getId(), $applyFlag->getApplyColumn());
                    $orderFlag->setOrderId($order->getId());
                    $orderFlag->setFlagId($applyFlag->getEntityId());
                    $orderFlag->setComment($applyFlag->getComment());
                    $orderFlag->setColumnId($applyFlag->getApplyColumn());
                    $orderFlag->save();
                }
            }
        }
    }

    public function addNewActions($observer)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/assign_flags')
            && $this->_isSalesGrid($observer->getPage()))
        {
            $block = $observer->getBlock();

            $block->addItem('amflags_separator_pre', array(
                'label'=> Mage::helper('amflags')->__('---Order-Flags-Actions---'),
                'url'  => ''
            ));

            $columnCollection = Mage::getModel('amflags/column')->getCollection();
            $columns = array();
            foreach ($columnCollection as $column)
            {
                if ($column->getApplyFlag())
                {
                    $columns[] = array('value' => $column->getEntityId(), 'label' => '"' . $column->getAlias() . '"');
                    $columnFlags = explode(',', $column->getApplyFlag());
                    $flags = Mage::getModel('amflags/flag')->getCollection();
                    $values   = array();

                    foreach ($flags as $flag)
                    {
                        if (in_array($flag->getEntityId(), $columnFlags))
                        {
                            $url = Amasty_Flags_Model_Flag::getUploadUrl() . $flag['entity_id'] . '.jpg';
                            $style = 'background-image:url('. $url .'); background-repeat:no-repeat; padding-left:25px;';
                            $values[] = array('value' => $flag->getEntityId(), 'label' => $flag->getAlias(), 'style' => $style);
                        }
                    }
                    $block->addItem('amflags_apply_' . $column->getEntityId(), array(
                        'label'      => Mage::helper('amflags')->__('For') . ' ' . $column->getAlias() . ' ' . Mage::helper('amflags')->__('Column'),
                        'url'    => Mage::helper('adminhtml')->getUrl('adminhtml/amflagsassign/massApply', array('column' => $column->getEntityId())),
                        'additional' => array(
                            'visibility'  => array(
                                'name'   => 'flags_' . $column->getEntityId(),
                                'type'   => 'select',
                                'class'  => 'required-entry',
                                'label'  => Mage::helper('amflags')->__('Apply Flag'),
                                'values' => $values
                            )
                        )
                    ));
                }
            }

            if (!empty($columns)
                && Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/assign_flags'))
            {
                array_unshift($columns, array('value' => 0, 'label' => 'All'));
                $block->addItem('amflags_remove', array(
                    'label'      => Mage::helper('amflags')->__('Remove Flags'),
                    'url'        => Mage::helper('adminhtml')->getUrl('adminhtml/amflagsassign/massApply', array('column' => 0)),
                    'additional' => array(
                        'visibility'  => array(
                            'name'   => 'flags_0',
                            'type'   => 'select',
                            'class'  => 'required-entry',
                            'label'  => Mage::helper('amflags')->__('From Column'),
                            'values' => $columns
                        )
                    )
                ));
            }

            $block->addItem('amflags_separator_post', array(
                'label'=> '----------------------------------',
                'url'  => '',
            ));
        }
        else
            return $this;
    }

    protected function _isSalesGrid($page)
    {
        return in_array($page, array('adminhtml_sales_order', 'sales_order', 'orderspro_order'));
    }

    protected function _isJoined($from, $key)
    {
        $found = false;
        foreach ($from as $alias => $data) {
            if ($key === $alias) {
                $found = true;
            }
        }
        return $found;
    }

    protected function _isControllerName($place)
    {
        $found = false;
        foreach ($this->_controllerNames as $controllerName) {
            if (false !== strpos(Mage::app()->getRequest()->getControllerName(), $controllerName . $place)) {
                $found = true;
            }
        }
        return $found;
    }

    protected function _prepareCollection($collection, $place = 'order')
    {
        $actions = array_merge($this->_permissibleActions, $this->_exportActions);
        if (!$this->_isControllerName($place) ||
            !in_array(Mage::app()->getRequest()->getActionName(), $actions) )
            return $collection;

        $columnCollection = Mage::getModel('amflags/column')->getCollection();
        if ($columnCollection->getSize() > 0) {
            $isVersion14 = ! Mage::helper('ambase')->isVersionLessThan(1,4);
            $alias = $isVersion14 ? 'main_table' : 'e';
            foreach ($columnCollection as $column) {
                if ($column->getApplyFlag()
                    && !$this->_isJoined($collection->getSelect()->getPart('from'), 'f2o'.$column->getEntityId())) {
                    $collection->getSelect()
                        ->joinLeft(
                            array('f2o'.$column->getEntityId() => Mage::getModel('amflags/order_flag')->getResource()->getMainTable()),
                            'f2o'.$column->getEntityId().'.order_id = ' . $alias . '.entity_id ' .
                            'AND f2o'.$column->getEntityId().'.column_id = '.$column->getEntityId(),
                            array())
                        ->joinLeft(
                            array('f'.$column->getEntityId() => Mage::getModel('amflags/flag')->getResource()->getMainTable()),
                            'f'.$column->getEntityId().'.entity_id = f2o'.$column->getEntityId().'.flag_id',
                            array('priority'.$column->getEntityId() => 'f'.$column->getEntityId().'.priority')
                        );
                }
            }
        }

        return $collection;
    }

    public function onCoreCollectionAbstractLoadBefore($observer)
    {
        $collection = $observer->getCollection();

        if (version_compare(Mage::getVersion(), '1.5', '<')) {
            $orderCollectionClass = Mage::getConfig()->getResourceModelClassName('sales/order_collection');
        } else {
            $orderCollectionClass = Mage::getConfig()->getResourceModelClassName('sales/order_grid_collection');
        }

        if ($collection instanceof Mage_Sales_Model_Resource_Order_Grid_Collection
            || $orderCollectionClass == get_class($collection)) {
            if (self::$collectionModified && !Mage::helper('core')->isModuleEnabled('BL_CustomGrid'))
                return;
            else
                self::$collectionModified = true;

            $this->_prepareCollection($collection);
        }
    }

    protected function _prepareColumns(&$grid, $columnCollection, $export = false, $place = 'order', $after = 'entity_id')
    {
        $actions = array_merge($this->_permissibleActions, $this->_exportActions);
        if (!$this->_isControllerName($place) ||
            !in_array(Mage::app()->getRequest()->getActionName(), $actions) )
            return $grid;

        $flagCollection = Mage::getModel('amflags/flag')->getCollection();

        foreach ($columnCollection as $column) {
            if (($column->getApplyFlag()) && ($flagCollection->getSize() > 0)) {
                $flagFilterOptions = array();
                $columnFlags = explode(',', $column->getApplyFlag());

                foreach ($flagCollection as $flag) {
                    if (in_array($flag->getEntityId(), $columnFlags)) {
                        $flagFilterOptions[$flag->getEntityId()] = $flag->getAlias();
                    }
                }

                $column = array(
                    'header'       => Mage::helper('amflags')->__($column->getAlias()),
                    'index'        => 'priority'.$column->getEntityId(),
                    'filter_index' => 'f'.$column->getEntityId().'.entity_id',
                    'width'        => '80px',
                    'align'        => 'center',
                    'sortable'     => true,
                    'renderer'     => 'amflags/adminhtml_renderer_flag',
                    'type'         => 'options',
                    'options'      => $flagFilterOptions,
                );

                if ($export) {
                    $column['is_system'] = true;
                }

                $grid->addColumnAfter($column['index'], $column, $after);
                $after = $column['index'];
            }
        }

        return $grid;
    }

    public function onCoreLayoutBlockCreateAfter($observer)
    {
        $block = $observer->getBlock();
        $blockClass = Mage::getConfig()->getBlockClassName('adminhtml/sales_order_grid');
        if ($blockClass == get_class($block)) {
            $columnCollection = Mage::getModel('amflags/column')->getCollection();
            $columnCollection->getSelect()->order('pos ASC');
            if ($columnCollection->getSize() > 0) {
                $this->_prepareColumns($block, $columnCollection, in_array(Mage::app()->getRequest()->getActionName(), $this->_exportActions));
            }
        }
    }

    public function onCoreBlockAbstractToHtmlAfter($observer)
    {
        $block = $observer->getBlock();
        $blockClass = Mage::getConfig()->getBlockClassName('adminhtml/sales_order_grid');
        if ($blockClass == get_class($block)) {
            $transport = $observer->getTransport();
            $html = $transport->getHtml();
            $insert = Mage::app()->getLayout()->createBlock('amflags/adminhtml_sales_order_grid_modifyjs')->toHtml();
            $html .= $insert;
            $transport->setHtml($html);
        }
    }
}

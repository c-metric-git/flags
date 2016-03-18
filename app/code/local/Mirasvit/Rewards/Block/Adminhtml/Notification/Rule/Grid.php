<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Reward Points + Referral program
 * @version   1.1.2
 * @build     928
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Rewards_Block_Adminhtml_Notification_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
        $this->setDefaultSort('notification_rule_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('rewards/notification_rule')
            ->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('notification_rule_id', array(
            'header' => Mage::helper('rewards')->__('ID'),
            'index' => 'notification_rule_id',
            'filter_index' => 'main_table.notification_rule_id',
            )
        );
        $this->addColumn('name', array(
            'header' => Mage::helper('rewards')->__('Rule Name'),
            'index' => 'name',
            'filter_index' => 'main_table.name',
            )
        );
        $this->addColumn('active_from', array(
            'header' => Mage::helper('rewards')->__('Active From'),
            'index' => 'active_from',
            'filter_index' => 'main_table.active_from',
            'type' => 'date',
            )
        );
        $this->addColumn('active_to', array(
            'header' => Mage::helper('rewards')->__('Active To'),
            'index' => 'active_to',
            'filter_index' => 'main_table.active_to',
            'type' => 'date',
            )
        );
        $this->addColumn('is_active', array(
            'header' => Mage::helper('rewards')->__('Is Active'),
            'index' => 'is_active',
            'filter_index' => 'main_table.is_active',
            'type' => 'options',
            'options' => array(
                0 => $this->__('No'),
                1 => $this->__('Yes'),
            ),
            )
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('notification_rule_id');
        $this->getMassactionBlock()->setFormFieldName('notification_rule_id');
        $statuses = array(
                array('label' => '', 'value' => ''),
                array('label' => $this->__('Disabled'), 'value' => 0),
                array('label' => $this->__('Enabled'), 'value' => 1),
        );
        $this->getMassactionBlock()->addItem('is_active', array(
             'label' => Mage::helper('rewards')->__('Change status'),
             'url' => $this->getUrl('*/*/massChange', array('_current' => true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'is_active',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('rewards')->__('Status'),
                         'values' => $statuses,
                     ),
             ),
        ));
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('rewards')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('rewards')->__('Are you sure?'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /************************/
}

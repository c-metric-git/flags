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



class Mirasvit_Rewards_Block_Adminhtml_Referral_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
        $this->setDefaultSort('referral_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('rewards/referral')
            ->getCollection();
        $collection->addNameToSelect();
        $select = $collection->getSelect();
//        echo $select;die;
//        $select->joinLeft('customer/entity', 'order_address/firstname', 'billing_address_id', null, 'left');
        $this->setCollection($collection);
//        echo $collection->getSelect();die;
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('referral_id', array(
            'header' => Mage::helper('rewards')->__('ID'),
            'index' => 'referral_id',
            'filter_index' => 'main_table.referral_id',
            )
        );
        $this->addColumn('customer', array(
            'header' => Mage::helper('rewards')->__('Affiliate Customer'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index' => 'customer',
            'frame_callback' => array($this, '_customerFormat'),
            'filter_index' => 'CONCAT(`ce1`.`value`, \' \',`ce2`.`value`)',
            )
        );
        $this->addColumn('new_customer', array(
            'header' => Mage::helper('rewards')->__('Referral Customer'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index' => 'new_customer',
            'frame_callback' => array($this, '_newCustomerFormat'),
            'filter_index' => 'CONCAT(`ce3`.`value`, \' \',`ce4`.`value`)',
            )
        );
        $this->addColumn('email', array(
            'header' => Mage::helper('rewards')->__('Referral Email'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index' => 'email',
            'filter_index' => 'main_table.email',
            'frame_callback' => array($this, '_renderOfEmptyFields'),
            )
        );
        $this->addColumn('name', array(
            'header' => Mage::helper('rewards')->__('Referral Name'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index' => 'name',
            'filter_index' => 'main_table.name',
            'frame_callback' => array($this, '_renderOfEmptyFields'),
            )
        );
        $this->addColumn('status', array(
            'header' => Mage::helper('rewards')->__('Status'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index' => 'status',
            'filter_index' => 'main_table.status',
            'type' => 'options',
            'options' => Mage::getSingleton('rewards/config_source_referral_status')->toArray(),
            )
        );
        $this->addColumn('created_at', array(
            'header' => Mage::helper('rewards')->__('Created At'),
            'index' => 'created_at',
            'filter_index' => 'main_table.created_at',
            'type' => 'date',
            )
        );
        $this->addColumn('store_id', array(
            'header' => Mage::helper('rewards')->__('Store'),
            'index' => 'store_id',
            'filter_index' => 'main_table.store_id',
            'type' => 'options',
            'options' => Mage::helper('rewards')->getCoreStoreOptionArray(),
            )
        );

        return parent::_prepareColumns();
    }

    public function _customerFormat($renderedValue, $row, $column, $isExport)
    {
        $url = Mage::helper('rewards/mage')->getBackendCustomerUrl($row['customer_id']);

        return "<a href='{$url}'>{$row['customer_name']}</a>";
    }

    public function _newCustomerFormat($renderedValue, $row, $column, $isExport)
    {
        if ((int) $row['new_customer_id'] == 0) {
            return '';
        }
        $url = Mage::helper('rewards/mage')->getBackendCustomerUrl($row['new_customer_id']);

        return "<a href='{$url}'>{$row['new_customer_name']}</a>";
    }

    public function _renderOfEmptyFields($renderedValue, $row, $column, $isExport)
    {
        if ($renderedValue == '') {
            return '-';
        }

        return $renderedValue;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('referral_id');
        $this->getMassactionBlock()->setFormFieldName('referral_id');
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('rewards')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('rewards')->__('Are you sure?'),
        ));

        return $this;
    }

    /************************/
}

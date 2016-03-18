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



class Mirasvit_Rewards_Block_Adminhtml_Transaction_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_customFilters = array();
    protected $_removeFilters = array();
    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
        $this->setDefaultSort('transaction_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    public function addCustomFilter($field, $filter = false)
    {
        if ($filter) {
            $this->_customFilters[$field] = $filter;
        } else {
            $this->_customFilters[] = $field;
        }

        return $this;
    }

    public function removeFilter($field)
    {
        $this->_removeFilters[$field] = true;

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('rewards/transaction')
            ->getCollection()
            ->joinCustomerName();
        foreach ($this->_customFilters as $key => $value) {
            if ((int) $key === $key && is_string($value)) {
                $collection->getSelect()->where($value);
            } else {
                $collection->addFieldToFilter($key, $value);
            }
        }
        $this->setCollection($collection);
//         echo $collection->getSelect();die;
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', array(
            'header' => Mage::helper('rewards')->__('ID'),
            'index' => 'transaction_id',
            'filter_index' => 'main_table.transaction_id',
            )
        );
        $this->addColumn('customer_name', array(
            'header' => Mage::helper('rewards')->__('Customer Name'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index' => 'customer_name',
            'filter_index' => 'CONCAT(cft.value, \' \', clt.value)',
            )
        );
        $this->addColumn('customer_email', array(
            'header' => Mage::helper('rewards')->__('Customer Email'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index' => 'customer_email',
            'filter_index' => 'customer.email',
            )
        );
        $this->addColumn('amount', array(
            'header' => Mage::helper('rewards')->__('Balance Change'),
            'index' => 'amount',
            'filter_index' => 'main_table.amount',
            )
        );
        $this->addColumn('comment', array(
            'header' => Mage::helper('rewards')->__('Comment'),
            'index' => 'comment',
            'filter_index' => 'main_table.comment',
            )
        );
        $this->addColumn('created_at', array(
            'header' => Mage::helper('rewards')->__('Created At'),
            'index' => 'created_at',
            'filter_index' => 'main_table.created_at',
            'type' => 'date',
            )
        );
        $this->addColumn('expires_at', array(
            'header' => Mage::helper('rewards')->__('Expires At'),
            'index' => 'expires_at',
            'filter_index' => 'main_table.expires_at',
            'type' => 'date',
            )
        );
        $this->addColumn('action',
            array(
                'header' => Mage::helper('customer')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('customer')->__('Delete'),
                        'url' => array('base' => '*/*/delete'),
                        'field' => 'id',
                    ),
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('transaction_id');
        $this->getMassactionBlock()->setFormFieldName('transaction_id');
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('rewards')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('rewards')->__('Are you sure?'),
        ));

        return $this;
    }

    // public function getRowUrl($row)
    // {
    //     return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    // }

    /************************/
}

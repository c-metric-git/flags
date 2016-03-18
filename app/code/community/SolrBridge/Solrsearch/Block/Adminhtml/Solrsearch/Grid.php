<?php

class SolrBridge_Solrsearch_Block_Adminhtml_Solrsearch_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('solrsearchGrid');
      $this->setDefaultSort('solrsearch_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }
  
  protected function _prepareColumns()
  {
      $this->addColumn('web_id', array(
          'header'    => Mage::helper('solrsearch')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'web_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('solrsearch')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('solrsearch')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
   
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('solrsearch')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('solrsearch')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
   
      return parent::_prepareColumns();
  }
}
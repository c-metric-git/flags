<?php
/**
 * Custom Order Number Pro
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Ordernum
 * @version      5.1.5
 * @license:     d0NyTkcYcW64yuyl9Cf2M6q3gBilLUVMAwQSumkwPP
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 */
class AdjustWare_Ordernum_Block_Adminhtml_Ordernum_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('ordernumGrid');
      $this->setDefaultSort('config_id');
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('core/store')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
    $hlp =  Mage::helper('adjordernum'); 
    
    $this->addColumn('config_id', array(
      'header'    => $hlp->__('ID'),
      'align'     => 'right',
      'width'     => '50px',
      'index'     => 'store_id',
    ));

    $this->addColumn('store_id', array(
        'header'    => $hlp->__('Store'),
        'index'     => 'store_id',
        'type'      => 'store',
        'store_view' => true,
    )); 

    $this->addColumn('order', array(
        'header'    => $hlp->__('Order Data'),
        'index'     => 'order_data',
        'renderer'  => 'adjordernum/adminhtml_renderer_data',
        'sortable'  => false,
        'filter'  => false,
    ));

    $this->addColumn('invoice', array(
        'header'    => $hlp->__('Invoice Data'),
        'index'     => 'invoice_data',
        'renderer'  => 'adjordernum/adminhtml_renderer_data',
        'sortable'  => false,
        'filter'  => false,
    ));
    
    $this->addColumn('shipment', array(
        'header'    => $hlp->__('Shipment Data'),
        'index'     => 'shipment_data',
        'renderer'  => 'adjordernum/adminhtml_renderer_data',
        'sortable'  => false,
        'filter'  => false,
    ));
    $this->addColumn('creditmemo', array(
        'header'    => $hlp->__('Credit Memo Data'),
        'index'     => 'creditmemo_data',
        'renderer'  => 'adjordernum/adminhtml_renderer_data',
        'sortable'  => false,
        'filter'    => false,
    ));

    return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
      $hlpr = Mage::helper('adjordernum/path');
      $store = Mage::app()->getStore($row->getId());
      $section = $hlpr->getSection();
      $websiteCode = $store->getWebsite()->getCode();
      $storeCode = $store->getCode();
      $params = array(
          'section' => $section,
          'website' => $websiteCode,
          'store' => $storeCode,
          );
      return $this->getUrl('adminhtml/system_config/edit', $params);
  }
 
}
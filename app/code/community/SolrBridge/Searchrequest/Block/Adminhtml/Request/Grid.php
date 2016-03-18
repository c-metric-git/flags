<?php
/**
 * @category SolrBridge
 * @package WebMods_Solrsearch
 * @author    Hau Danh
 * @copyright    Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class SolrBridge_Searchrequest_Block_Adminhtml_Request_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	protected function _construct() {
		parent::_construct ();
		$this->setId ( 'searchRequestGrid' );
	}
	protected function _prepareCollection() {
		$collection = Mage::getModel ( 'searchrequest/request' )->getCollection ();
		$this->setCollection ( $collection );
		$collection->setOrder ( 'id', 'DESC' );
		$this->setCollection ( $collection );
		return parent::_prepareCollection ();
	}
	protected function _prepareColumns() {
		$this->addColumn ( 'Id', array (
				'header' => Mage::helper ( 'searchrequest' )->__ ( 'Request ID' ),
				'align' => 'left',
				'width' => '50px',
				'index' => 'id' 
		) );
		$this->addColumn ( 'mobile_number', array (
				'header' => Mage::helper ( 'searchrequest' )->__ ( 'Mobile Number' ),
				'align' => 'left',
				'width' => '50px',
				'index' => 'mobile_number' 
		) );
		
		$this->addColumn ( 'email_id', array (
				'header' => Mage::helper ( 'searchrequest' )->__ ( 'Email' ),
				'align' => 'left',
				'width' => '50px',
				'index' => 'email_id' 
		) );
		
		$this->addColumn ( 'category', array (
				'header' => Mage::helper ( 'searchrequest' )->__ ( 'Category' ),
				'align' => 'left',
				'width' => '50px',
				'index' => 'category' 
		) );
		$this->addColumn ( 'message', array (
				'header' => Mage::helper ( 'searchrequest' )->__ ( 'Message' ),
				'align' => 'left',
				'width' => '50px',
				'index' => 'message' 
		) );
		$this->addColumn ( 'edit', array (
				'header' => Mage::helper ( 'searchrequest' )->__ ( 'Edit' ),
				'width' => '50px',
				'type' => 'action',
				'getter' => 'getId',
				'actions' => array (
						array (
								'caption' => $this->__ ( 'Edit' ),
								'class' => 'search-request-edit',
								'url' => array (
										'base' => '*/*/edit' 
								),
								'field' => 'id' 
						) 
				),
				'filter' => false,
				'sortable' => false,
				'index' => 'id' 
		) );
		$this->addColumn ( 'delete', array (
				'header' => Mage::helper ( 'searchrequest' )->__ ( 'Delete' ),
				'width' => '50px',
				'type' => 'action',
				'getter' => 'getId',
				'actions' => array (
						array (
								'caption' => $this->__ ( 'Delete' ),
								'class' => 'solrbridge-request-delete',
								'url' => '#solrbridge-request-delete',
								'field' => 'id' 
						) 
				),
				'filter' => false,
				'sortable' => false,
				'index' => 'id' 
		) );
		return parent::_prepareColumns ();
	}
	public function getRowUrl($row) {
		return $row->getId ();
	}
	public function getAdditionalJavaScript() {
		$editUrl = Mage::helper ( "adminhtml" )->getUrl ( "searchrequest/adminhtml_request/edit" );
		$deleteUrl = Mage::helper ( "adminhtml" )->getUrl ( "searchrequest/adminhtml_request/ajaxdelete" );
		$messageDelete = Mage::helper ( 'searchrequest' )->__ ( 'Are you sure to delete this Request?' );
		$okLabel = Mage::helper ( 'searchrequest' )->__ ( 'Yes' );
		$canelLabel = Mage::helper ( 'searchrequest' )->__ ( 'No' );
		return 'document.observe("dom:loaded", function() {
        	new SolrSearchRequest(\'' . $editUrl . '\' , \'' . $deleteUrl . '\' , \'' . $messageDelete . '\' , \'' . $okLabel . '\' , \'' . $canelLabel . '\' );
        });';
	}
}

<?php 
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_PRODUCTQA
 * @copyright  Copyright (c) 2012 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */
class Itoris_ProductQa_Block_Admin_Questions_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	public function __construct() {
		parent::__construct();
	}

	protected function _prepareCollection() {
		/** @var $collection Itoris_ProductQa_Model_Mysql4_Questions_Collection */
		$collection = Mage::registry('questions');

		$this->setCollection($collection);
		$this->setDefaultSort('datetime');
       	$this->setDefaultDir('desc');
		return parent::_prepareCollection();
	}

	protected function _prepareColumns() {
		$page = Mage::registry('questionsPage');

		$this->addColumn('id',
			array(
				'header' => $this->__('ID'),
				'width'  => '30px',
				'index'  => 'main_table.id',
				'getter' => 'getId'
			)
		);

		if (empty($page) || $page == Itoris_ProductQa_Block_Admin_Questions::PAGE_NOT_ANSWERED) {
			$this->addColumn('inappr',
				array(
					'header'   => $this->__('Inappr'),
					'width'    => '26px',
					'index'    => 'main_table.inappr',
					'type'     => 'options',
					'options'  => array(
						0 => $this->__('No'),
						1 => $this->__('Yes')
					),
					'renderer' => 'itoris_productqa/admin_renderer_inappr',
				)
			);
		}

		$this->addColumn('datetime',
			array(
				'header'   => $this->__('Created On'),
				'width'    => '100px',
				'type'     => 'datetime',
				'index'    => 'main_table.created_datetime',
				'type'     => 'datetime',
				'getter'   => 'getCreatedDatetime',
				'renderer' => 'itoris_productqa/admin_renderer_datetime',
			)
		);

		if ($page != Itoris_ProductQa_Block_Admin_Questions::PAGE_PENDING) {
			$this->addColumn('status',
				array(
					'header'  => $this->__('Status'),
					'width'   => '70px',
					'index'   => 'main_table.status',
					'type'    => 'options',
					'options' => array(
						Itoris_ProductQa_Model_Questions::STATUS_PENDING      => $this->__('Pending'),
						Itoris_ProductQa_Model_Questions::STATUS_APPROVED     => $this->__('Approved'),
						Itoris_ProductQa_Model_Questions::STATUS_NOT_APPROVED => $this->__('Not Approved'),
					),
					'renderer' => 'itoris_productqa/admin_renderer_status',
				)
			);
		}

		$this->addColumn('nickname',
			array(
				'header' => $this->__('Nickname'),
				'width'  => '100px',
				'index'  => 'main_table.nickname',
				'getter' => 'getNickname',
			)
		);

		$this->addColumn('question',
			array(
				'header'   => $this->__('Question'),
				'width'    => '150px',
				'renderer' => 'itoris_productqa/admin_renderer_question',
				'index'    => 'main_table.content',
			)
		);

		$this->addColumn('visible',
			array(
				'header'   => $this->__('Visible In'),
				'width'    => '130px',
				'type'     => 'store',
				'index'    => 'v.store_id',
				'renderer' => 'itoris_productqa/admin_renderer_visible'
			)
		);

		$this->addColumn('type',
			array(
				'header'   => $this->__('Type'),
				'width'    => '70px',
				'type'     => 'options',
				'options'  => array(
					Itoris_ProductQa_Model_Questions::SUBMITTER_ADMIN    => $this->__('Administrator'),
					Itoris_ProductQa_Model_Questions::SUBMITTER_CUSTOMER => $this->__('Customer'),
					Itoris_ProductQa_Model_Questions::SUBMITTER_VISITOR  => $this->__('Guest'),
				),
				'index'    => 'main_table.submitter_type',
				'renderer' => 'itoris_productqa/admin_renderer_submitter'
			)
		);

		$this->addColumn('productName',
			array(
				'header' => $this->__('Product Name'),
				'width'  => '100px',
				'index'  => 'value',
			)
		);

		$this->addColumn('productSku',
			array(
				'header' => $this->__('Product SKU'),
				'width'  => '100px',
				'index'  => 'sku'
			)
		);

		$this->addColumn('action',
			array(
				'header'  => $this->__('Action'),
				'width'   => '50px',
				'type'    => 'action',
				'getter'  => 'getId',
				'actions' => array(
					array(
						'caption' => $this->__('Edit'),
						'url'     => array(
							'base'=>'*/*/edit',
						),
						'field' => 'id'
					)
				),
				'filter'   => false,
				'sortable' => false,
			)
		);


		return parent::_prepareColumns();
	}

	protected function _prepareMassaction() {
		$this->setMassactionIdField('main_table.id');

		$this->getMassactionBlock()->setFormFieldName('question');

		$this->getMassactionBlock()->addItem('delete', array(
			 'label'   => $this->__('Delete'),
			 'url'     => $this->getUrl('*/*/massDelete'),
			 'confirm' => $this->__('Do you really want to remove the question? All answers will be removed as well')
		));

		$this->getMassactionBlock()->addItem('status', array(
			 'label'      => $this->__('Change status'),
			 'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			 'additional' => array(
				'visibility' => array(
					 'name'   => 'status',
					 'type'   => 'select',
					 'class'  => 'required-entry',
					 'label'  => $this->__('Status'),
					 'values' => array(
						Itoris_ProductQa_Model_Questions::STATUS_PENDING      => $this->__('Pending'),
					 	Itoris_ProductQa_Model_Questions::STATUS_APPROVED     => $this->__('Approved'),
					 	Itoris_ProductQa_Model_Questions::STATUS_NOT_APPROVED => $this->__('Not Approved')
					 )
				)
			 )
		));

		return $this;
	}

	public function getRowUrl($question) {
		return $this->getUrl('itoris_productqa/admin_questions/edit', array('id' => $question->getId()));
	}
}
?>

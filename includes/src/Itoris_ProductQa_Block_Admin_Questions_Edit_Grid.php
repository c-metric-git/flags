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
class Itoris_ProductQa_Block_Admin_Questions_Edit_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	public function __construct() {
		$this->setTemplate('itoris/productqa/grid.phtml');
        $this->setRowClickCallback('openGridRow');
        $this->_emptyText = $this->__('No records found.');
		$this->_defaultLimit = 1000;
	}

	protected function _prepareCollection() {
			$collection = Mage::registry('answerCollection');
			$this->setCollection($collection);
			return parent::_prepareCollection();
	}

	protected function _prepareColumns() {
		$this->addColumn('del',
			array(
				'header'   => Mage::helper('catalog')->__('Del'),
				'width'    => '10px',
				'type'     => 'checkbox',
				'sortable' => false,
				'filter'   => false,
				'renderer' => 'itoris_productqa/admin_renderer_edit_delete',
			)
		);

		$this->addColumn('datetime',
			array(
				'header'   => $this->__('Posted On'),
				'width'    => '100px',
				'index'    => 'main_table.created_datetime',
				'type'     => 'datetime',
				'getter'   => 'getCreatedDatetime',
				'renderer' => 'itoris_productqa/admin_renderer_datetime',
				'sortable' => false,
				'filter'   => false,
			)
		);

		$this->addColumn('posted_by',
			array(
				'header'   => $this->__('Posted By'),
				'width'    => '50px',
				'type'     => 'text',
				'sortable' => false,
				'filter'   => false,
				'renderer' => 'itoris_productqa/admin_renderer_edit_postedBy',
			)
		);

		$this->addColumn('inappr',
			array(
				'header'   => $this->__('Inappr'),
				'width'    => '30px',
				'type'     => 'options',
				'sortable' => false,
				'filter'   => false,
				'renderer' => 'itoris_productqa/admin_renderer_edit_inappr',
			)
		);

		$this->addColumn('status',
			array(
				'header'   => $this->__('Status'),
				'width'    => '70px',
				'type'     => 'options',
				'sortable' => false,
				'filter'   => false,
				'renderer' => 'itoris_productqa/admin_renderer_edit_status',
			)
		);

		$this->addColumn('nickname',
			array(
				'header'   => $this->__('Nickname'),
				'width'    => '100px',
				'getter'   => 'getNickname',
				'sortable' => false,
				'filter'   => false,
				'renderer' => 'itoris_productqa/admin_renderer_edit_input',
			)
		);

		$this->addColumn('answer',
			array(
				'header'   => $this->__('Answer') . ' (' . $this->__('HTML Tags allowed') . ')',
				'width'    => '450px',
				'getter'   => 'getContent',
				'sortable' => false,
				'filter'   => false,
				'renderer' => 'itoris_productqa/admin_renderer_edit_textarea',
			)
		);

		return parent::_prepareColumns();
	}

	public function getRowUrl($item) {
		return '';
	}
}
?>
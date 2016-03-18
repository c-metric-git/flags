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

class Itoris_ProductQa_Block_Admin_Questions extends Mage_Adminhtml_Block_Widget_Grid_Container {

	const PAGE_PENDING = 1;
	const PAGE_INAPPR = 2;
	const PAGE_NOT_ANSWERED = 3;

	public function __construct() {
		$this->_controller = 'admin_questions';
		$this->_blockGroup = 'itoris_productqa';
        parent::__construct();
	}

	protected function _prepareLayout() {
		$this->_prepareSettings(Mage::registry('questionsPage'));
        return parent::_prepareLayout();
    }

	protected function _prepareSettings($page) {
		switch ($page) {
			case Itoris_ProductQa_Block_Admin_Questions::PAGE_PENDING:
				$this->_headerText = $this->__('Pending Questions');
				break;
			case Itoris_ProductQa_Block_Admin_Questions::PAGE_INAPPR:
				$this->_headerText = $this->__('Inappropriate Questions');
				break;
			case Itoris_ProductQa_Block_Admin_Questions::PAGE_NOT_ANSWERED:
				$this->_headerText = $this->__('Not Answered Questions');
				break;
			default:
				$this->_headerText = $this->__('All Questions');
		}
		if (empty($page)) {
			$this->_updateButton('add','label', $this->__('Add New Question'));
		} else {
			$this->_removeButton('add');
		}
	}
}
?>
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

class Itoris_ProductQa_Block_Admin_Answers extends Mage_Adminhtml_Block_Widget_Grid_Container {

	const PAGE_PENDING = 1;
	const PAGE_INAPPR = 2;

	public function __construct() {
		$this->_controller = 'admin_answers';
		$this->_blockGroup = 'itoris_productqa';
        parent::__construct();
	}

	protected function _prepareLayout() {
		$this->_prepareSettings(Mage::registry('answersPage'));
        return parent::_prepareLayout();
    }

	protected function _prepareSettings($page) {
		switch ($page) {
			case Itoris_ProductQa_Block_Admin_Answers::PAGE_PENDING:
				$this->_headerText = $this->__('Pending Answers');
				break;
			case Itoris_ProductQa_Block_Admin_Questions::PAGE_INAPPR:
				$this->_headerText = $this->__('Inappropriate Answers');
				break;
			default:
				$this->_headerText = $this->__('All Answers');
		}
		$this->_removeButton('add');
	}
}
?>
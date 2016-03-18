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

class Itoris_ProductQa_Block_Admin_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

	public function __construct() {
        $this->_blockGroup = 'itoris_productqa';
        $this->_controller = 'admin_questions';
		$this->_headerText = $this->__('Edit Question');
		parent::__construct();
        $this->_updateButton('save', 'label', $this->__('Save Question'));
        $this->_updateButton('delete', 'label', $this->__('Delete Question'));
        $this->_updateButton('delete', 'onclick' ,'deleteConfirm(\'' . $this->__('Do you really want to remove the question? All answers will be removed as well') . '\', \'' . $this->getDeleteUrl() . '\')');
		$this->_addButton('saveandcontinue', array(
			'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
			'onclick'   => 'editForm.submit(\''.$this->_getSaveAndContinueUrl().'\')',
			'class'     => 'save',
		), -100);
    }

	public function getDeleteUrl() {
        return $this->getUrl('*/*/massDelete', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }

	protected function _getSaveAndContinueUrl() {
		return $this->getUrl('*/*/save', array(
			'_current'  => true,
			'back'      => 'edit',
		));
	}
}
?>
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

class Itoris_ProductQa_Block_Admin_Renderer_Edit_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text {

	public function render(Varien_Object $row) {
		$options = array(
			Itoris_ProductQa_Model_Answers::STATUS_PENDING      => $this->__('Pending'),
			Itoris_ProductQa_Model_Answers::STATUS_APPROVED     => $this->__('Approved'),
			Itoris_ProductQa_Model_Answers::STATUS_NOT_APPROVED => $this->__('Not Approved'),
		);
		$html = '';
		$html .= '<input type="hidden" name="answer['. $row->getId() .'][status_before]" value="'. $row->getStatus() .'" /><select style="margin-top:5px;" name="answer['. $row->getId() .'][status]">';
		foreach ($options as $key => $value) {
			$html .= '<option value="'. $key .'" '. (($row->getStatus() == $key) ? 'selected="selected"' : '') .'>'
						. $value . '</option>';
		}
		$html .= '</select>';
		return $html;
	}
}
?>

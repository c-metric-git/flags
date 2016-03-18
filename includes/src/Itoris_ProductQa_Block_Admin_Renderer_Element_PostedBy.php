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

class Itoris_ProductQa_Block_Admin_Renderer_Element_PostedBy extends Mage_Adminhtml_Block_Widget_Form_Renderer_Element {

	public function render(Varien_Data_Form_Element_Abstract $element) {
		$value = $element->getValue();
		$date = $this->helper('core')->formatDate($value['posted_on_date'], 'medium', true);
		$html = '<tr><td class="label"><label for="'. $element->getId() .'">'. $element->getLabelHtml() .'</label></td><td class="value">';
		if ($value['user_name']) {
    		$html .= '<a href="'. $value['user_url'] .'">' . $value['user_name'] . ' </a>
    				<a href="mailto:' . $value['user_email'] .'"> ('. $value['user_email'] .') </a>';
		}
		$html .= $value['user_type'] . ' <span style="margin-left: 80px; margin-right: 20px;">'. $value['posted_on_label'] .'</span> ' . $date . '</td></tr>';
		return $html;
	}
}
?>
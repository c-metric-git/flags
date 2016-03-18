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

class Itoris_ProductQa_Block_ProductQaWidget extends Itoris_ProductQa_Block_ProductQa implements Mage_Widget_Block_Interface {

	protected function _toHtml() {
		/** @var $jsUrl Mage_Core_Helper_Js */
		$jsUrl = Mage::helper('core/js');

		$script = $jsUrl->getJsUrl('itoris/productqa/productqa.js');
		$html = '<script type="text/javascript" src="'. $script .'"></script>';
		
		$html .= '<link rel="stylesheet" type="text/css" href="'. $this->styleTheme .'" />';
		if (!is_null($this->styleThemeColor)) {
			$html .= '<link rel="stylesheet" type="text/css" href="'. $this->styleThemeColor .'" />';
		}

		$html .= parent::_toHtml();
		
		return $html;
	}
}
?>
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

class Itoris_ProductQa_Helper_Captcha extends Mage_Core_Helper_Abstract {

	/**
	 * Check the captcha code by a captcha type
	 *
	 * @param $code
	 * @param $captcha
	 * @return bool
	 */
	public function captchaValidate($code, $captcha) {
		switch ($captcha) {
			case Itoris_ProductQa_Model_Settings::SHOW_SECURIMAGE:
				require_once Mage::getBaseDir() . "/app/code/local/Itoris/ProductQa/Helper/Captcha/securimage/securimage.php";
				$img = new Securimage();
				return $img->check($code);
			case Itoris_ProductQa_Model_Settings::SHOW_ALIKON:
				require_once Mage::getBaseDir() . "/app/code/local/Itoris/ProductQa/Helper/Captcha/alikon/captcha.php";
				return (strtolower($_SESSION['captcha_code']) == strtolower($code)) ? true : false;
			case Itoris_ProductQa_Model_Settings::SHOW_CAPTCHA:
				require_once Mage::getBaseDir() . "/app/code/local/Itoris/ProductQa/Helper/Captcha/captchaform/captchaform5.php";
				return (strtolower($_SESSION['captcha_code']) == strtolower($code)) ? true : false;
		}
	}
}
?>
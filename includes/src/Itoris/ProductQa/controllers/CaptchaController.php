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

ini_set("pcre.recursion_limit", "524");
 
class Itoris_ProductQa_CaptchaController extends Mage_Core_Controller_Front_Action {

	/**
	 * Get securimage captcha image
	 */
	public function securimageAction() {
		require_once Mage::getBaseDir() . "/app/code/local/Itoris/ProductQa/Helper/Captcha/securimage/securimage.php";
		$img = new Securimage();
		$img->show();
	}

	/**
	 * Get alikon mod captcha image
	 */
	public function alikonAction(){
		require_once Mage::getBaseDir() . "/app/code/local/Itoris/ProductQa/Helper/Captcha/alikon/captcha.php";
		$img = new alikoncaptcha();
		$img->image($img->captchacode());
	}

	/**
	 * Get captcha form image
	 */
	public function captchaFormAction(){
		require_once Mage::getBaseDir() . "/app/code/local/Itoris/ProductQa/Helper/Captcha/captchaform/captchaform5.php";
		$img = new captchaform();
		$img->image($img->captchacode());
	}
}
?>
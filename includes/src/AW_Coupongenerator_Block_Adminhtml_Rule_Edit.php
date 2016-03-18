<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Coupongenerator
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Coupongenerator_Block_Adminhtml_Rule_Edit extends Mage_Adminhtml_Block_Promo_Quote_Edit
{
    public function __construct()
    {
        parent::__construct();
        $this->updateButton('save', 'id', 'aw_coupon_save');
        $this->updateButton(
            'save',
            'onclick',
            'if ( ! editForm.submit()) ' . $this->_getEnableSaveButtonsJs()
        );
        $versionAdapter = Mage::getSingleton('coupongenerator/version_adapter');
        $saveAndContinueButtonId = $versionAdapter->call('getRuleSaveAndContinueButtonId');
        $this->updateButton($saveAndContinueButtonId, 'id', 'aw_coupon_save_and_continue_edit');
        $this->updateButton(
            $saveAndContinueButtonId,
            'onclick',
            'if ( ! editForm.submit($(\'edit_form\').action + \'back/edit/\')) ' . $this->_getEnableSaveButtonsJs()
        );
        $this->_formScripts[] = "varienGlobalEvents.attachEventHandler('formSubmit', function(){
            " . $this->_getDisableSaveButtonsJs() . "
        })";
    }

    /**
     * @return string
     */
    protected function _getEnableSaveButtonsJs()
    {
        return "[$('aw_coupon_save'),$('aw_coupon_save_and_continue_edit')].each(function(elem){
            $(elem).removeClassName('disabled').writeAttribute('disabled', null);
        });";
    }

    /**
     * @return string
     */
    protected function _getDisableSaveButtonsJs()
    {
        return "[$('aw_coupon_save'),$('aw_coupon_save_and_continue_edit')].each(function(elem){
            $(elem).addClassName('disabled').writeAttribute('disabled', 'disabled');
        });";
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }
        return $this->getUrl('*/*/save');
    }
}

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

class Itoris_Productqa_Block_Admin_Config_Edit_Form extends Mage_Adminhtml_Block_System_Config_Form {

	protected function _prepareForm() {
		try {
			$defaultSettings = Mage::getModel('itoris_productqa/settings');
			$defaultSettings->load($this->getWebsiteId(), $this->getStoreId());
		} catch (Exception $e) {
			Mage::getSingleton('core/session')->addError($e->getMessage());
		}
		$useWebsite = (bool)$this->getStoreId();
		
		if (!$useWebsite) {
			$useDefault = (bool)$this->getWebsiteId();
		} else {
			$useDefault = false;
		}
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>$this->__('General Settings')));

		$fieldset->addField('enable', 'select', array(
                'name'  => 'settings[enable][value]',
                'label' => $this->__('Extension Enabled'),
                'title' => $this->__('Extension Enabled'),
                'required' => true,
				'values' => array(
									array('label' => $this->__('Yes'),
										'value' => Itoris_ProductQa_Model_Settings::ENABLE_YES),
									array('label' => $this->__('No'),
										'value' => Itoris_ProductQa_Model_Settings::ENABLE_NO),
				),
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('enable', $useWebsite),
			)
        )->getRenderer()->setTemplate('itoris/productqa/config/form/element.phtml');
		
        $fieldset->addField('visible', 'select', array(
                'name'  => 'settings[visible][value]',
                'label' => $this->__('Module is visible for'),
                'title' => $this->__('Module is visible for'),
                'required' => true,
				'values' => array(
					array(
						'label' => $this->__('Visitors & Customers'),
						'value' => Itoris_ProductQa_Model_Settings::VISIBLE_ALL
					),
					array(
						'label' => $this->__('Customers only'),
						'value' => Itoris_ProductQa_Model_Settings::VISIBLE_CUSTOMER
					),
				),
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('visible', $useWebsite),
            )
        );

		$disableField = '';
		if (($defaultSettings->getVisible() == Itoris_ProductQa_Model_Settings::VISIBLE_CUSTOMER)) {
			$disableField = 'disabled';
		}

		$fieldset->addField('hidden_visitor_post', 'hidden', array(
                'name'  => 'settings[visitor_post][value]',
            )
        );

		$fieldset->addField('visitor_post', 'select', array(
                'name'  => 'settings[visitor_post][value]',
                'label' => $this->__('Visitors can post'),
                'title' => $this->__('Visitors can post'),
                'required' => true,
				'values' => array(
					array(
						'label' => $this->__('Questions & Answers'),
						'value' => Itoris_ProductQa_Model_Settings::VISITOR_POST_Q_A
					),
					array(
						'label' => $this->__('Questions only'),
						'value' => Itoris_ProductQa_Model_Settings::VISITOR_POST_Q
					),
					array(
						'label' => $this->__('Answers only'),
						'value' => Itoris_ProductQa_Model_Settings::VISITOR_POST_A
					),
				),
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('visitor_post', $useWebsite),
				$disableField => $disableField,
            )
        );

		$fieldset->addField('hidden_visitor_can_rate', 'hidden', array(
				'name'  => 'settings[visitor_can_rate][value]',
			)
		);

		$fieldset->addField('visitor_can_rate', 'select', array(
			'name'   => 'settings[visitor_can_rate][value]',
			'label'  => $this->__('Visitors can rate'),
			'title'  => $this->__('Visitors can rate'),
			'values' => array(
				array(
					'label' => $this->__('No'),
					'value' => 0,
				),
				array(
					'label' => $this->__('Yes, helpful/not helpful/inappropriate'),
					'value' => Itoris_ProductQa_Model_Settings::VISITORS_RATE_ALL,
				),
				array(
					'label' => $this->__('Yes, inappropriate only'),
					'value' => Itoris_ProductQa_Model_Settings::VISITORS_RATE_INAPPR,
				),
			),
			$disableField => $disableField,
		));

		$fieldset->addField('color_scheme', 'select', array(
                'name'  => 'settings[color_scheme][value]',
                'label' => Mage::helper('itoris_productqa')->__('Color scheme'),
                'title' => Mage::helper('itoris_productqa')->__('Color scheme'),
                'required' => true,
				'values' => array(
					array(
						'label' => $this->__('Simple'),
						'value' => Itoris_ProductQa_Model_Settings::THEME_SIMPLE,
					),
					array(
						'label' => $this->__('Sharp - Magento'),
						'value' => Itoris_ProductQa_Model_Settings::THEME_SHARP_MAGENTO
					),
					array(
						'label' => $this->__('Sharp - Blue'),
						'value' => Itoris_ProductQa_Model_Settings::THEME_SHARP_BLUE
					),
					array(
						'label' => $this->__('Sharp - White'),
						'value' => Itoris_ProductQa_Model_Settings::THEME_SHARP_WHITE
					),
					array(
						'label' => $this->__('Sharp - Gray'),
						'value' => Itoris_ProductQa_Model_Settings::THEME_SHARP_GRAY
					),
					array(
						'label' => $this->__('Sharp - Black'),
						'value' => Itoris_ProductQa_Model_Settings::THEME_SHARP_BLACK
					),
					array(
						'label' => $this->__('Smooth - Magento'),
						'value' => Itoris_ProductQa_Model_Settings::THEME_SMOOTH_MAGENTO
					),
					array(
						'label' => $this->__('Smooth - Blue'),
						'value' => Itoris_ProductQa_Model_Settings::THEME_SMOOTH_BLUE
					),
					array(
						'label' => $this->__('Smooth - White'),
						'value' => Itoris_ProductQa_Model_Settings::THEME_SMOOTH_WHITE
					),
					array(
						'label' => $this->__('Smooth - Gray'),
						'value' => Itoris_ProductQa_Model_Settings::THEME_SMOOTH_GRAY
					),
					array(
						'label' => $this->__('Smooth - Black'),
						'value' => Itoris_ProductQa_Model_Settings::THEME_SMOOTH_BLACK
					),
				),
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('color_scheme', $useWebsite),
            )
        );

		$disableField = '';
		if (($defaultSettings->getVisible() == Itoris_ProductQa_Model_Settings::VISIBLE_CUSTOMER)) {
			$disableField = 'disabled';
		}

		$fieldset->addField('hidden_captcha', 'hidden', array(
                'name'  => 'settings[captcha][value]',
            )
        );

		$fieldset->addField('captcha', 'select', array(
                'name'  => 'settings[captcha][value]',
                'label' => $this->__('Show Captcha image for visitors'),
                'title' => $this->__('Show Captcha image for visitors'),
                'required' => true,
				'values' => array(
					array(
						'label' => $this->__('Alikon mod'),
						'value' => Itoris_ProductQa_Model_Settings::SHOW_ALIKON
					),
					array(
						'label' => $this->__('Captcha form'),
						'value' => Itoris_ProductQa_Model_Settings::SHOW_CAPTCHA
					),
					array(
						'label' => $this->__('SecurImage'),
						'value' => Itoris_ProductQa_Model_Settings::SHOW_SECURIMAGE
					),
					array(
						'label' => $this->__('No Captcha'),
						'value' => Itoris_ProductQa_Model_Settings::NO_CAPTCHA
					),
				),
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('captcha', $useWebsite),
				$disableField => $disableField,
            )
        );

		$fieldset->addField('questions_approval', 'select', array(
                'name'  => 'settings[questions_approval][value]',
                'label' => $this->__('Questions approval'),
                'title' => $this->__('Questions approval'),
                'required' => true,
				'values' => array(
					array(
						'label' => $this->__('Manual approval'),
						'value' => Itoris_ProductQa_Model_Settings::Q_APPROVAL_MANUAL
					),
					array(
						'label' => $this->__('Auto approval for Customers'),
						'value' => Itoris_ProductQa_Model_Settings::Q_APPROVAL_AUTO_CUSTOMER
					),
					array(
						'label' => $this->__('Auto approval for Customers & Visitors'),
						'value' => Itoris_ProductQa_Model_Settings::Q_APPROVAL_AUTO
					),
				),
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('questions_approval', $useWebsite),
            )
        );

		$fieldset->addField('answers_approval', 'select', array(
                'name'  => 'settings[answers_approval][value]',
                'label' => $this->__('Answers approval'),
                'title' => $this->__('Answers approval'),
                'required' => true,
				'values' => array(
					array(
						'label' => $this->__('Manual approval'),
						'value' => Itoris_ProductQa_Model_Settings::A_APPROVAL_MANUAL
					),
					array(
						'label' => $this->__('Auto approval for Customers'),
						'value' => Itoris_ProductQa_Model_Settings::A_APPROVAL_AUTO_CUSTOMER
					),
					array(
						'label' => $this->__('Auto approval for Customers & Visitors'),
						'value' => Itoris_ProductQa_Model_Settings::A_APPROVAL_AUTO
					),
				),
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('answers_approval', $useWebsite),
            )
        );

        $fieldset->addField('question_length', 'text', array(
                'name'  => 'settings[question_length][value]',
                'label' => $this->__('Maximum question length'),
                'title' => $this->__('Maximum question length'),
                'required' => true,
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('question_length', $useWebsite),
            )
        );

		$fieldset->addField('answer_length', 'text', array(
                'name'  => 'settings[answer_length][value]',
                'label' => $this->__('Maximum answer length'),
                'title' => $this->__('Maximum answer length'),
                'required' => true,
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('answer_length', $useWebsite),
            )
        );

		$fieldset->addField('questions_per_page', 'text', array(
                'name'  => 'settings[questions_per_page][value]',
                'label' => $this->__('Questions per page'),
                'title' => $this->__('Questions per page'),
                'required' => true,
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('questions_per_page', $useWebsite),
            )
        );

		$fieldset->addField('notify_administrator', 'checkbox', array(
                'name'  => 'settings[notify_administrator][value]',
                'label' => $this->__('Notify administrator when new question/answer received'),
                'title' => $this->__('Notify administrator when new question/answer received'),
				'checked' =>  $defaultSettings->getNotifyAdministrator(),
				'onclick'    => 'this.value = this.checked ? 1 : 0; toogleFieldEditMode(this, admin_email);',
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('notify_administrator', $useWebsite),
            )
        );

		$fieldset->addField('admin_email', 'text', array(
                'name'  => 'settings[admin_email][value]',
                'label' => $this->__('Administrator Email'),
                'title' => $this->__('Administrator Email'),
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'require' => true,
				'disabled' => (!$useDefault && !$useWebsite && $defaultSettings->isParentValue('notify_administrator', $useWebsite)) ? !$defaultSettings->getAdminEmail() : ($defaultSettings->isParentValue('notify_administrator', $useWebsite)) ? true : !$defaultSettings->getAdminEmail(),
            )
        );

		$fieldset->addField('allow_subscribing_question', 'checkbox', array(
				'name'  => 'settings[allow_subscribing_question][value]',
				'label' => $this->__('Allow subscribing to questions'),
				'title' => $this->__('Allow subscribing to questions'),
				'checked' =>  $defaultSettings->getAllowSubscribingQuestion(),
				'onclick'    => 'this.value = this.checked ? 1 : 0;',
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('allow_subscribing_question', $useWebsite),
			)
		);

		$lingual_fieldset = $form->addFieldset('lingual_fieldset', array('legend' => $this->__('Email Settings')));

		$lingual_fieldset->addField('template_admin_name', 'text', array(
                'name'  => 'settings[template_admin_name][value]',
                'label' => $this->__('Admin email from name'),
                'title' => $this->__('Admin email from name'),
				'required' => true,
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('template_admin_name', $useWebsite),
            )
        );

		$lingual_fieldset->addField('template_admin_email', 'text', array(
                'name'  => 'settings[template_admin_email][value]',
                'label' => $this->__('Admin from email'),
                'title' => $this->__('Admin from email'),
				'required' => true,
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('template_admin_email', $useWebsite),
            )
        );

		$lingual_fieldset->addField('template_admin_subject', 'text', array(
                'name'  => 'settings[template_admin_subject][value]',
                'label' => $this->__('Admin email subject'),
                'title' => $this->__('Admin email subject'),
				'required' => true,
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('template_admin_subject', $useWebsite),
            )
        );

		$lingual_fieldset->addField('template_admin_notification', 'textarea', array(
                'name'  => 'settings[template_admin_notification][value]',
                'label' => $this->__('Admin Email Notification Template'),
                'title' => $this->__('Admin Email Notification Template'),
				'required' => true,
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('template_admin_notification', $useWebsite),
            )
        );

		$lingual_fieldset->addField('template_user_name', 'text', array(
                'name'  => 'settings[template_user_name][value]',
                'label' => $this->__('Customer email from name'),
                'title' => $this->__('Customer email from name'),
				'required' => true,
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('template_user_name', $useWebsite),
            )
        );

		$lingual_fieldset->addField('template_user_email', 'text', array(
                'name'  => 'settings[template_user_email][value]',
                'label' => $this->__('Customer from email'),
                'title' => $this->__('Customer from email'),
				'required' => true,
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('template_user_email', $useWebsite),
            )
        );

		$lingual_fieldset->addField('template_user_subject', 'text', array(
                'name'  => 'settings[template_user_subject][value]',
                'label' => $this->__('Customer email subject'),
                'title' => $this->__('Customer email subject'),
				'required' => true,
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('template_user_subject', $useWebsite),
            )
        );

		$lingual_fieldset->addField('template_user_notification', 'textarea', array(
                'name'  => 'settings[template_user_notification][value]',
                'label' => $this->__('Customer Email Notification Template'),
                'title' => $this->__('Customer Email Notification Template'),
				'required' => true,
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('template_user_notification', $useWebsite),
            )
        );

		$lingual_fieldset->addField('template_guest_name', 'text', array(
                'name'  => 'settings[template_guest_name][value]',
                'label' => $this->__('Guest email from name'),
                'title' => $this->__('Guest email from name'),
				'required' => true,
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('template_guest_name', $useWebsite),
            )
        );

		$lingual_fieldset->addField('template_guest_email', 'text', array(
                'name'  => 'settings[template_guest_email][value]',
                'label' => $this->__('Guest from email'),
                'title' => $this->__('Guest from email'),
				'required' => true,
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('template_guest_email', $useWebsite),
            )
        );

		$lingual_fieldset->addField('template_guest_subject', 'text', array(
                'name'  => 'settings[template_guest_subject][value]',
                'label' => $this->__('Guest email subject'),
                'title' => $this->__('Guest email subject'),
				'required' => true,
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('template_guest_subject', $useWebsite),
            )
        );

		$lingual_fieldset->addField('template_guest_notification', 'textarea', array(
                'name'  => 'settings[template_guest_notification][value]',
                'label' => $this->__('Guest Email Notification Template'),
                'title' => $this->__('Guest Email Notification Template'),
				'required' => true,
				'use_default' => $useDefault,
				'use_website' => $useWebsite,
				'use_parent_value' => $defaultSettings->isParentValue('template_guest_notification', $useWebsite),
            )
        );

        $form->setValues($defaultSettings->getDefaultData());

        $form->setAction($this->getUrl('*/*/save', array( 'website_id' => $this->getWebsiteId(), 'store_id' => $this->getStoreId())));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');

        $this->setForm($form);

        return parent::_prepareForm();
    }

	/**
	 * Retrieve store id by store code from the request
	 *
	 * @return int
	 */
	protected function getStoreId() {
		if ($this->getStoreCode()) {
            return Mage::app()->getStore($this->getStoreCode())->getId();
        }
		return 0;
	}

	/**
	 * Retrieve website id by website code from the request
	 *
	 * @return int
	 */
	protected function getWebsiteId() {
		if ($this->getWebsiteCode()) {
            return Mage::app()->getWebsite($this->getWebsiteCode())->getId();
        }
		return 0;
	}
}
?>
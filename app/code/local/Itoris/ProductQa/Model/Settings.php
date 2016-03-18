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

/**
 * @method getAllowSubscribingQuestion()
 */

class Itoris_ProductQa_Model_Settings extends Varien_Object {

	/** @var Varien_Db_Adapter_Pdo_Mysql */
	private $_resource;
	private $_table = 'itoris_productqa_settings';
	private $_tableTextSettings = 'itoris_productqa_settings_text';
	private $_textOptions = array( 'admin_email',
			'template_admin_name', 'template_admin_email', 'template_admin_subject', 'template_admin_notification',
			'template_user_name', 'template_user_email', 'template_user_subject', 'template_user_notification',
			'template_guest_name', 'template_guest_email', 'template_guest_subject', 'template_guest_notification',
			);
	private $_scope;
	private $_scopeId;
	private $_settings;

	const ENABLE_YES = 1;
	const ENABLE_NO = 2;
	const VISIBLE_ALL = 3;
	const VISIBLE_CUSTOMER = 4;
	const VISITOR_POST_Q_A =5;
	const VISITOR_POST_Q = 6;
	const VISITOR_POST_A = 7;
	const THEME_SHARP_MAGENTO = 8;
	const THEME_SHARP_BLUE = 9;
	const THEME_SHARP_WHITE = 10;
	const THEME_SHARP_GRAY = 11;
	const THEME_SHARP_BLACK = 12;
	const THEME_SMOOTH_MAGENTO = 13;
	const THEME_SMOOTH_BLUE = 14;
	const THEME_SMOOTH_WHITE = 15;
	const THEME_SMOOTH_GRAY = 16;
	const THEME_SMOOTH_BLACK = 17;
	const THEME_SIMPLE = 18;
	const SHOW_ALIKON = 18;
	const SHOW_CAPTCHA = 19;
	const SHOW_SECURIMAGE = 20;
	const NO_CAPTCHA = 21;
	const Q_APPROVAL_MANUAL = 22;
	const Q_APPROVAL_AUTO_CUSTOMER = 23;
	const Q_APPROVAL_AUTO = 24;
	const A_APPROVAL_MANUAL = 25;
	const A_APPROVAL_AUTO_CUSTOMER = 26;
	const A_APPROVAL_AUTO = 27;
	const VISITORS_RATE_ALL = 28;
	const VISITORS_RATE_INAPPR = 29;

	public function __construct() {
		$this->_getConnection();
		$this->_table = Mage::getSingleton('core/resource')->getTableName($this->_table);
		$this->_tableTextSettings = Mage::getSingleton('core/resource')->getTableName($this->_tableTextSettings);
	}

	/**
	 * Save settings
	 *
	 * @param $settings
	 * @param string $scope
	 * @param int $scopeId
	 */
	public function save($settings, $scope = 'default', $scopeId = 0) {
		$this->_scope = $this->_resource->quote($scope);
		$this->_scopeId = (int)$scopeId;
		
		$this->_deleteSettings();
		$newSettings = array();
		foreach ($settings as $key => $value) {
			$value = isset($value['value']) ? $value['value'] : 0;
			if (!isset($settings[$key]['use_parent'])  || $scope == 'default') {
				$newSettings[$key] = array('value' => $value, 'type' => 'default');
				if ($this->_isTextOption($key)) {
					$newSettings[$key]['type'] = 'text';
				}
			}
		}
		if (!isset($settings['notify_administrator']['use_parent']) && !isset($settings['notify_administrator'])) {
			$newSettings['notify_administrator'] = array('value' => 0, 'type' => 'default');
			$newSettings['admin_email'] = array('value' => 0, 'type' => 'text');
		}
		if (!empty($newSettings)) {
			$this->_saveSettings($newSettings);
		}
		$this->_scope = null;
		$this->_scopeId = null;
	}

	/**
	 * Load settings for a scope view
	 *
	 * @param $websiteId
	 * @param $storeId
	 * @return Itoris_ProductQa_Model_Settings
	 */
	public function load($websiteId, $storeId) {
		$websiteId = (int)$websiteId;
		$storeId = (int)$storeId;
		$settings = $this->_resource->fetchAll("
			SELECT e.key, e.scope, if(STRCMP(e.type, 'text'), e.value, t.value) as value
			FROM $this->_table as e
			left join $this->_tableTextSettings as t on e.id = t.setting_id
			WHERE (e.scope = 'default' and e.scope_id = 0)
			OR (e.scope = 'website' and e.scope_id = $websiteId)
			OR (e.scope = 'store' and e.scope_id = $storeId)
		");
		$this->_saveSettingsIntoArray($settings);
		return $this;
	}

	private function _saveSettingsIntoArray($settings) {
		foreach($settings as $value) {
			$this->_settings[$value['scope']][$value['key']] = $value['value'];
		}
	}

	public function __call($method, $args) {
        if(substr($method, 0, 3) == 'get') {
                $key = $this->_underscore(substr($method,3));
                if (isset($this->_settings['store'][$key])) {
					return $this->_settings['store'][$key];
				} elseif (isset($this->_settings['website'][$key])) {
					return $this->_settings['website'][$key];
				} elseif (isset($this->_settings['default'][$key])) {
					return $this->_settings['default'][$key];
				}
				return $this->getData($key, isset($args[0]) ? $args[0] : null);
        } else {
			parent::__call($method,$args);
		}
    }

	/**
	 * Check setting value is value of the parent scope view
	 *
	 * @param $key
	 * @param bool $isStore
	 * @return bool
	 */
	public function isParentValue($key, $isStore = false) {
		if (isset($this->_settings['store'][$key])) {
			return false;
		}
		if (!$isStore) {
			if (isset($this->_settings['website'][$key])) {
				return false;
			}
		}
		return true;
	}

	private function _getConnection() {
		$this->_resource = Mage::getSingleton('core/resource')->getConnection('core_write');
		return $this->_resource;
	}

	private function _deleteSettings() {
		$this->_resource->query("DELETE FROM $this->_table WHERE `scope`=$this->_scope and `scope_id`=$this->_scopeId");
	}

	private function _saveSettings($settings) {
		$settingsValues = '';
		$textValues = array();
		foreach ($settings as $key => $values) {
			$value = 0;
			$type = $values['type'];
			if ($type != 'text') {
				$value = (int)$values['value'];
			} else {
				$textValues[$key] = $this->_resource->quote($values['value']);
			}
			$settingsValues .=  "($this->_scope, $this->_scopeId, '$key', $value, '$type'),";
		}
		$settingsValues = substr($settingsValues, 0, strlen($settingsValues) - 1);
		$this->_resource->query("INSERT INTO $this->_table (`scope`, `scope_id`, `key`, `value`, `type`) VALUES $settingsValues");
		if (!empty($textValues)) {
			$this->_saveTextSettings($textValues);
		}
	}

	private function _saveTextSettings($values) {
		$textSettings = $this->_resource->fetchAll("SELECT `id`, `scope`, `scope_id`, `key` FROM $this->_table WHERE `type` = 'text' and `scope` = $this->_scope and `scope_id` = $this->_scopeId");
		$textValues = array();
		foreach ($textSettings as $setting) {
			$key = $setting['key'];
			$textValues[] = "( {$setting['id']}, {$values[$key]})";
		}
		$textValues = implode(',', $textValues);

		$this->_resource->query("INSERT INTO $this->_tableTextSettings (`setting_id`, `value`) VALUES $textValues");
	}

	private function _isTextOption($key) {
		foreach ($this->_textOptions as $value) {
			if ($value == $key) {
				return true;
			}
		}
		return false;
	}

	public function _isValid($settings) {
		$errors = array();
		if (isset($settings['question_length']['value'])) {
			if (!Zend_Validate::is($settings['question_length']['value'], 'GreaterThan', array(-1))
				|| !Zend_Validate::is($settings['question_length']['value'], 'Int')
			) {
				$errors[] = Mage::helper('itoris_productqa')->__('Maximum question length should be number zero or greater than 0!');
			}
		}
		if (isset($settings['answer_length']['value'])) {
			if (!Zend_Validate::is($settings['answer_length']['value'], 'GreaterThan', array(-1))
				|| !Zend_Validate::is($settings['answer_length']['value'], 'Int')
			) {
				$errors[] = Mage::helper('itoris_productqa')->__('Maximum answer length should be number zero or greater than 0!');
			}
		}
		if (isset($settings['questions_per_page']['value'])) {
			if (!Zend_Validate::is($settings['questions_per_page']['value'], 'GreaterThan', array(-1))
				|| !Zend_Validate::is($settings['questions_per_page']['value'], 'Int')
			) {
				$errors[] = Mage::helper('itoris_productqa')->__('Questions per page should be number zero or greater than 0!');
			}
		}
		if (isset($settings['admin_email'])) {
			if (!Zend_Validate::is($settings['admin_email']['value'], 'EmailAddress')) {
				$errors[] = Mage::helper('itoris_productqa')->__('Please enter a valid email!');
			}
		}

		if (isset($settings['template_admin_email']['value'])) {
			if (!Zend_Validate::is($settings['template_admin_email']['value'], 'EmailAddress')) {
				$errors[] = Mage::helper('itoris_productqa')->__('Please enter a valid email!');
			}
		}
		if (isset($settings['template_user_email']['value'])) {
			if (!Zend_Validate::is($settings['template_user_email']['value'], 'EmailAddress')) {
				$errors[] = Mage::helper('itoris_productqa')->__('Please enter a valid email!');
			}
		}
		if (empty($errors)) {
			return true;
		}
		return $errors;
	}

	public function getDefaultData() {
		return array(
			'enable'                      => $this->getEnable(),
			'visible'                     => $this->getVisible(),
			'visitor_post'                => $this->getVisitorPost(),
			'hidden_visitor_post'         => $this->getVisitorPost(),
			'color_scheme'                => $this->getColorScheme(),
			'captcha'                     => $this->getCaptcha(),
			'hidden_captcha'              => $this->getCaptcha(),
			'questions_approval'          => $this->getQuestionsApproval(),
			'answers_approval'            => $this->getAnswersApproval(),
			'question_length'             => $this->getQuestionLength(),
			'answer_length'               => $this->getAnswerLength(),
			'questions_per_page'          => $this->getQuestionsPerPage(),
			'notify_administrator'        => $this->getNotifyAdministrator(),
			'admin_email'                 => ($this->getAdminEmail()) ? $this->getAdminEmail() : '',
			'template_admin_name'         => $this->getTemplateAdminName(),
			'template_admin_email'        => $this->getTemplateAdminEmail(),
			'template_admin_subject'      => $this->getTemplateAdminSubject(),
			'template_admin_notification' => $this->getTemplateAdminNotification(),
			'template_user_name'          => $this->getTemplateUserName(),
			'template_user_email'         => $this->getTemplateUserEmail(),
			'template_user_subject'       => $this->getTemplateUserSubject(),
			'template_user_notification'  => $this->getTemplateUserNotification(),
			'template_guest_name'         => $this->getTemplateGuestName(),
			'template_guest_email'        => $this->getTemplateGuestEmail(),
			'template_guest_subject'      => $this->getTemplateGuestSubject(),
			'template_guest_notification' => $this->getTemplateGuestNotification(),
			'visitor_can_rate'            => $this->getVisitorCanRate(),
			'hidden_visitor_can_rate'     => $this->getVisitorCanRate(),
			'allow_subscribing_question'  => $this->getAllowSubscribingQuestion(),
		);
	}

	public function canVisitorRate() {
		return $this->getVisitorCanRate() == self::VISITORS_RATE_ALL;
	}

	public function canVisitorRateInappr() {
		return $this->canVisitorRate() || $this->getVisitorCanRate() == self::VISITORS_RATE_INAPPR;
	}
}
?>
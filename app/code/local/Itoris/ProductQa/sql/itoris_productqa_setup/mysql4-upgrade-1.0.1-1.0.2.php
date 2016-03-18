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
 
$this->startSetup();

$this->run("

ALTER TABLE {$this->getTable('itoris_productqa_questions')} ADD `email` VARCHAR( 255 ) NULL;

REPLACE INTO {$this->getTable('itoris_productqa_settings')} (`scope`, `scope_id`, `key`, `value`, `type`) VALUES
		('default', 0, 'template_guest_name', 0, 'text');

REPLACE INTO {$this->getTable('itoris_productqa_settings_text')} (`setting_id`, `value`)
		 values ((SELECT LAST_INSERT_ID()), '{{store_view_name}}');

REPLACE INTO {$this->getTable('itoris_productqa_settings')} (`scope`, `scope_id`, `key`, `value`, `type`) VALUES
		('default', 0, 'template_guest_email', 0, 'text');

REPLACE INTO {$this->getTable('itoris_productqa_settings_text')} (`setting_id`, `value`)
		 values ((SELECT LAST_INSERT_ID()), 'admin@admin.com');

REPLACE INTO {$this->getTable('itoris_productqa_settings')} (`scope`, `scope_id`, `key`, `value`, `type`) VALUES
		('default', 0, 'template_guest_subject', 0, 'text');

REPLACE INTO {$this->getTable('itoris_productqa_settings_text')} (`setting_id`, `value`)
		 values ((SELECT LAST_INSERT_ID()), 'New answer added to your question.');

REPLACE INTO {$this->getTable('itoris_productqa_settings')} (`scope`, `scope_id`, `key`, `value`, `type`) VALUES
		('default', 0, 'template_guest_notification', 0, 'text');

REPLACE INTO {$this->getTable('itoris_productqa_settings_text')} (`setting_id`, `value`)
		 values ((SELECT LAST_INSERT_ID()), '<p>Dear {{username}},</p><p>&nbsp;</p><p>There is a new reply on your question about {{product_name}}.</p><p>The Question is {{question}}.</p><p>The reply is {{reply}}.</p><p>&nbsp;</p><p>The reply can be found on {{product_page}}.</p>');

");

$this->endSetup();
?>
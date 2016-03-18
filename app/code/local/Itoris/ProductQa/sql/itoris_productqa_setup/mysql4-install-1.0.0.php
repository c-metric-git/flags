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

	DROP TABLE IF EXISTS {$this->getTable('itoris_productqa_settings_text')};
	DROP TABLE IF EXISTS {$this->getTable('itoris_productqa_settings')};

	CREATE TABLE IF NOT EXISTS {$this->getTable('itoris_productqa_settings')} (
		`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`scope` ENUM('default', 'website', 'store') NOT NULL ,
		`scope_id` INT UNSIGNED NOT NULL ,
		`key` VARCHAR( 255 ) NOT NULL ,
		`value` INT UNSIGNED NOT NULL ,
		`type` ENUM('text', 'default') NULL,
		UNIQUE(`scope`, `scope_id`, `key`)
	) ENGINE = InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

	CREATE TABLE IF NOT EXISTS {$this->getTable('itoris_productqa_settings_text')} (
		`setting_id` INT UNSIGNED NOT NULL ,
		`value` TEXT NOT NULL,
		INDEX ( `setting_id`)
	) ENGINE = InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

	ALTER TABLE {$this->getTable('itoris_productqa_settings_text')} ADD FOREIGN KEY ( `setting_id` ) REFERENCES {$this->getTable('itoris_productqa_settings')} (
	`id`
	) ON DELETE CASCADE ON UPDATE CASCADE ;

	CREATE TABLE IF NOT EXISTS {$this->getTable('itoris_productqa_questions')} (
		`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`inappr` BOOLEAN NOT NULL ,
		`created_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`status` INT UNSIGNED NOT NULL ,
		`submitter_type` INT UNSIGNED NOT NULL ,
		`product_id` INT UNSIGNED NOT NULL ,
		`nickname` VARCHAR( 30 ) NOT NULL ,
		`content` TEXT NOT NULL ,
		`customer_id` INT UNSIGNED NULL ,
		`notify` BOOLEAN NOT NULL ,
		INDEX ( `product_id`), INDEX(`customer_id`)
	) ENGINE = InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

	ALTER TABLE {$this->getTable('itoris_productqa_questions')} ADD FOREIGN KEY ( `product_id` ) REFERENCES {$this->getTable('catalog_product_entity')} (
	`entity_id`
	) ON DELETE CASCADE ON UPDATE CASCADE ;

	CREATE TABLE IF NOT EXISTS {$this->getTable('itoris_productqa_questions_ratings')} (
		`customer_id` INT UNSIGNED NULL ,
		`q_id` INT UNSIGNED NOT NULL ,
		`value` ENUM( '-1', '1' ) NOT NULL ,
		UNIQUE ( `q_id`,`customer_id` )
	) ENGINE = InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

	ALTER TABLE {$this->getTable('itoris_productqa_questions_ratings')} ADD FOREIGN KEY ( `customer_id` ) REFERENCES {$this->getTable('customer_entity')} (
	`entity_id`
	) ON DELETE SET NULL ON UPDATE SET NULL ;

	ALTER TABLE {$this->getTable('itoris_productqa_questions_ratings')} ADD FOREIGN KEY ( `q_id` ) REFERENCES {$this->getTable('itoris_productqa_questions')} (
	`id`
	) ON DELETE CASCADE ON UPDATE CASCADE ;

	CREATE TABLE IF NOT EXISTS  {$this->getTable('itoris_productqa_questions_visibility')} (
		`q_id` INT UNSIGNED NOT NULL ,
		`store_id` SMALLINT UNSIGNED NOT NULL ,
		UNIQUE ( `q_id`,`store_id` )
	) ENGINE = InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

	ALTER TABLE {$this->getTable('itoris_productqa_questions_visibility')} ADD FOREIGN KEY ( `q_id` ) REFERENCES {$this->getTable('itoris_productqa_questions')} (
	`id`
	) ON DELETE CASCADE ON UPDATE CASCADE ;

	ALTER TABLE {$this->getTable('itoris_productqa_questions_visibility')} ADD FOREIGN KEY ( `store_id` ) REFERENCES {$this->getTable('core_store')} (
	`store_id`
	) ON DELETE CASCADE ON UPDATE CASCADE ;

	CREATE TABLE IF NOT EXISTS {$this->getTable('itoris_productqa_answers')} (
		`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`q_id` INT UNSIGNED NOT NULL ,
		`inappr` BOOLEAN NOT NULL ,
		`created_datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`status` INT UNSIGNED NOT NULL ,
		`submitter_type` INT UNSIGNED NOT NULL ,
		`nickname` VARCHAR( 30 ) NOT NULL ,
		`content` TEXT NOT NULL ,
		`customer_id` INT UNSIGNED NULL ,
		INDEX ( `q_id`), INDEX (`customer_id`)
	) ENGINE = InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

	ALTER TABLE {$this->getTable('itoris_productqa_answers')} ADD FOREIGN KEY ( `q_id` ) REFERENCES {$this->getTable('itoris_productqa_questions')} (
	`id`
	) ON DELETE CASCADE ON UPDATE CASCADE ;

	CREATE TABLE IF NOT EXISTS {$this->getTable('itoris_productqa_answers_ratings')} (
		`customer_id` INT UNSIGNED NULL ,
		`a_id` INT UNSIGNED NOT NULL ,
		`value` ENUM( '-1', '1' ) NOT NULL ,
		UNIQUE ( `a_id`, `customer_id` )
	) ENGINE = InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

	ALTER TABLE {$this->getTable('itoris_productqa_answers_ratings')} ADD FOREIGN KEY ( `customer_id` ) REFERENCES {$this->getTable('customer_entity')} (
	`entity_id`
	) ON DELETE SET NULL ON UPDATE SET NULL ;

	ALTER TABLE {$this->getTable('itoris_productqa_answers_ratings')} ADD FOREIGN KEY ( `a_id` ) REFERENCES {$this->getTable('itoris_productqa_answers')} (
	`id`
	) ON DELETE CASCADE ON UPDATE CASCADE ;

	REPLACE INTO {$this->getTable('itoris_productqa_settings')} (`scope`, `scope_id`, `key`, `value`, `type`) VALUES
		('default', 0, 'enable', 1, 'default'),
		('default', 0, 'visible', 3, 'default'),
		('default', 0, 'visitor_post', 5, 'default'),
		('default', 0, 'color_scheme', 8, 'default'),
		('default', 0, 'captcha', 18, 'default'),
		('default', 0, 'questions_approval', 22, 'default'),
		('default', 0, 'answers_approval', 25, 'default'),
		('default', 0, 'question_length', 255, 'default'),
		('default', 0, 'answer_length', 1000, 'default'),
		('default', 0, 'questions_per_page', 0, 'default'),
		('default', 0, 'notify_administrator', 0, 'default');

	REPLACE INTO {$this->getTable('itoris_productqa_settings')} (`scope`, `scope_id`, `key`, `value`, `type`) VALUES
		('default', 0, 'template_admin_name', 0, 'text');

	REPLACE INTO {$this->getTable('itoris_productqa_settings_text')} (`setting_id`, `value`)
			 values ((SELECT LAST_INSERT_ID()), '{{store_view_name}}');

	REPLACE INTO {$this->getTable('itoris_productqa_settings')} (`scope`, `scope_id`, `key`, `value`, `type`) VALUES
			('default', 0, 'template_admin_email', 0, 'text');

	REPLACE INTO {$this->getTable('itoris_productqa_settings_text')} (`setting_id`, `value`)
			 values ((SELECT LAST_INSERT_ID()), 'admin@admin.com');

	REPLACE INTO {$this->getTable('itoris_productqa_settings')} (`scope`, `scope_id`, `key`, `value`, `type`) VALUES
			('default', 0, 'template_admin_subject', 0, 'text');

	REPLACE INTO {$this->getTable('itoris_productqa_settings_text')} (`setting_id`, `value`)
			 values ((SELECT LAST_INSERT_ID()), 'New {{question_or_answer}} received for moderation.');

	REPLACE INTO {$this->getTable('itoris_productqa_settings')} (`scope`, `scope_id`, `key`, `value`, `type`) VALUES
			('default', 0, 'template_admin_notification', 0, 'text');

	REPLACE INTO {$this->getTable('itoris_productqa_settings_text')} (`setting_id`, `value`)
			 values ((SELECT LAST_INSERT_ID()), '<p>Dear admin,</p><p>&nbsp;</p><p>New {{question_or_answer}} received for moderation. Details:</p><p>Store: {{store_view_name}}</p><p>User Type: {{user_type}}</p><p>Nickname: {{nickname}}</p><p>Product: {{product_name}}</p><p>{{question_or_answer_details}}</p><p>&nbsp;</p><p>The {{question_or_answer}} details are available in the backend following the link: {{question_details_backend_url}}.</p><p>&nbsp;</p><p>Do not reply on this email as it has been generated automatically.</p>');

	REPLACE INTO {$this->getTable('itoris_productqa_settings')} (`scope`, `scope_id`, `key`, `value`, `type`) VALUES
			('default', 0, 'template_user_name', 0, 'text');

	REPLACE INTO {$this->getTable('itoris_productqa_settings_text')} (`setting_id`, `value`)
			 values ((SELECT LAST_INSERT_ID()), '{{store_view_name}}');

	REPLACE INTO {$this->getTable('itoris_productqa_settings')} (`scope`, `scope_id`, `key`, `value`, `type`) VALUES
			('default', 0, 'template_user_email', 0, 'text');

	REPLACE INTO {$this->getTable('itoris_productqa_settings_text')} (`setting_id`, `value`)
			 values ((SELECT LAST_INSERT_ID()), 'admin@admin.com');

	REPLACE INTO {$this->getTable('itoris_productqa_settings')} (`scope`, `scope_id`, `key`, `value`, `type`) VALUES
			('default', 0, 'template_user_subject', 0, 'text');

	REPLACE INTO {$this->getTable('itoris_productqa_settings_text')} (`setting_id`, `value`)
			 values ((SELECT LAST_INSERT_ID()), 'New answer added to your question.');

	REPLACE INTO {$this->getTable('itoris_productqa_settings')} (`scope`, `scope_id`, `key`, `value`, `type`) VALUES
			('default', 0, 'template_user_notification', 0, 'text');

	REPLACE INTO {$this->getTable('itoris_productqa_settings_text')} (`setting_id`, `value`)
			 values ((SELECT LAST_INSERT_ID()), '<p>Dear {{customer_first_name}},</p><p>&nbsp;</p><p>New answer added to your question. Details:</p><p>Store: {{store_view_name}}</p><p>Product: {{product_name}}</p><p>Your question: {{question}}</p><p>Answer: {{answer}}</p><p>&nbsp;</p><p>Please find more information in My Questions/Answers section of your Profile.</p><p>&nbsp;</p><p>Do not reply on this email as it has been generated automatically.</p>');

	REPLACE INTO {$this->getTable('itoris_productqa_settings')} (`scope`, `scope_id`, `key`, `value`, `type`) VALUES
			('default', 0, 'admin_email', 0, 'text');

	REPLACE INTO {$this->getTable('itoris_productqa_settings_text')} (`setting_id`, `value`)
			 values ((SELECT LAST_INSERT_ID()), '0');

");

$this->endSetup();
?>
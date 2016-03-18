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
 * @copyright  Copyright (c) 2013 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

$this->run("

alter table {$this->getTable('itoris_productqa_questions_ratings')} add `guest_ip` varchar(19) null;
alter table {$this->getTable('itoris_productqa_questions_ratings')} add unique question_rating_unique (`q_id`,`customer_id`,`guest_ip`);
alter table {$this->getTable('itoris_productqa_questions_ratings')} drop index q_id;

alter table {$this->getTable('itoris_productqa_answers_ratings')} add `guest_ip` varchar(19) null;
alter table {$this->getTable('itoris_productqa_answers_ratings')} add unique answer_rating_unique (`a_id`,`customer_id`,`guest_ip`);
alter table {$this->getTable('itoris_productqa_answers_ratings')} drop index a_id;

create table {$this->getTable('itoris_productqa_question_subscriber')} (
	`subscriber_id` int unsigned not null auto_increment primary key,
	`question_id` int unsigned not null,
	`email` varchar(255) null,
	`customer_id` int(10) unsigned null,
	`store_id` smallint(5) unsigned not null,
	foreign key (`question_id`) references {$this->getTable('itoris_productqa_questions')} (`id`) on delete cascade on update cascade,
	foreign key (`customer_id`) references {$this->getTable('customer_entity')} (`entity_id`) on delete cascade on update cascade,
	foreign key (`store_id`) references {$this->getTable('core_store')} (`store_id`) on delete cascade on update cascade
) ENGINE = InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

");
?>

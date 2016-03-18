<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('twlogin_customer'), 'instagram_id', 'text default NULL');

$installer->endSetup(); 
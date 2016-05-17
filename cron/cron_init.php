<?php

/**

 * Prepare cron envinronment

 */

	define('BASE_PATH', $_SERVER["DOCUMENT_ROOT"].'/');//'/home/stripedsocks/beta/');
    echo date("c")." - Cron system init, base path set to: " . BASE_PATH . "\n";
    require_once(BASE_PATH."lib/Teamdesk/engine_config.php");
    require_once(BASE_PATH."lib/Teamdesk/engine_mysql.php");
    require_once(BASE_PATH."lib/Teamdesk/engine_teamdesk_api.php");  
    require_once(BASE_PATH."lib/Teamdesk/engine_teamdesk_dataset.php");
    require_once(BASE_PATH."lib/Teamdesk/engine_teamdesk_common.php"); 
    
    //init db
    $db = new DB();
    $db->init(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    //get settings
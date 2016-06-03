<?php

	define('DB_PREFIX', '');
    ini_set('display_errors',0);
    define('TD_LIVE_FLAG',0);
    define('TD_DOMAIN','clownantics.teamdesk.net');
    define('TD_DB_ID','27503');
    define('TD_USERNAME', 'blake@clownantics.com');    
    define('TD_PASSWORD', 'julian202');    
    define('TD_UPDATE_MAIL_ID','dhiraj@clownantics.com');
    
    if($_SERVER['HTTP_HOST'] == 'amazon.flagsrus.org') {
        define('DB_HOST', 'localhost');
        define('DB_USER', 'flagsrusnew');
        define('DB_PASSWORD', 'harddisk');
        define('DB_NAME', 'flagsrus');
    }     
    else {
        define('DB_HOST', 'localhost');
        define('DB_USER', 'stripeds_user');
        define('DB_PASSWORD', 'S@t(d#S*K-S');
        define('DB_NAME', 'stripeds_beta');
    } 
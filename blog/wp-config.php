<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
//define('WP_CACHE', true); //Added by WP-Cache Manager
//define( 'WPCACHEHOME', '/home/flagsrus/public_html/blog/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('DB_NAME', 'stripeds_beta');


/** MySQL database username */
define('DB_USER', 'stripeds_user');


/** MySQL database password */
define('DB_PASSWORD', 'S@t(d#S*K-S');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'su@aN7 Fv)lZ|>ciZ>fw)Z:$yA2WfpGp5f$`9lTI!  =kRZ|Z2KAv@``R9A!=|cM');
define('SECURE_AUTH_KEY',  '32:ayD#mx1WF5ziYy[Hm-UX-N(qz]@bwjfl;)uuq&V-+~sy:7 DkdpLEY-+V% !]');
define('LOGGED_IN_KEY',    'u$Dak [akl~Nf-+>L `q+>LAEE_B`9+MF~~<4E5A56AFr,+w+M?^|P.*ayup|)yA');
define('NONCE_KEY',        ';jds]7F{w,|6GP(L52HT!DLZ3:Q4.N >9OQVeT{Vy}e A;RYfcL5lF63MH]lcl%?');
define('AUTH_SALT',        ' }uDuhvEf@b- hS/{+v=Wa+$z0*C}~I8eunO#->g1/4x=i[vJ:JQ}$M|e>0.g?-N');
define('SECURE_AUTH_SALT', 'B-Mygl;fdB<JvjOU)|-L`:UWJ>vO,pmraDH m#h[Y 5$mQ$YVjUi=J/@ |,~QAi+');
define('LOGGED_IN_SALT',   '7=o`$z/OUqnaNg>Z/T[HP%yv9wilVHW&r&-*0wq|5#OPR1qy-)15:p@it86;]aVM');
define('NONCE_SALT',       'X-6UIAZOb;iu;H+_GLJ1tXve<ZJkN)t-Svo [BV|^q@EjK~<mYE[t(}vtC~5;)tm');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'stripeds_betablog');


/** MySQL database username */
define('DB_USER', 'stripeds_user');


/** MySQL database password */
define('DB_PASSWORD', 'S@t(d#S*K-S');


/** MySQL hostname */
define('DB_HOST', 'localhost');


/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');


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
define('AUTH_KEY',         '0p>NJWCqIr9IN R2$]G)>,[,xtZL*6?^W>l]?+~^zK@| /7J5F^uBF-}KowH>Jk_');

define('SECURE_AUTH_KEY',  'P)?YeNr]i$b<3]NS9D|DV(M*%8a:~6]M}+ZZu}zpd:v=j@_AMa9p0gvihx?jJ;Es');

define('LOGGED_IN_KEY',    'yj4Xu]X:.htO2H}<U6;_RthC1K-;Dy=>$F{GnOpjC_x1T++??.=8.O~,m(}PU`b^');

define('NONCE_KEY',        '5+FJ:Kb5?C?<HT,kh+(n9a8ThYRqU7VsN2shW90PTm6/)/h9OHp-rsCXn-g,?T--');

define('AUTH_SALT',        '[y%WMOJQ*H`Z3W|w9bG{|x1loauN+NcM=s d` ,1zdflsO[SQQZ$tyr%Y6x O<XQ');

define('SECURE_AUTH_SALT', ';`LeJ(kp>xcX<aEV&TmKtWfym}WW}fH[(eZv+{|O+kqBs}H9Y`5Z^gc>0xyEydaV');

define('LOGGED_IN_SALT',   'p*-KL 0GRojo;as%48!Qqo ^%,[6D|W<.g;jK$9|R9/J,OAwz/sZ<Qt@^-7);|jK');

define('NONCE_SALT',       ']W9M|oP=f/[fv}XiR:y9M,x6&iTU^9H5]q0rCeF$J7J8{LckiVF]X,}0;X>1F>I;');


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';


/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

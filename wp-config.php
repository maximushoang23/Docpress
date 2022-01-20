<?php

/** Enable W3 Total Cache */

define( 'WP_CACHE', true ); // Added by W3 Total Cache

define('FS_METHOD','direct'); 


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
define( 'DB_NAME', "wp_docpresss" );

/** MySQL database username */
define( 'DB_USER', "root" );

/** MySQL database password */
define( 'DB_PASSWORD', "" );

/** MySQL hostname */
define( 'DB_HOST', "localhost" );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'kUxI!ss8gUEyOL^wZFDo%}s@DSBD_[4&8bu8)DX]?R}hCsv~Sp1kWhoTciwqY<h|' );
define( 'SECURE_AUTH_KEY',   '=PMM)-aCB/N(u`9+^d=cb4Qu3S.JjJyxaY!gj<bI%]nWqn>?Lt-eqZp5l=s5Pnu=' );
define( 'LOGGED_IN_KEY',     '+?dC_/@xL5iX(OiHy%hqI<qbEzF?Ae+gv^~mNk)Xu]P0>sNc@)2X0,&-Mb|]kk5e' );
define( 'NONCE_KEY',         'V bw%B tBj7w}KxPh!#]Anmxbtzbo#v 8r<D>WjoP[49!.6`*oj;s!8a|*^HZ5`?' );
define( 'AUTH_SALT',         '=5rP26KfpY J|>{GiiFp_uj.B>hlTH_o&Vh9^eF9m(@CGckZ#L [_R*(@>Ln8_[#' );
define( 'SECURE_AUTH_SALT',  ']1G yu6.Vh$==L`s:p*j|%Yc@9)rBPv:X+O5hh97oo>a w/Sz]@=Z`_;{4S1]wf{' );
define( 'LOGGED_IN_SALT',    ']xrN}41P#w>Wxr*A_slbdW6Xo4Z&XipIJfw1@]Y-rrEL9!E%@ -U/D^*QA60ma:m' );
define( 'NONCE_SALT',        'nC EU8cv]1XTeDr+&2Oy@R[26${KP:}7]S2`9A$r#U4I~LZcb~K,9SXa~6tX6Veu' );
define( 'WP_CACHE_KEY_SALT', 'HCiYF} QDm/!]l#Bu5sy&6H`$By{72pzp%%9SLB,00N[nHRZ+1~8F=,SD&A wIzh' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


if ( isset( $_SERVER["HTTP_X_FORWARDED_PROTO"] ) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https" ) {
	$_SERVER["HTTPS"] = "on";
}

// Enable WP_DEBUG mode.
define( "WP_DEBUG", false );

// Enable Debug logging to the /wp-content/debug.log file
define( "WP_DEBUG_LOG", false );

// Disable display of errors and warnings.
define( "WP_DEBUG_DISPLAY", false );
@ini_set( "display_errors", 0 );

// Use dev versions of core JS and CSS files (only needed if you are modifying these core files)
define( "SCRIPT_DEBUG", false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

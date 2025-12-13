<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
define('DB_NAME', 'wordpress');
define('DB_USER', 'wordpress');
define('DB_PASSWORD', 'wordpress123');
define('DB_HOST', 'db');

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '5QT7-k{phSlsLNys)!a#2I*`Zc2M3XTpDQ(^v+6xIFSl4RgcKQ.hC{^pO[+qK+/f');
define('SECURE_AUTH_KEY',  'Z<%5|bYQW}<f<|o0JIl jl*HW4Sh~MLdVx#Y*)>CKYFr*MjH3@osY-?e|p9+mgOi');
define('LOGGED_IN_KEY',    'Mvbv ~eFZjbC^vZ@p0,S|m6Ja~x6Fk12<hzf~tB3/M-6Y-ib:-WH/pnSdHK?yPqU');
define('NONCE_KEY',        'PS-?PVPbR@W]jYkK ->B?GD+rlhIL>+3i-ASTCzT5eOZl[3-2d`Ur?r5=E|Z4zFY');
define('AUTH_SALT',        '#gf.?x4)MvHDV(mu((A?,J^Lwt6Vr+q{L&7Es7]qK5Pu)GEU?4GJ!+~+r4ps;A#A');
define('SECURE_AUTH_SALT', 'r}@XQ}5Z#m[|H{+@8$?pN&}0#i_>9H_e@<Z>!N!0N7z<-Mq%vXvT(*It#,zUs8z1');
define('LOGGED_IN_SALT',   '&BRP8{Fu=@>SD#(3]|PR/+q/~/s!uvn4nmG5l>jqCz`xeN,ufIcdg3gzE 4TSr98');
define('NONCE_SALT',       '}F`/Ys%IpYm[/;<}2]{+i1|GcYTTp)hDxT>yh2.c4c(m7rs;t49]3*-F.OX1$su&');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_8dls_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
  define('ABSPATH', __DIR__ . '/wp/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

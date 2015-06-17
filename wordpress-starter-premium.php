<?php
/**
 * Plugin Name: WordPress Starter Premium
 * Plugin URI: http://store.axelerant.com/downloads/wordpress-starter-premium/
 * Description: TBD
 * Version: 1.0.0
 * Author: Axelerant
 * Author URI: https://axelerant.com
 * License: GPLv2 or later
 * Text Domain: wordpress-starter-premium
 * Domain Path: /languages
 */


/**
WordPress Starter Premium
Copyright (C) 2015 Axelerant

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPSP_BASE', plugin_basename( __FILE__ ) );
define( 'WPSP_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPSP_DIR_INC', WPSP_DIR . 'includes/' );
define( 'WPSP_DIR_LIB', WPSP_DIR_INC . 'libraries/' );
define( 'WPSP_NAME', 'WordPress Starter Premium' );
define( 'WPSP_PRODUCT_ID', 'TBD' );
define( 'WPSP_REQ_BASE', 'wordpress-starter/wordpress-starter.php' );
define( 'WPSP_REQ_NAME', 'WordPress Starter' );
define( 'WPSP_VERSION', '1.0.0' );

require_once WPSP_DIR_LIB . WPSP_REQ_BASE;
require_once WPSP_DIR_INC . 'requirements.php';

if ( ! wpsp_requirements_check() ) {
	return false;
}

require_once WPSP_DIR_INC . 'class-wordpress-starter-premium.php';


add_action( 'plugins_loaded', 'wordpress_starter_premium_init', 99 );


/**
 *
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
function wordpress_starter_premium_init() {
	if ( ! is_admin() ) {
		return;
	}

	global $WPSP_Licensing;
	if ( is_null( $WPSP_Licensing ) ) {
		$WPSP_Licensing = new WordPress_Starter_Premium_Licensing();
	}

	if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		require_once WPSP_DIR_LIB . 'EDD_SL_Plugin_Updater.php';
	}

	$WPSP_Updater = new EDD_SL_Plugin_Updater(
		$WPSP_Licensing->store_url,
		__FILE__,
		array(
			'version' => WPSP_VERSION,
			'license' => $WPSP_Licensing->get_license(),
			'item_name' => WPSP_NAME,
			'author' => $WPSP_Licensing->author,
		)
	);

	if ( WordPress_Starter_Premium::version_check() ) {
		global $WordPress_Starter_Premium;
		if ( is_null( $WordPress_Starter_Premium ) ) {
			$WordPress_Starter_Premium = new WordPress_Starter_Premium();
		}

		do_action( 'wpsp_init' );
	}
}


register_activation_hook( __FILE__, array( 'WordPress_Starter_Premium', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'WordPress_Starter_Premium', 'deactivation' ) );
register_uninstall_hook( __FILE__, array( 'WordPress_Starter_Premium', 'uninstall' ) );

?>

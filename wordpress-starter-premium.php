<?php
/**
 * Plugin Name: WordPress Starter Premium
 * Plugin URI: http://aihr.us/products/wordpress-starter-premium/
 * Description: TBD
 * Version: 1.0.0
 * Author: Michael Cannon
 * Author URI: http://aihr.us/resume/
 * License: GPLv2 or later
 * Text Domain: wordpress-starter-premium
 * Domain Path: /languages
 */


/**
 * Copyright 2013 Michael Cannon (email: mc@aihr.us)
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

define( 'WPSP_AIHR_VERSION', '1.0.1' );
define( 'WPSP_BASE', plugin_basename( __FILE__ ) );
define( 'WPSP_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPSP_DIR_INC', WPSP_DIR . 'includes/' );
define( 'WPSP_DIR_LIB', WPSP_DIR_INC . 'libraries/' );
define( 'WPSP_NAME', 'WordPress Starter Premium' );
define( 'WPSP_REQ_BASE', 'wordpress-starter/wordpress-starter.php' );
define( 'WPSP_REQ_NAME', 'WordPress Starter by Aihrus' );
define( 'WPSP_REQ_SLUG', 'wordpress-starter' );
define( 'WPSP_REQ_VERSION', '1.0.0' );
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
	if ( ! is_admin() )
		return;

	global $WPSP_Licensing;
	if ( is_null( $WPSP_Licensing ) )
		$WPSP_Licensing = new WordPress_Starter_Premium_Licensing();

	if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) )
		require_once WPSP_DIR_LIB . 'EDD_SL_Plugin_Updater.php';

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
		if ( is_null( $WordPress_Starter_Premium ) )
			$WordPress_Starter_Premium = new WordPress_Starter_Premium();

		do_action( 'wpsp_init' );
	}
}


register_activation_hook( __FILE__, array( 'WordPress_Starter_Premium', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'WordPress_Starter_Premium', 'deactivation' ) );
register_uninstall_hook( __FILE__, array( 'WordPress_Starter_Premium', 'uninstall' ) );

?>

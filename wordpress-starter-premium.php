<?php
/**
 * Plugin Name: WordPress Starter Premium
 * Plugin URI: http://aihr.us/products/wordpress-starter-premium/
 * Description: TBD
 * Version: 0.0.1
 * Author: Michael Cannon
 * Author URI: http://aihr.us/resume/
 * License: GPLv2 or later
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

if ( ! defined( 'WPSP_PLUGIN_DIR' ) )
	define( 'WPSP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'WPSP_PLUGIN_DIR_LIB' ) )
	define( 'WPSP_PLUGIN_DIR_LIB', WPSP_PLUGIN_DIR . '/lib' );

require_once WPSP_PLUGIN_DIR_LIB . '/aihrus/class-aihrus-common.php';


class WordPress_Starter_Premium extends Aihrus_Common {
	const FREE_PLUGIN_BASE = 'wordpress-starter/wordpress-starter.php';
	const FREE_VERSION     = '0.0.1';
	const ID               = 'wordpress-starter-premium';
	const ITEM_NAME        = 'WordPress Starter Premium';
	const PLUGIN_BASE      = 'wordpress-starter-premium/wordpress-starter-premium.php';
	const SLUG             = 'wpsp_';
	const VERSION          = '0.0.1';

	public static $class = __CLASS__;
	public static $notice_key;


	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_shortcode( 'wordpress_starter_premium_shortcode', array( $this, 'wordpress_starter_premium_shortcode' ) );

		if ( WordPress_Starter::do_load() ) {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			self::load_options();
		}
	}


	public function admin_init() {
		if ( ! self::version_check() )
			return;

		global $WPSP_Licensing;
		if ( ! $WPSP_Licensing->valid_license() ) {
			self::set_notice( 'notice_license', DAY_IN_SECONDS );
			self::check_notices();
		}

		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
	}


	public function admin_menu() {
		add_action( 'admin_print_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_print_styles', array( $this, 'styles' ) );
	}


	public function init() {
		load_plugin_textdomain( self::ID, false, 'wordpress-starter-premium/languages' );

		add_action( 'wps_scripts', array( $this, 'scripts' ) );
		add_action( 'wps_styles', array( $this, 'styles' ) );
	}


	public function plugin_action_links( $links, $file ) {
		if ( self::PLUGIN_BASE == $file )
			array_unshift( $links, WordPress_Starter::$settings_link );

		return $links;
	}


	public function activation() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		if ( ! is_plugin_active( WordPress_Starter_Premium::FREE_PLUGIN_BASE ) ) {
			deactivate_plugins( WordPress_Starter_Premium::PLUGIN_BASE );
			add_action( 'admin_notices', array( 'WordPress_Starter_Premium', 'notice_version' ) );
			return;
		}
	}


	public function deactivation() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		WordPress_Starter_Premium::delete_notices();
	}


	public function uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		$WPSP_Licensing = new WordPress_Starter_Premium_Licensing();
		$WPSP_Licensing->deactivate_license();
	}


	public function notice_0_0_1() {
		$text = sprintf( __( 'If your WordPress Starter Premium display has gone to funky town, please <a href="%s">read the FAQ</a> about possible CSS fixes.' ), 'https://aihrus.zendesk.com/entries/23722573-Major-Changes-Since-2-10-0' );

		parent::notice_updated( $text );
	}


	public static function scripts( $atts ) {
		do_action( 'wpsp_scripts' );
	}


	public static function styles() {
		if ( ! is_admin() ) {
			wp_register_style( __CLASS__, plugins_url( 'wordpress-starter-premium.css', __FILE__ ) );
			wp_enqueue_style( __CLASS__ );
		}

		do_action( 'wpsp_styles' );
	}


	public static function wordpress_starter_premium_shortcode( $atts ) {
		WordPress_Starter::call_scripts_styles( $atts );

		return __CLASS__ . ' shortcode';
	}


	public static function version_check() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$good_version = true;
		if ( ! is_plugin_active( self::PLUGIN_BASE ) )
			$good_version = false;

		return $good_version;
	}


	public function load_options() {
		add_filter( 'wps_sections', array( $this, 'sections' ) );
		add_filter( 'wps_settings', array( $this, 'settings' ) );
	}


	public function sections( $sections ) {
		$sections[ 'premium' ] = esc_html__( 'Premium' );

		return $sections;
	}


	public function settings( $settings ) {
		$settings['disable_donate'] = array(
			'section' => 'premium',
			'title' => esc_html__( 'Disable Donate Text?' ),
			'desc' => esc_html__( 'Remove "If you like…" text with the donate and premium purchase links from the settings screen.' ),
			'type' => 'checkbox',
		);

		return $settings;
	}


	public static function notice_license( $post_type = null, $settings_id = null, $free_name = null, $purchase_url = null, $item_name = null ) {
		$post_type    = null;
	   	$settings_id  = WordPress_Starter_Settings::ID;
	   	$free_name    = 'WordPress Starter';
	   	$purchase_url = 'http://aihr.us/products/wordpress-starter-premium-wordpress-plugin/';
	   	$item_name    = self::ITEM_NAME;

		parent::notice_license( $post_type, $settings_id, $free_name, $purchase_url, $item_name );
	}


}


register_activation_hook( __FILE__, array( 'WordPress_Starter_Premium', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'WordPress_Starter_Premium', 'deactivation' ) );
register_uninstall_hook( __FILE__, array( 'WordPress_Starter_Premium', 'uninstall' ) );


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

	require_once WPS_PLUGIN_DIR_LIB . '/class-wordpress-starter-settings.php';
	require_once WPSP_PLUGIN_DIR_LIB . '/class-wordpress-starter-premium-licensing.php';

	global $WPSP_Licensing;
	if ( is_null( $WPSP_Licensing ) )
		$WPSP_Licensing = new WordPress_Starter_Premium_Licensing();

	if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) )
		require_once WPSP_PLUGIN_DIR_LIB . '/EDD_SL_Plugin_Updater.php';

	$WPSP_Updater = new EDD_SL_Plugin_Updater(
		$WPSP_Licensing->store_url,
		__FILE__,
		array(
			'version' => WordPress_Starter_Premium::VERSION,
			'license' => $WPSP_Licensing->get_license(),
			'item_name' => WordPress_Starter_Premium::ITEM_NAME,
			'author' => $WPSP_Licensing->author,
		)
	);

	if ( WordPress_Starter_Premium::version_check() ) {
		global $WordPress_Starter_Premium;
		if ( is_null( $WordPress_Starter_Premium ) )
			$WordPress_Starter_Premium = new WordPress_Starter_Premium();
	}
}


?>

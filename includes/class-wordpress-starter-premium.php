<?php
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

require_once WPSP_DIR_LIB . 'aihrus-framework/class-aihrus-common.php';
require_once WPSP_DIR_INC . 'class-wordpress-starter-premium-licensing.php';

if ( class_exists( 'WordPress_Starter_Premium' ) )
	return;


class WordPress_Starter_Premium extends Aihrus_Common {
	const BASE    = WPSP_BASE;
	const ID      = 'wordpress-starter-premium';
	const SLUG    = 'wpsp_';
	const VERSION = WPSP_VERSION;

	public static $class = __CLASS__;
	public static $notice_key;
	public static $plugin_assets;


	public function __construct() {
		parent::__construct();

		self::$plugin_assets = plugins_url( '/assets/', dirname( __FILE__ ) );
		self::$plugin_assets = self::strip_protocol( self::$plugin_assets );

		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		add_action( 'init', array( __CLASS__, 'init' ) );
		add_shortcode( 'wordpress_starter_premium_shortcode', array( __CLASS__, 'wordpress_starter_premium_shortcode' ) );

		if ( ! WordPress_Starter::do_load() )
			return;

		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.LongVariable)
	 */
	public static function admin_init() {
		if ( ! self::version_check() )
			return;

		global $WPSP_Licensing;
		if ( ! $WPSP_Licensing->valid_license() ) {
			$need_license = true;
			$license_key  = wps_get_option( $WPSP_Licensing->license_key() );
			if ( $WPSP_Licensing->valid_hash( $license_key ) ) {
				$success = wpsp_update_license( $license_key );
				if ( $WPSP_Licensing->valid_hash( $success ) )
					$need_license = false;
			}

			if ( $need_license )
				self::set_notice( 'notice_license', DAY_IN_SECONDS );
		}

		add_filter( 'plugin_action_links', array( __CLASS__, 'plugin_action_links' ), 10, 2 );
	}


	public static function admin_menu() {
		add_action( 'admin_print_scripts', array( __CLASS__, 'scripts' ) );
		add_action( 'admin_print_styles', array( __CLASS__, 'styles' ) );
	}


	public static function init() {
		load_plugin_textdomain( self::ID, false, 'wordpress-starter-premium/languages' );

		if ( ! WordPress_Starter::do_load() )
			return;

		self::load_options();

		add_action( 'wps_scripts', array( __CLASS__, 'scripts' ) );
		add_action( 'wps_styles', array( __CLASS__, 'styles' ) );
	}


	public static function plugin_action_links( $links, $file ) {
		if ( self::BASE == $file )
			array_unshift( $links, WordPress_Starter::$settings_link );

		return $links;
	}


	public static function activation() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
	}


	public static function deactivation() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.LongVariable)
	 */
	public static function uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
		
		require_once WPSP_DIR_INC . 'class-wordpress-starter-premium-licensing.php';

		$WPSP_Licensing = new WordPress_Starter_Premium_Licensing();
		$WPSP_Licensing->deactivate_license();
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function scripts( $atts ) {
		do_action( 'wpsp_scripts' );
	}


	public static function styles() {
		if ( ! is_admin() ) {
			wp_register_style( __CLASS__, self::$plugin_assets . 'css/wordpress-starter-premium.css' );
			wp_enqueue_style( __CLASS__ );
		}

		do_action( 'wpsp_styles' );
	}


	public static function wordpress_starter_premium_shortcode( $atts ) {
		WordPress_Starter::call_scripts_styles( $atts );

		return __CLASS__ . ' shortcode';
	}


	public static function version_check() {
		$valid_version = true;

		$valid_base = true;
		if ( ! is_plugin_active( WPSP_REQ_BASE ) ) {
			$valid_base = false;
		} elseif ( ! defined( 'WPS_VERSION' ) ) {
			$valid_base = false;
		} elseif ( ! version_compare( WPS_VERSION, WPSP_REQ_VERSION, '>=' ) ) {
			$valid_base = false;
		}

		if ( ! $valid_base ) {
			$valid_version = false;
			self::set_notice( 'wpsp_notice_version' );
		}

		if ( ! $valid_version ) {
			deactivate_plugins( self::BASE );
			self::check_notices();
		}

		return $valid_version;
	}


	public static function load_options() {
		add_filter( 'wps_sections', array( __CLASS__, 'sections' ) );
		add_filter( 'wps_settings', array( __CLASS__, 'settings' ) );
	}


	public static function sections( $sections ) {
		$sections[ 'premium' ] = esc_html__( 'Premium' );

		return $sections;
	}


	public static function settings( $settings ) {
		$settings['disable_donate'] = array(
			'section' => 'premium',
			'title' => esc_html__( 'Disable Donate Text?' ),
			'desc' => esc_html__( 'Remove "If you like…" text with the donate and premium purchase links from the settings screen.' ),
			'type' => 'checkbox',
		);

		return $settings;
	}


	public static function notice_license() {
		$post_type     = null;
		$settings_id   = WordPress_Starter_Settings::ID;
		$required_name = WPSP_REQ_NAME;
		$purchase_url  = 'http://aihr.us/products/wordpress-starter-premium-wordpress-plugin/';
		$item_name     = WPSP_NAME;

		aihr_notice_license( $post_type, $settings_id, $required_name, $purchase_url, $item_name );
	}
}

?>
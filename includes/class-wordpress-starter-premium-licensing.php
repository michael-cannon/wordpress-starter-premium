<?php
/**
Aihrus WordPress Starter Premium
Copyright (C) 2014  Michael Cannon

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

require_once AIHR_DIR_INC . 'class-aihrus-licensing.php';

if ( class_exists( 'WordPress_Starter_Premium_Licensing' ) )
	return;


class WordPress_Starter_Premium_Licensing extends Aihrus_Licensing{
	public static $settings_id = WordPress_Starter_Settings::ID;


	public function __construct() {
		parent::__construct( WordPress_Starter_Premium::SLUG, WPSP_NAME );

		add_filter( 'wps_settings', array( $this, 'settings' ), 5 );
	}


	public function settings( $settings ) {
		$title = esc_html__( 'License Key for %1$s' );

		$settings[ WordPress_Starter_Premium::SLUG . 'license_key' ] = array(
			'section' => 'premium',
			'title' => esc_html__( 'License Key' ),
			'title' => sprintf( $title, WPSP_NAME ),
			'desc' => esc_html__( 'Required to enable premium plugin updating. Activation is automatic. Use `0` to deactivate.' ),
			'validate' => 'wpsp_update_license',
			'widget' => 0,
		);

		return $settings;
	}


}


/**
 *
 *
 */
/**
 *
 *
 * @SuppressWarnings(PHPMD.Superglobals)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
function wpsp_update_license( $license ) {
	global $WPSP_Licensing;

	if ( ! empty( $_REQUEST['option_page'] ) && WordPress_Starter_Settings::ID == $_REQUEST['option_page'] ) {
		$current_license = $WPSP_Licensing->get_license();
		$valid_license   = $WPSP_Licensing->valid_license();
		if ( ! $valid_license || $license != $current_license ) {
			$result        = $WPSP_Licensing->update_license( $license );
			$valid_license = $WPSP_Licensing->valid_license();
			if ( ! $valid_license ) {
				WordPress_Starter_Premium::set_notice( 'notice_license', HOUR_IN_SECONDS );
			}
		}

		return $result;
	}

	return $license;
}


?>

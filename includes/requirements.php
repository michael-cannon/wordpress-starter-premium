<?php
/**
WordPress Starter Premium
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

require_once AIHR_DIR . 'aihrus-framework.php';


function wpsp_requirements_check( $force_check = false ) {
	if ( is_plugin_active( WPSP_REQ_BASE ) ) {
		aihr_deactivate_plugin( WPSP_REQ_BASE );
		add_action( 'admin_notices', 'wpsp_notice_wps_deactivated' );
	}

	$check_okay = get_transient( 'wpsp_requirements_check' );
	if ( empty( $force_check ) && $check_okay !== false ) {
		return $check_okay;
	}

	$deactivate_reason = false;
	if ( ! aihr_check_php( WPSP_BASE, WPSP_NAME ) ) {
		$deactivate_reason = esc_html__( 'Old PHP version detected' );
	}

	if ( ! aihr_check_wp( WPSP_BASE, WPSP_NAME ) ) {
		$deactivate_reason = esc_html__( 'Old WordPress version detected' );
	}

	global $wps_activated;

	if ( empty( $wps_activated ) ) {
		$deactivate_reason = esc_html__( 'Internal WordPress Starter not detected' );
	}

	if ( ! empty( $deactivate_reason ) ) {
		aihr_deactivate_plugin( WPSP_BASE, WPSP_NAME, $deactivate_reason );
	}
	
	$check_okay = empty( $deactivate_reason );
	if ( $check_okay ) {
		delete_transient( 'wpsp_requirements_check' );
		set_transient( 'wpsp_requirements_check', $check_okay, HOUR_IN_SECONDS );
	}

	return $check_okay;
}


function wpsp_notice_aihrus() {
	$help_url  = esc_url( 'https://aihrus.zendesk.com/entries/35689458' );
	$help_link = sprintf( __( '<a href="%1$s">Update plugins</a>. <a href="%2$s">More information</a>.' ), self_admin_url( 'update-core.php' ), $help_url );

	$text = sprintf( esc_html__( 'Plugin "%1$s" has been deactivated as it requires a current Aihrus Framework. Once corrected, "%1$s" can be activated. %2$s' ), WPSP_NAME, $help_link );

	aihr_notice_error( $text );
}


function wpsp_notice_wps_deactivated() {
	$text = sprintf( esc_html__( 'Plugin "%1$s" has been deactivated as it is no longer required by "%2$s". You can safely delete plugin "%1$s" given that "Remove Plugin Data on Deletion?" isn\'t checked on the Reset tab of Settings.' ), WPSP_REQ_NAME, WPSP_NAME );

	aihr_notice_error( $text );
}

?>

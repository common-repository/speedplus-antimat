<?php
/**
 * SpeedPlus AntiMat
 * Uninstall and remove database options
 */
 
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

global $wpdb;

require_once ABSPATH . '/wp-admin/includes/plugin.php';


$plugin_way = 'speedplus_antimat/speedplus_antimat.php';

if ( is_plugin_active( $plugin_way ) ) {
	if ( is_multisite() && is_plugin_active_for_network( $plugin_way ) ) {
		deactivate_plugins( $plugin_way, false, true );
	} else {
		deactivate_plugins( $plugin_way );
	}
}

delete_plugins( array( $plugin_way ) );

function speedplus_antimat_uninstall() {
	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name = 'speedplus_antimat_plugin_options';" );
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'speedplus_antimat_%';" );

}

if ( is_multisite() ) {
	global $wpdb, $wp_version;

	$wpdb->query( "DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE 'speedplus_antimat_%';" );

	$blogs = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

	if ( ! empty( $blogs ) ) {
		foreach ( $blogs as $id ) {

			switch_to_blog( $id );

			speedplus_antimat_uninstall();

			restore_current_blog();
		}
	}
} else {
	speedplus_antimat_uninstall();
}
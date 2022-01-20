<?php

/**
 * Admin functions
 *
 * @package   Thim_Core
 * @since     0.1.0
 */

/**
 * Clean all keys which is a number, e.g: Array( [0] => ..., ..., [69] => ...);
 *
 * @since 0.4.0
 *
 * @param $theme_mods
 *
 * @return mixed
 */
function thim_clean_theme_mods( $theme_mods ) {
	// Gets mods keys
	$mod_keys = array_keys( $theme_mods );
	foreach ( $mod_keys as $mod_key ) {
		// Removes from array if the key is a number
		if ( is_numeric( $mod_key ) ) {
			unset( $theme_mods[ $mod_key ] );
		}
	}

	return $theme_mods;
}

/**
 * Add log.
 *
 * @since 0.8.3
 *
 * @param $message
 * @param string $handle
 * @param bool $clear
 */
function thim_add_log( $message, $handle = 'log', $clear = false ) {
	if ( ! TP::is_debug() ) {
		return;
	}

	if ( version_compare( phpversion(), '5.6', '<' ) ) {
		return;
	}

	$thim_log = Thim_Logger::instance();
	@$thim_log->add( $message, $handle, $clear );
}

/**
 * Set current demo has installed of a theme.
 * Default is current installed theme
 *
 * @since 0.4.0
 *
 * @param        $demo
 * @param string $theme
 */
function thim_set_current_demo( $demo, $theme = '' ) {
	if ( ! $theme ) {
		$theme = get_option( 'stylesheet' );
	}
	update_option( 'thim_current_demo', array( 'slug' => $theme, 'demo' => $demo ) );
}

/**
 * Get current demo has installed of a theme.
 * Default is current installed theme
 *
 * @since 0.4.0
 *
 * @param string $theme
 *
 * @return mixed
 */
function thim_get_current_demo( $theme = '' ) {
	if ( ! $theme ) {
		$theme = get_option( 'stylesheet' );
	}
	$demo = get_option( 'thim_current_demo' );
	if ( $demo && $demo['slug'] == $theme ) {
		return $demo['demo'];
	}

	return false;
}

/*
 * Skip export object's meta data if it's _thim_demo_content
 */
add_filter( 'wxr_export_skip_postmeta', '_thim_export_skip_object_meta', 1000, 2 );
add_filter( 'wxr_export_skip_commentmeta', '_thim_export_skip_object_meta', 1000, 2 );
add_filter( 'wxr_export_skip_termmeta', '_thim_export_skip_object_meta', 1000, 3 );

function _thim_export_skip_object_meta( $return_me, $meta_key, $meta_value = false ) {
	if ( '_thim_demo_content' == $meta_key ) {
		$return_me = true;
	}

	return $return_me;
}
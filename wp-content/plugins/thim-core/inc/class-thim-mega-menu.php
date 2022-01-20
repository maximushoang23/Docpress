<?php

/**
 * Created by PhpStorm.
 * User: khoapq
 * Date: 8/24/2016
 * Time: 10:02 AM
 */


/**
 * Class Thim_Mega_Menu
 *
 * @package Thim_Core
 * @since   0.3.1
 */
class Thim_Mega_Menu extends Thim_Singleton {
	/**
	 * Thim_Mega_Menu constructor.
	 *
	 * @since 0.3.1
	 */
	protected function __construct() {
		// Compatibility with Max Mega Menu
		if ( class_exists( 'Mega_Menu' ) ) {
			return;
		}

		$this->init();
	}

	/**
	 * Include Mega Menu
	 *
	 * @since 0.3.1
	 */
	private function init() {
		include_once THIM_CORE_INC_PATH . '/includes/megamenu/megamenu.php';
	}

	/**
	 * Determines if Mega Menu has been enabled for a given menu location.
	 *
	 * @since 0.3.1
	 *
	 * @param mixed $location - theme location identifier
	 *
	 * @return bool
	 */
	public static function menu_is_enabled( $location = false ) {

		if ( ! $location ) {
			return true; // the plugin is enabled
		}

		if ( ! has_nav_menu( $location ) ) {
			return false;
		}

		// if a location has been passed, check to see if MMM has been enabled for the location
		$settings = get_option( 'megamenu_settings' );

		return is_array( $settings ) && isset( $settings[ $location ]['enabled'] ) && $settings[ $location ]['enabled'] == true;
	}
}
<?php
/**
 * Plugin Name: Starter Theme Shortcodes
 * Plugin URI: http://thimpress.com
 * Description: Starter Theme Shortcodes for Visual Composer
 * Author: ThimPress
 * Author URI: http://thimpress.com
 * Version: 1.0.0
 * Text Domain: startertheme_shortcodes
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Thim_Plugin_Startertheme_Shortcodes' ) ) {

	// Depend on Visual Composer
	if ( ! class_exists( 'Vc_Manager' ) ) {
		return;
	}

	class Thim_Plugin_Startertheme_Shortcodes {

		/**
		 * @var null
		 *
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * Return unique instance.
		 *
		 * @since 1.0.0
		 */
		static function instance() {
			if ( ! self::$_instance ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * constructor.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {
			$this->init();
			$this->load_shortcodes();
			vc_add_shortcode_param( 'number', array( $this, 'param_number' ) );
		}


		/**
		 * Register shortcodes.
		 *
		 * @since 1.0.0
		 */
		public function load_shortcodes() {
			require_once( 'testimonials/testimonials.php' );
		}

		/**
		 * Load functions.
		 *
		 * @since 1.0.0
		 */
		public function init() {
			require_once( 'functions.php' );
		}

		/**
		 * Create custom param number
		 *
		 * @param $settings
		 * @param $value
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function param_number( $settings, $value ) {
			$param_name = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
			$type       = isset( $settings['type'] ) ? $settings['type'] : '';
			$min        = isset( $settings['min'] ) ? $settings['min'] : '';
			$max        = isset( $settings['max'] ) ? $settings['max'] : '';
			$suffix     = isset( $settings['suffix'] ) ? $settings['suffix'] : '';
			$class      = isset( $settings['class'] ) ? $settings['class'] : '';
			$output     = '<input type="number" min="' . $min . '" max="' . $max . '" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '" style="max-width:100px; margin-right: 10px;" />' . $suffix;

			return $output;
		}
	}

	Thim_Plugin_Startertheme_Shortcodes::instance();
}
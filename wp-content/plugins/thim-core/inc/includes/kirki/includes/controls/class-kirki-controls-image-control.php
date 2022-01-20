<?php
/**
 * Customizer Control: kirki-image.
 *
 * @package     Kirki
 * @subpackage  Controls
 * @copyright   Copyright (c) 2016, Aristeides Stathopoulos
 * @license     http://opensource.org/licenses/https://opensource.org/licenses/MIT
 * @since       1.0
 */

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Kirki_Controls_Image_Control' ) ) {

	/**
	 * Class Kirki_Controls_Image_Control
	 */
	class Kirki_Controls_Image_Control extends WP_Customize_Image_Control {
		public $type = 'kirki-image';
		public $raw_active_callback = '';
		public function __construct( WP_Customize_Manager $manager, $id, array $args ) {
			$this->raw_active_callback = !empty( $args['raw_active_callback'] ) ? $args['raw_active_callback'] : null;
			$args['raw_active_callback'] = !empty( $args['raw_active_callback'] ) ? $args['raw_active_callback'] : null;
			parent::__construct( $manager, $id, $args );
		}

		public function to_json() {
			parent::to_json();
			$this->json['active_callback'] = $this->raw_active_callback;
		}

		/**
		 * Enqueue control related scripts/styles.
		 *
		 * @access public
		 */
		public function enqueue() {
			wp_enqueue_script( 'kirki-image' );
		}
	}
}

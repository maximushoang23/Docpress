<?php
/**
 * Customizer Control: accordion.
 *
 * Creates a new custom control.
 *
 * @thim_created 27/07/2016
 * @package      Kirki
 * @subpackage   Controls
 * @copyright    Copyright (c) 2016, Aristeides Stathopoulos
 * @license      https://opensource.org/licenses/MIT
 * @since        1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Kirki_Controls_Accordion_Control' ) ) {

	/**
	 * The "custom" control allows you to add any raw HTML.
	 */
	class Kirki_Controls_Accordion_Control extends Kirki_Customize_Control {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'kirki-accordion';

		protected $fields = array();

		public function __construct( $manager, $id, $args = array() ) {
			parent::__construct( $manager, $id, $args );
		}

		/**
		 * Enqueue control related scripts/styles.
		 *
		 * @access public
		 */
		public function enqueue() {
			wp_enqueue_script( 'kirki-accordion' );
		}

		public function to_json() {
			parent::to_json();

			$array_id = array();
			foreach ( $this->fields as $field ) {
				$id = $field['id'];
				$id = '#customize-control-' . $id;

				array_push( $array_id, $id );
			}

			$this->json['fields'] = json_encode( $array_id );
		}

		/**
		 * An Underscore (JS) template for this control's content (but not its container).
		 *
		 * Class variables for this control class are available in the `data` JS object;
		 * export custom variables by overriding {@see Kirki_Customize_Control::to_json()}.
		 *
		 * @see    WP_Customize_Control::print_template()
		 *
		 * @access protected
		 */
		protected function content_template() {
			?>
			<div class="thim-customize-accordion" data-fields="{{ data.fields }}">
				<button class="accordion" type="button">{{ data.label }}</button>
			</div>
			<?php
		}
	}
}

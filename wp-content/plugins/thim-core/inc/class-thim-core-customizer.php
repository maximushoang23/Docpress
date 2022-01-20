<?php

/**
 * Class Thim_Core_Customizer
 *
 * @package   Thim_Core
 * @since     0.1.0
 */
class Thim_Core_Customizer extends Thim_Singleton {
	/**
	 * @var string
	 *
	 * @since 0.1.0
	 */
	public static $key_stylesheet_uri = 'thim_core_stylesheet';

	/**
	 * Thim_Integrate_Kirki constructor.
	 *
	 * @since 0.1.0
	 */
	protected function __construct() {
		$this->init();
		$this->init_hooks();
	}

	/**
	 * Init class.
	 *
	 * @since 0.1.0
	 */
	private function init() {
		$this->include_kirki();
	}

	/**
	 * Init hooks.
	 *
	 * @since 0.1.0
	 */
	private function init_hooks() {
		add_filter( 'kirki/config', array( $this, 'config' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_stylesheet_uri' ) );

		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'customize_save_after', array( $this, 'after_save_customize' ) );
		add_action( 'customize_save', array( $this, 'before_save_customize' ) );

		add_filter( 'customize_save_response', array( $this, 'customize_save_response' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_preview' ), 100 );

		add_action( 'wp_loaded', array( $this, 'customizer_register' ) );
	}

	/**
	 * Include Kirki.
	 *
	 * @since 0.1.0
	 */
	private function include_kirki() {
		if ( class_exists( 'Kirki' ) ) {
			return;
		}

		include_once THIM_CORE_INC_PATH . '/includes/kirki/kirki.php';
	}

	/**
	 * Register hook register customizer.
	 *
	 * @since 0.1.0
	 */
	public function customizer_register() {
		do_action( 'thim_customizer_register' );
	}

	/**
	 * Filter config kirki.
	 *
	 * @param $config
	 *
	 * @return array
	 * @since 0.1.0
	 */
	public function config( $config ) {
		return wp_parse_args( array(
			'logo_image'  => THIM_CORE_ASSETS_URI . '/images/logo.png',
			'description' => esc_html__( 'Designed by ThimPress.', 'thim-core' ),
			'url_path'    => THIM_CORE_INC_URI . '/includes/kirki/',
		), $config );
	}


	/**
	 * Add panel.
	 *
	 * @param array $panel
	 *
	 * @since 0.1.0
	 */
	public function add_panel( array $panel ) {
		Kirki::add_panel( $panel['id'], $panel );
	}

	/**
	 * Add section.
	 *
	 * @param array $section
	 *
	 * @since 0.1.0
	 */
	public function add_section( array $section ) {
		Kirki::add_section( $section['id'], $section );
	}

	/**
	 * Add field.
	 *
	 * @param array $field
	 *
	 * @since 0.1.0
	 */
	public function add_field( array $field ) {
		if ( ! array_key_exists( 'settings', $field ) ) {
			$field['settings'] = $field['id'];
		}

		Kirki::add_field( $field['id'], $field );
	}

	/**
	 * Add group fields.
	 *
	 * @param array $group
	 *
	 * @since 0.1.0
	 */
	public function add_group( array $group ) {
		$section  = $group['section'];
		$groups   = $group['groups'];
		$priority = isset( $group['priority'] ) ? $group['priority'] : 10;

		foreach ( $groups as $group ) {
			$fields   = $group['fields'];
			$group_id = $group['id'];

			/**
			 * Header
			 */
			$filed_title = array(
				'id'       => $group_id,
				'type'     => 'accordion',
				'section'  => $section,
				'label'    => $group['label'],
				'priority' => $priority,
				'fields'   => $fields,
			);
			$this->add_field( $filed_title );

			/**
			 * Body
			 */
			foreach ( $fields as $field ) {
				$update_field             = $field;
				$update_field['section']  = $section;
				$update_field['priority'] = $priority;
				$update_field['hide']     = true;

				$this->add_field( $update_field );
			}
		}
	}

	/**
	 * Get SASS variables from customizer.
	 *
	 * @return array
	 * @since 0.1.0
	 */
	public static function get_sass_variables() {
		$variables = array();
		$prefix    = TP::$prefix;

		$fields = Kirki::$fields;

		/**
		 * Fixes get old values.
		 */
		global $thim_customizer_options;
		$thim_customizer_options = get_theme_mods();

		foreach ( $fields as $field_id => $field ) {
			$type          = $field['type'];
			$excluded_type = array(
				'repeater',
				'kirki-generic',
				'kirki-sortable',
				'kirki-code',
				'kirki-editor',
				'kirki-dropdown-pages',
				'kirki-custom',
			);

			if ( in_array( $type, $excluded_type ) ) {//Excluded
				continue;
			}

			$default_value = $field['default'];
			$values        = self::get_option( $field_id, $default_value );

			/**
			 * Add double quote if the field is text.
			 */
			$string_type = array(
				'image',
				'upload',
				'cropped_image',
				'kirki-radio-image',
			);
			if ( in_array( $type, $string_type ) ) {
				$values = '"' . $values . '"';
			}

			if ( is_array( $values ) ) {
				foreach ( $values as $key => $val ) {
					if ( 'subsets' === $key ) {//Excluded subsets
						continue;
					}

					if ( 'variant' === $key ) {
						if ( 'regular' === $val ) {
							$val = '400normal';
						}

						if ( 'italic' === $val ) {
							$val = '400italic';
						}

						$font_weight = intval( $val );

						if ( 0 === $font_weight ) {
							$font_weight = 400;
						}

						$font_style = str_replace( $font_weight, '', $val );

						if ( empty( $font_style ) ) {
							$font_style = 'normal';
						}

						$key = $field_id;
						$key = $prefix . $key;

						$variables[ $key . '_font_weight' ] = $font_weight;
						$variables[ $key . '_font_style' ]  = $font_style;
						continue;
					}

					$key = $field_id . '_' . $key;
					$key = $prefix . $key;
					$key = str_replace( '-', '_', $key );

					$variables[ $key ] = $val;
				}
			} else {
				if ( empty( $values ) ) {
					$values = '""';
				}
				$variables[ $prefix . $field_id ] = $values;
			}
		}

		return $variables;
	}

	/**
	 * Get options customizer.
	 *
	 * @return array
	 * @since 0.1.0
	 */
	public static function get_options() {
		global $thim_customizer_options;

		if ( empty( $thim_customizer_options ) ) {
			$thim_customizer_options = get_theme_mods();
		}

		return $thim_customizer_options;
	}

	/**
	 * Get option customizer by key.
	 *
	 * @param string $key
	 * @param        $default
	 *
	 * @return mixed|null
	 * @since 0.1.0
	 */
	public static function get_option( $key, $default = false ) {
		$thim_customizer_options = self::get_options();

		if ( ! array_key_exists( $key, $thim_customizer_options ) ) {
			return $default;
		}

		return $thim_customizer_options[ $key ];
	}

	/**
	 * Enqueue scripts in Customize.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'thim_core_customizer_panel', THIM_CORE_ASSETS_URI . '/css/customizer/panel.css', array(), THIM_CORE_VERSION );
	}

	/**
	 * Enqueue scripts for preview.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_scripts_preview() {
		global $wp_customize;
		if ( ! $wp_customize ) {
			return;
		}

		wp_enqueue_style( 'thim_core_customizer_preview', THIM_CORE_ASSETS_URI . '/css/customizer/preview.css', array(), THIM_CORE_VERSION );
	}

	/**
	 * Hook before save customize.
	 *
	 * @since 0.1.0
	 */
	public function before_save_customize() {
		$this->save_default_values();
	}

	/**
	 * Save default values to theme mods.
	 *
	 * @since 0.1.0
	 */
	private function save_default_values() {
		$fields = Kirki::$fields;

		foreach ( $fields as $field_id => $field ) {
			$option  = self::get_option( $field_id, null );
			$default = $field['default'];

			if ( null === $option ) {
				set_theme_mod( $field_id, $default );
			}
		}
	}

	/**
	 * Handle after saving customize.
	 *
	 * @since 0.1.0
	 */
	public function after_save_customize() {
		$file_sass_options = apply_filters( 'thim_core_config_sass', array() );

		if ( empty( $file_sass_options ) ) {
			return;
		}

		$file_sass_options = wp_parse_args( $file_sass_options, array(
			'dir'  => '',
			'name' => 'options.scss',
		) );

		try {
			require_once THIM_CORE_INC_PATH . '/class-thim-compile-sass.php';
			Thim_Compile_SASS::instance()->compile_scss( $file_sass_options );
		} catch ( Exception $e ) {
			Thim_Core_Customizer::message_customize_error( $e->getMessage() );
		}
	}

	/**
	 * Filter response after saving customizer.
	 *
	 * @return object
	 * @since 0.1.0
	 */
	public function customize_save_response() {
		$message = esc_html__( 'Save customizer success!', 'thim-core' );
		$message = apply_filters( 'thim_core_message_response_save_customize', $message );

		$response = new stdClass();

		$response->msg   = $message;
		$response->info  = array(
			'mem' => @memory_get_usage( true ) / 1048576,
			'php' => @phpversion(),
		);
		$response->error = apply_filters( 'thim_core_error_save_customize', false );

		return $response;
	}

	/**
	 * Add filter notify error in response when save customize.
	 *
	 * @param $error
	 *
	 * @return true|void
	 * @since 0.1.0
	 */
	public static function notify_error_customize( $error ) {
		return add_filter( 'thim_core_error_save_customize', function () use ( $error ) {
			return $error;
		} );
	}

	/**
	 * Add filter message in response when save customize.
	 *
	 * @param $message
	 *
	 * @return true|void
	 * @since 0.1.0
	 */
	public static function message_customize( $message ) {
		return add_filter( 'thim_core_message_response_save_customize', function () use ( $message ) {
			return $message;
		} );
	}

	/**
	 * Add filter message error in response when save customize.
	 *
	 * @param $message
	 *
	 * @return bool
	 * @since 0.1.0
	 */
	public static function message_customize_error( $message ) {
		return self::notify_error_customize( true ) && self::message_customize( $message );
	}

	/**
	 * Get uri stylesheet.
	 *
	 * @return bool|mixed|void
	 * @since 0.1.0
	 */
	public static function get_stylesheet_uri() {
		$option = self::get_option( self::$key_stylesheet_uri );

		if ( empty( $option ) ) {
			return false;
		}

		return $option;
	}

	/**
	 * Enqueue stylesheet (theme options) uri.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_stylesheet_uri() {
		$stylesheet = self::get_stylesheet_uri();

		if ( ! $stylesheet ) {
			$stylesheet = apply_filters( 'thim_style_default_uri', trailingslashit( get_stylesheet_directory_uri() ) . 'inc/data/default.css' );
		}

		wp_enqueue_style( 'thim-style-options', $stylesheet, array( 'thim-style' ) );
	}
}

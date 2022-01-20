<?php

class Thim_Plugins_Manager extends Thim_Admin_Sub_Page {
	public $key_page = 'plugins';

	/**
	 * @var string
	 *
	 * @since 0.4.0
	 */
	public static $page_key = 'plugins';

	/**
	 * @var null
	 *
	 * @since 0.5.0
	 */
	public static $all_plugins_require = null;

	/**
	 * Is writable.
	 *
	 * @since 0.5.0
	 *
	 * @var bool
	 */
	private static $is_writable = null;

	/**
	 * Add notice.
	 *
	 * @since 0.5.0
	 *
	 * @param string $content
	 * @param string $type
	 */
	public static function add_notification( $content = '', $type = 'success' ) {
		Thim_Dashboard::add_notification( array(
			'content' => $content,
			'type'    => $type,
			'page'    => self::$page_key,
		) );
	}

	/**
	 * Thim_Plugins_Manager constructor.
	 *
	 * @since 0.4.0
	 */
	protected function __construct() {
		parent::__construct();

		$this->init();
		$this->init_hooks();
	}

	/**
	 * Init.
	 *
	 * @since 0.5.0
	 */
	private function init() {
		$this->notification();
	}

	/**
	 * Init hooks.
	 *
	 * @since 0.4.0
	 */
	private function init_hooks() {
		add_action( 'wp_ajax_thim_plugins_manager', array( $this, 'handle_ajax' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'thim_dashboard_sub_pages', array( $this, 'add_sub_page' ) );
	}

	/**
	 * Add sub page.
	 *
	 * @since 0.8.5
	 *
	 * @param $sub_pages
	 *
	 * @return mixed
	 */
	public function add_sub_page( $sub_pages ) {
		$sub_pages['plugins'] = array(
			'title' => __( 'Plugins', 'thim-core' ),
		);

		return $sub_pages;
	}

	/**
	 * Handle ajax.
	 *
	 * @since 0.4.0
	 */
	public function handle_ajax() {
		$slug   = isset( $_POST['slug'] ) ? $_POST['slug'] : false;
		$action = isset( $_POST['plugin_action'] ) ? $_POST['plugin_action'] : false;

		$plugins = self::get_all_plugins();
		foreach ( $plugins as $plugin ) {
			if ( $plugin['slug'] == $slug ) {
				$thim_plugin = new Thim_Plugin();
				$thim_plugin->set_args( $plugin );

				$next_action = 'activate';

				if ( $action == 'install' ) {
					$result = $thim_plugin->install();
				} else if ( $action == 'activate' ) {
					$result      = $thim_plugin->activate( true );
					$next_action = 'deactivate';
				} else {
					$result      = $thim_plugin->deactivate();
					$next_action = 'activate';
				}

				if ( $result ) {
					wp_send_json_success( array(
						'messages' => $thim_plugin->get_messages(),
						'action'   => $next_action,
						'text'     => ucfirst( $next_action ),
					) );
				}

				wp_send_json_error( array(
					'messages' => $thim_plugin->get_messages()
				) );
			}
		}

		wp_send_json_error();
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 0.4.0
	 *
	 * @param $page_now
	 */
	public function enqueue_scripts( $page_now ) {
		if ( ! $this->is_myself() ) {
			return;
		}

		wp_enqueue_script( 'thim-isotope', THIM_CORE_ADMIN_URI . '/assets/js/plugins/isotope.pkgd.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'thim-plugins', THIM_CORE_ADMIN_URI . '/assets/js/plugins/thim-plugins.js', array( 'jquery' ) );
		wp_enqueue_script( 'thim-plugins-manager', THIM_CORE_ADMIN_URI . '/assets/js/plugins/plugins-manager.js', array( 'thim-plugins', 'thim-isotope' ) );

		$this->localize_script();
	}

	/**
	 * Localize script.
	 *
	 * @since 0.4.0
	 */
	public function localize_script() {
		wp_localize_script( 'thim-plugins', 'thim_plugins_manager', array(
			'admin_ajax_action' => admin_url( 'admin-ajax.php?action=thim_plugins_manager' ),
		) );
	}

	/**
	 * Get all plugins.
	 *
	 * @since 0.4.0
	 *
	 * @return array
	 */
	public static function get_all_plugins() {
		if ( self::$all_plugins_require ) {
			return self::$all_plugins_require;
		}

		$plugins = array();

		$plugins = apply_filters( 'thim_core_get_all_plugins_require', $plugins );

		foreach ( $plugins as $index => $plugin ) {
			if ( ! isset( $plugin['required'] ) ) {
				$plugins[ $index ]['required'] = false;
			}
		}

		uasort( $plugins, function ( $first, $second ) {
			if ( $first['required'] ) {
				return false;
			}

			return true;
		} );

		self::$all_plugins_require = $plugins;

		return self::get_all_plugins();
	}

	/**
	 * Get list slug plugins require all demo.
	 *
	 * @since 0.5.0
	 *
	 * @return array
	 */
	public static function get_slug_plugins_require_all() {
		$all_plugins = self::get_all_plugins();

		$plugins_require_all = array();
		foreach ( $all_plugins as $index => $plugin ) {
			if ( isset( $plugin['required'] ) && $plugin['required'] ) {
				array_push( $plugins_require_all, $plugin['slug'] );
			}
		}

		return $plugins_require_all;
	}

	/**
	 * Get plugin by slug.
	 *
	 * @since 0.5.0
	 *
	 * @param $slug
	 *
	 * @return bool|array
	 */
	public static function get_plugin_by_slug( $slug ) {
		$all_plugins = self::get_all_plugins();

		if ( count( $all_plugins ) === 0 ) {
			return false;
		}

		foreach ( $all_plugins as $plugin ) {
			if ( $plugin['slug'] == $slug ) {
				return $plugin;
			}
		}

		return false;
	}

	/**
	 * Check permission plugins directory.
	 *
	 * @since 0.5.0
	 */
	private static function check_permission() {
		self::$is_writable = wp_is_writable( WP_PLUGIN_DIR );
	}

	/**
	 * Get permission writable plugins directory.
	 *
	 * @since 0.8.2
	 */
	public static function get_permission() {
		if ( is_null( self::$is_writable ) ) {
			self::check_permission();
		}

		return self::$is_writable;
	}

	/**
	 * Notice waring.
	 *
	 * @since 0.5.0
	 */
	private function notification() {
		if ( ! self::get_permission() ) {
			Thim_Dashboard::add_notification( array(
				'content' => '<strong>Important!</strong> Please check permission directory <code>' . WP_PLUGIN_DIR . '</code>. Please follow <a href="http://goo.gl/sKRoXT" target="_blank">the guide</a>.',
				'type'    => 'error'
			) );
		}
	}
}
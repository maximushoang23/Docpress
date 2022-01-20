<?php

/**
 * Class Thim_Core_Admin.
 *
 * @package   Thim_Core
 * @since     0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Thim_Core_Admin extends Thim_Singleton {
	/**
	 * Go to theme dashboard.
	 *
	 * @since 0.8.1
	 */
	public static function go_to_theme_dashboard() {
		if ( headers_sent() ) {
			return;
		}

		$link_page = admin_url( '?thim-core-redirect-to-dashboard' );

		wp_redirect( $link_page );
		exit();
	}

	/**
	 * Detect my theme.
	 *
	 * @since 0.8.0
	 *
	 * @return bool
	 */
	public static function is_my_theme() {
		$theme = Thim_Theme_Manager::get_metadata();

		if ( strtolower( $theme['author'] ) === 'thimpress' ) {
			return true;
		}

		return (bool) get_theme_support( 'thim-core' );
	}

	/**
	 * Thim_Core_Admin constructor.
	 *
	 * @since 0.1.0
	 */
	protected function __construct() {
		spl_autoload_register( array( $this, 'autoload' ) );

		add_action( 'admin_init', array( $this, 'redirect_to_dashboard' ) );

		if ( ! self::is_my_theme() ) {
			return;
		}

		$this->init();
		$this->init_hooks();
	}

	/**
	 * Fake page to redirect to dashboard.
	 *
	 * @since 0.8.1
	 */
	public function redirect_to_dashboard() {
		$request = isset( $_REQUEST['thim-core-redirect-to-dashboard'] ) ? true : false;

		if ( ! $request ) {
			return;
		}

		if ( headers_sent() ) {
			return;
		}

		if ( ! is_callable( 'Thim_Dashboard', 'get_link_main_dashboard' ) ) {
			return;
		}

		$this->redirect_user();
	}

	/**
	 * Handle redirect the user.
	 *
	 * @since 0.8.5
	 */
	private function redirect_user() {
		if ( Thim_Dashboard::check_first_install() ) {
			$url = Thim_Dashboard::get_link_page_by_slug( 'getting-started' );

			wp_redirect( $url );
			exit();
		}

		wp_redirect( Thim_Dashboard::get_link_main_dashboard() );
		exit();
	}

	/**
	 * Init.
	 *
	 * @since 0.1.0
	 */
	private function init() {
		$this->functions();
		$this->run();
	}

	/**
	 * Autoload classes.
	 *
	 * @since 0.3.0
	 *
	 * @param $class
	 */
	public function autoload( $class ) {
		$class = strtolower( $class );

		$file_name = 'class-' . str_replace( '_', '-', $class ) . '.php';

		if ( strpos( $class, 'service' ) !== false ) {
			$file_name = 'services/' . $file_name;
		}

		$file = THIM_CORE_ADMIN_PATH . DIRECTORY_SEPARATOR . $file_name;
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}

	/**
	 * Run admin core.
	 *
	 * @since 0.3.0
	 */
	private function run() {
		Thim_Metabox::instance();
		Thim_Post_Formats::instance();
		Thim_Singular_Settings::instance();
		Thim_Sidebar_Manager::instance();
		Thim_Dashboard::instance();
		Thim_Layout_Builder::instance();
	}

	/**
	 * Init hooks.
	 *
	 * @since 0.1.0
	 */
	private function init_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( "plugin_action_links_thim-core/thim-core.php", array( $this, 'add_action_links' ) );
	}

	/**
	 * Add action links.
	 *
	 * @since 0.8.0
	 *
	 * @param $links
	 *
	 * @return array
	 */
	public function add_action_links( $links ) {
		$links[] = '<a href="' . esc_url( Thim_Dashboard::get_link_main_dashboard() ) . '">' . __( 'Dashboard', 'thim-core' ) . '</a>';
		$links[] = '<a href="https://thimpress.com/forums/" target="_blank">' . __( 'Support', 'thim-core' ) . '</a>';

		return $links;
	}

	/**
	 * Include functions.
	 *
	 * @since 0.1.0
	 */
	private function functions() {
		include_once THIM_CORE_ADMIN_PATH . '/functions.php';
	}

	/**
	 * Enqueue scripts.
	 *
	 * @param string $page_now
	 *
	 * @since 0.2.1
	 */
	public function enqueue_scripts( $page_now ) {
		wp_enqueue_style( 'thim-admin', THIM_CORE_ADMIN_URI . '/assets/css/admin.css', array(), THIM_CORE_VERSION );
		wp_enqueue_style( 'thim-icomoon', THIM_CORE_ADMIN_URI . '/assets/css/icomoon.css', array(), THIM_CORE_VERSION );
	}
}

/**
 * Thim Core Admin init.
 *
 * @since 0.8.1
 */
function thim_core_admin_init() {
	Thim_Core_Admin::instance();
}

add_action( 'after_setup_theme', 'thim_core_admin_init', - 10 );
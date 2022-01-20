<?php

/**
 * Thim Core Plugin.
 *
 * @package   Thim_Core
 * @since     0.1.0
 * @author    ThimPress <contact@thimpress.com>
 * @link      http://thimpress.com
 * @copyright 2016 ThimPress
 *
 * Plugin Name:       Thim Core
 * Plugin URI:        http://thimpress.com
 * Description:       Thim Core Plugin.
 * Version:           0.8.5
 * Author:            ThimPress
 * Author URI:        ThimPress
 * Text Domain:       thim-core
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class TP
 *
 * @since 0.1.0
 */
class TP {
	/**
	 * @var null
	 *
	 * @since 0.1.0
	 */
	protected static $_instance = null;

	/**
	 * @since 0.8.5
	 *
	 * @var string
	 */
	public static $require_WP = '4.6';

	/**
	 * @since 0.8.5
	 *
	 * @var string
	 */
	public static $compatibility_WP = '4.6.1';

	/**
	 * @var string
	 *
	 * @since 0.1.0
	 */
	public static $prefix = 'thim_';

	/**
	 * @var string
	 *
	 * @since 0.8.5
	 */
	public static $slug = 'thim-core';

	/**
	 * @var string
	 *
	 * @since 0.2.0
	 */
	private static $option_version = 'thim_core_version';

	/**
	 * Return unique instance of TP.
	 *
	 * @since 0.1.0
	 */
	static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Thim_Framework constructor.
	 *
	 * @since 0.1.0
	 */
	private function __construct() {
		$this->init();
		$this->hooks();

		do_action( 'thim_core_loaded' );
	}

	/**
	 * Get is debug?
	 *
	 * @return bool
	 * @since 0.1.0
	 */
	public static function is_debug() {
		if ( ! defined( 'THIM_DEBUG' ) ) {
			return false;
		}

		return (bool) THIM_DEBUG;
	}

	/**
	 * Init class.
	 *
	 * @since 0.1.0
	 */
	public function init() {
		do_action( 'before_thim_core_init' );

		$this->define_constants();
		$this->providers();
		$this->inc();
		$this->admin();

		do_action( 'thim_core_init' );
	}

	/**
	 * Init hooks.
	 *
	 * @since 0.1.0
	 */
	private function hooks() {
		register_activation_hook( __FILE__, array( $this, 'install' ) );
		add_action( 'activated_plugin', array( $this, 'activated' ) );
		add_action( 'plugins_loaded', array( $this, 'text_domain' ), 1 );
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'inject_update' ) );
		add_action( 'admin_notices', array( $this, 'notice_update' ) );
		add_filter( 'plugins_api', array( $this, 'filter_plugin_information' ), 10, 3 );
	}

	/**
	 * Add filter plugin information.
	 *
	 * @since 0.8.5
	 *
	 * @param $false
	 * @param $action
	 * @param $args
	 *
	 * @return stdClass
	 */
	public function filter_plugin_information( $false, $action, $args ) {
		if ( $action != 'plugin_information' ) {
			return $false;
		}

		if ( isset( $args->slug ) && $args->slug === 'thim-core' ) {
			$information = new stdClass();

			$information->name                  = __( 'Thim Core by ThimPress', 'thim-core' );
			$information->slug                  = self::$slug;
			$information->author                = __( '<a href="https://thimpress.com" target="_blank">ThimPress</a>', 'thim-core' );
			$information->version               = $this->get_version_require();
			$information->requires              = self::$require_WP;
			$information->sections['changelog'] = self::get_changelog();

			return $information;
		}

		return $false;
	}

	/**
	 * Notice update thim core.
	 *
	 * @since 0.8.4
	 */
	public function notice_update() {
		if ( ! $this->check_self_update() ) {
			return;
		}

		?>
		<div class="notice notice-success">
			<h3><?php _e( 'Important Update!', 'thim-core' ); ?></h3>
			<p><?php printf( __( 'Thim Core %s is available for your system and is ready to install.', 'thim-core' ), $this->get_version_require() ); ?></p>
			<p><a class="button button-primary" href="<?php echo network_admin_url( 'update-core.php#update-plugins-table' ); ?>"><?php esc_html_e( 'Go to update', 'thim-core' ); ?></a></p>
		</div>
		<?php
	}

	/**
	 * Get version require.
	 *
	 * @since 0.8.4
	 *
	 * @return string
	 */
	private function get_version_require() {
		$package = $this->get_package_require();

		if ( ! $package || ! isset( $package['version'] ) ) {
			return THIM_CORE_VERSION;
		}

		return $package['version'];
	}

	/**
	 * Get package core require.
	 *
	 * @since 0.8.4
	 *
	 * @return mixed|void
	 */
	public function get_package_require() {
		return $package = apply_filters( 'thim_core_get_package', false );
	}

	/**
	 * Inject update.
	 *
	 * @since 0.8.4
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function inject_update( $value ) {
		if ( ! $this->check_self_update() ) {
			return $value;
		}

		$package = $this->get_package_require();

		$value->response['thim-core/thim-core.php'] = (object) array(
			'slug'        => $package['slug'],
			'new_version' => $package['version'],
			'url'         => 'https://thimpress.com',
			'package'     => $package['source'],
			'tested'      => self::$compatibility_WP,
		);

		return $value;
	}

	/**
	 * Check self update.
	 *
	 * @since 0.8.4
	 *
	 * @return bool
	 */
	public function check_self_update() {
		if ( ! version_compare( THIM_CORE_VERSION, $this->get_version_require(), '<' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Providers.
	 *
	 * @since 0.8.5
	 */
	private function providers() {
		require_once THIM_CORE_PATH . '/providers/class-thim-singleton.php';
	}

	/**
	 * Core.
	 *
	 * @since 0.1.0
	 */
	private function inc() {
		require_once THIM_CORE_INC_PATH . '/class-thim-core.php';
	}

	/**
	 * Admin.
	 *
	 * @since 0.1.0
	 */
	private function admin() {
		require_once THIM_CORE_PATH . '/admin/class-thim-core-admin.php';
	}

	/**
	 * Define constants.
	 *
	 * @since 0.2.0
	 */
	private function define_constants() {
		define( 'THIM_CORE_FILE', __FILE__ );
		define( 'THIM_CORE_PATH', dirname( __FILE__ ) );
		define( 'THIM_CORE_URI', untrailingslashit( plugins_url( '/', THIM_CORE_FILE ) ) );
		define( 'THIM_CORE_ASSETS_URI', THIM_CORE_URI . '/assets' );
		define( 'THIM_CORE_VERSION', '0.8.5' );

		define( 'THIM_CORE_ADMIN_PATH', THIM_CORE_PATH . '/admin' );
		define( 'THIM_CORE_ADMIN_URI', THIM_CORE_URI . '/admin' );
		define( 'THIM_CORE_INC_PATH', THIM_CORE_PATH . '/inc' );
		define( 'THIM_CORE_INC_URI', THIM_CORE_URI . '/inc' );
	}

	/**
	 * Activation hook.
	 *
	 * @since 0.2.0
	 */
	public function install() {
		add_option( self::$option_version, THIM_CORE_VERSION );
	}

	/**
	 * Hook after plugin was activated.
	 *
	 * @since 0.2.0
	 *
	 * @param $plugin
	 */
	public function activated( $plugin ) {
		$plugins_are_activating = isset( $_POST['checked'] ) ? $_POST['checked'] : array();

		if ( count( $plugins_are_activating ) > 1 ) {
			return;
		}

		if ( 'thim-core/thim-core.php' !== $plugin ) {
			return;
		}

		Thim_Core_Admin::go_to_theme_dashboard();
	}

	/**
	 * Get active network.
	 *
	 * @since 0.8.1
	 *
	 * @return bool
	 */
	public static function is_active_network() {
		if ( ! is_multisite() ) {
			return true;
		}

		$plugin_file            = 'thim-core/thim-core.php';
		$active_plugins_network = get_site_option( 'active_sitewide_plugins' );

		if ( isset( $active_plugins_network[ $plugin_file ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get changelog content.
	 *
	 * @since 0.8.5
	 *
	 * @return bool|string
	 */
	public static function get_changelog() {
		$changelog_file = THIM_CORE_PATH . '/changelog.html';
		if ( ! is_readable( $changelog_file ) ) {
			return false;
		}

		ob_start();
		include_once( $changelog_file );

		return $content = ob_get_clean();
	}

	/**
	 * Load text domain.
	 *
	 * @since 0.1.0
	 *
	 */
	function text_domain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'thim-core' );

		load_textdomain( 'thim-core', trailingslashit( WP_LANG_DIR ) . 'thim-core' . '/' . 'thim-core' . '-' . $locale . '.mo' );
		load_plugin_textdomain( 'thim-core', false, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
	}
}

TP::instance();
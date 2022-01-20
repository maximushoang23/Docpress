<?php

/**
 * Class Thim_Product_Registration.
 *
 * @package   Thim_Core
 * @since     0.2.1
 */
class Thim_Product_Registration {
	/**
	 * @since 0.2.1
	 *
	 * @var null
	 */
	protected static $_instance = null;

	/**
	 * @since 0.2.1
	 *
	 * @var string
	 */
	public static $key_callback_request = 'tc_callback_registration';

	/**
	 * Return unique instance of Thim_Product_Registration.
	 *
	 * @since 0.2.1
	 */
	static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Check update theme from envato.
	 *
	 * @since 0.8.0
	 *
	 * @return bool|WP_Error
	 */
	public static function check_update() {
		if ( wp_installing() ) {
			return false;
		}

		$token   = self::get_token();
		$item_id = self::get_item_id();

		try {
			$theme_metadata = Thim_Envato_Service::get_theme_metadata( $item_id, $token );
			$version        = $theme_metadata['version'];
			Thim_Theme_Manager::update_latest_version( $version );
			self::update_stylesheet_can_update();
		} catch ( Exception $exception ) {
			self::destroy_active();

			return new WP_Error( $exception->getCode(), $exception->getMessage() );
		}

		return true;
	}

	/**
	 * Can update?
	 *
	 * @since 0.7.0
	 *
	 * @return bool
	 */
	public static function can_update() {
		$theme_meta_data = Thim_Theme_Manager::get_metadata();

		$local_version  = $theme_meta_data['version'];
		$latest_version = $theme_meta_data['latest_version'];

		return $can_update = version_compare( $latest_version, $local_version ) > 0;
	}

	/**
	 * Set activated theme.
	 *
	 * @since 0.2.1
	 *
	 * @deprecated
	 *
	 * @param $purchase_code
	 */
	private function set_activated( $purchase_code ) {
		$this->save_item_id( $purchase_code );
	}

	/**
	 * Save item id.
	 *
	 * @since 0.7.0
	 *
	 * @param $item_id
	 */
	private static function save_item_id( $item_id ) {
		update_site_option( 'thim_envato_item_id', $item_id );
	}

	/**
	 * Get item id.
	 *
	 * @since 0.7.0
	 *
	 * @return bool|string
	 */
	public static function get_item_id() {
		$option = get_site_option( 'thim_envato_item_id' );

		if ( empty( $option ) ) {
			return false;
		}

		return $option;
	}

	/**
	 * Save personal token.
	 *
	 * @since 0.7.0
	 *
	 * @param $token
	 */
	private static function save_token( $token ) {
		update_site_option( 'thim_envato_personal_token', $token );
	}

	/**
	 * Get personal token.
	 *
	 * @since 0.7.0
	 *
	 * @return bool|string
	 */
	public static function get_token() {
		$option = get_site_option( 'thim_envato_personal_token' );

		if ( empty( $option ) ) {
			return false;
		}

		return $option;
	}

	/**
	 * Save theme stylesheet can update.
	 *
	 * @since 0.8.0
	 *
	 * @param string $stylesheet
	 */
	private static function update_stylesheet_can_update( $stylesheet = null ) {
		if ( ! $stylesheet ) {
			$stylesheet = get_option( 'stylesheet' );
		}

		update_site_option( 'thim_stylesheet_update', $stylesheet );
	}

	/**
	 * Get theme stylesheet can update.
	 *
	 * @since 0.8.0
	 *
	 * @return bool|string
	 */
	public static function get_stylesheet_can_update() {
		$option = get_site_option( 'thim_stylesheet_update' );

		if ( empty( $option ) ) {
			return false;
		}

		return $option;
	}

	/**
	 * Update latest version for theme.
	 *
	 * @since 0.7.0
	 *
	 * @deprecated use Thim_Theme_Manager::update_latest_version()
	 *
	 * @param $new_version
	 */
	public static function update_latest_version( $new_version ) {
		Thim_Theme_Manager::update_latest_version( $new_version );
	}

	/**
	 * Get latest version.
	 *
	 * @since 0.7.0
	 *
	 * @deprecated use Thim_Theme_Manager::get_latest_version()
	 *
	 * @return string|bool
	 */
	public static function get_latest_version() {
		return Thim_Theme_Manager::get_latest_version();
	}

	/**
	 * Get active theme from envato.s
	 *
	 * @since 0.2.1
	 *
	 * @return bool
	 */
	public static function is_active() {
		$activated = self::get_item_id();

		if ( empty( $activated ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Destroy active theme from envato.
	 *
	 * @since 0.8.0
	 */
	public static function destroy_active() {
		self::save_item_id( false );
	}

	/**
	 * Get url auth.
	 *
	 * @since 0.2.1
	 *
	 * @return string
	 */
	public static function get_url_auth() {
		return 'http://dev.thimpress.com/tc/?envato_auth=1';
	}

	/**
	 * Get verify callback url.
	 *
	 * @since 0.2.1
	 *
	 * @return string|void
	 */
	public static function get_url_verify_callback() {
		return home_url( '/?' . self::$key_callback_request . '=1' );
	}

	/**
	 * Get link fake download theme from envato.
	 *
	 * @since 0.7.0
	 *
	 * @return string
	 */
	private static function _get_fake_url_download_theme() {
		return null;
	}

	/**
	 * Get url link download theme from envato.
	 *
	 * @since 0.7.0
	 *
	 * @return bool|string
	 */
	private static function _get_url_download_theme() {
		$token   = self::get_token();
		$item_id = self::get_item_id();

		return Thim_Envato_Service::get_url_download_item( $item_id, $token );
	}

	/**
	 * Get link go to update theme.
	 *
	 * @since 0.8.1
	 *
	 * @return string
	 */
	public static function get_link_go_to_update() {
		if ( TP::is_active_network() ) {
			return network_admin_url( 'update-core.php#update-themes-table' );
		}

		return admin_url( 'themes.php' );
	}

	/**
	 * Thim_Product_Registration constructor.
	 *
	 * @since 0.2.1
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Init hooks.
	 *
	 * @since 0.2.1
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'handle_callback_verify' ) );
		add_action( 'init', array( $this, 'activate_theme_by_personal_token' ) );
		add_action( 'wp_ajax_thim_check_update', array( $this, 'handle_check_update_theme' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'site_transient_update_themes', array( $this, 'inject_update' ) );
		add_filter( 'transient_update_themes', array( $this, 'inject_update' ) );
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'schedule_check_update' ) );
		add_filter( 'upgrader_package_options', array( $this, 'pre_update_theme' ) );
	}

	/**
	 * Schedule check update theme.
	 *
	 * @since 0.8.1
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function schedule_check_update( $value ) {
		if ( ! self::is_active() ) {
			return $value;
		}

		$stylesheet = self::get_stylesheet_can_update();
		if ( $stylesheet && ! empty( $value->response[ $stylesheet ] ) ) {
			return $value;
		}

		self::check_update();

		$stylesheet                     = self::get_stylesheet_can_update();
		$value->response[ $stylesheet ] = array(
			'theme'       => $stylesheet,
			'new_version' => Thim_Theme_Manager::get_latest_version(),
			'url'         => 'https://thimpress.com/forums/',
			'package'     => self::_get_fake_url_download_theme(),
		);

		return $value;
	}

	/**
	 * Pre update theme, get again link download theme.
	 *
	 * @since 0.8.0
	 *
	 * @param $options
	 *
	 * @return mixed
	 */
	public function pre_update_theme( $options ) {
		$hook_extra = isset( $options['hook_extra'] ) ? $options['hook_extra'] : false;

		if ( ! $hook_extra ) {
			return $options;
		}

		$theme = isset( $hook_extra['theme'] ) ? $hook_extra['theme'] : false;

		if ( ! $theme ) {
			return $options;
		}

		if ( $theme !== self::get_stylesheet_can_update() ) {
			return $options;
		}

		$options['package'] = self::_get_url_download_theme();

		return $options;
	}

	/**
	 * Add filter update theme.
	 *
	 * @since 0.7.0
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function inject_update( $value ) {
		if ( ! self::is_active() || ! TP::is_active_network() ) {
			return $value;
		}

		if ( self::can_update() ) {
			$stylesheet = self::get_stylesheet_can_update();

			$value->response[ $stylesheet ] = array(
				'theme'       => $stylesheet,
				'new_version' => Thim_Theme_Manager::get_latest_version(),
				'url'         => 'https://thimpress.com/forums/',
				'package'     => self::_get_fake_url_download_theme(),
			);
		}

		return $value;
	}

	/**
	 * Handle ajax check update theme.
	 *
	 * @since 0.7.0
	 */
	public function handle_check_update_theme() {
		if ( ! self::is_active() ) {
			wp_send_json_error();
		}

		$check = self::check_update();

		if ( is_wp_error( $check ) ) {
			wp_send_json_error( $check->get_error_message() );
		}

		if ( ! $check ) {
			wp_send_json_error();
		}

		wp_send_json_success( array(
			'can_update' => self::can_update(),
			'current'    => Thim_Theme_Manager::get_current_version(),
			'latest'     => Thim_Theme_Manager::get_latest_version(),
		) );
	}

	/**
	 * Activate theme by personal token.
	 *
	 * @since 0.7.0
	 */
	public function activate_theme_by_personal_token() {
		$detect = isset( $_REQUEST['thim-activate-theme'] ) ? true : false;
		if ( ! $detect ) {
			return;
		}

		$token = ! empty( $_REQUEST['token'] ) ? $_REQUEST['token'] : false;
		if ( ! $token ) {
			return;
		}

		$theme_name = ! empty( $_REQUEST['theme'] ) ? $_REQUEST['theme'] : false;
		if ( ! $theme_name ) {
			return;
		}

		$verify = Thim_Envato_Service::verify_by_token( $token, $theme_name );

		if ( is_wp_error( $verify ) ) {
			Thim_Dashboard::add_notification( array(
				'content' => $verify->get_error_message(),
				'type'    => 'error',
			) );

			return;
		}

		if ( ! $verify ) {
			Thim_Dashboard::add_notification( array(
				'content' => __( 'Verify failed. Please try again or enter another personal token!', 'thim-core' ),
				'type'    => 'error',
			) );

			return;
		}

		$this->save_token( $token );
		$this->save_item_id( $verify['id'] );
		Thim_Dashboard::add_notification( array(
			'content' => __( 'Activate theme success!', 'thim-core' ),
			'type'    => 'success',
		) );
	}

	/**
	 * Handle callback from server verify.
	 *
	 * @deprecated
	 *
	 * @since 0.2.1
	 */
	public function handle_callback_verify() {
		$detect_request = ! empty( $_GET[ self::$key_callback_request ] ) ? $_GET[ self::$key_callback_request ] : false;

		if ( ! $detect_request ) {
			return;
		}

		$purchase_code = isset( $_GET['purchase_code'] ) ? $_GET['purchase_code'] : '';
		$this->set_activated( $purchase_code );

		wp_redirect( Thim_Dashboard::get_link_main_dashboard() );
		exit();
	}

	/**
	 * Enqueue scripts.
	 *
	 * @param $page_now
	 *
	 * @since 0.7.0
	 */
	public function enqueue_scripts( $page_now ) {
		if ( strpos( $page_now, Thim_Dashboard::$prefix_slug . 'dashboard' ) === false ) {
			return;
		}

		wp_enqueue_script( 'thim-theme-update', THIM_CORE_ADMIN_URI . '/assets/js/theme-update.js', array( 'jquery' ), THIM_CORE_VERSION );

		$this->_localize_script();
	}

	/**
	 * Localize script.
	 *
	 * @since 0.7.0
	 */
	private function _localize_script() {
		wp_localize_script( 'thim-theme-update', 'thim_theme_update', array(
			'admin_ajax' => admin_url( 'admin-ajax.php?action=thim_check_update' ),
			'i18l'       => array(
				'check_failed'   => __( 'Check update failed!', 'thim-core' ),
				'can_update'     => __( 'Your theme can update, click "Update" to start.', 'thim-core' ),
				'can_not_update' => __( 'Your theme is the latest version.', 'thim-core' ),
				'wrong'          => __( 'Some thing went wrong. Please try again!', 'thim-core' ),
			)
		) );
	}
}

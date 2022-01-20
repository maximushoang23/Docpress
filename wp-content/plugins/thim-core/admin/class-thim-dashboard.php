<?php

/**
 * Class Thim_Dashboard.
 *
 * @package   Thim_Core
 * @since     0.1.0
 */
class Thim_Dashboard extends Thim_Singleton {
	/**
	 * Do not edit value.
	 *
	 * @since 0.2.0
	 *
	 * @var string
	 */
	private static $main_slug = 'dashboard';

	/**
	 * @var string
	 *
	 * @since 0.2.0
	 */
	public static $prefix_slug = 'thim-';

	/**
	 * List sub pages.
	 *
	 * @since 0.2.0
	 * @var array
	 */
	private static $sub_pages = array();

	/**
	 * @since 0.8.5
	 *
	 * @var null
	 */
	private static $current_key_page = null;

	/**
	 * Check first install.
	 *
	 * @since 0.8.5
	 *
	 * @todo need update
	 */
	public static function check_first_install() {
		return true;
	}

	/**
	 * Get link page by slug.
	 *
	 * @since 0.5.0
	 *
	 * @param $slug
	 *
	 * @return string
	 */
	public static function get_link_page_by_slug( $slug ) {
		if ( ! Thim_Core_Admin::is_my_theme() ) {
			return admin_url();
		}

		return admin_url( 'admin.php?page=' . self::$prefix_slug . $slug );
	}

	/**
	 * Get link main dashboard.
	 *
	 * @since 0.2.0
	 *
	 * @param array $args [key => value] => &key=value
	 *
	 * @return string
	 */
	public static function get_link_main_dashboard( $args = null ) {
		$url = self::get_link_page_by_slug( self::$main_slug );

		if ( is_array( $args ) ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}

	/**
	 * Get key (slug) current page.
	 *
	 * @since 0.3.0
	 */
	public static function get_current_page_key() {
		if ( is_null( self::$current_key_page ) ) {
			$current_page = isset( $_GET['page'] ) ? $_GET['page'] : '';

			$prefix_slug = Thim_Dashboard::$prefix_slug;

			$pages = self::get_sub_pages();
			foreach ( $pages as $key => $page ) {
				if ( $prefix_slug . $key === $current_page ) {
					self::$current_key_page = $key;

					return self::$current_key_page;
				}
			}

			self::$current_key_page = false;
		}

		return self::$current_key_page;
	}

	/**
	 * Check current request is for a page of Thim Core Dashboard interface.
	 *
	 * @since 0.3.0
	 *
	 * @return bool True if inside Thim Core Dashboard interface, false otherwise.
	 */
	public static function is_dashboard() {
		$current_page = self::get_current_page_key();

		return (bool) ( $current_page );
	}

	/**
	 * Set list sub pages.
	 *
	 * @since 0.2.0
	 */
	private static function set_sub_pages() {
		self::$sub_pages = apply_filters( 'thim_dashboard_sub_pages', array() );
	}

	/**
	 * Get list sub pages.
	 *
	 * @since 0.2.0
	 *
	 * @return array
	 */
	public static function get_sub_pages() {
		if ( empty( self::$sub_pages ) ) {
			self::set_sub_pages();
		}

		return self::$sub_pages;
	}

	/**
	 * Add notifications.
	 *
	 * @since 0.3.0
	 *
	 * @param array $args
	 */
	public static function add_notification( $args = array() ) {
		global $thim_dashboard;
		$current_page = $thim_dashboard['current_page'];

		$default = array(
			'content' => '',
			'type'    => 'success',
			'page'    => $current_page,
		);
		$args    = wp_parse_args( $args, $default );

		$page = $args['page'];
		if ( $page !== $current_page ) {
			return;
		}

		$type    = $args['type'];
		$content = $args['content'];
		add_action( 'thim_dashboard_notifications', function () use ( $type, $content ) {

			?>
			<div class="tc-notice tc-<?php echo esc_attr( $type ); ?>">
				<div class="content"><?php echo $content; ?></div>
			</div>
			<?php
		} );
	}

	/**
	 * Get page template.
	 *
	 * @since 0.5.0
	 *
	 * @param $template
	 * @param array $args
	 *
	 * @return bool
	 */
	public static function get_template( $template, $args = array() ) {
		$dir_path = THIM_CORE_ADMIN_PATH . '/views/dashboard/';

		$file = $dir_path . $template;
		if ( ! file_exists( $file ) ) {
			return false;
		}

		include $file;

		return true;
	}

	/**
	 * Thim_Dashboard constructor.
	 *
	 * @since 0.2.0
	 */
	protected function __construct() {
		$this->init();
		$this->init_hooks();
	}

	/**
	 * Init.
	 *
	 * @since 0.2.0
	 */
	private function init() {
		$this->run();
		$this->set_values_global();
	}

	/**
	 * Run.
	 *
	 * @since 0.3.0
	 */
	private function run() {
		Thim_Main_Dashboard::instance();
		Thim_Product_Registration::instance();
		Thim_Getting_Started::instance();
		Thim_Importer::instance();
		Thim_Plugins_Manager::instance();
		Thim_System_Status::instance();
		Thim_For_Developer::instance();
	}

	/**
	 * Set values global.
	 *
	 * @sin 0.3.0
	 */
	private function set_values_global() {
		global $thim_dashboard;

		$thim_dashboard = array(
			'is_active'    => Thim_Product_Registration::is_active(),
			'current_page' => Thim_Dashboard::get_current_page_key(),
		);

		Thim_Theme_Manager::set_metadata();
	}

	/**
	 * Get page template.
	 *
	 * @since 0.2.0
	 *
	 * @param        $template
	 * @param  mixed $args
	 *
	 * @return bool
	 */
	private function get_page_template( $template, $args = array() ) {
		$dir_path = THIM_CORE_ADMIN_PATH . '/views/dashboard/';

		$file = $dir_path . $template;
		if ( ! file_exists( $file ) ) {
			wp_die( "$file not found!" );

			return false;
		}

		include $file;

		return true;
	}

	/**
	 * Init hooks.
	 *
	 * @since 0.2.0
	 */
	private function init_hooks() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_menu', array( $this, 'add_sub_menu_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'thim_dashboard_notifications', array( $this, 'add_notification_requirements' ) );

		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_menu_admin_bar' ), 50 );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );
		add_filter( 'update_footer', array( $this, 'admin_footer_version' ), 999 );
	}

	/**
	 * Add notification requirements.
	 *
	 * @since 0.8.3
	 */
	public function add_notification_requirements() {
		$version_require = '5.3';

		if ( version_compare( phpversion(), $version_require, '>=' ) ) {
			return;
		}

		?>
		<div class="tc-notice tc-error">
			<div class="content">
				<?php printf( __( '<strong>Important:</strong> We found out your system is using PHP version %1$s. Please consider upgrading to version %2$s or higher.', 'thim-core' ), phpversion(), $version_require ); ?>
			</div>
		</div>
		<?php
		exit();
	}

	/**
	 * Filter admin footer version (on the right).
	 *
	 * @since 0.8.5
	 *
	 * @param $msg
	 *
	 * @return string
	 */
	public function admin_footer_version( $msg ) {
		if ( ! self::is_dashboard() ) {
			return $msg;
		}

		return sprintf( __( 'Thim Core Version %s', 'thim-core' ), THIM_CORE_VERSION );
	}

	/**
	 * Filter admin footer text.
	 *
	 * @since 0.8.2
	 *
	 * @param $html
	 *
	 * @return string
	 */
	public function admin_footer_text( $html ) {
		if ( ! self::is_dashboard() ) {
			return $html;
		}

		$text = sprintf( __( 'Thank you for creating with <a href="%s" target="_blank">ThimPress</a>.', 'thim-core' ), __( 'https://thimpress.com' ) );

		return $html = '<span id="footer-thankyou">' . $text . '</span>';
	}

	/**
	 * Add admin bar menu.
	 *
	 * @since 0.5.0
	 *
	 * @param $wp_admin_bar
	 */
	public function add_menu_admin_bar( $wp_admin_bar ) {
		if ( is_admin() ) {
			return;
		}

		global $thim_dashboard;
		$theme_data = $thim_dashboard['theme_data'];
		$theme_name = $theme_data['name'];

		$menu_title = ! empty( $theme_name ) ? $theme_name : __( 'ThimPress Dashboard', 'thim-core' );

		$args = array(
			'id'    => 'thim_core',
			'title' => $menu_title,
			'href'  => self::get_link_main_dashboard()
		);
		$wp_admin_bar->add_node( $args );

		$pages = self::get_sub_pages();
		foreach ( $pages as $key => $page ) {
			$args = array(
				'id'     => self::$prefix_slug . $key,
				'title'  => $page['title'],
				'href'   => self::get_link_page_by_slug( $key ),
				'parent' => 'thim_core'
			);
			$wp_admin_bar->add_node( $args );
		}
	}

	/**
	 * Enqueue scripts.
	 *
	 * @param string $page_now
	 *
	 * @since 0.2.0
	 */
	public function enqueue_scripts( $page_now ) {
		if ( ! self::is_dashboard() ) {
			return;
		}

		wp_enqueue_style( 'thim-dashboard', THIM_CORE_ADMIN_URI . '/assets/css/dashboard.css', array(), THIM_CORE_VERSION );
	}

	/**
	 * Add class to body class in admin.
	 *
	 * @since 0.3.0
	 *
	 * @param $body_classes
	 *
	 * @return string
	 */
	public function admin_body_class( $body_classes ) {
		if ( ! self::is_dashboard() ) {
			return $body_classes;
		}

		$current_page_key = self::get_current_page_key();
		$prefix           = self::$prefix_slug;
		$current_page     = $prefix . $current_page_key;
		$main_page        = $prefix . self::$main_slug;

		$body_classes .= $main_page . ' ' . $current_page . '-wrapper';

		return $body_classes;
	}

	/**
	 * Add menu page (Main page).
	 *
	 * @since 0.2.0
	 */
	public function add_menu_page() {
		global $thim_dashboard;
		$theme_data = $thim_dashboard['theme_data'];
		$theme_name = $theme_data['name'];

		$menu_title = ! empty( $theme_name ) ? $theme_name : __( 'ThimPress Dashboard', 'thim-core' );

		add_menu_page( $menu_title, $menu_title, 'manage_options', self::$prefix_slug . self::$main_slug, array(
			$this,
			'master_template'
		), THIM_CORE_ADMIN_URI . '/assets/images/logo.svg', 2 );

	}

	/**
	 * Add sub menu pages.
	 *
	 * @since 0.2.0
	 */
	public function add_sub_menu_pages() {
		$sub_pages = $this->get_sub_pages();
		$prefix    = Thim_Dashboard::$prefix_slug;

		foreach ( $sub_pages as $key => $page ) {
			$default = array(
				'title'    => '',
				'template' => '',
			);
			$page    = wp_parse_args( $page, $default );

			$slug  = $prefix . $key;
			$title = $page['title'];

			add_submenu_page( self::$prefix_slug . self::$main_slug, $title, $title, 'manage_options', $slug, array( $this, 'master_template' ) );
		}
	}

	/**
	 * Master template.
	 *
	 * @since 0.8.5
	 */
	public function master_template() {
		$this->get_page_template( 'master.php' );
	}
}
<?php

/**
 * Class Thim_Getting_Started
 *
 * @since 0.8.3
 */
class Thim_Getting_Started extends Thim_Admin_Sub_Page {
	/**
	 * @var string
	 *
	 * @since 0.8.5
	 */
	public $key_page = 'getting-started';

	/**
	 * Get steps.
	 *
	 * @since 0.8.3
	 *
	 * @return array
	 */
	public static function get_steps() {
		$steps = array();

		$steps[] = array(
			'key'   => 'welcome',
			'title' => __( 'Welcome to ThimPress', 'thim-core' ),
		);

		$steps[] = array(
			'key'   => 'quick-setup',
			'title' => __( 'Quick setup', 'thim-core' ),
		);

		$steps[] = array(
			'key'   => 'install-plugins',
			'title' => __( 'Install plugins required', 'thim-core' ),
		);

		$steps[] = array(
			'key'   => 'import-demo',
			'title' => __( 'Import demo content', 'thim-core' ),
		);

		$steps[] = array(
			'key'   => 'customizer',
			'title' => __( 'Go to customizer', 'thim-core' ),
		);

		return $steps;
	}

	/**
	 * Thim_Getting_Started constructor.
	 *
	 * @since 0.8.3
	 */
	protected function __construct() {
		parent::__construct();

		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 0.8.3
	 */
	private function init_hooks() {
		add_action( 'after_thim_dashboard_wrapper', array( $this, 'add_modals_importer' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'thim_getting_started_main_content', array( $this, 'render_step_templates' ) );
		add_action( 'wp_ajax_thim-get-started', array( $this, 'handle_ajax' ) );
		add_filter( 'thim_dashboard_sub_pages', array( $this, 'add_sub_page' ) );
	}

	/**
	 * Add modals importer.
	 *
	 * @since 0.8.5
	 */
	public function add_modals_importer() {
		if ( ! $this->is_myself() ) {
			return;
		}

		Thim_Dashboard::get_template( 'partials/importer-modal.php' );
		Thim_Dashboard::get_template( 'partials/importer-uninstall-modal.php' );
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
		$sub_pages['getting-started'] = array(
			'title' => __( 'Getting Started', 'thim-core' ),
		);

		return $sub_pages;
	}

	/**
	 * Enqueue scripts.
	 *
	 * @param $page_now
	 *
	 * @since 0.8.3
	 */
	public function enqueue_scripts( $page_now ) {
		if ( ! $this->is_myself() ) {
			return;
		}

		wp_enqueue_script( 'thim-plugins', THIM_CORE_ADMIN_URI . '/assets/js/plugins/thim-plugins.js', array( 'jquery' ) );
		wp_enqueue_script( 'thim-getting-started', THIM_CORE_ADMIN_URI . '/assets/js/getting-started.js', array( 'thim-plugins' ) );
		wp_enqueue_script( 'thim-importer', THIM_CORE_ADMIN_URI . '/assets/js/importer/thim-importer.js', array( 'jquery' ) );

		$this->localize_script();
	}

	/**
	 * Localize script.
	 *
	 * @since 0.8.3
	 */
	private function localize_script() {
		wp_localize_script( 'thim-getting-started', 'thim_gs', array(
			'url_ajax' => admin_url( 'admin-ajax.php?action=thim-get-started&step=' ),
		) );

		$thim_plugins_manager = Thim_Plugins_Manager::instance();
		$thim_plugins_manager->localize_script();

		$thim_importer = Thim_Importer::instance();
		$thim_importer->localize_script();
	}

	/**
	 * Handle ajax.
	 *
	 * @since 0.8.3
	 */
	public function handle_ajax() {
		$step = ! empty( $_REQUEST['step'] ) ? $_REQUEST['step'] : false;

		switch ( $step ) {
			case 'quick-setup':
				$this->handle_quick_setup();
				break;

			case 'abc':
				break;

			default:
				break;
		}

		wp_die();
	}

	/**
	 * Handle quick setup.
	 *
	 * @since 0.8.3
	 */
	private function handle_quick_setup() {
		$blog_name = isset( $_POST['blogname'] ) ? $_POST['blogname'] : false;
		if ( $blog_name !== false ) {
			update_option( 'blogname', $blog_name );
		}

		$blog_description = isset( $_POST['blogdescription'] ) ? $_POST['blogdescription'] : false;
		if ( $blog_description !== false ) {
			update_option( 'blogdescription', $blog_description );
		}

		wp_send_json_success( 'Saving successful!' );
	}

	/**
	 * Render step templates.
	 *
	 * @since 0.8.3
	 */
	public function render_step_templates() {
		$steps = self::get_steps();

		foreach ( $steps as $index => $step ) {
			$key               = strtolower( $step['key'] );
			$key               = str_replace( '-', '_', $key );
			$callback_function = 'content_step_' . $key;
			$arr               = array( $this, $callback_function );

			if ( ! is_callable( $arr ) ) {
				continue;
			}

			call_user_func( $arr, $index );
		}
	}

	/**
	 * Get step template by slug.
	 *
	 * @since 0.8.3
	 *
	 * @param $slug
	 * @param array $args
	 *
	 * @return bool
	 */
	public function render_step_template( $slug, $args = array() ) {
		$dir_path = THIM_CORE_ADMIN_PATH . '/views/dashboard/gs-steps/';

		$path = $dir_path . $slug . '.php';
		if ( ! file_exists( $path ) ) {
			return false;
		}

		ob_start();
		include $path;
		$html = ob_get_clean();

		include $dir_path . 'master.php';

		return true;
	}

	/**
	 * Step welcome.
	 *
	 * @since 0.8.3
	 */
	public function content_step_welcome() {
		$this->render_step_template( 'welcome' );
	}

	/**
	 * Step welcome.
	 *
	 * @since 0.8.3
	 */
	public function content_step_quick_setup() {
		$this->render_step_template( 'quick-setup' );
	}

	/**
	 * Step welcome.
	 *
	 * @since 0.8.3
	 */
	public function content_step_install_plugins() {
		$this->render_step_template( 'install-plugins' );
	}

	/**
	 * Step welcome.
	 *
	 * @since 0.8.3
	 */
	public function content_step_import_demo() {
		$this->render_step_template( 'import-demo' );
	}

	/**
	 * Step welcome.
	 *
	 * @since 0.8.3
	 */
	public function content_step_customizer() {
		$this->render_step_template( 'customizer' );
	}
}
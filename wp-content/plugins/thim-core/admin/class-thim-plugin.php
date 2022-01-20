<?php

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Class Thim_Plugin.
 *
 * @since 0.4.0
 */
class Thim_Plugin {
	/**
	 * @var string
	 */
	private $slug = '';

	/**
	 * @var string
	 */
	private $plugin = '';

	/**
	 * @var null
	 */
	private $info = null;

	/**
	 * @var array
	 */
	private $args = array();

	/**
	 * @var bool
	 */
	private $is_wporg = false;

	/**
	 * @var array
	 */
	private $messages = array();


	/**
	 * Thim_Plugin constructor.
	 *
	 * @since 0.4.0ß
	 *
	 * @param string $slug
	 */
	public function __construct( $slug = '' ) {
		$this->slug = strtolower( $slug );

		if ( ! empty( $this->slug ) ) {
			$this->set_plugin_file();
		}
	}


	/**
	 * Set plugin file.
	 *
	 * @since 0.4.0
	 *
	 * @return bool
	 */
	public function set_plugin_file() {
		$plugins_installed = get_plugins();

		if ( ! count( $plugins_installed ) ) {
			return false;
		}

		foreach ( $plugins_installed as $key => $value ) {
			if ( strpos( $key, $this->slug . '/' ) !== false ) {

				$this->plugin = $key;

				return true;
			}
		}

		return false;
	}

	/**
	 * Set args.
	 *
	 * @since 0.4.0
	 *
	 * @param array $args
	 */
	public function set_args( array $args ) {
		$default    = array(
			'name' => '',
			'slug' => '',
		);
		$this->args = wp_parse_args( $args, $default );

		$source = isset( $args['source'] ) ? $args['source'] : false;

		if ( ! $source || $source === 'repo' ) {
			$this->is_wporg = true;
		}

		$this->slug = $this->args['slug'];
		$this->set_plugin_file();
	}

	/**
	 * Install plugin.
	 *
	 * @since 0.4.0
	 *
	 * @return bool
	 */
	public function install() {
		$status = $this->get_status();

		if ( $status !== 'not_installed' ) {
			return false;
		}

		if ( $this->is_wporg ) {
			return $this->install_form_wporg();
		}

		$source = $this->args['source'];

		return $this->install_by_local_file( $source );
	}

	/**
	 * Get messages.
	 *
	 * @since 0.8.4
	 *
	 * @return array
	 */
	public function get_messages() {
		return $this->messages;
	}

	/**
	 * Get plugin file. Ex: thim-core/thim-core.php
	 *
	 * @since 0.4.0
	 *
	 * @return string
	 */
	public function get_plugin_file() {
		return $this->plugin;
	}

	/**
	 * Get is active.
	 *
	 * @since 0.4.0
	 *
	 * @return bool
	 */
	public function is_active() {
		$is_active = is_plugin_active( $this->plugin );

		return $is_active;
	}

	/**
	 * Get slug plugin.
	 *
	 * @since 0.4.0
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Get name plugin.
	 *
	 * @since 0.5.0
	 *
	 * @return bool|mixed
	 */
	public function get_name() {
		$args = $this->args;
		$name = ! empty( $args['name'] ) ? $args['name'] : false;

		return $name;
	}

	/**
	 * Get plugin status
	 *
	 * @since 0.4.0
	 *
	 * @return string
	 */
	public function get_status() {
		if ( empty( $this->plugin ) ) {
			return 'not_installed';
		}

		$file_plugin = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->plugin;

		if ( ! file_exists( $file_plugin ) ) {
			return 'not_installed';
		}

		$is_active = $this->is_active();
		if ( ! $is_active ) {
			return 'inactive';
		}

		return 'active';
	}

	/**
	 * Get text status.
	 *
	 * @since 0.8.5
	 *
	 * @return mixed|string
	 */
	public function get_text_status() {
		$arrText = array(
			'active'        => __( 'Active', 'thim-core' ),
			'inactive'      => __( 'Inactive', 'thim-core' ),
			'not_installed' => __( 'Not Installed', 'thim-core' ),
		);

		$status = $this->get_status();

		if ( isset( $arrText[ $status ] ) ) {
			return $arrText[ $status ];
		}

		return '';
	}

	/**
	 * Get url icon plugin.
	 *
	 * @since 0.5.0
	 *
	 * @return bool|string
	 */
	public function get_icon() {
		$args = $this->args;
		$icon = ! empty( $args['icon'] ) ? $args['icon'] : false;

		return $icon;
	}

	/**
	 * Get array args.
	 *
	 * @since 0.5.0
	 *
	 * @return array
	 */
	public function toArray() {
		return array(
			'slug'   => $this->get_slug(),
			'name'   => $this->get_name(),
			'status' => $this->get_status()
		);
	}

	/**
	 * Activate plugin.
	 *
	 * @since 0.4.0
	 *
	 * @param bool $silent
	 *
	 * @return bool
	 */
	public function activate( $silent = false ) {
		$status = $this->get_status();

		if ( $status == 'active' || $status == 'not_installed' ) {
			return false;
		}

		$plugin = $this->plugin;

		$result = activate_plugin( $plugin, $redirect = '', $network_wide = false, $silent );

		$recent = (array) get_option( 'recently_activated' );
		unset( $recent[ $plugin ] );
		update_option( 'recently_activated', $recent );

		if ( is_wp_error( $result ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Deactivate plugin.
	 *
	 * @since 0.4.0
	 *
	 * @return bool
	 */
	public function deactivate() {
		$plugin = $this->plugin;
		deactivate_plugins( $plugin );

		update_option( 'recently_activated', array( $plugin => time() ) + (array) get_option( 'recently_activated' ) );

		return true;
	}

	/**
	 * Get plugin is form wporg.
	 *
	 * @since 0.4.0
	 *
	 * @return bool
	 */
	public function is_wporg() {
		return $this->is_wporg;
	}

	/**
	 * Get info plugin.
	 *
	 * @since 0.4.0
	 *
	 * @return array|bool
	 */
	public function get_info() {
		if ( empty( $this->plugin ) ) {
			return false;
		}

		$plugin_file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->plugin;

		if ( ! file_exists( $plugin_file ) ) {
			return false;
		}

		return get_plugin_data( $plugin_file );
	}

	/**
	 * Install plugin from wp.org
	 *
	 * @since 0.4.0
	 *
	 * @return bool
	 */
	public function install_form_wporg() {
		$info = $this->get_info_wporg();

		if ( ! $info ) {
			return false;
		}

		$download_link = $info['download_link'];

		$install = $this->wp_install( $download_link );

		return $install;
	}

	/**
	 * Install plugin by zip file.
	 *
	 * @since 0.4.0
	 *
	 * @param $file_path
	 *
	 * @return bool
	 */
	public function install_by_local_file( $file_path ) {
		return $install = $this->wp_install( $file_path );
	}

	/**
	 * Get info plugin from wporg.
	 *
	 * @since 0.4.0
	 *
	 * @return array|bool
	 */
	public function get_info_wporg() {
		if ( ! $this->is_wporg() ) {
			return false;
		}

		if ( $this->info ) {
			return $this->info;
		}

		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		$api = plugins_api( 'plugin_information', array(
			'slug' => $this->slug,
		) );

		if ( is_wp_error( $api ) ) {
			return false;
		}

		$this->info = (array) $api;

		return $this->get_info_wporg();
	}

	/**
	 * Install plugin by uri or local path.
	 *
	 * @since 0.8.4
	 *
	 * @param $package
	 *
	 * @return bool
	 */
	public function wp_install( $package ) {
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		$skin            = new WP_Ajax_Upgrader_Skin();
		$plugin_upgrader = new Plugin_Upgrader( $skin );
		$result          = $plugin_upgrader->install( $package );
		$messages        = $skin->get_upgrade_messages();

		$this->messages = $messages;

		if ( is_wp_error( $result ) ) {
			return false;
		}

		return (bool) $result;
	}

	/**
	 * Update plugin.
	 *
	 * @since 0.8.4
	 *
	 * @param $plugin
	 *
	 * @return bool
	 */
	public function update( $plugin ) {
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		wp_update_plugins();

		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$result   = $upgrader->bulk_upgrade( array( $plugin ) );

		$this->messages = $skin->get_upgrade_messages();

		if ( is_wp_error( $result ) ) {
			return false;
		}

		return (bool) $result;
	}
}
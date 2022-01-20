<?php

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
include_once( ABSPATH . 'wp-admin/includes/file.php' );
include_once( ABSPATH . 'wp-includes/pluggable.php' );
WP_Filesystem();

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

		return self::install_by_zip_file( $source );
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
	 * Activate plugin.
	 *
	 * @since 0.4.0
	 *
	 * @param bool $silent
	 *
	 * @return bool
	 */
	public function activate( $silent = true ) {
		$status = $this->get_status();

		if ( $status === 'active' ) {
			return false;
		}

		$activate = activate_plugin( $this->plugin, '', false, $silent );

		if ( is_wp_error( $activate ) ) {
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
		deactivate_plugins( $this->plugin );

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

		$file = $this->download_package( $download_link );
		if ( ! $file ) {
			return false;
		}

		$unpack = $this->unpack_package( $file );

		return $unpack;
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
	public static function install_by_zip_file( $file_path ) {
		$result = unzip_file( $file_path, WP_PLUGIN_DIR );

		if ( $result !== true ) {
			return false;
		}

		return true;
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
	 *
	 * Download package by url.
	 *
	 * @since 0.4.0
	 *
	 * @param $url
	 *
	 * @return bool|mixed
	 */
	private function download_package( $url ) {
		$file = download_url( $url );

		if ( is_wp_error( $file ) ) {
			return false;
		}

		return $file;
	}

	/**
	 * Unpack package.
	 *
	 * @since 0.4.0
	 *
	 * @param $path
	 *
	 * @return bool
	 */
	private function unpack_package( $path ) {
		$result = unzip_file( $path, WP_PLUGIN_DIR );
		@unlink( $path );

		if ( $result !== true ) {
			return false;
		}

		return true;
	}
}
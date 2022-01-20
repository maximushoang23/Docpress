<?php

/**
 * Class Thim_Theme_Manager.
 *
 * @since 0.7.0
 *
 * @package Thim_Core_Admin
 */
class Thim_Theme_Manager {
	/**
	 * Set theme data.
	 *
	 * @since 0.3.0
	 */
	public static function set_metadata() {
		global $thim_dashboard;

		$wp_theme = wp_get_theme();
		$theme    = array(
			'name'           => $wp_theme->get( 'Name' ),
			'description'    => $wp_theme->get( 'Description' ),
			'version'        => $wp_theme->get( 'Version' ),
			'author'         => $wp_theme->get( 'Author' ),
			'text_domain'    => $wp_theme->get( 'TextDomain' ),
			'stylesheet'     => $wp_theme->get_stylesheet(),
			'changelog_file' => false,
		);

		/**
		 * Latest version.
		 */
		$theme['latest_version'] = self::get_latest_version();

		/**
		 * Set purchase link.
		 */
		$purchase_link          = apply_filters( 'thim_envato_purchase_link', '#' );
		$theme['purchase_link'] = $purchase_link;

		/**
		 * Changelog file
		 */
		$changelog_file = get_template_directory() . '/changelog.html';
		$changelog_file = apply_filters( 'thim_theme_changelog_file', $changelog_file );
		if ( file_exists( $changelog_file ) ) {
			$theme['changelog_file'] = $changelog_file;
		}

		/**
		 * Documentation links
		 */
		$links_default = array(
			'docs'      => '#',
			'knowledge' => 'https://thimpress.com/knowledge-base/',
			'support'   => 'https://thimpress.com/forums/',
		);

		$links          = apply_filters( 'thim_theme_links_guide_user', array() );
		$links          = wp_parse_args( $links, $links_default );
		$theme['links'] = $links;

		$thim_dashboard['theme_data'] = $theme;
	}

	/**
	 * Get theme metadata.
	 *
	 * @since 0.7.0
	 *
	 * @return bool
	 */
	public static function get_metadata() {
		global $thim_dashboard;

		$theme_data = isset( $thim_dashboard['theme_data'] ) ? $thim_dashboard['theme_data'] : false;
		if ( ! $theme_data ) {
			self::set_metadata();

			return self::get_metadata();
		}

		return $theme_data;
	}

	/**
	 * Get current theme version.
	 *
	 * @since 0.8.0
	 *
	 * @return string
	 */
	public static function get_current_version() {
		$theme_data = self::get_metadata();

		return $theme_data['version'];
	}

	/**
	 * Get latest version.
	 *
	 * @since 0.7.0
	 *
	 * @return string|bool
	 */
	public static function get_latest_version() {
		return get_theme_mod( 'thim_latest_version', false );
	}

	/**
	 * Update latest version for theme.
	 *
	 * @since 0.7.0
	 *
	 * @param $new_version
	 */
	public static function update_latest_version( $new_version ) {
		set_theme_mod( 'thim_latest_version', $new_version );
		self::refresh_metadata();
	}

	/**
	 * Refresh metadata.
	 *
	 * @since 0.8.1
	 */
	public static function refresh_metadata() {
		self::set_metadata();
	}
}
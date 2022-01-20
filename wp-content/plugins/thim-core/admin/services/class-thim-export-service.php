<?php

/**
 * Class Thim_Export_Service.
 *
 * @since 0.5.0
 */
class Thim_Export_Service {
	/**
	 * Send file download.
	 *
	 * @since 0.5.0
	 *
	 * @param        $content
	 * @param string $file_name
	 * @param string $type
	 */
	private static function _send_download_file( $content, $file_name = 'test.dat', $type = 'text/plain' ) {
		header( "Content-type: $type" );
		header( "Content-Disposition: attachment; filename=$file_name" );

		echo $content;
		die();
	}

	/**
	 * Export file settings.dat
	 *
	 * @since 0.5.0
	 */
	public static function settings() {
		$options = array();

		/**
		 * Export basic settings.
		 */
		$basic_settings = Thim_Importer_Service::get_key_basic_settings();
		foreach ( $basic_settings as $basic_setting ) {
			$options[ $basic_setting ] = get_option( $basic_setting );
		}

		/**
		 * Convert page id settings to page slug settings.
		 */
		$settings_key = Thim_Importer_Service::get_key_page_id_settings();
		foreach ( $settings_key as $key ) {
			$page_id = get_option( $key );
			if ( ! empty( $page_id ) ) {
				$path = get_page_uri( $page_id );

				if ( ! empty( $path ) ) {
					$options[ $key ] = $path;
				}
			}
		}

		$text = serialize( $options );

		self::_send_download_file( $text, 'settings.dat', 'text/plain' );
	}

	/**
	 * Export file theme options
	 *
	 * @since 0.5.0
	 */
	public static function theme_options() {
		$theme_options = get_theme_mods();
		$text          = serialize( $theme_options );

		self::_send_download_file( $text, 'theme_options.dat', 'text/plain' );
	}
}
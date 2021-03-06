<?php

/**
 * Class Thim_Importer_Service.
 *
 * @package   Thim_Core_Admin
 * @since     0.3.1
 */
class Thim_Importer_Service {
	/**
	 * Validate file.
	 *
	 * @since 0.3.1
	 *
	 * @param $file
	 *
	 * @throws Thim_Error
	 *
	 * @return bool
	 */
	private static function _file_validate( $file ) {
		if ( ! file_exists( $file ) ) {
			throw Thim_Error::create( 'File not found!', 5, 'Please check the existence of file <code>' . $file . '</code>' );
		}

		if ( ! is_readable( $file ) ) {
			throw Thim_Error::create( 'File is not readable!', 6, 'Please check the permission of file <code>' . $file . '</code>' );
		}

		return true;
	}

	/**
	 * Get file contents.
	 *
	 * @since 0.3.1
	 *
	 * @param $file
	 *
	 * @return string
	 * @throws Thim_Error
	 */
	private static function _file_get_contents( $file ) {
		self::_file_validate( $file );

		$contents = file_get_contents( $file );
		if ( ! $contents ) {
			throw Thim_Error::create( 'Get file content failed!', 6, 'Please check file <code>' . $file . '</code>' );
		}

		return $contents;
	}

	/**
	 * Import main content and replace all image by image demo.
	 *
	 * @since 0.3.2
	 *
	 * @param $xml_file
	 *
	 * @return bool
	 */
	public static function main_content_and_replace_image_demo( $xml_file ) {
		self::_file_validate( $xml_file );

		ob_start();
		$thim_wp_import = new Thim_WP_Import_Service();
		$thim_wp_import->import_no_image( $xml_file );
		ob_get_clean();

		return true;
	}

	/**
	 * Import main content
	 *
	 * @since 0.4.0
	 *
	 * @param $xml_file
	 *
	 * @return bool
	 * @throws Thim_Error
	 */
	public static function main_content( $xml_file ) {
		self::_file_validate( $xml_file );

		ob_start();
		$thim_wp_import = new Thim_WP_Import_Service();
		/***/
		$thim_wp_import->import_main_content( $xml_file );

		/*******/
		ob_get_clean();

		return true;
	}

	/**
	 * Import media file.
	 *
	 * @since 0.3.1
	 *
	 * @param $xml_file
	 *
	 * @return bool
	 * @throws Thim_Error
	 */
	public static function media( $xml_file ) {
		self::_file_validate( $xml_file );

		ob_start();
		$thim_wp_import = new Thim_WP_Import_Service();
		$thim_wp_import->import_media( $xml_file );
		ob_get_clean();

		return true;
	}

	/**
	 * Import theme options.
	 *
	 * @since 0.3.1
	 *
	 * @param $options_file
	 *
	 * @return bool
	 * @throws Thim_Error
	 */
	public static function theme_options( $options_file ) {
		$content = self::_file_get_contents( $options_file );

		$options = maybe_unserialize( $content );
		if ( ! $options || is_string( $options ) ) {
			throw Thim_Error::create( 'Decode file failed!', 7, 'Please check file <code>' . $options_file . '</code>' );
		}

		$theme = get_option( 'stylesheet' );
		// Get current theme mods
		$theme_mods = get_option( "theme_mods_{$theme}" );

		// Mergers new options and clean
		if ( $theme_mods ) {
			// Update to a new option so we can restore after that
			update_option( "theme_mods_backup_$theme", $theme_mods );

			$theme_mods = array_merge( $theme_mods, $options );
		} else {
			$theme_mods = $options;
		}
		$theme_mods = thim_clean_theme_mods( $theme_mods );

		// Update theme mods
		update_option( "theme_mods_$theme", $theme_mods );

		return true;
	}

	/**
	 * Analyze content for current demo
	 */
	public static function analyze_content() {
		$thim_wp_import = new Thim_WP_Import_Service( true );
		$thim_wp_import->analyze_content();
	}

	/**
	 * Import widgets
	 *
	 * @param $widget_file
	 * @param $widget_logic
	 *
	 * @since 0.4.0
	 *
	 * @return bool
	 * @throws Thim_Error
	 */
	public static function widget( $widget_file, $widget_logic ) {
		$json_data = self::_file_get_contents( $widget_file );
		$json_data = json_decode( $json_data, true );

		if ( empty( $json_data ) || ! is_array( $json_data ) ) {
			throw Thim_Error::create( 'Import data could not be read.', 6, 'Please check file <code>' . $widget_file . '</code>' );
		}

		$thim_widget_importer = new Thim_Widget_Importer_Service();
		$thim_widget_importer->import( $json_data, $widget_logic, true );

		return true;
	}

	/**
	 * Import Slider Revolution.
	 *
	 * @param $folder_path
	 *
	 * @since 0.4.0
	 * @return bool
	 * @throws Thim_Error
	 */
	public static function revslider( $folder_path ) {

		if ( ! is_dir( $folder_path ) ) {
			throw Thim_Error::create( 'Slider data not found.', 5, 'Please check folder <code>' . $folder_path . '</code>' );
		}

		// Get all slider file in folder revslider
		if ( $dir = @opendir( $folder_path ) ) {
			while ( false !== ( $file = readdir( $dir ) ) ) {
				if ( $file != "." && $file != ".." ) {
					$_FILES['import_file']['tmp_name'] = $folder_path . $file;
					if ( class_exists( 'RevSlider' ) ) {
						$slider   = new RevSlider();
						$response = $slider->importSliderFromPost( true, true );
						if ( ! $response['success'] ) {
							throw Thim_Error::create( 'Import slider fail', 0, '' );
						}
					}
				}
			}
			@closedir( $dir );
		} else {
			throw Thim_Error::create( 'Import data could not be read.', 6, 'Please check folder <code>' . $folder_path . '</code>' );
		}

		return true;
	}

	/**
	 * Update site settings.
	 *
	 * @since 0.5.0
	 *
	 * @param $settings_file
	 */
	public static function settings( $settings_file ) {
		$text     = self::_file_get_contents( $settings_file );
		$settings = maybe_unserialize( $text );

		if ( ! is_array( $settings ) ) {
			return;
		}

		/**
		 * Update basic settings.
		 */
		$basic_settings = Thim_Importer_Service::get_key_basic_settings();
		foreach ( $basic_settings as $basic_setting ) {
			if ( isset( $settings[ $basic_setting ] ) ) {
				update_option( $basic_setting, $settings[ $basic_setting ] );
			}
		}

		/**
		 * Mapping settings page slug to page id.
		 */
		$settings_key = Thim_Importer_Service::get_key_page_id_settings();
		foreach ( $settings_key as $key ) {
			$page_slug = isset( $settings[ $key ] ) ? $settings[ $key ] : false;

			$page_id = thim_get_page_id_by_path( $page_slug );
			if ( $page_id ) {
				update_option( $key, $page_id );
			}
		}
	}

	/**
	 * Reset demo data.
	 *
	 * @since 0.6.0
	 *
	 * @return bool
	 */
	public static function reset_data_demo() {
		$thim_wp_import = new Thim_WP_Import_Service();
		$thim_wp_import->clean_demo_content();

		/**
		 * Remove all widgets.
		 */
		Thim_Widget_Importer_Service::remove_all();

		/**
		 * Reset show on front page.
		 */
		update_option( 'show_on_front', 'posts' );

		/**
		 * Backup theme mods.
		 */
		$theme             = get_option( 'stylesheet' );
		$theme_mods_backup = get_option( "theme_mods_backup_{$theme}" );
		thim_clean_theme_mods( $theme_mods_backup );
		update_option( "theme_mods_$theme", $theme_mods_backup );

		Thim_Importer::update_key_demo_installed( false );

		return true;
	}

	/**
	 * Get key page id settings.
	 *
	 * @since 0.8.2
	 *
	 * @return array
	 */
	public static function get_key_page_id_settings() {
		$list = array(
			'woocommerce_shop_page_id',
			'woocommerce_cart_page_id',
			'woocommerce_checkout_page_id',
			'woocommerce_terms_page_id',
			'yith_wcwl_wishlist_page_id',
			'page_for_posts',
			'page_on_front',
		);

		return apply_filters( 'thim_importer_page_id_settings', $list );
	}

	/**
	 * Get key basic settings.
	 *
	 * @since 0.8.2
	 *
	 * @return array
	 */
	public static function get_key_basic_settings() {
		$list = array(
			'show_on_front',
			'megamenu_settings',
			'sb_instagram_settings',
		);

		return apply_filters( 'thim_importer_basic_settings', $list );
	}

}
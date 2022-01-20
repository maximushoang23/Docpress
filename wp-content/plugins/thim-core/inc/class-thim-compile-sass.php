<?php

/**
 * Class Thim_Compile_SASS
 *
 * @package   Thim_Core
 * @since     0.1.0
 */

use Leafo\ScssPhp\Compiler;

class Thim_Compile_SASS extends Thim_Singleton {
	/**
	 * Thim_Compile_SASS constructor.
	 *
	 * @since 0.1.0
	 */
	protected function __construct() {
		$this->init();
		$this->init_hooks();
	}

	/**
	 * Init class.
	 *
	 * @since 0.1.0
	 */
	private function init() {
		$this->libraries();
	}

	/**
	 * Init hooks.
	 *
	 * @since 0.1.0
	 */
	private function init_hooks() {

	}

	/**
	 * Includes libraries.
	 *
	 * @since 0.1.0
	 */
	private function libraries() {
		require_once THIM_CORE_INC_PATH . '/includes/sass/scss.inc.php';
	}

	/**
	 * Get css from scss.
	 *
	 * @param array $scss_config
	 *
	 * @return bool|string
	 * @since 0.1.0
	 */
	private function get_css( array $scss_config ) {
		try {
			$dir  = $scss_config['dir'];
			$name = $scss_config['name'];

			if ( ! file_exists( trailingslashit( $dir ) . $name ) ) {
				Thim_Core_Customizer::message_customize_error( 'File ' . $name . ' not exist!' );

				return false;
			}

			$scss = new Compiler();
			$scss->setImportPaths( $dir );
			$scss->setFormatter( 'Leafo\ScssPhp\Formatter\Compressed' );

			$variables_sass = Thim_Core_Customizer::get_sass_variables();
			$scss->setVariables( $variables_sass );

			$custom_css = trim( get_theme_mod( 'custom_css_field', '' ) );
			$css        = $scss->compile( '@import "' . $name . '"; ' . $custom_css . '' );

			return $css;
		} catch ( Exception $e ) {
			Thim_Core_Customizer::message_customize_error( $e->getMessage() );

			return false;
		}
	}

	/**
	 * Save file stylesheet.
	 *
	 * @param $file_name
	 * @param $content
	 *
	 * @return string
	 * @throws Exception
	 * @since 0.1.0
	 */
	private function save_file_theme_options( $file_name, $content ) {
		/**
		 * Get uploads dir.
		 */
		$wp_upload_dir = wp_upload_dir();
		$path_uploads  = $wp_upload_dir['basedir'];
		$uri_uploads   = $wp_upload_dir['baseurl'];

		/**
		 * Put file.
		 */
		self::put_file( $path_uploads, $file_name, $content );

		/**
		 * Return uri file.
		 */
		return trailingslashit( $uri_uploads ) . $file_name;
	}

	/**
	 * Compile scss to css.
	 *
	 * @param array $scss_config
	 *
	 * @return bool|void
	 * @since 0.1.0
	 */
	public function compile_scss( array $scss_config ) {
		if ( TP::is_debug() ) {
			try {
				$this->put_file_variables_scss( $scss_config['dir'] );
			} catch ( Exception $e ) {
				Thim_Core_Customizer::message_customize( 'Put file variables SCSS error!' . '(' . $e->getMessage() . ')' );
			}
		}

		try {
			$css = $this->get_css( $scss_config );
			if ( ! $css ) {
				return false;
			}

			$key_theme      = $this->get_key_theme();
			$uri_stylesheet = $this->save_file_theme_options( $key_theme . '.css', $css );
			$this->update_stylesheet_uri( $uri_stylesheet );

			return true;
		} catch ( Exception $e ) {
			return Thim_Core_Customizer::message_customize_error( $e->getMessage() );
		}
	}

	/**
	 * Get key theme.
	 *
	 * @return string
	 *
	 * @since 0.1.0
	 */
	private function get_key_theme() {
		$current_theme = wp_get_theme();
		$key_theme     = $current_theme->get( 'TextDomain' );
		if ( ! $key_theme ) {
			$key_theme = $current_theme->get_stylesheet();
		}

		if ( empty( $key_theme ) ) {
			$key_theme = 'thim-options';
		}

		return $key_theme;
	}

	/**
	 * Put file variables scss.
	 *
	 * @param $dir
	 *
	 * @since 0.1.0
	 */
	private function put_file_variables_scss( $dir ) {
		$variables = Thim_Core_Customizer::get_sass_variables();
		if ( empty( $variables ) ) {
			return;
		}

		$scss = '';
		foreach ( $variables as $key => $value ) {
			$variable = '$' . $key;

			$scss .= $variable . ':' . $value . ";\n";
		}

		self::put_file( $dir, '_thim_customize.scss', $scss );
	}

	/**
	 * Put file content.
	 *
	 * @param $dir
	 * @param $file_name
	 * @param $content
	 *
	 * @return bool
	 * @throws Exception
	 * @since 0.1.0
	 */
	private static function put_file( $dir, $file_name, $content ) {
		/**
		 * Call $wp_filesystem
		 */
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		/**
		 * Directory didn't exist, so let's create it.
		 */
		if ( ! $wp_filesystem->is_dir( $dir ) ) {
			$wp_filesystem->mkdir( $dir );
		}

		if ( ! wp_is_writable( $dir ) ) {
			throw new Exception( 'Can not write in directory ' . $dir );
		}

		$put_file = $wp_filesystem->put_contents(
			trailingslashit( $dir ) . $file_name,
			$content,
			FS_CHMOD_FILE
		);

		if ( ! $put_file ) {
			throw new Exception( 'Put file error!' );
		}

		return true;
	}

	/**
	 * Update uri stylesheet.
	 *
	 * @param string $uri
	 * @param bool   $refresh
	 *
	 * @since 0.1.0
	 */
	public function update_stylesheet_uri( $uri, $refresh = true ) {
		if ( $refresh ) {
			$uri = $uri . '?thim=' . md5( time() );
		}

		set_theme_mod( Thim_Core_Customizer::$key_stylesheet_uri, $uri );
	}
}
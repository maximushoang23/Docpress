<?php
/**
 * Theme functions and definitions.
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 */

define( 'THIM_DIR', trailingslashit( get_template_directory() ) );
define( 'THIM_URI', trailingslashit( get_template_directory_uri() ) );
define( 'THIM_THEME_VERSION', '1.1' );

if ( ! function_exists( 'thim_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function thim_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on this theme, use a find and replace
		 * to change 'thim-docspress' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'thim-docspress', THIM_DIR . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Add support Woocommerce
		add_theme_support( 'woocommerce' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'primary' => esc_html__( 'Primary Menu', 'thim-docspress' ),
		) );

		if ( get_theme_mod( 'copyright_menu', true ) ) {
			register_nav_menus( array(
				'copyright_menu' => esc_html__( 'Copyright Menu', 'thim-docspress' ),
			) );
		}

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		/*
		 * Enable support for Post Formats.
		 * See https://developer.wordpress.org/themes/functionality/post-formats/
		 */
		add_theme_support( 'post-formats', array(
				'aside',
				'image',
				'video',
				'audio',
				'quote',
				'link',
				'gallery',
				'chat',
			) );

		add_theme_support( 'custom-background' );

		add_editor_style();

	}
endif;
add_action( 'after_setup_theme', 'thim_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function thim_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'thim_content_width', 640 );
}

add_action( 'after_setup_theme', 'thim_content_width', 0 );


/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function thim_widgets_init() {
	$thim_options = get_theme_mods();

	/**
	 * Sidebar for module Topbar
	 */
	if ( get_theme_mod( 'header_topbar_display', true ) ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Topbar Left', 'thim-docspress' ),
			'id'            => 'topbar_left',
			'description'   => esc_html__( 'Display in topbar left.', 'thim-docspress' ),
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget'  => '</li>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
		register_sidebar( array(
			'name'          => esc_html__( 'Topbar Right', 'thim-docspress' ),
			'id'            => 'topbar_right',
			'description'   => esc_html__( 'Display in topbar right.', 'thim-docspress' ),
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget'  => '</li>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}

	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'thim-docspress' ),
		'id'            => 'sidebar',
		'description'   => esc_html__( 'Appears in the Sidebar section of the site.', 'thim-docspress' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	if ( isset( $thim_options['footer_columns'] ) ) {
		$footer_columns = (int) $thim_options['footer_columns'];
		for ( $i = 1; $i <= $footer_columns; $i ++ ) {
			register_sidebar( array(
				'name'          => sprintf( 'Footer Sidebar %s', $i ),
				'id'            => 'footer-sidebar-' . $i,
				'description'   => esc_html__( 'Sidebar display widgets.', 'thim-docspress' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			) );
		}
	}

	/**
	 * Not remove
	 * Function create sidebar on wp-admin.
	 */
	$sidebars = apply_filters( 'thim_core_list_sidebar', array() );
	if ( count( $sidebars ) > 0 ) {
		foreach ( $sidebars as $sidebar ) {
			$new_sidebar = array(
				'name'          => $sidebar['name'],
				'id'            => $sidebar['id'],
				'description'   => '',
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			);

			register_sidebar( $new_sidebar );
		}
	}

}

add_action( 'widgets_init', 'thim_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function thim_scripts() {
	global $current_blog, $wp_locale;
	wp_enqueue_style( 'thim-style-custom', get_template_directory_uri() . '/assets/custom/custom.css');
	//	Style
	if ( is_multisite() ) {
		if ( file_exists( THIM_DIR . 'style-' . $current_blog->blog_id . '.css' ) ) {
			wp_enqueue_style( 'thim-style', get_template_directory_uri() . '/style-' . $current_blog->blog_id . '.css', array(), array(), THIM_THEME_VERSION );
		} else {
			wp_enqueue_style( 'thim-style', get_stylesheet_uri(), array(), THIM_THEME_VERSION );
		}
	} else {
		wp_enqueue_style( 'thim-style', get_stylesheet_uri(), array(), THIM_THEME_VERSION );
	}

	// Style default
	if ( ! thim_plugin_active( 'thim-core' ) ) {
		wp_enqueue_style( 'thim-default', THIM_URI . 'inc/data/default.css', array() );
	}

	//	RTL
	if ( get_theme_mod( 'theme_feature_rtl_support', false ) ) {
		wp_enqueue_style( 'thim-style-rtl', THIM_URI . 'rtl.css', array() );
	}
    wp_enqueue_script( 'easing', THIM_URI . 'assets/js/jquery.easing.min.js', array( 'jquery' ), '20151215', true );

	//	Scripts
	wp_enqueue_script( 'sticky-sidebar', THIM_URI . 'assets/js/libs/theia-sticky-sidebar.js', array( 'jquery' ), '1.3', true );
	//wp_enqueue_script( 'thim-main', THIM_URI . 'assets/js/main.min.js', array( 'jquery', 'imagesloaded' ), '1.3', true );
 	wp_enqueue_script( 'thim-bootstrap', THIM_URI . 'assets/js/libs/autoload/bootstrap.min.js', array( 'jquery', 'imagesloaded' ), '1.4', true );
	wp_enqueue_script( 'thim-dropkick', THIM_URI . 'assets/js/libs/autoload/dropkick.2.1.8.min.js', array( 'jquery' ), '1.4', true );
	wp_enqueue_script( 'thim-flexslider', THIM_URI . 'assets/js/libs/autoload/jquery.flexslider-min.js', array( 'jquery'), '1.4', true );
	wp_enqueue_script( 'thim-stellar', THIM_URI . 'assets/js/libs/autoload/stellar.min.js', array( 'jquery'), '1.4', true );
	//
 	wp_enqueue_script( 'thim-custom', THIM_URI . 'assets/js/thim-custom.js', array( 'jquery' ), '1.1.3', true );
	wp_enqueue_script( 'thim-custom-js', THIM_URI . 'assets/custom/custom.js', array( 'jquery' ), '1.0', true );

	if ( get_theme_mod( 'feature_smoothscroll', false ) ) {
		wp_enqueue_script( 'thim-smoothscroll', THIM_URI . 'assets/js/libs/smoothscroll.min.js', array( 'jquery' ), '', true );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
    wp_enqueue_script( 'doc_js');
}

add_action( 'wp_enqueue_scripts', 'thim_scripts' );

/**
 * Implement the theme wrapper.
 */
require THIM_DIR . 'inc/libs/theme-wrapper.php';

/**
 * Implement the Custom Header feature.
 */
require THIM_DIR . 'inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require THIM_DIR . 'inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require THIM_DIR . 'inc/extras.php';

/**
 * Load Jetpack compatibility file.
 */
require THIM_DIR . 'inc/jetpack.php';

/**
 * Custom wrapper layout for theme
 */
require THIM_DIR . 'inc/wrapper-layout.php';

/**
 * Custom functions
 */
require THIM_DIR . 'inc/custom-functions.php';

/**
 * Customizer additions.
 */
require THIM_DIR . 'inc/customizer.php';
if ( is_admin() && current_user_can( 'manage_options' ) ) {
	require THIM_DIR . 'inc/admin/require-thim-core.php';
	require THIM_DIR . 'inc/admin/plugins-require.php';
}

/**
 * WooCommerce.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require THIM_DIR . 'woocommerce/woocommerce.php';
}


/**
 * Load shortcodes
 * thim-THEME-SLUG-shortcodes.php
 */
if ( file_exists( THIM_DIR . 'shortcodes/thim-thim-startertheme-shortcodes.php' ) && ( ! class_exists( 'Thim_Plugin_Startertheme_Shortcodes' ) ) ) {
	require THIM_DIR . 'shortcodes/thim-thim-startertheme-shortcodes.php';
}

//if(class_exists('CMB2_Bootstrap_270')){
    require get_template_directory() . '/meta-box.php';
//}
 
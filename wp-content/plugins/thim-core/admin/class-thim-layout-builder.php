<?php

/**
 * Class Thim_Layout_Builder
 *
 * @since 0.8.2
 */
class Thim_Layout_Builder extends Thim_Singleton {
	/**
	 * Thim_Layout_Builder constructor.
	 *
	 * @since 0.8.2
	 */
	protected function __construct() {
		if ( ! class_exists( 'Vc_Manager' ) ) {
			return;
		}

		$this->init_hooks();
	}

	/**
	 * Init hooks.
	 *
	 * @since 0.8.2
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'register_post_type' ), 0 );
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'disable_revisions' ) );
		add_filter( 'get_user_option_screen_layout_thim-component', array( $this, 'screen_layout_post' ) );
		add_action( 'save_post', array( $this, 'update_widget_content' ) );
		add_action( 'admin_init', array( $this, 'enable_visual_composer' ) );
		add_action( 'admin_init', array( $this, 'handle_request_edit_content_widget' ) );
		add_action( 'delete_widget', array( $this, 'delete_page_linking_widget' ), 10, 3 );
	}

	/**
	 * Enable visual composer.
	 *
	 * @since 0.8.2
	 */
	public function enable_visual_composer() {
		$post_id = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : false;
		if ( get_post_type( $post_id ) !== 'thim-component' ) {
			return;
		}

		add_filter( 'vc_role_access_with_post_types_get_state', '__return_true' );
		add_filter( 'vc_role_access_with_backend_editor_get_state', '__return_true' );
		add_filter( 'vc_role_access_with_frontend_editor_get_state', '__return_false' );
		add_filter( 'vc_check_post_type_validation', '__return_true' );
	}

	/**
	 * Action delete page link to widget.
	 *
	 * @since 0.8.2
	 *
	 * @param $widget_id
	 * @param $sidebar_id
	 * @param $id_base
	 */
	public function delete_page_linking_widget( $widget_id, $sidebar_id, $id_base ) {
		if ( $id_base !== Thim_Widget_Layout_Builder::get_id_base() ) {
			return;
		}

		$id = str_replace( $id_base . '-', '', $widget_id );
		if ( ! is_numeric( $id ) ) {
			return;
		}

		Thim_Builder_Linking_Widget::delete_page_linking_widget( $id );
	}

	/**
	 * Handle request go to page builder.
	 *
	 * @since 0.8.2
	 */
	public function handle_request_edit_content_widget() {
		Thim_Builder_Linking_Widget::handle_request_edit_content_widget();
	}

	/**
	 * Add action update widget content after update post.
	 *
	 * @since 0.8.2
	 *
	 * @param $post_id
	 */
	public function update_widget_content( $post_id ) {
		Thim_Builder_Linking_Widget::update_widget_content( $post_id );
	}

	/**
	 * Add filter screen layout edit post type thim-component is 1 column.
	 *
	 * @since 0.8.2
	 *
	 * @return int
	 */
	public function screen_layout_post() {
		return 1;
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 0.8.2
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();

		if ( $screen->id === 'widgets' ) {
			$this->enqueue_scripts_page_widgets();
		}

		if ( $screen->post_type !== 'thim-component' ) {
			return;
		}

		wp_enqueue_style( 'thim-edit-component', THIM_CORE_ADMIN_URI . '/assets/css/thim-layout-builder.css' );
	}

	/**
	 * Enqueue scripts in page widgets.php
	 *
	 * @since 0.8.2
	 */
	private function enqueue_scripts_page_widgets() {
		add_thickbox();
		wp_enqueue_script( 'thim-widget-builder-layout', THIM_CORE_ADMIN_URI . '/assets/js/thim-layout-builder.js', array( 'jquery' ) );
	}

	/**
	 * Disable revisions.
	 *
	 * @since 0.8.2
	 */
	public function disable_revisions() {
		remove_post_type_support( 'thim-component', 'revisions' );
	}

	/**
	 * Register custom post type.
	 *
	 * @since 0.8.2
	 */
	public function register_post_type() {
		$labels  = array(
			'name'                  => _x( 'TP Component', 'Post Type General Name', 'thim-core' ),
			'singular_name'         => _x( 'TP Component', 'Post Type Singular Name', 'thim-core' ),
			'menu_name'             => __( 'TP Components', 'thim-core' ),
			'name_admin_bar'        => __( 'TP Component', 'thim-core' ),
			'archives'              => __( 'Item Archives', 'thim-core' ),
			'parent_item_colon'     => __( 'Parent Item:', 'thim-core' ),
			'all_items'             => __( 'All Items', 'thim-core' ),
			'add_new_item'          => __( 'Add New Item', 'thim-core' ),
			'add_new'               => __( 'Add New', 'thim-core' ),
			'new_item'              => __( 'New Item', 'thim-core' ),
			'edit_item'             => __( 'Edit Item', 'thim-core' ),
			'update_item'           => __( 'Update Item', 'thim-core' ),
			'view_item'             => __( 'View Item', 'thim-core' ),
			'search_items'          => __( 'Search Item', 'thim-core' ),
			'not_found'             => __( 'Not found', 'thim-core' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'thim-core' ),
			'featured_image'        => __( 'Featured Image', 'thim-core' ),
			'set_featured_image'    => __( 'Set featured image', 'thim-core' ),
			'remove_featured_image' => __( 'Remove featured image', 'thim-core' ),
			'use_featured_image'    => __( 'Use as featured image', 'thim-core' ),
			'insert_into_item'      => __( 'Insert into item', 'thim-core' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'thim-core' ),
			'items_list'            => __( 'Items list', 'thim-core' ),
			'items_list_navigation' => __( 'Items list navigation', 'thim-core' ),
			'filter_items_list'     => __( 'Filter items list', 'thim-core' ),
		);
		$rewrite = array(
			'slug'       => 'thim-component',
			'with_front' => true,
			'pages'      => false,
			'feeds'      => false,
		);
		$args    = array(
			'label'               => __( 'Thim Component', 'thim-core' ),
			'description'         => __( 'Post Type Description', 'thim-core' ),
			'labels'              => $labels,
			'supports'            => array( 'editor', ),
			'hierarchical'        => true,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-layout',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => $rewrite,
			'capability_type'     => 'page',
		);
		register_post_type( 'thim-component', $args );
	}

	/**
	 * Register widget.
	 *
	 * @since 0.8.2
	 */
	public function register_widget() {
		register_widget( 'Thim_Widget_Layout_Builder' );
	}
}
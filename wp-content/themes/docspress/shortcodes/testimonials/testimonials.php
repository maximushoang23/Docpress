<?php
/**
 * Created by PhpStorm.
 * User: khoapq
 * Date: 8/10/2016
 * Time: 8:59 AM
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mapping shortcode Testimonials
 */
vc_map( array(
	'name'        => esc_html__( 'Thim Testimonials', 'startertheme_shortcodes' ),
	'base'        => 'thim-testimonials',
	'category'    => esc_html__( 'Thim Shortcodes', 'startertheme_shortcodes' ),
	'description' => esc_html__( 'Display testimonials.', 'startertheme_shortcodes' ),
	'params'      => array(
		// Title
		array(
			'type'        => 'textfield',
			'admin_label' => true,
			'heading'     => esc_html__( 'Block Title', 'startertheme_shortcodes' ),
			'param_name'  => 'title',
			'description' => esc_html__( 'Write the title for the block.', 'startertheme_shortcodes' )
		),

		// Number post
		array(
			'type'        => 'number',
			'admin_label' => true,
			'heading'     => esc_html__( 'Number posts', 'startertheme_shortcodes' ),
			'param_name'  => 'number_post',
			'min'         => 1,
			'value'       => 5,
			'description' => esc_html__( 'Number posts display.', 'startertheme_shortcodes' ),
		),


		// Visible post
		array(
			'type'        => 'number',
			'admin_label' => true,
			'heading'     => esc_html__( 'Item Visible', 'startertheme_shortcodes' ),
			'param_name'  => 'item_visible',
			'min'         => 1,
			'value'       => 3,
		),

		// Mousewheel Scroll
		array(
			"type"        => "dropdown",
			"heading"     => esc_html__( "Mousewheel Scroll", "startertheme_shortcodes" ),
			"param_name"  => "mousewheel",
			"admin_label" => true,
			"value"       => array(
				esc_html__( "No", "startertheme_shortcodes" )  => false,
				esc_html__( "Yes", "startertheme_shortcodes" ) => true,
			),
		),


		// Background image
		array(
			'type'        => 'attach_image',
			'heading'     => esc_html__( 'Background Image', 'startertheme_shortcodes' ),
			'param_name'  => 'background_image',
			'admin_label' => true,
			'value'       => '',
		),

		// Animation
		array(
			"type"        => "dropdown",
			"heading"     => esc_html__( "Animation", "startertheme_shortcodes" ),
			"param_name"  => "css_animation",
			"admin_label" => true,
			"value"       => array(
				esc_html__( "No", "startertheme_shortcodes" )                 => '',
				esc_html__( "Top to bottom", "startertheme_shortcodes" )      => "top-to-bottom",
				esc_html__( "Bottom to top", "startertheme_shortcodes" )      => "bottom-to-top",
				esc_html__( "Left to right", "startertheme_shortcodes" )      => "left-to-right",
				esc_html__( "Right to left", "startertheme_shortcodes" )      => "right-to-left",
				esc_html__( "Appear from center", "startertheme_shortcodes" ) => "appear"
			),
			"description" => esc_html__( "Select type of animation if you want this element to be animated when it enters into the browsers viewport. Note: Works only in modern browsers.", "startertheme_shortcodes" )
		),
		// Extra class
		array(
			'type'        => 'textfield',
			'admin_label' => true,
			'heading'     => esc_html__( 'Extra class', 'startertheme_shortcodes' ),
			'param_name'  => 'el_class',
			'value'       => '',
			'description' => esc_html__( 'Add extra class name that will be applied to the icon box, and you can use this class for your customizations.', 'startertheme_shortcodes' ),
		),
	)
) );


/**
 * Template
 */
include_once 'tpl/default.php';
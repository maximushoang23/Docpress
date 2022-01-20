<?php
/**
 * Created by PhpStorm.
 * User: khoapq
 * Date: 8/10/2016
 * Time: 9:08 AM
 */


add_shortcode( 'thim-testimonials', 'thim_shortcode_testimonials' );
function thim_shortcode_testimonials( $atts ) {
	$param = shortcode_atts( array(
		'title'            => '',
		'number_post'      => 5,
		'item_visible'     => 3,
		'mousewheel'       => '0',
		'background_image' => '',
		'css_animation'    => '',
		'el_class'         => '',
	), $atts );

	$css_animation = thim_getCSSAnimation( $param['css_animation'] );
	$box_css       = '';

	$limit        = $param['number_post'];
	$item_visible = $param['item_visible'];
	$mousewheel   = $param['mousewheel'];

	$testimonial_args = array(
		'post_type'           => 'testimonials',
		'posts_per_page'      => $limit,
		'ignore_sticky_posts' => true
	);

	$testimonials = new WP_Query( $testimonial_args );

	$background_image = wp_get_attachment_image_src( $param['background_image'], 'full' );
	if ( $background_image ) {
		$box_css .= 'background-image: url(' . $background_image[0] . ')';
	}
	$box_style = 'style="' . $box_css . '"';

	$html = '<div class="thim-testimonial-slider ' . esc_attr( $param['el_class'] ) . '" ' . $css_animation . ' ' . $box_style . '>';
	$html .= do_shortcode( '[thim-heading alignment="center" heading_primary="' . $param['title'] . '"]' );
	$html .= '<span class="icon-quote"></span>';
	if ( $testimonials->have_posts() ) {
		$html .= '<div class="testimonial-slider" data-visible="' . $item_visible . '" data-mousewheel="' . $mousewheel . '">';
		while ( $testimonials->have_posts() ) : $testimonials->the_post();
			$link    = get_post_meta( get_the_ID(), 'website_url', true );
			$regency = get_post_meta( get_the_ID(), 'regency', true );

			$html .= '<div class="item">';
			if ( has_post_thumbnail() ) {
				$src     = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
				$img_src = thim_aq_resize( $src[0], 80, 80, true );
				$html .= '<img src="' . esc_attr( $img_src ) . '" alt="' . get_the_title() . '" title="' . get_the_title() . '"  data-heading="' . get_the_title() . '" data-content="' . esc_attr( $regency ) . '" />';
			}
			$html .= '<div class="content">' . get_the_content() . '</div>';

			$html .= '</div>';

		endwhile;
		$html .= '</div>';
	}
	$html .= '</div>';
	echo ent2ncr( $html );

	wp_reset_postdata();
}
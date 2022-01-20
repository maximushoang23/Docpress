<?php
/**
 * Section Header Sticky Menu
 * 
 * @package Thim_Starter_Theme
 */

thim_customizer()->add_section(
	array(
		'id'             => 'header_sticky_menu',
		'title'          => esc_html__( 'Sticky Menu', 'thim-docspress' ),
		'panel'			 => 'header',
		'priority'       => 55,
	)
);

// Enable or Disable
thim_customizer()->add_field(
	array(
		'id'          => 'show_sticky_menu',
		'type'        => 'switch',
		'label'       => esc_html__( 'Sticky On Scroll', 'thim-docspress' ),
		'tooltip'     => esc_html__( 'Allows you can show or hide sticky header menu on your site . ', 'thim-docspress' ),
		'section'     => 'header_sticky_menu',
		'default'     => true,
		'priority'    => 10,
		'choices'     => array(
			true  	  => esc_html__( 'On', 'thim-docspress' ),
			false	  => esc_html__( 'Off', 'thim-docspress' ),
		),
	)
);

// Select Style
thim_customizer()->add_field(
	array(
		'id'          => 'sticky_menu_style',
		'type'        => 'select',
		'label'       => esc_html__( 'Select style', 'thim-docspress' ),
		'tooltip'     => esc_html__( 'Allows you can select sticky menu style for your header . ', 'thim-docspress' ),
		'section'     => 'header_sticky_menu',
		'default'     => 'same',
		'priority'    => 10,
		'multiple'    => 0,
		'choices'     => array(
			'same' 	  => esc_html__( 'The same with main menu', 'thim-docspress' ),
			'custom'  => esc_html__( 'Custom', 'thim-docspress' )
		),
	)
);

// Background Header
thim_customizer()->add_field(
	array(
		'id'          => 'sticky_menu_background_color',
		'type'        => 'color',
		'label'       => esc_html__( 'Background Color', 'thim-docspress' ),
		'tooltip'     => esc_html__( 'Allows you can select a color make background color for header sticky menu . ', 'thim-docspress' ),
		'section'     => 'header_sticky_menu',
		'default'     => '#ffffff',
		'priority'    => 16,
		'alpha'       => true,
		'transport' => 'postMessage',
		'js_vars'   => array(
			array(
				'choice'   => 'color',
				'element'  => 'body header#masthead.site-header.custom-sticky.affix',
				'property' => 'background-color',
			)
		),
		'active_callback' => array(
			array(
				'setting'  => 'sticky_menu_style',
				'operator' => '===',
				'value'    => 'custom',
			),
		),
	)
);

// Text Color
thim_customizer()->add_field(
	array(
		'id'          => 'sticky_menu_text_color',
		'type'        => 'color',
		'label'       => esc_html__( 'Text Color', 'thim-docspress' ),
		'tooltip'     => esc_html__( 'Allows you can select a color make text color on header sticky menu . ', 'thim-docspress' ),
		'section'     => 'header_sticky_menu',
		'default'     => '#333333',
		'priority'    => 18,
		'alpha'       => true,
		'transport' => 'postMessage',
		'js_vars'   => array(
			array(
				'choice'   => 'color',
				'element'  => 'body header#masthead.site-header.affix.custom-sticky #primary-menu >li >a,
                               header#masthead.site-header.affix.custom-sticky #primary-menu >li >span',
				'property' => 'color',
			)
		),
		'active_callback' => array(
			array(
				'setting'  => 'sticky_menu_style',
				'operator' => '===',
				'value'    => 'custom',
			),
		),
	)
);

// Text Hover Color
thim_customizer()->add_field(
	array(
		'id'          => 'sticky_menu_text_color_hover',
		'type'        => 'color',
		'label'       => esc_html__( 'Text Hover Color', 'thim-docspress' ),
		'tooltip'     => esc_html__( 'Allows you can select color for text link when hover text link on header sticky menu. ', 'thim-docspress' ),
		'section'     => 'header_sticky_menu',
		'default'     => '#439fdf',
		'priority'    => 19,
		'alpha'       => true,
		'transport' => 'postMessage',
		'js_vars'   => array(
			array(
				'choice'   => 'color',
				'element'  => 'body header#masthead.site-header.affix.custom-sticky #primary-menu >li >a:hover,
                               body header#masthead.site-header.affix.custom-sticky #primary-menu >li >span:hover',
				'property' => 'color',
			)
		),
		'active_callback' => array(
			array(
				'setting'  => 'sticky_menu_style',
				'operator' => '===',
				'value'    => 'custom',
			),
		),
	)
);
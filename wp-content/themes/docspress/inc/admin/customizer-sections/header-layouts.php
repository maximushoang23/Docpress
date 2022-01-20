<?php
/**
 * Section Header Layout
 * 
 * @package Thim_Starter_Theme
 */

thim_customizer()->add_section(
	array(
		'id'             => 'header_layout',
		'title'          => esc_html__( 'Layouts', 'thim-docspress' ),
		'panel'			 => 'header',
		'priority'       => 20,
	)
);

// Select Header Layout
thim_customizer()->add_field(
	array(
		'id'            => 'header_style',
		'type'          => 'radio-image',
		'label'         => esc_html__( 'Header Layouts', 'thim-docspress' ),
		'tooltip'     	=> esc_html__( 'Allows you can select header layout for header on your site. ', 'thim-docspress' ),
		'section'       => 'header_layout',
		'default'       => 'header_v1',
		'priority'      => 10,
		'choices'       => array(
			'header_v1'     => THIM_URI . 'assets/images/header/classic.png',
			'header_v2'     => THIM_URI . 'assets/images/header/stack-center.png',
			'header_v3'     => THIM_URI . 'assets/images/header/magazine.png',
		),
	)
);

// Select Header Position
thim_customizer()->add_field(
	array(
		'id'          => 'header_position',
		'type'        => 'select',
		'label'       => esc_html__( 'Header Positions', 'thim-docspress' ),
		'tooltip'     => esc_html__( 'Allows you can select position layout for header layout. ', 'thim-docspress' ),
		'section'     => 'header_layout',
		'priority'    => 20,
		'multiple'    => 0,
		'default'     => 'default',
		'choices'     => array(
			'default' => esc_html__( 'Default', 'thim-docspress' ),
			'overlay' => esc_html__( 'Overlay', 'thim-docspress' ),
		),
	)
);


// Background Header
thim_customizer()->add_field(
	array(
		'id'          => 'header_background_color',
		'type'        => 'color',
		'label'       => esc_html__( 'Background Color', 'thim-docspress' ),
		'tooltip'     => esc_html__( 'Allows you can choose background color for your header. ', 'thim-docspress' ),
		'section'     => 'header_layout',
		'default'     => '#439fdf',
		'priority'    => 30,
		'alpha'       => true,
		'transport' => 'postMessage',
		'js_vars'   => array(
			array(
				'choice'   => 'color',
				'element'  => 'body #masthead.site-header',
				'property' => 'background-color',
			)
		)
	)
);
<?php
/**
 * Section Header Top bar
 */

thim_customizer()->add_section(
	array(
		'id'       => 'header_topbar',
		'title'    => esc_html__( 'Top bar', 'thim-docspress' ),
		'panel'    => 'header',
		'priority' => 20,
	)
);

// Enable or disable top bar
thim_customizer()->add_field(
	array(
		'id'       => 'header_topbar_display',
		'type'     => 'switch',
		'label'    => esc_html__( 'Show Topbar', 'thim-docspress' ),
		'tooltip'  => esc_html__( 'Allows you to enable or disable Top bar.', 'thim-docspress' ),
		'section'  => 'header_topbar',
		'default'  => true,
		'priority' => 10,
		'choices'  => array(
			true  => esc_html__( 'On', 'thim-docspress' ),
			false => esc_html__( 'Off', 'thim-docspress' ),
		),
	)
);

// Show line after topbar
thim_customizer()->add_field(
	array(
		'id'       => 'show_line_after_topbar',
		'type'     => 'switch',
		'label'    => esc_html__( 'Show Line After Topbar', 'thim-docspress' ),
		'tooltip'  => esc_html__( 'Allows you to show or hide line between topbar and main menu.', 'thim-docspress' ),
		'section'  => 'header_topbar',
		'default'  => true,
		'priority' => 11,
		'choices'  => array(
			true  => esc_html__( 'On', 'thim-docspress' ),
			false => esc_html__( 'Off', 'thim-docspress' ),
		),
	)
);

thim_customizer()->add_field(
	array(
		'id'        => 'font_topbar',
		'type'      => 'typography',
		'label'     => esc_html__( 'Topbar Fonts', 'thim-docspress' ),
		'tooltip'   => esc_html__( 'Allows you to select font properties for topbar. ', 'thim-docspress' ),
		'section'   => 'header_topbar',
		'priority'  => 20,
		'default'   => array(
			'font-size'      => '12px',
			'color'          => '#878787',
		),
		'transport' => 'postMessage',
		'js_vars'   => array(
			array(
				'choice'   => 'font-size',
				'element'  => '#thim-header-topbar,
				               #thim-header-topbar li,
				               #thim-header-topbar span,
                               #thim-header-topbar a,
                               .thim-top-logo,
				               .thim-top-logo li,
				               .thim-top-logo span,
                               .thim-top-logo a',
				'property' => 'font-size',
			),
			array(
				'choice'   => 'color',
				'element'  => '#thim-header-topbar,
				               #thim-header-topbar li,
				               #thim-header-topbar span,
                               #thim-header-topbar a,
                               .thim-top-logo,
				               .thim-top-logo li,
				               .thim-top-logo span,
                               .thim-top-logo a',
				'property' => 'color',
			),
		),
	)
);

// Topbar background color
thim_customizer()->add_field(
	array(
		'id'          => 'topbar_background_color',
		'type'        => 'color',
		'label'       => esc_html__( 'Background Color', 'thim-docspress' ),
		'tooltip'     => esc_html__( 'Allows you to choose a background color for widget on topbar. ', 'thim-docspress' ),
		'section'     => 'header_topbar',
		'default'     => '#333333',
		'priority'    => 30,
		'alpha'       => true,
		'transport' => 'postMessage',
		'js_vars'   => array(
			array(
				'choice'   => 'background-color',
				'element'  => 'body header#masthead #thim-header-topbar',
				'property' => 'background-color',
			)
		)
	)
);
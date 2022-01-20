<?php
/**
 * Section Header Main Menu
 *
 * @package Hair_Salon
 */

thim_customizer()->add_section(
    array(
        'id'       => 'header_main_menu',
        'title'    => esc_html__( 'Main Menu', 'hairsalon' ),
        'panel'    => 'header',
        'priority' => 30,
    )
);

// Select font
thim_customizer()->add_field(
    array(
        'id'       => 'main_menu_font_family',
        'type'     => 'select',
        'label'    => esc_html__( 'Font Family', 'hairsalon' ),
        'tooltip'  => esc_html__( 'Allows you to select font title or body. ', 'hairsalon' ),
        'section'  => 'header_main_menu',
        'default'  => 'body',
        'priority' => 10,
        'multiple' => 0,
        'choices'  => array(
            'body'  => esc_html__( 'Body Font', 'hairsalon' ),
            'title' => esc_html__( 'Title Font', 'hairsalon' )
        ),
    )
);

// Select All Fonts For Main Menu
thim_customizer()->add_field(
    array(
        'id'        => 'main_menu',
        'type'      => 'typography',
        'label'     => esc_html__( 'Fonts', 'hairsalon' ),
        'tooltip'   => esc_html__( 'Allows you to select all font font properties for header. ', 'hairsalon' ),
        'section'   => 'header_main_menu',
        'priority'  => 10,
        'default'   => array(
            'variant'        => '700',
            'font-size'      => '13px',
            'line-height'    => '1.6em',
            'color'          => '#333333',
            'text-transform' => 'uppercase',
        ),
        'transport' => 'postMessage',
        'js_vars'   => array(
            array(
                'choice'   => 'variant',
                'element'  => 'header#masthead.site-header .width-navigation .inner-navigation #primary-menu >li >a,
                               header#masthead.site-header .width-navigation .inner-navigation #primary-menu >li >span',
                'property' => 'font-weight',
            ),
            array(
                'choice'   => 'font-size',
                'element'  => 'header#masthead.site-header #primary-menu >li >a,
                               header#masthead.site-header #primary-menu >li >span',
                'property' => 'font-size',
            ),
            array(
                'choice'   => 'line-height',
                'element'  => 'header#masthead.site-header #primary-menu >li >a,
                               header#masthead.site-header #primary-menu >li >span',
                'property' => 'line-height',
            ),
            array(
                'choice'   => 'color',
                'element'  => 'header#masthead.site-header #primary-menu >li >a,
                               header#masthead.site-header #primary-menu >li >span,
                               header#masthead.site-header .navigation .width-navigation .inner-navigation .navbar > .current-menu-item a',
                'property' => 'color',
            ),
            array(
                'choice'   => 'text-transform',
                'element'  => 'header#masthead.site-header #primary-menu >li >a,
                               header#masthead.site-header #primary-menu >li >span',
                'property' => 'text-transform',
            ),
        )
    )
);

// Text Link Hover
thim_customizer()->add_field(
    array(
        'id'        => 'main_menu_hover_color',
        'type'      => 'color',
        'label'     => esc_html__( 'Text Color Hover', 'hairsalon' ),
        'tooltip'   => esc_html__( 'Allows you to select color for text link when hover text link . ', 'hairsalon' ),
        'section'   => 'header_main_menu',
        'default'   => '#439fdf',
        'priority'  => 16,
        'alpha'     => true,
        'transport' => 'postMessage',
        'js_vars'   => array(
            array(
                'choice'   => 'color',
                'element'  => 'header#masthead.site-header #primary-menu >li >a:hover,
                               header#masthead.site-header #primary-menu >li >span:hover',
                'property' => 'color',
            )
        )
    )
);
<?php
/**
 * Section Sharing
 *
 * @package Hair_Salon
 */

thim_customizer()->add_section(
    array(
        'id'       => 'sharing',
        'panel'    => 'blog',
        'title'    => esc_html__( 'Social Share', 'thim-docspress' ),
        'priority' => 21,
    )
);

// Sharing Group
thim_customizer()->add_field(
    array(
        'id'       => 'group_sharing',
        'type'     => 'sortable',
        'label'    => esc_html__( 'Sortable Buttons Sharing', 'thim-docspress' ),
        'tooltip'  => esc_html__( 'Click on eye icons to show or hide buttons. Use drag and drop to change the position of social share icons..', 'thim-docspress' ),
        'section'  => 'sharing',
        'priority' => 10,
        'default'  => array(
            'facebook',
            'twitter',
            'pinterest',
            'google',
            'fancy'
        ),
        'choices'  => array(
            'facebook'  => esc_html__( 'Facebook', 'thim-docspress' ),
            'twitter'   => esc_html__( 'Twitter', 'thim-docspress' ),
            'pinterest' => esc_html__( 'Pinterest', 'thim-docspress' ),
            'google'    => esc_html__( 'Google Plus', 'thim-docspress' ),
            'fancy'     => esc_html__( 'Fancy', 'thim-docspress' ),
        ),
    )
);


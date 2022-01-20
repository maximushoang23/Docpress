<?php
/**
 * Section Blog General
 *
 * @package Hair_Salon
 */

thim_customizer()->add_section(
    array(
        'id'             => 'blog_general',
        'panel'			 => 'blog',
        'title'          => esc_html__( 'Settings', 'hairsalon' ),
        'priority'       => 10,
    )
);

// Blog Archive Group
thim_customizer()->add_group( array(
    'id'       => 'blog_archive_setting_group',
    'section'  => 'blog_general',
    'priority' => 10,
    'groups'   => array(
        array(
            'id'     => 'blog_archive_page_group',
            'label'  => esc_html__( 'Archive Page', 'hairsalon' ),
            'fields' => array(
                //Blog Columns
                array(
                    'type'        => 'select',
                    'id'          => 'archive_post_column',
                    'label'       => esc_html__( 'Blog Columns', 'hairsalon' ),
                    'tooltip'     => esc_html__( 'Choose the number of columns for archive post.', 'hairsalon' ),
                    'default'     => '1',
                    'priority'    => 10,
                    'multiple'    => 0,
                    'choices'     => array(
                        '1' => esc_html__( '1', 'hairsalon' ),
                        '2' => esc_html__( '2', 'hairsalon' ),
                        '3' => esc_html__( '3', 'hairsalon' ),
                        '4' => esc_html__( '4', 'hairsalon' ),
                    ),
                ),
                // Excerpt Content
                array(
                    'id'          => 'excerpt_archive_content',
                    'type'        => 'slider',
                    'label'       => esc_html__( 'Excerpt Length', 'hairsalon' ),
                    'tooltip'     => esc_html__( 'Choose the number of words you want to cut from the content to be the excerpt of search and archive', 'hairsalon' ),
                    'priority'    => 20,
                    'default'     => 20,
                    'choices'     => array(
                        'min'  => '10',
                        'max'  => '100',
                        'step' => '5',
                    ),
                )
            ),
        ),
    )
) );

// Blog Single Group
thim_customizer()->add_group( array(
    'id'       => 'blog_single_setting_group',
    'section'  => 'blog_general',
    'priority' => 20,
    'groups'   => array(
        array(
            'id'     => 'blog_single_page_group',
            'label'  => esc_html__( 'Single Page', 'hairsalon' ),
            'fields' => array(
                // Show Feature Image
                array(
                    'type'     => 'switch',
                    'id'       => 'blog_single_feature_image',
                    'label'    => esc_html__( 'Featured Image', 'hairsalon' ),
                    'tooltip'  => esc_html__( 'Turn on to display featured images on single blog posts..', 'hairsalon' ),
                    'default'  => true,
                    'priority' => 10,
                    'choices'  => array(
                        true  => esc_html__( 'On', 'hairsalon' ),
                        false => esc_html__( 'Off', 'hairsalon' ),
                    ),
                ),
                // Turn On Comments
                array(
                    'type'     => 'switch',
                    'id'       => 'blog_single_comment',
                    'label'    => esc_html__( 'Comments', 'hairsalon' ),
                    'tooltip'  => esc_html__( 'Turn on to display comments.', 'hairsalon' ),
                    'default'  => true,
                    'priority' => 20,
                    'choices'  => array(
                        true  => esc_html__( 'On', 'hairsalon' ),
                        false => esc_html__( 'Off', 'hairsalon' ),
                    ),
                ),
                // Turn On Related Post
                array(
                    'type'     => 'switch',
                    'id'       => 'blog_single_related_post',
                    'label'    => esc_html__( 'Related Posts', 'hairsalon' ),
                    'tooltip'  => esc_html__( 'Turn on to display related posts.', 'hairsalon' ),
                    'default'  => true,
                    'priority' => 30,
                    'choices'  => array(
                        true  => esc_html__( 'On', 'hairsalon' ),
                        false => esc_html__( 'Off', 'hairsalon' ),
                    ),
                ),
                // Select Post Numbers For Related Post
                array(
                    'type'            => 'slider',
                    'id'              => 'blog_single_related_post_number',
                    'label'           => esc_html__( 'Numbers of Related Post', 'hairsalon' ),
                    'default'         => 3,
                    'priority'        => 40,
                    'choices'         => array(
                        'min'  => 1,
                        'max'  => 20,
                        'step' => 1,
                    ),
                    'active_callback' => array(
                        array(
                            'setting'  => 'blog_single_related_post',
                            'operator' => '==',
                            'value'    => true,
                        ),
                    ),
                ),
                // Select Post Column Numbers For Related Post
                array(
                    'type'            => 'slider',
                    'id'              => 'blog_single_related_post_column',
                    'label'           => esc_html__( 'Columns of Related Post', 'hairsalon' ),
                    'default'         => 3,
                    'priority'        => 50,
                    'choices'         => array(
                        'min'  => 1,
                        'max'  => 12,
                        'step' => 1,
                    ),
                    'active_callback' => array(
                        array(
                            'setting'  => 'blog_single_related_post',
                            'operator' => '==',
                            'value'    => true,
                        ),
                    ),
                )

            ),
        ),
    )
) );

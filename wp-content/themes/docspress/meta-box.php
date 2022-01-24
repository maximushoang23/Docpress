<?php
// Register Custom Post Type
function custom_post_type()
{

    $labels = array(
        'name' => _x('Section Docs', 'Post Type General Name', 'text_domain'),
        'singular_name' => _x('Section Docs', 'Post Type Singular Name', 'text_domain'),
        'menu_name' => __('Section Docs', 'text_domain'),
        'name_admin_bar' => __('Section Docs', 'text_domain'),
        'archives' => __('Item Archives', 'text_domain'),
        'attributes' => __('Item Attributes', 'text_domain'),
        'parent_item_colon' => __('Parent Item:', 'text_domain'),
        'all_items' => __('All Items', 'text_domain'),
        'add_new_item' => __('Add New Item', 'text_domain'),
        'add_new' => __('Add New', 'text_domain'),
        'new_item' => __('New Item', 'text_domain'),
        'edit_item' => __('Edit Item', 'text_domain'),
        'update_item' => __('Update Item', 'text_domain'),
        'view_item' => __('View Item', 'text_domain'),
        'view_items' => __('View Items', 'text_domain'),
        'search_items' => __('Search Item', 'text_domain'),
        'not_found' => __('Not found', 'text_domain'),
        'not_found_in_trash' => __('Not found in Trash', 'text_domain'),
        'featured_image' => __('Featured Image', 'text_domain'),
        'set_featured_image' => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image' => __('Use as featured image', 'text_domain'),
        'insert_into_item' => __('Insert into item', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'text_domain'),
        'items_list' => __('Items list', 'text_domain'),
        'items_list_navigation' => __('Items list navigation', 'text_domain'),
        'filter_items_list' => __('Filter items list', 'text_domain'),
    );
    $args = array(
        'label' => __('Section Docs', 'text_domain'),
        'description' => __('section docs', 'text_domain'),
        'labels' => $labels,
        'supports' => array('title', 'editor'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'page',
    );
    register_post_type('thimdocs', $args);

}

add_action('init', 'custom_post_type', 0);

add_filter('rwmb_meta_boxes', 'meta_box_group_demo_register');
function meta_box_group_demo_register($meta_boxes)
{
    $prefix = 'thim_';

    $meta_boxes[] = array(
        'title' => esc_html__('Sections', 'docspress'),
        'post_types' => array('post','page'),
        'fields' => array(
            array(
                'id' => 'link_demo_preview',
                'type' => 'text',
                'name' => esc_html__('Link Buy Theme', 'docspress'),
            ),
            array(
                'id' => $prefix . 'items_docs',
                'type' => 'group',
                'clone' => true,
                'sort_clone' => true,
                'collapsible' => true,
                'save_state' => true,
                'group_title' => array('field' => 'title'),
                'fields' => array(
                    array(
                        'id' => 'title',
                        'type' => 'text',
                        'name' => esc_html__('Title', 'docspress'),
                    ),
                    array(
                        'id' => 'sub_section',
                        'name' => esc_html__('Set Is Sub Section', 'docspress'),
                        'type' => 'checkbox',
                    ),
                    array(
                        'id' => 'type',
                        'name' => esc_html__('Type', 'docspress'),
                        'type' => 'select',
                        'options' => array(
                            'text' => 'Text',
                            'page' => 'Page',
                        ),
                        'default' => 'text',
                    ),
                    array(
                        'id' => 'text',
                        'name' => '',
                        'type' => 'wysiwyg',
                        // 'raw'     => false,
                        'hidden' => ['type', '!=', 'text'],
                        'desc_none' => ''
                    ),
                    array(
                        'id' => 'page',
                        'type' => 'post',
                        'name' => esc_html__('Page', 'docspress'),
                        'post_type' => 'thimdocs',
                        'field_type' => 'select_advanced',
                        'hidden' => ['type', '!=', 'page'],
                        'desc_none' => '',
                    ),
                 ),
            ),
        ),
    );
    return $meta_boxes;
}
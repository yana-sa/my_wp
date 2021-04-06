<?php
/*
Plugin Name: Game
Plugin URI: http://localhost:8000/
Description: Game plugin
Author: Unknown Yana
Author URI: http://localhost:8000
Version: 1.0.0
*/

function game_script_enqueue()
{
    wp_register_script('game-js', get_stylesheet_directory_uri() . '/js/game.js', ['jquery']);
    wp_localize_script('game-js', 'myAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);

    wp_enqueue_script('jquery-js');
    wp_enqueue_script('game-js');
}

add_action('init', 'game_script_enqueue');

function insert_cell($x, $y)
{
    $title = 'Cell ' . $x . ':' . $y;
    $data = [
        'post_type' => 'cell',
        'post_name' => 'cell',
        'post_title' => $title,
        'post_content' => 'X:' . $x . ', Y:' . $y,
        'post_status' => 'publish',
    ];

    $cell_id = wp_insert_post($data, true);
    update_post_meta($cell_id, '_x', $x);
    update_post_meta($cell_id, '_y', $y);
}

function game_plugin_activate()
{
    for ($x = 1; $x <= 10; $x++) {
        for ($y = 1; $y <= 10; $y++) {
            insert_cell($x, $y);
        }
    }

    do_action('game_plugin_activate');
}

register_activation_hook(__FILE__, 'game_plugin_activate');

function add_post_types()
{
    register_post_type('cell', [
            'labels' => [
                'name' => 'Cells',
                'singular_name' => 'Cell',
                'add_new' => 'Add Cell',
                'all_items' => 'All Cell',
                'edit_item' => 'Edit Cell',
                'view_item' => 'View Cell'
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'cell'],
            'capability_type' => 'post',
            'show_in_rest' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-smiley',
            'supports' => ['title', 'editor', 'custom-fields'],
            'menu_position' => 3,
        ]
    );

    register_post_type('building', [
            'labels' => [
                'name' => 'Buildings',
                'singular_name' => 'Building',
                'add_new' => 'Add Building',
                'all_items' => 'All Building',
                'edit_item' => 'Edit Building',
                'view_item' => 'View Building'
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'building'],
            'capability_type' => 'post',
            'show_in_rest' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-admin-home',
            'supports' => ['title', 'editor', 'custom-fields'],
            'menu_position' => 3,
        ]
    );
}

add_action('init', 'add_post_types');

function add_building_type()
{
    register_taxonomy('building_type', 'building', [
        'hierarchical' => false,
        'labels' => [
            'name' => _x('Building Types', 'taxonomy general name'),
            'singular_name' => _x('Building Type', 'taxonomy singular name'),
            'search_items' => __('Search Building Types'),
            'all_items' => __('All Building Types'),
            'edit_item' => __('Edit Building Type'),
            'update_item' => __('Update Building Type'),
            'add_new_item' => __('Add new Building Type'),
            'new_item_name' => __('New Building Type name'),
            'menu_name' => __('Building Types')],
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'taxonomies' => ['building_type'],
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => ['slug' => 'building_type']
    ]);
}

add_action('init', 'add_building_type', 0);

function building_type_box()
{
    add_meta_box(
        'building_type',
        __('Building Type', 'sitepoint'),
        'building_type_box_content',
        'building',
        'side'
    );
}

add_action('add_meta_boxes_building', 'building_type_box');

function building_type_box_content($post)
{
    echo '<h3>Building Types';
    $building_types = get_terms('building_type', ['hide_empty' => false]);;
    $type = get_post_meta($post->ID, '_building_type');
    foreach ($building_types as $building_type) {
        echo '<label for="building_type"><h3>Building Types</h3></label>
            <select name="building_type" id="building_type">';
        echo '<option value="' . $building_type->slug . '" ' . ($type == $building_type->slug) ? "selected" : "" . '>' . $building_type->name . '</option>';
    }
}


function building_type_edit_image($term)
{
    echo '<img src="/wp-content/uploads/2021/04/' . $term->slug . '.png" alt="' . $term->name . '">';
}

add_action( 'building_type_edit_form_fields', 'building_type_edit_image', 10 );

function building_type_columns($building_type_columns): array
{
    $new_columns = array(
        'cb' => '<input type="checkbox" />',
        'name' => __('Name'),
        'header_icon' => __('Image'),
        'slug' => __('Slug'),
        'posts' => __('Posts')
    );
    return $new_columns;
}

add_filter("manage_edit-building_type_columns", 'building_type_columns');

function manage_building_type_columns($out, $column_name, $building_type_id) {
    $theme = get_term($building_type_id, 'building_type');
    switch ($column_name) {
        case 'header_icon':
            $data = maybe_unserialize($theme->description);
            $out .= '<img src="/wp-content/uploads/2021/04/' . $theme->slug . '.png">';
            break;

        default:
            break;
    }
    return $out;
}
add_filter("manage_building_type_custom_column", 'manage_building_type_columns', 10, 3);

function get_cells(): array
{
    $cells = [];
    for ($i = 10; $i >= 1; $i--) {
        $cellscol = [];
        $query = new WP_Query([
            'post_type' => 'cell',
            'nopaging' => true,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => '_y',
                    'value' => $i
                ],
                [
                    'key' => '_x',
                    'value' => range(1, 10),
                ]
            ],
            'orderby' => 'meta_value_num',
            'order' => 'DESC'
        ]);

        while ($query->have_posts()) {
            $query->the_post();
            $cellscol[] = [
                'x' => get_post_meta(get_the_ID(), '_x')[0],
                'y' => get_post_meta(get_the_ID(), '_y')[0]
            ];
        }
        $cells[$i] = $cellscol;
    }

    return $cells;
}

function add_building()
{
    $x = !empty($_POST['x']) ? $_POST['x'] : null;
    $y = !empty($_POST['y']) ? $_POST['y'] : null;
    $user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : null;
    $building_type = !empty($_POST['building_type']) ? $_POST['building_type'] : null;
    $user_balance = get_user_meta($user_id, 'balance')[0];

    $res = [$building_type,
        $user_id,
        $user_balance,
        $x,
        $y];

    wp_send_json($res);
}

add_action('wp_ajax_add_building', 'add_building');

function get_building_price()
{
    $building_type = !empty($_POST['building_type']) ? $_POST['building_type'] : null;

    wp_send_json($building_type);
}

add_action('wp_ajax_get_building_price', 'get_building_price');

function game_plugin_deactivate()
{
    $query = new WP_Query(['post_type' => ['cell', 'building']]);

    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        wp_delete_post($post_id, true);
    }

    do_action('game_plugin_deactivate');
}

register_deactivation_hook(__FILE__, 'game_plugin_deactivate');

/*
barrack
farm
mine
sawmill
*/
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
    wp_register_script('sad_game-js', get_stylesheet_directory_uri() . '/js/sad_game.js', ['jquery']);
    wp_localize_script('sad_game-js', 'myAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);

    wp_enqueue_script('jquery-js');
    wp_enqueue_script('game-js');
    wp_enqueue_script('sad_game-js');
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
            'show_in_rest' => false,
            'show_in_menu' => false,
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

function building_type_menu()
{
    add_menu_page('Building Types', 'Building Types', 'add_users', 'edit-tags.php?taxonomy=building_type', '', 'dashicons-admin-home', 3);
}

add_action('admin_menu', 'building_type_menu');

function user_balance($user)
{
    echo '<label for="balance"><h3>Balance</h3></label>
    <input type="number" name="balance" id="balance" value="' . get_the_author_meta('balance', $user->ID) . '"/>$
    <br/>';
}

add_action('show_user_profile', 'user_balance');
add_action('edit_user_profile', 'user_balance');

function save_user_balance($user_id)
{
    if (!is_admin($user_id)) {
        update_user_meta($user_id, 'balance', $_POST['balance']);
    }
}

add_action('personal_options_update', 'save_user_balance');
add_action('edit_user_profile_update', 'save_user_balance');

function building_box()
{
    add_meta_box(
        'building',
        __('Building', 'sitepoint'),
        'building_box_content',
        'cell',
        'side'
    );
}

add_action('add_meta_boxes_cell', 'building_box');

function building_box_content($post)
{
    $building = get_post_meta($post->ID, '_building') ? get_the_title(get_post_meta($post->ID, '_building')[0]) : 'Cell is empty';
    echo $building;
}

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
    $type = get_post_meta($post->ID, '_building_type') ? ucfirst(get_post_meta($post->ID, '_building_type')[0]) : 'No type';
    echo $type;
}

function cell_box()
{
    add_meta_box(
        'cell',
        __('Position', 'sitepoint'),
        'cell_box_content',
        'building',
        'side'
    );
}

add_action('add_meta_boxes_building', 'cell_box');

function cell_box_content($post)
{
    $cell = get_post_meta($post->ID, '_cell') ? get_the_title(get_post_meta($post->ID, '_cell')[0]) : 'Not placed';
    echo $cell;
}

function building_type_image($term)
{
    echo '<img src="wp-content/plugins/game_plugin/images/' . $term->slug . '.png" alt="' . $term->name . '">';
}

add_action('building_type_edit_form_fields', 'building_type_image', 10);

function building_type_price($building_type)
{
    $price = get_option('price_' . $building_type->slug);
    $out = '<tr class="form-field">';
    $out .= '<th scope="row"><label for="price">Price</label></th>';
    $out .= '<td><input type="number" name="price" value="';
    $out .= $price ? $price : '';
    $out .= '">$</td></tr>';
    echo $out;
}

add_action('building_type_edit_form_fields', 'building_type_price', 10);

function building_type_price_save($term_id)
{
    if (isset($_POST['price'])) {
        $building_type = get_term($term_id, 'building_type');
        update_option('price_' . $building_type->slug, $_POST['price']);
    }
}

add_action('create_building_type', 'building_type_price_save');
add_action('edited_building_type', 'building_type_price_save');

function building_type_columns(): array
{
    return [
        'cb' => '<input type="checkbox" />',
        'name' => __('Name'),
        'price' => __('Price'),
        'header_icon' => __('Image'),
        'slug' => __('Slug'),
        'posts' => __('Count')
    ];
}

add_filter("manage_edit-building_type_columns", 'building_type_columns');

function manage_building_type_columns($out, $column_name, $building_type_id)
{
    $building_type = get_term($building_type_id, 'building_type');
    switch ($column_name) {
        case 'header_icon':
            $out .= '<img src="/wp-content/plugins/game_plugin/images/' . $building_type->slug . '.png" style="width: 100px;">';
            break;
        case 'price':
            $price = get_option('price_' . $building_type->slug);
            $out .= $price . '$';
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
            $building_id = get_post_meta(get_the_ID(), '_building');
            $building = $building_id ? get_post_meta($building_id[0], '_building_type')[0] : '';
            $cellscol[] = [
                'x' => get_post_meta(get_the_ID(), '_x')[0],
                'y' => get_post_meta(get_the_ID(), '_y')[0],
                'building' => $building
            ];
        }
        $cells[$i] = $cellscol;
    }

    return $cells;
}

function handle_add_building()
{
    $x = !empty($_POST['x']) ? $_POST['x'] : null;
    $y = !empty($_POST['y']) ? $_POST['y'] : null;
    $user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : null;
    $building_type = !empty($_POST['building_type']) ? $_POST['building_type'] : null;
    $price = get_option('price_' . $building_type);
    $user_balance = get_user_meta($user_id, 'balance')[0];
    $response = [];

    if (!$x && !$y && !$user_id && !$building_type) {
        $response = [
            'status' => 'error',
            'message' => 'Something went wrong!'
        ];
    }

    if ($user_balance < $price) {
        $response = [
            'status' => 'error',
            'message' => 'You have insufficient balance for this purchase!'
        ];
    }

    if (empty($response)) {
        $upd_balance = $user_balance - $price;
        update_user_meta($user_id, 'balance', $upd_balance);
        add_building($x, $y, $building_type);

        $response = [
            'status' => 'success',
            'message' => 'A ' . ucfirst($building_type) . ' building was successfully bought',
            'building' => $building_type
        ];
    }

    wp_send_json($response);
}

add_action('wp_ajax_handle_add_building', 'handle_add_building');

function add_building($x, $y, $building_type)
{
    $building_data = [
        'post_type' => 'building',
        'post_name' => 'building',
        'post_title' => ucfirst($building_type),
        'post_status' => 'publish',
    ];
    $building_id = wp_insert_post($building_data, true);

    $query = new WP_Query([
        'post_type' => 'cell',
        'nopaging' => true,
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => '_y',
                'value' => $y
            ],
            [
                'key' => '_x',
                'value' => $x,
            ]
        ],
    ]);

    if ($query->have_posts()) {
        $query->the_post();
        update_post_meta($building_id, '_cell', get_the_ID());
        update_post_meta($building_id, '_building_type', $building_type);
        update_post_meta(get_the_ID(), '_building', $building_id);
    }

}

function handle_remove_building()
{
    $x = !empty($_POST['x']) ? $_POST['x'] : null;
    $y = !empty($_POST['y']) ? $_POST['y'] : null;
    $user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : null;
    $building_type = !empty($_POST['building']) ? $_POST['building'] : null;
    $response = [];

    if (!$x && !$y && !$user_id && !$building_type) {
        $response = [
            'status' => 'error',
            'message' => 'Something went wrong!'
        ];
    }

    if (empty($response)) {
        remove_building($x, $y);
        $response = [
            'status' => 'success',
            'message' => 'A ' . ucfirst($building_type) . ' building was successfully removed',
        ];
    }

    wp_send_json($response);
}

add_action('wp_ajax_handle_remove_building', 'handle_remove_building');

function remove_building($x, $y)
{
    $query = new WP_Query([
        'post_type' => 'cell',
        'nopaging' => true,
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => '_y',
                'value' => $y
            ],
            [
                'key' => '_x',
                'value' => $x,
            ]
        ],
    ]);

    if ($query->have_posts()) {
        $query->the_post();
        $building_id = get_post_meta(get_the_ID(), '_building')[0];
        delete_post_meta(get_the_ID(), '_building', $building_id);
        wp_delete_post($building_id);
    }
}

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
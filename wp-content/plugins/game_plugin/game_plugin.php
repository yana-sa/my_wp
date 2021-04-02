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

function cell_post_type()
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
            'menu_position' => 4,
        ]
    );
}

add_action('init', 'cell_post_type');

function get_cells()
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
            'orderby'  => 'meta_value_num',
            'order'    => 'DESC'
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

function game_plugin_deactivate()
{
    $query = new WP_Query(['post_type' => 'cell']);

    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        wp_delete_post($post_id, true);
    }

    do_action('game_plugin_deactivate');
}

register_deactivation_hook(__FILE__, 'game_plugin_deactivate');
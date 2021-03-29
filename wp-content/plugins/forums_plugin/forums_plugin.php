<?php
/*
Plugin Name: Forums
Plugin URI: http://localhost:8000/
Description: Forums plugin
Author: Unknown Yana
Author URI: http://localhost:8000
Version: 1.0.0
*/

function insert_forum()
{
    $title = 'The Forum ' . rand(1, 999);
    $randdesc = 'This is a test description ' . rand(1, 999) . '. Date: ' . date("d.m.Y");
    $randorder = rand(1, 100);

    $data = [
        'post_type' => 'forum',
        'post_name' => 'forum',
        'post_title' => $title,
        'post_content' => $randdesc,
        'post_status' => 'publish',
    ];
    $post_id = wp_insert_post($data, true);

    update_post_meta($post_id, '_order', $randorder);
}

function forums_plugin_activate()
{
    $i = 1;
    while ($i++ <= 10) {
        insert_forum();
    }
    do_action('forums_plugin_activate');
}

register_activation_hook(__FILE__, 'forums_plugin_activate');

function create_post_types()
{
    register_post_type('forum', [
            'labels' => [
                'name' => 'Forums',
                'singular_name' => 'Forum',
                'add_new' => 'Add Forum',
                'all_items' => 'All Forum',
                'edit_item' => 'Edit Forum',
                'view_item' => 'View Forum'
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'forum'],
            'capability_type' => 'post',
            'show_in_rest' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-format-chat',
            'supports' => ['title', 'editor', 'custom-fields'],
            'menu_position' => 4,
        ]
    );

    register_post_type('topic', [
            'labels' => [
                'name' => 'Topics',
                'singular_name' => 'Topic',
                'add_new' => 'Add Topic',
                'all_items' => 'All Topic',
                'edit_item' => 'Edit Topic',
                'view_item' => 'View Topic'
            ],
            'hierarchical' => true,
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'topic',
                'with_front' => false],
            'capability_type' => 'post',
            'show_in_rest' => false,
            'show_in_menu' => false,
            'supports' => ['page-attributes', 'title', 'editor', 'custom-fields'],
        ]
    );

    register_post_type('topic_post', [
            'labels' => [
                'name' => 'Topic posts',
                'singular_name' => 'Topic post',
                'add_new' => 'Add Topic post',
                'all_items' => 'All Topic post',
                'edit_item' => 'Edit Topic post',
                'view_item' => 'View Topic post'
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'topic_post'],
            'capability_type' => 'post',
            'show_in_rest' => false,
            'show_in_menu' => false,
            'supports' => ['title', 'editor', 'custom-fields'],
        ]
    );
}

add_action('init', 'create_post_types');

function forum_order_box()
{
    add_meta_box(
        'order',
        __('Order', 'sitepoint'),
        'forum_order_box_content',
        'forum',
        'side'
    );
}

add_action('add_meta_boxes_forum', 'forum_order_box');

function forum_order_box_content($post)
{
    $value = get_post_meta($post->ID, '_order', true);
    echo "<input type='number' style='width:95%' id='order' name='order' value='" . $value . "'>";
}

function forum_order_box_save($post_id)
{
    $order = !empty($_POST['order']) ? $_POST['order'] : 0;
    update_post_meta($post_id, '_order', $order);
}

add_action('save_post', 'forum_order_box_save');

function topic_forum_id_box()
{
    add_meta_box(
        'forum_id',
        __('Forum ID', 'sitepoint'),
        'topic_forum_id_box_content',
        'topic',
        'side'
    );
}

add_action('add_meta_boxes_forum', 'topic_forum_id_box');

function topic_forum_id_box_content($post)
{
    $query = new WP_Query([
        'post_type' => 'topic',
        'meta_key' => '_forum_id',
        'meta_value' => $post->ID
    ]);

    while ($query->have_posts()) {
        $query->the_post();
        echo "<li>" . get_the_title() . "</li>";
    }
}

function topic_post_topic_id_box()
{
    add_meta_box(
        'topic_id',
        __('Topic ID', 'sitepoint'),
        'topic_post_topic_id_box_content',
        'topic_post',
        'side'
    );
}

add_action('add_meta_boxes_forum', 'topic_post_topic_id_box');

function topic_post_topic_id_box_content($post)
{
    $query = new WP_Query([
        'post_type' => 'topic_post',
        'meta_key' => '_topic_id',
        'meta_value' => $post->ID
    ]);

    while ($query->have_posts()) {
        $query->the_post();
        echo "<li>" . get_the_title() . "</li>";
    }
}

function forum_list_data()
{
    $forums = [];
    $query = new WP_Query([
        'post_type' => 'forum',
        'meta_key' => '_order',
        'orderby' => ['meta_value_num' => 'ASC'],
    ]);

    while ($query->have_posts()) {
        $query->the_post();
        $forums[] = [
            'title' => get_the_title(),
            'link' => get_permalink(),
        ];
    }

    return $forums;
}

function handle_add_new_topic()
{
    $topic = !empty($_POST['title']) ? $_POST['title'] : null;
    $forum_id = !empty($_POST['forum_id']) ? $_POST['forum_id'] : null;
    $content = !empty($_POST['content']) ? $_POST['content'] : null;

    if ($topic || $forum_id || $content) {
        $topic_data = [
            'post_type' => 'topic',
            'post_name' => 'topic',
            'post_title' => $topic,
            'post_status' => 'publish',
            'post_author' => 1,
        ];

        $topic_id = wp_insert_post($topic_data);
        update_post_meta($topic_id, '_forum_id', $forum_id);

        $topic_post_data = [
            'post_type' => 'topic_post',
            'post_name' => 'topic_post',
            'post_title' => $topic,
            'post_content' => $content,
            'post_author' => get_current_user_id(),
            'post_status' => 'publish',
        ];

        $topic_post_id = wp_insert_post($topic_post_data);
        update_post_meta($topic_post_id, '_topic_id', $topic_id);

        wp_redirect(get_post_permalink($topic_id));
        exit;
    }
}

add_action('wp_loaded', 'handle_add_new_topic');

function handle_add_new_topic_post()
{
    $post_title = !empty($_POST['post_title']) ? $_POST['post_title'] : null;
    $post_content = !empty($_POST['post_content']) ? $_POST['post_content'] : null;
    $topic_id = !empty($_POST['topic_id']) ? $_POST['topic_id'] : null;

    if ($post_title || $topic_id || $post_content) {
        $topic_post_data = [
            'post_type' => 'topic_post',
            'post_name' => 'topic_post',
            'post_title' => $post_title,
            'post_content' => $post_content,
            'post_status' => 'publish',
        ];

        $topic_post_id = wp_insert_post($topic_post_data);
        update_post_meta($topic_post_id, '_topic_id', $topic_id);
    }
}

function forums_script_enqueue()
{
    wp_register_script('forums-js', get_stylesheet_directory_uri() . '/js/forums.js', ['jquery']);
    wp_localize_script('forums-js', 'myAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);
    wp_register_script('activity_statistics-js', get_stylesheet_directory_uri() . '/js/activity_statistics.js', ['jquery']);
    wp_localize_script('activity_statistics-js', 'myAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);
    wp_register_script('chart-min-js', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js');

    wp_enqueue_script('jquery-js');
    wp_enqueue_script('forums-js');
    wp_enqueue_script('activity_statistics-js');
    wp_enqueue_script('chart-min-js');
}

add_action('init', 'forums_script_enqueue');

function topic_posts_pagination()
{
    $page = !empty($_POST['page']) ? $_POST['page'] : null;
    $topic_id = !empty($_POST['topic_id']) ? $_POST['topic_id'] : null;
    $query = new WP_Query([
        'post_type' => 'topic_post',
        'meta_key' => '_topic_id',
        'meta_value' => $topic_id,
        'paged' => $page,
        'posts_per_page' => 3,
    ]);

    if ($topic_id && $page) {
        $response = [];
        while ($query->have_posts()) {
            $query->the_post();
            $response[] = [
                'link' => get_the_permalink(),
                'title' => get_the_title(),
                'content' => get_the_content(),
                'author_pic' => get_avatar(get_the_author_meta('ID')),
                'author_name' => get_the_author_meta('display_name'),
                'author_role' => ucfirst(get_the_author_meta('roles')[0]),
            ];
        }

        wp_send_json($response);
    }
}

add_action('wp_ajax_topic_posts_pagination', 'topic_posts_pagination');

function users_activity_statistics()
{
    global $wpdb;
    $month = $_POST['month'];
    $statistics = $wpdb->get_results(
        "SELECT u.display_name AS `author`, DAY(p.`post_date`) AS `day`, COUNT(*) AS posts
                FROM wp_posts p
                                                
                INNER JOIN wp_users u
                ON u.ID = p.post_author
                                                
                WHERE p.post_type = 'topic_post'
                AND MONTH(p.`post_date`) = $month 
                
                   GROUP BY author, `day`
                   ORDER BY author",
        ARRAY_A);

    $statistics_upd = [];
    $authors = array_unique(array_column($statistics, 'author'));
    $days_num = date('t', mktime(0, 0, 0, $month, 1, 2021));
    foreach ($authors as $author) {
        $statistics_upd[$author] = array_values(array_fill(1, $days_num, "0"));
    }

    foreach ($statistics as $stat) {
        $statistics_upd[$stat['author']][$stat['day']] = $stat['posts'];
    }

    wp_send_json($statistics_upd);
}

add_action('wp_ajax_users_activity_statistics', 'users_activity_statistics');

function forums_plugin_deactivate()
{
    $query = new WP_Query(['post_type' => ['forum', 'topic', 'topic_post']]);

    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        wp_delete_post($post_id, true);
    }

    do_action('forums_plugin_deactivate');
}

register_deactivation_hook(__FILE__, 'forums_plugin_deactivate');
<?php
/*
Plugin Name: Organizations
Plugin URI: http://localhost:8000/
Description: Organizations plugin
Author: Unknown Yana
Author URI: http://localhost:8000
Version: 1.0.0
*/

function insert_organzation()
{
    $title = 'The organization ' . rand(1, 999);
    $content = 'This is an organization description ' . rand(1, 999);
    $leader = rand(1, 6);
    $members = [rand(1, 6), rand(1, 6)];

    $data = [
        'post_type' => 'organization',
        'post_name' => 'organization',
        'post_title' => $title,
        'post_content' => $content,
        'post_status' => 'publish',
        'post_author' => $leader
    ];

    $organization_id = wp_insert_post($data, true);
    update_post_meta($organization_id, '_leader', $leader);
    update_post_meta($organization_id, '_members', $members);
}

function organizations_plugin_activate()
{
    $i = 1;
    while ($i++ <= 10) {
        insert_organzation();
    }
    do_action('organizations_plugin_activate');
}

register_activation_hook(__FILE__, 'organizations_plugin_activate');

function organization_post_type()
{
    register_post_type('organization', [
            'labels' => [
                'name' => 'Organizations',
                'singular_name' => 'Organization',
                'add_new' => 'Add Organization',
                'all_items' => 'All Organization',
                'edit_item' => 'Edit Organization',
                'view_item' => 'View Organization'
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'organization'],
            'capability_type' => 'post',
            'show_in_rest' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-groups',
            'supports' => ['title', 'editor', 'custom-fields'],
            'menu_position' => 4,
        ]
    );
}

add_action('init', 'organization_post_type');

function org_members_box()
{
    add_meta_box(
        'members',
        __('Members', 'sitepoint'),
        'org_members_box_content',
        'organization',
        'side'
    );
}

add_action('add_meta_boxes_organization', 'org_members_box');

function org_members_box_content($post)
{
    $members = get_post_meta($post->ID, '_members', true);
    if ($members) {
        $output = '<ul>';
        foreach ($members as $member) {
            $m = get_userdata($member);
            $output .= '<li>' . $m->display_name . '</li>';
        }
        $output .= '</ul>';
        echo $output;
    } else {
        echo 'No members';
    }
}

function org_leader_box()
{
    add_meta_box(
        'leader',
        __('Leader', 'sitepoint'),
        'org_leader_box_content',
        'organization',
        'side'
    );
}

add_action('add_meta_boxes_organization', 'org_leader_box');

function org_leader_box_content($post)
{
    $value = get_post_meta($post->ID, '_leader', true);
    $leader = get_userdata($value);
    echo $leader->display_name;
}

function org_parent_box()
{
    add_meta_box(
        'parent',
        __('Parent', 'sitepoint'),
        'org_parent_box_content',
        'organization',
        'side'
    );
}

add_action('add_meta_boxes_organization', 'org_parent_box');

function org_parent_box_content($post)
{
    $value = get_post_meta($post->ID, '_parent', true);
    echo get_the_title($value);
}

function add_new_organization()
{
    $name = !empty($_POST['org_name']) ? $_POST['org_name'] : null;
    $descr = !empty($_POST['org_desc']) ? $_POST['org_desc'] : null;
    $members = !empty($_POST['org_member']) ? $_POST['org_member'] : null;
    $parent = !empty($_POST['org_parent']) ? $_POST['org_parent'] : null;
    $leader = !empty($_POST['org_leader']) ? $_POST['org_leader'] : null;


    if ($name && $descr && $members && $parent) {
        $org_data = [
            'post_type' => 'organization',
            'post_name' => 'organization',
            'post_title' => $name,
            'post_content' => $descr,
            'post_status' => 'publish',
        ];

        $org_id = wp_insert_post($org_data);
        update_post_meta($org_id, '_members', $members);
        update_post_meta($org_id, '_leader', $leader);

        if ($parent !== 'none') {
            update_post_meta($org_id, '_parent', $parent);
        }

        wp_redirect(get_post_permalink($org_id));
        exit;
    }
}

add_action('wp_loaded', 'add_new_organization');

function organizations_data(): array
{
    $organizations = new WP_Query([
        'post_type' => 'organization',
        'nopaging' => true,
        'meta_query' => [
            ['key' => '_parent',
                'compare' => 'NOT EXISTS'],
        ]
    ]);
    $orgs = [];

    while ($organizations->have_posts()) {
        $organizations->the_post();
        $id = get_the_ID();
        $orgs[] = [
            'id' => $id,
            'name' => get_the_title(),
            'link' => get_the_permalink(),
            'leader' => get_post_meta(get_the_ID(), '_leader')[0],
            'subsidiaries' => get_organization_subsidiaries($id)
        ];
    }

    return $orgs;
}

function get_organization_subsidiaries($org_id): array
{
    $subsidiaries = new WP_Query([
        'post_type' => 'organization',
        'meta_key' => '_parent',
        'meta_value' => $org_id
    ]);

    $subsidiaries_data = [];
    while ($subsidiaries->have_posts()) {
        $subsidiaries->the_post();
        $parent = get_post_meta(get_the_ID(), '_parent')[0];
        $subsidiaries_data[] = [
            'id' => get_the_ID(),
            'name' => get_the_title(),
            'link' => get_the_permalink(),
            'leader' => get_post_meta(get_the_ID(), '_leader')[0],
            'parent' => $parent,
            'parent_leader' => get_post_meta($parent, '_leader')[0]
        ];
    }

    wp_reset_query();
    return $subsidiaries_data;
}

function edit_organization_data(): array
{
    $id = !empty($_POST['id']) ? $_POST['id'] : null;
    $org = get_post($id, ARRAY_A);
    $parent = get_post_meta($id, '_parent');
    $parent_leader = ($parent) ? get_post_meta($parent[0], '_leader') : [];

    return [
        'id' => $id,
        'name' => $org['post_title'],
        'descr' => $org['post_content'],
        'members' => get_post_meta($id, '_members'),
        'leader' => get_post_meta($id, '_leader'),
        'parent' => $parent,
        'parent_leader' => $parent_leader
    ];
}

function edit_organization()
{
    $id = !empty($_POST['org_id']) ? $_POST['org_id'] : null;
    $name = !empty($_POST['org_name']) ? $_POST['org_name'] : null;
    $descr = !empty($_POST['org_desc']) ? $_POST['org_desc'] : null;
    $members = !empty($_POST['member']) ? $_POST['member'] : null;
    $leader = !empty($_POST['org_leader']) ? $_POST['org_leader'] : null;

    if ($id) {
        $org_upd = [];
        if ($name && $descr) {
            $org_upd['ID'] = $id;
            $org_upd['post_title'] = $name;
            $org_upd['post_content'] = $descr;
            wp_update_post($org_upd);
        }

        if ($members) {
            update_post_meta($id, '_members', $members);
        }

        if ($leader) {
            update_post_meta($id, '_leader', $leader);
        }

        wp_redirect(get_permalink(get_page_by_path('organizations')));
        exit;
    }
}

function organizations_plugin_deactivate()
{
    $query = new WP_Query(['post_type' => 'organization']);

    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        wp_delete_post($post_id, true);
    }

    do_action('organizations_plugin_deactivate');
}

register_deactivation_hook(__FILE__, 'organizations_plugin_deactivate');
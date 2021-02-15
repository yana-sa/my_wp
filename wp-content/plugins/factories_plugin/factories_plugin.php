<?php
/*
Plugin Name: Factories
Plugin URI: http://localhost:8000/
Description: Factories plugin
Author: Unknown Yana
Author URI: http://localhost:8000
Version: 1.0.0
*/

//Seeding data on plugin activation
function insert_factory()
{
    $ftitle = 'The Factory ' . rand(1, 999);
    $randdesc = 'This is a test description ' . rand(1, 999) . '. Date: ' . date("d.m.Y");
    $randprofit = rand(10000, 1000000);

    $fdata = [
        'post_type' => 'factories',
        'post_name' => 'factories',
        'post_title' => $ftitle,
        'post_content' => $randdesc,
        'post_status' => 'publish',
    ];
    $post_id = wp_insert_post($fdata, true);

    update_post_meta($post_id, '_monthly_profit', $randprofit);
}

function insert_company()
{
    $ctitle = 'The Company ' . rand(1, 999);
    $randdesc = 'This is a test description ' . rand(1, 999) . '. Date: ' . date("d.m.Y");
    $randbalance = rand(100000, 1000000);

    $cdata = [
        'post_type' => 'companies',
        'post_name' => 'companies',
        'post_title' => $ctitle,
        'post_content' => $randdesc,
        'post_status' => 'publish',
    ];
    $post_id = wp_insert_post($cdata, true);

    update_post_meta($post_id, '_balance', $randbalance);
}

function factories_plugin_activate()
{
    $i = 1;
    while ($i++ <= 10) {
        insert_factory();
        insert_company();
    }
    do_action('factories_plugin_activate');
}

register_activation_hook(__FILE__, 'factories_plugin_activate');

function create_money_transfer_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'money_transfer';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
		  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		  transferor_id BIGINT UNSIGNED NOT NULL,
		  transferee_id BIGINT UNSIGNED NOT NULL,
		  sum INT UNSIGNED NOT NULL,
          date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          UNIQUE KEY id (id),
		  
		FOREIGN KEY (transferor_id) REFERENCES wp_posts(ID)
        ON DELETE CASCADE,
		FOREIGN KEY (transferee_id) REFERENCES wp_posts(ID)
		ON DELETE CASCADE
		) $charset_collate;
		";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'create_money_transfer_table');

function create_post_types_and_taxonomy()
{
    register_post_type('factories', [
            'labels' => [
                'name' => 'Factories',
                'singular_name' => 'Factory',
                'add_new' => 'Add Factory',
                'all_items' => 'All Factories',
                'edit_item' => 'Edit Factory',
                'view_item' => 'View Factory'
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'factories'],
            'capability_type' => 'post',
            'show_in_rest' => true,
            'show_in_menu' => true,
            'taxonomies' => ['companies'],
            'supports' => ['title', 'editor', 'custom-fields'],
            'menu_position' => 5,
            'register_meta_box_cb' => 'monthly_profit_box']
    );

    register_post_type('companies', [
            'labels' => [
                'name' => 'Companies',
                'singular_name' => 'Company',
                'add_new' => 'Add Company',
                'all_items' => 'All Companies',
                'edit_item' => 'Edit Company',
                'view_item' => 'View Company'
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'companies'],
            'capability_type' => 'post',
            'show_in_rest' => true,
            'show_in_menu' => true,
            'supports' => ['title', 'editor', 'custom-fields'],
            'menu_position' => 5,
            'register_meta_box_cb' => 'balance_box',]
    );

    create_taxonomy();
}

add_action('init', 'create_post_types_and_taxonomy');

function create_taxonomy()
{
    register_taxonomy('company_factories', 'factories', [
        'hierarchical' => false,
        'labels' => [
            'name' => _x('Company`s Factories', 'taxonomy general name'),
            'singular_name' => _x('Company`s Factories', 'taxonomy singular name'),
            'search_items' => __('Search Company'),
            'all_items' => __('All Companies'),
            'edit_item' => __('Edit Company'),
            'update_item' => __('Update Company'),
            'add_new_item' => __('Add New Company'),
            'new_item_name' => __('New Company Name'),
            'menu_name' => __('Company`s Factories')],
        'show_ui' => false,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'show_in_quick_edit' => false,
        'query_var' => true,
        'rewrite' => ['slug' => 'company_factories']
    ]);
}

function insert_taxonomies($post_id, $post, $update)
{
    create_taxonomy();
    if ($update == true) {
        return;
    }
    if ($post->post_type == 'companies' && $post->post_status == 'publish') {
        wp_insert_term(
            $post->post_title,
            'company_factories',
            ['description' => $post->content,
                'slug' => $post->post_name,]
        );
    }
}

add_action('wp_insert_post', 'insert_taxonomies', 10, 3);

//Company selection box
function companies_selection_add_meta_box()
{
    add_meta_box('company_factories',
        'Company',
        'companies_selection_meta_box',
        'factories',
        'side');
}

add_action('add_meta_boxes', 'companies_selection_add_meta_box');

function companies_selection_meta_box()
{
    if (get_post_type() !== 'factories') {
        return;
    }
    $taxonomy = 'company_factories';
    $terms_arr = get_the_terms(get_the_ID(), $taxonomy);
    if ($terms_arr !== false) {
        $term_obj = $terms_arr[0];
        $is_checked = $term_obj->term_id;
    }

    $terms = get_terms($taxonomy, ['hide_empty' => 0]);
    echo '<div>';
    foreach ($terms as $term) {
        echo '<label id="company_factories" name="company_factories">
            <input type="radio" id="company_factories" name="company_factories" value="' . $term->name . '" ' . ((isset($is_checked) && $is_checked == $term->term_id) ? "checked" : "") . '/>' . $term->name . '<br />
            </label></br>';
    }
    echo '</div>';

    if (isset($_POST['company_factories'])) {
        wp_set_object_terms(get_the_ID(), $_POST['company_factories'], $taxonomy, false);
    }
}

add_action('edit_post_factories', 'companies_selection_meta_box', 10, 2);

//Create monthly_profit meta box for factories
function monthly_profit_box()
{
    add_meta_box(
        'monthly_profit',
        __('Monthly Profit', 'sitepoint'),
        'monthly_profit_content',
        'factories',
        'side'
    );
}

add_action('add_meta_boxes_factories', 'monthly_profit_box');

function monthly_profit_content($post)
{
    $value = get_post_meta($post->ID, '_monthly_profit', true);
    echo '<input type="number" style="width:95%" id="monthly_profit" name="monthly_profit" value="' . $value . '">$';
}

function monthly_profit_box_save($post_id)
{
    $profit = $_POST['monthly_profit'];
    if (!isset($profit)) {
        $profit = 0;
    }
    update_post_meta($post_id, '_monthly_profit', $profit);
}

add_action('save_post', 'monthly_profit_box_save');

//Create balance meta box for companies
function balance_box()
{
    add_meta_box(
        'balance',
        __('Blance', 'sitepoint'),
        'balance_box_content',
        'companies',
        'side'
    );
}

add_action('add_meta_boxes_factories', 'balance_box');

function balance_box_content($post)
{
    $value = get_post_meta($post->ID, '_balance', true);
    echo '<input type="number" style="width:95%" id="balance" name="balance" value="' . $value . '">$';
}

function balance_box_save($post_id)
{
    $balance = $_POST['balance'];
        if (!isset($balance)) {
        $balance = 0;
    }

    update_post_meta($post_id, '_balance', $balance);
}

add_action('save_post', 'balance_box_save');

//Transfer money on company post page
function company_money_transfer_data()
{
    $current_company = [
        'id' => get_the_ID(),
        'balance' => get_post_meta(get_the_ID(), '_balance', true),
    ];

    wp_reset_query();
    $query = new WP_Query(['post_type' => 'companies']);
    $companies_arr = [];

    while ($query->have_posts()) {
        $query->the_post();
        $company = [
            'id' => get_the_ID(),
            'title' => get_the_title(),
        ];
        if ($company['id'] !== $current_company['id']) {
            $companies_arr[] = $company;
        }
    }

    return [
        'current' => $current_company,
        'companies' => $companies_arr
    ];
}

function handle_company_money_transfer($transferor_id)
{
    global $wpdb;
    if (empty($_POST['transfer']) || empty($_POST['select_company']) || empty($_POST['sum'])) {
        return;
    }

    $transferee_id = $_POST['select_company'];
    $sum = $_POST['sum'];
    $transferor_balance = get_post_meta($transferor_id, '_balance', true);
    $transferee_balance = get_post_meta($transferee_id, '_balance', true);

    if ($transferor_balance >= $sum) {
        $upd_transferor_balance = $transferor_balance - $sum;
        update_post_meta($transferor_id, '_balance', $upd_transferor_balance);

        $upd_transferee_balance = $transferee_balance + $sum;
        update_post_meta($transferee_id, '_balance', $upd_transferee_balance);

        $wpdb->insert('wp_money_transfer', ['transferor_id' => $transferor_id, 'transferee_id' => $transferee_id, 'sum' => $sum], ['%d']);
    } else {
        echo 'Double-check the balance and the sum to be transferred!';
    }
}

// Get data for money transfer logs page
function money_transfer_logs()
{
    global $wpdb;
    $raw_logs = $wpdb->get_results("SELECT `transferor_id`, `transferee_id`, `sum`, `date` FROM `wp_money_transfer`", ARRAY_A);
    $logs = [];

    foreach ($raw_logs as $raw_log) {
        $date = strtotime($raw_log['date']);
        $logs[] = [
            'transferor' => get_the_title($raw_log['transferor_id']),
            'transferee' => get_the_title($raw_log['transferee_id']),
            'sum' => $raw_log['sum'] . ' $',
            'date' => date("d.m.Y H:i", $date)
        ];
    }

    return $logs;
}

//Get data for companies report page
function factories_data($term)
{
    wp_reset_query();
    $args = [
        'post_type' => 'factories',
        'company_factories' => $term->slug,
    ];

    $query = new WP_Query($args);
    $total_profit = 0;
    $factories_data = [];

    while ($query->have_posts()) {
        $query->the_post();
        $monthly_profit = esc_attr(get_post_meta(get_the_ID(), '_monthly_profit', true));
        $factories_data[] = [
            'title' => get_the_title(),
            'link' => get_permalink(),
            'monthly_profit' => $monthly_profit,
        ];
        $total_profit += $monthly_profit;
    }

    return [
        'profit' => $total_profit,
        'data' => $factories_data
    ];
}

function monthly_profit_report_data()
{
    $report_data = [];
    $terms = get_terms([
        'taxonomy' => 'company_factories',
        'hide_empty' => false,
    ]);

    foreach ($terms as $term) {
        $factories_data = factories_data($term);
        $report_data[] = [
            'title' => $term->name,
            'sum_profit' => ($factories_data !== null) ? $factories_data['profit'] : 0,
            'factories' => ($factories_data !== null) ? $factories_data['data'] : null,
        ];
    }

    return $report_data;
}

//Get data for company post
function company_post_data()
{
    $term = get_term_by('name', get_the_title(), 'company_factories');
    $factories_data = factories_data($term);

    return $res = [
        'profit' => ($factories_data !== null) ? $factories_data['profit'] : null,
        'factories' => ($factories_data !== null) ? $factories_data['data'] : null,
    ];
}

//Deleting data on plugin deactivation
function factories_plugin_deactivate()
{
    $query = new WP_Query([
        'post_type' => ['factories', 'companies'],
        'post_status' => 'publish'
    ]);

    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        wp_delete_post($post_id, true);
    }

    $terms = get_terms('company_factories', [
        'fields' => 'ids',
        'hide_empty' => false
    ]);

    foreach ($terms as $term) {
        wp_delete_term($term, 'company_factories');
    }

    do_action('factories_plugin_deactivate');
}

register_deactivation_hook(__FILE__, 'factories_plugin_deactivate');

function drop_money_transfer_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'money_transfer';

    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
}

register_deactivation_hook(__FILE__, 'drop_money_transfer_table');
<?php

function my_theme_enqueue_styles()
{
    wp_enqueue_style( 'twentytwentyone-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'twentytwentyone-child-style', get_stylesheet_uri() );
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles', 11);

function my_theme_scripts() {
    wp_enqueue_script('book-likes-js', get_stylesheet_directory_uri() . '/js/book-likes.js', ['jquery'], '3.5.1');
    wp_enqueue_script('jquery', get_template_directory_uri() . '/js/jquery.js', ['jquery'], '3.5.1');
}
add_action('wp_enqueue_scripts', 'my_theme_scripts');


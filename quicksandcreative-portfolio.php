<?php
 
/*
Plugin Name: Quicksand Creative Portfolio
Plugin URI: https://quicksandcreative.com/
Description: Provides a custom post type, meta boxes, and page template for a portfolio gallery
Author: Quicksand Creative
Version: 1.0
Author URI: https://quicksandcreative.com/
*/

if ( function_exists( 'add_theme_support' ) ) { 
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 280, 210, true ); // Normal post thumbnails
    add_image_size( 'screen-shot', 720, 540 ); // Full size screen
}

add_action( 'init', 'create_portfolio' );

function create_portfolio() {  
    $args = array(  
        'label' => __('Portfolio'),  
        'singular_label' => __('Project'),  
        'public' => true,
        'menu_position' => 3,
        'show_ui' => true,  
        'capability_type' => 'post',  
        'hierarchical' => false,  
        'rewrite' => true,  
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields')  
       );  
   
    register_post_type( 'portfolio' , $args );  
}

add_action( 'init', 'create_project_type_tax' );

function create_project_type_tax() {
    register_taxonomy(
        'project-type',
        'portfolio',
        array(
            'label' => __( 'Project Types' ),
            'singular_label' => __( 'Project Type' ),
            'rewrite' => true,
            'hierarchical' => true,
        )
    );
}

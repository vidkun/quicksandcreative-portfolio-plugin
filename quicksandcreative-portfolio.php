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

add_action( 'admin_init', 'portfolio_admin' );

function portfolio_admin() {
  add_meta_box( 'project_options_meta_box',
        'Project Options',
        'display_project_options_meta_box',
        'portfolio', 'side', 'high'
    );
}

function display_project_options_meta_box( $project ) {
  // Retrieve current name of the Director and Movie Rating based on review ID
    $project_link = esc_html( get_post_meta( $project->ID, 'project_link', true ) );
    // $movie_rating = intval( get_post_meta( $movie_review->ID, 'movie_rating', true ) );
    ?>
    <table>
      <tr>
        <td style="width: 100%">Project Link</td>
      </tr>
      <tr>
        <td style="width: 80%"><input type="text" size="80" name="project_link" value="<?php echo $project_link; ?>" /></td>
      </tr>
    </table>
    <?php
}
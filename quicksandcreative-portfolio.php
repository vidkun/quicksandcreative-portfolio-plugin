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
  add_meta_box( 'project_gallery_meta_box',
    'Project Gallery',
    'media_uploader_box',
    'portfolio', 'normal', 'high'
  );
}

function display_project_options_meta_box( $project ) {
  $project_link = esc_html( get_post_meta( $project->ID, 'project_link', true ) );
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

function media_uploader_box() {
  wp_nonce_field( basename(__FILE__), 'project_gallery_meta_nonce' );
  global $post;
  ?>

  <div class="project-gallery">
    <table class="form-table">
        <tr><td>
          <a class="gallery-add button" href="#" data-uploader-title="Add image(s) to project gallery" data-uploader-button-text="Add image(s)">Add image(s)</a>

          <ul id="gallery-metabox-list">
          <?php if ($ids) : foreach ($ids as $key => $value) : $image = wp_get_attachment_image_src($value); ?>

            <li class="project_gallery_image">
              <input type="hidden" name="qc_gallery_id[<?php echo $key; ?>]" value="<?php echo $value; ?>">
              <img class="image-preview" src="<?php echo $image[0]; ?>"><br />
              <a class="change-image button button-small" href="#" data-uploader-title="Change image" data-uploader-button-text="Change image">Change image</a><br>
              <small><a class="remove-image" href="#">Remove image</a></small>
            </li>

          <?php endforeach; endif; ?>
          </ul>

        </td></tr>
      </table>
    </div>
  <?php
}

function admin_scripts() {
  wp_enqueue_script('media-upload');
  wp_enqueue_script('thickbox');
  wp_enqueue_script( 'quicksandcreative-portfolio', plugin_dir_url(__FILE__) . '/includes/js/quicksandcreative-portfolio.js', array(), '1.0.0', true );
}

function admin_styles() {
  wp_enqueue_style('thickbox');
  wp_enqueue_style( 'quicksandcreative-portfolio', plugin_dir_url(__FILE__) . '/includes/css/portfolio.css' );
}

add_action('admin_print_scripts', 'admin_scripts');
add_action('admin_print_styles', 'admin_styles');

function project_gallery_meta_save($post_id) {
  if (!isset($_POST['project_gallery_meta_nonce']) || !wp_verify_nonce($_POST['project_gallery_meta_nonce'], basename(__FILE__))) return;
  if (!current_user_can('edit_post', $post_id)) return;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if(isset($_POST['qc_gallery_id'])) {
    update_post_meta($post_id, 'qc_gallery_id', $_POST['qc_gallery_id']);
  } else {
    delete_post_meta($post_id, 'qc_gallery_id');
  }
}
add_action('save_post', 'project_gallery_meta_save');

add_action( 'save_post', 'add_project_options_fields', 10, 2 );

function add_project_options_fields( $project_id, $project ) {
  // Check post type for portfolio
  if ( $project->post_type == 'portfolio' ) {
    // Store data in post meta table if present in post data
    if ( isset( $_POST['project_link'] ) && $_POST['project_link'] != '' ) {
      update_post_meta( $project_id, 'project_link', $_POST['project_link'] );
    }
  }
}
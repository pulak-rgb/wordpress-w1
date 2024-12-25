<?php
/**
 * Plugin Name: Related Posts
 * Plugin URI: https://example.com/related-posts
 * Description: Displays related posts under each post based on the same category, with a maximum of 5 related posts.
 * Version: 1.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL2
 */

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include necessary files
require_once plugin_dir_path( __FILE__ ) . 'includes/class-related-posts.php';

// Initialize the plugin
function run_related_posts_plugin() {
    $related_posts = new Related_Posts();
    $related_posts->init();
}

run_related_posts_plugin();

function related_posts_plugin_menu() {
 add_menu_page(
     'Related Posts Settings', // Page Title
     'Related Posts',          // Menu Title
     'manage_options',         // Capability
     'related-posts-settings', // Menu Slug
     'related_posts_settings_page', // Function to display settings page
     'dashicons-admin-post',   // Icon
     60                        // Position
 );
}
add_action('admin_menu', 'related_posts_plugin_menu');
function related_posts_settings_page() {
 ?>
 <div class="wrap">
     <h1>Related Posts Settings</h1>
     <form method="post" action="options.php">
         <?php
         settings_fields('related_posts_options_group');
         do_settings_sections('related-posts-settings');
         ?>
         <table class="form-table">
             <tr valign="top">
                 <th scope="row">Number of Posts per Row</th>
                 <td><input type="number" name="related_posts_per_row" value="<?php echo get_option('related_posts_per_row'); ?>" /></td>
             </tr>

             <tr valign="top">
                 <th scope="row">Number of Posts</th>
                 <td><input type="number" name="related_posts_number" value="<?php echo get_option('related_posts_number'); ?>" /></td>
             </tr>

             <tr valign="top">
                 <th scope="row">Select Post Order</th>
                 <td>
                     <select name="related_posts_order">
                         <option value="ASC" <?php selected(get_option('related_posts_order'), 'ASC'); ?>>Ascending</option>
                         <option value="DESC" <?php selected(get_option('related_posts_order'), 'DESC'); ?>>Descending</option>
                     </select>
                 </td>
             </tr>

             <tr valign="top">
                 <th scope="row">Select Post Order By</th>
                 <td>
                     <select name="related_posts_order_by">
                         <option value="date" <?php selected(get_option('related_posts_order_by'), 'date'); ?>>Date</option>
                         <option value="title" <?php selected(get_option('related_posts_order_by'), 'title'); ?>>Title</option>
                         <option value="ID" <?php selected(get_option('related_posts_order_by'), 'ID'); ?>>ID</option>
                     </select>
                 </td>
             </tr>

             <tr valign="top">
                 <th scope="row">Show Thumbnail</th>
                 <td><input type="checkbox" name="related_posts_show_thumbnail" value="1" <?php checked(get_option('related_posts_show_thumbnail'), 1); ?> /></td>
             </tr>

             <tr valign="top">
                 <th scope="row">Show Date</th>
                 <td><input type="checkbox" name="related_posts_show_date" value="1" <?php checked(get_option('related_posts_show_date'), 1); ?> /></td>
             </tr>

             <tr valign="top">
                 <th scope="row">Show Time</th>
                 <td><input type="checkbox" name="related_posts_show_time" value="1" <?php checked(get_option('related_posts_show_time'), 1); ?> /></td>
             </tr>

             <tr valign="top">
                 <th scope="row">Short Description</th>
                 <td><input type="checkbox" name="related_posts_short_description" value="1" <?php checked(get_option('related_posts_short_description'), 1); ?> /></td>
             </tr>

             <tr valign="top">
                 <th scope="row">Description Length</th>
                 <td><input type="number" name="related_posts_description_length" value="<?php echo get_option('related_posts_description_length'); ?>" /></td>
             </tr>

             <tr valign="top">
                 <th scope="row">Show Author Name</th>
                 <td><input type="checkbox" name="related_posts_show_author" value="1" <?php checked(get_option('related_posts_show_author'), 1); ?> /></td>
             </tr>
         </table>

         <?php submit_button(); ?>
     </form>
 </div>
 <?php
}
function related_posts_plugin_settings() {
 register_setting('related_posts_options_group', 'related_posts_per_row');
 register_setting('related_posts_options_group', 'related_posts_number');
 register_setting('related_posts_options_group', 'related_posts_order');
 register_setting('related_posts_options_group', 'related_posts_order_by');
 register_setting('related_posts_options_group', 'related_posts_show_thumbnail');
 register_setting('related_posts_options_group', 'related_posts_show_date');
 register_setting('related_posts_options_group', 'related_posts_show_time');
 register_setting('related_posts_options_group', 'related_posts_short_description');
 register_setting('related_posts_options_group', 'related_posts_description_length');
 register_setting('related_posts_options_group', 'related_posts_show_author');
}
add_action('admin_init', 'related_posts_plugin_settings');
function display_related_posts() {
 $posts_per_row = get_option('related_posts_per_row', 3);
 $posts_number = get_option('related_posts_number', 5);
 $order = get_option('related_posts_order', 'ASC');
 $order_by = get_option('related_posts_order_by', 'date');
 $show_thumbnail = get_option('related_posts_show_thumbnail', 1);
 $show_date = get_option('related_posts_show_date', 1);
 $show_time = get_option('related_posts_show_time', 1);
 $short_description = get_option('related_posts_short_description', 1);
 $description_length = get_option('related_posts_description_length', 100);
 $show_author = get_option('related_posts_show_author', 1);

 // Query related posts
 $args = array(
     'posts_per_page' => $posts_number,
     'orderby' => $order_by,
     'order' => $order,
 );

 $related_posts = new WP_Query($args);

 if ($related_posts->have_posts()) {
     echo '<div class="related-posts-container" style="display: grid; grid-template-columns: repeat(' . esc_attr($posts_per_row) . ', 1fr);">';
     while ($related_posts->have_posts()) {
         $related_posts->the_post();
         echo '<div class="related-post-item">';
         
         if ($show_thumbnail && has_post_thumbnail()) {
             the_post_thumbnail('thumbnail');
         }

         if ($show_date) {
             echo '<p>' . get_the_date() . '</p>';
         }

         if ($show_time) {
             echo '<p>' . get_the_time() . '</p>';
         }

         if ($short_description) {
             echo '<p>' . wp_trim_words(get_the_content(), $description_length) . '</p>';
         }

         if ($show_author) {
             echo '<p>By ' . get_the_author() . '</p>';
         }

         echo '</div>';
     }
     echo '</div>';
 }

 wp_reset_postdata();
}

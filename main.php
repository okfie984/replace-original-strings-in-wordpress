<?php
/*
  Plugin Name: Replace Original Strings
  Plugin URI:  https://mokfie.com/replace-original-strings/
  Description: This plugin to help you to replacing original strings in WP with new strings you needed without change .po or .mo files.
  Version:     1.0
  Author:      Mohammad Okfie
  Author URI:  https://mokfie.com
  Text Domain: ros
 */


 /*
  * Security check
  * Prevent direct access to the file.
 */
 define('ROS_LANG','ros');
 if ( ! defined( 'ABSPATH' ) ) {
 	exit;
 }
 add_action( 'plugins_loaded', 'ros_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 */
function ros_load_textdomain() {
  load_plugin_textdomain( ROS_LANG, false, basename( dirname( __FILE__ ) ) . '/languages' );
}

 /*
 * Creating a function to create our CPT for strings
 */

 function replace_original_strings_post_type() {

 // Set UI labels for Custom Post Type
 	$labels = array(
 		'name'                => _x( 'Replace Strings', 'Post Type General Name', ROS_LANG ),
 		'singular_name'       => _x( 'Replace String', 'Post Type Singular Name', ROS_LANG ),
 		'menu_name'           => __( 'Replace Original Strings', ROS_LANG ),
 		'parent_item_colon'   => __( 'Parent String', ROS_LANG ),
 		'all_items'           => __( 'All Strings', ROS_LANG ),
 		'view_item'           => __( 'View String', ROS_LANG ),
 		'add_new_item'        => __( 'Add New String', ROS_LANG ),
 		'add_new'             => __( 'Add New', ROS_LANG ),
 		'edit_item'           => __( 'Edit String', ROS_LANG ),
 		'update_item'         => __( 'Update String', ROS_LANG ),
 		'search_items'        => __( 'Search String', ROS_LANG ),
 		'not_found'           => __( 'Not Found', ROS_LANG ),
 		'not_found_in_trash'  => __( 'Not found in Trash', ROS_LANG ),
 	);

 // Set other options for Custom Post Type

 	$args = array(
 		'labels'              => $labels,
 		// Features this CPT supports in Post Editor
 		'supports'            => false,
 		// You can associate this CPT with a taxonomy or custom taxonomy.
 		//'taxonomies'          => array( 'genres' ),
 		/* A hierarchical CPT is like Pages and can have
 		* Parent and child items. A non-hierarchical CPT
 		* is like Posts.
 		*/
 		'hierarchical'        => false,
 		'public'              => true,
 		'show_ui'             => true,
 		'show_in_menu'        => true,
 		'show_in_nav_menus'   => false,
 		'show_in_admin_bar'   => false,
 		'menu_position'       => 5,
 		'can_export'          => false,
 		'has_archive'         => false,
 		'exclude_from_search' => true,
 		'publicly_queryable'  => true,
 		'capability_type'     => 'page',
 	);

 	// Registering your Custom Post Type
 	register_post_type( 'ros', $args );

 }

 /* Hook into the 'init' action so that the function
 * Containing our post type registration is not
 * unnecessarily executed.
 */

 add_action( 'init', 'replace_original_strings_post_type', 0 );

 include('metabox.php');


 add_filter('manage_edit-ros_columns', 'ros_admin_columns');
 function ros_admin_columns($columns) {
 		$new_columns = array(
 		'_ros_orginal_string' => __('Original String', ROS_LANG),
 		'_ros_orginal_domain_string' => __('Domain', ROS_LANG),
 		'_ros_new_string' => __('New String', ROS_LANG),
 		'_ros_match_string' => __('Match String', ROS_LANG),
 	);

     return array_merge($columns, $new_columns);

 }
  add_action('manage_posts_custom_column', 'show_ros_admin_columns');
 	function show_ros_admin_columns($name) {
 		global $post;
 		switch ($name)
    {
     		case '_ros_orginal_string':
     			$orginal_string = get_post_meta($post->ID, '_ros_orginal_string', true);
     			if ($orginal_string) {
     				echo $orginal_string;
     			} else {
     				echo __('Not inserted', ROS_LANG);
     			}
          $post               = get_post( $post );
          $title              = _draft_or_post_title();
          $post_type_object   = get_post_type_object( $post->post_type );
          $can_edit_post      = current_user_can( 'edit_post', $post->ID );
          // set up row actions
          $actions = array();
          if ( $can_edit_post && 'trash' != $post->post_status ) {
              $actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '" title="' . esc_attr( __( 'Edit this item' , ROS_LANG) ) . '">' . __( 'Edit', ROS_LANG ) . '</a>';
          }
          if ( current_user_can( 'delete_post', $post->ID ) ) {
              if ( 'trash' == $post->post_status )
                  $actions['untrash'] = "<a title='" . esc_attr( __( 'Restore this item from the Trash' , ROS_LANG) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . "'>" . __( 'Restore' , ROS_LANG) . "</a>";
              elseif ( EMPTY_TRASH_DAYS )
                  $actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash' , ROS_LANG) ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Trash', ROS_LANG, ROS_LANG ) . "</a>";
              if ( 'trash' == $post->post_status || !EMPTY_TRASH_DAYS )
                  $actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently' , ROS_LANG) ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . __( 'Delete Permanently' , ROS_LANG) . "</a>";
          }

          // invoke row actions
          $table = new WP_Posts_List_Table;
          echo $table->row_actions( $actions, true );
        break;
        case '_ros_orginal_domain_string':
          $orginal_domain_string = get_post_meta($post->ID, '_ros_orginal_domain_string', true);
          if ($orginal_domain_string) {
            echo $orginal_domain_string;
          } else {
            echo __('Undefined', ROS_LANG);
          }
        break;
        case '_ros_new_string':
          $new_string = get_post_meta($post->ID, '_ros_new_string', true);
          if ($new_string) {
            echo $new_string;
          } else {
            echo __('Not inserted', ROS_LANG);
          }
       break;
        case '_ros_match_string':
          $match_string = get_post_meta($post->ID, '_ros_match_string', true);
          if ($match_string == 1) {
            echo __('Yes', ROS_LANG);
          } else {
            echo __('No', ROS_LANG);
          }
       break;
 		}
 }
 add_filter('manage_edit-ros_columns', 'ros_columns_remove');
  // REMOVE DEFAULT CATEGORY COLUMN
  function ros_columns_remove($defaults) {
      unset($defaults['title']);
      unset($defaults['date']);
      return $defaults;
  }

  function ros_change_text( $translated_text, $text, $domain  )
  {
    global $post;
    $args = array('post_type' => 'ros', 'posts_per_page' => -1 );
    $strings = get_posts( $args );
    foreach( $strings as $string ) {
       setup_postdata($post);
       $orginal_string = get_post_meta($string->ID, '_ros_orginal_string', true);
       $orginal_domain_string = get_post_meta($string->ID, '_ros_orginal_domain_string', true);
       $new_string = get_post_meta($string->ID, '_ros_new_string', true);
       	$match_string = get_post_meta($string->ID, '_ros_match_string', true);
       if ( ! empty( $orginal_domain_string ) ) {
         if ($match_string == 0 and $orginal_domain_string == $domain) {
          $translated_text = str_replace($orginal_string, $new_string, $translated_text);
         }
         elseif ( $translated_text == "$orginal_string" and $orginal_domain_string == $domain) {
          $translated_text = __("$new_string", "$orginal_domain_string");
         }
      }else{
        if ($match_string == 0 and $translated_text != "$orginal_string") {
         $translated_text = str_replace($orginal_string, $new_string, $translated_text);
        }
        if ($match_string == 1 and $translated_text == "$orginal_string" ) {
          $translated_text = "$new_string";
        }
      }

    }
  	return $translated_text;
  }
  add_filter( 'gettext', 'ros_change_text', 20, 3);

?>

<?php
add_action('add_meta_boxes', 'add_ros_strings_details_metaboxes');
function add_ros_strings_details_metaboxes() {
  	add_meta_box('ros_strings_details', __('Strings Details', ROS_LANG), 'ros_strings_details_fun', 'ros', 'normal', 'high');
}
function ros_strings_details_fun() {
	global $post;
 	$orginal_string = get_post_meta($post->ID, '_ros_orginal_string', true);
 	$orginal_domain_string = get_post_meta($post->ID, '_ros_orginal_domain_string', true);
 	$new_string = get_post_meta($post->ID, '_ros_new_string', true);
 	$match_string = get_post_meta($post->ID, '_ros_match_string', true);
	?>
   <table class="form-table">
  	<tbody>
      <tr>
    		<th><label for="_ros_orginal_string"><?php echo _e('Original String', ROS_LANG); ?></label></th>
    		<td> <input name="_ros_orginal_string" id="_ros_orginal_string" type="text" value="<?php echo $orginal_string; ?>" class="regular-text"></td>
    	</tr>
      <tr>
        <th><label for="_ros_orginal_domain_string"><?php echo _e('Text-Domain for original string', ROS_LANG); ?></label></th>
        <td> <input name="_ros_orginal_domain_string" id="_ros_orginal_domain_string" type="text" value="<?php echo $orginal_domain_string; ?>" class="regular-text">
             <p class="description"><?php echo _e('You can put the domain name for the original string to replace it for example woocommerce, when you keep this field empty will be replacing all strings in WP with the new string.', ROS_LANG); ?> </p>
        </td>
      </tr>
      <tr>
    		<th><label for="_ros_new_string"><?php echo _e('New String', ROS_LANG); ?></label></th>
    		<td> <input name="_ros_new_string" id="_ros_new_string" type="text" value="<?php echo $new_string; ?>" class="regular-text"></td>
    	</tr>
      <tr>
    		<th><label for="_ros_match_string"><?php echo _e('Match String?', ROS_LANG); ?></label></th>
    		<td>
          <select name="_ros_match_string" class="regular-text">
						<option value="1" <?php if($match_string == 1): echo 'selected'; endif; ?>><?php echo _e('Yes', ROS_LANG);?></option>
						<option value="0" <?php if($match_string == 0): echo 'selected'; endif; ?>><?php echo _e('No', ROS_LANG);?></option>
					</select>
          <p class="description"><?php echo _e('When you select "Yes" this means replace same this string/word only, or when you select "No" will be replacing that string/word at all places of the strings available whether first, last, or and in any other place in the strings.', ROS_LANG); ?> </p>

        </td>
    	</tr>
  	</tbody>
  </table>
<?php } ?>
<?php
function save_ros_strings_details($post_id, $post) {
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;
	$data = array();
	if(isset($_POST['_ros_orginal_string'])){
		$data['_ros_orginal_string'] = $_POST['_ros_orginal_string'];
 	}
	if(isset($_POST['_ros_orginal_domain_string'])){
 		$data['_ros_orginal_domain_string'] = $_POST['_ros_orginal_domain_string'];
 	}
	if(isset($_POST['_ros_new_string'])){
 		$data['_ros_new_string'] = $_POST['_ros_new_string'];
 	}
	if(isset($_POST['_ros_match_string'])){
 		$data['_ros_match_string'] = $_POST['_ros_match_string'];
 	}
	foreach ($data as $key => $value) {
		if( $post->post_type == 'revision' ) return;
		$value = implode(',', (array)$value);
		if(get_post_meta($post->ID, $key, FALSE)) {
			update_post_meta($post->ID, $key, $value);
		} else {
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key);
	}
}
add_action('save_post', 'save_ros_strings_details', 1, 2);
?>

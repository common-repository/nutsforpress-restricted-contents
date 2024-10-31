<?php
//if this file is called directly, abort.
if(!defined('ABSPATH')) die('please, do not call this page directly');

//add restricted metabox to post, page and public custom post types
if(!function_exists('nfprct_add_content_metabox')) {
	
	function nfprct_add_content_metabox() {
		
		add_meta_box( 
		'nfprct-restricted', 
		__('Restricted Content','nutsforpress-restricted-contents'), 
		'nfprct_add_content_metabox_content', 
		nfprct_post_type_to_include(),
		'side',
		'high'
		);
		
	}
	
	add_action('add_meta_boxes', 'nfprct_add_content_metabox');
	
} else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_add_content_metabox" already exists');
	
}

//custom metabox callback
if(!function_exists('nfprct_add_content_metabox_content')) {
	
	function nfprct_add_content_metabox_content() {
		
		if(
			
			!get_the_ID()
			|| !in_array(get_post_type(get_the_ID()), nfprct_post_type_to_include())
			|| !current_user_can('edit_posts')
			
		) {
			
			return;
			
		}
		
		$nfprct_is_restricted_role = get_post_meta(get_the_ID(), '_nfprct_allowed_role', true);
		$nfprct_is_restricted = get_post_meta(get_the_ID(), '_nfprct_is_restricted', true);
				
		if(
		
			!empty($nfprct_is_restricted) 
			&& $nfprct_is_restricted === '1'
			
		) {
			
			$nfprct_is_restricted_checked = 'checked';
			
		} else {
			
			$nfprct_is_restricted_checked = null;
		}
		
		
		?>
		
		<input type="hidden" value="<?php echo wp_create_nonce('nfprct-restricted-tag-nonce'); ?>" id="nfprct-restricted-tag-nonce" name="nfprct-restricted-tag-nonce">	
		
		<p>
		
			<?php echo __('Is this content restricted','nutsforpress-restricted-contents'); ?>?<br><br>
			<input type="checkbox" name="nfprct-restricted" class="nfproot-switch" id="nfprct-restricted-checkbox" value="1" <?php echo esc_attr($nfprct_is_restricted_checked); ?> />
			<label for="nfprct-restricted-checkbox">&nbsp;</label>
			
		</p>
		
		<p>
		
			<?php echo __('Which role is allowed to view it','nutsforpress-restricted-contents'); ?>?<br><br>
			<select name="nfprct_allowed_role[]" class="nfprct-allowed-role" id="nfprct_allowed_role" multiple>

				<?php
				if(
				
					!empty($nfprct_is_restricted_role) 
					&& in_array('all', $nfprct_is_restricted_role)
					
				) {
				
					?>
				
					<option value="all" selected><?php echo __('All','nutsforpress-restricted-contents'); ?></option>
		
					<?php
					
				} else {
					
					?>
				
					<option value="all"><?php echo __('All','nutsforpress-restricted-contents'); ?></option>
		
					<?php
					
				}
				
				//get all current WordPress roles
				$nfprct_get_all_role_names = wp_roles()->get_names();
				
				foreach($nfprct_get_all_role_names as $nfprct_role_slug => $nfprct_role_name) {
																	
					if(
					
						!empty($nfprct_is_restricted_role) 
						&& in_array($nfprct_role_slug, $nfprct_is_restricted_role)
						
					) {
						
						?>
					
						<option value="<?php echo esc_attr($nfprct_role_slug); ?>" selected><?php echo esc_attr(translate_user_role($nfprct_role_name)); ?></option>
						
						<?php
					
					} else {
						
						?>
					
						<option value="<?php echo esc_attr($nfprct_role_slug); ?>"><?php echo esc_attr(translate_user_role($nfprct_role_name)); ?></option>
						
						<?php
						
					}
				}
				
				?>
				
			</select>	
		
		</p>
		
		<?php
	}

} else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_add_content_metabox_content" already exists');
	
}

//custom metabox save
if(!function_exists('nfprct_save_content_metabox')) {
	
	function nfprct_save_content_metabox($nfprct_post_id_to_save) {
		
		if(
		
			!empty($_POST['nfprct-restricted-tag-nonce']) 
			&& wp_verify_nonce($_POST['nfprct-restricted-tag-nonce'], 'nfprct-restricted-tag-nonce')
			
		) {
					
			if(

				!current_user_can('edit_posts', get_the_ID())
				|| wp_is_post_revision( get_the_ID()) !== false
				|| !in_array(get_post_type(get_the_ID()),nfprct_post_type_to_include())
				|| (
					
					defined('DOING_AUTOSAVE') 
					&& DOING_AUTOSAVE
					
					)
					
				|| (
					
					defined('DOING_AJAX') 
					&& DOING_AJAX
					
					) 
				
				){ 
				
					return;	
				
				}
						
			if(
			
				!empty($_POST['nfprct-restricted']) 
				&& $_POST['nfprct-restricted'] === '1'
				
			) {
				
				
				//restricted and no roles
				if(empty($_POST['nfprct_allowed_role'])) {
										
					$nfprct_post_value_to_save = 'all';
					update_post_meta(
					
						$nfprct_post_id_to_save, 
						'_nfprct_allowed_role', 
						(array)$nfprct_post_value_to_save
						
					);
					
					update_post_meta($nfprct_post_id_to_save, '_nfprct_is_restricted', '1');
					
				//restricted and roles
				} else {
				
					$nfprct_posted_roles = $_POST['nfprct_allowed_role'];
					$nfprct_posted_roles_count = count($nfprct_posted_roles);
					
					//get all role names
					$nfprct_get_all_role_names = wp_roles()->get_names();

					//deal with attachment duplication created by WPML
					$nfprct_get_wpml_active_languages = apply_filters('wpml_active_languages', false);
					
					//if WPML has active languages
					if(!empty($nfprct_get_wpml_active_languages)) {
					  
						//loop into languages
						foreach($nfprct_get_wpml_active_languages as $nfprct_wpml_language) {
							
							$nfprct_wpml_language_code = $nfprct_wpml_language['language_code'];
							
							$nfprct_post_translation_id_to_save = apply_filters('wpml_object_id', $nfprct_post_id_to_save, get_post_type($nfprct_post_id_to_save), false, $nfprct_wpml_language_code);
							
							if(!empty($nfprct_post_translation_id_to_save)) {
								
								$nfprct_matching_roles = 0;
								
								foreach($nfprct_posted_roles as $nfprct_posted_role) {
									
									if(array_key_exists($nfprct_posted_role,$nfprct_get_all_role_names)) {

										$nfprct_matching_roles++;
									
									}
									
								}
								
								//check if dropdown select contains a valid role 
								if((int)$nfprct_posted_roles_count === (int)$nfprct_matching_roles) {
																
									update_post_meta($nfprct_post_translation_id_to_save, '_nfprct_allowed_role', (array)$nfprct_posted_roles);
									
								} else {
									
									$nfprct_post_value_to_save = 'all';
									update_post_meta($nfprct_post_translation_id_to_save, '_nfprct_allowed_role', (array)$nfprct_post_value_to_save);
									
								}				
								
								//define current post as restricted
								update_post_meta($nfprct_post_translation_id_to_save, '_nfprct_is_restricted', '1');	
								
							}
							
						}
						
					//if WPML has not active languages	
					} else {		
						
						$nfprct_matching_roles = 0;
						
						foreach($nfprct_posted_roles as $nfprct_posted_role) {
							
							if(array_key_exists($nfprct_posted_role,$nfprct_get_all_role_names)) {

								$nfprct_matching_roles++;
							
							}
							
						}
						
						//check if dropdown select contains a valid role 
						if((int)$nfprct_posted_roles_count === (int)$nfprct_matching_roles) {
							
							update_post_meta($nfprct_post_id_to_save, '_nfprct_allowed_role', (array)$nfprct_posted_roles);
							
						} else {
							
							$nfprct_post_value_to_save = 'all';
							update_post_meta($nfprct_post_id_to_save, '_nfprct_allowed_role', (array)$nfprct_post_value_to_save);
							
						}	
						
						update_post_meta($nfprct_post_id_to_save, '_nfprct_is_restricted', '1');
						
					}	
					
				}
						
		
			//not restricted
			} else {
				
				delete_post_meta($nfprct_post_id_to_save, '_nfprct_is_restricted');
				delete_post_meta($nfprct_post_id_to_save, '_nfprct_allowed_role');
				
			}
		
		}
			
	}
	
	
	
} else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_save_content_metabox" already exists');
	
}
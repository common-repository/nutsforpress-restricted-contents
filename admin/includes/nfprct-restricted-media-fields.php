<?php
//if this file is called directly, abort.
if(!defined('ABSPATH')) die('please, do not call this page directly');

if(!function_exists('nfprct_add_media_checkbox')) {
	
	//custom checkbox function
	function nfprct_add_media_checkbox($nfprct_attachment_form_fileds_array, $nfprct_post_object) {
		
		if(current_user_can('edit_posts')) {
			
			//get involved post id
			$nfprct_current_post_id = $nfprct_post_object->ID;
			
			//get post meta value
			$nfprct_restricted_media_checkbox_value = get_post_meta($nfprct_current_post_id, '_nfprct_is_restricted', true);
			$nfprct_restricted_media_select_value = get_post_meta($nfprct_current_post_id, '_nfprct_allowed_role', true);
			
			//define if checkbox have to be checked or not
			if(!empty($nfprct_restricted_media_checkbox_value) && $nfprct_restricted_media_checkbox_value === '1') {
				
				$nfprct_restricted_media_checkbox_checked = 'checked';
				
			} else {
				
				$nfprct_restricted_media_checkbox_checked = null;
				
			}
			
			$nfprct_allowed_roles_options = null;
			$nfprct_get_all_role_names = wp_roles()->get_names();
			
			foreach($nfprct_get_all_role_names as $nfprct_role_slug => $nfprct_role_name) {
						
				//if($nfprct_role_slug === $nfprct_restricted_media_select_value) {
				if(in_array($nfprct_role_slug, (array)$nfprct_restricted_media_select_value)) {	
				
					$nfprct_allowed_roles_options .= '<option value="'.$nfprct_role_slug.'" selected>'.translate_user_role($nfprct_role_name).'</option>';
				
				} else {
					
					$nfprct_allowed_roles_options .= '<option value="'.$nfprct_role_slug.'">'.translate_user_role($nfprct_role_name).'</option>';
					
				}
			}
			
			//add custom checkbox for media restriction
			$nfprct_attachment_form_fileds_array['nfprct_restricted_media_checkbox_value'] = array(
				'label' => __('Is restricted', 'nutsforpress-restricted-contents'),
				'input' => 'html',
				'html'  => "<input type='checkbox' ".$nfprct_restricted_media_checkbox_checked." name='attachments[".$nfprct_current_post_id."][nfprct-is-restricted]' class='nfproot-switch' id='attachments[".$nfprct_current_post_id."][nfprct-is-restricted]' value='1'><label for='attachments[".$nfprct_current_post_id."][nfprct-is-restricted]'>&nbsp;</label>",		
				'value' => $nfprct_restricted_media_checkbox_value,
				'helps' => __('Allow download from the URL below only to logged in users', 'nutsforpress-restricted-contents')
			);
					
			//get meta id of _wp_attached_file in order to use it as a query parameter in the next custom field
			global $wpdb;
			$nfprct_meta_id = $wpdb->get_var($wpdb->prepare("SELECT meta_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND post_id = %s", $nfprct_current_post_id));
			
			//add custom field for restricted media link
			$nfprct_attachment_form_fileds_array['nfprct_restricted_media_link'] = array(
				'label' => __('URL for downloading', 'nutsforpress-restricted-contents'),
				'input' => 'html',
				'html'  => "<input type='text' readonly='readonly' name='attachments[".$nfprct_current_post_id."][nfprct-url]' id='attachments[".$nfprct_current_post_id."][nfprct-url]' value='".get_site_url().'/restricted-media-download/?media='.$nfprct_meta_id."' />",
				'helps' => __('Use this URL to let logged in users download this media', 'nutsforpress-restricted-contents')
			);
	  
			//add dropdown select for allowed role
			$nfprct_attachment_form_fileds_array['nfprct_restricted_media_allowed_role'] = array(
				'label' => __('Allowed Role', 'nutsforpress-restricted-contents'),
				'input' => 'html',
				'html'  => "
					<select name='attachments[".$nfprct_current_post_id."][nfprct-allowed-role]' class='nfprct-allowed-role' id='attachments[".$nfprct_current_post_id."][nfprct-allowed-role]'>
					<option value='all' selected>".__('All','nutsforpress-restricted-contents')."</option>".$nfprct_allowed_roles_options."
					</select>
				",
				'helps' => __('Allow download to this role only', 'nutsforpress-restricted-contents')
			);  
			
		}
	
		return $nfprct_attachment_form_fileds_array;

	}
	
} else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_add_media_checkbox" already exists');
	
}


if(!function_exists('nfprct_save_media_checkbox')) {
	
	//save custom checkbox function
	function nfprct_save_media_checkbox($nfprct_post_data_array, $nfprct_attachment_array_metatada) {

		if(current_user_can('edit_posts')) {
		
			//get involved post id
			$nfprct_post_id_to_save = $nfprct_post_data_array['ID'];
			
			//if checkox is checked 
			if(
			
				!empty($nfprct_attachment_array_metatada['nfprct-is-restricted']) 
				//&& $nfprct_attachment_array_metatada['nfprct-is-restricted'] === 'on'
				&& $nfprct_attachment_array_metatada['nfprct-is-restricted'] === '1'
				
			) {
				
				$nfprct_posted_roles = (array)$nfprct_attachment_array_metatada['nfprct-allowed-role'];
				$nfprct_posted_roles_count = count($nfprct_posted_roles);			
				
				$nfprct_restricted_media_checkbox_value = '1';
				//$nfprct_restricted_media_checkbox_value = $nfprct_attachment_array_metatada['nfprct-is-restricted'];

				//get all role names
				$nfprct_get_all_role_names = wp_roles()->get_names();

				//deal with attachment duplication created by WPML
				$nfprct_get_wpml_active_languages = apply_filters('wpml_active_languages', false);
				
				//if WPML has active languages
				if(!empty($nfprct_get_wpml_active_languages)) {
				  
					//loop into languages
					foreach($nfprct_get_wpml_active_languages as $nfprct_wpml_language) {
						
						$nfprct_wpml_language_code = $nfprct_wpml_language['language_code'];
						
						$nfprct_post_translation_id_to_save = apply_filters('wpml_object_id', $nfprct_post_id_to_save, 'attachment', false, $nfprct_wpml_language_code);
						
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
				
					
					//define current post as restricted
					update_post_meta($nfprct_post_id_to_save, '_nfprct_is_restricted', $nfprct_restricted_media_checkbox_value);	
					
				}
								
			} else {
				
				//if checkox is not checked
				$nfprct_restricted_media_checkbox_value = '0';

				//deal with attachment duplication created by WPML
				$nfprct_get_wpml_active_languages = apply_filters('wpml_active_languages', false);
				
				//if WPML has active languages
				if(!empty($nfprct_get_wpml_active_languages)) {
				  
					//loop into languages
					foreach($nfprct_get_wpml_active_languages as $nfprct_wpml_language) {
						
						$nfprct_wpml_language_code = $nfprct_wpml_language['language_code'];
						
						$nfprct_post_translation_id_to_save = apply_filters('wpml_object_id', $nfprct_post_id_to_save, 'attachment', false, $nfprct_wpml_language_code);
						
						if(!empty($nfprct_post_translation_id_to_save)) {
										
							//define current post as not restricted
							update_post_meta($nfprct_post_translation_id_to_save, '_nfprct_is_restricted', $nfprct_restricted_media_checkbox_value);
							
						}
											
					}
					
				} else {			
					
					//define current post as not restricted
					update_post_meta($nfprct_post_id_to_save, '_nfprct_is_restricted', $nfprct_restricted_media_checkbox_value);		
					
				}						
							
			}	

			//set the option that is used to rewrite rules
			update_option('_nfprct_rewrite_restricted_media_list', '1', false);
	
		}
	
		return $nfprct_post_data_array;  
			
	}

} else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_save_media_checkbox" already exists');
	
}
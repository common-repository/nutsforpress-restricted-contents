<?php
//if this file is called directly, die.
if(!defined('ABSPATH')) die('please, do not call this page directly');

if(!function_exists('nfprct_check_involved_post')) {
	
	//do the check and redirect if something goes wrong
	function nfprct_check_involved_post($nfprct_involved_post_id) {
		
		//get options 
		global $nfproot_current_language_settings;
		
		$nfprct_page_to_redirect = false;
		$nfprct_redirect_archive = false;

		//if redirection is enabled
		if(

			!empty($nfproot_current_language_settings['nfprct']['nfproot_redirect'])
			&& $nfproot_current_language_settings['nfprct']['nfproot_redirect'] === '1'
								
		) {
			
			//get page to redirect to
			if(!empty($nfproot_current_language_settings['nfprct']['nfproot_target_page'])){
				
				$nfprct_page_to_redirect = absint($nfproot_current_language_settings['nfprct']['nfproot_target_page']);
								
			} 	
			
			//check archive redirect
			if(
			
				!empty($nfproot_current_language_settings['nfprct']['nfproot_redirect_archive'])
				&& $nfproot_current_language_settings['nfprct']['nfproot_redirect_archive'] === '1'
				
			){
				
				$nfprct_redirect_archive = true;
								
			} 	
			
			//if user is not logged in, redirect to the page defined in options
			if(!is_user_logged_in()){

				//save cookie before redirecting
				global $wp;
				$nfprct_redirect_start_value = add_query_arg($wp->query_vars, home_url($wp->request));
				$nfprct_redirect_start_value_encoded = base64_encode($nfprct_redirect_start_value);
				setcookie('nfprct_redirect_start', $nfprct_redirect_start_value_encoded, current_time('timestamp', 1) + 3600, '/');
												
				if(
				
					$nfprct_page_to_redirect !== false
					&& get_post_status($nfprct_page_to_redirect) === 'publish'
					
				) {
				
					$nfprct_page_to_redirect_permalink = get_permalink($nfprct_page_to_redirect);
					
					$nfprct_current_site_lang = null;
					
					$nfprct_get_post_language_details = apply_filters('wpml_post_language_details', null, $nfprct_involved_post_id);
							
					if(!empty($nfprct_get_post_language_details)) {
						
						$nfprct_current_site_lang = $nfprct_get_post_language_details['language_code'];
						
					}
					
					if(!empty($nfprct_current_site_lang)) {
						
						$nfprct_page_to_redirect_permalink = apply_filters('wpml_permalink', $nfprct_page_to_redirect_permalink, $nfprct_current_site_lang); 
						
					}
					
					if(
					
						!is_archive() 
						
						|| 
						
							(
					
							is_archive() 
							&& $nfprct_redirect_archive === true
						
							)
						
					) {				
								
						wp_safe_redirect($nfprct_page_to_redirect_permalink);
						die;
					
					}
					
				} else {
					
					wp_safe_redirect(wp_login_url());
					die;
					
				}
				
			} else {
							
				//prevent errors in case is_plugin_active does not exists
				include_once(ABSPATH.'wp-admin/includes/plugin.php');
				
				//prevent from loading frontend functions into elementor visual editor
				if(
					
					(
						//class_exists(\Elementor\Plugin::$instance)
						is_plugin_active('elementor/elementor.php')
						|| is_plugin_active('elementor-pro/elementor-pro.php')
					
					) && (
					
						\Elementor\Plugin::$instance->editor->is_edit_mode()
						|| \Elementor\Plugin::$instance->preview->is_preview_mode()
					
					)
					
				) {
					
					return;
					
				}			
				
				//check if media download is allowed
				$nfprct_allowed_role_slug = get_post_meta($nfprct_involved_post_id, '_nfprct_allowed_role', true);
				
				if(!is_array($nfprct_allowed_role_slug)) {
					
					$nfprct_allowed_role_slug = array($nfprct_allowed_role_slug);
				}
				
				if(!empty($nfprct_allowed_role_slug) && !in_array('all',$nfprct_allowed_role_slug)) {
														
					$nfprct_current_user_data = get_userdata(get_current_user_id());
					$nfprct_current_user_role_slug = $nfprct_current_user_data->roles;		
					
					$nfprct_lock_user = false;

					if(is_array($nfprct_allowed_role_slug)) {
						
						if(empty(array_intersect($nfprct_allowed_role_slug, $nfprct_current_user_role_slug))){
							
							$nfprct_lock_user = true;
						}
						
					} else {
					
						if(!in_array($nfprct_allowed_role_slug, $nfprct_current_user_role_slug)) {
						
							$nfprct_lock_user = true;
							
						}
					}
					
					if($nfprct_lock_user === true) {
						
						//save cookie before redirecting
						global $wp;
						$nfprct_redirect_start_value = add_query_arg($wp->query_vars, home_url($wp->request));
						$nfprct_redirect_start_value_encoded = base64_encode($nfprct_redirect_start_value);
						setcookie('nfprct_redirect_start', $nfprct_redirect_start_value_encoded, current_time('timestamp', 1) + 3600, '/');					
						
						if(
						
							$nfprct_page_to_redirect !== false
							&& get_post_status($nfprct_page_to_redirect) === 'publish'
						
						) {
						
							//set a five seconds cookie to print alert on Main Entrance
							setcookie('nfprct_not_allowed_role', '1', current_time('timestamp', 1) + 5, '/');
							
							$nfprct_page_to_redirect_permalink = get_permalink($nfprct_page_to_redirect);
																	
							$nfprct_current_site_lang = null;
							
							$nfprct_get_post_language_details = apply_filters('wpml_post_language_details', null, $nfprct_involved_post_id);
									
							if(!empty($nfprct_get_post_language_details)) {
								
								$nfprct_current_site_lang = $nfprct_get_post_language_details['language_code'];
								
							}
							
							if(!empty($nfprct_current_site_lang)) {
								
								$nfprct_page_to_redirect_permalink = apply_filters('wpml_permalink', $nfprct_page_to_redirect_permalink, $nfprct_current_site_lang); 
								
							}
							
							if(
							
								!is_archive() 
								
								|| 
								
									(
							
										is_archive() 
										&& $nfprct_redirect_archive === true
								
									)
								
							) {
								
								//this is needed to interact with "restrict to customers", used in some projects
								do_action('nfprct_user_role_not_allowed', $rsmd_current_site_lang);
																									
								wp_safe_redirect($nfprct_page_to_redirect_permalink);
								die;		

							}							
							
						} else {
							
							wp_safe_redirect(wp_login_url());
							die;
							
						}
						
					}
					
				}						
				
			}
			
		}
		
	}
	
} else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_check_involved_post" already exists');
	
}

if(!function_exists('nfprct_check_restriction')) {
	
	function nfprct_check_restriction() {
		
		//if is 404, maybe it is a restricted-media-download request
		if(is_404()) {
								
			//get current url
			global $wp;
			$nfprct_current_url = home_url($wp->request);
			
			//check if url is restricted-media-download
			if($nfprct_current_url === home_url().'/restricted-media-download') {
				
				//check if a meta_id exists
				if(!empty($_REQUEST['media'])) {
					
					//get involved media
					$nfprct_involved_meta_id = absint($_REQUEST['media']);
					
					//get post id by meta_id
					global $wpdb;
					$nfprct_involved_post_id = $wpdb->get_var(
					
						$wpdb->prepare("
						
							SELECT post_id 
							FROM $wpdb->postmeta 
							WHERE meta_id = %s", 
							$nfprct_involved_meta_id
							
						)
					
					);
					
					//if post id is found
					if(!empty($nfprct_involved_post_id)) {
						
						//get post type
						$nfprct_current_post_type = get_post_type($nfprct_involved_post_id);
						
						//go on only if the requested post is an attachment
						if(
						
							empty($nfprct_current_post_type) 
							|| $nfprct_current_post_type !== 'attachment'
							
						) {
														
							return;	
						
						} 
						
						//change status header
						status_header(200);
						
						//check if media is restricted						
						$nfprct_is_restricted = get_post_meta($nfprct_involved_post_id, '_nfprct_is_restricted', true);
						
						//check if this page, post, post type is restricted
						if(
						
							!empty($nfprct_is_restricted)
							&& $nfprct_is_restricted === '1'
							
						){

							//do the check and redirect if something goes wrong
							nfprct_check_involved_post($nfprct_involved_post_id);						

							//if WordPress is at least 5.3 and involved post is an image 
							if(function_exists('wp_get_original_image_path')) {
								
								//get current file path get original image path
								$nfprct_involved_post_original_path = wp_get_original_image_path($nfprct_involved_post_id);
								//returns "/var/www/vhosts/sitename.ext/wp-content/uploads/yyyy/mm/filename.ext"	
									
							} 
							
							//otherwise get attached file
							if(empty($nfprct_involved_post_original_path)) {
								
								$nfprct_involved_post_original_path = get_attached_file($nfprct_involved_post_id);
								//returns "/var/www/vhosts/sitename.ext/wp-content/uploads/yyyy/mm/filename.ext"
								
							}
							
							$nfprct_involved_post_absolute_url = wp_get_attachment_url($nfprct_involved_post_id);
							$nfprct_involved_post_relative_url = str_replace(site_url(),'',$nfprct_involved_post_absolute_url);
							$nfprct_involved_post_mime_type = get_post_mime_type($nfprct_involved_post_id);	

							$nfprct_involved_post_file_name = basename($nfprct_involved_post_original_path);
							
							if(
							
								!empty($nfprct_involved_post_relative_url) 
								&& !empty($nfprct_involved_post_mime_type) 
								&& !empty($nfprct_involved_post_file_name)
								
							) {
							
								$nfprct_involved_post_size = filesize($nfprct_involved_post_original_path);
															
								// Force the download
								header('Content-Description: File Transfer');
								header('Content-Type: '.$nfprct_involved_post_mime_type);
								header('Content-Length: '.$nfprct_involved_post_size);
								header('Content-Disposition: attachment; filename="'.$nfprct_involved_post_file_name.'"');
								header('Expires: 0');
								header('Cache-Control: must-revalidate');
								readfile($nfprct_involved_post_original_path, true);	
																
								exit();

							}								

						//media is not restricted, redirect to media
						} else {
							
							//get uploads directory object
							$nfprct_involved_upload_dir = wp_upload_dir();
							
							//get uploads base url
							$nfprct_involved_upload_baseurl = $nfprct_involved_upload_dir['baseurl'];
							//returns "sitename.ext/wp-content/uploads/"
							
							//get _wp_attached_file postmeta
							$nfprct_involved_post_attached_file = get_post_meta($nfprct_involved_post_id, '_wp_attached_file', true);
							
							wp_safe_redirect($nfprct_involved_upload_baseurl.'/'.$nfprct_involved_post_attached_file);
							die;
							
						}							

					} 

				} 
				
			} 
			
		} else {

			if(
				
				!get_the_ID()
				|| is_admin()
				
			) {
				
				return;
				
			}
			
			$nfprct_involved_post_id = get_the_ID();
			
			$nfprct_is_restricted = get_post_meta($nfprct_involved_post_id, '_nfprct_is_restricted', true);
			
			//check if this page, post, post type is restricted
			if(
				
				!empty($nfprct_is_restricted)
				&& $nfprct_is_restricted === '1'
				
			) {
										
				//do the check and redirect if something goes wrong
				nfprct_check_involved_post($nfprct_involved_post_id);
	
			}
			
		}

	}
	

} else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_check_restriction" already exists');
	
}
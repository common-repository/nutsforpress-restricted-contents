<?php
//if this file is called directly, die.
if(!defined('ABSPATH')) die('please, do not call this page directly');

//with this function we will define the NutsForPress menu page content
if(!function_exists('nfprct_settings_content')) {
	
	function nfprct_settings_content() {

		//create steps for page dropdown
		$nfprct_page_dropdown_values = array();
		$nfprct_page_dropdown_step = 1;
				
		$nfprct_home_page_id = get_option('page_on_front');
				
		if(!empty($nfprct_home_page_id)) {
			
			
			$nfprct_page_dropdown_values[$nfprct_page_dropdown_step]['option-value'] = $nfprct_home_page_id;
			$nfprct_page_dropdown_values[$nfprct_page_dropdown_step]['option-text'] = 'Homepage (id: '.$nfprct_home_page_id.')';
			$nfprct_page_dropdown_values[$nfprct_page_dropdown_step]['option-selected'] = 'selected';
			$nfprct_page_dropdown_step++;
			
		}
		
		$nfprct_pages_query_args = array(
		
			'post_type' => nfprct_post_type_to_include(),
			'post_status' => array('publish'),
			'post__not_in' => array($nfprct_home_page_id),
			'orderby' => 'post_title',
			'order' => 'asc',
			'posts_per_page' => -1,	
			'meta_query' => array(
			
				'relation' => 'AND',
								
				array(
				
					'key' => '_nfprct_is_restricted',
					'compare' => 'NOT EXISTS'
				),

				array(
				
					'key' => '_rsmd_is_restricted',
					'compare' => 'NOT EXISTS'
					
				)
				
			)
						
		);
		 
		$nfprct_pages_query = new WP_Query($nfprct_pages_query_args);
		 
		if($nfprct_pages_query->have_posts()){

			while($nfprct_pages_query->have_posts()) {
				
				$nfprct_pages_query->the_post();
				
				$nfprct_page_id = get_the_ID();
				$nfprct_page_title = get_the_title();
				
				$nfprct_page_dropdown_values[$nfprct_page_dropdown_step]['option-value'] = get_the_ID();
				$nfprct_page_dropdown_values[$nfprct_page_dropdown_step]['option-text'] = get_the_title().' (id: '.get_the_ID().')';
				$nfprct_page_dropdown_values[$nfprct_page_dropdown_step]['option-selected'] = null;
				$nfprct_page_dropdown_step++;
				
			}
			
		} 
		
		wp_reset_postdata();
		
		//create list for restricted attachments
		$nfprct_restricted_attachments = nfprct_restricted_list('attachment');
		$nfprct_restricted_attachments_list = null;

		//if restricted media are found
		if(!empty($nfprct_restricted_attachments)) {
			
			$nfprct_restricted_attachments_list .= '<ul class="nfprct-media-list">';
			
			//loop into involved media
			foreach($nfprct_restricted_attachments as $nfprct_restricted_attachments_id) {
					
				$nfprct_restricted_post_title = get_the_title($nfprct_restricted_attachments_id);
				
				$nfprct_restricted_old_post_meta = get_post_meta($nfprct_restricted_attachments_id, '_rsmd_allowed_role', true);
				$nfprct_restricted_new_post_meta = get_post_meta($nfprct_restricted_attachments_id, '_nfprct_allowed_role', true);
								
				if(
				
					empty($nfprct_restricted_old_post_meta)
					
				){
					
					if(is_array($nfprct_restricted_new_post_meta)){
					
						$nfprct_restricted_post_meta = implode(', ', $nfprct_restricted_new_post_meta);
						
					} else {
						
						$nfprct_restricted_post_meta = $nfprct_restricted_new_post_meta;
						
					}
					
				}
				
				else if(
				
					empty($nfprct_restricted_new_post_meta)
					
				){
					
					if(is_array($nfprct_restricted_old_post_meta)){
					
						$nfprct_restricted_post_meta = implode(', ', $nfprct_restricted_old_post_meta);
						
					} else {
						
						$nfprct_restricted_post_meta = $nfprct_restricted_old_post_meta;
						
					}
				
				} else {
				
					$nfprct_restricted_post_meta = implode(', ', array_unique(array_merge((array)$nfprct_restricted_old_post_meta, (array)$nfprct_restricted_new_post_meta), SORT_REGULAR));
					
				}
				
				$nfprct_restricted_post_meta = rtrim($nfprct_restricted_post_meta, ', ');
								
				//print post name and the link for a rapid edit
				$nfprct_restricted_attachments_list .= '<li><a href="'.admin_url().'upload.php?item='.$nfprct_restricted_attachments_id.'" target="_blank">'.$nfprct_restricted_post_title.'</a> ('.__('granted to','nutsforpress-restricted-contents').': <em>'.$nfprct_restricted_post_meta.'</em>)</li>';

			}
			
			$nfprct_restricted_attachments_list .= '</ul>';

		//if restricted media are not found
		} else {
			
			//print info
			$nfprct_restricted_attachments_list .= '<p>'.__('No restricted media found','nutsforpress-restricted-contents').'</p>';
			
		}
		
		//create list for restricted contents
		$nfprct_restricted_contents = nfprct_restricted_list('post');
		$nfprct_restricted_contents_list = null;
		
		//if restricted media are found
		if(!empty($nfprct_restricted_contents)) {
			
			$nfprct_restricted_contents_list .= '<ul class="nfprct-content-list">';

			//loop into involved contents
			foreach($nfprct_restricted_contents as $nfprct_restricted_posts_id) {
				
				$nfprct_restricted_post_title = get_the_title($nfprct_restricted_posts_id);
				
				$nfprct_restricted_old_post_meta = get_post_meta($nfprct_restricted_posts_id, '_rsmd_allowed_role', true);
				$nfprct_restricted_new_post_meta = get_post_meta($nfprct_restricted_posts_id, '_nfprct_allowed_role', true);
								
				if(
				
					empty($nfprct_restricted_old_post_meta)
					
				){
					
					if(is_array($nfprct_restricted_new_post_meta)){
					
						$nfprct_restricted_post_meta = implode(', ', $nfprct_restricted_new_post_meta);
						
					} else {
						
						$nfprct_restricted_post_meta = $nfprct_restricted_new_post_meta;
						
					}
					
				}
				
				else if(
				
					empty($nfprct_restricted_new_post_meta)
					
				){
					
					if(is_array($nfprct_restricted_old_post_meta)){
					
						$nfprct_restricted_post_meta = implode(', ', $nfprct_restricted_old_post_meta);
						
					} else {
						
						$nfprct_restricted_post_meta = $nfprct_restricted_old_post_meta;
						
					}
				
				} else {
				
					$nfprct_restricted_post_meta = implode(', ', array_unique(array_merge((array)$nfprct_restricted_old_post_meta, (array)$nfprct_restricted_new_post_meta), SORT_REGULAR));
					
				}
				
				$nfprct_restricted_post_meta = rtrim($nfprct_restricted_post_meta, ', ');
				
				//print post name and the link for a rapid edit
				$nfprct_restricted_contents_list .= '<li><a href="'.admin_url().'post.php?post='.$nfprct_restricted_posts_id.'&action=edit" target="_blank">'.$nfprct_restricted_post_title.'</a> ('.__('granted to','nutsforpress-restricted-contents').': <em>'.$nfprct_restricted_post_meta.'</em>)</li>';

			}
			
			$nfprct_restricted_contents_list .= '</ul>';

		//if restricted media are not found
		} else {
			
			//print info
			$nfprct_restricted_contents_list .= '<p>'.__('No restricted contents found','nutsforpress-restricted-contents').'</p>';
			
		}		
	
		$nfprct_settings_content = array(
		
			array(
			
				'container-title'	=> __('Activate redirection for restricted contents','nutsforpress-restricted-contents'),
				
				'container-id'		=> 'nfprct_redirect_container',
				'container-class' 	=> 'nfprct-redirect-container',
				'input-name'		=> 'nfproot_redirect',
				'add-to-settings'	=> 'global',
				'data-save'			=> 'nfprct',
				'input-id'			=> 'nfprct_redirect',
				'input-class'		=> 'nfprct-redirect',
				'input-description'	=> __('If switched, you can set every page, post or media as restricted to prevent not logged in userers to access it','nutsforpress-restricted-contents'),
				'arrow-before'		=> true,
				'after-input'		=> '',
				'input-type' 		=> 'switch',
				'input-value'		=> '1',
				
				'childs'			=> array(

					array(
						
						'container-title'	=> __('Redirect not logged in users to this courtesy page','nutsforpress-restricted-contents'),
					
						'container-id'		=> 'nfprct_target_page_container',
						'container-class' 	=> 'nfprct-target-page-container',					
						'input-name' 		=> 'nfproot_target_page',
						'add-to-settings'	=> 'local',
						'data-save'			=> 'nfprct',
						'input-id' 			=> 'nfprct_target_page',
						'input-class'		=> 'nfprct-target-page',
						'input-description' => __('When a not logged in user tries to browse a restricted content, redirect him to this courtesy page','nutsforpress-restricted-contents'),
						'arrow-before'		=> false,
						'after-input'		=> '',
						'input-type' 		=> 'dropdown',
						'input-value'		=> $nfprct_page_dropdown_values,
						
					),
					
					array(
					
						'container-title'	=> __('Redirect not logged in users to the above page also if they are browsing an archive page containing a restricted content','nutsforpress-restricted-contents'),
					
						'container-id'		=> 'nfprct_redirect_archive_container',
						'container-class' 	=> 'nfprct-redirect-archive-container',					
						'input-name' 		=> 'nfproot_redirect_archive',
						'add-to-settings'	=> 'global',
						'data-save'			=> 'nfprct',
						'input-id' 			=> 'nfprct_redirect_archive',
						'input-class'		=> 'nfprct-redirect-archive',
						'input-description' => __('Redirect even if the restricted content is an element of an archive page (for example a category archive including one ore more restricted posts)','nutsforpress-restricted-contents'),
						'arrow-before'		=> false,
						'after-input'		=> '',
						'input-type' 		=> 'switch',
						'input-value'		=> '1',
						
					),
					
				),
				
			),

			array(
			
				'container-title'	=> __('Restricted Elements','nutsforpress-restricted-contents'),
				
				'container-id'		=> 'nfprct_restricted_elements_container',
				'container-class' 	=> 'nfprct-restricted-elements-container',
				'input-name'		=> 'nfproot_restricted_elements',
				'add-to-settings'	=> 'global',
				'data-save'			=> 'nfprct',
				'input-id'			=> 'nfprct_restricted_elements',
				'input-class'		=> 'nfprct-restricted-elements',
				'input-description'	=> false,
				'arrow-before'		=> true,
				'after-input'		=> array(
				
					array(
					
						'type' 		=> 'paragraph',
						'id' 		=> 'nfpmgm_rebuild_thumbnails_description',
						'class' 	=> 'nfproot-after-input nfpmgm-rebuild-thumbnails-description',
						'hidden' 	=> false,
						'content' 	=> __('Click on the arrow to get a list of the current restricted elements and the roles granted to access them','nutsforpress-restricted-contents'),
						'value'		=> ''
					
					),
				
				),
				
				'input-type' 		=> false,
				'childs'			=> array(
					
					array(
					
						'container-title'	=> __('Restricted contents','nutsforpress-restricted-contents'),
					
						'container-id'		=> 'nfprct_restricted_content_list_container',
						'container-class' 	=> 'nfprct-restricted-content-list-container',					
						'input-name' 		=> 'nfproot_restricted_content_list',
						'add-to-settings'	=> 'global',
						'data-save'			=> 'nfprct',
						'input-id' 			=> 'nfprct_restricted_content_list',
						'input-class'		=> 'nfprct-restricted-content-list',
						'input-description' => false,
						'arrow-before'		=> false,
						'after-input'		=> '',
						'input-type' 		=> 'textonly',
						'input-value'		=> $nfprct_restricted_contents_list,
						
					),
					
					array(
					
						'container-title'	=> __('Restricted media','nutsforpress-restricted-contents'),
					
						'container-id'		=> 'nfprct_restricted_media_list_container',
						'container-class' 	=> 'nfprct-restricted-media-list-container',					
						'input-name' 		=> 'nfproot_restricted_media_list',
						'add-to-settings'	=> 'global',
						'data-save'			=> 'nfprct',
						'input-id' 			=> 'nfprct_restricted_media_list',
						'input-class'		=> 'nfprct-restricted-media-list',
						'input-description' => false,
						'arrow-before'		=> false,
						'after-input'		=> '',
						'input-type' 		=> 'textonly',
						'input-value'		=> $nfprct_restricted_attachments_list,
						
					),
									
				),
				
			),
				
		);
						
		return $nfprct_settings_content;
		
	}
	
} else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_settings_content" already exists');
	
}
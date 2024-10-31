<?php
//if this file is called directly, die.
if(!defined('ABSPATH')) die('please, do not call this page directly');

if(!function_exists('nfprct_post_type_to_include')) {
	
	function nfprct_post_type_to_include() {

		$nfprct_registered_post_types_args = array(
		
			'exclude_from_search' => false,
			'public'   => true,
			'_builtin' => false,
			'publicly_queryable' => true
			
		);
			
		$nfprct_registered_post_types = get_post_types($nfprct_registered_post_types_args);
	
		$nfprct_post_types_to_search = array('post','page');
		
		foreach($nfprct_registered_post_types as $nfprct_registered_post_type){
			
			$nfprct_post_types_to_search[] = $nfprct_registered_post_type;
			
		}

		if(!empty($nfprct_post_types_to_search)){
		
			return $nfprct_post_types_to_search;
			
		} else {
			
			return false;
			
		}
		
	}
		
} else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_post_type_to_include" already exists');
	
}

if(!function_exists('nfprct_restricted_list')) {
	
	//custom checkbox function
	function nfprct_restricted_list($nfprct_restricted_list_arg) {

		if($nfprct_restricted_list_arg === 'attachment') {
			
			$nfprct_post_types_to_search = 'attachment';
			$nfprct_post_status_to_search = 'inherit';
			
		} 
		
		elseif($nfprct_restricted_list_arg === 'post') {
			
			$nfprct_post_types_to_search = nfprct_post_type_to_include();
			$nfprct_post_status_to_search = 'publish';
			
		}
		
		$nfprct_posts_to_deal_with = new WP_Query(

			//post arguments
			array(
			
				'post_type' => $nfprct_post_types_to_search,
				'posts_per_page' => -1,
				'orderby' => 'title',
				'order' => 'ASC',						
				'suppress_filters' => false, //otherwise it loads WPML duplicates media
				'offset' => 0,
				'post_status' => $nfprct_post_status_to_search,
				'ignore_sticky_posts' => true,
				'no_found_rows' => true,
				'fields' => 'ids',
				'meta_query' => array(
				
					'relation' => 'OR',
					
					array(
					
						'key' => '_nfprct_is_restricted',
						'value' => '1',
						'compare' => '='
					),

					array(
					
						'key' => '_rsmd_is_restricted',
						'value' => '1',
						'compare' => '='
						
					)
				)				
				
			)
			
		);

		//get image post ids array
		$nfprct_posts_ids_to_deal_with = $nfprct_posts_to_deal_with->posts;

		wp_reset_postdata();	
		
		return $nfprct_posts_ids_to_deal_with;
		
	}
		
} else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_restricted_list" already exists');
	
}

//add attachment to htaccess
if(!function_exists('nfprct_add_mod_rewrite_rule')) {
	
	function nfprct_add_mod_rewrite_rule($nfprct_restricted_attachments_id) {

		if(
		
			!empty($nfprct_restricted_attachments_id)
			
		){
		
			$nfprct_current_post_absolute_url = wp_get_attachment_url($nfprct_restricted_attachments_id);			
			$nfprct_current_post_realtive_url = str_replace(site_url(), '', esc_url($nfprct_current_post_absolute_url));
			
			add_filter(
			
				'mod_rewrite_rules', 
				function($nfprct_current_rewrite_rules) use ($nfprct_current_post_realtive_url) {
					
				$nfprct_current_rewrite_rules .= '
					
					<files "'.basename($nfprct_current_post_realtive_url).'">
					  deny from all
					</files>'.
					"\r\n"
					
				;
				

				return $nfprct_current_rewrite_rules;
				}
				
			);
		
		}

	}
		
} else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_add_mod_rewrite_rule" already exists');
	
}
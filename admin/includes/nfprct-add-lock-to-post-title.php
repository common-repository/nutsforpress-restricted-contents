<?php
 //if this file is called directly, abort.
if(!defined('ABSPATH')) die('please, do not call this page directly');

//add a lock after post title
if(!function_exists('nfprct_add_lock_to_post_title')){
	
	function nfprct_add_lock_to_post_title($nfprct_post_states, $nfprct_post) {
		
		$nfprct_current_post_id = $nfprct_post->ID;
		
		$nfprct_restricted_old_post_meta = get_post_meta($nfprct_current_post_id, '_rsmd_is_restricted', true);
		$nfprct_restricted_new_post_meta = get_post_meta($nfprct_current_post_id, '_nfprct_is_restricted', true);
		
		if(
			
			(
		
				!empty($nfprct_restricted_old_post_meta)
				&& $nfprct_restricted_old_post_meta === '1'
				
			)
			
			||
			
			(
		
				!empty($nfprct_restricted_new_post_meta)
				&& $nfprct_restricted_new_post_meta === '1'
				
			)			
		
		){
			
			global $wpdb;
			$nfprct_table_prefix = $wpdb->prefix;
			
			if(
				
				get_post_type($nfprct_current_post_id) !== 'attachment'
				
				||
			
					(
					
					get_post_type($nfprct_current_post_id) === 'attachment'
					&& get_user_meta(get_current_user_id(), $nfprct_table_prefix.'media_library_mode', true) === 'list'
					
					)
					
			){
				
				$nfprct_current_post_lock = '<span title="'.__('Restricted by NutsForPress Restricted Contents','nutsforpress-restricted-contents').'" alt="'.__('Restricted by NutsForPress Restricted Contents','nutsforpress-restricted-contents').'" class="dashicons dashicons-privacy"></span>';
				$nfprct_post_states[] = $nfprct_current_post_lock;
				
			}
			
		} 
		
		return $nfprct_post_states;	
		
	}
	
} else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_add_lock_to_post_title" already exists');
	
}
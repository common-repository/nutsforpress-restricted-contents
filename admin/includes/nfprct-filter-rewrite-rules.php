<?php
//if this file is called directly, abort.
if(!defined('ABSPATH')) die('please, do not call this page directly');

if(!function_exists('nfprct_filter_rewrite_rules')) {

	//rewrite rules
	function nfprct_filter_rewrite_rules() {
		
		//only admin and editors
		if(current_user_can('edit_posts')){

			$nfprct_restricted_attachments_ids = nfprct_restricted_list('attachment');			
			
			if(!empty($nfprct_restricted_attachments_ids)){
			
				//loop into post id array
				foreach($nfprct_restricted_attachments_ids as $nfprct_restricted_attachments_id) {			

					nfprct_add_mod_rewrite_rule($nfprct_restricted_attachments_id);
				
				}
				
			}
			
			$nfprct_rewrite_restricted_media_list = get_option('_nfprct_rewrite_restricted_media_list', false);

			if(
			
				$nfprct_rewrite_restricted_media_list !== false
				&& $nfprct_rewrite_restricted_media_list === '1'
				
			){
				
				//rewrite rules
				save_mod_rewrite_rules();
				
				//delete temporary option
				delete_option('_nfprct_rewrite_restricted_media_list');
				
			}
		
		} 
			
	}
			
} else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_filter_rewrite_rules" already exists');
	
}
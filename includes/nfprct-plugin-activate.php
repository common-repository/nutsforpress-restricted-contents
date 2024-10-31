<?php
 //if this file is called directly, abort.
if(!defined('ABSPATH')) die('please, do not call this page directly');

//ACTIVATE

//plugin activate function
if(!function_exists('nfprct_plugin_activate')){

	function nfprct_plugin_activate() {
				
		//get NutsForPress setting
		global $nfproot_plugins_settings;
		
		//define plugin installaton type
		$nfproot_plugins_settings['nfprct']['prefix'] = 'nfprct';
		$nfproot_plugins_settings['nfprct']['slug'] = 'nfprct-settings';
		$nfproot_plugins_settings['nfprct']['edition'] = 'repository';
		$nfproot_plugins_settings['nfprct']['name'] = 'Restricted Contents';
		
		//update NutsForPress setting
		update_option('_nfproot_plugins_settings', $nfproot_plugins_settings, false);
		
		//get postmeta values from Restricted Media and translate them to NutsForPress
		global $wpdb;
		$nfprct_postmeta_table_name = $wpdb->prefix.'postmeta';
	
		$nfprct_convert_is_restricted = "
		
			UPDATE $nfprct_postmeta_table_name 
			SET meta_key = '_nfprct_is_restricted'
			WHERE meta_key = '_rsmd_is_restricted'
			
		;";
		
		$nfprct_convert_allowed_role = "
		
			UPDATE $nfprct_postmeta_table_name 
			SET meta_key = '_nfprct_allowed_role'
			WHERE meta_key = '_rsmd_allowed_role'
			
		;";
	
		$wpdb->query($nfprct_convert_is_restricted);
		$wpdb->query($nfprct_convert_allowed_role);
		
		require_once NFPRCT_BASE_PATH . 'admin/includes/nfprct-admin-common-functions.php';
		
		$nfprct_restricted_attachments_ids = nfprct_restricted_list('attachment');			
		
		//loop into post id array
		foreach($nfprct_restricted_attachments_ids as $nfprct_restricted_attachments_id) {			
		
			nfprct_add_mod_rewrite_rule($nfprct_restricted_attachments_id);
		
		}
		
		//update rules
		save_mod_rewrite_rules();		
	
	}
		
}  else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_plugin_activate" already exists');
	
}
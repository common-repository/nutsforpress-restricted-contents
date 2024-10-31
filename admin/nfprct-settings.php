<?php
//if this file is called directly, die.
if(!defined('ABSPATH')) die('please, do not call this page directly');

//with this function we will create the NutsForPress menu page
if(!function_exists('nfprct_settings')) {
	
	function nfprct_settings() {	
		
		global $nfproot_plugins_settings;
		$nfprct_pro = null;
		
		if(
		
			!empty($nfproot_plugins_settings) 
			&& !empty($nfproot_plugins_settings['installed_plugins']['nfprct']['edition'])
			&& $nfproot_plugins_settings['installed_plugins']['nfprct']['edition'] === 'registered'
			
		) {
			
			$nfprct_pro = ' <span class="dashicons dashicons-saved"></span>';
			
		}
		
		add_submenu_page(
	
			'nfproot-settings',
			'Restricted Contents',
			'Restricted Contents'.$nfprct_pro,
			'manage_options',
			'nfprct-settings',
			'nfprct_settings_callback'
		
		);
		
		
	}
	
} else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_base_options" already exists');
	
}
	
//with this function we will define the NutsForPress menu page content
if(!function_exists('nfprct_settings_callback')) {
	
	function nfprct_settings_callback() {
		
		?>
		
		<div class="wrap nfproot-settings-wrap">
			
			<h1>Restricted Contents</h1>
			
			<div class="nfproot-settings-main-container">
		
				<?php
				
				//include option content page
				require_once NFPRCT_BASE_PATH.'admin/nfprct-settings-content.php';
				
				//define contents as result of the function nfprct_settings_content
				$nfprct_settings_content = nfprct_settings_content();
				
				//invoke nfproot_options_structure functions included into /root/options/nfproot-options-structure.php
				nfproot_settings_structure($nfprct_settings_content);
				
				?>
			
			</div>
		
		</div>
		
		<?php
		
	}
	
} else {
	
	error_log('NUTSFORPRESS ERROR: function "nfprct_settings" already exists');
	
}
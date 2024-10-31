<?php
/*
Plugin Name: 	NutsForPress Restricted Contents
Plugin URI:		https://www.nutsforpress.com/
Description: 	NutsForPress Restricted Contents allows you to restrict pages, posts and media (images, zip files, pdf) to logged in users only.
Version:     	1.4
Author:			Christian Gatti
Author URI:		https://profiles.wordpress.org/christian-gatti/
License:		GPL-2.0+
License URI:	http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain:	nutsforpress-restricted-contents
*/

//if this file is called directly, die.
if(!defined('ABSPATH')) die('please, do not call this page directly');


//DEFINITIONS

if(!defined('NFPROOT_BASE_RELATIVE')) {define('NFPROOT_BASE_RELATIVE', dirname(plugin_basename( __FILE__ )).'/root');}
define('NFPRCT_BASE_PATH', plugin_dir_path( __FILE__ ));
define('NFPRCT_BASE_URL', plugins_url().'/'.plugin_basename( __DIR__ ).'/');
define('NFPRCT_BASE_RELATIVE', dirname( plugin_basename( __FILE__ )));
define('NFPRCT_DEBUG', false);


//NUTSFORPRESS ROOT CONTENT
	
//add NutsForPress parent menu page
require_once NFPRCT_BASE_PATH.'root/nfproot-settings.php';
add_action('admin_menu', 'nfproot_settings');

//add NutsForPress save settings function and make it available through ajax
require_once NFPRCT_BASE_PATH.'root/nfproot-save-settings.php';
add_action('wp_ajax_nfproot_save_settings', 'nfproot_save_settings');

//add NutsForPress saved settings and make them available through the global varibales $nfproot_current_language_settings and $nfproot_options_name
require_once NFPRCT_BASE_PATH.'root/nfproot-saved-settings.php';
add_action('plugins_loaded', 'nfproot_saved_settings');

//register NutsForPress styles and scripts
require_once NFPRCT_BASE_PATH.'root/nfproot-styles-and-scripts.php';
add_action('admin_enqueue_scripts', 'nfproot_styles_and_scripts');
	
//add NutsForPress settings structure that contains nfproot_options_structure function invoked by plugin settings
require_once NFPRCT_BASE_PATH.'root/nfproot-settings-structure.php';


//PLUGIN INCLUDES

//add activate actions
require_once NFPRCT_BASE_PATH.'includes/nfprct-plugin-activate.php';
register_activation_hook(__FILE__, 'nfprct_plugin_activate');

//add deactivate actions
require_once NFPRCT_BASE_PATH.'includes/nfprct-plugin-deactivate.php';
register_deactivation_hook(__FILE__, 'nfprct_plugin_deactivate');

//add uninstall actions
require_once NFPRCT_BASE_PATH.'includes/nfprct-plugin-uninstall.php';
register_uninstall_hook(__FILE__, 'nfprct_plugin_uninstall');


//PLUGIN SETTINGS

//add plugin settings
require_once NFPRCT_BASE_PATH.'admin/nfprct-settings.php';
add_action('admin_menu', 'nfprct_settings');


//ADMIN INCLUDES CONDITIONALLY

//add common functions
require_once NFPRCT_BASE_PATH.'admin/includes/nfprct-admin-common-functions.php';

//filter rewrite rules
require_once NFPRCT_BASE_PATH.'admin/includes/nfprct-filter-rewrite-rules.php';
add_action('admin_init', 'nfprct_filter_rewrite_rules');

//add a lock after post title
require_once NFPRCT_BASE_PATH.'admin/includes/nfprct-add-lock-to-post-title.php';
add_filter('display_post_states', 'nfprct_add_lock_to_post_title', 10, 2);
add_filter('display_media_states', 'nfprct_add_lock_to_post_title', 10, 2);

//add custom fields to contents
require_once NFPRCT_BASE_PATH.'admin/includes/nfprct-restricted-contents-fields.php';
add_action('add_meta_boxes', 'nfprct_add_content_metabox');
add_action('save_post', 'nfprct_save_content_metabox', 10, 3);

//add custom fields to media
require_once NFPRCT_BASE_PATH.'admin/includes/nfprct-restricted-media-fields.php';
add_filter('attachment_fields_to_edit', 'nfprct_add_media_checkbox', 10, 2);
add_filter('attachment_fields_to_save', 'nfprct_save_media_checkbox', 10, 2);

//PUBLIC INCLUDES CONDITIONALLY

//add common functions
//require_once NFPRCT_BASE_PATH.'admin/includes/nfprct-admin-common-functions.php';

//check restriction
require_once NFPRCT_BASE_PATH.'public/includes/nfprct-check-restriction.php';
add_action('template_redirect', 'nfprct_check_restriction', 1);
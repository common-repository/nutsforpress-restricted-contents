=== NutsForPress Restricted Contents ===

Contributors: Christian Gatti
Tags: NutsForPress,restrict,protect,hide,private,image,post,page
Donate link: https://www.paypal.com/paypalme/ChristianGatti
Requires at least: 5.3
Tested up to: 6.5
Requires PHP: 7.x
Stable tag: 1.4
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

NutsForPress Restricted Contents allows you to restrict pages, posts and media (images, zip files, pdf) to logged in users only.


== Description ==

*Restricted Contents* is one of the several NutsForPress plugins providing some essential features that WordPress does not offer itself or offers only partially.   


Install and activate **Restricted Contents** plugin and you can easily mark as restricted: images, pdf file, zip files, pages, posts and custom posts, deciding to grant access to a specific role or to any logged-in users.

Mark as restricted your **media** by the checkbox that you'll find at the right sidebar of the selected element, copy and paste the provided alternative URL to link your media into pages and posts.

When "Is restricted" checkbox is flagged, the selected element will get downloadable only to logged-in users and only through the alternative URL (original URL will be protected through a htaccess rule). When "Is restricted" checkbox is not flagged, the media element will be downloadable for anyone, through both the original and the alternative URL.

Mark as restricted your **pages and posts** too by the checkbox that you'll find at the right sidebar of the post/page editor.

If a not logged-in user tries to access to a restricted content, he will be redirected to the login page or to the page you have defined into "Restricted Contents" plugin options. 

Furthermore, you can grant access to post or pages only to a specific role, to more roles or to every logged-in users: chose from the dropdown menu, just after the "is restricted" checkbox.

In association with [Main Entrance](https://wordpress.org/plugins/main-entrance/), Restricted Contents helps you to build a restricted content page, for allowing to download documents only to logged in users.

Restricted Contents is full compliant with WPML (you don't need to translate any option value)

Take a look at the others [NutsForPress Plugins](https://wordpress.org/plugins/search/nutsforpress/)

**Whatever is worth doing at all is worth doing well**


== Installation ==

= Installation From Plugin Repository =

* Into your WordPress plugin section, press "Add New"
* Use "NutsForPress" as search term
* Click on *Install Now* on *NutsForPress Restricted Contents* into result page, then click on *Activate*
* Setup "NutsForPress Restricted Contents" options by clicking on the link you find under the "NutsForPress" menu
* Enjoy!

= Manual Installation =

* Download *NutsForPress Restricted Contents* from https://wordpress.org/plugins/nutsforpress
* Into your WordPress plugin section, press "Add New" then press "Load Plugin"
* Choose nutsforpress-smtp-mail.zip file from your local download folder
* Press "Install Now"
* Activate *NutsForPress Restricted Contents*
* Setup "NutsForPress Restricted Contents" options by clicking on the link you find under the "NutsForPress" menu
* Enjoy!


== Changelog ==

= 1.4 =
* Fixed a bug that caused the reset of the options of this plugin when WPML was installed and activated after the configuration of this plugin

= 1.3 =
* Tested up to WordPress 6.2

= 1.2 =
* Now translations are provided by translate.wordpress.org, instead of being locally provided: please contribute!

= 1.1.3 =
* Fixed a bug that caused an error with the function is_plugin_active

= 1.1.2 =
* Fixed a serious bug that caused an error when using this plugin with a different page builder then Elementor

= 1.1.1 =
* Fixed a bug that displayed some option messages that should have been kept hidden by a css rule miswritten by an escape rule

= 1.1 =
* Fixed a bug which prevented from editing with Elementor the restricted pages with access granted to a role different from the admin or the editor
* Fixed a bug which caused the deletion of the htaccess rules for all the restricted media during permalink rebuild
* Fixed a bug that, in some cases, prevented to set media as restricted 
* Added the correct class/style to "is restricted" checkbox into media element

= 1.0 =
* First full working release


== Translations ==

* English: default language
* Italian: entirely translated


== Credits ==

* Very many thanks to [DkR](https://dkr.srl/) and [SviluppoEuropa](https://sviluppoeuropa.it/)!
<?php
/*
Plugin Name: WP Embed Facebook
Plugin URI: http://www.wpembedfb.com
Description: Embed any public Facebook video, photo, album, event, page, profile, or post. Copy the facebook url to a single line on your post, or use shortcode [facebook url ] more information at <a href="http://www.wpembedfb.com" title="plugin website">www.wpembedfb.com</a>
Author: Miguel Sirvent
Version: 2.1.0
Author URI: http://www.wpembedfb.com
Text Domain: wp-embed-facebook
Domain Path: /lang
*/

require_once( 'lib/class-wp-embed-fb-plugin.php' );
require_once( 'lib/class-wp-embed-fb.php' );
require_once( 'lib/class-wef-social-plugins.php' );

//Session start when there is a facebook app
add_action( 'init', 'WP_Embed_FB_Plugin::init', 999 );

//Translation string and wp_embed_register_handler
add_action( 'plugins_loaded', 'WP_Embed_FB_Plugin::plugins_loaded' );

register_activation_hook( __FILE__, 'WP_Embed_FB_Plugin::install' );
register_uninstall_hook( __FILE__, 'WP_Embed_FB_Plugin::uninstall' );
register_deactivation_hook( __FILE__, 'WP_Embed_FB_Plugin::deactivate' );

//register all scripts and styles
add_action( 'wp_enqueue_scripts', 'WP_Embed_FB_Plugin::wp_enqueue_scripts' );

if ( is_admin() ) {
	require_once( 'lib/class-wp-embed-fb-admin.php' );

	//Donate or review notice
	add_action( 'admin_notices', 'WP_Embed_FB_Admin::admin_notices' );
	add_action( 'wp_ajax_wpemfb_close_warning', 'WP_Embed_FB_Admin::wpemfb_close_warning' );
	add_action( 'wp_ajax_wpemfb_video_down', 'WP_Embed_FB_Admin::wpemfb_video_down' );

	//settings link
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'WP_Embed_FB_Admin::add_action_link' );

	//settings page
	add_action( 'admin_menu', 'WP_Embed_FB_Admin::add_page' );
	add_action( 'in_admin_footer', 'WP_Embed_FB_Admin::in_admin_footer' );

	//editor style
	add_action( 'admin_init', 'WP_Embed_FB_Admin::admin_init' );

	//register styles and scripts
	add_action( 'admin_enqueue_scripts', 'WP_Embed_FB_Admin::admin_enqueue_scripts' );

}

if ( WP_Embed_FB_Plugin::get_option( 'quote_plugin_active' ) === 'true' ) {
	add_filter( 'the_content', 'WEF_Social_Plugins::the_content' );
}
if ( WP_Embed_FB_Plugin::get_option( 'auto_comments_active' ) === 'true' ) {
	add_filter( 'comments_template', 'WEF_Social_Plugins::comments_template' );
	if ( WP_Embed_FB_Plugin::get_option( 'comments_count_active' ) === 'true' ) {
		add_filter( 'get_comments_number', 'WEF_Social_Plugins::get_comments_number', 10, 2 );
		add_filter( 'save_post', 'WEF_Social_Plugins::save_post', 10, 3 );
		add_filter( 'wp_ajax_wpemfb_comments', 'WEF_Social_Plugins::wpemfb_comments'  );
		add_filter( 'wp_ajax_nopriv_wpemfb_comments', 'WEF_Social_Plugins::wpemfb_comments' );
		add_action( 'pre_get_posts', 'WEF_Social_Plugins::pre_get_posts' );
	}
}

//Magic here
add_shortcode( 'facebook', 'WP_Embed_FB::shortcode' );

//Official magic very powerful since v2.1.1
add_shortcode( 'fb_plugin', 'WEF_Social_Plugins::shortcode' );



//TODO add content filter and option to force embed when it fails for weirb reasons


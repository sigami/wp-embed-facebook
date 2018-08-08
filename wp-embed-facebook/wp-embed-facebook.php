<?php
/*
Plugin Name: WP Embed Facebook
Plugin URI: http://www.wpembedfb.com
Description: Embed any public Facebook video, photo, album, event, page, comment, profile, or post. Add Facebook comments to all your site, insert Facebook social plugins (like, save, send, share, follow, quote, comments) anywhere on your site. View the <a href="http://www.wpembedfb.com/demo-site/" title="plugin website" target="_blank">demo site</a>.
Author: Miguel Sirvent
Version: 2.9.11
Author URI: http://www.wpembedfb.com
Text Domain: wp-embed-facebook
Domain Path: /lang
*/

namespace SIGAMI\WP_Embed_FB;

spl_autoload_register( __NAMESPACE__ . '\auto_loader' );

function auto_loader( $class_name ) {
	if ( false !== strpos( $class_name, __NAMESPACE__ ) ) {
		$dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR;
		require_once $dir . str_replace( [ __NAMESPACE__, '\\' ], '', $class_name ) . '.php';
	}
}

Plugin::instance( __FILE__ );

Magic_Embeds::instance();

if ( Plugin::get_option( 'auto_comments_active' ) === 'true' ) {
	Comments::instance();
}

if ( is_admin() ) {
	Admin::instance();
}

//COMPATIBILITY
include Plugin::path().'inc/deprecated/deprecated.php';


//TODO CHECK IF ALL OPTIIONS ARE THERE AND REMOVE OLD ONES
//TODO change lightbox css to make it more hermetic
//TODO PASS ADD ON TO NEW FORMAT

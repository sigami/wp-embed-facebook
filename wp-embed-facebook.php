<?php
/**
@author    Miguel Sirvent
@license   GPL-3.0+ https://www.gnu.org/licenses/gpl-3.0.txt
@link      https://www.wpembedfb.com
@package   WP Embed FB
@wordpress-plugin
Plugin Name: Magic Embeds
Plugin URI: http://www.wpembedfb.com
Description: Embed any public Facebook video, photo, album, event, page, comment, profile, or post. Add Facebook comments to all your site, insert Facebook social plugins (like, save, send, share, follow, quote, comments) anywhere on your site. View the <a href="https://wpembedfb.com/features" title="plugin website" target="_blank">features</a>.
Author: Miguel Sirvent
Version: 3.1.2
Author URI: http://www.wpembedfb.com
Text Domain: wp-embed-facebook
GitHub Plugin URI: sigami/wp-embed-facebook
 */

namespace SIGAMI\WP_Embed_FB;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

spl_autoload_register( 'SIGAMI\WP_Embed_FB\auto_loader' );

/**
 * Plugin class autoloader.
 *
 * @param string $class_name Class name to load.
 *
 * @return void
 */
function auto_loader( string $class_name ): void {
	if ( str_starts_with( $class_name, __NAMESPACE__ ) ) {
		$dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR;
		require_once $dir . str_replace( [ __NAMESPACE__, '\\' ], '', $class_name ) . '.php';
	}
}

Plugin::instance( __FILE__ );

Magic_Embeds::instance()->hooks();

if ( Plugin::is_on( 'auto_comments_active' ) ) {
	Comments::instance()->hooks();
}

if ( is_admin() ) {
	Admin::instance()->hooks();
}

// Backwards compatibility. Maybe never going to be removed.
require Plugin::path() . 'inc/deprecated/deprecated.php';

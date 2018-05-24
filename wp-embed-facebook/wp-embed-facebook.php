<?php
/*
Plugin Name: WP Embed Facebook
Plugin URI: http://www.wpembedfb.com
Description: Embed any public Facebook video, photo, album, event, page, comment, profile, or post. Add Facebook comments to all your site, insert Facebook social plugins (like, save, send, share, follow, quote, comments) anywhere on your site. View the <a href="http://www.wpembedfb.com/demo-site/" title="plugin website" target="_blank">demo site</a>.
Author: Miguel Sirvent
Version: 3.0.0
Author URI: http://www.wpembedfb.com
Text Domain: wp-embed-facebook
Domain Path: /lang
 */

namespace SIGAMI\WP_Embed_FB;

spl_autoload_register( __NAMESPACE__ . '\auto_loader' );

function auto_loader( $class_name ) {
	if ( false !== strpos( $class_name, __NAMESPACE__ ) ) {
		$classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'inc'
		               . DIRECTORY_SEPARATOR;
		/** @noinspection PhpIncludeInspection */
		require_once $classes_dir . str_replace( [ __NAMESPACE__, '\\' ], '', $class_name )
		             . '.php';
	}
}

final class Plugin extends Plugin_Framework {

	const FILE = __FILE__;

	const OPTION = 'wp-embed-fb';

	# Sections of the admin page
	protected static $tabs = [ 'General' ];

	static function load_translation() {
		load_plugin_textdomain( 'wp-embed-fb', false,
			dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	static function defaults() {
		if ( self::$defaults === null ) {
			self::$defaults = [
				'option' => 'on',
			];
		}

		return self::$defaults;
	}

	static function form_content() {
		ob_start();
		?>
        <section id="general" class="section">
			<?php
			self::section( __( 'Options', 'wp-embed-fb' ) );
			self::field( 'checkbox', 'option', __( 'Option', 'wp-embed-fb' ),
				__( 'Description', 'wp-embed-fb' ) );
			self::section();
			?>
        </section>
		<?php

		return ob_get_clean();
	}

	protected function __construct() {

		self::$page_title = __( 'Page Title', 'wp-embed-fb' );
		self::$menu_title = __( 'Menu Title', 'wp-embed-fb' );
		self::$menu_slug  = 'wp-embed-fb';

		parent::__construct();

		$options = self::get_option();

	}

}

Plugin::instance();
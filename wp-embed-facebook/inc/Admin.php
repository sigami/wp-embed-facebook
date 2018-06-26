<?php

namespace SIGAMI\WP_Embed_FB;

class Admin {

	private static $instance = null;

	static $url = null;

	static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {

		self::$url = admin_url( 'options-general.php?page=' . Plugin::$menu_slug );

		//Donate or review notice
		add_action( 'admin_notices', __CLASS__ . '::admin_notices' );
		add_action( 'wp_ajax_wpemfb_close_warning', __CLASS__ . '::wpemfb_close_warning' );

		//editor style
		add_action( 'admin_init', __CLASS__ . '::admin_init' );

		//register styles and scripts
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );

		add_filter( 'plugin_action_links_' . plugin_basename( Plugin::$FILE ),
			__CLASS__ . '::add_action_link' );
	}

	static function admin_notices() {
		if ( ( Plugin::get_option( 'close_warning2' ) == 'false' ) ) :
			?>
            <div class="notice wpemfb_warning is-dismissible">
                <h2>WP Embed Facebook</h2>
                <p>
					<?php
					printf( __( 'To enable comment moderation and embed albums, events, profiles and video as HTML5 setup a facebook app on <a id="wef_settings_link" href="%s">settings</a>',
						'wp-embed-facebook' ), Admin::$url )
					?>
                </p>
            </div>
		<?php
		endif;
	}

	static function wpemfb_close_warning() {
		if ( current_user_can( 'manage_options' ) ) {
			$options                   = Plugin::get_option();
			$options['close_warning2'] = 'true';
			Plugin::set_options( $options );
		}
		die;
	}

	/**
	 * Enqueue WP Embed Facebook js and css to admin page
	 *
	 * @param string $hook_suffix current page
	 */
	static function admin_enqueue_scripts( $hook_suffix ) {
		if ( $hook_suffix == 'settings_page_' . Plugin::$menu_slug ) {
			wp_enqueue_style( 'wpemfb-admin-css', Plugin::url() . 'inc/css/admin.css' );
		}
		wp_enqueue_style( 'wpemfb-default', Plugin::url() . 'templates/default/default.css', [],
			false );
		wp_enqueue_style( 'wpemfb-classic', Plugin::url() . 'templates/classic/classic.css', [],
			false );
		wp_enqueue_style( 'wpemfb-elegant', Plugin::url() . 'templates/elegant/elegant.css', [],
			false );
		wp_enqueue_style( 'wpemfb-lightbox', Plugin::url() . 'inc/wef-lightbox/css/lightbox.css',
			[], false );
	}

	static function add_action_link( $links ) {
		array_unshift( $links,
			'<a title="WP Embed Facebook Settings" href="' . Admin::$url . '">' . __( "Settings" ) . '</a>' );

		return $links;
	}

	/**
	 * Add template editor style to the embeds.
	 */
	static function admin_init() {
		//add_editor_style( Plugin::url() . 'templates/default/default.css' );
		//add_editor_style( Plugin::url() . 'templates/classic/classic.css' );
		//This way I only have to change the version instead of all
		add_filter( 'mce_css', __CLASS__ . '::mce_css' );
	}

	static function mce_css( $css ) {

		$list = [];

		$list[] = add_query_arg(
			'version',
			Plugin::PLUGIN_VERSION,
			Plugin::url() . 'templates/classic/classic.css'
		);
		$list[] = add_query_arg(
			'version',
			Plugin::PLUGIN_VERSION,
			Plugin::url() . 'templates/default/default.css'
		);
		$list[] = add_query_arg(
			'version',
			Plugin::PLUGIN_VERSION,
			Plugin::url() . 'templates/elegant/elegant.css'
		);

		if ( ! empty( $css ) ) {
			$css .= ',';
		}

		return $css . implode( ',', $list );
	}

}

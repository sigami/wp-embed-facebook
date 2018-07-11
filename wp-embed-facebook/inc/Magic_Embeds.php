<?php

namespace SIGAMI\WP_Embed_FB;

/**
 * Where all the embedding happens.
 *
 * @uses WP_Embed_FB
 * @uses Social_Plugins
 * @uses WP_Embed_FB_Plugin
 */
class Magic_Embeds {
	private static $instance = null;

	static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {

		/** @see Magic_Embeds::plugins_loaded */
		if ( Plugin::get_option( 'auto_embed_active' ) == 'true' ) {
			add_filter( 'plugins_loaded', __CLASS__ . '::plugins_loaded' );
		}

		/** @see Magic_Embeds::the_content */
		add_filter( 'the_content', __CLASS__ . '::the_content' );

		//Deprecate old api versions
		add_action( 'init', __CLASS__ . '::init', 999 );

		/** @see Embed_FB::shortcode */
		add_shortcode( 'facebook', __NAMESPACE__ . '\Embed_FB::shortcode' );
		add_shortcode( 'embedfb', __NAMESPACE__ . '\Embed_FB::shortcode' );

		/** @see Social_Plugins::shortcode */
		add_shortcode( 'fb_plugin', __NAMESPACE__ . '\Social_Plugins::shortcode' );

		add_action( 'widgets_init', __CLASS__ . '::widgets_init' );
		//TODO do some magic with [facebook] JetPack shortcode.

	}

	static function widgets_init() {
		//TODO test widget 3.0
		register_widget( '\SIGAMI\WP_Embed_FB\Widget' );
	}

	static function init() {
		if ( Helpers::has_fb_app() ) {
			if ( (float) substr( Plugin::get_option( 'sdk_version' ), 1 ) <= 2.3 ) {
				$options                = Plugin::get_option();
				$options['sdk_version'] = 'v2.11';
				Plugin::set_options( $options );
			}
		}
	}

	/**
	 * Adds fb_foot to top and quote plugin
	 *
	 * @param string $the_content Post content
	 *
	 * @return string
	 */
	static function the_content( $the_content ) {
		if ( Plugin::get_option( 'fb_root' ) === 'true' ) {
			$the_content = '<div id="fb-root"></div>' . PHP_EOL . $the_content;
		}
		if ( is_single() && ( Plugin::get_option( 'quote_plugin_active' ) === 'true' ) ) {
			$array = Helpers::string_to_array( Plugin::get_option( 'quote_post_types' ) );
			if ( in_array( $GLOBALS['post']->post_type, $array ) ) {
				$the_content .= Social_Plugins::get( 'quote' );
			}
		}

		return $the_content;
	}

	/**
	 * Adds Embed register handler
	 */
	static function plugins_loaded() {
		wp_embed_register_handler( "wpembedfb", "/(http|https):\/\/www\.facebook\.com\/([^<\s]*)/",
			__CLASS__ . '::embed_register_handler' );
	}

	static function embed_register_handler(
		$match, /** @noinspection PhpUnusedParameterInspection */
		$attr, $url = null, $atts = null
	) {
		return Embed_FB::fb_embed( $match, $url, $atts );
	}

	static function wp_enqueue_scripts() {
		//Legacy for custom templates previous to version 3.0
		foreach ( [ 'default', 'classic', 'elegant' ] as $theme ) {
			$on_theme = locate_template( "/plugins/wp-embed-facebook/$theme/$theme.css" );
			if ( ! empty( $on_theme ) ) {
				wp_register_style( 'wpemfb-' . $theme, $on_theme, [], Plugin::PLUGIN_VERSION );
			}
		}
		wp_register_style( 'wpemfb-custom', Plugin::url() . 'templates/custom-embeds/styles.css',
			[], Plugin::PLUGIN_VERSION );
		wp_register_style( 'wpemfb-lightbox', Plugin::url() . 'inc/wef-lightbox/css/lightbox.css',
			[], Plugin::PLUGIN_VERSION );
		wp_register_script( 'wpemfb-lightbox',
			Plugin::url() . 'inc/wef-lightbox/js/lightbox.min.js', [ 'jquery' ],
			Plugin::PLUGIN_VERSION );
		$lb_defaults       = Helpers::get_lb_defaults();
		$options           = Plugin::get_option();
		$translation_array = [];
		foreach ( $lb_defaults as $default_name => $value ) {
			if ( $options[ 'LB_' . $default_name ] !== $value ) {
				$translation_array[ $default_name ] = $options[ 'LB_' . $default_name ];
			}
		}
		if ( ! empty( $translation_array ) ) {
			//TODO use something like wp_add_inline_script('wpemfb-lightbox','new Lightbox(WEF_LB)') for LightBox options naaaa
			wp_localize_script( 'wpemfb-lightbox', 'WEF_LB', $translation_array );
		}
		wp_register_script( 'wpemfb', Plugin::url() . 'inc/js/wpembedfb.min.js', [ 'jquery' ],
			Plugin::PLUGIN_VERSION, true );

		wp_register_script( 'wpemfb-fbjs', Plugin::url() . 'inc/js/fb.min.js', [],
			Plugin::PLUGIN_VERSION );
		$translation_array = [
			'local'   => $options['sdk_lang'],
			'version' => $options['sdk_version'],
			'fb_id'   => $options['app_id'] == '0' ? '' : $options['app_id']
		];
		if ( $options['auto_comments_active'] == 'true' && $options['comments_count_active'] == 'true' ) {
			$translation_array = $translation_array + [
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
				];
		}
		wp_localize_script( 'wpemfb-fbjs', 'WEF', $translation_array );

		if ( $options['enq_when_needed'] == 'false' ) {
			if ( $options['enq_lightbox'] == 'true' ) {
				wp_enqueue_script( 'wpemfb-lightbox' );
				wp_enqueue_style( 'wpemfb-lightbox' );
			}
			if ( $options['enq_wpemfb'] == 'true' ) {
				wp_enqueue_script( 'wpemfb' );
			}
			if ( $options['enq_fbjs'] == 'true' ) {
				wp_enqueue_script( 'wpemfb-fbjs' );
			}
		}
		if ( $options['enq_fbjs_global'] == 'true' ) {
			wp_enqueue_script( 'wpemfb-fbjs' );
		}

		if ( ( $options['auto_comments_active'] == 'true' ) && is_single() ) {
			$array          = Helpers::string_to_array( $options['auto_comments_post_types'] );
			$queried_object = get_queried_object();
			if ( in_array( $queried_object->post_type, $array ) ) {
				wp_enqueue_script( 'wpemfb-fbjs' );
			}
		}
	}

}
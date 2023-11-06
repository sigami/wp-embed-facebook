<?php
/**
 * Embed Helpers.
 *
 * @package Magic Embeds
 */

namespace SIGAMI\WP_Embed_FB;

// If this file is called directly, abort.
use WP_Post;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Where all the embedding happens.
 *
 * @uses WP_Embed_FB
 * @uses Social_Plugins
 * @uses WP_Embed_FB_Plugin
 */
class Magic_Embeds {
	private static ?self $instance = null;

	public static function instance(): ?self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
	}

	public function hooks() {

		/** @uses static::plugins_loaded */
		if ( Plugin::get_option( 'auto_embed_active' ) === 'true' ) {
			add_filter( 'plugins_loaded', [ $this, 'plugins_loaded' ] );
		}

		/** @see Magic_Embeds::wp_enqueue_scripts */
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );

		/** @see Magic_Embeds::the_content */
		add_filter( 'the_content', [ $this, 'the_content' ] );

		// Deprecate old api versions
		add_action( 'init', [ $this, 'init' ], 999 );

		/** @uses Embed_FB::shortcode */
		add_shortcode( 'facebook', __NAMESPACE__ . '\Embed_FB::shortcode' );
		add_shortcode( 'embedfb', __NAMESPACE__ . '\Embed_FB::shortcode' );

		/** @uses Social_Plugins::shortcode */
		add_shortcode( 'fb_plugin', __NAMESPACE__ . '\Social_Plugins::shortcode' );

		add_action( 'widgets_init', [ $this, 'widgets_init' ] );
	}

	public function widgets_init() {
		register_widget( '\SIGAMI\WP_Embed_FB\Widget' );
	}

	public function init() {
		if ( Helpers::has_fb_app() ) {
			if ( (float) substr( Plugin::get_option( 'sdk_version' ), 1 ) <= 2.10 ) {
				$options                = Plugin::get_option();
				$options['sdk_version'] = 'v3.3';
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
	public function the_content( string $the_content ): string {
		if ( Plugin::get_option( 'fb_root' ) === 'true' ) {
			$the_content = '<div id="fb-root"></div>' . PHP_EOL . $the_content;
		}
		if ( is_single() && ( Plugin::get_option( 'quote_plugin_active' ) === 'true' ) ) {
			$array = Helpers::string_to_array( Plugin::get_option( 'quote_post_types' ) );
			if ( in_array( $GLOBALS['post']->post_type, $array, true ) ) {
				$the_content .= Social_Plugins::get( 'quote' );
			}
		}

		return $the_content;
	}

	/**
	 * Adds Embed register handler
	 */
	public function plugins_loaded() {
		wp_embed_register_handler(
			'wpembedfb',
			'/(http|https):\/\/www\.facebook\.com\/([^<\s]*)/',
			[ $this, 'embed_register_handler' ]
		);
	}

	private static function pre_get_posts() {
		// TODO properly test this view actions: pre_get_posts, current_screen actions.
		if ( ! self::active_on_post_type() ) {
			wp_embed_unregister_handler( 'wpembedfb' );
		}
	}

	private static function active_on_post_type(): bool {
		global $post_type, $post;

		$allowed_post_types = Plugin::get_option( 'auto_embed_post_types' );

		if ( in_array( $post_type, $allowed_post_types, true )
			|| ( ( $post instanceof WP_Post )
					&& in_array( $post->post_type, $allowed_post_types, true ) ) ) {
			return true;
		}

		return false;
	}

	public function embed_register_handler(
		$url_parts,
		$attr,
		$url = null,
		$attrs = null
	) {
		return Embed_FB::fb_embed( $url_parts, $url, $attrs );
	}

	public function wp_enqueue_scripts() {
		// Legacy for custom templates previous to version 3.0
		// now add /plugins/wp-embed-facebook/custom-embeds/ to your theme
		foreach ( [ 'default', 'classic', 'elegant' ] as $theme ) {
			$on_theme = locate_template( "/plugins/wp-embed-facebook/$theme/$theme.css" );
			if ( ! empty( $on_theme ) ) {
				wp_register_style( 'wpemfb-' . $theme, $on_theme, [], Plugin::VER );
			}
		}
		wp_register_style(
			'wpemfb-custom',
			Plugin::url() . 'templates/custom-embeds/styles.css',
			[],
			Plugin::VER
		);
		wp_register_style(
			'wpemfb-lightbox',
			Plugin::url() . 'templates/lightbox/css/lightbox.css',
			[],
			Plugin::VER
		);
		wp_register_script(
			'wpemfb-lightbox',
			Plugin::url() . 'templates/lightbox/js/lightbox.min.js',
			[ 'jquery' ],
			Plugin::VER,
			false
		);
		$lb_defaults       = Helpers::get_lb_defaults();
		$options           = Plugin::get_option();
		$translation_array = [];
		foreach ( $lb_defaults as $default_name => $value ) {
			if ( $options[ 'LB_' . $default_name ] !== $value ) {
				$translation_array[ $default_name ] = $options[ 'LB_' . $default_name ];
			}
		}
		if ( ! empty( $translation_array ) ) {
			wp_localize_script( 'wpemfb-lightbox', 'WEF_LB', $translation_array );
		}

		$deps = ( 'true' === $options['adaptive_fb_plugin'] ) ? [ 'jquery' ] : [];

		wp_register_script(
			'wpemfb-fbjs',
			Plugin::url() . 'inc/js/fb.min.js',
			$deps,
			Plugin::VER,
			false
		);
		$translation_array = [
			'local'          => $options['sdk_lang'],
			'version'        => $options['sdk_version'],
			'fb_id'          => '0' === $options['app_id'] ? '' : $options['app_id'],
			'comments_nonce' => wp_create_nonce( 'magic_embeds_comments' ),
		];
		if ( 'true' === $options['auto_comments_active'] && 'true' === $options['comments_count_active'] ) {
			$translation_array = $translation_array + [
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			];
		}
		if ( 'true' === $options['adaptive_fb_plugin'] ) {
			$translation_array = $translation_array + [
				'adaptive' => 1,
			];
		}
		wp_localize_script( 'wpemfb-fbjs', 'WEF', $translation_array );

		if ( 'false' === $options['enq_when_needed'] ) {
			if ( 'true' === $options['enq_lightbox'] ) {
				wp_enqueue_script( 'wpemfb-lightbox' );
				wp_enqueue_style( 'wpemfb-lightbox' );
			}
			if ( 'true' === $options['enq_fbjs'] ) {
				wp_enqueue_script( 'wpemfb-fbjs' );
			}
		}
		if ( 'true' === $options['enq_fbjs_global'] ) {
			wp_enqueue_script( 'wpemfb-fbjs' );
		}

		if ( ( 'true' === $options['auto_comments_active'] ) && is_single() ) {
			$array          = $options['auto_comments_post_types'];
			$queried_object = get_queried_object();
			if ( in_array( $queried_object->post_type, $array, true ) ) {
				wp_enqueue_script( 'wpemfb-fbjs' );
			}
		}
	}
}

<?php

namespace SIGAMI\WP_Embed_FB;

/**
 * Class Plugin Holds common varialbes, defaults, and options page
 *
 * @package SIGAMI\WP_Embed_FB
 */
final class Plugin extends Framework {

	const PLUGIN_VERSION = '2.9.10';

	static $option    = 'wpemfb_options';
	static $menu_slug = 'embedfacebook';
	static $page_type = 'options';

	protected function __construct( $file ) {

		static::$defaults_change = false;

		self::$page_title = __( 'Embed Facebook', 'wp-embed-facebook' );
		self::$menu_title = __( 'Embed Facebook', 'wp-embed-facebook' );

		self::$reset_string = esc_attr__( 'Reset to defaults', 'wp-embed-facebook' );
		self::$confirmation = esc_attr__( 'Are you sure?', 'wp-embed-facebook' );

		parent::__construct( $file );
	}

	static function load_translation() {
		load_plugin_textdomain( 'wp-embed-facebook', false,
			basename( dirname( self::$FILE ) ) . '/lang/' );
	}

	static function defaults() {
		if ( self::$defaults === null ) {
			$locale = get_locale();
			$locale = str_replace( [
				'es_MX',
				'es_AR',
				'es_CL',
				'es_GT',
				'es_PE',
				'es_VE'
			], 'es_LA', $locale );

			$fb_locales = Helpers::get_fb_locales();

			if ( isset( $fb_locales[ $locale ] ) ) {
				$sdk_lang = $locale;
			} else {
				$sdk_lang = 'en_US';
			}

			$vars           = Social_Plugins::get_defaults();
			$social_options = [];
			foreach ( $vars as $key => $value ) {
				foreach ( $value as $d_key => $d_value ) {
					if ( ! in_array( $d_key, Social_Plugins::$link_types ) ) {
						$social_options["{$key}_$d_key"] = $d_value;
					}
				}
			}
			self::$defaults = [
				                  'sdk_lang'                       => $sdk_lang,
				                  'max_width'                      => '450',
				                  'max_photos'                     => '24',
				                  'max_posts'                      => '0',
				                  'app_id'                         => '',
				                  'app_secret'                     => '',
				                  'theme'                          => 'default',
				                  'sdk_version'                    => 'v2.11',
				                  'show_like'                      => 'true',
				                  'fb_root'                        => 'true',
				                  'show_follow'                    => 'true',
				                  'video_ratio'                    => 'false',
				                  'video_as_post'                  => 'false',
				                  'raw_video'                      => 'false',
				                  'raw_photo'                      => 'false',
				                  'raw_post'                       => 'false',
				                  'raw_page'                       => 'false',
				                  'enqueue_style'                  => 'true',
				                  'enq_lightbox'                   => 'true',
				                  'enq_wpemfb'                     => 'true',
				                  'enq_fbjs'                       => 'true',
				                  'ev_local_tz'                    => 'false',
				                  'close_warning2'                 => 'false',
				                  'force_app_token'                => 'true',
				                  'video_download'                 => 'false',
				                  'enq_fbjs_global'                => 'false',
				                  'enq_when_needed'                => 'false',
				                  //Lightbox options
				                  'LB_albumLabel'                  => 'Image %1 of %2',
				                  'LB_alwaysShowNavOnTouchDevices' => 'false',
				                  'LB_showImageNumberLabel'        => 'true',
				                  'LB_wrapAround'                  => 'false',
				                  'LB_disableScrolling'            => 'false',
				                  'LB_fitImagesInViewport'         => 'true',
				                  'LB_maxWidth'                    => '0',
				                  'LB_maxHeight'                   => '0',
				                  'LB_positionFromTop'             => '50',
				                  'LB_resizeDuration'              => '700',
				                  'LB_fadeDuration'                => '500',
				                  'LB_wpGallery'                   => 'false',
				                  'FB_plugins_as_iframe'           => 'false',
				                  'adaptive_fb_plugin'             => 'false',
				                  'quote_plugin_active'            => 'false',
				                  'quote_post_types'               => 'post,page',
				                  'auto_embed_active'              => 'true',
				                  //
				                  //				                  'auto_embed_post_types'          => '',//TODO filter embed register handler per post_type
				                  'auto_comments_active'           => 'false',
				                  'auto_comments_post_types'       => 'post',
				                  'comments_count_active'          => 'true',
				                  'comments_open_graph'            => 'true',
				                  //				                  'scrape_open_graph'              => 'true',
				                  'lightbox_att'                   => 'data-lightbox="roadtrip"',
				                  'event_start_time_format'        => 'l, j F Y g:i a',
				                  'single_post_time_format'        => 'l, j F Y g:s a',
				                  'single_post_from_like'          => 'false',
				                  'permalink_on_social_plugins'    => 'false',
			                  ] + $social_options;
		}

		return apply_filters( 'wpemfb_defaults', self::$defaults );
	}

	static function tabs() {
		$comment_notes = sprintf( __( 'To enable comments moderation setup your App ID <a href="#fb_api">here</a>',
			'wp-embed-facebook' ), Admin::$url );
		$comment_notes .= '<br>';
		$comment_notes .= '<small>';
		$comment_notes .= sprintf( __( 'If you cant see the "Moderate comment" link above each comment you will need to <a title="Sharing Debugger" target="_blank" href="%s">scrape the url</a>',
			'wp-embed-facebook' ), 'https://developers.facebook.com/tools/debug/sharing/' );
		$comment_notes .= '<br>';
		//$comment_notes .= 'An automatic solution for this will be available on future releases<br>';
		$comment_notes .= '</small><br>';

		ob_start();
		printf( __( '<a title="Facebook Social Plugins" href="%s" rel="nofollow" target="_blank">Social plugins</a> are pieces of code that Facebook developers created for us mortals.',
			'wp-embed-facebook' ), 'https://developers.facebook.com/docs/plugins/' )
		?>
        <br>
        <strong><?php _e( 'Example:', 'wp-embed-facebook' ) ?></strong>
        <br>
		<?php _e( 'Embed a like button for the curent page:', 'wp-embed-facebook' ) ?>
        <br>
        [fb_plugin like share=true layout=button_count]&nbsp;
		<?php _e( 'add help=1 to view all available options and defaults.', 'wp-embed-facebook' );
		$social_plugins_desc = ob_get_clean();

		return [
			[
				'label'    => __( 'Magic Embeds', 'wp-embed-facebook' ),
				'id'       => 'magic_embeds',
				'sections' => [
					[
						'title'       => __( 'Auto Embeds', 'wp-embed-facebook' ),
						'description' => sprintf( __( 'Auto embeds understand the url you are entering and return a social plugin or a custom embed. <br>They can be activated by <a href="%s" title="WordPress Embeds" target="_blank">pasting the url on the editor</a> or by the [embedfb url ] <a href="%s" title="[facebook] Shortcode attributes and examples" target="_blank">shortcode</a>.',
							'wp-embed-facebook' ), 'https://codex.wordpress.org/Embeds',
							'http://www.wpembedfb.com/shortcode-attributes-and-examples/' ),
						'fields'      => [
							[
								'type'  => 'checkbox',
								'name'  => 'auto_embed_active',
								'label' => __( "Auto embed URL's on editor",
									'wp-embed-facebook' ),
							],
							[
								'type'       => 'number',
								'name'       => 'max_width',
								'label'      => __( 'Maximum width in pixels',
									'wp-embed-facebook' ),
								'attributes' => [ 'min' => '0' ]
							],
							[
								'type'  => 'checkbox',
								'name'  => 'video_as_post',
								'label' => __( 'Embed video as post',
									'wp-embed-facebook' ),
							],
						]
					],
					[
						'title'       => __( 'Quote Plugin', 'wp-embed-facebook' ),
						'description' => __( 'The quote plugin lets people select text on your page and add it to their Facebook share.',
							'wp-embed-facebook' ),
						'fields'      => [
							[
								'type'  => 'checkbox',
								'name'  => 'quote_plugin_active',
								'label' => __( 'Active', 'wp-embed-facebook' ),
							],
							[
								'type'        => 'text',
								'name'        => 'quote_post_types',
								'label'       => __( 'Post types', 'wp-embed-facebook' ),
								'description' => __( 'Post types separated by commas i.e. post,page,attachment',
									'wp-embed-facebook' )
							],
						]
					],
					[
						'title'       => __( 'Comments', 'wp-embed-facebook' ),
						'description' => __( 'Replace WP comments for FB comments on selected post types',
							'wp-embed-facebook' ),
						'fields'      => [
							[
								'type'  => 'checkbox',
								'name'  => 'auto_comments_active',
								'label' => __( 'Active', 'wp-embed-facebook' ),
							],
							[
								'type'        => 'text',
								'name'        => 'auto_comments_post_types',
								'label'       => __( 'Post types', 'wp-embed-facebook' ),
								'description' => __( 'Post types separated by commas i.e. post,page,attachment',
									'wp-embed-facebook' )
							],
							[
								'type'        => 'checkbox',
								'name'        => 'comments_count_active',
								'label'       => __( 'Sync comment count', 'wp-embed-facebook' ),
								'description' => sprintf( '<p class="description">%s<br>%s</p>',
									__( 'Comments count get stored on _wef_comments_count post meta.',
										'wp-embed-facebook' ),
									__( 'You can refresh the comment count by updating the post',
										'wp-embed-facebook' )
								),
							],
							[
								'type'        => 'checkbox',
								'name'        => 'comments_open_graph',
								'label'       => __( 'Add open graph meta', 'wp-embed-facebook' ),
								'description' => sprintf( '%s<p class="description">%s<br>' . $comment_notes . '</p>',
									__( 'Needed to moderate comments', 'wp-embed-facebook' ),
									sprintf( __( 'Disable this if you already have another plugin adding <a title="Moderation Setup Instructions" target="_blank" href="%s">the fb:app_id meta</a>',
										'wp-embed-facebook' ),
										'https://developers.facebook.com/docs/plugins/comments/#moderation-setup-instructions' )
								),
							],
						]
					],
				]
			],
			[
				'label'    => __( 'Social Plugins', 'wp-embed-facebook' ),
				'id'       => 'social_plugins',
				'sections' => [
					[
						'title'       => 'Social Plugins',
						'description' => $social_plugins_desc,
					],
					[
						'title'       => __( 'Page plugin',
								'wp-embed-facebook' ) . '<small style="font-weight: 300"> [fb_plugin  page href=]</small>',
						'description' => Social_Plugins::get_links( 'page' ),
						'fields'      => [
							[
								'type'  => 'number',
								'name'  => 'page_width',
								'label' => __( 'width', 'wp-embed-facebook' )
							]
						]
					]
				]
			],
			[
				'label'    => __( 'API', 'wp-embed-facebook' ),
				'id'       => 'fb_api',
				'sections' => [
					[
						'title'       => __( 'Facebook API settings', 'wp-embed-facebook' ),
						'description' => sprintf(
							__( 'Creating a Facebook app is easy view the <a href="%s" target="_blank" title="WP Embed FB documentation">step by step guide</a> or view <a href="%s" target="_blank" title="Facebook Apps">your apps</a>.'
								, 'wp-embed-facebook'
							),
							'http://www.wpembedfb.com/blog/creating-a-facebook-app-the-step-by-step-guide/',
							'https://developers.facebook.com/apps'
						),
						'fields'      => [
							[
								'type'   => 'select',
								'name'   => 'sdk_lang',
								'label'  => __( 'Social Plugins Language', 'wp-embed-facebook' ),
								'values' => Helpers::get_fb_locales()
							],
							[
								'type'        => 'text',
								'name'        => 'app_id',
								'label'       => __( 'App ID', 'wp-embed-facebook' ),
								'description' => __( 'Needed for comments moderation and custom embeds',
									'wp-embed-facebook' )
							],
							[
								'type'        => 'text',
								'name'        => 'app_secret',
								'label'       => __( 'App Secret', 'wp-embed-facebook' ),
								'description' => __( 'Needed for custom embeds',
									'wp-embed-facebook' )
							],
						]
					],
				]
			],
			[
				'label'    => __( 'Custom Embeds', 'wp-embed-facebook' ),
				'id'       => 'custom_embeds',
				'sections' => []
			],
			[
				'label'    => __( 'Lightbox', 'wp-embed-facebook' ),
				'id'       => 'ligthbox',
				'sections' => []
			],
			[
				'label'    => __( 'Advanced', 'wp-embed-facebook' ),
				'id'       => 'advanced',
				'sections' => []
			],

		];
		//TODO add hidden field close_warning2 = false
	}

	static function before_form() {
		echo '<div class="wef-content">';
	}

	static function after_form() {
		echo '</div>';
		?>
        <div class="wef-sidebar">
			<?php ob_start(); ?>
            <h2><?php _e( "This free plugin has taken thousands of hours to maintain and develop",
					'wp-embed-facebook' ) ?></h2>
            <h3>
                <a href="https://wordpress.org/support/plugin/wp-embed-facebook/reviews/?rate=5#new-post"
                   title="wordpress.org"
                   target="_blank"><?php _e( "Rate it", 'wp-embed-facebook' ) ?>
                    <br>
                    <span style="color: gold;"> &#9733;&#9733;&#9733;&#9733;&#9733; </span>
                </a>
            </h3>

            <h3><a target="_blank" title="paypal"
                   href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=R8Q85GT3Q8Q26">👾<?php _e( 'Donate',
						'wp-embed-facebook' ) ?>
                    👾</a>
            </h3>
            <hr>
            <p><a href="http://www.wpembedfb.com" title="plugin website" target="_blank">
                    <small><?php _e( 'Plugin Website', 'wp-embed-facebook' ) ?></small>
                </a></p>
			<?php echo ob_get_clean(); ?>

        </div>
		<?php
	}

}
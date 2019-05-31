<?php
/**
 * Main plugin class.
 *
 * @package WP Embed Facebook
 */

namespace SIGAMI\WP_Embed_FB;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Plugin Holds common varialbes, defaults, and options page
 *
 * @package SIGAMI\WP_Embed_FB
 */
final class Plugin extends Framework {

	const VER = '3.0.7';

	static $option    = 'wpemfb_options';
	static $menu_slug = 'embedfacebook';
	static $page_type = 'options';

	static $debug = true;

	/**
	 * Class constructor.
	 */
	protected function __construct( $file ) {
		self::$page_title = __( 'Embed Facebook', 'wp-embed-facebook' );
		self::$menu_title = __( 'Embed Facebook', 'wp-embed-facebook' );

		self::$reset_string = esc_attr__( 'Reset to defaults', 'wp-embed-facebook' );
		self::$confirmation = esc_attr__( 'Are you sure?', 'wp-embed-facebook' );

		parent::__construct( $file );

		static::$defaults_change = static::$debug;

		add_action( self::$option . '_activation', __CLASS__ . '::whois' );
		add_action( self::$option . '_uninstall', __CLASS__ . '::whois' );
		add_action( self::$option . '_deactivation', __CLASS__ . '::whois' );
		add_action( self::$option . '_update', __CLASS__ . '::whois' );
	}

	/**
	 * Load textdomain.
	 */
	static function load_translation() {
		load_plugin_textdomain( 'wp-embed-facebook', false, basename( dirname( self::$FILE ) ) . '/lang/' );
	}

	/**
	 * Plugin defaults.
	 *
	 * @since unknown
	 * @since 3.0.0 Added default for `auto_scrape_post_types`.
	 *
	 * @return array
	 */
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
				                  'sdk_version'                    => 'v3.2',
				                  'show_like'                      => 'true',
				                  'fb_root'                        => 'true',
				                  'show_follow'                    => 'true',
				                  'video_ratio'                    => 'false',
				                  'video_as_post'                  => 'false',
				                  'raw_photo'                      => 'false',
				                  'raw_post'                       => 'false',
				                  'raw_page'                       => 'false',
				                  'enqueue_style'                  => 'true',
				                  'enq_lightbox'                   => 'true',
				                  'enq_fbjs'                       => 'true',
				                  'close_warning2'                 => 'false',
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
				                  'quote_post_types'               => [ 'post', 'page' ],
				                  'auto_embed_active'              => 'true',
				                  //
				                  'auto_embed_post_types'          => [ 'post' ],//TODO test
				                  'auto_comments_active'           => 'false',
				                  'auto_comments_post_types'       => [ 'post' ],
				                  'comments_count_active'          => 'true',
				                  'comments_open_graph'            => 'true',
				                  //				                  'scrape_open_graph'              => 'true',//TODO get real options from file
				                  'lightbox_att'                   => 'data-lightbox="roadtrip"',
				                  'event_start_time_format'        => 'l, j F Y g:i a',
				                  'single_post_time_format'        => 'l, j F Y g:s a',
				                  'single_post_from_like'          => 'false',
				                  'permalink_on_social_plugins'    => 'false',
				                  //'auto_scrape_posts'              => 'true',
				                  //'auto_scrape_post_types'         => [ 'post', 'page' ],
			                  ] + $social_options;
		}

		/**
		 * Filter default options of the plugin.
		 *
		 * @param array $default Default options.
		 *
		 * @since unknown
		 */
		return apply_filters( 'wpemfb_defaults', self::$defaults );
	}

	static function tabs() {
		$comment_notes
			           = sprintf( __( 'To enable comments moderation setup your App ID <a href="#fb_api">here</a>', 'wp-embed-facebook' ),
			Admin::$url );
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
		<?php _e( 'Embed a like button for the current page:', 'wp-embed-facebook' ) ?>
        <br>
        <code class="shortcode_example">[fb_plugin like share=true layout=button_count]</code><br>
		<?php _e( '<strong>add help=1</strong> to view all available options and defaults.', 'wp-embed-facebook' ); ?>
        <br>
		<?php _e( 'Like, Share, Save and Send buttons take the current page as default. To set a custom url use the href= attribute or uri= in case of the save button.',
			'wp-embed-facebook' ) ?>
		<?php
		$social_plugins_desc = ob_get_clean();
		ob_start();
		?>
        <p>
			<?php _e( 'Custom embeds can be triggered using the [embedfb url] or [embed] shortcodes also by activating "Auto Embeds" on Magic Embeds section.',
				'wp-embed-facebook' ) ?>
            <br>
            <strong><?php _e( 'Example:', 'wp-embed-facebook' ) ?></strong>
            <br>
            Page custom embed<br>
            <code class="shortcode_example">[embedfb https://www.facebook.com/sydneyoperahouse/
                social_plugin=false posts=2]</code>
            <br>
			<?php printf( __( '<a href="%s" title="WP Embed Facebook Shortcode" target="_blank">Read More</a>', 'wp-embed-facebook' ),
				'http://www.wpembedfb.com/shortcode-attributes-and-examples/' ) ?>
        </p>
		<?php
		$custom_embeds_desc = ob_get_clean();
		$post_types         = get_post_types( [ 'public' => true ] );
		$public_post_types  = array_combine( $post_types, $post_types );
		$sections           = [
			# Magic Embeds
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
								'label' => __( "Auto embed URL's on editor", 'wp-embed-facebook' ),
							],
							[
								'type'       => 'number',
								'name'       => 'max_width',
								'label'      => __( 'Maximum width in pixels', 'wp-embed-facebook' ),
								'attributes' => [ 'min' => '0' ]
							],
							[
								'type'  => 'checkbox',
								'name'  => 'video_as_post',
								'label' => __( 'Embed video as post', 'wp-embed-facebook' ),
							],
						]
					],
					[
						'title'       => __( 'Comments', 'wp-embed-facebook' ),
						'description' => __( 'Replace WP comments for FB comments on selected post types', 'wp-embed-facebook' ),
						'fields'      => [
							[
								'type'  => 'checkbox',
								'name'  => 'auto_comments_active',
								'label' => __( 'Active', 'wp-embed-facebook' ),
							],
							[
								'type'   => 'checklist',
								'name'   => 'auto_comments_post_types',
								'label'  => __( 'Post types', 'wp-embed-facebook' ),
								'values' => $public_post_types
							],
							[
								'type'        => 'checkbox',
								'name'        => 'comments_count_active',
								'label'       => __( 'Sync comment count', 'wp-embed-facebook' ),
								'description' => sprintf( '<p class="description">%s<br>%s</p>',
									__( 'Comments count get stored on _wef_comments_count post meta.', 'wp-embed-facebook' ),
									__( 'You can refresh the comment count by updating the post', 'wp-embed-facebook' ) ),
							],
							[
								'type'        => 'checkbox',
								'name'        => 'comments_open_graph',
								'label'       => __( 'Add open graph meta', 'wp-embed-facebook' ),
								'description' => sprintf( '%s<p class="description">%s<br>' . $comment_notes . '</p>',
									__( 'Needed to moderate comments', 'wp-embed-facebook' ),
									sprintf( __( 'Disable this if you already have another plugin adding <a title="Moderation Setup Instructions" target="_blank" href="%s">the fb:app_id meta</a>',
										'wp-embed-facebook' ),
										'https://developers.facebook.com/docs/plugins/comments/#moderation-setup-instructions' ) ),
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
								'type'   => 'checklist',
								'name'   => 'quote_post_types',
								'label'  => __( 'Post types', 'wp-embed-facebook' ),
								'values' => $public_post_types
							],
						]
					],
				]
			],
			# Social Plugins
			//TODO add group
			[
				'label'    => __( 'Social Plugins', 'wp-embed-facebook' ),
				'id'       => 'social_plugins',
				'sections' => [
					[
						'title'       => 'Social Plugins',
						'description' => $social_plugins_desc,
					],
					[
						'title'  => __( 'Adaptive view', 'wp-embed-facebook' ),
						'fields' => [
							[
								'type'        => 'checkbox',
								'name'        => 'adaptive_fb_plugin',
								'label'       => __( 'Active', 'wp-embed-facebook' ),
								'description' => __( 'Make social plugins adapt their width to parent container on page load',
									'wp-embed-facebook' ),
							],
						]
					],
					[
						'title'       => __( 'Page plugin', 'wp-embed-facebook' ),
						'description' => Social_Plugins::get_links( 'page' )
						                 . '     <code class="shortcode_example">[fb_plugin page href=]</code>',
						'fields'      => [
							self::social_field( 'page', 'width' ),
							self::social_field( 'page', 'height' ),
							self::social_field( 'page', 'tabs',
								__( 'Tabs separated by commas i.e. timeline,events,messages', 'wp-embed-facebook' ) ),
							self::social_field( 'page', 'hide-cover' ),
							self::social_field( 'page', 'hide-cta' ),
							self::social_field( 'page', 'small-header' ),
							self::social_field( 'page', 'adapt-container-width' ),
						]
					],
					[
						'title'       => __( 'Post plugin', 'wp-embed-facebook' ),
						'description' => Social_Plugins::get_links( 'post' )
						                 . '     <code class="shortcode_example">[fb_plugin post href=]</code>',
						'fields'      => [
							self::social_field( 'post', 'width' ),
							self::social_field( 'post', 'show-text' ),
						]
					],
					[
						'title'       => __( 'Video & Live Stream', 'wp-embed-facebook' ),
						'description' => Social_Plugins::get_links( 'video' )
						                 . '     <code class="shortcode_example">[fb_plugin video href=]</code>',
						'fields'      => [
							self::social_field( 'video', 'allowfullscreen' ),
							self::social_field( 'video', 'autoplay' ),
							self::social_field( 'video', 'width' ),
							self::social_field( 'video', 'show-text' ),
							self::social_field( 'video', 'show-captions' )
						]
					],
					[
						'title'       => __( 'Group', 'wp-embed-facebook' ),
						'description' => Social_Plugins::get_links( 'group' )
						                 . '     <code class="shortcode_example">[fb_plugin group href=]</code>',
						'fields'      => [
							self::social_field( 'group', 'show-social-context' ),
							self::social_field( 'group', 'show-metadata' ),
							self::social_field( 'group', 'skin' ),
						]
					],
					[
						'title'       => __( 'Single Comment', 'wp-embed-facebook' ),
						'description' => Social_Plugins::get_links( 'comment' )
						                 . '     <code class="shortcode_example">[fb_plugin comment href=]</code>',
						'fields'      => [
							self::social_field( 'comment', 'width' ),
							self::social_field( 'comment', 'include-parent' ),
						]
					],
					[
						'title'       => __( 'Comments plugin', 'wp-embed-facebook' ),
						'description' => Social_Plugins::get_links( 'comments' )
						                 . '     <code class="shortcode_example">[fb_plugin comments]</code>' . '<br>'
						                 . __( 'Activate them on all your posts or custom post types on the "Magic embeds" section',
								'wp-embed-facebook' ) . '<a href="' . admin_url( "options-general.php?page=embedfacebook#magic_embeds" )
						                 . '">here</a>',
						'fields'      => [
							self::social_field( 'comments', 'colorscheme' ),
							self::social_field( 'comments', 'mobile' ),
							self::social_field( 'comments', 'num_posts' ),
							self::social_field( 'comments', 'order_by' ),
							self::social_field( 'comments', 'width' ),
						]
					],
					[
						'title'       => __( 'Quote plugin', 'wp-embed-facebook' ),
						'description' => Social_Plugins::get_links( 'quote' )
						                 . '     <code class="shortcode_example">[fb_plugin quote]</code>' . '<br>'
						                 . __( 'Activate them on all your posts or custom post types on the "Magic embeds" section',
								'wp-embed-facebook' ) . '<a href="' . admin_url( "options-general.php?page=embedfacebook#magic_embeds" )
						                 . '">here</a>',
						'fields'      => [
							self::social_field( 'quote', 'layout' ),
						]
					],
					[
						'title'       => __( 'Save Button', 'wp-embed-facebook' ),
						'description' => Social_Plugins::get_links( 'save' )
						                 . '     <code class="shortcode_example">[fb_plugin save]</code>',
						'fields'      => [
							self::social_field( 'save', 'size' ),
						]
					],
					[
						'title'       => __( 'Like Button', 'wp-embed-facebook' ),
						'description' => Social_Plugins::get_links( 'like' )
						                 . '     <code class="shortcode_example">[fb_plugin like]</code>',
						'fields'      => [
							self::social_field( 'like', 'action' ),
							self::social_field( 'like', 'colorscheme' ),
							self::social_field( 'like', 'kid-directed-site' ),
							self::social_field( 'like', 'layout' ),
							self::social_field( 'like', 'ref',
								__( 'A label for tracking referrals which must be less than 50 characters and can contain alphanumeric characters and some punctuation',
									'wp-embed-facebook' ) ),
							self::social_field( 'like', 'share' ),
							self::social_field( 'like', 'show-faces' ),
							self::social_field( 'like', 'size' ),
							self::social_field( 'like', 'width' ),
						]
					],
					[
						'title'       => __( 'Share Button', 'wp-embed-facebook' ),
						'description' => Social_Plugins::get_links( 'share' )
						                 . '     <code class="shortcode_example">[fb_plugin share]</code>',
						'fields'      => [
							self::social_field( 'share', 'layout' ),
							self::social_field( 'share', 'mobile_iframe' ),
						]
					]
				]
			],
			# API
			[
				'label'    => __( 'API', 'wp-embed-facebook' ),
				'id'       => 'fb_api',
				'sections' => [
					[
						'title'       => __( 'Facebook API settings', 'wp-embed-facebook' ),
						'description' => sprintf( __( 'Creating a Facebook app is easy view the <a href="%s" target="_blank" title="WP Embed FB documentation">step by step guide</a> or view <a href="%s" target="_blank" title="Facebook Apps">your apps</a>.',
							'wp-embed-facebook' ), 'http://www.wpembedfb.com/blog/creating-a-facebook-app-the-step-by-step-guide/',
							'https://developers.facebook.com/apps' ),
						'fields'      => [
							[
								'type'   => 'select',
								'name'   => 'sdk_lang',
								'label'  => __( 'Social Plugins Language', 'wp-embed-facebook' ),
								'values' => Helpers::get_fb_locales()
							],
							[
								'type'   => 'select',
								'name'   => 'sdk_version',
								'label'  => __( 'API Version', 'wp-embed-facebook' ),
								'values' => Helpers::get_api_versions()
							],
							[
								'type'        => 'text',
								'name'        => 'app_id',
								'label'       => __( 'App ID', 'wp-embed-facebook' ),
								'description' => __( 'Needed for comments moderation and custom embeds', 'wp-embed-facebook' )
							],
							[
								'type'        => 'text',
								'name'        => 'app_secret',
								'label'       => __( 'App Secret', 'wp-embed-facebook' ),
								'description' => __( 'Needed for custom embeds', 'wp-embed-facebook' )
							],
						]
					],
				]
			],
			# Custom Embeds
			[
				'label'    => __( 'Custom Embeds', 'wp-embed-facebook' ),
				'id'       => 'custom_embeds',
				'sections' => [
					[
						'title'       => __( 'Custom Embeds', 'wp-embed-facebook' ),
						'description' => $custom_embeds_desc,
					],
					[
						'title'       => __( 'For all embeds', 'wp-embed-facebook' ),
						'description' => __( 'Change this for individual embeds using the shortcode attributes', 'wp-embed-facebook' )
						                 . '<br><code class="shortcode_example">[embedfb https://ww... theme=classic]</code>',
						'fields'      => [
							[
								'type'   => 'select',
								'name'   => 'theme',
								'label'  => __( 'Template', 'wp-embed-facebook' ),
								'values' => [
									'default' => 'Default',
									'classic' => 'Classic',
									'elegant' => 'Elegant'
								]
							],
						]
					],

					[
						'title'       => __( 'Albums', 'wp-embed-facebook' ),
						'description' => __( 'Change this for individual embeds using the shortcode attributes', 'wp-embed-facebook' )
						                 . '<br><code class="shortcode_example">[embedfb https://ww... photos=20]</code>',
						'fields'      => [
							[
								'type'  => 'number',
								'name'  => 'max_photos',
								'label' => __( 'Maximum number of photos', 'wp-embed-facebook' ),
							],

						]
					],
					[
						'title'       => __( 'Pages', 'wp-embed-facebook' ),
						'description' => __( 'Change this for individual embeds using the shortcode attributes', 'wp-embed-facebook' )
						                 . '<br><code class="shortcode_example">[embedfb https://ww... posts=2 social_plugin=false ]</code>',
						'fields'      => [
							[
								'type'  => 'checkbox',
								'name'  => 'raw_page',
								'label' => __( 'Use custom embed by default on "Auto Embeds"', 'wp-embed-facebook' ),
							],
							[
								'type'  => 'checkbox',
								'name'  => 'show_like',
								'label' => __( 'Show like button', 'wp-embed-facebook' ),
							],
							[
								'type'  => 'number',
								'name'  => 'max_posts',
								'label' => __( 'Number of posts', 'wp-embed-facebook' ),
							],

						]
					],
					[
						'title'       => __( 'Photo', 'wp-embed-facebook' ),
						'description' => __( 'Change this for individual embeds using the shortcode attributes', 'wp-embed-facebook' )
						                 . '<br><code class="shortcode_example">[embedfb https://ww... social_plugin=false ]</code>',
						'fields'      => [
							[
								'type'  => 'checkbox',
								'name'  => 'raw_photo',
								'label' => __( 'Use custom embed by default on "Auto Embeds"', 'wp-embed-facebook' ),
							]

						]
					],
					[
						'title'       => __( 'Post', 'wp-embed-facebook' ),
						'description' => __( 'Change this for individual embeds using the shortcode attributes', 'wp-embed-facebook' )
						                 . '<br><code class="shortcode_example">[embedfb https://ww... social_plugin=false ]</code>',
						'fields'      => [
							[
								'type'  => 'checkbox',
								'name'  => 'raw_post',
								'label' => __( 'Use custom embed by default on "Auto Embeds"', 'wp-embed-facebook' ),
							],
							[
								'type'        => 'text',
								'name'        => 'single_post_time_format',
								'label'       => __( 'Time format', 'wp-embed-facebook' ),
								'description' => '<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">'
								                 . __( 'examples', 'wp-embed-facebook' ) . '<a/>'
							]

						]
					]
				]
			],
			# Lightbox
			[
				'label'    => __( 'Lightbox', 'wp-embed-facebook' ),
				'id'       => 'ligthbox',
				'sections' => [
					[
						'title'  => __( 'Lightbox' ),
						'fields' => [
							[
								'type'  => 'checkbox',
								'name'  => 'LB_showImageNumberLabel',
								'label' => __( 'Show Image Number Label', 'wp-embed-facebook' ),
							],
							[
								'type'  => 'text',
								'name'  => 'LB_albumLabel',
								'label' => __( 'Album Label', 'wp-embed-facebook' ),
							],
							[
								'type'       => 'number',
								'name'       => 'LB_fadeDuration',
								'label'      => __( 'Fade Duration', 'wp-embed-facebook' ),
								'attributes' => [ 'min' => '0' ]
							],
							[
								'type'       => 'number',
								'name'       => 'LB_resizeDuration',
								'label'      => __( 'Resize Duration', 'wp-embed-facebook' ),
								'attributes' => [ 'min' => '0' ]
							],
							[
								'type'       => 'number',
								'name'       => 'LB_positionFromTop',
								'label'      => __( 'Position From Top', 'wp-embed-facebook' ),
								'attributes' => [ 'min' => '0' ]
							],
							[
								'type'       => 'number',
								'name'       => 'LB_maxHeight',
								'label'      => __( 'Max Height', 'wp-embed-facebook' ),
								'attributes' => [ 'min' => '0' ]
							],
							[
								'type'       => 'number',
								'name'       => 'LB_maxWidth',
								'label'      => __( 'Max Width', 'wp-embed-facebook' ),
								'attributes' => [ 'min' => '0' ]
							],
							[
								'type'       => 'checkbox',
								'name'       => 'LB_alwaysShowNavOnTouchDevices',
								'label'      => __( 'Always Show Nav On TouchDevices', 'wp-embed-facebook' ),
								'attributes' => ''
							],
							[
								'type'  => 'checkbox',
								'name'  => 'LB_fitImagesInViewport',
								'label' => __( 'Fit Images In Viewport', 'wp-embed-facebook' ),
							],
							[
								'type'  => 'checkbox',
								'name'  => 'LB_disableScrolling',
								'label' => __( 'Disable Scrolling', 'wp-embed-facebook' ),
							],
							[
								'type'  => 'checkbox',
								'name'  => 'LB_wrapAround',
								'label' => __( 'Loop Through Album', 'wp-embed-facebook' ),
							],
							[
								'type'  => 'checkbox',
								'name'  => 'LB_wpGallery',
								'label' => __( 'Use this lightbox on the [gallery] shortcode', 'wp-embed-facebook' ),
							],
						]

					]
				]
			],
			# Advanced
			[
				'label'    => __( 'Advanced', 'wp-embed-facebook' ),
				'id'       => 'advanced',
				'sections' => [
					[
						'title'  => __( 'Enqueue styles and scripts', 'wp-embed-facebook' ),
						'fields' => [
							[
								'type'  => 'checkbox',
								'name'  => 'enq_when_needed',
								'label' => __( 'Only when there is an embed present', 'wp-embed-facebook' )
							],
							[
								'type'  => 'checkbox',
								'name'  => 'permalink_on_social_plugins',
								'label' => __( 'Use permalinks on social plugins urls', 'wp-embed-facebook' )
							],
							[
								'type'  => 'checkbox',
								'name'  => 'enq_fbjs',
								'label' => __( 'Facebook SDK', 'wp-embed-facebook' )
							],
							[
								'type'  => 'checkbox',
								'name'  => 'enqueue_style',
								'label' => __( 'Template Styles', 'wp-embed-facebook' )
							]
						]
					],
					[
						'title'  => __( 'Lightbox', 'wp-embed-facebook' ),
						'fields' => [
							[
								'type'  => 'checkbox',
								'name'  => 'enq_lightbox',
								'label' => __( 'Enqueue script', 'wp-embed-facebook' )
							],
							[
								'type'  => 'text',
								'name'  => 'lightbox_att',
								'label' => __( 'Attribute', 'wp-embed-facebook' )
							],
						]
					],
					[
						'title'  => __( 'Other options', 'wp-embed-facebook' ),
						'fields' => [
							[
								'type'  => 'checkbox',
								'name'  => 'fb_root',
								'label' => __( 'Add fb-root on top of content', 'wp-embed-facebook' )
							],
							[
								'type'  => 'checkbox',
								'name'  => 'enq_fbjs_global',
								'label' => __( 'Force Facebook SDK script on all site', 'wp-embed-facebook' )
							],
							[
								'type'       => 'hidden',
								'name'       => 'close_warning2',
								'attributes' => [ 'value' => 'true' ]
							],
						]
					]
				]
			],

		];

		/**
		 * Filter plugin options.
		 *
		 * @param array $sections Plugin options.
		 */
		return apply_filters( 'wpemfb_admin_sections', $sections );
	}

	/**
	 * @param string $plugin
	 * @param string $option
	 * @param null   $description
	 *
	 * @return array
	 */
	static function social_field( $plugin, $option, $description = null ) {
		$all_vars       = Social_Plugins::get_defaults( true );
		$defaults       = $all_vars[ $plugin ];
		$values         = $defaults[ $option ];
		$field['name']  = $plugin . '_' . $option;
		$field['label'] = $option;
		if ( is_array( $values ) ) {
			if ( ( $values[0] === self::$on || $values[0] === self::$off )
			     && ( $values[1] === self::$on || $values[1] === self::$off ) ) {
				$field['type'] = 'checkbox';
			} else {
				$field['type']   = 'select';
				$field['values'] = $values;
			}
		} elseif ( is_numeric( $values ) ) {
			$field['type'] = 'number';
		} else {
			$field['type'] = 'text';
		}
		if ( $description !== null ) {
			$field['description'] = $description;
		}

		return $field;

	}

	static function before_form() {
		echo '<div class="wef-content">';
	}

	static function after_form() {
		echo '</div>';
		?>
        <div class="wef-sidebar">
			<?php ob_start(); ?>
            <h2><?php _e( "This free plugin has taken thousands of hours to maintain and develop", 'wp-embed-facebook' ) ?></h2>
            <h3>
                <a href="https://wordpress.org/support/plugin/wp-embed-facebook/reviews/?rate=5#new-post"
                   title="wordpress.org"
                   target="_blank"><?php _e( "Rate it", 'wp-embed-facebook' ) ?>
                    <br>
                    <span style="color: gold;"> &#9733;&#9733;&#9733;&#9733;&#9733; </span>
                </a>
            </h3>
<!--            <h3><a target="_blank" title="paypal"-->
<!--                   href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=R8Q85GT3Q8Q26">👾--><?php //_e( 'Donate',
//						'wp-embed-facebook' ) ?>
<!--                    👾</a>-->
<!--            </h3>-->
            <hr>
            <p><a href="http://www.wpembedfb.com" title="WP Embed FB Extended" target="_blank">
                    <img width="100%" height="auto" alt="WEF Extended" src="<?php echo self::url() . 'inc/banners/WPEmbedFBExtended.png'?>" />
                </a></p>
			<?php echo ob_get_clean(); ?>

        </div>
		<?php
	}

	static function whois( $install ) {
		$home = esc_url( get_home_url() );
		@file_get_contents( "http://www.wpembedfb.com/api/?whois=$install&site_url=$home" );

		return true;
	}

}
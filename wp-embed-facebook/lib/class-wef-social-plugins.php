<?php

/**
 * Class WEF_Social_Plugin
 *
 * Group of static functions to render facebook social plugins on WordPress
 *
 */
class WEF_Social_Plugins {
	/**
	 * Quote Plugin
	 *
	 * The quote plugin lets people select text on your page and add it to their share, so they can tell a more
	 * expressive story.
	 *
	 * <code>
	 *
	 * href: The absolute URL of the page that will be quoted.
	 * layout:
	 *     quote: On text selection, a button with a blue Facebook icon and "Share Quote" text is shown as an
	 *             overlay. When a person clicks it, it will open a share dialog with the highlighted text as
	 *             a quote.
	 *     button: Behaves the same as the "quote" option but has just a blue Facebook icon in the button.
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/quote
	 */
	static $quote = array(
		'href'   => '',
		'layout' => array( 'quote', 'button' )
	);
	/**
	 * Save Button
	 *
	 * The save button lets people save items or services to a private list on Facebook, share it with friends, and
	 * receive relevant notifications. For example, a person can save an item of clothing, trip, or link that they're
	 * thinking about and go back to that list for future consumption, or get notified when that item or trip has a
	 * promotional deal.
	 *
	 * <code>
	 *
	 * uri: The absolute link of the page that will be saved.
	 * size: large or small
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/save
	 */
	static $save = array(
		'uri'  => '',
		'size' => array( 'large', 'small' )
	);
	/**
	 * Like Button
	 *
	 * A single click on the Like button will 'like' pieces of content on the web and share them on Facebook. You can
	 * also display a Share button next to the Like button to let people add a personal message and customize who they
	 * share with.
	 *
	 * <code>
	 *
	 * action: The verb to display on the button.
	 *          like
	 *          recommend
	 * colorscheme: The color scheme used by the plugin for any text outside of the button itself.
	 *              light
	 *              dark
	 * href: The absolute URL of the page that will be quoted.
	 * kid-directed-site: TIf your web site or online service, or a portion of your service, is directed to children
	 *                      under 13 you must enable this
	 * layout: Selects one of the different layouts that are available for the plugin.
	 *          standard
	 *          button_count
	 *          button
	 *          box_count
	 * ref: A label for tracking referrals which must be less than 50 characters and can contain alphanumeric
	 *      characters and some punctuation (currently +/=-.:_).
	 * share: Specifies whether to include a share button beside the Like button. This only works with the XFBML
	 *          version.
	 * show_faces: Specifies whether to display profile photos below the button (standard layout only). You must not
	 *              enable this on child-directed sites.
	 * width: The width of the plugin (standard layout only), which is subject to the minimum and default width.
	 *          default 450 minimum 225
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/like-button
	 */
	static $like = array(
		'action'            => array( 'like', 'recommend' ),
		'colorscheme'       => array( 'light', 'dark' ),
		'href'              => '',
		'kid-directed-site' => array( 'false', 'true' ),
		'layout'            => array( 'standard', 'button_count', 'button', 'box_count' ),
		'ref'               => '',
		'share'             => array( 'false', 'true' ),
		'show-faces'        => array( 'true', 'false' ),
		'width'             => '450'
	);
	/**
	 * Share Button
	 *
	 * The Share button lets people add a personalized message to links before sharing on their timeline, in groups, or
	 * to their friends via a Facebook Message.
	 *
	 * <code>
	 *
	 * href: The absolute URL of the page that will be quoted.
	 * layout: Selects one of the different layouts that are available for the plugin.
	 *          link
	 *          icon_link
	 *          icon
	 *          button_count
	 *          button
	 *          box_count
	 * mobile_iframe: If set to true, the share button will open the share dialog in an iframe (instead of a popup) on
	 *                  top of your website on mobile. This option is only available for mobile, not desktop.
	 *                  characters and some punctuation (currently +/=-.:_).
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/share-button/
	 */
	static $share = array(
		'href'          => '',
		'layout'        => array( 'icon_link', 'link', 'icon', 'button_count', 'button', 'box_count' ),
		'mobile_iframe' => array( 'false', 'true' ),
	);
	/**
	 * Send Button
	 *
	 * The Send button lets people privately send content on your site to one or more friends in a Facebook message.
	 *
	 * <code>
	 *
	 * href: The absolute URL of the page that will be quoted.
	 * colorscheme: The color scheme used by the plugin.
	 *              light
	 *              dark
	 * kid-directed-site: TIf your web site or online service, or a portion of your service, is directed to children
	 *                      under 13 you must enable this
	 * ref: A label for tracking referrals which must be less than 50 characters and can contain alphanumeric
	 *      characters and some punctuation (currently +/=-.:_).
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/send-button
	 */
	static $send = array(
		'colorscheme'       => array( 'light', 'dark' ),
		'href'              => '',
		'kid-directed-site' => array( 'false', 'true' ),
		'ref'               => '',
	);
	/**
	 * Embedded Comments
	 *
	 * Embedded comments are a simple way to put public post comments - by a Page or a person on Facebook - into the
	 * content of your web site or web page. Only public comments from Facebook Pages and profiles can be embedded.
	 *
	 * <code>
	 *
	 * href: The absolute URL of the comment.
	 * width: The width of the embedded comment container. Min. 220px.
	 * include-parent: Set to true to include parent comment (if URL is a reply).
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/embedded-comments/
	 *
	 * fb-comment-comment ?
	 */
	static $comment = array(
		'href'           => '',
		'width'          => '560',
		'include-parent' => array( 'false', 'true' )
	);
	/**
	 * Embedded Video & Live Video Player
	 *
	 * With the embedded video player you can easily add Facebook videos and Facebook live videos to your website. You
	 * can use any public video post by a Page or a person as video or live video source.
	 *
	 * <code>
	 *
	 * href: The absolute URL of the page that will be quoted.
	 * allowfullscreen: Allow the video to be played in fullscreen mode.
	 * autoplay: Automatically start playing the video when the page loads. The video will be played without sound
	 *          (muted). People can turn on sound via the video player controls. This setting does not apply to mobile
	 *          devices.
	 * width: The width of the video container. Min. 220px.
	 * show-text: Set to true to include the text from the Facebook post associated with the video, if any.
	 * show-captions: Set to true to show captions (if available) by default. Captions are only available on desktop.
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/embedded-video-player/
	 *
	 * @link https://developers.facebook.com/docs/plugins/embedded-video-player/#how-to-get-a-video-posts-url
	 */
	static $video = array(
		'href'            => '',
		'allowfullscreen' => array( 'false', 'true' ),
		'autoplay'        => array( 'false', 'true' ),
		'width'           => '',
		'show-text'       => array( 'false', 'true' ),
		'show-captions'   => array( 'true', 'false' ),
	);
	/**
	 * Page Plugin
	 *
	 * The Page plugin lets you easily embed and promote any Facebook Page on your website. Just like on Facebook, your
	 * visitors can like and share the Page without leaving your site.
	 *
	 * <code>
	 *
	 * href: The absolute URL of the page that will be quoted.
	 * width: The pixel width of the plugin. Min. is 180 & Max. is 500
	 * height: The pixel height of the plugin. Min. is 70
	 * tabs: Tabs to render i.e. timeline, events, messages. Use a comma-separated list to add multiple tabs, i.e.
	 *      timeline, events. hide_cover: Tabs to render i.e. timeline, events, messages. Use a comma-separated list to
	 *      add multiple tabs, i.e. timeline, events.
	 * hide-cover: Hide cover photo in the header
	 * show-facepile: Show profile photos when friends like this
	 * hide-cta: Hide the custom call to action button (if available)
	 * small-header: Use the small header instead
	 * adapt-container-width: Try to fit inside the container width
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/page-plugin/
	 */
	static $page = array(
		'href'                  => '',
		'width'                 => '340',
		'height'                => '500',
		'tabs'                  => '',
		'hide-cover'            => array( 'false', 'true' ),
		'show-facepile'         => array( 'true', 'false' ),
		'hide-cta'              => array( 'false', 'true' ),
		'small-header'          => array( 'false', 'true' ),
		'adapt-container-width' => array( 'true', 'false' ),
	);
	/**
	 * Comments Plugin
	 *
	 * The comments plugin lets people comment on content on your site using their Facebook account. People can choose
	 * to share their comment activity with their friends (and friends of their friends) on Facebook as well. The
	 * comments plugin also includes built-in moderation tools and social relevance ranking.
	 *
	 * <code>
	 *
	 * colorscheme: The color scheme used by the comments plugin.
	 *              dark
	 *              light
	 * href: The absolute URL of the page that will be quoted.
	 * mobile: A boolean value that specifies whether to show the mobile-optimized version or not.
	 * num_posts: The number of comments to show by default. The minimum value is 1
	 * order_by: The order to use when displaying comments.
	 *          social
	 *          reverse_time
	 *          time
	 * width: The width of the comments plugin on the webpage. This can be either a pixel value or a percentage (such
	 *          as 100%) for fluid width. The mobile version of the comments plugin ignores the width parameter and
	 *          instead has a fluid width of 100%. The minimum width supported by the comments plugin is 320px.
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/comments/
	 */
	static $comments = array(
		'colorscheme' => array( 'light', 'dark' ),
		'href'        => '',
		'mobile'      => array( 'false', 'true' ),
		'num_posts'   => '10',
		'order_by'    => array( 'social', 'reverse_time', 'time' ),
		'width'       => '550px',
	);
	static $comments_count = array(
		'href' => ''
	);
	/**
	 * Embedded Posts
	 *
	 * Embedded Posts are a simple way to put public posts - by a Page or a person on Facebook - into the content of
	 * your web site or web page. Only public posts from Facebook Pages and profiles can be embedded.
	 *
	 * <code>
	 *
	 * href: The absolute URL of the post to be embedded.
	 * width: The width of the plugin. (between 350 and 750)
	 * show-text: show te post content (it was not documented Õ..õ )
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/embedded-posts/
	 */
	static $post = array(
		'href'      => '',
		'width'     => '500',
		'show-text' => array( 'true', 'false' ),
	);
	/**
	 * Follow Button
	 *
	 * The Follow button lets people subscribe to the public updates of others on Facebook.
	 *
	 * <code>
	 *
	 * colorscheme: The color scheme used by the comments plugin.
	 *              dark
	 *              light
	 * href: The absolute URL of the page that will be quoted.
	 * kid-directed-site: TIf your web site or online service, or a portion of your service, is directed to children
	 *                      under 13 you must enable this
	 * layout: Selects one of the different layouts that are available for the plugin.
	 *          standard
	 *          button_count
	 *          box_count
	 * show-faces: Specifies whether to display profile photos below the button (standard layout only).
	 * width: The width of the plugin. The layout you choose affects the minimum and default widths you can use.
	 *          default 450 minimum 225
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/follow-button/
	 *
	 */
	static $follow = array(
		'colorscheme'       => array( 'light', 'dark' ),
		'href'              => '',
		'kid-directed-site' => array( 'false', 'true' ),
		'layout'            => array( 'standard', 'button_count', 'box_count' ),
		'show-faces'        => array( 'true', 'false' ),
		'width'             => '450',
	);
	/**
	 * Associative array with the default variables interpreted by fb
	 */
	private static $defaults = null;
	/**
	 * Associative array containing links for demos and documentation
	 */
	private static $links = array(
		'quote'    => array(
			'docs' => 'https://developers.facebook.com/docs/plugins/quote',
			'demo' => 'http://www.wpembedfb.com/demo-site/?p=99'
		),
		'save'     => array(
			'docs' => 'https://developers.facebook.com/docs/plugins/save',
			'demo' => 'http://www.wpembedfb.com/demo-site/?p=41'
		),
		'like'     => array(
			'docs' => 'https://developers.facebook.com/docs/plugins/like-button',
			'demo' => 'http://www.wpembedfb.com/demo-site/?p=26'
		),
		'share'    => array(
			'docs' => 'https://developers.facebook.com/docs/plugins/share-button/',
			'demo' => 'http://www.wpembedfb.com/demo-site/?p=36'
		),
		'send'     => array(
			'docs' => 'https://developers.facebook.com/docs/plugins/send-button',
			'demo' => 'http://www.wpembedfb.com/demo-site/?p=46'
		),
		'comment'  => array(
			'docs' => 'https://developers.facebook.com/docs/plugins/embedded-comments/',
			'demo' => 'http://www.wpembedfb.com/demo-site/?p=50'
		),
		'video'    => array(
			'docs' => 'https://developers.facebook.com/docs/plugins/embedded-video-player/',
			'demo' => 'http://www.wpembedfb.com/demo-site/?p=62'
		),
		'page'     => array(
			'docs' => 'https://developers.facebook.com/docs/plugins/page-plugin/',
			'demo' => 'http://www.wpembedfb.com/demo-site/?p=70'
		),
		'comments' => array(
			'docs' => 'https://developers.facebook.com/docs/plugins/comments/',
			'demo' => 'http://www.wpembedfb.com/demo-site/?p=105'
		),
		'post'     => array(
			'docs' => 'https://developers.facebook.com/docs/plugins/embedded-posts/',
			'demo' => 'http://www.wpembedfb.com/demo-site/?p=94'
		),
		'follow'   => array(
			'docs' => 'https://developers.facebook.com/docs/plugins/follow-button/',
			'demo' => 'http://www.wpembedfb.com/demo-site/?p=89'
		)
	);

	static function get_links( $type, $link = true ) {
		$ret = '';
		if ( isset( self::$links[ $type ] ) ) {
			if($link){
				$ret = '<br><small>';
				$ret .= '<a href="'.self::$links[ $type ]['demo'].'" target="_blank" title="WP Embed Facebook Demo">Demo</a> ';
				$ret .= '<a href="'.self::$links[ $type ]['docs'].'" target="_blank" title="Official FB documentation">Info</a> ';
				$ret .= '</small>';
			} else {
				$ret = self::$links[ $type ];
			}

		}

		return $ret;
	}

	static function get_defaults() {

		if ( self::$defaults === null ) {
			$vars = get_class_vars( __CLASS__ );
			unset($vars['defaults']);
			unset($vars['links']);
			foreach ( $vars as $type => $options ) {
				foreach ( $options as $option => $default ) {
					if ( is_array( $default ) ) {
						$vars[ $type ][ $option ] = $default[0];
					}
				}
			}
			self::$defaults = $vars;
		}

		return self::$defaults;
	}

	/**
	 * Gets the HTML code of any social plugin if
	 *
	 * @param string $type    = quote|save|like|share|send|comment|video|page|comments|post|follow
	 * @param array  $options Defaults are WEF_Social_Plugin::$type
	 *
	 * @see WEF_Social_Plugins::$quote
	 * @see WEF_Social_Plugins::$save
	 * @see WEF_Social_Plugins::$like
	 * @see WEF_Social_Plugins::$share
	 * @see WEF_Social_Plugins::$send
	 * @see WEF_Social_Plugins::$comment
	 * @see WEF_Social_Plugins::$video
	 * @see WEF_Social_Plugins::$page
	 * @see WEF_Social_Plugins::$comments
	 * @see WEF_Social_Plugins::$post
	 * @see WEF_Social_Plugins::$follow
	 *
	 * @return string
	 */
	static function get( $type = 'like', $options = array() ) {
		if ( $type == 'comment' ) {
			$type_clean = 'comment-embed';
		} elseif ( $type == 'comments_count' ) {
			$type_clean = 'comments-count';
		} elseif ( $type == 'share' ) {
			$type_clean = 'share-button';
		} else {
			$type_clean = $type;
		}
		$vars = self::get_defaults();

		$defaults    = $vars[ $type ];
		$options     = wp_parse_args( $options, $defaults );
		$extra       = '';
		$extra_array = array();
		foreach ( $options as $key => $value ) {
			if ( $type == 'comments' ) {

			}
			if ( $defaults[ $key ] != $value ) {
				$extra .= "data-$key=\"$value\" ";
			}
			$extra_array[ $key ] = $value;

		}

		return "<div class=\"fb-$type_clean\" $extra></div>";
	}

	static function shortcode( $atts = array() ) {
		$type     = array_shift( $atts );
		$type_raw = $type;
		if ( $type == 'comments-count' ) {
			$type = 'comments_count';
		}
		$defaults = self::get_defaults();
		if ( isset( $defaults[ $type ] ) ) {
			$atts_raw = $atts;
			$ret      = '';
			foreach ( WP_Embed_FB_Plugin::$link_types as $link_type ) {
				if ( isset( $defaults[ $type ][ $link_type ] ) && ( ! isset( $atts[ $link_type ] ) || empty( $atts[ $link_type ] ) ) ) {
					$atts[ $link_type ] = wp_get_shortlink( get_queried_object_id() );
				}
			}

			if ( ( WP_Embed_FB_Plugin::get_option( 'enq_when_needed' ) == 'true' ) && ( WP_Embed_FB_Plugin::get_option( 'enq_fbjs' ) == 'true' ) ) {
				wp_enqueue_script( 'wpemfb-fbjs' );
			}


			if ( isset( $defaults[ $type ]['width'] ) && $type != 'comments' ) {
				$default_width = $defaults[ $type ]['width'];
				if ( isset( $atts['adaptive'] ) ) {
					if ( $atts['adaptive'] == 'true' ) {
						$ret .= self::add_adaptive( $default_width, $atts );
					}
				} elseif ( WP_Embed_FB_Plugin::get_option( 'adaptive_fb_plugin' ) == 'true' ) {
					$ret .= self::add_adaptive( $default_width, $atts );
				}
			}

			$data = shortcode_atts( self::get_options( $type, $defaults ), $atts );

			$ret .= self::get( $type, $data );

			if ( isset( $atts['debug'] ) ) {
				$debug           = '';
				$atts_raw_string = '';
				unset( $atts_raw['debug'] );
				foreach ( $atts_raw as $key => $value ) {
					$atts_raw_string .= "$key=$value ";
				}
				$debug .= '<br><pre>';
				$debug .= '<strong>';
				$debug .= __( 'Shortcode used:', 'wp-embed-facebook' ) . "<br>";
				$debug .= '</strong>';
				$debug .= esc_html( htmlentities( "[fb_plugin $type_raw $atts_raw_string]" ) );
				$debug .= '<br>';
				$debug .= '<strong>';
				$debug .= __( 'Final code:', 'wp-embed-facebook' ) . "<br>";
				$debug .= '</strong>';
				$debug .= esc_html( htmlentities( $ret, ENT_QUOTES ) );
				$debug .= '</pre>';
				$ret .= $debug;
			}


//			return print_r($data,true);
			return $ret;
		}

		return __( 'Invalid FB Plugin type', 'wp-embed-facebook' );
	}

	static function the_content( $the_content ) {
		if ( WP_Embed_FB_Plugin::get_option( 'fb_root' ) === 'true' ) {
			$the_content = '<div id="fb-root"></div>' . PHP_EOL . $the_content;
		}
		if ( is_single() ) {
			$array = WP_Embed_FB_Plugin::string_to_array( WP_Embed_FB_Plugin::get_option( 'quote_post_types' ) );
			if ( in_array( $GLOBALS['post']->post_type, $array ) ) {
				$the_content .= WEF_Social_Plugins::get( 'quote' );
			}
		}

		return $the_content;
	}

	/* COMMENTS */

	static function comments_template( $template ) {
		$array = WP_Embed_FB_Plugin::string_to_array( WP_Embed_FB_Plugin::get_option( 'auto_comments_post_types' ) );
		if ( in_array( $GLOBALS['post']->post_type, $array ) ) {
			$template = WP_Embed_FB_Plugin::get_path() . 'templates/comments.php';
		}

		return $template;

	}

	static function get_comments_number( $number, $post_id ) {
		$count = get_post_meta( $post_id, '_wef_comment_count', true );
		if ( empty( $count ) ) {
			$count = 0;
		}

		return $count;
	}

	static function save_post( $post_id, $post, $update ) {
		if ( wp_is_post_revision( $post_id ) || ! $update ) {
			return;
		}
		$options = WP_Embed_FB_Plugin::get_option();
		$array   = WP_Embed_FB_Plugin::string_to_array( $options['auto_comments_post_types'] );
		//https://graph.facebook.com/?id=http://t-underboot.sigami.net/?p=4
		if ( in_array( $post->post_type, $array ) ) {
			$args     = array(
				'fields' => 'share{comment_count}',
				'id'     => wp_get_shortlink( $post_id )
			);
			$url      = "https://graph.facebook.com/{$options[ 'sdk_version' ]}/?" . http_build_query( $args );
			$request  = wp_remote_get( $url );
			$response = wp_remote_retrieve_body( $request );
			if ( ! is_wp_error( $request ) && ! empty( $response ) ) {
				$data = json_decode( $response, true );
//					print_r($data);die();
				if ( is_array( $data ) && isset( $data['share'] ) && isset( $data['share']['comment_count'] ) ) {
					update_post_meta( $post->ID, '_wef_comment_count', intval( $data['share']['comment_count'] ) );
				}

			}
		}
	}

	static function wpemfb_comments() {
		if ( isset( $_POST['response'] ) && isset( $_POST['response']['href'] ) ) {
			$post_id = url_to_postid( $_POST['response']['href'] );
			$count   = self::get_comments_number( '', $post_id );
			if ( isset( $_POST['response']['message'] ) ) {
				$count ++;
			} else {
				$count --;
			}
			update_post_meta( $post_id, '_wef_comment_count', intval( $count ) );
		}
		wp_die();
	}

	/**
	 * @param WP_Query $query
	 *
	 * @return WP_Query
	 */
	static function pre_get_posts( $query ) {
		if ( isset( $query->query_vars['orderby'] ) && $query->query_vars['orderby'] == 'comment_count' ) {
			$query->set(
				'meta_query',
				array(
					'relation' => 'OR',
					array(
						'key'     => '_wef_comment_count',
						'compare' => 'NOT EXISTS'
					),
					array(
						'key'     => '_wef_comment_count',
						'compare' => 'EXISTS'
					)
				)
			);
			$query->set( 'orderby', 'meta_value_num' );
		}

		return $query;
	}

	static function wp_head(){
		$app_id = WP_Embed_FB_Plugin::get_option('app_id');
		if(!empty($app_id)){
			echo '<meta property="fb:app_id" content="'.$app_id.'" />'.PHP_EOL;
		}
	}

	/* UTILITIES */

	private static function add_adaptive( $default_width, $atts ) {
		$width = isset( $atts['width'] ) ? $atts['width'] : $default_width;
		wp_enqueue_script( 'wpemfb' );
		$ret = '';
		$ret .= '<div class="wef-measure"';
		if ( ! empty( $width ) ) {
			$ret .= ' style="max-width: ' . $width . 'px;"';
		}
		$ret .= '></div>';

		return $ret;
	}

	private static function get_options( $type, $defaults ) {
		$options  = WP_Embed_FB_Plugin::get_option();
		$vars_opt = array();
		foreach ( $defaults[ $type ] as $key => $value ) {
			if ( ! in_array( $key, WP_Embed_FB_Plugin::$link_types ) ) {
				$vars_opt[ $key ] = $options["{$type}_$key"];
			} else {
				$vars_opt[ $key ] = '';
			}
		}

		return $vars_opt;
	}

	/* DEPRECATED FUNCTIONS TO BE REMOVED ON v2.2 */

	/**
	 * @param string $href
	 * @param        $options array colorscheme | share | layout | show_faces
	 *
	 * @return string
	 *
	 * @deprecated
	 *
	 * @see WEF_Social_Plugins::get()
	 */
	static function like_btn( $href, $options = array() ) {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__ . '()', '2.1.1', __CLASS__ . '::' . 'get(\'like\')' );

		return self::get( 'like', array( 'href' => $href ) + $options );
	}

	/**
	 * @param       $href
	 * @param array $options
	 *
	 * @return string
	 *
	 * @deprecated
	 * @see WEF_Social_Plugins::get()
	 */
	static function follow_btn( $href, $options = array() ) {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__ . '()', '2.1.1', __CLASS__ . '::' . 'get(\'follow\')' );

		return self::get( 'follow', array( 'href' => $href ) + $options );
	}

	/**
	 * @param string $href
	 * @param string $layout Can be one of "box_count", "button_count", "button", "link", "icon_link", or "icon".
	 *
	 * @return string
	 *
	 * @deprecated
	 * @see WEF_Social_Plugins::get()
	 */
	static function share_btn( $href, $layout = 'icon_link' ) {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__ . '()', '2.1.1', __CLASS__ . '::' . 'get(\'share\')' );

		return self::get( 'share', array( 'href' => $href, 'layout' => $layout ) );
	}

	/**
	 * @param string $href
	 * @param int    $width
	 * @param array  $options hide_cover,show_facepile,show_posts,small_header,height
	 *
	 * @return string
	 *
	 * @deprecated
	 * @see WEF_Social_Plugins::get()
	 */
	static function page_plugin( $href, $width, $options = array() ) {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__ . '()', '2.1.1', __CLASS__ . '::' . 'get(\'page\')' );

		return self::get( 'page', array( 'href' => $href, 'width' => $width ) + $options );
	}

	/**
	 * @param string $href
	 * @param int    $width
	 *
	 * @return string
	 *
	 * @deprecated
	 * @see WEF_Social_Plugins::get()
	 */
	static function embedded_post( $href, $width ) {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__ . '()', '2.1.1', __CLASS__ . '::' . 'get(\'post\')' );

		return self::get( 'post', array( 'href' => $href, 'width' => $width ) );
	}

	/**
	 * @param string $href
	 * @param int    $width
	 *
	 * @return string
	 *
	 * @deprecated
	 * @see WEF_Social_Plugins::get()
	 */
	static function embedded_video( $href, $width ) {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__ . '()', '2.1.1', __CLASS__ . '::' . 'get(\'video\')' );

		return self::get( 'video', array( 'href' => $href, 'width' => $width ) );
	}
}

WEF_Social_Plugins::get_defaults();
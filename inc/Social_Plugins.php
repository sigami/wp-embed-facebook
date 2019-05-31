<?php
/**
 * Group of static functions to render facebook social
 * plugins on WordPress it has no dependencies.
 *
 * @author  Miguel Sirvent & Rahul Aryan
 * @package WP Embed Facebook
 * @subpackage Classes
 */

namespace SIGAMI\WP_Embed_FB;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Social plugin class.
 *
 * @since unknown
 */
class Social_Plugins {

	/**
	 * @var array $link_types Link fields needed for rendering a social plugin
	 */
	static $link_types = [ 'href', 'uri' ];

	/**
	 * Quote Plugin
	 *
	 * The quote plugin lets people select text on your page and add it to their share, so they can
	 * tell a more expressive story.
	 *
	 * <code>
	 *
	 * href: The absolute URL of the page that will be quoted.
	 * layout:
	 *     quote: On text selection, a button with a blue Facebook icon and "Share Quote" text is
	 *     shown as an overlay. When a person clicks it, it will open a share dialog with the
	 *     highlighted text as a quote. button: Behaves the same as the "quote" option but has just
	 *     a blue Facebook icon in the button.
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/quote
	 */
	static $quote
		= [
			'href'   => '',
			'layout' => [ 'quote', 'button' ]
		];
	/**
	 * Save Button
	 *
	 * The save button lets people save items or services to a private list on Facebook, share it
	 * with friends, and receive relevant notifications. For example, a person can save an item of
	 * clothing, trip, or link that they're thinking about and go back to that list for future
	 * consumption, or get notified when that item or trip has a promotional deal.
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
	static $save
		= [
			'uri'  => '',
			'size' => [ 'large', 'small' ]
		];
	/**
	 * Like Button
	 *
	 * A single click on the Like button will 'like' pieces of content on the web and share them on
	 * Facebook. You can also display a Share button next to the Like button to let people add a
	 * personal message and customize who they share with.
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
	 * kid-directed-site: TIf your web site or online service, or a portion of your service, is
	 * directed to children under 13 you must enable this layout: Selects one of the different
	 * layouts that are available for the plugin. standard button_count button box_count ref: A
	 * label for tracking referrals which must be less than 50 characters and can contain
	 * alphanumeric characters and some punctuation (currently +/=-.:_). share: Specifies whether
	 * to include a share button beside the Like button. This only works with the XFBML version.
	 * show_faces: Specifies whether to display profile photos below the button (standard layout
	 * only). You must not enable this on child-directed sites. width: The width of the plugin
	 * (standard layout only), which is subject to the minimum and default width. default 450
	 * minimum 225
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/like-button
	 */
	static $like
		= [
			'href'              => '',
			'action'            => [ 'like', 'recommend' ],
			'colorscheme'       => [ 'light', 'dark' ],
			'kid-directed-site' => [ 'false', 'true' ],
			'layout'            => [ 'standard', 'button_count', 'button', 'box_count' ],
			'ref'               => '',
			'share'             => [ 'false', 'true' ],
			'show-faces'        => [ 'true', 'false' ],
			'size'              => [ 'small', 'large' ],
			'width'             => '450'
		];
	/**
	 * Share Button
	 *
	 * The Share button lets people add a personalized message to links before sharing on their
	 * timeline, in groups, or to their friends via a Facebook Message.
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
	 * mobile_iframe: If set to true, the share button will open the share dialog in an iframe
	 * (instead of a popup) on top of your website on mobile. This option is only available for
	 * mobile, not desktop. characters and some punctuation (currently +/=-.:_).
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/share-button/
	 */
	static $share
		= [
			'href'          => '',
			'layout'        => [
				'icon_link',
				'link',
				'icon',
				'button_count',
				'button',
				'box_count'
			],
			'mobile_iframe' => [ 'false', 'true' ],
		];
	/**
	 * Send Button
	 *
	 * The Send button lets people privately send content on your site to one or more friends in a
	 * Facebook message.
	 *
	 * <code>
	 *
	 * href: The absolute URL of the page that will be quoted.
	 * colorscheme: The color scheme used by the plugin.
	 *              light
	 *              dark
	 * kid-directed-site: TIf your web site or online service, or a portion of your service, is
	 * directed to children under 13 you must enable this ref: A label for tracking referrals which
	 * must be less than 50 characters and can contain alphanumeric characters and some punctuation
	 * (currently +/=-.:_).
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/send-button
	 */
	//static $send
	//	= [
	//		'href'              => '',
	//		'colorscheme'       => [ 'light', 'dark' ],
	//		'kid-directed-site' => [ 'false', 'true' ],
	//		'ref'               => '',
	//	];
	/**
	 * Group
	 *
	 * The Group Plugins lets people join your Facebook group from a link in an email message or a
	 * web page.
	 *
	 * @link https://developers.facebook.com/docs/plugins/group-plugin
	 */
	static $group
		= [
			'href'                => '',
			'show-social-context' => [ 'false', 'true' ],
			'show-metadata'       => [ 'false', 'true' ],
			'skin'                => [ 'light', 'dark' ],
		];
	/**
	 * Embedded Comments
	 *
	 * Embedded comments are a simple way to put public post comments - by a Page or a person on
	 * Facebook - into the content of your web site or web page. Only public comments from Facebook
	 * Pages and profiles can be embedded.
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
	static $comment
		= [
			'href'           => '',
			'width'          => '560',
			'include-parent' => [ 'false', 'true' ]
		];
	/**
	 * Embedded Video & Live Video Player
	 *
	 * With the embedded video player you can easily add Facebook videos and Facebook live videos
	 * to your website. You can use any public video post by a Page or a person as video or live
	 * video source.
	 *
	 * <code>
	 *
	 * href: The absolute URL of the page that will be quoted.
	 * allowfullscreen: Allow the video to be played in fullscreen mode.
	 * autoplay: Automatically start playing the video when the page loads. The video will be
	 * played without sound
	 *          (muted). People can turn on sound via the video player controls. This setting does
	 *          not apply to mobile devices. width: The width of the video container. Min. 220px.
	 *          show-text: Set to true to include the text from the Facebook post associated with
	 *          the video, if any. show-captions: Set to true to show captions (if available) by
	 *          default. Captions are only available on desktop.
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/embedded-video-player/
	 *
	 * @link https://developers.facebook.com/docs/plugins/embedded-video-player/#how-to-get-a-video-posts-url
	 */
	static $video
		= [
			'href'            => '',
			'allowfullscreen' => [ 'false', 'true' ],
			'autoplay'        => [ 'false', 'true' ],
			'width'           => '',
			'show-text'       => [ 'false', 'true' ],
			'show-captions'   => [ 'true', 'false' ],
		];
	/**
	 * Page Plugin
	 *
	 * The Page plugin lets you easily embed and promote any Facebook Page on your website. Just
	 * like on Facebook, your visitors can like and share the Page without leaving your site.
	 *
	 * <code>
	 *
	 * href: The absolute URL of the page that will be quoted.
	 * width: The pixel width of the plugin. Min. is 180 & Max. is 500
	 * height: The pixel height of the plugin. Min. is 70
	 * tabs: Tabs to render i.e. timeline, events, messages. Use a comma-separated list to add
	 * multiple tabs, i.e. timeline, events. hide_cover: Tabs to render i.e. timeline, events,
	 * messages. Use a comma-separated list to add multiple tabs, i.e. timeline, events.
	 * hide-cover: Hide cover photo in the header show-facepile: Show profile photos when friends
	 * like this hide-cta: Hide the custom call to action button (if available) small-header: Use
	 * the small header instead adapt-container-width: Try to fit inside the container width
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/page-plugin/
	 */
	static $page
		= [
			'href'                  => '',
			'width'                 => '340',
			'height'                => '500',
			'tabs'                  => '',
			'hide-cover'            => [ 'false', 'true' ],
			'show-facepile'         => [ 'true', 'false' ],
			'hide-cta'              => [ 'false', 'true' ],
			'small-header'          => [ 'false', 'true' ],
			'adapt-container-width' => [ 'true', 'false' ],
		];
	/**
	 * Comments Plugin
	 *
	 * The comments plugin lets people comment on content on your site using their Facebook
	 * account. People can choose to share their comment activity with their friends (and friends
	 * of their friends) on Facebook as well. The comments plugin also includes built-in moderation
	 * tools and social relevance ranking.
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
	 * width: The width of the comments plugin on the webpage. This can be either a pixel value or
	 * a percentage (such as 100%) for fluid width. The mobile version of the comments plugin
	 * ignores the width parameter and instead has a fluid width of 100%. The minimum width
	 * supported by the comments plugin is 320px.
	 *
	 * </code>
	 *
	 * @link https://developers.facebook.com/docs/plugins/comments/
	 */
	static $comments
		= [
			'href'        => '',
			'colorscheme' => [ 'light', 'dark' ],
			'mobile'      => [ 'false', 'true' ],
			'num_posts'   => '10',
			'order_by'    => [ 'social', 'reverse_time', 'time' ],
			'width'       => '550px',
		];
	/**
	 * Embedded Posts
	 *
	 * Embedded Posts are a simple way to put public posts - by a Page or a person on Facebook -
	 * into the content of your web site or web page. Only public posts from Facebook Pages and
	 * profiles can be embedded.
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
	static $post
		= [
			'href'      => '',
			'width'     => '500',
			'show-text' => [ 'true', 'false' ],
		];
	/**
	 * Associative array with the default variables interpreted by fb
	 */
	private static $defaults = null;

	/**
	 * Associative array containing links for demos and documentation.
	 *
	 * @param false|string $feature Feature slug.
	 * @return array|false
	 */
	private static function doc_links( $feature = false ) {
		$links = [
			'quote'    => [
				'docs' => 'plugins/quote',
				'demo' => 'social-plugins/quote',
			],
			'save'     => [
				'docs' => 'plugins/save',
				'demo' => 'social-plugins/save-button',
			],
			'like'     => [
				'docs' => 'plugins/like-button',
				'demo' => 'social-plugins/like-button',
			],
			'share'    => [
				'docs' => 'plugins/share-button',
				'demo' => 'social-plugins/share-button',
			],
			'send'     => [
				'docs' => 'plugins/send-button',
				'demo' => 'social-plugins/send-button',
			],
			'comment'  => [
				'docs' => 'plugins/embedded-comments',
				'demo' => 'social-plugins/single-comment',
			],
			'video'    => [
				'docs' => 'plugins/embedded-video-player',
				'demo' => 'social-plugins/embedded-video-live-video-player',
			],
			'page'     => [
				'docs' => 'plugins/page-plugin',
				'demo' => 'social-plugins/page-embed',
			],
			'comments' => [
				'docs' => 'plugins/comments',
				'demo' => 'social-plugins/comments-plugin',
			],
			'post'     => [
				'docs' => 'plugins/embedded-posts',
				'demo' => 'social-plugins/post-embed',
			],
			'group'     => [
				'docs' => 'plugins/group-plugin',
				'demo' => 'social-plugins/group',
			],
		];

		if ( false === $feature ) {
			return $links;
		}

		if ( ! isset( $links[ $feature ] ) ) {
			return false;
		}

		return $links[ $feature ];
	}

	static function get_links( $type, $link = true ) {
		$ret = '';

		$doc_link = self::doc_links( $type );

		if ( false !== $doc_link ) {
			if ( $link ) {
				$ret = '<small>';
				$ret .= '<a href="http://www.wpembedfb.com/' . user_trailingslashit( $doc_link['demo'] ) . '" target="_blank" title="WP Embed Facebook Demo">Demo</a> ';
				$ret .= '<a href="https://developers.facebook.com/docs/' . user_trailingslashit( $doc_link['docs'] ) . '" target="_blank" title="Official FB documentation">Info</a>';
				$ret .= '</small>';
			} else {
				$ret = $doc_link[ $type ];
			}
		}

		return $ret;
	}

	static function get_defaults( $all_options = false ) {

		if ( self::$defaults === null || $all_options ) {
			$vars = get_class_vars( __CLASS__ );
			unset( $vars['defaults'] );
			unset( $vars['links'] );
			unset( $vars['link_types'] );

			foreach ( $vars as $type => $options ) {

				foreach ( $options as $option => $default ) {
					if ( is_array( $default ) ) {
						$vars[ $type ][ $option ] = $all_options ? $default : $default[0];
					}
				}
			}
			if ( $all_options ) {
				return $vars;
			}

			self::$defaults = $vars;
		}

		return self::$defaults;
	}

	/**
	 * Gets the HTML code of any social plugin if any
	 *
	 * @see Social_Plugins::$quote
	 * @see Social_Plugins::$save
	 * @see Social_Plugins::$like
	 * @see Social_Plugins::$share
	 * @see Social_Plugins::$send
	 * @see Social_Plugins::$comment
	 * @see Social_Plugins::$video
	 * @see Social_Plugins::$page
	 * @see Social_Plugins::$comments
	 * @see Social_Plugins::$post
	 *
	 * @param string $type    = quote|save|like|share|send|comment|video|page|comments|post
	 * @param array  $options Defaults are Social_Plugins::$type
	 *
	 * @return string
	 */
	static function get( $type = 'like', $options = [] ) {
		if ( $type == 'comment' ) {
			$type_clean = 'comment-embed';
		} elseif ( $type == 'comments_count' ) {
			$type_clean = 'comments-count';
		} elseif ( $type == 'share' ) {
			$type_clean = 'share-button';
		} else {
			$type_clean = $type;
		}

		/**
		 * Before getting the HTML code of any social plugin.
		 *
		 * @since unknown
		 */
		do_action( 'wef_sp_get_action' );

		$defaults     = self::get_defaults();
		$settings     = Plugin::get_option();
		$real_options = $defaults[ $type ];
		foreach ( $defaults[ $type ] as $key => $def_value ) {
			if ( in_array( $key, Social_Plugins::$link_types ) ) {
				if ( empty( $options[ $key ] ) ) {
					$real_options[ $key ] = Helpers::get_true_url();
				} else {
					$real_options[ $key ] = $options[ $key ];
				}
			} else {
				if ( $settings["{$type}_$key"] != $def_value ) {
					$def_value = $settings["{$type}_$key"];
				}
				$real_options[ $key ] = isset( $options[ $key ] ) ? $options[ $key ] : $def_value;
			}
		}

		/**
		 * Filter defaults options of social plugin.
		 *
		 * @param string $default Defaults.
		 * @param string $type    Type of plugin.
		 * @since unknown
		 */
		$filtered_options = apply_filters( 'wef_sp_defaults', $real_options, $type );
		$extra            = '';
		foreach ( $filtered_options as $option => $value ) {
			$extra .= "data-$option=\"$value\" ";
		}
		$extra = trim( $extra );

		/**
		 * Filter social plugin HTML output.
		 *
		 * @param string $html     HTML of social plugin.
		 * @param string $type     Type.
		 * @param mixed  $options  Options.
		 * @param string $defaults Default options.
		 *
		 * @since unknown
		 */
		return apply_filters( 'wef_sp_get_filter', "<div class=\"fb-$type_clean\" $extra></div>",
			$type, $options, $defaults );
	}

	static function shortcode( $atts = [] ) {
		$type = array_shift( $atts );
		if ( $type == 'comments-count' ) {
			$type = 'comments_count';
		}

		$defaults = self::get_defaults();

		if ( isset( $defaults[ $type ] ) ) {

			if ( isset( $atts['help'] )
			     || isset( $atts['usage'] )
			     || ( isset( $atts[0] ) && ( ( $atts[0] == 'help' ) || ( $atts[0] == 'usage' ) ) )
			) {
				return self::usage( $type );
			}

			$ret = self::get( $type, $atts );

			if ( ( Plugin::get_option( 'enq_when_needed' ) == 'true' ) && ( Plugin::get_option( 'enq_fbjs' ) == 'true' ) ) {
				wp_enqueue_script( 'wpemfb-fbjs' );
			}

			/**
			 * Action triggered while generating shortcode content.
			 *
			 * @since unknown
			 */
			do_action( 'wef_sp_shortcode_action' );

			if ( isset( $defaults[ $type ]['width'] ) && $type != 'comments' && $type != 'page' ) {
				$default_width = $defaults[ $type ]['width'];
				if ( isset( $atts['adaptive'] ) ) {
					if ( $atts['adaptive'] == 'true' ) {
						$ret = self::add_adaptive( $default_width, $atts ) . $ret;
					}
				} elseif ( Plugin::get_option( 'adaptive_fb_plugin' ) == 'true' ) {
					$ret = self::add_adaptive( $default_width, $atts ) . $ret;
				}
			}

			if ( isset( $atts['debug'] ) ) {
				$ret .= self::debug( $ret, $atts, $type );
			}

			/**
			 * Filter social plugin shortcode output.
			 *
			 * @param string $ret      HTML output.
			 * @param string $type     Type.
			 * @param array  $atts     Attributes.
			 * @param string $defaults Defaults option.
			 *
			 * @since unknown
			 */
			return apply_filters( 'wef_sp_shortcode_filter', $ret, $type, $atts, $defaults );
		}

		ob_start();
		$types = array_keys( $defaults );
		echo '<p>' . __( 'Invalid Facebook plugin type use it like this:',
				'wp-embed-facebook' ) . '</p>';
		echo '<p>[fb_plugin ' . implode( '|', $types ) . ' help ]</p>';

		return ob_get_clean();
	}

	static function debug( $ret, $atts, $type ) {
		$atts_raw        = $atts;
		$debug           = '';
		$atts_raw_string = '';
		unset( $atts_raw['debug'] );
		foreach ( $atts_raw as $key => $value ) {
			$atts_raw_string .= "$key=\"$value\" ";
		}
		$debug .= '<br><pre>';
		$debug .= '<strong>';
		$debug .= __( 'Shortcode used:', 'wp-embed-facebook' ) . "<br>";
		$debug .= '</strong>';
		$debug .= esc_html( htmlentities( "[fb_plugin $type $atts_raw_string]" ) );
		$debug .= '<br>';
		$debug .= '<strong>';
		$debug .= __( 'Final code:', 'wp-embed-facebook' ) . "<br>";
		$debug .= '</strong>';
		$debug .= esc_html( htmlentities( $ret, ENT_QUOTES ) );
		$debug .= '<br>';
		$debug .= '<strong>';
		$debug .= __( 'More information:', 'wp-embed-facebook' );
		$debug .= '</strong>';
		$debug .= self::get_links( $type );
		$debug .= '</pre>';

		return $debug;
	}

	static function usage( $type ) {

		$all_defaults = self::get_defaults( true );

		$string = ' ';

		foreach ( $all_defaults[ $type ] as $att => $default ) {
			if ( is_array( $default ) ) {
				$default = implode( '|', $default );
			}
			$string .= "$att=\"$default\" ";
		}
		ob_start();
		echo sprintf( "<p>%s</p>", __( 'Shortcode usage example:', 'wp-embed-facebook' ) );
		echo sprintf( "<p>[fb_plugin %s%s adaptive=\"false|true\" ]</p>", $type, $string );
		echo sprintf( "<p>%s %s</p>", __( 'More information:', 'wp-embed-facebook' ), self::get_links( $type ) );

		return ob_get_clean();
	}

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

}
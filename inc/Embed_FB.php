<?php
/**
 * Shortcode helper.
 *
 * @package Magic Embeds
 */

namespace SIGAMI\WP_Embed_FB;

// If this file is called directly, abort.
use Exception;
use WP_Error;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Embed_FB {
	/**
	 * @var string|null Width of the current embed
	 */
	public static ?string $width = null;
	/**
	 * @var bool|null if the current embed is in raw format
	 */
	public static ?bool $raw = null;
	/**
	 * @var string|null Theme to use on the embed
	 */
	public static ?string $theme = null;
	/**
	 * @var int|null Number of posts on the page embed
	 */
	public static ?int $num_posts = null;
	/**
	 * @var int|null Number of photos on album
	 */
	public static ?int $num_photos = null;

	/* MAGIC HERE */

	/**
	 * Shortcode function
	 * [facebook='url' width='600' raw='true' social_plugin='true' posts='2'   ] width is optional
	 *
	 * @param array $attrs [0]=>url ['width']=>embed width ['raw']=>for videos and photos
	 *
	 * @return string
	 */
	public static function shortcode( $attrs ) {
		if ( ! is_array( $attrs ) ) {
			return '';
		}

		$compat = [ 'href', 'uri', 'src', 'url', 'link' ];

		foreach ( $compat as $com ) {
			if ( isset( $attrs[ $com ] ) ) {
				$attrs[0] = $attrs[ $com ];
				unset( $attrs[ $com ] );
			}
		}
		if ( ! empty( $attrs ) && isset( $attrs[0] ) ) {
			$clean_url = trim( $attrs[0], '=' );
			$clean_url = html_entity_decode( $clean_url );

			if ( is_numeric( $clean_url ) ) {
				$url_path  = $clean_url;
				$clean_url = "https://www.facebook.com/$url_path";
			} else {
				if ( strpos( $clean_url, 'facebook.com' ) === false ) {
					return '<p>' . esc_html__( 'This is not a valid facebook url', 'wp-embed-facebook' ) . " $clean_url </p>";
				}
				$url_path = str_replace(
					[
						'https:',
						'http:',
						'//facebook.com/',
						'//m.facebook.com/',
						'//www.facebook.com/',
					],
					'',
					$clean_url
				);
			}

			return self::fb_embed( [ 'https', '://www.facebook.com/', $url_path ], $clean_url, $attrs );
		}

		return wp_kses(
			sprintf( // translators: %s is a link to the examples page.
				__( 'You are using the [facebook] shortcode wrong. See examples <a title="Examples" target="_blank" href="%s" >here</a>.', 'wp-embed-facebook' ),
				'https://www.magicembeds.com'
			),
			Helpers::link_kses()
		);
	}

	/**
	 * Run rabbit
	 *
	 * @param      $url_parts
	 * @param null $url
	 * @param null $attrs
	 *
	 * @return mixed|null|string|string[]
	 */
	public static function fb_embed( $url_parts, $url = null, $attrs = null ) {
		$url_path = $url_parts[2];
		self::set_attrs( $attrs );

		/**
		 * Allows filtering facebook embed id and type.
		 *
		 * @param string|array $id_type  Type and id.
		 * @param string       $url_path The Juicy part ;).
		 * @param string       $url      Url.
		 *
		 * @since unknown
		 */
		$type_and_id = apply_filters( 'wpemfb_type_id', self::get_type_and_id( $url_path, $url ), $url_path, $url );

		if ( is_string( $type_and_id ) ) {
			return $type_and_id;
		}

		if ( Plugin::get_option( 'enq_when_needed' ) === 'true' ) {
			if ( 'album' === $type_and_id['type'] ) {
				if ( Plugin::get_option( 'enq_lightbox' ) === 'true' ) {
					wp_enqueue_script( 'wpemfb-lightbox' );
					wp_enqueue_style( 'wpemfb-lightbox' );
				}
			}
			if ( Plugin::get_option( 'enq_fbjs' ) === 'true' ) {
				wp_enqueue_script( 'wpemfb-fbjs' );
			}
		}
		if ( self::is_raw( $type_and_id['type'] ) ) {
			wp_enqueue_style( 'wpemfb-custom' );
			// Legacy support for custom embeds on
			wp_enqueue_style( 'wpemfb-' . self::get_theme() );
		}

		/**
		 * Action is triggered while generating embed code.
		 *
		 * @since unknown
		 */
		do_action( 'wp_embed_fb' );

		$return = self::print_embed( $type_and_id['fb_id'], $type_and_id['type'], $url_path );
		if ( is_wp_error( $return ) ) {
			$return = $return->get_error_message();
		}
		self::clear_attrs();

		return $return;
	}

	/**
	 * @param $fb_path
	 * @param $original
	 *
	 * @return array|string String with message on error and array of data on success.
	 */
	public static function get_type_and_id( $fb_path, $original ) {
		$has_fb_app   = Helpers::has_fb_app();
		$fbsdk        = FB_API::instance();
		$access_token = apply_filters( 'wef_access_token', $fbsdk->getAccessToken() );
		$fbsdk->setAccessToken( $access_token );
		$fb_id      = null;
		$type       = null;
		$q_mark_pos = strpos( $fb_path, '?' );
		if ( false !== $q_mark_pos ) {
			$vars = [];
			parse_str( wp_parse_url( $fb_path, PHP_URL_QUERY ), $vars );
			if ( isset( $vars['fbid'] ) ) {
				$fb_id = $vars['fbid'];
			}
			if ( isset( $vars['id'] ) ) {
				$fb_id = $vars['id'];
			}
			if ( isset( $vars['v'] ) ) {
				$fb_id = $vars['v'];
				$type  = 'video';
			}
			if ( isset( $vars['set'] ) ) {
				$set_array = explode( '.', $vars['set'] );
				$fb_id     = $set_array[1];
				$type      = 'album';
			}

			if ( isset( $vars['album_id'] ) ) {
				$fb_id = $vars['album_id'];
				$type  = 'album';
			}

			if ( isset( $vars['story_fbid'] ) ) {
				$fb_id = $vars['story_fbid'];
				$type  = 'post';
			}

			$fb_path = substr( $fb_path, 0, $q_mark_pos );
		}
		$fb_path_array = explode( '/', trim( $fb_path, '/' ) );
		if ( ! $fb_id ) {
			$fb_id       = end( $fb_path_array );
			$fb_id_array = explode( '-', $fb_id );
			if ( is_numeric( end( $fb_id_array ) ) ) {
				$fb_id = end( $fb_id_array );
			}
			$fb_id = str_replace( ':0', '', $fb_id );
		}
		if ( ! $type ) {
			if ( in_array( 'posts', $fb_path_array, true ) ) {
				$type = 'post';
				if ( $has_fb_app && ( self::is_raw( 'post' ) ) ) {
					try {
						$data  = $fbsdk->api( '/' . $fb_path_array[0] . '?fields=id' );
						$fb_id = $data['id'] . '_' . $fb_id;
					} catch ( Exception $e ) {
						$res = '<p><a href="' . $original . '" target="_blank" rel="nofollow">' . $original . '</a>';
						if ( is_super_admin() ) {
							if ( $e->getCode() === '803' ) {
								$res .= '<br><span style="color: #4a0e13">';
								$res .= esc_html__( 'Error: Try embedding this post as a social plugin (only visible to admins)', 'wp-embed-facebook' );
								$res .= '</span>';
							} else {
								$res .= '<br><span style="color: #4a0e13">' . __( 'Code' ) . ':&nbsp;' . $e->getCode() . '&nbsp;in type</span>';
								$res .= '<br><span style="color: #4a0e13">' . __( 'Error' ) . ':&nbsp;' . $e->getMessage() . ' (only visible to admins)</span>';
							}
						}
						$res .= '</p>';

						return $res;
					}
				}
			} elseif ( in_array( 'photos', $fb_path_array, true ) || in_array( 'photo.php', $fb_path_array, true ) ) {
				$type = 'photo';
			} elseif ( end( $fb_path_array ) === 'events' ) {
				$type = 'events';
			} elseif ( in_array( 'events', $fb_path_array, true ) ) {
				$type = 'event';
			} elseif ( in_array( 'videos', $fb_path_array, true ) || in_array( 'video.php', $fb_path_array, true ) ) {
				$type = 'video';
			}
		}

		/**
		 * Filter the embed type.
		 *
		 * @param string $type  the embed type.
		 * @param array  $clean url parts of the request.
		 *
		 * @since 1.8
		 */
		$type = apply_filters( 'wpemfb_embed_type', $type, $fb_path_array );

		if ( ! $type ) {
			if ( $has_fb_app ) {
				try {
					$metadata = $fbsdk->api( '/' . $fb_id . '?metadata=1' );
					$type     = $metadata['metadata']['type'];
				} catch ( Exception $e ) {
					$res = '<p><a href="https://www.facebook.com/' . $fb_path . '" target="_blank" rel="nofollow">https://www.facebook.com/' . $fb_path . '</a>';
					if ( is_super_admin() ) {
						$res .= '<br><span style="color: #4a0e13">' . __( 'Code' ) . ':&nbsp;' . $e->getCode() . '&nbsp;' . $type . '</span>';
						$res .= '<br><span style="color: #4a0e13">' . __( 'Error' ) . ':&nbsp;' . $e->getMessage() . ' (only visible to admins)</span>';
					}
					$res .= '</p>';

					return $res;
				}
			} else {
				$type = 'page';
			}
		}

		/**
		 * Filter the FB id.
		 *
		 * @param integer $fb_id       Facebook id.
		 * @param array   $fb_path_array Juice array.
		 *
		 * @since unknown
		 */
		$fb_id = apply_filters( 'wpemfb_embed_fb_id', $fb_id, $fb_path_array, $type );

		return [
			'type'  => $type,
			'fb_id' => $fb_id,
		];
	}

	public static function print_embed( $fb_id, $type, $fb_path ) {

		/**
		 * Short circuit `print_embed`. If `true` is returned.
		 *
		 * @param boolean $ret   Return.
		 * @param integer $fb_id Facebook id.
		 * @param string  $type  Type.
		 * @param string  $fb_path Juice.
		 */
		$interrupt = apply_filters( 'wef_interrupt', '', $fb_id, $type, $fb_path );

		if ( $interrupt ) {
			return $interrupt;
		}

		if ( ! self::is_raw( $type ) || 'video' === $type || 'group' === $type ) {
			$fb_data       = [
				'social_plugin' => true,
				'link'          => $fb_path,
				'type'          => $type,
			];
			$template_name = 'social-plugin';
		} else {
			switch ( $type ) {
				case 'user':
					$fb_data       = self::fb_api_get( $fb_id, $fb_path, 'profile' );
					$template_name = 'profile';
					break;
				case 'album':
				case 'post':
				case 'photo':
				case 'page':
				default:
					$fb_data       = self::fb_api_get( $fb_id, $fb_path, $type );
					$template_name = $type;
					break;
			}
		}

		if ( ! self::valid_fb_data( $fb_data ) ) {
			if ( is_string( $fb_data ) ) {
				return new WP_Error( 'api_error', $fb_data );
			}

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r -- Debug.
			return 'Invalid fb_data' . print_r( $fb_data, true );
		}

		// get default variables to use on templates
		$width = ! empty( self::$width ) ? self::$width : Plugin::get_option( 'max_width' );
		$theme = ! empty( self::$theme ) ? self::$theme : Plugin::get_option( 'theme' );

		ob_start();
		// show embed post on admin
		if ( is_admin() || wp_doing_ajax()
				 // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- X Theme compat.
				|| ( isset( $_GET['action'] ) && 'cs_render_element' === $_GET['action'] )
				 // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Divi Theme compat.
				|| isset( $_GET['et_fb'] ) ) :
			$src  = '//connect.facebook.net/' . Plugin::get_option( 'sdk_lang' ) . '/sdk.js#xfbml=1';
			$src .= '&version=v' . Plugin::get_option( 'sdk_version' );
			$src .= '&appId=' . Plugin::get_option( 'app_id' );
			$src .= '&autoLogAppEvents=1';
			?>
			<script>(function (d, s, id) {
					let js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) return;
					js = d.createElement(s);
					js.id = id;
					js.src = <?php echo esc_url_raw( $src ); ?>;
					fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
				FB.XFBML.parse();</script>
			<?php
		endif;

		/**
		 * Use this filter to remove `Magic Embeds` copyright comment from HTML.
		 * I could have hardcoded this but... I know you will leave it there :)
		 *
		 * @param string $text Copyright text.
		 *
		 * @since unknown
		 */
		echo '<!-- ' . esc_html( apply_filters( 'wef_embedded_with', 'Embedded with Magic Embeds - https://magicembeds.com' ) ) . ' -->';

		$template = self::locate_template( $template_name );

		/**
		 * Change the file to include on a certain embed.
		 *
		 * @param string $template file full path
		 * @param array  $fb_data  data from facebook
		 *
		 * @since 1.8
		 */
		$template = apply_filters( 'wpemfb_template', $template, $fb_data, $type );
		include $template;

		return preg_replace( '/^\s+|\n|\r|\s+$/m', '', ob_get_clean() );
	}

	/**
	 * Get data from fb using WP_Embed_FB::$fbsdk->api('/'.$fb_id) :)
	 *
	 * @param null|string    $fb_id   Facebook id
	 * @param string $fb_path Facebook url
	 * @param string $type    Type of embed
	 *
	 * @return array|string
	 */
	public static function fb_api_get( ?string $fb_id, string $fb_path, string $type = '' ) {
		if ( Helpers::has_fb_app() ) {

			/**
			 * Allow passing custom data before getting data from `WP_Embed_FB::$fbsdk->api('/'.$fb_id)`.
			 *
			 * @param array   $fb_data FB data.
			 * @param string  $type    Type.
			 * @param integer $fb_id   FB id.
			 * @param string  $fb_path url.
			 */
			$fb_data = apply_filters( 'wpemfb_custom_fb_data', [], $type, $fb_id, $fb_path );

			if ( empty( $fb_data ) ) {
				$fbsdk = FB_API::instance();
				try {
					switch ( $type ) {
						case 'album':
							self::$num_photos = is_numeric( self::$num_photos ) ? self::$num_photos : Plugin::get_option( 'max_photos' );
							$api_string       = $fb_id . '?fields=name,id,from,description,count,photos.fields(name,picture,source,id).limit(' . self::$num_photos . ')';
							break;
						case 'page':
							$num_posts  = is_numeric( self::$num_posts ) ? self::$num_posts : Plugin::get_option( 'max_posts' );
							$api_string = $fb_id . '?locale=' . Plugin::get_option( 'sdk_lang' ) . '&fields=name,picture,is_community_page,link,id,cover,category,website,genre,fan_count';
							if ( intval( $num_posts ) > 0 ) {
								$api_string .= ',posts.limit(' . $num_posts . '){id,full_picture,type,via,source,parent_id,call_to_action,story,place,child_attachments,icon,created_time,message,description,caption,name,shares,link,picture,object_id,likes.limit(1).summary(true),comments.limit(1).summary(true)}';
							}
							break;
						case 'photo':
							$api_string = $fb_id . '?fields=id,source,link,likes.limit(1).summary(true),comments.limit(1).summary(true)';
							break;
						case 'post':
							$api_string = $fb_id . '?locale=' . Plugin::get_option( 'sdk_lang' ) . '&fields=from{id,name,likes,link},id,full_picture,type,via,source,parent_id,call_to_action,story,place,child_attachments,icon,created_time,message,description,caption,name,shares,link,picture,object_id,likes.limit(1).summary(true),comments.limit(1).summary(true)';
							break;
						default:
							$api_string = $fb_id;
							break;
					}
					/**
					 * Filter the fist fbsdk query
					 *
					 * @param string $api_string The fb api request string according to type
					 * @param string $fb_id      The id of the object being requested.
					 * @param string $type       The detected type of embed
					 *
					 * @since 1.9
					 */
					$api_string = apply_filters( 'wpemfb_api_string', $api_string, $fb_id, $type );

					$fb_data = $fbsdk->api( Plugin::get_option( 'sdk_version' ) . '/' . $api_string );

					$api_string2 = '';

					/**
					 * Filter the second fbsdk query if necessary
					 *
					 * @param string $api_string2 The second request string empty if not necessary
					 * @param array  $fb_data     The result from the first query
					 * @param string $type        The detected type of embed
					 *
					 * @since 1.9
					 */
					$api_string2 = apply_filters( 'wpemfb_2nd_api_string', $api_string2, $fb_data, $type );

					if ( ! empty( $api_string2 ) ) {
						$extra_data = $fbsdk->api( Plugin::get_option( 'sdk_version' ) . '/' . $api_string2 );
						$fb_data    = array_merge( $fb_data, $extra_data );
					}
				} catch ( Exception $e ) {
					$fb_data = '<p><a href="https://www.facebook.com/' . $fb_path . '" target="_blank" rel="nofollow">https://www.facebook.com/' . $fb_path . '</a>';
					if ( is_super_admin() ) {
						$fb_data .= '<br><small style="color: #4a0e13">' . __( 'Error' ) . ':&nbsp;' . $e->getMessage() . ' (only visible to admins)</small>';
						if ( 'event' === $type || 'events' === $type ) {
							$fb_data .= '<br><small style="color: #114ac2">' . sprintf( // translators: %s is a link to the examples page.
								esc_html__( 'You can embed this resource with Extended Embeds Add-on visit %s to know more', 'wp-embed-facebook' ),
								'https://magicembeds.com'
							) . '</small>';
							$fb_data .= '</p>';
						}
					}
					$fb_data .= '</p>';

				}
			}
		} else {
			$fb_data = "<p><a href=\"https://www.facebook.com/$fb_path\" target=\"_blank\" rel=\"nofollow\">https://www.facebook.com/$fb_path</a>";
			if ( is_super_admin() ) {
				$fb_data .= '<br><span style="color: #4a0e13"><small>' . wp_kses(
					sprintf( // translators: %s is a link to the settings page.
						esc_html__( 'To embed this type of content you need to set up a facebook app on <a href="%s" title="Magic Embeds Settings">settings</a>', 'wp-embed-facebook' ),
						Admin::instance()->url
					),
					Helpers::link_kses()
				) . '</small></span>';
			}
			$fb_data .= '</p>';
		}

		/**
		 * Filter fb_data
		 *
		 * @param array  $fb_data the final result
		 * @param string $type    The detected type of embed
		 *
		 * @since 1.9
		 */
		return apply_filters( 'wpemfb_fb_data', $fb_data, $type );
	}

	public static function set_attrs( $attrs ) {
		if ( Helpers::has_photon() ) {
			add_filter( 'jetpack_photon_skip_image', '__return_false' );
		}
		if ( isset( $attrs['width'] ) ) {
			self::$width = $attrs['width'];
		}
		if ( isset( $attrs['raw'] ) ) {
			if ( 'true' === $attrs['raw'] ) {
				self::$raw = true;
			} else {
				self::$raw = false;
			}
		}

		if ( isset( $attrs['custom_embed'] ) ) {
			self::$raw = true;
		} elseif ( isset( $attrs['social_plugin'] ) ) {
			if ( 'true' === $attrs['social_plugin'] ) {
				self::$raw = false;
			} else {
				self::$raw = true;
			}
		}

		if ( isset( $attrs['theme'] ) ) {
			self::$theme = $attrs['theme'];
		}
		if ( isset( $attrs['posts'] ) ) {
			self::$num_posts = intval( $attrs['posts'] );
		}
		if ( isset( $attrs['photos'] ) ) {
			self::$num_photos = intval( $attrs['photos'] );
		}
	}

	public static function clear_attrs() {
		self::$width      = null;
		self::$raw        = null;
		self::$num_posts  = null;
		self::$theme      = null;
		self::$num_photos = null;
		if ( Helpers::has_photon() ) {
			add_filter( 'jetpack_photon_skip_image', '__return_true' );
		}
	}

	/* UTILITIES */

	public static function get_theme() {
		if ( ! self::$theme ) {
			self::$theme = Plugin::get_option( 'theme' );
		}

		return self::$theme;
	}

	public static function is_raw( $type ): ?bool {
		if ( null === self::$raw ) {
			switch ( $type ) {
				case 'page':
				case 'photo':
				case 'post':
					self::$raw = ! ( Plugin::get_option( 'raw_' . $type ) === 'false' );
					break;
				default:
					self::$raw = true;
					break;
			}
		}

		return self::$raw;
	}

	/**
	 * Locate the template inside plugin or theme
	 *
	 * @param string $template_name Template file name
	 *
	 * @return string Template location
	 */
	public static function locate_template( string $template_name ): string {
		$theme   = self::get_theme();
		$located = locate_template(
			[
				'plugins/wp-embed-facebook/custom-embeds/' . $template_name . '.php',
				'plugins/wp-embed-facebook/' . $theme . '/' . $template_name . '.php',
			]
		);
		if ( empty( $located ) ) {
			$located = Plugin::path() . 'templates/custom-embeds/' . $template_name . '.php';

		}

		return $located;
	}

	public static function valid_fb_data( $fb_data ): bool {
		if ( is_array( $fb_data ) && ( isset( $fb_data['id'] ) || isset( $fb_data['social_plugin'] ) || isset( $fb_data['data'] ) ) ) {
			return true;
		}

		return false;
	}
}

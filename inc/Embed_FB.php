<?php
/**
 * Shortcode helper.
 *
 * @package WP Embed Facebook
 */

namespace SIGAMI\WP_Embed_FB;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Embed_FB {
	/**
	 * @var string Width of the current embed
	 */
	static $width = null;
	/**
	 * @var bool|null if the current embed is in raw format
	 */
	static $raw = null;
	/**
	 * @var string Theme to use on the embed
	 */
	static $theme = null;
	/**
	 * @var int|null Number of posts on the page embed
	 */
	static $num_posts = null;
	/**
	 * @var int|null Number of photos on album
	 */
	static $num_photos = null;

	/* MAGIC HERE */

	/**
	 * Shortcode function
	 * [facebook='url' width='600' raw='true' social_plugin='true' posts='2'   ] width is optional
	 *
	 * @param array $atts [0]=>url ['width']=>embed width ['raw']=>for videos and photos
	 *
	 * @return string
	 */
	static function shortcode( $atts ) {

		$compat = [ 'href', 'uri', 'src', 'url', 'link' ];
		foreach ( $compat as $com ) {
			if ( isset( $atts[ $com ] ) ) {
				$atts[0] = $atts[ $com ];
				unset( $atts[ $com ] );
			}
		}
		if ( ! empty( $atts ) && isset( $atts[0] ) ) {
			$clean = trim( $atts[0], '=' );
			$clean = html_entity_decode( $clean );

			if ( is_numeric( $clean ) ) {
				$juice = $clean;
				$clean = "https://www.facebook.com/$juice";
			} else {
				if ( strpos( $clean, 'facebook.com' ) === false ) {
					return "<p>" . __( "This is not a valid facebook url", "wp-embed-facebook" ) . " $clean </p>";
				}
				$juice = str_replace( [
					'https:',
					'http:',
					'//facebook.com/',
					'//m.facebook.com/',
					'//www.facebook.com/'
				], '', $clean );
			}
			$embed = self::fb_embed( [ 'https', '://www.facebook.com/', $juice ], $clean, $atts );

			return $embed;
		}

		return sprintf( __( /** @lang text */
			'You are using the [facebook] shortcode wrong. See examples <a title="Examples" target="_blank" href="%s" >here</a>.',
			'wp-embed-facebook' ), 'http://www.wpembedfb.com' );
	}

	/**
	 * Run rabbit
	 *
	 * @param      $match
	 * @param null $url
	 * @param null $atts
	 *
	 * @return mixed|null|string|string[]
	 */
	static function fb_embed( $match, $url = null, $atts = null ) {
		$juice = $match[2];
		self::set_atts( $atts );

		/**
		 * Allows filtering facebook embed id and type.
		 *
		 * @param string|array $id_type Type and id.
		 * @param string       $juice   Juice.
		 * @param string       $url     Url.
		 *
		 * @since unknown
		 */
		$type_and_id = apply_filters( 'wpemfb_type_id', self::get_type_and_id( $juice, $url ), $juice, $url );

		if ( is_string( $type_and_id ) ) {
			return $type_and_id;
		}

		if ( Plugin::get_option( 'enq_when_needed' ) == 'true' ) {
			if ( $type_and_id['type'] == 'album' ) {
				if ( Plugin::get_option( 'enq_lightbox' ) == 'true' ) {
					wp_enqueue_script( 'wpemfb-lightbox' );
					wp_enqueue_style( 'wpemfb-lightbox' );
				}
			}
			if ( Plugin::get_option( 'enq_fbjs' ) == 'true' ) {
				wp_enqueue_script( 'wpemfb-fbjs' );
			}
		}
		if ( self::is_raw( $type_and_id['type'] ) ) {
			wp_enqueue_style( 'wpemfb-custom' );
			//Legacy support for custom embeds on
			wp_enqueue_style( 'wpemfb-' . self::get_theme() );
		}

		/**
		 * Action is triggered while generating embed code.
		 *
		 * @since unknown
		 */
		do_action( 'wp_embed_fb' );

		$return = self::print_embed( $type_and_id['fb_id'], $type_and_id['type'], $juice );
		if(is_wp_error($return)){
		    $return = $return->get_error_message();
        }
		self::clear_atts();

		return $return;
	}

	/**
	 * @param $juice
	 * @param $original
	 *
	 * @return array|string
	 */
	static function get_type_and_id( $juice, $original ) {
		$has_fb_app   = Helpers::has_fb_app();
		$fbsdk        = FB_API::instance();
		$access_token = apply_filters( 'wef_access_token', $fbsdk->getAccessToken() );
		$fbsdk->setAccessToken( $access_token );
		$fb_id = null;
		$type  = null;
		if ( ( $pos = strpos( $juice, "?" ) ) !== false ) {
			$vars = [];
			parse_str( parse_url( $juice, PHP_URL_QUERY ), $vars );
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
				$setArray = explode( '.', $vars['set'] );
				$fb_id    = $setArray[1];
				$type     = 'album';
			}

			if ( isset( $vars['album_id'] ) ) {
				$fb_id = $vars['album_id'];
				$type  = 'album';
			}

			if ( isset( $vars['story_fbid'] ) ) {
				$fb_id = $vars['story_fbid'];
				$type  = 'post';
			}

			$juice = substr( $juice, 0, $pos );
		}
		$juiceArray = explode( '/', trim( $juice, '/' ) );
		if ( ! $fb_id ) {
			$fb_id       = end( $juiceArray );
			$fb_id_array = explode( '-', $fb_id );
			if ( is_numeric( end( $fb_id_array ) ) ) {
				$fb_id = end( $fb_id_array );
			}
			$fb_id = str_replace( ':0', '', $fb_id );
		}
		if ( ! $type ) {
			if ( in_array( 'posts', $juiceArray ) ) {
				$type = 'post';
				if ( $has_fb_app && ( self::is_raw( 'post' ) ) ) {
					try {
						/** @noinspection PhpUndefinedVariableInspection */
						$data  = $fbsdk->api( '/' . $juiceArray[0] . '?fields=id' );
						$fb_id = $data['id'] . '_' . $fb_id;
					} catch ( \Exception $e ) {
						$res = '<p><a href="' . $original . '" target="_blank" rel="nofollow">' . $original . '</a>';
						if ( is_super_admin() ) {
							if ( $e->getCode() == '803' ) {
								$res .= '<br><span style="color: #4a0e13">'
								        . __( 'Error: Try embedding this post as a social plugin (only visible to admins)',
										'wp-embed-facebook' ) . '</span>';
							} else {
								$res .= '<br><span style="color: #4a0e13">' . __( 'Code' ) . ':&nbsp;' . $e->getCode()
								        . '&nbsp;in type</span>';
								$res .= '<br><span style="color: #4a0e13">' . __( 'Error' ) . ':&nbsp;'
								        . $e->getMessage() . ' (only visible to admins)</span>';
							}
						}
						$res .= '</p>';

						return $res;
					}
				}
			} elseif ( in_array( 'photos', $juiceArray ) || in_array( 'photo.php', $juiceArray ) ) {
				$type = 'photo';
			} elseif ( end( $juiceArray ) == 'events' ) {
				$type = 'events';
			} elseif ( in_array( 'events', $juiceArray ) ) {
				$type = 'event';
			} elseif ( in_array( 'videos', $juiceArray ) || in_array( 'video.php', $juiceArray ) ) {
				$type = 'video';
			}
		}

		/**
		 * Filter the embed type.
		 *
		 * @since 1.8
		 *
		 * @param string $type  the embed type.
		 * @param array  $clean url parts of the request.
		 */
		$type = apply_filters( 'wpemfb_embed_type', $type, $juiceArray );

		if ( ! $type ) {
			if ( $has_fb_app ) {
				try {
					/** @noinspection PhpUndefinedVariableInspection */
					$metadata = $fbsdk->api( '/' . $fb_id . '?metadata=1' );
					$type     = $metadata['metadata']['type'];
				} catch ( \Exception $e ) {
					$res = '<p><a href="https://www.facebook.com/' . $juice
					       . '" target="_blank" rel="nofollow">https://www.facebook.com/' . $juice . '</a>';
					if ( is_super_admin() ) {
						$res .= '<br><span style="color: #4a0e13">' . __( 'Code' ) . ':&nbsp;' . $e->getCode()
						        . '&nbsp;' . $type . '</span>';
						$res .= '<br><span style="color: #4a0e13">' . __( 'Error' ) . ':&nbsp;' . $e->getMessage()
						        . ' (only visible to admins)</span>';
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
		 * @param integer $fb_id      Facebook id.
		 * @param array   $juiceArray Juice array.
		 *
		 * @since unknown
		 */
		$fb_id = apply_filters( 'wpemfb_embed_fb_id', $fb_id, $juiceArray, $type );

		return [ 'type' => $type, 'fb_id' => $fb_id ];
	}

	static function print_embed( $fb_id, $type, $juice ) {

		/**
		 * Short circuit `print_embed`. If `true` is returned.
		 *
		 * @param boolean $ret   Return.
		 * @param integer $fb_id Facebook id.
		 * @param string  $type  Type.
		 * @param string  $juice Juice.
		 *
		 * @since unknwon
		 */
		$interrupt = apply_filters( 'wef_interrupt', '', $fb_id, $type, $juice );

		if ( $interrupt ) {
			return $interrupt;
		}

		if ( ! self::is_raw( $type ) || $type == 'video' || $type == 'group' ) {
			$fb_data       = [ 'social_plugin' => true, 'link' => $juice, 'type' => $type ];
			$template_name = 'social-plugin';
		} else {
			switch ( $type ) {
				case 'page' :
				case 'photo' :
				case 'post':
				case 'album' :
					$fb_data       = self::fb_api_get( $fb_id, $juice, $type );
					$template_name = $type;
					break;
				case 'user' :
					$fb_data       = self::fb_api_get( $fb_id, $juice, 'profile' );
					$template_name = 'profile';
					break;
				default :
					$fb_data       = self::fb_api_get( $fb_id, $juice, $type );
					$template_name = $type;
					break;
			}
		}

		if ( ! self::valid_fb_data( $fb_data ) ) {
			if ( is_string( $fb_data ) ) {
				return new \WP_Error('api_error',$fb_data);
			}

			return print_r( $fb_data, true );
		}

		//get default variables to use on templates
		/** @noinspection PhpUnusedLocalVariableInspection */
		$width = ! empty( self::$width ) ? self::$width : Plugin::get_option( 'max_width' );
		$theme = ! empty( self::$theme ) ? self::$theme : Plugin::get_option( 'theme' );
		/** @noinspection PhpUnusedLocalVariableInspection */
		ob_start();
		//show embed post on admin
		if ( is_admin()
		     || wp_doing_ajax()
		     || ( isset( $_GET['action'] ) && $_GET['action'] == 'cs_render_element' )//X Theme compat
		     || isset( $_GET['et_fb'] ) //Divi builder compatibility. The most awesome builder BTW
		) : ?>
            <script>(function (d, s, id) {
                let js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.src = "//connect.facebook.net/<?php echo Plugin::get_option( 'sdk_lang' ); ?>/sdk.js#xfbml=1&version=<?php echo Plugin::get_option( 'sdk_version' ) ?>";
                fjs.parentNode.insertBefore(js, fjs);
              }(document, 'script', 'facebook-jssdk'));
              FB.XFBML.parse();</script>
		<?php endif;

		/**
		 * Use this filter to remove `WP Embed Facebook` copyright comment from HTML.
		 *
		 * I could have hardcoded this but... I know you will leave it there :)
		 *
		 * @param string $text Copyright text.
		 *
		 * @since unknown
		 */
		echo apply_filters( 'wef_embedded_with', '<!-- Embedded with WP Embed Facebook - http://wpembedfb.com -->' );

		$template = self::locate_template( $template_name );

		/**
		 * Change the file to include on a certain embed.
		 *
		 * @since 1.8
		 *
		 * @param string $template file full path
		 * @param array  $fb_data  data from facebook
		 */
		$template = apply_filters( 'wpemfb_template', $template, $fb_data, $type );
		/** @noinspection PhpIncludeInspection */
		include( $template );

		return preg_replace( '/^\s+|\n|\r|\s+$/m', '', ob_get_clean() );
	}

	/**
	 * get data from fb using WP_Embed_FB::$fbsdk->api('/'.$fb_id) :)
	 *
	 * @param int    $fb_id Facebook id
	 * @param string $url   Facebook url
	 *
	 * @type string type of embed
	 * @return array|string
	 */
	static function fb_api_get( $fb_id, $url, $type = "" ) {
		if ( Helpers::has_fb_app() ) {

			/**
			 * Allow passing custom data before getting data from `WP_Embed_FB::$fbsdk->api('/'.$fb_id)`.
			 *
			 * @param array   $fb_data FB data.
			 * @param string  $type    Type.
			 * @param integer $fb_id   FB id.
			 * @param string  $url     url.
			 */
			$fb_data = apply_filters( 'wpemfb_custom_fb_data', [], $type, $fb_id, $url );

			if ( empty( $fb_data ) ) {
				$fbsdk = FB_API::instance();
				try {
					switch ( $type ) {
						case 'album' :
							self::$num_photos = is_numeric( self::$num_photos ) ? self::$num_photos
								: Plugin::get_option( 'max_photos' );
							$api_string       = $fb_id
							                    . '?fields=name,id,from,description,count,photos.fields(name,picture,source,id).limit('
							                    . self::$num_photos . ')';
							break;
						case 'page' :
							$num_posts  = is_numeric( self::$num_posts ) ? self::$num_posts
								: Plugin::get_option( 'max_posts' );
							$api_string = $fb_id . '?locale=' . Plugin::get_option( 'sdk_lang' )
							              . '&fields=name,picture,is_community_page,link,id,cover,category,website,genre,fan_count';
							if ( intval( $num_posts ) > 0 ) {
								$api_string .= ',posts.limit(' . $num_posts
								               . '){id,full_picture,type,via,source,parent_id,call_to_action,story,place,child_attachments,icon,created_time,message,description,caption,name,shares,link,picture,object_id,likes.limit(1).summary(true),comments.limit(1).summary(true)}';
							}
							break;
						case 'photo' :
							$api_string = $fb_id
							              . '?fields=id,source,link,likes.limit(1).summary(true),comments.limit(1).summary(true)';
							break;
						case 'post' :
							$api_string = $fb_id . '?locale=' . Plugin::get_option( 'sdk_lang' )
							              . '&fields=from{id,name,likes,link},id,full_picture,type,via,source,parent_id,call_to_action,story,place,child_attachments,icon,created_time,message,description,caption,name,shares,link,picture,object_id,likes.limit(1).summary(true),comments.limit(1).summary(true)';
							break;
						default :
							$api_string = $fb_id;
							break;
					}
					/**
					 * Filter the fist fbsdk query
					 *
					 * @since 1.9
					 *
					 * @param string $api_string The fb api request string according to type
					 * @param string $fb_id      The id of the object being requested.
					 * @param string $type       The detected type of embed
					 *
					 */
					$api_string = apply_filters( 'wpemfb_api_string', $api_string, $fb_id, $type );

					$fb_data = $fbsdk->api( Plugin::get_option( 'sdk_version' ) . '/' . $api_string );

					$api_string2 = '';

					/**
					 * Filter the second fbsdk query if necessary
					 *
					 * @since 1.9
					 *
					 * @param string $api_string2 The second request string empty if not necessary
					 * @param array  $fb_data     The result from the first query
					 * @param string $type        The detected type of embed
					 *
					 */
					$api_string2 = apply_filters( 'wpemfb_2nd_api_string', $api_string2, $fb_data, $type );

					if ( ! empty( $api_string2 ) ) {
						$extra_data = $fbsdk->api( Plugin::get_option( 'sdk_version' ) . '/' . $api_string2 );
						$fb_data    = array_merge( $fb_data, $extra_data );
					}
				} catch ( \Exception $e ) {

					$fb_data = '<p><a href="https://www.facebook.com/' . $url
					           . '" target="_blank" rel="nofollow">https://www.facebook.com/' . $url . '</a>';
					if ( is_super_admin() ) {
						$fb_data .= '<br><small style="color: #4a0e13">' . __( 'Error' ) . ':&nbsp;' . $e->getMessage()
						            . ' (only visible to admins)</small>';
						if ( $type == 'event' || $type == 'events' ) {
							$fb_data .= '<br><small style="color: #114ac2">'
							            . sprintf( __( 'You can embed this resource with Extended Embeds Add-on visit %s to know more',
									'wp-embed-facebook' ), 'https://wpembedfb.com' ) . '</small>';
							$fb_data .= '</p>';
						}
					}
					$fb_data .= '</p>';

				}
			}
		} else {
			$fb_data = '<p><a href="https://www.facebook.com/' . $url
			           . '" target="_blank" rel="nofollow">https://www.facebook.com/' . $url . '</a>';
			if ( is_super_admin() ) {
				$fb_data .= '<br><span style="color: #4a0e13">' . sprintf( __( /** @lang text */
						'<small>To embed this type of content you need to setup a facebook app on <a href="%s" title="WP Embed Facebook Settings">settings</a></small>',
						'wp-embed-facebook' ), Admin::$url ) . '</span>';
			}
			$fb_data .= '</p>';
		}

		/**
		 * Filter fb_data
		 *
		 * @since 1.9
		 *
		 * @param array  $fb_data the final result
		 * @param string $type    The detected type of embed
		 */
		$fb_data = apply_filters( 'wpemfb_fb_data', $fb_data, $type );

		return $fb_data;
	}

	static function set_atts( $atts ) {
		if ( Helpers::has_photon() ) {
			add_filter( 'jetpack_photon_skip_image', '__return_false' );
		}
		if ( isset( $atts['width'] ) ) {
			self::$width = $atts['width'];
		}
		if ( isset( $atts['raw'] ) ) {
			if ( $atts['raw'] == 'true' ) {
				self::$raw = true;
			} else {
				self::$raw = false;
			}
		}

		if ( isset( $atts['custom_embed'] ) ) {
			self::$raw = true;
		} elseif ( isset( $atts['social_plugin'] ) ) {
			if ( $atts['social_plugin'] == 'true' ) {
				self::$raw = false;
			} else {
				self::$raw = true;
			}
		}

		if ( isset( $atts['theme'] ) ) {
			self::$theme = $atts['theme'];
		}
		if ( isset( $atts['posts'] ) ) {
			self::$num_posts = intval( $atts['posts'] );
		}
		if ( isset( $atts['photos'] ) ) {
			self::$num_photos = intval( $atts['photos'] );
		}
	}

	static function clear_atts() {
		self::$width = self::$raw = self::$num_posts = self::$theme = self::$num_photos = null;
		if ( Helpers::has_photon() ) {
			add_filter( 'jetpack_photon_skip_image', '__return_true' );
		}
	}

	/* UTILITIES */

	static function get_theme() {
		if ( self::$theme ) {
			return self::$theme;
		} else {
			self::$theme = Plugin::get_option( 'theme' );

			return self::$theme;
		}
	}

	static function is_raw( $type ) {
		if ( self::$raw !== null ) {
			return self::$raw;
		} else {
			switch ( $type ) {
				case 'page':
				case 'photo':
				case 'post':
					self::$raw = ( Plugin::get_option( 'raw_' . $type ) === 'false' ) ? false : true;
					break;
				default:
					self::$raw = true;
					break;
			}

			return self::$raw;
		}
	}

	/**
	 * Locate the template inside plugin or theme
	 *
	 * @param string $template_name Template file name
	 *
	 * @return string Template location
	 */
	static function locate_template( $template_name ) {
		$theme   = self::get_theme();
		$located = locate_template( [
			'plugins/wp-embed-facebook/custom-embeds/' . $template_name . '.php',
			'plugins/wp-embed-facebook/' . $theme . '/' . $template_name . '.php',
		] );
		if ( empty( $located ) ) {
			$located = Plugin::path() . 'templates/custom-embeds/' . $template_name . '.php';;
		}

		return $located;
	}

	static function valid_fb_data( $fb_data ) {
		if ( is_array( $fb_data )
		     && ( isset( $fb_data['id'] ) || isset( $fb_data['social_plugin'] )
		          || isset( $fb_data['data'] ) ) ) {
			return true;
		}

		return false;
	}
}
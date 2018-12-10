<?php
/**
 * Plugin Helpers.
 *
 * @package WP Embed Facebook
 */

namespace SIGAMI\WP_Embed_FB;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Helpers {

	static $has_photon  = null;
	static $wp_timezone = null;
	static $lb_defaults = null;

	static function string_to_array( $string ) {
		return explode( ',', trim( $string, ' ,' ) );
	}

	static function has_photon() {
		if ( self::$has_photon === null ) {
			if ( class_exists( 'Jetpack' )
			     && method_exists( 'Jetpack', 'get_active_modules' )
			     && in_array( 'photon', Jetpack::get_active_modules() ) ) {
				self::$has_photon = true;
			} else {
				self::$has_photon = false;
			}
		}

		return self::$has_photon;
	}

	static function has_fb_app() {
		$options = Plugin::get_option();
		$has_app = true;
		if ( empty( $options['app_secret'] ) || empty( $options['app_id'] ) ) {
			$has_app = false;
		}

		return apply_filters('wef_has_fb_app',$has_app);
	}

	static function get_timezone() {
		if ( self::$wp_timezone === null ) {
			$tzstring = get_option( 'timezone_string', '' );
			if ( empty( $tzstring ) ) {
				$current_offset = get_option( 'gmt_offset', 0 );
				if ( 0 == $current_offset ) {
					$tzstring = 'Etc/GMT';
				} else {
					$tzstring = ( $current_offset < 0 ) ? 'Etc/GMT' . $current_offset
						: 'Etc/GMT+' . $current_offset;
				}
			}
			self::$wp_timezone = $tzstring;
		}

		return self::$wp_timezone;
	}

	static function get_true_url() {
		global $wp;
		if ( is_home() ) {
			return home_url();
		}

		if ( in_the_loop() ) {
			global $post;
			if ( Plugin::get_option( 'permalink_on_social_plugins' ) === 'true' ) {
				return get_permalink( $post->ID );
			} else {
				$query = '/?p=' . $post->ID;
			}
		} else {
			if ( Plugin::get_option( 'permalink_on_social_plugins' ) === 'true' ) {
				$query = $wp->request;
			} else {
				$query = '/?' . $wp->query_string;
			}
		}

		return home_url( $query );
	}

	static function lightbox_title( $title ) {
		$clean_title = esc_attr( wp_rel_nofollow( make_clickable( str_replace( [ '"', "'" ], [
			'&#34;',
			'&#39;'
		], $title ) ) ) );

		/**
		 * Filter lightbox title.
		 *
		 * @param string $clean_title Sanitized title with `data-title` attribute.
		 * @param string $title       Raw title.
		 *
		 * @since unknown
		 */
		return apply_filters( 'wef_lightbox_title', 'data-title="' . $clean_title . '"', $title );
	}

	static function get_lb_defaults() {
		if ( self::$lb_defaults === null ) {
			$keys              = [
				'albumLabel',
				'alwaysShowNavOnTouchDevices',
				'showImageNumberLabel',
				'wrapAround',
				'disableScrolling',
				'fitImagesInViewport',
				'maxWidth',
				'maxHeight',
				'positionFromTop',
				'resizeDuration',
				'fadeDuration',
				'wpGallery'
			];
			self::$lb_defaults = [];
			$defaults          = Plugin::defaults();
			foreach ( $keys as $key ) {
				self::$lb_defaults[ $key ] = $defaults[ 'LB_' . $key ];
			}
		}

		return self::$lb_defaults;
	}

	static function make_clickable( $text ) {
		if ( empty( $text ) ) {
			return $text;
		}

		return wpautop( self::rel_nofollow( make_clickable( $text ) ) );
	}

	static function rel_nofollow( $text ) {
		$text = stripslashes( $text );

		return preg_replace_callback( /** @lang text */
			'|<a (.+?)>|i', [ __CLASS__, 'nofollow_callback' ], $text );
	}

	static function nofollow_callback( $matches ) {
		$text = $matches[1];
		$text = str_replace( [ ' rel="nofollow"', " rel='nofollow'" ], '', $text );

		return "<a $text rel=\"nofollow\">";
	}

	static function get_api_versions() {
		return [
			//'v2.6'  => '2.6',
			//'v2.7'  => '2.7',
			//'v2.8'  => '2.8',
			'v2.9'  => '2.9',
			'v2.10' => '2.10',
			'v2.11' => '2.11',
			'v2.12' => '2.12',
			'v3.0'  => '3.0',
			'v3.1'  => '3.1',
			'v3.2'  => '3.2',
		];
	}

	static function get_fb_locales() {
		return [

			'af_ZA' => 'Afrikaans',
			'ar_AR' => 'Arabic',
			'ar_IN' => 'Assamese',
			'az_AZ' => 'Azerbaijani',
			'be_BY' => 'Belarusian',
			'bg_BG' => 'Bulgarian',
			'bn_IN' => 'Bengali',
			'br_FR' => 'Breton',
			'bs_BA' => 'Bosnian',
			'ca_ES' => 'Catalan',
			'cb_IQ' => 'Sorani Kurdish',
			'co_FR' => 'Corsican',
			'cs_CZ' => 'Czech',
			'cx_PH' => 'Cebuano',
			'cy_GB' => 'Welsh',
			'da_DK' => 'Danish',
			'de_DE' => 'German',
			'el_GR' => 'Greek',
			'en_GB' => 'English (UK)',
			'en_UD' => 'English (Upside Down)',
			'en_US' => 'English (US)',
			'es_ES' => 'Spanish (Spain)',
			'es_LA' => 'Spanish',
			'et_EE' => 'Estonian',
			'eu_ES' => 'Basque',
			'fa_IR' => 'Persian',
			'ff_NG' => 'Fulah',
			'fi_FI' => 'Finnish',
			'fo_FO' => 'Faroese',
			'fr_CA' => 'French (Canada)',
			'fr_FR' => 'French (France)',
			'fy_NL' => 'Frisian',
			'ga_IE' => 'Irish',
			'gl_ES' => 'Galician',
			'gn_PY' => 'Guarani',
			'gu_IN' => 'Gujarati',
			'ha_NG' => 'Hausa',
			'he_IL' => 'Hebrew',
			'hi_IN' => 'Hindi',
			'hr_HR' => 'Croatian',
			'hu_HU' => 'Hungarian',
			'hy_AM' => 'Armenian',
			'id_ID' => 'Indonesian',
			'is_IS' => 'Icelandic',
			'it_IT' => 'Italian',
			'ja_JP' => 'Japanese',
			'ja_KS' => 'Japanese (Kansai)',
			'jv_ID' => 'Javanese',
			'ka_GE' => 'Georgian',
			'kk_KZ' => 'Kazakh',
			'km_KH' => 'Khmer',
			'kn_IN' => 'Kannada',
			'ko_KR' => 'Korean',
			'ku_TR' => 'Kurdish (Kurmanji)',
			'lt_LT' => 'Lithuanian',
			'lv_LV' => 'Latvian',
			'mg_MG' => 'Malagasy',
			'mk_MK' => 'Macedonian',
			'ml_IN' => 'Malayalam',
			'mn_MN' => 'Mongolian',
			'mr_IN' => 'Marathi',
			'ms_MY' => 'Malay',
			'mt_MT' => 'Maltese',
			'my_MM' => 'Burmese',
			'nb_NO' => 'Norwegian (bokmal)',
			'ne_NP' => 'Nepali',
			'nl_BE' => 'Dutch (België)',
			'nl_NL' => 'Dutch',
			'nn_NO' => 'Norwegian (nynorsk)',
			'or_IN' => 'Oriya',
			'pa_IN' => 'Punjabi',
			'pl_PL' => 'Polish',
			'ps_AF' => 'Pashto',
			'pt_BR' => 'Portuguese (Brazil)',
			'pt_PT' => 'Portuguese (Portugal)',
			'qz_MM' => 'Burmese',
			'ro_RO' => 'Romanian',
			'ru_RU' => 'Russian',
			'rw_RW' => 'Kinyarwanda',
			'sc_IT' => 'Sardinian',
			'si_LK' => 'Sinhala',
			'sk_SK' => 'Slovak',
			'sl_SI' => 'Slovenian',
			'so_SO' => 'Somali',
			'sq_AL' => 'Albanian',
			'sr_RS' => 'Serbian',
			'sv_SE' => 'Swedish',
			'sw_KE' => 'Swahili',
			'sz_PL' => 'Silesian',
			'ta_IN' => 'Tamil',
			'te_IN' => 'Telugu',
			'tg_TJ' => 'Tajik',
			'th_TH' => 'Thai',
			'tl_PH' => 'Filipino',
			'tr_TR' => 'Turkish',
			'tz_MA' => 'Tamazight',
			'uk_UA' => 'Ukrainian',
			'ur_PK' => 'Urdu',
			'uz_UZ' => 'Uzbek',
			'vi_VN' => 'Vietnamese',
			'zh_CN' => 'Simplified Chinese (China)',
			'zh_HK' => 'Traditional Chinese (Hong Kong)',
			'zh_TW' => 'Traditional Chinese (Taiwan)',

		];
	}

	/**
	 * Soon to be deprecated
	 *
	 * @return array
	 */
	static function old_options() {
		return [
			'show_posts',
			'close_warning',
			'height',
			'close_warning1',
			'max_width',
			'max_photos',
			'max_posts',
			'app_id',
			'app_secret',
			'proportions',
			'show_like',
			'fb_root',
			'theme',
			'show_follow',
			'video_ratio',
			'video_as_post',
			'raw_video',
			'raw_photo',
			'raw_post',
			'raw_page',
			'enqueue_style',
			'enq_lightbox',
			'enq_wpemfb',
			'enq_fbjs',
			'ev_local_tz',
			'page_height',
			'page_show_faces',
			'page_small_header',
			'page_hide_cover',
			'page_show_posts',
			'sdk_lang',
			'close_warning2',
			'force_app_token',
			'video_download',
			'sdk_version'
		];
	}

	/**
	 * Get $_POST or $_GET request.
	 *
	 * @param string $name    Name of request.
	 * @param mixed  $default Default value if request not found.
	 * @return mixed
	 *
	 * @author Rahul Aryan <rah12@live.com>
	 *
	 * @since 3.0.0
	 */
	public static function get_request( $name, $default = null ) {
		return isset( $_REQUEST[ $name ] ) ? $_REQUEST[ $name ] : $default; // WPCS: input var ok. sanitization ok. CSRF ok.
	}
}

<?php
/**
 * Plugin Helpers.
 *
 * @package Magic Embeds
 */

namespace SIGAMI\WP_Embed_FB;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Helpers {

	public static $has_photon  = null;
	public static $wp_timezone = null;
	public static $lb_defaults = null;

	public static function string_to_array( $text ) {
		if ( is_array( $text ) ) {
			return $text;
		}

		return explode( ',', trim( $text, ' ,' ) );
	}

	public static function has_photon(): ?bool {
		if ( null === self::$has_photon ) {
			if ( class_exists( 'Jetpack' )
					&& method_exists( 'Jetpack', 'get_active_modules' )
					&& in_array( 'photon', \Jetpack::get_active_modules(), true ) ) {
				self::$has_photon = true;
			} else {
				self::$has_photon = false;
			}
		}

		return self::$has_photon;
	}

	public static function has_fb_app() {
		$options = Plugin::get_option();
		$has_app = true;
		if ( empty( $options['app_secret'] ) || empty( $options['app_id'] ) ) {
			$has_app = false;
		}

		return apply_filters( 'wef_has_fb_app', $has_app );
	}

	public static function get_timezone() {
		if ( null === self::$wp_timezone ) {
			$tzstring = get_option( 'timezone_string', '' );
			if ( empty( $tzstring ) ) {
				$current_offset = get_option( 'gmt_offset', 0 );
				if ( 0 === $current_offset ) {
					$tzstring = 'Etc/GMT';
				} else {
					$tzstring = ( $current_offset < 0 ) ? 'Etc/GMT' . $current_offset : 'Etc/GMT+' . $current_offset;
				}
			}
			self::$wp_timezone = $tzstring;
		}

		return self::$wp_timezone;
	}

	public static function get_true_url() {
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
		} elseif ( Plugin::get_option( 'permalink_on_social_plugins' ) === 'true' ) {
			$query = $wp->request;
		} else {
			$query = '/?' . $wp->query_string;
		}

		return home_url( $query );
	}

	public static function lightbox_title( $title ) {
		$clean_title = esc_attr(
			wp_rel_nofollow(
				make_clickable(
					str_replace(
						[ '"', "'" ],
						[
							'&#34;',
							'&#39;',
						],
						$title
					)
				)
			)
		);

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

	public static function get_lb_defaults(): ?array {
		if ( null === self::$lb_defaults ) {
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
				'wpGallery',
			];
			self::$lb_defaults = [];
			$defaults          = Plugin::defaults();
			foreach ( $keys as $key ) {
				self::$lb_defaults[ $key ] = $defaults[ 'LB_' . $key ];
			}
		}

		return self::$lb_defaults;
	}

	public static function make_clickable( $text ) {
		if ( empty( $text ) ) {
			return $text;
		}

		return wpautop( self::rel_nofollow( make_clickable( $text ) ) );
	}

	public static function rel_nofollow( $text ) {
		$text = stripslashes( $text );

		return preg_replace_callback( /** @lang text */ '|<a (.+?)>|i', [ __CLASS__, 'nofollow_callback' ], $text );
	}

	public static function nofollow_callback( $matches ): string {
		$text = $matches[1];
		$text = str_replace( [ ' rel="nofollow"', " rel='nofollow'" ], '', $text );

		return "<a $text rel=\"nofollow\">";
	}

	public static function get_api_versions(): array {
		return [
			'v5.0'  => '5.0',
			'v18.0' => '18.0',
		];
	}

	public static function get_fb_locales(): array {
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
	 * Get $_POST or $_GET request.
	 *
	 * @param string $name          Name of request.
	 * @param mixed  $default_value Default value if request not found.
	 *
	 * @return mixed
	 * @since  3.0.0
	 */
	public static function get_request( string $name, $default_value = null ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is not required here.
		return isset( $_REQUEST[ $name ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $name ] ) ) : $default_value;
	}

	public static function notice_kses(): array {
		return array_merge(
			[
				'div' => [
					'id'    => [],
					'class' => [],
				],
				'h2'  => [],
				'p'   => [],
			],
			self::link_kses()
		);
	}

	public static function link_kses(): array {
		return [
			'a' => [
				'href'   => [],
				'title'  => [],
				'id'     => [],
				'class'  => [],
				'target' => [],
				'rel'    => [],
			],
		];
	}

	public static function get_lightbox_attr(): string {
		$lightbox_attr = Plugin::get_option( 'lightbox_att' );
		if ( empty( $lightbox_attr ) ) {
			return '';
		}

		$lightbox_attr_array = explode( '=', $lightbox_attr );
		if ( count( $lightbox_attr_array ) !== 2 ) {
			return '';
		}

		$lightbox_attr_array[0] = str_replace( [ '"','"' ], [ '','' ], $lightbox_attr_array[0] );
		$lightbox_attr_array[1] = str_replace( [ '"','"' ], [ '','' ], $lightbox_attr_array[1] );

		$lightbox_attr_array = array_map( 'esc_attr', $lightbox_attr_array );
		$lightbox_attr_array = array_map( 'trim', $lightbox_attr_array );

		return sprintf( '%s="%s"', $lightbox_attr_array[0], $lightbox_attr_array[1] );
	}
}

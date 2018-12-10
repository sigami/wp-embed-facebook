<?php
/**
 * API helper.
 *
 * @package WP Embed Facebook
 */

namespace SIGAMI\WP_Embed_FB;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class FB_API
 * Why use a SDK when WordPress has everything you need.
 *
 * Example:
 * use SIGAMI\WP_Embed_FB\FB_API;
 * FB_API::instance()->api(); //on error throws \Exception
 * FB_API::instance()->run(); //on error returns WP_Error
 *
 * @package SIGAMI\WP_Embed_FB
 */
class FB_API {

	const GRAPH_URL = 'https://graph.facebook.com/';

	protected $access_token, $app_id, $app_secret;

	public $app_access_token;

	/**
	 * @var FB_API
	 */
	protected static $instance = null;

	/**
	 * @return FB_API
	 */
	static function instance($app_id = null, $app_secret = null) {
		if ( static::$instance === null ) {
			static::$instance = new static($app_id,$app_secret);
		}

		return static::$instance;
	}

	protected function __construct($app_id = null,$app_secret = null) {
		$this->app_id           = $app_id === null ? Plugin::get_option( 'app_id' ) : $app_id;
		$this->app_secret       = $app_secret === null ? Plugin::get_option( 'app_secret' ) : $app_secret;
		$this->app_access_token = "{$this->app_id}|{$this->app_secret}";
		$this->access_token     = strlen( $this->app_access_token ) > 5 ? $this->app_access_token : null;
	}

	/**
	 * @param string $token
	 */
	public function setAccessToken( $token ) {
		$this->access_token = $token;
	}

	/**
	 * @return string
	 */
	public function getAccessToken() {
		return $this->access_token;
	}

	/**
	 * Get token information
	 *
	 * @param string $token
	 *
	 * @return array|\WP_Error
	 */
	public function debugToken( $token ) {
		return $this->run( 'debug_token?input_token=' . $token );
	}

	/**
	 * API call to extend an access token
	 *
	 * @param string $user_token
	 *
	 * @return array|\WP_Error
	 */
	public function extendAccessToken( $user_token ) {

		$args['client_id']         = $this->app_id;
		$args['client_secret']     = $this->app_secret;
		$args['grant_type']        = 'fb_exchange_token';
		$args['fb_exchange_token'] = $user_token;

		$string = add_query_arg( $args, 'oauth/access_token' );

		return $this->run( $string );
	}

	/**
	 * So simple...
	 * //TODO add a successful call example here
	 *
	 * @param string $link
	 *
	 * @return array|\WP_Error
	 */
	public function scrape_url( $link ) {
		return $this->run( '', 'POST', [
			'scrape' => 'true',
			'id'     => esc_url_raw( $link )
		] );
	}

	/**
	 * API Wrapper to handle error the WordPress way
	 *
	 * @param string $string
	 * @param string $method
	 * @param array  $message
	 *
	 * @return array|\WP_Error
	 */
	public function run( $string = '', $method = 'GET', $message = [] ) {
		try {
			return $this->api( $string, $method, $message );
		} catch ( \Exception $e ) {
			return new \WP_Error( $e->getCode(), $e->getMessage() );
		}
	}

	/**
	 * Same name as in FB PHP SDK
	 *
	 * @param string $string
	 * @param string $method
	 * @param array  $message
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function api( $string = '', $method = 'GET', $message = [] ) {

		$vars     = [];
		$base_url = self::GRAPH_URL;
		$pos      = strpos( $string, "?" );
		if ( $pos !== false ) {
			parse_str( parse_url( $string, PHP_URL_QUERY ), $vars );
			$base_url .= substr( trim( $string, '/' ), 0, $pos );
		} else {
			$base_url .= trim( $string, '/' );
		}

		if ( $this->access_token !== null ) {
			$vars['access_token'] = $this->access_token;
		}

		$url = add_query_arg( $vars, $base_url );

		if ( $method == 'GET' ) {
			if ( ! empty( $message ) ) {
				$url = add_query_arg( $message, $url );
			}
			$response = wp_remote_get( $url );
		} elseif ( $method == 'POST' ) {
			$response = wp_remote_post( $url, ! empty( $message ) ? [ 'body' => $message ] : [] );
		} else {
			throw new \Exception( __( 'Invalid API method', 'wp-embed-facebook' ), 69 );
		}

		if ( is_wp_error( $response ) ) {
			throw new \Exception( $response->get_error_code() . '=>'
			                      . $response->get_error_message(), 500 );
		}

		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );
		$response_body    = wp_remote_retrieve_body( $response );

		$data = empty( $response_body ) ? false : json_decode( $response_body, true );

		if ( is_array( $data ) ) {

			$api_error = false;
			$code      = isset( $data['error_code'] ) ? $data['error_code'] : 0;

			if ( isset( $data['error'] ) && is_array( $data['error'] ) ) {
				$api_error = $data['error']['message'];
				$code      = isset( $data['error']['code'] ) ? $data['error']['code'] : $code;
			} elseif ( isset( $data['error_description'] ) ) {
				$api_error = $data['error_description'];
			} elseif ( isset( $data['error_msg'] ) ) {
				$api_error = $data['error_msg'];
			}

			if ( $api_error !== false ) {
				throw new \Exception( $api_error, (int) $code );
			}
		}

		if ( 200 != $response_code && ! empty( $response_message ) ) {
			throw new \Exception( $response_message, (int) $response_code );
		}

		if ( 200 != $response_code || ! is_array( $data ) || empty( $data ) ) {
			throw new \Exception( __( 'Unknown error occurred', 'wp-embed-facebook' ),
				(int) $response_code );
		}

		return $data;
	}
}
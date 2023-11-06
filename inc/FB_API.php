<?php
/**
 * API helper.
 *
 * @package Magic Embeds
 */

namespace SIGAMI\WP_Embed_FB;

// If this file is called directly, abort.
use Exception;
use WP_Error;
use WP_Http;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class FB_API
 * Why use an SDK when WordPress has everything you need.
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

	protected ?string $access_token;

	protected ?string $app_id;
	protected ?string $app_secret;

	public ?string $app_access_token;

	protected static ?self $instance = null;

	/**
	 * Default instance created with plugin options.
	 *
	 * @return FB_API
	 */
	public static function instance(): ?self {
		if ( null === static::$instance ) {
			static::$instance = new static( Plugin::get_option( 'app_id' ), Plugin::get_option( 'app_secret' ) );
		}

		return static::$instance;
	}

	public function __construct( $app_id = '', $app_secret = '' ) {
		$this->app_id           = $app_id;
		$this->app_secret       = $app_secret;
		$this->app_access_token = "$app_id|$app_secret";
		$this->access_token     = strlen( $this->app_access_token ) > 5 ? $this->app_access_token : null;
	}

	/**
	 * @param string|null $token
	 */
	public function setAccessToken( ?string $token ) {
		$this->access_token = $token;
	}

	/**
	 * @return string|null
	 */
	public function getAccessToken(): ?string {
		return $this->access_token;
	}

	/**
	 * Get token information
	 *
	 * @param string $token
	 *
	 * @return array|WP_Error
	 * @noinspection PhpUnused -- Debug function.
	 */
	public function debugToken( string $token ) {
		return $this->run( 'debug_token?input_token=' . $token );
	}

	/**
	 * API call to extend an access token
	 *
	 * @param string $user_token
	 *
	 * @return array|WP_Error
	 */
	public function extendAccessToken( string $user_token ) {

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
	 * @return array|WP_Error
	 */
	public function scrape_url( string $link ) {
		return $this->run(
			'',
			'POST',
			[
				'scrape' => 'true',
				'id'     => esc_url_raw( $link ),
			]
		);
	}

	/**
	 * API Wrapper to handle error the WordPress way
	 *
	 * @param string $endpoint
	 * @param string $method
	 * @param array  $message
	 *
	 * @return array|WP_Error
	 */
	public function run( string $endpoint = '', string $method = 'GET', array $message = [] ) {
		try {
			return $this->api( $endpoint, $method, $message );
		} catch ( Exception $e ) {
			return new WP_Error( $e->getCode(), $e->getMessage() );
		}
	}

	/**
	 * Same name as in FB PHP SDK
	 *
	 * @param string $endpoint
	 * @param string $method
	 * @param array  $message
	 *
	 * @return array
	 * @throws Exception
	 */
	public function api( string $endpoint = '', string $method = 'GET', array $message = [] ): array {

		$vars     = [];
		$base_url = self::GRAPH_URL;
		$position = strpos( $endpoint, '?' );
		if ( false !== $position ) {
			parse_str( wp_parse_url( $endpoint, PHP_URL_QUERY ), $vars );
			$base_url .= substr( trim( $endpoint, '/' ), 0, $position );
		} else {
			$base_url .= trim( $endpoint, '/' );
		}

		if ( null !== $this->access_token ) {
			$vars['access_token'] = $this->access_token;
		}

		$url           = add_query_arg( $vars, $base_url );
		$valid_methods = apply_filters( 'wef_valid_api_methods', [ 'GET', 'POST' ] );

		if ( ! in_array( $method, $valid_methods, true ) ) {
			throw new Exception( esc_html__( 'Invalid API method', 'wp-embed-facebook' ), absint( WP_Http::METHOD_NOT_ALLOWED ) );
		}

		$response = [];

		if ( 'GET' === $method ) {
			if ( ! empty( $message ) ) {
				$url = add_query_arg( $message, $url );
			}
			$response = wp_remote_get( $url );
		}

		if ( 'POST' === $method ) {
			$response = wp_remote_post( $url, ! empty( $message ) ? [ 'body' => $message ] : [] );
		}

		$response = apply_filters( 'wef_api_response', $response, $url, $method, $message );

		if ( is_wp_error( $response ) ) {
			$message = $response->get_error_code() . '=>' . $response->get_error_message();
			throw new Exception( esc_html( $message ), absint( WP_Http::INTERNAL_SERVER_ERROR ) );
		}

		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );
		$response_body    = wp_remote_retrieve_body( $response );

		$data = empty( $response_body ) ? false : json_decode( $response_body, true );

		if ( is_array( $data ) ) {

			$api_error = false;
			$code      = $data['error_code'] ?? 0;

			if ( isset( $data['error'] ) && is_array( $data['error'] ) ) {
				$api_error = $data['error']['message'];
				$code      = $data['error']['code'] ?? $code;
			} elseif ( isset( $data['error_description'] ) ) {
				$api_error = $data['error_description'];
			} elseif ( isset( $data['error_msg'] ) ) {
				$api_error = $data['error_msg'];
			}

			if ( false !== $api_error ) {
				throw new Exception( esc_html( $api_error ), (int) $code );
			}
		}

		if ( 200 !== $response_code && ! empty( $response_message ) ) {
			throw new Exception( esc_html( $response_message ), (int) $response_code );
		}

		if ( 200 !== $response_code || ! is_array( $data ) || empty( $data ) ) {
			throw new Exception( esc_html__( 'Unknown error occurred', 'wp-embed-facebook' ), (int) $response_code );
		}

		return $data;
	}
}

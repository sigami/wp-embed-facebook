<?php

use SIGAMI\WP_Embed_FB\Embed_FB;
use SIGAMI\WP_Embed_FB\FB_API;
use SIGAMI\WP_Embed_FB\Plugin;

/**
 * Class WP_Embed_FB
 *
 * @deprecated use FB_API::instance()->api('')
 */
final class WP_Embed_FB extends Embed_FB {
	/**
	 * @deprecated use SIGAMI\WP_Embed_FB\Embed_Facebook; Embed_Facebook::get_fbsdk();
	 *
	 */
	static function get_fbsdk() {
		_deprecated_function( 'WP_Embed_FB::get_fbsdk()', '3.0',
			"Example: \n use SIGAMI\WP_Embed_FB\FB_API; \n FB_API::instance()->api('') " );

		return new WP_Embed_FB_Deprecated_API;
	}
}

/**
 * Class WP_Embed_FB_API
 *
 * @deprecated Never used only created for backwards compatibility
 */
final class WP_Embed_FB_Deprecated_API {

	/**
	 * @param string $string
	 * @param string $method
	 * @param array  $message
	 *
	 * @throws FacebookApiException
	 */
	public function api( $string = '', $method = 'GET', $message = [] ) {
		if ( ! class_exists( 'FacebookApiException' ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once( Plugin::path() . 'deprecated/FacebookApiException.php' );
		}
		try {
			FB_API::instance()->api( $string, $method, $message );
		} catch ( Exception $e ) {
			throw new FacebookApiException( [
				'error_code'        => $e->getCode(),
				'error_description' => $e->getMessage()
			] );
		}
	}

	public function setExtendedAccessToken() {
		$extended = FB_API::instance()->extendAccessToken( FB_API::instance()->getAccessToken() );
		if ( ! is_wp_error( $extended ) ) {
			FB_API::instance()->setAccessToken( $extended['token'] );//TODO test this
		}
	}
}



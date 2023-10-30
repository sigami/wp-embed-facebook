<?php
/**
 * Deprecated functions that exist only for backwards compatibility.
 *
 * phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound -- Deprecated classes.
 *
 * @noinspection PhpDeprecationInspection
 * */

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
	 * @noinspection PhpUnused
	 */
	public static function get_fbsdk(): WP_Embed_FB_Deprecated_API {
		_deprecated_function(
			'WP_Embed_FB::get_fbsdk()',
			'3.0',
			"Example: \n use SIGAMI\WP_Embed_FB\FB_API; \n FB_API::instance()->api('') "
		);

		return new WP_Embed_FB_Deprecated_API();
	}
}

/**
 * Class WP_Embed_FB_API
 *
 * @deprecated Never used only created for backwards compatibility
 */
final class WP_Embed_FB_Deprecated_API {

	/**
	 * @param string $text
	 * @param string $method
	 * @param array  $message
	 *
	 * @throws FacebookApiException
	 */
	public function api( string $text = '', string $method = 'GET', array $message = [] ) {
		if ( ! class_exists( 'FacebookApiException' ) ) {
			require_once Plugin::path() . 'inc/deprecated/FacebookApiException.php';
		}
		try {
			FB_API::instance()->api( $text, $method, $message );
		} catch ( Exception $e ) {
			throw new FacebookApiException(
				[
					'error_code'        => esc_html( $e->getCode() ),
					'error_description' => esc_html( $e->getMessage() ),
				]
			);
		}
	}

	/**
	 * @param $token
	 *
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function setAccessToken( $token ) {
		FB_API::instance()->setAccessToken( $token );
	}

	/**
	 * @return string|null
	 * @noinspection PhpUnused
	 */
	public function getAccessToken(): ?string {
		return FB_API::instance()->getAccessToken();
	}

	/**
	 * @return array
	 * @noinspection PhpUnused
	 */
	public function setExtendedAccessToken(): array {
		$extended = FB_API::instance()->extendAccessToken( FB_API::instance()->getAccessToken() );
		if ( ! is_wp_error( $extended ) ) {
			FB_API::instance()->setAccessToken( $extended['token'] );
		}
		return $extended;
	}
}

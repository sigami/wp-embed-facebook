<?php

use SIGAMI\WP_Embed_FB\Embed_FB;
use SIGAMI\WP_Embed_FB\FB_API;
use SIGAMI\WP_Embed_FB\Plugin;
use SIGAMI\WP_Embed_FB\Helpers;

final class WP_Embed_FB_Options {
	protected static $instance = null;
	static function instance(){
		if(self::$instance === null){
			self::$instance =  new self;
		}
		return self::$instance;
	}
	//This runs after plugin activation and update
	protected function __construct() {
		$this->remove_old_options();
		$this->string_options_to_array();
	}

	function string_options_to_array(){
		$options = Plugin::get_option();
		foreach ($options as $key => $value){
			if(in_array($key,['quote_post_types','auto_comments_post_types']) && is_string($value)){
				$options[$value] = Helpers::string_to_array($value);
			}
		}
		Plugin::set_options($options);
	}

	function remove_old_options(){
		if ( get_option( 'wpemfb_theme' ) ) {
			//upgrade options
			$defaults = Plugin::get_option();
			foreach ( Helpers::old_options() as $old_option ) {
				if ( isset( $defaults[ $old_option ] ) ) {
					$defaults[ $old_option ] = get_option( 'wpemfb_' . $old_option, $defaults[ $old_option ] );
				}
				delete_option( 'wpemfb_' . $old_option );
			}
			Plugin::set_options( $defaults );
		}
	}
}

add_action(Plugin::$option.'_activation','WP_Embed_FB_Options::instance');
add_action(Plugin::$option.'_update','WP_Embed_FB_Options::instance');

WP_Embed_FB_Options::instance();
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

	public function setAccessToken($token){
		FB_API::instance()->setAccessToken($token);
	}

	public function getAccessToken(){
		return FB_API::instance()->getAccessToken();
	}

	public function setExtendedAccessToken() {
		$extended = FB_API::instance()->extendAccessToken( FB_API::instance()->getAccessToken() );
		if ( ! is_wp_error( $extended ) ) {
			FB_API::instance()->setAccessToken( $extended['token'] );//TODO test this
		}
		return $extended;
	}
}



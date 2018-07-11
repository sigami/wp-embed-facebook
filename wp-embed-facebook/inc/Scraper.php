<?php
/**
 * Created for: wp-embed-facebook
 * By: Miguel Sirvent
 */

namespace SIGAMI\WP_Embed_FB;

class Scraper {
	private static $instance = null;

	static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {

	}

	/**
	 * Update Facebook share cache on the updated post
	 *
	 * @param $post_id
	 * @param $post \WP_Post
	 * @param $update
	 */
	static function save_post( $post_id, $post, $update ) {
		$allowed_post_types = Helpers::string_to_array( trim( Plugin::get_option( 'auto_scrape_post_types' ),
			' ,' ) );
		if ( Plugin::get_option( 'auto_scrape_posts' ) === 'false'
		     || wp_is_post_revision( $post_id )
		     || ! $update
		     || ! in_array( get_post_type( $post ), $allowed_post_types )
		     || $post->post_status != 'publish' ) {
			return;
		}

		FB_API::instance()->scrape_url( get_the_permalink( $post ) );
	}
}
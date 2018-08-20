<?php
/**
 * Handles link scrapping.
 *
 * @author     Miguel Sirvent and Rahul Aryan
 * @package    WP Facebook Embed
 * @subpackage Scraper
 * @since      3.0.0
 */

namespace SIGAMI\WP_Embed_FB;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Hooks for link scraping.
 */
class Scraper {
	/**
	 * Instance of this class.
	 *
	 * @var Scraper Instance of `SIGAMI\WP_Embed_FB\Scraper`.
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Scraper Instance of `SIGAMI\WP_Embed_FB\Scraper`.
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	private function __construct() {
		add_action( 'save_post', [ $this, 'save_post' ], 10, 3 );

		//TODO add link to scrape url manually on selected post types
		//TODO add bundle scrape
	}

	/**
	 * Update Facebook share cache on the updated post
	 *
	 * @param integer  $post_id Post ID.
	 * @param \WP_Post $post WP Post object.
	 * @param boolean  $update  Is update or new.
	 */
	public function save_post( $post_id, $post, $update ) {
		$allowed_post_types = Plugin::get_option( 'auto_scrape_post_types' );

		if ( ! Plugin::is_on( 'auto_scrape_posts' )
			|| wp_is_post_revision( $post_id )
			|| ! $update
			|| ! in_array( get_post_type( $post ), $allowed_post_types, true )
			|| 'publish' !== $post->post_status ) {
			return;
		}

		FB_API::instance()->scrape_url( get_the_permalink( $post ) );
	}
}

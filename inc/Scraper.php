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
	 * Allowed post types to run scrape for.
	 *
	 * @var array
	 * @since 3.0.0
	 */
	public $allowed_cpt = [];

	/**
	 * Is scrape on?
	 *
	 * @var boolean
	 * @since 3.0.0
	 */
	public $scrape_on = false;

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
		$this->allowed_cpt = Plugin::get_option( 'auto_scrape_post_types' );
		$this->scrape_on   = Plugin::get_option( 'auto_scrape_posts' );

		add_action( 'save_post', [ $this, 'save_post' ], 10, 2 );
		add_filter( 'post_row_actions', [ $this, 'actions' ], 10, 2 );
		add_filter( 'page_row_actions', [ $this, 'actions' ], 10, 2 );
		add_filter( 'admin_action_wef_scrape', [ $this, 'admin_action' ] );

		//TODO add bundle scrape
	}

	/**
	 * Update Facebook share cache on the updated post
	 *
	 * @param integer  $post_id Post ID.
	 * @param \WP_Post $post WP Post object.
	 */
	public function save_post( $post_id, $post ) {
		if ( ! $this->scrape_on
			|| wp_is_post_revision( $post_id )
			|| ! $update
			|| ! in_array( get_post_type( $post ), $this->allowed_cpt, true )
			|| 'publish' !== $post->post_status ) {
			return;
		}

		FB_API::instance()->scrape_url( get_the_permalink( $post ) );
	}

	/**
	 * Add custom post action link to scrape link.
	 *
	 * @param array    $actions Action links.
	 * @param \WP_Post $post    Post object.
	 * @return array
	 * @author Rahul Aryan <rah12@live.com>
	 * @since 3.0.0
	 */
	public function actions( $actions, $post ) {
		if ( in_array( get_post_type( $post ), $this->allowed_cpt, true ) ) {
			$nonce = wp_create_nonce( 'wef_scrape' );

			$actions['wef_scrape'] = '<a href="' . admin_url( 'admin.php?action=wef_scrape&id=' . $post->ID . '&_nonce=' . $nonce ) . '">' . __( 'FB Scrape', 'wp-embed-facebook' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * Admin action for scraping link.
	 *
	 * @return void
	 * @author Rahul Aryan <rah12@live.com>
	 * @since 3.0.0
	 */
	public function admin_action() {
		// Check nonce.
		if ( ! wp_verify_nonce( Helpers::get_request( '_nonce' ), 'wef_scrape' ) || ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_attr__( 'Trying to cheat!?', 'wp-embed-facebook' ) );
		}

		$post_id = (int) Helpers::get_request( 'id' );
		$post    = get_post( $post_id );

		// Trigger API.
		$this->save_post( $post_id, $post );

		// Redirect to previous page.
		wp_redirect( $_SERVER['HTTP_REFERER'] );

   		exit();
	}
}

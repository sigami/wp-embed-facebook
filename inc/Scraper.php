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

		add_action( 'save_post', [ $this, 'save_post' ] );
		add_filter( 'post_row_actions', [ $this, 'actions' ], 10, 2 );
		add_filter( 'page_row_actions', [ $this, 'actions' ], 10, 2 );
		add_filter( 'admin_action_wef_scrape', [ $this, 'admin_action' ] );
		add_action( 'current_screen', [ $this, 'current_screen' ] );
		add_action( 'admin_notices', [ $this, 'admin_notice' ] );
	}

	/**
	 * Update Facebook share cache on the updated post
	 *
	 * @param integer  $post_id Post ID.
	 */
	public function save_post( $post_id ) {
		$post = get_post( $post_id );

		if ( ! $this->scrape_on
			|| wp_is_post_revision( $post_id )
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

			$actions['wef_scrape'] = '<a href="' . admin_url( 'admin.php?action=wef_scrape&id=' . $post->ID . '&_nonce=' . $nonce ) . '">' . __( 'FB Scrape Link', 'wp-embed-facebook' ) . '</a>';
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
		$this->save_post( $post_id );

		// Redirect to previous page.
		wp_redirect( $_SERVER['HTTP_REFERER'] ); // WPCS: input var ok. XSS ok. CSRF ok.

   		exit();
	}

	/**
	 * Add our bulk actions for screens.
	 *
	 * @return void
	 * @author Rahul Aryan <rah12@live.com>
	 * @since 3.0.0
	 */
	public function current_screen() {
		$screen = get_current_screen();

		if ( in_array( $screen->post_type, $this->allowed_cpt, true ) ) {
			add_filter( 'bulk_actions-edit-' . $screen->post_type, [ $this, 'bulk_action' ] );
			add_filter( 'handle_bulk_actions-edit-' . $screen->post_type, [ $this, 'bulk_action_handler' ], 10, 3 );
		}
	}

	/**
	 * Add link scraping in post bulk actions dropdown.
	 *
	 * @param array $bulk_actions Bulk actions.
	 * @return array
	 * @author Rahul Aryan <rah12@live.com>
	 * @since 3.0.0
	 */
	public function bulk_action( $bulk_actions ) {
		$bulk_actions['wef_bulk_scrape'] = __( 'FB Scrape Link', 'wp-embed-facebook' );

		return $bulk_actions;
	}

	/**
	 * Bulk action handler for link scraping.
	 *
	 * @param string $redirect_to Redirect link.
	 * @param string $doaction    Name of action.
	 * @param array  $post_ids    Post ids.
	 * @return string
	 * @author Rahul Aryan <rah12@live.com>
	 * @since 3.0.0
	 */
	public function bulk_action_handler( $redirect_to, $doaction, $post_ids ) {
		if ( 'wef_bulk_scrape' !== $doaction ) {
			return $redirect_to;
		}

		// Scrape link.
		foreach ( $post_ids as $post_id ) {
			$this->save_post( $post_id );
		}

		$redirect_to = add_query_arg( 'wef_scraped_link', count( $post_ids ), $redirect_to );

		return $redirect_to;
	}

	/**
	 * Show a admin notice when bulk action is done.
	 *
	 * @return void
	 * @author Rahul Aryan <rah12@live.com>
	 * @since 3.0.0
	 */
	public function admin_notice() {
		$action = Helpers::get_request( 'wef_scraped_link', false );

		if ( false === $action ) {
			return;
		}

		printf(
			'<div class="notice notice-success is-dismissible"><p>' .
			_n( 'Link scraped of %d post.', 'Link scraped of %d posts.', $action, 'wp-embed-facebook' ) . '</p></div>', $action
		);
	}
}

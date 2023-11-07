<?php
/**
 * Comments helper.
 *
 * @package Magic Embeds
 */

namespace SIGAMI\WP_Embed_FB;

use WP_Post;
use WP_Query;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Handles comments auto embeds and comment count synchronization. It includes all actions and filters.
 * Comments plugin can also be invoked using the [fb_plugin comments] shortcode.
 */
class Comments {

	public static string $current_post_type = '';

	private static ?self $instance = null;

	private function __construct() {
	}

	public function hooks() {
		add_filter( 'comments_template', [ $this, 'comments_template' ] );

		if ( Plugin::get_option( 'comments_count_active' ) === 'true' ) {

			add_filter( 'get_comments_number', [ $this, 'get_comments_number' ], 10, 2 );

			add_action( 'save_post', [ $this, 'save_post' ], 10, 3 );

			add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ] );

			add_filter( 'wp_ajax_wpemfb_comments', [ $this, 'wpemfb_comments' ] );
			add_filter( 'wp_ajax_nopriv_wpemfb_comments', [ $this, 'wpemfb_comments' ] );

			add_action( Plugin::$option . '_uninstall', [ $this, 'uninstall' ] );
		}

		if ( Plugin::get_option( 'comments_open_graph' ) === 'true' ) {
			add_action( 'wp_head', [ $this, 'wp_head' ] );
		}
	}

	public static function instance(): ?self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Run on plugin uninstall.
	 */
	public function uninstall() {
		delete_post_meta_by_key( '_wef_comment_count' );
	}

	/**
	 * Adds FB open graph app_id meta tag to head
	 */
	public function wp_head() {
		$app_id = Plugin::get_option( 'app_id' );
		if ( ! empty( $app_id ) ) {
			echo '<meta property="fb:app_id" content="' . esc_attr( $app_id ) . '" />' . PHP_EOL;
		}
	}

	/**
	 * Replace theme template for FB comments.
	 *
	 * @param $template
	 *
	 * @return string
	 */
	public function comments_template( $template ): string {
		if ( ! self::active_on_post_type() ) {
			return $template;
		}

		return Plugin::path() . 'templates/comments.php';
	}

	private static function active_on_post_type(): bool {
		global $post_type, $post;

		$allowed_post_types = Plugin::get_option( 'auto_comments_post_types' );

		return in_array( self::$current_post_type, $allowed_post_types, true )
					|| in_array( $post_type, $allowed_post_types, true )
					|| ( ( $post instanceof WP_Post ) && in_array( $post->post_type, $allowed_post_types, true ) );
	}

	/**
	 * Update the comment count on post update
	 *
	 * @param $post_id
	 * @param $post
	 * @param $update
	 */
	public function save_post( $post_id, $post, $update ) {
		self::$current_post_type = get_post_type( $post );
		if ( wp_is_post_revision( $post_id ) || ! $update || ! self::active_on_post_type() ) {
			return;
		}

		$fbapi = FB_API::instance();

		$old_token = $fbapi->getAccessToken();
		$fbapi->setAccessToken( null );
		$data = FB_API::instance()->run( '?fields=share{comment_count}&id=' . home_url( "/?p=$post_id" ) );
		$fbapi->setAccessToken( $old_token );

		if ( ! is_wp_error( $data ) && isset( $data['share'] ) && isset( $data['share']['comment_count'] ) ) {
			update_post_meta( $post->ID, '_wef_comment_count', absint( $data['share']['comment_count'] ) );
		}
	}

	/**
	 * Alter order by 'comment_count' to use _wef_comment_count meta instead
	 *
	 * @param WP_Query $query
	 *
	 * @return WP_Query
	 */
	public function pre_get_posts( WP_Query $query ): WP_Query {
		if ( ! isset( $query->query_vars['orderby'] )
				|| 'comment_count' !== $query->query_vars['orderby']
				|| is_array( $query->get( 'post_type' ) )
		) {
			return $query;
		}

		self::$current_post_type = $query->get( 'post_type' );

		if ( self::active_on_post_type() ) {
			$query->set(
				'meta_query',
				[
					'relation' => 'OR',
					[
						'key'     => '_wef_comment_count',
						'compare' => 'NOT EXISTS',
					],
					[
						'key'     => '_wef_comment_count',
						'compare' => 'EXISTS',
					],
				]
			);
			$query->set( 'orderby', 'meta_value_num' );
		}

		return $query;
	}

	/**
	 * Ajax function for updating comment count
	 */
	public function wpemfb_comments() {
		if ( ! isset( $_POST['comments_nonce'] )
				|| ! wp_verify_nonce( sanitize_key( $_POST['comments_nonce'] ), 'magic_embeds_comments' )
		) {
			wp_send_json_error();
		}

		if ( empty( $_POST['response'] ) || ! is_array( $_POST['response'] ) ) {
			wp_send_json_error();
		}

		$response_values = array_map( 'sanitize_text_field', wp_unslash( $_POST['response'] ) );
		$response_keys   = array_map( 'sanitize_text_field', array_keys( $response_values ) );
		$response        = array_combine( $response_keys, $response_values );

		if ( isset( $response['href'] ) ) {
			$post_id = url_to_postid( $response['href'] );
			if ( ! $post_id ) {
				wp_send_json_error();
			}
			$count = self::get_comments_number( '', $post_id );
			if ( isset( $response['message'] ) ) {
				++$count;
			} else {
				--$count;
			}
			update_post_meta( $post_id, '_wef_comment_count', absint( $count ) );

			/**
			 * Action triggered after posts comments counts get updated.
			 *
			 * @param integer $post_id  Post id.
			 * @param object  $response Facebook event response.
			 *
			 * @todo  save info of the last 50 comments for recent comments widget on extended embeds.
			 * @since unknown
			 */
			do_action( 'wef_updated_comment', $post_id, $response );
		}

		wp_send_json_success();
	}

	/**
	 * @param string $number Number of comments on WP
	 * @param int    $post_id
	 *
	 * @return string
	 * @see get_comments_number
	 */
	public function get_comments_number( string $number, int $post_id ): string {
		// Detect post type if this function is called on an ajax call
		if ( wp_doing_ajax() && empty( self::$current_post_type ) ) {
			self::$current_post_type = get_post_type( $post_id );
		}

		if ( ! self::active_on_post_type() ) {
			return $number;
		}

		$count = get_post_meta( $post_id, '_wef_comment_count', true );

		return $count ? "$count" : '0';
	}
}

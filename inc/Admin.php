<?php
/**
 * Adds wp-admin related actions and filters.
 *
 * @package      Magic Embeds
 * @subpackage   Admin
 */

namespace SIGAMI\WP_Embed_FB;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Admin {
	private static ?self $instance = null;

	public ?string $url = null;

	private function __construct() {
		$this->url = admin_url( 'options-general.php?page=' . Plugin::$menu_slug );
	}

	public static function instance(): ?self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function hooks() {
		if ( ! Plugin::is_on( 'close_warning2' ) ) {
			// Notices.
			add_action( 'admin_notices', [ $this, 'admin_notices' ] );
			add_action( 'wp_ajax_wpemfb_close_warning', [ $this, 'wpemfb_close_warning' ] );
			add_action( 'in_admin_footer', [ $this, 'in_admin_footer' ] );
		}

		// editor style
		add_action( 'admin_init', [ $this, 'admin_init' ] );

		// register styles and scripts
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

		add_filter( 'plugin_action_links_' . plugin_basename( Plugin::$file ), [ $this, 'add_action_link' ] );
	}

	public function admin_notices() {
		// TODO change this for the new notice functions on wp 6.4.
		$notice = sprintf( // translators: %s: settings page url.
			__( 'To enable comment moderation and embed albums, events, profiles and video as HTML5 set up a facebook app on <a id="wef_settings_link" href="%s">settings</a>', 'wp-embed-facebook' ),
			esc_url_raw( $this->url )
		);
		ob_start();

		?>

		<div class="notice wpemfb_warning is-dismissible">
			<h2>Magic Embeds</h2>
			<p><?php echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?></p>
		</div>
		<?php
		echo wp_kses( ob_get_clean(), Helpers::notice_kses() );
	}

	public function wpemfb_close_warning() {
		if ( current_user_can( 'manage_options' ) ) {
			$options                   = Plugin::get_option();
			$options['close_warning2'] = 'true';
			Plugin::set_options( $options );
		} else {
			wp_send_json_error();
		}
		wp_send_json_success();
	}

	public function in_admin_footer() {
		ob_start();
		?>
		<script>
			jQuery(document).on('click', '.wpemfb_warning .notice-dismiss', () => {
				jQuery.post(ajaxurl, {action: 'wpemfb_close_warning'});
			});

			jQuery(document).on('click', '#wef_settings_link', event => {
				event.preventDefault();
				jQuery.post(ajaxurl, {action: 'wpemfb_close_warning'}, () => {
					window.location = "<?php echo esc_url_raw( $this->url ); ?>"
				});
			});
		</script>
		<?php

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Intentionally unescaped.
		echo ob_get_clean();
	}

	/**
	 * Enqueue $fb_path js and css to admin page
	 *
	 * @param string $hook_suffix current page
	 */
	public function admin_enqueue_scripts( string $hook_suffix ) {
		if ( 'settings_page_' . Plugin::$menu_slug === $hook_suffix ) {
			wp_enqueue_style( 'wpemfb-admin-css', Plugin::url() . 'inc/css/admin.css', [], Plugin::VER );
		}

		wp_enqueue_style( 'wpemfb-custom', Plugin::url() . 'templates/custom-embeds/styles.css', [], Plugin::VER );
	}

	public function add_action_link( $links ) {
		array_unshift( $links, '<a title="$fb_path Settings" href="' . $this->url . '">' . __( 'Settings' ) . '</a>' );

		return $links;
	}

	/**
	 * Add template editor style to the embeds.
	 */
	public function admin_init() {
		add_filter( 'mce_css', [ $this, 'mce_css' ] );
	}

	public function mce_css( $css ): string {
		$styles = add_query_arg( 'version', Plugin::VER, Plugin::url() . 'templates/custom-embeds/styles.css' );

		if ( ! empty( $css ) ) {
			$css .= ',';
		}

		return $css . $styles;
	}
}

<?php
/**
 * Adds wp-admin related actions and filters.
 *
 * @package      WP Embed Facebook
 * @subpackage   Admin
 */

namespace SIGAMI\WP_Embed_FB;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Admin {

	private static $instance = null;

	static $url = null;

	static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {

		self::$url = admin_url( 'options-general.php?page=' . Plugin::$menu_slug );

		//notices
		if ( ! Plugin::is_on( 'close_warning2' ) ) {
			add_action( 'admin_notices', __CLASS__ . '::admin_notices' );
			add_action( 'wp_ajax_wpemfb_close_warning', __CLASS__ . '::wpemfb_close_warning' );
			add_action( 'in_admin_footer', __CLASS__ . '::in_admin_footer' );
		}

		//editor style
		add_action( 'admin_init', __CLASS__ . '::admin_init' );

		//register styles and scripts
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );

		add_filter( 'plugin_action_links_' . plugin_basename( Plugin::$FILE ),
			__CLASS__ . '::add_action_link' );

	}

	static function admin_notices() {
		ob_start();
		?>
        <div class="notice wpemfb_warning is-dismissible">
            <h2>WP Embed Facebook</h2>
            <p>
				<?php
				printf( __( 'To enable comment moderation and embed albums, events, profiles and video as HTML5 setup a facebook app on <a id="wef_settings_link" href="%s">settings</a>',
					'wp-embed-facebook' ), Admin::$url )
				?>
            </p>
        </div>
		<?php
		echo ob_get_clean();
	}

	static function wpemfb_close_warning() {
		if ( current_user_can( 'manage_options' ) ) {
			$options                   = Plugin::get_option();
			$options['close_warning2'] = 'true';
			Plugin::set_options( $options );
		}
		die;
	}

	static function in_admin_footer() {
		ob_start();
		?>
        <script type="text/javascript">
            jQuery(document).on('click', '.wpemfb_warning .notice-dismiss', function () {
                jQuery.post(ajaxurl, {action: 'wpemfb_close_warning'});
            });

            jQuery(document).on('click', '#wef_settings_link', function (e) {
                e.preventDefault();
                jQuery.post(ajaxurl, {action: 'wpemfb_close_warning'}, function () {
                    window.location = "<?php echo Admin::$url; ?>"
                });

            });
        </script>
		<?php
		echo ob_get_clean();
	}

	/**
	 * Enqueue WP Embed Facebook js and css to admin page
	 *
	 * @param string $hook_suffix current page
	 */
	static function admin_enqueue_scripts( $hook_suffix ) {
		if ( $hook_suffix == 'settings_page_' . Plugin::$menu_slug ) {
			wp_enqueue_style( 'wpemfb-admin-css', Plugin::url() . 'inc/css/admin.css' );
		}

		wp_enqueue_style( 'wpemfb-custom', Plugin::url() . 'templates/custom-embeds/styles.css', [],
			Plugin::VER );
	}

	static function add_action_link( $links ) {
		array_unshift( $links,
			'<a title="WP Embed Facebook Settings" href="' . Admin::$url . '">' . __( "Settings" )
			. '</a>' );

		return $links;
	}

	/**
	 * Add template editor style to the embeds.
	 */
	static function admin_init() {
		add_filter( 'mce_css', __CLASS__ . '::mce_css' );
	}

	static function mce_css( $css ) {

		$styles = add_query_arg( 'version', Plugin::VER,
			Plugin::url() . 'templates/custom-embeds/styles.css' );

		if ( ! empty( $css ) ) {
			$css .= ',';
		}

		return $css . $styles;
	}

}

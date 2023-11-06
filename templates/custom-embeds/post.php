<?php
/**
 * Data available for template:
 *
 * @var array $fb_data Facebook data.
 * @var string $theme Theme.
 * @var string $width Width.
 */

use SIGAMI\WP_Embed_FB\Plugin;
use SIGAMI\WP_Embed_FB\Social_Plugins;

$fb_post = $fb_data ?>
<div class="wef-container wef-<?php echo esc_attr( $theme ); ?>" style="max-width: <?php echo absint( $width ); ?>px">
	<div class="wef-col-3 wef-text-center">
		<a href="https://www.facebook.com/<?php echo esc_attr( $fb_post['from']['id'] ); ?>" target="_blank" rel="nofollow">
			<img src="https://graph.facebook.com/<?php echo esc_attr( $fb_post['from']['id'] ); ?>/picture" width="50px" height="50px"/>
		</a>
	</div>
	<div class="wef-col-9 wef-pl-none">
		<p>
			<a href="https://www.facebook.com/<?php echo esc_html( $fb_post['from']['id'] ); ?>" target="_blank" rel="nofollow">
				<span class="wef-title"><?php echo esc_html( $fb_post['from']['name'] ); ?></span>
			</a>
		</p>
		<div>
			<?php
			$opt = Plugin::get_option( 'show_like' );
			if ( 'true' === $opt ) :
				// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
				echo Social_Plugins::get(
					'like',
					[
						'href'   => 'https://www.facebook.com/' . $fb_data['id'],
						'share'  => 'true',
						'layout' => 'button_count',
					]
				);
			// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
			else :
				// translators: %d like counts.
				printf( esc_html__( '%d people like this.', 'wp-embed-facebook' ), esc_html( $fb_post['likes'] ) );
			endif;
			?>
		</div>
	</div>
	<?php if ( isset( $fb_post['picture'] ) || isset( $fb_post['message'] ) ) : ?>
		<?php
		global $wp_embed;
		include 'single-post.php';
		?>
	<?php endif; ?>
</div>

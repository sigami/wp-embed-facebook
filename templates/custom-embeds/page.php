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

?>
<div class="wef-container wef-<?php echo esc_attr( $theme ); ?>" style="max-width: <?php echo esc_attr( $width ); ?>px" >
	<?php if ( isset( $fb_data['cover'] ) ) : ?>
		<div class="wef-relative-container wef-cover"><div class="wef-relative" style="background-image: url('<?php echo esc_url_raw( $fb_data['cover']['source'] ); ?>'); background-position-y: <?php echo esc_attr( $fb_data['cover']['offset_y'] ); ?>%" onclick="window.open('https://www.facebook.com/<?php echo esc_attr( $fb_data['id'] ); ?>', '_blank')"></div></div>
	<?php endif; ?>
	<div class="wef-row wef-pad-top">
			<div class="wef-col-2 wef-text-center">
				<a href="<?php echo esc_url_raw( $fb_data['link'] ); ?>" target="_blank" rel="nofollow">
					<img src="<?php echo esc_url_raw( $fb_data['picture']['data']['url'] ); ?>" width="50px" height="50px" />
				</a>
			</div>
			<div class="wef-col-10 wef-pl-none">
				<a href="<?php echo esc_url_raw( $fb_data['link'] ); ?>" target="_blank" rel="nofollow">
					<span class="wef-title"><?php echo esc_html( $fb_data['name'] ); ?></span>
				</a>
				<br>
				<?php
				if ( 'Musician/band' === $fb_data['category'] ) {
					echo isset( $fb_data['genre'] ) ? esc_html( $fb_data['genre'] ) : '';
				} else {
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- not needed.
					esc_html_e( $fb_data['category'], 'wp-embed-facebook' );
				}
				?>
				<br>
				<?php if ( isset( $fb_data['website'] ) && ( wp_strip_all_tags( $fb_data['website'] ) !== '' ) ) : ?>
					<a  href="<?php echo esc_url_raw( $fb_data['website'] ); ?>" title="<?php esc_attr_e( 'Web Site', 'wp-embed-facebook' ); ?>" target="_blank">
						<?php esc_html_e( 'Web Site', 'wp-embed-facebook' ); ?>
					</a>
				<?php endif; ?>
				<div style="float: right;">
					<?php
					$opt = Plugin::get_option( 'show_like' );
					if ( 'true' === $opt ) :
						// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
						echo Social_Plugins::get(
							'like',
							[
								'href'       => 'https://www.facebook.com/' . $fb_data['id'],
								'share'      => 'true',
								'layout'     => 'button_count',
								'show-faces' => 'false',
							]
						);
						// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
					else :
						// translators: % like count.
						printf( esc_html__( '%d people like this.', 'wp-embed-facebook' ), esc_html( $fb_data['fan_count'] ) );
					endif;
					?>
				</div>
			</div>
	</div>
	<?php
	if ( isset( $fb_data['posts'] ) ) :
		global $wp_embed;
		?>
		<?php foreach ( $fb_data['posts']['data'] as $fb_post ) : ?>
			<?php if ( isset( $fb_post['picture'] ) || isset( $fb_post['message'] ) ) : ?>
				<?php include 'single-post.php'; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

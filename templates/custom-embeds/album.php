<?php
/**
 * Data available for template:
 *
 * @var array $fb_data Facebook data.
 * @var string $theme Theme.
 * @var string $width Width.
 */

use SIGAMI\WP_Embed_FB\Helpers;
use SIGAMI\WP_Embed_FB\Plugin;
?>
<div class="wef-container wef-<?php echo esc_attr( $theme ); ?>" style="max-width: <?php echo esc_attr( $width ); ?>px">
	<div class="wef-row">
		<div class="wef-col-3 wef-text-center">
			<a href="https://facebook.com/<?php echo esc_attr( $fb_data['from']['id'] ); ?>" target="_blank" rel="nofollow">
				<img src="https://graph.facebook.com/<?php echo esc_attr( $fb_data['from']['id'] ); ?>/picture" alt="<?php echo esc_attr( $fb_data['from']['name'] ); ?>" />
			</a>
		</div>
		<div class="wef-col-9 wef-pl-none">
			<a href="https://facebook.com/<?php echo esc_attr( $fb_data['from']['id'] ); ?>" target="_blank" rel="nofollow">
				<span class="wef-title"><?php echo esc_html( $fb_data['from']['name'] ); ?></span>
			</a>
			<br>
			<?php if ( isset( $fb_data['from']['category'] ) ) : ?>
				<?php echo esc_html( $fb_data['from']['category'] ) . '<br>'; ?>
			<?php endif; ?>
			<a href="https://www.facebook.com/<?php echo esc_attr( $fb_data['id'] ); ?>" target="_blank" rel="nofollow"><?php echo esc_attr( $fb_data['name'] ); ?></a>
		</div>
	</div>
	<hr class="wef-hr">
	<div class="wef-row">
		<div class="wef-col-12 wef-text-center">
			<div class="wef-text-center wef-album-thumbs">
			<?php
			if ( isset( $fb_data['photos'] ) ) {
				foreach ( $fb_data['photos']['data'] as $pic ) {
					$data_title = $pic['name'] ?? '';
					?>
					<?php //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helpers  ?>
					<a class="wef-album-thumbs" href="<?php echo esc_url_raw( $pic['source'] ); ?>" <?php echo Helpers::get_lightbox_attr(); ?> <?php echo ! empty( $data_title ) ? Helpers::lightbox_title( $data_title ) : ''; ?> >
						<span class="wef-album-thumb" style="background-image: url('<?php echo esc_url_raw( $pic['picture'] ); ?>')"></span>
					</a>
					<?php
				}
			}
			?>
			</div>
		</div>
	</div>
</div>

<?php
/**
 * Data available for template:
 *
 * @var array $fb_data Facebook data.
 * @var string $theme Theme.
 * @var string $width Width.
 */

use SIGAMI\WP_Embed_FB\Plugin;

?>
<div class="wef-container wef-<?php echo esc_attr( $theme ); ?>" style="max-width: <?php echo esc_attr( $width ); ?>px" >
	<a href="<?php echo esc_url_raw( $fb_data['link'] ); ?>" target="_blank" rel="nofollow">
		<img src="<?php echo esc_url_raw( $fb_data['source'] ); ?>" width="100%" height="auto" >
	</a>

	<a class="wef-post-link" href="<?php echo esc_url_raw( $fb_data['link'] ); ?> " target="_blank" rel="nofollow">
		<?php echo isset( $fb_data['likes'] ) ? '<img width="16px" height="16px" src="' . esc_url_raw( Plugin::url() ) . 'templates/images/like.png" /> ' . esc_html( $fb_data['likes']['summary']['total_count'] ) . ' ' : ''; ?>
		<?php echo isset( $fb_data['comments'] ) ? ' <img width="16px" height="16px" src="' . esc_url_raw( Plugin::url() ) . 'templates/images/comments.png"/> ' . esc_html( $fb_data['comments']['summary']['total_count'] ) . ' ' : ''; ?>
	</a>
</div>

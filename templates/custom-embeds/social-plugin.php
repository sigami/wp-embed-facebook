<?php
/**
 * Data available for template:
 *
 * @var array $fb_data Facebook data.
 * @var string $theme Theme.
 * @var string $width Width.
 * @var string $type Width.
 */
use SIGAMI\WP_Embed_FB\Plugin;
use SIGAMI\WP_Embed_FB\Social_Plugins;
?>
<div class="wef-measure" style="max-width: <?php echo esc_attr( $width ); ?>px;"></div>
<?php
switch ( $type ) {
	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in Social_Plugin::get().
	case 'page':
		echo Social_Plugins::get(
			'page',
			[
				'href'  => 'https://www.facebook.com/' . $fb_data['link'],
				'width' => $width,
			]
		);
		break;
	case 'video':
		if ( Plugin::get_option( 'video_as_post' ) === 'true' ) {
			echo Social_Plugins::get(
				'post',
				[
					'href'  => 'https://www.facebook.com/' . $fb_data['link'],
					'width' => $width,
				]
			);
		} else {
			echo Social_Plugins::get(
				'video',
				[
					'href'  => 'https://www.facebook.com/' . $fb_data['link'],
					'width' => $width,
				]
			);
		}

		break;
	// Values 'post', 'photo' are default.
	default:
		echo Social_Plugins::get(
			'post',
			[
				'href'  => 'https://www.facebook.com/' . $fb_data['link'],
				'width' => $width,
			]
		);
		break;
	// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
}

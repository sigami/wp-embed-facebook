<?php
/**
 * Album template.
 *
 * @var array $fb_data Facebook data.
 * @var string $theme Theme.
 * @var string $width Width.
 */

use SIGAMI\WP_Embed_FB\Embed_FB;
use SIGAMI\WP_Embed_FB\Plugin;
use SIGAMI\WP_Embed_FB\Helpers;

$story = isset( $fb_post['story'] ) ? '<p>' . $fb_post['story'] . '</p>' : '';

$icon = isset( $fb_post['icon'] ) ? '<img class="wef-icon" title="Facebook ' . $fb_post['type'] . '" src="' . $fb_post['icon'] . '">' : '';

$format = Plugin::get_option( 'single_post_time_format' );
$time   = wp_date(
	$format,
	strtotime( $fb_post['created_time'] ),
	new \DateTimeZone( Helpers::get_timezone() )
);

$description = ! empty( $fb_post['description'] ) ? Helpers::make_clickable( $fb_post['description'] ) : '';

$link_array = explode( '_', $fb_post['id'] );

$post_link = ( ! empty( $fb_post['link'] ) ) ? $fb_post['link'] : 'https://www.facebook.com/' . $link_array[0] . '/posts/' . $link_array[1];

$message = ( ! empty( $fb_post['message'] ) ) ? $fb_post['message'] : '';

$caption = ! empty( $fb_post['caption'] ) ? $fb_post['caption'] : '';

$name = ! empty( $fb_post['name'] ) ? $fb_post['name'] : '';


if ( $caption === $message ) {
	$caption = '';
}

$message = Helpers::make_clickable( $message );


$name = empty( $name ) ? '' : "<p class=\"caption-title\"><a href=\"$post_link\" title=\"$name\" target=\"_blank\" rel=\"nofollow\">$name</a></p>";

$description = empty( $description ) ? '' : "<div class=\"caption-description\">$description</div>";

$caption = empty( $caption ) ? '' : "<p class=\"caption-link\"><a href=\"$post_link\" target=\"_blank\" rel=\"nofollow\"></a>$caption</p>";

$link_info = $name . $description . $caption;
?>
<hr class="wef-hr">
<div class="wef-row">
	<div class="wef-col-12">
		<?php echo wp_kses_post( $story ); ?>
		<p class="wef-post-time"><?php echo esc_html( $time ); ?></p>
		<?php
		echo $message ? '<p>' . wp_kses_post( $message ) . '</p>' : '';
		switch ( $fb_post['type'] ) :
			case 'video':
				if ( strpos( $post_link, 'facebook.com' ) !== false ) {
					$raw             = Embed_FB::$raw;
					$width_r         = Embed_FB::$width;
					Embed_FB::$raw   = true;
					Embed_FB::$width = (int) str_replace( [ 'px','%' ], [], $width ) - 40;
					echo $wp_embed->shortcode( [ 'src' => $post_link ] );
					Embed_FB::$raw   = $raw;
					Embed_FB::$width = $width_r;
					echo $link_info;
				} else {
					$use_ratio = ( Plugin::get_option( 'video_ratio' ) == 'true' );
					echo '<div class="wef-post-link">';
					echo $use_ratio ? '<div class="wef-video">' : '';
					echo $wp_embed->shortcode(
						[
							'src'   => $post_link,
							'width' => $width - 20,
						]
					);
					echo $use_ratio ? '</div>' : '';
					echo $link_info;
					echo '</div>';
				}
				break;
			case 'event':
				Embed_FB::$width = $width - 40;
				echo $wp_embed->shortcode( [ 'src' => $post_link ] );
				Embed_FB::$width = $width;
				break;
			case 'photo':
				?>

				<a href="<?php echo $fb_post['full_picture']; ?>" <?php echo Plugin::get_option( 'lightbox_att' ); ?> <?php echo $message ? Helpers::lightbox_title( $message ) : ''; ?> >
					<div class="wef-relative-container wef-fbpost-image">
						<div class="wef-relative"
							style="background-image: url('<?php echo $fb_post['full_picture']; ?>');"></div>
					</div>
				</a>
				<?php echo $link_info; ?>
				<?php
				break;
			case 'music':
			case 'link':
				?>
				<div class="wef-post-link" style="max-width: <?php echo $width; ?>px;">
					<?php if ( isset( $fb_post['full_picture'] ) && ! empty( $fb_post['full_picture'] ) ) : ?>
						<div class="wef-relative-container wef-fbpost-image">
							<div class="wef-relative"
								style="background-image: url('<?php echo $fb_post['full_picture']; ?>');"
								onclick="window.open('<?php echo $post_link; ?>', '_blank')"></div>
						</div>
					<?php endif ?>
					<?php if ( $fb_post['type'] == 'music' ) : ?>
						<p>
							<audio controls>
								<source src="<?php echo $fb_post['source']; ?>" type="audio/mpeg">
							</audio>
						</p>
					<?php endif ?>
					<?php echo $link_info; ?>
				</div>
				<?php
				break;
			case 'status':
			default:
				?>
				<?php if ( isset( $fb_post['full_picture'], $post_link ) && ! empty( $fb_post['full_picture'] ) && ! empty( $post_link ) ) : ?>
				<a href="<?php echo $fb_post['full_picture']; ?>" <?php echo Plugin::get_option( 'lightbox_att' ); ?> <?php echo $message ? \SIGAMI\WP_Embed_FB\Helpers::lightbox_title( $message ) : ''; ?> >
					<div class="wef-relative-container wef-fbpost-image">
						<div class="wef-relative"
							style="background-image: url('<?php echo $fb_post['full_picture']; ?>');"></div>
					</div>
				</a>
			<?php endif; ?>
				<?php echo $link_info; ?>
				<?php
				break;
		endswitch;
		$title_count = '';
		if ( isset( $fb_post['likes'] ) ) {
			$title_count .= $fb_post['likes']['summary']['total_count'] . ' ' . __( 'likes', 'wp-embed-facebook' ) . ' ';
		}
		if ( isset( $fb_post['comments'] ) ) {
			$title_count .= $fb_post['comments']['summary']['total_count'] . ' ' . __( 'comments', 'wp-embed-facebook' ) . ' ';
		}
		if ( isset( $fb_post['shares'] ) ) {
			$title_count .= $fb_post['shares']['count'] . ' ' . __( 'shares', 'wp-embed-facebook' ) . ' ';
		}
		?>
		<br>
		<a class="wef-post-likes"
			href="<?php echo 'https://www.facebook.com/' . $link_array[0] . '/posts/' . $link_array[1]; ?> "
			target="_blank" rel="nofollow" title="<?php echo esc_attr( $title_count ); ?>">
			<?php echo isset( $fb_post['likes'] ) ? ' <img width="16px" height="16px" src="' . Plugin::url() . 'templates/images/like.png" /> ' . $fb_post['likes']['summary']['total_count'] . ' ' : ''; ?>
			<?php echo isset( $fb_post['comments'] ) ? ' <img width="16px" height="16px" src="' . Plugin::url() . 'templates/images/comments.png" /> ' . $fb_post['comments']['summary']['total_count'] . ' ' : ''; ?>
			<?php echo isset( $fb_post['shares'] ) ? ' <img width="16px" height="16px" src="' . Plugin::url() . 'templates/images/share.png" /> ' . $fb_post['shares']['count'] . ' ' : ''; ?>
		</a>
	</div>
</div>

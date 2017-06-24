<?php


$story = isset($fb_post['story']) ? '<p>' . $fb_post['story'] . '</p>' : '';

$icon = isset($fb_post["icon"]) ? '<img class="icon" title="Facebook ' . $fb_post["type"] . '" src="' . $fb_post["icon"] . '">' : '';

$old_time_zone = date_default_timezone_get();
date_default_timezone_set(WP_Embed_FB_Plugin::get_timezone());
$time = $icon . date_i18n(WP_Embed_FB_Plugin::get_option('single_post_time_format'), strtotime($fb_post['created_time']));
date_default_timezone_set($old_time_zone);

$description = isset($fb_post['description']) && !empty($fb_post['description']) ? WP_Embed_FB::make_clickable($fb_post['description']) : '';

$linkArray = explode("_", $fb_post['id']);
$link = (isset($fb_post['link']) && !empty($fb_post['link'])) ? $fb_post['link'] : "https://www.facebook.com/" . $linkArray[0] . "/posts/" . $linkArray[1];

$message = (isset($fb_post['message']) && !empty($fb_post['message'])) ? $fb_post['message'] : '';

$caption = isset($fb_post['caption']) && !empty($fb_post['caption']) ? $fb_post['caption'] : '';

$name = isset($fb_post['name']) && !empty($fb_post['name']) ? $fb_post['name'] : '';


if ($caption == $message) {
	$caption = '';
}

$message = WP_Embed_FB::make_clickable($message);


$name = empty($name) ? '' : "<p class=\"caption-title\"><a href=\"$link\" title=\"$name\" target=\"_blank\" rel=\"nofollow\">$name</a></p>";

$description = empty($description) ? '' : "<div class=\"caption-description\">$description</div>";

$caption = empty($caption) ? '' : "<p class=\"caption-link\"><a href=\"$link\" target=\"_blank\" rel=\"nofollow\"></a>$caption</p>";

$link_info = $name . $description . $caption;
?>
<hr>
<div class="row">
	<div class="col-12 page-post">
		<?php //echo '<pre>'.wpautop(print_r($fb_post,true)).'</pre>'; ?>
		<?php echo $story ?>
		<p class="post-time"><?php echo $time ?></p>
		<?php
		echo $message ? '<p>' . $message . '</p>' : '';
		switch ($fb_post["type"]) :
			case 'video':
				if (strpos($link, 'facebook.com') !== false) {
					$raw = WP_Embed_FB::$raw;
					$width_r = WP_Embed_FB::$width;
					WP_Embed_FB::$raw = true;
					WP_Embed_FB::$width = $width - 40;
					echo $wp_embed->shortcode(array('src' => $link));
					WP_Embed_FB::$raw = $raw;
					WP_Embed_FB::$width = $width_r;
					echo $link_info;
				} else {
					$use_ratio = (WP_Embed_FB_Plugin::get_option('video_ratio') == 'true');
					echo '<div class="post-link">';
					echo $use_ratio ? '<div class="video">' : '';
					echo $wp_embed->shortcode(array('src' => $link, 'width' => $width - 20));
					echo $use_ratio ? '</div>' : '';
					echo $link_info;
					echo '</div>';
				}
				break;
			case 'event':
				WP_Embed_FB::$width = $width - 40;
				echo $wp_embed->shortcode(array('src' => $link));
				WP_Embed_FB::$width = $width;
				break;
			case 'photo':
				?>

				<a href="<?php echo $fb_post['full_picture'] ?>" <?php echo WP_Embed_FB_Plugin::get_option('lightbox_att') ?> <?php echo $message ? WP_Embed_FB_Plugin::lightbox_title($message) : '' ?> >
					<div class="relative-container fbpost-image">
						<div class="relative"
						     style="background-image: url('<?php echo $fb_post['full_picture'] ?>');"></div>
					</div>
				</a>
				<?php echo $link_info; ?>
				<?php
				break;
			case 'music':
			case 'link':
				?>
				<div class="post-link" style="max-width: <?php echo $width ?>px;">
					<?php if (isset($fb_post['full_picture']) && !empty($fb_post['full_picture'])) : ?>
						<div class="relative-container fbpost-image">
							<div class="relative"
							     style="background-image: url('<?php echo $fb_post['full_picture'] ?>');"
							     onclick="window.open('<?php echo $link ?>', '_blank')"></div>
						</div>
					<?php endif ?>
					<?php if ($fb_post["type"] == 'music') : ?>
						<p>
							<audio controls>
								<source src="<?php echo $fb_post['source'] ?>" type="audio/mpeg">
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
				<?php if (isset($fb_post['full_picture'], $link) && !empty($fb_post['full_picture']) && !empty($link)) : ?>
				<a href="<?php echo $fb_post['full_picture'] ?>" <?php echo WP_Embed_FB_Plugin::get_option('lightbox_att') ?> <?php echo $message ? WP_Embed_FB_Plugin::lightbox_title($message) : '' ?> >
					<div class="relative-container fbpost-image">
						<div class="relative"
						     style="background-image: url('<?php echo $fb_post['full_picture'] ?>');"></div>
					</div>
				</a>
			<?php endif; ?>
				<?php echo $link_info; ?>
				<?php
				break;
		endswitch;
		$title_count = '';
		if (isset($fb_post['likes'])) {
			$title_count .= $fb_post['likes']['summary']['total_count'] . ' ' . __('likes', 'wp-embed-facebook') . ' ';
		}
		if (isset($fb_post['comments'])) {
			$title_count .= $fb_post['comments']['summary']['total_count'] . ' ' . __('comments', 'wp-embed-facebook') . ' ';
		}
		if (isset($fb_post['shares'])) {
			$title_count .= $fb_post['shares']['count'] . ' ' . __('shares', 'wp-embed-facebook') . ' ';
		}
		?><br>
		<a class="post-likes"
		   href="<?php echo "https://www.facebook.com/" . $linkArray[0] . "/posts/" . $linkArray[1] ?> "
		   target="_blank" rel="nofollow" title="<?php echo esc_attr($title_count) ?>">
			<?php echo isset($fb_post['likes']) ? '<img src="https://fbstatic-a.akamaihd.net/rsrc.php/v2/y6/r/l9Fe9Ugss0S.gif" />' . $fb_post['likes']['summary']['total_count'] . ' ' : "" ?>
			<?php echo isset($fb_post['comments']) ? '<img src="https://fbstatic-a.akamaihd.net/rsrc.php/v2/yg/r/V8Yrm0eKZpi.gif" />' . $fb_post['comments']['summary']['total_count'] . ' ' : "" ?>
			<?php echo isset($fb_post['shares']) ? '<img src="https://fbstatic-a.akamaihd.net/rsrc.php/v2/y2/r/o19N6EzzbUm.png" />' . $fb_post['shares']['count'] . ' ' : "" ?>
		</a>
	</div>
</div>
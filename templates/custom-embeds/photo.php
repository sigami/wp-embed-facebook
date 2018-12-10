<?php
use SIGAMI\WP_Embed_FB\Plugin;
?>
<div class="wef-container wef-<?php echo $theme ?>" style="max-width: <?php echo $width ?>px" >
	<a href="<?php /** @noinspection PhpUndefinedVariableInspection */
	echo $fb_data['link'] ?>" target="_blank" rel="nofollow">
		<img src="<?php echo $fb_data['source'] ?>" width="100%" height="auto" >
	</a>

	<a class="wef-post-link" href="<?php echo $fb_data['link'] ?> " target="_blank" rel="nofollow">
		<?php echo isset($fb_data['likes']) ? '<img width="16px" height="16px" src="'.Plugin::url().'templates/images/like.png" /> '.$fb_data['likes']['summary']['total_count'].' ' : ""  ?>
		<?php echo isset($fb_data['comments']) ? ' <img width="16px" height="16px" src="'.Plugin::url().'templates/images/comments.png"/> '.$fb_data['comments']['summary']['total_count'].' ' : ""  ?>
	</a>
</div>

<?php $fb_post = /** @noinspection PhpUndefinedVariableInspection */
	$fb_data;
$linkArray = explode('_',$fb_post['id']);

?>
<div class="wef-default" style="max-width: <?php echo $width ?>px" >
	<div class="col-3 text-center">
		<a href="https://www.facebook.com/<?php echo $fb_post['from']['id'] ?>" target="_blank" rel="nofollow">
			<img src="https://graph.facebook.com/<?php echo $fb_post['from']['id'] ?>/picture" width="50px" height="50px" />
		</a>
	</div>
	<div class="col-9 pl-none">
		<p>
			<a href="https://www.facebook.com/<?php echo $fb_post['from']['id'] ?>" target="_blank" rel="nofollow">
				<span class="title"><?php echo $fb_post['from']['name'] ?></span>
			</a>
		</p>
	</div>
	<?php if(isset($fb_post['picture']) || isset($fb_post['message'])) : ?>
		<?php
		global $wp_embed;
		include('single-post.php');
		?>
	<?php endif; ?>
</div>

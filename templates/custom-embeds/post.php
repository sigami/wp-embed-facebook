<?php
use SIGAMI\WP_Embed_FB\Plugin;
use SIGAMI\WP_Embed_FB\Social_Plugins;
$fb_post = $fb_data ?>
<div class="wef-container wef-<?php echo $theme ?>" style="max-width: <?php echo $width ?>px" >
	<div class="wef-col-3 wef-text-center">
		<a href="https://www.facebook.com/<?php echo $fb_post['from']['id'] ?>" target="_blank" rel="nofollow">
			<img src="https://graph.facebook.com/<?php echo $fb_post['from']['id'] ?>/picture" width="50px" height="50px" />
		</a>
	</div>
	<div class="wef-col-9 wef-pl-none">
		<p>
			<a href="https://www.facebook.com/<?php echo $fb_post['from']['id'] ?>" target="_blank" rel="nofollow">
				<span class="wef-title"><?php echo $fb_post['from']['name'] ?></span>
			</a>
		</p>
		<div>
			<?php
			$opt = Plugin::get_option('show_like');
			if($opt === 'true') :
				echo Social_Plugins::get('like',array('href'=>'https://www.facebook.com/'.$fb_data['id'],'share'=>'true','layout'=>'button_count'));
			else :
				printf( __( '%d people like this.', 'wp-embed-facebook' ), $fb_post['likes'] );
			endif;
			?>
		</div>
	</div>
	<?php if(isset($fb_post['picture']) || isset($fb_post['message'])) : ?>
		<?php
		global $wp_embed;
		include('single-post.php');
		?>
	<?php endif; ?>
</div>

<?php
use SIGAMI\WP_Embed_FB\Plugin;
use SIGAMI\WP_Embed_FB\Social_Plugins;
use SIGAMI\WP_Embed_FB\Embed_Facebook;
?>
<div class="wef-classic aligncenter" style="max-width: <?php echo $width ?>px">
	<div class="wef-row">
		<div class="wef-col-3 wef-text-center">
			<a href="https://www.facebook.com/<?php /** @noinspection PhpUndefinedVariableInspection */
			echo $fb_data['id'] ?>" target="_blank" rel="nofollow">
				<img src="https://graph.facebook.com/<?php echo $fb_data['id'] ?>/picture" />
			</a>
		</div>
		<div class="wef-col-9 wef-pl-none">
			<a href="https://www.facebook.com/<?php echo $fb_data['id'] ?>" target="_blank" rel="nofollow">
				<span class="wef-title"><?php echo $fb_data['name'] ?></span>
			</a>
			<br>
			<div>
				<?php
				$opt = Plugin::get_option('show_like');
				if($opt === 'true') :
					echo Social_Plugins::get('like',array('href'=>'https://www.facebook.com/'.$fb_data['id'],'share'=>'true','layout'=>'button_count'));
				else :
					printf( __( '%d people like this.', 'wp-embed-facebook' ), $fb_data['likes'] );
				endif;
				?>
			</div>
			<?php if(isset($fb_data["website"])) : ?>
				<br>
				<a href="<?php echo Embed_Facebook::getwebsite($fb_data["website"]) ?>" title="<?php _e('Web Site', 'wp-embed-facebook')  ?>" target="_blank">
					<?php _e('Web Site','wp-embed-facebook') ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</div>

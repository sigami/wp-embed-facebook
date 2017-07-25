<div class="wef-default" style="max-width: <?php echo $width ?>px" >
	<?php if(isset($fb_data['cover'])) : ?>
		<div class="wef-relative-container wef-cover"><div class="wef-relative" style="background-image: url('<?php echo $fb_data['cover']['source'] ?>'); background-position-y: <?php echo $fb_data['cover']['offset_y'] ?>%" onclick="window.open('https://www.facebook.com/<?php echo $fb_data['id'] ?>', '_blank')"></div></div>
	<?php endif; ?>
	<div class="wef-row wef-pad-top">
			<div class="wef-col-2 wef-text-center">
				<a href="<?php echo $fb_data['link'] ?>" target="_blank" rel="nofollow">
					<img src="<?php echo $fb_data['picture']['data']['url'] ?>" width="50px" height="50px" />
				</a>		
			</div>
			<div class="wef-col-10 wef-pl-none">
				<a href="<?php echo $fb_data['link'] ?>" target="_blank" rel="nofollow">
					<span class="wef-title"><?php echo $fb_data['name'] ?></span>
				</a>
				<br>
				<?php
					if($fb_data['category'] == 'Musician/band'){
						echo isset($fb_data['genre']) ? $fb_data['genre'] : '';
					} else {
						_e($fb_data['category'],'wp-embed-facebook');
					}
				?><br>
				<?php if(isset($fb_data["website"]) && (strip_tags($fb_data["website"]) != '')) :  ?>
					<a  href="<?php echo WP_Embed_FB::getwebsite($fb_data["website"]) ?>" title="<?php _e('Web Site', 'wp-embed-facebook')  ?>" target="_blank">
						<?php _e('Web Site','wp-embed-facebook') ?>
					</a>						
				<?php endif; ?>
				<div style="float: right;">
					<?php
					$opt = WP_Embed_FB_Plugin::get_option('show_like');
					if($opt === 'true') :
						echo WEF_Social_Plugins::get('like',array('href'=>'https://www.facebook.com/'.$fb_data['id'],'share'=>'true','layout'=>'button_count','show-faces'=> 'false'));
					else :
						printf( __( '%d people like this.', 'wp-embed-facebook' ), $fb_data['fan_count'] );
					endif;
					?>
				</div>
			</div>
	</div>	
	<?php if(isset($fb_data['posts'])) : global $wp_embed;   ?>
		<?php foreach($fb_data['posts']['data'] as $fb_post) : ?>
			<?php if(isset($fb_post['picture']) || isset($fb_post['message'])) : ?>
				<?php include('single-post.php') ?>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
<?php
use SIGAMI\WP_Embed_FB\Plugin;
use SIGAMI\WP_Embed_FB\Social_Plugins;
?>
<div class="wef-container wef-<?php echo $theme ?>" style="max-width: <?php echo $width ?>px">
	<div class="wef-row">
			<div class="wef-col-3 wef-text-center">
				<a href="https://www.facebook.com/<?php /** @noinspection PhpUndefinedVariableInspection */
				echo $fb_data['id'] ?>" target="_blank" rel="nofollow">
					<img src="https://graph.facebook.com/<?php echo $fb_data['id'] ?>/picture" />
				</a>		
			</div>
			<div class="wef-col-9 wef-pl-none">
				<p>
					<a href="https://www.facebook.com/<?php echo $fb_data['id'] ?>" target="_blank" rel="nofollow">
						<span class="wef-title"><?php echo $fb_data['name'] ?></span>
					</a>
				</p>
				<div>
					<?php
					$opt = Plugin::get_option('show_follow');
					if($opt === 'true') :
						Social_Plugins::get('follow',array('href'=>'https://www.facebook.com/'.$fb_data['id']));
					endif;
					?>
				</div>
			</div>
	</div>	
</div>


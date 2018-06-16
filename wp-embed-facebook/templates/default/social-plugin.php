<?php
use SIGAMI\WP_Embed_FB\Plugin;
use SIGAMI\WP_Embed_FB\Social_Plugins;
?>
<div class="wef-measure" style="max-width: <?php echo $width ?>px;"></div>
<?php
switch ( $type ) {
	case 'page' :
		/** @noinspection PhpUndefinedVariableInspection */
		echo Social_Plugins::get('page',array('href'=>'https://www.facebook.com/' . $fb_data['link'],'width'=>$width));
		break;
	case 'video' :
		if ( Plugin::get_option( 'video_as_post' ) == 'true' ) /** @noinspection PhpUndefinedVariableInspection */ {
			echo Social_Plugins::get('post',array('href'=>'https://www.facebook.com/' . $fb_data['link'],'width'=>$width));
		} else {
			/** @noinspection PhpUndefinedVariableInspection */
			echo Social_Plugins::get('video',array('href'=>'https://www.facebook.com/' . $fb_data['link'],'width'=>$width));
		}

		break;
	//case 'photo' :
	//case 'post' :
	default:
		/** @noinspection PhpUndefinedVariableInspection */
		echo Social_Plugins::get('post',array('href'=>'https://www.facebook.com/' . $fb_data['link'],'width'=>$width));
		break;
}
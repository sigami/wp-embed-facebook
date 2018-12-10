<?php
use SIGAMI\WP_Embed_FB\Social_Plugins;
if ( post_password_required() ) {
	return;
}
echo "<style>.fb_iframe_widget iframe{width: 100% !important;}</style>";
echo Social_Plugins::get('comments',array('href'=>wp_get_shortlink(get_queried_object_id()),'width'=>'100%'));
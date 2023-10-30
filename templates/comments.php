<?php
use SIGAMI\WP_Embed_FB\Social_Plugins;
if ( post_password_required() ) {
	return;
}
echo '<style>.fb_iframe_widget iframe{width: 100% !important;}</style>';
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in Social_Plugins::get().
echo Social_Plugins::get(
	'comments',
	[
		'href'  => esc_url_raw( wp_get_shortlink( get_queried_object_id() ) ),
		'width' => '100%',
	]
);

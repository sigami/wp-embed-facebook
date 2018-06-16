<?php
use SIGAMI\WP_Embed_FB\Plugin;
use SIGAMI\WP_Embed_FB\Helpers;
$start_time_format = Plugin::get_option('event_start_time_format');
$old_time_zone = date_default_timezone_get();
if(Plugin::get_option('ev_local_tz') == 'true'){
	$timezone = Helpers::get_timezone();
} else {
	$timezone = isset( $fb_data['timezone'] ) ? $fb_data['timezone'] : Helpers::get_timezone();
}
date_default_timezone_set( $timezone );
/** @noinspection PhpUndefinedVariableInspection */
$start_time = date_i18n( $start_time_format, strtotime( $fb_data['start_time'] ) );
date_default_timezone_set( $old_time_zone );
?>
<div class="wef-elegant" style="max-width: <?php echo $width ?>px">
	<?php if(isset($fb_data['cover'])) : ?>
		<div class="wef-relative-container wef-cover"><div class="wef-relative" style="background-image: url('<?php echo $fb_data['cover']['source'] ?>'); background-position-y: <?php echo $fb_data['cover']['offset_y'] ?>%" onclick="window.open('https://www.facebook.com/<?php echo $fb_data['id'] ?>', '_blank')"></div></div>
	<?php endif; ?>
	<div class="wef-row wef-pad-top">
		<div class="wef-col-12">
			<a href="https://www.facebook.com/<?php echo $fb_data['id'] ?>" target="_blank" rel="nofollow">
				<span class="wef-title"><?php echo $fb_data['name'] ?></span>
			</a>
			<p><?php echo $start_time ?></p>
			<p>
				<?php
				if ( isset( $fb_data['place']['id'] ) ) {
					_e( '@ ', 'wp-embed-facebook' );
					echo '<a href="https://www.facebook.com/' . $fb_data['place']['id'] . '" target="_blank">' . $fb_data['place']['name'] . '</a>';
					if(isset($fb_data['place']['location']) && !empty($fb_data['place']['location']) ){
						$location = $fb_data['place']['location'];
						$street = (isset($location['street']) && !empty($location['street']) ) ? $location['street'] : '';
						$city = (isset($location['city']) && !empty($location['city']) ) ? $location['city'].',' : '';
						$country = (isset($location['country']) && !empty($location['country']) ) ? $location['country'].'.' : '';
						echo "<span class=\"event_address\"> $street $city $country</span>";
					}
				} else {
					echo isset( $fb_data['place']['name'] ) ? __( '@ ', 'wp-embed-facebook' ) . $fb_data['place']['name'] : '';
				}
				?>
			</p>
			<?php if(isset($fb_data['ticket_uri']) && !empty($fb_data['ticket_uri']) ) : ?>
                <p class="wef-text-right"><a class="wef-button" href="<?php echo $fb_data['ticket_uri'] ?>"><?php _e('Get Tickets', 'wp-embed-facebook') ?></a>
                </p>
			<?php endif; ?>
		</div>
	</div>
</div>
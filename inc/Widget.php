<?php
/**
 * Register widget.
 *
 * @author  Miguel Sirvent
 * @package Magic Embeds
 */

namespace SIGAMI\WP_Embed_FB;

// If this file is called directly, abort.
use WP_Widget;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Adds WEF_Widget widget.
 */
class Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'wef_widget', // Base ID
			esc_html__( '$fb_path', 'wp-embed-facebook' ), // Name
			[ 'description' => esc_html__( 'Shortcode widget', 'wp-embed-facebook' ) ] // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $instance['title'] ) ) {
			/**
			 * Allow filtering widget title.
			 *
			 * @param string $title Title of widget.
			 * @since unknown
			 */
			$title = apply_filters( 'widget_title', $instance['title'] );

			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}
		echo do_shortcode( $instance['shortcode'] );
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Back-end widget form.
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @return true
	 *@see WP_Widget::form()
	 */
	public function form( $instance ): bool {
		$title     = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$shortcode = ! empty( $instance['shortcode'] ) ? $instance['shortcode'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
									<?php
									esc_attr_e(
										'Title:',
										'wp-embed-facebook'
									);
									?>
					</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
					value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'shortcode' ) ); ?>">
									<?php
									esc_attr_e(
										'Shortcode:',
										'wp-embed-facebook'
									);
									?>
					<br>
				<small>Example: [embedfb https://www.facebook... ] or [fb_plugin like]</small>
			</label>
			<input class="widefat"
					id="<?php echo esc_attr( $this->get_field_id( 'shortcode' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'shortcode' ) ); ?>"
					type="text" value="<?php echo esc_attr( $shortcode ); ?>">
		</p>
		<?php
		return true;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ): array {
		$instance          = [];
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_kses_post( $new_instance['title'] ) : '';

		$instance['shortcode'] = ( ! empty( $new_instance['shortcode'] ) ) ? wp_kses_post( $new_instance['shortcode'] ) : '';

		return $instance;
	}
}

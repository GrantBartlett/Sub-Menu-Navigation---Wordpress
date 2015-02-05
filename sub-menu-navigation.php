<?php

/*
 * Plugin Name: Sub Menu Navigation
 * Plugin URI: http://grant-bartlett.com
 * Description: Provides a theme-independent widget to display the child pages of the current page.
 * Author: Grant Bartlett
 * Author URI: http://grant-bartlett.com/
 * Version: 1.0.0
 */

add_action( 'widgets_init', function () {
	register_widget( 'Sub_Menu_Navigation' );
} );

class Sub_Menu_Navigation extends WP_Widget {

	/**
	 * Set up widgets name etc
	 */
	function __construct() {
		parent::__construct(
			'Sub_Menu_Navigation', // Base ID
			__( 'Sub Menu Navigation', 'text_domain' ), // Name
			array( 'description' => __( 'Outputting sub menu navigation', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// Outputs content of the widget

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		echo __( ' Hello World!', 'text_domain' );

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * Outputs array $instance The Widget Options
	 *
	 * @param array $instance
	 *
	 * @return form
	 */
	public function form( $instance ) {
		// Outputs the options form on admin
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
	<?php
	}

	/**
	 * Sanitize Widget form values as they are saved
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		// Processes widget options to be saved
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
}


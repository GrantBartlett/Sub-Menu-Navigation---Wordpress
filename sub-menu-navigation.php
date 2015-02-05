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
});

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
		global $post;

		/** @see get_pages() **/
		$pageArgs = array(
			'child_of'    => $post->ID,
			'parent'      => $post->ID,
			'sort_order'  => 'asc',
			'post_type'   => 'page',
			'post_status' => 'publish'
		);

		$childPageList = get_pages($pageArgs);

		if($post->post_parent ) {
			$pageArgs['child_of'] = $pageArgs['parent'] = $post->post_parent;
			$childPageList = get_pages($pageArgs);
		}

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		if ( ! empty( $instance['class'] ) ) {
			echo apply_filters( 'widget_class', '', $instance['class'] = "class='". $instance['class'] . "'");
		}

		echo "<ul ". $instance['class'] .">";
		foreach ( $childPageList as $page ) {
			switch ( $page->ID ) {
				case $post->ID:
					$pageIsActive = "class=\"active\"";
					break;
				default:
					$pageIsActive = "";
					break;
			}
			echo "<li><a href='" . get_permalink( $page->ID ) . "' ". $pageIsActive ." title='". get_the_title( $page->ID ) ."'>" . get_the_title( $page->ID ) . "</a></li>";
		}
		echo "</ul>";

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
		$class = ! empty( $instance['class'] ) ? $instance['class'] : __( 'Add a class', 'text_domain' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'class' ); ?>"><?php _e( 'Class:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'class' ); ?>" name="<?php echo $this->get_field_name( 'class' ); ?>" type="text" value="<?php echo esc_attr( $class ); ?>">
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
		$instance = [];

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['class'] = ( ! empty( $new_instance['class'] ) ) ? strip_tags( $new_instance['class'] ) : '';

		return $instance;
	}
}


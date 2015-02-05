<?php

/*
 * Plugin Name: Sub Menu Navigation
 * Plugin URI: https://github.com/GrantBartlett/Sub-Menu-Navigation-Wordpress
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

		// Get Current Post Type
		$curPostType = get_post_type();


		/**
		 * If field isn't empty, use widget fields
		 */
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		if ( ! empty( $instance['class'] ) ) {
			echo apply_filters( 'widget_class', '', $instance['class'] = "class='". $instance['class'] . "'");
		}

		if ( ! empty( $instance['types'] ) ) {
			echo apply_filters( 'widget_class', '', $instance['types'] . "'" );
		}


		/**
		 * List of @see get_pages() args
		 **/
		$pageArgs = array(
			'sort_column' => 'menu_order',
			'post_type'   => 'page',
			'post_status' => 'publish'
		);

		$childPageList = get_pages($pageArgs);


		/**
		 * Store page ID's into new array
		 * @pageIdArr
		 */
		$pageIdArr = [];

		foreach ( $childPageList as $page => $v ) {
			$pageIdArr['id'][] = $v->ID;
		}


		/**
		 * Find position of active page in @pageIdArr
		 */
		$current = array_search( get_the_ID(), $pageIdArr['id'] );


		/**
		 * Find previous/next post
		 * @see get_previous_post()
		 * @see get_next_post()
		 */
		$prev_post = get_previous_post();
		$next_post = get_next_post();


		/**
		 * Begin widget content
		 */
		echo $args['before_widget'];


		/**
		 * Check through post types and generate appropriately.
		 * @curPostType
		 */
		switch ( $curPostType ) {

			// Retrieve and Post Types Entered into Widget Admin form

			case $instance['types']:
				echo "<ul " . $instance['class'] . ">";
				for ( $i = - 1; $i < 3; $i ++ ) {
					switch ( $pageIdArr['id'][ $current + $i ] ) {
						case get_the_ID():
							$pageIsActive = "class=\"active\"";
							break;

						default:
							$pageIsActive = '';
					}
					echo "<li><a href='" . get_permalink( $pageIdArr['id'][ $current + $i ] ) . "' " . $pageIsActive . " title='" . get_the_title( $pageIdArr['id'][ $current + $i ] ) . "'>" . get_the_title( $pageIdArr['id'][ $current + $i ] ) . "</a></li>";
				}
				echo "</ul>";
				break;

			default:
				// Simple Navigation using prev, current, and next post if no post type matched
				echo "<ul " . $instance['class'] . ">"; ?>
					<li><a href="<?php echo get_permalink($prev_post->ID); ?>" title="<?php echo $prev_post->post_title; ?>"><?php echo $prev_post->post_title; ?></a></li>
					<li><a href="<?php echo get_permalink($post->ID); ?>" title="<?php echo $post->post_title; ?>" class="active"><?php echo $post->post_title; ?></a></li>
					<li><a href="<?php echo get_permalink($next_post->ID); ?>" title="<?php echo $next_post->post_title; ?>"><?php echo $next_post->post_title; ?></a></li>
				<?php echo "</ul>";
				break;
		}


		/**
		 * End widget content
		 */
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
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( '', 'text_domain' );
		$class = ! empty( $instance['class'] ) ? $instance['class'] : __( '', 'text_domain' );
		$types = ! empty( $instance['types'] ) ? $instance['types'] : __( '', 'text_domain' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'class' ); ?>"><?php _e( 'Class:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'class' ); ?>" name="<?php echo $this->get_field_name( 'class' ); ?>" type="text" value="<?php echo esc_attr( $class ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'types' ); ?>"><?php _e( 'Post Types:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'class' ); ?>" name="<?php echo $this->get_field_name( 'types' ); ?>" type="text" value="<?php echo esc_attr( $types ); ?>">
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
		$instance['types'] = ( ! empty( $new_instance['types'] ) ) ? strip_tags( $new_instance['types'] ) : '';

		return $instance;
	}
}
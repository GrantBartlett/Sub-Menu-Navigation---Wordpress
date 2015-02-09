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


        // Empty left/right input form fields for arrows
        $arrLeft = "";
        $arrRight = "";
        $pageIsActive = false;


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

        if ( ! empty( $instance['arrLeft'] ) ) {
            echo apply_filters( 'widget_class', '', $instance['arrLeft'] . "'" );
            $arrLeft = "<li><i class='". $instance['arrLeft'] ."'></i></li>";
        }

        if ( ! empty( $instance['arrRight'] ) ) {
            echo apply_filters( 'widget_class', '', $instance['arrRight'] . "'" );
            $arrRight = "<li><i class='". $instance['arrRight'] ."'></i></li>";
        }

        if ( ! empty( $instance['exclude'] ) ) {
            $exclude = $instance['exclude'];
        }


        /**
         * List of @see get_pages() args
         **/
        $pageArgs = array(
            'sort_column' => 'menu_order',
            'post_type'   => 'page',
            'post_status' => 'publish',
            'exclude' => $exclude
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
                // Begin list
                echo "<ul " . $instance['class'] . ">";
                echo $arrLeft;

                for ( $i = - 1; $i < 2; $i ++ ) :?>

                    <?php
                    if ( $pageIdArr['id'][ $current + $i ] == get_the_ID() ) {
                        $pageIsActive = true;
                    } else {
                        $pageIsActive = false;
                    }
                    ?>

                    <?php if ( ! empty( $pageIdArr['id'][ $current + $i ] ) ) : ?>
                        <?php if ( $pageIsActive ): ?><?php echo $arrLeft; ?><?php endif; ?>

                        <li>
                            <a href="<?php echo get_permalink( $pageIdArr['id'][ $current + $i ] ) ?>" <?php if ( $pageIsActive ) {
                                echo "class=\"active\"";
                            } ?> title="<?php echo get_the_title( $pageIdArr['id'][ $current + $i ] ) ?>"><?php echo get_the_title( $pageIdArr['id'][ $current + $i ] ) ?></a>
                        </li>

                        <?php if ( $pageIsActive ): ?>
                            <?php echo $arrRight; ?>
                        <?php endif; ?>

                    <?php endif; ?>
                <?php endfor; ?>

                <?php
                if( end($pageIdArr['id']) != get_the_ID()) {
                    echo $arrRight;
                }

                // End list
                echo "</ul>";
                break;

            default:
                // Simple Navigation using prev, current, and next post if no post type matched
                echo "<ul " . $instance['class'] . ">"; ?>

                <?php if ( ! empty( $prev_post->ID ) ): ?>
                    <?php echo $arrLeft; ?>
                    <li><a href="<?php echo get_permalink($prev_post->ID); ?>" title="<?php echo get_the_title( $prev_post->ID ); ?>"><?php echo get_the_title( $prev_post->ID ); ?></a></li>
                    <?php echo $arrLeft; ?>
                <?php endif; ?>

                    <li><a href="<?php echo get_permalink($post->ID); ?>" class="active" title="<?php echo get_the_title( $post->ID ); ?>"><?php echo get_the_title( $post->ID ); ?></a></li>

                <?php if ( ! empty( $next_post->ID ) ): ?>
                    <?php echo $arrRight; ?>
                    <li><a href="<?php echo get_permalink($next_post->ID); ?>" title="<?php echo get_the_title( $next_post->ID ); ?>"><?php echo get_the_title( $next_post->ID ); ?></a></li>
                    <?php echo $arrRight; ?>
                <?php endif; ?>

                <?php echo "</ul>";
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
        $arrRight = ! empty( $instance['arrRight'] ) ? $instance['arrRight'] : __( '', 'text_domain' );
        $arrLeft = ! empty( $instance['arrLeft'] ) ? $instance['arrLeft'] : __( '', 'text_domain' );
        $exclude = ! empty( $instance['exclude'] ) ? $instance['exclude'] : __( '', 'text_domain' );
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
            <label for="<?php echo $this->get_field_id( 'types' ); ?>"><?php _e( 'Post Type?' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'class' ); ?>" name="<?php echo $this->get_field_name( 'types' ); ?>" type="text" value="<?php echo esc_attr( $types ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'arrLeft' ); ?>"><?php _e( 'Left Arrow <small>(Font Awesome)</small>' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'arrLeft' ); ?>" name="<?php echo $this->get_field_name( 'arrLeft' ); ?>" type="text" value="<?php echo esc_attr( $arrLeft ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'arrRight' ); ?>"><?php _e( 'Right Arrow <small>(Font Awesome)</small>' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'arrRight' ); ?>" name="<?php echo $this->get_field_name( 'arrRight' ); ?>" type="text" value="<?php echo esc_attr( $arrRight ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><?php _e( 'Exclude Page IDs' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" type="text" value="<?php echo esc_attr( $exclude ); ?>">
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
     * sanitize_text_field()
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        // Processes widget options to be saved
        $instance = [];

        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['class'] = ( ! empty( $new_instance['class'] ) ) ? strip_tags( $new_instance['class'] ) : '';
        $instance['types'] = ( ! empty( $new_instance['types'] ) ) ? strip_tags( $new_instance['types'] ) : '';
        $instance['arrLeft'] = ( ! empty( $new_instance['arrLeft'] ) ) ? strip_tags($new_instance['arrLeft'])  : '';
        $instance['arrRight'] = ( ! empty( $new_instance['arrRight'] ) ) ?  strip_tags($new_instance['arrRight'])  : '';
        $instance['exclude'] = ( ! empty( $new_instance['exclude'] ) ) ?  strip_tags($new_instance['exclude'])  : '';

        return $instance;
    }
}
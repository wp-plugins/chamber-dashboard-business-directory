<?php

// ------------------------------------------------------------------------
// SEARCH WIDGET
// ------------------------------------------------------------------------

// Add function to widgets_init that'll load our widget.

function cdash_search_widget() {
	register_widget( 'Cdash_Search_Widget' );
}

add_action( 'widgets_init', 'cdash_search_widget' );

class Cdash_Search_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function Cdash_Search_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'cdash', 'description' => __('Display a search form for the business directory', 'cdash') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'cdash-search' );

		/* Create the widget. */
		$this->WP_Widget( 'cdash-search', __('Business Directory Search', 'cdash'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$results_page = $instance['results_page'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Display name from widget settings if one was input. */
		echo do_shortcode('[business_search_form results_page="'.$results_page.'"]');

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['results_page'] = $new_instance['results_page'];

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Search the Business Directory', 'cdash'), );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'cdash'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<!-- Results Page: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'results_page' ); ?>"><?php _e('<b>Page to display search results:</b><br />(this page must contain the [business_search_results] shortcode)', 'cdash'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'results_page' ); ?>" name="<?php echo $this->get_field_name( 'results_page' ); ?>" class="widefat" style="width:100%;">
				<?php $pagelist = get_posts( 'post_type=page&posts_per_page=-1' );
				foreach( $pagelist as $page ) { ?>
					<option value="<?php echo $page->post_name; ?>" <?php if ( $page->post_name == $instance['results_page'] ) echo 'selected="selected"'; ?>><?php echo $page->post_title; ?></option>
				<?php } ?>
			</select>
		</p>

	<?php
	}
}

// ------------------------------------------------------------------------
// FEATURED BUSINESS WIDGET
// ------------------------------------------------------------------------




?>
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



// ------------------------------------------------------------------------
// BUSINESS CATEGORIES WIDGET
// ------------------------------------------------------------------------

// Add function to widgets_init that'll load our widget.

function cdash_business_categories() {
	register_widget( 'Cdash_Business_Categories_Widget' );
}

add_action( 'widgets_init', 'cdash_business_categories' );

class Cdash_Business_Categories_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function Cdash_Business_Categories_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'cdash', 'description' => __('Display a list of business categories', 'cdash') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'cdash-business-categories' );

		/* Create the widget. */
		$this->WP_Widget( 'cdash-business-categories', __('Business Categories', 'cdash'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$orderby = $instance['orderby'];
		$showcount = $instance['showcount'];
		$hierarchical = $instance['hierarchical'];
		$hide_empty = $instance['hide_empty'];
		$child_of = $instance['child_of'];
		$exclude = $instance['exclude'];

		$args = "orderby='" . $orderby . "' show_count='" . $showcount . "' hierarchical='" . $hierarchical . "' hide_empty='" . $hide_empty . "' child_of='" . $child_of . "' exclude='" . $exclude . "'";;
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Display name from widget settings if one was input. */
		echo do_shortcode('[business_categories ' . $args . ']');

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
		$instance['orderby'] = $new_instance['orderby'];
		$instance['showcount'] = $new_instance['showcount'];
		$instance['hierarchical'] = $new_instance['hierarchical'];
		$instance['hide_empty'] = $new_instance['hide_empty'];
		$instance['child_of'] = $new_instance['child_of'];
		$instance['exclude'] = $new_instance['exclude'];

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Business Categories', 'cdash'), );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'cdash'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<!-- Order By: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e('<b>Order by:</b>', 'cdash'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>" class="widefat" style="width:100%;">
				<option value="name" <?php if ( 'name' == $instance['orderby'] ) echo 'selected="selected"'; ?>><?php _e( 'Name', 'cdash' ); ?></option>
				<option value="count" <?php if ( 'count' == $instance['orderby'] ) echo 'selected="selected"'; ?>><?php _e( 'Count', 'cdash' ); ?></option>
			</select>
		</p>

		<!-- Show Count: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'showcount' ); ?>"><?php _e('<b>Show Number of Businesses in Category:</b>', 'cdash'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'showcount' ); ?>" name="<?php echo $this->get_field_name( 'showcount' ); ?>" class="widefat" style="width:100%;">
				<option value="0" <?php if ( '0' == $instance['showcount'] ) echo 'selected="selected"'; ?>><?php _e( 'No', 'cdash' ); ?></option>
				<option value="1" <?php if ( '1' == $instance['showcount'] ) echo 'selected="selected"'; ?>><?php _e( 'Yes', 'cdash' ); ?></option>
			</select>
		</p>

		<!-- Hierarchical: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>"><?php _e('<b>Display Hierarchy:</b>', 'cdash'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>" class="widefat" style="width:100%;">
				<option value="0" <?php if ( '0' == $instance['hierarchical'] ) echo 'selected="selected"'; ?>><?php _e( 'No', 'cdash' ); ?></option>
				<option value="1" <?php if ( '1' == $instance['hierarchical'] ) echo 'selected="selected"'; ?>><?php _e( 'Yes', 'cdash' ); ?></option>
			</select>
		</p>

		<!-- Hide Empty: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>"><?php _e('<b>Hide Empty Categories:</b>', 'cdash'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" class="widefat" style="width:100%;">
				<option value="0" <?php if ( '0' == $instance['hide_empty'] ) echo 'selected="selected"'; ?>><?php _e( 'No', 'cdash' ); ?></option>
				<option value="1" <?php if ( '1' == $instance['hide_empty'] ) echo 'selected="selected"'; ?>><?php _e( 'Yes', 'cdash' ); ?></option>
			</select>
		</p>

		<!-- Child Of: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'child_of' ); ?>"><?php _e('<b>Display only children of:</b>', 'cdash'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'child_of' ); ?>" name="<?php echo $this->get_field_name( 'child_of' ); ?>" class="widefat" style="width:100%;">
				<option value=""> </option>
				<?php $termlist = get_terms( 'business_category', 'hide_empty=false' );
				foreach( $termlist as $term ) { ?>
					<option value="<?php echo $term->term_id; ?>" <?php if ( $term->term_id == $instance['child_of'] ) echo 'selected="selected"'; ?>><?php echo $term->name; ?></option>
				<?php } ?>
			</select>
		</p>

		<!-- Exclude: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><?php _e('<b>Exclude:</b>', 'cdash'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" class="widefat" style="width:100%;">
				<option value=""> </option>
				<?php $termlist = get_terms( 'business_category', 'hide_empty=false' );
				foreach( $termlist as $term ) { ?>
					<option value="<?php echo $term->term_id; ?>" <?php if ( $term->term_id == $instance['exclude'] ) echo 'selected="selected"'; ?>><?php echo $term->name; ?></option>
				<?php } ?>
			</select>
		</p>



	<?php
	}
}

?>
<?php

if ( ! defined( 'ABSPATH' ) ) exit;

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
		parent::__construct( 'cdash-search', __('Business Directory Search', 'cdash'), $widget_ops, $control_ops );
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

// enqueue scripts and styles this widget needs 
function cdash_widget_enqueue_scripts( $hook ) {
    if ( 'widgets.php' == $hook ) {
        wp_enqueue_style( 'chosen', plugin_dir_url(__FILE__) . 'css/chosen.css' );
        wp_enqueue_script( 'chosen', plugin_dir_url(__FILE__) . 'js/chosen.jquery.min.js', array( 'jquery' ) );
    }
}
add_action( 'admin_enqueue_scripts', 'cdash_widget_enqueue_scripts' );

function cdash_featured_business() {
	register_widget( 'Cdash_Featured_Business_Widget' );
}

add_action( 'widgets_init', 'cdash_featured_business' );

class Cdash_Featured_Business_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function Cdash_Featured_Business_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'cdash', 'description' => __( 'Display featured businesses from the Chamber Dashboard business directory.', 'cdash' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'cdash-featured' );

		/* Create the widget. */
		parent::__construct( 'cdash-featured', __( 'Featured Business', 'cdash' ), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Display widget */
		// find the businesses
		$args = array( 
	        'post_type' => 'business',
	        'posts_per_page' => $instance['how_many'],
	        'orderby' => 'rand',
        );

        if( 'select-manual' == $instance['select_method'] && isset( $instance['business'] ) && !empty( $instance['business'] ) ) {
        	// add selected businesses to args
        	$args['post__in'] = $instance['business'];
        } elseif( 'select-criteria' == $instance['select_method'] ) {
        	// add taxonomies to args
        	$args['tax_query'] = array(
		    	'relation' => 'AND'
		    );
			if( isset( $instance['category'] ) && !empty( $instance['category'] ) ) {
				$args['tax_query'][] = array(
			        'taxonomy' => 'business_category',
			        'field' => 'id',
			        'terms' => $instance['category'],
			        'include_children' => false,
			      );
			}
			if( isset( $instance['private'] ) && !empty( $instance['private'] ) ) {
				$args['tax_query'][] = array(
			        'taxonomy' => 'private_category',
			        'field' => 'id',
			        'terms' => $instance['private'],
			        'include_children' => false,
			      );
			}
			if( isset( $instance['level'] ) && !empty( $instance['level'] ) ) {
				$args['tax_query'][] = array(
			        'taxonomy' => 'membership_level',
			        'field' => 'id',
			        'terms' => $instance['level'],
			        'include_children' => false,
			      );
			}
			if( isset( $instance['status'] ) && !empty( $instance['status'] ) ) {
				$args['tax_query'][] = array(
			        'taxonomy' => 'membership_status',
			        'field' => 'id',
			        'terms' => $instance['status'],
			        'include_children' => false,
			      );
			}
        } else {
        	_e( 'You must choose whether to manually select businesses or randomly display businesses based on certain criteria.  Edit this widget to update these settings.', 'cdash' );
        }

        $featured_business = new WP_Query( $args );

		// The Loop
		if ( $featured_business->have_posts() ) {
			while ( $featured_business->have_posts() ) : $featured_business->the_post(); ?>
				<div class="cdash-featured-business">
					<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
					<?php // make location/address metabox data available
					global $buscontact_metabox;
					$contactmeta = $buscontact_metabox->the_meta();
					$post_id = get_the_id();
					if( in_array( 'thumbnail', $instance['display'] ) ) {
						the_post_thumbnail( 'thumbnail' );
					}
					if( in_array( 'logo', $instance['display'] ) ) {
						global $buslogo_metabox;
						$logometa = $buslogo_metabox->the_meta();
						if( isset( $logometa['buslogo'] ) ) {
							echo wp_get_attachment_image( $logometa['buslogo'], 'full' );
						}
					}
					if( in_array( 'description', $instance['display'] ) ) {
						the_content();
					}
					if( in_array( 'excerpt', $instance['display'] ) ) {
						the_excerpt();
					}
					if( isset( $contactmeta['location'] ) ) {
						$locations = $contactmeta['location'];
						foreach( $locations as $location ) {
							if( in_array( 'location_name', $instance['display'] ) && isset( $location['altname'] ) ) {
								echo '<strong>' . $location['altname'] . '</strong><br />';
							}
							if( in_array( 'address', $instance['display'] ) ) {
								echo cdash_display_address( $location );
							}
						}
					}
					if( in_array( 'url', $instance['display'] ) && isset( $location['url'] ) ) {
						echo cdash_display_url( $location['url'] );
					}
					if( in_array( 'phone', $instance['display'] ) && isset( $location['phone'] ) ) {
						echo cdash_display_phone_numbers( $location['phone'] );
					}
					if( in_array( 'email', $instance['display'] ) && isset( $location['email'] ) ) {
						echo cdash_display_email_addresses( $location['email'] );
					}
					if( in_array( 'social', $instance['display'] ) ) {
						echo cdash_display_social_media( $post_id );
					}
					if( in_array( 'level', $instance['display'] ) ) {
						echo cdash_display_membership_level( $post_id );
					}
					if( in_array( 'category', $instance['display'] ) ) {
						echo cdash_display_business_categories( $post_id );
					} ?>
				</div>
			<?php endwhile;
		} else {
			_e( 'No featured business found', 'cdash' ); 
		}

		// Reset Post Data
		wp_reset_postdata();

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
		$instance['how_many'] = strip_tags( $new_instance['how_many'] );
		$instance['select_method'] = esc_sql( $new_instance['select_method'] );
		$instance['business'] = esc_sql( $new_instance['business'] );
		$instance['category'] = esc_sql( $new_instance['category'] );
		$instance['private'] = esc_sql( $new_instance['private'] );
		$instance['level'] = esc_sql( $new_instance['level'] );
		$instance['status'] = esc_sql( $new_instance['status'] );
		$instance['display'] = esc_sql( $new_instance['display'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __( 'Featured Business', 'cdash' ), 'how_many' => 1, 'display' => array( 'address', 'url' ) );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cdash' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<!-- Number of businesses: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'how_many' ); ?>"><?php _e( '# of Businesses to Display:', 'cdash' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'how_many' ); ?>" style="width: 4em;" type="number" name="<?php echo $this->get_field_name( 'how_many' ); ?>" value="<?php echo $instance['how_many']; ?>" />
		</p>

		<h4><?php _e( 'Select Which Business(es) to Display', 'cdash' ); ?></h4>
			<input class="radio" type="radio" <?php checked( $instance['select_method'], 'select-manual' ); ?> id="select-manual" name="<?php echo $this->get_field_name( 'select_method' ); ?>" value="select-manual" /> 
			<label for="select-manual"><b><?php _e( 'Manually Select Business(es):', 'cdash' ); ?></b></label>
			<div style="margin-left:2em;">
	 			<?php // Select individual businesses
	            printf (
	                '<select multiple name="%s[]" id="%s" class="cdash-select-business" data-placeholder="%s">',
	                $this->get_field_name('business'),
	                $this->get_field_id('business'),
	                __( 'Select Business(es)', 'cdash' )
	            );
	            $args = array(
					'post_type' => 'business',
					'posts_per_page' => '-1',
					); 
				$businesses = get_posts( $args );
	            foreach( $businesses as $business ) {
	            	$selected = '';
	            	if( is_array( $instance['business'] ) && in_array( $business->ID, $instance['business'] ) ) {
	            		$selected = 'selected="selected"';
	            	}
	                printf(
	                    '<option value="%s" %s>%s</option>',
	                    $business->ID,
	                    $selected,
	                    $business->post_title
	                );
	            }
	            echo '</select>'; ?>
			</div>

			<input class="radio" type="radio" <?php checked( $instance['select_method'], 'select-criteria' ); ?> id="select-criteria" name="<?php echo $this->get_field_name( 'select_method' ); ?>" value="select-criteria" /> 
			<label for="select-criteria"><b><?php _e( 'Randomly Select Business(es) Based on These Criteria:', 'cdash' ); ?></b></label>
			<div style="margin-left:2em;">
				<p>
					<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category:', 'cdash' ); ?></label> 
					<?php // Select categories
		            printf (
		                '<select multiple name="%s[]" id="%s" class="cdash-select-category" data-placeholder="%s">',
		                $this->get_field_name('category'),
		                $this->get_field_id('category'),
		                __( 'Select Categories', 'cdash' )
		            );
					$category_list = get_terms( 'business_category', 'hide_empty=true' );
					foreach( $category_list as $term ) {
		                printf(
		                    '<option value="%s" %s>%s</option>',
		                    $term->term_id,
		                    in_array( $term->term_id, $instance['category']) ? 'selected="selected"' : '',
		                    $term->name
		                );
		            }
		            echo '</select>'; ?>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'private' ); ?>"><?php _e( 'Private Category:', 'cdash' ); ?></label> 
					<?php // Select private categories
		            printf (
		                '<select multiple name="%s[]" id="%s" class="cdash-select-private" data-placeholder="%s">',
		                $this->get_field_name('private'),
		                $this->get_field_id('private'),
		                __( 'Select Private Categories', 'cdash' )
		            );
					$category_list = get_terms( 'private_category', 'hide_empty=true' );
					foreach( $category_list as $term ) {
		                printf(
		                    '<option value="%s" %s>%s</option>',
		                    $term->term_id,
		                    in_array( $term->term_id, $instance['private']) ? 'selected="selected"' : '',
		                    $term->name
		                );
		            }
		            echo '</select>'; ?>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'level' ); ?>"><?php _e( 'Membership Level:', 'cdash' ); ?></label> 
					<?php // Select membership level
		            printf (
		                '<select multiple name="%s[]" id="%s" class="cdash-select-level" data-placeholder="%s">',
		                $this->get_field_name('level'),
		                $this->get_field_id('level'),
		                __( 'Select Membership Levels', 'cdash' )
		            );
					$category_list = get_terms( 'membership_level', 'hide_empty=true' );
					foreach( $category_list as $term ) {
		                printf(
		                    '<option value="%s" %s>%s</option>',
		                    $term->term_id,
		                    in_array( $term->term_id, $instance['level']) ? 'selected="selected"' : '',
		                    $term->name
		                );
		            }
		            echo '</select>'; ?>
				</p>
				<?php $active_plugins = wp_get_active_and_valid_plugins();
			    $plugin_names = array();
			    foreach( $active_plugins as $plugin ) {
			        $plugin_names[] = substr($plugin, strrpos($plugin, '/') + 1);
			    }
			    if( in_array( 'cdash-member-manager.php', $plugin_names ) ) { ?>
				    <p>
						<label for="<?php echo $this->get_field_id( 'status' ); ?>"><?php _e( 'Membership Status:', 'cdash' ); ?></label> 
						<?php // Select membership status
			            printf (
			                '<select multiple name="%s[]" id="%s" class="cdash-select-status" data-placeholder="%s">',
			                $this->get_field_name('status'),
			                $this->get_field_id('status'),
			                __( 'Select Membership Statuses', 'cdash' )
			            );
						$category_list = get_terms( 'membership_status', 'hide_empty=true' );
						foreach( $category_list as $term ) {
			                printf(
			                    '<option value="%s" %s>%s</option>',
			                    $term->term_id,
			                    in_array( $term->term_id, $instance['status']) ? 'selected="selected"' : '',
			                    $term->name
			                );
			            }
			            echo '</select>'; ?>
					</p>
			    <?php }?>
			</div>
		<h4><?php _e( 'Select What Information to Display', 'cdash' ); ?></h4>
		<p>
			<?php $display_opts = array(
				'description' => __( 'Description', 'cdash' ),
				'excerpt' => __( 'Excerpt', 'cdash' ),
				'location_name' => __( 'Location Name', 'cdash' ),
				'address' => __( 'Location Address', 'cdash' ),
				'url' => __( 'Web Address', 'cdash' ),
				'phone' => __( 'Phone Number(s)', 'cdash' ),
				'email' => __( 'Email Address(es)', 'cdash' ),
				'logo' => __( 'Logo', 'cdash' ),
				'thumbnail' => __( 'Featured Image', 'cdash' ),
				'level' => __( 'Membership Level', 'cdash' ),
				'category' => __( 'Business Categories', 'cdash' ),
				'social' => __( 'Social Media Links', 'cdash' ),
			);
			foreach( $display_opts as $opt_val => $opt_name ) {
                printf(
                    '<input type="checkbox" name="%s[]" id="%s" value="%s" %s>&nbsp;
                    <label for="%s">%s</label><br />',
                    $this->get_field_name('display'),
			        $this->get_field_id('display'),
                    $opt_val,
                    in_array( $opt_val, $instance['display'] ) ? 'checked="checked"' : '',
                    $this->get_field_id('display'),
                    $opt_name
                );
            } ?>
		</p>

	<?php
	}
}

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
		parent::__construct( 'cdash-business-categories', __('Business Categories', 'cdash'), $widget_ops, $control_ops );
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

		$args = "orderby='" . $orderby . "' showcount='" . $showcount . "' hierarchical='" . $hierarchical . "' hide_empty='" . $hide_empty . "' child_of='" . $child_of . "' exclude='" . $exclude . "'";;
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
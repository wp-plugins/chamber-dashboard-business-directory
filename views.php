<?php

// ------------------------------------------------------------------------
// SINGLE BUSINESS VIEW
// ------------------------------------------------------------------------

// Enqueue stylesheet for single businesses and taxonomies
function cdash_single_business_style() {
	if( is_singular( 'business' ) || is_tax( 'business_category' ) || is_tax( 'membership_level' ) ) {
		wp_enqueue_style( 'cdash-business-directory', plugin_dir_url(__FILE__) . 'css/cdash-business-directory.css' );
	}
}

add_action( 'wp_enqueue_scripts', 'cdash_single_business_style' );

// Display single business (filter content)

function cdash_single_business($content) {
	if( is_singular('business') ) {
		$post_id = get_the_id();
		$meta = get_post_custom($post_id); 

		$options = get_option('cdash_directory_options');

		// make location/address metabox data available
		global $buscontact_metabox;
		$contactmeta = $buscontact_metabox->the_meta();

		// make logo metabox data available
		global $buslogo_metabox;
		$logometa = $buslogo_metabox->the_meta();

		global $post;

		$business_content = "<div id='business'>";
		if (isset($options['sv_thumb']) && $options['sv_thumb'] == "1") { 
			$business_content .= get_the_post_thumbnail( $post->ID, 'full');
		}
		if (isset($options['sv_logo']) && isset($logometa['buslogo']) && $options['sv_logo'] == "1") { 
			$attr = array(
				'class'	=> 'alignleft logo',
			);
			$business_content .= wp_get_attachment_image($logometa['buslogo'], 'full', 0, $attr );
		}
		if (isset($options['sv_description']) && $options['sv_description'] == "1") { 
			$business_content .= $content;
		}
		if( isset( $options['sv_social'] ) && "1" == $options['sv_social'] ) { 
			$business_content .= cdash_display_social_media( get_the_id() );
		}
		if (isset($options['sv_memberlevel']) && $options['sv_memberlevel'] == "1") { 
			$id = get_the_id();
			$levels = get_the_terms( $id, 'membership_level');
			if($levels) {
				$business_content .= "<p class='membership'><span>" . __('Membership Level:&nbsp;', 'cdash') . "</span>";
				$i = 1;
				foreach($levels as $level) {
					if($i !== 1) {
						$business_content .= ",&nbsp;";
					}
					$business_content .= $level->name;
					$i++;
				}
			}
		}
		if (isset($options['sv_category']) && $options['sv_category'] == "1") { 
			$id = get_the_id();
			$buscats = get_the_terms( $id, 'business_category');
			if($buscats) {
				$business_content .= "<p class='categories'><span>" . __('Categories:&nbsp;', 'cdash') . "</span>";
				$i = 1;
				foreach($buscats as $buscat) {
					if($i !== 1) {
						$business_content .= ",&nbsp;";
					}
					$business_content .= $buscat->name;
					$i++;
				}
			}
		}
		if(isset($contactmeta['location'])) {
			$locations = $contactmeta['location'];
			foreach($locations as $location) {
				if( isset( $location['donotdisplay'] ) && "1" == $location['donotdisplay'] ) {
					continue;
				} else {
					$business_content .= "<div class='location'>";
					if (isset($options['sv_name']) && ($options['sv_name']) == "1" && isset($location['altname'])) { 
						$business_content .= "<h3>" . $location['altname'] . "</h3>";
					}
					if (isset($options['sv_address']) && $options['sv_address'] == "1") { 
						$business_content .= "<p class='address'>";
		 					if(isset($location['address'])) {
								$address = $location['address'];
								$business_content .= str_replace("\n", '<br />', $address);
							}
							if(isset($location['city'])) {
								$business_content .= "<br />" . $location['city'] . ",&nbsp;";
							}
							if(isset($location['state'])) {
								$business_content .= $location['state'] . "&nbsp;";
							}
							if(isset($location['zip'])) {
								$business_content .= $location['zip'];
							} 
						$business_content .= "</p>";
					}
					if (isset($options['sv_url']) && $options['sv_url'] == "1" && isset($location['url'])) { 
						$business_content .= cdash_display_url( $location['url'] );
					}
					if (isset($options['sv_phone']) && $options['sv_phone'] == "1" && isset($location['phone'])) { 
						$business_content .= cdash_display_phone_numbers( $location['phone'] );
					}
					if (isset($options['sv_email']) && $options['sv_email'] == "1" && isset($location['email'])) { 
						$business_content .= cdash_display_email_addresses( $location['email'] );
					}
				$business_content .= "</div>";
				}
			}
		}
		if($options['bus_custom']) {
		 	$business_content .= cdash_display_custom_fields( get_the_id() );
		}
		if (isset($options['sv_map']) && $options['sv_map'] == "1" ) {
			// only show the map if locations have addresses entered
			$needmap = "false";
			foreach ($locations as $location) {
				if(isset($location['address']) && !isset($location['donotdisplay'])) {
					$rawaddress = $location['address'] . ' ' . $location['city'] . ' ' . $location['state'] . ' ' . $location['zip'];
					$address = urlencode($rawaddress);
					$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address");
					$json = json_decode($json, true);
					if(is_array($json) && $json['status'] == 'OK') {
						$needmap = "true";
					}
				}
			} 
			if($needmap == "true") { 
				$business_content .= "<div id='map-canvas' style='width: 100%; height: 300px; margin: 20px 0;'></div>";
				add_action('wp_footer', 'cdash_single_business_map');
			}
		}
		$business_content .= "</div>";
	$content = $business_content;
	} 

	return $content;

}
add_filter('the_content', 'cdash_single_business');

// ------------------------------------------------------------------------
// Add map to single business view
// ------------------------------------------------------------------------

function cdash_single_business_map() {
	$options = get_option('cdash_directory_options');
	if( is_singular('business') && isset($options['sv_map']) && $options['sv_map'] == "1" ) { 
		global $buscontact_metabox;
		$contactmeta = $buscontact_metabox->the_meta();
		$locations = $contactmeta['location'];  ?>
		<script type="text/javascript"
			src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDF-0o3jloBzdzSx7rMlevwNSOyvq0G35A&sensor=false">
		</script>
		<script type="text/javascript">

		function initialize() {
			var locations = [
				<?php 
				foreach($locations as $location) {
					if(isset($location['donotdisplay']) && $location['donotdisplay'] == "1") {
						continue;
					} else {
				    	$rawaddress = $location['address'] . ' ' . $location['city'] . ' ' . $location['state'] . ' ' . $location['zip'];
						$address = urlencode($rawaddress);
						$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address");
						$json = json_decode($json, true);
						if(is_array($json) && $json['status'] == 'OK') {
							$lat = $json['results'][0]['geometry']['location']['lat'];
							$long = $json['results'][0]['geometry']['location']['lng']; 
							// get the map icon
							$id = get_the_id();
							$buscats = get_the_terms( $id, 'business_category');
							foreach($buscats as $buscat) {
								$buscatid = $buscat->term_id;
								$iconid = get_tax_meta($buscatid,'category_map_icon');
								if($iconid !== '') {
									$icon = $iconid['src'];
								}
							}
							if(!isset($icon)) {
								$icon = plugins_url() . '/chamber-dashboard-business-directory/images/map_marker.png'; 
							}
							if(isset($location['altname'])) {
								$htmlname = $location['altname'];
								$poptitle = htmlentities($htmlname, ENT_QUOTES);
							} else {
								$htmltitle = htmlentities(get_the_title(), ENT_QUOTES);
								$poptitle = htmlentities($htmltitle, ENT_QUOTES);
							}
							// get other information for the pop-up window
							$popaddress = esc_html( $location['address'] );
							$popcity = esc_html( $location['city'] );
							$popstate = esc_html( $location['state'] );
							?>
							['<div class="business" style="width: 150px; height: 150px;"><h5><?php echo $poptitle; ?></h5><?php echo $popaddress; ?><br /><?php echo $popcity; ?>, <?php echo $location["state"]; ?> <?php echo $location["zip"]; ?></div>', <?php echo $lat; ?>, <?php echo $long; ?>, '<?php echo $icon; ?>'],
							<?php
						}
		

					}
				} ?>

				];

				var bounds = new google.maps.LatLngBounds();
				var mapOptions = {
				    // zoom: 13,
				}
				var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
				var infowindow = new google.maps.InfoWindow();
				var marker, i;

			    for (i = 0; i < locations.length; i++) {  
			    	marker = new google.maps.Marker({
			        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
			        map: map,
			        icon: locations[i][3]
			    	});

					bounds.extend(marker.position);

					// Don't zoom in too far on only one marker - http://stackoverflow.com/questions/3334729/google-maps-v3-fitbounds-zoom-too-close-for-single-marker
				    if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
				       var extendPoint1 = new google.maps.LatLng(bounds.getNorthEast().lat() + 0.01, bounds.getNorthEast().lng() + 0.01);
				       var extendPoint2 = new google.maps.LatLng(bounds.getNorthEast().lat() - 0.01, bounds.getNorthEast().lng() - 0.01);
				       bounds.extend(extendPoint1);
				       bounds.extend(extendPoint2);
				    }

				    map.fitBounds(bounds);

					google.maps.event.addListener(marker, 'click', (function(marker, i) {
					    return function() {
					        infowindow.setContent(locations[i][0]);
					        infowindow.open(map, marker);
					    }
					})(marker, i));

					map.fitBounds(bounds);

				}
			}

		google.maps.event.addDomListener(window, 'load', initialize);

		</script>
	<?php }
}


function cdash_info_window() {
	global $post;
	$output .= "<div style=\x22width: 200px; height: 150px\x22>";
	$output .= $location['altname'];
	$output .= "</div>";
	return $output;
}


// ------------------------------------------------------------------------
// TAXONOMY VIEW
// ------------------------------------------------------------------------

function cdash_taxonomy_filter($content) {
	if( is_tax('business_category') || is_tax('membership_level') ) {
		$options = get_option('cdash_directory_options');

		// make location/address metabox data available
		global $buscontact_metabox;
		$contactmeta = $buscontact_metabox->the_meta();

		// make logo metabox data available
		global $buslogo_metabox;
		$logometa = $buslogo_metabox->the_meta();

		global $post;

		$tax_content = '';
		if (isset($options['tax_thumb']) && $options['tax_thumb'] == "1") { 
			$tax_content .= get_the_post_thumbnail( $post->ID, 'full');
		}
		if (isset($options['tax_logo']) && $options['tax_logo'] == "1" && isset($logometa['buslogo'])) { 
			$attr = array(
				'class'	=> 'alignleft logo',
			);
			$tax_content .= wp_get_attachment_image($logometa['buslogo'], 'full', false, $attr );
		}
		$tax_content .= $content; 
		if ( isset( $options['tax_social'] ) && "1" == $options['tax_social'] ) {
			$tax_content .= cdash_display_social_media( get_the_id() );
		}
		if (isset($options['tax_memberlevel']) && $options['tax_memberlevel'] == "1") { 
			$id = get_the_id();
			$levels = get_the_terms( $id, 'membership_level');
			if($levels) {
				$tax_content .= "<p class='membership'><span>" . __('Membership Level:&nbsp;', 'cdash') . "</span>";
				$i = 1;
				foreach($levels as $level) {
					if($i !== 1) {
						$tax_content .= ",&nbsp;";
					}
					$tax_content .= $level->name;
					$i++;
				}
			}
		}
		if (isset($options['tax_category']) && $options['tax_category'] == "1") { 
			$id = get_the_id();
			$buscats = get_the_terms( $id, 'business_category');
			if($buscats) {
				$tax_content .= "<p class='categories'><span>" . __('Categories:&nbsp;', 'cdash') . "</span>";
				$i = 1;
				foreach($buscats as $buscat) {
					if($i !== 1) {
						$tax_content .= ",&nbsp;";
					}
					$tax_content .= $buscat->name;
					$i++;
				}
			}
		}
		$locations = $contactmeta['location'];
		if( is_array( $locations ) ) {
			foreach($locations as $location) {
				if(isset($location['donotdisplay']) && $location['donotdisplay'] == "1") {
					continue;
				} else {
					$tax_content .= "<div class='location'>";
					if (isset($options['tax_name']) && $options['tax_name'] == "1" && isset($location['altname'])) { 
						$tax_content .= "<h3>" . $location['altname'] . "</h3>";
					}
					if (isset($options['tax_address']) && $options['tax_address'] == "1") { 
						$tax_content .= "<p class='address'>";
		 					if(isset($location['address'])) {
								$address = $location['address'];
								$tax_content .= str_replace("\n", '<br />', $address);
							}
							if(isset($location['city'])) {
								$tax_content .= "<br />" . $location['city'] . ",&nbsp;";
							}
							if(isset($location['state'])) {
								$tax_content .= $location['state'] . "&nbsp;";
							}
							if(isset($location['zip'])) {
								$tax_content .= $location['zip'];
							} 
						$tax_content .= "</p>";
					}
					if (isset($options['tax_url']) && $options['tax_url'] == "1" && isset($location['url'])) { 
						$tax_content .= cdash_display_url( $location['url'] );
					}
					if (isset($options['tax_phone']) && $options['tax_phone'] == "1" && isset($location['phone'])) { 
						$tax_content .= cdash_display_phone_numbers( $location['phone'] );
					}
					if (isset($options['tax_email']) && $options['tax_email'] == "1" && isset($location['email'])) { 
						$tax_content .= cdash_display_email_addresses( $location['email'] );
					}
				$tax_content .= "</div>";
				}
			}
		}
		if($options['bus_custom']) {
		 	$tax_content .= cdash_display_custom_fields( get_the_id() );
		}
	$content = $tax_content;
	}
	return $content;
}
add_filter( 'the_content', 'cdash_taxonomy_filter' );
// add_filter( 'get_the_excerpt', 'cdash_taxonomy_filter' ); this won't retain formatting

// ------------------------------------------------------------------------
// BUSINESS DIRECTORY SHORTCODE
// ------------------------------------------------------------------------

function cdash_business_directory_shortcode( $atts ) {
	// Set our default attributes
	extract( shortcode_atts(
		array(
			'format' => 'list',  // options: list, grid2, grid3, grid4
			'category' => '', // options: slug of any category
			'level' => '', // options: sluf of any membership level
			'text' => 'excerpt', // options: excerpt, description, none
			'display' => '', // options: address, url, phone, email, location_name, category, level, social_media_links, social_media_icons
			'single_link' => 'yes', // options: yes, no
			'perpage' => '-1', // options: any number
			'orderby' => 'title', // options: date, modified, menu_order, rand, priority
			'order' => 'ASC', //options: asc, desc
			'image' => 'logo', // options: logo, featured, none
		), $atts )
	);

	// Enqueue stylesheet if the display format is columns instead of list
	wp_enqueue_style( 'cdash-business-directory', plugin_dir_url(__FILE__) . 'css/cdash-business-directory.css' );
	if($format !== 'list') {
		wp_enqueue_script( 'cdash-business-directory', plugin_dir_url(__FILE__) . 'js/cdash-business-directory.js' );
	}

	// If user wants to display stuff other than the default, turn their display options into an array for parsing later
	if($display !== '') {
  		$displayopts = explode( ", ", $display);
  	}

  	$paged = get_query_var('paged') ? get_query_var('paged') : 1;

	$args = array( 
		'post_type' => 'business',
		'posts_per_page' => $perpage, 
		'paged' => $paged,
	    'order' => $order,
	    'orderby' => $orderby, 	
	    'business_category' => $category,	
	    'membership_level' => $level,								 
	);

	$businessquery = new WP_Query( $args );

	// The Loop
	if ( $businessquery->have_posts() ) :
		$business_list = '';
		$business_list .= "<div id='businesslist' class='" . $format . "'>";
			while ( $businessquery->have_posts() ) : $businessquery->the_post();
				$business_list .= "<div class='business'>";
				if($single_link == "yes") {
					$business_list .= "<h3><a href='" . get_the_permalink() . "'>" . get_the_title() . "</a></h3>";
				} else {
					$business_list .= "<h3>" . get_the_title() . "</h3>";
				}
				$business_list .= "<div class='description'>";
			  	if($image == "logo") {
			  		global $buslogo_metabox;
					$logometa = $buslogo_metabox->the_meta();
					if(isset($logometa['buslogo'])) {
					  	$logoattr = array(
							'class'	=> 'alignleft logo',
						);
						if($single_link == "yes") {
							$business_list .= "<a href='" . get_the_permalink() . "'>" . wp_get_attachment_image($logometa['buslogo'], 'thumb', 0, $logoattr ) . "</a>";
						} else {
							$business_list .= wp_get_attachment_image($logometa['buslogo'], 'thumb', 0, $logoattr );
						}
					}
			  	} elseif($image == "featured") {
			  		$thumbattr = array(
						'class'	=> 'alignleft logo',
					);
			  		$business_list .= get_the_post_thumbnail( $post->ID, 'thumb', $thumbattr);
			  	} 
			  	if($text == "excerpt") {
			  		$business_list .= get_the_excerpt();
			  	} elseif($text == "description") {
			  		$business_list .= get_the_content();
			  	}
			  	$business_list .= "</div>";
			  	if($display !== '') {
			  		global $buscontact_metabox;
					$contactmeta = $buscontact_metabox->the_meta();
				  	if( isset( $contactmeta['location'] ) ) {
				  		$locations = $contactmeta['location'];
						foreach($locations as $location) {
							if(isset($location['donotdisplay']) && $location['donotdisplay'] == "1") {
								continue;
							} else {
							  	if(in_array("location_name", $displayopts)) {
							  		$business_list .= "<p class='location-name'>" . $location['altname'] . "</p>";
							  	}
							  	if(in_array("address", $displayopts)) {
									$business_list .= "<p class='address'>";
					 					if(isset($location['address'])) {
											$address = $location['address'];
											$business_list .= str_replace("\n", '<br />', $address);
										}
										if(isset($location['city'])) {
											$business_list .= "<br />" . $location['city'] . ",&nbsp;";
										}
										if(isset($location['state'])) {
											$business_list .= $location['state'] . "&nbsp";
										}
										if(isset($location['zip'])) {
											$business_list .= $location['zip'];
										} 
									$business_list .= "</p>";
							  	}
							  	if(in_array("phone", $displayopts) && isset($location['phone'])) {
									$business_list .= cdash_display_phone_numbers( $location['phone'] );
							  	} 
							  	if(in_array("email", $displayopts) && isset($location['email'])) {
									$business_list .= cdash_display_email_addresses( $location['email'] );
								}
						  	} 
						  	if(in_array("url", $displayopts) && isset($location['url'])) {
						  		$business_list .= cdash_display_url( $location['url'] );
						  	} 
				  		}
				  	}
			  		if(in_array("social_media", $displayopts)) {
			  			$business_list .= cdash_display_social_media( get_the_id() );
			  		}
			  		if(in_array("category", $displayopts)) {
						$id = get_the_id();
						$buscats = get_the_terms( $id, 'business_category');
							if( $buscats ) {
							$business_list .= "<p class='categories'><span>" . __('Categories:&nbsp;', 'cdash') . "</span>";
							$i = 1;
							foreach( $buscats as $buscat ) {
								if($i !== 1) {
									$business_list .= ",&nbsp;";
								}
								$business_list .= $buscat->name;
								$i++;
							}
						}
				  	}
				  	if(in_array("level", $displayopts)) {
						$id = get_the_id();
						$levels = get_the_terms( $id, 'membership_level');
						$business_list .= "<p class='membership'><span>" . __('Membership Level:&nbsp;', 'cdash') . "</span>";
						$i = 1;
						foreach($levels as $level) {
							if($i !== 1) {
								$business_list .= ",&nbsp;";
							}
							$business_list .= $level->name;
							$i++;
						}
				  	}
			  	}
			  	$options = get_option('cdash_directory_options');
			  	if($options['bus_custom']) {
					$business_list .= cdash_display_custom_fields( get_the_id() );
				}

			  	$business_list .= "</div>";
			endwhile;

			// pagination links
			$total_pages = $businessquery->max_num_pages;
			if ($total_pages > 1){
				$current_page = max(1, get_query_var('paged'));
   				$business_list .= "<div class='pagination'>";
			  	$business_list .= paginate_links(array(
			      'base' => get_pagenum_link(1) . '%_%',
			      'format' => '/page/%#%',
			      'current' => $current_page,
			      'total' => $total_pages,
			    ));
			    $business_list .= "</div>";
			}

		$business_list .= "</div>";
		return $business_list;
	endif;
	wp_reset_postdata();
}
add_shortcode( 'business_directory', 'cdash_business_directory_shortcode' );

// ------------------------------------------------------------------------
// BUSINESS MAP SHORTCODE
// ------------------------------------------------------------------------

function cdash_business_map_shortcode( $atts ) {
	// Set our default attributes
	extract( shortcode_atts(
		array(
			'category' => '', // options: slug of any category
			'level' => '', // options: slug of any membership level
			'single_link' => 'yes', // options: yes, no
			'perpage' => '-1', // options: any number
		), $atts )
	);

	$args = array( 
		'post_type' => 'business',
		'posts_per_page' => $perpage, 
	    'business_category' => $category,	
	    'membership_level' => $level,								 
	);

	wp_enqueue_style( 'cdash-business-directory', plugin_dir_url(__FILE__) . 'css/cdash-business-directory.css' );

	$mapquery = new WP_Query( $args );
	$business_map = "<div id='map-canvas' style='width: 100%; height: 500px;'></div>";
	$business_map .= "<script type='text/javascript' src='https://maps.googleapis.com/maps/api/js?key=AIzaSyDF-0o3jloBzdzSx7rMlevwNSOyvq0G35A&sensor=false'></script>";
	$business_map .= "<script type='text/javascript'>";
	$business_map .= "function initialize() {
				var locations = [";

	// The Loop
	if ( $mapquery->have_posts() ) :
		while ( $mapquery->have_posts() ) : $mapquery->the_post();
			global $buscontact_metabox;
			$contactmeta = $buscontact_metabox->the_meta();
			if( isset( $contactmeta['location'] ) ) {
				$locations = $contactmeta['location'];
				if(!empty($locations)) {
					foreach($locations as $location) {
						if(isset($location['donotdisplay']) && $location['donotdisplay'] == "1") {
							continue;
						} elseif(isset($location['address'])) {
							// Get the latitude and longitude from the address
					    	$rawaddress = $location['address'] . ' ' . $location['city'] . ' ' . $location['state'] . ' ' . $location['zip'];
							$address = urlencode($rawaddress);
							$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address");
							$json = json_decode($json, true);
							if(is_array($json) && $json['status'] == 'OK') {
								$lat = $json['results'][0]['geometry']['location']['lat'];
								$long = $json['results'][0]['geometry']['location']['lng']; 
								// Get the map icon
								$id = get_the_id();
								$buscats = get_the_terms( $id, 'business_category');
								foreach($buscats as $buscat) {
									$buscatid = $buscat->term_id;
									$iconid = get_tax_meta($buscatid,'category_map_icon');
									if($iconid !== '') {
										$icon = $iconid['src'];
									}
								}
								if(!isset($icon)) {
									$icon = plugins_url() . '/chamber-dashboard-business-directory/images/map_marker.png'; 
								}
								// Create the pop-up info window
								$popaddress = esc_html( $location['address'] );
								$popcity = esc_html( $location['city'] );
								$popstate = esc_html( $location['state'] );
								$poptitle = esc_html( get_the_title() );
								if($single_link == "yes") {
									$thismapmarker = "['<div class=\x22business\x22 style=\x22width: 150px; height: 150px;\x22><h5><a href=\x22" . get_the_permalink() . "\x22>" . $poptitle . "</a></h5> " . $popaddress . "<br />" . $popcity . ", " . $popstate . "&nbsp;" . $location['zip'] . "</div>', " . $lat . ", " . $long . ", '" . $icon . "'],";
									$business_map .= str_replace(array("\r", "\n"), '', $thismapmarker);
								} else {
									$thismapmarker .= "['<div class=\x22business\x22 style=\x22width: 150px; height: 150px;\x22><h5>" . $poptitle . "</h5> " . $popaddress . "<br />" . $popcity . ", " . $popstate . "&nbsp;" . $location['zip'] . "</div>', " . $lat . ", " . $long . ", '" . $icon . "'],";
									$business_map .= str_replace(array("\r", "\n"), '', $thismapmarker);
								}
							}

						}
					}
				}
			}
		endwhile;
	endif;

	$business_map .= "];

					var bounds = new google.maps.LatLngBounds();
					var mapOptions = {
					    // zoom: 13,
					}
					var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
					var infowindow = new google.maps.InfoWindow();
					var marker, i;

				    for (i = 0; i < locations.length; i++) {  
				    	marker = new google.maps.Marker({
				        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
				        map: map,
				        icon: locations[i][3]
				    	});

						bounds.extend(marker.position);

						google.maps.event.addListener(marker, 'click', (function(marker, i) {
						    return function() {
						        infowindow.setContent(locations[i][0]);
						        infowindow.open(map, marker);
						    }
						})(marker, i));

						map.fitBounds(bounds);

						if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
					       var extendPoint1 = new google.maps.LatLng(bounds.getNorthEast().lat() + 0.01, bounds.getNorthEast().lng() + 0.01);
					       var extendPoint2 = new google.maps.LatLng(bounds.getNorthEast().lat() - 0.01, bounds.getNorthEast().lng() - 0.01);
					       bounds.extend(extendPoint1);
					       bounds.extend(extendPoint2);
					    }

					}
				}

			google.maps.event.addDomListener(window, 'load', initialize);

		</script>";

	return $business_map;
	wp_reset_postdata();
}
add_shortcode( 'business_map', 'cdash_business_map_shortcode' );



// ------------------------------------------------------------------------
// BUSINESS SEARCH RESULTS SHORTCODE
// ------------------------------------------------------------------------

function cdash_business_search_results_shortcode() {

	$search_results = "";
	// Search results 
	if($_GET) {
		// Get the search terms
		$buscat = $_GET['buscat'];
		$searchtext = $_GET['searchtext'];

		// Set up a query with the search terms
		$paged = get_query_var('paged') ? get_query_var('paged') : 1;
		$args = array( 
                'post_type' => 'business',
                'posts_per_page' => 25,      
                );

            if ( $buscat ) {
                $buscat_params = array(
                    'taxonomy' => 'business_category',
                    'field' => 'slug',
                    'terms' => $buscat,
                    'operator' => 'IN',
                );
                $args['tax_query'] = array(
	                $buscat_params,
	            );
            }

            if ( $searchtext ) {
            	$args['s'] = $searchtext;
            }
                
        $search_query = new WP_Query( $args );
		if ( $search_query->have_posts() ) :
			// Display the search results
			$search_results .= "<div id='search-results'>";
			$search_results .= "<h2>" . __('Search Results', 'cdash') . "</h2>";
			while ( $search_query->have_posts() ) : $search_query->the_post();

				$search_results .= "<div class='search-result'>";
				$search_results .= "<h3><a href='" . get_the_permalink() . "'>" . get_the_title() . "</a></h3>";
				$options = get_option('cdash_directory_options');

				// make location/address metabox data available
				global $buscontact_metabox;
				$contactmeta = $buscontact_metabox->the_meta();

				// make logo metabox data available
				global $buslogo_metabox;
				$logometa = $buslogo_metabox->the_meta();

				global $post;

				if ( isset( $options['tax_thumb'] ) && $options['tax_thumb'] == "1") { 
					$search_results .= get_the_post_thumbnail( $post->ID, 'full');
				}
				if (($options['tax_logo']) == "1") { 
					$attr = array(
						'class'	=> 'alignleft logo',
					);
					$search_results .= wp_get_attachment_image($logometa['buslogo'], 'full', 0, $attr );
				}
				$search_results .= get_the_excerpt(); 
				if ( isset( $options['tax_memberlevel'] ) && $options['tax_memberlevel'] == "1") { 
					$id = get_the_id();
					$levels = get_the_terms( $id, 'membership_level');
					if($levels) {
						$search_results .= "<p class='membership'><span>" . __('Membership Level:', 'cdash') . "</span>&nbsp;";
						$i = 1;
						foreach($levels as $level) {
							if($i !== 1) {
								$search_results .= ",&nbsp;";
							}
							$search_results .= $level->name;
							$i++;
						}
					}
				}
				if ( isset( $options['tax_category'] ) && $options['tax_category'] == "1") { 
					$id = get_the_id();
					$buscats = get_the_terms( $id, 'business_category');
					if($buscats) {
						$search_results .= "<p class='categories'><span>" . __('Categories:&nbsp;', 'cdash') . "</span>";
						$i = 1;
						foreach($buscats as $buscat) {
							if($i !== 1) {
								$search_results .= ",&nbsp;";
							}
							$search_results .= $buscat->name;
							$i++;
						}
					}
				}
				if ( isset( $options['tax_social'] ) && "1" == $options['tax_social'] ) {
					$search_results .= cdash_display_social_media( get_the_id() );
				}
				$locations = $contactmeta['location'];
				foreach($locations as $location) {
					if( isset( $location['donotdisplay'] ) && $location['donotdisplay'] == "1") {
						continue;
					} else {
						$search_results .= "<div class='location'>";
						if ( isset( $options['tax_name'] ) && $options['tax_name'] == "1" && isset( $location['altname'] ) ) { 
							$search_results .= "<h3>" . $location['altname'] . "</h3>";
						}
						if ( isset( $options['tax_address'] ) && $options['tax_address']== "1") { 
							$search_results .= "<p class='address'>";
			 					if( isset( $location['address'] ) ) {
									$address = $location['address'];
									$search_results .= str_replace("\n", '<br />', $address);
								}
								if( isset( $location['city'] ) ) {
									$search_results .= "<br />" . $location['city'] . ",&nbsp;";
								}
								if( isset( $location['state'] ) ) {
									$search_results .= $location['state'] . "&nbsp";
								}
								if( isset( $location['zip'] ) ) {
									$search_results .= $location['zip'];
								} 
							$search_results .= "</p>";
						}
						if ( isset( $options['tax_url'] ) && $options['tax_url'] == "1" && isset( $location['url'] ) ) { 
							$search_results .= cdash_display_url( $location['url'] );
						}
						if ( isset( $options['tax_phone'] ) && $options['tax_phone'] == "1" && isset( $location['phone'] ) ) { 
							$search_results .= cdash_display_phone_numbers( $location['phone'] );
						}
						if ( isset( $options['tax_email'] ) && $options['tax_email'] == "1" && isset( $location['email'] ) ) { 
							$search_results .= cdash_display_email_addresses( $location['email'] );
						$search_results .= "</div><!-- .location -->";
						}
					$search_results .= "</div><!-- .search-result -->";
					}
				}
				if($options['bus_custom']) {
					$search_results .= cdash_display_custom_fields( get_the_id() );
				}

				$search_results .= "</div><!-- #search-results --><div style='clear:both'></div>";
			endwhile;
			$total_pages = $search_query->max_num_pages;
			if ($total_pages > 1){
				$current_page = max(1, get_query_var('paged'));
   				$search_results .= "<div class='pagination'>";
			  	$search_results .= paginate_links(array(
			      'base' => get_pagenum_link(1) . '%_%',
			      'format' => '/page/%#%',
			      'current' => $current_page,
			      'total' => $total_pages,
			    ));
			    $search_results .= "</div>";
			}
			$search_results .= "</div>";
		endif;

		// Reset Post Data
		wp_reset_postdata();
	} 


	return $search_results;
}
add_shortcode( 'business_search_results', 'cdash_business_search_results_shortcode' );

// ------------------------------------------------------------------------
// BUSINESS SEARCH SHORTCODE
// ------------------------------------------------------------------------

function cdash_business_search_form_shortcode( $atts ) {
	extract( shortcode_atts(
		array(
			'results_page' => 'notset',  // options: any url
		), $atts )
	); 

	// Search form
	$search_form = "<div id='business-search'><h3>" . __('Search', 'cdash') . "</h3>";
	if( $results_page == 'notset') {
		$search_form .= __( 'You must enter a results page!', 'cdash' );
	} else {
		$search_form .= "<form method='get' action='" . home_url('/') . $results_page . "'>";
	}
	$search_form .= "<p><label>" . __('Search Term', 'cdash') . "</label><br /><input type='text' value='' name='searchtext' id='searchtext' /></p>";
	// $search_form .= "<p><label>Business Name</label><br /><input type='text' value='' name='business_name' id='business_name' /></p>";
		// searching by business name seems like a good idea, but you can only query the slug, so if the name isn't exactly like the slug, it won't find anything
	// $search_form .= "<p><label>City</label><br /><input type='text' value='' name='city' id='city' /></p>";
		// I would really like to be able to search by city, but since WPAlchemy serializes the locations array, I don't think this is possible
	$search_form .= "<p><label>" . __('Business Category', 'cdash') . "</label><br /><select name='buscat'><option value=''>";
	$terms = get_terms( 'business_category', 'hide_empty=0' );
        foreach ($terms as $term) {
            $search_form .= "<option value='" . $term->slug . "'>" . $term->name;
        } 
    $search_form .= "</select></p>";
	
	$search_form .= "<input type='submit' value='" . __('Search', 'cdash') . "'>";
	$search_form .= "</form>";
	$search_form .= "</div>";

	return $search_form;
}
add_shortcode( 'business_search_form', 'cdash_business_search_form_shortcode' );


// ------------------------------------------------------------------------
// BUSINESS SEARCH SHORTCODE
// ------------------------------------------------------------------------

function cdash_business_search_shortcode( $atts ) {

	$business_search = do_shortcode('[business_search_results]');
	$business_search .= do_shortcode('[business_search_form results_page='.the_permalink().']');

	return $business_search;
}
add_shortcode( 'business_search', 'cdash_business_search_shortcode' );

// ------------------------------------------------------------------------
// Business Category shortcode = [business_categories]
// Thanks to https://github.com/justinribeiro/chamber-dashboard-business-directory/blob/add-category-shortcode/cdash-business-directory.php
// Structure:
// <ul class="business-categories">
// <li class="cat-item cat-item-[ID]"><a href="[LINK]">[NAME]</a></li>
// </ul>
//
// ------------------------------------------------------------------------
function cdash_business_categories_shortcode( $atts ) {
	// Set our default attributes
	extract( shortcode_atts(
		array(
		'orderby' => 'name', // options: date, modified, menu_order, rand
		'showcount' => 0,
		'padcounts' => 0,
		'hierarchical' => 1,
		'title' => ''
		), $atts )
	);
	$taxonomy = 'business_category';
	$args = array(
		'taxonomy' => $taxonomy,
		'orderby' => $orderby,
		'show_count' => $showcount,
		'pad_counts' => $padcounts,
		'hierarchical' => $hierarchical,
		'title_li' => $title
	);
	echo '<ul class="business-categories">';
	wp_list_categories($args);
	echo '</ul>';
}
add_shortcode( 'business_categories', 'cdash_business_categories_shortcode' );



// ------------------------------------------------------------------------
// DISPLAY SOCIAL MEDIA
// ------------------------------------------------------------------------

function cdash_display_social_media( $postid ) {
	// get options
	$options = get_option( 'cdash_directory_options' );
	// get meta
	global $buscontact_metabox;
	$meta = $buscontact_metabox->the_meta();

	$display = '<div class="cdash-social-media">';

	if( isset( $options['sm_display'] ) && "text" == $options['sm_display'] ) {
		// display text links
		if( isset( $meta['social'] ) ) {
			$social_links = $meta['social'];
			if( isset( $social_links ) ) {
				$display .= '<ul class="text-links">';
				foreach( $social_links as $link ) {
					$display .= '<li><a href="' . $link['socialurl'] . '" target="_blank">' . ucfirst( $link['socialservice'] ) . '</a></li>';
				}
				$display .= '</ul>';
			}
		}

	} elseif( isset( $options['sm_display'] ) && "icons" == $options['sm_display'] ) {
		// display icons
		if( isset( $meta['social'] ) ) {
			$social_links = $meta['social'];
			if( isset( $social_links ) ) {
				$display .= '<ul class="icons">';
				foreach( $social_links as $link ) {
					$display .= '<li><a href="' . $link['socialurl'] . '" target="_blank"><img src="' . plugins_url() . '/chamber-dashboard-business-directory/images/social-media/' . $link['socialservice'] . '-' . $options['sm_icon_size'] . '.png" alt="' . ucfirst( $link['socialservice'] ) . '"></a></li>';
				}
				$display .= '</ul>';
			}
		}
	}

	$display .= "</div>";

	return $display;
}

// ------------------------------------------------------------------------
// DISPLAY CUSTOM FIELDS
// ------------------------------------------------------------------------

function cdash_display_custom_fields( $postid ) {
	$options = get_option( 'cdash_directory_options' );
	$customfields = $options['bus_custom'];
	global $custom_metabox;
	$custommeta = $custom_metabox->the_meta();

	$custom_fields = ''; 

	foreach($customfields as $field) { 
		if( is_singular( 'business' ) || "yes" == $field['display_single'] ) {
			$fieldname = $field['name'];
			if(isset($custommeta[$fieldname])) {
				$custom_fields .= "<p><strong>" . $field['name'] . ":</strong>&nbsp;" . $custommeta[$fieldname] . "</p>";
			}	
		} elseif( "yes" !== $field['display_dir'] ) {
			continue;
		} else {
			$fieldname = $field['name'];
			if(isset($custommeta[$fieldname])) {
				$custom_fields .= "<p><strong>" . $field['name'] . ":</strong>&nbsp;" . $custommeta[$fieldname] . "</p>";
			}	
		}
	}

	return $custom_fields;
}

// ------------------------------------------------------------------------
// DISPLAY PHONE NUMBERS
// ------------------------------------------------------------------------

function cdash_display_phone_numbers( $phone_numbers ) {
	$phones_content = '';

	if( is_array( $phone_numbers ) ) {
		$phones_content .= "<p class='phone'>";
			$i = 1;
			foreach($phone_numbers as $phone) {
				if($i !== 1) {
					$phones_content .= "<br />";
				}
				$phones_content .= "<a href='tel:" . $phone['phonenumber'] . "'>" . $phone['phonenumber'] . "</a>";
				if(isset($phone['phonetype'])) {
					$phones_content .= "&nbsp;(&nbsp;" . $phone['phonetype'] . "&nbsp;)";
				}
				$i++;
			}
		$phones_content .= "</p>";
	}

	return $phones_content;
}

// ------------------------------------------------------------------------
// DISPLAY EMAIL ADDRESSES
// ------------------------------------------------------------------------

function cdash_display_email_addresses( $email_addresses ) {
	$email_content = '';

	if( is_array( $email_addresses ) ) {
		$email_content .= "<p class='email'>";
			$i = 1;
			foreach($email_addresses as $email) {
				if($i !== 1) {
					$email_content .= "<br />";
				}
				$email_content .= "<a href='mailto:" . $email['emailaddress'] . "'>" . $email['emailaddress'] . "</a>";
				if(isset($email['emailtype'])) {
					$email_content .= "&nbsp;(&nbsp;" . $email['emailtype'] . "&nbsp;)";
				}
				$i++;
			}
		$email_content .= "</p>";
	}

	return $email_content;
}

// ------------------------------------------------------------------------
// DISPLAY URL
// ------------------------------------------------------------------------

function cdash_display_url( $url ) {
	if( null === parse_url( $url, PHP_URL_SCHEME )) {
		$url = "http://" . $url;
	}

	$url_content = "<p class='website'><a href='" . $url . "' target='_blank'>" . $url . "</a></p>";

	return $url_content;
}

?>
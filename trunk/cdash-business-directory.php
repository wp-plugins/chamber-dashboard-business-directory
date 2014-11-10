<?php
/*
Plugin Name: Chamber Dashboard Business Directory
Plugin URI: http://chamberdashboard.com
Description: Create a database of the businesses in your chamber of commerce
Version: 1.7.3
Author: Morgan Kay
Author URI: http://wpalchemists.com
*/

/*  Copyright 2014 Morgan Kay and the Fremont Chamber of Commerce (email : info@chamberdashboard.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// ------------------------------------------------------------------------
// REQUIRE MINIMUM VERSION OF WORDPRESS:                                               
// ------------------------------------------------------------------------


function cdash_requires_wordpress_version() {
	global $wp_version;
	$plugin = plugin_basename( __FILE__ );
	$plugin_data = get_plugin_data( __FILE__, false );

	if ( version_compare($wp_version, "3.8", "<" ) ) {
		if( is_plugin_active($plugin) ) {
			deactivate_plugins( $plugin );
			wp_die( "'".$plugin_data['Name']."' requires WordPress 3.3 or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to <a href='".admin_url()."'>WordPress admin</a>." );
		}
	}
}
add_action( 'admin_init', 'cdash_requires_wordpress_version' );

// ------------------------------------------------------------------------
// REGISTER HOOKS & CALLBACK FUNCTIONS:
// ------------------------------------------------------------------------

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'cdash_add_defaults');
register_activation_hook(__FILE__, 'cdash_activation_transient');
register_uninstall_hook(__FILE__, 'cdash_delete_plugin_options');
add_action('admin_init', 'cdash_init' );
add_action('admin_menu', 'cdash_add_options_page');
add_filter( 'plugin_action_links', 'cdash_plugin_action_links', 10, 2 );

// Require options stuff
require_once( plugin_dir_path( __FILE__ ) . 'options.php' );

// set up a transient on activation so we know whether or not to show the welcome screen
function cdash_activation_transient() {
	set_transient('_cdash_activation_redirect', 1, 3600);
}
// Require welcome page
require_once( plugin_dir_path( __FILE__ ) . 'welcome-page.php' );



// Initialize language so it can be translated
function cdash_language_init() {
  load_plugin_textdomain( 'cdash', false, 'chamber-dashboard-business-directory/languages' );
}
add_action('init', 'cdash_language_init');

// ------------------------------------------------------------------------
// SET UP CUSTOM POST TYPES AND TAXONOMIES
// ------------------------------------------------------------------------

// Register Custom Taxonomy - Business Cateogory
function cdash_register_taxonomy_business_category() {

	$labels = array(
		'name'                       => _x( 'Business Categories', 'Taxonomy General Name', 'cdash' ),
		'singular_name'              => _x( 'Business Category', 'Taxonomy Singular Name', 'cdash' ),
		'menu_name'                  => __( 'Business Category', 'cdash' ),
		'all_items'                  => __( 'All Business Categories', 'cdash' ),
		'parent_item'                => __( 'Parent Business Category', 'cdash' ),
		'parent_item_colon'          => __( 'Parent Business Category:', 'cdash' ),
		'new_item_name'              => __( 'New Business Category Name', 'cdash' ),
		'add_new_item'               => __( 'Add New Business Category', 'cdash' ),
		'edit_item'                  => __( 'Edit Business Category', 'cdash' ),
		'update_item'                => __( 'Update Business Category', 'cdash' ),
		'separate_items_with_commas' => __( 'Separate Business Categories with commas', 'cdash' ),
		'search_items'               => __( 'Search Business Categories', 'cdash' ),
		'add_or_remove_items'        => __( 'Add or remove Business Category', 'cdash' ),
		'choose_from_most_used'      => __( 'Choose from the most used Business Categories', 'cdash' ),
		'not_found'                  => __( 'Not Found', 'cdash' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'rewrite' => array (
            'slug' => _x( 'business_category', 'business_category', 'cdash' )
        )
	);
	register_taxonomy( 'business_category', array( 'business' ), $args );

}

add_action( 'init', 'cdash_register_taxonomy_business_category', 0 );

// Register Custom Taxonomy - Membership Level
function cdash_register_taxonomy_membership_level() {

	$labels = array(
		'name'                       => _x( 'Membership Levels', 'Taxonomy General Name', 'cdash' ),
		'singular_name'              => _x( 'Membership Level', 'Taxonomy Singular Name', 'cdash' ),
		'menu_name'                  => __( 'Membership Level', 'cdash' ),
		'all_items'                  => __( 'All Membership Levels', 'cdash' ),
		'parent_item'                => __( 'Parent Membership Level', 'cdash' ),
		'parent_item_colon'          => __( 'Parent Membership Level:', 'cdash' ),
		'new_item_name'              => __( 'New Membership Level Name', 'cdash' ),
		'add_new_item'               => __( 'Add New Membership Level', 'cdash' ),
		'edit_item'                  => __( 'Edit Membership Level', 'cdash' ),
		'update_item'                => __( 'Update Membership Level', 'cdash' ),
		'separate_items_with_commas' => __( 'Separate Membership Levels with commas', 'cdash' ),
		'search_items'               => __( 'Search Membership Levels', 'cdash' ),
		'add_or_remove_items'        => __( 'Add or remove Membership Level', 'cdash' ),
		'choose_from_most_used'      => __( 'Choose from the most used Membership Levels', 'cdash' ),
		'not_found'                  => __( 'Not Found', 'cdash' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'rewrite' => array (
            'slug' => _x( 'membership_level', 'membership_level', 'cdash' )
        )
	);
	register_taxonomy( 'membership_level', array( 'business' ), $args );

}

add_action( 'init', 'cdash_register_taxonomy_membership_level', 0 );


// Register Custom Post Type - Businesses
function cdash_register_cpt_business() {

	$labels = array(
		'name'                => _x( 'Businesses', 'Post Type General Name', 'cdash' ),
		'singular_name'       => _x( 'Business', 'Post Type Singular Name', 'cdash' ),
		'menu_name'           => __( 'Businesses', 'cdash' ),
		'parent_item_colon'   => __( 'Parent Business:', 'cdash' ),
		'all_items'           => __( 'All Businesses', 'cdash' ),
		'view_item'           => __( 'View Business', 'cdash' ),
		'add_new_item'        => __( 'Add New Business', 'cdash' ),
		'add_new'             => __( 'Add New', 'cdash' ),
		'edit_item'           => __( 'Edit Business', 'cdash' ),
		'update_item'         => __( 'Update Business', 'cdash' ),
		'search_items'        => __( 'Search Businesses', 'cdash' ),
		'not_found'           => __( 'Not found', 'cdash' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'cdash' ),
	);
	$args = array(
		'label'               => __( 'business', 'cdash' ),
		'description'         => __( 'Businesses and Organizations', 'cdash' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', ),
		'taxonomies'          => array( 'business_category', ' membership_level' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		//'menu_icon'           => 'dashicons-shop',
		'menu_icon'           => 'dashicons-flag',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		'rewrite' => array (
            'slug' => _x( 'business', 'business', 'cdash' )
        )
	);
	register_post_type( 'business', $args );

}

add_action( 'init', 'cdash_register_cpt_business', 0 );


// ------------------------------------------------------------------------
// SET UP METABOXES
// ------------------------------------------------------------------------

if(!class_exists('WPAlchemy_MetaBox')) { //only include metabox files if another plugin hasn't done it
	include_once 'wpalchemy/MetaBox.php';
}

define( 'CDASH_PATH', plugin_dir_path(__FILE__) );

include_once 'wpalchemy/MediaAccess.php';
$wpalchemy_media_access = new WPAlchemy_MediaAccess();

// Add a stylesheet to the admin area to make meta boxes look nice
function cdash_metabox_stylesheet()
{
    if ( is_admin() )
    {
        wp_enqueue_style( 'wpalchemy-metabox', plugins_url() . '/chamber-dashboard-business-directory/wpalchemy/meta.css' );
    }
}
add_action( 'init', 'cdash_metabox_stylesheet' );

// Create metabox for location/address information
$buscontact_metabox = new WPAlchemy_MetaBox(array
(
    'id' => 'buscontact_meta',
    'title' => 'Locations',
    'types' => array('business'),
    'template' => CDASH_PATH . '/wpalchemy/buscontact.php',
    'mode' => WPALCHEMY_MODE_EXTRACT,
    'prefix' => '_cdash_'
));

// Create metabox for business logo
$buslogo_metabox = new WPAlchemy_MetaBox(array
(
    'id' => 'buslogo_meta',
    'title' => 'Logo',
    'types' => array('business'),
    'template' => CDASH_PATH . '/wpalchemy/buslogo.php',
    'mode' => WPALCHEMY_MODE_EXTRACT,
    'prefix' => '_cdash_'
));

// Create metabox for internal notes
$busnotes_metabox = new WPAlchemy_MetaBox(array
(
    'id' => 'busnotes_meta',
    'title' => 'Notes',
    'types' => array('business'),
    'template' => CDASH_PATH . '/wpalchemy/busnotes.php',
    'mode' => WPALCHEMY_MODE_EXTRACT,
    'prefix' => '_cdash_'
));

$options = get_option('cdash_directory_options');
if(!empty($options['bus_custom'])) {
	// Create metabox for custom fields
	$custom_metabox = new WPAlchemy_MetaBox(array
	(
	    'id' => 'custom_meta',
	    'title' => 'Custom Fields',
	    'types' => array('business'),
	    'template' => CDASH_PATH . '/wpalchemy/buscustom.php',
	    'mode' => WPALCHEMY_MODE_EXTRACT,
	    'prefix' => '_cdash_'
	));
}


// ------------------------------------------------------------------------
// ADD CUSTOM META DATA TO TAXONOMIES - http://en.bainternet.info/wordpress-taxonomies-extra-fields-the-easy-way/
// ------------------------------------------------------------------------

//include the main class file
require_once( plugin_dir_path( __FILE__ ) . "/Tax-meta-class/Tax-meta-class.php");

// configure custom fields
$config = array(
   'id' => 'business_category_meta',
   'title' => 'Business Category Information',
   'pages' => array('business_category'),
   'context' => 'normal',
   'fields' => array(),
   'local_images' => true,
   'use_with_theme' => false
);

$buscat_meta = new Tax_Meta_Class($config);
$buscat_meta->addImage('category_map_icon',array('name'=> 'Map Icon '));
$buscat_meta->Finish();


// ------------------------------------------------------------------------
// ADD COLUMNS TO BUSINESSES OVERVIEW PAGE
// ------------------------------------------------------------------------

function cdash_business_overview_columns_headers($defaults) {
    $defaults['phone'] = 'Phone Number(s)';
    return $defaults;
}

function cdash_business_overview_columns($column_name, $post_ID) {
	global $buscontact_metabox;
	$contactmeta = $buscontact_metabox->the_meta();
    if ($column_name == 'phone') {
    	$phonenumbers = '';
    	$locations = $contactmeta['location'];
		foreach($locations as $location) {
			if(isset($location['phone'])) {
				$phones = $location['phone'];
				if(is_array($phones)) {
					foreach($phones as $phone) {
						$phonenumbers .= $phone['phonenumber'];
						if(isset($phone['phonetype'])) {
							$phonenumbers .= "&nbsp;(" . $phone['phonetype'] . "&nbsp;)";
						}
						$phonenumbers .= "<br />";
					}
				}
			}
		}
        echo $phonenumbers;
    }    
}

add_filter('manage_business_posts_columns', 'cdash_business_overview_columns_headers', 10);
add_action('manage_business_posts_custom_column', 'cdash_business_overview_columns', 10, 2);


// ------------------------------------------------------------------------
// SINGLE BUSINESS VIEW
// ------------------------------------------------------------------------

// Enqueue stylesheet for single businesses
function cdash_single_business_style() {
	if(is_singular('business')) {
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
		if (isset($options['sv_memberlevel']) && $options['sv_memberlevel'] == "1") { 
			$id = get_the_id();
			$levels = get_the_terms( $id, 'membership_level');
			if($levels) {
				$business_content .= "<p class='membership'><span>" . __('Membership Level:', 'cdash') . "</span>&nbsp;";
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
				$business_content .= "<p class='categories'><span>" . __('Categories:', 'cdash') . "</span>&nbsp;";
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
				if(isset($location['donotdisplay']) && $location['donotdisplay'] == "1") {
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
								$business_content .= $location['state'] . "&nbsp";
							}
							if(isset($location['zip'])) {
								$business_content .= $location['zip'];
							} 
						$business_content .= "</p>";
					}
					if (isset($options['sv_url']) && $options['sv_url'] == "1" && isset($location['url'])) { 
						$business_content .= "<p class='website'><a href='" . $location['url'] . "' target='_blank'>" . $location['url'] . "</a></p>";
					}
					if (isset($options['sv_phone']) && $options['sv_phone'] == "1" && isset($location['phone'])) { 
						$business_content .= "<p class='phone'>";
							$i = 1;
							$phones = $location['phone'];
							foreach($phones as $phone) {
								if($i !== 1) {
									$business_content .= "<br />";
								}
								$business_content .= "<a href='tel:" . $phone['phonenumber'] . "'>" . $phone['phonenumber'] . "</a>";
								if(isset($phone['phonetype'])) {
									$business_content .= "&nbsp;(" . $phone['phonetype'] . "&nbsp;)";
								}
								$i++;
							}
						$business_content .= "</p>";
					}
					if (isset($options['sv_email']) && $options['sv_email'] == "1" && isset($location['email'])) { 
						$business_content .= "<p class='email'>";
							$i = 1;
							$emails = $location['email'];
							foreach($emails as $email) {
								if($i !== 1) {
									$business_content .= "<br />";
								}
								$business_content .= "<a href='mailto:" . $email['emailaddress'] . "'>" . $email['emailaddress'] . "</a>";
								if(isset($email['emailtype'])) {
									$business_content .= "&nbsp;(&nbsp;" . $email['emailtype'] . "&nbsp;)";
								}
								$i++;
							}
						$business_content .= "</p>";
					}
				$business_content .= "</div>";
				}
			}
		}
		if($options['bus_custom']) {
			$customfields = $options['bus_custom'];
			global $custom_metabox;
			$custommeta = $custom_metabox->the_meta();
			foreach($customfields as $field) { 
				if($field['display_dir'] !== "yes") {
					continue;
				} else {
					if(isset($custommeta[$field['name']]) && is_array($custommeta)) {
						$business_content .= "<p><strong>" . $field['name'] . ":</strong>&nbsp;" . $custommeta[$field['name']] . "</p>";
					}	
				}
			}
		}
		if (isset($options['sv_map']) && $options['sv_map'] == "1" ) {
			// only show the map if locations have addresses entered
			$needmap = "false";
			foreach ($locations as $location) {
				if(isset($location['address']) && !isset($location['donotdisplay'])) {
					$needmap = "true";
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
							$htmladdress = htmlentities($location['address'], ENT_QUOTES);
							$popaddress = htmlentities($htmladdress, ENT_QUOTES); // why do I have to do this twice?  I have no idea, but it works... :P
							$htmlcity = htmlentities($location['city'], ENT_QUOTES);
							$popcity = htmlentities($htmlcity, ENT_QUOTES);
							$htmlstate = htmlentities($location['state'], ENT_QUOTES);
							$popstate = htmlentities($htmlstate, ENT_QUOTES);
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
		if (isset($options['tax_memberlevel']) && $options['tax_memberlevel'] == "1") { 
			$id = get_the_id();
			$levels = get_the_terms( $id, 'membership_level');
			if($levels) {
				$tax_content .= "<p class='membership'><span>" . __('Membership Level:', 'cdash') . "</span>&nbsp;";
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
				$tax_content .= "<p class='categories'><span>" . __('Categories:', 'cdash') . "</span>&nbsp;";
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
					$tax_content .= "<p class='website'><a href='" . $location['url'] . "' target='_blank'>" . $location['url'] . "</a></p>";
				}
				if (isset($options['tax_phone']) && $options['tax_phone'] == "1" && isset($location['phone'])) { 
					$tax_content .= "<p class='phone'>";
						$i = 1;
						$phones = $location['phone'];
						foreach($phones as $phone) {
							if($i !== 1) {
								$tax_content .= "<br />";
							}
							$tax_content .= "<a href='tel:" . $phone['phonenumber'] . "'>" . $phone['phonenumber'] . "</a>";
							if(isset($phone['phonetype'])) {
								$tax_content .= "&nbsp;(" . $phone['phonetype'] . "&nbsp;)";
							}
							$i++;
						}
					$tax_content .= "</p>";
				}
				if (isset($options['tax_email']) && $options['tax_email'] == "1" && isset($location['email'])) { 
					$tax_content .= "<p class='email'>";
						$i = 1;
						$emails = $location['email'];
						foreach($emails as $email) {
							if($i !== 1) {
								$tax_content .= "<br />";
							}
							$tax_content .= "<a href='mailto:" . $email['emailaddress'] . "'>" . $email['emailaddress'] . "</a>";
							if(isset($email['emailtype'])) {
								$tax_content .= "&nbsp;(&nbsp;" . $email['emailtype'] . "&nbsp;)";
							}
							$i++;
						}
					$tax_content .= "</p>";
				}
			$tax_content .= "</div>";
			}
		}
		if($options['bus_custom']) {
			$customfields = $options['bus_custom'];
			global $custom_metabox;
			$custommeta = $custom_metabox->the_meta();
			foreach($customfields as $field) { 
				if($field['display_dir'] !== "yes") {
					continue;
				} else {
					if(isset($custommeta[$field['name']]) && is_array($custommeta)) {
						$tax_content .= "<p><strong>" . $field['name'] . ":</strong>&nbsp;" . $custommeta[$field['name']] . "</p>";
					}	
				}
			}
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
			'display' => '', // options: address, url, phone, email, location_name, category, level
			'single_link' => 'yes', // options: yes, no
			'perpage' => '-1', // options: any number
			'orderby' => 'title', // options: date, modified, menu_order, rand
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
				  	$locations = $contactmeta['location'];
					foreach($locations as $location) {
						if(isset($location['donotdisplay']) && $location['donotdisplay'] == "1") {
							continue;
						} else {
						  	if(in_array("location_name", $displayopts)) {
						  		// $business_list .= "<p class='location-name'>" . $location['altname'] . "</p>";
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
								$business_list .= "<p class='phone'>";
									$i = 1;
									$phones = $location['phone'];
									foreach($phones as $phone) {
										if($i !== 1) {
											$business_list .= "<br />";
										}
										$business_list .= "<a href='tel:" . $phone['phonenumber'] . "'>" . $phone['phonenumber'] . "</a>";
										if(isset($phone['phonetype'])) {
											$business_list .= "&nbsp;(" . $phone['phonetype'] . "&nbsp;)";
										}
										$i++;
									}
								$business_list .= "</p>";
						  	} 
						  	if(in_array("email", $displayopts)) {
								$business_list .= "<p class='email'>";
									$i = 1;
									$emails = $location['email'];
									foreach($emails as $email) {
										if($i !== 1) {
											$business_list .= "<br />";
										}
										$business_list .= "<a href='mailto:" . $email['emailaddress'] . "'>" . $email['emailaddress'] . "</a>";
										if(isset($email['emailtype'])) {
											$business_list .= "&nbsp;(&nbsp;" . $email['emailtype'] . "&nbsp;)";
										}
										$i++;
									}
								$business_list .= "</p>";
							}
					  	} 
					  	if(in_array("url", $displayopts) && isset($location['url'])) {
					  		$business_list .= "<p class='website'><a href='" . $location['url'] . "' target='_blank'>" . $location['url'] . "</a></p>";
					  	} 
			  		}
			  		if(in_array("category", $displayopts)) {
						$id = get_the_id();
						$buscats = get_the_terms( $id, 'business_category');
						$business_list .= "<p class='categories'><span>" . __('Categories:', 'cdash') . "</span>&nbsp;";
						$i = 1;
						foreach($buscats as $buscat) {
							if($i !== 1) {
								$business_list .= ",&nbsp;";
							}
							$business_list .= $buscat->name;
							$i++;
						}
				  	}
				  	if(in_array("level", $displayopts)) {
						$id = get_the_id();
						$levels = get_the_terms( $id, 'membership_level');
						$business_list .= "<p class='membership'><span>" . __('Membership Level:', 'cdash') . "</span>&nbsp;";
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
					$customfields = $options['bus_custom'];
					global $custom_metabox;
					$custommeta = $custom_metabox->the_meta();
					foreach($customfields as $field) { 
						if($field['display_dir'] !== "yes") {
							continue;
						} else {
							if(isset($custommeta[$field['name']]) && is_array($custommeta)) {
								$business_list .= "<p><strong>" . $field['name'] . ":</strong>&nbsp;" . $custommeta[$field['name']] . "</p>";
							}	
						}
					}
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
							$htmladdress = htmlentities($location['address'], ENT_QUOTES);
							$popaddress = htmlentities($htmladdress, ENT_QUOTES); // why do I have to do this twice?  I have no idea, but it works... :P
							$htmlcity = htmlentities($location['city'], ENT_QUOTES);
							$popcity = htmlentities($htmlcity, ENT_QUOTES);
							$htmlstate = htmlentities($location['state'], ENT_QUOTES);
							$popstate = htmlentities($htmlstate, ENT_QUOTES);
							$htmltitle = htmlentities(get_the_title(), ENT_QUOTES);
							$poptitle = htmlentities($htmltitle, ENT_QUOTES);
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
// BUSINESS SEARCH SHORTCODE
// ------------------------------------------------------------------------

function cdash_business_search_shortcode() {
	$business_search = "";
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
            }

            $args['tax_query'] = array(
                $buscat_params,
            );

            if ( $searchtext ) {
            	$args['s'] = $searchtext;
            }
                
        $search_query = new WP_Query( $args );
		if ( $search_query->have_posts() ) :
			// Display the search results
			$business_search .= "<div id='search-results'>";
			$business_search .= "<h2>" . __('Search Results', 'cdash') . "</h2>";
			$business_search .= "<p><a href='#business-search'>" . __('Search again', 'cdash') . "</a></p>";
			while ( $search_query->have_posts() ) : $search_query->the_post();

				$business_search .= "<div class='search-result'>";
				$business_search .= "<h3><a href='" . get_the_permalink() . "'>" . get_the_title() . "</a></h3>";
				$options = get_option('cdash_directory_options');

				// make location/address metabox data available
				global $buscontact_metabox;
				$contactmeta = $buscontact_metabox->the_meta();

				// make logo metabox data available
				global $buslogo_metabox;
				$logometa = $buslogo_metabox->the_meta();

				global $post;

				if (($options['tax_thumb']) == "1") { 
					$business_search .= get_the_post_thumbnail( $post->ID, 'full');
				}
				if (($options['tax_logo']) == "1") { 
					$attr = array(
						'class'	=> 'alignleft logo',
					);
					$business_search .= wp_get_attachment_image($logometa['buslogo'], 'full', 0, $attr );
				}
				$business_search .= get_the_excerpt(); 
				if (($options['tax_memberlevel']) == "1") { 
					$id = get_the_id();
					$levels = get_the_terms( $id, 'membership_level');
					if($levels) {
						$business_search .= "<p class='membership'><span>" . __('Membership Level:', 'cdash') . "</span>&nbsp;";
						$i = 1;
						foreach($levels as $level) {
							if($i !== 1) {
								$business_search .= ",&nbsp;";
							}
							$business_search .= $level->name;
							$i++;
						}
					}
				}
				if (($options['tax_category']) == "1") { 
					$id = get_the_id();
					$buscats = get_the_terms( $id, 'business_category');
					if($buscats) {
						$business_search .= "<p class='categories'><span>" . __('Categories:', 'cdash') . "</span>&nbsp;";
						$i = 1;
						foreach($buscats as $buscat) {
							if($i !== 1) {
								$business_search .= ",&nbsp;";
							}
							$business_search .= $buscat->name;
							$i++;
						}
					}
				}
				$locations = $contactmeta['location'];
				foreach($locations as $location) {
					if($location['donotdisplay'] == "1") {
						continue;
					} else {
						$business_search .= "<div class='location'>";
						if (($options['tax_name']) == "1" && isset($location['altname'])) { 
							$business_search .= "<h3>" . $location['altname'] . "</h3>";
						}
						if (($options['tax_address']) == "1") { 
							$business_search .= "<p class='address'>";
			 					if(isset($location['address'])) {
									$address = $location['address'];
									$business_search .= str_replace("\n", '<br />', $address);
								}
								if(isset($location['city'])) {
									$business_search .= "<br />" . $location['city'] . ",&nbsp;";
								}
								if(isset($location['state'])) {
									$business_search .= $location['state'] . "&nbsp";
								}
								if(isset($location['zip'])) {
									$business_search .= $location['zip'];
								} 
							$business_search .= "</p>";
						}
						if (($options['tax_url']) == "1") { 
							$business_search .= "<p class='website'><a href='" . $location['url'] . "' target='_blank'>" . $location['url'] . "</a></p>";
						}
						if (($options['tax_phone']) == "1" && isset($location['phone'])) { 
							$business_search .= "<p class='phone'>";
								$i = 1;
								$phones = $location['phone'];
								foreach($phones as $phone) {
									if($i !== 1) {
										$business_search .= "<br />";
									}
									$business_search .= "<a href='tel:" . $phone['phonenumber'] . "'>" . $phone['phonenumber'] . "</a>";
									if(isset($phone['phonetype'])) {
										$business_search .= "&nbsp;(" . $phone['phonetype'] . "&nbsp;)";
									}
									$i++;
								}
							$business_search .= "</p>";
						}
						if (($options['tax_email']) == "1" && isset($location['email'])) { 
							$business_search .= "<p class='email'>";
								$i = 1;
								$emails = $location['email'];
								foreach($emails as $email) {
									if($i !== 1) {
										$business_search .= "<br />";
									}
									$business_search .= "<a href='mailto:" . $email['emailaddress'] . "'>" . $email['emailaddress'] . "</a>";
									if(isset($email['emailtype'])) {
										$business_search .= "&nbsp;(&nbsp;" . $email['emailtype'] . "&nbsp;)";
									}
									$i++;
								}
							$business_search .= "</p>";
						$business_search .= "</div><!-- .location -->";
						}
					$business_search .= "</div><!-- .search-result -->";
					}
				}
				if($options['bus_custom']) {
					$customfields = $options['bus_custom'];
					global $custom_metabox;
					$custommeta = $custom_metabox->the_meta();
					foreach($customfields as $field) { 
						if($field['display_dir'] !== "yes") {
							continue;
						} else {
							if(isset($custommeta[$field['name']]) && is_array($custommeta)) {
								$business_search .= "<p><strong>" . $field['name'] . ":</strong>&nbsp;" . $custommeta[$field['name']] . "</p>";
							}	
						}
					}
				}

				$business_search .= "</div><!-- #search-results --><div style='clear:both'></div>";
			endwhile;
			$total_pages = $search_query->max_num_pages;
			if ($total_pages > 1){
				$current_page = max(1, get_query_var('paged'));
   				$business_search .= "<div class='pagination'>";
			  	$business_search .= paginate_links(array(
			      'base' => get_pagenum_link(1) . '%_%',
			      'format' => '/page/%#%',
			      'current' => $current_page,
			      'total' => $total_pages,
			    ));
			    $business_search .= "</div>";
			}
			$business_search .= "</div>";
		endif;

		// Reset Post Data
		wp_reset_postdata();
	} 

	// Search form
	$business_search .= "<div id='business-search'><h3>" . __('Search', 'cdash') . "</h3>";
	$business_search .= "<form method='get' action='" . get_the_permalink() . "'>";
	$business_search .= "<p><label>" . __('Search Term', 'cdash') . "</label><br /><input type='text' value='' name='searchtext' id='searchtext' /></p>";
	// $business_search .= "<p><label>Business Name</label><br /><input type='text' value='' name='business_name' id='business_name' /></p>";
		// searching by business name seems like a good idea, but you can only query the slug, so if the name isn't exactly like the slug, it won't find anything
	// $business_search .= "<p><label>City</label><br /><input type='text' value='' name='city' id='city' /></p>";
		// I would really like to be able to search by city, but since WPAlchemy serializes the locations array, I don't think this is possible
	$business_search .= "<p><label>" . __('Business Category', 'cdash') . "</label><br /><select name='buscat'><option value=''>";
	$terms = get_terms( 'business_category', 'hide_empty=0' );
        foreach ($terms as $term) {
            $business_search .= "<option value='" . $term->slug . "'>" . $term->name;
        } 
    $business_search .= "</select></p>";
	
	$business_search .= "<input type='submit' value='" . __('Search', 'cdash') . "'>";
	$business_search .= "</form>";
	$business_search .= "</div>";

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
// add business category and member level slugs as body and post class
// ------------------------------------------------------------------------

function cdash_add_taxonomy_classes($classes) {
	global $post;
	if($post) {
		$buscats = get_the_terms($post->ID, 'business_category');
		if ($buscats) {
			foreach($buscats as $taxonomy) {
				$classes[] = $taxonomy->slug;
			}
		}
		$buslevels = get_the_terms($post->ID, 'membership_level');
		if ($buslevels) {
			foreach($buslevels as $taxonomy) {
				$classes[] = $taxonomy->slug;
			}
		}
		return $classes;
	}

}
add_filter('post_class', 'cdash_add_taxonomy_classes');
add_filter('body_class', 'cdash_add_taxonomy_classes');

?>
<?php
/*
Plugin Name: Chamber Dashboard Business Directory
Plugin URI: http://chamberdashboard.com
Description: Crate a database of the businesses in your chamber of commerce
Version: 2.7.4
Author: Morgan Kay
Author URI: http://wpalchemists.com
Text Domain: cdash
*/

/*  Copyright 2015 Morgan Kay and Chamber Dashboard (email : info@chamberdashboard.com)

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
			wp_die( "'".$plugin_data['Name']."' requires WordPress 3.8 or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to <a href='".admin_url()."'>WordPress admin</a>." );
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
require_once( plugin_dir_path( __FILE__ ) . 'includes/welcome-page.php' );
// Require views
require_once( plugin_dir_path( __FILE__ ) . 'views.php' );
// Require widgets
require_once( plugin_dir_path( __FILE__ ) . 'widgets.php' );
// Require currency list
require_once( plugin_dir_path( __FILE__ ) . 'includes/currency_list.php' );



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
		'menu_name'                  => __( 'Business Categories', 'cdash' ),
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
		'menu_name'                  => __( 'Membership Levels', 'cdash' ),
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

// Register Custom Taxonomy - Private Category
function cdash_register_taxonomy_private_category() {

	$labels = array(
		'name'                       => _x( 'Private Categories', 'Taxonomy General Name', 'cdash' ),
		'singular_name'              => _x( 'Private Category', 'Taxonomy Singular Name', 'cdash' ),
		'menu_name'                  => __( 'Private Categories', 'cdash' ),
		'all_items'                  => __( 'All Private Categories', 'cdash' ),
		'parent_item'                => __( 'Parent Private Category', 'cdash' ),
		'parent_item_colon'          => __( 'Parent Private Category:', 'cdash' ),
		'new_item_name'              => __( 'New Private Category Name', 'cdash' ),
		'add_new_item'               => __( 'Add New Private Category', 'cdash' ),
		'edit_item'                  => __( 'Edit Private Category', 'cdash' ),
		'update_item'                => __( 'Update Private Category', 'cdash' ),
		'separate_items_with_commas' => __( 'Separate Private Categories with commas', 'cdash' ),
		'search_items'               => __( 'Search Private Categories', 'cdash' ),
		'add_or_remove_items'        => __( 'Add or remove Private Category', 'cdash' ),
		'choose_from_most_used'      => __( 'Choose from the most used Private Categories', 'cdash' ),
		'not_found'                  => __( 'Not Found', 'cdash' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => false,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
		'rewrite' => array (
            'slug' => _x( 'private_category', 'private_category', 'cdash' )
        )
	);
	register_taxonomy( 'private_category', array( 'business' ), $args );

}

add_action( 'init', 'cdash_register_taxonomy_private_category', 0 );


// Register Custom Post Type - Businesses
function cdash_register_cpt_business() {

	$options = get_option( 'cdash_directory_options' );
	$supports = array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', );
	if( isset( $options['sv_comments'] ) && "1" == $options['sv_comments'] ) {
		$supports[] = 'comments';
	}

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
		'supports'            => $supports,
		'taxonomies'          => array( 'business_category', ' membership_level', 'private_category' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		//'menu_icon'           => 'dashicons-shop',
		'menu_icon'           => plugin_dir_url( __FILE__ ) . '/images/cdash-business.png',
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
	include_once 'wpalchemy/MediaAccess.php';
	$wpalchemy_media_access = new WPAlchemy_MediaAccess();
}

define( 'CDASH_PATH', plugin_dir_path(__FILE__) );

// Enqueue styles and scripts
function cdash_admin_scripts_and_styles($hook)
{
    if ( is_admin() ) {
        wp_enqueue_style( 'wpalchemy-metabox', plugins_url() . '/chamber-dashboard-business-directory/wpalchemy/meta.css' );
    }

    global $post;

    // business AJAX
    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
	    if ( isset( $post ) && 'business' === $post->post_type ) {     
	    	wp_enqueue_script( 'google-maps' , 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDF-0o3jloBzdzSx7rMlevwNSOyvq0G35A&sensor=false' );  
		    wp_enqueue_script( 'business-meta', plugin_dir_url(__FILE__) . 'js/cdash-business-meta.js', array( 'jquery' ) );
		    // wp_localize_script( 'business-meta', 'businessajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) ); 
		}
	}
}
add_action( 'admin_enqueue_scripts', 'cdash_admin_scripts_and_styles' );

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

// Create metabox for billing information
$billing_metabox = new WPAlchemy_MetaBox(array
(
    'id' => 'billing_meta',
    'title' => 'Billing Information',
    'types' => array('business'),
    'template' => CDASH_PATH . '/wpalchemy/busbilling.php',
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
if( !empty( $options['bus_custom'] ) ) {
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

// if member manager isn't installed, create metabox to advertise it
function cdash_promote_member_manager() {
	if( !function_exists( 'cdashmm_language_init' ) ) {
		$advert_metabox = new WPAlchemy_MetaBox(array
		(
		    'id' => 'cdash_advert',
		    'title' => 'Membership Payments',
		    'types' => array('business'),
		    'template' => CDASH_PATH . '/wpalchemy/advert.php',
		    'mode' => WPALCHEMY_MODE_EXTRACT,
		    'prefix' => '_cdash_',
		    'context' => 'side',
	        'priority' => 'default'
		));
	}
}
add_action( 'plugins_loaded', 'cdash_promote_member_manager' );


// ------------------------------------------------------------------------
// SET UP P2P IF OTHER PLUGINS NEED IT
// ------------------------------------------------------------------------

function cdash_p2p_check() {
	if( defined('CDCRM_PATH') || defined('CDASHMM_STATUS') ) {
		if ( !class_exists( 'P2P_Autoload' ) ) {
			require_once dirname( __FILE__ ) . '/wpp2p/autoload.php';
		}
		if( !defined( 'P2P_PLUGIN_VERSION') ) {
			define( 'P2P_PLUGIN_VERSION', '1.6.3' );
		}
		if( !defined( 'P2P_TEXTDOMAIN') ) {
			define( 'P2P_TEXTDOMAIN', 'cdash' );
		}
	}
}

add_action( 'admin_init', 'cdash_p2p_check' );

function cdash_p2p_load() {
	if ( !class_exists( 'P2P_Autoload' ) && ( defined('CDCRM_PATH') || defined('CDASHMM_STATUS') ) ) {
		//load_plugin_textdomain( P2P_TEXTDOMAIN, '', basename( dirname( __FILE__ ) ) . '/languages' );
		if ( !function_exists( 'p2p_register_connection_type' ) ) {
			require_once dirname( __FILE__ ) . '/wpp2p/autoload.php';
		}
		P2P_Storage::init();
		P2P_Query_Post::init();
		P2P_Query_User::init();
		P2P_URL_Query::init();
		P2P_Widget::init();
		P2P_Shortcodes::init();
		register_uninstall_hook( __FILE__, array( 'P2P_Storage', 'uninstall' ) );
		if ( is_admin() )
			cdash_load_admin();
	}
}

function cdash_load_admin() {
	if ( defined('CDCRM_PATH') || defined('CDASHMM_STATUS') ) {
		P2P_Autoload::register( 'P2P_', dirname( __FILE__ ) . '/wpp2p/admin' );
		new P2P_Box_Factory;
		new P2P_Column_Factory;
		new P2P_Dropdown_Factory;
		new P2P_Tools_Page;
	}
}

function cdash_p2p_init() {
	if ( defined('CDCRM_PATH') || defined('CDASHMM_STATUS') ) {
		// Safe hook for calling p2p_register_connection_type()
		do_action( 'p2p_init' );
	}
}

require dirname( __FILE__ ) . '/wpp2p/scb/load.php';
scb_init( 'cdash_p2p_load' );
add_action( 'wp_loaded', 'cdash_p2p_init' );


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
    	if( isset($contactmeta['location']) && is_array( $contactmeta['location'] ) ) {
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
		}
        echo $phonenumbers;
    }    
}

add_filter('manage_business_posts_columns', 'cdash_business_overview_columns_headers', 10);
add_action('manage_business_posts_custom_column', 'cdash_business_overview_columns', 10, 2);


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


// ------------------------------------------------------------------------
// SAVE GEOLOCATION DATA, with extra noodles to make sure this runs very last when business is saved
// This is a fallback in case JavaScript didn't save geolocation data
// ------------------------------------------------------------------------

function cdash_get_latest_priority( $filter ) // figure out what priority the geolocation function needs, thanks to http://wordpress.stackexchange.com/questions/116221/how-to-force-function-to-run-as-the-last-one-when-saving-the-post
{
    if ( empty ( $GLOBALS['wp_filter'][ $filter ] ) )
        return PHP_INT_MAX;

    $priorities = array_keys( $GLOBALS['wp_filter'][ $filter ] );
    $last       = end( $priorities );

    if ( is_numeric( $last ) )
        return PHP_INT_MAX;

    return "$last-z";
}
add_action( 'save_post', 'cdash_run_that_action_last', 0 ); 

function cdash_run_that_action_last() {  // add the action now, with lowest priority so it runs after meta data has been saved
    add_action( 
        'save_post', 
        'cdash_store_geolocation_data',
        cdash_get_latest_priority( current_filter() ),
        2 
    ); 

}

function cdash_store_geolocation_data( $post_id ) {

	// get the addresses
	$locations = get_post_meta( $post_id, '_cdash_location', true );

	if( !empty( $locations ) && is_array( $locations ) ) {
		foreach( $locations as $key => $location ) {
			if( !isset( $location['latitude'] ) && !isset( $location['longitude'] ) ) { // don't do this if we already have lat and long
				if( isset( $location['city'] ) ) {
					// ask Google for the latitude and longitude
					$rawaddress = $location['address'];
					if( isset( $location['city'] ) ) {
						$rawaddress .= ' ' . $location['city'];
					}
					if( isset( $location['state'] ) ) {
						$rawaddress .= ' ' . $location['state'];
					}
					if( isset( $location['zip'] ) ) {
						$rawaddress .= ' ' . $location['zip'];
					}
					if( isset( $location['country'] ) ) {
						$rawaddress .= ' ' . $location['country'];
					}
					$address = urlencode( $rawaddress );
					$json = wp_remote_get( "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyBq9JVPgmORIVfuzmgpzrzRTVyttSNyJ3A&address=" . $address . "&sensor=true" );
					$json = json_decode($json['body'], true);
					if( is_array( $json ) && $json['status'] == 'OK') {
						$locations[$key]['latitude'] = $json['results'][0]['geometry']['location']['lat'];
						$locations[$key]['longitude'] = $json['results'][0]['geometry']['location']['lng']; 
					} else {
						$locations[$key]['latitude'] = '0';
						$locations[$key]['longitude'] = '0';
					}
				}
			}
		}
		// save the latitude and longitude
		update_post_meta( $post_id, '_cdash_location', $locations );
	}
}

// ------------------------------------------------------------------------
// ADD GEOLOCATION DATA TO BUSINESSES CREATED BEFORE VERSION 3
// ------------------------------------------------------------------------

// make activation hook that checks for existence of businesses, updates geolocation data, and saves geolocation_updated option
register_activation_hook(__FILE__, 'cdash_activation_geolocation_check');

function cdash_activation_geolocation_check() {
	// if we have stored the geolocation option, we don't need to do this
	$options = get_option('cdash_directory_version');
	if( "yes" == $options['geolocation_updated'] ) {
		// do nothing
	} else { 
		// check if there are businesses
		$args = array( 'post_type' => 'business' );
		$businesses = get_posts( $args );
		if( is_array( $businesses ) ) {
			// there are businesses, so we need to update them
			cdash_find_and_update_all_business_geolocation( 'no-return' );
		} else {
			// there are no businesses, so we can save the geolocation option and move on
			$options['geolocation_updated'] = "yes";
    		update_option( 'cdash_directory_version', $options );
		}
	}
}

// add admin_init that checks for geolocation_updated option and displays "need update" message
add_action( 'admin_init', 'cdash_check_geolocation' );

function cdash_check_geolocation() {
	// if we have stored the geolocation option, we don't need to do this
	$options = get_option('cdash_directory_version');
	if( "yes" == $options['geolocation_updated'] ) {
		// do nothing
	} else { 
		// check if there are businesses
		$args = array( 'post_type' => 'business' );
		$businesses = get_posts( $args );
		if( is_array( $businesses ) ) {
			// add admin notice 
			add_action( 'admin_notices', 'cdash_ask_to_update_geolocation' );
		} 
	}

}

function cdash_ask_to_update_geolocation() {
	?>
    <div class="update-nag">
        <p><?php _e( 'Chamber Dashboard needs to update your database to ensure that your maps display correctly.', 'cdash' ); ?></p>
        <p><a class="button submit-button" href="<?php echo admin_url( 'admin.php?page=chamber-dashboard-update-geolocation' ); ?>"><?php _e( 'Update Now', 'cdash' ); ?></a></p>
    </div>
    <?php
}


function cdash_update_geolocation_data_page() {
	// TODO - add a nonce ?>

	<div class="wrap">

		<h2><img src="<?php echo plugin_dir_url( __FILE__ ) . '/images/cdash-32.png'?>"><?php _e('Chamber Dashboard Business Directory', 'cdash'); ?></h2>

		<?php 
		// make sure we haven't done this before
		$options = get_option('cdash_directory_options');
		if( "yes" == $options['geolocation_updated'] ) { ?>
			<p><?php _e( 'Your businesses are already up to date!', 'cdash' ); ?></p>
		<?php } else {
				$number = cdash_find_and_update_all_business_geolocation( 'return' );
			 ?>

		    <p><?php echo $number . __( ' businesses were updated.  Thank you!', 'cdash' ); ?></p>
		 <?php } ?>

	</div>
    
<?php }

function cdash_find_and_update_all_business_geolocation( $return ) {

	$args = array( 
        'post_type' => 'business',
        'posts_per_page' => -1,
        'post_status' => 'any',
    );
    
    $businesses = new WP_Query( $args );
    $i = 0;
    
    if ( $businesses->have_posts() ) :
	    while ( $businesses->have_posts() ) : $businesses->the_post();
	    	$id = get_the_id();
	    	cdash_store_geolocation_data( $id );
	    	$i++;
	    endwhile;
    endif;
    
    wp_reset_postdata(); 
    
    $options = get_option('cdash_directory_version');
    $options['geolocation_updated'] = "yes";
    update_option( 'cdash_directory_version', $options );

    if( "return" == $return ) {
	    return $i;
	}

}

?>
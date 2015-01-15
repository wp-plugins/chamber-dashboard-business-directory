<?php
/*
Plugin Name: Chamber Dashboard Business Directory
Plugin URI: http://chamberdashboard.com
Description: Create a database of the businesses in your chamber of commerce
Version: 2.0
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

// Create metabox for location/address information
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
    	if( isset( $contactmeta['location'] ) ) {
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

?>

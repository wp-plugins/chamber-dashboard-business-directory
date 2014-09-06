<?php 
// force WP functionality to load so we can use wp_query
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );

//output the headers for the CSV file
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Description: File Transfer');
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=chamber-dashboard-export.csv");
header("Expires: 0");
header("Pragma: public");

//open the file 
$fh = @fopen( 'php://output', 'w' );

// Find all the businesses
$args = array( 
    'post_type' => 'business', 
    'posts_per_page' => -1,   
    'order' => 'ASC',
);

// Find out how many locations there are, so that we know how many columns to put in the CSV
$exportquery = new WP_Query( $args );
$numberoflocations = 0;

if ( $exportquery->have_posts() ) :
	while ( $exportquery->have_posts() ) : $exportquery->the_post();
		$id = get_the_id();
		$variable = get_post_meta($id, '_cdash_location', true);
		if(sizeof($variable) > $numberoflocations) {
			$numberoflocations= sizeof($variable);
		}
	endwhile;
endif;

// Get a list of headers we need for each business
$header = array(
	'Business Name',
	'Description',
	'Category',
	'Membership Level',
);

// Get a list of the headers we need for each location
$locationheaders = array( 
	'Location Name',
	'Address',
	'City',
	'State',
	'Zip',
	'URL',
	'Phone',
	'Email',
);

// Add location headers to the list of business headers
for ($i=0; $i < $numberoflocations; $i++) { 
	$header = array_merge($header, $locationheaders);
}
unset($i);

// Add the headers to the CSV			
fputcsv($fh, $header);

// Loop through businesses and add a line to the CSV for each business
if ( $exportquery->have_posts() ) :
while ( $exportquery->have_posts() ) : $exportquery->the_post();
	$cats = wp_get_post_terms($post->ID, 'business_category', array("fields" => "names"));
	$catlist = implode(", ", $cats);
	$levels = wp_get_post_terms($post->ID, 'membership_level', array("fields" => "names"));
	$levellist = implode(", ", $levels);
	$fields = array(
		get_the_title(),
		get_the_content(),
		$catlist,
		$levellist,
		);
	global $buscontact_metabox;
	$contactmeta = $buscontact_metabox->the_meta();
	$locations = $contactmeta['location'];
	foreach($locations as $location) {
		$locationinfo = array(
			'altname' => '',
			'address' => '',
			'city' => '',
			'state' => '',
			'zip' => '',
			'url' => '',
			);
		foreach ($locationinfo as $key => $value) {

			if(isset($location[$key])) {
				#$locationinfo[$key.$location_number] = $location[$key];
				$fields[] = $location[$key];
			} else {
				#$locationinfo[$key.$location_number] = '';
				$fields[] = '';
			}
		}


		if(isset($location['phone'])) {
			$phones = $location['phone'];
			if( is_array($phones)) {
				$phoneinfo = '';
				foreach($phones as $phone) {
					$phoneinfo .= $phone['phonenumber'] . " (" . $phone['phonetype'] . ")";
				}
				$fields[] = $phoneinfo;
			} 
		} else {
			$fields[] = '';
		}

		if(isset($location['email'])) {
			$emails = $location['email'];
			if(is_array($emails)) {
				$emailinfo = '';
				foreach($emails as $email) {
					$emailinfo .= $email['emailaddress'] . "(" . $email['emailtype'] . ")";
				}
				$fields[] = $emailinfo;
			}
		} else {
			$fields[] = '';
		}
	}

// Add the business to the CSV
	fputcsv($fh, $fields);

endwhile;
endif;

// Reset Post Data
wp_reset_postdata(); 
// Close the file stream
fclose($fh);
?>
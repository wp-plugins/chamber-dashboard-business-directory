<?php
/* Options Page for Chamber Dashboard Business Directory */

// --------------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_uninstall_hook(__FILE__, 'cdash_delete_plugin_options')
// --------------------------------------------------------------------------------------

// Delete options table entries ONLY when plugin deactivated AND deleted
function cdash_delete_plugin_options() {
	delete_option('cdash_directory_options');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'cdash_add_defaults')
// ------------------------------------------------------------------------------

// Define default option settings
function cdash_add_defaults() {
	$tmp = get_option('cdash_directory_options');
    if(!is_array($tmp)) {
		delete_option('cdash_directory_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(	"bus_phone_type" => "Main, Office, Cell",
						"bus_email_type" => "Main, Sales, Accounting, HR",
						"sv_description" => "1",
						"sv_name"		 => "1",
						"sv_address"	 => "1",
						"sv_url"		 => "1",
						"sv_logo"		 => "1",
						"sv_category"	 => "1",
						"tax_logo"		 => "1"
		);
		update_option('cdash_directory_options', $arr);
	}
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_init', 'cdash_init' )
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_init' HOOK FIRES, AND REGISTERS YOUR PLUGIN
// SETTING WITH THE WORDPRESS SETTINGS API. YOU WON'T BE ABLE TO USE THE SETTINGS
// API UNTIL YOU DO.
// ------------------------------------------------------------------------------

// Init plugin options to white list our options
function cdash_init(){
	register_setting( 'cdash_plugin_options', 'cdash_directory_options', 'cdash_validate_options' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_menu', 'cdash_add_options_page');
// ------------------------------------------------------------------------------

// Add menu page
function cdash_add_options_page() {
	add_menu_page( 
		'Chamber Dashboard', 
		'Chamber Dashboard', 
		'manage_options', 
		'/chamber-dashboard-business-directory/options.php', 
		'cdash_render_form', 
		'dashicons-admin-generic', 
		85 
	);
	add_submenu_page( '/chamber-dashboard-business-directory/options.php', 'Export', 'Export', 'manage_options', 'chamber-dashboard-export', 'cdash_export_form' );
	add_submenu_page( '/chamber-dashboard-business-directory/options.php', 'Import', 'Import', 'manage_options', 'chamber-dashboard-import', 'cdash_import_form' );
}


// ------------------------------------------------------------------------------
// CALLBACK FUNCTION SPECIFIED IN: add_options_page()
// ------------------------------------------------------------------------------
// THIS FUNCTION IS SPECIFIED IN add_options_page() AS THE CALLBACK FUNCTION THAT
// ACTUALLY RENDER THE PLUGIN OPTIONS FORM AS A SUB-MENU UNDER THE EXISTING
// SETTINGS ADMIN MENU.
// ------------------------------------------------------------------------------

// Render the Plugin options form
function cdash_render_form() {
	?>
	<div class="wrap">
		
		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php _e('Chamber Dashboard Business Directory Settings', 'cdash'); ?></h2>


		<div id="main" style="width: 70%; min-width: 350px; float: left;">
			<!-- Beginning of the Plugin Options Form -->
			<form method="post" action="options.php">
				<?php settings_fields('cdash_plugin_options'); ?>
				<?php $options = get_option('cdash_directory_options'); ?>

				<!-- Table Structure Containing Form Controls -->
				<!-- Each Plugin Option Defined on a New Table Row -->
				<table class="form-table">

					<!-- Phone Number types -->
					<tr>
						<th scope="row"><?php _e('Phone Number Types', 'cdash'); ?></th>
						<td>
							<input type="text" size="57" name="cdash_directory_options[bus_phone_type]" value="<?php echo $options['bus_phone_type']; ?>" />
							<br /><span style="color:#666666;margin-left:2px;"><?php _e('When you enter a phone number for a business, you can choose what type of phone number it is.  The default options are "Main, Office, Cell".  To change these options, enter a comma-separated list here.  (Note: your entry will over-ride the default, so if you still want main and/or office and/or cell, you will need to enter them.)', 'cdash'); ?></span>
						</td>
					</tr>

					<!-- Email types -->
					<tr>
						<th scope="row"><?php _e('Email Types', 'cdash'); ?></th>
						<td>
							<input type="text" size="57" name="cdash_directory_options[bus_email_type]" value="<?php echo $options['bus_email_type']; ?>" />
							<br /><span style="color:#666666;margin-left:2px;"><?php _e('When you enter an email address for a business, you can choose what type of email address it is.  The default options are "Main, Sales, Accounting, HR".  To change these options, enter a comma-separated list here.  (Note: your entry will over-ride the default, so if you still want main and/or sales and/or accounting and/or HR, you will need to enter them.)', 'cdash'); ?></span>
						</td>
					</tr>	

					<!-- Single View Options -->
					<tr valign="top">
						<th scope="row"><?php _e('Single Business View Options', 'cdash'); ?></th>
						<td>
							<span style="color:#666666;margin-left:2px;"><?php _e('What information would you like to display on the single business view?', 'cdash'); ?></span><br />
							<label><input name="cdash_directory_options[sv_description]" type="checkbox" value="1" <?php if (isset($options['sv_description'])) { checked('1', $options['sv_description']); } ?> /><?php _e(' Description', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[sv_name]" type="checkbox" value="1" <?php if (isset($options['sv_name'])) { checked('1', $options['sv_name']); } ?> /><?php _e(' Location Name <em>Note: you can hide individual locations in the "edit business" view</em>', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[sv_address]" type="checkbox" value="1" <?php if (isset($options['sv_address'])) { checked('1', $options['sv_address']); } ?> /><?php _e(' Location Address', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[sv_map]" type="checkbox" value="1" <?php if (isset($options['sv_map'])) { checked('1', $options['sv_map']); } ?> /><?php _e(' Map', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[sv_url]" type="checkbox" value="1" <?php if (isset($options['sv_url'])) { checked('1', $options['sv_url']); } ?> /><?php _e(' Location Web Address', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[sv_phone]" type="checkbox" value="1" <?php if (isset($options['sv_phone'])) { checked('1', $options['sv_phone']); } ?> /><?php _e(' Phone Number(s)', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[sv_email]" type="checkbox" value="1" <?php if (isset($options['sv_email'])) { checked('1', $options['sv_email']); } ?> /><?php _e(' Email Address(es)', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[sv_logo]" type="checkbox" value="1" <?php if (isset($options['sv_logo'])) { checked('1', $options['sv_logo']); } ?> /><?php _e(' Logo', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[sv_thumb]" type="checkbox" value="1" <?php if (isset($options['sv_thumb'])) { checked('1', $options['sv_thumb']); } ?> /><?php _e(' Featured Image <em>Your theme might already display the featured image.  If it does not, you can check this box to display the featured image</em>', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[sv_memberlevel]" type="checkbox" value="1" <?php if (isset($options['sv_memberlevel'])) { checked('1', $options['sv_memberlevel']); } ?> /><?php _e(' Membership Level', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[sv_category]" type="checkbox" value="1" <?php if (isset($options['sv_category'])) { checked('1', $options['sv_category']); } ?> /><?php _e(' Business Categories', 'cdash'); ?></label><br />
						</td>
					</tr>

					<!-- Category/Membership Level View Options -->
					<tr valign="top">
						<th scope="row"><?php _e('Category/Membership Level View Options', 'cdash'); ?></th>
						<td>
							<span style="color:#666666;margin-left:2px;"><?php _e('What information would you like to display on the category/membership level view?  Note: Chamber Dashboard might not be able to over-ride all of your theme settings (for instance, your theme might show the featured image on category pages).  If you don\'t like how your theme displays category and membership level pages, you might want to create custom pages using the [business_directory] shortcode.  This is more labor-intensive, but gives you more control over appearance.', 'cdash'); ?></span><br />
							<label><input name="cdash_directory_options[tax_name]" type="checkbox" value="1" <?php if (isset($options['tax_name'])) { checked('1', $options['tax_name']); } ?> /><?php _e(' Location Name <em>Note: you can hide individual locations in the "edit business" view</em>', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[tax_address]" type="checkbox" value="1" <?php if (isset($options['tax_address'])) { checked('1', $options['tax_address']); } ?> /><?php _e(' Location Address', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[tax_url]" type="checkbox" value="1" <?php if (isset($options['tax_url'])) { checked('1', $options['tax_url']); } ?> /><?php _e(' Location Web Address', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[tax_phone]" type="checkbox" value="1" <?php if (isset($options['tax_phone'])) { checked('1', $options['tax_phone']); } ?> /><?php _e(' Phone Number(s)', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[tax_email]" type="checkbox" value="1" <?php if (isset($options['tax_email'])) { checked('1', $options['tax_email']); } ?> /><?php _e(' Email Address(es)', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[tax_logo]" type="checkbox" value="1" <?php if (isset($options['tax_logo'])) { checked('1', $options['tax_logo']); } ?> /><?php _e(' Logo', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[tax_thumb]" type="checkbox" value="1" <?php if (isset($options['tax_thumb'])) { checked('1', $options['tax_thumb']); } ?> /><?php _e(' Featured Image <em>Your theme might already display the featured image.  If it does not, you can check this box to display the featured image</em>', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[tax_memberlevel]" type="checkbox" value="1" <?php if (isset($options['tax_memberlevel'])) { checked('1', $options['tax_memberlevel']); } ?> /><?php _e(' Membership Leve', 'cdash'); ?>l</label><br />
							<label><input name="cdash_directory_options[tax_category]" type="checkbox" value="1" <?php if (isset($options['tax_category'])) { checked('1', $options['tax_category']); } ?> /><?php _e(' Business Categories', 'cdash'); ?></label><br />
						</td>
					</tr>				

					<!-- Custom Fields -->
					<tr>
						<th scope="row"><?php _e('Custom Fields', 'cdash'); ?></th>
						<td>
							<p><span style="color:#666666;margin-left:2px;"><?php _e('If you need to store additional information about businesses, you can create custom fields here.', 'cdash'); ?></span></p><br />
							<?php if(!empty($options['bus_custom'])) {
								$customfields = $options['bus_custom'];
								$i = 1;
								foreach($customfields as $field) { ?>
									<div class="repeating" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
										<p><strong><?php _e('Custom Field Name', 'cdash'); ?></strong></p>
										<p><span style="color:#666666;margin-left:2px;"><?php _e('<strong>Note:</strong> If you change the name of an existing custom field, you will lose all data stored in that field!', 'cdash'); ?></span></p>
											<input type="text" size="30" name="cdash_directory_options[bus_custom][<?php echo $i; ?>][name]" value="<?php echo $field['name']; ?>" />
										<p><strong><?php _e('Custom Field Type', 'cdash'); ?></strong></p>	
											<select name='cdash_directory_options[bus_custom][<?php echo $i; ?>][type]'>
												<option value=''></option>
												<option value='text' <?php selected('text', $field['type']); ?>><?php _e('Short Text Field', 'cdash'); ?></option>
												<option value='textarea' <?php selected('textarea', $field['type']); ?>><?php _e('Multi-line Text Area', 'cdash'); ?></option>
											</select>
										<p><strong><?php _e('Display in Business Directory?', 'cdash'); ?></strong></p>	
											<label><input name="cdash_directory_options[bus_custom][<?php echo $i; ?>][display_dir]" type="radio" value="yes" <?php checked('yes', $field['display_dir']); ?> /><?php _e(' Yes', 'cdash'); ?></label><br />
											<label><input name="cdash_directory_options[bus_custom][<?php echo $i; ?>][display_dir]" type="radio" value="no" <?php checked('no', $field['display_dir']); ?> /><?php _e(' No', 'cdash'); ?></label><br />

										<p><strong><?php _e('Display in Single Business View?', 'cdash'); ?></strong></p>
											<label><input name="cdash_directory_options[bus_custom][<?php echo $i; ?>][display_single]" type="radio" value="yes" <?php checked('yes', $field['display_single']); ?> /><?php _e(' Yes', 'cdash'); ?></label><br />
											<label><input name="cdash_directory_options[bus_custom][<?php echo $i; ?>][display_single]" type="radio" value="no" <?php checked('no', $field['display_single']); ?> /><?php _e(' No', 'cdash'); ?></label><br />	
										<p><a href="#" class="repeat"><?php _e('Add Another', 'cdash'); ?></a></p>
									</div>
									<?php $i++;
								}
							} else { ?>
								<div class="repeating" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
									<p><strong><?php _e('Custom Field Name', 'cdash'); ?></strong></p>
										<input type="text" size="30" name="cdash_directory_options[bus_custom][1][name]" value="<?php echo $options['bus_custom'][1]['name']; ?>" />
									<p><strong><?php _e('Custom Field Type'); ?></strong></p>	
										<select name='cdash_directory_options[bus_custom][1][type]'>
											<option value=''></option>
											<option value='text' <?php selected('one', $options['bus_custom'][1]['type']); ?>><?php _e('Short Text Field', 'cdash'); ?></option>
											<option value='textarea' <?php selected('two', $options['bus_custom'][1]['type']); ?>><?php _e('Multi-line Text Area', 'cdash'); ?></option>
										</select>
									<p><strong><?php _e('Display in Business Directory?', 'cdash'); ?></strong></p>	
										<label><input name="cdash_directory_options[bus_custom][1][display_dir]" type="radio" value="yes" <?php checked('yes', $options['bus_custom'][1]['display_dir']); ?> /><?php _e(' Yes', 'cdash'); ?></label><br />
										<label><input name="cdash_directory_options[bus_custom][1][display_dir]" type="radio" value="no" <?php checked('no', $options['bus_custom'][1]['display_dir']); ?> /><?php _e(' No', 'cdash'); ?></label><br />

									<p><strong><?php _e('Display in Single Business View?', 'cdash'); ?></strong></p>
										<label><input name="cdash_directory_options[bus_custom][1][display_single]" type="radio" value="yes" <?php checked('yes', $options['bus_custom'][1]['display_single']); ?><?php _e(' /> Yes', 'cdash'); ?></label><br />
										<label><input name="cdash_directory_options[bus_custom][1][display_single]" type="radio" value="no" <?php checked('no', $options['bus_custom'][1]['display_single']); ?><?php _e(' /> No', 'cdash'); ?></label><br />	
									<p><a href="#" class="repeat"><?php _e('Add Another', 'cdash'); ?></a></p>
								</div>
							<?php } ?>
						</td>
					</tr>	


				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cdash') ?>" />
				</p> 
			</form>

			<script type="text/javascript">
			// Add a new repeating section
			var attrs = ['for', 'id', 'name'];
			function resetAttributeNames(section) { 
			    var tags = section.find('input, label'), idx = section.index();
			    tags.each(function() {
			      var $this = jQuery(this);
			      jQuery.each(attrs, function(i, attr) {
			        var attr_val = $this.attr(attr);
			        if (attr_val) {
			            $this.attr(attr, attr_val.replace(/\[bus_custom\]\[\d+\]\[/, '\[bus_custom\]\['+(idx + 1)+'\]\['))
			        }
			      })
			    })
			}
			                   
			jQuery('.repeat').click(function(e){
			        e.preventDefault();
			        var lastRepeatingGroup = jQuery('.repeating').last();
			        var cloned = lastRepeatingGroup.clone(true)  
			        cloned.insertAfter(lastRepeatingGroup);
			        cloned.find("input").val("");
			        cloned.find("select").val("");
			        cloned.find("input:radio").attr("checked", false);
			        resetAttributeNames(cloned)
			    });

			</script>
		</div><!-- #main -->
		<div id="sidebar" style="width: 28%; float: right; min-width: 150px;">
			<h3><?php _e('Documentation', 'cdash'); ?></h3>
			<p><?php _e('If you\'re looking for more information about how to use this plugin, visit the <a href="http://chamberdashboard.com/support/documentation/" target="_blank">Documentation page at ChamberDashboard.com', 'cdash'); ?></a></p>
			<h3><?php _e('Contact', 'cdash'); ?></h3>
			<p><?php _e('Don\'t hesitate to <a href="http://chamberdashboard.com/contact/" target="_blank">contact us</a> to request new features, ask questions, or just say hi.', 'cdash'); ?></p>
			<h3><?php _e('Other Chamber Dashboard Plugins', 'cdash'); ?></h3>
			<p><?php _e('This plugin is designed to work with the <a href="https://wordpress.org/plugins/chamber-dashboard-crm/" target="_blank">Chamber Dashboard CRM plugin</a> - keep track of the people associated with your businesses!', 'cdash'); ?></p> 
			<h3><?php _e('Donate', 'cdash'); ?></h3>
			<p><?php _e('All donations go to the <a href="http://fremont.com" target="_blank">Fremont Chamber of Commerce</a> to support further development of Chamber Dashboard.', 'cdash'); ?></p>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
			<input type="hidden" name="cmd" value="_donations">
			<input type="hidden" name="business" value="director@fremont.com">
			<input type="hidden" name="lc" value="US">
			<input type="hidden" name="item_name" value="Fremont Chamber of Commerce">
			<input type="hidden" name="item_number" value="Chamber Dashboard">
			<input type="hidden" name="no_note" value="0">
			<input type="hidden" name="currency_code" value="USD">
			<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>

		</div>
	</div>

	<?php	
}



// Sanitize and validate input. Accepts an array, return a sanitized array.
function cdash_validate_options($input) {
	$input['bus_phone_type'] =  wp_filter_nohtml_kses($input['bus_phone_type']); 
	$input['bus_email_type'] =  wp_filter_nohtml_kses($input['bus_email_type']);
	// $input['txt_one'] =  wp_filter_nohtml_kses($input['txt_one']); // Sanitize textbox input (strip html tags, and escape characters)
	// $input['textarea_one'] =  wp_filter_nohtml_kses($input['textarea_one']); // Sanitize textarea input (strip html tags, and escape characters)
	return $input;
}

// Display a Settings link on the main Plugins page
function cdash_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$cdash_links = '<a href="'.get_admin_url().'options-general.php?page=chamber-dashboard-business-directory/options.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $cdash_links );
	}

	return $links;
}

function cdash_export_form() { ?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php _e('Export', 'cdash'); ?></h2>
		<p><?php _e('Click the button below to download a CSV of all of your businesses.', 'cdash'); ?></p>
		<form action="<?php echo plugin_dir_url( __FILE__ ); ?>export.php">

		<input type="submit" value="Download CSV">
		</form>
	</div>
<?php }

function cdash_import_form() { ?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
			<h2><?php _e('Import', 'cdash'); ?></h2>
			<p><?php _e('You can import businesses from a CSV file.  First, you must format the CSV properly.  Your CSV must have the following columns in the following order, even if some of the columns are empty: <ul><li>Business Name</li><li>Description</li><li>Category (separate multiple with semicolons)</li><li>Membership Level (separate multiple with semicolons)</li><li>Location Name</li><li>Address</li><li>City</li><li>State</li><li>Zip</li><li>URL</li><li>Phone (separate multiple with semicolons)</li><li>Email (separate multiple with semicolons)</li></ul>', 'cdash'); ?></p>
			<p><a href="<?php echo plugin_dir_url( __FILE__ ); ?>cdash-import-sample.zip"><?php _e('Download a sample CSV to see how to format your file.', 'cdash'); ?></a></p>
			<?php wp_import_upload_form('admin.php?page=chamber-dashboard-import'); ?>
		</div> 

	<?php $file = wp_import_handle_upload();

	if(isset($file['file'])) {

		$row = 0;
		if (($handle = fopen($file['file'], "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		    	
		    	if($row == 0) {
		    		// Don't do anything with the header row
		    		$row++;
		    		continue;
		    	} else {
		    		$row++;
					// Get the post data
					$businessinfo = array (
						'post_type'     => 'business',
						'post_title'    => $data[0],
						'post_content' 	=> $data[1],
						'post_status'   => 'publish',
						);
					// Create a business
					$newbusiness = wp_insert_post($businessinfo, true);
					// Add business categories
					if(isset($data[2])) {
						$categories = explode(';', $data[2]);
						wp_set_object_terms( $newbusiness, $categories, 'business_category' );
					}
					// Add membership levels
					if(isset($data[3])) {
						$levels = explode(';', $data[3]);
						wp_set_object_terms( $newbusiness, $levels, 'membership_level' );
					}
					// add a serialised array for wpalchemy to work - see http://www.2scopedesign.co.uk/wpalchemy-and-front-end-posts/
					$fields = array('_cdash_location');
					$str = $fields;
					update_post_meta( $newbusiness, 'buscontact_meta_fields', $str );

					// Get all the phone numbers and put them in the array format wpalchemy expects
					$numbers = array();
					if(isset($data[10]) && !empty($data[10])) {
						$tempnums = explode(';', $data[10]);
						foreach ($tempnums as $number) {
							$numbers[]['phonenumber'] = $number;
						}
					} else {
						$numbers = '';
					}

					// Get all the email addresses and put them in the array format wpalchemy expects
					$emails = array();
					if(isset($data[11]) && !empty($data[11])) {
						$tempmails = explode(';', $data[11]);
						foreach ($tempmails as $email) {
							$emails[]['emailaddress'] = $email;
						}
					} else {
						$emails = '';
					}

					// Create the array of location information for wpalchemy
					$locationfields = array(
							array(
							'altname' 	=> $data[4],
							'address'	=> $data[5],
							'city'		=> $data[6],
							'state'		=> $data[7],
							'zip'		=> $data[8],
							'url'		=> $data[9],
							'phone'		=> $numbers,
							'email'		=> $emails,
							)
						);

					// Add all of the post meta data in one fell swoop
					add_post_meta( $newbusiness, '_cdash_location', $locationfields );
				}
		    }
		    $success = $row - 1;
		    echo "<p style='font-size:1.2em;'>" . $success . " businesses successfully imported!</p>";
		    fclose($handle);
		}
	}
	
}

?>
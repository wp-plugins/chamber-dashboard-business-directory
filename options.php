<?php

if ( ! defined( 'ABSPATH' ) ) exit;

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
						"tax_name"		 => "1",
						"tax_address"	 => "1",
						"tax_url" 		 => "1",
						"tax_logo"		 => "1",
						"sm_display"	 => "icons",
						"sm_icon_size"	 => "32px",
						"currency_position" => "before",
						"currency_symbol" => "$",
						"currency" => "USD",
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
	register_setting( 'cdash_plugin_version', 'cdash_directory_version', 'cdash_validate_options' );
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
		plugin_dir_url( __FILE__ ) . '/images/cdash-settings.png', 
		85 
	);
	add_submenu_page( '/chamber-dashboard-business-directory/options.php', 'Export', 'Export', 'manage_options', 'chamber-dashboard-export', 'cdash_export_form' );
	add_submenu_page( '/chamber-dashboard-business-directory/options.php', 'Import', 'Import', 'manage_options', 'chamber-dashboard-import', 'cdash_import_form' );
	add_submenu_page( '/chamber-dashboard-business-directory/options.php', 'Add-Ons', 'Add-Ons', 'manage_options', 'chamber-dashboard-addons', 'cdash_addons_page' );
	// this is a hidden submenu page for updating geolocation data
	add_submenu_page( NULL, 'Update Geolocation Data', 'Update Geolocation Data', 'manage_options', 'chamber-dashboard-update-geolocation', 'cdash_update_geolocation_data_page' );
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
		<h1><img src="<?php echo plugin_dir_url( __FILE__ ) . '/images/cdash-32.png'?>"><?php _e('Chamber Dashboard Business Directory Settings', 'cdash'); ?></h1>


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
							<label><input name="cdash_directory_options[sv_social]" type="checkbox" value="1" <?php if (isset($options['sv_social'])) { checked('1', $options['sv_social']); } ?> /><?php _e(' Social Media Links', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[sv_comments]" type="checkbox" value="1" <?php if (isset($options['sv_comments'])) { checked('1', $options['sv_comments']); } ?> /><?php _e(' Comments', 'cdash'); ?></label><br />
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
							<label><input name="cdash_directory_options[tax_social]" type="checkbox" value="1" <?php if (isset($options['tax_social'])) { checked('1', $options['tax_social']); } ?> /><?php _e(' Social Media Links', 'cdash'); ?></label><br />
							<label><input name="cdash_directory_options[tax_orderby_name]" type="checkbox" value="1"<?php if (isset($options['tax_orderby_name'])) { checked('1', $options['tax_orderby_name']); } ?> /><?php _e(' Order category pages by business name (default order is by publication date)', 'cdash'); ?></label><br />
						</td>
					</tr>				

					<!-- Social Media Options -->
					<tr valign="top">
					<th scope="row">Social Media Display</th>
						<td>
							<label><input name="cdash_directory_options[sm_display]" type="radio" value="text" <?php checked('text', $options['sm_display']); ?> /> <?php _e( 'Text links ', 'cdash' ); ?><span style="color:#666666;margin-left:32px;"><?php _e( 'Display social media as text links', 'cdash' ); ?></span></label><br />
							<label><input name="cdash_directory_options[sm_display]" type="radio" value="icons" <?php checked('icons', $options['sm_display']); ?> /> <?php _e( 'Icons ', 'cdash' ); ?><span style="color:#666666;margin-left:32px;"><?php _e( 'Display social media links as icons', 'cdash' ); ?></span></label><br />
							<label><?php _e('Icon Size: ', 'cdash'); ?></label>	
								<select name='cdash_directory_options[sm_icon_size]'>
								<option value='16px' <?php selected('16px', $options['sm_icon_size']); ?>>16px</option>
								<option value='32px' <?php selected('32px', $options['sm_icon_size']); ?>>32px</option>
								<option value='64px' <?php selected('64px', $options['sm_icon_size']); ?>>64px</option>
								<option value='128px' <?php selected('128px', $options['sm_icon_size']); ?>>128px</option>
							</select>
						</td>
					</tr>

					<!-- Currency -->
					<tr>
						<th scope="row"><?php _e('Currency', 'cdash'); ?></th>
						<td>
							<select name='cdash_directory_options[currency]'>
								<?php global $currencies;
								foreach($currencies['codes'] as $code => $currency)
								{
									echo '<option value="'.esc_attr($code).'"'.selected($options['currency'], $code, false).'>'.esc_html($currency).'</option>';
								} ?>
							</select>
							<span style="color:#666666;margin-left:2px;"><?php _e('Select the currency that will be used on invoices.', 'cdash'); ?></span>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php _e('Currency Symbol', 'cdash'); ?></th>
						<td>
							<input type="text" size="35" name="cdash_directory_options[currency_symbol]" value="<?php if(isset($options['currency_symbol'])) { echo $options['currency_symbol']; } ?>" />
							<br /><span style="color:#666666;margin-left:2px;"><?php _e('Enter the symbol that should appear next to all currency.', 'cdashmm'); ?></span>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Currency Position', 'cdash' ); ?></th>
						<td>
							<!-- First radio button -->
							<label><input name="cdash_directory_options[currency_position]" type="radio" value="before" <?php checked('before', $options['currency_position']); ?> /><?php _e( ' Before the price', 'cdash' ); ?>
							</label><br />

							<!-- Second radio button -->
							<label><input name="cdash_directory_options[currency_position]" type="radio" value="after" <?php checked('after', $options['currency_position']); ?> /><?php _e( ' After the price', 'cdash' ); ?>
							</label>
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
										<a href="#" class="delete-this"><?php _e('Delete This Custom Field', 'cdash'); ?></a>
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
									<a href="#" class="delete-this"><?php _e('Delete This Custom Field', 'cdash'); ?></a>
								</div>
							<?php } ?>
							<p><a href="#" class="repeat"><?php _e('Add Another Custom Field', 'cdash'); ?></a></p>
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

			jQuery('.delete-this').click(function(e){
				e.preventDefault(); 
			    jQuery(this).parent('div').remove();
			});

			</script>
		</div><!-- #main -->
		<?php include( plugin_dir_path( __FILE__ ) . '/includes/aside.php' ); ?>
	</div>

	<?php	
}



// Sanitize and validate input. Accepts an array, return a sanitized array.
function cdash_validate_options($input) {
	// delete the old custom fields
	delete_option('cdash_directory_options');
	$input['bus_phone_type'] =  wp_filter_nohtml_kses($input['bus_phone_type']); 
	$input['bus_email_type'] =  wp_filter_nohtml_kses($input['bus_email_type']);
	if( isset( $input['currency_symbol'] ) ) {
		$input['currency_symbol'] =  wp_filter_nohtml_kses($input['currency_symbol']); 
	}
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

function cdash_export_form() {

	$export_form = 
		'<p>' . __( 'Click the button below to download a CSV of all of your businesses.', 'cdash' ) . '</p>
		<form action="' . plugin_dir_url( __FILE__ ) . 'export.php">
		<input type="submit" class="button-primary" value="' . __( 'Download CSV', 'cdash' ) . '">
		</form>
		<p>' . __( 'This exporter can only export limited information about businesses.  If you want to export more information, or export people or businesses, try the <a href="https://chamberdashboard.com/downloads/chamber-dashboard-exporter/" target="_blank">Chamber Dashboard Exporter</a>.', 'cdash' );

	$export_form = apply_filters( 'cdash_export_form', $export_form );

	$export_page = 
		'<div class="wrap">
			<div class="icon32" id="icon-options-general"><br></div>
			<h1>' . __( 'Export', 'cdash' ) . '</h1>' .
			$export_form . 
		'</div>';

	echo $export_page;
}

function cdash_import_form() { ?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
			<h1><?php _e('Import', 'cdash'); ?></h1>
			<h3><?php _e( 'Import Businesses', 'cdash' ); ?></h3>
			<p><?php _e('You can import businesses from a CSV file.  First, you must format the CSV properly.  Your CSV must have the following columns in the following order, even if some of the columns are empty: <ul><li>Business Name</li><li>Description</li><li>Category (separate multiple with semicolons)</li><li>Membership Level (separate multiple with semicolons)</li><li>Location Name</li><li>Address</li><li>City</li><li>State</li><li>Zip</li><li>URL</li><li>Phone (separate multiple with semicolons)</li><li>Email (separate multiple with semicolons)</li></ul>', 'cdash'); ?></p>
			<p><?php _e( 'Some programs format CSV files differently.  You might need to use either Google Drive or Open Office to save your CSV file so that it will upload correctly.', 'cdash' ); ?></p>
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

					// Get the geolocation data
					if( isset( $data[5] ) ) {
						// ask Google for the latitude and longitude
						$rawaddress = $data[5];
						if( isset( $data[6] ) ) {
							$rawaddress .= ' ' . $data[6];
						}
						if( isset( $data[7] ) ) {
							$rawaddress .= ' ' . $data[7];
						}
						if( isset( $data[8] ) ) {
							$rawaddress .= ' ' . $data[8];
						}
						$address = urlencode( $rawaddress );
						$json = wp_remote_get( "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyBq9JVPgmORIVfuzmgpzrzRTVyttSNyJ3A&address=" . $address . "&sensor=true" );
						$json = json_decode($json['body'], true);
						if( is_array( $json ) && $json['status'] == 'OK') {
							$latitude = $json['results'][0]['geometry']['location']['lat'];
							$longitude = $json['results'][0]['geometry']['location']['lng']; 
						}
					}

					// Create the array of location information for wpalchemy
					$locationfields = array(
							array(
							'altname' 	=> $data[4],
							'address'	=> $data[5],
							'city'		=> $data[6],
							'state'		=> $data[7],
							'zip'		=> $data[8],
							'latitude'	=> $latitude,
							'longitude'	=> $longitude,
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
	
	do_action( 'cdash_importer' );
}

function cdash_addons_page() { ?>
	<div class="wrap">
		<h1><?php _e( 'Add-Ons', 'cdash' ); ?></h1>
		<?php _e( 'You can extend the functionality of Chamber Dashboard even more with these add-ons!', 'cdash' ); ?>
		<div id="add-ons-container">
			<div class="add-on recurring">
				<h3><?php _e( 'Recurring Payments', 'cdash' ); ?></h3>
				<?php $recurring_payments_content = '
				<p>' . __( 'Make the membership manager even more powerful by adding automatic recurring payments!', 'cdash' ) . '</p>
				<p>' . __( 'With the Recurring Payments add-on, you will never have to create membership invoices again - the plugin will create and send annual membership invoices to your customers, and give them the option to sign up for automatic recurring payments through PayPal.', 'cdash' ) . '</p>
				<p class="center"><a href="https://chamberdashboard.com/downloads/recurring-payments/?utm_source=plugin&utm_medium=addons_page&utm_campaign=business-directory" class="button button-primary">' . __( 'Learn More', 'cdash' ) . '</a></p>';
				echo apply_filters( 'cdash_addons_recurring', $recurring_payments_content ); ?>
			</div>

			<div class="add-on export">
				<h3><?php _e( 'Exporter', 'cdash' ); ?></h3>
				<?php 
		    	$exporter_content = '
				<p>' . __( 'Advanced export functionality makes it easy to export businesses, invoices, or people based on criteria you select.  You can select what information to export.', 'cdash' ) . '</p>
				<p class="center"><a href="https://chamberdashboard.com/downloads/chamber-dashboard-exporter/?utm_source=plugin&utm_medium=addons_page&utm_campaign=business-directory" class="button button-primary">' . __( 'Learn More', 'cdash' ) . '</a></p>';
				echo apply_filters( 'cdash_addons_exporter_content', $exporter_content );
				do_action( 'cdash_addons_exporter' );
			    ?>
			</div>
			
			<div class="add-on tickets coming">
				<h3><?php _e( 'Ticket Sales', 'cdash' ); ?></h3>
				<?php $ticket_content = '
				<p>' . __( 'Integrate with the Chamber Dashboard Events Calendar to sell tickets to your events, email ticketholders, and track attendance.', 'cdash' ) . '</p>
				<p>' . __( 'Coming Soon!  Sign up for our newsletter to find out when Ticket Sales is released!', 'cdash' ) . '</p>
				<iframe width="100%" scrolling="no" frameborder="0" src="https://chamberdashboard.com/?wysija-page=1&controller=subscribers&action=wysija_outter&wysija_form=3&external_site=1&wysijap=subscriptions" class="iframe-wysija" vspace="0" tabindex="0" style="position: static; top: 0pt; margin: 0px; border-style: none; height: 125px; left: 0pt; visibility: visible;" marginwidth="0" marginheight="0" hspace="0" allowtransparency="true" title="Subscription MailPoet"></iframe>';
				echo apply_filters( 'cdash_addons_ticket', $ticket_content ); ?>
			</div>

			<div class="add-on import coming">
				<h3><?php _e( 'Importer', 'cdash' ); ?></h3>
				<?php $importer_content = '
				<p>' . __( 'Extend Chamber Dashboard\'s import function to import businesses and people, along with custom fields and other data.', 'cdash' ) . '</p>
				<p>' . __( 'Coming Soon!  Sign up for our newsletter to find out when the Importer is released!', 'cdash' ) . '</p>
				<iframe width="100%" scrolling="no" frameborder="0" src="https://chamberdashboard.com/?wysija-page=1&controller=subscribers&action=wysija_outter&wysija_form=3&external_site=1&wysijap=subscriptions" class="iframe-wysija" vspace="0" tabindex="0" style="position: static; top: 0pt; margin: 0px; border-style: none; height: 125px; left: 0pt; visibility: visible;" marginwidth="0" marginheight="0" hspace="0" allowtransparency="true" title="Subscription MailPoet"></iframe>';
				echo apply_filters( 'cdash_addons_importer', $importer_content ); ?>
			</div>
		</div>
	</div>
	<?php 
}

?>
<?php
/* Options Page */

// --------------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_uninstall_hook(__FILE__, 'cdash_delete_plugin_options')
// --------------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE USER DEACTIVATES AND DELETES THE PLUGIN. IT SIMPLY DELETES
// THE PLUGIN OPTIONS DB ENTRY (WHICH IS AN ARRAY STORING ALL THE PLUGIN OPTIONS).
// --------------------------------------------------------------------------------------

// Delete options table entries ONLY when plugin deactivated AND deleted
function cdash_delete_plugin_options() {
	delete_option('cdash_directory_options');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'cdash_add_defaults')
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE PLUGIN IS ACTIVATED. IF THERE ARE NO THEME OPTIONS
// CURRENTLY SET, OR THE USER HAS SELECTED THE CHECKBOX TO RESET OPTIONS TO THEIR
// DEFAULTS THEN THE OPTIONS ARE SET/RESET.
//
// OTHERWISE, THE PLUGIN OPTIONS REMAIN UNCHANGED.
// ------------------------------------------------------------------------------

// Define default option settings
function cdash_add_defaults() {
	$tmp = get_option('cdash_directory_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('cdash_directory_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(	"bus_phone_type" => "Main, Office, Cell",
						"bus_email_type" => "Main, Sales, Accounting, HR",
						"sv_description" => "1",
						"sv_name"		 => "1",
						"sv_address"	 => "1",
						"sv_url"		 => "1",
						"sv_logo"		 => "1",
						"sv_category"	 => "1",



						// "chk_button1" => "1",
						// "chk_button3" => "1",
						// "textarea_one" => "This type of control allows a large amount of information to be entered all at once. Set the 'rows' and 'cols' attributes to set the width and height.",
						// "textarea_two" => "This text area control uses the TinyMCE editor to make it super easy to add formatted content.",
						// "textarea_three" => "Another TinyMCE editor! It is really easy now in WordPress 3.3 to add one or more instances of the built-in WP editor.",
						// "txt_one" => "Enter whatever you like here..",
						// "drp_select_box" => "four",
						// "chk_default_options_db" => "",
						// "rdo_group_one" => "one",
						// "rdo_group_two" => "two"
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
// THIS FUNCTION RUNS WHEN THE 'admin_menu' HOOK FIRES, AND ADDS A NEW OPTIONS
// PAGE FOR YOUR PLUGIN TO THE SETTINGS MENU.
// ------------------------------------------------------------------------------

// Add menu page
function cdash_add_options_page() {
	add_menu_page( 
		'Chamber Dashboard', 
		'Chamber Dashboard', 
		'manage_options', 
		'/cdash-business-directory/options.php', 
		'cdash_render_form', 
		'dashicons-admin-generic', 
		85 
	);
	//add_options_page('Chamber Dashboard Settings', 'Chamber Dashboard', 'manage_options', __FILE__, 'cdash_render_form');
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
		<h2><?php _e('Chamber Dashboard Settings', 'cdash'); ?></h2>

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

				<!-- TODO - make field for default city -->

				<!-- Single View -->
				<tr valign="top">
					<th scope="row">Enable Single Business View</th>
					<td>
						<span style="color:#666666;margin-left:2px;">Do you want each business to have it's own individual page on your site?<br /></span>
						<label><input name="cdash_directory_options[enable_single]" type="checkbox" value="1" <?php if (isset($options['enable_single'])) { checked('1', $options['enable_single']); } ?> /> Enable Single Business View</label><br />
					</td>
				</tr>

				<!-- Single View Options -->
				<tr valign="top">
					<th scope="row">Single Business View Options</th>
					<td>
						<span style="color:#666666;margin-left:2px;">What information would you like to display on the single business view?</span><br />
						<label><input name="cdash_directory_options[sv_description]" type="checkbox" value="1" <?php if (isset($options['sv_description'])) { checked('1', $options['sv_description']); } ?> /> Description</label><br />
						<label><input name="cdash_directory_options[sv_name]" type="checkbox" value="1" <?php if (isset($options['sv_name'])) { checked('1', $options['sv_name']); } ?> /> Location Name <em>Note: you can hide individual locations in the "edit business" view</em></label><br />
						<label><input name="cdash_directory_options[sv_address]" type="checkbox" value="1" <?php if (isset($options['sv_address'])) { checked('1', $options['sv_address']); } ?> /> Location Address</label><br />
						<label><input name="cdash_directory_options[sv_url]" type="checkbox" value="1" <?php if (isset($options['sv_url'])) { checked('1', $options['sv_url']); } ?> /> Location Web Address</label><br />
						<label><input name="cdash_directory_options[sv_phone]" type="checkbox" value="1" <?php if (isset($options['sv_phone'])) { checked('1', $options['sv_phone']); } ?> /> Phone Number(s)</label><br />
						<label><input name="cdash_directory_options[sv_email]" type="checkbox" value="1" <?php if (isset($options['sv_email'])) { checked('1', $options['sv_email']); } ?> /> Email Address(es)</label><br />
						<label><input name="cdash_directory_options[sv_logo]" type="checkbox" value="1" <?php if (isset($options['sv_logo'])) { checked('1', $options['sv_logo']); } ?> /> Logo</label><br />
						<label><input name="cdash_directory_options[sv_thumb]" type="checkbox" value="1" <?php if (isset($options['sv_thumb'])) { checked('1', $options['sv_thumb']); } ?> /> Featured Image <em>Your theme might already display the featured image.  If it does not, you can check this box to display the featured image</em></label><br />
						<label><input name="cdash_directory_options[sv_memberlevel]" type="checkbox" value="1" <?php if (isset($options['sv_memberlevel'])) { checked('1', $options['sv_memberlevel']); } ?> /> Membership Level</label><br />
						<label><input name="cdash_directory_options[sv_category]" type="checkbox" value="1" <?php if (isset($options['sv_category'])) { checked('1', $options['sv_category']); } ?> /> Business Categories</label><br />
					</td>
				</tr>



				<!-- Text Area Control 
				<tr>
					<th scope="row">Sample Text Area</th>
					<td>
						<textarea name="cdash_directory_options[textarea_one]" rows="7" cols="50" type='textarea'><?php echo $options['textarea_one']; ?></textarea><br /><span style="color:#666666;margin-left:2px;">Add a comment here to give extra information to Plugin users</span>
					</td>
				</tr>

				Text Area Using the Built-in WP Editor 
				<tr>
					<th scope="row">Sample Text Area WP Editor 1</th>
					<td>
						<?php
							$args = array("textarea_name" => "cdash_directory_options[textarea_two]");
							wp_editor( $options['textarea_two'], "cdash_directory_options[textarea_two]", $args );
						?>
						<br /><span style="color:#666666;margin-left:2px;">Add a comment here to give extra information to Plugin users</span>
					</td>
				</tr>



				Textbox Control 
				<tr>
					<th scope="row">Enter Some Information</th>
					<td>
						<input type="text" size="57" name="cdash_directory_options[txt_one]" value="<?php echo $options['txt_one']; ?>" />
					</td>
				</tr>

				Radio Button Group 
				<tr valign="top">
					<th scope="row">Radio Button Group #1</th>
					<td>
						 First radio button
						<label><input name="cdash_directory_options[rdo_group_one]" type="radio" value="one" <?php checked('one', $options['rdo_group_one']); ?> /> Radio Button #1 <span style="color:#666666;margin-left:32px;">[option specific comment could go here]</span></label><br />

						Second radio button 
						<label><input name="cdash_directory_options[rdo_group_one]" type="radio" value="two" <?php checked('two', $options['rdo_group_one']); ?> /> Radio Button #2 <span style="color:#666666;margin-left:32px;">[option specific comment could go here]</span></label><br /><span style="color:#666666;">General comment to explain more about this Plugin option.</span>
					</td>
				</tr> 

				Checkbox Buttons 
				<tr valign="top">
					<th scope="row">Group of Checkboxes</th>
					<td>
						First checkbox button 
						<label><input name="cdash_directory_options[chk_button1]" type="checkbox" value="1" <?php if (isset($options['chk_button1'])) { checked('1', $options['chk_button1']); } ?> /> Checkbox #1</label><br />

						Second checkbox button 
						<label><input name="cdash_directory_options[chk_button2]" type="checkbox" value="1" <?php if (isset($options['chk_button2'])) { checked('1', $options['chk_button2']); } ?> /> Checkbox #2 <em>(useful extra information can be added here)</em></label><br />


					</td>
				</tr>

				Select Drop-Down Control
				<tr>
					<th scope="row">Sample Select Box</th>
					<td>
						<select name='cdash_directory_options[drp_select_box]'>
							<option value='one' <?php selected('one', $options['drp_select_box']); ?>>One</option>
							<option value='two' <?php selected('two', $options['drp_select_box']); ?>>Two</option>
							<option value='three' <?php selected('three', $options['drp_select_box']); ?>>Three</option>
							<option value='four' <?php selected('four', $options['drp_select_box']); ?>>Four</option>
							<option value='five' <?php selected('five', $options['drp_select_box']); ?>>Five</option>
							<option value='six' <?php selected('six', $options['drp_select_box']); ?>>Six</option>
							<option value='seven' <?php selected('seven', $options['drp_select_box']); ?>>Seven</option>
							<option value='eight' <?php selected('eight', $options['drp_select_box']); ?>>Eight</option>
						</select>
						<span style="color:#666666;margin-left:2px;">Add a comment here to explain more about how to use the option above</span>
					</td>
				</tr>

				<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options</th>
					<td>
						<label><input name="cdash_directory_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> Restore defaults upon plugin deactivation/reactivation</label>
						<br /><span style="color:#666666;margin-left:2px;">Only check this if you want to reset plugin settings upon Plugin reactivation</span>
					</td>
				</tr> -->
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cdash') ?>" />
			</p> 
		</form>


	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function cdash_validate_options($input) {
	 // strip html from textboxes
	$input['textarea_one'] =  wp_filter_nohtml_kses($input['textarea_one']); // Sanitize textarea input (strip html tags, and escape characters)
	$input['bus_phone_type'] =  wp_filter_nohtml_kses($input['bus_phone_type']); 
	$input['txt_one'] =  wp_filter_nohtml_kses($input['txt_one']); // Sanitize textbox input (strip html tags, and escape characters)
	return $input;
}

// Display a Settings link on the main Plugins page
function cdash_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$cdash_links = '<a href="'.get_admin_url().'options-general.php?page=plugin-options-starter-kit/plugin-options-starter-kit.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $cdash_links );
	}

	return $links;
}
 ?>
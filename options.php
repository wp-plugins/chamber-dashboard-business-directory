<?php
/* Options Page */

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
		'/cdash-business-directory/options.php', 
		'cdash_render_form', 
		'dashicons-admin-generic', 
		85 
	);
	// add_submenu_page( '/cdash-business-directory/options.php', 'Export', 'Export', 'manage_options', 'chamber-dashboard-export', 'cdash_export_form' );
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
						<span style="color:#666666;margin-left:2px;"><?php _e('If you need to store additional information about businesses, you can create custom fields here.', 'cdash'); ?></span><br />
						<?php if(!empty($options['bus_custom'])) {
							$customfields = $options['bus_custom'];
							$i = 1;
							foreach($customfields as $field) { ?>
								<div class="repeating" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
									<p><strong><?php _e('Custom Field Name', 'cdash'); ?></strong></p>
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
		$cdash_links = '<a href="'.get_admin_url().'options-general.php?page=cdash-business-directory/options.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $cdash_links );
	}

	return $links;
}

function cdash_export_form() { ?>
		<div class="wrap">
			<div class="icon32" id="icon-options-general"><br></div>
			<h2><?php _e('Export', 'cdash'); ?></h2>
			<form action="<?php echo plugin_dir_url( __FILE__ ); ?>export.php">

			<input type="submit" value="Download CSV">
			</form>
		</div>
			
			
			
		

<?php }
 ?>

<div class="my_meta_control">

<?php $options = get_option('cdash_directory_options');
$customfields = $options['bus_custom'];

foreach($customfields as $field) {
	if($field['type'] == "text") {
		$mb->the_field($field['name']); ?>
		<label><?php echo $field['name']; ?></label>
		<p><input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/></p>

	<?php } elseif ($field['type'] == "textarea") { ?>
		<label><?php echo $field['name']; ?></label>
		<p>
			<?php $metabox->the_field($field['name']); ?>
			<textarea name="<?php $metabox->the_name(); ?>" rows="5"><?php $metabox->the_value(); ?></textarea>
		</p>

	<?php } else {
		_e('<p>The field ' . $field['name'] . ' does not have an assigned field type.  Please go to the <a href="' . home_url() . '/wp-admin/admin.php?page=cdash-business-directory/options.php">Chamber Dashboard settings page</a> and give this field a type so you can use it.</p>', 'cdash');
	}

} ?>

</div>
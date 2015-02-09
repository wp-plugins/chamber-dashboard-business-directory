<div class="my_meta_control clearfix">

	<label><?php _e('Billing Address', 'cdash'); ?></label>
	<p>
		<?php $metabox->the_field('billing_address'); ?>
		<textarea name="<?php $metabox->the_name(); ?>" rows="3"><?php $metabox->the_value(); ?></textarea>
	</p>

	<div class="third">
		<?php $mb->the_field('billing_city'); ?>
		<label><?php _e('City', 'cdash'); ?></label>
		<p><input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/></p> 
	</div>

	<div class="third">
		<?php $mb->the_field('billing_state'); ?>
		<label><?php _e('State', 'cdash'); ?></label>
		<p><input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/></p>
	</div>

	<div class="third">
		<?php $mb->the_field('billing_zip'); ?>
		<label><?php _e('Zip', 'cdash'); ?></label>
		<p><input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/></p>
	</div>

	<div class="half">
		<?php $mb->the_field('billing_email'); ?>
		<label><?php _e('Billing Email', 'cdash'); ?></label>
		<p><input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/></p>
	</div>

	<div class="half">
		<?php $mb->the_field('billing_phone'); ?>
		<label><?php _e('Billing Phone', 'cdash'); ?></label>
		<p><input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/></p>
	</div>

</div>
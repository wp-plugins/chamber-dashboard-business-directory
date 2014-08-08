<div class="my_meta_control">

	<p class="explain"><?php _e('These notes are for your internal use, and will never be displayed on the website.', 'cdash'); ?></p>

	<a href="#" class="dodelete-note button"><?php _e('Remove All Notes', 'cdash'); ?></a>
 
	<?php while($mb->have_fields_and_multi('note')): ?>
	<?php $mb->the_group_open(); ?>
		<div class="note">

			<?php $mb->the_field('date'); ?>
			<label><?php _e('Date', 'cdash'); ?></label>
			<p><input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/></p>

			<label><?php _e('Note', 'cdash'); ?></label>
			<p>
				<?php $metabox->the_field('notetext'); ?>
				<textarea name="<?php $metabox->the_name(); ?>" rows="5"><?php $metabox->the_value(); ?></textarea>
			</p>
 		</div>

	<?php $mb->the_group_close(); ?>
	<?php endwhile; ?>
	<p style="margin-bottom:15px; padding-top:5px;"><a href="#" class="docopy-note button"><?php _e('Add Another Note', 'cdash'); ?></a></p>

</div>
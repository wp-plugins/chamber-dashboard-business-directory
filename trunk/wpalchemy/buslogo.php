<div class="my_meta_control">
	<?php global $wpalchemy_media_access; ?>
		<?php $mb->the_field('buslogo'); ?>
	    <?php $wpalchemy_media_access->setGroupName('nn')->setInsertButtonLabel('Insert'); ?>
	 	<div class="preview">
			<?php $img = $mb->get_the_value(); ?>
			<?php echo wp_get_attachment_image( $img, 'thumbnail' ); ?>
		</div>
	    <p>
	        <?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
	        <?php echo $wpalchemy_media_access->getButton(array('label' => 'Select Image')); ?>
	    </p>

</div>
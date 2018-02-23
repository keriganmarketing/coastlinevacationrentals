<div class="wrap" id="translations">
	<h2><?php _e( 'Manage translations' ); ?></h2>
	<div style="margin:15px 10px;padding:7px;background-color:white">
		Notes:
		<ul>
			<li>To overwritte the default translation, simply right the translation you want in the column "Your translation" and save.</li>
			<li>To stop overwritting the default translation, simply empty the corresponding field and save.</li>
			<li>To load default translations <a href="#" class="kigo_update_translation_files">click here</a>.</li>
		</ul>
	</div>
	<?php $my_table->display(); ?>
</div>

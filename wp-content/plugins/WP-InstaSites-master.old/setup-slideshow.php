<?php	
	// handle if this is a post
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		update_option('bapi_slideshow_image1', $_POST['bapi_slideshow_image1']);
		update_option('bapi_slideshow_image2', $_POST['bapi_slideshow_image2']);
		update_option('bapi_slideshow_image3', $_POST['bapi_slideshow_image3']);
		update_option('bapi_slideshow_image4', $_POST['bapi_slideshow_image4']);
		update_option('bapi_slideshow_image5', $_POST['bapi_slideshow_image5']);
		update_option('bapi_slideshow_image6', $_POST['bapi_slideshow_image6']);
		
		update_option('bapi_slideshow_caption1', $_POST['bapi_slideshow_caption1']);
		update_option('bapi_slideshow_caption2', $_POST['bapi_slideshow_caption2']);
		update_option('bapi_slideshow_caption3', $_POST['bapi_slideshow_caption3']);
		update_option('bapi_slideshow_caption4', $_POST['bapi_slideshow_caption4']);
		update_option('bapi_slideshow_caption5', $_POST['bapi_slideshow_caption5']);
		update_option('bapi_slideshow_caption6', $_POST['bapi_slideshow_caption6']);
		
		echo '<div id="message" class="updated"><p><strong>Settings saved.</strong></p></div>';
	}
	define( 'WPIS_URL', get_relative( plugins_url( '', __FILE__ ) ) );
?>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('input#bapi_slideshow_image1,input#image-pick1').click(function(){
			//alert('test');
			wp.media.editor.send.attachment = function(props, attachment){
				$('input#bapi_slideshow_image1').val(attachment.url);
			}
			wp.media.editor.open(this);
			return false;
		});
		$('input#bapi_slideshow_image2,input#image-pick2').click(function(){
			//alert('test');
			wp.media.editor.send.attachment = function(props, attachment){
				$('input#bapi_slideshow_image2').val(attachment.url);
			}
			wp.media.editor.open(this);
			return false;
		});
		$('input#bapi_slideshow_image3,input#image-pick3').click(function(){
			//alert('test');
			wp.media.editor.send.attachment = function(props, attachment){
				$('input#bapi_slideshow_image3').val(attachment.url);
			}
			wp.media.editor.open(this);
			return false;
		});
		$('input#bapi_slideshow_image4,input#image-pick4').click(function(){
			//alert('test');
			wp.media.editor.send.attachment = function(props, attachment){
				$('input#bapi_slideshow_image4').val(attachment.url);
			}
			wp.media.editor.open(this);
			return false;
		});
		$('input#bapi_slideshow_image5,input#image-pick5').click(function(){
			//alert('test');
			wp.media.editor.send.attachment = function(props, attachment){
				$('input#bapi_slideshow_image5').val(attachment.url);
			}
			wp.media.editor.open(this);
			return false;
		});
		$('input#bapi_slideshow_image6,input#image-pick6').click(function(){
			//alert('test');
			wp.media.editor.send.attachment = function(props, attachment){
				$('input#bapi_slideshow_image6').val(attachment.url);
			}
			wp.media.editor.open(this);
			return false;
		});
		/* Clear the Favicon input field */
		$('.clear-btn').click(function() {
				$('#'+$(this).attr('data-for')).val('');
			return false;
		});
		//sort
		$('.up-btn').click(function() {
			var thisVal = $('#bapi_slideshow_image'+$(this).attr('data-for')).val();
			var aboveFieldNumber = parseInt($(this).attr('data-for'))-1;
			var aboveField = '#bapi_slideshow_image'+aboveFieldNumber;
			var aboveFieldVal = $(aboveField).val();
			if(thisVal !== ''){
				$(aboveField).val(thisVal);
				$('#bapi_slideshow_image'+$(this).attr('data-for')).val(aboveFieldVal);
			}
			return false;
		});
		$('.down-btn').click(function() {
			var thisVal = $('#bapi_slideshow_image'+$(this).attr('data-for')).val();
			var belowFieldNumber = parseInt($(this).attr('data-for'))+1;
			var belowField = '#bapi_slideshow_image'+belowFieldNumber;
			var belowFieldVal = $(belowField).val();
			if(thisVal !== ''){
				$(belowField).val(thisVal);
				$('#bapi_slideshow_image'+$(this).attr('data-for')).val(belowFieldVal);
			}
			return false;
		});
	});	
</script>
<style>.button.clear-btn,.button.down-btn,.button.up-btn{padding-top:4px;}</style>
<div class="wrap">
<?php
    echo '<h1><img src="' . plugins_url('/img/logo_kigo.png', __FILE__) . '"/></h1>';
?>
<h2><?php echo 'Slideshow Setup'; ?></h2>
<form method="post">
<table class="form-table">
<tr>
	<th scope="row">Slide 1</th>
	<td>
		Image: <input type="text" id='bapi_slideshow_image1' name="bapi_slideshow_image1" size="60" value="<?php echo get_option('bapi_slideshow_image1'); ?>" /><input type="button" id="image-pick1" name="image-pick1" value="Select Image" class="button" /><a href="#" class="button clear-btn" data-for="bapi_slideshow_image1"><img alt="Clear" title="Clear Field" src="<?php echo WPIS_URL; ?>/img/remove_2.png" /></a><a href="#" class="button down-btn" data-for="1"><img alt="Down" src="<?php echo WPIS_URL; ?>/img/down_arrow.png" /></a>
		<br/>
		Caption: <input type="text" id='bapi_slideshow_caption1' name="bapi_slideshow_caption1" size="58" value="<?php echo get_option('bapi_slideshow_caption1'); ?>" />
	</td>
	</tr>
	<tr>
	<th scope="row">Slide 2</th>
	<td>
		Image: <input type="text" id='bapi_slideshow_image2' name="bapi_slideshow_image2" size="60" value="<?php echo get_option('bapi_slideshow_image2'); ?>" /><input type="button" id="image-pick2" name="image-pick2" value="Select Image" class="button" /><a href="#" class="button clear-btn" data-for="bapi_slideshow_image2"><img alt="Clear" title="Clear Field" src="<?php echo WPIS_URL; ?>/img/remove_2.png" /></a><a href="#" class="button down-btn" data-for="2"><img alt="Down" src="<?php echo WPIS_URL; ?>/img/down_arrow.png" /></a><a href="#" class="button up-btn" data-for="2"><img alt="Up" src="<?php echo WPIS_URL; ?>/img/up_arrow.png" /></a><br/>
		Caption: <input type="text" id='bapi_slideshow_caption2' name="bapi_slideshow_caption2" size="58" value="<?php echo get_option('bapi_slideshow_caption2'); ?>" />
	</td>
	</tr>
	<tr>
	<th scope="row">Slide 3</th>
	<td>
		Image: <input type="text" id='bapi_slideshow_image3' name="bapi_slideshow_image3" size="60" value="<?php echo get_option('bapi_slideshow_image3'); ?>" /><input type="button" id="image-pick3" name="image-pick3" value="Select Image" class="button" /><a href="#" class="button clear-btn" data-for="bapi_slideshow_image3"><img alt="Clear" title="Clear Field" src="<?php echo WPIS_URL; ?>/img/remove_2.png" /></a><a href="#" class="button down-btn" data-for="3"><img alt="Down" src="<?php echo WPIS_URL; ?>/img/down_arrow.png" /></a><a href="#" class="button up-btn" data-for="3"><img alt="Up" src="<?php echo WPIS_URL; ?>/img/up_arrow.png" /></a><br/>
		Caption: <input type="text" id='bapi_slideshow_caption3' name="bapi_slideshow_caption3" size="58" value="<?php echo get_option('bapi_slideshow_caption3'); ?>" />
	</td>
	</tr>
	<tr>
	<th scope="row">Slide 4</th>
	<td>
		Image: <input type="text" id='bapi_slideshow_image4' name="bapi_slideshow_image4" size="60" value="<?php echo get_option('bapi_slideshow_image4'); ?>" /><input type="button" id="image-pick4" name="image-pick4" value="Select Image" class="button" /><a href="#" class="button clear-btn" data-for="bapi_slideshow_image4"><img alt="Clear" title="Clear Field" src="<?php echo WPIS_URL; ?>/img/remove_2.png" /></a><a href="#" class="button down-btn" data-for="4"><img alt="Down" src="<?php echo WPIS_URL; ?>/img/down_arrow.png" /></a><a href="#" class="button up-btn" data-for="4"><img alt="Up" src="<?php echo WPIS_URL; ?>/img/up_arrow.png" /></a><br/>
		Caption: <input type="text" id='bapi_slideshow_caption4' name="bapi_slideshow_caption4" size="58" value="<?php echo get_option('bapi_slideshow_caption4'); ?>" />
	</td>
	</tr>
	<tr>
	<th scope="row">Slide 5</th>
	<td>
		Image: <input type="text" id='bapi_slideshow_image5' name="bapi_slideshow_image5" size="60" value="<?php echo get_option('bapi_slideshow_image5'); ?>" /><input type="button" id="image-pick5" name="image-pick5" value="Select Image" class="button" /><a href="#" class="button clear-btn" data-for="bapi_slideshow_image5"><img alt="Clear" title="Clear Field" src="<?php echo WPIS_URL; ?>/img/remove_2.png" /></a><a href="#" class="button down-btn" data-for="5"><img alt="Down" src="<?php echo WPIS_URL; ?>/img/down_arrow.png" /></a><a href="#" class="button up-btn" data-for="5"><img alt="Up" src="<?php echo WPIS_URL; ?>/img/up_arrow.png" /></a><br/>
		Caption: <input type="text" id='bapi_slideshow_caption5' name="bapi_slideshow_caption5" size="58" value="<?php echo get_option('bapi_slideshow_caption5'); ?>" />
	</td>
	</tr>
	<tr>
	<th scope="row">Slide 6</th>
	<td>
		Image: <input type="text" id='bapi_slideshow_image6' name="bapi_slideshow_image6" size="60" value="<?php echo get_option('bapi_slideshow_image6'); ?>" /><input type="button" id="image-pick6" name="image-pick6" value="Select Image" class="button" /><a href="#" class="button clear-btn" data-for="bapi_slideshow_image6"><img alt="Clear" title="Clear Field" src="<?php echo WPIS_URL; ?>/img/remove_2.png" /></a><a href="#" class="button up-btn" data-for="6"><img alt="Up" src="<?php echo WPIS_URL; ?>/img/up_arrow.png" /></a>
		<br/>
		Caption: <input type="text" id='bapi_slideshow_caption6' name="bapi_slideshow_caption6" size="58" value="<?php echo get_option('bapi_slideshow_caption6'); ?>" />
	</td>
	</tr>
</table>
<div class="clear"></div>
<?php submit_button(); ?>
</form>
</div>

jQuery(document).ready(function() {
 
	jQuery('#upload_logo_button').click(function() {
	//    wp.media.editor.send.attachment = function(props, attachment){
	//        $('input#bapi_slideshow_image1').val(attachment.url);
	//    }
	//    wp.media.editor.open(this);
	//    return false;
	//    
	 formfield = jQuery('#upload_logo').attr('name');
	 tb_show('Site Logo', 'media-upload.php?type=image&amp;TB_iframe=true');
	 return false;
	});

	 
	window.send_to_editor = function(html) {
	 var imgObj = jQuery('<div>'+html+'</div>'); 
	 var imgurl = imgObj.find('img').attr('src'); 
	 jQuery('#upload_logo').val(imgurl);
	 jQuery('#bapi-logo').show();
	 jQuery('#LogoPreview').attr('src',imgurl);
	 tb_remove();
	}
 
});
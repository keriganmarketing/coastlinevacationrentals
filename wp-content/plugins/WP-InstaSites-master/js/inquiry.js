
function BookingHelper_ValidateForm(reqfields) {
	for (i=0; i < reqfields.length; ++i) {
		var rf = $(reqfields[i]);
		$.validity.clear();
		$.validity.start();	
		var match = rf.attr('data-validity');				
		if (typeof(match)==="undefined" || match===null) {
			rf.require();
		} else {
			rf.require().match(match);
		}								
		var result = $.validity.end();
		if (!result.valid) {
			if(rf.attr('data-validity') == 'email'){
				/* message for email type field */
				alert('Invalid Email Address'); rf.focus(); return false;
			}
			alert(BAPI.textdata['Please fill out all required fields']); rf.focus(); return false;
		}

		// special case for credit card field
		if( rf.hasClass('ccverify') ) {
			//The credit card field should only contain digits (no space or - etc.. ) It could also be auto-cleaned up by rf.val().replace(/[^\d]/g, '');
			if( !$.isArray( rf.val().match( /^\d+$/ ) ) ) {
				alert(BAPI.textdata['The credit card number should contain only digits']); rf.focus(); return false;
			}
			else if (rf.attr('data-isvalid')!='1') {
				alert(BAPI.textdata['The entered credit card is invalid']); rf.focus(); return false;
			}
		}

		if (rf.hasClass('checkbox')) {
			BAPI.log(rf.attr('checked'));
			if (!rf.attr('checked')) {
				alert(BAPI.textdata['Please accept the terms and conditions']); rf.focus(); return false;
			}
		}
	}
	return true;
}


//Date picker setup in scripts.js



var processing = false;	
var loadingImgUrl = '//booktplatform.s3.amazonaws.com/App_SharedStyles/CCImages/loading.gif';
$(".doleadrequest-new").on("click", function () {
	$("#event-success, #event-error").remove();
	var that = $(this);

	$('input[name="tzname"]').val( BAPI.defaultOptions.tzname );

	var targetid = $(this).parents('.widget');
	console.log("Processing lead request");
	if (processing) { return; } // already in here
	/* block the Inquiry form */
	$(targetid).block({ message: "<img src='" + loadingImgUrl + "' />" });
	/* get all the required fields */
	var reqfields = $.extend([],$('.widget_bapi_inquiry_form .required'));

	/* validate the required fields */
	var validData = BookingHelper_ValidateForm(reqfields);
	/* if its not valid data unblock and do nothing */
	if(!validData){
		/* unblock the inquiry form so the user can enter valid data*/
		$(targetid).unblock();
		return;
	}
	
	/* data is valid we are processing now */
	processing = true; // make sure we do not reenter
	var reqdata = {};

	var form = $(this).parents('form');

	/* we get the date formats */
	var dfparse = BAPI.defaultOptions.dateFormat.toUpperCase();
	var df = BAPI.defaultOptions.dateFormatBAPI.toUpperCase();

	var data = {};
	$.each(form.serializeArray(), function(i,v) {
		data[v.name] = v.value;
	});

	console.log(data);

	if (data.special.length > 0) { 
		validData = false;
		//window.location.href = '?special=1';
	}

	if(data.los) {
		data.los = parseInt(data.los);
		data.checkout = moment(data.checkin, df).add(data.los, 'days').format(df);
	}

	reqdata = data;  
	console.log('reqdata:');
	console.log(reqdata);


	//Validate checkin is the future
	if(reqdata.checkin !== null && reqdata.checkin != '' && typeof(reqdata.checkin) !== 'undefined'){
		var today = moment();
		var selectedDate = moment(reqdata.checkin); 
		if(selectedDate.diff(today, 'days') < 0){
			alert("Invalid Date");
			$("#txtCheckIn").focus();
			$(targetid).unblock();
			processing = false;
			return;
		}
	}

	//Create the inquiry
	var success = true;
	if(validData) {
		var querystring = $.param(reqdata);
		var url = '//'+bapiurl+'/ws/?method=createevent&'+querystring;

		$.get(url, function() {

		}).success(function(res) {
			$(targetid).unblock();
			processing = false;

			if(res.error) { 
				success = false; 
			} else {
				/* Execute google adwords code if exists */
				if ( typeof googleConversionTrack == 'function' ) { googleConversionTrack(); }
				
				form[0].reset();

				that.before('<div id="event-success" class="alert alert-success">'+translations["Thank you, your request has been submitted."]+'</div>');
			}

		}).fail(function(res) {
			that.before('<div id="event-error" class="alert alert-error">'+translations["An unexpected error occurred"]+'</div>');
		});

	}

	$(targetid).unblock();
	processing = false;

	return false;  //Force exit here

});

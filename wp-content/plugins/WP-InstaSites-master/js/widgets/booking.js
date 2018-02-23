jQuery(function($) {
	function updateBookingForm() { 
		$('form[name="getQuote"]').addClass('loading').each(function() {
			var that = $(this);
			
			var cookies = {};
			var cookieData = Cookies.getJSON('searchdata');

			if(!cookieData) { that.removeClass('loading'); return; }

			
			$.each(cookieData, function(i,v) {
			
				var c = v.name.indexOf('['); 

				if (c == -1) {
					cookies[v.name] = v.value;
				} else {
					// special case when the data-attribute value has nested brackets (such as adults[min])
					var k1 = v.name.substring(0,c);
					var k2 = v.name.substring(c+1,v.name.length-1);
					
					cookies[k1] = {};
					cookies[k1][k2] = v.value;
				}
				cookies[v.name] = v.value;
			});

			var options = {
				'method': 'get',
				'apikey': apikey,
				'entity': 'property',
				'ids': $(this).data('propid'),
				'avail2': true,
				'rates': 1,
				'language': locale,
				'currency': BAPI.session.currency || currency,
			};

			if(cookies.checkin) {
				options.checkin = cookies.checkin,
				options.scheckin = cookies.checkin.split('-').join('/'); //%2F
			}

			if(cookies.checkout) { 
				options.checkout = cookies.checkout; 
				options.scheckout = cookies.checkout.split('-').join('/');
			}

			if(cookies.los) {
				options.los = parseInt(cookies.los); 
				if(cookies.checkin) { 
					options.checkout = moment(cookies.checkin, "MM-DD-YYYY").add(options.los, 'days').format('MM-DD-YYYY');
				}
			}
			
			if(!cookies.adults) { cookies.adults = {}; }
			if(cookies.adults.min) { 
				options.adults = cookies.adults.min;
			}

			if(!cookies.children) { cookies.children = {}; }
			if(cookies.children.min) {
				options.children = cookies.children.min;
			}


			var url = '//'+bapiurl+"/ws/?"+$.param(options);

			//Fetch
			$.get(url, function(Data) { 

				if(Data.error) { 
					console.log('BAPI error');
					return; 
				}


				data = Data.result[0];
				var context = data.ContextData,
					quote = data.ContextData.Quote;

				if(quote.ValidationMessage) {
					$("#book-block .alert-error").text(quote.ValidationMessage).removeClass('hide').show();

					return; //Don't update calendars.  The data is not real time, so it will update with stale data which we already have anyway.

					var availability = context.Availability;
					var availString = JSON.stringify(availability);
					var avails = "";

					//Use lodash here for unique function?
					
					$.each(availability, function(i,v){
						v = JSON.stringify(v); 
						if(-1 == avails.indexOf(v)) {
							avails = avails + v + ",";
						}
					});

					avails = avails.substring(0, avails.length - 1);
					avails = "[" + avails + "]";

					Data.result[0].ContextData.Availability = JSON.parse(avails);

					$(window).trigger('update-calendars', Data);
					$(window).trigger('update-pickers', ['disable', Data.result[0].ContextData.Availability]); //old: availability

				} else {
					$("#book-block .alert-error").text(quote.ValidationMessage).addClass('hide').hide();;
				}

				if(quote.QuoteDisplay.value) {
					that.find('.rate').text(quote.QuoteDisplay.value);
					that.find('.quote-prefix').text(quote.QuoteDisplay.prefix);
					that.find('.quote-suffix').text(quote.QuoteDisplay.suffix);
					that.find('.quote-display').removeClass('hide');
				}

				if(false === quote.IsValid || false === data.IsBookable) {
					$('.bapi-booknow').addClass('hide');
					$('.bapi-inquirenow').removeClass('hide');
				} else {
					$('.bapi-booknow').removeClass('hide');
					$('.bapi-inquirenow').addClass('hide');
				}


				//$.extend(BAPI.session.searchparams, options);

				//console.log(JSON.stringify(BAPI.session.searchparams));

				console.log('save session from quote fetch');
				BAPI.savesession();

			}).done(function() {
				that.removeClass('loading');
			});
		});
	}

	updateBookingForm();
	$(window).on('updateBookingForm', function() {
		updateBookingForm();
	});
});
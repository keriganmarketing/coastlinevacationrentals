jQuery(function($) {
	//Search multi select
	var orderCount = 0;
	$("select.checkboxes").each(function() {
		var that = $(this);
		var title = that.attr('title') || false;

		that.multiselect({
			header: false,
			buttonClass: 'btn btn-link btn-white align-left',
			buttonWidth: '100%',
			nonSelectedText: title,
		});
	});

	$("form[name=property-search], #book-block")
		.on("submit", function (e) { 
			e.preventDefault();

			$(this).addClass('loading');

			Cookies.set('searchdata', $(this).serializeArray() );

			BAPI.session.searchparams = {};

			$.each($(this).serializeArray(), function(i,v) {
				BAPI.session.searchparams[v.name] = v.value;
			});

			$(this).off('submit').submit();

			$(this).removeClass('loading');

			return false;
		})
		.addClass('loading')
		.each(function() {
			var searchdata = Cookies.getJSON('searchdata'),
				that = $(this);

			/**
			 * The Query String
			 */
			var QueryString = function () {
				// This function is anonymous, is executed immediately and
				// the return value is assigned to QueryString!
				var query_string = [];
				var query = window.location.search.substring(1);
				var vars = query.split("&");
				for (var i=0;i<vars.length;i++) {
					var pair = vars[i].split("=");

					// If the key is urlencoded:
					var param_key = decodeURIComponent(pair[0]);
					if (typeof query_string[i] === "undefined" && typeof pair[1] !== "undefined" ) {
						query_string[i] =
						{
							name: param_key,
							value: decodeURIComponent(pair[1])
						};

					}

				}
				return query_string;
			}();
			/**
			 * Try to get the info from the url.
			 */
			var formData = QueryString; // By default try to get the data from the querystring

			if(typeof formData === "undefined" || formData.length < 1)
			{
                formData = searchdata;
			}

			if(typeof formData !== "undefined")
            {
                $.each(formData, function(i,v) { 

                	if(typeof(v.value) == 'string' && v.value.indexOf('+') >= 0) {
	                	v.value = v.value.split('+').join(' '); //Needed because spaces are plus signs in the url query string
	                }

                    //Convert dates
                    if( ('checkout' == v.name || 'checkin' == v.name) && v.value !== "" ) {
                        v.value = moment(v.value, BAPI.defaultOptions.dateFormatBAPI).format(BAPI.defaultOptions.dateFormat.toUpperCase());
                    }

                    //Check amenities
                    if( 'amenities' == v.name && $('select[name="amenities"]').length > 0) {
                        $('select[name="amenities"]').multiselect('select', v.value);
                    }

                    if( 'adults' == v.name ) {
                        v.name = v.name+"[min]";
                        v.value = v.value.min || 2;
                    }

                    if( 'children' == v.name ) {
                        v.name = v.name+"[min]";
                        v.value = v.value.min || 0;
                    }
                    var selectedInput = that.find(':input:not(:hidden)[name="'+v.name+'"]');
                    //we need this for datepicker fields since the datepicker is messing the name value
                    if(selectedInput.length == 0 ){
                        if('checkin' == v.name ){
                            selectedInput = that.find('#searchcheckin');
                        }
                        if('checkout' == v.name ){
                            selectedInput = that.find('#searchcheckout');
                        }
                    }
                    selectedInput.val(v.value);
                });
            }




			that.removeClass('loading');
		});


	//Lazy load images
	$("img[data-src]").css({'opacity':0}).unveil(200, function() {
		$(this).load(function() {
			this.style.opacity = 1;
		})
	});

	$(".flexslider li").show();


	//Sliders
	function create_sliders() {
		$('.bapi-flexslider:not(.active)').css({'opacity':0, 'height':0}).each(function (i) {
			var that = $(this);

			/* check if the fullScreen Carousel is present so we attach the click event to the flexslider viewport */
			if($('#fullScreenCarousel').length > 0){
				//$("#fullScreenCarousel").carousel({"interval": 10800000});
				$("#fullScreenCarousel").carousel('next');
				
				$(window).scroll();
				$( "#slider .slides" ).on( "click", function() {
					if(typeof($("#slider").data('flexslider')) !== 'undefined'){
						$("#fullScreenCarousel").carousel($("#slider").data('flexslider').currentSlide);
					}
					/* lets not do this each time the image is clicked */
					if($('#fullScreenCarousel.alreadyOpened').length > 0){
						
						$('#fullScreenSlideshow').modal('show');
						
					}else{
					$('#fullScreenCarousel .carousel-inner .item').each(function( index ) {
						//if(index == 0){$(this).addClass('active');}
						var img = document.createElement('img');
						img.src = $(this).data("imgurl");
						img.alt = $(this).data("caption");
						$(this).append(img);
					});
						$('#fullScreenSlideshow').modal('show');
						$('#fullScreenCarousel').addClass('alreadyOpened');
					}
				});
			}
			
			var ctl = $(this);		

			var options = null;
			var defaults = {};
			try { options = $.parseJSON(ctl.attr('data-options')); } catch(err) {}
			defaults.start = function(slider) {
				//Positioning function?
				if(typeof positionQuickSearch != 'function') {
					$('.home-qsearch').addClass('qsFixed');
				}
				$(window).scroll().trigger('resize');
				that.addClass('active').css({'opacity':1, 'height':'auto'});
                                slider.removeClass('loadimg');
			};
			defaults.before = function(slider) {
				var slides     = slider.slides,
			          index      = slider.animatingTo,
			          $slide     = $(slides[index]),
			          $img       = $slide.find('img[data-src]'),
			          current    = index,
			          nxt_slide  = current + 1,
			          prev_slide = current - 1;

			      $slide
			        .parent()
			        .find('img:eq(' + current + '), img:eq(' + prev_slide + '), img:eq(' + nxt_slide + ')')
			        .each(function() {
			          var src = $(this).attr('data-src');
			          if(src) {
				          $(this).attr('src', src).removeAttr('data-src').css({'opacity':1});
				      }
			        });
			};
			defaults.smoothHeight = false;
			defaults.animationLoop = true;

			options = $.extend(defaults, options);

			var selector = '#' + ctl.attr('id');
			console.log( "Applying flexslider to " + $(this).index() );
			ctl.flexslider(options);
			$(window).scroll().trigger('resize');			
			$('#page').trigger('click');
		});	
	}
	create_sliders();

	$(window).on('applyflexsliders', function() {
		create_sliders();
	});



	//Maps
	function createMaps() {
		if( $('.bapi-map').length > 0 ) {
			if(
				0 === $('#google-map-script').length &&
				(
					!$.isPlainObject( window.google ) ||
					!$.isPlainObject( window.google.maps )
				)
			) {
				BAPI.log("loading google maps.");
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.id = "google-map-script";
				script.src = "//maps.google.com/maps/api/js?sensor=false&key=AIzaSyAY7wxlnkMG6czYy9K-wM4OWXs0YFpFzEE&callback=BAPI.UI.setupmapwidgetshelper";
				document.body.appendChild(script);
			}
			else {
				BAPI.log("google maps already loaded.");
				BAPI.UI.setupmapwidgetshelper();
			}
		}
	}


	function doSearchRender(targetid, ids, entity, options, data, alldata, callback) {
		// package up the data to bind to the mustache template
		data.result = applyMyList(alldata,entity);
		data.totalcount = ids.length;
		data.isfirstpage = (options.searchoptions.page == 1);
		data.islastpage = (options.searchoptions.page*options.searchoptions.pagesize) >= data.totalcount;
		data.curpage = options.searchoptions.page - 1;
		data.config = BAPI.config(); 						
		data.session = BAPI.session.searchparams;
		data.textdata = options.textdata;
		data.totalitemsleft = ids.length - data.result.length;
		
		//the top info part, what we are showing from a total
	        data.fromItem = (options.searchoptions.page - 1) * options.searchoptions.pagesize + 1;
	        if (data.isfirstpage) {data.fromItem= 1}; // *it happens only for the first run*
	        data.toItem = data.fromItem+options.searchoptions.pagesize-1;
	        if (data.toItem < options.searchoptions.pagesize){   // happens when records less than per page  
	            data.toItem = options.searchoptions.pagesize;  
	        }else if (data.toItem > data.totalcount){  // happens when result end is greater than total records  
	            data.toItem = data.totalcount;
	        }

	        //we need the number of pages for the pagination
	        data.totalPages = Math.ceil(data.totalcount/options.searchoptions.pagesize);
	        data.pageArray = [];
	        //we need an array for mustache
	        for(var i = 1; i <= data.totalPages; i++){
	            var activePage = (i == options.searchoptions.page);
	            var pageObject = {pageIndex:i,active:activePage};
	            data.pageArray.push(pageObject);
	        }
	        if(options.showingMapView){data.islastpage = (options.searchoptions.page == data.totalPages);}

		// The search is triggered by my list page
		if( 1 === options.usemylist )
		{
			data.usemylist = (options.usemylist == 1); // we identify that the widget in question is a wishlist
			data.mylist = BAPI.session.mylist; // contains the wishlist itself
		}
		
		/* we need this only for the attractions page */
		if(entity == 'poi'){
			data.site = options.site;
		}
		var html = context.mustacheHelpers.render(options.template, data); // do the mustache call
		$(targetid).html(html); // update the target
		//bootstrap pagination
	        if($('#listing-pag').length > 0){
	            var pagOptions = {
	                bootstrapMajorVersion: 2,
	                numberOfPages: 4,
	                currentPage: data.curpage + 1,
	                totalPages: data.totalPages,
	                onPageClicked: function(e,originalEvent,type,page){
						$(targetid).block({ message: "<img src='" + loadingImgUrl + "' />" });
	                    options.searchoptions.page = page;
	                    alldata = [];
	                    doSearch(targetid, ids, entity, options, alldata, callback);
	                }
	            }
	            $('#listing-pag').bootstrapPaginator(pagOptions);
	        }
		$("img").unveil(); // since we have our template rendered, we can start showing the carousel
		/* set the dropdown to the selected value */
		if($("#poitypefilter-dpd").length > 0 && entity == 'poi'){
			$("#poitypefilter-dpd").val($(targetid).data("poitypeselected"));
		}
		
		// apply rowfix
		var rowfixselector = $(targetid).attr('data-rowfixselector');
		var rowfixcount = parseInt($(targetid).attr('data-rowfixcount'));
		if (typeof(rowfixselector)!=="undefined" && rowfixselector!='' && rowfixcount>0) {
			rowfixselector = decodeURIComponent(rowfixselector)
			BAPI.log("Applying row fix to " + rowfixselector + " on every " + rowfixcount + " row.");
			context.rowfix(rowfixselector, rowfixcount);
		}
		
		if (options.applyfixers==1) {
			BAPI.log("Applying fixers.");
			context.inithelpers.applytruncate();	
			context.inithelpers.applydotdotdot();
			context.inithelpers.applyflexsliders(options);
			context.inithelpers.setupmapwidgets(options);
		}
		
		if (callback) { callback(data); }
	}


	//Keep these input fields in sync
	var inputs = {
		'.adultsfield': 		'[name=adults]',
		'.childrenfield': 		'#txtChildren',
		'#quoteLos': 			'#questionsLos',
	};

	$.each(inputs, function(i,v) {
		$(i).on('change keyup', function(){
		   $(v).val($(i).val());
		}).trigger('keyup');

		$(v).on('change keyup', function(){
		   $(i).val($(v).val());
		}).trigger('keyup');
	});

	//Sync
	var $checkin = $('#rateblockcheckin, #txtCheckIn-new, #searchcheckin');  
	$checkin.on('change keyup', function () {
	     $checkin.val($(this).val());
	});

	var $checkout = $('#rateblockcheckout, #txtCheckOut-new, #searchcheckout');  
	$checkout.on('change keyup', function () {
	     $checkout.val($(this).val());
	});


	/*
	** Date Pickers
	*/

	//Get translations
	$.ajax({
		url: ajaxurl,
		type: 'POST',
		data: {
			action: 'get_translations'
		},
		dataType: 'JSON',
		success: function(response) {

		}
	});


	function saveToSession(sesh) {
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'set_session',
				session: sesh
			},
			dataType: 'html',
			success: function(response) { console.log(response); }
		});
	}

	// Extend the default picker options for all instances.
	$.extend($.fn.pickadate.defaults, {
		format: BAPI.defaultOptions.dateFormat.toLowerCase(),
	  	formatSubmit: BAPI.defaultOptions.dateFormatBAPI.toLowerCase(),
	  	min: true,
	  	onOpen: function() {
	  		var v = this.get() || new Date();
	  		if(v) { this.set('select', v); }
	  	},
	})


	function createPickers() { 
		var today = new Date(),
			checkins = [],
			checkouts = [];

		if(typeof(availability) === 'undefined') { 
			availability = false; 
		} else {
			availability = $.parseJSON(availability);

			
			$.each(availability, function(i,v) {

				var from = v.from.split('/'),
					to = v.to.split('/');

					//Months start at 0 index
					from[0] = from[0]-1;
					to[0] = to[0]-1;

					//Change from mm/dd/yyyy to yyyy/mm/dd
					v.from = [ Number(from[2]), Number(from[0]), Number(from[1]) ];
					v.to = [ Number(to[2]), Number(to[0]), Number(to[1]) ];

				var cin = moment(v.from),
					cout = moment(v.to);
				var ciny = cin.year(),
					cinm = cin.month(),
					cind = cin.date(),
					couty = cout.year(),
					coutm = cout.month(),
					coutd = cout.date();


				//If one night booking
				if( cout.diff(cin, 'days') == 1 ) { //console.log('one night stay: '+v.from+' to '+v.to);
					checkins.push( [ciny, cinm, cind] ); //console.log('checkin: '+[ciny, cinm, cind]);
					checkouts.push( [couty, coutm, coutd] ); //console.log('checkout: '+[couty, coutm, coutd]);
				} else {
					//Multiple nights
					checkins.push( [ciny, cinm, cind] ); //Dont allow checkinss on this day
					checkouts.push( [couty, coutm, coutd] ); //Dont allow checkouts on this day

					var from = cin.add(1, 'days'),
						to = cout.subtract(1, 'days');
					var booking = {
						from: [ from.year(), from.month(), from.date() ],
						to: [ to.year(), to.month(), to.date() ]
					};

					//console.log('multi day')
					//console.log(booking);

					checkins.push(booking);
					checkouts.push(booking);
				}

			});

			// console.log('---checkins');
			// console.log(checkins);

			// console.log('---checkouts');
			// console.log(checkouts);
			
		}


		var $checkin = $("#searchcheckin, #rateblockcheckin, #txtCheckIn-new");

		$checkin.each(function(i,v) {
			$(v).pickadate({
				hiddenName: true,
				min: true,
				disable: checkins,
			});
		});
		
		var minStay = $checkin.data('min-stay') || 1;
		

		var $checkout = $("#searchcheckout, #rateblockcheckout, #txtCheckOut-new");

		if($checkout.length > 0) { 

			$checkout.each(function(i,v) {
				$(v).pickadate({
					hiddenName: true,
					min: true,
					disable: checkouts,
				});
			});
		}

		
		var checkin = $checkin.pickadate('picker');
		var checkout = $checkout.pickadate('picker');


		$checkin.each(function() {
			$(this).pickadate('picker').on('open', function() {
				var date = $checkin.attr('setval');

				if(typeof(date) !== 'undefined') {
					checkin.set( 'select', new Date(date) );
				}
			});
		});

		$checkin.each(function(i,v) { 
			var that = $(v);
			
			that.pickadate('picker').on('close', function(e) {
				var date = checkin.get();

				saveToSession({
					'scheckin': checkin.get()
				});

				if($checkout.length > 0 && date) {
					checkout.set( 'select', moment(date, BAPI.defaultOptions.dateFormat.toUpperCase()).add(minStay, 'days').toArray() );
					$(".picker__input").eq( $(".picker__input").index( that ) + 1 ).pickadate('picker').open();
				}
				
			});
		});

		if($checkout.length > 0) {
			$checkout.each(function() {
				$(this).pickadate('picker').on('close', function() {
					var date = checkout.get();
					
					saveToSession({
						'scheckout': checkout.get()
					});

					if(date) {
						//checkin.set('max', moment(date).subtract(minStay, 'days').toArray() );
					}
				});
			});
		}

		$(window).on('clear-dates', function() {
			checkin.clear().set({
				'min': true,
				'max': false
			});
			if($checkout.length > 0) {
				checkout.clear().set({
					'min': true, //minStay, 
					'max': false
				});
			}
		});

		$(window).on('update-pickers', function(event, method, data) { 
			console.log('---updating pickers');
			
			if(data) {
				checkin.set(method, data);
				if($checkout.length > 0) { checkout.set(method, data); }
			}
		});

	}


	//Load date picker translations based on locale, error will run in English
	$(window).on('createPickers', function() {
		if($("#searchcheckin, #rateblockcheckin, #txtCheckIn-new, #searchcheckout, #rateblockcheckout, #txtCheckOut-new").length > 0) { 
			if(locale != 'en-US') {
				$.ajax({
				  	url: plugin_url+'js/pickadate/translations/'+locale+'.js',
				  	dataType: "script"
				}).complete(function() {
					createPickers();
				});
			} else { 
				createPickers();
			}		
		}
	});




	/*
	** Rate block
	*/
	$(".bapi-inquirenow").on('click', function(e) {
		e.preventDefault();
		$("#txtName").focus();
	});

	//Get Quote
	$("body").on('click', '.bapi-getquote', function(e) { 
		e.preventDefault();

		var that = $(this);

		$(".alert-error").hide();

		var data = $('form[name="getQuote"]').serializeArray();

		Cookies.set('searchdata', data);

		$(window).trigger('updateBookingForm');

		// //Get property details
		// $.ajax({
		// 	url: ajaxurl,
		// 	type: 'POST',
		// 	data: {
		// 		action: 'get_post_data',
		// 		id: $(this).data('id'),
		// 		key: 'bapi_property_data',
		// 		single: true
		// 	},
		// 	dataType: 'json',
		// 	success: function(response) { 
		// 		var quote = response.ContextData.Quote.QuoteDisplay;

		// 		$('.bapi-booknow, .quote-display').removeClass('hide');
		// 		$('.bapi-inquirenow').addClass('hide');

		// 		$(".quote-prefix").text(quote.prefix);
		// 		$(".rate").text(quote.value);
		// 		$(".quote-suffix").text(quote.suffix);

		// 	},
		// 	error: function(err) {
		// 		console.log('error');
		// 	}
		// }).done(function() { 
		// 	that.parents('.widget').removeClass('loading');
		// });
	});


	/*
	** Maps
	*/
	if( $('.bapi-map').length > 0 ) { 
		if(
			0 === $('#google-map-script').length &&
			(
				!$.isPlainObject( window.google ) ||
				!$.isPlainObject( window.google.maps )
			)
		) {
			console.log("loading google maps.");
			var script = document.createElement("script");
			script.type = "text/javascript";
			script.id = "google-map-script";
			script.src = "//maps.google.com/maps/api/js?sensor=false&key=AIzaSyAY7wxlnkMG6czYy9K-wM4OWXs0YFpFzEE&callback=BAPI.UI.setupmapwidgetshelper";
			document.body.appendChild(script);
		}
		else { 
			console.log("google maps already loaded.");
			BAPI.UI.setupmapwidgetshelper();
		}
	}


});

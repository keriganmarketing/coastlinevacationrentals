jQuery(function() {
	if($(".sessionheadline-new").length > 0) {
		$(".sessionheadline-new").typeaheadmap({
			"source": headlines,
			"key": "key",
			"value": "value",
			"displayer": function(that, item, highlighted) {
				return item[that.value].split('+').join(' '); //highlighted + ' (' + item[that.value] + ' )' ;
			}
		});
	}

	if($(".bapi-locationsearch-new").length > 0) {
		$(".bapi-locationsearch-new").typeaheadmap({
				"source": cities,
				"key": "key",
				"value": "value",
				"displayer": function(that, item, highlighted) {
					return item[that.value].split('+').join(' '); //highlighted + ' (' + item[that.value] + ' )' ;
				}

			});
	}

	if($(".bapi-malocationsearch-new").length > 0) {
		$(".bapi-malocationsearch-new").typeaheadmap({
				"source": hierarchy,
				"key": "key",
				"value": "value",
				"displayer": function(that, item, highlighted) {
					return item[that.value].split('+').join(' '); //highlighted + ' (' + item[that.value] + ' )' ;
				}

			});
	}


	$(".quicksearch-doclear").on('click', function() {
		$(window).trigger('clear-dates');
		Cookies.set('searchdata', {});
		BAPI.session.searchparams = {};
	});


	// _blank if in iframe _self if not
	var form_target = (window.self !== window.top) ? '_blank' : '_self' ;

	$('.kigo-booking-theme form[name="property-search"]').attr('target',form_target);
});
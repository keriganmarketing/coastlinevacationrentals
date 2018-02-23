/*typeaheadmap version 1.0.2*/
!function(t){"use strict";var e=function(e,s){this.$element=t(e),this.options=t.extend({},t.fn.typeaheadmap.defaults,s),this.matcher=this.options.matcher||this.matcher,this.sorter=this.options.sorter||this.sorter,this.highlighter=this.options.highlighter||this.highlighter,this.updater=this.options.updater||this.updater,this.$menu=t(this.options.menu),this.source=this.options.source,this.shown=!1,this.key=this.options.key,this.value=this.options.value,this.listener=this.options.listener||this.listener,this.displayer=this.options.displayer||this.displayer,this.notfound=this.options.notfound||new Array,this.listen()};e.prototype={constructor:e,listener:function(t,e){},select:function(){var t=this.$menu.find(".active"),e=t.attr("data-key");return this.listener(e,t.attr("data-value")),this.$element.val(this.updater(e)).change(),this.hide()},updater:function(t){return t},show:function(){var e=t.extend({},this.$element.position(),{height:this.$element[0].offsetHeight});return this.$menu.insertAfter(this.$element).css({top:e.top+e.height,left:e.left}).show(),this.shown=!0,this},hide:function(){return this.$menu.hide(),this.shown=!1,this},lookup:function(e){var s;return this.query=this.$element.val(),!this.query||this.query.length<this.options.minLength?this.shown?this.hide():this:(s=t.isFunction(this.source)?this.source(this.query,t.proxy(this.process,this)):this.source,s?this.process(s):this)},process:function(e){var s=this;return e=t.grep(e,function(t){return s.matcher(t)}),e=this.sorter(e),e.length?this.render(e.slice(0,this.options.items)).show():this.shown?this.notfound.length?this.render(this.notfound).show():this.hide():this},matcher:function(t){return~t[this.key].toLowerCase().indexOf(this.query.toLowerCase())},sorter:function(t){for(var e,s=[],i=[],n=[];e=t.shift();)e[this.key].toLowerCase().indexOf(this.query.toLowerCase())?~e[this.key].indexOf(this.query)?i.push(e):n.push(e):s.push(e);return s.concat(i,n)},highlighter:function(t,e){var s=this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&");return e.displayer(e,t,t[e.key].replace(new RegExp("("+s+")","ig"),function(t,e){return"<strong>"+e+"</strong>"}))},displayer:function(t,e,s){return s+" "+e[t.value]},render:function(e){var s=this;return e=t(e).map(function(e,i){return e=t(s.options.item).attr("data-key",i[s.key]),e.attr("data-value",i[s.value]),e.find("a").html(s.highlighter(i,s)),e[0]}),e.first().addClass("active"),this.$menu.html(e),this},next:function(e){var s=this.$menu.find(".active").removeClass("active"),i=s.next();i.length||(i=t(this.$menu.find("li")[0])),i.addClass("active")},prev:function(t){var e=this.$menu.find(".active").removeClass("active"),s=e.prev();s.length||(s=this.$menu.find("li").last()),s.addClass("active")},listen:function(){this.$element.on("blur",t.proxy(this.blur,this)).on("keypress",t.proxy(this.keypress,this)).on("keyup",t.proxy(this.keyup,this)),this.eventSupported("keydown")&&this.$element.on("keydown",t.proxy(this.keydown,this)),this.$menu.on("click",t.proxy(this.click,this)).on("mouseenter","li",t.proxy(this.mouseenter,this))},eventSupported:function(t){var e=t in this.$element;return e||(this.$element.setAttribute(t,"return;"),e="function"==typeof this.$element[t]),e},move:function(t){if(this.shown){switch(t.keyCode){case 9:case 13:case 27:t.preventDefault();break;case 38:t.preventDefault(),this.prev();break;case 40:t.preventDefault(),this.next()}t.stopPropagation()}},keydown:function(e){this.suppressKeyPressRepeat=~t.inArray(e.keyCode,[40,38,9,13,27]),this.move(e)},keypress:function(t){this.suppressKeyPressRepeat||this.move(t)},keyup:function(t){switch(t.keyCode){case 40:case 38:case 16:case 17:case 18:break;case 9:case 13:if(!this.shown)return;this.select();break;case 27:if(!this.shown)return;this.hide();break;default:this.lookup()}t.stopPropagation(),t.preventDefault()},blur:function(t){var e=this;setTimeout(function(){e.hide()},150)},click:function(t){t.stopPropagation(),t.preventDefault(),this.select()},mouseenter:function(e){this.$menu.find(".active").removeClass("active"),t(e.currentTarget).addClass("active")}};var s=t.fn.typeaheadmap;t.fn.typeaheadmap=function(s){return this.each(function(){var i=t(this),n=i.data("typeaheadmap"),h="object"==typeof s&&s;n||i.data("typeaheadmap",n=new e(this,h)),"string"==typeof s&&n[s]()})},t.fn.typeaheadmap.defaults={source:[],items:8,menu:'<ul class="typeaheadmap dropdown-menu"></ul>',item:'<li><a href="#"></a></li>',minLength:1},t.fn.typeaheadmap.Constructor=e,t.fn.typeaheadmap.noConflict=function(){return t.fn.typeaheadmap=s,this},t(document).on("focus.typeaheadmap.data-api",'[data-provide="typeaheadmap"]',function(e){var s=t(this);s.data("typeaheadmap")||(e.preventDefault(),s.typeaheadmap(s.data()))})}(window.jQuery);
//custom JS
function attachEventsFpWidget(addProperty,propertyNamesList,idsList,sourceHeadlines){
		var thisNameList,thisList,thisListVal,tempArrayList;
		tmpArrayList = [];
		thisNameList = $(propertyNamesList);
		thisList = $(idsList);
		thisListVal = thisList.val();
		$(addProperty).off("focus");
		$(addProperty).on("focus", function() {
				$(this).typeaheadmap({ 
					"source": sourceHeadlines, 
					"key": "obj", 
					"value": "id",
					"listener": function(k,v) {
						//dont add if its already there
						if(thisListVal.indexOf(v) > -1){return;}
						//we load objects if there is any
						if(thisListVal != ""){
							tmpArrayList = JSON.parse(thisListVal);
						}
						//we create an object
						tmpObj = {};
						tmpObj.headline = k;
						tmpObj.id = v;
						
						//we put it in the array of objects
						tmpArrayList.push(tmpObj);
						
						//we put in the hidden field JSON data
						thisList.val(JSON.stringify(tmpArrayList));
						//add ot the name list
						thisNameList.append("<li data-value='"+v+"' data-headline='"+k+"'><span class='item-wrap'><span class='head-block'>"+k+'</span><span class="group-btn"><button type="button" class="button  remove-btn button-small"><span class="dashicons dashicons-trash"></span></button><button type="button" class="button down-btn button-small"><span class="dashicons dashicons-arrow-down-alt"></span></button><button type="button" class="button up-btn button-small"><span class="dashicons dashicons-arrow-up-alt"></span></button></span></span>'+"</li>");
						//refresh
						thisListVal = thisList.val();
						hideBtns(thisNameList,addProperty);
					},
					"updater": function(item){/*empty*/},
					"displayer": function(that, item, highlighted) {
						return highlighted;
					}
				});
		});
		$(propertyNamesList+" li .remove-btn").off("click");
		$(propertyNamesList).on("click", '.remove-btn', function(){
			var thisIDprop = $(this).parents("li").data("value");
			//remove the ID from the list
			var tmpArrayList = JSON.parse(thisListVal);

			for( var i=0; i<tmpArrayList.length; i++ ) {
				if(tmpArrayList[i]["id"] == thisIDprop){
					tmpArrayList.splice(i,1);
					break;
				}
			}

			thisList.val(JSON.stringify(tmpArrayList));
			//refresh
			thisListVal = thisList.val();
			//remove from the list
			$(this).parents("li").remove();
			hideBtns(thisNameList,addProperty);
		});
		$(propertyNamesList+" li .down-btn").off("click");
		$(propertyNamesList).on("click", '.down-btn', function(){
			//element to move
			var $el = $(this).parents("li");
			//move element down one step
			if ($el.not(':last-child')){
				$el.next().after($el);
			}
			updateValFromOl(thisList,thisNameList);
		});
		$(propertyNamesList+" li .up-btn").off("click");
		$(propertyNamesList).on("click", '.up-btn', function(){
			//element to move
			var $el = $(this).parents("li");
			//move element down one step
			if ($el.not(':first-child')){
				$el.prev().before($el);
			}
			updateValFromOl(thisList,thisNameList);
		});
}
function updateValFromOl(thisList,thisNameList){
	var tmpArrayList2 = [];
	thisNameList.children("li").each( function( index, element ) {
		var $theEl = $(this);
		//we create an object
		tmpObj = {};
		tmpObj.headline = $theEl.data("headline");
		tmpObj.id = $theEl.data("value");
		
		//we put it in the array of objects
		tmpArrayList2.push(tmpObj);
		//buttons
		if($theEl.is(':last-child')){
			$theEl.find(".down-btn").hide();
		}else{
			$theEl.find(".down-btn").show();
		}
		if($theEl.is(':first-child')){
			$theEl.find(".up-btn").hide();
		}else{
			$theEl.find(".up-btn").show();
		}
	});
	//we put in the hidden field JSON data
	thisList.val(JSON.stringify(tmpArrayList2));
}
function hideBtns(thisNameList,addProperty){
	thisNameList.find("li:first-child").find(".up-btn").hide();
	thisNameList.find("li:first-child").find(".down-btn").show();
	thisNameList.find("li:last-child").find(".down-btn").hide();
	thisNameList.find("li").not(":first-child").not(":last-child").find(".up-btn,.down-btn").show();
}

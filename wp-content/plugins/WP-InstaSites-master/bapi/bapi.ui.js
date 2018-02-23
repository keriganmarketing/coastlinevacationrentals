;
;

/* mustache.js - Logic-less {{mustache}} templates with JavaScript */
var Mustache;(function(a){if(typeof module!=="undefined"&&module.exports){module.exports=a}else{if(typeof define==="function"){define(a)}else{Mustache=a}}}((function(){var u={};u.name="mustache.js";u.version="0.7.0";u.tags=["{{","}}"];u.Scanner=t;u.Context=r;u.Writer=p;var d=/\s*/;var l=/\s+/;var j=/\S/;var h=/\s*=/;var n=/\s*\}/;var s=/#|\^|\/|>|\{|&|=|!/;function o(x,w){return RegExp.prototype.test.call(x,w)}function g(w){return !o(j,w)}var k=Array.isArray||function(w){return Object.prototype.toString.call(w)==="[object Array]"};function f(w){return w.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&")}var c={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;","/":"&#x2F;"};function m(w){return String(w).replace(/[&<>"'\/]/g,function(x){return c[x]})}u.escape=m;function t(w){this.string=w;this.tail=w;this.pos=0}t.prototype.eos=function(){return this.tail===""};t.prototype.scan=function(x){var w=this.tail.match(x);if(w&&w.index===0){this.tail=this.tail.substring(w[0].length);this.pos+=w[0].length;return w[0]}return""};t.prototype.scanUntil=function(x){var w,y=this.tail.search(x);switch(y){case -1:w=this.tail;this.pos+=this.tail.length;this.tail="";break;case 0:w="";break;default:w=this.tail.substring(0,y);this.tail=this.tail.substring(y);this.pos+=y}return w};function r(w,x){this.view=w;this.parent=x;this.clearCache()}r.make=function(w){return(w instanceof r)?w:new r(w)};r.prototype.clearCache=function(){this._cache={}};r.prototype.push=function(w){return new r(w,this)};r.prototype.lookup=function(w){var z=this._cache[w];if(!z){if(w==="."){z=this.view}else{var y=this;while(y){if(w.indexOf(".")>0){var A=w.split("."),x=0;z=y.view;while(z&&x<A.length){z=z[A[x++]]}}else{z=y.view[w]}if(z!=null){break}y=y.parent}}this._cache[w]=z}if(typeof z==="function"){z=z.call(this.view)}return z};function p(){this.clearCache()}p.prototype.clearCache=function(){this._cache={};this._partialCache={}};p.prototype.compile=function(x,w){return this._compile(this._cache,x,x,w)};p.prototype.compilePartial=function(x,y,w){return this._compile(this._partialCache,x,y,w)};p.prototype.render=function(y,w,x){return this.compile(y)(w,x)};p.prototype._compile=function(x,z,B,y){if(!x[z]){var C=u.parse(B,y);var A=e(C);var w=this;x[z]=function(D,F){if(F){if(typeof F==="function"){w._loadPartial=F}else{for(var E in F){w.compilePartial(E,F[E])}}}return A(w,r.make(D),B)}}return x[z]};p.prototype._section=function(w,x,E,D){var C=x.lookup(w);switch(typeof C){case"object":if(k(C)){var y="";for(var z=0,B=C.length;z<B;++z){y+=D(this,x.push(C[z]))}return y}return C?D(this,x.push(C)):"";case"function":var F=this;var A=function(G){return F.render(G,x)};return C.call(x.view,E,A)||"";default:if(C){return D(this,x)}}return""};p.prototype._inverted=function(w,x,z){var y=x.lookup(w);if(!y||(k(y)&&y.length===0)){return z(this,x)}return""};p.prototype._partial=function(w,x){if(!(w in this._partialCache)&&this._loadPartial){this.compilePartial(w,this._loadPartial(w))}var y=this._partialCache[w];return y?y(x):""};p.prototype._name=function(w,x){var y=x.lookup(w);if(typeof y==="function"){y=y.call(x.view)}return(y==null)?"":String(y)};p.prototype._escaped=function(w,x){return u.escape(this._name(w,x))};function i(x){var z=x[3];var w=z;var y;while((y=x[4])&&y.length){x=y[y.length-1];w=x[3]}return[z,w]}function e(y){var w={};function x(A,D,C){if(!w[A]){var B=e(D);w[A]=function(F,E){return B(F,E,C)}}return w[A]}function z(G,E,F){var B="";var D,H;for(var C=0,A=y.length;C<A;++C){D=y[C];switch(D[0]){case"#":H=F.slice.apply(F,i(D));B+=G._section(D[1],E,H,x(C,D[4],F));break;case"^":B+=G._inverted(D[1],E,x(C,D[4],F));break;case">":B+=G._partial(D[1],E);break;case"&":B+=G._name(D[1],E);break;case"name":B+=G._escaped(D[1],E);break;case"text":B+=D[1];break}}return B}return z}function v(B){var w=[];var A=w;var C=[];var y,z;for(var x=0;x<B.length;++x){y=B[x];switch(y[0]){case"#":case"^":y[4]=[];C.push(y);A.push(y);A=y[4];break;case"/":if(C.length===0){throw new Error("Unopened section: "+y[1])}z=C.pop();if(z[1]!==y[1]){throw new Error("Unclosed section: "+z[1])}if(C.length>0){A=C[C.length-1][4]}else{A=w}break;default:A.push(y)}}z=C.pop();if(z){throw new Error("Unclosed section: "+z[1])}return w}function a(z){var y,w;for(var x=0;x<z.length;++x){y=z[x];if(w&&w[0]==="text"&&y[0]==="text"){w[1]+=y[1];w[3]=y[3];z.splice(x--,1)}else{w=y}}}function q(w){if(w.length!==2){throw new Error("Invalid tags: "+w.join(" "))}return[new RegExp(f(w[0])+"\\s*"),new RegExp("\\s*"+f(w[1]))]}u.parse=function(I,K){K=K||u.tags;var J=q(K);var z=new t(I);var G=[],E=[],C=false,L=false;function x(){if(C&&!L){while(E.length){G.splice(E.pop(),1)}}else{E=[]}C=false;L=false}var w,F,H,A;while(!z.eos()){w=z.pos;H=z.scanUntil(J[0]);if(H){for(var B=0,D=H.length;B<D;++B){A=H.charAt(B);if(g(A)){E.push(G.length)}else{L=true}G.push(["text",A,w,w+1]);w+=1;if(A==="\n"){x()}}}w=z.pos;if(!z.scan(J[0])){break}C=true;F=z.scan(s)||"name";z.scan(d);if(F==="="){H=z.scanUntil(h);z.scan(h);z.scanUntil(J[1])}else{if(F==="{"){var y=new RegExp("\\s*"+f("}"+K[1]));H=z.scanUntil(y);z.scan(n);z.scanUntil(J[1]);F="&"}else{H=z.scanUntil(J[1])}}if(!z.scan(J[1])){throw new Error("Unclosed tag at "+z.pos)}G.push([F,H,w,z.pos]);if(F==="name"||F==="{"||F==="&"){L=true}if(F==="="){K=H.split(l);J=q(K)}}a(G);return v(G)};var b=new p();u.clearCache=function(){return b.clearCache()};u.compile=function(x,w){return b.compile(x,w)};u.compilePartial=function(x,y,w){return b.compilePartial(x,y,w)};u.render=function(y,w,x){return b.render(y,w,x)};u.to_html=function(z,x,y,A){var w=u.render(z,x,y);if(typeof A==="function"){A(w)}else{return w}};return u}())));

/* Weather */
(function(i){i.fn.weatherfeed=function(o,h,t){var h=i.extend({unit:"c",image:!0,country:!1,highlow:!0,wind:!0,humidity:!1,visibility:!1,sunrise:!1,sunset:!1,forecast:!1,link:!0,showerror:!0,linktarget:"_self",woeid:!1},h),p="odd";return this.each(function(m,q){var k=i(q);k.hasClass("weatherFeed")||k.addClass("weatherFeed");if(!i.isArray(o))return!1;var l=o.length;10<l&&(l=10);for(var j="",m=0;m<l;m++)""!=j&&(j+=","),j+="'"+o[m]+"'";now=new Date;l="//query.yahooapis.com/v1/public/yql?q="+encodeURIComponent("select * from weather.forecast where "+
(h.woeid?"woeid":"location")+" in ("+j+") and u='"+h.unit+"'")+"&rnd="+now.getFullYear()+now.getMonth()+now.getDay()+now.getHours()+"&format=json&callback=?";i.ajax({type:"GET",url:l,dataType:"json",success:function(f){if(f.query){if(0<f.query.results.channel.length)for(var c=f.query.results.channel.length,e=0;e<c;e++)u(q,f.query.results.channel[e],h);else u(q,f.query.results.channel,h);i.isFunction(t)&&t.call(this,k)}else h.showerror&&k.html("<p>Weather information unavailable</p>")},error:function(){h.showerror&&
k.html("<p>Weather request failed</p>")}});var u=function(f,c,e){var f=i(f),a=c.wind.direction;348.75<=a&&360>=a&&(a="N");0<=a&&11.25>a&&(a="N");11.25<=a&&33.75>a&&(a="NNE");33.75<=a&&56.25>a&&(a="NE");56.25<=a&&78.75>a&&(a="ENE");78.75<=a&&101.25>a&&(a="E");101.25<=a&&123.75>a&&(a="ESE");123.75<=a&&146.25>a&&(a="SE");146.25<=a&&168.75>a&&(a="SSE");168.75<=a&&191.25>a&&(a="S");191.25<=a&&213.75>a&&(a="SSW");213.75<=a&&236.25>a&&(a="SW");236.25<=a&&258.75>a&&(a="WSW");258.75<=a&&281.25>a&&(a="W");
281.25<=a&&303.75>a&&(a="WNW");303.75<=a&&326.25>a&&(a="NW");326.25<=a&&348.75>a&&(a="NNW");var g=c.item.forecast[0];wpd=c.item.pubDate;n=wpd.indexOf(":");tpb=s(wpd.substr(n-2,8));tsr=s(c.astronomy.sunrise);tss=s(c.astronomy.sunset);daynight=tpb>tsr&&tpb<tss?"day":"night";var b='<div class="weatherItem '+p+" "+daynight+'"';e.image&&(b+=' style="background-image: url(http://l.yimg.com/a/i/us/nws/weather/gr/'+c.item.condition.code+daynight.substring(0,1)+'.png); background-repeat: no-repeat;"');b=b+
">"+('<div class="weatherCity">'+c.location.city+"</div>");e.country&&(b+='<div class="weatherCountry">'+c.location.country+"</div>");b+='<div class="weatherTemp">'+c.item.condition.temp+"&deg;</div>";b+='<div class="weatherDesc">'+c.item.condition.text+"</div>";e.highlow&&(b+='<div class="weatherRange">High: '+g.high+"&deg; Low: "+g.low+"&deg;</div>");e.wind&&(b+='<div class="weatherWind">Wind: '+a+" "+c.wind.speed+c.units.speed+"</div>");e.humidity&&(b+='<div class="weatherHumidity">Humidity: '+
c.atmosphere.humidity+"</div>");e.visibility&&(b+='<div class="weatherVisibility">Visibility: '+c.atmosphere.visibility+"</div>");e.sunrise&&(b+='<div class="weatherSunrise">Sunrise: '+c.astronomy.sunrise+"</div>");e.sunset&&(b+='<div class="weatherSunset">Sunset: '+c.astronomy.sunset+"</div>");if(e.forecast){b+='<div class="weatherForecast">';a=c.item.forecast;for(g=0;g<a.length;g++)b+='<div class="weatherForecastItem" style="background-image: url(http://l.yimg.com/a/i/us/nws/weather/gr/'+a[g].code+
's.png); background-repeat: no-repeat;">',b+='<div class="weatherForecastDay">'+a[g].day+"</div>",b+='<div class="weatherForecastDate">'+a[g].date+"</div>",b+='<div class="weatherForecastText">'+a[g].text+"</div>",b+='<div class="weatherForecastRange">High: '+a[g].high+" Low: "+a[g].low+"</div>",b+="</div>";b+="</div>"}e.link&&(b+='<div class="weatherLink"><a href="'+c.link+'" target="'+e.linktarget+'" title="Read full forecast">Full forecast</a></div>');p="odd"==p?"even":"odd";f.append(b+"</div>")},
s=function(f){d=new Date;return r=new Date(d.toDateString()+" "+f)}})}})(jQuery);

/* jTruncate */
(function($){$.fn.jTruncate=function(h){var i={length:300,minTrail:20,moreText:"more",lessText:"less",ellipsisText:"...",moreAni:"",lessAni:""};var h=$.extend(i,h);return this.each(function(){obj=$(this);var a=obj.html();if(a.length>h.length+h.minTrail){var b=a.indexOf(' ',h.length);if(b!=-1){var b=a.indexOf(' ',h.length);var c=a.substring(0,b);var d=a.substring(b,a.length-1);obj.html(c+'<span class="truncate_ellipsis">'+h.ellipsisText+'</span>'+'<span class="truncate_more">'+d+'</span>');obj.find('.truncate_more').css("display","none");obj.append('<div class="clearboth">'+'<a href="#" class="truncate_more_link">'+h.moreText+'</a>'+'</div>');var e=$('.truncate_more_link',obj);var f=$('.truncate_more',obj);var g=$('.truncate_ellipsis',obj);e.click(function(){if(e.text()==h.moreText){f.show(h.moreAni);e.text(h.lessText);g.css("display","none")}else{f.hide(h.lessAni);e.text(h.moreText);g.css("display","inline")}return false})}}})}})(jQuery);

/* dot dot dot */
(function(a){function c(a,b,c){var d=a.children(),e=!1;a.empty();for(var g=0,h=d.length;h>g;g++){var i=d.eq(g);if(a.append(i),c&&a.append(c),f(a,b)){i.remove(),e=!0;break}c&&c.remove()}return e}function d(b,c,g,h,i){var j=b.contents(),k=!1;b.empty();for(var l="table, thead, tbody, tfoot, tr, col, colgroup, object, embed, param, ol, ul, dl, select, optgroup, option, textarea, script, style",m=0,n=j.length;n>m&&!k;m++){var o=j[m],p=a(o);void 0!==o&&(b.append(p),i&&b[b.is(l)?"after":"append"](i),3==o.nodeType?f(g,h)&&(k=e(p,c,g,h,i)):k=d(p,c,g,h,i),k||i&&i.remove())}return k}function e(a,b,c,d,h){var k=!1,l=a[0];if(l===void 0)return!1;for(var m="letter"==d.wrap?"":" ",n=j(l).split(m),o=-1,p=-1,q=0,r=n.length-1;r>=q;){var s=Math.floor((q+r)/2);if(s==p)break;p=s,i(l,n.slice(0,p+1).join(m)+d.ellipsis),f(c,d)?r=p:(o=p,q=p)}if(-1==o||1==n.length&&0==n[0].length){var u=a.parent();a.remove();var v=h?h.length:0;if(u.contents().size()>v){var w=u.contents().eq(-1-v);k=e(w,b,c,d,h)}else{var l=u.prev().contents().eq(-1)[0];if(l!==void 0){var t=g(j(l),d);i(l,t),u.remove(),k=!0}}}else{var t=g(n.slice(0,o+1).join(m),d);k=!0,i(l,t)}return k}function f(a,b){return a.innerHeight()>b.maxHeight}function g(b,c){for(;a.inArray(b.slice(-1),c.lastCharacter.remove)>-1;)b=b.slice(0,-1);return 0>a.inArray(b.slice(-1),c.lastCharacter.noEllipsis)&&(b+=c.ellipsis),b}function h(a){return{width:a.innerWidth(),height:a.innerHeight()}}function i(a,b){a.innerText?a.innerText=b:a.nodeValue?a.nodeValue=b:a.textContent&&(a.textContent=b)}function j(a){return a.innerText?a.innerText:a.nodeValue?a.nodeValue:a.textContent?a.textContent:""}function k(b,c){return b===void 0?!1:b?"string"==typeof b?(b=a(b,c),b.length?b:!1):"object"==typeof b?b.jquery===void 0?!1:b:!1:!1}function l(a){for(var b=a.innerHeight(),c=["paddingTop","paddingBottom"],d=0,e=c.length;e>d;d++){var f=parseInt(a.css(c[d]),10);isNaN(f)&&(f=0),b-=f}return b}function m(a,b){return a?(b="string"==typeof b?"dotdotdot: "+b:["dotdotdot:",b],window.console!==void 0&&window.console.log!==void 0&&window.console.log(b),!1):!1}if(!a.fn.dotdotdot){a.fn.dotdotdot=function(e){if(0==this.length)return e&&e.debug===!1||m(!0,'No element found for "'+this.selector+'".'),this;if(this.length>1)return this.each(function(){a(this).dotdotdot(e)});var g=this;g.data("dotdotdot")&&g.trigger("destroy.dot"),g.bind_events=function(){return g.bind("update.dot",function(b,e){b.preventDefault(),b.stopPropagation(),j.maxHeight="number"==typeof j.height?j.height:l(g),j.maxHeight+=j.tolerance,e!==void 0&&(("string"==typeof e||e instanceof HTMLElement)&&(e=a("<div />").append(e).contents()),e instanceof a&&(i=e)),q=g.wrapInner('<div class="dotdotdot" />').children(),q.empty().append(i.clone(!0)).css({height:"auto",width:"auto",border:"none",padding:0,margin:0});var h=!1,k=!1;return n.afterElement&&(h=n.afterElement.clone(!0),n.afterElement.remove()),f(q,j)&&(k="children"==j.wrap?c(q,j,h):d(q,g,q,j,h)),q.replaceWith(q.contents()),q=null,a.isFunction(j.callback)&&j.callback.call(g[0],k,i),n.isTruncated=k,k}).bind("isTruncated.dot",function(a,b){return a.preventDefault(),a.stopPropagation(),"function"==typeof b&&b.call(g[0],n.isTruncated),n.isTruncated}).bind("originalContent.dot",function(a,b){return a.preventDefault(),a.stopPropagation(),"function"==typeof b&&b.call(g[0],i),i}).bind("destroy.dot",function(a){a.preventDefault(),a.stopPropagation(),g.unwatch().unbind_events().empty().append(i).data("dotdotdot",!1)}),g},g.unbind_events=function(){return g.unbind(".dot"),g},g.watch=function(){if(g.unwatch(),"window"==j.watch){var b=a(window),c=b.width(),d=b.height();b.bind("resize.dot"+n.dotId,function(){c==b.width()&&d==b.height()&&j.windowResizeFix||(c=b.width(),d=b.height(),p&&clearInterval(p),p=setTimeout(function(){g.trigger("update.dot")},10))})}else o=h(g),p=setInterval(function(){var a=h(g);(o.width!=a.width||o.height!=a.height)&&(g.trigger("update.dot"),o=h(g))},100);return g},g.unwatch=function(){return a(window).unbind("resize.dot"+n.dotId),p&&clearInterval(p),g};var i=g.contents(),j=a.extend(!0,{},a.fn.dotdotdot.defaults,e),n={},o={},p=null,q=null;return n.afterElement=k(j.after,g),n.isTruncated=!1,n.dotId=b++,g.data("dotdotdot",!0).bind_events().trigger("update.dot"),j.watch&&g.watch(),g},a.fn.dotdotdot.defaults={ellipsis:"... ",wrap:"word",lastCharacter:{remove:[" ",",",";",".","!","?"],noEllipsis:[]},tolerance:0,callback:null,after:null,height:null,watch:!1,windowResizeFix:!0,debug:!1};var b=1,n=a.fn.html;a.fn.html=function(a){return a!==void 0?this.data("dotdotdot")&&"function"!=typeof a?this.trigger("update",[a]):n.call(this,a):n.call(this)};var o=a.fn.text;a.fn.text=function(b){if(b!==void 0){if(this.data("dotdotdot")){var c=a("<div />");return c.text(b),b=c.html(),c.remove(),this.trigger("update",[b])}return o.call(this,b)}return o.call(this)}}})(jQuery);

/* BlockUI */
(function(){"use strict";function e(e){function a(i,a){var l,h;var m=i==window;var g=a&&a.message!==undefined?a.message:undefined;a=e.extend({},e.blockUI.defaults,a||{});if(a.ignoreIfBlocked&&e(i).data("blockUI.isBlocked"))return;a.overlayCSS=e.extend({},e.blockUI.defaults.overlayCSS,a.overlayCSS||{});l=e.extend({},e.blockUI.defaults.css,a.css||{});if(a.onOverlayClick)a.overlayCSS.cursor="pointer";h=e.extend({},e.blockUI.defaults.themedCSS,a.themedCSS||{});g=g===undefined?a.message:g;if(m&&o)f(window,{fadeOut:0});if(g&&typeof g!="string"&&(g.parentNode||g.jquery)){var y=g.jquery?g[0]:g;var b={};e(i).data("blockUI.history",b);b.el=y;b.parent=y.parentNode;b.display=y.style.display;b.position=y.style.position;if(b.parent)b.parent.removeChild(y)}e(i).data("blockUI.onUnblock",a.onUnblock);var w=a.baseZ;var E,S,x,T;if(n||a.forceIframe)E=e('<iframe class="blockUI" style="z-index:'+w++ +';display:none;border:none;margin:0;padding:0;position:absolute;width:100%;height:100%;top:0;left:0" src="'+a.iframeSrc+'"></iframe>');else E=e('<div class="blockUI" style="display:none"></div>');if(a.theme)S=e('<div class="blockUI blockOverlay ui-widget-overlay" style="z-index:'+w++ +';display:none"></div>');else S=e('<div class="blockUI blockOverlay" style="z-index:'+w++ +';display:none;border:none;margin:0;padding:0;width:100%;height:100%;top:0;left:0"></div>');if(a.theme&&m){T='<div class="blockUI '+a.blockMsgClass+' blockPage ui-dialog ui-widget ui-corner-all" style="z-index:'+(w+10)+';display:none;position:fixed">';if(a.title){T+='<div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">'+(a.title||"�")+"</div>"}T+='<div class="ui-widget-content ui-dialog-content"></div>';T+="</div>"}else if(a.theme){T='<div class="blockUI '+a.blockMsgClass+' blockElement ui-dialog ui-widget ui-corner-all" style="z-index:'+(w+10)+';display:none;position:absolute">';if(a.title){T+='<div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">'+(a.title||"�")+"</div>"}T+='<div class="ui-widget-content ui-dialog-content"></div>';T+="</div>"}else if(m){T='<div class="blockUI '+a.blockMsgClass+' blockPage" style="z-index:'+(w+10)+';display:none;position:fixed"></div>'}else{T='<div class="blockUI '+a.blockMsgClass+' blockElement" style="z-index:'+(w+10)+';display:none;position:absolute"></div>'}x=e(T);if(g){if(a.theme){x.css(h);x.addClass("ui-widget-content")}else x.css(l)}if(!a.theme)S.css(a.overlayCSS);S.css("position",m?"fixed":"absolute");if(n||a.forceIframe)E.css("opacity",0);var N=[E,S,x],C=m?e("body"):e(i);e.each(N,function(){this.appendTo(C)});if(a.theme&&a.draggable&&e.fn.draggable){x.draggable({handle:".ui-dialog-titlebar",cancel:"li"})}var k=s&&(!e.support.boxModel||e("object,embed",m?null:i).length>0);if(r||k){if(m&&a.allowBodyStretch&&e.support.boxModel)e("html,body").css("height","100%");if((r||!e.support.boxModel)&&!m){var L=v(i,"borderTopWidth"),A=v(i,"borderLeftWidth");var O=L?"(0 - "+L+")":0;var M=A?"(0 - "+A+")":0}e.each(N,function(e,t){var n=t[0].style;n.position="absolute";if(e<2){if(m)n.setExpression("height","Math.max(document.body.scrollHeight, document.body.offsetHeight) - (jQuery.support.boxModel?0:"+a.quirksmodeOffsetHack+') + "px"');else n.setExpression("height",'this.parentNode.offsetHeight + "px"');if(m)n.setExpression("width",'jQuery.support.boxModel && document.documentElement.clientWidth || document.body.clientWidth + "px"');else n.setExpression("width",'this.parentNode.offsetWidth + "px"');if(M)n.setExpression("left",M);if(O)n.setExpression("top",O)}else if(a.centerY){if(m)n.setExpression("top",'(document.documentElement.clientHeight || document.body.clientHeight) / 2 - (this.offsetHeight / 2) + (blah = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"');n.marginTop=0}else if(!a.centerY&&m){var r=a.css&&a.css.top?parseInt(a.css.top,10):0;var i="((document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "+r+') + "px"';n.setExpression("top",i)}})}if(g){if(a.theme)x.find(".ui-widget-content").append(g);else x.append(g);if(g.jquery||g.nodeType)e(g).show()}if((n||a.forceIframe)&&a.showOverlay)E.show();if(a.fadeIn){var _=a.onBlock?a.onBlock:t;var D=a.showOverlay&&!g?_:t;var P=g?_:t;if(a.showOverlay)S._fadeIn(a.fadeIn,D);if(g)x._fadeIn(a.fadeIn,P)}else{if(a.showOverlay)S.show();if(g)x.show();if(a.onBlock)a.onBlock()}c(1,i,a);if(m){o=x[0];u=e(":input:enabled:visible",o);if(a.focusInput)setTimeout(p,20)}else d(x[0],a.centerX,a.centerY);if(a.timeout){var H=setTimeout(function(){if(m)e.unblockUI(a);else e(i).unblock(a)},a.timeout);e(i).data("blockUI.timeout",H)}}function f(t,n){var r=t==window;var i=e(t);var s=i.data("blockUI.history");var a=i.data("blockUI.timeout");if(a){clearTimeout(a);i.removeData("blockUI.timeout")}n=e.extend({},e.blockUI.defaults,n||{});c(0,t,n);if(n.onUnblock===null){n.onUnblock=i.data("blockUI.onUnblock");i.removeData("blockUI.onUnblock")}var f;if(r)f=e("body").children().filter(".blockUI").add("body > .blockUI");else f=i.find(">.blockUI");if(n.cursorReset){if(f.length>1)f[1].style.cursor=n.cursorReset;if(f.length>2)f[2].style.cursor=n.cursorReset}if(r)o=u=null;if(n.fadeOut){f.fadeOut(n.fadeOut);setTimeout(function(){l(f,s,n,t)},n.fadeOut)}else l(f,s,n,t)}function l(t,n,r,i){var s=e(i);t.each(function(e,t){if(this.parentNode)this.parentNode.removeChild(this)});if(n&&n.el){n.el.style.display=n.display;n.el.style.position=n.position;if(n.parent)n.parent.appendChild(n.el);s.removeData("blockUI.history")}if(s.data("blockUI.static")){s.css("position","static")}if(typeof r.onUnblock=="function")r.onUnblock(i,r);var o=e(document.body),u=o.width(),a=o[0].style.width;o.width(u-1).width(u);o[0].style.width=a}function c(t,n,r){var i=n==window,s=e(n);if(!t&&(i&&!o||!i&&!s.data("blockUI.isBlocked")))return;s.data("blockUI.isBlocked",t);if(!r.bindEvents||t&&!r.showOverlay)return;var u="mousedown mouseup keydown keypress keyup touchstart touchend touchmove";if(t)e(document).bind(u,r,h);else e(document).unbind(u,h)}function h(t){if(t.keyCode&&t.keyCode==9){if(o&&t.data.constrainTabKey){var n=u;var r=!t.shiftKey&&t.target===n[n.length-1];var i=t.shiftKey&&t.target===n[0];if(r||i){setTimeout(function(){p(i)},10);return false}}}var s=t.data;var a=e(t.target);if(a.hasClass("blockOverlay")&&s.onOverlayClick)s.onOverlayClick();if(a.parents("div."+s.blockMsgClass).length>0)return true;return a.parents().children().filter("div.blockUI").length===0}function p(e){if(!u)return;var t=u[e===true?u.length-1:0];if(t)t.focus()}function d(e,t,n){var r=e.parentNode,i=e.style;var s=(r.offsetWidth-e.offsetWidth)/2-v(r,"borderLeftWidth");var o=(r.offsetHeight-e.offsetHeight)/2-v(r,"borderTopWidth");if(t)i.left=s>0?s+"px":"0";if(n)i.top=o>0?o+"px":"0"}function v(t,n){return parseInt(e.css(t,n),10)||0}e.fn._fadeIn=e.fn.fadeIn;var t=e.noop||function(){};var n=/MSIE/.test(navigator.userAgent);var r=/MSIE 6.0/.test(navigator.userAgent)&&!/MSIE 8.0/.test(navigator.userAgent);var i=document.documentMode||0;var s=e.isFunction(document.createElement("div").style.setExpression);e.blockUI=function(e){a(window,e)};e.unblockUI=function(e){f(window,e)};e.growlUI=function(t,n,r,i){var s=e('<div class="growlUI"></div>');if(t)s.append("<h1>"+t+"</h1>");if(n)s.append("<h2>"+n+"</h2>");if(r===undefined)r=3e3;e.blockUI({message:s,fadeIn:700,fadeOut:1e3,centerY:false,timeout:r,showOverlay:false,onUnblock:i,css:e.blockUI.defaults.growlCSS})};e.fn.block=function(t){var n=e.extend({},e.blockUI.defaults,t||{});this.each(function(){var t=e(this);if(n.ignoreIfBlocked&&t.data("blockUI.isBlocked"))return;t.unblock({fadeOut:0})});return this.each(function(){if(e.css(this,"position")=="static"){this.style.position="relative";e(this).data("blockUI.static",true)}this.style.zoom=1;a(this,t)})};e.fn.unblock=function(e){return this.each(function(){f(this,e)})};e.blockUI.version=2.57;e.blockUI.defaults={message:"<h1>Please wait...</h1>",title:null,draggable:true,theme:false,css:{padding:0,margin:0,width:"30%",top:"40%",left:"35%",textAlign:"center",color:"#000",border:"3px solid #aaa",backgroundColor:"#fff",cursor:"wait"},themedCSS:{width:"30%",top:"40%",left:"35%"},overlayCSS:{backgroundColor:"#000",opacity:.6,cursor:"wait"},cursorReset:"default",growlCSS:{width:"350px",top:"10px",left:"",right:"10px",border:"none",padding:"5px",opacity:.6,cursor:"default",color:"#fff",backgroundColor:"#000","-webkit-border-radius":"10px","-moz-border-radius":"10px","border-radius":"10px"},iframeSrc:/^https/i.test(window.location.href||"")?"javascript:false":"about:blank",forceIframe:false,baseZ:1e3,centerX:true,centerY:true,allowBodyStretch:true,bindEvents:true,constrainTabKey:true,fadeIn:200,fadeOut:400,timeout:0,showOverlay:true,focusInput:true,onBlock:null,onUnblock:null,onOverlayClick:null,quirksmodeOffsetHack:4,blockMsgClass:"blockMsg",ignoreIfBlocked:false};var o=null;var u=[]}if(typeof define==="function"&&define.amd&&define.amd.jQuery){define(["jquery"],e)}else{e(jQuery)}})();

/* Validity */
(function($,undefined){var defaults={outputMode:"tooltip",scrollTo:false,modalErrorsClickable:true,defaultFieldName:"This field",elementSupport:":text, :password, textarea, select, :radio, :checkbox, input[type='hidden'], input[type='tel'], input[type='email']",argToString:function(val){return val.getDate?[val.getMonth()+1,val.getDate(),val.getFullYear()].join("/"):val+""},debugPrivates:false},__private;$.validity={settings:$.extend(defaults,{}),patterns:{integer:/^\d+$/,date:/^((0?\d)|(1[012]))[\/-]([012]?\d|30|31)[\/-]\d{1,4}$/,email:/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,usd:/^\$?((\d{1,3}(,\d{3})*)|\d+)(\.(\d{2})?)?$/,url:/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i,number:/^[+-]?(\d+(\.\d*)?|\.\d+)([Ee]-?\d+)?$/,zip:/^\d{5}(-\d{4})?$/,phone:/^[2-9]\d{2}-\d{3}-\d{4}$/,guid:/^(\{?([0-9a-fA-F]){8}-(([0-9a-fA-F]){4}-){3}([0-9a-fA-F]){12}\}?)$/,time12:/^((0?\d)|(1[012])):[0-5]\d?\s?[aApP]\.?[mM]\.?$/,time24:/^(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})$/,nonHtml:/^[^<>]*$/},messages:{require:"#{field} is required.",match:"#{field} is in an invalid format.",integer:"#{field} must be a positive, whole number.",date:"#{field} must be formatted as a date. (mm/dd/yyyy)",email:"#{field} must be formatted as an email.",usd:"#{field} must be formatted as a US Dollar amount.",url:"#{field} must be formatted as a URL.",number:"#{field} must be formatted as a number.",zip:"#{field} must be formatted as a zipcode ##### or #####-####.",phone:"#{field} must be formatted as a phone number ###-###-####.",guid:"#{field} must be formatted as a guid like {3F2504E0-4F89-11D3-9A0C-0305E82C3301}.",time24:"#{field} must be formatted as a 24 hour time: 23:00.",time12:"#{field} must be formatted as a 12 hour time: 12:00 AM/PM",lessThan:"#{field} must be less than #{max}.",lessThanOrEqualTo:"#{field} must be less than or equal to #{max}.",greaterThan:"#{field} must be greater than #{min}.",greaterThanOrEqualTo:"#{field} must be greater than or equal to #{min}.",range:"#{field} must be between #{min} and #{max}.",tooLong:"#{field} cannot be longer than #{max} characters.",tooShort:"#{field} cannot be shorter than #{min} characters.",nonHtml:"#{field} cannot contain HTML characters.",alphabet:"#{field} contains disallowed characters.",minCharClass:"#{field} cannot have more than #{min} #{charClass} characters.",maxCharClass:"#{field} cannot have less than #{min} #{charClass} characters.",equal:"Values don't match.",distinct:"A value was repeated.",sum:"Values don't add to #{sum}.",sumMax:"The sum of the values must be less than #{max}.",sumMin:"The sum of the values must be greater than #{min}.",radioChecked:"The selected value is not valid.",generic:"Invalid."},out:{start:function(){this.defer("start")},end:function(results){this.defer("end",results)},raise:function($obj,msg){this.defer("raise",$obj,msg)},raiseAggregate:function($obj,msg){this.defer("raiseAggregate",$obj,msg)},defer:function(name){var v=$.validity,o=v.outputs[v.settings.outputMode];o[name].apply(o,Array.prototype.slice.call(arguments,1))}},charClasses:{alphabetical:/\w/g,numeric:/\d/g,alphanumeric:/[A-Za-z0-9]/g,symbol:/[^A-Za-z0-9]/g},outputs:{},__private:undefined,setup:function(options){this.settings=$.extend(this.settings,options);if(this.settings.debugPrivates){this.__private=__private}else{this.__private=undefined}},report:null,isValidating:function(){return !!this.report},start:function(){this.out.start();this.report={errors:0,valid:true}},end:function(){var results=this.report||{errors:0,valid:true};this.report=null;this.out.end(results);return results},clear:function(){this.start();this.end()}};$.fn.extend({validity:function(arg){return this.each(function(){if(this.tagName.toLowerCase()=="form"){var f=null;if(typeof(arg)=="string"){f=function(){$(arg).require()}}else{if($.isFunction(arg)){f=arg}}if(arg){$(this).bind("submit",function(){$.validity.start();f();return $.validity.end().valid})}}})},require:function(msg){return validate(this,function(obj){return !!$(obj).val().length},msg||$.validity.messages.require)},match:function(rule,msg){if(!msg){msg=$.validity.messages.match;if(typeof(rule)==="string"&&$.validity.messages[rule]){msg=$.validity.messages[rule]}}if(typeof(rule)=="string"){rule=$.validity.patterns[rule]}return validate(this,$.isFunction(rule)?function(obj){return !obj.value.length||rule(obj.value)}:function(obj){if(rule.global){rule.lastIndex=0}return !obj.value.length||rule.test(obj.value)},msg)},range:function(min,max,msg){return validate(this,min.getTime&&max.getTime?function(obj){var d=new Date(obj.value);return d>=new Date(min)&&d<=new Date(max)}:min.substring&&max.substring&&Big?function(obj){var n=new Big(obj.value);return(n.greaterThanOrEqualTo(new Big(min))&&n.lessThanOrEqualTo(new Big(max)))}:function(obj){var f=parseFloat(obj.value);return f>=min&&f<=max},msg||format($.validity.messages.range,{min:$.validity.settings.argToString(min),max:$.validity.settings.argToString(max)}))},greaterThan:function(min,msg){return validate(this,min.getTime?function(obj){return new Date(obj.value)>min}:min.substring&&Big?function(obj){return new Big(obj.value).greaterThan(new Big(min))}:function(obj){return parseFloat(obj.value)>min},msg||format($.validity.messages.greaterThan,{min:$.validity.settings.argToString(min)}))},greaterThanOrEqualTo:function(min,msg){return validate(this,min.getTime?function(obj){return new Date(obj.value)>=min}:min.substring&&Big?function(obj){return new Big(obj.value).greaterThanOrEqualTo(new Big(min))}:function(obj){return parseFloat(obj.value)>=min},msg||format($.validity.messages.greaterThanOrEqualTo,{min:$.validity.settings.argToString(min)}))},lessThan:function(max,msg){return validate(this,max.getTime?function(obj){return new Date(obj.value)<max}:max.substring&&Big?function(obj){return new Big(obj.value).lessThan(new Big(max))}:function(obj){return parseFloat(obj.value)<max},msg||format($.validity.messages.lessThan,{max:$.validity.settings.argToString(max)}))},lessThanOrEqualTo:function(max,msg){return validate(this,max.getTime?function(obj){return new Date(obj.value)<=max}:max.substring&&Big?function(obj){return new Big(obj.value).lessThanOrEqualTo(new Big(max))}:function(obj){return parseFloat(obj.value)<=max},msg||format($.validity.messages.lessThanOrEqualTo,{max:$.validity.settings.argToString(max)}))},maxLength:function(max,msg){return validate(this,function(obj){return obj.value.length<=max},msg||format($.validity.messages.tooLong,{max:max}))},minLength:function(min,msg){return validate(this,function(obj){return obj.value.length>=min},msg||format($.validity.messages.tooShort,{min:min}))},alphabet:function(alpha,msg){var chars=[];return validate(this,function(obj){for(var idx=0;idx<obj.value.length;++idx){if(alpha.indexOf(obj.value.charAt(idx))==-1){chars.push(obj.value.charAt(idx));return false}}return true},msg||format($.validity.messages.alphabet,{chars:chars.join(", ")}))},minCharClass:function(charClass,min,msg){if(typeof(charClass)=="string"){charClass=charClass.toLowerCase();if($.validity.charClasses[charClass]){charClass=$.validity.charClasses[charClass]}}return validate(this,function(obj){return(obj.value.match(charClass)||[]).length>=min},msg||format($.validity.messages.minCharClass,{min:min,charClass:charClass}))},maxCharClass:function(charClass,max,msg){if(typeof(charClass)=="string"){charClass=charClass.toLowerCase();if($.validity.charClasses[charClass]){charClass=$.validity.charClasses[charClass]}}return validate(this,function(obj){return(obj.value.match(charClass)||[]).length<=max},msg||format($.validity.messages.maxCharClass,{max:max,charClass:charClass}))},nonHtml:function(msg){return validate(this,function(obj){return $.validity.patterns.nonHtml.test(obj.value)},msg||$.validity.messages.nonHtml)},equal:function(arg0,arg1){var $reduction=(this.reduction||this).filter($.validity.settings.elementSupport),transform=function(val){return val},msg=$.validity.messages.equal;if($reduction.length){if($.isFunction(arg0)){transform=arg0;if(typeof(arg1)=="string"){msg=arg1}}else{if(typeof(arg0)=="string"){msg=arg0}}var map=$.map($reduction,function(obj){return transform(obj.value)}),first=map[0],valid=true;for(var i in map){if(map[i]!=first){valid=false}}if(!valid){raiseAggregateError($reduction,msg);this.reduction=$([])}}return this},distinct:function(arg0,arg1){var $reduction=(this.reduction||this).filter($.validity.settings.elementSupport),transform=function(val){return val},msg=$.validity.messages.distinct,subMap=[],repeatedVal=[],valid=true;if($reduction.length){if($.isFunction(arg0)){transform=arg0;if(typeof(arg1)=="string"){msg=arg1}}else{if(typeof(arg0)=="string"){msg=arg0}}var map=$.map($reduction,function(obj){return transform(obj.value)});for(var i1=0;i1<map.length;++i1){if(map[i1].length){for(var i2=0;i2<subMap.length;++i2){if(subMap[i2]==map[i1]){valid=false;repeatedVal.push(map[i1])}}subMap.push(map[i1])}}if(!valid){repeatedVal=$.unique(repeatedVal);for(var i=0,repeatedLength=repeatedVal.length;i<repeatedLength;++i){raiseAggregateError($reduction.filter("[value='"+repeatedVal[i]+"']"),msg)}this.reduction=$([])}}return this},sum:function(sum,msg){var $reduction=(this.reduction||this).filter($.validity.settings.elementSupport);if($reduction.length&&sum!=numericSum($reduction)){raiseAggregateError($reduction,msg||format($.validity.messages.sum,{sum:sum}));this.reduction=$([])}return this},sumMax:function(max,msg){var $reduction=(this.reduction||this).filter($.validity.settings.elementSupport);if($reduction.length&&max<numericSum($reduction)){raiseAggregateError($reduction,msg||format($.validity.messages.sumMax,{max:max}));this.reduction=$([])}return this},sumMin:function(min,msg){var $reduction=(this.reduction||this).filter($.validity.settings.elementSupport);if($reduction.length&&min>numericSum($reduction)){raiseAggregateError($reduction,msg||format($.validity.messages.sumMin,{min:min}));this.reduction=$([])}return this},radioChecked:function(val,msg){var $reduction=(this.reduction||this).filter($.validity.settings.elementSupport);if($reduction.is(":radio")&&$reduction.find(":checked").val()!=val){raiseAggregateError($reduction,msg||$.validity.messages.radioChecked)}},radioNotChecked:function(val,msg){var $reduction=(this.reduction||this).filter($.validity.settings.elementSupport);if($reduction.is(":radio")&&$reduction.filter(":checked").val()==val){raiseAggregateError($reduction,msg||$.validity.messages.radioChecked)}},checkboxChecked:function(msg){var $reduction=(this.reduction||this).filter($.validity.settings.elementSupport);if($reduction.is(":checkbox")&&!$reduction.is(":checked")){raiseAggregateError($reduction,msg||$.validity.messages.radioChecked)}},assert:function(expression,msg){var $reduction=this.reduction||this;if($reduction.length){if($.isFunction(expression)){return validate(this,expression,msg||$.validity.messages.generic)}else{if(!expression){raiseAggregateError($reduction,msg||$.validity.messages.generic);this.reduction=$([])}}}return this},fail:function(msg){return this.assert(false,msg)}});function validate($obj,regimen,message){var $reduction=($obj.reduction||$obj).filter($.validity.settings.elementSupport),elements=[];$reduction.each(function(){if(regimen(this)){elements.push(this)}else{raiseError(this,format(message,{field:infer(this)}))}});$obj.reduction=$(elements);return $obj}function addToReport(){if($.validity.isValidating()){$.validity.report.errors++;$.validity.report.valid=false}}function raiseError(obj,msg){addToReport();$.validity.out.raise($(obj),msg)}function raiseAggregateError($obj,msg){addToReport();$.validity.out.raiseAggregate($obj,msg)}function numericSum(obj){var accumulator=0;obj.each(function(){var n=parseFloat(this.value);accumulator+=isNaN(n)?0:n});return accumulator}function format(str,obj){for(var p in obj){if(obj.hasOwnProperty(p)){str=str.replace(new RegExp("#\\{"+p+"\\}","g"),obj[p])}}return capitalize(str)}function infer(field){var $f=$(field),id=$f.prop("id"),ret=$.validity.settings.defaultFieldName;if($f.prop("title").length){ret=$f.prop("title")}else{if(/^([A-Z0-9][a-z]*)+$/.test(id)){ret=id.replace(/([A-Z0-9])[a-z]*/g," $&")}else{if(/^[a-z0-9]+(_[a-z0-9]+)*$/.test(id)){var arr=id.split("_");for(var i=0;i<arr.length;++i){arr[i]=capitalize(arr[i])}ret=arr.join(" ")}}}return $.trim(ret)}function capitalize(sz){return sz.substring?sz.substring(0,1).toUpperCase()+sz.substring(1,sz.length):sz}__private={validate:validate,addToReport:addToReport,raiseError:raiseError,raiseAggregateError:raiseAggregateError,numericSum:numericSum,format:format,infer:infer,capitalize:capitalize}})(jQuery);(function($){$.validity.outputs.tooltip={tooltipClass:"validity-tooltip",start:function(){$("."+$.validity.outputs.tooltip.tooltipClass).remove()},end:function(results){if(!results.valid&&$.validity.settings.scrollTo){$(document).scrollTop($("."+$.validity.outputs.tooltip.tooltipClass).offset().top)}},raise:function($obj,msg){var pos=$obj.offset();pos.left+=$obj.width()+18;pos.top+=8;$('<div class="validity-tooltip">'+msg+'<div class="validity-tooltip-outer"><div class="validity-tooltip-inner"></div></div></div>').click(function(){$obj.focus();$(this).fadeOut()}).css(pos).hide().appendTo("body").fadeIn()},raiseAggregate:function($obj,msg){if($obj.length){this.raise($obj.filter(":last"),msg)}}}})(jQuery);(function($){function getIdentifier($obj){return $obj.attr("id").length?$obj.attr("id"):$obj.attr("name")}$.validity.outputs.label={cssClass:"error",start:function(){$("."+$.validity.outputs.label.cssClass).remove()},end:function(results){if(!results.valid&&$.validity.settings.scrollTo){location.hash=$("."+$.validity.outputs.label.cssClass+":eq(0)").attr("for")}},raise:function($obj,msg){var labelSelector="."+$.validity.outputs.label.cssClass+"[for='"+getIdentifier($obj)+"']";if($(labelSelector).length){$(labelSelector).text(msg)}else{$("<label/>").attr("for",getIdentifier($obj)).addClass($.validity.outputs.label.cssClass).text(msg).click(function(){if($obj.length){$obj[0].select()}}).insertAfter($obj)}},raiseAggregate:function($obj,msg){if($obj.length){this.raise($($obj.get($obj.length-1)),msg)}}}})(jQuery);(function($){var errorClass="validity-modal-msg",container="body";$.validity.outputs.modal={start:function(){$("."+errorClass).remove()},end:function(results){if(!results.valid&&$.validity.settings.scrollTo){location.hash=$("."+errorClass+":eq(0)").attr("id")}},raise:function($obj,msg){if($obj.length){var off=$obj.offset(),obj=$obj.get(0),errorStyle={left:parseInt(off.left+$obj.width()+4,10)+"px",top:parseInt(off.top-10,10)+"px"};$("<div/>").addClass(errorClass).css(errorStyle).text(msg).click($.validity.settings.modalErrorsClickable?function(){$(this).remove()}:null).appendTo(container)}},raiseAggregate:function($obj,msg){if($obj.length){this.raise($($obj.get($obj.length-1)),msg)}}}})(jQuery);(function($){var container=".validity-summary-container",erroneous="validity-erroneous",errors="."+erroneous,wrapper="<li/>",buffer=[];$.validity.outputs.summary={start:function(){$(errors).removeClass(erroneous);buffer=[]},end:function(results){$(container).hide().find("ul").html("");if(buffer.length){for(var i=0;i<buffer.length;++i){$(wrapper).text(buffer[i]).appendTo(container+" ul")}$(container).show();if($.validity.settings.scrollTo){location.hash=$(errors+":eq(0)").attr("id")}}},raise:function($obj,msg){buffer.push(msg);$obj.addClass(erroneous)},raiseAggregate:function($obj,msg){this.raise($obj,msg)},container:function(){document.write('<div class="validity-summary-container">The form didn\'t submit for the following reason(s):<ul></ul></div>')}}})(jQuery);

/* CC Validation (jquerycreditcardvalidator.com) */
(function(){var $,__indexOf=[].indexOf||function(item){for(var i=0,l=this.length;i<l;i++){if(i in this&&this[i]===item){return i}}return -1};$=jQuery;$.fn.validateCreditCard=function(callback,options){var card,card_type,card_types,get_card_type,is_valid_length,is_valid_luhn,normalize,validate,validate_number,_i,_len,_ref,_ref1;card_types=[{name:"amex",pattern:/^3[47]/,valid_length:[15]},{name:"diners_club_carte_blanche",pattern:/^30[0-5]/,valid_length:[14]},{name:"diners_club_international",pattern:/^36/,valid_length:[14]},{name:"jcb",pattern:/^35(2[89]|[3-8][0-9])/,valid_length:[16]},{name:"laser",pattern:/^(6304|670[69]|6771)/,valid_length:[16,17,18,19]},{name:"visa_electron",pattern:/^(4026|417500|4508|4844|491(3|7))/,valid_length:[16]},{name:"visa",pattern:/^4/,valid_length:[16]},{name:"mastercard",pattern:/^5[1-5]/,valid_length:[16]},{name:"maestro",pattern:/^(5018|5020|5038|6304|6759|676[1-3])/,valid_length:[12,13,14,15,16,17,18,19]},{name:"discover",pattern:/^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)/,valid_length:[16]}];if(options==null){options={}}if((_ref=options.accept)==null){options.accept=(function(){var _i,_len,_results;_results=[];for(_i=0,_len=card_types.length;_i<_len;_i++){card=card_types[_i];_results.push(card.name)}return _results})()}_ref1=options.accept;for(_i=0,_len=_ref1.length;_i<_len;_i++){card_type=_ref1[_i];if(__indexOf.call((function(){var _j,_len1,_results;_results=[];for(_j=0,_len1=card_types.length;_j<_len1;_j++){card=card_types[_j];_results.push(card.name)}return _results})(),card_type)<0){throw"Credit card type '"+card_type+"' is not supported"}}get_card_type=function(number){var _j,_len1,_ref2;_ref2=(function(){var _k,_len1,_ref2,_results;_results=[];for(_k=0,_len1=card_types.length;_k<_len1;_k++){card=card_types[_k];if(_ref2=card.name,__indexOf.call(options.accept,_ref2)>=0){_results.push(card)}}return _results})();for(_j=0,_len1=_ref2.length;_j<_len1;_j++){card_type=_ref2[_j];if(number.match(card_type.pattern)){return card_type}}return null};is_valid_luhn=function(number){var digit,n,sum,_j,_len1,_ref2;sum=0;_ref2=number.split("").reverse();for(n=_j=0,_len1=_ref2.length;_j<_len1;n=++_j){digit=_ref2[n];digit=+digit;if(n%2){digit*=2;if(digit<10){sum+=digit}else{sum+=digit-9}}else{sum+=digit}}return sum%10===0};is_valid_length=function(number,card_type){var _ref2;return _ref2=number.length,__indexOf.call(card_type.valid_length,_ref2)>=0};validate_number=function(number){var length_valid,luhn_valid;card_type=get_card_type(number);luhn_valid=false;length_valid=false;if(card_type!=null){luhn_valid=is_valid_luhn(number);length_valid=is_valid_length(number,card_type)}return callback({card_type:card_type,luhn_valid:luhn_valid,length_valid:length_valid})};validate=function(){var number;number=normalize($(this).val());return validate_number(number)};normalize=function(number){return number.replace(/[ -]/g,"")};this.bind("input",function(){$(this).unbind("keyup");return validate.call(this)});this.bind("keyup",function(){return validate.call(this)});if(this.length!==0){validate.call(this)}return this}}).call(this);


/* Bookt API */
var BAPI = BAPI || {};
BAPI.UI = BAPI.UI || {};

(function(context) {

context.maps = {};
//context.newdatepicker = false;
context.newdatepicker = true;

/*
	Group: Initialization
*/
context.jsroot = '/';
context.init = function(options) {
	BAPI.log("BAPI.UI initializing.");
	if (typeof(options)==="undefined" || options===null) { options = {} };
	context.inithelpers.getParamToSession();
	context.inithelpers.applyentityadvisor(options);
	context.inithelpers.setupsummarywidgets(options);
	context.inithelpers.setupsearchformwidgets(options);
	context.inithelpers.setupbookingform(options);
	context.inithelpers.setupmakepaymentform(options);
	context.inithelpers.setupinquiryformwidgets(options);
	context.inithelpers.setuppopupinquiryformwidgets(options);
	context.inithelpers.setupavailcalendarwidgets(options);
	context.inithelpers.applymovemes(options);
	context.inithelpers.setuprateblockwidgets(options);
	context.inithelpers.applyflexsliders(options);
	context.inithelpers.applytruncate(options);
	context.inithelpers.applydotdotdot(options);
	context.inithelpers.setupmapwidgets(options);
	context.inithelpers.setupprintlisteners(options);
	context.inithelpers.setupbapitracker(options);
	context.inithelpers.loadRatingStars(options);

	// ensure that searchmodes exists
	if (BAPI.isempty(BAPI.config().searchmodes)) { BAPI.config().searchmodes = {} };
	if (BAPI.isempty(BAPI.config().searchmodes.listview)) { BAPI.config().searchmodes.listview=true; }
	if (BAPI.isempty(BAPI.config().searchmodes.photoview)) { BAPI.config().searchmodes.photoview=true; }
	if (BAPI.isempty(BAPI.config().searchmodes.widephotoview)) { BAPI.config().searchmodes.widephotoview=false; }
	if (BAPI.isempty(BAPI.config().searchmodes.mapview)) { BAPI.config().searchmodes.mapview=false; }
	if (BAPI.isempty(BAPI.config().searchmodes.hotelview)) { BAPI.config().searchmodes.hotelview=false; }
}

context.inithelpers = {
	setupsummarywidgets: function(options) {
		$("#results").addClass('loading');
		$.each($('.bapi-summary'), function (i, item) {
			var ctl = $(item);
			var dologging = (ctl.attr('data-log') == '1');
			var searchoptions = null;

			try {
				searchoptions = $.parseJSON(ctl.attr('data-searchoptions'));
				if (searchoptions.similarto) { searchoptions.similarto = BAPI.curentity.ID; }
			} catch(err) {}

			if(searchoptions == null && typeof(BAPI.config().pagesize) !== 'undefined'){
				searchoptions = {};
				searchoptions.pagesize = BAPI.config().pagesize;
			}

			var selector = '#' + ctl.attr('id');
			var templateName = ctl.attr('data-templatename');
			if(ctl.attr('data-defaultsearchresultview') == '1' && BAPI.config().defaultsearchresultview !== null && typeof(BAPI.config().defaultsearchresultview) !== 'undefined'){
				templateName = BAPI.config().defaultsearchresultview;
			}


			if(null === searchoptions) {
				searchoptions = {};
			}


			//Add query params
			/*
			var vars = [], hash;
			var url = decodeURIComponent(document.URL);
			url = url.replace('+', ' ');

			var q = url.split('?')[1];

			if(q != undefined){
				q = q.split('&');
			    for(var i = 0; i < q.length; i++){
			    	hash = q[i].split('=');
			        vars[hash[0]] = hash[1];
			        if(hash[1]) {
				        searchoptions[hash[0]] = hash[1];
				    }
			    }
			}
			*/

			// console.log('---');
			// console.log(JSON.parse(JSON.stringify(searchoptions)));


			var cookies = {};
			var cookieData = Cookies.getJSON('searchdata');

			// console.log('raw cookies');
			// console.log(JSON.parse(JSON.stringify(cookieData)));

			if(cookieData) {
				$.each(cookieData, function(i,v) {
					// assign to the req and the session
					var c = v.name.indexOf('['); 

					if (c == -1) {
						searchoptions[v.name] = v.value;
					} else {
						// special case when the data-attribute value has nested brackets (such as adults[min])
						var k1 = v.name.substring(0,c);
						var k2 = v.name.substring(c+1,v.name.length-1);
						
						searchoptions[k1] = {};
						searchoptions[k1][k2] = v.value;
					}
					
				});

			}

			if(searchoptions.los) { 
				searchoptions.los = parseInt(searchoptions.los);

				if(searchoptions.checkin) {
					searchoptions.checkout = moment(searchoptions.checkin, BAPI.defaultOptions.dateFormatBAPI.toUpperCase()).add(searchoptions.los, 'days').format(BAPI.defaultOptions.dateFormatBAPI.toUpperCase());
				}
			}


			if(!searchoptions.adults) { searchoptions.adults = {}; }
			if(!searchoptions.children) { searchoptions.children = {}; }
			if(!searchoptions.adults.min) { searchoptions.adults.min = 2; }
			if(!searchoptions.children.min) { searchoptions.children.min = 0; }
                        
                    //default settings
                        if(typeof(searchoptions.sort) == 'undefined' && typeof(BAPI.config().sort) != 'undefined') { searchoptions.sort = BAPI.config().sort; }
                        if(typeof(searchoptions.sortasc) == 'undefined' && typeof(BAPI.config().sortdesc) != 'undefined') { searchoptions.sortasc = BAPI.config().sortdesc; }
                        

			//searchoptions = $.extend(searchoptions, cookies);


			//Filter searchoptions?
			if(ctl.data('searchparams')) {
				var params = ctl.data('searchparams'),
					options = ctl.data('searchoptions');

					console.log(params);

				$.each(options, function(i,v) {
					params.push(i);
				});

				console.log(params);

				Object.keys(searchoptions).filter(function(value) {
					return params.indexOf(value) == -1;
				}).forEach(function(v) {
					delete searchoptions[v];
				});
			}


			console.log('---filtered');
			console.log(JSON.stringify(searchoptions));


			var options = {
				"entity": ctl.attr('data-entity'),
				"template": BAPI.templates.get(templateName),
				"log": dologging,
				"applyfixers": parseInt(ctl.attr('data-applyfixers')),
				"usemylist": parseInt(ctl.attr('data-usemylist')),
				"ignoresession": parseInt(ctl.attr('data-ignoresession')),
				"ignoreconfigsort":parseInt(ctl.attr('data-ignoreconfigsort')),
				"searchoptions": searchoptions,
			};

			context.createSummaryWidget(selector, options);

		});
		$("#results").removeClass('loading');
	},
	setupsearchformwidgets: function(options) {
		$.each($('.bapi-search'), function (i, item) {
			var ctl = $(item);
			var dologging = (ctl.attr('data-log') == '1');
			var templatename = ctl.attr('data-templatename');
			var searchurl = ctl.attr('data-searchurl');
			var selector = '#' + ctl.attr('id');
			BAPI.log("Creating search widget for " + selector, 3);
			context.createSearchWidget(selector, { "searchurl": searchurl, "template": BAPI.templates.get(templatename), "log": dologging });
		});
	},
	loadRatingStars: function(options){
		function roundHalf(num) {
    		num = Math.round(num*2)/2;
    		return num;
			}
		 $('.starsreviews div').each(function(i,item) {
    		var stars = $(item).attr('ID');
				stars = stars.split('-');
				stars = stars[1];
				stars = roundHalf(stars);
				/* we rounded the avg review to 2 decimals (x,xx) */
				var floatstars = parseFloat( stars ).toFixed( 2 );
				var review = floatstars;

        		// value is in 0 - 5 range, multiply to get width of image
        		var widthsize = Math.max(0, (Math.min(5, review))) * 16;
        		var $span = $('<span />').width(widthsize);
				$('span.stars',this).html($span);
				$('i.starsvalue',this).html('('+review+')');
		});
	},
	setuprateblockwidgets: function(options) {
		$.each($('.bapi-rateblock'), function (i, item) {
			var ctl = $(item);
			var dologging = (ctl.attr('data-log') == '1');
			var templatename = ctl.attr('data-templatename');
			BAPI.log("Creating rate block widget for ", ctl, 3);
			context.createRateBlockWidget(ctl, { "template": BAPI.templates.get(templatename), "log": dologging });
		});
	},
	setupinquiryformwidgets: function(options) {
		$.each($('.bapi-inquiryform'), function (i, item) {
			var ctl = $(item);
			var dologging = (ctl.attr('data-log') == '1');
			var templatename = ctl.attr('data-templatename');
			var pkid = ctl.attr('data-propid');
			var selector = '#' + ctl.attr('id');
			var hasdates = false;

			/* we get each value from the html markup created by the inquiry form widget at the same time we create an object and populate it with each value */
			var InquiryFormFields = {};
			 if ( typeof (ctl.attr('data-showphonefield')) === "undefined" || ctl.attr('data-showphonefield') == ''){
				InquiryFormFields["Phone"] = true;
			 }else{
				InquiryFormFields["Phone"] = (ctl.attr('data-showphonefield') == '1');
			 }
			 if ( typeof (ctl.attr('data-phonefieldrequired')) === "undefined" || ctl.attr('data-phonefieldrequired') == '') {
				InquiryFormFields["PhoneRequired"] = true;
			 }else{
				InquiryFormFields["PhoneRequired"] = (ctl.attr('data-phonefieldrequired') == '1');
			 }
			 if ( typeof (ctl.attr('data-showdatefields')) === "undefined" || ctl.attr('data-showdatefields') == '') {
			 	if($('.contact-form').length > 0)
				{
					InquiryFormFields["Dates"] = false;
				}else{
					InquiryFormFields["Dates"] = true;
				}

			 }else{
				 InquiryFormFields["Dates"] = (ctl.attr('data-showdatefields') == '1');
			 }
			 if ( typeof (ctl.attr('data-shownumberguestsfields')) === "undefined" || ctl.attr('data-shownumberguestsfields') == '' ) {
			 	if($('.contact-form').length > 0)
				{
					InquiryFormFields["NumberOfGuests"] = false;
				}else{
					InquiryFormFields["NumberOfGuests"] = true;
				}

			 }else{
				 InquiryFormFields["NumberOfGuests"] = (ctl.attr('data-shownumberguestsfields') == '1');
			 }
			 if ( typeof (ctl.attr('data-showleadsourcedropdown')) === "undefined" || ctl.attr('data-showleadsourcedropdown') == '' ) {
				InquiryFormFields["LeadSourceDropdown"] = true;
			 }else{
				InquiryFormFields["LeadSourceDropdown"] = (ctl.attr('data-showleadsourcedropdown') == '1');
			 }
			 if ( typeof (ctl.attr('data-leadsourcedropdownrequired')) === "undefined" || ctl.attr('data-leadsourcedropdownrequired') == '' ){
				InquiryFormFields["LeadSourceRequired"] = false;
			 }else{
				InquiryFormFields["LeadSourceRequired"] = (ctl.attr('data-leadsourcedropdownrequired') == '1');
			 }
			 if ( typeof (ctl.attr('data-showcommentsfield')) === "undefined" || ctl.attr('data-showcommentsfield') == '' ) {
				InquiryFormFields["Comments"] = true;
			 }else{
				InquiryFormFields["Comments"] = (ctl.attr('data-showcommentsfield') == '1');
			 }
			BAPI.log("Creating inquiry form for " + selector);
			context.createInquiryForm(selector, { "pkid": pkid, "template": BAPI.templates.get(templatename), "hasdatesoninquiryform": hasdates, "log": dologging, "InquiryFormFields": InquiryFormFields });
		});
	},
	setuppopupinquiryformwidgets: function(options) {
		$('.bapi-inquirypopup').live("click", function() {
			BAPI.log(this);
			var ctl = $(this);
			var pkid = ctl.attr("data-pkid");
			if (pkid!==null && pkid!='') {
				BAPI.get(pkid, BAPI.entities.property, { "avail": 1, "rates": 1 }, function(data) {
					var selector = '#' + ctl.attr('id');
					var options = {};
					try { options = $.parseJSON(ctl.attr('data-options')); } catch(err) {}
					BAPI.log("Creating availability calendar for " + selector, 3);
					context.createAvailabilityWidget(selector, data, options);
				});
			}
		});
	},
	setupavailcalendarwidgets: function(options) {
		$.each($('.bapi-availcalendar'), function (i, item){ 
			var ctl = $(item),
				pkid = ctl.attr("data-pkid"),
				selector = '#' + ctl.attr('id'),
				availability = ctl.data('availability'),
				options = ctl.data('options');

			if( typeof(availability) != 'undefined' ) {
				context.createAvailabilityWidget(selector, availability, options);
			} else {
				if (pkid!==null && pkid!='') {
					BAPI.get(pkid, BAPI.entities.property, { "avail2": true, "rates": 1 }, function(data) {
						try { options = $.parseJSON(options); } catch(err) { }
						BAPI.log("Creating availability calendar for " + selector, 3);
						context.createAvailabilityWidget(selector, data, options);
					});
				}
			}

			//Listen for event, will be passed raw availability data
			$(window).on('update-calendars', function(event, data) {
				context.createAvailabilityWidget(selector, data, options);
			});
			
		});
		//rate table
		$.each($('.bapi-ratetable'), function (i, item){
			var ctl = $(item);
			var pkid = ctl.attr("data-pkid");
			if(typeof(pkid) === 'undefined'){
				if($(".bapi-entityadvisor").length > 0){
					pkid = $(".bapi-entityadvisor").attr("data-pkid");
				}
			}
			if (typeof(pkid) !== 'undefined' && pkid !== null && pkid != '') {
				BAPI.get(pkid, BAPI.entities.property, { "rates": 1 }, function(data) {
					var selector = '#' + ctl.attr('id');
					var options = {};
					try { options = $.parseJSON(ctl.attr('data-options')); } catch(err) { }
					var rateselector = '.bapi-ratetable'; // TODO: set to what is passed in the options
					var ratetemplate = BAPI.templates.get('tmpl-properties-ratetable');
					/* When the start date and the end date are equals the end date will change to a "-" */
					var datesValues = data.result[0].ContextData.Rates.Values;
					 for( var i = 0; i < datesValues.length; i++ ){
						if(datesValues[i][0] == datesValues[i][1]){
							datesValues[i][1] = '-';
						}
					 }
					 var nData = data;
					$(rateselector).html(context.mustacheHelpers.render(ratetemplate, nData));
				});
			}
		});
	},
	setupmakepaymentform: function (options) {
	    var ctl = $('.bapi-makepaymentform');
	    if (typeof (ctl) === "undefined" || ctl === null || ctl.length == 0) { return; }
	    /* verify this payment logic */
	    var verifyUserTemplate = BAPI.templates.get('tmpl-booking-makepayment-verify-user');
		//we check if this template exist for legacy purposes
		var templateMissing = BAPI.isempty(verifyUserTemplate);
		if(templateMissing){
			//we check if this template exist for legacy purposes
			var newpaymentpage = BAPI.isempty(BAPI.templates.get('tmpl-booking-makepayment-renter'));
			var newCreditCardTemplate = '';
			if(newpaymentpage){newCreditCardTemplate = BAPI.templates.get('tmpl-booking-makepayment-creditcard')}
			var options = {
				"mastertemplate": BAPI.templates.get('tmpl-booking-makepayment-masterlayout'),
				"targetids": {
					"stayinfo": "#stayinfo",
					"statement": "#statement",
					"renter": "#renter",
					"creditcard": "#creditcard",
					"accept": "#accept"
				},
				"templates": {
					"stayinfo": BAPI.templates.get('tmpl-booking-makepayment-stayinfo'),
					"statement": BAPI.templates.get('tmpl-booking-makepayment-statement'),
					"renter": BAPI.templates.get('tmpl-booking-makepayment-renter'),
					"creditcard": BAPI.templates.get('tmpl-booking-makebooking-creditcard'),
					"creditcard2": newCreditCardTemplate,
					"accept": BAPI.templates.get('tmpl-booking-makepayment-pay')
				},
				"newpaymentpage":newpaymentpage
			};
			$.getScript(context.jsroot + "bapi/bapi.ui.cchelper.js", function (data, ts, jqxhr) {
				BAPI.UI.createMakePaymentWidget('#paymentform', options);
			});
		}else{
			var verifyForm = $('#paymentform');
			var retrieveBid = false;
			var data = {};
			data.textdata = BAPI.textdata;
			/* render the form */
			verifyForm.html(context.mustacheHelpers.render(verifyUserTemplate, data));
			/* check if there is booking ID */
			var u = $.url(window.location.href);
			var bid = u.param('bid');
			if (BAPI.isempty(bid) || bid == ''){
				bid = u.param('ebid');
			}
			if (BAPI.isempty(bid) || bid == ''){
			    $('#verifybookingid-block').removeClass('hide');
			    $('#verifyBookingId').val(u.param("keyid"));
				retrieveBid = true;
			}else{
				$('#verifybookingid-block').remove();
			}
			var processing = false;
			$('#submitVerifyUser').live('click',function(){
			if (processing) { return; } // already in here
			verifyForm.block({ message: "<img src='" + loadingImgUrl + "' />" });
			// get the list of required fields and validate them
			var reqfields = $.extend([], $('.required'));
			/* lets check the required fields */
			var validData = BookingHelper_ValidateForm(reqfields);
			/* if its not valid data do nothing */
			if(!validData){ verifyForm.unblock(); return; }
			/* data is valid we are processing now */
			processing = true; // make sure we do not re-enter
			/* we have the input lets get the booking */
			var securityInfo = {};
			/* we get the info to send it*/
			var renterEmail = $("#verifyEmail").val();
			securityInfo.LastName = $("#verifyLastName").val();
			securityInfo.renterEmail = renterEmail;
			/* we need to get the Booking id if it was not supplied*/
			if(retrieveBid){ bid = u.param("keyid");	}
			if(
				BAPI.isempty( bid ) &&
				'string' !== $.type( bid = $( '#verifyBookingId').val() )
			) {
				bid = '';
			}

			function render_verification_error( msg ) {
				alert( msg );
				verifyForm.unblock();
				processing = false;
			}
			//Add query param for payment processor
			securityInfo.resolveprocessor = 1;

			BAPI.get(bid, BAPI.entities.booking, securityInfo, function (data) {


				// Unexpected error
				if( !$.isPlainObject( data ) ) {
					render_verification_error( BAPI.textdata['An unexpected error occurred'] + '. ' + BAPI.textdata['Please try again later'] + '.' );// we should log this in Loggly
					return;
				}

				if( !$.isArray( data.result ) ) {
					// Unexpected error
					if(
						!$.isPlainObject( data.error ) ||
						'string' !== $.type( data.error.message )
					){
						render_verification_error( BAPI.textdata['An unexpected error occurred'] + '. ' + BAPI.textdata['Please try again later'] + '.' );// we should log this in Loggly
						return;
					}

					/* wrong email */
					render_verification_error( data.error.message );
					$("#verifyEmail").focus();
					return;
				}

				/* the bid was supplied we need to know if there are results */
				if( !data.result.length ){
					render_verification_error( BAPI.textdata['Booking not found'] );
					return;
				}

				//Detect canceled bookings
				if( 'x' === data.result[0].Status.toLowerCase() ) {
					render_verification_error( BAPI.textdata['This booking has been canceled'] );
					return;
				}
				else{
					//we check if this template exist for legacy purposes
					var newpaymentpage = BAPI.isempty(BAPI.templates.get('tmpl-booking-makepayment-renter'));
					var newCreditCardTemplate = '';
					if(newpaymentpage){newCreditCardTemplate = BAPI.templates.get('tmpl-booking-makepayment-creditcard')}
					var options = {
						"mastertemplate": BAPI.templates.get('tmpl-booking-makepayment-masterlayout'),
						"targetids": {
							"stayinfo": "#stayinfo",
							"statement": "#statement",
							"renter": "#renter",
							"creditcard": "#creditcard",
							"accept": "#accept"
						},
						"templates": {
							"stayinfo": BAPI.templates.get('tmpl-booking-makepayment-stayinfo'),
							"statement": BAPI.templates.get('tmpl-booking-makepayment-statement'),
							"renter": BAPI.templates.get('tmpl-booking-makepayment-renter'),
							"creditcard": BAPI.templates.get('tmpl-booking-makebooking-creditcard'),
							"creditcard2": newCreditCardTemplate,
							"accept": BAPI.templates.get('tmpl-booking-makepayment-pay')
						},
						"newpaymentpage":newpaymentpage,
						"renterEmail":renterEmail,
						"bookingId": bid
					};
					$.getScript(context.jsroot + "bapi/bapi.ui.cchelper.js", function (data, ts, jqxhr) {
						BAPI.UI.createMakePaymentWidget('#paymentform', options);
					});
				}
				verifyForm.unblock();
				processing = false;
			});
		});
		}

	},
	setupbookingform: function (options) {
		console.log('setup booking form');
		BAPI.session.searchparams = BAPI.session.searchparams || {};
		console.log(JSON.stringify(BAPI.session.searchparams));
		var ctl = $('.bapi-bookingform');
		if (typeof(ctl)==="undefined" || ctl===null || ctl.length==0) { return; }

		var options = {
			"mastertemplate": BAPI.templates.get('tmpl-booking-makebooking-masterlayout'),
			"targetids": {
				"stayinfo": "#stayinfo",
				"statement": "#statement",
				"renter": "#renter",
				"creditcard": "#creditcard",
				"accept": "#accept"
			},
			"templates": {
				"stayinfo": BAPI.templates.get('tmpl-booking-makebooking-stayinfo'),
				"statement": BAPI.templates.get('tmpl-booking-makebooking-statement'),
				"renter": BAPI.templates.get('tmpl-booking-makebooking-renter'),
				"creditcard": BAPI.templates.get('tmpl-booking-makebooking-creditcard'),
				"accept": BAPI.templates.get('tmpl-booking-makebooking-accept')
			}
		};
		$.getScript(context.jsroot + "bapi/bapi.ui.cchelper.js", function(data, ts, jqxhr) {
			BAPI.UI.createMakeBookingWidget('#bookingform', options);
		});
	},
	applyflexsliders: function(options) { $(window).trigger('applyflexsliders');
		/*
		$('.bapi-flexslider img').unveil();
		$('.bapi-flexslider').each(function (i) {
			//check if the fullScreen Carousel is present so we attach the click event to the flexslider viewport
			if($('#fullScreenCarousel').length > 0){
				$("#fullScreenCarousel").carousel({"interval": 10800000});
				$("#fullScreenCarousel").carousel('next');
				$( "#slider .slides" ).on( "click", function() {
					if(typeof($("#slider").data('flexslider')) !== 'undefined'){
						$("#fullScreenCarousel").carousel($("#slider").data('flexslider').currentSlide);
					}
					//lets not do this each time the image is clicked
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
			try { options = $.parseJSON(ctl.attr('data-options')); } catch(err) {}
			options.start = function() {
				//Positioning function?
				if(typeof positionQuickSearch != 'function') {
					$('.home-qsearch').addClass('qsFixed');
				}
			}
			options.animationLoop = true;
			var selector = '#' + ctl.attr('id');
			BAPI.log("Applying flexslider to " + selector, 3);
			if (selector === null) { BAPI.log("--> Error, options for flexslider could not be parsed correctly.  Check JSON format."); }
			else { ctl.flexslider(options); }
		});
		*/
	},
	applytruncate: function(options) {
		$.each($('.bapi-truncate'), function (i, item) {
			var ctl = $(item);
			var selector = '#' + ctl.attr('id');
			var len = parseInt(ctl.attr('data-trunclen'));
			//BAPI.log("Applying jTruncate to " + selector + ", len=" + len, 3);
			ctl.jTruncate({ length: len, moreText: BAPI.textdata.more, lessText: BAPI.textdata.less });
		});
	},
	applydotdotdot: function(options) {
		$.each($('.bapi-dotdotdot'), function (i, item) {
			var ctl = $(item);
			var selector = '#' + ctl.attr('id');
			//BAPI.log("Applying dot dot dot to " + selector);
			/* we need a default value */
			var dotdotdotHeight = 100;
			/* check if another value was specified */
			if(typeof (ctl.attr('data-dotdotdotheight')) !== "undefined" || ctl.attr('data-dotdotdotheight') != ''){
				dotdotdotHeight = ctl.attr('data-dotdotdotheight');
			}
			/* lets add the height before dotdotdot so the content gets truncated */
			if(ctl.attr('data-readmorelink')){
				ctl.css('height', dotdotdotHeight);
			}
			ctl.dotdotdot();
			/* we need to append the read more link */
			if(ctl.attr('data-readmorelink')){
				ctl.append('<div class="clearfix"><a href="javascript:void(0)" class="read-more">'+BAPI.textdata.more+'</a></div>');
				ctl.css('height','auto');
				$(".read-more").click(function (e) {
					toggleRead(this);
				});
			}


		});
	},
	setupmapwidgets: function(options) {
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
	},
	applymovemes: function(options) {
		$.each($('.bapi-moveme'), function (i, item) {
			var ctl = $(item);
			var fromsel = ctl.attr('data-from');
			var tosel = ctl.attr('data-to');
			var method = ctl.attr('data-method');
			if (method===null || method=='') { method = 'prepend' }
			BAPI.log("Moving DOM object from " + fromsel + " to " + tosel + ", method=" + method, 3);
			if (method==="prepend") { $(fromsel).prepend($(tosel)); }
			else { $(fromsel).appendTo($(tosel)); }
		});
	},
	applyentityadvisor: function(options) {
		$.each($('.bapi-entityadvisor'), function (i, item) {
			var ctl = $(item);
			var pkid = ctl.attr('data-pkid');
			var entity = ctl.attr('data-entity');
			BAPI.log("Setting entity advisor to entity=" + entity + ", pkid=" + pkid, 2);
			BAPI.curentity = { "ID": pkid, "entity": entity };
		});
	},
	setupprintlisteners: function(options) {
		$('.bapi-print').live("click", function() {
			window.print(); return;
		});
	},
	setupbapitracker: function(options) {
	    $(document).on("click", '.bapi-wishlisttracker',function () {
			var ctl = $(this);
			var pkid = ctl.attr("data-pkid");
			var myList = Cookies.getJSON('mylist') || [];
			if (ctl.hasClass('active')) {
				BAPI.log("adding pkid=" + pkid, 2);
				BAPI.mylisttracker.add(pkid, BAPI.entities.property);
				myList.push(pkid);
				Cookies.set('mylist', myList, {path:'/'});
				ctl.html('<span class="halflings heart-empty"><i></i>' + BAPI.textdata['WishListed'] + '</span>');
			} else {
				BAPI.log("removing pkid=" + pkid, 2);
				BAPI.mylisttracker.del(pkid, BAPI.entities.property);
				myList.splice(myList.indexOf(pkid), 1);
				Cookies.set('mylist', myList, {path:'/'});
				ctl.html('<span class="halflings heart-empty"><i></i>' + BAPI.textdata['WishList'] + '</span>');
			}
			BAPI.savesession();
		});
	},
	getParamToSession: function() {
		if( 'object' !== $.type( BAPI.session ) ) {
			return;
		}

		// Check first if any params is sent
		var search_string;
		if(
			'string' !== $.type( window.location.search ) ||
			!window.location.search.length ||
			'?' !== window.location.search[0] ||
			!( search_string = window.location.search.substring(1) ).length
		) {
			return;
		}

		search_string = decodeURIComponent(search_string);

		// Retrieve all the get parameters.
		// if beds[min]=1 is sent we need to store it as beds.min = 1 and  beds=1 to ensure compatibility with existing code
		var get_params = {};
		$.each(
			search_string.split( '&' ),
			function() {
				var key_value_array;
				if(
					'string' !== $.type( this ) ||
					!this.length ||
					2 !== ( key_value_array = this.split( '=' ) ).length
				) {
					return true;
				}

				var c = key_value_array[0].indexOf('['); console.log(c);

				if (c == -1) {
					get_params[key_value_array[0]] = key_value_array[1].toString().split('+').join(' ');
				} else {
					// special case when the data-attribute value has nested brackets (such as adults[min])
					var k1 = key_value_array[0].substring(0,c);
					var k2 = key_value_array[0].substring(c+1,key_value_array[0].length-1);
				
					get_params[k1] = {};
					get_params[k1][k2] = key_value_array[1].toString().split('+').join(' ');
				}
			}
		);

		// console.log('---get_params');
		// console.log(JSON.parse(JSON.stringify(get_params)));


		// Control parameter, if the param search=1 is sent, we overwrite the session saved with the get parameters.
		if(
			'string' !== $.type( get_params.search ) ||
			'1' !== get_params.search
		) {
			return;
		}

		BAPI.session.searchparams = get_params;


		var searchdata = [];
		$.each(get_params, function(i,v) {
			searchdata.push( { name: i, value: v } );
		});

		// console.log('setting cookie from search');
		// console.log(JSON.stringify(searchdata));
		Cookies.set('searchdata', searchdata);

	}
}

context.mustacheHelpers = {
	render: function( template, data, cb ) {
		var html = null;
		if(
			'string' !== $.type( template ) ||
			template.length < 1 ||
			!$.isPlainObject( data ) ||

			'string' !== $.type( html = Mustache.render( template, data, context.mustacheHelpers.getPartials ) )
		) {
			return '';
		}

		if(typeof cb === "function") { cb(); }

		return html;
	},
	getPartials: function( partial_name ) {
		if(
			'string' !== $.type( partial_name ) ||
			'string' !== $.type( partial_template = BAPI.templates.get( partial_name ) )
		) {
			return '';
		}

		return partial_template;
	}
}


// Toggle truncation for dotdotdot
function toggleRead(caller) {
    var parent = $(caller).parent().parent();
    var isTruncated = $(parent).triggerHandler("isTruncated");
    if (isTruncated) {
        // Remove ellipsis
        $(parent).trigger("destroy.dot");
        if($(parent).find(".read-more").length == 0){
        $(parent).append('<div class="clearfix"><a href="javascript:void(0)" class="read-more">'+BAPI.textdata.less+'</a></div>');
		}
    } else {
		$(parent).css('height','100px');
        // Re-add ellipsis
        $(parent).dotdotdot();
        $(parent).append('<div class="clearfix"><a href="javascript:void(0)" class="read-more">'+BAPI.textdata.more+'</a></div>');
        $(parent).css('height','auto');
    }
    $(parent).find(".read-more").click(function () {
            toggleRead(this);
    });
}

context.rowfix = function(selector, wraprows) {
	var divs = $(selector);
	divs.addClass("span" + Math.ceil(12.0/wraprows))
	if (divs!==null && divs.length > 0) {
		for(var i = 0; i < divs.length; i+=wraprows) {
			try { divs.slice(i, i+wraprows).wrapAll("<div class='row-fluid'></div>"); } catch(err) {}
		}
	}
}

/*
	Group: Mapping
*/
context.setupmapwidgetshelper = function() {
	BAPI.log("Setup mapwidgets");

	var mapurl = '//maps.googleapis.com/maps/api/js?v=3.5&sensor=false&key=AIzaSyAY7wxlnkMG6czYy9K-wM4OWXs0YFpFzEE';
	/* Marker Manager */
	function MarkerManager(map,opt_opts){var me=this;me.map_=map;me.mapZoom_=map.getZoom();me.projectionHelper_=new ProjectionHelperOverlay(map);google.maps.event.addListener(me.projectionHelper_,"ready",function(){me.projection_=this.getProjection();me.initialize(map,opt_opts)})}MarkerManager.prototype.initialize=function(map,opt_opts){var me=this;opt_opts=opt_opts||{};me.tileSize_=MarkerManager.DEFAULT_TILE_SIZE_;var mapTypes=map.mapTypes;var mapMaxZoom=1;for(var sType in mapTypes){if(typeof map.mapTypes.get(sType)==="object"&&typeof map.mapTypes.get(sType).maxZoom==="number"){var mapTypeMaxZoom=map.mapTypes.get(sType).maxZoom;if(mapTypeMaxZoom>mapMaxZoom){mapMaxZoom=mapTypeMaxZoom}}}me.maxZoom_=opt_opts.maxZoom||19;me.trackMarkers_=opt_opts.trackMarkers;me.show_=opt_opts.show||true;var padding;if(typeof opt_opts.borderPadding==="number"){padding=opt_opts.borderPadding}else{padding=MarkerManager.DEFAULT_BORDER_PADDING_}me.swPadding_=new google.maps.Size(-padding,padding);me.nePadding_=new google.maps.Size(padding,-padding);me.borderPadding_=padding;me.gridWidth_={};me.grid_={};me.grid_[me.maxZoom_]={};me.numMarkers_={};me.numMarkers_[me.maxZoom_]=0;google.maps.event.addListener(map,"dragend",function(){me.onMapMoveEnd_()});google.maps.event.addListener(map,"zoom_changed",function(){me.onMapMoveEnd_()});me.removeOverlay_=function(marker){marker.setMap(null);me.shownMarkers_--};me.addOverlay_=function(marker){if(me.show_){marker.setMap(me.map_);me.shownMarkers_++}};me.resetManager_();me.shownMarkers_=0;me.shownBounds_=me.getMapGridBounds_();google.maps.event.trigger(me,"loaded")};MarkerManager.DEFAULT_TILE_SIZE_=1024;MarkerManager.DEFAULT_BORDER_PADDING_=100;MarkerManager.MERCATOR_ZOOM_LEVEL_ZERO_RANGE=256;MarkerManager.prototype.resetManager_=function(){var mapWidth=MarkerManager.MERCATOR_ZOOM_LEVEL_ZERO_RANGE;for(var zoom=0;zoom<=this.maxZoom_;++zoom){this.grid_[zoom]={};this.numMarkers_[zoom]=0;this.gridWidth_[zoom]=Math.ceil(mapWidth/this.tileSize_);mapWidth<<=1}};MarkerManager.prototype.clearMarkers=function(){this.processAll_(this.shownBounds_,this.removeOverlay_);this.resetManager_()};MarkerManager.prototype.getTilePoint_=function(latlng,zoom,padding){var pixelPoint=this.projectionHelper_.LatLngToPixel(latlng,zoom);var point=new google.maps.Point(Math.floor((pixelPoint.x+padding.width)/this.tileSize_),Math.floor((pixelPoint.y+padding.height)/this.tileSize_));return point};MarkerManager.prototype.addMarkerBatch_=function(marker,minZoom,maxZoom){var me=this;var mPoint=marker.getPosition();marker.MarkerManager_minZoom=minZoom;if(this.trackMarkers_){google.maps.event.addListener(marker,"changed",function(a,b,c){me.onMarkerMoved_(a,b,c)})}var gridPoint=this.getTilePoint_(mPoint,maxZoom,new google.maps.Size(0,0,0,0));for(var zoom=maxZoom;zoom>=minZoom;zoom--){var cell=this.getGridCellCreate_(gridPoint.x,gridPoint.y,zoom);cell.push(marker);gridPoint.x=gridPoint.x>>1;gridPoint.y=gridPoint.y>>1}};MarkerManager.prototype.isGridPointVisible_=function(point){var vertical=this.shownBounds_.minY<=point.y&&point.y<=this.shownBounds_.maxY;var minX=this.shownBounds_.minX;var horizontal=minX<=point.x&&point.x<=this.shownBounds_.maxX;if(!horizontal&&minX<0){var width=this.gridWidth_[this.shownBounds_.z];horizontal=minX+width<=point.x&&point.x<=width-1}return vertical&&horizontal};MarkerManager.prototype.onMarkerMoved_=function(marker,oldPoint,newPoint){var zoom=this.maxZoom_;var changed=false;var oldGrid=this.getTilePoint_(oldPoint,zoom,new google.maps.Size(0,0,0,0));var newGrid=this.getTilePoint_(newPoint,zoom,new google.maps.Size(0,0,0,0));while(zoom>=0&&(oldGrid.x!==newGrid.x||oldGrid.y!==newGrid.y)){var cell=this.getGridCellNoCreate_(oldGrid.x,oldGrid.y,zoom);if(cell){if(this.removeFromArray_(cell,marker)){this.getGridCellCreate_(newGrid.x,newGrid.y,zoom).push(marker)}}if(zoom===this.mapZoom_){if(this.isGridPointVisible_(oldGrid)){if(!this.isGridPointVisible_(newGrid)){this.removeOverlay_(marker);changed=true}}else{if(this.isGridPointVisible_(newGrid)){this.addOverlay_(marker);changed=true}}}oldGrid.x=oldGrid.x>>1;oldGrid.y=oldGrid.y>>1;newGrid.x=newGrid.x>>1;newGrid.y=newGrid.y>>1;--zoom}if(changed){this.notifyListeners_()}};MarkerManager.prototype.removeMarker=function(marker){var zoom=this.maxZoom_;var changed=false;var point=marker.getPosition();var grid=this.getTilePoint_(point,zoom,new google.maps.Size(0,0,0,0));while(zoom>=0){var cell=this.getGridCellNoCreate_(grid.x,grid.y,zoom);if(cell){this.removeFromArray_(cell,marker)}if(zoom===this.mapZoom_){if(this.isGridPointVisible_(grid)){this.removeOverlay_(marker);changed=true}}grid.x=grid.x>>1;grid.y=grid.y>>1;--zoom}if(changed){this.notifyListeners_()}this.numMarkers_[marker.MarkerManager_minZoom]--};MarkerManager.prototype.addMarkers=function(markers,minZoom,opt_maxZoom){var maxZoom=this.getOptMaxZoom_(opt_maxZoom);for(var i=markers.length-1;i>=0;i--){this.addMarkerBatch_(markers[i],minZoom,maxZoom)}this.numMarkers_[minZoom]+=markers.length};MarkerManager.prototype.getOptMaxZoom_=function(opt_maxZoom){return opt_maxZoom||this.maxZoom_};MarkerManager.prototype.getMarkerCount=function(zoom){var total=0;for(var z=0;z<=zoom;z++){total+=this.numMarkers_[z]}return total};MarkerManager.prototype.getMarker=function(lat,lng,zoom){var mPoint=new google.maps.LatLng(lat,lng);var gridPoint=this.getTilePoint_(mPoint,zoom,new google.maps.Size(0,0,0,0));var marker=new google.maps.Marker({position:mPoint});var cellArray=this.getGridCellNoCreate_(gridPoint.x,gridPoint.y,zoom);if(cellArray!==undefined){for(var i=0;i<cellArray.length;i++){if(lat===cellArray[i].getLatLng().lat()&&lng===cellArray[i].getLatLng().lng()){marker=cellArray[i]}}}return marker};MarkerManager.prototype.addMarker=function(marker,minZoom,opt_maxZoom){var maxZoom=this.getOptMaxZoom_(opt_maxZoom);this.addMarkerBatch_(marker,minZoom,maxZoom);var gridPoint=this.getTilePoint_(marker.getPosition(),this.mapZoom_,new google.maps.Size(0,0,0,0));if(this.isGridPointVisible_(gridPoint)&&minZoom<=this.shownBounds_.z&&this.shownBounds_.z<=maxZoom){this.addOverlay_(marker);this.notifyListeners_()}this.numMarkers_[minZoom]++};function GridBounds(bounds){this.minX=Math.min(bounds[0].x,bounds[1].x);this.maxX=Math.max(bounds[0].x,bounds[1].x);this.minY=Math.min(bounds[0].y,bounds[1].y);this.maxY=Math.max(bounds[0].y,bounds[1].y)}GridBounds.prototype.equals=function(gridBounds){if(this.maxX===gridBounds.maxX&&this.maxY===gridBounds.maxY&&this.minX===gridBounds.minX&&this.minY===gridBounds.minY){return true}else{return false}};GridBounds.prototype.containsPoint=function(point){var outer=this;return(outer.minX<=point.x&&outer.maxX>=point.x&&outer.minY<=point.y&&outer.maxY>=point.y)};MarkerManager.prototype.getGridCellCreate_=function(x,y,z){var grid=this.grid_[z];if(x<0){x+=this.gridWidth_[z]}var gridCol=grid[x];if(!gridCol){gridCol=grid[x]=[];return(gridCol[y]=[])}var gridCell=gridCol[y];if(!gridCell){return(gridCol[y]=[])}return gridCell};MarkerManager.prototype.getGridCellNoCreate_=function(x,y,z){var grid=this.grid_[z];if(x<0){x+=this.gridWidth_[z]}var gridCol=grid[x];return gridCol?gridCol[y]:undefined};MarkerManager.prototype.getGridBounds_=function(bounds,zoom,swPadding,nePadding){zoom=Math.min(zoom,this.maxZoom_);var bl=bounds.getSouthWest();var tr=bounds.getNorthEast();var sw=this.getTilePoint_(bl,zoom,swPadding);var ne=this.getTilePoint_(tr,zoom,nePadding);var gw=this.gridWidth_[zoom];if(tr.lng()<bl.lng()||ne.x<sw.x){sw.x-=gw}if(ne.x-sw.x+1>=gw){sw.x=0;ne.x=gw-1}var gridBounds=new GridBounds([sw,ne]);gridBounds.z=zoom;return gridBounds};MarkerManager.prototype.getMapGridBounds_=function(){return this.getGridBounds_(this.map_.getBounds(),this.mapZoom_,this.swPadding_,this.nePadding_)};MarkerManager.prototype.onMapMoveEnd_=function(){this.objectSetTimeout_(this,this.updateMarkers_,0)};MarkerManager.prototype.objectSetTimeout_=function(object,command,milliseconds){return window.setTimeout(function(){command.call(object)},milliseconds)};MarkerManager.prototype.visible=function(){return this.show_?true:false};MarkerManager.prototype.isHidden=function(){return !this.show_};MarkerManager.prototype.show=function(){this.show_=true;this.refresh()};MarkerManager.prototype.hide=function(){this.show_=false;this.refresh()};MarkerManager.prototype.toggle=function(){this.show_=!this.show_;this.refresh()};MarkerManager.prototype.refresh=function(){if(this.shownMarkers_>0){this.processAll_(this.shownBounds_,this.removeOverlay_)}if(this.show_){this.processAll_(this.shownBounds_,this.addOverlay_)}this.notifyListeners_()};MarkerManager.prototype.updateMarkers_=function(){this.mapZoom_=this.map_.getZoom();var newBounds=this.getMapGridBounds_();if(newBounds.equals(this.shownBounds_)&&newBounds.z===this.shownBounds_.z){return}if(newBounds.z!==this.shownBounds_.z){this.processAll_(this.shownBounds_,this.removeOverlay_);if(this.show_){this.processAll_(newBounds,this.addOverlay_)}}else{this.rectangleDiff_(this.shownBounds_,newBounds,this.removeCellMarkers_);if(this.show_){this.rectangleDiff_(newBounds,this.shownBounds_,this.addCellMarkers_)}}this.shownBounds_=newBounds;this.notifyListeners_()};MarkerManager.prototype.notifyListeners_=function(){google.maps.event.trigger(this,"changed",this.shownBounds_,this.shownMarkers_)};MarkerManager.prototype.processAll_=function(bounds,callback){for(var x=bounds.minX;x<=bounds.maxX;x++){for(var y=bounds.minY;y<=bounds.maxY;y++){this.processCellMarkers_(x,y,bounds.z,callback)}}};MarkerManager.prototype.processCellMarkers_=function(x,y,z,callback){var cell=this.getGridCellNoCreate_(x,y,z);if(cell){for(var i=cell.length-1;i>=0;i--){callback(cell[i])}}};MarkerManager.prototype.removeCellMarkers_=function(x,y,z){this.processCellMarkers_(x,y,z,this.removeOverlay_)};MarkerManager.prototype.addCellMarkers_=function(x,y,z){this.processCellMarkers_(x,y,z,this.addOverlay_)};MarkerManager.prototype.rectangleDiff_=function(bounds1,bounds2,callback){var me=this;me.rectangleDiffCoords_(bounds1,bounds2,function(x,y){callback.apply(me,[x,y,bounds1.z])})};MarkerManager.prototype.rectangleDiffCoords_=function(bounds1,bounds2,callback){var minX1=bounds1.minX;var minY1=bounds1.minY;var maxX1=bounds1.maxX;var maxY1=bounds1.maxY;var minX2=bounds2.minX;var minY2=bounds2.minY;var maxX2=bounds2.maxX;var maxY2=bounds2.maxY;var x,y;for(x=minX1;x<=maxX1;x++){for(y=minY1;y<=maxY1&&y<minY2;y++){callback(x,y)}for(y=Math.max(maxY2+1,minY1);y<=maxY1;y++){callback(x,y)}}for(y=Math.max(minY1,minY2);y<=Math.min(maxY1,maxY2);y++){for(x=Math.min(maxX1+1,minX2)-1;x>=minX1;x--){callback(x,y)}for(x=Math.max(minX1,maxX2+1);x<=maxX1;x++){callback(x,y)}}};MarkerManager.prototype.removeFromArray_=function(array,value,opt_notype){var shift=0;for(var i=0;i<array.length;++i){if(array[i]===value||(opt_notype&&array[i]===value)){array.splice(i--,1);shift++}}return shift};function ProjectionHelperOverlay(map){this.setMap(map);var TILEFACTOR=8;var TILESIDE=1<<TILEFACTOR;var RADIUS=7;this._map=map;this._zoom=-1;this._X0=this._Y0=this._X1=this._Y1=-1}ProjectionHelperOverlay.prototype=new google.maps.OverlayView();ProjectionHelperOverlay.prototype.LngToX_=function(lng){return(1+lng/180)};ProjectionHelperOverlay.prototype.LatToY_=function(lat){var sinofphi=Math.sin(lat*Math.PI/180);return(1-0.5/Math.PI*Math.log((1+sinofphi)/(1-sinofphi)))};ProjectionHelperOverlay.prototype.LatLngToPixel=function(latlng,zoom){var map=this._map;var div=this.getProjection().fromLatLngToDivPixel(latlng);var abs={x:~~(0.5+this.LngToX_(latlng.lng())*(2<<(zoom+6))),y:~~(0.5+this.LatToY_(latlng.lat())*(2<<(zoom+6)))};return abs};ProjectionHelperOverlay.prototype.draw=function(){if(!this.ready){this.ready=true;google.maps.event.trigger(this,"ready")}};
	/* StyledMarkers */
	var StyledIconTypes={};var StyledMarker,StyledIcon;(function(){var bu_="http://chart.apis.google.com/chart?chst=";var gm_=google.maps;var gp_=gm_.Point;var ge_=gm_.event;var gmi_=gm_.MarkerImage;StyledMarker=function(styledMarkerOptions){var me=this;var ci=me.styleIcon=styledMarkerOptions.styleIcon;me.bindTo("icon",ci);me.bindTo("shadow",ci);me.bindTo("shape",ci);me.setOptions(styledMarkerOptions)};StyledMarker.prototype=new gm_.Marker();StyledIcon=function(styledIconType,styledIconOptions,styleClass){var k;var me=this;var i_="icon";var sw_="shadow";var s_="shape";var a_=[];function gs_(){var image_=document.createElement("img");var simage_=document.createElement("img");ge_.addDomListenerOnce(simage_,"load",function(){var w=simage_.width,h=simage_.height;me.set(sw_,new gmi_(styledIconType.getShadowURL(me),null,null,styledIconType.getShadowAnchor(me,w,h)));simage=null});ge_.addDomListenerOnce(image_,"load",function(){var w=image_.width,h=image_.height;me.set(i_,new gmi_(styledIconType.getURL(me),null,null,styledIconType.getAnchor(me,w,h)));me.set(s_,styledIconType.getShape(me,w,h));image_=null});image_.src=styledIconType.getURL(me);simage_.src=styledIconType.getShadowURL(me)}me.as_=function(v){a_.push(v);for(k in styledIconOptions){v.set(k,styledIconOptions[k])}};if(styledIconType!==StyledIconTypes.CLASS){for(k in styledIconType.defaults){me.set(k,styledIconType.defaults[k])}me.setValues(styledIconOptions);me.set(i_,styledIconType.getURL(me));me.set(sw_,styledIconType.getShadowURL(me));if(styleClass){styleClass.as_(me)}gs_();me.changed=function(k){if(k!==i_&&k!==s_&&k!==sw_){gs_()}}}else{me.setValues(styledIconOptions);me.changed=function(v){styledIconOptions[v]=me.get(v);for(k=0;k<a_.length;k++){a_[k].set(v,me.get(v))}};if(styleClass){styleClass.as_(me)}}};StyledIcon.prototype=new gm_.MVCObject();StyledIconTypes.CLASS={};StyledIconTypes.MARKER={defaults:{text:"",color:"00ff00",fore:"000000",starcolor:null},getURL:function(props){var _url;var starcolor_=props.get("starcolor");var text_=props.get("text");var color_=props.get("color").replace(/#/,"");var fore_=props.get("fore").replace(/#/,"");if(starcolor_){_url=bu_+"d_map_xpin_letter&chld=pin_star|"}else{_url=bu_+"d_map_pin_letter&chld="}if(text_){text_=text_.substr(0,2)}_url+=text_+"|";_url+=color_+"|";_url+=fore_;if(starcolor_){_url+="|"+starcolor_.replace(/#/,"")}return _url},getShadowURL:function(props){if(props.get("starcolor")){return bu_+"d_map_xpin_shadow&chld=pin_star"}else{return bu_+"d_map_pin_shadow"}},getAnchor:function(props,width,height){return new gp_(width/2,height)},getShadowAnchor:function(props,width,height){return new gp_(width/4,height)},getShape:function(props,width,height){var _iconmap={};_iconmap.coord=[width/2,height,(7/16)*width,(5/8)*height,(5/16)*width,(7/16)*height,(7/32)*width,(5/16)*height,(5/16)*width,(1/8)*height,(1/2)*width,0,(11/16)*width,(1/8)*height,(25/32)*width,(5/16)*height,(11/16)*width,(7/16)*height,(9/16)*width,(5/8)*height];for(var i=0;i<_iconmap.coord.length;i++){_iconmap.coord[i]=Math.round(_iconmap.coord[i])}_iconmap.type="poly";return _iconmap}};StyledIconTypes.BUBBLE={defaults:{text:"",color:"00ff00",fore:"000000"},getURL:function(props){var _url=bu_+"d_bubble_text_small&chld=bb|";_url+=props.get("text")+"|";_url+=props.get("color").replace(/#/,"")+"|";_url+=props.get("fore").replace(/#/,"");return _url},getShadowURL:function(props){return bu_+"d_bubble_text_small_shadow&chld=bb|"+props.get("text")},getAnchor:function(props,width,height){return new google.maps.Point(0,42)},getShadowAnchor:function(props,width,height){return new google.maps.Point(0,44)},getShape:function(props,width,height){var _iconmap={};_iconmap.coord=[0,44,13,26,13,6,17,1,width-4,1,width,6,width,21,width-4,26,21,26];_iconmap.type="poly";return _iconmap}}})();
	/* jquery.meta */
	(function($){$.extend({metadata:{defaults:{type:"class",name:"metadata",cre:/({.*})/,single:"metadata"},setType:function(type,name){this.defaults.type=type;this.defaults.name=name},get:function(elem,opts){var settings=$.extend({},this.defaults,opts);if(!settings.single.length){settings.single="metadata"}var data=$.data(elem,settings.single);if(data){return data}data="{}";var getData=function(data){if(typeof data!="string"){return data}if(data.indexOf("{")<0){data=eval("("+data+")")}};var getObject=function(data){if(typeof data!="string"){return data}data=eval("("+data+")");return data};if(settings.type=="html5"){var object={};$(elem.attributes).each(function(){var name=this.nodeName;if(name.match(/^data-/)){name=name.replace(/^data-/,"")}else{return true}object[name]=getObject(this.nodeValue)})}else{if(settings.type=="class"){var m=settings.cre.exec(elem.className);if(m){data=m[1]}}else{if(settings.type=="elem"){if(!elem.getElementsByTagName){return}var e=elem.getElementsByTagName(settings.name);if(e.length){data=$.trim(e[0].innerHTML)}}else{if(elem.getAttribute!=undefined){var attr=elem.getAttribute(settings.name);if(attr){data=attr}}}}object=getObject(data.indexOf("{")<0?"{"+data+"}":data)}$.data(elem,settings.single,object);return object}}});$.fn.metadata=function(opts){return $.metadata.get(this[0],opts)}})(jQuery);
	/* jquery.jmapping */
	(function($){$.jMapping=function(map_elm,options){var settings,gmarkers,mapped,map,markerManager,places,bounds,jMapper,info_windows;map_elm=(typeof map_elm=="string")?$(map_elm).get(0):map_elm;if(!($(map_elm).data("jMapping"))){settings=$.extend(true,{},$.jMapping.defaults);$.extend(true,settings,options);gmarkers={};info_windows=[];var init=function(doUpdate){var info_window_selector,min_zoom,zoom_level;info_window_selector=[settings.side_bar_selector,settings.location_selector,settings.info_window_selector].join(" ");$(info_window_selector).hide();places=getPlaces();bounds=getBounds(doUpdate);if(doUpdate){gmarkers={};info_windows=[];markerManager.clearMarkers();google.maps.event.trigger(map,"resize");map.fitBounds(bounds);if(settings.force_zoom_level){map.setZoom(settings.force_zoom_level)}}else{map=createMap();markerManager=new MarkerManager(map)}places.each(function(){var marker=createMarker(this);if(!(settings.link_selector===false)){setupLink(this)}$(document).trigger("markerCreated.jMapping",[marker])});if(doUpdate){updateMarkerManager()}else{google.maps.event.addListener(markerManager,"loaded",function(){updateMarkerManager();if(settings.default_zoom_level){map.setZoom(settings.default_zoom_level)}})}if(!(settings.link_selector===false)&&!doUpdate){attachMapsEventToLinks()}};var createMap=function(){if(settings.map_config){map=new google.maps.Map(map_elm,settings.map_config)}else{map=new google.maps.Map(map_elm,{navigationControlOptions:{style:google.maps.NavigationControlStyle.SMALL},mapTypeControl:false,mapTypeId:google.maps.MapTypeId.ROADMAP,zoom:9})}map.fitBounds(bounds);if(settings.force_zoom_level){map.setZoom(settings.force_zoom_level)}return map};var getPlaces=function(){return $(settings.side_bar_selector+" "+settings.location_selector)};var getPlacesData=function(doUpdate){return places.map(function(){if(doUpdate){$(this).data("metadata",false)}return $(this).metadata(settings.metadata_options)})};var getBounds=function(doUpdate){var places_data=getPlacesData(doUpdate),newBounds,initialPoint;if(places_data.length){initialPoint=$.jMapping.makeGLatLng(places_data[0].point)}else{initialPoint=$.jMapping.makeGLatLng(settings.default_point)}newBounds=new google.maps.LatLngBounds(initialPoint,initialPoint);for(var i=1,len=places_data.length;i<len;i++){newBounds.extend($.jMapping.makeGLatLng(places_data[i].point))}return newBounds};var setupLink=function(place_elm){var $place_elm=$(place_elm),location_data=$place_elm.metadata(settings.metadata_options),link=$place_elm.find(settings.link_selector);link.attr("href",("#"+location_data.id))};var chooseIconOptions=function(category){if(settings.category_icon_options){if($.isFunction(settings.category_icon_options)){return settings.category_icon_options(category)}else{return settings.category_icon_options[category]||settings.category_icon_options["default"]}}else{return{}}};var createMarker=function(place_elm){var $place_elm=$(place_elm),place_data,point,marker,$info_window_elm,info_window;place_data=$place_elm.metadata(settings.metadata_options);point=$.jMapping.makeGLatLng(place_data.point);if(settings.category_icon_options){icon_options=chooseIconOptions(place_data.category);if((typeof icon_options==="string")||(icon_options instanceof google.maps.MarkerImage)){marker=new google.maps.Marker({icon:icon_options,position:point,map:map})}else{marker=new StyledMarker({styleIcon:new StyledIcon(StyledIconTypes.MARKER,icon_options),position:point,map:map})}}else{marker=new google.maps.Marker({position:point,map:map})}$info_window_elm=$place_elm.find(settings.info_window_selector);if($info_window_elm.length>0){info_window=new google.maps.InfoWindow({content:$info_window_elm.html(),maxWidth:settings.info_window_max_width});info_windows.push(info_window);google.maps.event.addListener(marker,"click",function(){$.each(info_windows,function(index,iwindow){if(info_window!=iwindow){iwindow.close()}});info_window.open(map,marker)})}gmarkers[parseInt(place_data.id,10)]=marker;return marker};var updateMarkerManager=function(){if(settings.always_show_markers===true){min_zoom=0}else{zoom_level=map.getZoom();min_zoom=(zoom_level<7)?0:(zoom_level-7)}markerManager.addMarkers(gmarkersArray(),min_zoom);markerManager.refresh();if(settings.force_zoom_level){map.setZoom(settings.force_zoom_level)}};var attachMapsEventToLinks=function(){var location_link_selector=[settings.side_bar_selector,settings.location_selector,settings.link_selector].join(" ");$(location_link_selector).live("click",function(e){e.preventDefault();var marker_index=parseInt($(this).attr("href").split("#")[1],10);google.maps.event.trigger(gmarkers[marker_index],"click")})};var gmarkersArray=function(){var marker_arr=[];$.each(gmarkers,function(key,value){marker_arr.push(value)});return marker_arr};if($(document).trigger("beforeMapping.jMapping",[settings])!=false){init();mapped=true}else{mapped=false}jMapper={gmarkers:gmarkers,settings:settings,mapped:mapped,map:map,markerManager:markerManager,gmarkersArray:gmarkersArray,getBounds:getBounds,getPlacesData:getPlacesData,getPlaces:getPlaces,update:function(){if($(document).trigger("beforeUpdate.jMapping",[this])!=false){init(true);this.map=map;this.gmarkers=gmarkers;this.markerManager=markerManager;$(document).trigger("afterUpdate.jMapping",[this])}}};$(document).trigger("afterMapping.jMapping",[jMapper]);return jMapper}else{return $(map_elm).data("jMapping")}};$.extend($.jMapping,{defaults:{side_bar_selector:"#map-side-bar:first",location_selector:".map-location",link_selector:"a.map-link",info_window_selector:".info-box",info_window_max_width:425,default_point:{lat:0,lng:0},metadata_options:{type:"attr",name:"data-jmapping"}},makeGLatLng:function(place_point){return new google.maps.LatLng(place_point.lat,place_point.lng)}});$.fn.jMapping=function(options){if((options=="update")&&$(this[0]).data("jMapping")){$(this[0]).data("jMapping").update()}else{if(options=="update"){options={}}$(this[0]).data("jMapping",$.jMapping(this[0],options))}return this}})(jQuery);

	$.each($('.bapi-map'), function (i, item) {
		var ctl = $(item);
		var selector = '#' + ctl.attr('id');
		var lsel = ctl.attr('data-refresh-selector');
		var lselevent = ctl.attr('data-refresh-selector-event');
		var locsel = ctl.attr('data-loc-selector');
		if (locsel===null || locsel=='') { locsel = '.map-location'; }
		var linksel = ctl.attr('data-link-selector');
		if (linksel===null || linksel=='') { linksel = '.map-item'; }
		var caticons = null;
		try {
			/* the data-category-icons wasnt specified */
			caticons = function(category){
				/* no category specified lets show the default pin */
					if (category == 'undefined' || category == '' || category == null || category == 'poi')
					{return new google.maps.MarkerImage(BAPI.UI.WPIS_PATH + 'img/pin.png');}
					else{
						/* this is a poi and is numbered */
						if (category.indexOf('poi') == 0){
							/* lets use an sprite for the numbered pins instead of individual images */
							//BAPI.log('im here ' + category);
							//BAPI.log(category.substring(4,category.length)*5);
							var theIconNumber = category.substring(4,category.length);
							if(parseInt(theIconNumber) <= 99){
							if($('.property-detail-page').length > 0){theIconNumber = parseInt(theIconNumber) + 1;}
							//BAPI.log('the icon number '+theIconNumber);
							var pointX = ((theIconNumber % 10)-1)*22;
							var pointY = Math.floor(theIconNumber / 10)* 39;
							if (theIconNumber % 10 == 0){pointX = 198; pointY = (Math.floor(theIconNumber / 10)-1)* 39;}
							//BAPI.log('point X '+pointX);
							//BAPI.log('point Y '+pointY);
							return new google.maps.MarkerImage(BAPI.UI.WPIS_PATH + 'img/pins-numbered.png', new google.maps.Size(22, 39), new google.maps.Point(pointX, pointY));
							}else{
								return new google.maps.MarkerImage(BAPI.UI.WPIS_PATH + 'img/pin.png');
							}

						} else if (category.indexOf('property') == 0){
							/* this is a property pin */
							return new google.maps.MarkerImage(BAPI.UI.WPIS_PATH + 'img/pin_properties.png');
						} else if (category.indexOf('mainPoi') == 0){
							/* this is an attraction poi */
							return new google.maps.MarkerImage(BAPI.UI.WPIS_PATH + 'img/pin_attractions.png');
						} else if (category.indexOf('pinOffice') == 0){
							/* this is an attraction poi */
							return new google.maps.MarkerImage(BAPI.UI.WPIS_PATH + 'img/pin_office.png');
						} else{
							/* none of the above lets use the default pin */
							return new google.maps.MarkerImage(BAPI.UI.WPIS_PATH + 'img/pin.png');
						}
					}

				}
			}
		catch(err) {}

		BAPI.log("Creating map widget for " + selector + ', location selector=' + locsel + ', link selector=' + linksel);
		/* Setup the jMapping params */
		var jMapping_params = {
			side_bar_selector: '#map-locations:first',
			location_selector: locsel,
			link_selector: linksel,
			info_window_selector: '.info-html',
			category_icon_options: caticons,
			map_config: {
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				zoom: 7
			}
		};



		if(
			'string' === $.type( ctl.attr( 'data-info-window-max-width' ) ) &&
			$.isNumeric( infowindowmaxwidth = parseInt( ctl.attr( 'data-info-window-max-width' ) ) )
		) {
			jMapping_params.info_window_max_width = infowindowmaxwidth;
		}

		if(
			'string' === $.type( BAPI.config().mapviewType ) &&
			'string' === $.type( mapTypeValue = google.maps.MapTypeId[ BAPI.config().mapviewType ] )
		) {
			jMapping_params.map_config.mapTypeId = mapTypeValue;
		}

		ctl.jMapping( jMapping_params );

		if (typeof(lsel)!=="undefined" && lsel!==null && lsel!='') {
			$(lsel).on(lselevent, function() {
				BAPI.log("Refresh selector clicked");
				ctl.jMapping('update');
			});
		}
	});
}


/*
	Group: Search Widgets
*/
var valid = false;
context.createRateBlockWidget = function (target, options){

	var cur = BAPI.curentity;
	if (typeof(cur)==="undefined" || cur===null || !(cur.ID>0) || cur.entity!=BAPI.entities.property) {
		return;
	}

	options.dataselector = "quicksearch";
	target.block({ message: "<img src='" + loadingImgUrl + "' />" });

	BAPI.datamanager.get(BAPI.entities.property, cur.ID, function(p) {
		target.unblock();
		var data = {};
		data.result = applyMyList([p],cur.entity);
		data.site = BAPI.site;
		data.config = BAPI.config();
		data.textdata = BAPI.textdata;
		data.session = BAPI.session;
		//if (options.log) { BAPI.log("--createSearchWidget.res--"); BAPI.log(data); }

		var contextData = data['result'][0].ContextData;

		if(contextData.Quote.IsValid) {
			$('.book-btn.bapi-inquirenow').addClass('hide');
			$('.book-btn.bapi-booknow').hide().removeClass('hide').fadeIn();
		} else {
			$('.book-btn.bapi-inquirenow').hide().removeClass('hide').fadeIn();
			$('.book-btn.bapi-booknow').addClass('hide');
		}

		target.html(context.mustacheHelpers.render(options.template, data));

		context.createDatePicker('#rateblockcheckin', { "property": p, "checkoutID": '#rateblockcheckout' });
		context.createDatePicker('#rateblockcheckout', { "property": p, "checkinID": '#rateblockcheckin' });

		// handle simple get quote
		$(".bapi-getquote").on("click", function () {
			var reqdata = saveFormToSession(this, options);
			BAPI.datamanager.clear(BAPI.entities.property, cur.ID);
			context.createRateBlockWidget(target, options);
		});

		var theInquiryForm = $('aside.span3 .widget_bapi_inquiry_form').attr('id');
		var ifID = $("#"+theInquiryForm);
		/* if we are in a property with inquiry mode lets use the inquiry mode title*/
		if(!p.IsBookable && !ifID.hasClass('openedib')){
				ifID.hide();
			$(".bapi-inquirynow").live("click",function(){
				//change title
				$("#"+theInquiryForm+" .widget-title").html($("#"+theInquiryForm+" .inquirymodetitle").html());
				//copy over the rateblock data
				copyRateBlockDataToInquiryForm(theInquiryForm);
				//everything read show the inquiry form
				ifID.show();
				if(!ifID.hasClass('openedib')) {
					ifID.addClass('openedib');
				}
				//center the  inquiry form if there is space
				var ifIDOffset = ifID.offset().top;
				var ifIDHeight = ifID.height();
				var windowHeight = $(window).height();
				var offset;
				if (ifIDHeight < windowHeight) {
					offset = ifIDOffset - ((windowHeight / 2) - (ifIDHeight / 2));
				}
				else {
					offset = ifIDOffset - 10;
				}
				$('html, body').animate({scrollTop:offset}, 700);
			});
		}
		$(".bapi-inquire").on("click", function() {
			context.createInquiryForm("#modal-inquiry-form");
			$("#modal-inquiry").dialog();
		});

		var tracker = $('.bapi-wishlisttracker');
        var tracker_data = tracker.length ? tracker.data('pkid').toString() : '';

		if( BAPI.mylisttracker.indexof(tracker_data, 'property') >= 0) {
			tracker.addClass('active').find('span span').text(BAPI.textdata['WishListed']);
		}

		$('.bapi-wishlisttracker').on("click", function () {
			var ctl = $(this);
			var pkid = ctl.attr("data-pkid");
			if (ctl.hasClass('active')) {
				BAPI.log("adding pkid=" + pkid, 2);
				BAPI.mylisttracker.del(pkid, BAPI.entities.property);
				ctl.find('span span').text(BAPI.textdata['WishList']);
			} else {
				BAPI.log("removing pkid=" + pkid, 2);
				BAPI.mylisttracker.add(pkid, BAPI.entities.property);
				ctl.find('span span').text(BAPI.textdata['WishListed']);
			}
			BAPI.savesession();
		});

		$(".bapi-booknow").on("click", function() {
			target.block({ message: "<img src='" + loadingImgUrl + "' />" });
			var reqdata = saveFormToSession(this, options);
			BAPI.log(BAPI.session.searchparams);
			var url = "/makebooking/?redir=1&keyid=" + cur.ID;
            if(!BAPI.isempty(BAPI.session.searchparams.checkin))
                url += "&checkin=" + BAPI.session.searchparams.checkin;
            if (!BAPI.isempty(BAPI.session.searchparams.checkout))
                url += "&checkout=" + BAPI.session.searchparams.checkout;
            if (!BAPI.isempty(BAPI.session.searchparams.adults))
                url += "&adults=" + BAPI.session.searchparams.adults.min;
            if (!BAPI.isempty(BAPI.session.searchparams.children))
                url += "&children=" + BAPI.session.searchparams.children.min;
            if (!BAPI.isempty(BAPI.session.searchparams.rooms))
			    url +=	"&rooms=" + BAPI.session.searchparams.rooms.min;

			url = context.secureurl(url);

			window.location.href = url;
		});
	});
}

context.createSearchWidget = function (targetid, options, doSearchCallback) {
	options = initOptions(options, 3, 'tmpl-search-rateblock');
	//we update the rate labels
	if(typeof(options.config.rate.enabled) !== 'undefined' && options.config.rate.enabled && typeof(BAPI.session.currency) !== 'undefined' && BAPI.session.currency != 'USD'){
		var theCurrencySymbol = '';
		var theConversionRate = 1;
		//we get the currency symbol and the conversion rate
		for(var i=0;i < options.config.currencies.length;i++){
			if(options.config.currencies[i].CurrencySymbol == BAPI.session.currency){
				theConversionRate = options.config.currencies[i].ConversionRate;
				theCurrencySymbol = options.config.currencies[i].RegionInfo.CurrencySymbol;
				break;
			}
		}
		//we get the new label
		for(var i=0;i < options.config.rate.values.length;i++){
			var theValue = options.config.rate.values[i].Data;
			var theDotPosition = theValue.indexOf(".");
			var theTimeSpan = theValue.substring(theDotPosition,theValue.length);
			theValue = theValue.substring(0, theDotPosition);
			if(theTimeSpan == ".001"){
				theValue = theValue*theConversionRate;
			}
			if(theTimeSpan == ".007"){
				theValue = (theValue*7)*theConversionRate;
			}
			if(theTimeSpan == ".03"){
				theValue = (theValue*30)*theConversionRate;
			}
			if(theTimeSpan == ".025"){
				theValue = (theValue*25)*theConversionRate;
			}
			theValue = parseFloat(theValue).toFixed(2);
			var theLabel = options.config.rate.values[i].Label;
			var theDollarPosition = theLabel.indexOf("$");
			var theSlashPosition = theLabel.indexOf("/");
			var preLabel = theLabel.substring(0,theDollarPosition);
			var postLabel = theLabel.substring(theSlashPosition,theLabel.length);
			//we update the label
			if(theDollarPosition > 0 && theSlashPosition > 0){
				options.config.rate.values[i].Label = preLabel + theCurrencySymbol + theValue + ' ' + postLabel;
				options.config.rate.values[i].Data = parseInt(theValue) + theTimeSpan;
			}
		}
	}
	if (typeof (options.dataselector) === "undefined") { options.dataselector = "quicksearch"; }
	context.loading.ctlshow(targetid);

	// do some pre-processing on the object to bind
	var res = {};
	res.result = [ options.property ];
	res.site = options.site;
	res.config = options.config;
	res.textdata = options.textdata;
	if (options.log) { BAPI.log("--createSearchWidget.res--"); BAPI.log(res); }
	$(targetid).html(context.mustacheHelpers.render(options.template, res));
	// see if there is some quote info to display
	var p = options.property;

	/* check if the amenities are enabled */
	if(!BAPI.isempty(BAPI.config().amenity.enabled) && BAPI.config().amenity.enabled){
		var arrayAmenitiesLength = BAPI.config().amenity.values.length;
		var arrayAmenities = [];
		var amenitiesInSession = !BAPI.isempty(BAPI.session.searchparams) && !BAPI.isempty(BAPI.session.searchparams.amenities);
		/* Are the amenities in Session ? */
		if(amenitiesInSession){
			/* array of the amenities from session */
			var arraySessionAmenities = BAPI.session.searchparams.amenities.split(',');
			/* this flag will make more efficient the loop */
			var howManyChecked = 0;
			/* lets make the array for the checkbox dropdown */
			for(var i = 0; i < arrayAmenitiesLength; i++){
				/* all unchecked */
				var amenityChecked = false;
				if(arraySessionAmenities.length >= howManyChecked ){
					/* check if the amenity its on the session, meaning it was checked */
					for (var c = 0; c < arraySessionAmenities.length; c++) {
						if (BAPI.config().amenity.values[i].Data == arraySessionAmenities[c]){
							/*checked because its on the session*/
							amenityChecked = true;
							howManyChecked = howManyChecked + 1;
						}
					}
				}
				/* we created the object */
				var amenity = {"id":BAPI.config().amenity.values[i].Data,"label":BAPI.config().amenity.values[i].Label,"isChecked":amenityChecked};
				/* we put it in the array */
				arrayAmenities.push(amenity);
			}
		}else{
			for(var i = 0; i < arrayAmenitiesLength; i++){
			var amenity = {"id":BAPI.config().amenity.values[i].Data,"label":BAPI.config().amenity.values[i].Label};
			arrayAmenities.push(amenity);
			}
		}
		$("#amenitiesDropdownCheckbox").dropdownCheckbox({
		  data: arrayAmenities,
		  showNbSelected:true,
		  templateButton: '<button class="dropdown-checkbox-toggle btn" data-toggle="dropdown">'+BAPI.config().amenity.prompt+' <span class="dropdown-checkbox-nbselected"></span><b class="caret"></b></button>'
		});
	}

	// load the session to the form
	loadFormFromSession(BAPI.session.searchparams);

	// setup date pickers
	context.createDatePicker('#searchcheckin', { "property": p, "checkoutID": '#searchcheckout' });
	context.createDatePicker('#searchcheckout', { "property": p, "checkinID": '#searchcheckin' });

	$(".bapi-locationsearch").live("focus", function() {
		$(this).typeaheadmap({
			"source": BAPI.config().city.values,
			"key": "Label",
			"value": "Data",
			"displayer": function(that, item, highlighted) {
				return highlighted;
			}
		});
	});
	$(".bapi-malocationsearch").live("focus", function() {
		$(this).typeaheadmap({
			"source": BAPI.config().location.values,
			"key": "Label",
			"value": "Data",
			"updater": function( item ) {
				this.$element.attr( 'data-value', this.$menu.find('.active').attr('data-value') );
				return item;
			},
			"displayer": function(that, item, highlighted) {
				return highlighted;
			}
		});
	});
	$(".sessionheadline").live("focus", function() {
		$(this).typeaheadmap({
			"source": BAPI.config().headline.values,
			"key": "Label",
			"value": "Label",
			"displayer": function(that, item, highlighted) {
				return highlighted;
			}
		});
	});
	// handle user clicking Search
	$(".quicksearch-dosearch").on("click", function (e) { console.log('do quick search');
		e.preventDefault();

	    BAPI.log("+quicksearch-dosearch handler", 2);
		$(targetid).block({ message: "<img src='" + loadingImgUrl + "' />" });
		var reqdata = saveFormToSession(this, options);
		BAPI.log(BAPI.session);
		if (doSearchCallback) { BAPI.log("Calling searchcallback",2); doSearchCallback(); }
		if (!BAPI.isempty(options.searchurl)) {
			var rurl = options.searchurl;
			if (rurl[rurl.length-1]!='/') { rurl = rurl + '/'; }
			// Check if the form in embed into an iframe
			if( window != window.parent ) {
				// Hack: some browser support cross domain cookies (chrome, firefox) some don't (IE, safarie) let's check if the cookie is set.
				// If the browser used by the client doesn't accept cross domain cookies, let's send everything into get parameters
				if(
					'string' !== $.type( $.cookie( 'BAPI2' ) ) &&
					'object' === $.type( reqdata )
				) {
					rurl += '?search=1'; // Adding this parameters, tell the page displaying result to disregard session, and overwite it with this information sent through get
					$.each(
						reqdata,
						function( key, value ) {
							if(
								'string' !== $.type( key ) ||
								!key.length
							) {
								return;
							}

							if(
								(
									'string' === $.type( value ) &&
									value.length > 0
								) ||
								'number' === $.type( value )
							) {
								rurl += '&' + encodeURIComponent( key ) + '=' + encodeURIComponent( value );
								return;
							}

							if( 'object' === $.type( value ) ) {
								$.each(
									value,
									function( sub_key, sub_value ) {
										if(
											'string' !== $.type( sub_key ) ||
											!sub_key.length ||
											'string' !== $.type( sub_value ) ||
											!sub_value.length
										) {
											return;
										}
										rurl += '&' + encodeURIComponent( key + '[' + sub_key + ']' ) + '=' + encodeURIComponent( sub_value );
									}
								);
								return;
							}
						}
					);
				}
				window.open( rurl );
				$(targetid).unblock();
			}
			else {
				window.location.href = rurl;
			}
		}
		else { $(targetid).unblock(); }
	});

	// handle user clicking Clear (not home page)
	$(".widget_bapi__search .quicksearch-doclear").on("click", function() {
		$(targetid).block({ message: "<img src='" + loadingImgUrl + "' />" });
		BAPI.clearsession();
		if (doSearchCallback) { doSearchCallback(); }
		$('.' + options.dataselector).val('');
		if( $('#amenitiesDropdownCheckbox').length ) {
			uncheck_dropdown_checkbox( $('#amenitiesDropdownCheckbox') );
		}
		if (!BAPI.isempty(options.searchurl)) {
			BAPI.savesession();
			var rurl = options.searchurl;
			if (rurl[rurl.length-1]!='/') { rurl = rurl + '/'; }
			window.location.href = rurl;
		}
	});

	// handle user clicking Clear on home page
	$(".widget_bapi_hp_search .quicksearch-doclear").on("click", function() {
		BAPI.clearsession();
		if (doSearchCallback) { doSearchCallback(); }
		$('.' + options.dataselector).val('');
		if( $('#amenitiesDropdownCheckbox').length ) {
			uncheck_dropdown_checkbox( $('#amenitiesDropdownCheckbox') );
		}
	});

	$(".quicksearch-doadvanced").on("click", function() {
		$(targetid).block({ message: "<img src='" + loadingImgUrl + "' />" });
		var reqdata = saveFormToSession(this, options);
		$(targetid).unblock();
	});

	// This function unchecked every element from dropdown checkbox element.
	function uncheck_dropdown_checkbox( dorpdown_element ) {
		var data =
		dorpdown_element.dropdownCheckbox(
			'reset',
			$.each(
				dorpdown_element.dropdownCheckbox('items'),
				function(index, item)
				{
					item.isChecked=false;
				}
			)
		);
	}
}

/*
	Group: Summary
*/
context.createSummaryWidget = function (targetid, options, callback) { 
	console.log('creating summary widget');
	var pagesize = 10;//default 10
	if(options.entity == "poi"){pagesize = 9;
	/* lets add the used types if its enabled*/
		if(typeof(BAPI.config().haspoitypefilter) !== 'undefined' && BAPI.config().haspoitypefilter.enabled){
			BAPI.search(BAPI.entities.poi, {"usedTypes":"true"}, function (data) {
				BAPI.config().haspoitypefilter.values = data.result;
			});
		}
	}
	options = initOptions(options, pagesize, 'tmpl-base-summary');
	if (options.log) { BAPI.log("--options--"); BAPI.log(options); }
	if (options.template===null) { BAPI.log("Undefined template for " + targetid); }
	var ids=[], alldata=[];
	context.loading.show();

	// Case for the Wishlist page
	if(options.usemylist) {
		ids = [];
		$.each(BAPI.session.mylist, function (index, item) {
			ids.push(parseInt(item.ID));
		});
		if (options.entity == BAPI.entities.property) {
			options.searchoptions = $.extend({}, BAPI.session.searchparams, options.searchoptions);
		}
		doSearch(targetid, ids, options.entity, options, alldata, callback);
	}
	else {
		// see if we should ignore the session for the initial search
		if (options.entity == BAPI.entities.property && options.ignoresession!='1') {

			options.searchoptions = $.extend({}, BAPI.session.searchparams, options.searchoptions);
		}
		if (BAPI.isempty(options.entity)) {
			BAPI.log("Invalid entityid specified for " + targetid);
			$(targetid).text("Invalid configuration");
			return;
		}

		/* the sort options need to be in searchparams to be honored */
		// if(options.entity == "property" && options.ignoreconfigsort != '1' && options.config.sort !== null && typeof(options.config.sort) !== 'undefined'){
		// 	options.searchoptions.sort = options.config.sort;
		// 	options.searchoptions.sortdesc = options.config.sortdesc;
		// }
		if(!options.searchoptions.sort) { options.config.sort }
		if(!options.searchoptions.sortdesc) { options.config.sortdesc }



		//we check if the IDs were provided
		if(typeof(options.searchoptions.ids) != 'undefined' && options.searchoptions.ids.length > 0){
			theIDs = options.searchoptions.ids;
			tmpIDsArray = [];
			//we extract the IDs only
			for( var i=0; i<theIDs.length; i++ ) {
				tmpIDsArray.push(theIDs[i]["id"]);
			}
			options.searchoptions.ids = tmpIDsArray;
			//we render the Properties with the ID provided
			if (options.log) { BAPI.log("--search result--"); BAPI.log(data); }
			ids = options.searchoptions.ids;
			// for the actual retrieval of the records, we want to include the context
			if (options.entity == BAPI.entities.property && !$(targetid).parent().hasClass('widget_bapi_featured_properties')) {
				options.searchoptions = $.extend({}, BAPI.session.searchparams, options.searchoptions);
			}
			doSearch(targetid, ids, options.entity, options, alldata, callback);
		}else{

			options.searchoptions = cleanupDuplicatesFromSearchOptions(options.searchoptions);

			if(options.entity != 'property'){
				options.searchoptions.city = null; //we don't want to filter out other location-aware entities
			}

			//IDs not provided so we need to search them
			BAPI.search(options.entity, options.searchoptions, function (data) {
				if (options.log) { BAPI.log("--search result--"); BAPI.log(data); }
				ids = data.result;

				// for the actual retrieval of the records, we want to include the context
				if (options.entity == BAPI.entities.property && !$(targetid).parent().hasClass('widget_bapi_featured_properties')) {
					options.searchoptions = $.extend({}, BAPI.session.searchparams, options.searchoptions);
				}
                                options.searchoptions = cleanupDuplicatesFromSearchOptions(options.searchoptions);
				doSearch(targetid, ids, options.entity, options, alldata, callback);
			});
		}
		$("#poitypefilter-dpd").die( "change" );
		$("#poitypefilter-dpd").live("change", function () {
			var poiTypeFilterDpd = $(this);
			$(targetid).data("poitypeselected",poiTypeFilterDpd.val());
			options.searchoptions.type = poiTypeFilterDpd.val();
			BAPI.search(options.entity, options.searchoptions, function (data) {
				if (options.log) { BAPI.log("--search result--"); BAPI.log(data); }
				options.searchoptions.page = 1;
				ids = data.result;
				doSearch(targetid, ids, options.entity, options, [], callback);
			});
		});
	}
}

/*
	Group: Properties
*/
context.createAvailabilityWidget = function (targetid, data, options) {
    if (typeof (options) === "undefined" || options === null) { options = new Object(); }
	if (typeof (options.availcalendarmonths) === "undefined" || options.availcalendarmonths === null) { options.availcalendarmonths = 6; }
	if (typeof (options.minbookingdays) === "undefined" || options.minbookingdays === null) { options.minbookingdays = 0; }
	if (typeof (options.maxbookingdays) === "undefined" || options.maxbookingdays === null) {
		//based on months now
	options.maxbookingdays = BAPI.config().maxbookingdays; }
	var supLangs=["af", "ar-DZ", "ar", "az", "be", "bg", "bs", "ca", "cs", "cy-GB", "da", "de", "el","en-AU", "en-GB",
	"en-NZ", "en", "eo", "es", "et", "eu", "fa", "fi", "fo", "fr-CA", "fr-CH", "fr","gl", "he", "hi", "hr", "hu",
	"hy", "id", "is", "it", "ja", "ka", "kk", "km", "ko", "ky","lb", "lt", "lv", "mk", "ml", "ms", "nb", "nl-BE", "nl",
	"nn", "no", "pl", "pt-BR", "pt","rm", "ro", "ru", "sk", "sl", "sq", "sr-SR", "sr", "sv", "ta", "th", "tj", "tr", "uk", "vi",
	"zh-CN", "zh-HK", "zh-TW", "zh-CHT"];

	if (typeof (options.languageISO) === "undefined" || options.languageISO === null) { options.languageISO = BAPI.site.language;}
	if (typeof(options.numinrow)==="undefined" || options.numinrow===null || options.numinrows<=0) { options.numinrow = 1; }
	options.numberOfMonths = [ Math.ceil(options.availcalendarmonths / options.numinrow), options.numinrow ];

	var lang=options.languageISO;
	if(lang == "pt-PT") {lang = "pt-BR"};
    if (lang == "en-IE") {lang = "en-GB"};
	if(supLangs.indexOf(lang)==-1 && lang.length==5){
        lang=lang.substring(0,2);
    }
   if(supLangs.indexOf(lang)==-1){
        lang='en-US';
    }
	var p = data.result[0];
	if (lang=='en' || lang=="en-US" ) {
		$.datepicker.setDefaults( $.datepicker.regional[''] );
	}else{
		$.datepicker.setDefaults( $.datepicker.regional[lang] );
	}
	/* Optimizing the availability array contiguos dates can be merged so they are not separated items in the array making the array smaller */
	var notavailableArrayDays = [];
	if (typeof(p)!=='undefined' && typeof(p.ContextData)!=='undefined' && typeof(p.ContextData.Availability)!=='undefined') {
		notavailableArrayDays = mergeContiguosDates(p.ContextData.Availability);
	}
	$(targetid).datepicker({
		numberOfMonths: options.numberOfMonths,
		minDate: options.minbookingdays,
		maxDate: "+" + options.maxbookingdays + "D",
		createButton: false,
		onSelect: function(date) {
			$("#rateblockcheckin").attr('setval', date).focus();
		},
		beforeShowDay: function (date) {
			if (notavailableArrayDays.length == 0) {
				return [true, "avail"];
			}
			var tdate = date;
			var bavail = true;
			var itscheckin = false;
			var itscheckout = false;
			function checkDateSetFlags(item, index, array) {
				var cin = new Date(item.SCheckIn);
                var cout = new Date(item.SCheckOut);
				if (tdate>cin && tdate<cout) {
					bavail = false;
				}
				/* its check in ? */
				if(cin-tdate==0){
					itscheckin = true;
				}
				/* its check out ? */
				if(cout-tdate==0){
					itscheckout = true;
				}
			}
			notavailableArrayDays.forEach(checkDateSetFlags);
			if (bavail && !itscheckin && !itscheckout) { return [true, "avail", "Available"]; }
			else if(itscheckout) { return [true, "checkInDate", "Check-in Only"]; }
			else if(itscheckin) { return [true, "checkOutDate", "Check-out Only"]; }
			else { return [false, "datepicker-notavailable", "Unavailable"]; }
		}
	});
}

function mergeContiguosDates(ArrayOfBookings){
	var mergedBookings = [];
	function populateNotAvailableDaysArray(item, index, array)
	{
		if (mergedBookings.length == 0)
		{
			mergedBookings.push(item);
		}else{
			var theLastItem = mergedBookings.length-1;
			/* if the check out of the previous booking is the same as the check in of the current booking merge */
			if(mergedBookings[theLastItem].SCheckOut == item.SCheckIn)
			{
				mergedBookings[theLastItem].SCheckOut = item.SCheckOut;
				mergedBookings[theLastItem].CheckOut = item.CheckOut;
			}else{
				mergedBookings.push(item);
			}
		}
	}
	if(typeof(ArrayOfBookings)!=='undefined' && ArrayOfBookings !== null){
		ArrayOfBookings.forEach(populateNotAvailableDaysArray);
	}
	return mergedBookings;
}

context.createSimilarPropertiesWidget = function (targetid, pid, options) {
	options = initOptions(options, 3, 'tmpl-featuredproperties');
	context.loading.ctlshow(targetid);
	var poptions = {
		"checkin": options.checkin,
		"checkout": options.checkout,
		"similarto": pid,
		"pagesize": options.pagesize,
		"seo": 1
	};
	BAPI.get(pid, BAPI.entities.property, poptions, function(data) {
		data.config = BAPI.config();
		data.textdata = BAPI.textdata;
		$(targetid).html(Mustache.to_html(options.template, data));
	});
	/* Load the rating function */
	context.inithelpers.loadRatingStars();
}

context.createFeaturedPropertiesWidget = function (targetid, options) {
	options = initOptions(options, 3, 'tmpl-featuredproperties-horiz');
	context.loading.ctlshow(targetid);
	BAPI.search(BAPI.entities.property, { sort: "random" }, function (data) {
		var pids = data.result;
		BAPI.get(pids, BAPI.entities.property, { pagesize: options.pagesize, seo: true }, function (res) {
			res.textdata = options.textdata;
			$(targetid).html(context.mustacheHelpers.render(options.template, res));
		});
	});
}

/* Lead Request */
context.createInquiryForm = function (targetid, options) {
	options = initOptions(options, 1, 'tmpl-leadrequestform-propertyinquiry');
	if (typeof (options.submitbuttonselector) === "undefined" || options.submitbuttonselector == null) { options.submitbuttonselector = 'doleadrequest'; }
	if (typeof (options.responseurl) === "undefined" || options.responseurl == null) { options.responseurl = '' }
	/* we check if we got the InquiryFormFields */
	if (typeof (options.InquiryFormFields) === "undefined" || options.InquiryFormFields == null) { options.InquiryFormFields = '' }

	if (options.dologging==1) { BAPI.log("--- Inquiry Form---"); BAPI.log("-> Options"); BAPI.log(options); }
	context.loading.ctlshow(targetid);
	/* we add the InquiryFormFields object to data so the values can get into account when rendereing the mustache template */
	var data = { "config": options.config, "site": options.site, "textdata": options.textdata, "InquiryFormFields": options.InquiryFormFields }
	$(targetid).html(context.mustacheHelpers.render(options.template, data));
	/* do we have the date fields ? */
	if(options.InquiryFormFields.Dates){
		/* lets attach the datepickers */
		$('#txtCheckIn').addClass('datepickercheckin');
		$('#txtCheckOut').addClass('datepickercheckout');
		/* check if we are in a property detail page so we use the availability for the calendars */
		if($('.property-detail-page').length > 0 && BAPI.curentity.entity == "property" )
		{
			BAPI.datamanager.get(BAPI.entities.property, BAPI.curentity.ID, function(p) {
				context.createDatePicker('#txtCheckIn', { "property": p,"checkoutID": '#txtCheckOut' });
				context.createDatePicker('#txtCheckOut', { "property": p,"checkinID": '#txtCheckIn' });
			});
		}else{
			context.createDatePicker('#txtCheckIn', {"checkoutID": '#txtCheckOut' });
			context.createDatePicker('#txtCheckOut', {"checkinID": '#txtCheckIn' });
		}
	}

	$('.specialform').hide(); // hide the spam control

	var processing = false;
	$(".doleadrequest").on("click", function () { console.log(options);
		var that = $(this);

		$("#event-success, #event-error").remove();

		BAPI.log("Processing lead request");
		if (processing) { return; } // already in here
		/* block the Inquiry form */
		$(targetid).block({ message: "<img src='" + loadingImgUrl + "' />" });
		/* get all the required fields */
		var reqfields = $.extend([],$(this).parents('fieldset').find('.required'));

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

		var selname = $(this).attr('data-field-selector');
		var reqdata = { "checkin": options.checkin, "checkout": options.checkout };
		if(
			$.isPlainObject(BAPI) &&
			$.isPlainObject(BAPI.curentity) &&
			"string" === $.type(BAPI.curentity.entity) &&
			"property" === BAPI.curentity.entity
		) {
			reqdata.pid = BAPI.curentity.ID
		}
		reqdata = $.extend({}, reqdata, BAPI.session.searchparams);

		/* we get the date formats */
		var dfparse = BAPI.defaultOptions.dateFormatMoment();
		var df = BAPI.defaultOptions.dateFormatBAPI;

		$('.' + selname).each(function() {
			var k = $(this).attr('data-field');
			var v = $(this).val();
			if (k == 'special') {
				if (v.length > 0) {
					window.location.href = options.responseurl + '?special=1';
					return; // special textbox has a value, not a real person
				}
			}
			else if (k != null && k.length>0) {
				/* we check if this is checkin */
				if (k=="checkin") {
					/* we change the date format to the BAPI date format */
					v = (typeof(v) == 'undefined' || v === null || v == '') ? null : moment(v, dfparse).format(df);
				}/* we check if this is checkout */
				else if (k=="checkout") {
					/* we change the date format to the BAPI date format */
					v = (typeof(v) == 'undefined' || v === null || v == '') ? null : moment(v, dfparse).format(df);
				}
				reqdata[k] = v;
			}
		});

		if (options.dologging==1) { BAPI.log("-> Request Data"); BAPI.log(reqdata); }
		if(reqdata.checkin !== null && reqdata.checkin != '' && typeof(reqdata.checkin) !== 'undefined'){
			var today = moment().subtract('days', 1);
			var selectedDate = moment(reqdata.checkin);
			if(selectedDate.diff(today, 'days') < 0){
				alert("Invalid Date");
				if($("#txtCheckIn").length > 0){
					$("#txtCheckIn").focus();
				}
				$(targetid).unblock();
				processing = false;
				return;
			}
		}

		var error = true;
		var timer;
		function displayError() {
			$(targetid).unblock();
			processing = false;
			that.before('<div id="event-error" class="alert alert-warning">'+BAPI.textdata['Please try again later']+'</div>');
		}

		function makeRequest(callback) {
			console.log('makeRequest');
			timer = setTimeout(function(){ displayError(); }, 10000);

			BAPI.createevent(reqdata, function(edata) {
				if (options.dologging==1) { BAPI.log("-> Response Data"); BAPI.log(edata); }

				console.log(edata);
				if(edata.error) {
					console.log('there was an error');
					return false;
				} else {
					error = false;
				}

				callback();

				$("#event-error").remove();

				if (options.responseurl == '') {
					$(targetid).unblock();
					processing = false;
					/* Execute google adwords code if exists */
					if ( typeof googleConversionTrack == 'function' ) { googleConversionTrack(); }
					that.before('<div id="event-success" class="alert alert-success">'+BAPI.textdata['Thank you, your request has been submitted.']+'</div>');
					$('.' + selname).val('');
				}
				else { window.location.href = options.responseurl + '?personid=' + edata.result.Lead.ID; }
			});
		}

		makeRequest(function() {
			clearTimeout(timer);
			if(error) {
				clearTimeout(timer);
				displayError();
			}
		});

	});
}
//Copy the dates from the rateblock to the inquiry form below
function copyRateBlockDataToInquiryForm(theInquiryForm){
	var ratechkinDate = $("#rateblockcheckin").data("pickadate").get('select');
	var ratechkoutDate = $("#rateblockcheckout").data("pickadate").get('select');
	if($("#"+theInquiryForm+" #txtCheckIn").length > 0){
		if(ratechkinDate !== null){
			$("#"+theInquiryForm+" #txtCheckIn").data("pickadate").set('select',ratechkinDate.pick);
		}else{
			$("#"+theInquiryForm+" #txtCheckIn").data("pickadate").clear();
		}
	}
	if($("#"+theInquiryForm+" #txtCheckOut").length > 0){
		if(ratechkoutDate !== null){
		$("#"+theInquiryForm+" #txtCheckOut").data("pickadate").set('select',ratechkoutDate.pick);
		}else{
			$("#"+theInquiryForm+" #txtCheckOut").data("pickadate").clear();
		}
	}
	copyRateBlockAdultChildrenFields(theInquiryForm)
}
//copy the adult and children values to the inquiry form below
function copyRateBlockAdultChildrenFields(theInquiryForm){
	//if($("#"+theInquiryForm+" #txtAdults").length > 0 ){$("#"+theInquiryForm+" #txtAdults").val($("#book-block .adultsfield").val());}
	//if($("#"+theInquiryForm+" #txtChildren").length > 0){$("#"+theInquiryForm+" #txtChildren").val($("#book-block .childrenfield").val());}
}

// Create an array from the date while parsing each date unit as an integer
function createDateArray( date ) {
    return date.split( '-' ).map(function( value ) { return +value })
}
/*
	Group: DatePickers
*/
function InitDatePickerOptions(options) {
	if (typeof (options) === "undefined" || options == null) { options = {}; }
	if (typeof (options.datepicker) === "undefined") { options.datepicker = {}; }
	if (typeof (options.datepicker.showOn) === "undefined") { options.datepicker.showOn = 'both'; }
	//if (typeof (options.datepicker.buttonImage) === "undefined") { options.datepicker.buttonImage = '//booktplatform.s3.amazonaws.com/App_SharedStyles/images/checkInBtn.png'; }
	//if (typeof (options.datepicker.buttonImageOnly) === "undefined") { options.datepicker.buttonImageOnly = true; }
	if (typeof (options.datepicker.numberOfMonths) === "undefined") { options.datepicker.numberOfMonths = 2; }
	if (typeof (options.datepicker.minDate) === "undefined") { options.datepicker.minDate = BAPI.config().minbookingdays; }
	if (typeof (options.datepicker.maxDate) === "undefined") { options.datepicker.maxDate = "+" + BAPI.config().maxbookingdays + "D"; }
	if (typeof (options.minlos) === "undefined") { options.minlos = BAPI.config().minlos; }
	if (typeof (options.languageISO) === "undefined") { options.languageISO = BAPI.defaultOptions.languageISO; }
	if (typeof (options.property) === "undefined") { options.property = null; }
	return options;
}

function createDatePickerJQuery(targetid, options) {
	options = InitDatePickerOptions(options);
	if (options.languageISO=='en' && options.language!='en-AU' && options.language!='en-GB' && options.language!='en-NZ') {
		$.datepicker.setDefaults( $.datepicker.regional[''] );
	}
	else {
		$.datepicker.setDefaults( $.datepicker.regional[options.languageISO] );
	}
	var p = options.property;
	options.datepicker.beforeShowDay = function (date) {
		if (p===null || p.ContextData===null || p.ContextData.Availability===null) {
			return [true, "avail"];
		}
		var tdate = moment(date);
		var bavail = true;
		$.each(p.ContextData.Availability, function (index, item) {
			var cin = moment(item.CheckIn);
			var cout = moment(item.CheckOut);
			if ((tdate.isSame(cin) || tdate.isAfter(cin)) && tdate.isBefore(cout)) {
				bavail = false;
			}
		});
		if (bavail) { return [true, "avail", "Available"]; }
		else { return [false, "unavail", "Unavailable"]; }
	}

	if (!(typeof (options.checkoutID) === "undefined")) {
		options.datepicker.onSelect = function(dateText, inst) {
		BAPI.log('--datepicker onSelect--');
			var df = 'd mmm, yyyy'; //BAPI.defaultOptions.dateFormatBAPI;
			var dpcheckout = $(options.checkoutID);
			var dpcheckin = $(this);
			var mind = moment(BAPI.config().minbookingdate);
			var maxd = moment(BAPI.config().maxbookingdate);

			var selcheckin = dpcheckin.datepicker('getDate');
			var selcheckout = dpcheckout.datepicker('getDate');
			var checkin = moment(selcheckin);
			var checkout = (selcheckout===null||selcheckout=='') ? moment(0) : moment(selcheckout);
			BAPI.log('->checkin=' + checkin.format(df) + ', checkout=' + checkout.format(df));
			if (checkout.isBefore(checkin)) {
				checkout = checkin.add('days', BAPI.config().minlos);
				//dpcheckout.datepicker('option', 'minDate', checkin.toDate());
				dpcheckout.datepicker('setDate', checkout.toDate());
				BAPI.log('->setting checkout to ' + checkout.format(df) + ', min date set to ' + checkin.format(df));
			}
		}
	}

	var trigger = $('<span>', { "class": "halflings calendar cal-icon-trigger" });
	trigger.append("<i>");
	$(targetid).after(trigger);

	options.datepicker.buttonImage = null;
	options.datepicker.buttonImageOnly = false;
	options.datepicker.showOn = 'focus';
	$(targetid).datepicker(options.datepicker);

	trigger.click(function() {
		BAPI.log("datepicker trigger");
		$(targetid).datepicker("show");
	});
}

function createDatePickerPickadate(targetid, options) {
	options = InitDatePickerOptions(options);
	/* default options for the pickadate calendar - English */
	if(typeof(options.pickadatetranslate)==="undefined" || options.pickadatetranslate===null){
	options.pickadatetranslate = {monthsFull: [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ],monthsShort: [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ],weekdaysFull: [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ],weekdaysShort: [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ],today: 'Today',clear: 'Clear'};
	}
	var p = options.property;
	var ctl = $(targetid);
	ctl.addClass("no-disabled");
	var checkinpickers = $(targetid+'.datepickercheckin'); 
	var checkoutpickers = $(targetid+'.datepickercheckout');
	var theCheckOutElement = $(options.checkoutID);
	var theCheckInElement = $(options.checkinID);
	var mind = true; if (BAPI.config().minbookingdays>0) { mind = BAPI.config().minbookingdays; }
	var cinblockouts = [];
	var coutblockouts=[];
	/* lets mirror the adults and children fields */
	if(p !== null){
		if(!p.IsBookable){
		var theInquiryForm = $('aside.span3 .widget_bapi_inquiry_form').attr('id');
		$("#book-block .adultsfield").on("keyup blur",function(){copyRateBlockAdultChildrenFields(theInquiryForm)});
		$("#book-block .childrenfield").on("keyup blur",function(){copyRateBlockAdultChildrenFields(theInquiryForm)});
		}
	}
	/* lets get the increment by default at least 1 night */
	var addDay = 1;
	if(options.property !== null && typeof(options.property.MinStay) !== 'undefined' && options.property.MinStay != 0){
		addDay = options.property.MinStay;
	}else{
		if(BAPI.config().los.minval !== null && typeof(BAPI.config().los.minval) !== 'undefined' && BAPI.config().los.minval != 0){
			addDay = BAPI.config().los.minval;
		}
	}

	/* check if we have the arrays already calculated and that they are for the property we are looking at */
	if(typeof(BAPI.site.currentproperty) !== "undefined" && typeof(BAPI.site.currentproperty[p.ID]) !== "undefined" ){
		/* arrays are already in the BAPI object just grab them lets not calculate them again */
		cinblockouts = BAPI.site.currentproperty[p.ID][0];
		coutblockouts = BAPI.site.currentproperty[p.ID][1];
	}else{
		/* the arrays dont exist yet lets make them */
		if (!BAPI.isempty(p) && !BAPI.isempty(p.ContextData) && !BAPI.isempty(p.ContextData.Availability)) {
			var mergedAvailability = [];
			mergedAvailability = mergeContiguosDates(p.ContextData.Availability);
			$.each(mergedAvailability, function (index, item) {
				var cin = moment(item.CheckIn);
				var cout = moment(item.CheckOut);
				//don't include the checkin as block out for checking out, people can check out on a day of check in
				var cinForCheckOut = moment(item.CheckIn).add('d', 1);
				//don't include the checkout as block out for checking in, people can check in on a day of check out
				var coutForCheckIn = moment(item.CheckOut).subtract('d', 1);
				//format the ranges for the pickadate
				cinblockouts.push({ from: [cin.year(),cin.month(),cin.date()], to: [coutForCheckIn.year(),coutForCheckIn.month(),coutForCheckIn.date()] });
				coutblockouts.push({ from: [cinForCheckOut.year(),cinForCheckOut.month(),cinForCheckOut.date()], to: [cout.year(),cout.month(),cout.date()] });
			});
			/* lets save the arrays in the BAPI object so they are not recreated for every datepicker */
			BAPI.site.currentproperty = {};
			BAPI.site.currentproperty[p.ID] = [cinblockouts,coutblockouts];
		}
	}
	//BAPI.log(blockouts);
	var poptions = {};
	if (ctl.hasClass("datepickercheckin")) {
		poptions = {
			monthsFull: options.pickadatetranslate.monthsFull,
			monthsShort: options.pickadatetranslate.monthsShort,
			weekdaysFull: options.pickadatetranslate.weekdaysFull,
			weekdaysShort: options.pickadatetranslate.weekdaysShort,
			today: options.pickadatetranslate.today,
			clear: options.pickadatetranslate.clear,
			min: mind,
			max: BAPI.config().maxbookingdays,
			disable: cinblockouts,
			format: BAPI.defaultOptions.dateFormat.toLowerCase(),
			formatSubmit: BAPI.defaultOptions.dateFormatBAPI.toLowerCase(),
			klass: {
				dayDisabled: 'datepicker-notavailable',
				dayToday: 'datepicker-today',
				daySelected: 'datepicker-selected'
			},
			onOpen: function() {},
			onSet: function(event) {
				//we check if there is a checkout field
				if (theCheckOutElement!==null && theCheckOutElement.length>0) {
					//was this a select event?
					if ( event.select ) {
						//this is a select event lets set the checkout field
						var theChkOutDate = moment(event.select).add(addDay, 'days').format(BAPI.defaultOptions.dateFormat.toUpperCase());

						theCheckOutElement.data('pickadate').set('select', theChkOutDate);
						
						//setting the min date for the checkout field
						//var theMinforCheckOut = moment(event.select, BAPI.defaultOptions.dateFormat.toUpperCase()).add(1, 'days').format(BAPI.defaultOptions.dateFormat.toUpperCase());
						theCheckOutElement.data('pickadate').set( 'min', moment(event.select).add(1, 'days').toArray() );
						theCheckOutElement.data('pickadate').open();
					}
					else if ( 'clear' in event ) {
						//this is a clear event remove the min date from the checkout field
						theCheckOutElement.data('pickadate').set('min', false);
					}
				}
				// if los is enabled and it doesn't have a value, set it to the default
				var slos = $('.sessionlos');
				if (BAPI.config().los.enabled && slos!==null && slos.length>0) {
					if (slos.val()===null || slos.val()=='') {
						slos.val(BAPI.config().los.defaultval);
					}
				}
				/* when selecting a date update the pickadate below */
				if(targetid == "#rateblockcheckin"){
					/* lets get the inquiry form below this rate block */
					if(!p.IsBookable){
						var theInquiryForm = $('aside.span3 .widget_bapi_inquiry_form').attr('id');
						copyRateBlockDataToInquiryForm(theInquiryForm);
					}
				}
			}
		}
	}
	else if (ctl.hasClass("datepickercheckout")) {
		poptions = {
			monthsFull: options.pickadatetranslate.monthsFull,
			monthsShort: options.pickadatetranslate.monthsShort,
			weekdaysFull: options.pickadatetranslate.weekdaysFull,
			weekdaysShort: options.pickadatetranslate.weekdaysShort,
			today: options.pickadatetranslate.today,
			clear: options.pickadatetranslate.clear,
			min: mind,
			max: BAPI.config().maxbookingdays,
			disable: coutblockouts,
			format: BAPI.defaultOptions.dateFormat.toLowerCase(),
			formatSubmit: BAPI.defaultOptions.dateFormatBAPI.toLowerCase(),
			klass: {
				dayDisabled: 'datepicker-notavailable',
				dayToday: 'datepicker-today',
				daySelected: 'datepicker-selected'
			},

			onSet: function(event) {
				/* when selecting a date update the pickadate below */
				if(targetid == "#rateblockcheckout"){
					/* lets get the inquiry form below this rate block */
					if(!p.IsBookable){
						var theInquiryForm = $('aside.span3 .widget_bapi_inquiry_form').attr('id');
						copyRateBlockDataToInquiryForm(theInquiryForm);
					}
				}
			}
		}
	}
	//BAPI.log(poptions);
	if(options.container) { poptions.container = options.container; }

	var input = $(targetid).pickadate(poptions);
		var trigger = '<span class="halflings calendar cal-icon-trigger"><i></i></span>';
		$(targetid).after(trigger);
		$(targetid).next('.cal-icon-trigger').on('click', function(e) {
			$(targetid).data('pickadate').open();
			/* cancelling the bubbling event to prevent the closing of the picker */
			return false;
		});

	// Create an array from the date while parsing each date unit as an integer
	function createDateArray( date ) { return date.split( '-' ).map(function( value ) { return +value }) }
}

context.createDatePicker = function (targetid, options) {
	var islegacy = (!$.support.leadingWhitespace);
    if (!context.newdatepicker || islegacy) {
		createDatePickerJQuery(targetid, options);
	}
	else {
		var url = context.jsroot + 'js/pickadate/source/pickadate.min.js';
		var cssurl = context.jsroot + 'js/pickadate/themes/pickadate.01.default.css';
		//$("<link/>", { rel: "stylesheet", type: "text/css", href: cssurl }).appendTo("head");
		/* we check if the file is already loaded in the context */
		if(typeof(BAPI.UI.pickadatetranslate)==="undefined" || BAPI.UI.pickadatetranslate===null){
			/* the file is not present lets load it */
			$.getScript(context.jsroot + "bapi/bapi.ui.pickadate.translate.js", function (data, ts, jqxhr) {
				options.pickadatetranslate = BAPI.UI.pickadatetranslate[BAPI.defaultOptions.languageISO];
				createDatePickerPickadate(targetid, options);
			});
		}else{
			/* the file is present */
			options.pickadatetranslate = BAPI.UI.pickadatetranslate[BAPI.defaultOptions.languageISO];
			createDatePickerPickadate(targetid, options);
		}
		//$.ajax({url: url, dataType: 'script', cache: true, success: function() {});
		//$.getScript(url, function(data, textStatus, jqxhr) { createDatePickerPickadate(targetid, options); });
	}
}

/*
	Group: Booking
*/

context.secureurl = function(path) {
	if (window.location.host=="localhost" || window.location.host.indexOf('localdomain') >= 0) {
		return BAPI.site.url + path;
	}
	return "https://" + BAPI.site.secureurl + path;
}

context.nonsecureurl = function(path) {
	if (window.location.host=="localhost" || window.location.host.indexOf('localdomain') >= 0) {
		return BAPI.site.url + path;
	}
	return "http://" + BAPI.site.url + path;
}

var curbooking = null;
function bookingHelper_getFormData(options, booking) {
	var treqdata = {};
	treqdata.CheckIn = BAPI.isempty(booking.CheckIn) ? null : booking.CheckIn;
	treqdata.CheckOut = BAPI.isempty(booking.CheckOut) ? null : booking.CheckOut;
	treqdata.Coupon = BAPI.isempty(booking.Coupon) ? null : booking.Coupon;
	treqdata.CreditCard = BAPI.isempty(booking.CreditCard) ? null : booking.CreditCard;
	treqdata.NumAdults = BAPI.isempty(booking.NumAdults) ? null : booking.NumAdults;
	treqdata.NumChildren = BAPI.isempty(booking.NumChildren) ? null : booking.NumChildren;
	treqdata.PropertyID = BAPI.isempty(booking.PropertyID) ? null : booking.PropertyID;
	treqdata.Renter = BAPI.isempty(booking.Renter) ? null : booking.Renter;
	treqdata.Statement = {};
	treqdata.Statement.DueOn = booking.Statement.DueOn;
	treqdata.Statement.Details = booking.Statement.Details;
	treqdata.Statement.Total = booking.Statement.Total;
	treqdata.Statement.Notes = booking.Statement.Notes;
	treqdata.Statement.Currency = booking.Statement.Currency;
	treqdata.Statement.CheckSum = booking.Statement.CheckSum;
	treqdata.TotalDueNow = BAPI.isempty(booking.TotalDueNow) ? null : booking.TotalDueNow;
	var reqdata = treqdata;

	var dfparse = BAPI.defaultOptions.dateFormatMoment();
	var df = BAPI.defaultOptions.dateFormatBAPI;
	$('.' + options.dataselector).each(function () {
		var k = $(this).attr('data-field');
		var v = $(this).attr('data-value');
		if (v == null | v == '') v = $(this).val();
		if (k != null && k.length > 0) {
			if (k=="checkin") {
			    v = (v === null || v == '') ? null : moment(v, dfparse).format(df);
			}
			else if (k=="checkout") {
			    v = (v === null || v == '') ? null : moment(v, dfparse).format(df);
			}

			// assign to the req and the session
			var i = k.indexOf('[');
			if (i==-1) {
				reqdata[k] = v;
			} else {
				// special case when the data-attribute value has nested brackets (such as adults[min])
				var k1 = k.substring(0,i);
				var k2 = k.substring(i+1,k.length-1);
				reqdata[k1] = reqdata[k1] || {};
				reqdata[k1][k2] = v;
			}
		}
	});
	return reqdata;
}

function bookingHelper_DoRedirect(u) {
	var redir = u.param("redir"); 
	if (redir != "1") { return false; }


	// first time getting to the page, get values from querystring, svae to session and then redirect
	var checkin = u.param('checkin');
	var checkout = u.param('checkout');
	var adults = u.param('adults').min;
	var children = u.param('children').min;
	var los = u.param('los');
	var df = BAPI.defaultOptions.dateFormatBAPI;
	var dfParse = BAPI.defaultOptions.dateFormatMoment();
	var sp = BAPI.session.searchparams; 
	if(!sp) {
		sp = {};
		BAPI.session.searchparams = {};
	}
	if (typeof (checkin) !== "undefined" && checkin !== null) {
		try {
			sp.checkin = moment(checkin, df).format(df);
			sp.scheckin = moment(sp.checkin, df).format(BAPI.defaultOptions.dateFormat.toUpperCase()); //dfParse
			BAPI.session.searchparams.checkin = sp.checkin;
			BAPI.session.searchparams.scheckin = sp.scheckin;
		} catch(err){}
	}
	if (typeof (checkout) !== "undefined" && checkout !== null) {
		try {
			sp.checkout = moment(checkout, df).format(df);
			sp.scheckout = moment(sp.checkout, df).format(BAPI.defaultOptions.dateFormat.toUpperCase()); //dfParse
			BAPI.session.searchparams.checkout = sp.checkout;
			BAPI.session.searchparams.scheckout = sp.scheckout;
		} catch(err){}
	}

	if (typeof (los) !== "undefined" && los !== null) {
		sp.los = los;
		BAPI.session.searchparams.los = los;
	}

	if (BAPI.isempty(sp.adults)) {
		sp.adults = {'min':2};
		BAPI.session.searchparams.adults = {'min': 2};
	}
	if (typeof (adults) !== "undefined" && adults != null) {
		sp.adults.min = adults;
		BAPI.session.searchparams.adults.min = adults;
	}

	if (BAPI.isempty(sp.children)) {
		sp.children = {'min':0};
		BAPI.session.searchparams.children = {'min': 0};
	}
	if (typeof (children) !== "undefined" && children != null) {
		sp.children.min = children;
		BAPI.session.searchparams.children.min = children;
	}
	
	// var cookie = [];
	// $.each(BAPI.session.searchparams, function(i,v) {
	// 	cookie.push({'name':i, 'value':v});
	// });

	BAPI.savesession(); // save the session
	var propid = u.param('keyid');

	//return;

	console.log('before redirect');
	console.log(JSON.stringify(BAPI.session.searchparams));

	window.location.href = window.location.pathname + '?keyid=' + propid; // redirect to the same page minus the qs params
	return true;
}

function bookingHelper_get_description() {
	if( $('#statement-details a.load_desc').length < 1 ) {
		return;
	}

	// Build an object where keys are entities' name and value is an array of id. Ie. { 'entity_name1' = array(id1, id2), 'entity_name2' = array(id1, id2) ..}
	var entities = new Object();
	$('#statement-details a.load_desc').each(
		function() {
			// All link are hidden until we receive or not a description
			$(this).hide();

			if( !$.isArray( entities[ $(this).attr('data-entity') ] )  ) {
				entities[ $(this).attr('data-entity') ] = new Array( $(this).attr('data-related-id') );
			}
			else {
				entities[ $(this).attr('data-entity') ].push( $(this).attr('data-related-id') )
			}
		}
	);

	// This should only occur if the template is not correctly displaying data-entity and data-related-id
	if( entities.length < 1 ) {
		return;
	}

	$.each(
		entities,
		function( entity, ids ) {
			BAPI.get(
				ids,
				entity,
				{ 'page' : 1, 'pagesize' : ids.length }, // Default value of pagesize is 5 so it is need to be hable to receive as much response as ids are passed.
				function ( data ) {
					if(
						!$.isPlainObject(data) ||
						$.isPlainObject(data.error) ||
						!$.isArray(data.result) ||
						data.result.length < 1
					) {
						return;
					}

					$.each(
						data.result,
						function( key, value ) {
							if(
								!$.isPlainObject(value) ||
								$.type( value.Description ) !== "string" ||
								value.Description.length < 1
							) {
								return;
							}
							$('#statement-details #description'+ value.ID).html( value.Description );
							$('#statement-details a.load_desc[data-related-id=' + value.ID + ']').show();
						}
					);
				}
			);
		}
	);
}

/**
 * Add the HasLocalCurrency param to statement info.
 * This is needed to display correctly the Total yelow box
 *
 * @param data
 * @returns data
 */
function calculate_has_local_currency( data ) {
	if(
		$.isPlainObject( data ) &&
		$.isArray( data.result ) &&
		$.isPlainObject( data.result[0] ) &&
		$.isPlainObject( data.result[0].ContextData ) &&
		$.isPlainObject( data.result[0].ContextData.Quote ) &&
		$.isPlainObject( data.result[0].ContextData.Quote.Statement ) &&
		$.isPlainObject( data.result[0].ContextData.Quote.Statement.TotalDue )
	) {
		data.result[0].ContextData.Quote.Statement.HasLocalCurrency = ( data.result[0].ContextData.Quote.Statement.TotalDue.Currency !== data.result[0].ContextData.Quote.Statement.TotalDue.LocalCurrency );
	}
}

function bookingHelper_FullLoad(targetid, options, propid) {
	console.log('booking helper: full');
	var session = {};
	var cookieData = Cookies.getJSON('searchdata') || [];

	// console.log('cookieData');
	// console.log(cookieData);

	// BAPI.session.searchparams = {};

	// //Add new cookie data to BAPI.session
	// if(cookieData.length > 0) {
	// 	$.each(cookieData, function(i,v) {
	// 		console.log(v.name+": "+v.value);
	// 		BAPI.session.searchparams[v.name] = v.value;
	// 	});
	// }


	// //Convert dates
	// if(BAPI.session.searchparams.los > 0 && !BAPI.session.searchparams.checkout) {
	// 	var los = parseInt(BAPI.session.searchparams.los);
	// 	BAPI.session.searchparams.checkout = moment(BAPI.session.searchparams.checkin, BAPI.defaultOptions.dateFormat).add(los, 'days').format(BAPI.defaultOptions.dateFormatBAPI);
	// }

	// if(!BAPI.session.searchparams.children) { BAPI.session.searchparams.children = 0; }

	var propoptions = $.extend( { avail2: true, seo: 1, loadconfig: 1 }, cleanupDuplicatesFromSearchOptions(BAPI.session.searchparams) );

	// console.log('propoptions');
	// console.log(propoptions);


	BAPI.get(propid, BAPI.entities.property, propoptions, function (data) {

		console.log('data');
		console.log(data);
		console.log('end data');

		data.site = BAPI.site;
		data.config = BAPI.config();
		data.textdata = BAPI.textdata;
		data.session = BAPI.session;
		data.config.kigo_plugin_url = BAPI.UI.WPIS_PATH;
		data.config.ipnURL = BAPI.defaultOptions.baseURL+"/post/paypal/IPN/default.aspx?apikey="+BAPI.defaultOptions.apikey;
		if(data.result[0].Currency) {
			data.config.currency = data.result[0].Currency;
		} else {
			data.config.currency = "USD";
		}

		if(!data.session.searchparams.adults) { data.session.searchparams.adults = {}; }
		if(!data.session.searchparams.children) { data.session.searchparams.children = {}; }
		if(!data.session.searchparams.adults.min) { data.session.searchparams.adults.min = 2; }
		if(!data.session.searchparams.children.min) { data.session.searchparams.children.min = 0; }


		//Convert dates
		if(data.session.searchparams.los > 0 && !data.session.searchparams.checkout) {
			var los = parseInt(data.session.searchparams.los);
			data.session.searchparams.checkout = moment(data.session.searchparams.checkin, BAPI.defaultOptions.dateFormat).add(los, 'days').format(BAPI.defaultOptions.dateFormatBAPI);
		}

		data.session.searchparams.scheckin = data.session.searchparams.checkin ? moment(data.session.searchparams.checkin, BAPI.defaultOptions.dateFormatBAPI).format(BAPI.defaultOptions.dateFormat.toUpperCase()) : null;
		data.session.searchparams.scheckout = data.session.searchparams.checkout ? moment(data.session.searchparams.checkout, BAPI.defaultOptions.dateFormatBAPI).format(BAPI.defaultOptions.dateFormat.toUpperCase()) : null;

		if(data.result[0].ChargeProcessor) {
			if(data.result[0].ChargeProcessor.ServiceLocation == "paypal") {
				data.config.paypal = {}; // = data.result[0].ChargeProcessor.ServiceLocation == "paypal" ? 1 : 0;
				data.config.paypal.username = data.result[0].ChargeProcessor.UserName;
				data.config.paypal.password = data.result[0].ChargeProcessor.Password;
				data.config.paypal.signature = data.result[0].ChargeProcessor.Params['Auth Code'];
				data.config.paypal.sandbox = data.result[0].ChargeProcessor.Params['Test Mode'];
				data.config.paypal.synclinkID = data.result[0].ChargeProcessor.ID;
			}
		}

		$(targetid).html(context.mustacheHelpers.render(options.mastertemplate, data));

		$(options.targetids.stayinfo).html(context.mustacheHelpers.render(options.templates.stayinfo, data));

        $("#stayinfo .alert-error").each(function() {
			var that = $(this);
			$(".modal-body").prepend( that.clone() );
		});

		/* we render the statements mustache */
		calculate_has_local_currency( data );
		$(options.targetids.statement).html(context.mustacheHelpers.render(options.templates.statement, data));
		$(options.targetids.renter).html(context.mustacheHelpers.render(options.templates.renter, data));
		$(options.targetids.creditcard).html(context.mustacheHelpers.render(options.templates.creditcard, data));
		$(options.targetids.accept).html(context.mustacheHelpers.render(options.templates.accept, data));
		move_loaded_ssl();
		$('.specialform').hide(); // hide the spam control
		context.createDatePicker('#makebookingcheckin', { 
			"property": data.result[0], 
			"checkoutID": '#makebookingcheckout',
			'container': '#revisedates' 
		});
		context.createDatePicker('#makebookingcheckout', { 
			"property": data.result[0], 
			"checkinID": '#makebookingcheckin',
			'container': '#revisedates' 
		});
		//set select option with leadData
		if(typeof(data.session.searchparams.leadData) !== "undefined"){$("#renterls").val(data.session.searchparams.leadData.leadrequest);}
		// show the revise your dates if quote is not valid
		BAPI.curentity = data.result[0];
		curbooking = data.result[0].ContextData.Quote;

		if (!data.result[0].ContextData.Quote.IsValid) { try { $('#revisedates').modal('show'); } catch(err) {} }


		function partialRender(sdata, options) { console.log(sdata);
			$(".modal").modal('hide');
			sdata.site = BAPI.site;
			sdata.config = BAPI.config();
			sdata.textdata = BAPI.textdata;
			sdata.session = BAPI.session;


			console.log('searchparams:');
			console.log(sdata.session.searchparams);

			if(sdata.session.searchparams.adults.min) {
				var min = sdata.session.searchparams.adults.min;
				sdata.session.searchparams.adults = {"min":min};
			}

			if(sdata.session.searchparams.children.min) {
				var min = sdata.session.searchparams.children.min;
				sdata.session.searchparams.children = {"min":min};
			}

			if(!sdata.session.searchparams.adults.min) { sdata.session.searchparams.adults.min = 2; }
			if(!sdata.session.searchparams.children.min) { sdata.session.searchparams.children.min = 0; }



			//Convert dates
			sdata.session.searchparams.scheckin = sdata.session.searchparams.checkin ? moment(sdata.session.searchparams.checkin.toUpperCase(), BAPI.defaultOptions.dateFormatBAPI).format(BAPI.defaultOptions.dateFormat.toUpperCase()) : null;
			sdata.session.searchparams.scheckout = sdata.session.searchparams.checkout ? moment(sdata.session.searchparams.checkout.toUpperCase(), BAPI.defaultOptions.dateFormatBAPI).format(BAPI.defaultOptions.dateFormat.toUpperCase()) : null;


			console.log('searchparams:');
			console.log(sdata.session.searchparams);

			var cookieData = [];
			$.each(sdata.session.searchparams, function(i,v) {
				cookieData.push({'name':i, 'value':v});
			});

			Cookies.set('searchdata', cookieData);

			/* we render the statements mustache */
			calculate_has_local_currency( sdata );
			$(options.targetids.statement).html(context.mustacheHelpers.render(options.templates.statement, sdata));
			$(options.targetids.stayinfo).html(context.mustacheHelpers.render(options.templates.stayinfo, sdata));

	        $("#stayinfo .alert-error").each(function() {
				var that = $(this);
				$(".modal-body").prepend( that.clone() );
			});
			$(options.targetids.accept).html(context.mustacheHelpers.render(options.templates.accept, sdata));
			move_loaded_ssl();
			$(options.targetids.stayinfo).parent('div').unblock();
			context.createDatePicker('#makebookingcheckin', { 
				"property": BAPI.curentity, 
				"checkoutID": '#makebookingcheckout',
				'container': '#revisedates'
			});
			context.createDatePicker('#makebookingcheckout', { 
				"property": BAPI.curentity, 
				"checkinID": '#makebookingcheckout',
				'container': '#revisedates' 
			});

			// Invalidate the entity description: entities description need to be reloaded each time the template is re-rendered.
			$('#statement-details').one('show.bs.modal', bookingHelper_get_description );
		}

		$(".bapi-revisedates").live("click", function () {

			$("#revisedates input").removeClass('has-error');

			var valid = true;
			$.each($("#revisedates input"), function(i,v) {
				if( !$(v).val() || !$(v).attr('value') ) {
					$(v).addClass('has-error');

					valid = valid == true ? false : false;
				}
			});


			if(valid) {
				$(options.targetids.stayinfo).parent('div').block({ message: "<img src='" + loadingImgUrl + "' />" });

				var reqdata = saveFormToSession($('.revisedates'), { dataselector: "revisedates" });
				reqdata.pid = propid;
				reqdata.quoteonly = 1;

				console.log('---reqdata 1');
				console.log(JSON.stringify(reqdata));

				BAPI.get(propid, BAPI.entities.property, reqdata, function (sdata) {
					curbooking = sdata.result[0].ContextData.Quote;
					partialRender(sdata, options);
				});
			}

		});

		function modifyStatement() {
			var reqdata = saveFormToSession($('.revisedates'), { dataselector: "revisedates" });
			reqdata.pid = propid;
			reqdata.quoteonly = 1;
			// get the optional fees
			reqdata.optionalfees = [];
			$('.bapi-optionalfee').each(function(i) {
				var c = $(this);
				var qty = (c.is(':checkbox') ? (c.is(":checked")?1:0) : c.val());
				/* the value cant be a negative number */
				if(qty < 0){qty = 0;}
				var ofee = { "RelatedToID": c.attr('data-rid'), "RelatedToEntityID": c.attr('data-reid'), "Quantity": qty };
				reqdata.optionalfees.push(ofee);
			});
			reqdata.numoptionalfees = reqdata.optionalfees.length;
            $(options.targetids.stayinfo).parent('div').block({ message: "<img src='" + loadingImgUrl + "' />" });
			BAPI.get(propid, BAPI.entities.property, reqdata, function (sdata) {
				curbooking = sdata.result[0].ContextData.Quote;
				partialRender(sdata, options);
			});
		}

		$('.bapi-optionalfee').live('change', function() {
			modifyStatement();
		});

		$('.bapi-applyspecial').live('click', function() {
			modifyStatement();
		});

		function renderValidUtil() {
			var seconds_left = 15 * 60; // 15min
			var interval = setInterval(
				function() {
					if( seconds_left < 1 ) {
						clearInterval( interval );
						alert( BAPI.textdata['This quote is not valid any more'] + '.' + '\n' + BAPI.textdata['The page will be automatically refreshed'] + '.' );
						location.reload();
					}

					$( "#quote_ValidUntil_M" ).html( parseInt( seconds_left / 60 ) );
					$( "#quote_ValidUntil_S" ).html( parseInt( seconds_left % 60 ) );

					seconds_left -= 1;
				},
				1000
			);

		}
		renderValidUtil();


		// Get entities description on modal show
		$('#statement-details').one('show.bs.modal', bookingHelper_get_description );

	});

}

function BookingHelper_SetupFormHandlers() {
	$('.bapi-country').live('focus', function() {
		$(this).typeaheadmap({ "source": BAPI.UI.countries, "key": "name", "value": "name", "displayer": function(that, item, highlighted) {return highlighted;} });
	});
	/*$('.bapi-state').live('focus', function() {
		new google.maps.places.Autocomplete(this, cco);
	});
	$('.bapi-city').live('focus', function() {
		var cco = { types: ['(cities)'], componentRestrictions: {country: $('.bapi-country').val()} };
		new google.maps.places.Autocomplete(this, cco);
	});*/

	// do credit card validtion
	$(".ccverify").live('keyup', function() {
		var ctl = $(this);
		ctl.validateCreditCard(function(e) {
			if (e.luhn_valid && e.length_valid) { ctl.attr('data-isvalid', '1'); }
			else { ctl.attr('data-isvalid', '0'); }
		})
	});

	// try to auto set the name on card
	$('.autofullname').live('focus', function() {
		var c = $(this);
		if (c.val()===null || c.val()=='') {
			c.val($('#renterfirstname').val() + ' ' + $('#renterlastname').val());
		}
	});
}

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


	amount = $('#paypalForm [name="amount"]').val();
	amount2 = parseFloat($('#txtAmountToCharge1').val() +"."+$('#txtAmountToCharge2').val());

	if(amount2 > amount) {
		return confirm( 'The amount paid is greater than the amount due.' );
	}
	if(amount2 < amount) {
		return confirm( 'The amount paid is less than the amount due. Subsequent payment will be required.' );
	}
	if(amount2 == 0) {
		alert('The amount paid must be greater than zero.');
		return false;
	}

	return true;
}

function BookingHelper_BookHandler(targetid, options, propid) {
	var processing = false;
	$(".makebooking").live("click", function () {
		if (processing) { return; } // already in here
		processing = true; // make sure we do not reenter

		// get the list of required fields and validate them
		var reqfields = $.extend([],$('.required'));
		processing = BookingHelper_ValidateForm(reqfields);
		if (!processing) { $(targetid).unblock(); return; }
		if (BAPI.isempty(curbooking)) { $(targetid).unblock(); alert("Fatal error trying to save this booking.  The context has been lost."); return; }

		var reqdata = bookingHelper_getFormData(options, curbooking);

		reqdata.paypal = {};
		reqdata.paypal.username = $("input[name=paypal-username]").val();
		reqdata.paypal.password = $("input[name=paypal-password]").val();
		reqdata.paypal.signature = $("input[name=paypal-signature]").val();
		reqdata.paypal.sandbox = $("input[name=paypal-sandbox]").val();

		// add the current booking context to our request form
		if (BAPI.isempty(BAPI.session.searchparams.adults)) { BAPI.session.searchparams.adults = {}; }
		if (BAPI.isempty(BAPI.session.searchparams.children)) { BAPI.session.searchparams.children = {}; }
		if (BAPI.isempty(BAPI.session.searchparams.rooms)) { BAPI.session.searchparams.rooms = {}; }
		if (BAPI.isempty(reqdata.CheckIn)) { reqdata.CheckIn = BAPI.session.searchparams.checkin; }
		if (BAPI.isempty(reqdata.CheckOut)) { reqdata.CheckOut = BAPI.session.searchparams.checkout; }
		if (BAPI.isempty(reqdata.NumAdults)) { reqdata.NumAdults = BAPI.session.searchparams.adults.min; }
		if (BAPI.isempty(reqdata.NumChildren)) { reqdata.NumChildren = BAPI.session.searchparams.children.min; }
		if (BAPI.isempty(reqdata.NumRooms)) { reqdata.NumRooms = BAPI.session.searchparams.rooms.min; }

		$(targetid).block({ message: "<img src='" + loadingImgUrl + "' />" });
		if (typeof(reqdata.special)!=="undefined" && reqdata.special!==null && reqdata.special!='') {
			window.location.href = options.responseurl + '?special=1';
			processing = false;
			return; // special textbox has a value, not a real person
		}

		// do extra cleanup on checkin/checkout
		try { reqdata.CheckIn = moment(reqdata.CheckIn).format(BAPI.defaultOptions.dateFormatBAPI); } catch(err) {}
		try { reqdata.CheckOut = moment(reqdata.CheckOut).format(BAPI.defaultOptions.dateFormatBAPI); } catch(err) {}

		var HeldUntilStirng = '';
		if(BAPI.curentity.ChargeProcessor) {
			if(BAPI.curentity.ChargeProcessor.ServiceLocation == 'paypal') {
				reqdata.Status = 'H'; //Held booking
				//reqdata.HeldUntil = 60; //Add 1 hour
				HeldUntilStirng = '&addMinutesToHeldUntil=60'; //4 hours
			}
		}


		var postdata = { "data": JSON.stringify(reqdata) };


		function createBooking(callback) {
			BAPI.save(BAPI.entities.booking+HeldUntilStirng, postdata, function(bres) {

				if (!BAPI.isempty(bres.error)) {
					$(targetid).unblock();
					processing = false;
				    alert("Oops, there was an error processing the reservation.  Our engineers have been alerted to the problem and are working to have it resolved.");
				} else if (!bres.result.IsValid) {
					$(targetid).unblock();
					processing = false;
					alert(bres.result.ValidationMessage);
				} else if(typeof callback === 'function') {
					callback(bres);
				} else {
					options.responseurl = "/bookingconfirmation";
					$(targetid).unblock();
					processing = false;
					window.location.href = context.nonsecureurl(options.responseurl + '?bid=' + bres.result.ID + '&pid=' + bres.result.PersonID);
				}
			});
		}

		//Process booking as PayPal?
		if(BAPI.curentity.ChargeProcessor) {
			if(BAPI.curentity.ChargeProcessor.ServiceLocation == 'paypal') {
				amount = reqdata.TotalDueNow ;

				//Make call to PHP script to write session
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: ajaxurl,
					data: {
						'action': 'set_session',
						'session': {
							'Payment_Amount' : amount
						}
					},
					success: function( response ) {
						//Create inquiry type=booking
						var newFields = '';
						newFields += '<input type="text" name="first_name" value="'+reqdata.Renter.FirstName+'" />';
						newFields += '<input type="text" name="last_name" value="'+reqdata.Renter.LastName+'" />';
						newFields += '<input type="text" name="email" value="'+reqdata.Renter.PrimaryEmail+'" />';
						newFields += '<input type="text" name="amount" value="'+reqdata.TotalDueNow+'" />';
						newFields += '<input type="text" name="Payment_Amount" value="'+reqdata.TotalDueNow+'" />';
						newFields += '<input type="text" name="desc" value="'+reqdata.Renter.PrimaryEmail+'|'+reqdata.Renter.LastName+'" />';

						$("#paypalForm").append(newFields);

						//Create booking and write booking details to session
						createBooking(function(response) {

							var form = $("#paypalForm");
							form.append('<input type="text" name="bookingID" value="'+response.result.ID+'" />')
								.find('input[name="custom_field"]').val(function(i,v) {
									return v.replace('bookingidhere', response.result.ID);
								});

							form.submit();

						});

						return true;
					},
					error: function (request, status, error) {
				        alert("error: "+request.responseText);
				    }
				});

			} else {
				createBooking();
			}

		} else {
			createBooking();
		}
	});
}

/* removes a param from an URL */
function removeParam(key, sourceURL) {
    var elem = document.createElement('textarea');
    elem.innerHTML = sourceURL;
    sourceURL = elem.value;
    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        rtn = rtn + "?" + params_arr.join("&");
    }
    return rtn;
}
/* gets the value of a param from the current URL */
function getQueryVariable(variable){
       var query = window.location.search.substring(1);
       var elem = document.createElement('textarea');
       elem.innerHTML = query;
       var decoded = elem.value;
       var vars = decoded.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
}

context.createMakeBookingWidget = function (targetid, options) {
	console.log('make booking widget');
	console.log(JSON.stringify(BAPI.session.searchparams));
	if (typeof (options.dataselector) === "undefined") { options.dataselector = "bookingfield"; }
	/* lead data present ?, checking email only since its a must to have the link with lead data */
	if(getQueryVariable('lemail')){
		/* saving lead details */
		leadData = {};
		leadData.name = decodeURIComponent(getQueryVariable('lname'));
		leadData.email = getQueryVariable('lemail');
		leadData.phone = getQueryVariable('lphone');
		leadData.leadrequest = getQueryVariable('leadrequest');
		leadData.comments = decodeURIComponent(getQueryVariable('lcomments'));
                if(BAPI.isempty(BAPI.session.searchparams)){BAPI.session.searchparams = {};}
		BAPI.session.searchparams.leadData = {};
		BAPI.session.searchparams.leadData = leadData;
	}
	var originalURL = window.location.href;
	var noEmailURL = removeParam("lemail", originalURL);//removing email
	var noCommentsURL = removeParam("lcomments", noEmailURL);//removing comments
	// check if we need to redirect
	var u = $.url(noCommentsURL);
	if (bookingHelper_DoRedirect(u)) { return; }

	var propid = u.param('keyid');
	if (typeof(propid)==="undefined" || propid===null) {
		alert("You have reached this page in error.  You will be redirected back to the home page.");
		window.location = "/"; //TODO: need to redirect back to the correct place
		return;
	}
	bookingHelper_FullLoad(targetid, options, propid);
	BookingHelper_SetupFormHandlers();
	BookingHelper_BookHandler(targetid, options, propid);
}

/* This function needs to be called eachtime templates.accept is render by mustache ! */
function move_loaded_ssl() {
	/* we wait for the mustache templates to render then we move the SSL to his repective place */
	if( $('#SSLcontent').length > 0 && $('#SSL').length > 0)
	{
		$("#SSL").html( $('#SSLcontent').html() );//We want to keep the original hidden one !
	}
}

function PaymentHelper_FullLoad(targetid, options, bid) {
    var propoptions = { avail2: true, seo: 1 ,"renterEmail": options.renterEmail }
    propoptions = $.extend({}, propoptions, BAPI.session.searchparams);
    propoptions.resolveprocessor = 1;
    BAPI.get(bid, BAPI.entities.booking, propoptions, function (data) {
        if (data.result.length == 0) {
            alert('Could not load booking');
            return;
        }
        curbooking = data.result[0];
        data.booking = curbooking;
        data.site = BAPI.site;
        data.config = BAPI.config();
        data.config.kigo_plugin_url = BAPI.UI.WPIS_PATH;
        data.config.ipnURL = BAPI.defaultOptions.baseURL+"/post/paypal/IPN/default.aspx?apikey="+BAPI.defaultOptions.apikey;
        data.textdata = BAPI.textdata;
        data.session = BAPI.session;

        if(curbooking.PropertyChargeProcessor) {
        	data.config.paypal = curbooking.PropertyChargeProcessor.ServiceLocation == 'paypal' ? true : false;
        } else {
        	data.config.paypal = false;
        }


        //data.booking.paypal = curbooking.ChargeProcessor.ServiceLocation == "paypal" ? true : false;
        $(targetid).html(context.mustacheHelpers.render(options.mastertemplate, data));
        $(options.targetids.stayinfo).html(context.mustacheHelpers.render(options.templates.stayinfo, data));

        $("#stayinfo .alert-error").each(function() {
			var that = $(this);
			$(".modal-body").prepend( that.clone() );
		});
        $(options.targetids.statement).html(context.mustacheHelpers.render(options.templates.statement, data));
        /* check if this is the new payment page*/
        if(options.newpaymentpage){
			var stringArrayTotalDueNow = data.result[0].TotalDueNow.toString().split('.');
			var stringTotalDueNow = {};
			stringTotalDueNow.Integer = BAPI.isempty(stringArrayTotalDueNow[0]) ? "0" : stringArrayTotalDueNow[0];
			stringTotalDueNow.Decimal = BAPI.isempty(stringArrayTotalDueNow[1]) ? "00" : stringArrayTotalDueNow[1];
			data.result[0].sTotalDueNow = stringTotalDueNow;
			$(options.targetids.creditcard).html(context.mustacheHelpers.render(options.templates.creditcard2, data));
		}else{
			/* this is legacy */
			$(options.targetids.renter).html(context.mustacheHelpers.render(options.templates.renter, data));
			$(options.targetids.creditcard).html(context.mustacheHelpers.render(options.templates.creditcard, data));
		}

        $(options.targetids.accept).html(context.mustacheHelpers.render(options.templates.accept, data));
		move_loaded_ssl();
        $('.specialform').hide(); // hide the spam control

        function partialRender(sdata, options) {
            $(".modal").modal('hide');
            sdata.site = BAPI.site;
            sdata.config = BAPI.config();
            sdata.textdata = BAPI.textdata;
            sdata.session = BAPI.session;
            $(options.targetids.statement).html(context.mustacheHelpers.render(options.templates.statement, sdata));
            $(options.targetids.stayinfo).html(context.mustacheHelpers.render(options.templates.stayinfo, sdata));

	        $("#stayinfo .alert-error").each(function() {
				var that = $(this);
				$(".modal-body").prepend( that.clone() );
			});
            $(options.targetids.accept).html(context.mustacheHelpers.render(options.templates.accept, sdata));
			move_loaded_ssl();
            $(options.targetids.stayinfo).unblock();
        }

        function modifyStatement() {
            var reqdata = saveFormToSession($('.revisedates'), { dataselector: "revisedates" });
            reqdata.pid = String(curbooking.PropertyID);
            reqdata.quoteonly = 1;
            // get the optional fees
            reqdata.optionalfees = [];
            $('.bapi-optionalfee').each(function (i) {
                var c = $(this);
                var qty = (c.is(':checkbox') ? (c.is(":checked") ? 1 : 0) : c.val());
                var ofee = { "RelatedToID": c.attr('data-rid'), "RelatedToEntityID": c.attr('data-reid'), "Quantity": qty };
                reqdata.optionalfees.push(ofee);
            });
            reqdata.numoptionalfees = reqdata.optionalfees.length;
            $(options.targetids.stayinfo).block({ message: "<img src='" + loadingImgUrl + "' />" });
            BAPI.get(bid, BAPI.entities.booking, reqdata, function (sdata) {
                curbooking = sdata.result[0];
                partialRender(sdata, options);
            });
        }

        $('.bapi-optionalfee').live('change', function () {
            modifyStatement();
        });

        $('.bapi-applyspecial').live('click', function () {
            modifyStatement();
        });
    });
}

function PaymentHelper_SetupFormHandlers() {
    $('.bapi-country').live('focus', function () {
        $(this).typeaheadmap({ "source": BAPI.UI.countries, "key": "name", "value": "name", "displayer": function (that, item, highlighted) { return highlighted; } });
    });
    /*$('.bapi-state').live('focus', function() {
		new google.maps.places.Autocomplete(this, cco);
	});
	$('.bapi-city').live('focus', function() {
		var cco = { types: ['(cities)'], componentRestrictions: {country: $('.bapi-country').val()} };
		new google.maps.places.Autocomplete(this, cco);
	});*/

    // do credit card validtion
    $(".ccverify").live('keyup', function () {
        var ctl = $(this);
        ctl.validateCreditCard(function (e) {
            if (e.luhn_valid && e.length_valid) { ctl.attr('data-isvalid', '1'); }
            else { ctl.attr('data-isvalid', '0'); }
        })
    });

    // try to auto set the name on card
    $('.autofullname').live('focus', function () {
        var c = $(this);
        if (c.val() === null || c.val() == '' && !BAPI.isempty($('#renterfirstname').val())) {
            c.val($('#renterfirstname').val() + ' ' + $('#renterlastname').val());
        }
    });
}

function PaymentHelper_ValidateForm(reqfields) {
    for (i = 0; i < reqfields.length; ++i) {
        var rf = $(reqfields[i]);
        $.validity.clear();
        $.validity.start();
        var match = rf.attr('data-validity');
        if (typeof (match) === "undefined" || match === null) {
            rf.require();
        } else {
            rf.require().match(match);
        }
        var result = $.validity.end();
        if (!result.valid) {
            alert(BAPI.textdata['Please fill out all required fields']); rf.focus(); return false;
        }
        // special case for credit card field
        if (rf.hasClass('ccverify') && rf.attr('data-isvalid') != '1') {
            alert(BAPI.textdata['The entered credit card is invalid']); rf.focus(); return false;
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
var processing = false;
function PaymentHelper_PayHandler(targetid, options, propid) {

	$('.paypalBtn').live('click', function(e) {

		var reqfields = $.extend([], $('.required'));
        /* lets check the required fields */
        var validData = BookingHelper_ValidateForm(reqfields);

        /* if its not valid data do nothing */
		if(!validData){ return false; }

		amount = $(this).parent('form').find('[name="amount"]').val();
		amount2 = parseFloat($('#txtAmountToCharge1').val() +"."+$('#txtAmountToCharge2').val());

		$(this).parent('form').append('<input name="Payment_Amount" type="hidden" value="'+amount+'" />');

		if(amount != amount2) {
			amount = amount2;
			$(this).parent('form').find('[name="amount"],[name="amt"],[name="Payment_Amount"]').val(amount);
		}


		//Make call to PHP script to write session
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: {
				'action': 'set_session',
				'session': {
					'Payment_Amount' : amount
				}
			},
			success: function( response ) {
				console.log('session set');
			},
			error: function (request, status, error) {
				e.preventDefault();
		        console.log("error: ");
		        console.log(error);
		        return false;
		    }
		});

	});

    $(".makepayment").live("click", function () {
        if (processing) { return; } // already in here
        // get the list of required fields and validate them
        var reqfields = $.extend([], $('.required'));
        /* lets check the required fields */
        var validData = BookingHelper_ValidateForm(reqfields);

        /* if its not valid data do nothing */
		if(!validData){ return; }
		/* data is valid we are processing now */
		processing = true; // make sure we do not reenter
        if (!processing) { $(targetid).unblock(); return; }
        if (BAPI.isempty(curbooking)) { $(targetid).unblock(); alert("Fatal error trying to save this booking.  The context has been lost."); return; }
        if(options.newpaymentpage){
			/* this is the new page */
			var paymentData = {};
			paymentData.AmountToCharge = parseFloat($('#txtAmountToCharge1').val() +"."+$('#txtAmountToCharge2').val());
			paymentData.CreditCard = {};
			paymentData.CreditCard.NameOncard = $("#noc").val();
			paymentData.CreditCard.CardNumber = $("#ccnum").val();
			paymentData.CreditCard.ExpiryMonth = $("#ccmonth").val();
			paymentData.CreditCard.ExpiryYear = $("#ccyear").val();
			paymentData.CreditCard.SecurityCode = $("#cccvv").val();
			paymentData.CreditCard.Country = $("#billingcountry").val();
			paymentData.CreditCard.Address = $("#billingaddress").val();
			paymentData.CreditCard.Address2 = $("#billingaddress2").val();
			paymentData.CreditCard.City = $("#billingcity").val();
			paymentData.CreditCard.State = $("#billingstate").val();
			paymentData.CreditCard.PostalCode = $("#billingpostalcode").val();
			paymentData.RelatedToEntity = "booking";
			paymentData.RelatedToId = curbooking.ID;
			paymentData.Currency = curbooking.Statement.Currency;
			var postdata = { "data":JSON.stringify(paymentData) };
			$(targetid).block({ message: "<img src='" + loadingImgUrl + "' />" });
			BAPI.save("transaction", postdata, function (bres) {
				if(bres){
					BAPI.log(bres);
					$(targetid).unblock();
					processing = false;
					if(bres.status == 1){
						options.responseurl = "/bookingconfirmation";
						window.location.href = context.nonsecureurl(options.responseurl + '?bid=' + curbooking.ID + '&pid=' + curbooking.Renter.ID);
					}else{
						if(bres.error){
							alert(bres.error.message);
						} else {
							alert("Error trying to process this payment");
						}
					}
				}
			});
        }else{
			/* this is legacy */
			var tempCin, tempCout;
			tempCin = curbooking.Check_In.LongDateTime;
			tempCout = curbooking.Check_Out.LongDateTime;
			var reqdata = bookingHelper_getFormData(options, curbooking);
			reqdata.CheckIn = tempCin;
			reqdata.CheckOut = tempCout;
			reqdata.AltID = curbooking.AltID;
			reqdata.ID = curbooking.ID;
			reqdata.Statement.ID=curbooking.Statement.ID
			reqdata.AmountToCharge=curbooking.AmountToCharge;
			reqdata.AmountToCharge = $('#txtAmountToCharge').val();
			var postdata = { "data":JSON.stringify(reqdata) };
			$(targetid).block({ message: "<img src='" + loadingImgUrl + "' />" });
			BAPI.save(BAPI.entities.booking, postdata, function (bres) {
            if (bres) {
                BAPI.log(bres);
                $(targetid).unblock();
                processing = false;
                if (!bres.result.IsValid) {
                    alert(bres.result.ValidationMessage);
                } else {
                    options.responseurl = "/bookingconfirmation";
                    window.location.href = context.nonsecureurl(options.responseurl + '?bid=' + bres.result.ID + '&pid=' + bres.result.PersonID);
                }
            }
			});
		}
    });
}

context.createMakePaymentWidget = function (targetid, options) {
    if (typeof (options.dataselector) === "undefined") { options.dataselector = "bookingfield"; }
    // check if we need to redirect
    var u = $.url(window.location.href);
    if (bookingHelper_DoRedirect(u)) { return; }
    var bid = u.param('bid');
    if (typeof (bid) === "undefined" || bid === null) {
        bid = u.param('ebid');
    }
	if (typeof (bid) === "undefined" || bid === null) {
        bid = options.bookingId;
    }
    if (typeof (bid) === "undefined" || bid === null) {
        alert("You have reached this page in error.  You will be redirected back to the home page.");
        window.location = "/"; //TODO: need to redirect back to the correct place
        return;
    }

    PaymentHelper_FullLoad(targetid, options, bid);
    PaymentHelper_SetupFormHandlers();
    PaymentHelper_PayHandler(targetid, options, bid);
}

/*
	Group: Misc
*/

/* Weather Widget */
context.createWeatherWidget = function (id, locid, options) {
     $(id).weatherfeed(locid, options);
}

/* Site Search Widget */
context.createSiteSearchWidget = function (id, options) {
	var c = $(id);
	c.append($('<input>', { id: 'sitesearchtxt', type: 'text', "class": 'input-search-watermark' }));
	c.append($('<input>', { type: 'button', "class": 'hdr-search-btn sitesearch' }));
	$(".sitesearch").on("click", function () {
		BAPI.log('/search/?q=' + $('#sitesearchtxt').val());
		window.location.href = '/search/?q=' + $('#sitesearchtxt').val();
	});
}

/* Currency Selector Widget */
context.createCurrencySelectorWidget = function (id, options) {
	var c = $(id);

	var wrapper = { "session": BAPI.session, "config": BAPI.config() }
	var template = BAPI.templates.get('tmpl-currencyselector');
	var html = context.mustacheHelpers.render(template, wrapper);
	c.html(html);
	$('.dropdown-toggle').dropdown();
	$(".changecurrency").live("click", function () {
		var newcurrency = $(this).attr('data-currency');
		$('#currencypopup').dialog("close");
		BAPI.session.currency = newcurrency;
		BAPI.session.searchparams.currency = newcurrency;
		BAPI.savesession();
		document.location.reload(true);
	});
}

/* Loading indicator */
var loadingImgUrl = '//booktplatform.s3.amazonaws.com/App_SharedStyles/CCImages/loading.gif';
context.loading = {
	getLoadingImgUrl: function() { return loadingImgUrl; },
	setLoadingImgUrl: function(s) { loadingImgUrl = s; },
	ctlshow: function(id) {
		$(id).html("<img src='" + loadingImgUrl + "' alt='loading' />");
	},
	show: function(s, options) {
		if (typeof (s) === "undefined" || s == null) {
			s = "Loading...";
		}
		if (typeof (options) === "undefined" || options == null) { options = new Object(); }
		var c = $('#bapiloader');
		if (c.length == 0) {
			c = $(document.body).append('<span id="bapiloader" style="z-index:9999999; left:50%; padding-left:5px; padding-right:5px;font-size:small; background-color:#CF4342; color:#fff; top: 0; position:fixed">Loading</span>');
			c = $('#bapiloader');
		}
		c.text(s);
		c.show();
	},
	hide: function() {
		$('#bapiloader').hide();
	}
}

// private functions
function initOptions(options, initpagesize, inittemplatename) {
	if (typeof (options) === "undefined" || options == null) { options = new Object(); }
	if (typeof (options.textdata) === "undefined" || options.textdata === null) { options.textdata = BAPI.textdata; }
	if (typeof (options.pagesize) === "undefined" || options.pagesize === null) { options.pagesize = initpagesize; }
	if (typeof (options.template) === "undefined" || options.template === null) { options.template = BAPI.templates.get(inittemplatename); }
	if (typeof (options.site) === "undefined" || options.site === null) { options.site = BAPI.site; }
	if (typeof (options.config) === "undefined" || options.config === null) { options.config = BAPI.config(); }
	if (typeof (options.searchoptions) === "undefined" || options.searchoptions == null) { options.searchoptions = {}; }
	if (typeof (options.searchoptions.seo) === "undefined" || options.searchoptions.seo == null) { options.searchoptions.seo = true; }
	if (typeof (options.searchoptions.pagesize) === "undefined" || options.searchoptions.pagesize == null) { options.searchoptions.pagesize = initpagesize; }
	options.searchoptions.page = 1; // start at the first page
	return options;
}

function applyMyList(result,entity) {
	if (BAPI.isempty(result)) { return null; }
	$.each(result, function (index, item) {
		if (!BAPI.isempty(item) && !BAPI.isempty(item.ID)) {
			item.inmylist = (BAPI.mylisttracker.indexof(item.ID.toString(),entity) > -1);
		} else { try { result = result.slice(index,1); } catch(err) {} }

	});
	return result;
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
        //created function to have space after the comma
        data.formatAmenities = function () {
            return function (text, render) {
                var temp = render(text);
              return temp.replace(/,/g , ", ");
            }
          };

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
        console.log(data);
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

function doSearch(targetid, ids, entity, options, alldata, callback) {
	//BAPI.log("Showing page: " + options.searchoptions.page);

    /**
     * The search options seem not to contain the dates.
     * This could explain and solve the issue with KD search.
     */
    var ptch_searchdata = Cookies.getJSON('searchdata');
    if(ptch_searchdata) {
	    for(var i in ptch_searchdata)
	    {
	        if (ptch_searchdata[i].name == 'checkin' || ptch_searchdata[i].name == 'checkout')
	        {
	            options.searchoptions[ptch_searchdata[i].name] = ptch_searchdata[i].value;
	        }
	    }
	}
    /**** End of patch ****/

	var showingMapView = options.template.indexOf('map-view-page') > 0;
	BAPI.get(ids, entity, options.searchoptions, function (data) {
		context.loading.hide(); // hide any loading indicator
		$.each(data.result, function (index, item) {
			if(options.searchoptions.status) {
				if(item.Status == options.searchoptions.status) {
					alldata.push(item); // update the alldata array
				}
			} else {
				alldata.push(item); // update the alldata array
			}
		}); // update the alldata array
		if (options.log) { BAPI.log("--data result--"); BAPI.log(data); }
		doSearchRender(targetid, ids, entity, options, data, alldata);
		/* if we are showing the map view lets load more properties async if we are not already showing all the properties */
		if(showingMapView && ids.length > alldata.length && alldata.length < 99){
			/* we set the pagenumber as 1 and an empty array since this is the map view */
			loadmoreProperties(targetid, ids, entity, options, [], 1, true, callback);
		}
	/* Load the rating function */
	context.inithelpers.loadRatingStars();
	});
	/*we remove the click event attached with live so we dont attach the vent more than 1 time, also we attach the event to the showmore of this targetid */
	$(targetid+" .showmore").die( "click" );
	$(targetid+" .showmore").live("click", function () {
		options.searchoptions.page++;
		$(this).block({ message: "<img src='" + loadingImgUrl + "' />" });
		doSearch(targetid, ids, entity, options, alldata, callback);
	});

	$('#results').on('click', '.changeview', function() {
		/* here we check if the clicked button is inside the targetid so it doesnt update other results like search buckets or featured properties if they are in the same page */
		if($(targetid).find(this).length > 0){
			options.template = BAPI.templates.get($(this).attr('data-template'));
			$(targetid).attr('data-rowfixselector',$(this).attr('data-rowfixselector'));
			$(targetid).attr('data-rowfixcount',$(this).attr('data-rowfixcount'));
			var showingMapView = options.template.indexOf('map-view-page') > 0;
			if(showingMapView && alldata.length > 99){
				var maxMarkersArray = alldata.slice(0,99);
				var maxMarkersArrayIds = ids.slice(0,99);
				doSearchRender(targetid, ids, entity, options, {}, maxMarkersArray);
			}else{
				doSearchRender(targetid, ids, entity, options, {}, alldata);
			}
			/* if we are showing the map view lets load more properties async if we are not already showing all the properties */
			if(showingMapView && ids.length > alldata.length && alldata.length < 99){
				/* we set the pagenumber as 1 and an empty array since this is the map view */
				loadmoreProperties(targetid, ids, entity, options, [], 1,true, callback);
			}
		}
	});

}

function cleanupDuplicatesFromSearchOptions(searchoptions){
        if(searchoptions.method){delete searchoptions.method}
        if(searchoptions.entity){delete searchoptions.entity}
        if(searchoptions.apikey){delete searchoptions.apikey}
        if(searchoptions.ids){delete searchoptions.ids}
        return searchoptions;
}

function loadmoreProperties(targetid, ids, entity, options, newAlldata, pagenumber, firstIteration, callback) {
	/* check if its first iteration */
	if(firstIteration){
		options.showalloptions = {};
		options.showalloptions.checkin = options.searchoptions.checkin;
		options.showalloptions.checkout = options.searchoptions.checkout;
		options.showalloptions.currency = options.searchoptions.currency;
		options.showalloptions.los = options.searchoptions.los;
		options.showalloptions.pagesize = 10;
		options.showalloptions.scheckin = options.searchoptions.scheckin;
		options.showalloptions.scheckout = options.searchoptions.scheckout;
		options.showalloptions.seo = true;
		/* the search result is grater than 99 we need to cut it*/
		if(ids.length > 99){
			/* we already have items in newAlldata lets get what is left */
			options.showalloptionsids = ids.slice(0,99);
		}else{
			options.showalloptionsids = ids;
		}
		//$(targetid).block({ message: "<img src='" + loadingImgUrl + "' />" });
	}
	options.showalloptions.page = pagenumber;
	BAPI.get(options.showalloptionsids, entity, options.showalloptions, function (data) {
		context.loading.hide(); // hide any loading indicator
		$.each(data.result, function (index, item) { newAlldata.push(item); }); // update the alldata array
		if (options.log) { BAPI.log("--data result--"); BAPI.log(data); }
			/* 100 is the max */
			if(newAlldata.length < options.showalloptionsids.length){
				loadmoreProperties(targetid, options.showalloptionsids, entity, options, newAlldata, pagenumber + 1,false, callback);
			}
			doSearchRender(targetid, options.showalloptionsids, entity, options, data, newAlldata);
	});
}

function loadFormFromSession(s) {
	if (BAPI.isempty(s)) { return; }

	/* this is only setting the input value not the calendar*/
	$('.sessioncheckin').val(s.scheckin);
	$('.sessioncheckout').val(s.scheckout);
	$('.sessionlos').val(s.los);
	$('.sessioncategory').val(s.category);
	$('.sessiondevid').val(s.dev);
	$('.sessionlocation').val(s.location);
	if(
		!BAPI.isempty( s.location ) &&
		$('input.sessionlocation').length &&
		$.isPlainObject( BAPI.config().location ) &&
		$.isArray( BAPI.config().location.values )
	) {
		$.each(
			BAPI.config().location.values,
			function() {
				if( this.Data === s.location ) {
					$('.sessionlocation').val( this.Label );
					$('.sessionlocation').attr( 'data-value', this.Data );
					return false;
				}
			}
		);
	}

	$('.sessionheadline').val(s.headline);
	$('.sessionaltid').val(s.altid);
	if (!BAPI.isempty(s.adults)) { $('.sessionadultsmin').val(s.adults.min); }
	if (!BAPI.isempty(s.children)) { $('.sessionchildrenmin').val(s.children.min); }
	if (!BAPI.isempty(s.sleeps) && !BAPI.isempty(s.sleeps.exactly)) { $('.sessionsleepsexactly').val(s.sleeps.exactly); }
	if (!BAPI.isempty(s.sleeps) && !BAPI.isempty(s.sleeps.min)) { $('.sessionsleepsmin').val(s.sleeps.min); }
	if (!BAPI.isempty(s.beds) && !BAPI.isempty(s.beds.exactly)) { $('.sessionbedsexactly').val(s.beds.exactly); }
	if (!BAPI.isempty(s.beds) && !BAPI.isempty(s.beds.min)) { $('.sessionbedsmin').val(s.beds.min); }
	if (!BAPI.isempty(s.baths)) { $('.sessionbathsmin').val(s.baths.min); }
	if (!BAPI.isempty(s.maxrate)) { $('.sessionmaxratemax').val(s.maxrate.max); }
	if (!BAPI.isempty(s.rooms)) { $('.sessionroomsmin').val(s.rooms.min); }
}

function saveFormToSession(ctl, options) {
    BAPI.log("+saveFormToSession", 2);
	var reqdata = {};
	var dfparse = BAPI.defaultOptions.dateFormatMoment(); 
	var df = BAPI.defaultOptions.dateFormatBAPI;
	//df = dfparse.split('/').join('-'); //This is ghetto
	BAPI.log("options dump->", 2); BAPI.log(options);
	$('.' + options.dataselector).each(function () {
		var k = $(this).attr('data-field');
		var v = $(this).attr('data-value');

		console.log(k+": "+v);

		if (v == null | v == '') v = $(this).val();
		//BAPI.log(k + '=' + v, 2);
		if (k != null && k.length > 0 ) {
			if (k=="checkin") {
			    v = (v === null || v == '') ? null : moment(v, dfparse).format(df);
			    reqdata.checkin = null;
			    reqdata.scheckin = v;
			}
			if (k=="checkout") {
			    v = (v === null || v == '') ? null : moment(v, dfparse).format(df);
			    reqdata.checkout = null;
			    reqdata.scheckout = v;
			}
			if (k=="scheckin") {
			    v = (v === null || v == '') ? null : moment(v, dfparse).format(BAPI.defaultOptions.dateFormat.toUpperCase());
			    reqdata.checkin = null;
			    reqdata.scheckin = v;
			}
			if (k=="scheckout") {
			    v = (v === null || v == '') ? null : moment(v, dfparse).format(BAPI.defaultOptions.dateFormat.toUpperCase());
			    reqdata.checkout = null;
			    reqdata.scheckout = v;
			}
			// assign to the req and the session
			var i = k.indexOf('[');
			if (i==-1) {
				reqdata[k] = v;
			} else {
				// special case when the data-attribute value has nested brackets (such as adults[min])
				var k1 = k.substring(0,i);
				var k2 = k.substring(i+1,k.length-1);
				reqdata[k1] = reqdata[k1] || {};
				reqdata[k1][k2] = v;
			}
		}
	});


	/* amenities activated and ready to be send */
	if($("#amenitiesDropdownCheckbox").length > 0){
		var checkedAmenities = $("#amenitiesDropdownCheckbox").dropdownCheckbox("checked");
		var amenitiesString = '';
		for(var i = 0; i < checkedAmenities.length; i++){
			amenitiesString = amenitiesString + checkedAmenities[i].id + ',';
		}
		amenitiesString = amenitiesString.substr(0, amenitiesString.length-1);
		reqdata.amenities = amenitiesString;
	}

	BAPI.session.searchparams = reqdata;

	if (BAPI.isempty(reqdata.los)) { BAPI.session.searchparams.los = null } // clear out the los if not supplied
	
	BAPI.savesession();
	BAPI.log("-saveFormToSession", 2);
	return reqdata;
}

function setRows(findThis,wrapthis,howManyWrap){
	var flag=false;
	if($(findThis).length > 0){
	  var timer = setInterval(function(){
		  var found = $(wrapthis).length;
		  if(found>0){flag=true;}
		  initRows(wrapthis,howManyWrap);
			  if(found==0 && flag){
			  	clearInterval(timer);
			  }
	  }, 200);
	}
}

function initRows(wrapthis,howManyWrap) {
	var divs = $(wrapthis);
	for(var i = 0; i < divs.length; i+=howManyWrap) {
		try { divs.slice(i, i+howManyWrap).wrapAll("<div class='row-fluid'></div>"); } catch(err) {}
	}
}

/*
	this function sets a timer that calls another function until it sets the rows if it is needed or the flexslider if there is one, pages that use this are:
	Attractions, Property Finders, Specials, Gallery View, List View
*/
function setRows(findThis,wrapthis,needFlex,needWrapRows,howManyWrap){
	if($(findThis).length > 0){
		var timer = setInterval(function(){
			if ($(".showmore").length > 0) {
				initRows(wrapthis,needFlex,needWrapRows,howManyWrap);
			}
			if ($(".nomore").length > 0) {
				initRows(wrapthis,needFlex,needWrapRows,howManyWrap);
				clearInterval(timer);
			}
		}, 200);
	}
}
function parseDate(jsonDateString) {
    return new Date(parseInt(jsonDateString.replace('/Date(', '')));
}


})(BAPI.UI); 


//Extend BAPI.savesession
// (function() {
// 		var savesession = BAPI.savesession;

// 		BAPI.savesession = function() {
// 			console.log('--start');

// 		var session = BAPI.session.searchparams;
// 		var cookieString = JSON.stringify(session);

// 		console.log('bapi search params at time of call');
// 		console.log(JSON.stringify(session));

// 		var cookieData = [];
// 		$.each(session, function(i,v) {
// 			if(null !== v) {
// 				cookieData.push({'name':i, 'value':v});
// 			}
// 		});

// 		console.log('cookiedata to be saved:');
// 		console.log(JSON.stringify(cookieData));

// 		Cookies.set('searchdata', cookieData);
// 		//BAPI.savesession();

// 		console.log('manually set bapi as');
// 		console.log(cookieString);
// 		//BAPI.session.searchparams = session;

// 		console.log('bapi after saving');
// 		console.log(JSON.stringify(BAPI.session.searchparams));

// 		console.log('----end cookie set from extended savesession()');

// 		savesession();
// 	}
// })(BAPI);

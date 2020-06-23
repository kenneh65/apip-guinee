/*!
 *
 * Jquery Mapael - Dynamic maps jQuery plugin (based on raphael.js)
 * Requires jQuery, raphael.js and jquery.mousewheel
 *
 * Version: 2.0.0
 *
 * Copyright (c) 2015 Vincent Brouté (http://www.vincentbroute.fr/mapael)
 * Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php).
 *
 * Thanks to Indigo744
 *
 */
!function(a){"object"==typeof exports?module.exports=a(require("jquery"),require("raphael"),require("mousewheel")):"function"==typeof define&&define.amd?define(["jquery","raphael","mousewheel"],a):a(jQuery,Raphael,jQuery.fn.mousewheel)}(function(a,b,c,d){"use strict";var e="mapael",f="2.0.0-dev",g=function(b,c){var d=this;d.container=b,d.$container=a(b),d.options=d.extendDefaultOptions(c),d.initialHTMLContent=d.$container.html(),d.zoomTO=0,d.zoomCenterX=0,d.zoomCenterY=0,d.previousPinchDist=0,d.zoomData={zoomLevel:0,zoomX:0,zoomY:0,panX:0,panY:0},d.resizeTO=0,d.panning=!1,d.panningTO=0,d.animationIntervalID=null,d.$map={},d.$tooltip={},d.paper={},d.areas={},d.plots={},d.links={},d.mapConf={},d.init()};g.prototype={version:f,init:function(){var c=this;if(""===c.options.map.cssClass||0===a("."+c.options.map.cssClass,c.container).length)throw new Error("The map class `"+c.options.map.cssClass+"` doesn't exists");if(c.$tooltip=a("<div>").addClass(c.options.map.tooltip.cssClass).css("display","none"),c.$map=a("."+c.options.map.cssClass,c.container).empty().append(c.$tooltip),a[e]&&a[e].maps&&a[e].maps[c.options.map.name])c.mapConf=a[e].maps[c.options.map.name];else{if(!(a.fn[e]&&a.fn[e].maps&&a.fn[e].maps[c.options.map.name]))throw new Error("Unknown map '"+c.options.map.name+"'");c.mapConf=a.fn[e].maps[c.options.map.name],window.console&&window.console.warn&&window.console.warn("Extending $.fn.mapael is deprecated (map '"+c.options.map.name+"')")}if(c.paper=new b(c.$map[0],c.mapConf.width,c.mapConf.height),c.isRaphaelBBoxBugPresent()===!0)throw c.destroy(),new Error("Can't get boundary box for text (is your container hidden? See #135)");c.$container.addClass(e),c.options.map.tooltip.css&&c.$tooltip.css(c.options.map.tooltip.css),c.paper.setViewBox(0,0,c.mapConf.width,c.mapConf.height,!1),c.options.map.width?(c.paper.setSize(c.options.map.width,c.mapConf.height*(c.options.map.width/c.mapConf.width)),c.createLegends("plot",c.plots,c.options.map.width/c.mapConf.width)):c.handleMapResizing(),a.each(c.mapConf.elems,function(a){var b=c.getElemOptions(c.options.map.defaultArea,c.options.areas[a]?c.options.areas[a]:{},c.options.legend.area);c.areas[a]={mapElem:c.paper.path(c.mapConf.elems[a]).attr(b.attrs)}}),c.options.map.beforeInit&&c.options.map.beforeInit(c.$container,c.paper,c.options),a.each(c.mapConf.elems,function(a){var b=c.getElemOptions(c.options.map.defaultArea,c.options.areas[a]?c.options.areas[a]:{},c.options.legend.area);c.initElem(c.areas[a],b,a)}),c.links=c.drawLinksCollection(c.options.links),a.each(c.options.plots,function(a){c.plots[a]=c.drawPlot(a)}),c.$container.on("zoom."+e,function(a,b){c.onZoomEvent(a,b)}),c.options.map.zoom.enabled&&c.initZoom(c.mapConf.width,c.mapConf.height,c.options.map.zoom),c.options.map.zoom.init!==d&&(c.options.map.zoom.init.animDuration===d&&(c.options.map.zoom.init.animDuration=0),c.$container.trigger("zoom."+e,c.options.map.zoom.init)),c.createLegends("area",c.areas,1),c.$container.on("update."+e,function(a,b){c.onUpdateEvent(a,b)}),c.$container.on("showElementsInRange."+e,function(a,b){c.onShowElementsInRange(a,b)}),c.options.map.afterInit&&c.options.map.afterInit(c.$container,c.paper,c.areas,c.plots,c.options),a(c.paper.desc).append(" and Mapael "+c.version+" (http://www.vincentbroute.fr/mapael/)")},destroy:function(){var b=this;b.$container.off("."+e),b.$container.empty(),b.onResizeEvent&&a(window).off("resize."+e,b.onResizeEvent),b.$container.html(b.initialHTMLContent),b.$container.removeClass(e),b.$container.removeData(e),b.container=d,b.$container=d,b.options=d,b.paper=d,b.$map=d,b.$tooltip=d,b.mapConf=d,b.areas=d,b.plots=d,b.links=d},handleMapResizing:function(){var b=this;b.onResizeEvent=function(){clearTimeout(b.resizeTO),b.resizeTO=setTimeout(function(){b.$map.trigger("resizeEnd."+e)},150)},a(window).on("resize."+e,b.onResizeEvent),b.$map.on("resizeEnd."+e,function(){var a=b.$map.width();if(b.paper.width!=a){var c=a/b.mapConf.width;b.paper.setSize(a,b.mapConf.height*c),b.createLegends("plot",b.plots,c)}}).trigger("resizeEnd."+e)},extendDefaultOptions:function(b){return b=a.extend(!0,{},g.prototype.defaultOptions,b),a.each(b.legend,function(c){if(a.isArray(b.legend[c]))for(var d=0;d<b.legend[c].length;++d)b.legend[c][d]=a.extend(!0,{},g.prototype.legendDefaultOptions[c],b.legend[c][d]);else b.legend[c]=a.extend(!0,{},g.prototype.legendDefaultOptions[c],b.legend[c])}),b},initElem:function(b,c,e){var f=this,g={},h={};c.value!==d&&(b.value=c.value),c.text&&c.text.content!==d&&(g=b.mapElem.getBBox(),h=f.getTextPosition(g,c.text.position,c.text.margin),c.text.attrs["text-anchor"]=h.textAnchor,b.textElem=f.paper.text(h.x,h.y,c.text.content).attr(c.text.attrs),a(b.textElem.node).attr("data-id",e)),c.eventHandlers&&f.setEventHandlers(e,c,b.mapElem,b.textElem),f.setHoverOptions(b.mapElem,c.attrs,c.attrsHover),b.textElem&&f.setHoverOptions(b.textElem,c.text.attrs,c.text.attrsHover),(a.isEmptyObject(c.attrsHover)===!1||b.textElem&&a.isEmptyObject(c.text.attrsHover)===!1)&&f.setHover(b.mapElem,b.textElem),c.tooltip&&(b.mapElem.tooltip=c.tooltip,f.setTooltip(b.mapElem),c.text&&c.text.content!==d&&(b.textElem.tooltip=c.tooltip,f.setTooltip(b.textElem))),c.href&&(b.mapElem.href=c.href,b.mapElem.target=c.target,f.setHref(b.mapElem),c.text&&c.text.content!==d&&(b.textElem.href=c.href,b.textElem.target=c.target,f.setHref(b.textElem))),a(b.mapElem.node).attr("data-id",e)},initZoom:function(b,c,f){var g=this,h=!1,i=0,j=0,k={reset:function(){g.$container.trigger("zoom."+e,{level:0})},"in":function(){g.$container.trigger("zoom."+e,{level:"+1"})},out:function(){g.$container.trigger("zoom."+e,{level:-1})}};a.extend(g.zoomData,{zoomLevel:0,panX:0,panY:0}),a.each(f.buttons,function(b,c){if(k[b]===d)throw new Error("Unknown zoom button '"+b+"'");var f=a("<div>").addClass(c.cssClass).html(c.content).attr("title",c.title);f.on("click."+e,k[b]),g.$map.append(f)}),g.options.map.zoom.mousewheel&&g.$map.on("mousewheel."+e,function(a){var b=a.deltaY>0?1:-1,c=g.mapPagePositionToXY(a.pageX,a.pageY);return g.$container.trigger("zoom."+e,{fixedCenter:!0,level:g.zoomData.zoomLevel+b,x:c.x,y:c.y}),!1}),g.options.map.zoom.touch&&(g.$map.on("touchstart."+e,function(a){2===a.originalEvent.touches.length&&(g.zoomCenterX=(a.originalEvent.touches[0].pageX+a.originalEvent.touches[1].pageX)/2,g.zoomCenterY=(a.originalEvent.touches[0].pageY+a.originalEvent.touches[1].pageY)/2,g.previousPinchDist=Math.sqrt(Math.pow(a.originalEvent.touches[1].pageX-a.originalEvent.touches[0].pageX,2)+Math.pow(a.originalEvent.touches[1].pageY-a.originalEvent.touches[0].pageY,2)))}),g.$map.on("touchmove."+e,function(a){var b=0,c=0;if(2===a.originalEvent.touches.length){if(b=Math.sqrt(Math.pow(a.originalEvent.touches[1].pageX-a.originalEvent.touches[0].pageX,2)+Math.pow(a.originalEvent.touches[1].pageY-a.originalEvent.touches[0].pageY,2)),Math.abs(b-g.previousPinchDist)>15){var d=g.mapPagePositionToXY(g.zoomCenterX,g.zoomCenterY);c=(b-g.previousPinchDist)/Math.abs(b-g.previousPinchDist),g.$container.trigger("zoom."+e,{fixedCenter:!0,level:g.zoomData.zoomLevel+c,x:d.x,y:d.y}),g.previousPinchDist=b}return!1}})),a("body").on("mouseup."+e+(f.touch?" touchend":""),function(){h=!1,setTimeout(function(){g.panning=!1},50)}),g.$map.on("mousedown."+e+(f.touch?" touchstart":""),function(a){a.pageX!==d?(h=!0,i=a.pageX,j=a.pageY):1===a.originalEvent.touches.length&&(h=!0,i=a.originalEvent.touches[0].pageX,j=a.originalEvent.touches[0].pageY)}).on("mousemove."+e+(f.touch?" touchmove":""),function(e){var k=g.zoomData.zoomLevel,l=0,m=0;if(e.pageX!==d?(l=e.pageX,m=e.pageY):1===e.originalEvent.touches.length?(l=e.originalEvent.touches[0].pageX,m=e.originalEvent.touches[0].pageY):h=!1,h&&0!==k){var n=(i-l)/(1+k*f.step)*(b/g.paper.width),o=(j-m)/(1+k*f.step)*(c/g.paper.height),p=Math.min(Math.max(0,g.paper._viewBox[0]+n),b-g.paper._viewBox[2]),q=Math.min(Math.max(0,g.paper._viewBox[1]+o),c-g.paper._viewBox[3]);return(Math.abs(n)>5||Math.abs(o)>5)&&(a.extend(g.zoomData,{panX:p,panY:q,zoomX:p+g.paper._viewBox[2]/2,zoomY:q+g.paper._viewBox[3]/2}),g.paper.setViewBox(p,q,g.paper._viewBox[2],g.paper._viewBox[3]),clearTimeout(g.panningTO),g.panningTO=setTimeout(function(){g.$map.trigger("afterPanning",{x1:p,y1:q,x2:p+g.paper._viewBox[2],y2:q+g.paper._viewBox[3]})},150),i=l,j=m,g.panning=!0),!1}})},mapPagePositionToXY:function(a,b){var c=this,d=c.$map.offset(),e=c.options.map.width?c.mapConf.width/c.options.map.width:c.mapConf.width/c.$map.width(),f=1/(1+c.zoomData.zoomLevel*c.options.map.zoom.step);return{x:f*e*(a-d.left)+c.zoomData.panX,y:f*e*(b-d.top)+c.zoomData.panY}},onZoomEvent:function(b,c){var e=this,f=e.zoomData.zoomLevel,g=0,h=0,i=1+e.zoomData.zoomLevel*e.options.map.zoom.step,j=0,k=c.animDuration!==d?c.animDuration:e.options.map.zoom.animDuration,l=0,m=0,n={};c.level!==d&&(f="string"==typeof c.level?"+"===c.level.slice(0,1)||"-"===c.level.slice(0,1)?e.zoomData.zoomLevel+parseInt(c.level):parseInt(c.level):c.level<0?e.zoomData.zoomLevel+c.level:c.level,f=Math.min(Math.max(f,e.options.map.zoom.minLevel),e.options.map.zoom.maxLevel)),j=1+f*e.options.map.zoom.step,c.latitude!==d&&c.longitude!==d&&(n=e.mapConf.getCoords(c.latitude,c.longitude),c.x=n.x,c.y=n.y),c.x===d&&(c.x=e.paper._viewBox[0]+e.paper._viewBox[2]/2),c.y===d&&(c.y=e.paper._viewBox[1]+e.paper._viewBox[3]/2),0===f?(g=0,h=0):c.fixedCenter!==d&&c.fixedCenter===!0?(l=e.zoomData.panX+(c.x-e.zoomData.panX)*(j-i)/j,m=e.zoomData.panY+(c.y-e.zoomData.panY)*(j-i)/j,g=Math.min(Math.max(0,l),e.mapConf.width-e.mapConf.width/j),h=Math.min(Math.max(0,m),e.mapConf.height-e.mapConf.height/j)):(g=Math.min(Math.max(0,c.x-e.mapConf.width/j/2),e.mapConf.width-e.mapConf.width/j),h=Math.min(Math.max(0,c.y-e.mapConf.height/j/2),e.mapConf.height-e.mapConf.height/j)),j==i&&g==e.zoomData.panX&&h==e.zoomData.panY||(k>0?e.animateViewBox(g,h,e.mapConf.width/j,e.mapConf.height/j,k,e.options.map.zoom.animEasing):(e.paper.setViewBox(g,h,e.mapConf.width/j,e.mapConf.height/j),clearTimeout(e.zoomTO),e.zoomTO=setTimeout(function(){e.$map.trigger("afterZoom",{x1:g,y1:h,x2:g+e.mapConf.width/j,y2:h+e.mapConf.height/j})},150)),a.extend(e.zoomData,{zoomLevel:f,panX:g,panY:h,zoomX:g+e.paper._viewBox[2]/2,zoomY:h+e.paper._viewBox[3]/2}))},onShowElementsInRange:function(a,b){var c=this;b.animDuration===d&&(b.animDuration=0),b.hiddenOpacity===d&&(b.hiddenOpacity=.3),b.ranges&&b.ranges.area&&c.showElemByRange(b.ranges.area,c.areas,b.hiddenOpacity,b.animDuration),b.ranges&&b.ranges.plot&&c.showElemByRange(b.ranges.plot,c.plots,b.hiddenOpacity,b.animDuration),b.ranges&&b.ranges.link&&c.showElemByRange(b.ranges.link,c.links,b.hiddenOpacity,b.animDuration),b.afterShowRange&&b.afterShowRange()},showElemByRange:function(b,c,e,f){var g=this,h={};b.min===d&&b.max===d||(b={0:b}),a.each(b,function(f){var g=b[f];return g.min===d&&g.max===d?!0:void a.each(c,function(a){var b=c[a].value;return"object"!=typeof b&&(b=[b]),b[f]===d?!0:void(g.min!==d&&b[f]<g.min||g.max!==d&&b[f]>g.max?h[a]=e:h[a]=1)})}),a.each(h,function(a){g.setElementOpacity(c[a],h[a],f)})},setElementOpacity:function(a,b,c){b>0&&(a.mapElem.show(),a.textElem&&a.textElem.show()),c>0?(a.mapElem.animate({opacity:b},c,"linear",function(){0===b&&a.mapElem.hide()}),a.textElem&&a.textElem.animate({opacity:b},c,"linear",function(){0===b&&a.textElem.hide()})):(a.mapElem.attr({opacity:b}),0===b&&a.mapElem.hide(),a.textElem&&(a.textElem.attr({opacity:b}),0===b&&a.textElem.hide()))},onUpdateEvent:function(b,c){var f=this;if("object"==typeof c){var g=0,h=c.animDuration?c.animDuration:0,i=function(a){f.unsetHover(a.mapElem,a.textElem),h>0?(a.mapElem.animate({opacity:0},h,"linear",function(){a.mapElem.remove()}),a.textElem&&a.textElem.animate({opacity:0},h,"linear",function(){a.textElem.remove()})):(a.mapElem.remove(),a.textElem&&a.textElem.remove())},j=function(a){a.mapElem.attr({opacity:0}),a.textElem&&a.textElem.attr({opacity:0}),f.setElementOpacity(a,a.mapElem.originalAttrs.opacity!==d?a.mapElem.originalAttrs.opacity:1,h)};if("object"==typeof c.mapOptions&&(c.replaceOptions===!0?f.options=f.extendDefaultOptions(c.mapOptions):a.extend(!0,f.options,c.mapOptions),c.mapOptions.areas===d&&c.mapOptions.plots===d&&c.mapOptions.legend===d||a("[data-type='elem']",f.$container).each(function(b,c){"1"===a(c).attr("data-hidden")&&a(c).trigger("click."+e,[!1,h])})),"object"==typeof c.deletePlotKeys)for(;g<c.deletePlotKeys.length;g++)f.plots[c.deletePlotKeys[g]]!==d&&(i(f.plots[c.deletePlotKeys[g]]),delete f.plots[c.deletePlotKeys[g]]);else"all"===c.deletePlotKeys&&(a.each(f.plots,function(a,b){i(b)}),f.plots={});if("object"==typeof c.deleteLinkKeys)for(g=0;g<c.deleteLinkKeys.length;g++)f.links[c.deleteLinkKeys[g]]!==d&&(i(f.links[c.deleteLinkKeys[g]]),delete f.links[c.deleteLinkKeys[g]]);else"all"===c.deleteLinkKeys&&(a.each(f.links,function(a,b){i(b)}),f.links={});if("object"==typeof c.newPlots&&a.each(c.newPlots,function(a){f.plots[a]===d&&(f.options.plots[a]=c.newPlots[a],f.plots[a]=f.drawPlot(a),h>0&&j(f.plots[a]))}),"object"==typeof c.newLinks){var k=f.drawLinksCollection(c.newLinks);a.extend(f.links,k),a.extend(f.options.links,c.newLinks),h>0&&a.each(k,function(a){j(k[a])})}if(a.each(f.areas,function(a){if("object"==typeof c.mapOptions&&("object"==typeof c.mapOptions.map&&"object"==typeof c.mapOptions.map.defaultArea||"object"==typeof c.mapOptions.areas&&"object"==typeof c.mapOptions.areas[a]||"object"==typeof c.mapOptions.legend&&"object"==typeof c.mapOptions.legend.area)||c.replaceOptions===!0){var b=f.getElemOptions(f.options.map.defaultArea,f.options.areas[a]?f.options.areas[a]:{},f.options.legend.area);f.updateElem(b,f.areas[a],h)}}),a.each(f.plots,function(a){if("object"==typeof c.mapOptions&&("object"==typeof c.mapOptions.map&&"object"==typeof c.mapOptions.map.defaultPlot||"object"==typeof c.mapOptions.plots&&"object"==typeof c.mapOptions.plots[a]||"object"==typeof c.mapOptions.legend&&"object"==typeof c.mapOptions.legend.plot)||c.replaceOptions===!0){var b=f.getElemOptions(f.options.map.defaultPlot,f.options.plots[a]?f.options.plots[a]:{},f.options.legend.plot);"square"==b.type?(b.attrs.width=b.size,b.attrs.height=b.size,b.attrs.x=f.plots[a].mapElem.attrs.x-(b.size-f.plots[a].mapElem.attrs.width)/2,b.attrs.y=f.plots[a].mapElem.attrs.y-(b.size-f.plots[a].mapElem.attrs.height)/2):"image"==b.type?(b.attrs.width=b.width,b.attrs.height=b.height,b.attrs.x=f.plots[a].mapElem.attrs.x-(b.width-f.plots[a].mapElem.attrs.width)/2,b.attrs.y=f.plots[a].mapElem.attrs.y-(b.height-f.plots[a].mapElem.attrs.height)/2):"svg"==b.type?b.attrs.transform!==d&&(b.attrs.transform=f.plots[a].mapElem.baseTransform+b.attrs.transform):b.attrs.r=b.size/2,f.updateElem(b,f.plots[a],h)}}),a.each(f.links,function(a){if("object"==typeof c.mapOptions&&("object"==typeof c.mapOptions.map&&"object"==typeof c.mapOptions.map.defaultLink||"object"==typeof c.mapOptions.links&&"object"==typeof c.mapOptions.links[a])||c.replaceOptions===!0){var b=f.getElemOptions(f.options.map.defaultLink,f.options.links[a]?f.options.links[a]:{},{});f.updateElem(b,f.links[a],h)}}),c.mapOptions&&("object"==typeof c.mapOptions.legend||"object"==typeof c.mapOptions.map&&"object"==typeof c.mapOptions.map.defaultArea||"object"==typeof c.mapOptions.map&&"object"==typeof c.mapOptions.map.defaultPlot)&&(a("[data-type='elem']",f.$container).each(function(b,c){"1"===a(c).attr("data-hidden")&&a(c).trigger("click."+e,[!1,h])}),f.createLegends("area",f.areas,1),f.options.map.width?f.createLegends("plot",f.plots,f.options.map.width/f.mapConf.width):f.createLegends("plot",f.plots,f.$map.width()/f.mapConf.width)),"object"==typeof c.setLegendElemsState)a.each(c.setLegendElemsState,function(b,c){var g=f.$container.find("."+b)[0];g!==d&&a("[data-type='elem']",g).each(function(b,d){("0"===a(d).attr("data-hidden")&&"hide"===c||"1"===a(d).attr("data-hidden")&&"show"===c)&&a(d).trigger("click."+e,[!1,h])})});else{var l="hide"===c.setLegendElemsState?"hide":"show";a("[data-type='elem']",f.$container).each(function(b,c){("0"===a(c).attr("data-hidden")&&"hide"===l||"1"===a(c).attr("data-hidden")&&"show"===l)&&a(c).trigger("click."+e,[!1,h])})}c.afterUpdate&&c.afterUpdate(f.$container,f.paper,f.areas,f.plots,f.options)}},drawLinksCollection:function(b){var c=this,e={},f={},g={},h={},i={};return a.each(b,function(a){var j=c.getElemOptions(c.options.map.defaultLink,b[a],{});e="string"==typeof b[a].between[0]?c.options.plots[b[a].between[0]]:b[a].between[0],f="string"==typeof b[a].between[1]?c.options.plots[b[a].between[1]]:b[a].between[1],e.latitude!==d&&e.longitude!==d?g=c.mapConf.getCoords(e.latitude,e.longitude):(g.x=e.x,g.y=e.y),f.latitude!==d&&f.longitude!==d?h=c.mapConf.getCoords(f.latitude,f.longitude):(h.x=f.x,h.y=f.y),i[a]=c.drawLink(a,g.x,g.y,h.x,h.y,j)}),i},drawLink:function(a,b,c,d,e,f){var g=this,h={},i=(b+d)/2,j=(c+e)/2,k=-1/((e-c)/(d-b)),l=j-k*i,m=Math.sqrt((d-b)*(d-b)+(e-c)*(e-c)),n=1+k*k,o=-2*i+2*k*l-2*k*j,p=i*i+l*l-l*j-j*l+j*j-f.factor*m*(f.factor*m),q=o*o-4*n*p,r=0,s=0;return f.factor>0?(r=(-o+Math.sqrt(q))/(2*n),s=k*r+l):(r=(-o-Math.sqrt(q))/(2*n),s=k*r+l),h.mapElem=g.paper.path("m "+b+","+c+" C "+r+","+s+" "+d+","+e+" "+d+","+e).attr(f.attrs),g.initElem(h,f,a),h},updateElem:function(a,b,c){var e,f,g,h=this,i=b.mapElem.getBBox();a.value!==d&&(b.value=a.value),b.textElem&&(a.text!==d&&a.text.content!==d&&a.text.content!=b.textElem.attrs.text&&b.textElem.attr({text:a.text.content}),(a.size||a.width&&a.height)&&("image"==a.type||"svg"==a.type?(f=(a.width-i.width)/2,g=(a.height-i.height)/2):(f=(a.size-i.width)/2,g=(a.size-i.height)/2),i.x-=f,i.x2+=f,i.y-=g,i.y2+=g),e=h.getTextPosition(i,a.text.position,a.text.margin),e.x==b.textElem.attrs.x&&e.y==b.textElem.attrs.y||(c>0?(b.textElem.attr({"text-anchor":e.textAnchor}),b.textElem.animate({x:e.x,y:e.y},c)):b.textElem.attr({x:e.x,y:e.y,"text-anchor":e.textAnchor})),h.setHoverOptions(b.textElem,a.text.attrs,a.text.attrsHover),c>0?b.textElem.animate(a.text.attrs,c):b.textElem.attr(a.text.attrs)),h.setHoverOptions(b.mapElem,a.attrs,a.attrsHover),c>0?b.mapElem.animate(a.attrs,c):b.mapElem.attr(a.attrs),"svg"==a.type&&b.mapElem.transform("m"+a.width/b.mapElem.originalWidth+",0,0,"+a.height/b.mapElem.originalHeight+","+i.x+","+i.y),a.tooltip&&(b.mapElem.tooltip===d&&(h.setTooltip(b.mapElem),b.textElem&&h.setTooltip(b.textElem)),b.mapElem.tooltip=a.tooltip,b.textElem&&(b.textElem.tooltip=a.tooltip)),a.href!==d&&(b.mapElem.href===d&&(h.setHref(b.mapElem),b.textElem&&h.setHref(b.textElem)),b.mapElem.href=a.href,b.mapElem.target=a.target,b.textElem&&(b.textElem.href=a.href,b.textElem.target=a.target))},drawPlot:function(a){var b=this,c={},e={},f=b.getElemOptions(b.options.map.defaultPlot,b.options.plots[a]?b.options.plots[a]:{},b.options.legend.plot);return e=f.x!==d&&f.y!==d?{x:f.x,y:f.y}:b.mapConf.getCoords(f.latitude,f.longitude),"square"==f.type?c={mapElem:b.paper.rect(e.x-f.size/2,e.y-f.size/2,f.size,f.size).attr(f.attrs)}:"image"==f.type?c={mapElem:b.paper.image(f.url,e.x-f.width/2,e.y-f.height/2,f.width,f.height).attr(f.attrs)}:"svg"==f.type?(f.attrs.transform===d&&(f.attrs.transform=""),c={mapElem:b.paper.path(f.path)},c.mapElem.originalWidth=c.mapElem.getBBox().width,c.mapElem.originalHeight=c.mapElem.getBBox().height,c.mapElem.baseTransform="m"+f.width/c.mapElem.originalWidth+",0,0,"+f.height/c.mapElem.originalHeight+","+(e.x-f.width/2)+","+(e.y-f.height/2),f.attrs.transform=c.mapElem.baseTransform+f.attrs.transform,c.mapElem.attr(f.attrs)):c={mapElem:b.paper.circle(e.x,e.y,f.size/2).attr(f.attrs)},b.initElem(c,f,a),c},setHref:function(b){var c=this;b.attr({cursor:"pointer"}),a(b.node).on("click."+e,function(){!c.panning&&b.href&&window.open(b.href,b.target)})},setTooltip:function(b){var c=this,f=0,g=c.$tooltip.attr("class"),h=function(a,d){var e=10,f=20;"object"==typeof b.tooltip.offset&&("undefined"!=typeof b.tooltip.offset.left&&(e=b.tooltip.offset.left),"undefined"!=typeof b.tooltip.offset.top&&(f=b.tooltip.offset.top));var g={left:Math.min(c.$map.width()-c.$tooltip.outerWidth()-5,a-c.$map.offset().left+e),top:Math.min(c.$map.height()-c.$tooltip.outerHeight()-5,d-c.$map.offset().top+f)};"object"==typeof b.tooltip.overflow&&(b.tooltip.overflow.right===!0&&(g.left=a-c.$map.offset().left+10),selem.tooltip.overflow.bottom===!0&&(g.top=d-c.$map.offset().top+20)),c.$tooltip.css(g)};a(b.node).on("mouseover."+e,function(a){f=setTimeout(function(){if(c.$tooltip.attr("class",g),b.tooltip!==d){if(b.tooltip.content!==d){var e="function"==typeof b.tooltip.content?b.tooltip.content(b):b.tooltip.content;c.$tooltip.html(e).css("display","block")}b.tooltip.cssClass!==d&&c.$tooltip.addClass(b.tooltip.cssClass)}h(a.pageX,a.pageY)},120)}).on("mouseout."+e,function(){clearTimeout(f),c.$tooltip.css("display","none")}).on("mousemove."+e,function(a){h(a.pageX,a.pageY)})},setEventHandlers:function(b,c,d,e){var f=this;a.each(c.eventHandlers,function(g){!function(g){a(d.node).on(g,function(a){f.panning||c.eventHandlers[g](a,b,d,e,c)}),e&&a(e.node).on(g,function(a){f.panning||c.eventHandlers[g](a,b,d,e,c)})}(g)})},drawLegend:function(c,e,f,g,h){var i=this,j={},k={},l=0,m=0,n=null,o={},p={},q={},r=0,s=0,t=0,u=0,v=[],w=0;for(j=a("."+c.cssClass,i.$container).empty(),k=new b(j.get(0)),a(k.canvas).attr({"data-type":e,"data-index":h}),m=l=0,c.title&&""!==c.title&&(n=k.text(c.marginLeftTitle,0,c.title).attr(c.titleAttrs),n.attr({y:.5*n.getBBox().height}),l=c.marginLeftTitle+n.getBBox().width,m+=c.marginBottomTitle+n.getBBox().height),r=0,w=c.slices.length;w>r;++r){var x=0;v[r]=a.extend(!0,{},"plot"==e?i.options.map.defaultPlot:i.options.map.defaultArea,c.slices[r]),c.slices[r].legendSpecificAttrs===d&&(c.slices[r].legendSpecificAttrs={}),a.extend(!0,v[r].attrs,c.slices[r].legendSpecificAttrs),"area"==e?(v[r].attrs.width===d&&(v[r].attrs.width=30),v[r].attrs.height===d&&(v[r].attrs.height=20)):"square"==v[r].type?(v[r].attrs.width===d&&(v[r].attrs.width=v[r].size),v[r].attrs.height===d&&(v[r].attrs.height=v[r].size)):"image"==v[r].type||"svg"==v[r].type?(v[r].attrs.width===d&&(v[r].attrs.width=v[r].width),v[r].attrs.height===d&&(v[r].attrs.height=v[r].height)):v[r].attrs.r===d&&(v[r].attrs.r=v[r].size/2),x=c.marginBottomTitle,n&&(x+=n.getBBox().height),x+="plot"!=e||v[r].type!==d&&"circle"!=v[r].type?g*v[r].attrs.height/2:g*v[r].attrs.r,u=Math.max(u,x)}for("horizontal"==c.mode&&(l=c.marginLeft),r=0,w=v.length;w>r;++r)if(v[r].display===d||v[r].display===!0){if("area"==e?("horizontal"==c.mode?(s=l+c.marginLeft,t=u-.5*g*v[r].attrs.height):(s=c.marginLeft,t=m),o=k.rect(s,t,g*v[r].attrs.width,g*v[r].attrs.height)):"square"==v[r].type?("horizontal"==c.mode?(s=l+c.marginLeft,t=u-.5*g*v[r].attrs.height):(s=c.marginLeft,t=m),o=k.rect(s,t,g*v[r].attrs.width,g*v[r].attrs.height)):"image"==v[r].type||"svg"==v[r].type?("horizontal"==c.mode?(s=l+c.marginLeft,t=u-.5*g*v[r].attrs.height):(s=c.marginLeft,t=m),"image"==v[r].type?o=k.image(v[r].url,s,t,g*v[r].attrs.width,g*v[r].attrs.height):(o=k.path(v[r].path),v[r].attrs.transform===d&&(v[r].attrs.transform=""),v[r].attrs.transform="m"+g*v[r].width/o.getBBox().width+",0,0,"+g*v[r].height/o.getBBox().height+","+s+","+t+v[r].attrs.transform)):("horizontal"==c.mode?(s=l+c.marginLeft+g*v[r].attrs.r,t=u):(s=c.marginLeft+g*v[r].attrs.r,t=m+g*v[r].attrs.r),o=k.circle(s,t,g*v[r].attrs.r)),delete v[r].attrs.width,delete v[r].attrs.height,delete v[r].attrs.r,o.attr(v[r].attrs),p=o.getBBox(),"horizontal"==c.mode?(s=l+c.marginLeft+p.width+c.marginLeftLabel,t=u):(s=c.marginLeft+p.width+c.marginLeftLabel,t=m+p.height/2),q=k.text(s,t,v[r].label).attr(c.labelAttrs),"horizontal"==c.mode){var y=c.marginBottom+p.height;l+=c.marginLeft+p.width+c.marginLeftLabel+q.getBBox().width,"image"!=v[r].type&&"area"!=e&&(y+=c.marginBottomTitle),n&&(y+=n.getBBox().height),m=Math.max(m,y)}else l=Math.max(l,c.marginLeft+p.width+c.marginLeftLabel+q.getBBox().width),m+=c.marginBottom+p.height;a(o.node).attr({"data-type":"elem","data-index":r,"data-hidden":0}),a(q.node).attr({"data-type":"label","data-index":r,"data-hidden":0}),c.hideElemsOnClick.enabled&&(q.attr({cursor:"pointer"}),o.attr({cursor:"pointer"}),i.setHoverOptions(o,v[r].attrs,v[r].attrs),i.setHoverOptions(q,c.labelAttrs,c.labelAttrsHover),i.setHover(o,q),i.handleClickOnLegendElem(c,v[r],q,o,f,h))}"SVG"!=b.type&&c.VMLWidth&&(l=c.VMLWidth),k.setSize(l,m)},handleClickOnLegendElem:function(b,c,f,g,h,i){var j=this,k=function(k,l,m){var n=0,o=a(f.node).attr("data-hidden"),p="0"===o?{"data-hidden":"1"}:{"data-hidden":"0"};m===d&&(m=b.hideElemsOnClick.animDuration),"0"===o?m>0?f.animate({opacity:.5},m):f.attr({opacity:.5}):m>0?f.animate({opacity:1},m):f.attr({opacity:1}),a.each(h,function(e){var f=h[e].mapElem.data("hidden-by");f===d&&(f={}),n=a.isArray(h[e].value)?h[e].value[i]:h[e].value,(c.sliceValue!==d&&n==c.sliceValue||c.sliceValue===d&&(c.min===d||n>=c.min)&&(c.max===d||n<=c.max))&&!function(c){"0"===o?(f[i]=!0,j.setElementOpacity(h[c],b.hideElemsOnClick.opacity,m)):(delete f[i],a.isEmptyObject(f)&&j.setElementOpacity(h[c],h[c].mapElem.originalAttrs.opacity!==d?h[c].mapElem.originalAttrs.opacity:1,m)),h[c].mapElem.data("hidden-by",f)}(e)}),a(g.node).attr(p),a(f.node).attr(p),l!==d&&l!==!0||b.exclusive===d||b.exclusive!==!0||a("[data-type='elem'][data-hidden=0]",j.$container).each(function(){a(this).attr("data-index")!==a(g.node).attr("data-index")&&a(this).trigger("click."+e,!1)})};a(f.node).on("click."+e,k),a(g.node).on("click."+e,k),c.clicked!==d&&c.clicked===!0&&a(g.node).trigger("click."+e,!1)},createLegends:function(b,c,d){var e=this,f=e.options.legend[b];a.isArray(e.options.legend[b])||(f=[e.options.legend[b]]);for(var g=0;g<f.length;++g){if(""===f[g].cssClass||0===a("."+f[g].cssClass,e.$container).length)throw new Error("The legend class `"+f[g].cssClass+"` doesn't exists.");f[g].display===!0&&a.isArray(f[g].slices)&&f[g].slices.length>0&&e.drawLegend(f[g],b,c,d,g)}},setHoverOptions:function(c,d,e){"SVG"!=b.type&&delete e.transform,c.attrsHover=e,c.attrsHover.transform?c.originalAttrs=a.extend({transform:"s1"},d):c.originalAttrs=d},setHover:function(b,c){var d=this,f={},g={},h=0,i=0,j=function(){clearTimeout(i),h=setTimeout(function(){d.elemHover(b,c)},120)},k=function(){clearTimeout(h),i=setTimeout(function(){d.elemOut(b,c)},120)};f=a(b.node),f.on("mouseover."+e,j),f.on("mouseout."+e,k),c&&(g=a(c.node),g.on("mouseover."+e,j),a(c.node).on("mouseout."+e,k))},unsetHover:function(b,c){a(b.node).off("."+e),c&&a(c.node).off("."+e)},elemHover:function(a,b){var c=this;a.attrsHover.animDuration>0?a.animate(a.attrsHover,a.attrsHover.animDuration):a.attr(a.attrsHover),b&&(b.attrsHover.animDuration>0?b.animate(b.attrsHover,b.attrsHover.animDuration):b.attr(b.attrsHover)),c.paper.safari&&c.paper.safari()},elemOut:function(a,b){var c=this;a.attrsHover.animDuration>0?a.animate(a.originalAttrs,a.attrsHover.animDuration):a.attr(a.originalAttrs),b&&(b.attrsHover.animDuration>0?b.animate(b.originalAttrs,b.attrsHover.animDuration):b.attr(b.originalAttrs)),c.paper.safari&&c.paper.safari()},getElemOptions:function(b,c,e){var f=this,g=a.extend(!0,{},b,c);if(g.value!==d)if(a.isArray(e))for(var h=0,i=e.length;i>h;++h)g=a.extend(!0,{},g,f.getLegendSlice(g.value[h],e[h]));else g=a.extend(!0,{},g,f.getLegendSlice(g.value,e));return g},getTextPosition:function(a,b,c){var d=0,e=0,f="";switch("number"==typeof c&&(c="bottom"===b||"top"===b?{x:0,y:c}:"right"===b||"left"===b?{x:c,y:0}:{x:0,y:0}),b){case"bottom":d=(a.x+a.x2)/2+c.x,e=a.y2+c.y,f="middle";break;case"top":d=(a.x+a.x2)/2+c.x,e=a.y-c.y,f="middle";break;case"left":d=a.x-c.x,e=(a.y+a.y2)/2+c.y,f="end";break;case"right":d=a.x2+c.x,e=(a.y+a.y2)/2+c.y,f="start";break;default:d=(a.x+a.x2)/2+c.x,e=(a.y+a.y2)/2+c.y,f="middle"}return{x:d,y:e,textAnchor:f}},getLegendSlice:function(a,b){for(var c=0,e=b.slices.length;e>c;++c)if(b.slices[c].sliceValue!==d&&a==b.slices[c].sliceValue||b.slices[c].sliceValue===d&&(b.slices[c].min===d||a>=b.slices[c].min)&&(b.slices[c].max===d||a<=b.slices[c].max))return b.slices[c];return{}},animateViewBox:function(a,c,d,e,f,g){var h,i=this,j=i.paper._viewBox?i.paper._viewBox[0]:0,k=a-j,l=i.paper._viewBox?i.paper._viewBox[1]:0,m=c-l,n=i.paper._viewBox?i.paper._viewBox[2]:i.paper.width,o=d-n,p=i.paper._viewBox?i.paper._viewBox[3]:i.paper.height,q=e-p,r=25,s=f/r,t=0;g=g||"linear",h=b.easing_formulas[g],clearInterval(i.animationIntervalID),i.animationIntervalID=setInterval(function(){var b=t/s;i.paper.setViewBox(j+k*h(b),l+m*h(b),n+o*h(b),p+q*h(b),!1),t++>=s&&(clearInterval(i.animationIntervalID),clearTimeout(i.zoomTO),i.zoomTO=setTimeout(function(){i.$map.trigger("afterZoom",{x1:a,y1:c,x2:a+d,y2:c+e})},150))},r)},isRaphaelBBoxBugPresent:function(){var a=this,b=a.paper.text(-50,-50,"TEST"),c=b.getBBox();return b.remove(),0===c.width&&0===c.height},defaultOptions:{map:{cssClass:"map",tooltip:{cssClass:"mapTooltip"},defaultArea:{attrs:{fill:"#343434",stroke:"#5d5d5d","stroke-width":1,"stroke-linejoin":"round"},attrsHover:{fill:"#f38a03",animDuration:300},text:{position:"inner",margin:10,attrs:{"font-size":15,fill:"#c7c7c7"},attrsHover:{fill:"#eaeaea",animDuration:300}},target:"_self"},defaultPlot:{type:"circle",size:15,attrs:{fill:"#0088db",stroke:"#fff","stroke-width":0,"stroke-linejoin":"round"},attrsHover:{"stroke-width":3,animDuration:300},text:{position:"right",margin:10,attrs:{"font-size":15,fill:"#c7c7c7"},attrsHover:{fill:"#eaeaea",animDuration:300}},target:"_self"},defaultLink:{factor:.5,attrs:{stroke:"#0088db","stroke-width":2},attrsHover:{animDuration:300},text:{position:"inner",margin:10,attrs:{"font-size":15,fill:"#c7c7c7"},attrsHover:{fill:"#eaeaea",animDuration:300}},target:"_self"},zoom:{enabled:!1,minLevel:0,maxLevel:10,step:.25,mousewheel:!0,touch:!0,animDuration:200,animEasing:"linear",buttons:{reset:{cssClass:"zoomButton zoomReset",content:"&#8226;",title:"Reset zoom"},"in":{cssClass:"zoomButton zoomIn",content:"+",title:"Zoom in"},out:{cssClass:"zoomButton zoomOut",content:"&#8722;",title:"Zoom out"}}}},legend:{area:[],plot:[]},areas:{},plots:{},links:{}},legendDefaultOptions:{area:{cssClass:"areaLegend",display:!0,marginLeft:10,marginLeftTitle:5,marginBottomTitle:10,marginLeftLabel:10,marginBottom:10,titleAttrs:{"font-size":16,fill:"#343434","text-anchor":"start"},labelAttrs:{"font-size":12,fill:"#343434","text-anchor":"start"},labelAttrsHover:{fill:"#787878",animDuration:300},hideElemsOnClick:{enabled:!0,opacity:.2,animDuration:300},slices:[],mode:"vertical"},plot:{cssClass:"plotLegend",display:!0,marginLeft:10,marginLeftTitle:5,marginBottomTitle:10,marginLeftLabel:10,marginBottom:10,titleAttrs:{"font-size":16,fill:"#343434","text-anchor":"start"},labelAttrs:{"font-size":12,fill:"#343434","text-anchor":"start"},labelAttrsHover:{fill:"#787878",animDuration:300},hideElemsOnClick:{enabled:!0,opacity:.2,animDuration:300},slices:[],mode:"vertical"}}},a[e]===d&&(a[e]=g),a.fn[e]=function(b){return this.each(function(){a.data(this,e)&&a.data(this,e).destroy(),a.data(this,e,new g(this,b))})}});
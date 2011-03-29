/**
 * Class TreeViewHandler
 * (c) Daniel Faivre 2011
 *
 * Tips :
 * - for loops are much faster than using each
 * - return false is required in functions
 */
function TreeViewHandler(treeviewInstance, allPartners, nbStyles) {
  this.treeview = jQuery("#" + treeviewInstance + "_in");
  this.loadingImage = jQuery("#" + treeviewInstance + "_loading");
  this.toolbox = jQuery("#tv_tools");
  this.buttons = jQuery(".tv_button:first", this.toolbox);
  this.toolboxOrientation = (this.toolbox.find("#tvToolsHandler").css("float") != "left") ? 'v' : 'h';
  this.nbStyles = nbStyles;
  this.zoom = 100; // in percent
  this.boxWidth = this.treeview.find(".tv_box:first").width(); // store the initial box width
  if (isNaN(this.boxWidth))
    this.boxWidth = 180; // default family box width
  this.boxExpandedWidth = 250; // default expanded family box width
  this.cookieDays = 360; // lifetime of preferences memory, in days
  this.ajaxUrl = "module.php?mod=tree&instance=" + treeviewInstance + 
	  "&allPartners=" + (allPartners ? "true" : "false") + 
  	"&mod_action=";

  this.container = this.treeview.parent(); // Store the container element ("#" + treeviewInstance + "_out")
  this.auto_box_width = false;
  this.showDates = true; 
  this.updating = false;
  this.overLevel = 0; // internal var for handling submenus
  
  var tv = this; // Store "this" for usage within jQuery functions where "this" is not this ;-)

  // Restore user preferences
  if (readCookie("zoom") != null) {
  	tv.setZoom(parseInt(readCookie("zoom")) / 100);
  }
  if (readCookie("compact") == "true") {
  	tv.compact();
  }
  // set/reset the cookie allPartners
  if (readCookie("allPartners") != allPartners)
    createCookie("allPartners", allPartners, this.cookieDays);

  // Define the draggables
  tv.toolbox.draggable({
    handle: "#tvToolsHandler",
    cursor: "move",
    containment: "#" + treeviewInstance + "_out",
    snap: "#" + treeviewInstance + "_out",
    snapMode: "inner",
    snapTolerance: 10
  });
  tv.treeview.draggable({
    cursor: "move",
    stop: function(event, ui) {
      tv.updateTree();
    }
  });
  
  // define the toolbox submenu's functions
  tv.toolbox.find("#tvStyleButton,#tvStylesSubmenu").each(function(index, tvStyleButton) {
  	var submenu =  tv.toolbox.find("#tvStylesSubmenu");
  	tvStyleButton.onmouseover = function() {
  		tv.overLevel++;
    	var bw = tv.buttons.outerWidth(true);
    	var bm = (tv.buttons.outerWidth(true) - tv.buttons.outerWidth()) / 2;
    	if (tv.toolboxOrientation != 'v') {
    		var deltaX = 6 + 8 * bw; // align with the 8th button
  			var deltaY = bw;
    	}
  		else {
  			var deltaX = bw;
  			var deltaY = 6 + 8 * (bw-bm); // align with the 8th button
    	}
  		submenu.css("left", deltaX);
  		submenu.css("top", deltaY);
  		submenu.css("display", "block");
  	}
  	tvStyleButton.onmouseout = function() {
  		tv.overLevel--;
 			window.setTimeout(function(){if (tv.overLevel < 1) jQuery(submenu).css("display", "none");}, 200);
		}
  });
  // Add click handlers to buttons
  tv.toolbox.find("#tvToolsHandler").each(function(index, tvthandler) {
  	tvthandler.ondblclick = function() {
  		//tv.changeToolsOrientation();
  	  var toolbox = "#tvToolsHandler, li.tv_button";
  	  var submenu = tv.toolbox.find("#tvStylesSubmenu");
  	  if (tv.toolbox.find(toolbox).css("float") == "left") {
  	  	tv.toolbox.find(toolbox).css("float", "none");
  	  	submenu.css("width", tv.nbStyles * tv.buttons.outerWidth(true));
  	  	submenu.find("li.tv_button").css("float", "left");
  	    jQuery("#tvToolsHandler", tv.toolbox).css("height", "2px").css("width", "22px");
  	    tv.toolboxOrientation = 'v';
  	  }
  	  else {
  	  	tv.toolbox.find(toolbox).css("float", "left");
  	  	submenu.css("width", tv.buttons.outerWidth(true));
  	  	submenu.find("li.tv_button").css("float", "none");
  	    jQuery("#tvToolsHandler", tv.toolbox).css("height", "22px").css("width", "2px");
  	    tv.toolboxOrientation = 'h';
  	  }
  	  return false;
  	}
  });
  tv.toolbox.find("#tvbZoomIn").each(function(index, tvbZoomIn) {
  	tvbZoomIn.onclick = function() {
  		tv.setZoom(1.1, tvbZoomIn);
  	}
  });
  tv.toolbox.find("#tvbZoomOut").each(function(index, tvbZoomOut) {
  	tvbZoomOut.onclick = function() {
  		tv.setZoom(0.9, tvbZoomOut);
  	}
  });
  tv.toolbox.find("#tvbNoZoom").each(function(index, tvbNoZoom) {
  	tvbNoZoom.onclick = function() {
  		tv.setZoom(0, tvbNoZoom);
  	}
  });
  tv.toolbox.find("#tvbLeft").each(function(index, tvLeft) {
  	var b = jQuery(tvLeft, tv.toolbox);
  	tvLeft.onclick = function() {
  		b.addClass("tvPressed");
  		tv.align("left", b);
  	}
  });
  tv.toolbox.find("#tvbCenter").each(function(index, tvCenter) {
  	tvCenter.onclick = function() {
  		tv.centerOnRoot();
  	}
  });
  tv.toolbox.find("#tvbRight").each(function(index, tvRight) {
  	var b = jQuery(tvRight, tv.toolbox);
  	tvRight.onclick = function() {
  		b.addClass("tvPressed");
  		tv.align("right", b);
  	}
  });
  tv.toolbox.find("#tvbDates").each(function(index, tvbDates) {
  	var b = jQuery(tvbDates, tv.toolbox);
  	tvbDates.onclick = function() {
  		if (tv.showDates) {
  			s = 'none';
  			b.removeClass("tvPressed");
  			tv.showDates = false;
  		}
  		else {
  			s = 'inline';
  			b.addClass("tvPressed");
  			tv.showDates = true;
  		}
  		tv.treeview.find(".tv_box .dates").css("display", s);
  	}
  });
  tv.toolbox.find("#tvbCompact").each(function(index, tvCompact) {  	
  	tvCompact.onclick = function() {
  		tv.compact();
  	}
  });
  tv.toolbox.find("#tvbPrint").each(function(index, tvbPrint) {
  	tvbPrint.onclick = function() {
  		tv.print(tvbPrint);
  	}
  });
  tv.toolbox.find("#tvbOpen").each(function(index, tvbOpen) {
  	var b = jQuery(tvbOpen, tv.toolbox);
  	tvbOpen.onclick = function() {
  		b.addClass("tvPressed");
  		tv.setLoading();
  		var e = jQuery.Event("click");
  		tv.treeview.find(".tv_box:not(.boxExpanded)").each(function(index, box){
  			var pos = jQuery(box, tv.treeview).offset();
  	    if ((pos.left >= tv.leftMin) && (pos.left <= tv.leftMax) && (pos.top >= tv.topMin) && (pos.top <= tv.topMax))
  	    	tv.expandBox(box,e);
  		});
  		b.removeClass("tvPressed");
  		tv.setComplete();
  	}
  });
  tv.toolbox.find("#tvbClose").each(function(index, tvbClose) {
  	var b = jQuery(tvbClose,tv.toolbox );
  	tvbClose.onclick = function() {
  		b.addClass("tvPressed");
  		tv.setLoading();
  		tv.treeview.find(".tv_box.boxExpanded").each(function(index, box){
  			jQuery(box).css("display", "none").removeClass("boxExpanded").parent().find(".tv_box.collapsedContent").css("display", "block");
  		});
  		b.removeClass("tvPressed");
  		tv.setComplete();
  	}
  });
  
  // Intercept the scroll event to keep the toolbox available
  jQuery(window).scroll(function(){
  	if (jQuery(window).scrollTop() > tv.container.offset().top)
  		tv.toolbox.css("position", "fixed");
  	else
  		tv.toolbox.css("position", "absolute");
  });
  
  tv.centerOnRoot(); // fire ajax update if needed, which call setComplete() when all is loaded
  return false;
}
/**
 * Class TreeView setLoading method
 */
TreeViewHandler.prototype.setLoading = function() {
	this.treeview.css("cursor", "wait");
  this.loadingImage.css("display", "block");
}
/**
 * Class TreeView setComplete  method
 */
TreeViewHandler.prototype.setComplete = function() {
	this.treeview.css("cursor", "move");
	this.loadingImage.css("display", "none");
}

/**
 * Class TreeView getSize  method
 * Store the viewport current size
 */
TreeViewHandler.prototype.getSize = function() {
  var tv = this;
  // retrieve the current container bounding box
  var container = tv.container.parent();
  var offset = container.offset();
  tv.leftMin = offset.left;
  tv.leftMax = tv.leftMin + container.innerWidth();
  tv.topMin = offset.top;
  tv.topMax = tv.topMin + container.innerHeight();
  /*
  var frm = jQuery("#tvTreeBorder");
  tv.treeview.css("width", frm.width());
  tv.treeview.css("height", frm.height());*/
}

/**
 * Class TreeView updateTree  method
 * Perform ajax requests to complete the tree after drag
 * param boolean @center center on root person when done
 */
TreeViewHandler.prototype.updateTree = function(center, button) {
  var tv = this; // Store "this" for usage within jQuery functions where "this" is not this ;-)
  var toLoad = new Array();
  var elts = new Array();
  this.getSize();

  // check which td with datafld attribute are within the container bounding box
  // and therefore need to be dynamically loaded
  tv.treeview.find("td[abbr]").each(function(index, el) {
    el = jQuery(el, tv.treeview);
    var pos = el.offset();
    if ((pos.left >= tv.leftMin) && (pos.left <= tv.leftMax) && (pos.top >= tv.topMin) && (pos.top <= tv.topMax)) {
      toLoad.push(el.attr("abbr"));
      elts.push(el);
    }
  });
  // if some boxes need update, we perform an ajax request
  if (toLoad.length > 0) {
    tv.updating = true;
    tv.setLoading();
    jQuery.ajax({
      url: tv.ajaxUrl + "getPersons",
      dataType: "json",
      data: "q=" + toLoad.join(";"),
      success: function(ret) {
        var nb = elts.length;
        var rootEl = jQuery(".rootPerson", this.treeview);
        var l = rootEl.offset().left;
        for (var i=0;i<nb;i++) {
          elts[i].removeAttr("abbr").html(ret[i]);
        }
        // repositionning
        tv.treeview.offset({left: tv.treeview.offset().left - rootEl.offset().left +l});
        // we now ajust the draggable treeview size to its content size
        tv.getSize();
      },
      complete: function() {
        if (tv.treeview.find("td[abbr]").length)
          tv.updateTree(center, button); // recursive call
        // the added boxes need that in mode compact boxes
        if (tv.auto_box_width)
          tv.treeview.find(".tv_box").css("width", "auto");
        tv.updating = true; // avoid an unuseful recursive call when all requested persons are loaded
        if (center == true)
          tv.centerOnRoot();
        if (button)
        	button.removeClass("tvPressed");
        tv.setComplete();
        tv.updating = false;
      },
      timeout: function() {
        if (button)
        	button.removeClass("tvPressed");
        tv.updating = false;
        tv.setComplete();
      }
    });
  }
  else {
    if (button)
    	button.removeClass("tvPressed");
  	tv.setComplete();
  }
  return false;
}

/**
 * Class TreeView setZoom method
 */
TreeViewHandler.prototype.setZoom = function(zoom, button) {
  this.treeview.css("cursor", "wait");
  jQuery(button).addClass("tvPressed");
  if (zoom == 0)
    this.zoom = 100;
  else
    this.zoom *= zoom;
  this.treeview.css("font-size", this.zoom + "%");

  // we zoom the person boxes only if boxes width is fixed
  if (!this.auto_box_width) {
    jQuery(".tv_box:not(.expanded)", this.treeview).css("width", this.boxWidth * (this.zoom / 100) + "px");
    jQuery(".boxExpanded", this.treeview).css("width", this.boxExpandedWidth * (this.zoom / 100) + "px");
  }
  createCookie("zoom", this.zoom.toString(), this.cookieDays);
  jQuery(button).removeClass("tvPressed");
  this.treeview.css("cursor", "move");
  return false;
}

/**
 * Class TreeView compacte  method
 */
TreeViewHandler.prototype.compact = function() {
	var tv = this;
	var b = jQuery("#tvbCompact", tv.toolbox);
  tv.setLoading();
  if (!tv.auto_box_width) {
  	tv.treeview.find(".tv_box").css("width", "auto");
  	tv.auto_box_width = true;
    if (!readCookie("compact"))
      createCookie("compact", true, tv.cookieDays);
    if (!tv.updating)
    	tv.updateTree();
    b.addClass("tvPressed");
  }
  else {
    var w = tv.boxWidth * (tv.zoom / 100) + "px";
    var ew = tv.boxExpandedWidth * (tv.zoom / 100) + "px";
    tv.treeview.find(".tv_box:not(boxExpanded)", tv.treeview).css("width", w);
    tv.treeview.find(".boxExpanded", tv.treeview).css("width", ew);
    tv.auto_box_width = false;
    if (readCookie("compact"))
      createCookie("compact", false, tv.cookieDays);
    b.removeClass("tvPressed");
  }
  tv.setComplete();
  return false;	
}


/**
 * Class TreeView align method
 */
TreeViewHandler.prototype.align = function(alignment, button) {
	this.setLoading();
  switch (alignment) {
    case "left":
      this.treeview.offset({left: this.container.offset().left, top: this.container.offset().top});
      break;
    case "right":
      this.treeview.offset({left: this.container.offset().left + this.container.width() - this.treeview.width(), top: this.container.offset().top});
      break;
    default:
      return false;
  }
  if (!this.updating)
    this.updateTree(false, button);
  return false;
}

/**
 * Class TreeView centerOnRoot method
 */
TreeViewHandler.prototype.centerOnRoot = function() {
	this.loadingImage.css("display", "block");
  var tvc = this.container;
  var tvcW = tvc.innerWidth() / 2;
  if(isNaN(tvcW))
    return false;
  var tvcH = tvc.innerHeight() / 2;
  var el = jQuery(".rootPerson", this.treeview);
  var dLeft = tvc.offset().left + this.treeview.offset().left + tvcW - el.offset().left - el.outerWidth()/2;
  var dTop = tvc.offset().top + this.treeview.offset().top + tvcH - el.offset().top - el.outerHeight()/2;
  this.treeview.offset({left: dLeft, top: dTop});

  if (!this.updating)
    this.updateTree(true);
  return false;
}

/**
 * Class TreeView style method
 * param string @style the style directory
 */
TreeViewHandler.prototype.style = function(stylepath, style, el) {	
	jQuery("#tvCSS").remove();
	jQuery("#tvStylesSubmenu .tv_button").removeClass("tvPressed");
	if (style)
		jQuery("head").append('<link id="tvCSS" rel="stylesheet" type="text/css" href="' + stylepath + style + '/' + style + '.css">');
	jQuery(el).parent().addClass("tvPressed");
	jQuery("#tvStyleButton").html(jQuery(el).clone());
	createCookie("tvStyle", style, this.cookieDays)
}

/**
 * Class TreeView expandBox method
 * param string @box the person box element
 * param string @event the call event
 * param string @pid the person id
 * 
 * called ONLY for elements which have NOT the class tv_link to avoid unuseful requests to the server
 */
TreeViewHandler.prototype.expandBox = function(box, event) {
	var t = jQuery(event.target);
	if (t.hasClass("tv_link"))
		return false;

  var box = jQuery(box, this.treeview);
  var bc = box.parent(); // bc is Box Container
  var pid = box.attr("abbr");
  var tv = this; // Store "this" for usage within jQuery functions where "this" is not this ;-)
  var expanded;
  var collapsed;
  
  if (bc.hasClass("detailsLoaded")) {
    collapsed = bc.find(".collapsedContent");
    expanded = bc.find(".tv_box:not(.collapsedContent)");
  }
  else {
    // Cache the box content as an hidden person's box in the box's parent element
  	expanded = box;
  	collapsed = box.clone();
  	bc.append(collapsed.addClass("collapsedContent").css("display", "none"));
    // we add a waiting image at the right side of the box
    // TODO : manage rtl (left side instead of right in rtl mode)
    var loadingImage = this.loadingImage.find("img").clone().addClass("tv_box_loading").css("display", "block");
    box.prepend(loadingImage);
    tv.updating = true;
    tv.setLoading();
    // perform the Ajax request and load the result in the box
    box.load(tv.ajaxUrl + "getDetails&pid=" + pid, function() {
      // If Lightbox module is active, we reinitialize it for the new links
      if (typeof CB_Init == "function") {
        CB_Init();
      }
      box.css("width", tv.boxExpandedWidth * (tv.zoom / 100) + "px");
      loadingImage.remove();
      bc.addClass("detailsLoaded");
      tv.setComplete();
      tv.updating = false;
    });
  }
  if (box.hasClass("boxExpanded")) {
		expanded.css("display", "none");
 		collapsed.css("display", "block");
    box.removeClass("boxExpanded");
  }
  else {
 		expanded.css("display", "block");
 		collapsed.css("display", "none");
    expanded.addClass("boxExpanded");
  }
  // we must ajust the draggable treeview size to its content size
  this.getSize();
  return false;
}

/**
 * Class TreeView print method :load full resolution medias for opened details boxes, and open the print dialog after
 */
TreeViewHandler.prototype.print = function() {
	var tv = this;
	var medias = this.treeview.find(".boxExpanded .pedigree_image_portrait, .boxExpanded .pedigree_image_landscape").not(".HiRes");
	if (medias.length) {
		var ml = new Array();
		tv.img2load = new Array();
		medias.each(function(index, media) {
			var alt = jQuery(media).attr("alt");
			if (alt.length) {
				ml.push(jQuery(media).attr("alt"));
				tv.img2load.push(media);
			}
		});
    tv.updating = true;
    tv.setLoading();
    jQuery.ajax({
      url: tv.ajaxUrl + "getMedias",
      dataType: "json",
      data: "q=" + ml.join(";"),
      success: function(ret) {
        var nb = tv.img2load.length;
        for (var i=0;i<nb;i++) {
        	tv.img2load[i].src = ret[i];
        }
      },
      complete: function() {
      	tv.printWhenLoaded();
      },
      timeout: function() {
        tv.updating = false;
        tv.setComplete();
      }
    });
	}
	else
		window.print();
}

/**
 * Class TreeView printIfLoaded method :a callback function called when loading medias is pending
 */
TreeViewHandler.prototype.printWhenLoaded = function() {
	var tv = this;
  var nb = nbImg = tv.img2load.length;
  for (var i=0;i<nbImg;i++) {
  	if (tv.img2load[i].complete) {
  		jQuery(tv.img2load[i]).addClass("HiRes");
  		nb--;
  	}
  }
	if (nb > 0)
		window.setTimeout(tv.printWhenLoaded(), 200);
	tv.setComplete();
	tv.updating = false;
	window.print();
}

function createCookie(name,value,days) {
  if (days) {
    var date = new Date();
    date.setTime(date.getTime()+(days*24*60*60*1000));
    var expires = "; expires="+date.toGMTString();
  }
  else var expires = "";
  document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ')
    	c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0)
    	return c.substring(nameEQ.length,c.length);
  }
  return null;
}

function eraseCookie(name) {
  createCookie(name,"",-1);
}

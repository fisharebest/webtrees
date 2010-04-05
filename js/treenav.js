<!-- @version $Id$ -->
var loadingMessage = "<p style=\"margin: 20px 20px 20px 20px\"><img src=\"images/loading.gif\" alt=\"\" title=\"\" /></p>";

function tempNavObj(target, oXmlHttp, callback) {
	this.processFunc = function()
	{
 			if (oXmlHttp.readyState==4)
 			{
  				evalAjaxJavascript(oXmlHttp.responseText, target);
  				if (callback && callback.callback) callback.callback();
  			}
 		};
}

/* silly IE bug */
function getElementsByNameIE(tag, name) {
	var els = new Array();
	var temps = document.getElementsByTagName(tag);
	j = 0;
	for(i=0; i<temps.length; i++) {
		if (temps[i].name==name) {
			els[j] = temps[i];
			j++;
		}
	}
	return els;
}

function NavTree(outerId, innerId, name, xref) {

	this.innerPort = document.getElementById(innerId);
	this.outerPort = document.getElementById(outerId);
	if (this.innerPort) this.rootTable = this.innerPort.getElementsByTagName("table")[0];
	this.loading = document.getElementById(name+"_loading");
	this.oldText = new Array();
	this.oldWidth = new Array();
	this.opennedBox = new Array();
	this.zoom = 0;
	this.name = name;
	this.collapseBox = true;
	this.allSpouses = true;
	this.rootId = xref;
	this.ajaxCounter = 0;
	
	this.callback = function() { }
	
	this.reInit = function () {
		this.innerPort = document.getElementById('in_'+this.name);
		this.outerPort = document.getElementById('out_'+this.name);
		this.rootTable = this.innerPort.getElementsByTagName("table")[0];
		this.sizeLines();
	}
	
	this.decreaseCounter = function() {
		this.ajaxCounter--;
		if (this.ajaxCounter<=1) {
			this.sizeLines();
			this.restoreCursor();
		}
	}
	
	this.restoreCursor = function() {
		if (this.ajaxCounter>2) return window.setTimeout(this.name+".restoreCursor()", 2000);
		this.outerPort.style.cursor = "";
		this.ajaxCounter = 0;
		this.loading.style.display = "none";
		Behaviour.apply();
	}

	this.sizeLines = function() {
		// -- resize innerport
		if (this.rootTable) {
			this.innerPort.style.width = this.rootTable.offsetWidth + 'px';
			this.innerPort.style.height = this.rootTable.offsetHeight + 'px';
		}
		var vlines;
		if (browser.isIE) vlines = getElementsByNameIE("img", "vertline");
		else vlines = document.getElementsByName("vertline");
		for(i=0; i<vlines.length; i++) {
			id = vlines[i].id.substr(vlines[i].id.indexOf("_")+1);
			outerParent = document.getElementById("ch_"+id);
			children = outerParent.childNodes;
			tables = new Array();
			k=0;
			for(j=0; j<children.length; j++) {
				if (children[j].tagName=='TABLE') {
					tables[k] = children[j];
					k++;
				}
			}
			if (tables.length>0) {
				toptable = tables[0];
				bottable = tables[tables.length-1];
				
				y1 = findPosY(toptable);
				y1 = y1 + (toptable.offsetHeight/2);
				y2 = findPosY(bottable);
				y2 = y2 + (bottable.offsetHeight/2);
				
				vlines[i].style.top = y1+'px';
				vlines[i].style.height = (y2-y1)+'px';
			}
		}
		//-- parent lines
		if (browser.isIE) vlines = getElementsByNameIE("img", "pvertline");
		else vlines = document.getElementsByName("pvertline");
		for(i=0; i<vlines.length; i++) {
			ids = vlines[i].id.split("_");
			var y1 = 0;
			var y2 = 0;
			if (ids.length>1) {
				toptable = document.getElementById('box_'+ids[1]);
				bottable = document.getElementById('box_'+ids[2]);
				if (toptable) {
					y1 = findPosY(toptable);
					y1 = y1 + (toptable.offsetHeight/2);
					if (!bottable) {
						y2 = y1 + (toptable.offsetHeight/2);
					}
				}
				if (bottable) {
					y2 = findPosY(bottable);
					y2 = y2 + (bottable.offsetHeight/2);
					if (!toptable) {
						y1 = y2 + (bottable.offsetHeight/2);
					}
				}
				vlines[i].style.top = y1+'px';
				vlines[i].style.height = (y2-y1)+'px';
				
			}
		}
	}

	this.loadChild = function(target, xref) {
		oXmlHttp = createXMLHttp();
		link = "treenav.php?navAjax=1&jsname="+this.name+"&rootid="+xref+"&zoom="+this.zoom;
		link = link + "&allSpouses="+this.allSpouses;
		oXmlHttp.open("get", link, true);
		this.ajaxCounter++;
		this.callback = this.decreaseCounter;
		temp = new tempNavObj(target, oXmlHttp, this);
		oXmlHttp.onreadystatechange=temp.processFunc;
 		oXmlHttp.send(null);
 		target.onclick=null;
 		target.name=null;
	}

	this.loadParent = function(target, xref, ptype) {
		oXmlHttp = createXMLHttp();
		link = "treenav.php?navAjax=1&jsname="+this.name+"&rootid="+xref+"&parent="+ptype+"&zoom="+this.zoom;
		link = link + "&allSpouses="+this.allSpouses;
		oXmlHttp.open("get", link, true);
		this.ajaxCounter++;
		this.callback = this.decreaseCounter;
		temp = new tempNavObj(target, oXmlHttp, this);
		oXmlHttp.onreadystatechange=temp.processFunc;
 		oXmlHttp.send(null);
 		target.onclick=null;
 		target.name=null;
	}


	this.expandBox = function(target, xref, famid) {
		if (!this.collapseBox) {
			this.collapseBox = true;
			return;
		}
		if (this.oldText[xref]) {
			if (this.opennedBox[xref]) this.opennedBox[xref] = false;
			else this.opennedBox[xref] = true;
			
			temp = target.innerHTML;
			target.innerHTML = this.oldText[xref];
			this.oldText[xref] = temp;
			
			temp = target.style.width;
			target.style.width=this.oldWidth[xref];
			this.oldWidth[xref] = temp;
			this.sizeLines();
			return;
		}
				
		this.oldText[xref] = target.innerHTML;
		this.oldWidth[xref] = target.style.width;
		this.opennedBox[xref] = true;
		
		oXmlHttp = createXMLHttp();
		link = "treenav.php?navAjax=1&jsname="+this.name+"&rootid="+xref+"&famid="+famid+"&details=1&zoom="+this.zoom;
		link = link + "&allSpouses="+this.allSpouses;
		oXmlHttp.open("get", link, true);
		this.callback = this.expandCallback;
		temp = new tempNavObj(target, oXmlHttp, this);
		oXmlHttp.onreadystatechange=temp.processFunc;
 		oXmlHttp.send(null);
 		target.style.width='200px';
	}
	
	this.expandCallback = function() {
		this.sizeLines();
		Behaviour.apply();
	}

	this.newRoot = function(xref, element, gedcom) {
		oXmlHttp = createXMLHttp();
		link = "treenav.php?navAjax=1&jsname="+this.name+"&rootid="+xref+"&newroot=1&zoom="+this.zoom;
		link = link + "&allSpouses="+this.allSpouses;	
		if (gedcom) link += "&ged="+gedcom;
		
		this.rootId = xref;
		
		oXmlHttp.open("get", link, true);
		if (!element) element=this.innerPort;
		this.callback = this.center;
		temp = new tempNavObj(element, oXmlHttp, this);
		element.style.left='10px';
		element.style.top='0px';
		element.innerHTML = loadingMessage;
		oXmlHttp.onreadystatechange=temp.processFunc;
 		oXmlHttp.send(null);
 		var biglink = document.getElementById("biglink");
 		biglink.parentNode.style.display="block";
 		return false;
	}
	
	this.toggleSpouses = function(xref) {
		if (this.allSpouses) this.allSpouses = false;
		else this.allSpouses = true;
	
		if (!xref || xref=='') xref = this.rootId; 
		this.newRoot(xref);
	}
	
	this.loadBigTree = function(xref, gedcom) {
		if (!xref || xref=='') xref = this.rootId;
		link = "treenav.php?jsname="+this.name+"&rootid="+xref+"&newroot=1";
		link = link + "&allSpouses="+this.allSpouses;
		if (gedcom) link += "&ged="+gedcom;
		window.location = link;
	}
	
	this.center = function() {
		this.reInit();
		this.sizeLines();
		Behaviour.apply();
		//-- load up any other people to fill in the page
		this.loadChildren(this.innerPort);
 		this.loadParents(this.innerPort);
 		if (this.rootTable) {
			x = this.rootTable.offsetWidth/2;
			y = this.rootTable.offsetHeight/2;
			cx = this.outerPort.offsetWidth/2;
			cy = this.outerPort.offsetHeight/2;
			x = cx-x;
			y = cy-y;
			x = (this.innerPort.offsetLeft+x);
			y = (this.innerPort.offsetTop+y);
			//alert(x+" "+y);
			this.innerPort.style.top = y+'px';
			this.innerPort.style.left = x+'px';
		}
	}

	this.drawViewport = function(element) {
		oXmlHttp = createXMLHttp();
		link = "treenav.php?navAjax=1&jsname="+this.name+"&newroot=1&drawport=1&zoom=-2";
		link = link + "&allSpouses="+this.allSpouses;
		oXmlHttp.open("get", link, true);
		this.callback = this.sizeLines;
		temp = new tempNavObj(element, oXmlHttp, this);
		element.innerHTML = loadingMessage;
		oXmlHttp.onreadystatechange=temp.processFunc;
	 	oXmlHttp.send(null);
	 	return false;
	}
	
	this.zoomIn = function() {
		boxes = this.innerPort.getElementsByTagName("div");
		for(i=0; i<boxes.length; i++) {
			child = boxes[i];
			child.style.width = (parseInt(child.style.width) + 18)+'px';
			child.style.fontSize = (parseInt(child.style.fontSize)+1)+'px';
			this.zoomImgs(child);
		}
		this.zoom++;
		this.sizeLines();
		this.loadParents(this.innerPort);
		this.loadChildren(this.innerPort);
	}

	this.zoomOut = function() {
		boxes = this.innerPort.getElementsByTagName("div");
		for(i=0; i<boxes.length; i++) {
			child = boxes[i];
			child.style.width = (parseInt(child.style.width) - 18)+'px';
			child.style.fontSize = (parseInt(child.style.fontSize)-1)+'px'; 
			this.zoomImgs(child);
		}
		this.zoom--;
		this.sizeLines();
		this.loadParents(this.innerPort);
		this.loadChildren(this.innerPort);
	}

	this.zoomImgs = function(child) {
		imgs = child.getElementsByTagName("img");
		for(j=0; j<imgs.length; j++) {
			if (this.zoom<-1) imgs[j].style.display = 'none';
			else {
				imgs[j].style.display = 'inline';
				imgs[j].style.width = (10+this.zoom)+'px';
				imgs[j].style.height = (10+this.zoom)+'px';
			}
		}
	}
	
	/**
	 * Check if any of the parent boxes need to be loaded
	 */
	this.loadParents = function(elNode) {
		if (elNode && this.rootTable && elNode.offsetLeft + this.rootTable.offsetWidth < this.outerPort.offsetWidth+40) {
		  	var chil = document.getElementsByName(this.name+'_pload');
		  	if (chil.length>0) {
		  		//-- give the user some feedback that we are loading data
		  		this.outerPort.style.cursor = "wait";
		  		//-- prevent the wait from staying on forever
		  		window.setTimeout(this.name+".restoreCursor()", 2000);
		  		this.loading.style.display = "block";
			  	for(i=0; i<chil.length; i++) {
			  		if (chil[i] && chil[i].onclick) {
			  			cell = chil[i];
			  			y = findPosY(cell);
			  			if (y < this.outerPort.offsetHeight + this.outerPort.offsetTop) {
			  				if (cell.onclick) {
			  					cell.onclick();
			  				}
			  			}
			  		}
			  	}
		  	}
	  	}
	}
	
	/**
	 * Check if any of the children boxes need to be loaded
	 */
	this.loadChildren = function(elNode) {
		if (elNode && elNode.offsetLeft > -40) {
		  	children = document.getElementsByName(this.name+'_cload');
		  	if (children.length>0) {
		  		//-- give the user some feedback that we are loading data
		  		this.outerPort.style.cursor = "wait";
		  		//-- prevent the wait from staying on forever
		  		window.setTimeout(this.name+".restoreCursor()", 2000);
		  		this.loading.style.display = "block";
			  	for(i=0; i<children.length; i++) {
			  		if (children[i] && children[i].onclick) {
			  			cell = children[i];
			  			x = findPosX(cell);
			  			y = findPosY(cell);
			  			if (x > -10 && y < this.outerPort.offsetHeight + this.outerPort.offsetTop) {
			  				if (cell.onclick) {
			  					cell.onclick();
			  				}
			  			}
			  		}
			  	}
		  	}
		 }
	}
}
  
	
// Browser check
function Browser() {

  var ua, s, i;

  this.isIE    = false;
  this.isNS    = false;
  this.isOpera = false;
  this.version = null;

  ua = navigator.userAgent;

  s = "MSIE";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isIE = true;
    this.version = parseFloat(ua.substr(i + s.length));
    return;
  }

  s = "Netscape6/";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isNS = true;
    this.version = parseFloat(ua.substr(i + s.length));
    return;
  }

  s = "Opera";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isOpera = true;
	this.version = parseFloat(ua.substr(i + s.length + 1));
    return;
  }
  
  // Treat any other "Gecko" browser as NS 6.1.

  s = "Gecko";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isNS = true;
    this.version = 6.1;
    return;
  }
}

var browser = new Browser();

// Global object to hold drag information.

var dragObj = new Object();
dragObj.zIndex = 0;
var curLeft = 0;

// Start dragging the chart
function dragStart(event, id, nav) {
curLeft = 0;
  var el;
  var x, y;

  // If an element id was given, find it. Otherwise use the element being
  // clicked on.
  if (id)
    dragObj.elNode = document.getElementById(id);
  else {
    if (browser.isIE || browser.isOpera)
      dragObj.elNode = window.event.srcElement;
    if (browser.isNS)
      dragObj.elNode = event.target;
    // If this is a text node, use its parent element.

    if (dragObj.elNode.nodeType == 3)
      dragObj.elNode = dragObj.elNode.parentNode;
  }
  
  dragObj.nav = nav;
  dragObj.nav.reInit();

  // Get cursor position with respect to the page.

  if (browser.isIE || browser.isOpera) {
    x = window.event.clientX + document.documentElement.scrollLeft
      + document.body.scrollLeft;
    y = window.event.clientY + document.documentElement.scrollTop
      + document.body.scrollTop;
  }
  if (browser.isNS) {
    x = event.clientX + window.scrollX;
    y = event.clientY + window.scrollY;
  }

  // Save starting positions of cursor and element.

  dragObj.cursorStartX = x;
  dragObj.cursorStartY = y;
  dragObj.elStartLeft  = parseInt(dragObj.elNode.style.left, 10);
  dragObj.elStartTop   = parseInt(dragObj.elNode.style.top,  10);

  if (isNaN(dragObj.elStartLeft)) dragObj.elStartLeft = 0;
  if (isNaN(dragObj.elStartTop))  dragObj.elStartTop  = 0;

  // Update element's z-index.

  dragObj.elNode.style.zIndex = ++dragObj.zIndex;

  // Capture mousemove and mouseup events on the page.

  if (browser.isIE || browser.isOpera) {
    document.attachEvent("onmousemove", dragGo);
    document.attachEvent("onmouseup",   dragStop);
    window.event.cancelBubble = true;
    window.event.returnValue = false;
  }
  if (browser.isNS) {
    document.addEventListener("mousemove", dragGo,   true);
    document.addEventListener("mouseup",   dragStop, true);
    event.preventDefault();
  }
}

// The actual movement of the chart happens here
function dragGo(event) {

  var x, y;

  // Get cursor position with respect to the page.

  if (browser.isIE || browser.isOpera) {
    x = window.event.clientX + document.documentElement.scrollLeft
      + document.body.scrollLeft;
    y = window.event.clientY + document.documentElement.scrollTop
      + document.body.scrollTop;
  }
  if (browser.isNS) {
    x = event.clientX + window.scrollX;
    y = event.clientY + window.scrollY;
  }
  
  // Move drag element by the same amount the cursor has moved.
  dragObj.elNode.style.left = (dragObj.elStartLeft + x - dragObj.cursorStartX) + "px";
  dragObj.elNode.style.top  = (dragObj.elStartTop  + y - dragObj.cursorStartY) + "px";
  
  if (browser.isIE) {
    window.event.cancelBubble = true;
    window.event.returnValue = false;
  }
  if (browser.isNS || browser.isOpera)
    event.preventDefault();
  
  //-- load children by ajax  
  dragObj.nav.loadChildren(dragObj.elNode);
  
  //-- load parents by ajax
  dragObj.nav.loadParents(dragObj.elNode);
}

// Stop dragging the chart
function dragStop(event) {

	//dragObj.nav.collapseBox = true;
	
  // Stop capturing mousemove and mouseup events.
  if (browser.isIE || browser.isOpera) {
    document.detachEvent("onmousemove", dragGo);
    document.detachEvent("onmouseup",   dragStop);
  }
  if (browser.isNS) {
    document.removeEventListener("mousemove", dragGo,   true);
    document.removeEventListener("mouseup",   dragStop, true);
  }
  Behaviour.apply();
}

/**
 * Common javascript functions
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * Modifications Copyright (c) 2010 Greg Roach
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Display
 * @version $Id$
 */
if (!document.getElementById)	// Check if browser supports the getElementByID function
{
	curloc = window.location.toString();
	if (curloc.indexOf('nosupport.php')==-1) window.location.href = "nosupport.php";
}

var helpWin;
function helpPopup(which, mod) {
	if (which==null) which = "help_contents_help";
	if (mod!='') which=which+'&mod='+mod;
	if ((!helpWin)||(helpWin.closed)) {
		helpWin = window.open('help_text.php?help='+which,'_blank','left=50,top=50,width=500,height=320,resizable=1,scrollbars=1');
	} else {
		helpWin.location = 'help_text.php?help='+which;
	}
	return false;
}
function closeHelp() {
	if (helpWin) helpWin.close();
}

function openImage(filename, width, height) {
	height=height+50;
	screenW = screen.width;
	screenH = screen.height;
	if (width>screenW-100) width=screenW-100;
	if (height>screenH-110) height=screenH-120;
//	if (filename.search(/\.(jpe?g|gif|png)$/gi)!=-1)
		window.open('imageview.php?filename='+filename,'_blank','top=50,left=50,height='+height+',width='+width+',scrollbars=1,resizable=1');
//	else window.open(unescape(filename),'_blank','top=50,left=50,height='+height+',width='+width+',scrollbars=1,resizable=1');
	return false;
}

// variables to hold mouse x-y pos.s
	var msX = 0;
	var msY = 0;

//  the following javascript functions are for the positioning and hide/show of
//  DIV layers used in the display of the pedigree chart.
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_showHideLayers() { //v6.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-3); i+=4) {
	  if ((obj=MM_findObj(args[i]))!=null) {
    	if (obj.style) {
	      div=obj;
	      obj=obj.style;
	    }
	    v=args[i+2];
	    if (v=='toggle') {
		    if (obj.visibility.indexOf('hid')!=-1) v='show';
		    else v='hide';
	    }
	    v=(v=='show')?'visible':(v=='hide')?'hidden':v;
    	obj.visibility=v;
    	if (args[i+1]=='followmouse') {
	    	pobj = MM_findObj(args[i+3]);
	    	if (pobj!=null) {
//		    	if (pobj.style.top!="auto") {
		    	if (pobj.style.top!="auto" && args[i+3]!="relatives") {
			    	obj.top=5+msY-parseInt(pobj.style.top)+'px';
			    	if (textDirection=="ltr") obj.left=5+msX-parseInt(pobj.style.left)+'px';
			    	if (textDirection=="rtl") obj.right=5+msX-parseInt(pobj.style.right)+'px';
		    	}
		    	else {
			    	obj.top="auto";
			    	//obj.left="80%";
			    	pagewidth = document.documentElement.offsetWidth+document.documentElement.scrollLeft;
			    	if (textDirection=="rtl") pagewidth -= document.documentElement.scrollLeft;
			    	if (msX > pagewidth-160) msX = msX-150-pobj.offsetLeft;
			    	contentdiv = document.getElementById("content");
			    	msX = msX - contentdiv.offsetLeft;
			    	if (textDirection=="ltr") obj.left=(5+msX)+'px';
			    	obj.zIndex=1000;
		    	}
	    	}
	    	else {
	    		//obj.top="auto";
	    		if (SCRIPT_NAME.indexOf("fanchart")>0) {
		    		obj.top=(msY-20)+'px';
			    	obj.left=(msX-20)+'px';
	    		}
	    		else if (SCRIPT_NAME.indexOf("index.php")==-1) {
		    		Xadjust = document.getElementById('content').offsetLeft;
		    		obj.left=(5+(msX-Xadjust))+'px';
		    		obj.top="auto";
	    		}
	    		else {
		    		Xadjust = document.getElementById('content').offsetLeft;
		    		obj.top=(msY-50)+'px';
			    	obj.left=(10+(msX-Xadjust))+'px';
	    		}
	    		obj.zIndex=1000;
    		}
    	}
    }
  }
}

var show = false;
	function togglechildrenbox(pid) {
		if (!pid) pid='';
		else pid = '.'+pid;
		if (show) {
			MM_showHideLayers('childbox'+pid, ' ', 'hide',' ');
			show=false;
		}
		else {
			MM_showHideLayers('childbox'+pid, ' ', 'show', ' ');
			show=true;
		}
		return false;
	}

	function togglefavoritesbox() {
		favsbox = document.getElementById("favs_popup");
		if (favsbox) {
			if (favsbox.style.visibility=="visible") {
				MM_showHideLayers('favs_popup', ' ', 'hide',' ');
			}
			else {
				MM_showHideLayers('favs_popup', ' ', 'show', ' ');
			}
		}
		return false;
	}

	var lastfamilybox = "";
	var popupopen = 0;
	function show_family_box(boxid, pboxid) {
		popupopen = 1;
		lastfamilybox=boxid;
		if (pboxid=='relatives') MM_showHideLayers('I'+boxid+'links', 'followmouse', 'show',''+pboxid);
		else {
			famlinks = document.getElementById("I"+boxid+"links");
			divbox = document.getElementById("out-"+boxid);
			parentbox = document.getElementById("box"+boxid);
			//alert(famlinks+" "+divbox+" "+parentbox);
			if (famlinks && divbox && parentbox) {
				famlinks.style.top = "0px";
				if (textDirection=="ltr") famleft = parseInt(divbox.style.width)+15;
				else famleft = 0;
				if (isNaN(famleft)) {
					famleft = 0;
					famlinks.style.top = parentbox.offsetTop+"px";
				}
				pagewidth = document.documentElement.offsetWidth+document.documentElement.scrollLeft;
				if (textDirection=="rtl") pagewidth -= document.documentElement.scrollLeft;
				if (famleft+parseInt(parentbox.style.left) > pagewidth-100) famleft=25;
				famlinks.style.left = famleft + "px";
				if (SCRIPT_NAME.indexOf("index.php")!=-1) famlinks.style.left = "100%";
				MM_showHideLayers('I'+boxid+'links', ' ', 'show',''+pboxid);
				return;
			}
			MM_showHideLayers('I'+boxid+'links', 'followmouse', 'show',''+pboxid);
		}
	}

	function toggle_family_box(boxid, pboxid) {
		if (popupopen==1) {
			MM_showHideLayers('I'+lastfamilybox+'links', ' ', 'hide',''+pboxid);
			popupopen = 0;
		}
		if (boxid==lastfamilybox) {
			lastfamilybox = "";
			return;
		}
		popupopen = 1;
		lastfamilybox=boxid;
		if (pboxid=='relatives') MM_showHideLayers('I'+boxid+'links', 'followmouse', 'show',''+pboxid);
		else {
			famlinks = document.getElementById("I"+boxid+"links");
			divbox = document.getElementById("out-"+boxid);
			parentbox = document.getElementById("box"+boxid);
			if (!parentbox) parentbox = document.getElementById(pboxid+".0");
			if (famlinks && divbox && parentbox) {
				divWidth = parseInt(divbox.style.width);
				linkWidth = parseInt(famlinks.style.width);
				parentWidth = parseInt(parentbox.style.width);
				//alert('Widths div:'+divWidth+' parent:'+parentWidth+' links:'+linkWidth);
				famlinks.style.top = "3px";
				famleft = divWidth+8;
				if (textDirection=="rtl") {
					famleft -= (divWidth+linkWidth+5);
					if (browserType!="mozilla") famleft -= 11;
				}
				pagewidth = document.documentElement.offsetWidth+document.documentElement.scrollLeft;
				//alert(pagewidth);
				if (famleft+parseInt(parentbox.style.left) > pagewidth-100) famleft=25;
				famlinks.style.left = famleft + "px";
				if (SCRIPT_NAME.indexOf("index.php")!=-1) famlinks.style.left = "100%";
				MM_showHideLayers('I'+boxid+'links', ' ', 'show',''+pboxid);
			}
			else MM_showHideLayers('I'+boxid+'links', 'followmouse', 'show',''+pboxid);
		}
	}

	function hide_family_box(boxid) {
		MM_showHideLayers('I'+boxid+'links', '', 'hide','');
		popupopen = 0;
		lastfamilybox="";
	}

	var timeouts = new Array();
	function family_box_timeout(boxid) {
		tout = setTimeout("hide_family_box('"+boxid+"')", 2500);
		timeouts[boxid] = tout;
	}

	function clear_family_box_timeout(boxid) {
		clearTimeout(timeouts[boxid]);
	}

	function expand_layer(sid,show) {
		var sbox = document.getElementById(sid);
		var sbox_img = document.getElementById(sid+"_img");
		var sbox_style = sbox.style;
		if (show===true) {
			sbox_style.display='block';
			if (sbox_img) {
				sbox_img.src = plusminus[1].src;
				sbox_img.title = plusminus[1].title;
			}
		}
		else if (show===false) {
			sbox_style.display='none';
			if (sbox_img) {
				sbox_img.src = plusminus[0].src;
				sbox_img.title = plusminus[0].title;
			}
		}
		else {
			if ((sbox_style.display=='none')||(sbox_style.display=='')) {
				sbox_style.display='block';
				if (sbox_img) {
					sbox_img.src = plusminus[1].src;
					sbox_img.title = plusminus[1].title;
				}
			}
			else {
				sbox_style.display='none';
				if (sbox_img) {
					sbox_img.src = plusminus[0].src;
					sbox_img.title = plusminus[0].title;
				}
			}
		}
		//if (!lasttab) lasttab=0;
		return false;
	}

	//-- function used for mouse overs of arrows
	//- arrow is the id of the arrow to swap
	//- index is the index into the arrows array
	//- set index=0 for left pointing arrows
	//- set index=1 for right pointing arrows
	//- set index=2 for up pointing arrows
	//- set index=3 for down pointing arrows
	function swap_image(arrow, index) {
		arrowimg = document.getElementById(arrow);
		tmp = arrowimg.src;
		arrowimg.src = arrows[index].src;
		arrows[index].src = tmp;
	}

// Main function to retrieve mouse x-y pos.s
function getMouseXY(e) {
  if (IE) { // grab the x-y pos.s if browser is IE
    msX = event.clientX + document.documentElement.scrollLeft;
    msY = event.clientY + document.documentElement.scrollTop;
  } else {  // grab the x-y pos.s if browser is NS
    msX = e.pageX;
    msY = e.pageY;
  }
  return true;
}

function edit_record(pid, linenum) {
	window.open('edit_interface.php?action=edit&pid='+pid+'&linenum='+linenum+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function edit_raw(pid) {
	window.open('edit_interface.php?action=editraw&pid='+pid+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=400,height=400,resizable=1,scrollbars=1');
	return false;
}

function edit_note(pid) {
	window.open('edit_interface.php?action=editnote&pid='+pid+'&linenum=1&'+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function edit_source(pid) {
	window.open('edit_interface.php?action=editsource&pid='+pid+'&linenum=1&'+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function add_record(pid, fact) {
	factfield = document.getElementById(fact);
	if (factfield) {
		factvalue = factfield.options[factfield.selectedIndex].value;
		if (factvalue == "OBJE") window.open('addmedia.php?action=showmediaform&linkid='+pid, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
		else window.open('edit_interface.php?action=add&pid='+pid+'&fact='+factvalue+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	}
	return false;
}

function addClipboardRecord(pid, fact) {
	factfield = document.getElementById(fact);
	if (factfield) {
		factvalue = factfield.options[factfield.selectedIndex].value;
		window.open('edit_interface.php?action=paste&pid='+pid+'&fact='+factvalue.substr(10)+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	}
	return false;
}

function add_new_record(pid, fact) {
		window.open('edit_interface.php?action=add&pid='+pid+'&fact='+fact+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function addnewchild(famid,gender) {
	window.open('edit_interface.php?action=addchild&gender='+gender+'&famid='+famid+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function addnewspouse(famid, famtag) {
	window.open('edit_interface.php?action=addspouse&famid='+famid+'&famtag='+famtag+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function addopfchild(pid, gender) {
	window.open('edit_interface.php?action=addopfchild&pid='+pid+'&gender='+gender+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function addspouse(pid, famtag) {
	window.open('edit_interface.php?action=addspouse&pid='+pid+'&famtag='+famtag+'&famid=new&'+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function linkspouse(pid, famtag) {
	window.open('edit_interface.php?action=linkspouse&pid='+pid+'&famtag='+famtag+'&famid=new&'+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function add_famc(pid) {
	 window.open('edit_interface.php?action=addfamlink&pid='+pid+'&famtag=CHIL'+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function add_fams(pid, famtag) {
	 window.open('edit_interface.php?action=addfamlink&pid='+pid+'&famtag='+famtag+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function edit_name(pid, linenum) {
	window.open('edit_interface.php?action=editname&pid='+pid+'&linenum='+linenum+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function add_name(pid) {
	window.open('edit_interface.php?action=addname&pid='+pid+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function addnewparent(pid, famtag) {
	window.open('edit_interface.php?action=addnewparent&pid='+pid+'&famtag='+famtag+'&famid=new'+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function addnewparentfamily(pid, famtag, famid) {
	window.open('edit_interface.php?action=addnewparent&pid='+pid+'&famtag='+famtag+'&famid='+famid+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function copy_record(pid, linenum) {
	window.open('edit_interface.php?action=copy&pid='+pid+'&linenum='+linenum+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function reorder_children(famid) {
	window.open('edit_interface.php?action=reorder_children&pid='+famid+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function reorder_families(pid) {
	window.open('edit_interface.php?action=reorder_fams&pid='+pid+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function chat(username) {
	alert('This feature is not implement yet');
	return false;
}

function reply(username, subject) {
	window.open('message.php?to='+username+'&subject='+subject+"&"+sessionname+"="+sessionid, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function delete_message(id) {
	window.open('message.php?action=delete&id='+id+"&"+sessionname+"="+sessionid, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function delete_family(famid) {
	window.open('edit_interface.php?famid='+famid+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime+"&action=deletefamily", '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function deletesource(pid) {
	 window.open('edit_interface.php?action=deletesource&pid='+pid+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 return false;
}

function deletenote(pid) {
	 window.open('edit_interface.php?action=deletenote&pid='+pid+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	 return false;
}

function edit_family(famid) {
	window.open('edit_interface.php?famid='+famid+'&linenum=1&'+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime+"&action=edit_family", '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function change_family_members(famid) {
	window.open('edit_interface.php?famid='+famid+"&"+sessionname+"="+sessionid+"&accesstime="+accesstime+"&action=changefamily", '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function addnewsource(field) {
	pastefield = field;
	window.open('edit_interface.php?action=addnewsource&pid=newsour', '_blank', 'top=70,left=70,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}
function addnewnote(field) {
	pastefield = field;
	window.open('edit_interface.php?action=addnewnote&noteid=newnote', '_blank', 'top=70,left=70,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}
function addnewnote_assisted(field, iid) {
	pastefield = field;
	window.open('edit_interface.php?action=addnewnote_assisted&noteid=newnote&pid='+iid, '_blank', 'top=70,left=70,width=870,height=726,scrollbars=no,resizable=no');
	return false;
}
function addmedia_links(field, iid, iname) {
	pastefield = field;
	insertRowToTable(iid, iname);
	return false;
}

function valid_date(datefield) {
	var months = new Array("JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC");

	var datestr=datefield.value;
	// if a date has a date phrase marked by () this has to be excluded from altering
	var datearr=datestr.split("(");
	var datephrase="";
	if (datearr.length > 1) {
		datestr=datearr[0];
		datephrase=datearr[1];
	}

	// Gedcom dates are upper case
	datestr=datestr.toUpperCase();
	// Gedcom dates have no leading/trailing/repeated whitespace
	datestr=datestr.replace(/\s+/, " ");
	datestr=datestr.replace(/(^\s)|(\s$)/, "");
	// Gedcom dates have spaces between letters and digits, e.g. "01JAN2000" => "01 JAN 2000"
	datestr=datestr.replace(/(\d)([A-Z])/, "$1 $2");
	datestr=datestr.replace(/([A-Z])(\d)/, "$1 $2");

	// Shortcut for quarter format, "Q1 1900" => "BET JAN 1900 AND MAR 1900".  See [ 1509083 ]
 	if (datestr.match(/^Q ([1-4]) (\d\d\d\d)$/)) {
		datestr = "BET "+months[RegExp.$1*3-3]+" "+RegExp.$2+" AND "+months[RegExp.$1*3-1]+" "+RegExp.$2;
	}

	// e.g. 17.11.1860, 03/04/2005 or 1999-12-31.  Use locale settings where DMY order is ambiguous.
	var qsearch = /(.*)(\d+)[^\d](\d+)[^\d](\d+)(.*)/i;
 	if (qsearch.exec(datestr)) {
 		var f0=RegExp.$1;
		var f1=parseInt(RegExp.$2, 10);
		var f2=parseInt(RegExp.$3, 10);
		var f3=parseInt(RegExp.$4, 10);
 		var f4=RegExp.$5;
		var dmy='DMY';
		if (typeof(locale_date_format)!='undefined')
			if (locale_date_format=='MDY' || locale_date_format=='YMD')
				dmy=locale_date_format;
		var yyyy=new Date().getFullYear();
		var yy=yyyy % 100;
		var cc=yyyy - yy;
	 	if (dmy=='DMY' && f1<=31 && f2<=12 || f1>13 && f1<=31 && f2<=12 && f3>31)
			datestr=f0+f1+" "+months[f2-1]+" "+(f3>=100?f3:(f3<=yy?f3+cc:f3+cc-100))+f4;
		else if (dmy=='MDY' && f1<=12 && f2<=31 || f2>13 && f2<=31 && f1<=12 && f3>31)
			datestr=f0+f2+" "+months[f1-1]+" "+(f3>=100?f3:(f3<=yy?f3+cc:f3+cc-100))+f4;
		else if (dmy=='YMD' && f2<=12 && f3<=31 || f3>13 && f3<=31 && f2<=12 && f1>31)
			datestr=f0+f3+" "+months[f2-1]+" "+(f1>=100?f1:(f1<=yy?f1+cc:f1+cc-100))+f4;
	}

	// Shortcuts for date ranges
	datestr=datestr.replace(/^[~*]([\w ]+)$/, "ABT $1");
	datestr=datestr.replace(/^[>+]([\w ]+)$/, "AFT $1");
	datestr=datestr.replace(/^([\w ]+)[-/]$/, "AFT $1");
	datestr=datestr.replace(/^[</-]([\w ]+)$/, "BEF $1");
	datestr=datestr.replace(/^([\w ]+) ?- ?([\w ]+)$/, "BET $1 AND $2");
	if (datestr.match(/^=([\d ()/+*-]+)$/)) datestr=eval(RegExp.$1);

	// Americans frequently enter dates as SEPTEMBER 20, 1999
	// No need to internationalise this, as this is an english-language issue
	datestr=datestr.replace(/(JAN)(?:UARY)? (\d\d?)[, ]+(\d\d\d\d)/, "$2 $1 $3");
	datestr=datestr.replace(/(FEB)(?:RUARY)? (\d\d?)[, ]+(\d\d\d\d)/, "$2 $1 $3");
	datestr=datestr.replace(/(MAR)(?:CH)? (\d\d?)[, ]+(\d\d\d\d)/, "$2 $1 $3");
	datestr=datestr.replace(/(APR)(?:IL)? (\d\d?)[, ]+(\d\d\d\d)/, "$2 $1 $3");
	datestr=datestr.replace(/(MAY) (\d\d?)[, ]+(\d\d\d\d)/, "$2 $1 $3");
	datestr=datestr.replace(/(JUN)(?:E)? (\d\d?)[, ]+(\d\d\d\d)/, "$2 $1 $3");
	datestr=datestr.replace(/(JUL)(?:Y)? (\d\d?)[, ]+(\d\d\d\d)/, "$2 $1 $3");
	datestr=datestr.replace(/(AUG)(?:UST)? (\d\d?)[, ]+(\d\d\d\d)/, "$2 $1 $3");
	datestr=datestr.replace(/(SEP)(?:TEMBER)? (\d\d?)[, ]+(\d\d\d\d)/, "$2 $1 $3");
	datestr=datestr.replace(/(OCT)(?:OBER)? (\d\d?)[, ]+(\d\d\d\d)/, "$2 $1 $3");
	datestr=datestr.replace(/(NOV)(?:EMBER)? (\d\d?)[, ]+(\d\d\d\d)/, "$2 $1 $3");
	datestr=datestr.replace(/(DEC)(?:EMBER)? (\d\d?)[, ]+(\d\d\d\d)/, "$2 $1 $3");

	// Apply leading zero to day numbers
	datestr=datestr.replace(/(^| )(\d [A-Z]{3,5} \d{4})/, "$10$2");

	if (datephrase != "") {
		datestr=datestr+" ("+datephrase;
	}
	datefield.value=datestr;
}

var oldheight = 0;
var oldwidth = 0;
var oldz = 0;
var oldleft = 0;
var big = 0;
var oldboxid = "";
var oldimgw = 0;
var oldimgh = 0;
var oldimgw1 = 0;
var oldimgh1 = 0;
var diff = 0;
var oldfont = 0;
var oldname = 0;
var oldthumbdisp = 0;
var repositioned = 0;
var oldiconsdislpay = 0;

function expandbox(boxid, bstyle) {
	if (big==1) {
		restorebox(oldboxid, bstyle);
		if (boxid==oldboxid) return true;
	}
	url = window.location.toString();
	divbox = document.getElementById("out-"+boxid);
	inbox = document.getElementById("inout-"+boxid);
	inbox2 = document.getElementById("inout2-"+boxid);
	parentbox = document.getElementById("box"+boxid);
	if (!parentbox) {
		parentbox=divbox;
	//	if (bstyle!=2) divbox.style.position="absolute";
	}
	gender = document.getElementById("box-"+boxid+"-gender");
	thumb1 = document.getElementById("box-"+boxid+"-thumb");
	famlinks = document.getElementById("I"+boxid+"links");
	icons = document.getElementById("icons-"+boxid);
	iconz = document.getElementById("iconz-"+boxid);	// This is the Zoom icon

	if (divbox) {
		if (icons) {
		oldiconsdislpay = icons.style.display;
		icons.style.display = "block";
		}
		if (iconz) {
			if (iconz.src==zoominout[0].src) iconz.src = zoominout[1].src;
			else iconz.src = zoominout[0].src;
		}
		oldboxid=boxid;
		big = 1;
		oldheight=divbox.style.height;
		oldwidth=divbox.style.width;
		oldz = parentbox.style.zIndex;
		if (url.indexOf("descendancy.php")==-1) parentbox.style.zIndex='100';
		if (bstyle!=2) {
			divbox.style.width='350px';
			diff = 350-parseInt(oldwidth);
			if (famlinks) {
				famleft = parseInt(famlinks.style.left);
				famlinks.style.left = (famleft+diff)+"px";
			}
			//parentbox.style.width = parseInt(parentbox.style.width)+diff;
		}
		divleft = parseInt(parentbox.style.left);
		if (textDirection=="rtl") divleft = parseInt(parentbox.style.right);
		oldleft=divleft;
		divleft = divleft - diff;
		repositioned = 0;
		if (divleft<0) {
			repositioned = 1;
			divleft=0;
		}
		if (url.indexOf("pedigree.php")!=-1) {
			if (textDirection=="ltr") parentbox.style.left=divleft+"px";
			//else parentbox.style.right=divleft+"px";
		}
		divbox.style.height='auto';
		if (inbox)
		{
			inbox.style.display='block';
			if ( inbox.innerHTML.indexOf("LOADING")>0 )
			{
				//-- load data from expand_view.php
				var pid = boxid.split(".")[0];
				var oXmlHttp = createXMLHttp();
				oXmlHttp.open("get", "expand_view.php?pid=" + pid, true);
				oXmlHttp.onreadystatechange=function()
				{
		  			if (oXmlHttp.readyState==4)
		  			{
		   				inbox.innerHTML = oXmlHttp.responseText;
		   			}
		  		};
		  		oXmlHttp.send(null);
	  		}
		}
		else
		{
			inbox.style.display='none';
		}

		if (inbox2) inbox2.style.display='none';

		fontdef = document.getElementById("fontdef-"+boxid);
		if (fontdef) {
			oldfont = fontdef.className;
			fontdef.className = 'detailsZoom';
		}
		namedef = document.getElementById("namedef-"+boxid);
		if (namedef) {
			oldname = namedef.className;
			namedef.className = 'nameZoom';
		}
		addnamedef = document.getElementById("addnamedef-"+boxid);
		if (addnamedef) {
			oldaddname = addnamedef.className;
			addnamedef.className = 'nameZoom';
		}
		if (thumb1) {
			oldthumbdisp = thumb1.style.display;
			thumb1.style.display='block';
			oldimgw = thumb1.offsetWidth;
			oldimgh = thumb1.offsetHeight;
			if (oldimgw) thumb1.style.width = (oldimgw*2)+"px";
			if (oldimgh) thumb1.style.height = (oldimgh*2)+"px";
		}
		if (gender) {
			oldimgw1 = gender.offsetWidth;
			oldimgh1 = gender.offsetHeight;
			if (oldimgw1) gender.style.width = "15px";
			if (oldimgh1) gender.style.height = "15px";
		}
	}
	return true;
}
function createXMLHttp()
{
	if (typeof XMLHttpRequest != "undefined")
	{
		return new XMLHttpRequest();
	}
	else if (window.ActiveXObject)
	{
		var ARR_XMLHTTP_VERS=["MSXML2.XmlHttp.5.0","MSXML2.XmlHttp.4.0",
			"MSXML2.XmlHttp.3.0","MSXML2.XmlHttp","Microsoft.XmlHttp"];

		for (var i = 0; i < ARR_XMLHTTP_VERS.length; i++)
		{
			try
			{
				var oXmlHttp = new ActiveXObject(ARR_XMLHTTP_VERS[i]);
				return oXmlHttp;
			}
			catch (oError) {;}
		}
	}
	throw new Error("XMLHttp object could not be created.");
};

/**
 * function to extract JS code from a text string.  Useful to call when loading
 * content dynamically through AJAX which contains a mix of HTML and JavaScript.
 * retrieves all of the JS code between <script></script> tags and adds it as a <script> node
 * @param string text   the text that contains a mix of html and inline javascript
 * @param DOMElement parentElement	the element that the text and JavaScript will added to
 */
function evalAjaxJavascript(text, parentElement) {
	parentElement.innerHTML = "";
	/* -- uncomment for debugging
	debugelement = document.createElement("pre");
	debugelement.appendChild(document.createTextNode(text));
	parentElement.appendChild(debugelement);
	*/
	pos2 = -1;
	//-- find the first occurrence of <script>
	pos1 = text.indexOf("<script", pos2+1);
	while(pos1>-1) {
		//-- append the text up to the <script tag to the content of the parent element
		parentElement.innerHTML += text.substring(0, pos1);

		//-- find the close of the <script> tag
		pos2 = text.indexOf(">",pos1+5);
		if (pos2==-1) {
			parentElement.innerHTML += "Error: incomplete text";
			return;
		}
		//-- create a new <script> element to add to the parentElement
		jselement = document.createElement("script");
		jselement.type = "text/javascript";
		//-- look for any src attributes
		scripttag = text.substring(pos1, pos2);
		regex = new RegExp("\\ssrc=\".*\"", "gi");
		results = scripttag.match(regex);
		if (results) {
			for(i=0; i<results.length; i++) {
				src = results[i].substring(results[i].indexOf("\"")+1, results[i].indexOf("\"", 6));
				src = src.replace(/&amp;/gi, "&");
				jselement.src = src;
			}
		}
		opos1 = pos1;
		pos1 = pos2;
		//-- find the closing </script> tag
		pos2 = text.indexOf("</script",pos1+1);
		if (pos2==-1) {
			parentElement.innerHTML += "Error: incomplete text";
			return;
		}
		//-- get the JS code between the <script></script> tags
		if (!results || results.length==0) {
			jscode = text.substring(pos1+1, pos2);
			if (jscode.length>0) {
				ttext = document.createTextNode(jscode);
				//-- add the JS code to the <script> element as a text node
				jscode=jscode.replace(/<!--/g, ''); // remove html comment [ 1737256 ]
				jscode=jscode.replace(/function ([^( ]*)/g,'window.$1 = function');
				eval(jscode);
			}
		}
		//-- add the javascript element to the parent element
		parentElement.appendChild(jselement);
		//-- shrink the text for the next iteration
		text = text.substring(pos2+9, text.length);
		//-- look for the next <script> tag
		pos1 = text.indexOf("<script");
	}
	//-- make sure any HTML/text after the last </script> gets added
	parentElement.innerHTML += text;
}

function restorebox(boxid, bstyle) {
	divbox = document.getElementById("out-"+boxid);
	inbox = document.getElementById("inout-"+boxid);
	inbox2 = document.getElementById("inout2-"+boxid);
	parentbox = document.getElementById("box"+boxid);
	if (!parentbox) {
		parentbox=divbox;
	}
	thumb1 = document.getElementById("box-"+boxid+"-thumb");
	icons = document.getElementById("icons-"+boxid);
	iconz = document.getElementById("iconz-"+boxid);	// This is the Zoom icon
	if (divbox) {
		if (icons) icons.style.display = oldiconsdislpay;
		if (iconz) {
			if (iconz.src==zoominout[0].src) iconz.src = zoominout[1].src;
			else iconz.src = zoominout[0].src;
		}
		big = 0;
		if (gender) {
			oldimgw1 = oldimgw1+"px";
			oldimgh1 = oldimgh1+"px";
			gender.style.width = oldimgw1;
			gender.style.height = oldimgh1;
		}
		if (thumb1) {
			oldimgw = oldimgw+"px";
			oldimgh = oldimgh+"px";
			thumb1.style.width = oldimgw;
			thumb1.style.height = oldimgh;
			thumb1.style.display=oldthumbdisp;
		}
		divbox.style.height=oldheight;
		divbox.style.width=oldwidth;
		if (parentbox) {
			//if (parentbox!=divbox) parentbox.style.width = parseInt(parentbox.style.width)-diff;
			//alert("here");
			parentbox.style.zIndex=oldz;
			if (url.indexOf("pedigree.php")!=-1) {
				if (textDirection=="ltr") parentbox.style.left=oldleft+"px";
				else parentbox.style.right=oldleft+"px";
			}
		}
		if (inbox) inbox.style.display='none';
		if (inbox2) inbox2.style.display='block';
		fontdef = document.getElementById("fontdef-"+boxid);
		if (fontdef) fontdef.className = oldfont;
		namedef = document.getElementById("namedef-"+boxid);
		if (namedef) namedef.className = oldname;
		addnamedef = document.getElementById("addnamedef-"+boxid);
		if (addnamedef) addnamedef.className = oldaddname;
	}
	return true;
}

/**
 * changes a CSS class for the given element
 *
 * @author John Finlay
 * @param string elementid the id for the dom element you want to give a new class
 * @param string newclass the name of the new class to apply to the element
 */
function change_class(elementid, newclass) {
	element = document.getElementById(elementid);
	if (element) {
		element.className = newclass;
	}
}

/**
 * changes the src of an image
 *
 * @author John Finlay
 * @param string elementid the id for the dom element you want to give a new icon
 * @param string newicon the src path of the new icon to apply to the element
 */
function change_icon(elementid, newicon) {
	element = document.getElementById(elementid);
	if (element) {
		element.src = newicon;
	}
}

var menutimeouts = new Array();
var currentmenu = null;
/**
 * Shows a submenu
 *
 * @author John Finlay
 * @param string elementid the id for the dom element you want to show
 */
function show_submenu(elementid, parentid, dir) {
	var pagewidth = document.body.scrollWidth+document.documentElement.scrollLeft;
	var element = document.getElementById(elementid);
	if (element && element.style) {
				if (document.all) {
					pagewidth = document.body.offsetWidth;
					//if (textDirection=="rtl") element.style.left = (element.offsetLeft-70)+'px';
				}
				else {
					pagewidth = document.body.scrollWidth+document.documentElement.scrollLeft-70;
					if (textDirection=="rtl") {
						boxright = element.offsetLeft+element.offsetWidth+10;
					}
				}

		//-- make sure the submenu is the size of the largest child
		var maxwidth = 0;
		var count = element.childNodes.length;
		for(var i=0; i<count; i++) {
			var child = element.childNodes[i];
			if (child.offsetWidth > maxwidth+5) maxwidth = child.offsetWidth;
		}
		if (element.offsetWidth <  maxwidth) {
			element.style.width = maxwidth+"px";
		}

		if (dir=="down") {
			var pelement = document.getElementById(parentid);
			if (pelement) {
				element.style.left=pelement.style.left;
				var boxright = element.offsetLeft+element.offsetWidth+10;
				if (boxright > pagewidth) {
					var menuleft = pagewidth-element.offsetWidth;
					element.style.left = menuleft + "px";
				}
			}
		}
		if (dir=="right") {
			var pelement = document.getElementById(parentid);
			if (pelement) {
				if (textDirection=="ltr") {
				var boxleft = pelement.offsetLeft+pelement.offsetWidth-40;
				var boxright = boxleft+element.offsetWidth+10;
				if (boxright > pagewidth) {
					element.style.right = pelement.offsetLeft + "px";
				}
				else {
					element.style.left=boxleft+"px";
				}
				}
				else {
//					element.style.right = pelement.offsetLeft+"px";
					element.style.left = (pelement.offsetLeft-element.offsetWidth)+"px";
//					alert(element.style.left);
				}
				element.style.top = pelement.offsetTop+"px";
			}
		}

		if (element.offsetLeft < 0) element.style.left = "0px";

		//-- put scrollbars on really long menus
		if (element.offsetHeight > 500) {
			element.style.height = '400px';
			element.style.overflow = 'auto';
		}

		currentmenu = elementid;
		element.style.visibility='visible';
	}
	clearTimeout(menutimeouts[elementid]);
	menutimeouts[elementid] = null;
}

/**
 * Hides a submenu
 *
 * @author John Finlay
 * @param string elementid the id for the dom element you want to hide
 */
function hide_submenu(elementid) {
if (menutimeouts[elementid] != null) {
	element = document.getElementById(elementid);
	if (element && element.style) {
		element.style.visibility='hidden';
	}
	clearTimeout(menutimeouts[elementid]);
	menutimeouts[elementid] = null;
}
}

/**
 * Sets a timeout to hide a submenu
 *
 * @author John Finlay
 * @param string elementid the id for the dom element you want to hide
 */
function timeout_submenu(elementid) {
	if (menutimeouts[elementid] == null) {
		tout = setTimeout("hide_submenu('"+elementid+"')", 100);
		menutimeouts[elementid] = tout;
	}
}
function checkKeyPressed(e) {
	if (IE) key = window.event.keyCode;
	else key = e.which;
	if (key==118) {
		if (pastefield) findSpecialChar(pastefield);
	}
	if (key==112) {
		helpPopup(whichhelp);
	}
	//else if (pastefield) pastefield.value=key;
}

function focusHandler(evt) {
	var e = evt ? evt : window.event;
	if (!e) return;
	if (e.target)
		pastefield = e.target;
	else if(e.srcElement) pastefield = e.srcElement;
}

function loadHandler() {
	var i, j;

	for (i = 0; i < document.forms.length; i++)
		for (j = 0; j < document.forms[i].elements.length; j++) {
			if (document.forms[i].elements[j].type=="text") {
				if (document.forms[i].elements[j].onfocus==null) document.forms[i].elements[j].onfocus = focusHandler;
			}
		}
}
var IE = document.all?true:false;
if (!IE) document.captureEvents(Event.MOUSEMOVE|Event.KEYDOWN|Event.KEYUP);
document.onmousemove = getMouseXY;
document.onkeyup = checkKeyPressed;

//Highlight image script - START
//Highlight image script- By Dynamic Drive
//For full source code and more DHTML scripts, visit http://www.dynamicdrive.com
//This credit MUST stay intact for use

function makevisible(cur,which){
strength=(which==0)? 1 : 0.2

if (cur.style.MozOpacity)
cur.style.MozOpacity=strength
else if (cur.filters)
cur.filters.alpha.opacity=strength*100
}
//Highlight image script - END

function toggleStatus(sel) {
	var cbox = document.getElementById(sel);
	cbox.disabled=!(cbox.disabled);
}


function statusDisable(sel) {
	var cbox = document.getElementById(sel);
	cbox.checked = false;
	cbox.disabled = true;
}

function statusEnable(sel) {
	var cbox = document.getElementById(sel);
	cbox.disabled = false;
}

function statusChecked(sel) {
	var cbox = document.getElementById(sel);
	cbox.checked = true;
}

var monthLabels = new Array();
  monthLabels[1] = "January";
  monthLabels[2] = "February";
  monthLabels[3] = "March";
  monthLabels[4] = "April";
  monthLabels[5] = "May";
  monthLabels[6] = "June";
  monthLabels[7] = "July";
  monthLabels[8] = "August";
  monthLabels[9] = "September";
  monthLabels[10] = "October";
  monthLabels[11] = "November";
  monthLabels[12] = "December";

  var monthShort = new Array();
  monthShort[1] = "JAN";
  monthShort[2] = "FEB";
  monthShort[3] = "MAR";
  monthShort[4] = "APR";
  monthShort[5] = "MAY";
  monthShort[6] = "JUN";
  monthShort[7] = "JUL";
  monthShort[8] = "AUG";
  monthShort[9] = "SEP";
  monthShort[10] = "OCT";
  monthShort[11] = "NOV";
  monthShort[12] = "DEC";

  var daysOfWeek = new Array();
  daysOfWeek[0] = "S";
  daysOfWeek[1] = "M";
  daysOfWeek[2] = "T";
  daysOfWeek[3] = "W";
  daysOfWeek[4] = "T";
  daysOfWeek[5] = "F";
  daysOfWeek[6] = "S";

  var weekStart = 0;

  function cal_setMonthNames(jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, dec) {
  	monthLabels[1] = jan;
  	monthLabels[2] = feb;
  	monthLabels[3] = mar;
  	monthLabels[4] = apr;
  	monthLabels[5] = may;
  	monthLabels[6] = jun;
  	monthLabels[7] = jul;
  	monthLabels[8] = aug;
  	monthLabels[9] = sep;
  	monthLabels[10] = oct;
  	monthLabels[11] = nov;
  	monthLabels[12] = dec;
  }

  function cal_setDayHeaders(sun, mon, tue, wed, thu, fri, sat) {
  	daysOfWeek[0] = sun;
  	daysOfWeek[1] = mon;
  	daysOfWeek[2] = tue;
  	daysOfWeek[3] = wed;
  	daysOfWeek[4] = thu;
  	daysOfWeek[5] = fri;
  	daysOfWeek[6] = sat;
  }

  function cal_setWeekStart(day) {
  	if (day >=0 && day < 7) weekStart = day;
  }

  function cal_toggleDate(dateDivId, dateFieldId) {
  	var dateDiv = document.getElementById(dateDivId);
  	if (!dateDiv) return false;

  	if (dateDiv.style.visibility=='visible') {
  		dateDiv.style.visibility = 'hidden';
  		return false;
  	}
  	if (dateDiv.style.visibility=='show') {
  		dateDiv.style.visibility = 'hide';
  		return false;
  	}

  	var dateField = document.getElementById(dateFieldId);
  	if (!dateField) return false;

  	var dateStr = dateField.value;
  	var date = new Date();
  	if (dateStr!="" && dateStr.indexOf("@")==-1) date = new Date(dateStr);
  	if (!date) return;

  	dateDiv.innerHTML = cal_generateSelectorContent(dateFieldId, dateDivId, date);
  	if (dateDiv.style.visibility=='hidden') {
  		dateDiv.style.visibility = 'visible';
  		return false;
  	}
  	if (dateDiv.style.visibility=='hide') {
  		dateDiv.style.visibility = 'show';
  		return false;
  	}
  	return false;
  }

  function cal_generateSelectorContent(dateFieldId, dateDivId, date) {
  	var content = '<table border="1"><tr>';
  	content += '<td><select name="'+dateFieldId+'_daySelect" id="'+dateFieldId+'_daySelect" onchange="return cal_updateCalendar(\''+dateFieldId+'\', \''+dateDivId+'\');">';
  	for(i=1; i<32; i++) {
  		content += '<option value="'+i+'"';
  		if (date.getDate()==i) content += ' selected="selected"';
  		content += '>'+i+'</option>';
  	}
  	content += '</select></td>';
  	content += '<td><select name="'+dateFieldId+'_monSelect" id="'+dateFieldId+'_monSelect" onchange="return cal_updateCalendar(\''+dateFieldId+'\', \''+dateDivId+'\');">';
  	for(i=1; i<13; i++) {
  		content += '<option value="'+i+'"';
  		if (date.getMonth()+1==i) content += ' selected="selected"';
  		content += '>'+monthLabels[i]+'</option>';
  	}
  	content += '</select></td>';
  	content += '<td><input type="text" name="'+dateFieldId+'_yearInput" id="'+dateFieldId+'_yearInput" size="5" value="'+date.getFullYear()+'" onchange="return cal_updateCalendar(\''+dateFieldId+'\', \''+dateDivId+'\');" /></td></tr>';
  	content += '<tr><td colspan="3">';
  	content += '<table width="100%">';
  	content += '<tr>';
  	j = weekStart;
	for(i=0; i<7; i++) {
		content += '<td ';
		content += 'class="descriptionbox"';
		content += '>';
		content += daysOfWeek[j];
		content += '</td>';
		j++;
		if (j>6) j=0;
	}
	content += '</tr>';

  	var tdate = new Date(date.getFullYear(), date.getMonth(), 1);
  	var day = tdate.getDay();
  	day = day - weekStart;
  	var daymilli = (1000*60*60*24);
  	tdate = tdate.getTime() - (day*daymilli) + (daymilli/2);
  	tdate = new Date(tdate);

  	for(j=0; j<6; j++) {
  		content += '<tr>';
  		for(i=0; i<7; i++) {
  			content += '<td ';
  			if (tdate.getMonth()==date.getMonth()) {
  				if (tdate.getDate()==date.getDate()) content += 'class="descriptionbox"';
  				else content += 'class="optionbox"';
  			}
  			else content += 'style="background-color:#EAEAEA; border: solid #AAAAAA 1px;"';
  			content += '><a href="#" onclick="return cal_dateClicked(\''+dateFieldId+'\', \''+dateDivId+'\', '+tdate.getFullYear()+', '+tdate.getMonth()+', '+tdate.getDate()+');">';
  			content += tdate.getDate();
  			content += '</a></td>';
  			datemilli = tdate.getTime() + daymilli;
  			tdate = new Date(datemilli);
  		}
  		content += '</tr>';
  	}
  	content += '</table>';
  	content += '</td></tr>';
  	content += '</table>';

  	return content;
  }

  function cal_setDateField(dateFieldId, year, month, day) {
  	var dateField = document.getElementById(dateFieldId);
  	if (!dateField) return false;
  	if (day<10) day = "0"+day;
  	dateField.value = day+' '+monthShort[month+1]+' '+year;
  	return false;
  }

  function cal_updateCalendar(dateFieldId, dateDivId) {
  	var dateSel = document.getElementById(dateFieldId+'_daySelect');
  	if (!dateSel) return false;
  	var monthSel = document.getElementById(dateFieldId+'_monSelect');
  	if (!monthSel) return false;
  	var yearInput = document.getElementById(dateFieldId+'_yearInput');
  	if (!yearInput) return false;

  	var month = parseInt(monthSel.options[monthSel.selectedIndex].value);
  	month = month-1;

  	var date = new Date(yearInput.value, month, dateSel.options[dateSel.selectedIndex].value);
  	if (!date) alert('Date error '+date);
  	cal_setDateField(dateFieldId, date.getFullYear(), date.getMonth(), date.getDate());

  	var dateDiv = document.getElementById(dateDivId);
  	if (!dateDiv) {
  		alert('no dateDiv '+dateDivId);
  		return false;
  	}
  	dateDiv.innerHTML = cal_generateSelectorContent(dateFieldId, dateDivId, date);

  	return false;
  }

  function cal_dateClicked(dateFieldId, dateDivId, year, month, day) {
  	cal_setDateField(dateFieldId, year, month, day);
  	cal_toggleDate(dateDivId, dateFieldId);
  	return false;
  }
function findIndi(field, indiname, multiple, ged,filter) {
        pastefield = field;
        nameElement = indiname;
        if(filter)
        {
        window.open('find.php?type=indi&multiple='+multiple+'&ged='+ged+'&filter='+filter, '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
        }
        else
        {
        window.open('find.php?type=indi&multiple='+multiple+'&ged='+ged, '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
        }
        return false;
}

function findPlace(field, ged) {
	pastefield = field;
	window.open('find.php?type=place&ged='+ged, '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}

function findFamily(field, ged) {
	pastefield = field;
	window.open('find.php?type=fam&ged='+ged, '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}
function findMedia(field, choose, ged) {
	pastefield = field;
	if (!choose) choose="0all";
	window.open('find.php?type=media&choose='+choose+'&ged='+ged, '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}
function findSource(field, sourcename, ged) {
	pastefield = field;
	nameElement = sourcename;
	window.open('find.php?type=source&ged='+ged, '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}
function findnote(field, notename, ged) {
	pastefield = field;
	nameElement = notename;
	window.open('find.php?type=note&ged='+ged, '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}
function findRepository(field, ged) {
	pastefield = field;
	window.open('find.php?type=repo&ged='+ged, '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
	return false;
}
function findSpecialChar(field) {
	pastefield = field;
	window.open('find.php?type=specialchar', '_blank', 'top=55,left=55,width=500,height=500,scrollbars=1,resizeable=1');
	return false;
}
function findFact(field, ged) {
	pastefield = field;
	tags = field.value;
	left = screen.width-555;
	window.open('find.php?type=facts&tags='+tags+'&ged='+ged, '_blank', 'top=55,left='+left+',width=500,height=500,scrollbars=1,resizeable=1');
	return false;
}

function toggleByClassName(tagName, className) {
	var disp = "";
	var elements = document.getElementsByTagName(tagName.toUpperCase());
	for (var i = 0; i < elements.length; i++) {
		var ecn = elements[i].className;
		if (ecn && ecn.match(new RegExp("(^|\\s)" + className + "(\\s|$)"))) {
			disp = elements[i].style.display;
			if (disp == "none") {
				if (tagName == "TR") {
					disp = "table-row";
					if (document.all && !window.opera) disp = "inline"; // IE
				}
				else disp = "block";
				if (tagName == "SPAN") disp = "inline";
			}
			else disp = "none";
			elements[i].style.display = disp;
		}
	}
	// save status in a cookie
	/*if (!navigator.cookieEnabled) return;
	var cookieName = className;
	var cookieValue = (disp=="none")? 0 : 1;
	var cookieDate = new Date(2020,0,1);
	document.cookie = cookieName+"="+cookieValue+"; expires="+cookieDate.toGMTString();*/
}

/**
 * Load a CSS file from the body of a document
 *
 * CSS files are normally loaded through a <link rel="stylesheet" type="text/css" href="something" />
 * statement.  This statement is only allowed in the <head> section of the document.
 *
 * See : http://www.phpied.com/javascript-include-ready-onload/
 *
 */
function include_css(css_file) {
    var html_doc = document.getElementsByTagName('head')[0];
    var css = document.createElement('link');
    css.setAttribute('rel', 'stylesheet');
    css.setAttribute('type', 'text/css');
    css.setAttribute('href', css_file);
    html_doc.appendChild(css);
}

function include_js(file) {
    var html_doc = document.getElementsByTagName('head')[0];
    var js = document.createElement('script');
    js.setAttribute('type', 'text/javascript');
    js.setAttribute('src', file);
    html_doc.appendChild(js);
}

  function findPosX(obj)
  {
    var curleft = 0;
    if(obj.offsetParent)
        while(1)
        {
          curleft += obj.offsetLeft;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.x)
        curleft += obj.x;
    return curleft;
  }

  function findPosY(obj)
  {
    var curtop = 0;
    if(obj.offsetParent)
        while(1)
        {
        	if (obj.style.position=="relative") break;
          curtop += obj.offsetTop;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.y)
        curtop += obj.y;
    return curtop;
  }

	function hidePrint() {
		var printlink = document.getElementById("printlink");
		var printlinktwo = document.getElementById("printlinktwo");
		if (printlink) {
			printlink.style.display="none";
			printlinktwo.style.display="none";
		}
	}
	function showBack() {
		var printlink = document.getElementById("printlink");
		var printlinktwo = document.getElementById("printlinktwo");
		if (printlink) {
			printlink.style.display="inline";
			printlinktwo.style.display="inline";
		}
	}

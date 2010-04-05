/**
 *
 * Copyright (c) 1997-2006 Stuart Langridge (www.kryogenix.org)
 *
 * @licence MIT-licence http://www.kryogenix.org/code/browser/licence.html
 * @author Stuart Langridge
 * @see http://www.kryogenix.org/code/browser/sorttable/
 *
 * @package webtrees
 * @subpackage Display
 * @version $Id$
 */

addEvent(window, "load", sortables_init);

var SORT_COLUMN_INDEX;

function sortables_init() {
	// Find all tables with class sortable and make them sortable
	if (!document.getElementsByTagName) return;
	tbls = document.getElementsByTagName("table");
	for (ti=0;ti<tbls.length;ti++) {
		thisTbl = tbls[ti];
		if (((' '+thisTbl.className+' ').indexOf("sortable") != -1) && (thisTbl.id)) {
			//initTable(thisTbl.id);
			ts_makeSortable(thisTbl);
		}
	}
}

function ts_makeSortable(table) {
	if (table.rows && table.rows.length > 0) {
		var firstRow = table.rows[0];
	}
	if (!firstRow) return;

	// We have a first row: assume it's the header, and make its contents clickable links
	for (var i=0;i<firstRow.cells.length;i++) {
		var cell = firstRow.cells[i];
		var txt = ts_getInnerText(cell);
		if (cell.className.match(/\bsorttable_nosort\b/)) continue // PGV: skip this col
		if (cell.getElementsByTagName("img") && cell.nodeName.toLowerCase()=="th") txt = cell.innerHTML; // PGV: allow icon as text
		if (txt=="" || txt.match(/javascript/)) continue; // PGV: do not process empty cols or custom sorting
		cell.innerHTML = '<a href="javascript:;" class="sortheader" '+
		'onmousedown="this.style.cursor=\'wait\';" ' + // PGV: set cursor
		'onclick="ts_resortTable(this, '+i+');return false;">' +
		txt+'<span class="sortarrow">&nbsp;&nbsp;</span></a>';
	}
}

function ts_getInnerText(el) {
	if (typeof el == "string") return el;
	if (typeof el == "undefined") { return el };
	if (el.innerText) return el.innerText;	//Not needed but it is faster
	var str = "";

	var cs = el.childNodes;
	var l = cs.length;
	for (var i = 0; i < l; i++) {
		switch (cs[i].nodeType) {
			case 1: //ELEMENT_NODE
				str += ts_getInnerText(cs[i]);
				break;
			case 3:	//TEXT_NODE
				str += cs[i].nodeValue;
				break;
		}
	}
	return str;
}

function ts_resortTable(lnk,clid) {
	// get the span
	var span;
	for (var ci=0;ci<lnk.childNodes.length;ci++) {
		if (lnk.childNodes[ci].tagName && lnk.childNodes[ci].tagName.toLowerCase() == 'span') span = lnk.childNodes[ci];
	}
	var spantext = ts_getInnerText(span);
	var td = lnk.parentNode;
	var column = clid || td.cellIndex;
	var table = getParent(td,'TABLE');

	// PGV : confirm action for big table
	if (table.rows.length > 500
	&& !confirm("Sorting this big table may take a long time\r\nContinue ?")) {
		lnk.style.cursor='pointer';
		return;
	}
	lnk.style.cursor='wait';
	if (table.rows.length <= 1) return;
	SORT_COLUMN_INDEX = column;
	var firstRow = new Array();
	var newRows = new Array();
	for (i=0;i<table.rows[0].length;i++) { firstRow[i] = table.rows[0][i]; }
	for (j=1;j<table.rows.length;j++) { newRows[j-1] = table.rows[j]; }

	newRows.sort(ts_pgv_sort);

	if (span.getAttribute("sortdir") == 'down') {
		ARROW = '&nbsp;&uarr;';
		newRows.reverse();
		span.setAttribute('sortdir','up');
	} else {
		ARROW = '&nbsp;&darr;';
		span.setAttribute('sortdir','down');
	}

	// We appendChild rows that already exist to the tbody, so it moves them rather than creating new ones
	// don't do sortbottom rows
	for (i=0;i<newRows.length;i++) { if (!newRows[i].className || (newRows[i].className && (newRows[i].className.indexOf('sortbottom') == -1))) table.tBodies[0].appendChild(newRows[i]);}
	// do sortbottom rows only
	for (i=0;i<newRows.length;i++) { if (newRows[i].className && (newRows[i].className.indexOf('sortbottom') != -1)) table.tBodies[0].appendChild(newRows[i]);}

	// Delete any other arrows there may be showing
	var allspans = document.getElementsByTagName("span");
	for (var ci=0;ci<allspans.length;ci++) {
		if (allspans[ci].className == 'sortarrow') {
			if (getParent(allspans[ci],"table") == getParent(lnk,"table")) { // in the same table as us?
				allspans[ci].innerHTML = '&nbsp;&nbsp;';
				if (allspans[ci]!=span) allspans[ci].setAttribute('sortdir','up'); // PGV: reset sortdir
			}
		}
	}

	span.innerHTML = ARROW;
	table_renum(table.id); // PGV: update line counter
	lnk.style.cursor='pointer'; // PGV: reset cursor
}

function getParent(el, pTagName) {
	if (el == null) return null;
	else if (el.nodeType == 1 && el.tagName.toLowerCase() == pTagName.toLowerCase())	// Gecko bug, supposed to be uppercase
		return el;
	else
		return getParent(el.parentNode, pTagName);
}

function ts_pgv_sort(a,b) {
	akey = a.cells[SORT_COLUMN_INDEX].getElementsByTagName('a');
	bkey = b.cells[SORT_COLUMN_INDEX].getElementsByTagName('a');
	if (akey.length && akey[0].name && bkey.length && bkey[0].name) {
		// use "name" value as numeric sortkey, if exists
		aa = parseInt(akey[0].name);
		bb = parseInt(bkey[0].name);
		if (aa==bb) return a.rowIndex-b.rowIndex; // equal values sort by their original sequence
		if (aa<bb) return -1;
		if (aa>bb) return 1;
	}
	aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
	bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
	if (aa==bb) return a.rowIndex-b.rowIndex; // equal values sort by their original sequence
	return _lc_sort(aa,bb); // locale.js
}

function addEvent(elm, evType, fn, useCapture)
// addEvent and removeEvent
// cross-browser event handling for IE5+,  NS6 and Mozilla
// By Scott Andrew
{
	if (elm.addEventListener){
		elm.addEventListener(evType, fn, useCapture);
		return true;
	} else if (elm.attachEvent){
		var r = elm.attachEvent("on"+evType, fn);
		return r;
	} else {
		alert("Handler could not be removed");
	}
}

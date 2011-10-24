/**
 *
 * Additional filtering functions for sorttable.js
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2007  John Finlay and Others
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
 * $Id$
 */

function table_filter(id, keyword, filter) {
	var table = document.getElementById(id);
	// get column number
	var firstRow = table.rows[0];
	for (var c=0;c<firstRow.cells.length;c++) {
		if (ts_getInnerText(firstRow.cells[c]).indexOf(keyword)!=-1) {
			COLUMN=c;
			break;
		}
	}
	// apply filter
	for (var r=1;r<table.rows.length;r++) {
		var row = table.rows[r];
		// don't do sortbottom last rows
		if (row.className && (row.className.indexOf('sortbottom') != -1)) break;
		// display row when matching filter
		var disp = "none";
		if (row.cells[COLUMN] && ts_getInnerText(row.cells[COLUMN]).indexOf(filter)!=-1) {
			disp="table-row";
			if (document.all && !window.opera) disp = "inline"; // IE
		}
		row.style.display=disp;
	}
	table_renum(id);
	return false;
}

function table_renum(id) {
	var table = document.getElementById(id);
	// is first column counter ?
	var firstRow = table.rows[0];
	if (ts_getInnerText(firstRow.cells[0])!='') return false;
	// renumbering
	var count=1;
	for (var r=1;r<table.rows.length;r++) {
		row = table.rows[r];
		// don't do sortbottom last rows
		if (row.className && (row.className.indexOf('sortbottom') != -1)) break;
		// count only visible rows
		if (row.style.display!='none') row.cells[0].innerHTML = count++;
	}
}

function sortByOtherCol(node, offset) {
	var td = node.parentNode;
	var tr = td.parentNode;
	var tbody = tr.parentNode;
	var table = tbody.parentNode;
	if (table.getElementsByTagName('tbody').length == 0) table = tbody;
	var thead = table.firstChild;
	if (table.getElementsByTagName('thead').length == 0) thead = table;
	for (var c = 0; c < tr.childNodes.length; c++) if (tr.childNodes[c] == td) break;
	c+=offset; // c is current col => c+1 is hidden column to right, c-1 is hidden column to left, etc.
	var a = thead.rows[0].cells[c].getElementsByTagName("a"); // get hidden col header links
	if (a.length) ts_resortTable(a[0], c);
	return false;
}

<?php
/**
 * Media Link Assistant Control module for phpGedView
 *
 * Media Link information about an individual
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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
 * @subpackage Census Assistant
 * @version $Id$
 */
?>

<script>
function parseAddLinks() {
	str = "";
	var tbl = document.getElementById('addlinkQueue');
	for(var i=1; i<tbl.rows.length; i++){ // start at i=1 because we need to avoid header
		var tr = tbl.rows[i];
		var strRow = ''; 
		for(var j=1; j<tr.cells.length; j++){ // Start at col 1 (j=1)
			if (j>=2) {
				//	dont show 	 col 0	 index
				//  SHOW		 col 1	 id
				//	miss out	 col 2	 name
				//	miss out	 col 3	 relationship
				//	miss out	 col 4	 delete button
				continue;
			}else{
				if (IE) {
					strRow += (strRow==''?'':'') + tr.cells[j].childNodes[0].innerHTML;
				}else{
					strRow += (strRow==''?'':'') + tr.cells[j].childNodes[0].textContent;
				}
			}
		}
		str += (str==''?'':', ') + strRow;
	}
	// str += (str==''?'':'' '); // Adds just final single quote at end of string (\')
}

function parseRemLinks() {
	remstr = "";
	var tbl = document.getElementById('existLinkTbl');
	for(var i=1; i<tbl.rows.length; i++){ // start at i=1 because we need to avoid header
		var remtr = tbl.rows[i];
		var remstrRow = ''; 
		for(var j=1; j<remtr.cells.length; j++){ // Start at col 1 (j=1)
			if (j!=4 ) {
				//	dont show col	0	index
				//	miss out  col	2	name
				//	miss out  col	3	keep radio button
				//	choose    col	4	remove radio button
				continue;
			}else{
				 if (remtr.cells[j].childNodes[0].checked)  {
					remstrRow += (remstrRow==''?'':'') + remtr.cells[j].childNodes[0].name + ', ';
				 }
			}
		}
		remstr += (remstr==''?'':'') + remstrRow;
	}
	// remstr += (remstr==''?'':','); // Adds just final comma at end of string (\')
}

function preview() {
	parseAddLinks();
	alert (str);
}

function shiftlinks() {

	parseRemLinks();
	//	alert('remstring = '+ remstr);
	if (remstr) {
		document.link.exist_links.value = remstr;
	}
	
	parseAddLinks();
	//	alert('string = '+ str);
	if (str) {
		document.link.more_links.value = str;
	}else{
		// leave hidden input morelinks as "No Values"
		var inputField = document.getElementById('gid');
	//	alert(inputField.value)
		if (inputField) {
			document.link.more_links.value = inputField.value+',';
		}
	}
	if (winNav) {
		winNav.close();
	}
}


</script>
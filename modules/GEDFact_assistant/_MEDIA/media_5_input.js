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

var INPUT_NAME_PREFIX = 'InputCell_'; // this is being set via script
var RADIO_NAME = "totallyrad"; // this is being set via script
var TABLE_NAME = 'addlinkQueue'; // this should be named in the HTML
var ROW_BASE = 1; // first number (for display)
var hasLoaded = false;

window.onload=fillInRows;

function fillInRows()
{
	hasLoaded = true;
	//insertRowToTable();
	//addRowToTable();
}

// CONFIG:
// myRowObject is an object for storing information about the table rows
//function myRowObject(zero, one, two, three, four, five, six, seven, eight, nine, ten, cb, ra)
function myRowObject(zero, one, two, cb, ra)
{
	this.zero	 = zero;	 // text object
	this.one	 = one;		 // input text object
	this.two	 = two;		 // input text object

	this.cb		 = cb;		 // input checkbox object
	this.ra		 = ra;		 // input radio object
}

/*
 * insertRowToTable
 * Insert and reorder
 */
//function insertRowToTable(pid, nam, label, gend, cond, yob, age, YMD, occu, birthpl)
function insertRowToTable(pid, nam, head)
{
	if (hasLoaded) {
	
		var tbl = document.getElementById(TABLE_NAME);
		var rowToInsertAt = "";
		
		// Get links list ====================================
		var links 	= document.getElementById('existLinkTbl');
		var numrows = links.rows.length;
		var strRow = '';
		for (var i=1; i<numrows; i++) {
			if (IE) {
				strRow += (strRow==''?'':', ') + links.rows[i].cells[1].innerText;
			}else{
				strRow += (strRow==''?'':', ') + links.rows[i].cells[1].textContent;
			}
		}
		strRow += (strRow==''?'':', ');
		
		//Check if id exists in Links list =================================
		if (strRow.match(pid+',')!= pid+',') {
			// alert('NO MATCH');
		}else{
			rowToInsertAt = 'EXIST' ;
		}

		// Check if id exists in "Add links" list ==========================
		for (var i=0; i<tbl.tBodies[0].rows.length; i++) {
			if (tbl.tBodies[0].rows[i].myRow.one.textContent==pid) {
				rowToInsertAt = 'EXIST' ;
			}else
			if (tbl.tBodies[0].rows[i].myRow && tbl.tBodies[0].rows[i].myRow.ra.getAttribute('type') == 'radio' && tbl.tBodies[0].rows[i].myRow.ra.checked) {
				rowToInsertAt = i;
				break;
			}
		}
		
		// If Link does not exist then add it, or show alert ===============
		if (rowToInsertAt!='EXIST') {
			rowToInsertAt = i;
			//addRowToTable(rowToInsertAt, pid, nam, label, gend, cond, yob, age, YMD, occu, birthpl);
			addRowToTable(rowToInsertAt, pid, nam, head);
			reorderRows(tbl, rowToInsertAt);
		}else{
			alert(nam+' ('+pid+') - '+linkExists);
		}
		
	}
}

function removeHTMLTags(htmlString)
{
	if(htmlString) {
		var mydiv = document.createElement("div");
			mydiv.innerHTML = htmlString;
		if (document.all) // IE Stuff
		{
			return mydiv.innerText;
		}    
		else // Mozilla does not work with innerText
		{
			return mydiv.textContent;
		}                            
	}
} 

/*
 * addRowToTable
 * Inserts at row 'num', or appends to the end if no arguments are passed in. Don't pass in empty strings.
 */
// function addRowToTable(num, pid, nam, label, gend, cond, yob, age, YMD, occu, birthpl)
function addRowToTable(num, pid, nam, head)
{
		if (hasLoaded) {
			var tbl = document.getElementById(TABLE_NAME);
			var nextRow = tbl.tBodies[0].rows.length;
			var iteration = nextRow + ROW_BASE;
			// var txtcolor = "#888888";
			
			if (num == null) { 
				num = nextRow;
			} else {
				iteration = num + ROW_BASE;
			}
			
			// add the row
			var row = tbl.tBodies[0].insertRow(num);
			
			// CONFIG: requires classe
			row.className = 'descriptionbox';
			
			// CONFIG: This whole section can be configured
			
			// cell 0 - Count
			var cell0 = row.insertCell(0);
			cell0.style.fontSize="11px";
			var textNode = document.createTextNode(iteration);
			cell0.appendChild(textNode);
			
			// cell 1 - ID:
			var cell1 = row.insertCell(1);
			//	cell1.setAttribute('align', 'left');
			if ( pid == ''){
				var txtInp1 = document.createElement('div');
				txtInp1.setAttribute('type', 'checkbox');
				if (txtInp1.checked!=''){
					txtInp1.setAttribute('value', 'no');
				}else{
					txtInp1.setAttribute('value', 'add');
				}
			}else{
				var txtInp1 = document.createElement('div');
				txtInp1.setAttribute('type', 'text');
				txtInp1.innerHTML = pid; // Required for IE
				txtInp1.textContent = pid;
			}
				txtInp1.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_1');
				txtInp1.style.color=txtcolor;
				txtInp1.style.background='transparent';
				txtInp1.style.border='0px';
				txtInp1.style.fontSize="11px";
			cell1.appendChild(txtInp1);
			
			// cell 2 - Name
			var cell2 = row.insertCell(2);
			//	cell2.setAttribute('align', 'left');
			var txtInp2 = document.createElement('div');
				txtInp2.setAttribute('type', 'text');
				txtInp2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_2');
				txtInp2.style.color=txtcolor;
				txtInp2.style.background='transparent';
				txtInp2.style.border='0px';
				txtInp2.style.fontSize="11px";
				txtInp2.innerHTML = unescape(removeHTMLTags(nam)); //Required for IE
				txtInp2.textContent = unescape(removeHTMLTags(nam));
			cell2.appendChild(txtInp2);

			// cell btn - remove img button
			var cellbtn = row.insertCell(3);
				cellbtn.setAttribute('align', 'center');
			var btnEl = document.createElement('img');
				btnEl.setAttribute('type', 'img');
				btnEl.setAttribute('src', imageDir+'/remove.gif');
				btnEl.setAttribute('alt', remove);
				btnEl.setAttribute('title', remove);
				btnEl.setAttribute('height', '13px');
				btnEl.onclick = function () {deleteCurrentRow(this)};
			cellbtn.appendChild(btnEl);
			
			// cell btn - family img button
			var cellbtn2 = row.insertCell(4);
				cellbtn2.setAttribute('align', 'center');
			if (pid.match("I")=="I" || pid.match("i")=="i") {
				var btn2El = document.createElement('img');
					btn2El.setAttribute('type', 'img');
					btn2El.setAttribute('src', imageDir+'/buttons/family.gif');
					btn2El.setAttribute('alt', ifamily);
					btn2El.setAttribute('title', ifamily);
					btn2El.onclick = function () {openFamNav(pid)};
				cellbtn2.appendChild(btn2El);
			}else if (pid.match("F")=="F" || pid.match("f")=="f") {
				var btn2El = document.createElement('img');
					btn2El.setAttribute('type', 'img');
					btn2El.setAttribute('src', imageDir+'/buttons/family.gif');
					btn2El.setAttribute('alt', ifamily);
					btn2El.setAttribute('title', ifamily);
					btn2El.onclick = function () {openFamNav(head)};
				cellbtn2.appendChild(btn2El);
			}else{
				// Show No Icon
			}
			
			// cell cb - input checkbox
			var cbEl = document.createElement('input');
			cbEl.type = "hidden";
			
			// cell ra - input radio
			//var cellra = row.insertCell(5);
			var cellra = document.createElement('input');
			cellra.type = "hidden";
			
			// Pass in the elements you want to reference later
			// Store the myRow object in each row
			row.myRow = new myRowObject(textNode, txtInp1, txtInp2, cbEl, cellra);
		}
}

// CONFIG: this entire function is affected by myRowObject settings
// If there isn't a checkbox in your row, then this function can't be used.
function deleteChecked()
{
	if (hasLoaded) {
		var checkedObjArray = new Array();
		var cCount = 0;
	
		var tbl = document.getElementById(TABLE_NAME);
		for (var i=0; i<tbl.tBodies[0].rows.length; i++) {
			if (tbl.tBodies[0].rows[i].myRow && tbl.tBodies[0].rows[i].myRow.cb.getAttribute('type') == 'checkbox' && tbl.tBodies[0].rows[i].myRow.cb.checked) {
				checkedObjArray[cCount] = tbl.tBodies[0].rows[i];
				cCount++;
			}
		}
		if (checkedObjArray.length > 0) {
			var rIndex = checkedObjArray[0].sectionRowIndex;
			deleteRows(checkedObjArray);
			reorderRows(tbl, rIndex);
		}
	}
}

// If there isn't an element with an onclick event in your row, then this function can't be used.
function deleteCurrentRow(obj)
{
	if (hasLoaded) {
		var delRow = obj.parentNode.parentNode;
		var tbl = delRow.parentNode.parentNode;
		var rIndex = delRow.sectionRowIndex;
		var rowArray = new Array(delRow);
		deleteRows(rowArray);
		reorderRows(tbl, rIndex);
	}
}

function reorderRows(tbl, startingIndex)
{
	if (hasLoaded) {
		if (tbl.tBodies[0].rows[startingIndex]) {
			var count = startingIndex + ROW_BASE;
			for (var i=startingIndex; i<tbl.tBodies[0].rows.length; i++) {
			
				// CONFIG: next line is affected by myRowObject settings
				tbl.tBodies[0].rows[i].myRow.zero.data	 = count; // text
				
				tbl.tBodies[0].rows[i].myRow.one.id		 = INPUT_NAME_PREFIX + count + '_1'; // input text
				tbl.tBodies[0].rows[i].myRow.two.id 	 = INPUT_NAME_PREFIX + count + '_2'; // input text
				
				tbl.tBodies[0].rows[i].myRow.one.name	 = INPUT_NAME_PREFIX + count + '_1'; // input text
				tbl.tBodies[0].rows[i].myRow.two.name 	 = INPUT_NAME_PREFIX + count + '_2'; // input text
				
				// tbl.tBodies[0].rows[i].myRow.cb.value = count; // input checkbox
				tbl.tBodies[0].rows[i].myRow.ra.value = count; // input radio
				
				// CONFIG: requires class named classy0 and classy1
				tbl.tBodies[0].rows[i].className = 'classy' + (count % 2);
				
				count++;
			}
		}
	}
}

function deleteRows(rowObjArray)
{
	if (hasLoaded) {
		for (var i=0; i<rowObjArray.length; i++) {
			var rIndex = rowObjArray[i].sectionRowIndex;
			rowObjArray[i].parentNode.deleteRow(rIndex);
		}
	}
}

function openInNewWindow(frm)
{
	// open a blank window
	var aWindow = window.open('', 'TableAddRow2NewWindow',
	'scrollbars=yes,menubar=yes,resizable=yes,location=no,toolbar=no,width=550,height=700');
	aWindow.focus();
	
	// set the target to the blank window
	frm.target = 'TableAddRow2NewWindow';
	
	// submit
	frm.submit();
}



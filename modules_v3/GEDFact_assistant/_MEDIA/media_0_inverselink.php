<?php
// Media Link Assistant Control module for webtrees
//
// Media Link information about an individual
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

// GEDFact Media assistant replacement code for inverselink.php: ===========================

//-- extra page parameters and checking
$more_links = safe_REQUEST($_REQUEST, 'more_links', WT_REGEX_UNSAFE);
$exist_links = safe_REQUEST($_REQUEST, 'exist_links', WT_REGEX_UNSAFE);
$gid = safe_GET_xref('gid');
$update_CHAN = safe_REQUEST($_REQUEST, 'preserve_last_changed', WT_REGEX_UNSAFE);

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';

$paramok =  true;
if (!empty($linktoid)) $paramok = WT_GedcomRecord::getInstance($linktoid)->canDisplayDetails();

if ($action == "choose" && $paramok) {

	?>
	<script type="text/javascript">
	<!--
	// Javascript variables
	var id_empty = "<?php echo WT_I18N::translate('When adding a Link, the ID field cannot be empty.'); ?>";

	var pastefield;

	function openerpasteid(id) {
		window.opener.paste_id(id);
		window.close();
	}

	function paste_id(value) {
		pastefield.value = value;
	}

	function paste_char(value) {
		pastefield.value += value;
	}

	function blankwin() {
		if (document.getElementById('gid').value == "" || document.getElementById('gid').value.length<=1) {
			alert(id_empty);
		} else {
			var iid = document.getElementById('gid').value;
			var winblank = window.open('module.php?mod=GEDFact_assistant&mod_action=media_query_3a&iid='+iid, 'winblank', 'top=100, left=200, width=400, height=20, toolbar=0, directories=0, location=0, status=0, menubar=0, resizable=1, scrollbars=1');
		}
	}

	var GEDFact_assist = "installed";
//-->
	</script>
	<script src="<?php echo WT_STATIC_URL; ?>webtrees.js" type="text/javascript"></script>
	<link href ="<?php echo WT_STATIC_URL, WT_MODULES_DIR; ?>GEDFact_assistant/css/media_0_inverselink.css" rel="stylesheet" type="text/css" media="screen">

	<?php
	echo '<form name="link" method="get" action="inverselink.php">';
	// echo '<input type="hidden" name="action" value="choose">';
	echo '<input type="hidden" name="action" value="update">';
	if (!empty($mediaid)) {
		echo '<input type="hidden" name="mediaid" value="', $mediaid, '">';
	}
	if (!empty($linktoid)) {
		echo '<input type="hidden" name="linktoid" value="', $linktoid, '">';
	}
	echo '<input type="hidden" name="linkto" value="', $linkto, '">';
	echo '<input type="hidden" name="ged" value="', $GEDCOM, '">';
	echo '<table class="facts_table center">';
	echo '<tr><td class="topbottombar" colspan="2">';
	echo WT_I18N::translate('Link media'), help_link('add_media_linkid');
	echo '</td></tr><tr><td class="descriptionbox width20 wrap">', WT_I18N::translate('Media'), '</td>';
	echo '<td class="optionbox wrap">';
	if (!empty($mediaid)) {
		//-- Get the title of this existing Media item
		$title=
			WT_DB::prepare("SELECT m_titl FROM `##media` where m_media=? AND m_gedfile=?")
			->execute(array($mediaid, WT_GED_ID))
			->fetchOne();
		if ($title) {
			echo '<b>', $title, '</b>';
		} else {
			echo '<b>', $mediaid, '</b>';
		}
		echo '<table><tr><td>';
		//-- Get the filename of this existing Media item
		$filename=
			WT_DB::prepare("SELECT m_file FROM `##media` where m_media=? AND m_gedfile=?")
			->execute(array($mediaid, WT_GED_ID))
			->fetchOne();
		$filename = str_replace(" ", "%20", $filename);
		$thumbnail = thumbnail_file($filename, false);
		echo '<img src = ', $thumbnail, ' class="thumbheight">';
		echo '</td></tr></table>';
		echo '</td></tr>';
		echo '<tr><td class="descriptionbox width20 wrap">', WT_I18N::translate('Links'), '</td>';
		echo '<td class="optionbox wrap">';
		$links = get_media_relations($mediaid);
		echo "<table><tr><td>";
		echo "<table id=\"existLinkTbl\" width=\"430\" cellspacing=\"1\" >";
		echo "<tr>";
		echo '<td class="topbottombar" width="15"  style="font-weight:100;" >#</td>';
		echo '<td class="topbottombar" width="50"  style="font-weight:100;" >ID:</td>';
		echo '<td class="topbottombar" width="340" style="font-weight:100;" >', WT_I18N::translate('Name'), '</td>';
		echo '<td class="topbottombar" width="20"  style="font-weight:100;" >', WT_I18N::translate('Keep'), '</td>';
		echo '<td class="topbottombar" width="20"  style="font-weight:100;" >', WT_I18N::translate('Remove'), '</td>';
		echo '<td class="topbottombar" width="20"  style="font-weight:100;" >', WT_I18N::translate('Navigator'), '</td>';
		echo "</tr>";
	
		$i=1;
		foreach (array_keys($links) as $link) {
			$record=WT_GedcomRecord::getInstance($link);
			echo "<tr ><td>";
			echo $i++;
			echo "</td><td id=\"existId_", $i, "\" class=\"row2\">";
			echo $link;
			echo "</td><td>";
			echo $record->getFullName();
			echo "</td>";
			echo "<td align='center'><input alt='", WT_I18N::translate('Keep Link in list'), "', title='", WT_I18N::translate('Keep Link in list'), "' type='radio' id='", $link, "_off' name='", $link, "' checked></td>";
			echo "<td align='center'><input alt='", WT_I18N::translate('Remove Link from list'), "', title='", WT_I18N::translate('Remove Link from list'), "' type='radio' id='", $link, "_on'  name='", $link, "'></td>";
	
			if ($record->getType()=='INDI') {
				?>
				<td align="center"><img style="border-style:none; margin-top:5px;" src="<?php echo $WT_IMAGES['button_family']; ?>" alt="<?php echo WT_I18N::translate('Open Family Navigator'); ?>" title="<?php echo WT_I18N::translate('Open Family Navigator'); ?>" name="family_'<?php echo $link; ?>'" onclick="openFamNav('<?php echo $link; ?>');"></td>
				<?php
			} elseif ($record->getType()=='FAM') {
				if ($record->getHusband()) {
					$head=$record->getHusband()->getXref();
				} elseif ($record->getWife()) {
					$head=$record->getWife()->getXref();
				} else {
					$head='';
				}
				?>
				<td align="center"><img style="border-style:none; margin-top:5px;" src="<?php echo $WT_IMAGES['button_family']; ?>" alt="<?php echo WT_I18N::translate('Open Family Navigator'); ?>" title="<?php echo WT_I18N::translate('Open Family Navigator'); ?>" name="family_'<?php echo $link; ?>'" onclick="openFamNav('<?php echo $head; ?>');"></td>
				<?php
			} else {
				echo '<td></td>';
			}
			echo '</tr>';
		}
	
		echo "</table>";
		echo "</td></tr></table>";
		echo "<br>";
		echo '</td></tr>';
	}

	if (!isset($linktoid)) { $linktoid = ""; }

	echo '<tr><td class="descriptionbox wrap">';
	echo WT_I18N::translate('Add links');
	echo '<td class="optionbox wrap ">';
	if ($linktoid=="") {
		// ----
	} else {
		$record=WT_Person::getInstance($linktoid);
		echo '<b>', $record->getFullName(), '</b>';
	}
	echo '<table><tr><td>';
		echo "<input type=\"text\" name=\"gid\" id=\"gid\" size=\"6\" value=\"\">";
		// echo ' Enter Name or ID &nbsp; &nbsp; &nbsp; <b>OR</b> &nbsp; &nbsp; &nbsp;Search for ID ';
	echo '</td><td style=" padding-bottom:2px; vertical-align:middle">';
		echo '&nbsp;';
		if (isset($WT_IMAGES["add"])) {
			echo '<img style="border-style:none;" src="', $WT_IMAGES["add"], '" alt="', WT_I18N::translate('Add'), ' "title="', WT_I18N::translate('Add'), '" align="middle" name="addLink" value="" onclick="blankwin(); return false;">';
			} else {
			echo '<button name="addLink" value="" type="button" onclick="blankwin(); return false;">', WT_I18N::translate('Add'), '</button>';
		}
		echo '&nbsp;&nbsp;&nbsp;&nbsp;';
		print_findindi_link("gid", "");
		echo '&nbsp;';
		print_findfamily_link("gid");
		echo '&nbsp;';
		print_findsource_link("gid");
	echo '</td></tr></table>';
	echo "<sub>" . WT_I18N::translate('Enter or search for the ID of the person, family, or source to which this media item should be linked.') . "</sub>";


	echo '<br><br>';
	echo '<input type="hidden" name="idName" id="idName" size="36" value="Name of ID">';
?>
<script type="text/javascript">

	function addlinks(iname) {
		// iid=document.getElementById('gid').value;
		if (document.getElementById('gid').value == "") {
			alert(id_empty);
		} else {
			addmedia_links(document.getElementById('gid'), document.getElementById('gid').value, iname );
			return false;
		}
	}

	function openFamNav(id) {
		//id=document.getElementById('gid').value;
		if (id.match("I")=="I" || id.match("i")=="i") {
			id = id.toUpperCase();
			winNav = window.open('edit_interface.php?action=addmedia_links&noteid=newnote&pid='+id, 'winNav', 'top=50,left=640,width=300,height=630,resizable=1,scrollbars=1');
			if (window.focus) {winNav.focus();}
		} else if (id.match("F")=="F") {
			id = id.toUpperCase();
			// TODO --- alert('Opening Navigator with family id entered will come later');
		}
	}
</script>
<table border="0" cellpadding="1" cellspacing="2" >
<tr>
<td width="350" class="row2">
	<style type="text/css">
	<!--
	.classy0 { font-family: Verdana, Arial, Helvetica, sans-serif; background-color: transparent; color: #000000; font-size: 10px; }
	.classy1 { font-family: Verdana, Arial, Helvetica, sans-serif; background-color: transparent; color: #000000; font-size: 10px; }
	-->
	</style>
	
<?php

// Various JavaScript variables required --------------------------------- ?>
<script type="text/javascript">
	var ifamily = "<?php echo WT_I18N::translate('Open Family Navigator'); ?>";
	var remove = "<?php echo WT_I18N::translate('Remove'); ?>";
	var linkExists = "<?php echo WT_I18N::translate('This link already exists'); ?>";
	/* ===icons === */
	var removeLinkIcon = "<?php echo $WT_IMAGES['remove']; ?>";
	var familyNavIcon = "<?php echo $WT_IMAGES['button_family']; ?>";
	

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
			} else {
				strRow += (strRow==''?'':', ') + links.rows[i].cells[1].textContent;
			}
		}
		strRow += (strRow==''?'':', ');
		
		//Check if id exists in Links list =================================
		if (strRow.match(pid+',')!= pid+',') {
			// alert('NO MATCH');
		} else {
			rowToInsertAt = 'EXIST' ;
		}

		// Check if id exists in "Add links" list ==========================
		for (var i=0; i<tbl.tBodies[0].rows.length; i++) {
			if (tbl.tBodies[0].rows[i].myRow.one.textContent==pid) {
				rowToInsertAt = 'EXIST' ;
			} else
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
		} else {
			alert(nam+' ('+pid+') - '+linkExists);
		}
		
	}
}

function removeHTMLTags(htmlString)
{
	if (htmlString) {
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
			
			if (num == null) { 
				num = nextRow;
			} else {
				iteration = num + ROW_BASE;
			}
			
			// add the row
			var row = tbl.tBodies[0].insertRow(num);
			
			// CONFIG: requires class
			row.className = 'descriptionbox';
			
			// CONFIG: This whole section can be configured
			
			// cell 0 - Count
			var cell0 = row.insertCell(0);
			cell0.style.fontSize="11px";
			var textNode = document.createTextNode(iteration);
			cell0.appendChild(textNode);
			
			// cell 1 - ID:
			var cell1 = row.insertCell(1);
			if (pid=='') {
				var txtInp1 = document.createElement('div');
				txtInp1.setAttribute('type', 'checkbox');
				if (txtInp1.checked!='') {
					txtInp1.setAttribute('value', 'no');
				} else {
					txtInp1.setAttribute('value', 'add');
				}
			} else {
				var txtInp1 = document.createElement('div');
				txtInp1.setAttribute('type', 'text');
				txtInp1.innerHTML = pid; // Required for IE
				txtInp1.textContent = pid;
			}
				txtInp1.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_1');
				txtInp1.style.background='transparent';
				txtInp1.style.border='0px';
				txtInp1.style.fontSize="11px";
			cell1.appendChild(txtInp1);
			
			// cell 2 - Name
			var cell2 = row.insertCell(2);
			var txtInp2 = document.createElement('div');
				txtInp2.setAttribute('type', 'text');
				txtInp2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_2');
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
				btnEl.setAttribute('src', removeLinkIcon);
				btnEl.setAttribute('alt', remove);
				btnEl.setAttribute('title', remove);
				btnEl.setAttribute('height', '14px');
				btnEl.onclick = function () {deleteCurrentRow(this)};
			cellbtn.appendChild(btnEl);
			
			// cell btn - family img button
			var cellbtn2 = row.insertCell(4);
				cellbtn2.setAttribute('align', 'center');
			if (pid.match("I")=="I" || pid.match("i")=="i") {
				var btn2El = document.createElement('img');
					btn2El.setAttribute('type', 'img');
					btn2El.setAttribute('src', familyNavIcon);
					btn2El.setAttribute('alt', ifamily);
					btn2El.setAttribute('title', ifamily);
					btn2El.onclick = function () {openFamNav(pid)};
				cellbtn2.appendChild(btn2El);
			} else if (pid.match("F")=="F" || pid.match("f")=="f") {
				var btn2El = document.createElement('img');
					btn2El.setAttribute('type', 'img');
					btn2El.setAttribute('src', familyNavIcon);
					btn2El.setAttribute('alt', ifamily);
					btn2El.setAttribute('title', ifamily);
					btn2El.onclick = function () {openFamNav(head)};
				cellbtn2.appendChild(btn2El);
			} else {
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


</script>

	<table width="430" border="0" cellspacing="1" id="addlinkQueue">
		<thead>
		<tr>
			<th class="topbottombar" width="10"  style="font-weight:100;" align="left">#</th>
			<th class="topbottombar" width="55"  style="font-weight:100;" align="left">ID:</th>
			<th class="topbottombar" width="370" style="font-weight:100;" align="left"><?php echo WT_I18N::translate('Name'); ?></th>
			<th class="topbottombar" width="20"  style="font-weight:100;" align="left"><?php echo WT_I18N::translate('Remove'); ?></th>
			<th class="topbottombar" width="20"  style="font-weight:100;" align="left"><?php echo WT_I18N::translate('Navigator'); ?></th>
		</tr>
		</thead>
		<tbody></tbody>
	</table>
</td>
</tr>
</table>
<?php
	echo '</td></tr>';
	// Admin Option CHAN log update override =======================
	if (WT_USER_IS_ADMIN) {
		echo "<tr><td class=\"descriptionbox wrap width25\">";
		echo WT_Gedcom_Tag::getLabel('CHAN'), "</td><td class=\"optionbox wrap\">";
		if ($NO_UPDATE_CHAN) {
			echo "<input type=\"checkbox\" checked=\"checked\" name=\"preserve_last_changed\">";
		} else {
			echo "<input type=\"checkbox\" name=\"preserve_last_changed\">";
		}
		echo WT_I18N::translate('Do not update the “last change” record'), help_link('no_update_CHAN'), '<br><br>';
		echo "</td></tr>";
	}
	echo '</tr>';
	echo '<input type="hidden" name="more_links" value="No_Values">';
	echo '<input type="hidden" name="exist_links" value="No_Values">';
	echo '<tr><td colspan="2">';
	echo '</td></tr>';
	echo '<tr><td class="topbottombar" colspan="2">';
	echo '<center><input type="submit" value="', WT_I18N::translate('Save'), '" onclick="shiftlinks();">';
	echo '</center></td></tr>';
?>
<script>
function parseAddLinks() {
	str = '';
	var tbl = document.getElementById('addlinkQueue');
	for (var i=1; i<tbl.rows.length; i++) { // start at i=1 because we need to avoid header
		var tr = tbl.rows[i];
		if (IE) {
			str += (str==''?'':', ') + tr.cells[1].childNodes[0].innerHTML;
		} else {
			str += (str==''?'':', ') + tr.cells[1].childNodes[0].textContent;
		}
	}
}

function parseRemLinks() {
	remstr = "";
	var tbl = document.getElementById('existLinkTbl');
	for (var i=1; i<tbl.rows.length; i++) { // start at i=1 because we need to avoid header
		var remtr = tbl.rows[i];
		var remstrRow = '';
		for (var j=1; j<remtr.cells.length; j++) { // Start at col 1 (j=1)
			if (j!=4 ) {
				// dont show col 0 index
				// miss out  col 2 name
				// miss out  col 3 keep radio button
				// choose    col 4 remove radio button
				continue;
			} else {
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
	// alert('remstring = '+ remstr);
	if (remstr) {
		document.link.exist_links.value = remstr;
	}

	parseAddLinks();
	// alert('string = '+ str);
	if (str) {
		document.link.more_links.value = str;
	} else {
		// leave hidden input morelinks as "No Values"
		var inputField = document.getElementById('gid');
	// alert(inputField.value)
		if (inputField) {
			document.link.more_links.value = inputField.value+',';
		}
	}
	if (winNav) {
		winNav.close();
	}
}

</script>
<?php
	echo '</table>';
	echo '</form>';
	echo '<br><br><center><a href="#" onclick="if (window.opener.showchanges) window.opener.showchanges(); window.close(); winNav.close(); ">', WT_I18N::translate('Close Window'), '</a><br></center>';

} elseif ($action == "update" && $paramok) {

	echo "<b>", $mediaid, "</b><br><br>";

	// Unlink records indicated by radio button =========
	if (isset($exist_links) && $exist_links!="No_Values") {
		$exist_links = substr($exist_links, 0, -1);
		$rem_exist_links = (explode(", ", $exist_links));
		foreach ($rem_exist_links as $remLinkId) {
			echo WT_I18N::translate('Link to %s deleted', $remLinkId);
			echo '<br>';
			if ($update_CHAN=='no_change') {
				unlinkMedia($remLinkId, 'OBJE', $mediaid, 1, false);
			} else {
				unlinkMedia($remLinkId, 'OBJE', $mediaid, 1, true);
			}
		}
		echo '<br>';
	} else {
		// echo nothing and do nothing
	}

	// Add new Links ====================================
	if (isset($more_links) && $more_links!="No_Values" && $more_links!=",") {
		$add_more_links = (explode(", ", $more_links));
		foreach ($add_more_links as $addLinkId) {
			echo WT_I18N::translate('Link to %s added', $addLinkId);
			if ($update_CHAN=='no_change') {
				linkMedia($mediaid, $addLinkId, 1, false);
			} else {
				linkMedia($mediaid, $addLinkId, 1, true);
			}
			echo '<br>';
		}
		echo '<br>';
	}

	if ($update_CHAN=='no_change') {
		echo WT_I18N::translate('No CHAN (Last Change) records were updated');
		echo '<br>';
	}

	echo '<br><br><center><a href="#" onclick="if (window.opener.showchanges) window.opener.showchanges(); window.close(); winNav.close(); ">', WT_I18N::translate('Close Window'), '</a><br></center>';
} else {
	// echo '<center>You must be logged in as an Administrator<center>';
	echo '<br><br><center><a href="#" onclick="if (window.opener.showchanges) window.opener.showchanges(); window.close(); winNav.close();">', WT_I18N::translate('Close Window'), '</a><br></center>';
}

<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Functions\FunctionsPrint;

$more_links  = Filter::get('more_links');
$exist_links = Filter::get('exist_links');
$gid         = Filter::get('gid', WT_REGEX_XREF);
$update_CHAN = Filter::get('preserve_last_changed');

$controller
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();');

$paramok = true;
if (!empty($linktoid)) {
	$paramok = GedcomRecord::getInstance($linktoid, $WT_TREE)->canShow();
}

if ($action == 'choose' && $paramok) {

	?>
	<script>
	// Javascript variables
	var id_empty = "<?php echo I18N::translate('When adding a link, the ID field cannot be empty.'); ?>";

	function blankwin() {
		if (document.getElementById('gid').value == "" || document.getElementById('gid').value.length<=1) {
			alert(id_empty);
		} else {
			var iid = document.getElementById('gid').value;
			var winblank = window.open('module.php?mod=GEDFact_assistant&mod_action=media_query_3a&iid='+iid, 'winblank', 'top=100, left=200, width=400, height=20, toolbar=0, directories=0, location=0, status=0, menubar=0, resizable=1, scrollbars=1');
		}
	}

	var GEDFact_assist = 'installed';
	</script>

	<?php
	echo '<form class="medialink" name="link" method="get" action="inverselink.php">';
	echo '<input type="hidden" name="action" value="update">';
	if (!empty($mediaid)) {
		echo '<input type="hidden" name="mediaid" value="', $mediaid, '">';
	}
	if (!empty($linktoid)) {
		echo '<input type="hidden" name="linktoid" value="', $linktoid, '">';
	}
	echo '<input type="hidden" name="linkto" value="', $linkto, '">';
	echo '<input type="hidden" name="ged" value="', $WT_TREE->getNameHtml(), '">';
	echo '<table class="facts_table center">';
	echo '<tr><td class="topbottombar" colspan="2">';
	echo I18N::translate('Link to an existing media object');
	echo '</td></tr><tr><td class="descriptionbox width20 wrap">', I18N::translate('Media'), '</td>';
	echo '<td class="optionbox wrap">';
	if (!empty($mediaid)) {
		//-- Get the title of this existing Media item
		$title =
			Database::prepare("SELECT m_titl FROM `##media` where m_id=? AND m_file=?")
			->execute(array($mediaid, $WT_TREE->getTreeId()))
			->fetchOne();
		if ($title) {
			echo '<b>', $title, '</b>';
		} else {
			echo '<b>', $mediaid, '</b>';
		}
		echo '<table><tr><td>';
		//-- Get the filename of this existing Media item
		$filename =
			Database::prepare("SELECT m_filename FROM `##media` where m_id=? AND m_file=?")
			->execute(array($mediaid, $WT_TREE->getTreeId()))
			->fetchOne();
		$media = Media::getInstance($mediaid, $WT_TREE);
		echo $media->displayImage();
		echo '</td></tr></table>';
		echo '</td></tr>';
		echo '<tr><td class="descriptionbox width20 wrap">', I18N::translate('Links'), '</td>';
		echo '<td class="optionbox wrap">';
		echo '<table><tr><td>';
		echo '<table id="existLinkTbl" width="430" cellspacing="1" >';
		echo '<tr>';
		echo '<td class="topbottombar" width="15"  style="font-weight:100;" >#</td>';
		echo '<td class="topbottombar" width="50"  style="font-weight:100;" >', I18N::translate('Record'), '</td>';
		echo '<td class="topbottombar" width="340" style="font-weight:100;" >', I18N::translate('Name'), '</td>';
		echo '<td class="topbottombar" width="20"  style="font-weight:100;" >', I18N::translate('Keep'), '</td>';
		echo '<td class="topbottombar" width="20"  style="font-weight:100;" >', I18N::translate('Remove'), '</td>';
		echo '<td class="topbottombar" width="20"  style="font-weight:100;" >', I18N::translate('Family navigator'), '</td>';
		echo "</tr>";

		$links = array_merge(
			$media->linkedIndividuals('OBJE'),
			$media->linkedFamilies('OBJE'),
			$media->linkedSources('OBJE'),
			$media->linkedNotes('OBJE'), // Invalid GEDCOM - you cannot link a NOTE to an OBJE
			$media->linkedRepositories('OBJE') // Invalid GEDCOM - you cannot link a REPO to an OBJE
		);
		$i = 1;
		foreach ($links as $record) {
			echo '<tr ><td>';
			echo $i++;
			echo '</td><td id="existId_', $i, '" class="row2">';
			echo $record->getXref();
			echo '</td><td>';
			echo $record->getFullName();
			echo '</td>';
			echo '<td><input title="', I18N::translate('Keep link in list'), '" type="radio" id="', $record->getXref(), '_off" name="', $record->getXref(), '" checked></td>';
			echo '<td><input title="', I18N::translate('Remove link from list'), '" type="radio" id="', $record->getXref(), '_on"  name="', $record->getXref(), '"></td>';

			if ($record instanceof Individual) {
				?>
				<td><a href="#" class="icon-button_family" name="family_'<?php echo $record->getXref(); ?>'" onclick="openFamNav('<?php echo $record->getXref(); ?>'); return false;"></a></td>
				<?php
			} elseif ($record instanceof Family) {
				if ($record->getHusband()) {
					$head = $record->getHusband()->getXref();
				} elseif ($record->getWife()) {
					$head = $record->getWife()->getXref();
				} else {
					$head = '';
				}
				?>
				<td><a href="#" class="icon-button_family" name="family_'<?php echo $record->getXref(); ?>'" onclick="openFamNav('<?php echo $head; ?>');"></a></td>
				<?php
			} else {
				echo '<td></td>';
			}
			echo '</tr>';
		}

		echo "</table>";
		echo "</td></tr></table>";
		echo '</td></tr>';
	}

	if (!isset($linktoid)) { $linktoid = ""; }

	echo '<tr><td class="descriptionbox wrap">';
	echo I18N::translate('Add links');
	echo '<td class="optionbox wrap ">';
	if ($linktoid == "") {
		// ----
	} else {
		$record = Individual::getInstance($linktoid, $WT_TREE);
		echo '<b>', $record->getFullName(), '</b>';
	}
	echo '<table><tr><td>';
	echo '<input type="text" data-autocomplete-type="IFS" name="gid" id="gid" size="6" value="">';
	echo '</td><td style="padding-bottom: 2px; vertical-align: middle;">';
	echo '&nbsp;';
	echo '<a href="#" class="icon-add" title="', I18N::translate('Add'), '" onclick="blankwin(); return false;"></a>';
	echo ' ', FunctionsPrint::printFindIndividualLink('gid');
	echo ' ', FunctionsPrint::printFindFamilyLink('gid');
	echo ' ', FunctionsPrint::printFindSourceLink('gid');
	echo '</td></tr></table>';
	echo "<sub>" . I18N::translate('Enter or search for the ID of the individual, family, or source to which this media object should be linked.') . "</sub>";
	echo '<br><br>';
	echo '<input type="hidden" name="idName" id="idName" size="36" value="Name of ID">';

?>
<script>

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
		winNav = window.open('edit_interface.php?action=addmedia_links&noteid=newnote&pid='+id, 'winNav', fam_nav_specs);
		if (window.focus) {
			winNav.focus();
		}
	}

var INPUT_NAME_PREFIX = 'InputCell_'; // this is being set via script
var RADIO_NAME = "totallyrad"; // this is being set via script
var TABLE_NAME = 'addlinkQueue'; // this should be named in the HTML
var ROW_BASE = 1; // first number (for display)
var hasLoaded = false;

window.onload=fillInRows;

function fillInRows() {
	hasLoaded = true;
}

// CONFIG
// myRowObject is an object for storing information about the table rows
//function myRowObject(zero, one, two, three, four, five, six, seven, eight, nine, ten, cb, ra)
function myRowObject(zero, one, two, cb, ra) {
	this.zero = zero; // text object
	this.one  = one;   // input text object
	this.two  = two;  // input text object
	this.cb   = cb;   // input checkbox object
	this.ra   = ra;   // input radio object
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
		var links = document.getElementById('existLinkTbl');
		var numrows = links.rows.length;
		var strRow = '';
		for (var i=1; i<numrows; i++) {
			if (typeof links.rows[i].cells[1].textContent !== "undefined") {
				strRow += (strRow==''?'':', ') + links.rows[i].cells[1].textContent;
			} else {
				strRow += (strRow==''?'':', ') + links.rows[i].cells[1].innerText;
			}
		}
		strRow += (strRow==''?'':', ');

		//Check if id exists in Links list =================================
		if (strRow.match(pid+',')!= pid+',') {
		} else {
			rowToInsertAt = 'EXIST' ;
		}

		// Check if id exists in "Add links" list ==========================
		for (var i=0; i<tbl.tBodies[0].rows.length; i++) {
			var cellText;
			if (typeof tbl.tBodies[0].rows[i].myRow.one.textContent !== "undefined") {
				cellText = tbl.tBodies[0].rows[i].myRow.one.textContent;
			} else {
				cellText = tbl.tBodies[0].rows[i].myRow.one.innerText;
			}
			if (cellText==pid) {
				rowToInsertAt = 'EXIST';
			} else
			if (tbl.tBodies[0].rows[i].myRow && tbl.tBodies[0].rows[i].myRow.ra.getAttribute('type') == 'radio' && tbl.tBodies[0].rows[i].myRow.ra.checked) {
				rowToInsertAt = i;
				break;
			}
		}

		// If Link does not exist then add it, or show alert ===============
		if (rowToInsertAt!='EXIST') {
			rowToInsertAt = i;
			addRowToTable(rowToInsertAt, pid, nam, head);
			reorderRows(tbl, rowToInsertAt);
		}

	}
}

function removeHTMLTags(htmlString)
{
	if (htmlString) {
		var mydiv = document.createElement("div");
			mydiv.innerHTML = htmlString;
		if (typeof mydiv.textContent !== "undefined") {
			return mydiv.textContent;
		}
		return mydiv.innerText;
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
				if (typeof txtInp1.textContent !== "undefined") {
					txtInp1.textContent = pid;
				} else {
					txtInp1.innerText = pid;
				}
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
				txtInp2.innerHTML = removeHTMLTags(unescape(nam));
			cell2.appendChild(txtInp2);

			// cell btn - remove img button
			var cellbtn = row.insertCell(3);
			var btnEl = jQuery('<a href="#" class="icon-remove"></a>');
			btnEl.on('click', function () {deleteCurrentRow(this)});
			jQuery(cellbtn).append(btnEl);

			// cell btn - family img button
			var cellbtn2 = row.insertCell(4);
			if (pid.match("I")=="I" || pid.match("i")=="i") {
				var btn2El = jQuery('<a href="#" class="icon-button_family"></a>');
				btn2El.on('click', function() {openFamNav(pid)});
				jQuery(cellbtn2).append(btn2El);
			} else if (pid.match("F")=="F" || pid.match("f")=="f") {
				var btn2El = jQuery('<a href="#" class="icon-button_family"></a>');
				btn2El.on('click', function () {openFamNav(head)});
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
// If there isn't a checkbox in your row, then this function can’t be used.
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

// If there isn't an element with an onclick event in your row, then this function can’t be used.
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

function reorderRows(tbl, startingIndex) {
	if (hasLoaded) {
		if (tbl.tBodies[0].rows[startingIndex]) {
			var count = startingIndex + ROW_BASE;
			for (var i=startingIndex; i<tbl.tBodies[0].rows.length; i++) {

				// CONFIG: next line is affected by myRowObject settings
				tbl.tBodies[0].rows[i].myRow.zero.data = count; // text
				tbl.tBodies[0].rows[i].myRow.one.id = INPUT_NAME_PREFIX + count + '_1'; // input text
				tbl.tBodies[0].rows[i].myRow.two.id = INPUT_NAME_PREFIX + count + '_2'; // input text
				tbl.tBodies[0].rows[i].myRow.one.name = INPUT_NAME_PREFIX + count + '_1'; // input text
				tbl.tBodies[0].rows[i].myRow.two.name = INPUT_NAME_PREFIX + count + '_2'; // input text

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

function parseAddLinks() {
	// start with the "newly added" ID.
	var str = document.getElementById('gid').value;
	// Add in the "keep" IDs.
	var tbl = document.getElementById('addlinkQueue');
	// start at i=1 because we need to avoid header
	for (var i=1; i<tbl.rows.length; i++) {
		var tr = tbl.rows[i];
		if (typeof tr.cells[1].childNodes[0].textContent !== "undefined") {
			str += (str==''?'':',') + tr.cells[1].childNodes[0].textContent;
		} else {
			str += (str==''?'':',') + tr.cells[1].childNodes[0].innerHTML;
		}
	}
	document.link.more_links.value = str;
}

function parseRemLinks() {
	var remstr = "";
	var tbl = document.getElementById('existLinkTbl');
	// start at i=1 because we need to avoid header
	for (var i=1; i<tbl.rows.length; i++) {
		var remtr = tbl.rows[i];
		if (remtr.cells[4].childNodes[0].checked)  {
			remstr += (remstr==''?'':',') + remtr.cells[4].childNodes[0].name;
		}
	}
	document.link.exist_links.value = remstr;
}

function shiftlinks() {
	parseRemLinks();
	parseAddLinks();
	if (winNav) {
		winNav.close();
	}
}

</script>

				<table width="430" border="0" cellspacing="1" id="addlinkQueue">
					<thead>
						<tr>
							<th class="topbottombar" width="10"  style="font-weight:100;">#</th>
							<th class="topbottombar" width="55"  style="font-weight:100;"><?php echo I18N::translate('Record'); ?></th>
							<th class="topbottombar" width="370" style="font-weight:100;"><?php echo I18N::translate('Name'); ?></th>
							<th class="topbottombar" width="20"  style="font-weight:100;"><?php echo I18N::translate('Remove'); ?></th>
							<th class="topbottombar" width="20"  style="font-weight:100;"><?php echo I18N::translate('Family navigator'); ?></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
		// Admin Option CHAN log update override =======================
		if (Auth::isAdmin()) {
			echo '<tr><td class="descriptionbox wrap width25">';
			echo GedcomTag::getLabel('CHAN'), '</td><td class="optionbox wrap">';
			if ($WT_TREE->getPreference('NO_UPDATE_CHAN')) {
				echo '<input type="checkbox" checked name="preserve_last_changed">';
			} else {
				echo '<input type="checkbox" name="preserve_last_changed">';
			}
			echo I18N::translate('Keep the existing “last change” information');
			echo '</td></tr>';
		}
		?>
	</table>
	<input type="hidden" name="more_links" value="No_Values">
	<input type="hidden" name="exist_links" value="No_Values">
	<p id="save-cancel">
		<input type="submit" class="save" value="<?php echo I18N::translate('save'); ?>" onclick="shiftlinks();">
		<input type="button" class="cancel" value="<?php echo I18N::translate('close'); ?>" onclick="window.close();">
	</p>
</form>
<?php
} elseif ($action == "update" && $paramok) {
	// Unlink records indicated by radio button =========
	if ($exist_links) {
		foreach (explode(',', $exist_links) as $remLinkId) {
			$indi = GedcomRecord::getInstance($remLinkId, $WT_TREE);
			$indi->removeLinks($mediaid, $update_CHAN != 'no_change');
		}
	}
	// Add new Links ====================================
	if ($more_links) {
		// array_unique() because parseAddLinks() may includes the gid field, even
		// when it is also in the list.
		foreach (array_unique(explode(',', $more_links)) as $addLinkId) {
			$indi = GedcomRecord::getInstance($addLinkId, $WT_TREE);
			$indi->createFact('1 OBJE @' . $mediaid . '@', $update_CHAN != 'no_change');
		}
	}
	$controller->addInlineJavascript('closePopupAndReloadParent();');
}

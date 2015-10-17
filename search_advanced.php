<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

use Fisharebest\Webtrees\Controller\AdvancedSearchController;

define('WT_SCRIPT_NAME', 'search_advanced.php');
require './includes/session.php';

$controller = new AdvancedSearchController;
$controller
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();')
	->pageHeader();

echo '<script>';
?>
	function checknames(frm) {
		action = "<?php echo $controller->action; ?>";

		return true;
	}

	var numfields = <?php echo count($controller->fields); ?>;
	/**
	 * add a row to the table of fields
	 */
	function addFields() {
		// get the table
		var tbl = document.getElementById('field_table').tBodies[0];
		// create the new row
		var trow = document.createElement('tr');
		// create the new label cell
		var label = document.createElement('td');
		label.className='list_label';
		// create a select for the user to choose the field
		var sel = document.createElement('select');
		sel.name = 'fields['+numfields+']';
		sel.rownum = numfields;
		sel.onchange = function() {
			showDate(this, this.rownum);
		};

		// all of the field options
		<?php foreach ($controller->getOtherFields() as $field => $label) { ?>
		opt = document.createElement('option');
		opt.value='<?php echo $field; ?>';
		opt.text='<?php echo Filter::escapeJs($label); ?>';
		sel.options.add(opt);
		<?php } ?>
		label.appendChild(sel);
		trow.appendChild(label);
		// create the new value cell
		var val = document.createElement('td');
		val.id = 'vcell'+numfields;
		val.className='list_value';

		var inp = document.createElement('input');
		inp.name='values['+numfields+']';
		inp.type='text';
		inp.id='value'+numfields;
		inp.tabindex=numfields+1;
		val.appendChild(inp);
		trow.appendChild(val);
		var lastRow = tbl.lastChild.previousSibling;

		tbl.insertBefore(trow, lastRow.nextSibling);
		numfields++;
	}

	/**
	 * add the date options selection
	 */
	function showDate(sel, row) {
		var type = sel.options[sel.selectedIndex].value;
		var pm = document.getElementById('plusminus'+row);
		if (!type.match("DATE$")) {
			// if it is not a date do not show the date
			if (pm) pm.parentNode.removeChild(pm);
			return;
		}
		// if it is a date and the plusminus is already show, then leave
		if (pm) return;
		var elm = document.getElementById('vcell'+row);
		var sel = document.createElement('select');
		sel.id = 'plusminus'+row;
		sel.name = 'plusminus['+row+']';
		var opt = document.createElement('option');
		opt.value='';
		opt.text='<?php echo I18N::translate('Exact date'); ?>';
		sel.appendChild(opt);
		opt = document.createElement('option');
		opt.value='';
		opt.text='<?php echo html_entity_decode(I18N::plural('±%s year', '±%s years', 2, I18N::number(2)), ENT_COMPAT, 'UTF-8'); ?>';
		sel.appendChild(opt);
		opt = document.createElement('option');
		opt.value='5';
		opt.text='<?php echo html_entity_decode(I18N::plural('±%s year', '±%s years', 5, I18N::number(5)), ENT_COMPAT, 'UTF-8'); ?>';
		sel.appendChild(opt);
		opt = document.createElement('option');
		opt.value='10';
		opt.text='<?php echo html_entity_decode(I18N::plural('±%s year', '±%s years', 10, I18N::number(10)), ENT_COMPAT, 'UTF-8'); ?>';
		sel.appendChild(opt);
		var spc = document.createTextNode(' ');
		elm.appendChild(spc);
		elm.appendChild(sel);
	}
<?php
echo '</script>';
?>
<div id="search-page">
<h2 class="center"><?php echo $controller->getPageTitle(); ?></h2>
<?php $controller->printResults(); ?>
<form name="searchform" onsubmit="return checknames(this);">
<input type="hidden" name="action" value="<?php echo $controller->action; ?>">
<input type="hidden" name="isPostBack" value="true">


<div class="table-holder">
<table id="field_table" class="list_table" border="0">
	<!-- // search terms -->
	<?php
	$fct = count($controller->fields);
	for ($i = 0; $i < $fct; $i++) {
		if (strpos($controller->getField($i), "FAMC:HUSB:NAME") === 0) {
			continue;
		}
		if (strpos($controller->getField($i), "FAMC:WIFE:NAME") === 0) {
			continue;
		}
	?>
	<tr>
		<td class="list_label">
			<?php echo $controller->getLabel($controller->getField($i)); ?>
		</td>
		<td id="vcell<?php echo $i; ?>" class="list_value">
			<?php
			$currentFieldSearch = $controller->getField($i); // Get this field’s name and the search criterion
			$currentField       = substr($currentFieldSearch, 0, strrpos($currentFieldSearch, ':')); // Get the actual field name
			?>
				<input type="text" id="value<?php echo $i; ?>" name="values[<?php echo $i; ?>]" value="<?php echo Filter::escapeHtml($controller->getValue($i)); ?>"<?php echo substr($controller->getField($i), -4) == 'PLAC' ? 'data-autocomplete-type="PLAC"' : ''; ?>>
			<?php if (preg_match("/^NAME:/", $currentFieldSearch) > 0) { ?>
				<select name="fields[<?php echo $i; ?>]">
					<option value="<?php echo $currentField; ?>:EXACT" <?php if (preg_match("/:EXACT$/", $currentFieldSearch) > 0) echo 'selected'; ?>><?php echo I18N::translate('Exact'); ?></option>
					<option value="<?php echo $currentField; ?>:BEGINS" <?php if (preg_match("/:BEGINS$/", $currentFieldSearch) > 0) echo 'selected'; ?>><?php echo I18N::translate('Begins with'); ?></option>
					<option value="<?php echo $currentField; ?>:CONTAINS" <?php if (preg_match("/:CONTAINS$/", $currentFieldSearch) > 0) echo 'selected'; ?>><?php echo I18N::translate('Contains'); ?></option>
					<option value="<?php echo $currentField; ?>:SDX" <?php if (preg_match("/:SDX$/", $currentFieldSearch) > 0) echo 'selected'; ?>><?php echo I18N::translate('Sounds like'); ?></option>
				</select>
			<?php } else { ?>
			<input type="hidden" name="fields[<?php echo $i; ?>]" value="<?php echo $controller->getField($i); ?>">
			<?php }
			if (preg_match("/:DATE$/", $currentFieldSearch) > 0) {
				?>
				<select name="plusminus[<?php echo $i; ?>]">
					<option value=""><?php echo I18N::translate('Exact date'); ?></option>
					<option value="2" <?php if (!empty($controller->plusminus[$i]) && $controller->plusminus[$i] == 2) echo 'selected'; ?>><?php echo I18N::plural('±%s year', '±%s years', 2, I18N::number(2)); ?></option>
					<option value="5" <?php if (!empty($controller->plusminus[$i]) && $controller->plusminus[$i] == 5) echo 'selected'; ?>><?php echo I18N::plural('±%s year', '±%s years', 5, I18N::number(5)); ?></option>
					<option value="10" <?php if (!empty($controller->plusminus[$i]) && $controller->plusminus[$i] == 10) echo 'selected'; ?>><?php echo I18N::plural('±%s year', '±%s years', 10, I18N::number(10)); ?></option>
				</select>
			<?php } ?>
		</td>
		<?php
		//-- relative fields
		if ($i == 0 && $fct > 4) {
			$j = $fct;
			// Get the current options for Father’s and Mother’s name searches
			$fatherGivnOption = 'SDX';
			$fatherSurnOption = 'SDX';
			$motherGivnOption = 'SDX';
			$motherSurnOption = 'SDX';
			for ($k = 0; $k < $fct; $k++) {
				$searchField  = $controller->getField($k);
				$searchOption = substr($searchField, 20); // Assume we have something like "FAMC:HUSB:NAME:GIVN:foo"
				switch (substr($searchField, 0, 20)) {
				case 'FAMC:HUSB:NAME:GIVN:':
					$fatherGivnOption = $searchOption;
					break;
				case 'FAMC:HUSB:NAME:SURN:':
					$fatherSurnOption = $searchOption;
					break;
				case 'FAMC:WIFE:NAME:GIVN:':
					$motherGivnOption = $searchOption;
					break;
				case 'FAMC:WIFE:NAME:SURN:':
					$motherSurnOption = $searchOption;
					break;
				}
			}
			?>
		<?php } ?>
	</tr>
	<?php } ?>
	<!--  father -->
	<tr>
		<td colspan="2" class="facts_label03" style="text-align:center;">
			<?php echo I18N::translate('Father'); ?>
		</td>
	</tr>
	<tr>
		<td class="list_label">
			<?php echo GedcomTag::getLabel('GIVN'); ?>
		</td>
		<td class="list_value">
			<input type="text" name="values[<?php echo $j; ?>]" value="<?php echo $controller->getValue($controller->getIndex('FAMC:HUSB:NAME:GIVN:' . $fatherGivnOption)); ?>">
			<select name="fields[<?php echo $j; ?>]">
				<option value="FAMC:HUSB:NAME:GIVN:EXACT" <?php if ($fatherGivnOption == 'EXACT') echo 'selected'; ?>><?php echo I18N::translate('Exact'); ?></option>
				<option value="FAMC:HUSB:NAME:GIVN:BEGINS" <?php if ($fatherGivnOption == 'BEGINS') echo 'selected'; ?>><?php echo I18N::translate('Begins with'); ?></option>
				<option value="FAMC:HUSB:NAME:GIVN:CONTAINS" <?php if ($fatherGivnOption == 'CONTAINS') echo 'selected'; ?>><?php echo I18N::translate('Contains'); ?></option>
				<option value="FAMC:HUSB:NAME:GIVN:SDX" <?php if ($fatherGivnOption == 'SDX') echo 'selected'; ?>><?php echo I18N::translate('Sounds like'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<?php $j++; ?>
		<td class="list_label">
			<?php echo GedcomTag::getLabel('SURN'); ?>
		</td>
		<td class="list_value">
			<input type="text" name="values[<?php echo $j; ?>]" value="<?php echo $controller->getValue($controller->getIndex('FAMC:HUSB:NAME:SURN:' . $fatherSurnOption)); ?>">
			<select name="fields[<?php echo $j; ?>]">
				<option value="FAMC:HUSB:NAME:SURN:EXACT" <?php if ($fatherSurnOption == 'EXACT') echo 'selected'; ?>><?php echo I18N::translate('Exact'); ?></option>
				<option value="FAMC:HUSB:NAME:SURN:BEGINS" <?php if ($fatherSurnOption == 'BEGINS') echo 'selected'; ?>><?php echo I18N::translate('Begins with'); ?></option>
				<option value="FAMC:HUSB:NAME:SURN:CONTAINS" <?php if ($fatherSurnOption == 'CONTAINS') echo 'selected'; ?>><?php echo I18N::translate('Contains'); ?></option>
				<option value="FAMC:HUSB:NAME:SURN:SDX" <?php if ($fatherSurnOption == 'SDX') echo 'selected'; ?>><?php echo I18N::translate('Sounds like'); ?></option>
			</select>
		</td>
	</tr>
	<!--  mother -->
	<?php $j++; ?>
	<tr>
		<td colspan="2" class="facts_label03" style="text-align:center;">
			<?php echo I18N::translate('Mother'); ?>
		</td>
	</tr>
	<tr>
		<td class="list_label">
			<?php echo GedcomTag::getLabel('GIVN'); ?>
		</td>
		<td class="list_value">
			<input type="text" name="values[<?php echo $j; ?>]" value="<?php echo $controller->getValue($controller->getIndex('FAMC:WIFE:NAME:GIVN:' . $motherGivnOption)); ?>">
			<select name="fields[<?php echo $j; ?>]">
				<option value="FAMC:WIFE:NAME:GIVN:EXACT" <?php if ($motherGivnOption == 'EXACT') echo 'selected'; ?>><?php echo I18N::translate('Exact'); ?></option>
				<option value="FAMC:WIFE:NAME:GIVN:BEGINS" <?php if ($motherGivnOption == 'BEGINS') echo 'selected'; ?>><?php echo I18N::translate('Begins with'); ?></option>
				<option value="FAMC:WIFE:NAME:GIVN:CONTAINS" <?php if ($motherGivnOption == 'CONTAINS') echo 'selected'; ?>><?php echo I18N::translate('Contains'); ?></option>
				<option value="FAMC:WIFE:NAME:GIVN:SDX" <?php if ($motherGivnOption == 'SDX') echo 'selected'; ?>><?php echo I18N::translate('Sounds like'); ?></option>
			</select>
		</td>
		<?php $j++; ?>
	</tr>
	<tr>
		<td class="list_label">
			<?php echo GedcomTag::getLabel('SURN'); ?>
		</td>
		<td class="list_value">
			<input type="text" name="values[<?php echo $j; ?>]" value="<?php echo $controller->getValue($controller->getIndex('FAMC:WIFE:NAME:SURN:' . $motherSurnOption)); ?>">
			<select name="fields[<?php echo $j; ?>]">
				<option value="FAMC:WIFE:NAME:SURN:EXACT" <?php if ($motherSurnOption == 'EXACT') echo 'selected'; ?>><?php echo I18N::translate('Exact'); ?></option>
				<option value="FAMC:WIFE:NAME:SURN:BEGINS" <?php if ($motherSurnOption == 'BEGINS') echo 'selected'; ?>><?php echo I18N::translate('Begins with'); ?></option>
				<option value="FAMC:WIFE:NAME:SURN:CONTAINS" <?php if ($motherSurnOption == 'CONTAINS') 'selected'; ?>><?php echo I18N::translate('Contains'); ?></option>
				<option value="FAMC:WIFE:NAME:SURN:SDX" <?php if ($motherSurnOption == 'SDX') echo 'selected'; ?>><?php echo I18N::translate('Sounds like'); ?></option>
			</select>
		</td>
		<?php $j++; ?>
	</tr>
		<tr><td colspan="2">
			<div class="center" style="margin-top:10px;">
			<a href="#" onclick="addFields();"><?php echo I18N::translate('Add more fields'); ?></a>
			</div>
		</td></tr>
	<?php $j++; ?>
	<!--/tr-->
</table>
</div>
		<div class="center" style="margin-top:10px;">
			<p><input type="submit" value="<?php echo I18N::translate('Search'); ?>"></p>
		</div>
</form>
</div>

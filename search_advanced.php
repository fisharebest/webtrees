<?php

/**
 * Searches based on user query.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009	PGV Development Team. All rights reserved.
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

define('WT_SCRIPT_NAME', 'search_advanced.php');
require './includes/session.php';
require WT_ROOT.'includes/controllers/advancedsearch_ctrl.php';
require WT_ROOT.'includes/functions/functions_print_lists.php';

$controller=new AdvancedSearchController();
$controller->init();

// Print the top header
print_header(i18n::translate('Advanced Search'));
?>
<script language="JavaScript" type="text/javascript">
<!--
	function checknames(frm) {
		action = "<?php print $controller->action ?>";

		return true;
	}

	var numfields = <?php print count($controller->fields); ?>;
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
		<?php foreach($controller->getOtherFields() as $field) { ?>
		opt = document.createElement('option');
		opt.value='<?php print $field; ?>';
		opt.text='<?php print $controller->getLabel($field); ?>';
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
		opt.text='<?php print i18n::translate('Exact'); ?>';
		sel.appendChild(opt);
		opt = document.createElement('option');
		opt.value='';
		opt.text='+/- 2 <?php print i18n::translate('years'); ?>';
		sel.appendChild(opt);
		opt = document.createElement('option');
		opt.value='5';
		opt.text='+/- 5 <?php print i18n::translate('years'); ?>';
		sel.appendChild(opt);
		opt = document.createElement('option');
		opt.value='10';
		opt.text='+/- 10 <?php print i18n::translate('years'); ?>';
		sel.appendChild(opt);
		var spc = document.createTextNode(' ');
		elm.appendChild(spc);
		elm.appendChild(sel);
	}
//-->
</script>

<h2 class="center"><?php print $controller->getPageTitle(); ?></h2>
<?php $somethingPrinted = $controller->PrintResults(); ?>
<!--	/*************************************************** Search Form Outer Table **************************************************/ -->
<form method="post" name="searchform" onsubmit="return checknames(this);" action="search_advanced.php">
<input type="hidden" name="action" value="<?php print $controller->action; ?>" />
<input type="hidden" name="isPostBack" value="true" />
<table id="field_table" class="list_table $TEXT_DIRECTION" width="35%" border="0">
	<tr>
		<td colspan="4" class="facts_label03" style="text-align:center; ">
			<?php echo i18n::translate('Advanced Search'), help_link('advanced_search'); ?>
		</td>
	</tr>
	<!-- // search terms -->
	<?php
	$fct = count($controller->fields);
	for($i=0; $i<$fct; $i++) {
		if (strpos($controller->getField($i), "FAMC:HUSB:NAME")===0) continue;
		if (strpos($controller->getField($i), "FAMC:WIFE:NAME")===0) continue;
	?>
	<tr>
		<td class="list_label">
			<?php print $controller->getLabel($controller->getField($i)); ?>
		</td>
		<td id="vcell<?php print $i; ?>" class="list_value">
			<?php
			$currentFieldSearch = $controller->getField($i);		// Get this field's name and the search criterion
			$currentField = substr($currentFieldSearch, 0, strrpos($currentFieldSearch, ':'));		// Get the actual field name
			?>
			<input tabindex="<?php print $i+1; ?>" type="text" id="value<?php print $i; ?>" name="values[<?php print $i; ?>]" value="<?php print $controller->getValue($i); ?>" />
			<?php if (preg_match("/^NAME:/", $currentFieldSearch)>0) {
				?>
				<select name="fields[<?php print $i ?>]">
					<option value="<?php print $currentField; ?>:EXACT"<?php if (preg_match("/:EXACT$/", $currentFieldSearch)>0) print " selected=\"selected\""; ?>><?php print i18n::translate('Exact'); ?></option>
					<option value="<?php print $currentField; ?>:BEGINS"<?php if (preg_match("/:BEGINS$/", $currentFieldSearch)>0) print " selected=\"selected\""; ?>><?php print i18n::translate('Begins with'); ?></option>
					<option value="<?php print $currentField; ?>:CONTAINS"<?php if (preg_match("/:CONTAINS$/", $currentFieldSearch)>0) print " selected=\"selected\""; ?>><?php print i18n::translate('Contains'); ?></option>
					<option value="<?php print $currentField; ?>:SDX"<?php if (preg_match("/:SDX$/", $currentFieldSearch)>0) print " selected=\"selected\""; ?>><?php print i18n::translate('Sounds like'); ?></option>
				</select>
			<?php } else { ?>
			<input type="hidden" name="fields[<?php print $i ?>]" value="<?php print $controller->getField($i); ?>" />
			<?php }
			if (preg_match("/:DATE$/", $currentFieldSearch)>0) {
				?>
				<select name="plusminus[<?php print $i ?>]">
					<option value=""><?php print i18n::translate('Exact'); ?></option>
					<option value="2" <?php if (!empty($controller->plusminus[$i]) && $controller->plusminus[$i]==2) print " selected=\"selected\""; ?>>+/- 2 <?php print i18n::translate('years'); ?></option>
					<option value="5" <?php if (!empty($controller->plusminus[$i]) && $controller->plusminus[$i]==5) print "selected=\"selected\""; ?>>+/- 5 <?php print i18n::translate('years'); ?></option>
					<option value="10" <?php if (!empty($controller->plusminus[$i]) && $controller->plusminus[$i]==10) print "selected=\"selected\""; ?>>+/- 10 <?php print i18n::translate('years'); ?></option>
				</select>
			<?php }?>
		</td>
		<?php
		//-- relative fields
		if ($i==0 && $fct>4) {
			$j=$fct;
			// Get the current options for Father's and Mother's name searches
			$fatherGivnOption = 'SDX';
			$fatherSurnOption = 'SDX';
			$motherGivnOption = 'SDX';
			$motherSurnOption = 'SDX';
			for($k=0; $k<$fct; $k++) {
				$searchField = $controller->getField($k);
				$searchOption = substr($searchField, 20);	// Assume we have something like "FAMC:HUSB:NAME:GIVN:foo"
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
			<td rowspan="100" class="list_value">&nbsp;</td>
			<td rowspan="100" class="list_value">
				<table>
					<!--  father -->
					<tr>
						<td colspan="2" class="facts_label03" style="text-align:center; ">
							<?php print i18n::translate('Father'); ?>
						</td>
					</tr>
					<tr>
						<td class="list_label">
							<?php echo i18n::translate('GIVN'); ?>
						</td>
						<td class="list_value">
							<input type="text" name="values[<?php print $j; ?>]" value="<?php print $controller->getValue($controller->getIndex('FAMC:HUSB:NAME:GIVN:'.$fatherGivnOption)); ?>" />
							<select name="fields[<?php print $j ?>]">
								<option value="FAMC:HUSB:NAME:GIVN:EXACT"<?php if ($fatherGivnOption == 'EXACT') print " selected=\"selected\""; ?>><?php print i18n::translate('Exact'); ?></option>
								<option value="FAMC:HUSB:NAME:GIVN:BEGINS"<?php if ($fatherGivnOption == 'BEGINS') print " selected=\"selected\""; ?>><?php print i18n::translate('Begins with'); ?></option>
								<option value="FAMC:HUSB:NAME:GIVN:CONTAINS"<?php if ($fatherGivnOption == 'CONTAINS') print " selected=\"selected\""; ?>><?php print i18n::translate('Contains'); ?></option>
								<option value="FAMC:HUSB:NAME:GIVN:SDX"<?php if ($fatherGivnOption == 'SDX') print " selected=\"selected\""; ?>><?php print i18n::translate('Sounds like'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<?php $j++; ?>
						<td class="list_label">
							<?php echo i18n::translate('SURN'); ?>
						</td>
						<td class="list_value">
							<input type="text" name="values[<?php print $j; ?>]" value="<?php print $controller->getValue($controller->getIndex('FAMC:HUSB:NAME:SURN:'.$fatherSurnOption)); ?>" />
							<select name="fields[<?php print $j ?>]">
								<option value="FAMC:HUSB:NAME:SURN:EXACT"<?php if ($fatherSurnOption == 'EXACT') print " selected=\"selected\""; ?>><?php print i18n::translate('Exact'); ?></option>
								<option value="FAMC:HUSB:NAME:SURN:BEGINS"<?php if ($fatherSurnOption == 'BEGINS') print " selected=\"selected\""; ?>><?php print i18n::translate('Begins with'); ?></option>
								<option value="FAMC:HUSB:NAME:SURN:CONTAINS"<?php if ($fatherSurnOption == 'CONTAINS') print " selected=\"selected\""; ?>><?php print i18n::translate('Contains'); ?></option>
								<option value="FAMC:HUSB:NAME:SURN:SDX"<?php if ($fatherSurnOption == 'SDX') print " selected=\"selected\""; ?>><?php print i18n::translate('Sounds like'); ?></option>
							</select>
						</td>
					</tr>
					<!--  mother -->
					<?php $j++; ?>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td colspan="2" class="facts_label03" style="text-align:center; ">
							<?php print i18n::translate('Mother'); ?>
						</td>
					</tr>
					<tr>
						<td class="list_label">
							<?php echo i18n::translate('GIVN'); ?>
						</td>
						<td class="list_value">
							<input type="text" name="values[<?php print $j; ?>]" value="<?php print $controller->getValue($controller->getIndex('FAMC:WIFE:NAME:GIVN:'.$motherGivnOption)); ?>" />
							<select name="fields[<?php print $j ?>]">
								<option value="FAMC:WIFE:NAME:GIVN:EXACT"<?php if ($motherGivnOption == 'EXACT') print " selected=\"selected\""; ?>><?php print i18n::translate('Exact'); ?></option>
								<option value="FAMC:WIFE:NAME:GIVN:BEGINS"<?php if ($motherGivnOption == 'BEGINS') print " selected=\"selected\""; ?>><?php print i18n::translate('Begins with'); ?></option>
								<option value="FAMC:WIFE:NAME:GIVN:CONTAINS"<?php if ($motherGivnOption == 'CONTAINS') print " selected=\"selected\""; ?>><?php print i18n::translate('Contains'); ?></option>
								<option value="FAMC:WIFE:NAME:GIVN:SDX"<?php if ($motherGivnOption == 'SDX') print " selected=\"selected\""; ?>><?php print i18n::translate('Sounds like'); ?></option>
							</select>
						</td>
						<?php $j++; ?>
					</tr>
					<tr>
						<td class="list_label">
							<?php echo i18n::translate('SURN'); ?>
						</td>
						<td class="list_value">
							<input type="text" name="values[<?php print $j; ?>]" value="<?php print $controller->getValue($controller->getIndex('FAMC:WIFE:NAME:SURN:'.$motherSurnOption)); ?>" />
							<select name="fields[<?php print $j ?>]">
								<option value="FAMC:WIFE:NAME:SURN:EXACT"<?php if ($motherSurnOption == 'EXACT') print " selected=\"selected\""; ?>><?php print i18n::translate('Exact'); ?></option>
								<option value="FAMC:WIFE:NAME:SURN:BEGINS"<?php if ($motherSurnOption == 'BEGINS') print " selected=\"selected\""; ?>><?php print i18n::translate('Begins with'); ?></option>
								<option value="FAMC:WIFE:NAME:SURN:CONTAINS"<?php if ($motherSurnOption == 'CONTAINS') print " selected=\"selected\""; ?>><?php print i18n::translate('Contains'); ?></option>
								<option value="FAMC:WIFE:NAME:SURN:SDX"<?php if ($motherSurnOption == 'SDX') print " selected=\"selected\""; ?>><?php print i18n::translate('Sounds like'); ?></option>
							</select>
						</td>
						<?php $j++; ?>
					</tr>
					<!-- spouse -->
					<!--tr-->
					<?php $j++; ?>
					<!--/tr-->
				</table>
			</td>
		<?php } ?>
	</tr>
	<?php } ?>
	<tr>
		<td class="list_value" style="vertical-align: middle; text-align: center; padding: 5px;"  colspan="10">
			<a href="#" onclick="addFields();"><?php print i18n::translate('Add More Fields'); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input tabindex="<?php print $i+1; ?>" type="submit" value="<?php print i18n::translate('Search'); ?>" />
		</td>
	</tr>
</table>
</form>
<br /><br /><br /><br />
<?php
// set the focus on the first field unless multisite or some search results have been printed
if (!$somethingPrinted ) {
?>
	<script language="JavaScript" type="text/javascript">
	<!--
		document.getElementById('value0').focus();
	//-->
	</script>
<?php
}
//-- somewhere the session gedcom gets changed, so we will change it back
$_SESSION['GEDCOM'] = $GEDCOM;
print_footer();
?>

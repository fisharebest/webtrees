<?php
// Change the blocks on "My page" and "Home page"
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

define('WT_SCRIPT_NAME', 'index_edit.php');
require './includes/session.php';

$controller=new WT_Controller_Simple();

$ctype=safe_REQUEST($_REQUEST, 'ctype', array('user', 'gedcom'));

if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];
if (isset($_REQUEST['main'])) $main = $_REQUEST['main'];
if (isset($_REQUEST['right'])) $right = $_REQUEST['right'];
if (isset($_REQUEST['setdefault'])) $setdefault = $_REQUEST['setdefault'];
if (isset($_REQUEST['side'])) $side = $_REQUEST['side'];
if (isset($_REQUEST['index'])) $index = $_REQUEST['index'];
if (isset($_REQUEST['name'])) $name = $_REQUEST['name'];

//-- make sure that they have user status before they can use this page
//-- otherwise have them login again
if (!WT_USER_ID || !$ctype) {
	$controller->pageHeader();
	$controller->addInlineJavaScript('opener.window.location.reload(); window.close();');
	exit;
}
if (!WT_USER_IS_ADMIN) $setdefault=false;

if (!isset($action)) $action='';
if (!isset($main)) $main=array();
if (!isset($right)) $right=array();
if (!isset($setdefault)) $setdefault=false;
if (!isset($side)) $side='main';
if (!isset($index)) $index=1;

$block_id=safe_REQUEST($_REQUEST, 'block_id');

// Define all the icons we're going to use
if($TEXT_DIRECTION=='ltr') {
	$IconUarrow = '<img src="'.$WT_IMAGES['uarrow'].'" width="20" height="20" alt="">';
	$IconDarrow = '<img src="'.$WT_IMAGES['darrow'].'" width="20" height="20" alt="">';
	$IconRarrow = '<img src="'.$WT_IMAGES['rarrow'].'" width="20" height="20" alt="">';
	$IconLarrow = '<img src="'.$WT_IMAGES['larrow'].'" width="20" height="20" alt="">';
	$IconRDarrow = '<img src="'.$WT_IMAGES['rdarrow'].'" width="20" height="20" alt="">';
	$IconLDarrow = '<img src="'.$WT_IMAGES['ldarrow'].'" width="20" height="20" alt="">';
} else {
	$IconUarrow = '<img src="'.$WT_IMAGES['uarrow'].'" width="20" height="20" alt="">';
	$IconDarrow = '<img src="'.$WT_IMAGES['darrow'].'" width="20" height="20" alt="">';
	$IconRarrow = '<img src="'.$WT_IMAGES['larrow'].'" width="20" height="20" alt="">';
	$IconLarrow = '<img src="'.$WT_IMAGES['rarrow'].'" width="20" height="20" alt="">';
	$IconRDarrow = '<img src="'.$WT_IMAGES['ldarrow'].'" width="20" height="20" alt="">';
	$IconLDarrow = '<img src="'.$WT_IMAGES['rdarrow'].'" width="20" height="20" alt="">';
}

$all_blocks=array();
foreach (WT_Module::getActiveBlocks() as $name=>$block) {
	if ($ctype=='user' && $block->isUserBlock() || $ctype=='gedcom' && $block->isGedcomBlock()) {
		$all_blocks[$name]=$block;
	}
}

//-- get the blocks list
if ($ctype=='user') {
	$controller->setPageTitle(WT_I18N::translate('My page'));
	if ($action=='reset') {
		WT_DB::prepare("DELETE `##block_setting` FROM `##block_setting` JOIN `##block` USING (block_id) WHERE user_id=?")->execute(array(WT_USER_ID));
		WT_DB::prepare("DELETE FROM `##block` WHERE user_id=?")->execute(array(WT_USER_ID));
	}
	$blocks=get_user_blocks(WT_USER_ID);
} else {
	$controller->setPageTitle(WT_I18N::translate(get_gedcom_setting(WT_GED_ID, 'title')));
	if ($action=='reset') {
		WT_DB::prepare("DELETE `##block_setting` FROM `##block_setting` JOIN `##block` USING (block_id) WHERE gedcom_id=?")->execute(array(WT_GED_ID));
		WT_DB::prepare("DELETE FROM `##block` WHERE gedcom_id=?")->execute(array(WT_GED_ID));
	}
	$blocks=get_gedcom_blocks(WT_GED_ID);
}

$controller->pageHeader();

if ($action=='update') {
	foreach (array('main', 'side') as $location) {
		if ($location=='main') {
			$new_blocks=$main;
		} else {
			$new_blocks=$right;
		}
		foreach ($new_blocks as $order=>$block_name) {
			if (is_numeric($block_name)) {
				// existing block
				WT_DB::prepare("UPDATE `##block` SET block_order=? WHERE block_id=?")->execute(array($order, $block_name));
				// existing block moved location
				WT_DB::prepare("UPDATE `##block` SET location=? WHERE block_id=?")->execute(array($location, $block_name));
			} else {
				// new block
				if ($ctype=='user') {
					WT_DB::prepare("INSERT INTO `##block` (user_id, location, block_order, module_name) VALUES (?, ?, ?, ?)")->execute(array(WT_USER_ID, $location, $order, $block_name));
				} else {
					WT_DB::prepare("INSERT INTO `##block` (gedcom_id, location, block_order, module_name) VALUES (?, ?, ?, ?)")->execute(array(WT_GED_ID, $location, $order, $block_name));
				}
			}
		}
		// deleted blocks
		foreach ($blocks[$location] as $block_id=>$block_name) {
			if (!in_array($block_id, $main) && !in_array($block_id, $right)) {
				WT_DB::prepare("DELETE FROM `##block_setting` WHERE block_id=?")->execute(array($block_id));
				WT_DB::prepare("DELETE FROM `##block`         WHERE block_id=?")->execute(array($block_id));
			}
		}
	}
	$controller->addInlineJavaScript('opener.window.location.reload(); window.close();');
	exit;
}

if ($action=="configure") {
	if (array_key_exists($block_id, $blocks['main'])) {
		$block_name=$blocks['main'][$block_id];
	} elseif (array_key_exists($block_id, $blocks['side'])) {
		$block_name=$blocks['side'][$block_id];
	} else {
		$controller->addInlineJavaScript('opener.window.location.reload(); window.close();');
		exit;
	}
	$class_name=$block_name.'_WT_Module';
	$block=new $class_name;
	echo '<table class="facts_table" width="99%">';
	echo '<tr><td class="facts_label">';
	echo '<h2>', WT_I18N::translate('Configure'), '</h2>';
	echo '</td></tr>';
	echo '<tr><td class="facts_label03">';
	echo '<b>', $block->getTitle(), ' - ', $block->getDescription(), '</b>';
	echo '</td></tr>';
	echo '</table>';
	echo '<form name="block" method="post" action="index_edit.php?action=configure&amp;ctype=', $ctype, '&amp;block_id=', $block_id, '">';
	echo '<input type="hidden" name="save" value="1">';
	echo '<table border="0" class="facts_table">';
	$block->configureBlock($block_id);
	echo '<tr><td colspan="2" class="topbottombar">';
	echo '<input type="button" value="', WT_I18N::translate('Save'), '" onclick="document.block.submit();">';
	echo '&nbsp;&nbsp;<input type ="button" value="', WT_I18N::translate('Cancel'), '" onclick="window.close()">';
	echo '</td></tr>';
	echo '</table>';
	echo '</form>';
	echo WT_JS_START;
	echo 'var pastefield; function paste_id(value) {pastefield.value=value;}';
	echo WT_JS_END;
} else { ?>
	<script type="text/javascript">
	<!--
/**
 * Move Up Block JavaScript function
 *
 * This function moves the selected option up in the given select list
 * @param String section_name the name of the select to move the options
 */
	function move_up_block(section_name) {
		section_select = document.getElementById(section_name);
		if (section_select) {
			if (section_select.selectedIndex <= 0) return false;
			index = section_select.selectedIndex;
			temp = new Option(section_select.options[index-1].text, section_select.options[index-1].value);
			section_select.options[index-1] = new Option(section_select.options[index].text, section_select.options[index].value);
			section_select.options[index] = temp;
			section_select.selectedIndex = index-1;
		}
	}

/**
 * Move Down Block JavaScript function
 *
 * This function moves the selected option down in the given select list
 * @param String section_name the name of the select to move the options
 */
	function move_down_block(section_name) {
		section_select = document.getElementById(section_name);
		if (section_select) {
			if (section_select.selectedIndex < 0) return false;
			if (section_select.selectedIndex >= section_select.length-1) return false;
			index = section_select.selectedIndex;
			temp = new Option(section_select.options[index+1].text, section_select.options[index+1].value);
			section_select.options[index+1] = new Option(section_select.options[index].text, section_select.options[index].value);
			section_select.options[index] = temp;
			section_select.selectedIndex = index+1;
		}
	}

/**
 * Move Block from one column to the other JavaScript function
 *
 * This function moves the selected option down in the given select list
 * @author KosherJava
 * @param String from_column the name of the select to move the option from
 * @param String to_column the name of the select to remove the option to
 */
	function move_left_right_block(from_column, to_column) {
		to_select = document.getElementById(to_column);
		from_select = document.getElementById(from_column);
		instruct = document.getElementById('instructions');
		if ((to_select) && (from_select)) {
			add_option = from_select.options[from_select.selectedIndex];
			if (to_column != 'available_select') {
				to_select.options[to_select.length] = new Option(add_option.text, add_option.value);
			}
			if (from_column != 'available_select') {
				from_select.options[from_select.selectedIndex] = null; //remove from list
			}
		}
	}
/**
 * Select Options JavaScript function
 *
 * This function selects all the options in the multiple select lists
 */
	function select_options() {
		section_select = document.getElementById('main_select');
		if (section_select) {
			for (i=0; i<section_select.length; i++) {
				section_select.options[i].selected=true;
			}
		}
		section_select = document.getElementById('right_select');
		if (section_select) {
			for (i=0; i<section_select.length; i++) {
				section_select.options[i].selected=true;
			}
		}
		return true;
	}

/**
 * Load Block Description array for use by javascript
 */
	<?php
	echo "var block_descr = new Array();";
	foreach ($all_blocks as $block_name=>$block) {
		echo "block_descr['$block_name'] = '".addslashes($block->getDescription())."';";
	}
	echo "block_descr['advice1'] = '".addslashes(WT_I18N::translate('Highlight a  block name and then click on one of the arrow icons to move that highlighted block in the indicated direction.'))."';";
	?>
/**
 * Show Block Description JavaScript function
 *
 * This function shows a description for the selected option
 * @param String list_name the name of the select to get the option from
 */
	function show_description(list_name) {
		list_select = document.getElementById(list_name);
		instruct = document.getElementById('instructions');
		if (block_descr[list_select.options[list_select.selectedIndex].value] && instruct) {
			instruct.innerHTML = block_descr[list_select.options[list_select.selectedIndex].value];
		} else {
			instruct.innerHTML = block_descr['advice1'];
		}
		list1 = document.getElementById('main_select');
		list2 = document.getElementById('available_select');
		list3 = document.getElementById('right_select');
		if (list_name=='main_select') {
			list2.selectedIndex = -1;
			list3.selectedIndex = -1;
		}
		if (list_name=='available_select') {
			list1.selectedIndex = -1;
			list3.selectedIndex = -1;
		}
		if (list_name=='right_select') {
			list1.selectedIndex = -1;
			list2.selectedIndex = -1;
		}
	}

	function save_form() {
		document.config_setup.submit();
	}
	//-->
	</script>
	<form name="config_setup" method="post" action="index_edit.php">
	<input type="hidden" name="ctype" value="<?php echo $ctype; ?>">
	<input type="hidden" name="action" value="update">
	<input type="hidden" name="name" value="<?php echo $name; ?>">
	<table border="1" width="400px">
	<tr><td class="topbottombar" colspan="7">
	<?php
	echo '<b>', WT_I18N::translate('Change the blocks on this page'), '</b>';
	echo '</td></tr>';
	// NOTE: Row 1: Column legends
	echo '<tr>';
		echo '<td class="descriptionbox center vmiddle" colspan="2">';
			echo '<b>', WT_I18N::translate('Main Section Blocks'), '</b>';
		echo '</td>';
		echo '<td class="descriptionbox center vmiddle" colspan="3">';
			echo '<b>', WT_I18N::translate('Available Blocks'), '</b>';
		echo '</td>';
		echo '<td class="descriptionbox center vmiddle" colspan="2">';
			echo '<b>', WT_I18N::translate('Right Section Blocks'), '</b>';
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
	// NOTE: Row 2 column 1: Up/Down buttons for left (main) block list
	echo '<td class="optionbox width20px center vmiddle">';
		echo '<a onclick="move_up_block(\'main_select\');" title="', WT_I18N::translate('Move up'), '">', $IconUarrow, '</a>';
		echo '<br>';
		echo '<a onclick="move_down_block(\'main_select\');" title="', WT_I18N::translate('Move down'), '">', $IconDarrow, '</a>';
		echo '<br><br>';
		echo help_link('block_move_up');
	echo '</td>';
	// NOTE: Row 2 column 2: Left (Main) block list
	echo '<td class="optionbox">';
		echo '<select multiple="multiple" id="main_select" name="main[]" size="10" onchange="show_description(\'main_select\');">';
		foreach ($blocks['main'] as $block_id=>$block_name) {
			echo '<option value="', $block_id, '">', $all_blocks[$block_name]->getTitle(), '</option>';
		}
		echo '</select>';
	echo '</td>';
	// NOTE: Row 2 column 3: Left/Right buttons for left (main) block list
	echo '<td class="optionbox width20 vmiddle">';
		echo '<a onclick="move_left_right_block(\'main_select\', \'right_select\');" title="', WT_I18N::translate('Move Right'), '">', $IconRDarrow, '</a>';
		echo '<br>';
		echo '<a onclick="move_left_right_block(\'main_select\', \'available_select\');" title="', WT_I18N::translate('Remove'), '">', $IconRarrow, '</a>';
		echo '<br>';
		echo '<a onclick="move_left_right_block(\'available_select\', \'main_select\');" title="', WT_I18N::translate('Add'), '">', $IconLarrow, '</a>';
		echo '<br><br>';
		echo help_link('block_move_right');
	echo '</td>';
	// Row 2 column 4: Middle (Available) block list
	echo '<td class="optionbox">';
		echo '<select id="available_select" name="available[]" size="10" onchange="show_description(\'available_select\');">';
		foreach ($all_blocks as $block_name=>$block) {
			echo '<option value="', $block_name, '">', $block->getTitle(), '</option>';
		}
		echo '</select>';
	echo '</td>';
	// NOTE: Row 2 column 5: Left/Right buttons for right block list
	echo '<td class="optionbox width20 vmiddle">';
		echo '<a onclick="move_left_right_block(\'right_select\', \'main_select\');" title="', WT_I18N::translate('Move Left'), '">', $IconLDarrow, '</a>';
		echo '<br>';
		echo '<a onclick="move_left_right_block(\'right_select\', \'available_select\');" title="', WT_I18N::translate('Remove'), '">', $IconLarrow, '</a>';
		echo '<br>';
		echo '<a onclick="move_left_right_block(\'available_select\', \'right_select\');" title="', WT_I18N::translate('Add'), '">', $IconRarrow, '</a>';
		echo '<br><br>';
		echo help_link('block_move_right');
	echo '</td>';
	// NOTE: Row 2 column 6: Right block list
	echo '<td class="optionbox">';
		echo '<select multiple="multiple" id="right_select" name="right[]" size="10" onchange="show_description(\'right_select\');">';
		foreach ($blocks['side'] as $block_id=>$block_name) {
			echo '<option value="', $block_id, '">', $all_blocks[$block_name]->getTitle(), '</option>';
		}
		echo '</select>';
	echo '</td>';
	// NOTE: Row 2 column 7: Up/Down buttons for right block list
	echo '<td class="optionbox width20 vmiddle">';
		echo '<a onclick="move_up_block(\'right_select\');" title="', WT_I18N::translate('Move up'), '">', $IconUarrow, '</a>';
		echo '<br>';
		echo '<a onclick="move_down_block(\'right_select\');" title="', WT_I18N::translate('Move down'), '">', $IconDarrow. '</a>';
		echo '<br><br>';
		echo help_link('block_move_up');
	echo '</td>';
	echo '</tr>';
	// NOTE: Row 3 columns 1-7: Summary description of currently selected block
	echo '<tr><td class="descriptionbox wrap" colspan="7"><div id="instructions">';
	echo WT_I18N::translate('Highlight a  block name and then click on one of the arrow icons to move that highlighted block in the indicated direction.');
	echo '</div></td></tr>';
	echo '<tr><td class="topbottombar" colspan="7">';

	/* This section temporarily removed as it does not work (kiwi - 15/08/2011)
	if (WT_USER_IS_ADMIN && $ctype=='user') {
		echo WT_I18N::translate('Use these blocks as the default block configuration for all users?'), '<input type="checkbox" name="setdefault" value="1"><br><br>';
	}*/
	echo '<input type="button" value="', WT_I18N::translate('Reset to Default Blocks'), '" onclick="window.location=\'index_edit.php?ctype=', $ctype, '&amp;action=reset&amp;name=', addslashes($name), '\';">';
	echo '&nbsp;&nbsp;';
	echo '<input type="button" value="', WT_I18N::translate('Save'), '" onclick="select_options(); save_form();">';
	echo '&nbsp;&nbsp;';
	echo '<input type ="button" value="', WT_I18N::translate('Cancel'), '" onclick="opener.window.location.reload(); window.close();">';
	echo '</td></tr></table>';
	echo '</form>';
}

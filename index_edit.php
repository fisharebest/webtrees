<?php
/**
 * My Page page allows a logged in user the abilty
 * to keep bookmarks, see a list of upcoming events, etc.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * This Page Is Valid XHTML 1.0 Transitional! > 13 August 2005
 *
 * @package webtrees
 * @subpackage Display
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'index_edit.php');
require './includes/session.php';
require_once WT_ROOT.'includes/index_cache.php';

$ctype=safe_REQUEST($_REQUEST, 'ctype', array('user', 'gedcom'));

if (!$ctype) {
	die("Internal error - missing ctype parameter");
}

if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];
if (isset($_REQUEST['main'])) $main = $_REQUEST['main'];
if (isset($_REQUEST['right'])) $right = $_REQUEST['right'];
if (isset($_REQUEST['setdefault'])) $setdefault = $_REQUEST['setdefault'];
if (isset($_REQUEST['side'])) $side = $_REQUEST['side'];
if (isset($_REQUEST['index'])) $index = $_REQUEST['index'];
if (isset($_REQUEST['name'])) $name = $_REQUEST['name'];

//-- make sure that they have user status before they can use this page
//-- otherwise have them login again
if (!WT_USER_ID) {
	print_simple_header('');
	echo i18n::translate('<b>Access Denied</b><br />You do not have access to this resource.');
	echo '<div class="center"><a href="javascript:;" onclick="self.close();">', i18n::translate('Close Window').'</a></div>';
	print_simple_footer();
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
$IconUarrow = "<img src=\"".$WT_IMAGE_DIR.'/'.$WT_IMAGES['uarrow']['other']."\" width=\"20\" height=\"20\" alt=\"\" />";
$IconDarrow = "<img src=\"".$WT_IMAGE_DIR.'/'.$WT_IMAGES['darrow']['other']."\" width=\"20\" height=\"20\" alt=\"\" />";
$IconRarrow = "<img src=\"".$WT_IMAGE_DIR.'/'.$WT_IMAGES['rarrow']['other']."\" width=\"20\" height=\"20\" alt=\"\" />";
$IconLarrow = "<img src=\"".$WT_IMAGE_DIR.'/'.$WT_IMAGES['larrow']['other']."\" width=\"20\" height=\"20\" alt=\"\" />";
$IconRDarrow = "<img src=\"".$WT_IMAGE_DIR.'/'.$WT_IMAGES['rdarrow']['other']."\" width=\"20\" height=\"20\" alt=\"\" />";
$IconLDarrow = "<img src=\"".$WT_IMAGE_DIR.'/'.$WT_IMAGES['ldarrow']['other']."\" width=\"20\" height=\"20\" alt=\"\" />";

$all_blocks=array();
foreach (WT_Module::getActiveBlocks() as $name=>$block) {
	if ($ctype=='user' && $block->isUserBlock() || $ctype=='gedcom' && $block->isGedcomBlock()) {
		$all_blocks[$name]=$block;
	}
}

//-- get the blocks list
if ($ctype=='user') {
	if ($action=='reset') {
		WT_DB::prepare("DELETE {$TBLPREFIX}block_setting FROM {$TBLPREFIX}block_setting JOIN {$TBLPREFIX}block USING (block_id) WHERE user_id=?")->execute(array(WT_USER_ID));
		WT_DB::prepare("DELETE FROM {$TBLPREFIX}block WHERE user_id=?")->execute(array(WT_USER_ID));
	}
	$blocks=get_user_blocks(WT_USER_ID);
} else {
	if ($action=='reset') {
		WT_DB::prepare("DELETE {$TBLPREFIX}block_setting FROM {$TBLPREFIX}block_setting JOIN {$TBLPREFIX}block USING (block_id) WHERE gedcom_id=?")->execute(array(WT_GED_ID));
	}
	$blocks=get_gedcom_blocks(WT_GED_ID);
}

if ($ctype=='user') {
	print_simple_header(i18n::translate('My Page'));
} else {
	print_simple_header(get_gedcom_setting(WT_GED_ID, 'title'));
}

if ($action=='update') {
	foreach (array('main', 'side') as $location) {
		if ($location=='main') {
			$new_blocks=$main;
		} else {
			$new_blocks=$right;
		}
		// Deleted blocks
		foreach ($blocks[$location] as $block_id=>$block_name) {
			if (!in_array($block_id, $new_blocks)) {
				WT_DB::prepare("DELETE FROM {$TBLPREFIX}block_setting WHERE block_id=?")->execute(array($block_id));
				WT_DB::prepare("DELETE FROM {$TBLPREFIX}block         WHERE block_id=?")->execute(array($block_id));
			}
		}
		foreach ($new_blocks as $order=>$block_name) {
			if (is_numeric($block_name)) {
				// existing block
				WT_DB::prepare("UPDATE {$TBLPREFIX}block SET block_order=? WHERE block_id=?")->execute(array($order, $block_name));
			} else {
				// new block
				if ($ctype=='user') {
					WT_DB::prepare("INSERT INTO {$TBLPREFIX}block (user_id, location, block_order, module_name) VALUES (?, ?, ?, ?)")->execute(array(WT_USER_ID, $location, $order, $block_name));
				} else {
					WT_DB::prepare("INSERT INTO {$TBLPREFIX}block (gedcom_id, location, block_order, module_name) VALUES (?, ?, ?, ?)")->execute(array(WT_GED_ID, $location, $order, $block_name));
				}
			}
		}
	}
	if (isset($_POST['nextaction'])) $action = $_POST['nextaction'];
	echo WT_JS_START, 'opener.window.location.reload(); window.close();', WT_JS_END;
	exit;
}

if ($action=="clearcache") {
	clearCache();
	echo "<span class=\"warning\">".i18n::translate('The cache files have been removed.')."</span><br /><br />";
}

//var_dump($blocks);die("eek");
if ($action=="configure") {
	if (array_key_exists($block_id, $blocks['main'])) {
		$block_name=$blocks['main'][$block_id];
	} elseif (array_key_exists($block_id, $blocks['side'])) {
		$block_name=$blocks['side'][$block_id];
	} else {
		echo WT_JS_START, 'window.close();', WT_JS_END;
		exit;
	}
	$class_name=$block_name.'_WT_Module';
	$block=new $class_name;
	echo "<table class=\"facts_table ".$TEXT_DIRECTION."\" width=\"99%\">";
	echo "<tr><td class=\"facts_label\">";
	echo "<h2>".i18n::translate('Configure')."</h2>";
	echo "</td></tr>";
	echo "<tr><td class=\"facts_label03\">";
	echo "<b>".$block->getTitle()."</b>";
	echo "</td></tr>";
	echo "</table>";
?>
<script language="JavaScript" type="text/javascript">
<!--
	var pastefield;
	function paste_id(value) {
		pastefield.value=value;
	}
//-->
</script>
<?php
	echo "\n<form name=\"block\" method=\"post\" action=\"index_edit.php?action=configure&amp;ctype={$ctype}&amp;block_id=", $block_id, "\">\n";
	echo "<input type=\"hidden\" name=\"save\" value=\"1\" />\n";
	echo "<table border=\"0\" class=\"facts_table ".$TEXT_DIRECTION."\">";
	$block->configureBlock($block_id);
	echo "<tr><td colspan=\"2\" class=\"topbottombar\">";
	echo "<input type=\"button\" value=\"".i18n::translate('Save')."\" onclick=\"document.block.submit();\" />";
	echo "&nbsp;&nbsp;<input type =\"button\" value=\"".i18n::translate('Cancel')."\" onclick=\"window.close()\" />";
	echo "</td></tr>";
	echo "</table>";
	echo "</form>";
} else {
	?>
	<script language="JavaScript" type="text/javascript">
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
 * @param String add_to_column the name of the select to move the option to
 * @param String remove_from_column the name of the select to remove the option from
 */
	function move_left_right_block(add_to_column, remove_from_column) {
		section_select = document.getElementById(remove_from_column);
		add_select = document.getElementById(add_to_column);
		instruct = document.getElementById('instructions');
		if ((section_select) && (add_select)) {
			add_option = add_select.options[add_select.selectedIndex];
			if (remove_from_column != 'available_select') {
				section_select.options[section_select.length] = new Option(add_option.text, add_option.value);
			}
			if (add_to_column != 'available_select') {
				add_select.options[add_select.selectedIndex] = null; //remove from list
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
			for(i=0; i<section_select.length; i++) {
				section_select.options[i].selected=true;
			}
		}
		section_select = document.getElementById('right_select');
		if (section_select) {
			for(i=0; i<section_select.length; i++) {
				section_select.options[i].selected=true;
			}
		}
		return true;
	}

/**
 * Load Block Description array for use by jscript
 */
	<?php
	echo "var block_descr = new Array();\n";
	foreach ($all_blocks as $block_name=>$block) {
		echo "block_descr['$block_name'] = '".str_replace("'", "\\'", $block->getDescription())."';\n";
	}
	echo "block_descr['advice1'] = '".str_replace("'", "\\'", i18n::translate('Highlight a  block name and then click on one of the arrow icons to move that highlighted block in the indicated direction.'))."';\n";
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
		if (list_select && instruct) {
			instruct.innerHTML = block_descr[list_select.options[list_select.selectedIndex].value];
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

	function save_form(){
		document.config_setup.submit();
	}
	//-->
	</script>
	<?php
	//--------------------------------Start 1st tab Configuration page
	?>
	<div id="configure" class="tab_page center" style="position: absolute; display: block; top: auto; left: auto; z-index: 1; ">
	<br />
	<form name="config_setup" method="post" action="index_edit.php">
	<input type="hidden" name="ctype" value="<?php echo $ctype;?>" />
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="name" value="<?php echo $name;?>" />
	<table dir="ltr" border="1" width="400px">
	<tr><td class="topbottombar" colspan="7">
	<?php
	if ($ctype=="user") echo "<b>".i18n::translate('Customize My Page')."</b>";
	else echo "<b>".i18n::translate('Customize this GEDCOM Home Page')."</b>";
	echo help_link('portal_config_intructions');
	echo "</td></tr>";
	// NOTE: Row 1: Column legends
	echo "<tr>";
		echo "<td class=\"descriptionbox center vmiddle\" colspan=\"2\">\n";
			echo "<b>".i18n::translate('Main Section Blocks')."</b>";
		echo "</td>\n";
		echo "<td class=\"descriptionbox center vmiddle\" colspan=\"3\">";
			echo "<b>".i18n::translate('Available Blocks')."</b>";
		echo "</td>\n";
		echo "<td class=\"descriptionbox center vmiddle\" colspan=\"2\">";
			echo "<b>".i18n::translate('Right Section Blocks')."</b>";
		echo "</td>";
	echo "</tr>\n";
	echo "<tr>";
	// NOTE: Row 2 column 1: Up/Down buttons for left (main) block list
	echo "<td class=\"optionbox width20px center vmiddle\">";
		echo "<a tabindex=\"-1\" onclick=\"move_up_block('main_select');\" title=\"".i18n::translate('Move Up')."\">".$IconUarrow."</a>";
		echo "<br />";
		echo "<a tabindex=\"-1\" onclick=\"move_down_block('main_select');\" title=\"".i18n::translate('Move Down')."\">".$IconDarrow."</a>";
		echo "<br /><br />";
		echo help_link('block_move_up');

	echo "</td>";
	// NOTE: Row 2 column 2: Left (Main) block list
	echo "<td class=\"optionbox\" dir=\"".$TEXT_DIRECTION."\">\n";
		echo "<select multiple=\"multiple\" id=\"main_select\" name=\"main[]\" size=\"10\" onchange=\"show_description('main_select');\">\n";
		foreach($blocks['main'] as $block_id=>$block_name) {
			echo "<option value=\"$block_id\">".$all_blocks[$block_name]->getTitle()."</option>\n";
		}
		echo "</select>\n";
	echo "</td>";
	// NOTE: Row 2 column 3: Left/Right buttons for left (main) block list
	echo "<td class=\"optionbox width20 vmiddle\">";
		echo "<a tabindex=\"-1\" onclick=\"move_left_right_block('main_select', 'right_select');\" title=\"".i18n::translate('Move Right')."\">".$IconRDarrow."</a>";
		echo "<br />";
		echo "<a tabindex=\"-1\" onclick=\"move_left_right_block('main_select', 'available_select');\" title=\"".i18n::translate('Remove')."\">".$IconRarrow."</a>";
		echo "<br />";
		echo "<a tabindex=\"-1\" onclick=\"move_left_right_block('available_select', 'main_select');\" title=\"".i18n::translate('Add')."\">".$IconLarrow."</a>";
		echo "<br /><br />";
		echo help_link('block_move_right');

	echo "</td>";
	// Row 2 column 4: Middle (Available) block list
	echo "<td class=\"optionbox\" dir=\"".$TEXT_DIRECTION."\">";
		echo "<select id=\"available_select\" name=\"available[]\" size=\"10\" onchange=\"show_description('available_select');\">\n";
		foreach($all_blocks as $block_name=>$block) {
			echo "<option value=\"$block_name\">".$block->getTitle()."</option>\n";
		}
		echo "</select>\n";
	echo "</td>";
	// NOTE: Row 2 column 5: Left/Right buttons for right block list
	echo "<td class=\"optionbox width20 vmiddle\">";
		echo "<a tabindex=\"-1\" onclick=\"move_left_right_block('right_select', 'main_select');\" title=\"".i18n::translate('Move Left')."\">".$IconLDarrow."</a>";
		echo "<br />";
		echo "<a tabindex=\"-1\" onclick=\"move_left_right_block('right_select', 'available_select');\" title=\"".i18n::translate('Remove')."\">".$IconLarrow."</a>";
		echo "<br />";
		echo "<a tabindex=\"-1\" onclick=\"move_left_right_block('available_select', 'right_select');\" title=\"".i18n::translate('Add')."\">".$IconRarrow."</a>";
		echo "<br /><br />";
		echo help_link('block_move_right');
	echo "</td>";
	// NOTE: Row 2 column 6: Right block list
	echo "<td class=\"optionbox\" dir=\"".$TEXT_DIRECTION."\">";
		echo "<select multiple=\"multiple\" id=\"right_select\" name=\"right[]\" size=\"10\" onchange=\"show_description('right_select');\">\n";
		foreach($blocks['side'] as $block_id=>$block_name) {
			echo "<option value=\"$block_id\">".$all_blocks[$block_name]->getTitle()."</option>\n";
		}
		echo "</select>\n";
	echo "</td>";
	// NOTE: Row 2 column 7: Up/Down buttons for right block list
	echo "<td class=\"optionbox width20 vmiddle\">";
		echo "<a tabindex=\"-1\" onclick=\"move_up_block('right_select');\" title=\"".i18n::translate('Move Up')."\">".$IconUarrow."</a>";
		echo "<br />";
		echo "<a tabindex=\"-1\" onclick=\"move_down_block('right_select');\" title=\"".i18n::translate('Move Down')."\">".$IconDarrow."</a>";
		echo "<br /><br />";
		echo help_link('block_move_up');
	echo "</td>";
	echo "</tr>";
	// NOTE: Row 3 columns 1-7: Summary description of currently selected block
	echo "<tr><td class=\"descriptionbox wrap\" colspan=\"7\" dir=\"".$TEXT_DIRECTION."\"><div id=\"instructions\">";
	echo i18n::translate('Highlight a  block name and then click on one of the arrow icons to move that highlighted block in the indicated direction.');
	echo "</div></td></tr>";
	echo "<tr><td class=\"topbottombar\" colspan=\"7\">";

	if (WT_USER_IS_ADMIN && $ctype=='user') {
		echo i18n::translate('Use these blocks as the default block configuration for all users?')."<input type=\"checkbox\" name=\"setdefault\" value=\"1\" /><br /><br />\n";
	}

	echo "<input type=\"button\" value=\"".i18n::translate('Reset to Default Blocks')."\" onclick=\"window.location='index_edit.php?ctype=$ctype&amp;action=reset&amp;name=".str_replace("'", "\'", $name)."';\" />\n";
	if ($ctype=='user') {
		echo help_link('block_default_portal');
	} else {
		echo help_link('block_default_index');
	}
	if (WT_USER_GEDCOM_ADMIN && $ctype!="user") {
		echo "<input type =\"button\" value=\"".i18n::translate('Clear cache files')."\" onclick=\"window.location='index_edit.php?ctype=$ctype&amp;action=clearcache&amp;name=".str_replace("'", "\'", $name)."';\" />";
		echo help_link('clear_cache');
	}
	echo "&nbsp;&nbsp;";
	echo "<input type=\"button\" value=\"".i18n::translate('Save')."\" onclick=\"select_options(); save_form();\" />\n";
	echo "&nbsp;&nbsp;";
	echo "<input type =\"button\" value=\"".i18n::translate('Cancel')."\" onclick=\"window.close()\" />";
	echo "</td></tr></table>";
	echo "</form>\n";

	// end of 1st tab
	echo "</div>\n";

	//--------------------------------Start 2nd tab Help page
	echo "\n\t<div id=\"help\" class=\"tab_page\" style=\"position: absolute; display: none; top: auto; left: auto; z-index: 2; \">\n\t";

	echo "<br /><center><input type=\"button\" value=\"".i18n::translate('Click here to continue')."\" onclick=\"expand_layer('configure', true); expand_layer('help', false);\" /></center><br /><br />\n";
	echo i18n::translate("Here is a short description of each of the blocks you can place on the Welcome or My Page.<br /><table border='1' align='center'><tr><td class='list_value'><b>Name</b></td><td class='list_value'><b>Description</b></td></tr></table>");

	// end of 2nd tab
	echo "</div>\n";
}

echo "</body></html>";		// Yes! Absolutely NOTHING at page bottom, please.

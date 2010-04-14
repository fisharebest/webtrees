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

if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];
if (isset($_REQUEST['ctype'])) $ctype = $_REQUEST['ctype'];
if (isset($_REQUEST['main'])) $main = $_REQUEST['main'];
if (isset($_REQUEST['right'])) $right = $_REQUEST['right'];
if (isset($_REQUEST['setdefault'])) $setdefault = $_REQUEST['setdefault'];
if (isset($_REQUEST['side'])) $side = $_REQUEST['side'];
if (isset($_REQUEST['index'])) $index = $_REQUEST['index'];
if (isset($_REQUEST['name'])) $name = $_REQUEST['name'];

//-- make sure that they have user status before they can use this page
//-- otherwise have them login again
if (!WT_USER_ID) {
	print_simple_header("");
	print i18n::translate('<b>Access Denied</b><br />You do not have access to this resource.');
	print "<div class=\"center\"><a href=\"javascript:;\" onclick=\"self.close();\">".i18n::translate('Close Window')."</a></div>\n";
	print_simple_footer();
	exit;
}
if (!WT_USER_IS_ADMIN) $setdefault=false;

if (!isset($action)) $action="";
if (!isset($ctype)) $ctype="user";
if (!isset($main)) $main=array();
if (!isset($right)) $right=array();
if (!isset($setdefault)) $setdefault=false;
if (!isset($side)) $side="main";
if (!isset($index)) $index=1;

// Define all the icons we're going to use
$IconUarrow = "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["uarrow"]["other"]."\" width=\"20\" height=\"20\" alt=\"\" />";
$IconDarrow = "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["darrow"]["other"]."\" width=\"20\" height=\"20\" alt=\"\" />";
$IconRarrow = "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["rarrow"]["other"]."\" width=\"20\" height=\"20\" alt=\"\" />";
$IconLarrow = "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["larrow"]["other"]."\" width=\"20\" height=\"20\" alt=\"\" />";
$IconRDarrow = "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["rdarrow"]["other"]."\" width=\"20\" height=\"20\" alt=\"\" />";
$IconLDarrow = "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["ldarrow"]["other"]."\" width=\"20\" height=\"20\" alt=\"\" />";

/**
 * Load List of Blocks in blocks directory (unchanged)
 */
$WT_BLOCKS = array();
$d = dir("blocks");
while (false !== ($entry = $d->read())) {
	if (preg_match("/\.php$/", $entry)>0) {
		require_once WT_ROOT.'blocks/'.$entry;
	}
}
$d->close();
/**
 * End loading list of Blocks in blocks directory
 *
 * Load List of Blocks in modules/XX/blocks directories
 */
if (file_exists(WT_ROOT.'modules')) {
	$dir=dir(WT_ROOT.'modules');
	while (false !== ($entry = $dir->read())) {
		if (!strstr($entry,".") && ($entry!="..") && ($entry!="CVS")&& !strstr($entry, "svn")) {
			$path = WT_ROOT.'modules/' . $entry.'/blocks';
			if (is_readable($path)) {
				$d=dir($path);
				while (false !== ($entry = $d->read())) {
				if (($entry!=".") && ($entry!="..") && ($entry!="CVS")&& !strstr($entry, "svn")&&(preg_match("/\.php$/", $entry)>0)) {
						$p=$path.'/'.$entry;
						require_once $p;
					}
				}
			}
		}
	}
}
/**
 * End loading list of Blocks in modules/XX/blocks directories
*/

//	Build sorted table of block names, BUT:
//		include in this table ONLY if the block is appropriate for this page
//		If $BLOCK["type"] is	"both", include in both page types
//					"user", include in My Page only
//					"gedcom", include in Index page only
$SortedBlocks = array();
foreach($WT_BLOCKS as $key => $BLOCK) {
	if ($BLOCK['type']=='both' || $BLOCK['type']==$ctype) {
		$SortedBlocks[$key] = $BLOCK['name'];
	}
}
asort($SortedBlocks);

//-- get the blocks list
if ($ctype=="user") {
	$ublocks = getBlocks(WT_USER_NAME);
	if (($action=="reset") || ((count($ublocks["main"])==0) && (count($ublocks["right"])==0))) {
		$ublocks["main"] = array();
		$ublocks["main"][] = array("print_todays_events", "");
		$ublocks["main"][] = array("print_user_messages", "");
		$ublocks["main"][] = array("print_user_favorites", "");

		$ublocks["right"] = array();
		$ublocks["right"][] = array("print_welcome_block", "");
		$ublocks["right"][] = array("print_random_media", "");
		$ublocks["right"][] = array("print_upcoming_events", "");
		$ublocks["right"][] = array("print_logged_in_users", "");
	}
} else {
	$ublocks = getBlocks($GEDCOM);
	if (($action=="reset") or ((count($ublocks["main"])==0) and (count($ublocks["right"])==0))) {
		$ublocks["main"] = array();
		$ublocks["main"][] = array("print_gedcom_stats", "");
		$ublocks["main"][] = array("print_gedcom_news", "");
		$ublocks["main"][] = array("print_gedcom_favorites", "");
		$ublocks["main"][] = array("review_changes_block", "");

		$ublocks["right"] = array();
		$ublocks["right"][] = array("print_gedcom_block", "");
		$ublocks["right"][] = array("print_random_media", "");
		$ublocks["right"][] = array("print_todays_events", "");
		$ublocks["right"][] = array("print_logged_in_users", "");
	}
}

if ($ctype=="user") {
	print_simple_header(i18n::translate('My Page'));
} else {
	print_simple_header(get_gedcom_setting(WT_GED_ID, 'title'));
}

$GEDCOM_TITLE=PrintReady(get_gedcom_setting(WT_GED_ID, 'title'));

?>
<script language="JavaScript" type="text/javascript">
<!--
function parentrefresh() {
	opener.window.location.reload();
	window.close();
}
//-->
</script>
<?php
if ($action=="updateconfig") {
	$block = $ublocks[$side][$index];
	if ($WT_BLOCKS[$block[0]]["canconfig"] && is_array($WT_BLOCKS[$block[0]]["config"])) {
		$config = $block[1];
		foreach($WT_BLOCKS[$block[0]]["config"] as $config_name=>$config_value) {
			if (isset($_POST[$config_name])) {
				$config[$config_name] = $_POST[$config_name];
			} else {
				$config[$config_name] = "";
			}
		}
		$ublocks[$side][$index][1] = $config;
		setBlocks($name, $ublocks, $setdefault);
	}
	print i18n::translate('Configuration file updated successfully.')."<br />\n";
	if (isset($_POST["nextaction"])) $action = $_POST["nextaction"];
	if ($ctype!="user") $_SESSION['clearcache'] = true;
}

if ($action=="update") {
	$newublocks["main"] = array();
	if (is_array($main)) {
		foreach($main as $indexval => $b) {
			$config = "";
			$index = "";
			reset($ublocks["main"]);
			foreach($ublocks["main"] as $index=>$block) {
				if ($block[0]==$b) {
					$config = $block[1];
					break;
				}
			}
			if ($index!="") unset($ublocks["main"][$index]);
			$newublocks["main"][] = array($b, $config);
		}
	}

	$newublocks["right"] = array();
	if (is_array($right)) {
		foreach($right as $indexval => $b) {
			$config = "";
			$index = "";
			reset($ublocks["right"]);
			foreach($ublocks["right"] as $index=>$block) {
				if ($block[0]==$b) {
					$config = $block[1];
					break;
				}
			}
			if ($index!="") unset($ublocks["right"][$index]);
			$newublocks["right"][] = array($b, $config);
		}
	}
	$ublocks = $newublocks;
	setBlocks($name, $ublocks, $setdefault);
	if (isset($_POST["nextaction"])) $action = $_POST["nextaction"];
	?><script language="JavaScript" type="text/javascript">parentrefresh();</script><?php
}

if ($action=="clearcache") {
	clearCache();
	print "<span class=\"warning\">".i18n::translate('The cache files have been removed.')."</span><br /><br />";
}

if ($action=="configure" && isset($ublocks[$side][$index])) {
	$block = $ublocks[$side][$index];
	print "<table class=\"facts_table ".$TEXT_DIRECTION."\" width=\"99%\">";
	print "<tr><td class=\"facts_label\">";
	print "<h2>".i18n::translate('Configure')."</h2>";
	print "</td></tr>";
	print "<tr><td class=\"facts_label03\">";
	print "<b>".$WT_BLOCKS[$block[0]]["name"]."</b>";
	print "</td></tr>";
	print "</table>";
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
	print "\n<form name=\"block\" method=\"post\" action=\"index_edit.php\">\n";
	print "<input type=\"hidden\" name=\"ctype\" value=\"$ctype\" />\n";
	print "<input type=\"hidden\" name=\"action\" value=\"updateconfig\" />\n";
	print "<input type=\"hidden\" name=\"name\" value=\"$name\" />\n";
	print "<input type=\"hidden\" name=\"nextaction\" value=\"configure\" />\n";
	print "<input type=\"hidden\" name=\"side\" value=\"$side\" />\n";
	print "<input type=\"hidden\" name=\"index\" value=\"$index\" />\n";
	print "<table border=\"0\" class=\"facts_table ".$TEXT_DIRECTION."\" width=\"99%\">";
	if ($WT_BLOCKS[$block[0]]["canconfig"]) {
		eval($block[0]."_config(\$block[1]);");
		print "<tr><td colspan=\"2\" class=\"topbottombar\">";
		print "<input type=\"button\" value=\"".i18n::translate('Click here to continue')."\" onclick=\"document.block.submit();\" />";
		print help_link('click_here');
		print "&nbsp;&nbsp;<input type =\"button\" value=\"".i18n::translate('Cancel')."\" onclick=\"window.close()\" />";
		print "</td></tr>";
	} else {
		print "<tr><td colspan=\"2\" class=\"optionbox\">";
		print i18n::translate('This block cannot be configured.');
		print "</td></tr>";
		print "<tr><td colspan=\"2\" class=\"topbottombar\">";
		print "<input type=\"button\" value=\"".i18n::translate('Click here to continue')."\" onclick=\"parentrefresh();\" />";
		print help_link('click_here');
		print "</td></tr>";
	}
	print "</table>";
	print "</form>";
}
else {
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
	print "var block_descr = new Array();\n";
	foreach ($WT_BLOCKS as $b=>$block) {
		print "block_descr['$b'] = '".str_replace("'", "\\'", embed_globals($block["descr"]))."';\n";
	}
	print "block_descr['advice1'] = '".str_replace("'", "\\'", i18n::translate('Highlight a  block name and then click on one of the arrow icons to move that highlighted block in the indicated direction.'))."';\n";
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
	<input type="hidden" name="ctype" value="<?php print $ctype;?>" />
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="name" value="<?php print $name;?>" />
	<table dir="ltr" border="1" width="400px">
	<tr><td class="topbottombar" colspan="7">
	<?php
	if ($ctype=="user") print "<b>".i18n::translate('Customize My Page')."</b>";
	else print "<b>".i18n::translate('Customize this GEDCOM Home Page')."</b>";
	echo help_link('portal_config_intructions');
	print "</td></tr>";
	// NOTE: Row 1: Column legends
	print "<tr>";
		print "<td class=\"descriptionbox center vmiddle\" colspan=\"2\">\n";
			print "<b>".i18n::translate('Main Section Blocks')."</b>";
		print "</td>\n";
		print "<td class=\"descriptionbox center vmiddle\" colspan=\"3\">";
			print "<b>".i18n::translate('Available Blocks')."</b>";
		print "</td>\n";
		print "<td class=\"descriptionbox center vmiddle\" colspan=\"2\">";
			print "<b>".i18n::translate('Right Section Blocks')."</b>";
		print "</td>";
	print "</tr>\n";
	print "<tr>";
	// NOTE: Row 2 column 1: Up/Down buttons for left (main) block list
	print "<td class=\"optionbox width20px center vmiddle\">";
		print "<a tabindex=\"-1\" onclick=\"move_up_block('main_select');\" title=\"".i18n::translate('Move Up')."\">".$IconUarrow."</a>";
		print "<br />";
		print "<a tabindex=\"-1\" onclick=\"move_down_block('main_select');\" title=\"".i18n::translate('Move Down')."\">".$IconDarrow."</a>";
		print "<br /><br />";
		print help_link('block_move_up');

	print "</td>";
	// NOTE: Row 2 column 2: Left (Main) block list
	print "<td class=\"optionbox\" dir=\"".$TEXT_DIRECTION."\">\n";
		print "<select multiple=\"multiple\" id=\"main_select\" name=\"main[]\" size=\"10\" onchange=\"show_description('main_select');\">\n";
		foreach($ublocks["main"] as $indexval => $block) {
			if (function_exists($block[0])) {
				print "<option value=\"$block[0]\">".$WT_BLOCKS[$block[0]]["name"]."</option>\n";
			}
		}
		print "</select>\n";
	print "</td>";
	// NOTE: Row 2 column 3: Left/Right buttons for left (main) block list
	print "<td class=\"optionbox width20 vmiddle\">";
		print "<a tabindex=\"-1\" onclick=\"move_left_right_block('main_select', 'right_select');\" title=\"".i18n::translate('Move Right')."\">".$IconRDarrow."</a>";
		print "<br />";
		print "<a tabindex=\"-1\" onclick=\"move_left_right_block('main_select', 'available_select');\" title=\"".i18n::translate('Remove')."\">".$IconRarrow."</a>";
		print "<br />";
		print "<a tabindex=\"-1\" onclick=\"move_left_right_block('available_select', 'main_select');\" title=\"".i18n::translate('Add')."\">".$IconLarrow."</a>";
		print "<br /><br />";
		print help_link('block_move_right');

	print "</td>";
	// Row 2 column 4: Middle (Available) block list
	print "<td class=\"optionbox\" dir=\"".$TEXT_DIRECTION."\">";
		print "<select id=\"available_select\" name=\"available[]\" size=\"10\" onchange=\"show_description('available_select');\">\n";
		foreach($SortedBlocks as $key => $value) {
			print "<option value=\"$key\">".$SortedBlocks[$key]."</option>\n";
		}
		print "</select>\n";
	print "</td>";
	// NOTE: Row 2 column 5: Left/Right buttons for right block list
	print "<td class=\"optionbox width20 vmiddle\">";
		print "<a tabindex=\"-1\" onclick=\"move_left_right_block('right_select', 'main_select');\" title=\"".i18n::translate('Move Left')."\">".$IconLDarrow."</a>";
		print "<br />";
		print "<a tabindex=\"-1\" onclick=\"move_left_right_block('right_select', 'available_select');\" title=\"".i18n::translate('Remove')."\">".$IconLarrow."</a>";
		print "<br />";
		print "<a tabindex=\"-1\" onclick=\"move_left_right_block('available_select', 'right_select');\" title=\"".i18n::translate('Add')."\">".$IconRarrow."</a>";
		print "<br /><br />";
		print help_link('block_move_right');
	print "</td>";
	// NOTE: Row 2 column 6: Right block list
	print "<td class=\"optionbox\" dir=\"".$TEXT_DIRECTION."\">";
		print "<select multiple=\"multiple\" id=\"right_select\" name=\"right[]\" size=\"10\" onchange=\"show_description('right_select');\">\n";
		foreach($ublocks["right"] as $indexval => $block) {
			if (function_exists($block[0])) {
				print "<option value=\"$block[0]\">".$WT_BLOCKS[$block[0]]["name"]."</option>\n";
			}
		}
		print "</select>\n";
	print "</td>";
	// NOTE: Row 2 column 7: Up/Down buttons for right block list
	print "<td class=\"optionbox width20 vmiddle\">";
		print "<a tabindex=\"-1\" onclick=\"move_up_block('right_select');\" title=\"".i18n::translate('Move Up')."\">".$IconUarrow."</a>";
		print "<br />";
		print "<a tabindex=\"-1\" onclick=\"move_down_block('right_select');\" title=\"".i18n::translate('Move Down')."\">".$IconDarrow."</a>";
		print "<br /><br />";
		print help_link('block_move_up');
	print "</td>";
	print "</tr>";
	// NOTE: Row 3 columns 1-7: Summary description of currently selected block
	print "<tr><td class=\"descriptionbox wrap\" colspan=\"7\" dir=\"".$TEXT_DIRECTION."\"><div id=\"instructions\">";
	print i18n::translate('Highlight a  block name and then click on one of the arrow icons to move that highlighted block in the indicated direction.');
	print "</div></td></tr>";
	print "<tr><td class=\"topbottombar\" colspan=\"7\">";

	if (WT_USER_IS_ADMIN && $ctype=='user') {
		print i18n::translate('Use these blocks as the default block configuration for all users?')."<input type=\"checkbox\" name=\"setdefault\" value=\"1\" /><br /><br />\n";
	}

	print "<input type=\"button\" value=\"".i18n::translate('Reset to Default Blocks')."\" onclick=\"window.location='index_edit.php?ctype=$ctype&amp;action=reset&amp;name=".str_replace("'", "\'", $name)."';\" />\n";
	if ($ctype=='user') {
		echo help_link('block_default_portal');
	} else {
		echo help_link('block_default_index');
	}
	print "&nbsp;&nbsp;";
	print "<input type=\"button\" value=\"".i18n::translate('Click here to continue')."\" onclick=\"select_options(); save_form();\" />\n";
	print help_link('click_here');
	print "&nbsp;&nbsp;";
	print "<input type =\"button\" value=\"".i18n::translate('Cancel')."\" onclick=\"window.close()\" />";
	if (WT_USER_GEDCOM_ADMIN && $ctype!="user") {
		print "<br />";
		print "<input type =\"button\" value=\"".i18n::translate('Clear cache files')."\" onclick=\"window.location='index_edit.php?ctype=$ctype&amp;action=clearcache&amp;name=".str_replace("'", "\'", $name)."';\" />";
		print help_link('clear_cache');
	}
	print "</td></tr></table>";
	print "</form>\n";

	// end of 1st tab
	print "</div>\n";

	//--------------------------------Start 2nd tab Help page
	print "\n\t<div id=\"help\" class=\"tab_page\" style=\"position: absolute; display: none; top: auto; left: auto; z-index: 2; \">\n\t";

	print "<br /><center><input type=\"button\" value=\"".i18n::translate('Click here to continue')."\" onclick=\"expand_layer('configure', true); expand_layer('help', false);\" /></center><br /><br />\n";
	echo i18n::translate("Here is a short description of each of the blocks you can place on the Welcome or My Page.<br /><table border='1' align='center'><tr><td class='list_value'><b>Name</b></td><td class='list_value'><b>Description</b></td></tr>&nbsp;</table>");

	// end of 2nd tab
	print "</div>\n";
}

print "</body></html>";		// Yes! Absolutely NOTHING at page bottom, please.
?>

<?php
/**
 * PopUp Window to provide editing features.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
 * @author Dparker
 * @package webtrees
 * @subpackage Admin
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'dir_editor.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

if (!WT_USER_IS_ADMIN) {
	header("Location: login.php?url=dir_editor.php");
	exit;
}

function full_rmdir($dir) {
	if (!is_writable($dir)) {
		if (!@chmod($dir, WT_PERM_EXE)) {
			return FALSE;
		}
	}

	$d = dir($dir);
	while (FALSE !== ($entry = $d->read())) {
		if ($entry == '.' || $entry == '..') {
			continue;
		}
		$entry = $dir . '/' . $entry;
		if (is_dir($entry)) {
			if (!full_rmdir($entry)) {
				return FALSE;
			}
			continue;
		}
		if (!@unlink($entry)) {
			$d->close();
			return FALSE;
		}
	}

	$d->close();
	rmdir($dir);
	return TRUE;
}

// Vars
$ajaxdeleted = false;
$elements = Array();
$locked_by_context = array("readme.txt", "index.php", "gedcoms.php");
$dbname = explode("/", $DBNAME);
$locked_by_context[] = end($dbname);

// If we are storing the media in the index directory (this is the
// default for the media firewall), then don't delete it.
if (
	$USE_MEDIA_FIREWALL &&
	$MEDIA_FIREWALL_ROOTDIR==$INDEX_DIRECTORY &&
	(substr($MEDIA_DIRECTORY, 0, 1)!='.')
) {
	$locked_by_context[]=trim($MEDIA_DIRECTORY, '/');
}

print_header(i18n::translate('Cleanup Index directory'));
echo "<h2>", i18n::translate('Cleanup Index directory'), "</h2>";

echo i18n::translate('To delete a file or subdirectory from the Index directory drag it to the wastebasket or select its checkbox.  Click the Delete button to permanently remove the indicated files.<br /><br />Files marked with <img src="./images/RESN_confidential.gif" alt="" /> are required for proper operation and cannot be removed.<br />Files marked with <img src="./images/RESN_locked.gif" alt="" /> have important settings or pending change data and should only be deleted if you are sure you know what you are doing.');

//post back
if(isset($_REQUEST["to_delete"])) {
	echo "<span class=\"error\">", i18n::translate('Deleted files:'), "</span><br/>";
	foreach($_REQUEST["to_delete"] as $k=>$v) {
		if (is_dir($INDEX_DIRECTORY.$v)) {
			full_rmdir($INDEX_DIRECTORY.$v);
		} elseif (file_exists($INDEX_DIRECTORY.$v)) {
			unlink($INDEX_DIRECTORY.$v);
		}
		echo "<span class=\"error\">", $v, "</span><br/>";
	}

}

require_once WT_ROOT.'js/prototype.js.htm';
require_once WT_ROOT.'js/scriptaculous.js.htm';

?>
<script type="text/javascript">
<!--
function warnuser(cbox) {
	if (cbox.checked) {
		if(!confirm('<?php print i18n::translate('This file contains important information such as language settings or pending change data.  Are you sure you want to delete this file?'); ?>')) cbox.checked = false;
	}
}
//-->
</script>
<form name="delete_form" method="post" action="">
<table>
	<tr>
		<td>
		<ul id="reorder_list">
		<?php

		//-- lock the GEDCOM and settings files
		foreach(get_all_gedcoms() as $ged_id=>$ged_name){
			$file=get_privacy_file($ged_id);
			if ($file!='privacy.php') {
				$locked_by_context[] = str_replace($INDEX_DIRECTORY, "", $file);
			}
			$file=get_config_file($ged_id);
			if ($file!='config_gedcom.php') {
				$locked_by_context[] = str_replace($INDEX_DIRECTORY, "", $file);
			}
		}
		$dir = dir($INDEX_DIRECTORY);

		$path = $INDEX_DIRECTORY; // snag our path
		$entryList = array();
		while (false !== ($entry = $dir->read())) {
			$entryList[] = $entry;
		}
		sort($entryList);
		foreach ($entryList as $entry) {
			//echo $entry, "\n";
			if ($entry{0} != '.') {
				if ($ged_id=get_id_from_gedcom($entry)) {
					print "<li class=\"facts_value\" name=\"$entry\" style=\"margin-bottom:2px;\" id=\"lock_$entry\" >";
					print "<img src=\"./images/RESN_confidential.gif\" alt=\"\" />&nbsp;&nbsp;";
					print "<span class=\"name2\">".$entry."</span>";
					print "&nbsp;&nbsp;".i18n::translate('Associated files:')."<i>&nbsp;&nbsp;".str_replace($path, "", get_gedcom_setting($ged_id, 'privacy'));
					print "&nbsp;&nbsp;".str_replace($path, "", get_gedcom_setting($ged_id, 'config'))."</i>";
				}
				else if (in_array($entry, $locked_by_context)) {
					print "<li class=\"facts_value\" name=\"$entry\" style=\"margin-bottom:2px;\" id=\"lock_$entry\" >";
					print "<img src=\"./images/RESN_confidential.gif\" alt=\"\" />&nbsp;&nbsp;";
					print "<span class=\"name2\">".$entry."</span>";
				}
				else{
					print "<li class=\"facts_value\" name=\"$entry\" style=\"cursor:move;margin-bottom:2px;\" id=\"li_$entry\" >";
					print "<input type=\"checkbox\" name=\"to_delete[]\" value=\"".$entry."\" />\n";
					print $entry;
					$element[] = "li_".$entry;
				}
				print "</li>";
			}
		}
		?>
		</ul>
		</td>
		<td valign="top" id="trash" class="facts_value02"><?php
		$dir->close();

		print "<div style=\"margin-bottom:2px;\">";
		print "<table><tr><td>";
		if (isset($WT_IMAGES["trashcan"]["medium"])) print "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["trashcan"]["medium"]."\" align=\"left\" alt=\"\" />";
		else print "<img src=\"images/trashcan.gif\" align=\"left\" alt=\"\" />";
		print "</td>";
		print "<td valign=\"top\"><ul id=\"trashlist\">";
		print "</ul></td></tr></table>";
		print "</div>";

		?> <script type="text/javascript" language="javascript">
	<!--
	new Effect.BlindDown('reorder_list', {duration: 1});

		<?php
		foreach($element as $key=>$val)
		{
			print "new Draggable('".$val."', {revert:true});";
		}
		?>

	Droppables.add('trash', {
	hoverclass: 'facts_valuered',
	onDrop: function(element)
	{
		if (element.attributes.warn) {
			if (!confirm('<?php print i18n::translate('This file contains important information such as language settings or pending change data.  Are you sure you want to delete this file?'); ?>')) return;
		}
		$('trashlist').innerHTML +=
			'<li class="facts_value">'+ element.attributes.name.value +'<input type="hidden" name="to_delete[]" value="'+element.attributes.name.value+'"/></li>' ;
			element.style.display = "none";
			// element.className='facts_valuered';
		}});
function ul_clear()
{
	$('trashlist').innerHTML = "";

	list = document.getElementById('reorder_list');
	children = list.childNodes;
	for(i=0; i<children.length; i++) {
		node = children[i];
		if (node.tagName=='li' || node.tagName=='LI') {
			//node.className='facts_value';
			node.style.display='list-item';
		}
	}
}

function removeAll() {
	var elements = document.getElementsByName('to_delete[]');
	for(i=0; i<elements.length; i++) {
		node = elements[i];
		if (!node.attributes.warn) node.checked = true;
	}
	document.delete_form.submit();
}
// -->
</script>

		<button type="submit"><?php print i18n::translate('Delete');?></button>
		<button type="button" onclick="ul_clear(); return false;"><?php print i18n::translate('Cancel');?></button><br /><br />
		<button type="button" onclick="removeAll(); return false;"><?php print i18n::translate('Remove all nonessential files');?></button>
		</td>
	</tr>
</table>
</form>
		<?php print_footer(); ?>

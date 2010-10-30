<?php
/**
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
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url='.WT_SCRIPT_NAME);
	exit;
}

$INDEX_DIRECTORY=get_site_setting('INDEX_DIRECTORY');

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
$elements = array();
$locked_by_context = array('index.php', 'config.ini.php');

// If we are storing the media in the data directory (this is the
// default for the media firewall), then don't delete it.
if (
	$USE_MEDIA_FIREWALL &&
	$MEDIA_FIREWALL_ROOTDIR==$INDEX_DIRECTORY &&
	(substr($MEDIA_DIRECTORY, 0, 1)!='.')
) {
	$locked_by_context[]=trim($MEDIA_DIRECTORY, '/');
}

print_header(i18n::translate('Cleanup data directory'));
echo '<p class="center"><input TYPE="button" VALUE="', i18n::translate('Return to Administration page'), '" onclick="javascript:window.location=\'admin.php\'" /></p>',
	'<h2 class="center">', i18n::translate('Cleanup data directory'), '</h2>';

echo i18n::translate('To delete a file or subdirectory from the data directory drag it to the wastebasket or select its checkbox.  Click the Delete button to permanently remove the indicated files.'), '<br /><br />', i18n::translate('Files marked with %s are required for proper operation and cannot be removed.', '<img src="./images/RESN_confidential.gif" alt="" />'), '<br />', i18n::translate('Files marked with %s have important settings or pending change data and should only be deleted if you are sure you know what you are doing.', '<img src="./images/RESN_locked.gif" alt="" />');

//post back
if (isset($_REQUEST["to_delete"])) {
	echo "<span class=\"error\">", i18n::translate('Deleted files:'), "</span><br/>";
	foreach ($_REQUEST["to_delete"] as $k=>$v) {
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
		if (!confirm('<?php echo i18n::translate('This file contains important information such as language settings or pending change data.  Are you sure you want to delete this file?'); ?>')) cbox.checked = false;
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
		$dir = dir($INDEX_DIRECTORY);

		$path = $INDEX_DIRECTORY; // snag our path
		$entryList = array();
		while (false !== ($entry = $dir->read())) {
			$entryList[] = $entry;
		}
		sort($entryList);
		foreach ($entryList as $entry) {
			if ($entry{0} != '.') {
				if (in_array($entry, $locked_by_context)) {
					echo "<li class=\"facts_value\" name=\"$entry\" style=\"margin-bottom:2px;\" id=\"lock_$entry\" >";
					echo "<img src=\"./images/RESN_confidential.gif\" alt=\"\" />&nbsp;&nbsp;";
					echo "<span class=\"name2\">".$entry."</span>";
				}
				else {
					echo "<li class=\"facts_value\" name=\"$entry\" style=\"cursor:move;margin-bottom:2px;\" id=\"li_$entry\" >";
					echo "<input type=\"checkbox\" name=\"to_delete[]\" value=\"".$entry."\" />";
					echo $entry;
					$elements[] = "li_".$entry;
				}
				echo "</li>";
			}
		}
		?>
		</ul>
		</td>
		<td valign="top" id="trash" class="facts_value02"><?php
		$dir->close();

		echo "<div style=\"margin-bottom:2px;\">";
		echo "<table><tr><td>";
		if (isset($WT_IMAGES["trashcan"]["medium"])) echo "<img src=\"".$WT_IMAGES["trashcan"]["medium"]."\" align=\"left\" alt=\"\" />";
		else echo "<img src=\"images/trashcan.gif\" align=\"left\" alt=\"\" />";
		echo "</td>";
		echo "<td valign=\"top\"><ul id=\"trashlist\">";
		echo "</ul></td></tr></table>";
		echo "</div>";

		?> <script type="text/javascript" language="javascript">
	<!--
	new Effect.BlindDown('reorder_list', {duration: 1});

		<?php
		foreach ($elements as $key=>$val)
		{
			echo "new Draggable('".$val."', {revert:true});";
		}
		?>

	Droppables.add('trash', {
	hoverclass: 'facts_valuered',
	onDrop: function(element)
	{
		if (element.attributes.warn) {
			if (!confirm('<?php echo i18n::translate('This file contains important information such as language settings or pending change data.  Are you sure you want to delete this file?'); ?>')) return;
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
	for (i=0; i<children.length; i++) {
		node = children[i];
		if (node.tagName=='li' || node.tagName=='LI') {
			//node.className='facts_value';
			node.style.display='list-item';
		}
	}
}

function removeAll() {
	var elements = document.getElementsByName('to_delete[]');
	for (i=0; i<elements.length; i++) {
		node = elements[i];
		if (!node.attributes.warn) node.checked = true;
	}
	document.delete_form.submit();
}
// -->
</script>
		<button type="submit"><?php echo i18n::translate('Delete'); ?></button>
		<button type="button" onclick="ul_clear(); return false;"><?php echo i18n::translate('Cancel'); ?></button><br /><br />
		<button type="button" onclick="removeAll(); return false;"><?php echo i18n::translate('Remove all nonessential files'); ?></button>
		</td>
	</tr>
</table>
</form>
<?php print_footer();

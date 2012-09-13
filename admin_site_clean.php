<?php
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
// version $Id$

define('WT_SCRIPT_NAME', 'admin_site_clean.php');
require './includes/session.php';

$controller=new WT_Controller_Base();
$controller
	->requireAdminLogin()
	->setPageTitle(WT_I18N::translate('Cleanup data directory'))
	->pageHeader();

require WT_ROOT.'includes/functions/functions_edit.php';

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
$locked_by_context = array('index.php', 'config.ini.php');

// If we are storing the media in the data directory (this is the
// default for the media firewall), then don't delete it.
// Need to consider the settings for all gedcoms
foreach (WT_Tree::getAll() as $tree) {
	$MEDIA_FIREWALL_ROOTDIR=get_gedcom_setting($tree->tree_id, 'MEDIA_FIREWALL_ROOTDIR', WT_DATA_DIR);
	$MEDIA_DIRECTORY       =get_gedcom_setting($tree->tree_id, 'MEDIA_DIRECTORY');
	if (realpath($MEDIA_FIREWALL_ROOTDIR)==realpath(WT_DATA_DIR)) {
		$locked_by_context[]=trim($MEDIA_DIRECTORY, '/');
	}
}

echo
	'<h3>', WT_I18N::translate('Cleanup data directory'), '</h3>',
	'<p>',
	WT_I18N::translate('Files marked with %s are required for proper operation and cannot be removed.', '<i class="icon-resn-confidential"></i>'),
	'</p>';

//post back
if (isset($_REQUEST['to_delete'])) {
	echo '<div class="error">', WT_I18N::translate('Deleted files:'), '</div>';
	foreach ($_REQUEST['to_delete'] as $k=>$v) {
		if (is_dir(WT_DATA_DIR.$v)) {
			full_rmdir(WT_DATA_DIR.$v);
		} elseif (file_exists(WT_DATA_DIR.$v)) {
			unlink(WT_DATA_DIR.$v);
		}
		echo '<div class="error">', $v, '</div>';
	}
}

echo '<form name="delete_form" method="post" action="">';
echo '<div id="cleanup"><ul>';

$dir=dir(WT_DATA_DIR);
$entries=array();
while (false !== ($entry=$dir->read())) {
	$entries[]=$entry;
}
sort($entries);
foreach ($entries as $entry) {
	if ($entry[0] != '.') {
		if (in_array($entry, $locked_by_context)) {
			echo "<li class=\"facts_value\" name=\"$entry\" id=\"lock_$entry\" >";
			echo '<i class="icon-resn-confidential"></i> <span>', $entry, '</span>';
		} else {
			echo "<li class=\"facts_value\" name=\"$entry\" id=\"li_$entry\" >";
			echo '<input type="checkbox" name="to_delete[]" value="', $entry, '">', $entry;
			$element[] = "li_".$entry;
		}
		echo '</li>';
	}
}
$dir->close();
echo
	'</ul>',
	'<button type="submit">', WT_I18N::translate('Delete'), '</button>',
	'</div>',
	'</form>';

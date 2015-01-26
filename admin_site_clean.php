<?php
// webtrees: Web based Family History software
// Copyright (C) 2015 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;
use Rhumsaa\Uuid\Uuid;

define('WT_SCRIPT_NAME', 'admin_site_clean.php');
require './includes/session.php';


$to_delete = WT_Filter::postArray('to_delete');
if ($to_delete && WT_Filter::checkCsrf()) {
	foreach ($to_delete as $path) {
		$is_dir = is_dir(WT_DATA_DIR . $path);
		if (WT_File::delete(WT_DATA_DIR . $path)) {
			if ($is_dir) {
				WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s was deleted.', WT_Filter::escapeHtml($path)), 'success');
			} else {
				WT_FlashMessages::addMessage(WT_I18N::translate('The file %s was deleted.', WT_Filter::escapeHtml($path)), 'success');
			}
		} else {
			if ($is_dir) {
				WT_FlashMessages::addMessage(WT_I18N::translate('The folder %s could not be deleted.', WT_Filter::escapeHtml($path)), 'danger');
			} else {
				WT_FlashMessages::addMessage(WT_I18N::translate('The file %s could not be deleted.', WT_Filter::escapeHtml($path)), 'danger');
			}
		}
	}

	header('Location: ' . WT_SERVER_NAME . WT_SCRIPT_PATH . WT_SCRIPT_NAME);

	return;
}

$controller = new WT_Controller_Page;
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(/* I18N: The “Data folder” is a configuration setting */ WT_I18N::translate('Clean up data folder'))
	->pageHeader();

$do_not_delete = array('index.php', 'config.ini.php');

// If we are storing the media in the data folder (this is the default), then don’t delete it.
foreach (WT_Tree::getAll() as $tree) {
	$MEDIA_DIRECTORY = $tree->getPreference('MEDIA_DIRECTORY');

	if (substr($MEDIA_DIRECTORY, 0, 3) != '../') {
		// Just need to add the first part of the path
		$tmp = explode('/', $MEDIA_DIRECTORY);
		$do_not_delete[] = $tmp[0];
	}
}

$locked_icon = '<i class="fa fa-ban text-danger"></i>';

$dir = dir(WT_DATA_DIR);
$entries = array();
while (false !== ($entry = $dir->read())) {
	if ($entry[0] != '.') {
		$entries[] = $entry;
	}
}

sort($entries);

?>
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo WT_I18N::translate('Control panel'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<p>
	<?php echo WT_I18N::translate('Files marked with %s are required for proper operation and cannot be removed.', $locked_icon); ?>
</p>

<form method="post">
	<?php echo WT_Filter::getCsrf(); ?>
	<ul class="fa-ul">
		<?php
		foreach ($entries as $entry) {
			if (in_array($entry, $do_not_delete)) {
				echo '<li><i class="fa-li fa fa-ban text-danger"></i>', WT_Filter::escapeHtml($entry), '</li>';
			} else {
				$uuid = 'label-' . Uuid::uuid4();
				echo '<li><i class="fa-li fa fa-trash-o"></i>';
				echo '<label>';
				echo WT_Filter::escapeHtml($entry);
				echo ' <input type="checkbox" name="to_delete[]" value="', WT_Filter::escapeHtml($entry), '">';
				echo '</label></li>';
			}
		}
		$dir->close();
		?>
	</ul>
	<button class="btn btn-primary" type="submit"><?php echo /* I18N: Button label */ WT_I18N::translate('Delete'); ?></button>
</form>

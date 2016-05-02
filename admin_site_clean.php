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

use Fisharebest\Webtrees\Controller\PageController;
use Rhumsaa\Uuid\Uuid;

define('WT_SCRIPT_NAME', 'admin_site_clean.php');
require './includes/session.php';

$to_delete = Filter::postArray('to_delete');
if ($to_delete && Filter::checkCsrf()) {
	foreach ($to_delete as $path) {
		$is_dir = is_dir(WT_DATA_DIR . $path);
		if (File::delete(WT_DATA_DIR . $path)) {
			if ($is_dir) {
				FlashMessages::addMessage(I18N::translate('The folder %s has been deleted.', Filter::escapeHtml($path)), 'success');
			} else {
				FlashMessages::addMessage(I18N::translate('The file %s has been deleted.', Filter::escapeHtml($path)), 'success');
			}
		} else {
			if ($is_dir) {
				FlashMessages::addMessage(I18N::translate('The folder %s could not be deleted.', Filter::escapeHtml($path)), 'danger');
			} else {
				FlashMessages::addMessage(I18N::translate('The file %s could not be deleted.', Filter::escapeHtml($path)), 'danger');
			}
		}
	}

	header('Location: ' . WT_BASE_URL . WT_SCRIPT_NAME);

	return;
}

$controller = new PageController;
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(/* I18N: The “Data folder” is a configuration setting */ I18N::translate('Clean up data folder'))
	->pageHeader();

$do_not_delete = array('index.php', 'config.ini.php');

// If we are storing the media in the data folder (this is the default), then don’t delete it.
foreach (Tree::getAll() as $tree) {
	$MEDIA_DIRECTORY = $tree->getPreference('MEDIA_DIRECTORY');

	if (substr($MEDIA_DIRECTORY, 0, 3) != '../') {
		// Just need to add the first part of the path
		$tmp             = explode('/', $MEDIA_DIRECTORY);
		$do_not_delete[] = $tmp[0];
	}
}

$locked_icon = '<i class="fa fa-ban text-danger"></i>';

$dir     = dir(WT_DATA_DIR);
$entries = array();
while (false !== ($entry = $dir->read())) {
	if ($entry[0] != '.') {
		$entries[] = $entry;
	}
}

sort($entries);

?>
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
	<li class="active"><?php echo $controller->getPageTitle(); ?></li>
</ol>

<h1><?php echo $controller->getPageTitle(); ?></h1>

<p>
	<?php echo I18N::translate('Files marked with %s are required for proper operation and cannot be removed.', $locked_icon); ?>
</p>

<form method="post">
	<?php echo Filter::getCsrf(); ?>
	<fieldset>
		<legend class="sr-only"><?php echo $controller->getPageTitle(); ?></legend>
		<ul class="fa-ul">
			<?php
			foreach ($entries as $entry) {
				if (in_array($entry, $do_not_delete)) {
					echo '<li><i class="fa-li fa fa-ban text-danger"></i>', Filter::escapeHtml($entry), '</li>';
				} else {
					$id = 'input-' . Uuid::uuid4();
					echo '<li><i class="fa-li fa fa-trash-o"></i>';
					echo '<label for="', $id, '">';
					echo '<input type="checkbox" id="', $id, '" name="to_delete[]" value="', Filter::escapeHtml($entry), '"> ';
					echo Filter::escapeHtml($entry);
					echo '</label></li>';
				}
			}
			$dir->close();
			?>
		</ul>
	</fieldset>
	<button class="btn btn-danger" type="submit">
		<i class="fa fa-trash-o"></i>
		<?php echo /* I18N: A button label */ I18N::translate('delete'); ?>
	</button>
</form>

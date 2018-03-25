<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use GuzzleHttp\Client;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Throwable;

require 'includes/session.php';

// Check for updates
$latest_version_txt = Functions::fetchLatestVersion();
if (preg_match('/^[0-9.]+\|[0-9.]+\|/', $latest_version_txt)) {
	list($latest_version, , $download_url) = explode('|', $latest_version_txt);
} else {
	// Cannot determine the latest version
	list($latest_version, , $download_url) = explode('|', '||');
}

$latest_version_html = '<span dir="ltr">' . $latest_version . '</span>';

// Show a friendly message while the site is being upgraded
$lock_file      = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'offline.txt';
$lock_file_text = I18N::translate('This website is being upgraded. Try again in a few minutes.') . PHP_EOL . FunctionsDate::formatTimestamp(WT_TIMESTAMP) . /* I18N: Timezone - http://en.wikipedia.org/wiki/UTC */ I18N::translate('UTC');

// Success/failure indicators
$icon_success = '<i class="icon-yes"></i>';
$icon_failure = '<i class="icon-failure"></i>';

// Need confirmation for various actions
$continue       = Filter::post('continue', '1') && Filter::checkCsrf();
$modules_action = Filter::post('modules', 'ignore|disable');
$themes_action  = Filter::post('themes', 'ignore|disable');

$controller = new PageController;
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(I18N::translate('Upgrade wizard'))
	->pageHeader();

echo '<h1>', $controller->getPageTitle(), '</h1>';

if ($latest_version == '') {
	echo '<p>', I18N::translate('No upgrade information is available.'), '</p>';

	return;
}

if (version_compare(WT_VERSION, $latest_version) >= 0) {
	echo '<p>', I18N::translate('This is the latest version of webtrees. No upgrade is available.'), '</p>';

	return;
}

echo '<form method="post" action="admin_site_upgrade.php">';
echo Filter::getCsrf();

if ($continue) {
	echo '<input type="hidden" name="continue" value="1">';
	echo '<p>', I18N::translate('It can take several minutes to download and install the upgrade. Be patient.'), '</p>';
} else {
	echo '<p>', I18N::translate('A new version of webtrees is available.'), '</p>';
	echo '<p>', I18N::translate('Depending on your server configuration, you may be able to upgrade automatically.'), '</p>';
	echo '<p>', I18N::translate('It can take several minutes to download and install the upgrade. Be patient.'), '</p>';
	echo '<button type="submit" name="continue" value="1">', /* I18N: %s is a version number, such as 1.2.3 */ I18N::translate('Upgrade to webtrees %s.', $latest_version_html), '</button>';
	echo '</form>';

	return;
}

echo '<ul>';

////////////////////////////////////////////////////////////////////////////////
// Cannot upgrade until pending changes are accepted/rejected
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to… */ I18N::translate('Check for pending changes…');

$changes = Database::prepare("SELECT 1 FROM `##change` WHERE status='pending' LIMIT 1")->fetchOne();

if ($changes) {
	echo '<br>', I18N::translate('You should accept or reject all pending changes before upgrading.'), $icon_failure;
	echo '<br><a class="btn btn-primary" href="' . e(route('show-pending')) . '">', I18N::translate('Pending changes'), '</a>';
	echo '</li></ul></form>';

	return;
} else {
	echo '<br>', I18N::translate('There are no pending changes.'), $icon_success;
}

echo '</li>';

////////////////////////////////////////////////////////////////////////////////
// Custom modules may not work with the new version.
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...] */ I18N::translate('Check for custom modules…');

$custom_modules = false;
foreach (Module::getInstalledModules('disabled') as $module) {
	if (!in_array($module->getName(), Module::getCoreModuleNames())) {
		switch ($modules_action) {
			case 'disable':
				Database::prepare(
					"UPDATE `##module` SET status = 'disabled' WHERE module_name = ?"
				)->execute([$module->getName()]);
				break;
			case 'ignore':
				echo '<br>', I18N::translate('Custom module'), ' — ', WT_MODULES_DIR, $module->getName(), ' — ', $module->getTitle(), $icon_success;
				break;
			default:
				echo '<br>', I18N::translate('Custom module'), ' — ', WT_MODULES_DIR, $module->getName(), ' — ', $module->getTitle(), $icon_failure;
				$custom_modules = true;
				break;
		}
	}
}
if ($custom_modules) {
	echo '<br>', I18N::translate('You should consult the module’s author to confirm compatibility with this version of webtrees.');
	echo '<br>', '<button type="submit" name="modules" value="disable">', I18N::translate('Disable these modules'), '</button> — ', I18N::translate('You can re-enable these modules after the upgrade.');
	echo '<br>', '<button type="submit" name="modules" value="ignore">', /* I18N: Ignore the warnings, and… */ I18N::translate('Upgrade anyway'), '</button> — ', I18N::translate('Caution: old modules may not work, or they may prevent webtrees from working.');
	echo '</li></ul></form>';

	return;
} else {
	if ($modules_action != 'ignore') {
		echo '<br>', I18N::translate('No custom modules are enabled.'), $icon_success;
	}
	echo '<input type="hidden" name="modules" value="', e($modules_action), '">';
}

echo '</li>';

////////////////////////////////////////////////////////////////////////////////
// Custom themes may not work with the new version.
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to… */ I18N::translate('Check for custom themes…');

$custom_themes = false;
foreach (Theme::themeNames() as $theme_id => $theme_name) {
	switch ($theme_id) {
		case 'clouds':
		case 'colors':
		case 'fab':
		case 'minimal':
		case 'webtrees':
		case 'xenea':
			break;
		default:
			$theme_used = Database::prepare(
				"SELECT EXISTS (SELECT 1 FROM `##site_setting`   WHERE setting_name='THEME_DIR' AND setting_value=?)" .
				" OR EXISTS (SELECT 1 FROM `##gedcom_setting` WHERE setting_name='THEME_DIR' AND setting_value=?)" .
				" OR EXISTS (SELECT 1 FROM `##user_setting`   WHERE setting_name='theme'     AND setting_value=?)"
			)->execute([$theme_id, $theme_id, $theme_id])->fetchOne();
			if ($theme_used) {
				switch ($themes_action) {
					case 'disable':
						Database::prepare(
							"DELETE FROM `##site_setting`   WHERE setting_name = 'THEME_DIR' AND setting_value = ?"
						)->execute([$theme_id]);
						Database::prepare(
							"DELETE FROM `##gedcom_setting` WHERE setting_name = 'THEME_DIR' AND setting_value = ?"
						)->execute([$theme_id]);
						Database::prepare(
							"DELETE FROM `##user_setting`   WHERE setting_name = 'theme'     AND setting_value = ?"
						)->execute([$theme_id]);
						break;
					case 'ignore':
						echo '<br>', I18N::translate('Custom theme'), ' — ', $theme_id, ' — ', $theme_name, $icon_success;
						break;
					default:
						echo '<br>', I18N::translate('Custom theme'), ' — ', $theme_id, ' — ', $theme_name, $icon_failure;
						$custom_themes = true;
						break;
				}
		}
		break;
	}
}

if ($custom_themes) {
	echo '<br>', I18N::translate('You should consult the theme’s author to confirm compatibility with this version of webtrees.');
	echo '<br>', '<button type="submit" name="themes" value="disable">', I18N::translate('Disable these themes'), '</button> — ', I18N::translate('You can re-enable these themes after the upgrade.');
	echo '<br>', '<button type="submit" name="themes" value="ignore">', I18N::translate('Upgrade anyway'), '</button> — ', I18N::translate('Caution: old themes may not work, or they may prevent webtrees from working.');
	echo '</li></ul></form>';

	return;
} else {
	if ($themes_action != 'ignore') {
		echo '<br>', I18N::translate('No custom themes are enabled.'), $icon_success;
	}
	echo '<input type="hidden" name="themes" value="', e($themes_action), '">';
}

echo '</li>';

////////////////////////////////////////////////////////////////////////////////
// Make a backup of genealogy data
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to… */ I18N::translate('Export all the family trees to GEDCOM files…');

foreach (Tree::getAll() as $tree) {
	$filename = WT_DATA_DIR . $tree->getName() . date('-Y-m-d') . '.ged';

	try {
		// To avoid partial trees on timeout/diskspace/etc, write to a temporary file first
		$stream = fopen($filename . '.tmp', 'w');
		$tree->exportGedcom($stream);
		fclose($stream);
		rename($filename . '.tmp', $filename);
		echo '<br>', I18N::translate('The family tree has been exported to %s.', Html::filename($filename)), $icon_success;
	} catch (Throwable $ex) {
		DebugBar::addThrowable($ex);

		echo '<br>', I18N::translate('The file %s could not be created.', Html::filename($filename)), $icon_failure;
	}
}

echo '</li>';

// The wiki tells people how to customize webtrees by modifying various files.
// Create a backup of these, just in case the user forgot!
try {
	copy('app/GedcomCode/GedcomCode/Rela.php', WT_DATA_DIR . 'GedcomCodeRela' . date('-Y-m-d') . '.php');
	copy('app/GedcomTag.php', WT_DATA_DIR . 'GedcomTag' . date('-Y-m-d') . '.php');
} catch (Throwable $ex) {
	DebugBar::addThrowable($ex);

	// No problem if we cannot do this.
}

////////////////////////////////////////////////////////////////////////////////
// Download a .ZIP file containing the new code
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to…; %s is a URL. */ I18N::translate('Download %s…', Html::filename($download_url));

$zip_file   = basename($download_url);
$zip_dir    = basename($download_url, '.zip');
$zip_stream = fopen(WT_DATA_DIR . $zip_file, 'w');

$start_time = microtime(true);

$client   = new Client();
$response = $client->get($download_url);
$stream   = $response->getBody();
while (!$stream->eof()) {
	fwrite($zip_stream, $stream->read(8192));
}
$stream->close();
fclose($zip_stream);
$zip_size = filesize(WT_DATA_DIR . $zip_file);

$end_time = microtime(true);

echo '<br>', /* I18N: %1$s is a number of KB, %2$s is a (fractional) number of seconds */ I18N::translate('%1$s KB were downloaded in %2$s seconds.', I18N::number($zip_size / 1024), I18N::number($end_time - $start_time, 2));
if ($zip_size) {
	echo $icon_success;
} else {
	echo $icon_failure;
	// Guess why we might have failed...
	if (preg_match('/^https:/', $download_url) && !in_array('ssl', stream_get_transports())) {
		echo '<br>', /* I18N: http://en.wikipedia.org/wiki/Https */ I18N::translate('This server does not support secure downloads using HTTPS.');
	}
}
echo '</li>';

// Mount the various filesystems
$zip_filesystem  = new Filesystem(new ZipArchiveAdapter(WT_DATA_DIR . $zip_file, null, 'webtrees'));
$data_filesystem = new Filesystem(new Local(WT_DATA_DIR));
$app_filesystem  = new Filesystem(new Local(__DIR__));

////////////////////////////////////////////////////////////////////////////////
// Unzip the file - this checks we have enough free disk space, that the .zip
// file is valid, etc.
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to…; %s is a .ZIP file. */ I18N::translate('Unzip %s to a temporary folder…', Html::filename(basename($download_url)));

try {
	$count      = 0;
	$start_time = microtime(true);

	foreach ($zip_filesystem->listContents('/', true) as $file) {
		if ($file['type'] === 'file') {
			$data_filesystem->put($zip_dir . '/' . $file['path'], $zip_filesystem->get($file['path']));
			$count++;
		}
	}

	$end_time = microtime(true);

	echo '<br>', /* I18N: …from the .ZIP file, %2$s is a (fractional) number of seconds */ I18N::plural('%1$s file was extracted in %2$s seconds.', '%1$s files were extracted in %2$s seconds.', $count, $count, I18N::number($end_time - $start_time, 2)), $icon_success;

	echo '</li>';
} catch (Throwable $ex) {
	DebugBar::addThrowable($ex);

	echo '<br>', I18N::translate('An error occurred when unzipping the file.'), $icon_failure;
	echo '<br>', $ex->getMessage();
	echo '</li></ul></form>';

	return;
}

////////////////////////////////////////////////////////////////////////////////
// Take the site offline first
////////////////////////////////////////////////////////////////////////////////

echo '<li>', I18N::translate('Place the website offline, by creating the file %s…', $lock_file);

try {
	$data_filesystem->put($lock_file, $lock_file_text);
	echo '<br>', I18N::translate('The file %s has been created.', Html::filename($lock_file)), $icon_success;
} catch (Throwable $ex) {
	DebugBar::addThrowable($ex);

	echo '<br>', I18N::translate('The file %s could not be created.', Html::filename($lock_file)), $icon_failure;
}

echo '</li>';

////////////////////////////////////////////////////////////////////////////////
// Copy files
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to… */ I18N::translate('Copy files…');

try {
	$count      = 0;
	$start_time = microtime(true);

	foreach ($zip_filesystem->listContents('/', true) as $file) {
		if ($file['type'] === 'file') {
			$app_filesystem->put($file['path'], $data_filesystem->get($zip_dir . '/' . $file['path']));
			$data_filesystem->delete($zip_dir . '/' . $file['path']);
			$count++;
		}
	}

	$end_time = microtime(true);

	echo '<br>', /* I18N: …from the .ZIP file, %2$s is a (fractional) number of seconds */ I18N::plural('%1$s file was extracted in %2$s seconds.', '%1$s files were extracted in %2$s seconds.', $count, $count, I18N::number($end_time - $start_time, 2)), $icon_success;
} catch (Throwable $ex) {
	DebugBar::addThrowable($ex);

	echo '<br>', I18N::translate('The file %s could not be updated.', Html::filename($file['path'])), $icon_failure;
	echo '</li></ul></form>';
	echo '<p class="error">', I18N::translate('To complete the upgrade, you should install the files manually.'), '</p>';
	echo '<p>', I18N::translate('The new files are currently located in the folder %s.', Html::filename(WT_DATA_DIR . $zip_dir)), '</p>';
	echo '<p>', I18N::translate('Copy these files to the folder %s, replacing any that have the same name.', Html::filename(WT_ROOT)), '</p>';
	echo '<p>', I18N::translate('To prevent visitors from accessing the website while you are in the middle of copying files, you can temporarily create a file %s on the server. If it contains a message, it will be displayed to visitors.', Html::filename($lock_file)), '</p>';

	return;
}

echo '</li>';

////////////////////////////////////////////////////////////////////////////////
// All done - put the site back online
////////////////////////////////////////////////////////////////////////////////

echo '<li>', I18N::translate('Place the website online, by deleting the file %s…', Html::filename($lock_file));

if ($data_filesystem->delete($lock_file)) {
	echo '<br>', I18N::translate('The file %s has been deleted.', Html::filename($lock_file)), $icon_success;
} else {
	echo '<br>', I18N::translate('The file %s could not be deleted.', Html::filename($lock_file)), $icon_failure;
}

echo '</li>';

////////////////////////////////////////////////////////////////////////////////
// Clean up
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to… */ I18N::translate('Delete temporary files…');

if ($data_filesystem->deleteDir($zip_dir)) {
	echo '<br>', I18N::translate('The folder %s has been deleted.', Html::filename(WT_DATA_DIR . $zip_dir)), $icon_success;
} else {
	echo '<br>', I18N::translate('The folder %s could not be deleted.', Html::filename(WT_DATA_DIR . $zip_dir)), $icon_failure;
}

if ($data_filesystem->deleteDir('cache')) {
	echo '<br>', I18N::translate('The folder %s has been deleted.', Html::filename(WT_DATA_DIR . 'cache')), $icon_success;
} else {
	echo '<br>', I18N::translate('The folder %s could not be deleted.', Html::filename(WT_DATA_DIR . 'cache')), $icon_failure;
}

if ($data_filesystem->delete($zip_file)) {
	echo '<br>', I18N::translate('The file %s has been deleted.', Html::filename(WT_DATA_DIR . $zip_file)), $icon_success;
} else {
	echo '<br>', I18N::translate('The file %s could not be deleted.', Html::filename(WT_DATA_DIR . $zip_file)), $icon_failure;
}

echo '</li>';
echo '</ul>';
echo '</form>';

echo '<p>', I18N::translate('The upgrade is complete.'), '</p>';

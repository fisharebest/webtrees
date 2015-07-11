<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
use PclZip;

define('WT_SCRIPT_NAME', 'admin_site_upgrade.php');

require './includes/session.php';

// Check for updates
$latest_version_txt = Functions::fetchLatestVersion();
if (preg_match('/^[0-9.]+\|[0-9.]+\|/', $latest_version_txt)) {
	list($latest_version, $earliest_version, $download_url) = explode('|', $latest_version_txt);
} else {
	// Cannot determine the latest version
	list($latest_version, $earliest_version, $download_url) = explode('|', '||');
}

$latest_version_html = '<span dir="ltr">' . $latest_version . '</span>';
$download_url_html   = '<b dir="auto"><a href="' . Filter::escapeHtml($download_url) . '">' . Filter::escapeHtml($download_url) . '</a></b>';

// Show a friendly message while the site is being upgraded
$lock_file           = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'offline.txt';
$lock_file_text      = I18N::translate('This website is being upgraded.  Try again in a few minutes.') . PHP_EOL . FunctionsDate::formatTimestamp(WT_TIMESTAMP) . /* I18N: Timezone - http://en.wikipedia.org/wiki/UTC */ I18N::translate('UTC');

// Success/failure indicators
$icon_success        = '<i class="icon-yes"></i>';
$icon_failure        = '<i class="icon-failure"></i>';

// Need confirmation for various actions
$continue            = Filter::post('continue', '1') && Filter::checkCsrf();
$modules_action      = Filter::post('modules', 'ignore|disable');
$themes_action       = Filter::post('themes', 'ignore|disable');

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
	echo '<p>', I18N::translate('This is the latest version of webtrees.  No upgrade is available.'), '</p>';

	return;
}

echo '<form method="post" action="admin_site_upgrade.php">';
echo Filter::getCsrf();

if ($continue) {
	echo '<input type="hidden" name="continue" value="1">';
	echo '<p>', I18N::translate('It can take several minutes to download and install the upgrade.  Be patient.'), '</p>';
} else {
	echo '<p>', I18N::translate('A new version of webtrees is available.'), '</p>';
	echo '<p>', I18N::translate('Depending on your server configuration, you may be able to upgrade automatically.'), '</p>';
	echo '<p>', I18N::translate('It can take several minutes to download and install the upgrade.  Be patient.'), '</p>';
	echo '<button type="submit" name="continue" value="1">', /* I18N: %s is a version number, such as 1.2.3 */ I18N::translate('Upgrade to webtrees %s.', $latest_version_html), '</button>';
	echo '</form>';

	return;
}

echo '<ul>';

////////////////////////////////////////////////////////////////////////////////
// Cannot upgrade until pending changes are accepted/rejected
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...] */ I18N::translate('Check for pending changes…');

$changes = Database::prepare("SELECT 1 FROM `##change` WHERE status='pending' LIMIT 1")->fetchOne();

if ($changes) {
	echo '<br>', I18N::translate('You should accept or reject all pending changes before upgrading.'), $icon_failure;
	echo '<br><button onclick="window.open(\'edit_changes.php\',\'_blank\', chan_window_specs); return false;"">', I18N::translate('Pending changes'), '</button>';
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
	switch ($module->getName()) {
	case 'GEDFact_assistant':
	case 'ahnentafel_report':
	case 'batch_update':
	case 'bdm_report':
	case 'birth_report':
	case 'cemetery_report':
	case 'change_report':
	case 'charts':
	case 'ckeditor':
	case 'clippings':
	case 'death_report':
	case 'descendancy':
	case 'descendancy_report':
	case 'extra_info':
	case 'fact_sources':
	case 'families':
	case 'family_group_report':
	case 'family_nav':
	case 'faq':
	case 'gedcom_block':
	case 'gedcom_favorites':
	case 'gedcom_news':
	case 'gedcom_stats':
	case 'googlemap':
	case 'html':
	case 'individual_ext_report':
	case 'individual_report':
	case 'individuals':
	case 'lightbox':
	case 'logged_in':
	case 'login_block':
	case 'marriage_report':
	case 'media':
	case 'missing_facts_report':
	case 'notes':
	case 'occupation_report':
	case 'page_menu':
	case 'pedigree_report':
	case 'personal_facts':
	case 'random_media':
	case 'recent_changes':
	case 'relative_ext_report':
	case 'relatives':
	case 'review_changes':
	case 'sitemap':
	case 'sources_tab':
	case 'stories':
	case 'theme_select':
	case 'todays_events':
	case 'todo':
	case 'top10_givnnames':
	case 'top10_pageviews':
	case 'top10_surnames':
	case 'tree':
	case 'upcoming_events':
	case 'user_blog':
	case 'user_favorites':
	case 'user_messages':
	case 'user_welcome':
	case 'yahrzeit':
		break;
	default:
		switch ($modules_action) {
		case 'disable':
			Database::prepare(
				"UPDATE `##module` SET status = 'disabled' WHERE module_name = ?"
			)->execute(array($module->getName()));
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
	echo '<br>', '<button type="submit" name="modules" value="ignore">', /* I18N: Ignore the warnings, and [...] */ I18N::translate('Upgrade anyway'), '</button> — ', I18N::translate('Caution: old modules may not work, or they may prevent webtrees from working.');
	echo '</li></ul></form>';

	return;
} else {
	if ($modules_action != 'ignore') {
		echo '<br>', I18N::translate('No custom modules are enabled.'), $icon_success;
	}
	echo '<input type="hidden" name="modules" value="', Filter::escapeHtml($modules_action), '">';
}

echo '</li>';

////////////////////////////////////////////////////////////////////////////////
// Custom themes may not work with the new version.
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...] */ I18N::translate('Check for custom themes…');

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
			" OR    EXISTS (SELECT 1 FROM `##gedcom_setting` WHERE setting_name='THEME_DIR' AND setting_value=?)" .
			" OR    EXISTS (SELECT 1 FROM `##user_setting`   WHERE setting_name='theme'     AND setting_value=?)"
		)->execute(array($theme_id, $theme_id, $theme_id))->fetchOne();
		if ($theme_used) {
			switch ($themes_action) {
			case 'disable':
				Database::prepare(
					"DELETE FROM `##site_setting`   WHERE setting_name = 'THEME_DIR' AND setting_value = ?"
				)->execute(array($theme_id));
				Database::prepare(
					"DELETE FROM `##gedcom_setting` WHERE setting_name = 'THEME_DIR' AND setting_value = ?"
				)->execute(array($theme_id));
				Database::prepare(
					"DELETE FROM `##user_setting`   WHERE setting_name = 'theme'     AND setting_value = ?"
				)->execute(array($theme_id));
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
	echo '<input type="hidden" name="themes" value="', Filter::escapeHtml($themes_action), '">';
}

echo '</li>';

////////////////////////////////////////////////////////////////////////////////
// Make a backup of genealogy data
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...] */ I18N::translate('Export all the family trees to GEDCOM files…');

foreach (Tree::getAll() as $tree) {
	reset_timeout();
	$filename = WT_DATA_DIR . $tree->getName() . date('-Y-m-d') . '.ged';

	try {
		// To avoid partial trees on timeout/diskspace/etc, write to a temporary file first
		$stream = fopen($filename . '.tmp', 'w');
		$tree->exportGedcom($stream);
		fclose($stream);
		rename($filename . '.tmp', $filename);
		echo '<br>', I18N::translate('The family tree has been exported to %s.', Html::filename($filename)), $icon_success;
	} catch (\ErrorException $ex) {
		echo '<br>', I18N::translate('The file %s could not be created.', Html::filename($filename)), $icon_failure;
	}
}

echo '</li>';

////////////////////////////////////////////////////////////////////////////////
// Download a .ZIP file containing the new code
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...]; %s is a URL. */ I18N::translate('Download %s…', Html::filename($download_url_html));

$zip_file   = WT_DATA_DIR . basename($download_url);
$zip_dir    = WT_DATA_DIR . basename($download_url, '.zip');
$zip_stream = fopen($zip_file, 'w');
reset_timeout();
$start_time = microtime(true);
File::fetchUrl($download_url, $zip_stream);
$end_time   = microtime(true);
$zip_size   = filesize($zip_file);
fclose($zip_stream);

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

////////////////////////////////////////////////////////////////////////////////
// Unzip the file - this checks we have enough free disk space, that the .zip
// file is valid, etc.
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...]; %s is a .ZIP file. */ I18N::translate('Unzip %s to a temporary folder…', Html::filename(basename($download_url)));

File::delete($zip_dir);
File::mkdir($zip_dir);

$archive = new PclZip($zip_file);

$res = $archive->properties();
if (!is_array($res) || $res['status'] != 'ok') {
	echo '<br>', I18N::translate('An error occurred when unzipping the file.'), $icon_failure;
	echo '<br>', $archive->errorInfo(true);
	echo '</li></ul></form>';

	return;
}

$num_files = $res['nb'];

reset_timeout();
$start_time = microtime(true);
$res        = $archive->extract(
	\PCLZIP_OPT_PATH, $zip_dir,
	\PCLZIP_OPT_REMOVE_PATH, 'webtrees',
	\PCLZIP_OPT_REPLACE_NEWER
);
$end_time = microtime(true);

if (is_array($res)) {
	foreach ($res as $result) {
		// Note that we're stripping the initial "webtrees/", so the top folder will fail.
		if ($result['status'] != 'ok' && $result['filename'] != 'webtrees/') {
			echo '<br>', I18N::translate('An error occurred when unzipping the file.'), $icon_failure;
			echo '<pre>', $result['status'], '</pre>';
			echo '<pre>', $result['filename'], '</pre>';
			echo '</li></ul></form>';

			return;
		}
	}
	echo '<br>', /* I18N: [...] from the .ZIP file, %2$s is a (fractional) number of seconds */ I18N::plural('%1$s file was extracted in %2$s seconds.', '%1$s files were extracted in %2$s seconds.', count($res), count($res), I18N::number($end_time - $start_time, 2)), $icon_success;
} else {
	echo '<br>', I18N::translate('An error occurred when unzipping the file.'), $icon_failure;
	echo '<pre>', $archive->errorInfo(true), '</pre>';
	echo '</li></ul></form>';

	return;
}

echo '</li>';

////////////////////////////////////////////////////////////////////////////////
// This is it - take the site offline first
////////////////////////////////////////////////////////////////////////////////

echo '<li>', I18N::translate('Check file permissions…');

reset_timeout();
$iterator = new \RecursiveDirectoryIterator($zip_dir);
$iterator->setFlags(\RecursiveDirectoryIterator::SKIP_DOTS);
foreach (new \RecursiveIteratorIterator($iterator) as $file) {
	$file = WT_ROOT . substr($file, strlen($zip_dir) + 1);
	if (file_exists($file) && (!is_readable($file) || !is_writable($file))) {
		echo '<br>', I18N::translate('The file %s could not be updated.', Html::filename($file)), $icon_failure;
		echo '</li></ul>';
		echo '<p class="error">', I18N::translate('To complete the upgrade, you should install the files manually.'), '</p>';
		echo '<p>', I18N::translate('The new files are currently located in the folder %s.', Html::filename($zip_dir)), '</p>';
		echo '<p>', I18N::translate('Copy these files to the folder %s, replacing any that have the same name.', Html::filename(WT_ROOT)), '</p>';
		echo '<p>', I18N::translate('To prevent visitors from accessing the website while you are in the middle of copying files, you can temporarily create a file %s on the server.  If it contains a message, it will be displayed to visitors.', Html::filename($lock_file)), '</p>';

		return;
	}
}

echo '<br>', I18N::translate('All files have read and write permission.'), $icon_success;

echo '</li>';

////////////////////////////////////////////////////////////////////////////////
// This is it - take the site offline first
////////////////////////////////////////////////////////////////////////////////

echo '<li>', I18N::translate('Place the website offline, by creating the file %s…', $lock_file);

try {
	file_put_contents($lock_file, $lock_file_text);
	echo '<br>', I18N::translate('The file %s has been created.', Html::filename($lock_file)), $icon_success;
} catch (\ErrorException $ex) {
	echo '<br>', I18N::translate('The file %s could not be created.', Html::filename($lock_file)), $icon_failure;
}

echo '</li>';

////////////////////////////////////////////////////////////////////////////////
// Copy files
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...] */ I18N::translate('Copy files…');

// The wiki tells people how to customize webtrees by modifying various files.
// Create a backup of these, just in case the user forgot!
try {
	copy('app/GedcomCode/GedcomCode/Rela.php', WT_DATA_DIR . 'GedcomCodeRela' . date('-Y-m-d') . '.php');
	copy('app/GedcomTag.php', WT_DATA_DIR . 'GedcomTag' . date('-Y-m-d') . '.php');
} catch (\ErrorException $ex) {
	// No problem if we cannot do this.
}

reset_timeout();
$start_time = microtime(true);
$res        = $archive->extract(
	\PCLZIP_OPT_PATH, WT_ROOT,
	\PCLZIP_OPT_REMOVE_PATH, 'webtrees',
	\PCLZIP_OPT_REPLACE_NEWER
);
$end_time = microtime(true);

if (is_array($res)) {
	foreach ($res as $result) {
		// Note that most of the folders will already exist, so it is not an error if we cannot create them
		if ($result['status'] != 'ok' && !substr($result['filename'], -1) == '/') {
			echo '<br>', I18N::translate('The file %s could not be created.', Html::filename($result['filename'])), $icon_failure;
		}
	}
	echo '<br>', /* I18N: [...] from the .ZIP file, %2$s is a (fractional) number of seconds */ I18N::plural('%1$s file was extracted in %2$s seconds.', '%1$s files were extracted in %2$s seconds.', count($res), count($res), I18N::number($end_time - $start_time, 2)), $icon_success;
} else {
	echo '<br>', I18N::translate('An error occurred when unzipping the file.'), $icon_failure;
	echo '</li></ul></form>';

	return;
}

echo '</li>';

////////////////////////////////////////////////////////////////////////////////
// All done - put the site back online
////////////////////////////////////////////////////////////////////////////////

echo '<li>', I18N::translate('Place the website online, by deleting the file %s…', Html::filename($lock_file));

if (File::delete($lock_file)) {
	echo '<br>', I18N::translate('The file %s has been deleted.', Html::filename($lock_file)), $icon_success;
} else {
	echo '<br>', I18N::translate('The file %s could not be deleted.', Html::filename($lock_file)), $icon_failure;
}

echo '</li>';

////////////////////////////////////////////////////////////////////////////////
// Clean up
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...] */ I18N::translate('Delete temporary files…');

reset_timeout();
if (File::delete($zip_dir)) {
	echo '<br>', I18N::translate('The folder %s has been deleted.', Html::filename($zip_dir)), $icon_success;
} else {
	echo '<br>', I18N::translate('The folder %s could not be deleted.', Html::filename($zip_dir)), $icon_failure;
}

if (File::delete($zip_file)) {
	echo '<br>', I18N::translate('The file %s has been deleted.', Html::filename($zip_file)), $icon_success;
} else {
	echo '<br>', I18N::translate('The file %s could not be deleted.', Html::filename($zip_file)), $icon_failure;
}

echo '</li>';
echo '</ul>';

echo '<p>', I18N::translate('The upgrade is complete.'), '</p>';

/**
 * Reset the time limit, as timeouts in this script could leave the upgrade incomplete.
 */
function reset_timeout() {
	if (!ini_get('safe_mode') && strpos(ini_get('disable_functions'), 'set_time_limit') === false) {
		set_time_limit(ini_get('max_execution_time'));
	}
}

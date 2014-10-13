<?php
// Welcome page for the administration module
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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

define('WT_SCRIPT_NAME', 'admin_site_upgrade.php');

require './includes/session.php';
require WT_ROOT . 'library/pclzip.lib.php'; // TODO - rename and use autoloading

// Check for updates
$latest_version_txt = fetch_latest_version();
if (preg_match('/^[0-9.]+\|[0-9.]+\|/', $latest_version_txt)) {
	list($latest_version, $earliest_version, $download_url) = explode('|', $latest_version_txt);
} else {
	// Cannot determine the latest version
	list($latest_version, $earliest_version, $download_url) = explode('|', '||');
}

$latest_version_html = '<span dir="ltr">' . $latest_version . '</span>';
$download_url_html   = '<b dir="auto"><a href="' . WT_Filter::escapeHtml($download_url) . '">' . WT_Filter::escapeHtml($download_url) . '</a></b>';

// Show a friendly message while the site is being upgraded
$lock_file           = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'offline.txt';
$lock_file_html      = '<span dir="ltr">' . WT_Filter::escapeHtml($lock_file) . '</span>';
$lock_file_text      = WT_I18N::translate('This site is being upgraded.  Try again in a few minutes.') . PHP_EOL . format_timestamp(WT_TIMESTAMP) .  WT_I18N::translate('UTC');

// Success/failure indicators
$icon_success        = '<i class="icon-yes"></i>';
$icon_failure        = '<i class="icon-failure"></i>';

// Need confirmation for various actions
$continue            = WT_Filter::post('continue', '1') && WT_Filter::checkCsrf();
$modules_action      = WT_Filter::post('modules',  'ignore|disable');
$themes_action       = WT_Filter::post('themes',   'ignore|disable');

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(WT_I18N::translate('Upgrade wizard'))
	->pageHeader();

// Flush output as it happens - only effective on some webserver configurations.
ob_implicit_flush(true);
if (ob_get_level()) {
	ob_end_flush();
}

echo '<h2>', $controller->getPageTitle(), '</h2>';

if ($latest_version == '') {
	echo '<p>', WT_I18N::translate('No upgrade information is available.'), '</p>';
	exit;
}

if (version_compare(WT_VERSION, $latest_version) >= 0) {
	echo '<p>', WT_I18N::translate('This is the latest version of webtrees.  No upgrade is available.'), '</p>';
	exit;
}

echo '<form method="POST" action="admin_site_upgrade.php">';
echo WT_Filter::getCsrf();

if ($continue) {
	echo '<input type="hidden" name="continue" value="1">';
	echo '<p>', WT_I18N::translate('It can take several minutes to download and install the upgrade.  Be patient.'), '</p>';
} else {
	echo '<p>', WT_I18N::translate('A new version of webtrees is available.'), '</p>';
	echo '<p>', WT_I18N::translate('Depending on your server configuration, you may be able to upgrade automatically.'), '</p>';
	echo '<p>', WT_I18N::translate('It can take several minutes to download and install the upgrade.  Be patient.'), '</p>';
	echo '<button type="submit" name="continue" value="1">', /* I18N: %s is a version number, such as 1.2.3 */ WT_I18N::translate('Upgrade to webtrees %s', $latest_version_html), '</button>';
	echo '</form>';
	exit;
}

echo '<ul>';

////////////////////////////////////////////////////////////////////////////////
// Cannot upgrade until pending changes are accepted/rejected
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...] */ WT_I18N::translate('Check for pending changes…');

$changes = WT_DB::prepare( "SELECT 1 FROM `##change` WHERE status='pending' LIMIT 1")->fetchOne();

if ($changes) {
	echo '<br>', WT_I18N::translate('You should accept or reject all pending changes before upgrading.'), $icon_failure;
	echo '<br><button onclick="window.open(\'edit_changes.php\',\'_blank\', chan_window_specs); return false;"">', WT_I18N::translate('Pending changes'), '</button>';
	echo '</li></ul></form>';
	exit;
} else {
	echo '<br>', WT_I18N::translate('There are no pending changes.'), $icon_success;
}

echo '</li>'; flush();

////////////////////////////////////////////////////////////////////////////////
// Custom modules may not work with the new version.
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...] */ WT_I18N::translate('Check for custom modules…');

$custom_modules = false;
foreach (WT_Module::getActiveModules() as $module) {
	switch($module->getName()) {
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
			WT_DB::prepare(
				"UPDATE `##module` SET status = 'disabled' WHERE module_name = ?"
			)->execute(array($module->getName()));
			break;
		case 'ignore':
			echo '<br>', WT_I18N::translate('Custom module'), ' — ', WT_MODULES_DIR, $module->getName(), ' — ', $module->getTitle(), $icon_success;
			break;
		default:
			echo '<br>', WT_I18N::translate('Custom module'), ' — ', WT_MODULES_DIR, $module->getName(), ' — ', $module->getTitle(), $icon_failure;
			$custom_modules = true;
			break;
		}
	}
}
if ($custom_modules) {
	echo '<br>', WT_I18N::translate('You should consult the module’s author to confirm compatibility with this version of webtrees.');
	echo '<br>', '<button type="submit" name="modules" value="disable">', WT_I18N::translate('Disable these modules'), '</button> — ', WT_I18N::translate('You can re-enable these modules after the upgrade.');
	echo '<br>', '<button type="submit" name="modules" value="ignore">', /* I18N: Ignore the warnings, and [...] */ WT_I18N::translate('Upgrade anyway'), '</button> — ', WT_I18N::translate('Caution: old modules may not work, or they may prevent webtrees from working.');
	echo '</li></ul></form>';
	exit;
} else {
	if ($modules_action != 'ignore') {
		echo '<br>', WT_I18N::translate('No custom modules are enabled.'), $icon_success;
	}
	echo '<input type="hidden" name="modules" value="', WT_Filter::escapeHtml($modules_action), '">';
}

echo '</li>'; flush();

////////////////////////////////////////////////////////////////////////////////
// Custom themes may not work with the new version.
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...] */ WT_I18N::translate('Check for custom themes…');

$custom_themes = false;
foreach (get_theme_names() as $theme_name => $theme_folder) {
	switch($theme_folder) {
	case 'clouds':
	case 'colors':
	case 'fab':
	case 'minimal':
	case 'webtrees':
	case 'xenea':
		break;
	default:
		$theme_used = WT_DB::prepare(
			"SELECT EXISTS (SELECT 1 FROM `##site_setting`   WHERE setting_name='THEME_DIR' AND setting_value=?)" .
			" OR    EXISTS (SELECT 1 FROM `##gedcom_setting` WHERE setting_name='THEME_DIR' AND setting_value=?)" .
			" OR    EXISTS (SELECT 1 FROM `##user_setting`   WHERE setting_name='theme'     AND setting_value=?)"
		)->execute(array($theme_folder, $theme_folder, $theme_folder))->fetchOne();
		if ($theme_used) {
			switch ($themes_action) {
			case 'disable':
				WT_DB::prepare(
					"DELETE FROM `##site_setting`   WHERE setting_name = 'THEME_DIR' AND setting_value = ?"
				)->execute(array($theme_folder));
				WT_DB::prepare(
					"DELETE FROM `##gedcom_setting` WHERE setting_name = 'THEME_DIR' AND setting_value = ?"
				)->execute(array($theme_folder));
				WT_DB::prepare(
					"DELETE FROM `##user_setting`   WHERE setting_name = 'theme'     AND setting_value = ?"
				)->execute(array($theme_folder));
				break;
			case 'ignore':
				echo '<br>', WT_I18N::translate('Custom theme'), ' — ', $theme_folder , ' — ', $theme_name, $icon_success;
				break;
			default:
				echo '<br>', WT_I18N::translate('Custom theme'), ' — ', $theme_folder , ' — ', $theme_name, $icon_failure;
				$custom_themes = true;
				break;
			}
		}
		break;
	}
}

if ($custom_themes) {
	echo '<br>', WT_I18N::translate('You should consult the theme’s author to confirm compatibility with this version of webtrees.');
	echo '<br>', '<button type="submit" name="themes" value="disable">', WT_I18N::translate('Disable these themes'), '</button> — ', WT_I18N::translate('You can re-enable these themes after the upgrade.');
	echo '<br>', '<button type="submit" name="themes" value="ignore">', WT_I18N::translate('Upgrade anyway'), '</button> — ', WT_I18N::translate('Caution: old themes may not work, or they may prevent webtrees from working.');
	echo '</li></ul></form>';
	exit;
} else {
	if ($themes_action != 'ignore') {
		echo '<br>', WT_I18N::translate('No custom themes are enabled.'), $icon_success;
	}
	echo '<input type="hidden" name="themes" value="', WT_Filter::escapeHtml($themes_action), '">';
}

echo '</li>'; flush();

////////////////////////////////////////////////////////////////////////////////
// Make a backup of genealogy data
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...] */ WT_I18N::translate('Export all family trees to GEDCOM files…');

foreach (WT_Tree::getAll() as $tree) {
	reset_timeout();
	$filename = WT_DATA_DIR . $tree->tree_name . date('-Y-m-d') . '.ged';
	if ($tree->exportGedcom($filename)) {
		echo '<br>', WT_I18N::translate('Family tree exported to %s.', '<span dir="ltr">' . $filename . '</span>'), $icon_success;
	} else {
		echo '<br>', WT_I18N::translate('Unable to create %s.  Check the permissions.', '<span dir="ltr">' . $filename . '</span>'), $icon_failure;
	}
	flush();
}

echo '</li>';

////////////////////////////////////////////////////////////////////////////////
// Download a .ZIP file containing the new code
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...]; %s is a URL. */ WT_I18N::translate('Download %s…', $download_url_html);

$zip_file   = WT_DATA_DIR . basename($download_url);
$zip_dir    = WT_DATA_DIR . basename($download_url, '.zip');
$zip_stream = fopen($zip_file, 'w');
reset_timeout();
$start_time = microtime(true);
WT_File::fetchUrl($download_url, $zip_stream);
$end_time   = microtime(true);
$zip_size   = filesize($zip_file);
fclose($zip_stream);

echo '<br>', /* I18N: %1$s is a number of KB, %2$s is a (fractional) number of seconds */ WT_I18N::translate('%1$s KB were downloaded in %2$s seconds.', WT_I18N::number($zip_size / 1024), WT_I18N::number($end_time - $start_time, 2));
if ($zip_size) {
	echo $icon_success;
} else {
	echo $icon_failure;
	// Guess why we might have failed...
	if (preg_match('/^https:/', $download_url) && !in_array('ssl', stream_get_transports())) {
		echo '<br>', /* I18N: http://en.wikipedia.org/wiki/Https */ WT_I18N::translate('This server does not support secure downloads using HTTPS.');
	}
}

echo '</li>'; flush();

////////////////////////////////////////////////////////////////////////////////
// Unzip the file - this checks we have enough free disk space, that the .zip
// file is valid, etc.
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...]; %s is a .ZIP file. */ WT_I18N::translate('Unzip %s to a temporary folder…', basename($download_url));

WT_File::delete($zip_dir);
WT_File::mkdir($zip_dir);

$archive = new PclZip($zip_file);

$res = $archive->properties();
if (!is_array($res) || $res['status'] != 'ok') {
	echo '<br>', WT_I18N::translate('An error occurred when unzipping the file.'), $icon_failure;
	echo '</li></ul></form>';
	exit;
}

$num_files = $res['nb'];

reset_timeout();
$start_time = microtime(true);
$res = $archive->extract(
	PCLZIP_OPT_PATH,         $zip_dir,
	PCLZIP_OPT_REMOVE_PATH, 'webtrees',
	PCLZIP_OPT_REPLACE_NEWER
);
$end_time = microtime(true);

if (is_array($res)) {
	foreach ($res as $result) {
		// Note that we're stripping the initial "webtrees/", so the top folder will fail.
		if ($result['status'] != 'ok' && $result['filename'] != 'webtrees/') {
			echo '<br>', WT_I18N::translate('An error occurred when unzipping the file.'), $icon_failure;
			echo '<pre>', $result['status'], '</pre>';
			echo '<pre>', $result['filename'], '</pre>';
			echo '</li></ul></form>';
			exit;
		}
	}
	echo '<br>', /* I18N: [...] from the .ZIP file, %2$s is a (fractional) number of seconds */ WT_I18N::plural('%1$s file was extracted in %2$s seconds.', '%1$s files were extracted in %2$s seconds.', count($res), count($res), WT_I18N::number($end_time - $start_time, 2)), $icon_success;
} else {
	echo '<br>', WT_I18N::translate('An error occurred when unzipping the file.'), $icon_failure;
	echo '<pre>', $archive->errorInfo(true), '</pre>';
	echo '</li></ul></form>';
	exit;
}

echo '</li>'; flush();

////////////////////////////////////////////////////////////////////////////////
// This is it - take the site offline first
////////////////////////////////////////////////////////////////////////////////

echo '<li>', WT_I18N::translate('Check file permissions…');

reset_timeout();
$iterator = new RecursiveDirectoryIterator($zip_dir);
$iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
foreach (new RecursiveIteratorIterator($iterator) as $file) {
	$file = WT_ROOT . substr($file, strlen($zip_dir) + 1);
	if (file_exists($file) && (!is_readable($file) || !is_writable($file))) {
		echo '<br>', WT_I18N::translate('The file %s could not be updated.', '<span dir="ltr">' . $file . '</span>'), $icon_failure;
		echo '</li></ul>';
		echo '<p class="error">', WT_I18N::translate('To complete the upgrade, you should install the files manually.'), '</p>';
		echo '<p>', WT_I18N::translate('The new files are currently located in the folder %s.', '<b dir="ltr">' . $zip_dir . DIRECTORY_SEPARATOR . '</b>'), '</p>';
		echo '<p>', WT_I18N::translate('Copy these files to the folder %s, replacing any that have the same name.', '<b dir="ltr">' . WT_ROOT . '</b>'), '</p>';
		echo '<p>', WT_I18N::translate('To prevent visitors from accessing the site while you are in the middle of copying files, you can temporarily create a file %s on the server.  If it contains a message, it will be displayed to visitors.', '<b>' . $lock_file_html . '</b>'), '</p>';
		exit;
	}
}

echo '<br>', WT_I18N::translate('All files have read and write permission.'), $icon_success;

echo '</li>'; flush();

////////////////////////////////////////////////////////////////////////////////
// This is it - take the site offline first
////////////////////////////////////////////////////////////////////////////////

echo '<li>', WT_I18N::translate('Place the site offline, by creating the file %s…', $lock_file_html);

@file_put_contents($lock_file, $lock_file_text);
if (@file_get_contents($lock_file) != $lock_file_text) {
	echo '<br>', WT_I18N::translate('The file %s could not be created.', '<span dir="ltr">' . $lock_file . '</span>'), $icon_failure;
} else {
	echo '<br>', WT_I18N::translate('The file %s was created.', '<span dir="ltr">' . $lock_file . '</span>'), $icon_success;
}

echo '</li>'; flush();

////////////////////////////////////////////////////////////////////////////////
// Copy files
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...] */ WT_I18N::translate('Copy files…');

// The wiki tells people how to customize webtrees by modifying various files.
// Create a backup of these, just in case the user forgot!
@copy('library/WT/Gedcom/Code/Rela.php', 'library/WT/Gedcom/Code/Rela' . date('-Y-m-d') . '.php');
@copy('library/WT/Gedcom/Tag.php', 'library/WT/Gedcom/Tag' . date('-Y-m-d') . '.php');

reset_timeout();
$start_time = microtime(true);
$res = $archive->extract(
	PCLZIP_OPT_PATH,        WT_ROOT,
	PCLZIP_OPT_REMOVE_PATH, 'webtrees',
	PCLZIP_OPT_REPLACE_NEWER
);
$end_time = microtime(true);

if (is_array($res)) {
	foreach ($res as $result) {
		// Note that most of the folders will already exist, so it is not an error if we cannot create them
		if ($result['status'] != 'ok' && !substr($result['filename'], -1) == '/') {
			echo '<br>', WT_I18N::translate('The file %s could not be created.', '<span dir="ltr">' . $result['filename'] . '</span>'), $icon_failure;
		}
	}
	echo '<br>', /* I18N: [...] from the .ZIP file, %2$s is a (fractional) number of seconds */ WT_I18N::plural('%1$s file was extracted in %2$s seconds.', '%1$s files were extracted in %2$s seconds.', count($res), count($res), WT_I18N::number($end_time - $start_time, 2)), $icon_success;
} else {
	echo '<br>', WT_I18N::translate('An error occurred when unzipping the file.'), $icon_failure;
	echo '</li></ul></form>';
	exit;
}

echo '</li>'; flush();

////////////////////////////////////////////////////////////////////////////////
// All done - put the site back online
////////////////////////////////////////////////////////////////////////////////

echo '<li>', WT_I18N::translate('Place the site online, by deleting the file %s…', $lock_file_html);

if (WT_File::delete($lock_file)) {
	echo '<br>', WT_I18N::translate('The file %s was deleted.', '<span dir="ltr">' . $lock_file . '</span>'), $icon_success;
} else {
	echo '<br>', WT_I18N::translate('The file %s could not be deleted.', '<span dir="ltr">' . $lock_file . '</span>'), $icon_failure;
}

echo '</li>'; flush();

////////////////////////////////////////////////////////////////////////////////
// Clean up
////////////////////////////////////////////////////////////////////////////////

echo '<li>', /* I18N: The system is about to [...] */ WT_I18N::translate('Delete temporary files…');

reset_timeout();
if (WT_File::delete($zip_dir)) {
	echo '<br>', WT_I18N::translate('The folder %s was deleted.', '<span dir="auto">' . $zip_dir . '</span>'), $icon_success;
} else {
	echo '<br>', WT_I18N::translate('The folder %s could not be deleted.', '<span dir="auto">' . $zip_dir . '</span>'), $icon_failure;
}

if (WT_File::delete($zip_file)) {
	echo '<br>', WT_I18N::translate('The file %s was deleted.', '<span dir="auto">' . $zip_file . '</span>'), $icon_success;
} else {
	echo '<br>', WT_I18N::translate('The file %s could not be deleted.', '<span dir="auto">' . $zip_file . '</span>'), $icon_failure;
}

echo '</li>';
echo '</ul>';

echo '<p>', WT_I18N::translate('The upgrade is complete.'), '</p>';

// Reset the time limit, as timeouts in this script could leave the upgrade incomplete.
function reset_timeout() {
	if (!ini_get('safe_mode') && strpos(ini_get('disable_functions'), 'set_time_limit')===false) {
		set_time_limit(ini_get('max_execution_time'));
	}
}

<?php
/**
 * PGV to webtrees transfer wizard
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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
 * @package webtrees
 * @version $Id: pgv_to_wt.php 9030 2010-07-07 21:54:31Z greg $
 */

define('WT_SCRIPT_NAME', 'pgv_to_wt.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

// We can only import into an empty system, so deny access if we have already created a gedcom or added users.
if (WT_GED_ID || get_user_count()>1) {
	header('Location: index.php');
	exit;
}

// Must be logged in as an admin
if (!WT_USER_IS_ADMIN) {
	header('Location: login.php?url='.WT_SCRIPT_NAME);
	exit;
}

print_header(i18n::translate('PhpGedView to webtrees transfer wizard'));

echo
	'<style type="text/css">
		#container {width: 70%; margin:15px auto; border: 1px solid gray; padding: 10px;}
		#container dl {margin:0 0 40px 25px;}
		#container dt {display:inline; width: 320px; font-weight:normal;}
		#container dd {color: #81A9CB; margin-bottom:20px;font-weight:bold;}
		#container p {color: #81A9CB; font-size: 14px; font-style: italic; font-weight:bold; padding: 0 5px 5px; align: top;
		h2 {color: #81A9CB;}
		.good {color: green;}
		.bad {color: red; font-weight: bold;}
		.indifferent {color: blue;}
	</style>';

$error='';
$warning='';
$PGV_PATH=safe_POST('PGV_PATH');

if ($PGV_PATH) {
	if (!is_dir($PGV_PATH) || !is_readable($PGV_PATH.'/config.php')) {
		$error=i18n::translate('The specified directory does not contain an installation of PhpGedView');
	} else {
		// Load the configuration settings
		$config_php=file_get_contents($PGV_PATH.'/config.php');
		// The easiest way to do this is to exec() the file - but not lines containing require or PHP tags
		$config_php=preg_replace(
			array(
				'/^\s*(include|require).*/',
				'/.*<\?php.*/',
				'/.*\?>.*/'
			), '', $config_php
		);
		eval($config_php);
		// $INDEX_DIRECTORY can be either absolute or relative to the PhpGedView root.
		if (preg_match('/^(\/|\\|[A-Z]:)/', $INDEX_DIRECTORY)) {
			$INDEX_DIRECTORY=realpath($INDEX_DIRECTORY);
		} else {
			$INDEX_DIRECTORY=realpath($PGV_PATH.'/'.$INDEX_DIRECTORY);
		}
		$wt_config=parse_ini_file(WT_ROOT.'data/config.ini.php');
		if ($DBHOST!=$wt_config['dbhost'] || $DBHOST!=$wt_config['dbhost']) {
			$error=i18n::translate('PhpGedView must use the same database as <b>webtrees</b>');
			unset($wt_config);
		} else {
			unset($wt_config);
			try {
				$PGV_VERSION=WT_DB::prepare(
					"SELECT site_setting_value FROM {$DBNAME}.{$TBLPREFIX}site_setting WHERE site_setting_name='PGV_SCHEMA_VERSION'"
				)->fetchOne();
				if ($PGV_VERSION<10) {
					$error=i18n::translate('The version of %s is too old', 'PhpGedView');
				} elseif ($PGV_VERSION>14) {
					$error=i18n::translate('The version of %s is too new', 'PhpGedView');
				} else {
					$IS_ADMIN=WT_DB::prepare(
						"SELECT setting_value FROM {$DBNAME}.{$TBLPREFIX}user_setting JOIN {$DBNAME}.{$TBLPREFIX}user USING (user_id) WHERE setting_name='canadmin' AND user_name=?"
					)->execute(array(WT_USER_NAME))->fetchOne();
					if (!$IS_ADMIN) {
						$error='Your username must exist in PhpGedView as an administrator';
					}
				}
			} catch (PDOException $ex) {
				$error=i18n::translate('The PhpGedView database configuration settings are bad: '.$ex);
			}
		}
	}
}

if ($error || empty($PGV_PATH)) {
	// Prompt for location of PhpGedView installation
	echo '<div id="container">';
	echo '<h2>', i18n::translate('PhpGedView to <b>webtrees</b> transfer wizard'), '</h2>';
	if ($error) {
		echo '<p class="bad">', $error, '</p>';
	}
	echo
		'<form action="', WT_SCRIPT_NAME, '" method="post">',
		'<p>', i18n::translate('Where is your PhpGedView installation?'), '</p>',
		'<dl>',
		'<dt>',i18n::translate('Installation directory'), '</dt>',
		'<dd><input type="text" name="PGV_PATH" size="40" value="'.htmlspecialchars($PGV_PATH).'"><dd>',
		'</dl>';
	// Get media options
	echo
		'<p>', i18n::translate('Media item options (select one):'), '</p>',
		'<dl>',
		'<dt>',i18n::translate('Use existing PGV media directory for <b>webtrees</b>'), '</dt>',
		'<dd>', edit_field_yes_no('media', get_gedcom_setting(WT_GED_ID, 'media')), '</dd>',
		'<dt>',i18n::translate('Copy media from PGV media directory to <b>webtrees</b> media directory'), '</dt>',
		'<dd>', edit_field_yes_no('media', get_gedcom_setting(WT_GED_ID, 'media')), '</dd>',
		'<dt>',i18n::translate('Move media from PGV media directory to <b>webtrees</b> media directory'), '</dt>',
		'<dd>', edit_field_yes_no('media', get_gedcom_setting(WT_GED_ID, 'media')), '</dd>',
		'</dl>';
	// Finish
	echo '<div class="center"><input type="submit" value="'.i18n::translate('next').'"></div>';
	echo '</form>';
	echo '</div>';
	exit;
}

// We have the info we need, and it has been validated.
WT_DB::prepare("START TRANSACTION")->execute();

////////////////////////////////////////////////////////////////////////////////

echo '<p>config.php => wt_site_setting ...</p>'; flush();
// TODO May need to set 'DATA_DIRECTORY' to $INDEX_DIRECTORY when dealing with media??
@set_site_setting('STORE_MESSAGES',                  $PGV_STORE_MESSAGES);
@set_site_setting('SMTP_SIMPLE_MAIL',                $PGV_SIMPLE_MAIL);
@set_site_setting('USE_REGISTRATION_MODULE',         $USE_REGISTRATION_MODULE);
@set_site_setting('REQUIRE_ADMIN_AUTH_REGISTRATION', $REQUIRE_ADMIN_AUTH_REGISTRATION);
@set_site_setting('ALLOW_USER_THEMES',               $ALLOW_USER_THEMES);
@set_site_setting('ALLOW_CHANGE_GEDCOM',             $ALLOW_CHANGE_GEDCOM);
// Don't copy $LOGFILE_CREATE - it is no longer used
// Don't copy $LOG_LANG_ERROR - it is no longer used
@set_site_setting('SESSION_SAVE_PATH',               $PGV_SESSION_SAVE_PATH);
@set_site_setting('SESSION_TIME',                    $PGV_SESSION_TIME);
// Don't copy $SERVER_URL - it will not be applicable!
// Don't copy $LOGIN_URL - it will not be applicable!
// $MAX_VIEWS and $MAX_VIEW_TIME are no longer used
@set_site_setting('MEMORY_LIMIT',                    $PGV_MEMORY_LIMIT);
// Don't copy $COMMIT_COMMAND - it will not be applicable!
@set_site_setting('SMTP_ACTIVE',                     $PGV_SMTP_ACTIVE);
@set_site_setting('SMTP_HOST',                       $PGV_SMTP_HOST);
@set_site_setting('SMTP_HELO',                       $PGV_SMTP_HELO);
@set_site_setting('SMTP_PORT',                       $PGV_SMTP_PORT);
@set_site_setting('SMTP_AUTH',                       $PGV_SMTP_AUTH);
@set_site_setting('SMTP_AUTH_USER',                  $PGV_SMTP_AUTH_USER);
@set_site_setting('SMTP_AUTH_PASS',                  $PGV_SMTP_AUTH_PASS);
@set_site_setting('SMTP_SSL',                        $PGV_SMTP_SSL);
@set_site_setting('SMTP_FROM_NAME',                  $PGV_SMTP_FROM_NAME);

////////////////////////////////////////////////////////////////////////////////

echo '<p>pgv_site_setting => wt_site_setting ...</p>'; flush();
WT_DB::prepare(
	"REPLACE INTO `##site_setting` (setting_name, setting_value)".
	" SELECT site_setting_name, site_setting_value FROM {$DBNAME}.{$TBLPREFIX}site_setting".
	" WHERE site_setting_name IN ('DEFAULT_GEDCOM', 'LAST_CHANGE_EMAIL')"
)->execute();

////////////////////////////////////////////////////////////////////////////////

echo '<p>pgv_site_setting => wt_module_setting ...</p>'; flush();
WT_DB::prepare(
	"REPLACE INTO `##module_setting` (module_name, setting_name, setting_value)".
	" SELECT 'googlemap', site_setting_name, site_setting_value FROM {$DBNAME}.{$TBLPREFIX}site_setting".
	" WHERE site_setting_name LIKE 'GM_%'"
)->execute();
WT_DB::prepare(
	"REPLACE INTO `##module_setting` (module_name, setting_name, setting_value)".
	" SELECT 'lightbox', site_setting_name, site_setting_value FROM {$DBNAME}.{$TBLPREFIX}site_setting".
	" WHERE site_setting_name LIKE 'LB_%'"
)->execute();

////////////////////////////////////////////////////////////////////////////////

echo '<p>pgv_gedcom => wt_gedcom ...</p>'; flush();
WT_DB::prepare(
	"REPLACE INTO `##gedcom` (gedcom_id, gedcom_name)".
	" SELECT gedcom_id, gedcom_name FROM {$DBNAME}.{$TBLPREFIX}gedcom"
)->execute();

////////////////////////////////////////////////////////////////////////////////

echo '<p>pgv_favorites => wt_favorites ...</p>'; flush();
// This is an (optional) module.  The table may not exist
WT_DB::exec(
	"CREATE TABLE IF NOT EXISTS `##favorites` (".
	" fv_id       INTEGER AUTO_INCREMENT NOT NULL,".
 	" fv_username VARCHAR(32)            NOT NULL,".
	" fv_gid      VARCHAR(20)                NULL,".
	" fv_type     VARCHAR(15)                NULL,".
	" fv_file     VARCHAR(100)               NULL,".
	" fv_url      VARCHAR(255)               NULL,".
 	" fv_title    VARCHAR(255)               NULL,".
	" fv_note     TEXT                       NULL,".
	" PRIMARY KEY (fv_id),".
	"         KEY ix1 (fv_username)".
	") COLLATE utf8_unicode_ci ENGINE=InnoDB"
);
WT_DB::prepare(
	"REPLACE INTO `##favorites` (fv_id, fv_username, fv_gid, fv_type, fv_file, fv_url, fv_title, fv_note)".
	" SELECT fv_id, fv_username, fv_gid, fv_type, fv_file, fv_url, fv_title, fv_note FROM {$DBNAME}.{$TBLPREFIX}favorites"
)->execute();

////////////////////////////////////////////////////////////////////////////////

echo '<p>pgv_favorites => wt_favorites ...</p>'; flush();
// This is an (optional) module.  The table may not exist
WT_DB::exec(
	"CREATE TABLE IF NOT EXISTS `##news` (".
	" n_id       INTEGER AUTO_INCREMENT NOT NULL,".
	" n_username VARCHAR(100)           NOT NULL,".
	" n_date     INTEGER                NOT NULL,".
	" n_title    VARCHAR(255)           NOT NULL,".
	" n_text     TEXT                   NOT NULL,".
	" PRIMARY KEY     (n_id),".
	"         KEY ix1 (n_username)".
	") COLLATE utf8_unicode_ci ENGINE=InnoDB"
);
WT_DB::prepare(
	"REPLACE INTO `##news` (n_id, n_username, n_date, n_title, n_text)".
	" SELECT n_id, n_username, n_date, n_title, n_text FROM {$DBNAME}.{$TBLPREFIX}news"
)->execute();

WT_DB::prepare("ROLLBACK")->execute();

////////////////////////////////////////////////////////////////////////////////

echo '<p>Done!</p>';

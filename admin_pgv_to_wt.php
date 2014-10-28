<?php
// PGV to webtrees transfer wizard
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
use WT\User;

define('WT_SCRIPT_NAME', 'admin_pgv_to_wt.php');
require './includes/session.php';

// We can only import into an empty system, so deny access if we have already created a gedcom or added users.
if (WT_GED_ID || count(User::all()) > 1) {
	header('Location: ' . WT_SERVER_NAME.WT_SCRIPT_PATH);
	exit;
}

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isAdmin())
	->setPageTitle(WT_I18N::translate('PhpGedView to webtrees transfer wizard'));

$error    = '';
$warning  = '';
$PGV_PATH = WT_Filter::post('PGV_PATH');

if ($PGV_PATH) {
	if (!is_dir($PGV_PATH) || !is_readable($PGV_PATH.'/config.php')) {
		$error=WT_I18N::translate('The specified directory does not contain an installation of PhpGedView');
	} else {
		// Load the configuration settings
		$config_php=file_get_contents($PGV_PATH.'/config.php');
		// The easiest way to do this is to exec() the file - but not lines containing require or PHP tags
		$config_php=preg_replace(
			array(
				'/^\s*(include|require).*/m',
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
		if ($DBHOST!=$wt_config['dbhost']) {
			$error=WT_I18N::translate('PhpGedView must use the same database as webtrees.');
			unset($wt_config);
		} else {
			unset($wt_config);
			try {
				$PGV_SCHEMA_VERSION=WT_DB::prepare(
					"SELECT site_setting_value FROM `{$DBNAME}`.`{$TBLPREFIX}site_setting` WHERE site_setting_name='PGV_SCHEMA_VERSION'"
				)->fetchOne();
				if ($PGV_SCHEMA_VERSION<10) {
					$error=WT_I18N::translate('The version of %s is too old.', 'PhpGedView');
				} elseif ($PGV_SCHEMA_VERSION>14) {
					$error=WT_I18N::translate('The version of %s is too new.', 'PhpGedView');
				}
			} catch (PDOException $ex) {
				$error=
					/* I18N: %s is a database name/identifier */
					WT_I18N::translate('webtrees cannot connect to the PhpGedView database: %s.', $DBNAME.'@'.$DBHOST).
					'<br>'.
					/* I18N: %s is an error message */
					WT_I18N::translate('MySQL gave the error: %s', $ex->getMessage());
			}
		}
	}
}

if ($PGV_PATH && !$error) {
	// The account we are using is about to be deleted.
	$WT_SESSION->wt_user = null;
}

$controller->pageHeader();

echo
	'<style type="text/css">
		#container {width: 70%; margin:15px auto; border: 1px solid gray; padding: 10px;}
		#container dl {margin:0 0 40px 25px;}
		#container dt {display:inline; width: 320px; font-weight:normal; margin: 0 0 15px 0;}
		#container dd {color: #81A9CB; margin-bottom:20px;font-weight:bold;}
		#container p {color: #81A9CB; font-size: 14px; font-style: italic; font-weight:bold; padding: 0 5px 5px;}
		h2 {color: #81A9CB;}
		.good {color: green;}
		.bad {color: red !important;}
		.indifferent {color: blue;}
		#container p.pgv  {color: black; font-size: 12px; font-style: normal; font-weight:normal; padding:0; margin:10px 0 0 320px;}
	</style>';

if ($error || !$PGV_PATH) {
	// Prompt for location of PhpGedView installation
	echo '<div id="container">';
	echo
		'<h2>',
		WT_I18N::translate('PhpGedView to webtrees transfer wizard'),
		help_link('PGV_WIZARD'),
		'</h2>';
	if ($error) {
		echo '<p class="bad">', $error, '</p>';
	}

	// Look for PGV in some nearby directories
	$pgv_dirs=array();
	$dir=opendir(realpath('..'));
	while (($subdir=readdir($dir))!==false) {
		if (is_dir('../'.$subdir) && file_exists('../'.$subdir.'/config.php')) {
			$pgv_dirs[]='../'.$subdir;
		}
	}
	closedir($dir);

	echo
		'<form action="', WT_SCRIPT_NAME, '" method="post">',
		'<p>', WT_I18N::translate('Where is your PhpGedView installation?'), '</p>',
		'<dl>',
		'<dt>',WT_I18N::translate('Installation folder'), '</dt>';
	switch (count($pgv_dirs)) {
	case '0':
		echo '<dd><input type="text" name="PGV_PATH" size="40" value="" autofocus></dd>';
		break;
	case '1':
		echo '<dd><input type="text" name="PGV_PATH" size="40" value="'.WT_Filter::escapeHtml($pgv_dirs[0]).'" autofocus></dd>';
		break;
	default:
		echo '<dd><input type="text" name="PGV_PATH" size="40" value="" autofocus></dd>';
		echo '<dt>', WT_I18N::translate('PhpGedView might be installed in one of these folders:'), '</dt>';
		echo '<dd>';
		foreach ($pgv_dirs as $pgvpath) {
			echo '<p class="pgv">', $pgvpath, '</p>';
		}
		echo '</dd>';
		break;
	}
	echo
		'</dl>',
		'<div class="center"><input type="submit" value="'.WT_I18N::translate('next').'"></div>',
		'</form>',
		'</div>';
	exit;
}

// Run in a transaction
WT_DB::exec("START TRANSACTION");

// Delete the existing user accounts, and any information associated with it
WT_DB::exec("UPDATE `##log` SET user_id=NULL");
WT_DB::exec("DELETE FROM `##change`");
WT_DB::exec("DELETE `##block_setting` FROM `##block_setting` JOIN  `##block` USING (block_id) WHERE user_id>0 OR gedcom_id>0");
WT_DB::exec("DELETE FROM `##block`               WHERE user_id>0 OR gedcom_id>0");
WT_DB::exec("DELETE FROM `##message`");
WT_DB::exec("DELETE FROM `##user_gedcom_setting` WHERE user_id>0");
WT_DB::exec("DELETE FROM `##user_setting`        WHERE user_id>0");
WT_DB::exec("DELETE FROM `##user`                WHERE user_id>0");

////////////////////////////////////////////////////////////////////////////////
if (ob_get_level() == 0) ob_start();
echo '<p>', $INDEX_DIRECTORY, DIRECTORY_SEPARATOR, 'config.php => wt_site_setting ...</p>';
flush();

// TODO May need to set 'DATA_DIRECTORY' to $INDEX_DIRECTORY when dealing with media??
@WT_Site::setPreference('USE_REGISTRATION_MODULE',         $USE_REGISTRATION_MODULE);
@WT_Site::setPreference('REQUIRE_ADMIN_AUTH_REGISTRATION', $REQUIRE_ADMIN_AUTH_REGISTRATION);
@WT_Site::setPreference('ALLOW_USER_THEMES',               $ALLOW_USER_THEMES);
@WT_Site::setPreference('ALLOW_CHANGE_GEDCOM',             $ALLOW_CHANGE_GEDCOM);
@WT_Site::setPreference('SESSION_TIME',                    $PGV_SESSION_TIME);
@WT_Site::setPreference('SMTP_ACTIVE',                     $PGV_SMTP_ACTIVE ? 'external' : 'internal');
@WT_Site::setPreference('SMTP_HOST',                       $PGV_SMTP_HOST);
@WT_Site::setPreference('SMTP_HELO',                       $PGV_SMTP_HELO);
@WT_Site::setPreference('SMTP_PORT',                       $PGV_SMTP_PORT);
@WT_Site::setPreference('SMTP_AUTH',                       $PGV_SMTP_AUTH);
@WT_Site::setPreference('SMTP_AUTH_USER',                  $PGV_SMTP_AUTH_USER);
@WT_Site::setPreference('SMTP_AUTH_PASS',                  $PGV_SMTP_AUTH_PASS);
@WT_Site::setPreference('SMTP_SSL',                        $PGV_SMTP_SSL);
@WT_Site::setPreference('SMTP_FROM_NAME',                  $PGV_SMTP_FROM_NAME);

////////////////////////////////////////////////////////////////////////////////

echo '<p>pgv_site_setting => wt_site_setting ...</p>';
flush();

WT_DB::prepare(
	"REPLACE INTO `##site_setting` (setting_name, setting_value)".
	" SELECT site_setting_name, site_setting_value FROM `{$DBNAME}`.`{$TBLPREFIX}site_setting`".
	" WHERE site_setting_name IN ('DEFAULT_GEDCOM', 'LAST_CHANGE_EMAIL')"
)->execute();

////////////////////////////////////////////////////////////////////////////////

if ($PGV_SCHEMA_VERSION>=12) {
	echo '<p>pgv_gedcom => wt_gedcom ...</p>';
	flush();

	WT_DB::prepare(
		"INSERT INTO `##gedcom` (gedcom_id, gedcom_name)".
		" SELECT gedcom_id, gedcom_name FROM `{$DBNAME}`.`{$TBLPREFIX}gedcom`"
	)->execute();

	echo '<p>pgv_gedcom_setting => wt_gedcom_setting ...</p>';
	flush();

	WT_DB::prepare(
		"INSERT INTO `##gedcom_setting` (gedcom_id, setting_name, setting_value)".
		" SELECT gedcom_id, setting_name,".
		"  CASE setting_name".
		"  WHEN 'THEME_DIR' THEN".
		"   CASE setting_value".
		"   WHEN ''                    THEN ''".
		"   WHEN 'themes/cloudy/'      THEN 'clouds'".
		"   WHEN 'themes/minimal/'     THEN 'minimal'".
		"   WHEN 'themes/simplyblue/'  THEN 'colors'".
		"   WHEN 'themes/simplygreen/' THEN 'colors'".
		"   WHEN 'themes/simplyred/'   THEN 'colors'".
		"   WHEN 'themes/xenea/'       THEN 'xenea'".
		"   ELSE 'themes/webtrees/'". // ocean, simplyred/blue/green, standard, wood
		"  END".
		"  WHEN 'LANGUAGE' THEN".
		"   CASE setting_value".
		"   WHEN 'catalan'    THEN 'ca'".
		"   WHEN 'english'    THEN 'en_US'".
		"   WHEN 'english-uk' THEN 'en_GB'". // PGV had the config for en_GB, but no language files
		"   WHEN 'polish'     THEN 'pl'".
		"   WHEN 'italian'    THEN 'it'".
		"   WHEN 'spanish'    THEN 'es'".
		"   WHEN 'finnish'    THEN 'fi'".
		"   WHEN 'french'     THEN 'fr'".
		"   WHEN 'german'     THEN 'de'".
		"   WHEN 'danish'     THEN 'da'".
		"   WHEN 'portuguese' THEN 'pt'".
		"   WHEN 'hebrew'     THEN 'he'".
		"   WHEN 'estonian'   THEN 'et'".
		"   WHEN 'turkish'    THEN 'tr'".
		"   WHEN 'dutch'      THEN 'nl'".
		"   WHEN 'slovak'     THEN 'sk'".
		"   WHEN 'norwegian'  THEN 'nn'".
		"   WHEN 'slovenian'  THEN 'sl'".
		"   WHEN 'hungarian'  THEN 'hu'".
		"   WHEN 'swedish'    THEN 'sv'".
		"   WHEN 'russian'    THEN 'ru'".
		"   ELSE 'en_US'". // PGV supports other languages that webtrees does not (yet)
		"  END".
		"  ELSE setting_value".
		"  END".
		" FROM `{$DBNAME}`.`{$TBLPREFIX}gedcom_setting`".
		" WHERE setting_name NOT IN ('HOME_SITE_TEXT', 'HOME_SITE_URL')"
	)->execute();

	echo '<p>pgv_user => wt_user ...</p>';
	flush();

	try {
		// "INSERT IGNORE" is needed to allow for PGV users with duplicate emails.  Only the first will be imported.
		WT_DB::prepare(
			"INSERT IGNORE INTO `##user` (user_id, user_name, real_name, email, password)".
			" SELECT user_id, user_name, CONCAT_WS(' ', us1.setting_value, us2.setting_value), us3.setting_value, password FROM `{$DBNAME}`.`{$TBLPREFIX}user`".
			" LEFT JOIN `{$DBNAME}`.`{$TBLPREFIX}user_setting` us1 USING (user_id)".
			" LEFT JOIN `{$DBNAME}`.`{$TBLPREFIX}user_setting` us2 USING (user_id)".
			" JOIN `{$DBNAME}`.`{$TBLPREFIX}user_setting` us3 USING (user_id)".
			" WHERE us1.setting_name='firstname'".
			" AND us2.setting_name='lastname'".
			" AND us3.setting_name='email'"
		)->execute();
	} catch (PDOException $ex) {
		// Ignore duplicates
	}

	echo '<p>pgv_user_setting => wt_user_setting ...</p>';
	flush();

	WT_DB::prepare(
		"INSERT INTO `##user_setting` (user_id, setting_name, setting_value)".
		" SELECT user_id, setting_name,".
		" CASE setting_name".
		" WHEN 'language' THEN ".
		"  CASE setting_value".
		"  WHEN 'catalan'    THEN 'ca'".
		"  WHEN 'english'    THEN 'en_US'".
		"  WHEN 'english-uk' THEN 'en_GB'". // PGV had the config for en_GB, but no language files
		"  WHEN 'polish'     THEN 'pl'".
		"  WHEN 'italian'    THEN 'it'".
		"  WHEN 'spanish'    THEN 'es'".
		"  WHEN 'finnish'    THEN 'fi'".
		"  WHEN 'french'     THEN 'fr'".
		"  WHEN 'german'     THEN 'de'".
		"  WHEN 'danish'     THEN 'da'".
		"  WHEN 'portuguese' THEN 'pt'".
		"  WHEN 'hebrew'     THEN 'he'".
		"  WHEN 'estonian'   THEN 'et'".
		"  WHEN 'turkish'    THEN 'tr'".
		"  WHEN 'dutch'      THEN 'nl'".
		"  WHEN 'slovak'     THEN 'sk'".
		"  WHEN 'norwegian'  THEN 'nn'".
		"  WHEN 'slovenian'  THEN 'sl'".
		"  WHEN 'hungarian'  THEN 'hu'".
		"  WHEN 'swedish'    THEN 'sv'".
		"  WHEN 'russian'    THEN 'ru'".
		"  ELSE 'en_US'". // PGV supports other languages that webtrees does not (yet)
		"  END".
		" WHEN 'theme' THEN".
		"  CASE setting_value".
		"  WHEN ''                    THEN ''".
		"  WHEN 'themes/cloudy/'      THEN 'clouds'".
		"  WHEN 'themes/minimal/'     THEN 'minimal'".
		"  WHEN 'themes/simplyblue/'  THEN 'colors'".
		"  WHEN 'themes/simplygreen/' THEN 'colors'".
		"  WHEN 'themes/simplyred/'   THEN 'colors'".
		"  WHEN 'themes/xenea/'       THEN 'xenea'".
		"  ELSE 'themes/webtrees/'". // ocean, simplyred/blue/green, standard, wood
		"  END".
		" ELSE".
		"  CASE".
		"  WHEN setting_value IN ('Y', 'yes') THEN 1 WHEN setting_value IN ('N', 'no') THEN 0 ELSE setting_value END".
		" END".
		" FROM `{$DBNAME}`.`{$TBLPREFIX}user_setting`".
		" JOIN `##user` USING (user_id)".
		" WHERE setting_name NOT IN ('email', 'firstname', 'lastname', 'loggedin')"
	)->execute();

	echo '<p>pgv_user_gedcom_setting => wt_user_gedcom_setting ...</p>';
	flush();

	WT_DB::prepare(
		"INSERT INTO `##user_gedcom_setting` (user_id, gedcom_id, setting_name, setting_value)".
		" SELECT user_id, gedcom_id, setting_name, setting_value FROM `{$DBNAME}`.`{$TBLPREFIX}user_gedcom_setting`".
		" JOIN `##user` USING (user_id)"
	)->execute();

} else {
	// Copied from PGV's db_schema_11_12
	if (file_exists("{$INDEX_DIRECTORY}/gedcoms.php")) {
		require_once "{$INDEX_DIRECTORY}/gedcoms.php";
		$file=$INDEX_DIRECTORY.'/gedcoms.php';
		echo '<p>', $file, ' => wt_gedcom ...</p>';
		flush();

		if (isset($GEDCOMS) && is_array($GEDCOMS)) {
			foreach ($GEDCOMS as $array) {
				try {
					WT_DB::prepare("INSERT INTO `##gedcom` (gedcom_id, gedcom_name) VALUES (?,?)")
						->execute(array($array['id'], $array['gedcom']));
				} catch (PDOException $ex) {
					// Ignore duplicates
				}
				// insert gedcom
				foreach ($array as $key=>$value) {
					if ($key!='id' && $key!='gedcom' && $key!='commonsurnames') {
						try {
							WT_DB::prepare("INSERT INTO `##gedcom_setting` (gedcom_id, setting_name, setting_value) VALUES (?,?, ?)")
								->execute(array($array['id'], $key, $value));
						} catch (PDOException $ex) {
							// Ignore duplicates
						}
					}
				}
			}
		}
	}

	// Migrate the data from pgv_users into pgv_user/pgv_user_setting/pgv_user_gedcom_setting
	echo '<p>pgv_users => wt_user ...</p>';
	flush();

	try {
		// "INSERT IGNORE" is needed to allow for PGV users with duplicate emails.  Only the first will be imported.
		WT_DB::prepare(
			"INSERT IGNORE INTO `##user` (user_name, real_name, email, password)".
			" SELECT u_username, CONCAT_WS(' ', u_firstname, u_lastname), u_email, u_password FROM `{$DBNAME}`.`{$TBLPREFIX}users`"
		)->execute();
	} catch (PDOException $ex) {
		// This could only fail if;
		// a) we've already done it (upgrade)
		// b) it doesn't exist (new install)
	}
	echo '<p>pgv_users => wt_user_setting ...</p>';
	flush();

	try {
		WT_DB::prepare(
			"INSERT INTO `##user_setting` (user_id, setting_name, setting_value)".
			" SELECT user_id, 'canadmin', ".
			" CASE WHEN u_canadmin IN ('Y', 'yes') THEN 1 WHEN u_canadmin IN ('N', 'no') THEN 0 ELSE u_canadmin END".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)".
			" UNION ALL".
			" SELECT user_id, 'verified', ".
			" CASE WHEN u_verified IN ('Y', 'yes') THEN 1 WHEN u_verified IN ('N', 'no') THEN 0 ELSE u_verified END".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)".
			" UNION ALL".
			" SELECT user_id, 'verified_by_admin', ".
			" CASE WHEN u_verified_by_admin IN ('Y', 'yes') THEN 1 WHEN u_verified_by_admin IN ('N', 'no') THEN 0 ELSE u_verified_by_admin END".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)".
			" UNION ALL".
			" SELECT user_id, 'language', ".
			" CASE u_language".
			"  WHEN 'catalan'    THEN 'ca'".
			"  WHEN 'english'    THEN 'en_US'".
			"  WHEN 'english-uk' THEN 'en_GB'". // PGV had the config for en_GB, but no language files
			"  WHEN 'polish'     THEN 'pl'".
			"  WHEN 'italian'    THEN 'it'".
			"  WHEN 'spanish'    THEN 'es'".
			"  WHEN 'finnish'    THEN 'fi'".
			"  WHEN 'french'     THEN 'fr'".
			"  WHEN 'german'     THEN 'de'".
			"  WHEN 'danish'     THEN 'da'".
			"  WHEN 'portuguese' THEN 'pt'".
			"  WHEN 'hebrew'     THEN 'he'".
			"  WHEN 'estonian'   THEN 'et'".
			"  WHEN 'turkish'    THEN 'tr'".
			"  WHEN 'dutch'      THEN 'nl'".
			"  WHEN 'slovak'     THEN 'sk'".
			"  WHEN 'norwegian'  THEN 'nn'".
			"  WHEN 'slovenian'  THEN 'sl'".
			"  WHEN 'hungarian'  THEN 'hu'".
			"  WHEN 'swedish'    THEN 'sv'".
			"  WHEN 'russian'    THEN 'ru'".
			"  ELSE 'en_US'". // PGV supports other languages that webtrees does not (yet)
			" END".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)".
			" UNION ALL".
			" SELECT user_id, 'reg_timestamp', u_reg_timestamp".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)".
			" UNION ALL".
			" SELECT user_id, 'reg_hashcode', u_reg_hashcode".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)".
			" UNION ALL".
			" SELECT user_id, 'theme', ".
			" CASE u_theme".
			"  WHEN ''                    THEN ''".
			"  WHEN 'themes/cloudy/'      THEN 'clouds'".
			"  WHEN 'themes/minimal/'     THEN 'minimal'".
			"  WHEN 'themes/simplyblue/'  THEN 'colors'".
			"  WHEN 'themes/simplygreen/' THEN 'colors'".
			"  WHEN 'themes/simplyred/'   THEN 'colors'".
			"  WHEN 'themes/xenea/'       THEN 'xenea'".
			"  ELSE 'themes/webtrees/'". // ocean, simplyred/blue/green, standard, wood
			" END".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)".
			" UNION ALL".
			" SELECT user_id, 'sessiontime', u_sessiontime".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)".
			" UNION ALL".
			" SELECT user_id, 'contactmethod', u_contactmethod".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)".
			" UNION ALL".
			" SELECT user_id, 'visibleonline', ".
			" CASE WHEN u_visibleonline IN ('Y', 'yes') THEN 1 WHEN u_visibleonline IN ('N', 'no') THEN 0 ELSE u_visibleonline END".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)".
			" UNION ALL".
			" SELECT user_id, 'editaccount', ".
			" CASE WHEN u_editaccount IN ('Y', 'yes') THEN 1 WHEN u_editaccount IN ('N', 'no') THEN 0 ELSE u_editaccount END".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)".
			" UNION ALL".
			" SELECT user_id, 'comment', u_comment".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)".
			" UNION ALL".
			" SELECT user_id, 'relationship_privacy', ".
			" CASE WHEN u_relationship_privacy IN ('Y', 'yes') THEN 1 WHEN u_relationship_privacy IN ('N', 'no') THEN 0 ELSE u_relationship_privacy END".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)".
			" UNION ALL".
			" SELECT user_id, 'max_relation_path', u_max_relation_path".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)".
			" UNION ALL".
			" SELECT user_id, 'auto_accept', ".
			" CASE WHEN u_auto_accept IN ('Y', 'yes') THEN 1 WHEN u_auto_accept IN ('N', 'no') THEN 0 ELSE u_auto_accept END".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)"
		)->execute();
	} catch (PDOException $ex) {
		// This could only fail if;
		// a) we've already done it (upgrade)
		// b) it doesn't exist (new install)
	}
	// Some PGV installations store the u_reg_timestamp in the format "2010-03-07 21:41:07"
	WT_DB::prepare(
		"UPDATE `##user_setting` SET setting_value=UNIX_TIMESTAMP(setting_value) WHERE setting_name='reg_timestamp' AND setting_value LIKE '____-__-__ __:__:__'"
	)->execute();
	// Some PGV installations have empty/invalid values for reg_timestamp
	WT_DB::prepare(
		"UPDATE `##user_setting` SET setting_value=CAST(setting_value AS UNSIGNED) WHERE setting_name='reg_timestamp'"
	)->execute();
	echo '<p>pgv_users => wt_user_gedcom_setting ...</p>';
	flush();

	$user_gedcom_settings=
		WT_DB::prepare(
			"SELECT user_id, u_gedcomid, u_rootid, u_canedit".
			" FROM `{$DBNAME}`.`{$TBLPREFIX}users`".
			" JOIN `##user` ON (user_name=CONVERT(u_username USING utf8) COLLATE utf8_unicode_ci)"
		)->fetchAll();
	foreach ($user_gedcom_settings as $setting) {
		@$array=unserialize($setting->u_gedcomid);
		if (is_array($array)) {
			foreach ($array as $gedcom=>$value) {
				try {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##user_gedcom_setting` (user_id, gedcom_id, setting_name, setting_value) VALUES (?, ?, ?, ?)"
					)->execute(array($setting->user_id, get_id_from_gedcom($gedcom), 'gedcomid', $value));
				} catch (PDOException $ex) {
					// Invalid data?  Reference to non-existing tree?
				}
			}
		}
		@$array=unserialize($setting->u_rootid);
		if (is_array($array)) {
			foreach ($array as $gedcom=>$value) {
				try {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##user_gedcom_setting` (user_id, gedcom_id, setting_name, setting_value) VALUES (?, ?, ?, ?)"
					)->execute(array($setting->user_id, get_id_from_gedcom($gedcom), 'rootid', $value));
				} catch (PDOException $ex) {
					// Invalid data?  Reference to non-existing tree?
				}
			}
		}
		@$array=unserialize($setting->u_canedit);
		if (is_array($array)) {
			foreach ($array as $gedcom=>$value) {
				try {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##user_gedcom_setting` (user_id, gedcom_id, setting_name, setting_value) VALUES (?, ?, ?, ?)"
					)->execute(array($setting->user_id, get_id_from_gedcom($gedcom), 'canedit', $value));
				} catch (PDOException $ex) {
					// Invalid data?  Reference to non-existing tree?
				}
			}
		}
	}
}

define('PGV_PHPGEDVIEW', true);
define('PGV_PRIV_PUBLIC', WT_PRIV_PUBLIC);
define('PGV_PRIV_USER', WT_PRIV_USER);
define('PGV_PRIV_NONE', WT_PRIV_NONE);
define('PGV_PRIV_HIDE', WT_PRIV_HIDE);
$PRIV_PUBLIC=WT_PRIV_PUBLIC;
$PRIV_USER=WT_PRIV_USER;
$PRIV_NONE=WT_PRIV_NONE;
$PRIV_HIDE=WT_PRIV_HIDE;

// Old versions of PGV used a $GEDCOMS[] array.
// New versions used a database.
$GEDCOMS=WT_DB::prepare(
	"SELECT" .
	" gedcom_id         AS id," .
	" gedcom_name       AS gedcom," .
	" gs1.setting_value AS config," .
	" gs2.setting_value AS privacy" .
	" FROM  `##gedcom`" .
	" JOIN  `##gedcom_setting` AS gs1 USING (gedcom_id)" .
	" JOIN  `##gedcom_setting` AS gs2 USING (gedcom_id)" .
	" WHERE gedcom_id>0" .
	" AND   gs1.setting_name='config'" .
	" AND   gs2.setting_name='privacy'"
)->fetchAll(PDO::FETCH_ASSOC);

foreach ($GEDCOMS as $GEDCOM=>$GED_DATA) {
	$config=$GED_DATA['config'];
	if ($PGV_SCHEMA_VERSION>=12) {
	$config=str_replace('${INDEX_DIRECTORY}', $INDEX_DIRECTORY.DIRECTORY_SEPARATOR, $config);
	} else {
		$config=str_replace($INDEX_DIRECTORY, $INDEX_DIRECTORY.DIRECTORY_SEPARATOR, $config);
	}
	if (substr($config, 0, 1)=='.') {
		$config=$PGV_PATH.'/'.$config;
	}
	// Some settings were added in later versions of PGV, and may not be set if the
	// user has not used the PGV admin pages since upgrading.
	$NOTE_FACTS_ADD = '';
	$FULL_SOURCES = '';
	if (is_readable($config)) {
		echo '<p>Reading configuration file ', $config, '</p>';
		require $config;
	} else {
		echo '<p>Error - could not read configuration file ', $config, '</p>';
	}

	$privacy=$GED_DATA['privacy'];
	if ($PGV_SCHEMA_VERSION>=12) {
		$privacy=str_replace('${INDEX_DIRECTORY}', $INDEX_DIRECTORY.DIRECTORY_SEPARATOR, $privacy);
	} else {
		$privacy=str_replace($INDEX_DIRECTORY, $INDEX_DIRECTORY.DIRECTORY_SEPARATOR, $privacy);
	}
	if (substr($config, 0, 1)=='.') {
		$privacy=$PGV_PATH.'/'.$privacy;
	}
	if (is_readable($privacy)) {
		echo '<p>Reading privacy file ', $privacy, '</p>';
		require $privacy;
	} else {
		echo '<p>Could not read privacy file ', $privacy, '</p>';
	}

	$stmt_gedcom_setting=WT_DB::prepare("INSERT INTO `##gedcom_setting` (gedcom_id, setting_name, setting_value) VALUES (?,?,?)");

	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'ADVANCED_NAME_FACTS',          $ADVANCED_NAME_FACTS));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'ADVANCED_PLAC_FACTS',          $ADVANCED_PLAC_FACTS));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'ALLOW_THEME_DROPDOWN',         $ALLOW_THEME_DROPDOWN));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'CALENDAR_FORMAT',              $CALENDAR_FORMAT));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'CHART_BOX_TAGS',               $CHART_BOX_TAGS));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'COMMON_NAMES_ADD',             $COMMON_NAMES_ADD));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'COMMON_NAMES_REMOVE',          $COMMON_NAMES_REMOVE));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'COMMON_NAMES_THRESHOLD',       $COMMON_NAMES_THRESHOLD));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'CONTACT_USER_ID',              User::findByIdentifier($CONTACT_EMAIL)->getUserId()));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'DEFAULT_PEDIGREE_GENERATIONS', $DEFAULT_PEDIGREE_GENERATIONS));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'EXPAND_NOTES',                 $EXPAND_NOTES));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'EXPAND_RELATIVES_EVENTS',      $EXPAND_RELATIVES_EVENTS));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'EXPAND_SOURCES',               $EXPAND_SOURCES));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'FAM_FACTS_ADD',                $FAM_FACTS_ADD));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'FAM_FACTS_QUICK',              $FAM_FACTS_QUICK));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'FAM_FACTS_UNIQUE',             $FAM_FACTS_UNIQUE));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'FAM_ID_PREFIX',                $FAM_ID_PREFIX));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'FULL_SOURCES',                 $FULL_SOURCES));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'GEDCOM_ID_PREFIX',             $GEDCOM_ID_PREFIX));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'GENERATE_UIDS',                $GENERATE_UIDS));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'HIDE_GEDCOM_ERRORS',           $HIDE_GEDCOM_ERRORS));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'HIDE_LIVE_PEOPLE',             $HIDE_LIVE_PEOPLE));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'INDI_FACTS_ADD',               $INDI_FACTS_ADD));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'INDI_FACTS_QUICK',             $INDI_FACTS_QUICK));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'INDI_FACTS_UNIQUE',            $INDI_FACTS_UNIQUE));
	switch ($LANGUAGE) {
	case 'catalan':    $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'ca')); break;
	case 'english-uk': $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'en_GB')); break;
	case 'polish':     $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'pl')); break;
	case 'italian':    $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'it')); break;
	case 'spanish':    $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'es')); break;
	case 'finnish':    $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'fi')); break;
	case 'french':     $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'fr')); break;
	case 'german':     $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'de')); break;
	case 'danish':     $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'da')); break;
	case 'portuguese': $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'pt')); break;
	case 'hebrew':     $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'he')); break;
	case 'estonian':   $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'et')); break;
	case 'turkish':    $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'tr')); break;
	case 'dutch':      $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'nl')); break;
	case 'slovak':     $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'sk')); break;
	case 'norwegian':  $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'nn')); break;
	case 'slovenian':  $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'sl')); break;
	case 'hungarian':  $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'hu')); break;
	case 'swedish':    $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'sv')); break;
	case 'russian':    $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'ru')); break;
	default:           $stmt_gedcom_setting->execute(array($GED_DATA['id'], 'LANGUAGE', 'en_US')); break;
	}
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'MAX_ALIVE_AGE',                $MAX_ALIVE_AGE));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'MAX_DESCENDANCY_GENERATIONS',  $MAX_DESCENDANCY_GENERATIONS));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'MAX_PEDIGREE_GENERATIONS',     $MAX_PEDIGREE_GENERATIONS));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'MAX_RELATION_PATH_LENGTH',     $MAX_RELATION_PATH_LENGTH));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'MEDIA_DIRECTORY',              'media/'));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'MEDIA_ID_PREFIX',              $MEDIA_ID_PREFIX));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'META_DESCRIPTION',             $META_DESCRIPTION));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'META_TITLE',                   $META_TITLE));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'MEDIA_UPLOAD',                 $MULTI_MEDIA)); // see schema v12-13
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'NOTE_FACTS_ADD',               $NOTE_FACTS_ADD));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'NOTE_FACTS_QUICK',             $NOTE_FACTS_QUICK));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'NOTE_FACTS_UNIQUE',            $NOTE_FACTS_UNIQUE));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'NOTE_ID_PREFIX',               'N'));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'NO_UPDATE_CHAN',               $NO_UPDATE_CHAN));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'PEDIGREE_FULL_DETAILS',        $PEDIGREE_FULL_DETAILS));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'PEDIGREE_LAYOUT',              $PEDIGREE_LAYOUT));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'PEDIGREE_ROOT_ID',             $PEDIGREE_ROOT_ID));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'PEDIGREE_SHOW_GENDER',         $PEDIGREE_SHOW_GENDER));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'PREFER_LEVEL2_SOURCES',        $PREFER_LEVEL2_SOURCES));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'QUICK_REQUIRED_FACTS',         $QUICK_REQUIRED_FACTS));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'QUICK_REQUIRED_FAMFACTS',      $QUICK_REQUIRED_FAMFACTS));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'REPO_FACTS_ADD',               $REPO_FACTS_ADD));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'REPO_FACTS_QUICK',             $REPO_FACTS_QUICK));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'REPO_FACTS_UNIQUE',            $REPO_FACTS_UNIQUE));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'REPO_ID_PREFIX',               $REPO_ID_PREFIX));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'REQUIRE_AUTHENTICATION',       $REQUIRE_AUTHENTICATION));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SAVE_WATERMARK_IMAGE',         $SAVE_WATERMARK_IMAGE));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SAVE_WATERMARK_THUMB',         $SAVE_WATERMARK_THUMB));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_AGE_DIFF',                $SHOW_AGE_DIFF));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_COUNTER',                 $SHOW_COUNTER));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_DEAD_PEOPLE',             $SHOW_DEAD_PEOPLE));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_EST_LIST_DATES',          $SHOW_EST_LIST_DATES));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_FACT_ICONS',              $SHOW_FACT_ICONS));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_GEDCOM_RECORD',           $SHOW_GEDCOM_RECORD));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_HIGHLIGHT_IMAGES',        $SHOW_HIGHLIGHT_IMAGES));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_LDS_AT_GLANCE',           $SHOW_LDS_AT_GLANCE));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_LEVEL2_NOTES',            $SHOW_LEVEL2_NOTES));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_LIST_PLACES',             $SHOW_LIST_PLACES));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_LIVING_NAMES',            $SHOW_LIVING_NAMES));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_MEDIA_DOWNLOAD',          $SHOW_MEDIA_DOWNLOAD));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_PARENTS_AGE',             $SHOW_PARENTS_AGE));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_PEDIGREE_PLACES',         $SHOW_PEDIGREE_PLACES));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_PRIVATE_RELATIONSHIPS',   $SHOW_PRIVATE_RELATIONSHIPS));

	// Update these - see db_schema_5_6.php
	$SHOW_RELATIVES_EVENTS=preg_replace('/_(BIRT|MARR|DEAT)_(COUS|MSIB|FSIB|GGCH|NEPH|GGPA)/', '', $SHOW_RELATIVES_EVENTS);
	$SHOW_RELATIVES_EVENTS=preg_replace('/_FAMC_(RESI_EMIG)/', '', $SHOW_RELATIVES_EVENTS);
	$SHOW_RELATIVES_EVENTS=preg_replace('/_MARR_(MOTH|FATH|FAMC)/', '_MARR_PARE', $SHOW_RELATIVES_EVENTS);
	$SHOW_RELATIVES_EVENTS=preg_replace('/_DEAT_(MOTH|FATH)/', '_DEAT_PARE', $SHOW_RELATIVES_EVENTS);
	preg_match_all('/[_A-Z]+/', $SHOW_RELATIVES_EVENTS, $match);
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_RELATIVES_EVENTS', implode(',', array_unique($match[0]))));

	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SHOW_STATS',                   $SHOW_STATS));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SOURCE_ID_PREFIX',             $SOURCE_ID_PREFIX));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SOUR_FACTS_ADD',               $SOUR_FACTS_ADD));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SOUR_FACTS_QUICK',             $SOUR_FACTS_QUICK));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SOUR_FACTS_UNIQUE',            $SOUR_FACTS_UNIQUE));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SUBLIST_TRIGGER_I',            $SUBLIST_TRIGGER_I));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SURNAME_LIST_STYLE',           $SURNAME_LIST_STYLE));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'SURNAME_TRADITION',            $SURNAME_TRADITION));
	switch (@$THEME_DIR) {
	case '':
		$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'THEME_DIR', ''));
		break;
	case 'themes/cloudy/':
		$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'THEME_DIR', 'clouds'));
		break;
	case 'themes/minimal/':
		$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'THEME_DIR', 'minimal'));
		break;
	case 'themes/simplyblue/':
	case 'themes/simplygreen/':
	case 'themes/simplyred/':
		$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'THEME_DIR', 'colors'));
		break;
	case 'themes/xenea/':
		$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'THEME_DIR', 'xenea'));
		break;
	default:
		$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'THEME_DIR', 'webtrees'));
		break;
	}
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'THUMBNAIL_WIDTH',              $THUMBNAIL_WIDTH));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'USE_RELATIONSHIP_PRIVACY',     $USE_RELATIONSHIP_PRIVACY));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'USE_RIN',                      $USE_RIN));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'WATERMARK_THUMB',              $WATERMARK_THUMB));
	@$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'WEBMASTER_USER_ID',           User::findByIdentifier($WEBMASTER_EMAIL)->getUserId()));
	$stmt_gedcom_setting->execute(array($GED_DATA['id'], 'WORD_WRAPPED_NOTES',           $WORD_WRAPPED_NOTES));
}
WT_DB::prepare("DELETE FROM `##gedcom_setting` WHERE setting_name in ('config', 'privacy', 'path', 'pgv_ver', 'imported')")->execute();

// webtrees 1.0.5 combines user and gedcom settings for relationship privacy
// into a combined user-gedcom setting, for more granular control
WT_DB::exec(
	"INSERT IGNORE INTO `##user_gedcom_setting` (user_id, gedcom_id, setting_name, setting_value)".
	" SELECT u.user_id, g.gedcom_id, 'RELATIONSHIP_PATH_LENGTH', LEAST(us1.setting_value, gs1.setting_value)".
	" FROM   `##user` u".
	" CROSS  JOIN `##gedcom` g".
	" LEFT   JOIN `##user_setting`   us1 ON (u.user_id  =us1.user_id   AND us1.setting_name='max_relation_path')".
	" LEFT   JOIN `##user_setting`   us2 ON (u.user_id  =us2.user_id   AND us2.setting_name='relationship_privacy')".
	" LEFT   JOIN `##gedcom_setting` gs1 ON (g.gedcom_id=gs1.gedcom_id AND gs1.setting_name='MAX_RELATION_PATH_LENGTH')".
	" LEFT   JOIN `##gedcom_setting` gs2 ON (g.gedcom_id=gs2.gedcom_id AND gs2.setting_name='USE_RELATIONSHIP_PRIVACY')".
	" WHERE  us2.setting_value AND gs2.setting_value"
);

WT_DB::exec(
	"DELETE FROM `##gedcom_setting` WHERE setting_name IN ('MAX_RELATION_PATH_LENGTH', 'USE_RELATIONSHIP_PRIVACY')"
);

WT_DB::exec(
	"DELETE FROM `##user_setting` WHERE setting_name IN ('relationship_privacy', 'max_relation_path_length')"
);

////////////////////////////////////////////////////////////////////////////////
// The PGV blocks don't migrate easily.
// Just give everybody and every tree default blocks
////////////////////////////////////////////////////////////////////////////////

WT_DB::prepare(
	"INSERT INTO `##block` (user_id, location, block_order, module_name)" .
	" SELECT `##user`.user_id, location, block_order, module_name" .
	" FROM `##block`" .
	" JOIN `##user`" .
	" WHERE `##block`.user_id = -1" .
	" AND   `##user`.user_id  >  0"
)->execute();

WT_DB::prepare(
	"INSERT INTO `##block` (gedcom_id, location, block_order, module_name)" .
	" SELECT `##gedcom`.gedcom_id, location, block_order, module_name" .
	" FROM `##block`" .
	" JOIN `##gedcom`" .
	" WHERE `##block`.gedcom_id = -1" .
	" AND   `##gedcom`.gedcom_id  >  0"
)->execute();


////////////////////////////////////////////////////////////////////////////////
// Hit counter
////////////////////////////////////////////////////////////////////////////////
//
if ($PGV_SCHEMA_VERSION>=13) {
	echo '<p>pgv_hit_counter => wt_hit_counter ...</p>';
	flush();

	WT_DB::prepare(
		"REPLACE INTO `##hit_counter` (gedcom_id, page_name, page_parameter, page_count)".
		" SELECT gedcom_id, page_name, page_parameter, page_count FROM `{$DBNAME}`.`{$TBLPREFIX}hit_counter`"
	)->execute();
} else {
	// Copied from PGV's db_schema_12_13
	$statement=WT_DB::prepare("INSERT IGNORE INTO `##hit_counter` (gedcom_id, page_name, page_parameter, page_count) VALUES (?, ?, ?, ?)");

	foreach ($GEDCOMS as $GEDCOM=>$GED_DATA) {
		// Caution these files might be quite large...
		$file=$INDEX_DIRECTORY.'/'.$GEDCOM.'pgv_counters.txt';
		echo '<p>', $file, ' => wt_hit_counter ...</p>';
		flush();

		if (file_exists($file)) {
			foreach (file($file) as $line) {
				if (preg_match('/(@([A-Za-z0-9:_-]+)@ )?(\d+)/', $line, $match)) {
					if ($match[2]) {
						$page_name='individual.php';
						$page_parameter=$match[2];
					} else {
						$page_name='index.php';
						$page_parameter='gedcom:'.$GED_DATA['id'];
					}
					try {
						$statement->execute(array($GED_DATA['id'], $page_name, $page_parameter, $match[3]));
					} catch (PDOException $ex) {
						// Primary key violation?  Ignore?
					}
				}
			}
		}
	}
}

////////////////////////////////////////////////////////////////////////////////

if ($PGV_SCHEMA_VERSION>=14) {
	echo '<p>pgv_ip_address => wt_ip_address ...</p>';
	flush();

	WT_DB::prepare(
		"INSERT IGNORE INTO `##ip_address` (ip_address, category, comment)".
		" SELECT ip_address, category, comment FROM `{$DBNAME}`.`{$TBLPREFIX}ip_address`"
	)->execute();
} else {
	// Copied from PGV's db_schema_13_14
	$statement=WT_DB::prepare("REPLACE INTO `##ip_address` (ip_address, category, comment) VALUES (?, ?, ?)");
	echo '<p>', $INDEX_DIRECTORY, DIRECTORY_SEPARATOR, 'banned.php => wt_ip_address ...</p>';
	flush();

	if (is_readable($INDEX_DIRECTORY.'/banned.php')) {
		@require $INDEX_DIRECTORY.'/banned.php';
		if (!empty($banned) && is_array($banned)) {
			foreach ($banned as $value) {
				try {
					if (is_array($value)) {
						// New format: array(ip, comment)
						$statement->execute(array($value[0], 'banned', $value[1]));
					} else {
						// Old format: string(ip)
						$statement->execute(array($value, 'banned', ''));
					}
				} catch (PDOException $ex) {
					echo $ex, '<br>';
				}
			}
		}
	}
	echo '<p>', $INDEX_DIRECTORY, DIRECTORY_SEPARATOR, 'search_engines.php => wt_ip_address ...</p>';
	flush();

	if (is_readable($INDEX_DIRECTORY.'/search_engines.php')) {
		@require $INDEX_DIRECTORY.'/search_engines.php';
		if (!empty($search_engines) && is_array($search_engines)) {
			foreach ($search_engines as $value) {
				try {
					if (is_array($value)) {
						// New format: array(ip, comment)
						$statement->execute(array($value[0], 'search-engine', $value[1]));
					} else {
						// Old format: string(ip)
						$statement->execute(array($value, 'search-engine', ''));
					}
				} catch (PDOException $ex) {
					echo $ex, '<br>';
				}
			}
		}
	}
}

////////////////////////////////////////////////////////////////////////////////

foreach ($GEDCOMS as $GED_DATA) {
	WT_Module::setDefaultAccess($GED_DATA['id']);
}

echo '<p>pgv_site_setting => wt_module_setting ...</p>';
flush();

WT_DB::prepare(
	"REPLACE INTO `##module_setting` (module_name, setting_name, setting_value)".
	" SELECT 'googlemap', site_setting_name, site_setting_value FROM `{$DBNAME}`.`{$TBLPREFIX}site_setting`".
	" WHERE site_setting_name LIKE 'GM_%'"
)->execute();
WT_DB::prepare(
	"REPLACE INTO `##module_setting` (module_name, setting_name, setting_value)".
	" SELECT 'lightbox', site_setting_name, site_setting_value FROM `{$DBNAME}`.`{$TBLPREFIX}site_setting`".
	" WHERE site_setting_name LIKE 'LB_%'"
)->execute();

////////////////////////////////////////////////////////////////////////////////

echo '<p>pgv_favorites => wt_favorite ...</p>';
flush();

try {
	WT_DB::prepare(
		"REPLACE INTO `##favorite` (favorite_id, user_id, gedcom_id, xref, favorite_type, url, title, note)".
		" SELECT fv_id, u.user_id, g.gedcom_id, fv_gid, fv_type, fv_url, fv_title, fv_note".
		" FROM `{$DBNAME}`.`{$TBLPREFIX}favorites` f".
		" LEFT JOIN `##gedcom` g ON (f.fv_username=g.gedcom_name)".
		" LEFT JOIN `##user`   u ON (f.fv_username=u.user_name)"
	)->execute();
} catch (PDOException $ex) {
	// This table will only exist if the favorites module is installed in WT
}

////////////////////////////////////////////////////////////////////////////////

echo '<p>pgv_news => wt_news ...</p>';
flush();

try {
	WT_DB::prepare(
		"REPLACE INTO `##news` (news_id, user_id, gedcom_id, subject, body, updated)".
		" SELECT n_id, u.user_id, g.gedcom_id, n_title, n_text, FROM_UNIXTIME(n_date)".
		" FROM `{$DBNAME}`.`{$TBLPREFIX}news` n".
		" LEFT JOIN `##gedcom` g ON (n.n_username=g.gedcom_name)".
		" LEFT JOIN `##user` u ON (n.n_username=u.user_name)"
	)->execute();
} catch (PDOException $ex) {
	// This table will only exist if the news/blog module is installed in WT
}

////////////////////////////////////////////////////////////////////////////////

echo '<p>pgv_nextid => wt_next_id ...</p>';
flush();

WT_DB::prepare(
	"REPLACE INTO `##next_id` (gedcom_id, record_type, next_id)".
	" SELECT ni_gedfile, ni_type, ni_id".
	" FROM `{$DBNAME}`.`{$TBLPREFIX}nextid`".
	" JOIN `##gedcom` ON (ni_gedfile = gedcom_id)".
	" WHERE ni_type IN ('INDI', 'FAM', 'SOUR', 'REPO', 'OBJE', 'NOTE')"
)->execute();

////////////////////////////////////////////////////////////////////////////////

echo '<p>pgv_messages => wt_message ...</p>';
flush();

WT_DB::prepare(
	"REPLACE INTO `##message` (message_id, sender, ip_address, user_id, subject, body, created)".
	" SELECT m_id, m_from, '127.0.0.1', user_id, m_subject, m_body, str_to_date(m_created,'%a, %d %M %Y %H:%i:%s')".
	" FROM `{$DBNAME}`.`{$TBLPREFIX}messages`".
	" JOIN `##user` ON (CONVERT(m_to USING utf8) COLLATE utf8_unicode_ci=user_name)"
)->execute();

////////////////////////////////////////////////////////////////////////////////

try {
	echo '<p>pgv_placelocation => wt_placelocation ...</p>';
	flush();

	WT_DB::prepare(
		"REPLACE INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon)".
		" SELECT pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon FROM `{$DBNAME}`.`{$TBLPREFIX}placelocation`"
	)->execute();
} catch (PDOexception $ex) {
	// This table will only exist if the gm module is installed in PGV/WT
}

////////////////////////////////////////////////////////////////////////////////

echo '<p>Genealogy records ...</p>';
flush();

WT_DB::prepare(
	"INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data, imported)" .
	" SELECT o_file, o_gedcom, 0 FROM `{$DBNAME}`.`{$TBLPREFIX}other`" .
	" JOIN `##gedcom` ON (o_file = gedcom_id)" .
	" ORDER BY o_type!='HEAD'" // Must load HEAD record first
)->execute();

WT_DB::prepare(
	"INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data, imported)" .
	" SELECT i_file, i_gedcom, 0 FROM `{$DBNAME}`.`{$TBLPREFIX}individuals`" .
	" JOIN `##gedcom` ON (i_file = gedcom_id)"
)->execute();

WT_DB::prepare(
	"INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data, imported)" .
	" SELECT f_file, f_gedcom, 0 FROM `{$DBNAME}`.`{$TBLPREFIX}families`" .
	" JOIN `##gedcom` ON (f_file = gedcom_id)"
)->execute();

WT_DB::prepare(
	"INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data, imported)" .
	" SELECT s_file, s_gedcom, 0 FROM `{$DBNAME}`.`{$TBLPREFIX}sources`" .
	" JOIN `##gedcom` ON (s_file = gedcom_id)"
)->execute();

WT_DB::prepare(
	"INSERT INTO `##gedcom_chunk` (gedcom_id, chunk_data, imported)" .
	" SELECT m_gedfile, m_gedrec, 0 FROM `{$DBNAME}`.`{$TBLPREFIX}media`" .
	" JOIN `##gedcom` ON (m_gedfile = gedcom_id)"
)->execute();

WT_DB::prepare(
	"UPDATE `##gedcom_setting` SET setting_value='0' WHERE setting_name='imported'"
)->execute();

////////////////////////////////////////////////////////////////////////////////

WT_DB::exec("COMMIT");

echo '<hr>';
echo '<p>', WT_I18N::translate('You need to login again, using your PhpGedView username and password.'), '</p>';
echo '<a href="index.php"><button>', WT_I18N::translate('continue'), '</button></a>';

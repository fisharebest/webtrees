<?php
/**
*
* Set/create default settings for a new gedcom.
*
* The calling module must set $ged_id and $ged_name
*
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
* @version $Id$
*/

if (!defined('WT_WEBTREES') || empty($ged_id) || empty($ged_name)) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

////////////////////////////////////////////////////////////////////////////////
// Configuration and privacy files
////////////////////////////////////////////////////////////////////////////////
$INDEX_DIRECTORY=get_site_setting('INDEX_DIRECTORY');
if (!file_exists($INDEX_DIRECTORY.$ged_name.'_conf.php')) {
	copy('config_gedcom.php', $INDEX_DIRECTORY.$ged_name.'_conf.php');
}
if (!file_exists($INDEX_DIRECTORY.$ged_name.'_priv.php')) {
	copy('privacy.php', $INDEX_DIRECTORY.$ged_name.'_priv.php');
}
set_gedcom_setting($ged_id, 'config',  $INDEX_DIRECTORY.$ged_name.'_conf.php');
set_gedcom_setting($ged_id, 'privacy', $INDEX_DIRECTORY.$ged_name.'_priv.php');

////////////////////////////////////////////////////////////////////////////////
// Module privacy
////////////////////////////////////////////////////////////////////////////////
require_once WT_ROOT.'includes/classes/class_module.php';
WT_Module::setDefaultAccess($ged_id);

////////////////////////////////////////////////////////////////////////////////
// General settings
////////////////////////////////////////////////////////////////////////////////
$statement=WT_DB::prepare(
	"INSERT IGNORE INTO {$TBLPREFIX}gedcom_setting (gedcom_id, setting_name, setting_value) VALUES (?, ?, ?)");

$statement->execute(array($ged_id, 'title', i18n::translate('Genealogy from [%s]', $ged_name)));
$statement->execute(array($ged_id, 'imported', 0));
$statement->execute(array($ged_id, 'CONTACT_USER_ID', WT_USER_ID));
$statement->execute(array($ged_id, 'WEBMASTER_USER_ID', WT_USER_ID));

// Eventually, all the settings in config_gedcom.php and privacy.php will be here...

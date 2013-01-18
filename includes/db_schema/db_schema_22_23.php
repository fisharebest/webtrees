<?php
// Update the database schema from version 21-22
// - data update for 1.4.0 media changes
//
// The script should assume that it can be interrupted at
// any point, and be able to continue by re-running the script.
// Fatal errors, however, should be allowed to throw exceptions,
// which will be caught by the framework.
// It shouldn't do anything that might take more than a few
// seconds, for systems with low timeout values.
//
// webtrees: Web based Family History software
// Copyright (C) 2013 Greg Roach
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
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$_data_dir = realpath(WT_Site::preference('INDEX_DIRECTORY') ? WT_Site::preference('INDEX_DIRECTORY') : 'data').DIRECTORY_SEPARATOR;

$_cfgs = self::prepare(
	"SELECT gs1.gedcom_id AS gedcom_id, gs1.setting_value AS media_directory, gs2.setting_value AS use_media_firewall, gs3.setting_value AS media_firewall_thumbs" .
	" FROM `##gedcom_setting` gs1" .
	" LEFT JOIN `##gedcom_setting` gs2 ON (gs1.gedcom_id = gs2.gedcom_id AND gs2.setting_name='USE_MEDIA_FIREWALL')" .
	" LEFT JOIN `##gedcom_setting` gs3 ON (gs1.gedcom_id = gs3.gedcom_id AND gs3.setting_name='MEDIA_FIREWALL_THUMBS')" .
	" WHERE gs1.setting_name = 'MEDIA_DIRECTORY'"
)->fetchAll();

// Check the config for each tree
foreach ($_cfgs as $_cfg) {
	if ($_cfg->use_media_firewall) {
		// We’re using the media firewall.
		$_mf_dir = realpath();
		if ($_mf_dir == $_data_dir) {
			// We’re already storing our media in the data folder - nothing to do.
		} else {
			// We’ve chosen a custom location for our media folder - need to update our media-folder to point to it.
			// We have, for example,
			// $_mf_dir = /home/fisharebest/my_pictures/
			// $_data_dir = /home/fisharebest/public_html/webtrees/data/
			// Therefore we need to calculate ../../../my_pictures/
			$_media_dir = '';
			$_tmp_dir = $_data_dir;
			while (strpos($_mf_dir, $_tmp_dir)!==0) {
				$_media_dir .= '../';
				$_tmp_dir = preg_replace('~[^/\\\\]+[/\\\\]$~', '', $_tmp_dir);
				if ($_tmp_dir=='') {
					// Shouldn't get here - but this script is not allowed to fail...
					continue 2;
				}
			}
			$_media_dir .= substr($_mf_dir, strlen($_tmp_dir));
			self::prepare(
				"UPDATE `##gedcom_setting`" .
				" SET setting_value=?" .
				" WHERE gedcom_id=? AND setting_name='MEDIA_DIRECTORY'"
			)->execute(array($_media_dir, $_cgf->gedcom_id));
		}
	} else {
		// Not using the media firewall - just move the public folder to the new location (if we can).
		if (
			file_exists(WT_ROOT . $_cfg->media_directory) &&
			is_dir(WT_ROOT . $_cfg->media_directory) &&
			!file_exists($_data_dir . $_cfg->media_directory)
		) {
			@rename(WT_ROOT . $_cfg->media_directory, $_data_dir . $_cfg->media_directory);
			@unlink($_data_dir . $_cfg->media_directory . '.htaccess');
			@unlink($_data_dir . $_cfg->media_directory . 'index.php');
			@unlink($_data_dir . $_cfg->media_directory . 'Mediainfo.txt');
			@unlink($_data_dir . $_cfg->media_directory . 'thumbs/Thumbsinfo.txt');
		}
	}
}

unset($_data_dir, $_cfgs, $_cfg, $_mf_dir, $_tmp_dir);

// Update the version to indicate success
WT_Site::preference($schema_name, $next_version);

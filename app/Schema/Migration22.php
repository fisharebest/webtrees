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
namespace Fisharebest\Webtrees\Schema;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\File;

/**
 * Upgrade the database schema from version 22 to version 23.
 */
class Migration22 implements MigrationInterface {
	/**
	 * Upgrade to to the next version
	 */
	public function upgrade() {
		// - data update for 1.4.0 media changes
		$_cfgs = Database::prepare(
			"SELECT gs1.gedcom_id AS gedcom_id, gs1.setting_value AS media_directory, gs2.setting_value AS use_media_firewall, gs3.setting_value AS media_firewall_thumbs, gs4.setting_value AS media_firewall_rootdir" .
			" FROM `##gedcom_setting` gs1" .
			" LEFT JOIN `##gedcom_setting` gs2 ON (gs1.gedcom_id = gs2.gedcom_id AND gs2.setting_name='USE_MEDIA_FIREWALL')" .
			" LEFT JOIN `##gedcom_setting` gs3 ON (gs1.gedcom_id = gs3.gedcom_id AND gs3.setting_name='MEDIA_FIREWALL_THUMBS')" .
			" LEFT JOIN `##gedcom_setting` gs4 ON (gs1.gedcom_id = gs4.gedcom_id AND gs4.setting_name='MEDIA_FIREWALL_ROOTDIR')" .
			" WHERE gs1.setting_name = 'MEDIA_DIRECTORY'"
		)->fetchAll();

		// The constant WT_DATA_DIR is not defined yet (although it was when this script was originally written).
		$WT_DATA_DIR = realpath('data');

		// Check the config for each tree
		foreach ($_cfgs as $_cfg) {
			if ($_cfg->use_media_firewall) {
				// We’re using the media firewall.
				$_mf_dir = realpath($_cfg->media_firewall_rootdir) . DIRECTORY_SEPARATOR;
				if ($_mf_dir == $WT_DATA_DIR) {
					// We’re already storing our media in the data folder - nothing to do.
				} else {
					// We’ve chosen a custom location for our media folder - need to update our media-folder to point to it.
					// We have, for example,
					// $_mf_dir = /home/fisharebest/my_pictures/
					// $WT_DATA_DIR = /home/fisharebest/public_html/webtrees/data/
					// Therefore we need to calculate ../../../my_pictures/
					$_media_dir = '';
					$_tmp_dir   = $WT_DATA_DIR;
					while (strpos($_mf_dir, $_tmp_dir) !== 0) {
						$_media_dir .= '../';
						$_tmp_dir = preg_replace('~[^/\\\\]+[/\\\\]$~', '', $_tmp_dir);
						if ($_tmp_dir == '') {
							// Shouldn't get here - but this script is not allowed to fail...
							continue 2;
						}
					}
					$_media_dir .= $_cfg->media_directory;
					Database::prepare(
						"UPDATE `##gedcom_setting`" .
						" SET setting_value=?" .
						" WHERE gedcom_id=? AND setting_name='MEDIA_DIRECTORY'"
					)->execute(array($_media_dir, $_cfg->gedcom_id));
				}
			} else {
				// Not using the media firewall - just move the public folder to the new location (if we can).
				if (
					file_exists(WT_ROOT . $_cfg->media_directory) &&
					is_dir(WT_ROOT . $_cfg->media_directory) &&
					!file_exists($WT_DATA_DIR . $_cfg->media_directory)
				) {
					try {
						rename(WT_ROOT . $_cfg->media_directory, $WT_DATA_DIR . $_cfg->media_directory);
					} catch (\ErrorException $ex) {
						// Cannot move the folder?
					}
					File::delete($WT_DATA_DIR . $_cfg->media_directory . '.htaccess');
					File::delete($WT_DATA_DIR . $_cfg->media_directory . 'index.php');
					File::delete($WT_DATA_DIR . $_cfg->media_directory . 'Mediainfo.txt');
					File::delete($WT_DATA_DIR . $_cfg->media_directory . 'thumbs/Thumbsinfo.txt');
				}
			}
		}

		// Delete old settings
		Database::exec("DELETE FROM `##gedcom_setting` WHERE setting_name IN ('USE_MEDIA_FIREWALL', 'MEDIA_FIREWALL_THUMBS', 'MEDIA_FIREWALL_ROOTDIR')");
	}
}

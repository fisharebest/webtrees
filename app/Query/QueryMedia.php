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
namespace Fisharebest\Webtrees\Query;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Media;

/**
 * Generate lists of files for admin_media.php
 */
class QueryMedia {
	/**
	 * Generate a list of all folders from all the trees - for the media admin.
	 *
	 * @return array
	 */
	public static function folderListAll() {
		$folders = Database::prepare(
			"SELECT SQL_CACHE LEFT(multimedia_file_refn, CHAR_LENGTH(multimedia_file_refn) - CHAR_LENGTH(SUBSTRING_INDEX(multimedia_file_refn, '/', -1))) AS media_path" .
			" FROM  `##media_file`" .
			" WHERE multimedia_file_refn NOT LIKE 'http://%'" .
			" AND   multimedia_file_refn NOT LIKE 'https://%'" .
			" GROUP BY 1" .
			" ORDER BY 1"
		)->execute()->fetchOneColumn();

		if ($folders) {
			return array_combine($folders, $folders);
		} else {
			return [];
		}
	}
}

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
namespace Fisharebest\Webtrees\Query;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Media;

/**
 * Generate lists of files for admin_media.php
 */
class QueryMedia {
	/**
	 * Generate a list of all the folders in the current tree - for the media list.
	 *
	 * @return string[]
	 */
	public static function folderList() {
		global $WT_TREE;

		$folders = Database::prepare(
			"SELECT SQL_CACHE LEFT(m_filename, CHAR_LENGTH(m_filename) - CHAR_LENGTH(SUBSTRING_INDEX(m_filename, '/', -1))) AS media_path" .
			" FROM  `##media`" .
			" WHERE m_file = ?" .
			" AND   m_filename NOT LIKE 'http://%'" .
			" AND   m_filename NOT LIKE 'https://%'" .
			" GROUP BY 1" .
			" ORDER BY 1"
		)->execute(array($WT_TREE->getTreeId()))->fetchOneColumn();

		if (!$folders || reset($folders) != '') {
			array_unshift($folders, '');
		}

		return array_combine($folders, $folders);
	}

	/**
	 * Generate a list of all folders from all the trees - for the media admin.
	 *
	 * @return array
	 */
	public static function folderListAll() {
		$folders = Database::prepare(
			"SELECT SQL_CACHE LEFT(m_filename, CHAR_LENGTH(m_filename) - CHAR_LENGTH(SUBSTRING_INDEX(m_filename, '/', -1))) AS media_path" .
			" FROM  `##media`" .
			" WHERE m_filename NOT LIKE 'http://%'" .
			" AND   m_filename NOT LIKE 'https://%'" .
			" GROUP BY 1" .
			" ORDER BY 1"
		)->execute()->fetchOneColumn();

		if ($folders) {
			return array_combine($folders, $folders);
		} else {
			return array();
		}
	}

	/**
	 * Generate a filtered, sourced, privacy-checked list of media objects - for the media list.
	 *
	 * @param string $folder     folder to search
	 * @param string $subfolders either "include" or "exclude"
	 * @param string $sort       either "file" or "title"
	 * @param string $filter     optional search string
	 * @param string $form_type  option OBJE/FILE/FORM/TYPE
	 *
	 * @throws \Exception
	 *
	 * @return Media[]
	 */
	public static function mediaList($folder, $subfolders, $sort, $filter, $form_type) {
		global $WT_TREE;

		// All files in the folder, plus external files
		$sql =
			"SELECT m_id AS xref, m_gedcom AS gedcom" .
			" FROM `##media`" .
			" WHERE m_file=?";
		$args = array(
			$WT_TREE->getTreeId(),
		);

		// Only show external files when we are looking at the root folder
		if ($folder == '') {
			$sql_external = " OR m_filename LIKE 'http://%' OR m_filename LIKE 'https://%'";
		} else {
			$sql_external = "";
		}

		// Include / exclude subfolders (but always include external)
		switch ($subfolders) {
		case 'include':
			$sql .= " AND (m_filename LIKE CONCAT(?, '%') $sql_external)";
			$args[] = Filter::escapeLike($folder);
			break;
		case 'exclude':
			$sql .= " AND (m_filename LIKE CONCAT(?, '%')  AND m_filename NOT LIKE CONCAT(?, '%/%') $sql_external)";
			$args[] = Filter::escapeLike($folder);
			$args[] = Filter::escapeLike($folder);
			break;
		default:
			throw new \Exception('Bad argument (subfolders=' . $subfolders . ') in QueryMedia::mediaList()');
		}

		// Apply search terms
		if ($filter) {
			$sql .= " AND (SUBSTRING_INDEX(m_filename, '/', -1) LIKE CONCAT('%', ?, '%') OR m_titl LIKE CONCAT('%', ?, '%'))";
			$args[] = Filter::escapeLike($filter);
			$args[] = Filter::escapeLike($filter);
		}

		if ($form_type) {
			$sql .= " AND (m_gedcom LIKE CONCAT('%\n3 TYPE ', ?, '%'))";
			$args[] = $form_type;
		}

		switch ($sort) {
		case 'file':
			$sql .= " ORDER BY m_filename";
			break;
		case 'title':
			$sql .= " ORDER BY m_titl";
			break;
		default:
			throw new \Exception('Bad argument (sort=' . $sort . ') in QueryMedia::mediaList()');
		}

		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		$list = array();
		foreach ($rows as $row) {
			$media = Media::getInstance($row->xref, $WT_TREE, $row->gedcom);
			if ($media->canShow()) {
				$list[] = $media;
			}
		}

		return $list;
	}
}

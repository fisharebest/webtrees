<?php

/**
 * Class WT_Query_Media - generate lists of files for admin_media.php
 *
 * @package   webtrees
 * @copyright (c) 2014 webtrees development team
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
 */
class WT_Query_Media {
	/**
	 * Generate a list of all the folders in the current tree - for the media list.
	 *
	 * @return string[]
	 */
	public static function folderList() {
		$folders = WT_DB::prepare(
			"SELECT SQL_CACHE LEFT(m_filename, CHAR_LENGTH(m_filename) - CHAR_LENGTH(SUBSTRING_INDEX(m_filename, '/', -1))) AS media_path" .
			" FROM  `##media`" .
			" WHERE m_file = ?" .
			" AND   m_filename NOT LIKE 'http://%'" .
			" AND   m_filename NOT LIKE 'https://%'" .
			" GROUP BY 1" .
			" ORDER BY 1"
		)->execute(array(WT_GED_ID))->fetchOneColumn();

		if (!$folders || reset($folders)!='') {
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
		$folders = WT_DB::prepare(
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
	 *
	 * @return WT_Media[]
	 * @throws Exception
	 */
	public static function mediaList($folder, $subfolders, $sort, $filter) {
		// All files in the folder, plus external files
		$sql =
			"SELECT m_id AS xref, m_file AS gedcom_id, m_gedcom AS gedcom" .
			" FROM `##media`" .
			" WHERE m_file=?";
		$args = array(
			WT_GED_ID,
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
			$args[] = WT_Filter::escapeLike($folder);
			break;
		case 'exclude':
			$sql .= " AND (m_filename LIKE CONCAT(?, '%')  AND m_filename NOT LIKE CONCAT(?, '%/%') $sql_external)";
			$args[] = WT_Filter::escapeLike($folder);
			$args[] = WT_Filter::escapeLike($folder);
			break;
		default:
			throw new Exception('Bad argument (subfolders=' . $subfolders . ') in WT_Query_Media::mediaList()');
		}

		// Apply search terms
		if ($filter) {
			$sql .= " AND (SUBSTRING_INDEX(m_filename, '/', -1) LIKE CONCAT('%', ?, '%') OR m_titl LIKE CONCAT('%', ?, '%'))";
			$args[] = WT_Filter::escapeLike($filter);
			$args[] = WT_Filter::escapeLike($filter);
		}

		switch ($sort) {
		case 'file':
			$sql .= " ORDER BY m_filename";
			break;
		case 'title':
			$sql .= " ORDER BY m_titl";
			break;
		default:
			throw new Exception('Bad argument (sort=' . $sort . ') in WT_Query_Media::mediaList()');
		}

		$rows = WT_DB::prepare($sql)->execute($args)->fetchAll();
		$list = array();
		foreach ($rows as $row) {
			$media = WT_Media::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
			if ($media->canShow()) {
				$list[] = $media;
			}
		}
		return $list;
	}
}

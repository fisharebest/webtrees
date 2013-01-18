<?php
// Static functions to support media lists
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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

class WT_Query_Media {
	// Generate a list of all the folders in the current tree - for the media list.
	public static function folderList() {
		$folders = WT_DB::prepare(
			"SELECT SQL_CACHE LEFT(m_filename, CHAR_LENGTH(m_filename) - CHAR_LENGTH(SUBSTRING_INDEX(m_filename, '/', -1))) AS media_path" .
			" FROM  `##media`" .
			" WHERE m_file = ?" .
			"	AND   m_filename NOT LIKE 'http://%'" .
			" AND   m_filename NOT LIKE 'https://%'" .
			" GROUP BY 1" .
			" ORDER BY 1"
		)->execute(array(WT_GED_ID))->fetchOneColumn();

		return array_combine($folders, $folders);
	}

	// Generate a list of all folders from all the trees - for the media admin.
	public static function folderListAll() {
		$folders = WT_DB::prepare(
			"SELECT SQL_CACHE LEFT(m_filename, CHAR_LENGTH(m_filename) - CHAR_LENGTH(SUBSTRING_INDEX(m_filename, '/', -1))) AS media_path" .
			" FROM  `##media`" .
			" WHERE m_filename NOT LIKE 'http://%'" .
			" AND   m_filename NOT LIKE 'https://%'" .
			" GROUP BY 1" .
			" ORDER BY 1"
		)->execute()->fetchOneColumn();

		return array_combine($folders, $folders);
	}

	// Generate a filtered, sourced, privacy-checked list of media objects - for the media list.
	public static function mediaList($folder, $subfolders, $sort, $filter) {
		$sql = 
			"SELECT 'OBJE' AS type, m_id AS xref, m_file AS ged_id, m_gedcom AS gedrec, m_titl, m_filename" .
			" FROM `##media`" .
			" WHERE m_file=?" .
			" AND   (m_filename LIKE CONCAT(?, '%', ?, '%')" .
			"  OR   m_filename LIKE CONCAT('http://%', ?, '%')" .
			"  OR   m_filename LIKE CONCAT('https://%', ?, '%')" .
			"  OR   m_titl LIKE CONCAT('%', ?, '%')" .
			" )";
		$args = array(
			WT_GED_ID,
			$folder,
			$filter,
			$filter,
			$filter,
			$filter,
		);

		switch ($subfolders) {
		case 'include':
			// subfolders are included by default
			break;
		case 'exclude':
			$sql .= " AND m_filename NOT LIKE CONCAT(?, '%/%')";
			$args[] = $folder;
			break;
		default:
			throw new Exception('Bad argument (subfolders=', $subfolders, ') in WT_Query_Media::mediaList()');
		}

		switch ($sort) {
		case 'file':
			$sql .= " ORDER BY m_filename";
			break;
		case 'title':
			$sql .= " ORDER BY m_titl";
			break;
		default:
			throw new Exception('Bad argument (sort=', $sort, ') in WT_Query_Media::mediaList()');
		}

		$rows = WT_DB::prepare($sql)->execute($args)->fetchAll(PDO::FETCH_ASSOC);
		$list = array();
		foreach ($rows as $row) {
			$media = WT_Media::getInstance($row);
			if ($media->canDisplayDetails()) {
				$list[] = $media;
			}
		}
		return $list;
	}
}

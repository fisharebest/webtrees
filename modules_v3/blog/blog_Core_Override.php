<?php

// functions used in the blog module that override functions of the
// same name in core webtrees code
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
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
// module developed by David Drury

use \WT\Auth;

class blog_DB {

	/**
	 * Adds a news item to the database
	 *
	 * @param stdClass $news a news item array
	 *
	 * @return void
	 */
	public static function addNews($news) {
		if ($news->news_id) {
			WT_DB::prepare(
				"UPDATE `##news`" .
				" SET body=:body, languages=:languages" .
				" WHERE news_id=:news_id")
			     ->execute(array('body' => $news->body, 'languages' => $news->languages, 'news_id' => $news->news_id));
		} else {
			WT_DB::prepare(
				"INSERT INTO `##news` (user_id, gedcom_id, body, languages)" .
				" VALUES (NULLIF(:user_id, ''), NULLIF(:gedcom_id, ''), :body, :languages)")
			     ->execute(array('user_id' => $news->user_id, 'gedcom_id' => $news->gedcom_id, 'body' => $news->body, 'languages' => $news->languages));
		}
	}

	/**
	 * Deletes a news item from the database
	 *
	 * @author John Finlay
	 *
	 * @param int $news_id
	 *
	 * @return bool
	 */
	public static function deleteNews($news_id) {
		return (bool)WT_DB::prepare(
				"DELETE FROM `##news` WHERE news_id=:news_id"
			)
			->execute(array('news_id' => $news_id));
	}

	/**
	 * Gets the news items for the given user or gedcom
	 *
	 * @param string $ctype
	 *
	 * @param bool $showAll
	 *
	 * @return array
	 */
	public static function getNews($ctype, $showAll = false) {
		$id = ($ctype === 'gedcom') ? WT_GED_ID : Auth::id();

		/* Note FIND_IN_SET is not particularly efficient but it is unlikely
		   that there will ever be more than a few records in the news table */
		$filter = $showAll ? '' : " AND FIND_IN_SET('" . WT_LOCALE . "', languages)";

		return WT_DB::prepare(
			"SELECT SQL_CACHE news_id, user_id, gedcom_id, languages, UNIX_TIMESTAMP(updated) AS updated, body" .
			" FROM `##news`" .
			" WHERE {$ctype}_id=:id{$filter}" .
			" ORDER BY updated DESC")
		            ->execute(array('id' => $id))
		            ->fetchAll();
	}

	/**
	 * Gets the news item for the given news id
	 *
	 * @param int $news_id
	 *
	 * @return stdClass
	 */
	public static function getNewsItem($news_id) {
		return WT_DB::prepare(
			"SELECT SQL_CACHE news_id, user_id, gedcom_id, languages, body" .
			" FROM `##news`" .
			" WHERE news_id=:news_id")
		            ->execute(array('news_id' => $news_id))
		            ->fetchOneRow();
	}

	/**
	 * static Function getNewsCount
	 *
	 * Gets the count of news items for the relevant type
	 * not currently used but could be used by library/WT/stats.php
	 *
	 * @param string $ctype
	 *
	 * @return int
	 */
	public static function getNewsCount($ctype) {

		$qry = WT_DB::prepare(
			"SELECT COUNT(*) AS count" .
			" FROM `##news`" .
			" WHERE {$ctype}_id IS NOT NULL")
		            ->execute()
		            ->fetchOneRow();
		return (int)$qry->count;
	}

	CONST CHUNK_SIZE = 4;

	/**
	 * static Function edit_language_checkboxes
	 * @param string $prefix
	 *
	 * @param string $languages
	 *
	 * @return string
	 */
	public static function edit_language_checkboxes($prefix, $languages) {
		$content = '<table>';
		foreach (array_chunk(WT_I18N::installed_languages(), self::CHUNK_SIZE, true) as $chunk) {
			$content .= '<tr>';
			foreach ($chunk as $locale => $name) {
				$content .= '<td>';
				$checked = strpos($languages, $locale) !== false ? 'checked="checked"' : '';
				$content .= sprintf('<input id="lang_%1$s" type="checkbox" name="%2$s[]" value="%1$s" %3$s><label for="lang_%1$s">%4$s</label>',
				                    $locale, $prefix, $checked, $name);
				$content .= '</td>';
			}
			$content .= '</tr>';
		}
		$content .= '</table>';
		return $content;
	}
}

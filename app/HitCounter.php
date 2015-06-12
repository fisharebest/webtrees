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
namespace Fisharebest\Webtrees;

/**
 * Measure page popularity.
 */
class HitCounter {
	/**
	 * Count the number of times this page has been viewed.
	 *
	 * @param Tree   $tree
	 * @param string $page
	 * @param string $parameter
	 *
	 * @return int
	 */
	public static function countHit(Tree $tree, $page, $parameter) {
		// Don't increment the counter while we stay on the same page.
		if (
			Session::get('last_tree_id') === $tree->getTreeId() &&
			Session::get('last_page') === $page &&
			Session::get('last_parameter') === $parameter
		) {
			return Session::get('last_count');
		}

		$page_count = self::getCount($tree, $page, $parameter);

		if ($page_count === 0) {
			Database::prepare(
				"INSERT INTO `##hit_counter` (gedcom_id, page_name, page_parameter, page_count)" .
				" VALUES (:tree_id, :page, :parameter, 1)"
			)->execute(array(
				'tree_id'   => $tree->getTreeId(),
				'page'      => $page,
				'parameter' => $parameter,
			));
		} else {
			Database::prepare(
				"UPDATE `##hit_counter` SET page_count = page_count + 1" .
				" WHERE gedcom_id = :tree_id AND page_name = :page AND page_parameter = :parameter"
			)->execute(array(
				'tree_id'   => $tree->getTreeId(),
				'page'      => $page,
				'parameter' => $parameter,
			));
		}

		$page_count++;

		Session::put('last_tree_id', $tree->getTreeId());
		Session::put('last_page', $page);
		Session::put('last_parameter', $parameter);
		Session::put('last_count', $page_count);

		return $page_count;
	}

	/**
	 * How many times has a page been viewed
	 *
	 * @param Tree   $tree
	 * @param string $page
	 * @param string $parameter
	 *
	 * @return int
	 */
	public static function getCount(Tree $tree, $page, $parameter) {
		return (int) Database::prepare(
			"SELECT page_count FROM `##hit_counter`" .
			" WHERE gedcom_id = :tree_id AND page_name = :page AND page_parameter = :parameter"
		)->execute(array(
			'tree_id'   => $tree->getTreeId(),
			'page'      => $page,
			'parameter' => $parameter,
		))->fetchOne();
	}
}

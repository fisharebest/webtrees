<?php
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

/**
 * Class WT_Place - Gedcom Place functionality.
 */
class WT_Place {
	const GEDCOM_SEPARATOR = ', ';
	private $gedcom_place;  // e.g. array("Westminster", "London", "England")
	private $gedcom_id;     // We may have the same place in different trees

	/**
	 * @param string  $gedcom_place
	 * @param integer $gedcom_id
	 */
	public function __construct($gedcom_place, $gedcom_id) {
		if ($gedcom_place) {
			$this->gedcom_place=explode(self::GEDCOM_SEPARATOR, $gedcom_place);
		} else {
			// Empty => "Top level"
			$this->gedcom_place=array();
		}
		$this->gedcom_id=$gedcom_id;
	}

	/**
	 * @return integer
	 */
	public function getPlaceId() {
		$place_id = 0;
		foreach (array_reverse($this->gedcom_place) as $place) {
			$place_id = WT_DB::prepare(
				"SELECT SQL_CACHE p_id FROM `##places` WHERE p_parent_id=? AND p_place=? AND p_file=?"
			)->execute(array($place_id, $place, $this->gedcom_id))->fetchOne();
		}

		return $place_id;
	}

	/**
	 * @return WT_Place
	 */
	public function getParentPlace() {
		return new WT_Place(implode(self::GEDCOM_SEPARATOR, array_slice($this->gedcom_place, 1)), $this->gedcom_id);
	}

	/**
	 * @return WT_Place[]
	 */
	public function getChildPlaces() {
		$children = array();
		if ($this->getPlaceId()) {
			$parent_text = self::GEDCOM_SEPARATOR . $this->getGedcomName();
		} else {
			$parent_text = '';
		}

		$rows =
			WT_DB::prepare("SELECT SQL_CACHE p_place FROM `##places` WHERE p_parent_id=? AND p_file=? ORDER BY p_place COLLATE '".WT_I18N::$collation."'")
			->execute(array($this->getPlaceId(), $this->gedcom_id))
			->fetchOneColumn();
		foreach ($rows as $row) {
			$children[] = new WT_Place($row . $parent_text, $this->gedcom_id);
		}

		return $children;
	}

	/**
	 * @return string
	 */
	public function getURL() {
		$url = 'placelist.php';
		foreach (array_reverse($this->gedcom_place) as $n => $place) {
			$url .= $n ? '&amp;' : '?';
			$url .= 'parent%5B%5D=' . rawurlencode($place);
		}
		$url .= '&amp;ged=' . rawurlencode(get_gedcom_from_id($this->gedcom_id));

		return $url;
	}

	/**
	 * @return string
	 */
	public function getGedcomName() {
		return implode(self::GEDCOM_SEPARATOR, $this->gedcom_place);
	}

	/**
	 * @return string
	 */
	public function getPlaceName() {
		$place = reset($this->gedcom_place);

		return $place ? '<span dir="auto">' . WT_Filter::escapeHtml($place) . '</span>' : WT_I18N::translate('unknown');
	}

	/**
	 * @return bool
	 */
	public function isEmpty() {
		return empty($this->gedcom_place);
	}

	/**
	 * @return string
	 */
	public function getFullName() {
		if (true) {
			// If a place hierarchy is a single entity
			return '<span dir="auto">' . WT_Filter::escapeHtml(implode(WT_I18N::$list_separator, $this->gedcom_place)) . '</span>';
		} else {
			// If a place hierarchy is a list of distinct items
			$tmp = array();
			foreach ($this->gedcom_place as $place) {
				$tmp[] = '<span dir="auto">' . WT_Filter::escapeHtml($place) . '</span>';
			}

			return implode(WT_I18N::$list_separator, $tmp);
		}
	}

	/**
	 * For lists and charts, where the full name won’t fit.
	 *
	 * @return string
	 */
	public function getShortName() {
		global $SHOW_PEDIGREE_PLACES, $SHOW_PEDIGREE_PLACES_SUFFIX;

		if ($SHOW_PEDIGREE_PLACES >= count($this->gedcom_place)) {
			// A short place name - no need to abbreviate
			return $this->getFullName();
		} else {
			// Abbreviate the place name, for lists
			if ($SHOW_PEDIGREE_PLACES_SUFFIX) {
				// The *last* $SHOW_PEDIGREE_PLACES components
				$short_name=implode(self::GEDCOM_SEPARATOR, array_slice($this->gedcom_place, -$SHOW_PEDIGREE_PLACES));
			} else {
				// The *first* $SHOW_PEDIGREE_PLACES components
				$short_name=implode(self::GEDCOM_SEPARATOR, array_slice($this->gedcom_place, 0, $SHOW_PEDIGREE_PLACES));
			}
			// Add a tool-tip showing the full name
			return '<span title="'.WT_Filter::escapeHtml($this->getGedcomName()).'" dir="auto">'.WT_Filter::escapeHtml($short_name).'</span>';
		}
	}

	/**
	 * For the "view all" option of placelist.php and find.php
	 *
	 * @return string
	 */
	public function getReverseName() {
		$tmp = array();
		foreach (array_reverse($this->gedcom_place) as $place) {
			$tmp[] = '<span dir="auto">' . WT_Filter::escapeHtml($place) . '</span>';
		}

		return implode(WT_I18N::$list_separator, $tmp);
	}

	/**
	 * @param integer $gedcom_id
	 *
	 * @return string[]
	 */
	public static function allPlaces($gedcom_id) {
		$places=array();
		$rows=
			WT_DB::prepare(
				"SELECT SQL_CACHE CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place)".
				" FROM      `##places` AS p1".
				" LEFT JOIN `##places` AS p2 ON (p1.p_parent_id=p2.p_id)".
				" LEFT JOIN `##places` AS p3 ON (p2.p_parent_id=p3.p_id)".
				" LEFT JOIN `##places` AS p4 ON (p3.p_parent_id=p4.p_id)".
				" LEFT JOIN `##places` AS p5 ON (p4.p_parent_id=p5.p_id)".
				" LEFT JOIN `##places` AS p6 ON (p5.p_parent_id=p6.p_id)".
				" LEFT JOIN `##places` AS p7 ON (p6.p_parent_id=p7.p_id)".
				" LEFT JOIN `##places` AS p8 ON (p7.p_parent_id=p8.p_id)".
				" LEFT JOIN `##places` AS p9 ON (p8.p_parent_id=p9.p_id)".
				" WHERE p1.p_file=?".
				" ORDER BY CONCAT_WS(', ', p9.p_place, p8.p_place, p7.p_place, p6.p_place, p5.p_place, p4.p_place, p3.p_place, p2.p_place, p1.p_place) COLLATE '".WT_I18N::$collation."'"
			)
			->execute(array($gedcom_id))
			->fetchOneColumn();
		foreach ($rows as $row) {
			$places[]=new WT_Place($row, $gedcom_id);
		}
		return $places;
	}

	/**
	 * @param string  $filter
	 * @param integer $gedcom_id
	 *
	 * @return WT_Place[]
	 */
	public static function findPlaces($filter, $gedcom_id) {
		$places=array();
		$rows=
			WT_DB::prepare(
				"SELECT SQL_CACHE CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place)".
				" FROM      `##places` AS p1".
				" LEFT JOIN `##places` AS p2 ON (p1.p_parent_id=p2.p_id)".
				" LEFT JOIN `##places` AS p3 ON (p2.p_parent_id=p3.p_id)".
				" LEFT JOIN `##places` AS p4 ON (p3.p_parent_id=p4.p_id)".
				" LEFT JOIN `##places` AS p5 ON (p4.p_parent_id=p5.p_id)".
				" LEFT JOIN `##places` AS p6 ON (p5.p_parent_id=p6.p_id)".
				" LEFT JOIN `##places` AS p7 ON (p6.p_parent_id=p7.p_id)".
				" LEFT JOIN `##places` AS p8 ON (p7.p_parent_id=p8.p_id)".
				" LEFT JOIN `##places` AS p9 ON (p8.p_parent_id=p9.p_id)".
				" WHERE CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place) LIKE CONCAT('%', ?, '%') AND CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place) NOT LIKE CONCAT('%,%', ?, '%') AND p1.p_file=?".
				" ORDER BY  CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place) COLLATE '".WT_I18N::$collation."'"
			)
			->execute(array($filter, preg_quote($filter), $gedcom_id))
			->fetchOneColumn();
		foreach ($rows as $row) {
			$places[]=new WT_Place($row, $gedcom_id);
		}
		return $places;
	}
}

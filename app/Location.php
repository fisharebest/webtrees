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
declare(strict_types = 1);

namespace Fisharebest\Webtrees;

class Location {

	/**
	 * @var \stdClass $record
	 */
	protected $record;

	/**
	 * Location constructor.
	 * @param string $gedcomName
	 * @throws \Exception
	 */
	public function __construct($gedcomName) {
		$this->record = $this->getRecordFromName($gedcomName);
	}

	/**
	 * @return bool
	 */
	public function knownLatLon() {
		return ($this->record->pl_lati && $this->record->pl_long);
	}

	/**
	 * @param string $format
	 *
	 * @return string|float
	 */
	public function getLat($format = 'signed') {
		switch ($format) {
			case 'signed':
				return (float) strtr($this->record->pl_lati, ['N' => '', 'S' => '-', ',' => '.']);
			default:
				return $this->record->pl_lati;
		}
	}

	/**
	 * @param string $format
	 *
	 * @return string|float
	 */
	public function getLon($format = 'signed') {
		switch ($format) {
			case 'signed':
				return (float) strtr($this->record->pl_long, ['E' => '', 'W' => '-', ',' => '.']);
			default:
				return $this->record->pl_long;
		}
	}

	/**
	 * @return array
	 */
	public function getLatLonJSArray() {
		return [$this->getLat('signed'), $this->getLon('signed')];
	}

	/**
	 * GeoJSON requires the parameters to be in the order longitude, latitude
	 *
	 * @return array
	 */
	public function getGeoJsonCoords() {
		return [$this->getLon('signed'), $this->getLat('signed')];
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->record->pl_id;
	}

	/**
	 * @return string
	 */
	public function getLevel() {
		return $this->record->pl_level;
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return $this->record->pl_id !== 0;
	}

	/**
	 * @return string
	 */
	public function getPlace() {
		return $this->record->pl_place;
	}

	/**
	 * @return string|null
	 */
	public function getZoom() {
		return $this->record->pl_zoom;
	}

	/**
	 * @return string|null
	 */
	public function getIcon() {
		return $this->record->pl_icon;
	}

	/**
	 * @return \stdClass
	 */
	public function getRecord() {
		return $this->record;
	}

	/**
	 * @param $place array
	 * @throws \Exception
	 */
	public function add($place) {
		$nextId = Database::prepare("SELECT IFNULL(MAX(pl_id)+1, 1) FROM `##placelocation`")
			->execute()
			->fetchOne();

		// place doesn't exist so find it's parent
		if ($place['level'] === 0) {
			$parent_id = 0;
			$placename = $place['fqpn'];
		} else {
			$newarr       = explode(Place::GEDCOM_SEPARATOR, $place['fqpn']);
			$placename    = array_shift($newarr);
			$parentPlace  = implode(Place::GEDCOM_SEPARATOR, $newarr);
			$parentRecord = $this->getRecordFromName($parentPlace);
			$parent_id    = $parentRecord->pl_id;
		}

		Database::prepare(
			"INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon)
							VALUES(:id, :parent_id, :level, :place, :long, :lati, :zoom, :icon)"
		)->execute(
			[
			'id'        => $nextId,
			'parent_id' => $parent_id,
			'level'     => $place['level'],
			'place'     => $placename,
			'long'      => $place['longitude'] ? $place['longitude'] : null,
			'lati'      => $place['latitude'] ? $place['latitude'] : null,
			'zoom'      => $place['zoom'] ? $place['zoom'] : null,
			'icon'      => $place['icon'] ? $place['icon'] : null,
		]
		);
	}

	/**
	 * @param $place array
	 * @throws \Exception
	 */
	public function update($place) {
		Database::prepare(
			"UPDATE `##placelocation` SET pl_level=:level, pl_lati=:lati, pl_long=:long, pl_zoom=:zoom, pl_icon=:icon WHERE pl_id=:id"
		)
			->execute([
				'level' => $place['level'],
				'lati'  => $place['latitude'] ? $place['latitude'] : null,
				'long'  => $place['longitude'] ? $place['longitude'] : null,
				'zoom'  => $place['zoom'] ? $place['zoom'] : null,
				'icon'  => $place['icon'] ? $place['icon'] : null,
				'id'    => $this->record->pl_id,
			]);
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function hasChildren() {
		return (bool) Database::prepare(
			"SELECT count(pl_id)>0 FROM `##placelocation`" .
				" WHERE pl_lati IS NOT NULL AND pl_long IS NOT NULL AND pl_parent_id=:id"
		)
			->execute(['id' => $this->record->pl_id])
			->fetchOne();
	}

	const OFFSET = 1;

	/**
	 * @return \stdClass[]|string[][]
	 * @throws \Exception
	 */
	public function getChildren() {
		$place_array = preg_split('/, ?/', $this->record->fqpn);
		$where       = '';

		if ($this->record->fqpn === '') {
			$where = " AND pl0.pl_parent_id=0 AND p0.p_id IS NOT NULL";
		} else {
			foreach ($place_array as $ndx => $place_part) {
				$where .= sprintf(
					' AND pl%s.pl_place = "%s" AND p%s.p_id IS NOT NULL',
					$ndx + self::OFFSET,
					$place_part,
					$ndx
				);
			}
		}

		$query = "SELECT SQL_CACHE pl0.pl_place, pl0.pl_long, pl0.pl_lati, pl0.pl_zoom, pl0.pl_icon" .
			" FROM `##placelocation` AS pl0" .
			" LEFT JOIN `##placelocation` AS pl1 ON (pl0.pl_parent_id = pl1.pl_id)" .
			" LEFT JOIN `##placelocation` AS pl2 ON (pl1.pl_parent_id = pl2.pl_id)" .
			" LEFT JOIN `##placelocation` AS pl3 ON (pl2.pl_parent_id = pl3.pl_id)" .
			" LEFT JOIN `##placelocation` AS pl4 ON (pl3.pl_parent_id = pl4.pl_id)" .
			" LEFT JOIN `##placelocation` AS pl5 ON (pl4.pl_parent_id = pl5.pl_id)" .
			" LEFT JOIN `##placelocation` AS pl6 ON (pl5.pl_parent_id = pl6.pl_id)" .
			" LEFT JOIN `##placelocation` AS pl7 ON (pl6.pl_parent_id = pl7.pl_id)" .
			" LEFT JOIN `##placelocation` AS pl8 ON (pl7.pl_parent_id = pl8.pl_id)" .
			" LEFT JOIN `##places` AS p0 ON (p0.p_place = pl0.pl_place)" .
			" LEFT JOIN `##places` AS p1 ON (p1.p_place = pl1.pl_place)" .
			" LEFT JOIN `##places` AS p2 ON (p2.p_place = pl2.pl_place)" .
			" LEFT JOIN `##places` AS p3 ON (p3.p_place = pl3.pl_place)" .
			" LEFT JOIN `##places` AS p4 ON (p4.p_place = pl4.pl_place)" .
			" LEFT JOIN `##places` AS p5 ON (p5.p_place = pl5.pl_place)" .
			" LEFT JOIN `##places` AS p6 ON (p6.p_place = pl6.pl_place)" .
			" LEFT JOIN `##places` AS p7 ON (p7.p_place = pl7.pl_place)" .
			" WHERE pl0.pl_lati IS NOT NULL AND pl0.pl_long IS NOT NULL " . $where .
			" ORDER BY pl0.pl_place";

		return Database::prepare($query)
			->execute()
			->fetchAll();
	}

	/**
	 * @param string $gedcomName
	 * @return null|object|\stdClass
	 * @throws \Exception
	 */
	private function getRecordFromName($gedcomName) {
		$data = Database::prepare(
			"
				SELECT
				  CONCAT_WS(:separator, t1.pl_place, t2.pl_place, t3.pl_place, t4.pl_place, t5.pl_place, t6.pl_place, t7.pl_place, t8.pl_place) AS fqpn,
					t1.pl_level, t1.pl_place, t1.pl_id, t1.pl_parent_id, t1.pl_lati, t1.pl_long, t1.pl_zoom, t1.pl_icon
				FROM `##placelocation` AS t1
				LEFT JOIN `##placelocation` AS t2 ON t1.pl_parent_id = t2.pl_id
				LEFT JOIN `##placelocation` AS t3 ON t2.pl_parent_id = t3.pl_id
				LEFT JOIN `##placelocation` AS t4 ON t3.pl_parent_id = t4.pl_id
				LEFT JOIN `##placelocation` AS t5 ON t4.pl_parent_id = t5.pl_id
				LEFT JOIN `##placelocation` AS t6 ON t5.pl_parent_id = t6.pl_id
				LEFT JOIN `##placelocation` AS t7 ON t6.pl_parent_id = t7.pl_id
				LEFT JOIN `##placelocation` AS t8 ON t7.pl_parent_id = t8.pl_id
				HAVING fqpn=:gedcomName;
			   "
		)
			->execute(
				[
					'separator'  => Place::GEDCOM_SEPARATOR,
					'gedcomName' => $gedcomName,
				]
			)
			->fetchOneRow();
		if ($data) {
			$place = $data;
		} else {
			$place = (object) [
				'fqpn'         => '',
				'pl_id'        => 0,
				'pl_parent_id' => 0,
				'pl_level'     => null,
				'pl_place'     => '',
				'pl_long'      => null,
				'pl_lati'      => null,
				'pl_zoom'      => null,
				'pl_icon'      => null,
			];
		}
		return $place;
	}
}

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
	 * @param array $record
	 * @throws \Exception
	 */
	public function __construct($gedcomName, $record=[]) {
		$tmp = $this->getRecordFromName($gedcomName);
		if ($tmp !== null) {
			$this->record = $tmp;
		} elseif (!empty($record)) {
			$this->record = (object)$record;
		} else {
			$this->record = (object)[
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
				return $this->record->pl_lati ?
					(float)strtr($this->record->pl_lati, ['N' => '', 'S' => '-', ',' => '.']) :
					$this->record->pl_lati;
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
				return $this->record->pl_long ?
					(float)strtr($this->record->pl_long, ['E' => '', 'W' => '-', ',' => '.']) :
					$this->record->pl_long;
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
	 * @throws \Exception
	 */
	public function add() {
		$this->record->pl_id = Database::prepare("SELECT IFNULL(MAX(pl_id)+1, 1) FROM `##placelocation`")
			->execute()
			->fetchOne();

		Database::prepare(
			"INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon)
							VALUES(:id, :parent_id, :level, :place, :long, :lati, :zoom, :icon)"
		)->execute(
			[
				'id'        => $this->record->pl_id,
				'parent_id' => $this->record->pl_parent_id,
				'level'     => $this->record->pl_level,
				'place'     => $this->record->pl_place,
				'long'      => $this->record->pl_long ?? null,
				'lati'      => $this->record->pl_lati ?? null,
				'zoom'      => $this->record->pl_zoom ?? null,
				'icon'      => $this->record->pl_icon ?? null,
			]
		);

		return $this->record->pl_id;
	}

	/**
	 * @param \stdClass $new_data
	 * @throws \Exception
	 */
	public function update($new_data) {
		Database::prepare(
			"UPDATE `##placelocation` SET pl_lati=:lati, pl_long=:long, pl_zoom=:zoom, pl_icon=:icon WHERE pl_id=:id"
		)
			->execute([
				'lati'  => $new_data->pl_lati ?? $this->record->pl_lati,
				'long'  => $new_data->pl_long ?? $this->record->pl_long,
				'zoom'  => $new_data->pl_zoom ?? $this->record->pl_zoom,
				'icon'  => $new_data->pl_icon ?? $this->record->pl_icon,
				'id'    => $this->record->pl_id,
			]);
	}

	/**
	 * @param string $gedcomName
	 * @return null|\stdClass
	 * @throws \Exception
	 */
	private function getRecordFromName($gedcomName) {

		return Database::prepare(
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
	}
}

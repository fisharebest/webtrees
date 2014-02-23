<?php
//declare(encoding = 'UTF-8');

// Classes and libraries for module system
//
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

/**
 * Helper class to handle places administration.
 *
 * @category Module
 * @package  Googlemap
 * @author   Rico Sonntag <mail@ricosonntag.de>
 * @link     http://webtrees.com
 */
class Googlemap_AdminPlaces
{
	/**
	 * Get maximum place location record id.
	 *
	 * @return integer
	 */
	protected static function getHighestIndex()
	{
		return (int) WT_DB::prepare('SELECT MAX(pl_id) FROM `##placelocation`')
			->fetchOne();
	}

	/**
	 * Get maximum place location level.
	 *
	 * @return integer
	 */
	protected static function getHighestLevel()
	{
		return (int) WT_DB::prepare('SELECT MAX(pl_level) FROM `##placelocation`')
			->fetchOne();
	}

	/**
	 * Recursively print out all place locations starting at a given
	 * parent record id.
	 *
	 * @param integer $parentId Parent record id
	 *
	 * @return void
	 */
	protected static function outputLevel($parentId)
	{
		$tmp      = self::placeIdToHierarchy($parentId);
		$maxLevel = self::getHighestLevel();

		if ($maxLevel > 8) {
			$maxLevel = 8;
		}

		$prefix = implode(';', $tmp);

		if ($prefix != '') {
			$prefix .= ';';
		}

		$suffix = str_repeat(';', $maxLevel - count($tmp));
		$level  = count($tmp);

		$rows = WT_DB::prepare(
			'SELECT pl_id, pl_place, pl_long, pl_lati, pl_zoom, pl_icon'
			. ' FROM `##placelocation`'
			. ' WHERE pl_parent_id = ?'
			. ' ORDER BY pl_place'
		)->execute(array($parentId))
			->fetchAll();

		foreach ($rows as $row) {
			echo $level, ';', $prefix, $row->pl_place, $suffix, ';',
				$row->pl_long, ';', $row->pl_lati, ';', $row->pl_zoom, ';',
				$row->pl_icon, "\r\n";

			if ($level < $maxLevel) {
				self::outputLevel($row->pl_id);
			}
		}
	}

	/**
	 * Recursively find all of the csv files on the server.
	 *
	 * @param array  $placefiles Array of files found
	 * @param string $path       Path where to search files
	 *
	 * @return array
	 */
	protected static function findFiles(array $placefiles, $path)
	{
		if (file_exists($path)) {
			$dir = dir($path);

			while (false !== ($entry = $dir->read())) {
				if (($entry != '.') && ($entry != '..') && ($entry != '.svn')) {
					if (is_dir($path . '/' . $entry)) {
						$placefiles
							= self::findFiles($placefiles, $path . '/' . $entry);
					} elseif (strstr($entry, '.csv') !== false) {
						$placefiles[] = preg_replace(
							'~' . WT_MODULES_DIR . 'googlemap/extra~',
							'',
							$path
						) . '/' . $entry;
					}
				}
			}

			$dir->close();
		}

		return $placefiles;
	}

	/**
	 * Take a place id and find its place in the hierarchy. Returns ordered
	 * array of id=>name values, starting with the top level.
	 *
	 * array(
	 *     0   => 'Top Level',
	 *     16  => 'England',
	 *     19  => 'London',
	 *     217 => 'Westminster'
	 * );
	 *
	 * @param integer $placeId Place id
	 *
	 * @return array
	 */
	public static function placeIdToHierarchy($placeId)
	{
		$statement = WT_DB::prepare(
			'SELECT pl_parent_id, pl_place'
			. ' FROM `##placelocation`'
			. ' WHERE pl_id = ?'
		);

		$result = array();

		while ($placeId != 0) {
			$row     = $statement->execute(array($placeId))->fetchOneRow();
			$result  = array($placeId => $row->pl_place) + $result;
			$placeId = $row->pl_parent_id;
		}

		return $result;
	}

	/**
	 * Find all of the places in the hierarchy.
	 *
	 * @param integer $parentId Parent record id
	 * @param boolean $inactive Set to TRUE to return also inactive locations
	 *
	 * @return array
	 */
	public static function getPlaceLocationList($parentId, $inactive = false)
	{
		if ($inactive) {
			$statement = WT_DB::prepare(
				'SELECT pl_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon'
				. ' FROM `##placelocation`'
				. ' WHERE pl_parent_id = ?'
				. ' ORDER BY pl_place'
				. ' COLLATE ' . WT_I18N::$collation
			);
		} else {
			$statement = WT_DB::prepare(
				'SELECT DISTINCT pl_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon'
				. ' FROM `##placelocation`'
				. ' INNER JOIN `##places`'
				. ' ON `##placelocation`.pl_place = `##places`.p_place'
				. ' WHERE pl_parent_id = ?'
				. ' ORDER BY pl_place'
				. ' COLLATE ' . WT_I18N::$collation
			);
		}

		$rows      = $statement->execute(array($parentId))->fetchAll();
		$placelist = array();

		foreach ($rows as $row) {
			// Count records of place
			$count = (int) WT_DB::prepare(
				'SELECT COUNT(pl_id)'
				. ' FROM `##placelocation`'
				. ' WHERE pl_parent_id = ?'
			)->execute(array($row->pl_id))
				->fetchOne();

			$placelist[] = array(
				'place_id' => $row->pl_id,
				'place'    => $row->pl_place,
				'lati'     => $row->pl_lati,
				'long'     => $row->pl_long,
				'zoom'     => $row->pl_zoom,
				'icon'     => $row->pl_icon,
				'count'    => $count,
			);
		}

		return $placelist;
	}

	/**
	 * Export all places belonging to the given parent id as csv.
	 *
	 * @param integer $parent Parent record id
	 *
	 * @return void
	 */
	public static function exportFile($parent)
	{
		Zend_Session::writeClose();

		$tmp      = self::placeIdToHierarchy($parent);
		$maxLevel = self::getHighestLevel();

		if ($maxLevel > 8) {
			$maxLevel = 8;
		}

		$tmp[0] = 'places';

		$outputFileName = preg_replace(
			'/[:;\/\\\(\)\{\}\[\] $]/',
			'_',
			implode('-', $tmp)
		) . '.csv';

		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $outputFileName . '"');

		echo '"', WT_I18N::translate('Level'), '";"',
			WT_I18N::translate('Country'), '";';

		if ($maxLevel > 0) {
			echo '"', WT_I18N::translate('State'), '";';
		}

		if ($maxLevel > 1) {
			echo '"', WT_I18N::translate('County'), '";';
		}

		if ($maxLevel > 2) {
			echo '"', WT_I18N::translate('City'), '";';
		}

		if ($maxLevel > 3) {
			echo '"', WT_I18N::translate('Place'), '";';
		}

		if ($maxLevel > 4) {
			echo '"', WT_I18N::translate('Place'), '";';
		}

		if ($maxLevel > 5) {
			echo '"', WT_I18N::translate('Place'), '";';
		}

		if ($maxLevel > 6) {
			echo '"', WT_I18N::translate('Place'), '";';
		}

		if ($maxLevel > 7) {
			echo '"', WT_I18N::translate('Place'), '";';
		}

		echo '"', WT_I18N::translate('Longitude'), '";"',
			WT_I18N::translate('Latitude'), '";';
		echo '"', WT_I18N::translate('Zoom level'), '";"',
			WT_I18N::translate('Icon'), '";', WT_EOL;

		self::outputLevel($parent);
		exit;
	}

	/**
	 * Import places from a selected gedcom.
	 *
	 * @return void
	 */
	public static function importGedcom()
	{
		$placeList = array();
		$j         = 0;

		$statement = WT_DB::prepare(
			'SELECT i_gedcom FROM `##individuals`'
			. ' WHERE i_file=? UNION ALL SELECT f_gedcom'
			. ' FROM `##families` WHERE f_file=?'
		)->execute(array(WT_GED_ID, WT_GED_ID));

		while ($gedRec = $statement->fetchColumn()) {
			$i = 1;

			// TODO
			$placeRec = get_sub_record(2, '2 PLAC', $gedRec, $i);

			while (!empty($placeRec)) {
				// Match place
				if (preg_match('/2 PLAC (.+)/', $placeRec, $matchPlace)) {
					$latitude  = null;
					$longitude = null;

					// Match latitude
					if (preg_match('/4 LATI (.*)/', $placeRec, $matchLati)) {
						$latitude = trim($matchLati[1]);

						if (($latitude[0] != 'N') && ($latitude[0] != 'S')) {
							if ($latitude < 0) {
								$latitude[0] = 'S';
							} else {
								$latitude = 'N' . $latitude;
							}
						}
					}

					// Match longitude
					if (preg_match('/4 LONG (.*)/', $placeRec, $matchLong)) {
						$longitude = trim($matchLong[1]);

						if (($longitude[0] != 'E') && ($longitude[0] != 'W')) {
							if ($longitude < 0) {
								$longitude[0] = 'W';
							} else {
								$longitude = 'E' . $longitude;
							}
						}
					}

					$placeList[] = array(
						'place' => trim($matchPlace[1]),
						'lati'  => $latitude,
						'long'  => $longitude,
					);
				}

				$i = $i + 1;

				// TODO
				$placeRec = get_sub_record(2, '2 PLAC', $gedRec, $i);
			}
		}

		asort($placeList);

		$prevPlace     = '';
		$prevLati      = '';
		$prevLong      = '';
		$placeListUniq = array();
		$j             = 0;

		foreach ($placeList as $place) {
			if ($place['place'] != $prevPlace) {
				$placeListUniq[] = array(
					'place' => $place['place'],
					'lati'  => $place['lati'],
					'long'  => $place['long'],
				);

				$j = $j + 1;
			} else {
				if (($place['place'] == $prevPlace)
					&& (($place['lati'] != $prevLati)
					|| ($place['long'] != $prevLong))
				) {
					if (($placeListUniq[$j - 1]['lati'] == 0)
						|| ($placeListUniq[$j - 1]['long'] == 0)
					) {
						$placeListUniq[$j - 1]['lati'] = $place['lati'];
						$placeListUniq[$j - 1]['long'] = $place['long'];
					} else {
						if (($place['lati'] != '0') || ($place['long'] != '0')) {
							echo 'Difference: previous value = ', $prevPlace, ', ',
								$prevLati, ', ', $prevLong, ' current = ',
								$place['place'], ', ', $place['lati'], ', ',
								$place['long'], '<br />';
						}
					}
				}
			}
			$prevPlace = $place['place'];
			$prevLati  = $place['lati'];
			$prevLong  = $place['long'];
		}

		$highestIndex     = self::getHighestIndex();
		$defaultZoomLevel = array(4, 7, 10, 12);

		foreach ($placeListUniq as $place) {
			$parent      = preg_split('/ *, */', $place['place']);
			$parent      = array_reverse($parent);
			$parentId    = 0;
			$parentCount = count($parent);

			for ($i = 0; $i < $parentCount; ++$i) {
				if (!isset($defaultZoomLevel[$i])) {
					$defaultZoomLevel[$i] = $defaultZoomLevel[$i - 1];
				}

				$escParent = $parent[$i];

				if ($escParent == '') {
					$escParent = 'Unknown';
				}

				$row = WT_DB::prepare(
					'SELECT pl_id, pl_long, pl_lati, pl_zoom'
					. ' FROM `##placelocation`'
					. ' WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ?'
				)->execute(array($i, $parentId, $escParent))
					->fetchOneRow();

				if ($i < ($parentCount - 1)) {
					// Create higher-level places, if necessary
					if (empty($row)) {
						++$highestIndex;

						WT_DB::prepare(
							'INSERT INTO `##placelocation`'
							. ' (pl_id, pl_parent_id, pl_level, pl_place, pl_zoom)'
							. ' VALUES (?, ?, ?, ?, ?)'
						)->execute(
							array(
								$highestIndex, $parentId, $i,
								$escParent, $defaultZoomLevel[$i]
							)
						);

						//echo WT_Filter::escapeHtml($escParent), '<br />';

						$parentId = $highestIndex;
					} else {
						$parentId = $row->pl_id;
					}
				} else {
					// Create lowest-level place, if necessary
					if (empty($row->pl_id)) {
						++$highestIndex;

						WT_DB::prepare(
							'INSERT INTO `##placelocation`'
							. ' (pl_id, pl_parent_id, pl_level, pl_place,'
							. ' pl_long, pl_lati, pl_zoom)'
							. ' VALUES (?, ?, ?, ?, ?, ?, ?)'
						)->execute(
							array(
								$highestIndex, $parentId, $i, $escParent,
								$place['long'], $place['lati'],
								$defaultZoomLevel[$i]
							)
						);

						//echo WT_Filter::escapeHtml($escParent), '<br />';
					} else {
						if (empty($row->pl_long)
							&& empty($row->pl_lati)
							&& ($place['lati'] != '0')
							&& ($place['long'] != '0')
						) {
							WT_DB::prepare(
								'UPDATE `##placelocation`'
								. ' SET pl_lati=?, pl_long=?'
								. ' WHERE pl_id=?'
							)->execute(
								array(
									$place['lati'], $place['long'], $row->pl_id
								)
							);

							//echo WT_Filter::escapeHtml($escParent), '<br />';
						}
					}
				}
			}
		}
	}

	/**
	 * Get list of all importable csv-files located on the server.
	 *
	 * @param string $dir Directory to search for files
	 *
	 * @return array
	 */
	public static function getImportablePlaceFiles($dir)
	{
		$placefiles = array();
		$placefiles = self::findFiles($placefiles, $dir);

		sort($placefiles);

		return $placefiles;
	}

	/**
	 * Performs the actual import of the selected csv file.
	 *
	 * @param string  $fileName      Name of file to import
	 * @param string  $flagDir       Directory containing flags
	 * @param integer $maxZoomLevel  Maximum configured zoom level
	 * @param boolean $clearDatabase Clear database before importing
	 * @param boolean $updateOnly    Update coordinates of existing locations,
	 *                               do not create new ones
	 * @param boolean $overwriteData Overwrite existing coordinates
	 *
	 * @return void
	 */
	public static function importFile(
		$fileName      = null,
		$flagDir       = null,
		$maxZoomLevel  = 20,
		$clearDatabase = false,
		$updateOnly    = false,
		$overwriteData = false
	) {
		$countryNames = array();

		foreach (WT_Stats::iso3166() as $key => $value) {
			$countryNames[$key] = WT_I18N::translate($key);
		}

		// Clear database before importing anything
		if ($clearDatabase) {
			WT_DB::exec('DELETE FROM `##placelocation` WHERE 1=1');
		}

		$lines = array();

		// Load file
		if ($fileName !== null) {
			$lines = file($fileName);
			$lines = array_map('trim', $lines);
		}

		// Strip BYTE-ORDER-MARK, if present
		if (!empty($lines[0]) && (substr($lines[0], 0, 3) == WT_UTF8_BOM)) {
			$lines[0] = substr($lines[0], 3);
		}

		asort($lines);

		$placeList = array();
		$fieldRec  = array();
		$maxLevel  = 0;

		// Get maximum level of places to import
		foreach ($lines as $placeRec) {
			$fieldRec = explode(';', $placeRec);

			if ($fieldRec[0] > $maxLevel) {
				$maxLevel = $fieldRec[0];
			}
		}

		$fields  = count($fieldRec);
		$setIcon = is_dir($flagDir);

		foreach ($lines as $placeRec) {
			$fieldRec = explode(';', $placeRec);

			if (is_numeric($fieldRec[0]) && ($fieldRec[0] <= $maxLevel)) {
				$place = array(
					'place' => '',
					'long'  => $fieldRec[$fields - 4],
					'lati'  => $fieldRec[$fields - 3],
					'zoom'  => $fieldRec[$fields - 2],
				);

				for ($j = $fields - 4; $j > 1; --$j) {
					if ($fieldRec[0] > ($j - 2)) {
						$place['place'] .= $fieldRec[$j] . ',';
					}
				}

				foreach ($countryNames as $countrycode => $countryName) {
					if ($countrycode == strtoupper($fieldRec[1])) {
						$fieldRec[1] = $countryName;
						break;
					}
				}

				$place['place'] .= $fieldRec[1];

				if ($setIcon) {
					$place['icon'] = trim($fieldRec[$fields - 1]);
				} else {
					$place['icon'] = '';
				}

				$placeList[] = $place;
			}
		}

		$prevPlace     = '';
		$prevLati      = '';
		$prevLong      = '';
		$placeListUniq = array();
		$j             = 0;

		foreach ($placeList as $place) {
			if ($place['place'] != $prevPlace) {
				$placeListUniq[] = array(
					'place' => $place['place'],
					'lati'  => $place['lati'],
					'long'  => $place['long'],
					'zoom'  => $place['zoom'],
					'icon'  => $place['icon'],
				);

				$j = $j + 1;
			} else {
				if (($place['place'] == $prevPlace)
					&& (($place['lati'] != $prevLati)
					|| ($place['long'] != $prevLong))
				) {
					if (($placeListUniq[$j - 1]['lati'] == 0)
						|| ($placeListUniq[$j - 1]['long'] == 0)
					) {
						$placeListUniq[$j - 1]['lati'] = $place['lati'];
						$placeListUniq[$j - 1]['long'] = $place['long'];
						$placeListUniq[$j - 1]['zoom'] = $place['zoom'];
						$placeListUniq[$j - 1]['icon'] = $place['icon'];
					} else {
						if (($place['lati'] != '0') || ($place['long'] != '0')) {
							echo 'Difference: previous value = ', $prevPlace,
								', ', $prevLati, ', ', $prevLong, ' current = ',
								$place['place'], ', ', $place['lati'], ', ',
								$place['long'], '<br />';
						}
					}
				}
			}

			$prevPlace = $place['place'];
			$prevLati  = $place['lati'];
			$prevLong  = $place['long'];
		}

		$highestIndex     = self::getHighestIndex();
		$defaultZoomLevel = array(4, 7, 10, 12);

		foreach ($placeListUniq as $place) {
			$parent      = explode(',', $place['place']);
			$parent      = array_reverse($parent);
			$parentId    = 0;
			$parentCount = count($parent);

			for ($i = 0; $i < $parentCount; ++$i) {
				$escParent = $parent[$i];

				if ($escParent == '') {
					$escParent = 'Unknown';
				}

				$row = WT_DB::prepare(
					'SELECT pl_id, pl_long, pl_lati, pl_zoom, pl_icon'
					. ' FROM `##placelocation`'
					. ' WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ?'
					. ' ORDER BY pl_place'
				)->execute(array($i, $parentId, $escParent))
					->fetchOneRow();

				// This name does not yet exist: create entry
				if (empty($row)) {
					if (!$updateOnly) {
						++$highestIndex;

						if (($i + 1) == $parentCount) {
							$zoomlevel = $place['zoom'];
						} elseif (isset($defaultZoomLevel[$i])) {
							$zoomlevel = $defaultZoomLevel[$i];
						} else {
							$zoomlevel = $maxZoomLevel;
						}

						if (($place['lati'] == '0')
							|| ($place['long'] == '0')
							|| (($i + 1) < $parentCount)
						) {
							WT_DB::prepare(
								'INSERT INTO `##placelocation`'
								. ' (pl_id, pl_parent_id, pl_level,'
								. ' pl_place, pl_zoom, pl_icon)'
								. ' VALUES (?, ?, ?, ?, ?, ?)'
							)->execute(
								array(
									$highestIndex, $parentId, $i, $escParent,
									$zoomlevel, $place['icon']
								)
							);

							//echo WT_Filter::escapeHtml($escParent), '<br />';
						} else {
							// Delete leading zero
							$pl_lati = str_replace(
								array('N', 'S', ','),
								array('', '-', '.'),
								$place['lati']
							);

							$pl_long = str_replace(
								array('E', 'W', ','),
								array('', '-', '.'),
								$place['long']
							);

							if ($pl_lati >= 0) {
								$place['lati'] = 'N' . abs($pl_lati);
							} elseif ($pl_lati < 0) {
								$place['lati'] = 'S' . abs($pl_lati);
							}

							if ($pl_long >= 0) {
								$place['long'] = 'E' . abs($pl_long);
							} elseif ($pl_long < 0) {
								$place['long'] = 'W' . abs($pl_long);
							}

							WT_DB::prepare(
								'INSERT INTO `##placelocation`'
								. ' (pl_id, pl_parent_id, pl_level, pl_place,'
								. ' pl_long, pl_lati, pl_zoom, pl_icon)'
								. ' VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
							)->execute(
								array(
									$highestIndex, $parentId, $i, $escParent,
									$place['long'], $place['lati'], $zoomlevel,
									$place['icon']
								)
							);

							//echo WT_Filter::escapeHtml($escParent), '<br />';
						}

						$parentId = $highestIndex;
					}
				} else {
					$parentId = $row->pl_id;

					if ($overwriteData && (($i + 1) == $parentCount)) {
						WT_DB::prepare(
							'UPDATE `##placelocation`'
							. ' SET pl_lati=?, pl_long=?, pl_zoom=?, pl_icon=?'
							. ' WHERE pl_id=?'
						)->execute(
							array(
								$place['lati'], $place['long'], $place['zoom'],
								$place['icon'], $parentId
							)
						);

						//echo WT_Filter::escapeHtml($escParent), '<br />';
					} else {
						if ((($row->pl_long == '0') || ($row->pl_long == null))
							&& (($row->pl_lati == '0') || ($row->pl_lati == null))
						) {
							WT_DB::prepare(
								'UPDATE `##placelocation`'
								. ' SET pl_lati=?, pl_long=?'
								. ' WHERE pl_id=?'
							)->execute(
								array(
									$place['lati'], $place['long'], $parentId
								)
							);

							//echo WT_Filter::escapeHtml($escParent), '<br />';
						}

						if (empty($row->pl_icon) && !empty($place['icon'])) {
							WT_DB::prepare(
								'UPDATE `##placelocation`'
								. ' SET pl_icon=?'
								. ' WHERE pl_id=?'
							)->execute(
								array(
									$place['icon'], $parentId
								)
							);
						}
					}
				}
			}
		}
	}

	/**
	 * Delete record from "placelocation" table. Returns TRUE if subrecords
	 * exists belonging to the parent record and record could not be deleted,
	 * otherwise FALSE.
	 *
	 * @param integer $parentId Parent record id
	 *
	 * @return boolean
	 */
	public static function deleteRecord($parentId)
	{
		$foundSubRecords = WT_DB::prepare(
			'SELECT 1 FROM `##placelocation` WHERE pl_parent_id = ? '
		)->execute(array($parentId))
			->fetchOne();

		if (!$foundSubRecords) {
			WT_DB::prepare('DELETE FROM `##placelocation` WHERE pl_id = ?')
				->execute(array($parentId));
		}

		return $foundSubRecords;
	}
}

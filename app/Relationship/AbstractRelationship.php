<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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
namespace Fisharebest\Webtrees\Relationship;

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;

/**
 * Definitions for localized relationships.
 */
class AbstractRelationship {
	/**
	 * Lookup table for relationships between members of the same family.
	 *
	 * @var string[]
	 */
	private $direct_relationships = array(
		'FAMCFAMCF' => 'sis',
		'FAMCFAMCM' => 'bro',
		'FAMCFAMCU' => 'sib',
		'FAMCFAMSF' => 'mot',
		'FAMCFAMSM' => 'fat',
		'FAMCFAMSU' => 'par',
		'FAMSFAMCF' => 'dau',
		'FAMSFAMCM' => 'son',
		'FAMSFAMCU' => 'chi',
		'FAMSFAMSF' => 'hus',
		'FAMSFAMSM' => 'wif',
		'FAMSFAMSU' => 'spo',
	);

	/**
	 * Find the relationship between two members of the same family
	 *
	 * @param Individual $i1
	 * @param Family     $family
	 * @param Individual $i2
	 *
	 * @return string
	 */
	protected function directRelationship(Individual $individual1, Family $family, Individual $individual2) {
		$relation1 = '';
		$relation2 = '';
		foreach ($family->getFacts('FAMC|FAMS') as $fact) {
			if ($fact->getTarget() === $individual1) {
				$relation1 = $fact->getTag();
			} elseif ($fact->getTarget() === $individual2) {
				$relation2 = $fact->getTag();
			}
		}

		return $this->direct_relationships[$relation1 . $relation2 . $individual2->getSex()];
	}
}

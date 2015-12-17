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
namespace Fisharebest\Webtrees\Census;

/**
 * Definitions for a census
 */
class CensusOfUnitedStates1870 extends CensusOfUnitedStates implements CensusInterface {
	/**
	 * When did this census occur.
	 *
	 * @return string
	 */
	public function censusDate() {
		return 'JUN 1870';
	}

	/**
	 * The columns of the census.
	 *
	 * @return CensusColumnInterface[]
	 */
	public function columns() {
		return array(
			new CensusColumnFullName($this, 'Name', 'Name'),
			new CensusColumnAge($this, 'Age', 'Age'),
			new CensusColumnSexMF($this, 'Sex', 'Sex'),
			new CensusColumnNull($this, 'Color', 'White, Black, Mulatto, Chinese, Indian'),
			new CensusColumnOccupation($this, 'Occupation', 'Profession, occupation, or trade'),
			new CensusColumnNull($this, 'RE', 'Value of real estate owned'),
			new CensusColumnNull($this, 'PE', 'Value of personal estate owned'),
			new CensusColumnBirthPlaceSimple($this, 'Birthplace', 'Place of birth, naming the state, territory, or country'),
			new CensusColumnFatherForeign($this, 'FFB', 'Father of foreign birth'),
			new CensusColumnMotherForeign($this, 'MFB', 'Mother of foreign birth'),
			new CensusColumnMonthIfBornWithinYear($this, 'Born', 'If born within the year, state month'),
			new CensusColumnMonthIfMarriedWithinYear($this, 'Mar', 'If married within the year, state month'),
			new CensusColumnNull($this, 'School', 'Attended school within the year'),
			new CensusColumnNull($this, 'Read', 'Cannot read'),
			new CensusColumnNull($this, 'Write', 'Cannot write'),
			new CensusColumnNull($this, 'Infirm', 'Whether deaf and dumb, blind, insane, or idiotic'),
			new CensusColumnNull($this, 'Cit', 'Male citizen of US'),
			new CensusColumnNull($this, 'Dis', 'Male citizen of US, where right to vote is denied or abridged'),
		);
	}
}

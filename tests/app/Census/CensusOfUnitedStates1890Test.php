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
 * Test harness for the class CensusOfUnitedStates1890
 */
class CensusOfUnitedStates1890Test extends \PHPUnit_Framework_TestCase {
	/**
	 * Test the census place and date
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1890
	 */
	public function testPlaceAndDate() {
		$census = new CensusOfUnitedStates1890;

		$this->assertSame('United States', $census->censusPlace());
		$this->assertSame('02 JUN 1890', $census->censusDate());
	}

	/**
	 * Test the census columns
	 *
	 * @covers Fisharebest\Webtrees\Census\CensusOfUnitedStates1890
	 * @covers Fisharebest\Webtrees\Census\AbstractCensusColumn
	 */
	public function testColumns() {
		$census  = new CensusOfUnitedStates1890;
		$columns = $census->columns();

		$this->assertCount(24, $columns);
		$this->assertInstanceOf(CensusColumnGivenNameInitial::class, $columns[0]);
		$this->assertInstanceOf(CensusColumnSurname::class, $columns[1]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[2]);
		$this->assertInstanceOf(CensusColumnRelationToHead::class, $columns[3]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[4]);
		$this->assertInstanceOf(CensusColumnSexMF::class, $columns[5]);
		$this->assertInstanceOf(CensusColumnAge::class, $columns[6]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[7]);
		$this->assertInstanceOf(CensusColumnMonthIfMarriedWithinYear::class, $columns[8]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[9]);
		$this->assertInstanceOf(CensusColumnBirthPlaceSimple::class, $columns[10]);
		$this->assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[11]);
		$this->assertInstanceOf(CensusColumnFatherBirthPlaceSimple::class, $columns[12]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[13]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[14]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[15]);
		$this->assertInstanceOf(CensusColumnOccupation::class, $columns[16]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[17]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[18]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[19]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[20]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[21]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[22]);
		$this->assertInstanceOf(CensusColumnNull::class, $columns[23]);

		$this->assertSame('Name', $columns[0]->abbreviation());
		$this->assertSame('Surname', $columns[1]->abbreviation());
		$this->assertSame('CW', $columns[2]->abbreviation());
		$this->assertSame('Relation', $columns[3]->abbreviation());
		$this->assertSame('Race', $columns[4]->abbreviation());
		$this->assertSame('Sex', $columns[5]->abbreviation());
		$this->assertSame('Age', $columns[6]->abbreviation());
		$this->assertSame('Condition', $columns[7]->abbreviation());
		$this->assertSame('Mar', $columns[8]->abbreviation());
		$this->assertSame('Children', $columns[9]->abbreviation());
		$this->assertSame('Birthplace', $columns[10]->abbreviation());
		$this->assertSame('Father’s birthplace', $columns[11]->abbreviation());
		$this->assertSame('Mother’s birthplace', $columns[12]->abbreviation());
		$this->assertSame('US', $columns[13]->abbreviation());
		$this->assertSame('Nat', $columns[14]->abbreviation());
		$this->assertSame('Papers', $columns[15]->abbreviation());
		$this->assertSame('Occupation', $columns[16]->abbreviation());
		$this->assertSame('Unemployed', $columns[17]->abbreviation());
		$this->assertSame('Read', $columns[18]->abbreviation());
		$this->assertSame('Write', $columns[19]->abbreviation());
		$this->assertSame('English', $columns[20]->abbreviation());
		$this->assertSame('Disease', $columns[21]->abbreviation());
		$this->assertSame('Infirm', $columns[22]->abbreviation());
		$this->assertSame('Prisoner', $columns[23]->abbreviation());

		$this->assertSame('Christian name in full, and initial of middle name', $columns[0]->title());
		$this->assertSame('Surname', $columns[1]->title());
		$this->assertSame('Whether a soldier, sailor or marine during the civil war (U.S. or Conf.), or widow of such person', $columns[2]->title());
		$this->assertSame('Relation to head of family', $columns[3]->title());
		$this->assertSame('Whether white, black, mulatto, quadroon, octoroon, Chinese, Japanese, or Indian', $columns[4]->title());
		$this->assertSame('Sex', $columns[5]->title());
		$this->assertSame('Age at nearest birthday.  If under one year, give age in months', $columns[6]->title());
		$this->assertSame('Whether single, married, widowed, or divorced', $columns[7]->title());
		$this->assertSame('Whether married duirng the census year (June 1, 1889, to May 31, 1890)', $columns[8]->title());
		$this->assertSame('Mother of how many children, and number of these children living', $columns[9]->title());
		$this->assertSame('Place of birth', $columns[10]->title());
		$this->assertSame('Place of birth of father', $columns[11]->title());
		$this->assertSame('Place of birth of mother', $columns[12]->title());
		$this->assertSame('Number of years in the United States', $columns[13]->title());
		$this->assertSame('Whether naturalized', $columns[14]->title());
		$this->assertSame('Whether naturalization papers have been taken out', $columns[15]->title());
		$this->assertSame('Profession, trade, occupation', $columns[16]->title());
		$this->assertSame('Months unemployed during the census year (June 1, 1889, to May 31, 1890)', $columns[17]->title());
		$this->assertSame('Able to read', $columns[18]->title());
		$this->assertSame('Able to write', $columns[19]->title());
		$this->assertSame('Able to speak English.  If not the language or dialect spoken', $columns[20]->title());
		$this->assertSame('Whether suffering from acute or chronic disease, with name of disease and length of time afflicted', $columns[21]->title());
		$this->assertSame('Whether defective in mind, sight, hearing, or speech, or whether crippled, maimed, or deformed, with name of defect', $columns[22]->title());
		$this->assertSame('Whether a prisoner, convict, homeless child, or pauper', $columns[23]->title());
	}
}

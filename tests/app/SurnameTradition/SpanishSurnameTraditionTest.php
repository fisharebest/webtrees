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
use Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\SurnameTraditionInterface;

/**
 * Test harness for the class SpanishSurnameTradition
 */
class SpanishSurnameTraditionTest extends PHPUnit_Framework_TestCase {
	/** @var SurnameTraditionInterface */
	private $surname_tradition;

	/**
	 * Prepare the environment for these tests
	 */
	public function setUp() {
		$this->surname_tradition = new SpanishSurnameTradition;
	}

	/**
	 * Test whether married surnames are used
	 */
	public function testMarriedSurnames() {
		$this->assertSame(false, $this->surname_tradition->hasMarriedNames());
	}

	/**
	 * Test whether surnames are used
	 */
	public function testSurnames() {
		$this->assertSame(true, $this->surname_tradition->hasSurnames());
	}

	/**
	 * Test new son names
	 */
	public function testNewSonNames() {
		$this->assertSame(
			array('NAME' => '/Garcia/ /Ruiz/', 'SURN' => 'Garcia,Ruiz'),
			$this->surname_tradition->newChildNames('Gabriel /Garcia/ /Iglesias/', 'Maria /Ruiz/ /Lorca/', 'M')
		);
	}

	/**
	 * Test new daughter names
	 */
	public function testNewDaughterNames() {
		$this->assertSame(
			array('NAME' => '/Garcia/ /Ruiz/', 'SURN' => 'Garcia,Ruiz'),
			$this->surname_tradition->newChildNames('Gabriel /Garcia/ /Iglesias/', 'Maria /Ruiz/ /Lorca/', 'M')
		);
	}

	/**
	 * Test new child names
	 */
	public function testNewChildNames() {
		$this->assertSame(
			array('NAME' => '/Garcia/ /Ruiz/', 'SURN' => 'Garcia,Ruiz'),
			$this->surname_tradition->newChildNames('Gabriel /Garcia/ /Iglesias/', 'Maria /Ruiz/ /Lorca/', 'M')
		);
	}

	/**
	 * Test new child names
	 */
	public function testNewChildNamesCompunds() {
		$this->assertSame(
			array('NAME' => '/Garcia/ /Ruiz/', 'SURN' => 'Garcia,Ruiz'),
			$this->surname_tradition->newChildNames('Gabriel /Garcia Iglesias/', 'Maria /Ruiz Lorca/', 'M')
		);
		$this->assertSame(
			array('NAME' => '/Garcia/ /Ruiz/', 'SURN' => 'Garcia,Ruiz'),
			$this->surname_tradition->newChildNames('Gabriel /Garcia y Iglesias/', 'Maria /Ruiz y Lorca/', 'M')
		);
	}

	/**
	 * Test new father names
	 */
	public function testNewFatherNames() {
		$this->assertSame(
			array('NAME' => '/Garcia/ //', 'SURN' => 'Garcia'),
			$this->surname_tradition->newParentNames('Gabriel /Garcia/ /Iglesias/', 'M')
		);
	}

	/**
	 * Test new mother names
	 */
	public function testNewMotherNames() {
		$this->assertSame(
			array('NAME' => '/Iglesias/ //', 'SURN' => 'Iglesias'),
			$this->surname_tradition->newParentNames('Gabriel /Garcia/ /Iglesias/', 'F')
		);
	}

	/**
	 * Test new parent names
	 */
	public function testNewParentNames() {
		$this->assertSame(
			array('NAME' => '// //'),
			$this->surname_tradition->newParentNames('Gabriel /Garcia/ /Iglesias/', 'U')
		);
	}

	/**
	 * Test new husband names
	 */
	public function testNewHusbandNames() {
		$this->assertSame(
			array('NAME' => '// //'),
			$this->surname_tradition->newSpouseNames('Maria /Ruiz/ /Lorca/', 'M')
		);
	}

	/**
	 * Test new wife names
	 */
	public function testNewWifeNames() {
		$this->assertSame(
			array('NAME' => '// //'),
			$this->surname_tradition->newSpouseNames('Gabriel /Garcia/ /Iglesias/', 'F')
		);
	}

	/**
	 * Test new spouse names
	 */
	public function testNewSpouseNames() {
		$this->assertSame(
			array('NAME' => '// //'),
			$this->surname_tradition->newSpouseNames('Gabriel /Garcia/ /Iglesias/', 'U')
		);
	}
}

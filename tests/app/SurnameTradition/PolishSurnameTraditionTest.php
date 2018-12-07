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
use Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\SurnameTraditionInterface;

/**
 * Test harness for the class SpanishSurnameTradition
 */
class PolishSurnameTraditionTest extends \PHPUnit_Framework_TestCase
{
    /** @var SurnameTraditionInterface */
    private $surname_tradition;

    /**
     * Prepare the environment for these tests
     */
    public function setUp()
    {
        $this->surname_tradition = new PolishSurnameTradition();
    }

    /**
     * Test whether married surnames are used
     *
     * @covers Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition
     * @covers Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     */
    public function testMarriedSurnames()
    {
        $this->assertSame(true, $this->surname_tradition->hasMarriedNames());
    }

    /**
     * Test whether surnames are used
     *
     * @covers Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition
     * @covers Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     */
    public function testSurnames()
    {
        $this->assertSame(true, $this->surname_tradition->hasSurnames());
    }

    /**
     * Test new son names
     *
     * @covers Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition
     * @covers Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     */
    public function testNewSonNames()
    {
        $this->assertSame(
            array('NAME' => '/White/', 'SURN' => 'White'),
            $this->surname_tradition->newChildNames('John /White/', 'Mary /Black/', 'M')
        );
    }

    /**
     * Test new daughter names
     *
     * @covers Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition
     * @covers Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     */
    public function testNewDaughterNames()
    {
        $this->assertSame(
            array('NAME' => '/White/', 'SURN' => 'White'),
            $this->surname_tradition->newChildNames('John /White/', 'Mary /Black/', 'F')
        );
    }

    /**
     * Test new daughter names
     *
     * @covers Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition
     * @covers Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     */
    public function testNewDaughterNamesInflected()
    {
        $this->assertSame(
            array('NAME' => '/Whitecka/', 'SURN' => 'Whitecki'),
            $this->surname_tradition->newChildNames('John /Whitecki/', 'Mary /Black/', 'F')
        );
        $this->assertSame(
            array('NAME' => '/Whitedzka/', 'SURN' => 'Whitedzki'),
            $this->surname_tradition->newChildNames('John /Whitedzki/', 'Mary /Black/', 'F')
        );
        $this->assertSame(
            array('NAME' => '/Whiteska/', 'SURN' => 'Whiteski'),
            $this->surname_tradition->newChildNames('John /Whiteski/', 'Mary /Black/', 'F')
        );
        $this->assertSame(
            array('NAME' => '/Whiteżka/', 'SURN' => 'Whiteżki'),
            $this->surname_tradition->newChildNames('John /Whiteżki/', 'Mary /Black/', 'F')
        );
    }

    /**
     * Test new child names
     *
     * @covers Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition
     * @covers Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     */
    public function testNewChildNames()
    {
        $this->assertSame(
            array('NAME' => '/White/', 'SURN' => 'White'),
            $this->surname_tradition->newChildNames('John /White/', 'Mary /Black/', 'U')
        );
    }

    /**
     * Test new child names
     *
     * @covers Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition
     * @covers Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     */
    public function testNewChildNamesWithNoParentsNames()
    {
        $this->assertSame(
            array('NAME' => '//'),
            $this->surname_tradition->newChildNames('', '', 'U')
        );
    }

    /**
     * Test new father names
     *
     * @covers Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition
     * @covers Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     */
    public function testNewFatherNames()
    {
        $this->assertSame(
            array('NAME' => '/White/', 'SURN' => 'White'),
            $this->surname_tradition->newParentNames('John /White/', 'M')
        );
    }

    /**
     * Test new father names
     *
     * @covers Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition
     * @covers Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     */
    public function testNewFatherNamesInflected()
    {
        $this->assertSame(
            array('NAME' => '/Whitecki/', 'SURN' => 'Whitecki'),
            $this->surname_tradition->newParentNames('Mary /Whitecka/', 'M')
        );
        $this->assertSame(
            array('NAME' => '/Whitedzki/', 'SURN' => 'Whitedzki'),
            $this->surname_tradition->newParentNames('Mary /Whitedzka/', 'M')
        );
        $this->assertSame(
            array('NAME' => '/Whiteski/', 'SURN' => 'Whiteski'),
            $this->surname_tradition->newParentNames('Mary /Whiteska/', 'M')
        );
        $this->assertSame(
            array('NAME' => '/Whiteżki/', 'SURN' => 'Whiteżki'),
            $this->surname_tradition->newParentNames('Mary /Whiteżka/', 'M')
        );
    }

    /**
     * Test new mother names
     *
     * @covers Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition
     * @covers Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     */
    public function testNewMotherNames()
    {
        $this->assertSame(
            array('NAME' => '//'),
            $this->surname_tradition->newParentNames('John /White/', 'F')
        );
    }

    /**
     * Test new parent names
     *
     * @covers Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition
     * @covers Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     */
    public function testNewParentNames()
    {
        $this->assertSame(
            array('NAME' => '//'),
            $this->surname_tradition->newParentNames('John /White/', 'U')
        );
    }

    /**
     * Test new husband names
     *
     * @covers Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition
     * @covers Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     */
    public function testNewHusbandNames()
    {
        $this->assertSame(
            array('NAME' => '//'),
            $this->surname_tradition->newSpouseNames('Mary /Black/', 'M')
        );
    }

    /**
     * Test new wife names
     *
     * @covers Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition
     * @covers Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     */
    public function testNewWifeNames()
    {
        $this->assertSame(
            array('NAME' => '//', '_MARNM' => '/White/'),
            $this->surname_tradition->newSpouseNames('John /White/', 'F')
        );
    }

    /**
     * Test new spouse names
     *
     * @covers Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition
     * @covers Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition
     */
    public function testNewSpouseNames()
    {
        $this->assertSame(
            array('NAME' => '//'),
            $this->surname_tradition->newSpouseNames('Chris /Green/', 'U')
        );
    }
}

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
use Fisharebest\Webtrees\SurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\DefaultSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\IcelandicSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\LithuanianSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\MatrilinealSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PatrilinealSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\PortugueseSurnameTradition;
use Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition;

/**
 * Test harness for the class Soundex
 */
class SurnameTraditionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Prepare the environment for these tests
     */
    public function setUp()
    {
    }

    /**
     * Test create()
     */
    public function testCreate()
    {
        $this->assertTrue(SurnameTradition::create('none') instanceof DefaultSurnameTradition);
        $this->assertTrue(SurnameTradition::create('icelandic') instanceof IcelandicSurnameTradition);
        $this->assertTrue(SurnameTradition::create('lithuanian') instanceof LithuanianSurnameTradition);
        $this->assertTrue(SurnameTradition::create('matrilineal') instanceof MatrilinealSurnameTradition);
        $this->assertTrue(SurnameTradition::create('paternal') instanceof PaternalSurnameTradition);
        $this->assertTrue(SurnameTradition::create('patrilineal') instanceof PatrilinealSurnameTradition);
        $this->assertTrue(SurnameTradition::create('polish') instanceof PolishSurnameTradition);
        $this->assertTrue(SurnameTradition::create('portuguese') instanceof PortugueseSurnameTradition);
        $this->assertTrue(SurnameTradition::create('spanish') instanceof SpanishSurnameTradition);
    }

    /**
     * Test create() with invalid input
     */
    public function testCreateInvalid()
    {
        $this->assertTrue(SurnameTradition::create('FOOBAR') instanceof DefaultSurnameTradition);
    }

    /**
     * Test allDescriptions()
     */
    public function testAllDescriptions()
    {
        $descriptions = SurnameTradition::allDescriptions();
        $this->assertCount(9, $descriptions);
    }
}

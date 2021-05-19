<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\SurnameTradition;

use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class SpanishSurnameTradition
 */
class SpanishSurnameTraditionTest extends TestCase
{
    private SurnameTraditionInterface $surname_tradition;

    /**
     * Prepare the environment for these tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->surname_tradition = new SpanishSurnameTradition();
    }

    /**
     * Test whether married surnames are used
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition
     *
     * @return void
     */
    public function testMarriedSurnames(): void
    {
        self::assertFalse($this->surname_tradition->hasMarriedNames());
    }

    /**
     * Test whether surnames are used
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition
     *
     * @return void
     */
    public function testSurnames(): void
    {
        self::assertTrue($this->surname_tradition->hasSurnames());
    }

    /**
     * Test new son names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition
     *
     * @return void
     */
    public function testNewSonNames(): void
    {
        self::assertSame(
            [
                'NAME' => '/Garcia/ /Ruiz/',
                'SURN' => 'Garcia,Ruiz',
            ],
            $this->surname_tradition->newChildNames('Gabriel /Garcia/ /Iglesias/', 'Maria /Ruiz/ /Lorca/', 'M')
        );
    }

    /**
     * Test new daughter names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition
     *
     * @return void
     */
    public function testNewDaughterNames(): void
    {
        self::assertSame(
            [
                'NAME' => '/Garcia/ /Ruiz/',
                'SURN' => 'Garcia,Ruiz',
            ],
            $this->surname_tradition->newChildNames('Gabriel /Garcia/ /Iglesias/', 'Maria /Ruiz/ /Lorca/', 'M')
        );
    }

    /**
     * Test new child names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition
     *
     * @return void
     */
    public function testNewChildNames(): void
    {
        self::assertSame(
            [
                'NAME' => '/Garcia/ /Ruiz/',
                'SURN' => 'Garcia,Ruiz',
            ],
            $this->surname_tradition->newChildNames('Gabriel /Garcia/ /Iglesias/', 'Maria /Ruiz/ /Lorca/', 'M')
        );
    }

    /**
     * Test new child names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition
     *
     * @return void
     */
    public function testNewChildNamesWithNoParentsNames(): void
    {
        self::assertSame(
            [
                'NAME' => '// //',
                'SURN' => '',
            ],
            $this->surname_tradition->newChildNames('', '', 'U')
        );
    }

    /**
     * Test new child names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition
     *
     * @return void
     */
    public function testNewChildNamesCompunds(): void
    {
        self::assertSame(
            [
                'NAME' => '/Garcia/ /Ruiz/',
                'SURN' => 'Garcia,Ruiz',
            ],
            $this->surname_tradition->newChildNames('Gabriel /Garcia Iglesias/', 'Maria /Ruiz Lorca/', 'M')
        );
        self::assertSame(
            [
                'NAME' => '/Garcia/ /Ruiz/',
                'SURN' => 'Garcia,Ruiz',
            ],
            $this->surname_tradition->newChildNames('Gabriel /Garcia y Iglesias/', 'Maria /Ruiz y Lorca/', 'M')
        );
    }

    /**
     * Test new father names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition
     *
     * @return void
     */
    public function testNewFatherNames(): void
    {
        self::assertSame(
            [
                'NAME' => '/Garcia/ //',
                'SURN' => 'Garcia',
            ],
            $this->surname_tradition->newParentNames('Gabriel /Garcia/ /Iglesias/', 'M')
        );
    }

    /**
     * Test new mother names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition
     *
     * @return void
     */
    public function testNewMotherNames(): void
    {
        self::assertSame(
            [
                'NAME' => '/Iglesias/ //',
                'SURN' => 'Iglesias',
            ],
            $this->surname_tradition->newParentNames('Gabriel /Garcia/ /Iglesias/', 'F')
        );
    }

    /**
     * Test new parent names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition
     *
     * @return void
     */
    public function testNewParentNames(): void
    {
        self::assertSame(
            ['NAME' => '// //'],
            $this->surname_tradition->newParentNames('Gabriel /Garcia/ /Iglesias/', 'U')
        );
    }

    /**
     * Test new husband names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition
     *
     * @return void
     */
    public function testNewHusbandNames(): void
    {
        self::assertSame(
            ['NAME' => '// //'],
            $this->surname_tradition->newSpouseNames('Maria /Ruiz/ /Lorca/', 'M')
        );
    }

    /**
     * Test new wife names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition
     *
     * @return void
     */
    public function testNewWifeNames(): void
    {
        self::assertSame(
            ['NAME' => '// //'],
            $this->surname_tradition->newSpouseNames('Gabriel /Garcia/ /Iglesias/', 'F')
        );
    }

    /**
     * Test new spouse names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\SpanishSurnameTradition
     *
     * @return void
     */
    public function testNewSpouseNames(): void
    {
        self::assertSame(
            ['NAME' => '// //'],
            $this->surname_tradition->newSpouseNames('Gabriel /Garcia/ /Iglesias/', 'U')
        );
    }
}

<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
declare(strict_types=1);

namespace Fisharebest\Webtrees\SurnameTradition;

/**
 * Test harness for the class PaternalSurnameTradition
 */
class PaternalSurnameTraditionTest extends \Fisharebest\Webtrees\TestCase
{
    /** @var SurnameTraditionInterface */
    private $surname_tradition;

    /**
     * Prepare the environment for these tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->surname_tradition = new PaternalSurnameTradition();
    }

    /**
     * Test whether married surnames are used
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testMarriedSurnames(): void
    {
        $this->assertTrue($this->surname_tradition->hasMarriedNames());
    }

    /**
     * Test whether surnames are used
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testSurnames(): void
    {
        $this->assertTrue($this->surname_tradition->hasSurnames());
    }

    /**
     * Test new son names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testNewSonNames(): void
    {
        $this->assertSame(
            [
                'NAME' => '/White/',
                'SURN' => 'White',
            ],
            $this->surname_tradition->newChildNames('John /White/', 'Mary /Black/', 'M')
        );
    }

    /**
     * Test new daughter names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testNewDaughterNames(): void
    {
        $this->assertSame(
            [
                'NAME' => '/White/',
                'SURN' => 'White',
            ],
            $this->surname_tradition->newChildNames('John /White/', 'Mary /Black/', 'F')
        );
    }

    /**
     * Test new child names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testNewChildNames(): void
    {
        $this->assertSame(
            [
                'NAME' => '/White/',
                'SURN' => 'White',
            ],
            $this->surname_tradition->newChildNames('John /White/', 'Mary /Black/', 'U')
        );
    }

    /**
     * Test new child names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testNewChildNamesWithSpfx(): void
    {
        $this->assertSame(
            [
                'NAME' => '/de White/',
                'SPFX' => 'de',
                'SURN' => 'White',
            ],
            $this->surname_tradition->newChildNames('John /de White/', 'Mary /van Black/', 'U')
        );
    }

    /**
     * Test new child names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testNewChildNamesWithMultipleSpfx(): void
    {
        $this->assertSame(
            [
                'NAME' => '/van der White/',
                'SPFX' => 'van der',
                'SURN' => 'White',
            ],
            $this->surname_tradition->newChildNames('John /van der White/', 'Mary /van Black/', 'U')
        );
    }

    /**
     * Test new child names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testNewChildNamesWithDutchSpfx(): void
    {
        $this->assertSame(
            [
                'NAME' => '/\'t White/',
                'SPFX' => '\'t',
                'SURN' => 'White',
            ],
            $this->surname_tradition->newChildNames('John /\'t White/', 'Mary /van Black/', 'U')
        );
        $this->assertSame(
            [
                'NAME' => '/’t White/',
                'SPFX' => '’t',
                'SURN' => 'White',
            ],
            $this->surname_tradition->newChildNames('John /’t White/', 'Mary /van Black/', 'U')
        );
    }

    /**
     * Test new child names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testNewChildNamesWithMultipleDutchSpfx(): void
    {
        $this->assertSame(
            [
                'NAME' => '/van \'t White/',
                'SPFX' => 'van \'t',
                'SURN' => 'White',
            ],
            $this->surname_tradition->newChildNames('John /van \'t White/', 'Mary /van Black/', 'U')
        );
        $this->assertSame(
            [
                'NAME' => '/van ’t White/',
                'SPFX' => 'van ’t',
                'SURN' => 'White',
            ],
            $this->surname_tradition->newChildNames('John /van ’t White/', 'Mary /van Black/', 'U')
        );
    }

    /**
     * Test new father names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testNewFatherNames(): void
    {
        $this->assertSame(
            [
                'NAME' => '/White/',
                'SURN' => 'White',
            ],
            $this->surname_tradition->newParentNames('John /White/', 'M')
        );
    }

    /**
     * Test new mother names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testNewMotherNames(): void
    {
        $this->assertSame(
            [
                'NAME'   => '//',
                '_MARNM' => '/White/',
            ],
            $this->surname_tradition->newParentNames('John /White/', 'F')
        );
    }

    /**
     * Test new parent names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testNewParentNames(): void
    {
        $this->assertSame(
            ['NAME' => '//'],
            $this->surname_tradition->newParentNames('John /White/', 'U')
        );
    }

    /**
     * Test new husband names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testNewHusbandNames(): void
    {
        $this->assertSame(
            ['NAME' => '//'],
            $this->surname_tradition->newSpouseNames('Mary /Black/', 'M')
        );
    }

    /**
     * Test new wife names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testNewWifeNames(): void
    {
        $this->assertSame(
            [
                'NAME'   => '//',
                '_MARNM' => '/White/',
            ],
            $this->surname_tradition->newSpouseNames('John /White/', 'F')
        );
    }

    /**
     * Test new wife names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testNewWifeNamesWithSpfx(): void
    {
        $this->assertSame(
            [
                'NAME'   => '//',
                '_MARNM' => '/van der White/',
            ],
            $this->surname_tradition->newSpouseNames('John /van der White/', 'F')
        );
    }

    /**
     * Test new spouse names
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\PaternalSurnameTradition
     *
     * @return void
     */
    public function testNewSpouseNames(): void
    {
        $this->assertSame(
            ['NAME' => '//'],
            $this->surname_tradition->newSpouseNames('Chris /Green/', 'U')
        );
    }
}

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

use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class ArabicSurnameTradition
 */
class ArabicSurnameTraditionTest extends TestCase
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

        $this->surname_tradition = new ArabicSurnameTradition();
    }

    /**
     * Test new son names when the father already has a father's name and grandfather's name.
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\ArabicSurnameTradition
     *
     * @return void
     */
    public function testNewSonNamesArabic(): void
    {
        // given the father name Muhammad Salmān Amīn al-Farsī
        $father_name = 'محمد /سلمان امین الفارسی/';
        // .. and given any mother's name
        $mother_name = 'Mary /Doe/';

        // the method is expected to extract 'al-Farsī' as SURNAME.
        $family_name_expected = 'الفارسی';
        // the method is expected to extract 'Muhammad Salmān' as Surname Prefixes.
        $surname_prefix_expected = 'محمد سلمان';
        // the complete new surname (NAME field) should be '/Muhammad Salmān al-Farsī/'.
        $name_expected = '/محمد سلمان الفارسی/';

        $this->assertSame(
            [
                'NAME' => $name_expected,
                'SURN' => $family_name_expected,

            ],
            $this->surname_tradition->newChildNames($father_name, $mother_name, 'M')
        );
    }

    /**
     * Test new son names when the father already has a father's name and grandfather's name.
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\ArabicSurnameTradition
     *
     * @return void
     */
    public function testNewDoughterNamesArabic(): void
    {
        // given the father name Muhammad Salmān Amīn al-Farsī
        $father_name = 'محمد /سلمان امین الفارسی/';
        // .. and given any mother's name
        $mother_name = 'Mary /Doe/';

        // the method is expected to extract 'al-Farsī' as SURNAME.
        $family_name_expected = 'الفارسی';
        // the method is expected to extract 'Muhammad Salmān' as Surname Prefixes.
        $surname_prefix_expected = 'محمد سلمان';
        // the complete new surname (NAME field) should be '/Muhammad Salmān al-Farsī/'.
        $name_expected = '/محمد سلمان الفارسی/';

        $this->assertSame(
            [
                'NAME' => $name_expected,
                'SURN' => $family_name_expected,

            ],
            $this->surname_tradition->newChildNames($father_name, $mother_name, 'F')
        );
    }

    /**
     * Test new son names when the father already has a father's name and grandfather's name.
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\ArabicSurnameTradition
     *
     * @return void
     */
    public function testNewUnknownNamesArabic(): void
    {
        // given the father name Muhammad Salmān Amīn al-Farsī
        $father_name = 'محمد /سلمان امین الفارسی/';
        // .. and given any mother's name
        $mother_name = 'Mary /Doe/';

        // the method is expected to extract 'al-Farsī' as SURNAME.
        $family_name_expected = 'الفارسی';
        // the method is expected to extract 'Muhammad Salmān' as Surname Prefixes.
        $surname_prefix_expected = 'محمد سلمان';
        // the complete new surname (NAME field) should be '/Muhammad Salmān al-Farsī/'.
        $name_expected = '/محمد سلمان الفارسی/';

        $this->assertSame(
            [
                'NAME' => $name_expected,
                'SURN' => $family_name_expected,

            ],
            $this->surname_tradition->newChildNames($father_name, $mother_name, 'U')
        );
    }

    /**
     * Test new son names when the father has neither father's name nor grandfather's name.
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\ArabicSurnameTradition
     *
     * @return void
     */
    public function testNewSonNamesUnknownFather()
    {
        // given the father name Muhammad Salmān Amīn al-Farsī
        $father_name = '//';
        // .. and given any mother's name
        $mother_name = 'Mary /Doe/';

        // the complete new surname (NAME field) should be '//', because nothing could be extracted.
        $name_expected = '//';

        $this->assertSame(
            [
                'NAME' => $name_expected,

            ],
            $this->surname_tradition->newChildNames($father_name, $mother_name, 'M')
        );
    }

    /**
     * Test new son names when the father already has only a family name.
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\ArabicSurnameTradition
     *
     * @return void
     */
    public function testNewSonNamesOnlyFamilyName()
    {
        // given the father name Muhammad al-Farsī
        $father_name = 'محمد /الفارسی/';
        // .. and given any mother's name
        $mother_name = 'Mary /Doe/';

        // the complete new surname (NAME field) should be '/Muhammad al-Farsī/', because no grandafather name is known.
        $name_expected = '/محمد الفارسی/';
        $surname_expected = 'الفارسی';

        $this->assertSame(
            [
                'NAME' => $name_expected,
                'SURN' => $surname_expected,

            ],
            $this->surname_tradition->newChildNames($father_name, $mother_name, 'M')
        );
    }

    /**
     * Test the method 'getFamilyName'. Should extract the family name only.
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\ArabicSurnameTradition
     *
     * @return void
     */
    public function testGetFamilyName(): void
    {
        // given the father name Muhammad Salmān Amīn al-Farsī
        $father_name = 'محمد /سلیمان امین الفارسی/';
        // the method is expected to extract 'al-Farsī'.
        $family_name_expected = 'الفارسی';
        $this->assertSame($family_name_expected, ArabicSurnameTradition::getFamilyName($father_name));
    }

    /**
     * Test the method 'getFamilyName' when there is only a familyname.
     * Should extract the family name only.
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\ArabicSurnameTradition
     *
     * @return void
     */
    public function testGetFamilyNameOnlyFamilyName(): void
    {
        // given the father name Muhammad Salmān Amīn al-Farsī
        $father_name = 'محمد /الفارسی/';
        // the method is expected to extract 'al-Farsī'.
        $family_name_expected = 'الفارسی';
        $this->assertSame($family_name_expected, ArabicSurnameTradition::getFamilyName($father_name));
    }

    /**
     * Test the method 'getFamilyName'. Should extract the family name only.
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\ArabicSurnameTradition
     *
     * @return void
     */
    public function testGetFamilyNameNoFamilyName(): void
    {
        // given the father name Muhammad Salmān Amīn al-Farsī
        $father_name = 'محمد //';
        // the method is expected to extract 'al-Farsī'.
        $family_name_expected = '';
        $this->assertSame($family_name_expected, ArabicSurnameTradition::getFamilyName($father_name));
    }

    public function testGetGrandfatherName(): void
    {
        // given the father name Muhammad Salmān
        $father_name = 'محمد /سلیمان امین الفارسی/';
        // the method is expected to extract Salmān.
        $grandfather_givnname_expected = 'سلیمان';
        $this->assertSame($grandfather_givnname_expected, ArabicSurnameTradition::getGrandfatherNameWithPrefix($father_name));
    }

    /**
     * Test new son names in latin.
     *
     * @covers \Fisharebest\Webtrees\SurnameTradition\ArabicSurnameTradition
     *
     * @return void
     */
    public function testNewSonNamesLatin(): void
    {
        // given the father name Muhammad Salmān Amīn al-Farsī
        $father_name = 'Muhammad /Salmān Amīn al-Farsī/';
        // .. and given any mother's name
        $mother_name = 'Mary /Doe/';

        // the method is expected to extract 'al-Farsī' as SURNAME.
        $family_name_expected = 'al-Farsī';
        // the method is expected to extract 'Muhammad Salmān' as Surname Prefixes.
        $surname_prefix_expected = 'Muhammad Salmān';
        // the complete new surname (NAME field) should be '/Muhammad Salmān al-Farsī/'.
        $name_expected = '/Muhammad Salmān al-Farsī/';

        $this->assertSame(
            [
                'NAME' => $name_expected,
                'SURN' => $family_name_expected,
            ],
            $this->surname_tradition->newChildNames($father_name, $mother_name, 'M')
        );
    }

    public function testGuessFatherName(): void
    {
        $son_name = 'Muhammad /Salmān Amīn al-Farsī/';
        $father_name = $this->surname_tradition->newParentNames($son_name, 'M');

        $this->assertSame(
            [
                'NAME' => 'Salmān Amīn al-Farsī',
                'SURN' => 'Amīn al-Farsī',
                'GIVN' => 'Salmān'
            ],
            $father_name
        );
    }
}

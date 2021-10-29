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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Factories\FamilyFactory;
use Fisharebest\Webtrees\Factories\IndividualFactory;
use Fisharebest\Webtrees\Module\LanguageEnglishAustralia;
use Fisharebest\Webtrees\Module\LanguageEnglishGreatBritain;
use Fisharebest\Webtrees\Module\LanguageEnglishUnitedStates;
use Fisharebest\Webtrees\Module\LanguageFrench;
use Fisharebest\Webtrees\Module\LanguageSlovakian;
use Fisharebest\Webtrees\Module\ModuleLanguageInterface;
use Fisharebest\Webtrees\Services\RelationshipService;

use function array_reverse;

/**
 * Test the user functions
 *
 * @covers \Fisharebest\Webtrees\Relationship
 * @covers \Fisharebest\Webtrees\Services\RelationshipService
 * @covers \Fisharebest\Webtrees\Module\LanguageEnglishGreatBritain
 * @covers \Fisharebest\Webtrees\Module\LanguageEnglishUnitedStates
 * @covers \Fisharebest\Webtrees\Module\LanguageFrench
 * @covers \Fisharebest\Webtrees\Module\ModuleLanguageTrait
 */
class RelationshipNamesTest extends TestCase
{
    /**
     * @return void
     */
    public function testRelationshipNames(): void
    {
        //                                                   i22m===f10===i23f
        //                                                           |
        //                                                     +-----+-----+
        //                                                     |           |
        //                                       i20m===f9===i21f         i24m===f11m===i25f
        //                                           |                            |
        //                              i19f===f8===i18m                         i26f===f12===i27m
        //                                      |                                        |
        //                                  +---+---+-----+                          +---+---+
        //                                  |       |     |                          |       |
        //                    i16m===f7===i17f     i30m  i37f                       i28u    i29m===f15
        //                            |                                                             |
        //          +-----------------+                                                             |
        //          |                 |                                                             |
        //         i38f i12f===f4m===i11m  i13m===f5m===i14f                                       i34f===f16
        //                      |                  |                                                       |
        //                     i1m===f1m==========i2f===f2d===i6m=====f13m===i31m===f14d===i32f           i35m===f17
        //                            |                  |                           |                            |
        //                        +---+---+          +---+---+                 +-----+                            |
        //                        |   |   |          |   |   |                 |     |                            |
        //          i10f===f3e===i3m i4f i5u       i7ma i8f i9u===f6===i15u   i33f  i39mo                        i36f
        //
        // Individual suffixes - m(ale), f(emale), u(nknown), a(dopted), o(foster)
        // Family suffixes - m(arried), d(ivorced), e(ngaged)
        //
        $tree = $this->createMock(Tree::class);

        $individual_factory = $this->createStub(IndividualFactory::class);
        $family_factory     = $this->createStub(FamilyFactory::class);

        Registry::familyFactory($family_factory);
        Registry::individualFactory($individual_factory);

        $i1m  = new Individual('i1m', "0 @i1m@ INDI\n1 SEX M\n1 FAMS @f1m@\n1 FAMC @f4m@", null, $tree);
        $i2f  = new Individual('i2f', "0 @i2f@ INDI\n1 SEX F\n1 FAMS @f1m@\n1 FAMS @f2d@\n2 FAMC @f5m@", null, $tree);
        $i3m  = new Individual('i3m', "0 @i3m@ INDI\n1 SEX M\n1 FAMC @f1m@\n1 FAMS @f3e@\n1 BIRT\n2 DATE 2000", null, $tree);
        $i4f  = new Individual('i4f', "0 @i4f@ INDI\n1 SEX F\n1 FAMC @f1m@\n1 BIRT\n2 DATE 2001", null, $tree);
        $i5u  = new Individual('i5u', "0 @i5u@ INDI\n1 SEX U\n1 FAMC @f1m@\n1 BIRT\n2 DATE 2002", null, $tree);
        $i6m  = new Individual('i6m', "0 @i6m@ INDI\n1 SEX M\n1 FAMS @f2d@", null, $tree);
        $i7ma = new Individual('i7ma', "0 @i7ma@ INDI\n1 SEX M\n1 FAMC @f2d@\n2 PEDI adopted", null, $tree);
        $i8f  = new Individual('i8f', "0 @i8f@ INDI\n1 SEX F\n1 FAMC @f2d@", null, $tree);
        $i9u  = new Individual('i9u', "0 @i9u@ INDI\n1 SEX U\n1 FAMC @f2d@\n1 FAMS @f6@", null, $tree);
        $i10f = new Individual('i10f', "0 @i10f@ INDI\n1 SEX F\n1 FAMS @f3e@", null, $tree);
        $i11m = new Individual('i11m', "0 @i11f@ INDI\n1 SEX M\n1 FAMS @f4m@\n1 FAMC @f7@", null, $tree);
        $i12f = new Individual('i12f', "0 @i12f@ INDI\n1 SEX F\n1 FAMS @f4m@", null, $tree);
        $i13m = new Individual('i13m', "0 @i13f@ INDI\n1 SEX M\n1 FAMS @f5m@", null, $tree);
        $i14f = new Individual('i14f', "0 @i14f@ INDI\n1 SEX F\n1 FAMS @f5m@", null, $tree);
        $i15u = new Individual('i15u', "0 @i15u@ INDI\n1 SEX U\n1 FAMS @f6@", null, $tree);
        $i16m = new Individual('i16m', "0 @i16m@ INDI\n1 SEX M\n1 FAMS @f7@", null, $tree);
        $i17f = new Individual('i17f', "0 @i17f@ INDI\n1 SEX F\n1 FAMS @f7@\n1 FAMC @f8@", null, $tree);
        $i18m = new Individual('i18m', "0 @i18m@ INDI\n1 SEX M\n1 FAMS @f8@\n1 FAMC @f9@", null, $tree);
        $i19f = new Individual('i19f', "0 @i19f@ INDI\n1 SEX F\n1 FAMS @f8@", null, $tree);
        $i20m = new Individual('i20m', "0 @i20m@ INDI\n1 SEX M\n1 FAMS @f9@", null, $tree);
        $i21f = new Individual('i21f', "0 @i21f@ INDI\n1 SEX F\n1 FAMS @f9@\n1 FAMC @f10@", null, $tree);
        $i22m = new Individual('i22m', "0 @i22m@ INDI\n1 SEX M\n1 FAMS @f10@", null, $tree);
        $i23f = new Individual('i23f', "0 @i23f@ INDI\n1 SEX F\n1 FAMS @f10@", null, $tree);
        $i24m = new Individual('i24m', "0 @i24m@ INDI\n1 SEX M\n1 FAMS @f11@\n1 FAMC @f10@", null, $tree);
        $i25f = new Individual('i25f', "0 @i25f@ INDI\n1 SEX F\n1 FAMS @f11@", null, $tree);
        $i26f = new Individual('i26f', "0 @i26f@ INDI\n1 SEX F\n1 FAMS @f12@\n1 FAMC @f11@", null, $tree);
        $i27m = new Individual('i27m', "0 @i27m@ INDI\n1 SEX M\n1 FAMS @f12@", null, $tree);
        $i28u = new Individual('i28u', "0 @i28u@ INDI\n1 SEX U\n1 FAMC @f12@", null, $tree);
        $i29m = new Individual('i29m', "0 @i29m@ INDI\n1 SEX M\n1 FAMC @f12@", null, $tree);
        $i30m = new Individual('i30m', "0 @i30m@ INDI\n1 SEX M\n1 FAMC @f8@", null, $tree);
        $i31m = new Individual('i31m', "0 @i31m@ INDI\n1 SEX M\n1 FAMS @f13m@\n1 FAMS @f14d@", null, $tree);
        $i32f = new Individual('i32f', "0 @i32f@ INDI\n1 SEX F\n1 FAMS @f14d@", null, $tree);
        $i33f = new Individual('i33f', "0 @i33f@ INDI\n1 SEX F\n1 FAMC @f14d@", null, $tree);
        $i34f = new Individual('i34f', "0 @i34f@ INDI\n1 SEX F\n1 FAMC @f15@", null, $tree);
        $i35m = new Individual('i35m', "0 @i35m@ INDI\n1 SEX M\n1 FAMS @f17@\n1 FAMC @f16@", null, $tree);
        $i36f = new Individual('i36f', "0 @i36f@ INDI\n1 SEX F\n1 FAMC @f17@", null, $tree);
        $i37f = new Individual('i37f', "0 @i37f@ INDI\n1 SEX F\n1 FAMC @f8@", null, $tree);
        $i38f = new Individual('i38f', "0 @i38f@ INDI\n1 SEX F\n1 FAMC @f7@", null, $tree);
        $i39mo = new Individual('i39mo', "0 @i39o@ INDI\n1 SEX M\n1 FAMC @f14d@\n2 PEDI foster", null, $tree);

        $individual_factory->method('make')->will($this->returnValueMap([
            'i1m'  => $i1m,
            'i2f'  => $i2f,
            'i3m'  => $i3m,
            'i4f'  => $i4f,
            'i5u'  => $i5u,
            'i6m'  => $i6m,
            'i7ma' => $i7ma,
            'i8f'  => $i8f,
            'i9u'  => $i9u,
            'i10f' => $i10f,
            'i11m' => $i11m,
            'i12f' => $i12f,
            'i13m' => $i13m,
            'i14f' => $i14f,
            'i15u' => $i15u,
            'i16m' => $i16m,
            'i17f' => $i17f,
            'i18m' => $i18m,
            'i19f' => $i19f,
            'i20m' => $i20m,
            'i21f' => $i21f,
            'i22m' => $i22m,
            'i23f' => $i23f,
            'i24m' => $i24m,
            'i25f' => $i25f,
            'i26f' => $i26f,
            'i27m' => $i27m,
            'i28u' => $i28u,
            'i29m' => $i29m,
            'i30m' => $i30m,
            'i31m' => $i31m,
            'i32f' => $i32f,
            'i33f' => $i33f,
            'i34f' => $i34f,
            'i35m' => $i35m,
            'i36f' => $i36f,
            'i37f' => $i37f,
            'i38f' => $i38f,
            'i39mo' => $i39mo
        ]));

        $f1m  = new Family('f1m', "0 @f1m@ FAM\n1 MARR Y\n1 HUSB @i1m@\n1 WIFE @i2f@\n1 CHIL @i3m@\n1 CHIL @i4f@\n1 CHIL @i5u@", null, $tree);
        $f2d  = new Family('f2d', "0 @f2d@ FAM\n1 DIV Y\n1 HUSB @i6m@\n1 WIFE @i2f@\n1 CHIL @i7ma@\n1 CHIL @i8f@\n1 CHIL @i9u@", null, $tree);
        $f3e  = new Family('f3e', "0 @f3e@ FAM\n1 ENGA Y\n1 HUSB @i3m@\n1 WIFE @i10f@", null, $tree);
        $f4m  = new Family('f4m', "0 @f4m@ FAM\n1 MARR Y\n1 HUSB @i11m@\n1 WIFE @i12f@\n1 CHIL @i1m@", null, $tree);
        $f5m  = new Family('f5m', "0 @f5m@ FAM\n1 MARR Y\n1 HUSB @i13m@\n1 WIFE @i14f@\n1 CHIL @i2f@", null, $tree);
        $f6   = new Family('f6', "0 @f6@ FAM\n1 HUSB @i9u@\n1 WIFE @i15u@", null, $tree);
        $f7   = new Family('f7', "0 @f7@ FAM\n1 HUSB @i16m@\n1 WIFE @i17f@\n1 CHIL @i11m@\n1 CHIL @i38f@", null, $tree);
        $f8   = new Family('f8', "0 @f8@ FAM\n1 HUSB @i18m@\n1 WIFE @i19f@\n1 CHIL @i17f@\n1 CHIL @i30m@\n1 CHIL @i37f@", null, $tree);
        $f9   = new Family('f9', "0 @f9@ FAM\n1 HUSB @i20m@\n1 WIFE @i21f@\n1 CHIL @i18m@", null, $tree);
        $f10  = new Family('f10', "0 @f10@ FAM\n1 HUSB @i22m@\n1 WIFE @i23f@\n1 CHIL @i21f@\n1 CHIL @i24m@", null, $tree);
        $f11m = new Family('f11m', "0 @f11m@ FAM\n1 MARR Y\n1 HUSB @i24m@\n1 WIFE @i25f@\n1 CHIL @i26f@", null, $tree);
        $f12  = new Family('f12', "0 @f12@ FAM\n1 HUSB @i27m@\n1 WIFE @i26f@\n1 CHIL @i28u@\n1 CHIL @i29m@", null, $tree);
        $f13m = new Family('f13m', "0 @f13m@ FAM\n1 MARR Y\n1 HUSB @i6m@\n1 WIFE @i31m@", null, $tree);
        $f14d = new Family('f14d', "0 @f14d@ FAM\n1 DIV Y\n1 HUSB @i31m@\n1 WIFE @i32f@\n1 CHIL @i33f@\n1 CHIL @i39mo@", null, $tree);
        $f15  = new Family('f15', "0 @f15@ FAM\n1 HUSB @i29m@\n1 CHIL @i34f@", null, $tree);
        $f16  = new Family('f16', "0 @f16@ FAM\n1 WIFE @i34f@\n1 CHIL @i35m@", null, $tree);
        $f17  = new Family('f17', "0 @f17@ FAM\n1 HUSB @i35m@\n1 CHIL @i36f@", null, $tree);

        $family_factory->method('make')->will($this->returnValueMap([
            'f1m'  => $f1m,
            'f2d'  => $f2d,
            'f3e'  => $f3e,
            'f4m'  => $f4m,
            'f5m'  => $f5m,
            'f6'   => $f6,
            'f7'   => $f7,
            'f8'   => $f8,
            'f9'   => $f9,
            'f10'  => $f10,
            'f11m' => $f11m,
            'f12'  => $f12,
            'f13m' => $f13m,
            'f14d' => $f14d,
            'f15'  => $f15,
            'f16'  => $f16,
            'f17'  => $f17
        ]));

        ///////////////////////////////////////////////////////////////////////
        // ENGLISH
        ///////////////////////////////////////////////////////////////////////

        $en_au = new LanguageEnglishAustralia();
        $en_gb = new LanguageEnglishGreatBritain();
        $en_us = new LanguageEnglishUnitedStates();

        foreach ([$en_us, $en_gb, $en_au] as $en) {
            self::assertRelationships('wife', 'husband', [$i1m, $f1m, $i2f], $en);
            self::assertRelationships('partner', 'partner', [$i9u, $f6, $i15u], $en);
            self::assertRelationships('ex-husband', 'ex-wife', [$i2f, $f2d, $i6m], $en);
            self::assertRelationships('fiancé', 'fiancée', [$i10f, $f3e, $i3m], $en);
            self::assertRelationships('son', 'father', [$i1m, $f1m, $i3m], $en);
            self::assertRelationships('daughter', 'mother', [$i2f, $f1m, $i4f], $en);
            self::assertRelationships('child', 'father', [$i1m, $f1m, $i5u], $en);
            self::assertRelationships('elder brother', 'younger sister', [$i4f, $f1m, $i3m], $en);
            self::assertRelationships('younger sibling', 'elder brother', [$i3m, $f1m, $i5u], $en);
            self::assertRelationships('brother', 'sister', [$i8f, $f2d, $i7ma], $en);
            self::assertRelationships('sibling', 'brother', [$i7ma, $f2d, $i9u], $en);
            self::assertRelationships('adoptive-mother', 'adopted-son', [$i7ma, $f2d, $i2f], $en);
            self::assertRelationships('stepfather', 'stepchild', [$i9u, $f2d, $i2f, $f1m, $i1m], $en);
            self::assertRelationships('stepdaughter', 'stepmother', [$i2f, $f1m, $i2f, $f2d, $i8f], $en);
            self::assertRelationships('stepsister', 'stepsibling', [$i9u, $f2d, $i6m, $f13m, $i31m, $f14d, $i33f], $en);
            self::assertRelationships('half-brother', 'half-sister', [$i8f, $f2d, $i2f, $f1m, $i3m], $en);
            self::assertRelationships('mother-in-law', 'daughter-in-law', [$i2f, $f1m, $i1m, $f4m, $i12f], $en);
            self::assertRelationships('paternal-grandfather', 'grandson', [$i3m, $f1m, $i1m, $f4m, $i11m], $en);
            self::assertRelationships('paternal-grandmother', 'granddaughter', [$i4f, $f1m, $i1m, $f4m, $i12f], $en);
            self::assertRelationships('maternal-grandfather', 'grandson', [$i3m, $f1m, $i2f, $f5m, $i13m], $en);
            self::assertRelationships('maternal-grandmother', 'grandchild', [$i5u, $f1m, $i2f, $f5m, $i14f], $en);
            self::assertRelationships('paternal great-grandfather', 'great-grandson', [$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i16m], $en);
            self::assertRelationships('paternal great-grandmother', 'great-granddaughter', [$i4f, $f1m, $i1m, $f4m, $i11m, $f7, $i17f], $en);
            self::assertRelationships('paternal great-great-grandfather', 'great-great-grandson', [$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m], $en);
            self::assertRelationships('paternal great-great-grandmother', 'great-great-granddaughter', [$i4f, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i19f], $en);
            self::assertRelationships('paternal great-great-great-grandfather', 'great-great-great-grandson', [$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i20m], $en);
            self::assertRelationships('paternal great-great-great-grandmother', 'great-great-great-granddaughter', [$i4f, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f], $en);
            self::assertRelationships('paternal great ×4 grandfather', 'great ×4 grandson', [$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i22m], $en);
            self::assertRelationships('paternal great ×4 grandmother', 'great ×4 granddaughter', [$i4f, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i23f], $en);
            self::assertRelationships('aunt', 'niece', [$i38f, $f7, $i17f, $f8, $i37f], $en);
            self::assertRelationships('great-uncle', 'great-nephew', [$i30m, $f8, $i18m, $f9, $i21f, $f10, $i24m], $en);
            self::assertRelationships('great-great-uncle', 'great-great-nephew', [$i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m], $en);
            self::assertRelationships('nephew', 'uncle', [$i24m, $f10, $i21f, $f9, $i18m], $en);
            self::assertRelationships('great-niece', 'great-uncle', [$i24m, $f10, $i21f, $f9, $i18m, $f8, $i17f], $en);
            self::assertRelationships('great-great-nephew', 'great-great-uncle', [$i24m, $f10, $i21f, $f9, $i18m, $f8, $i17f, $f7, $i11m], $en);
            self::assertRelationships('first cousin', 'first cousin', [$i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f], $en);
            self::assertRelationships('second cousin', 'second cousin', [$i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m], $en);
            self::assertRelationships('first cousin once removed ascending', 'first cousin once removed descending', [$i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f], $en);
            self::assertRelationships('third cousin', 'third cousin', [$i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m, $f15, $i34f], $en);
            self::assertRelationships('second cousin once removed ascending', 'second cousin once removed descending', [$i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m], $en);
            self::assertRelationships('first cousin twice removed ascending', 'first cousin twice removed descending', [$i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f], $en);
            self::assertRelationships('fourth cousin', 'fourth cousin', [$i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m, $f15, $i34f, $f16, $i35m], $en);
            self::assertRelationships('third cousin once removed ascending', 'third cousin once removed descending', [$i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m, $f15, $i34f], $en);
            self::assertRelationships('second cousin twice removed ascending', 'second cousin twice removed descending', [$i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m], $en);
            // Compound relationships
            self::assertRelationships('wife’s ex-husband', 'ex-wife’s husband', [$i1m, $f1m, $i2f, $f2d, $i6m], $en);
        }

        // This relationship has a different name in different variants of English.
        self::assertRelationships('first cousin thrice removed ascending', 'first cousin thrice removed descending', [$i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f], $en_au);
        self::assertRelationships('first cousin thrice removed ascending', 'first cousin thrice removed descending', [$i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f], $en_gb);
        self::assertRelationships('first cousin three times removed ascending', 'first cousin three times removed descending', [$i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f], $en_us);

        ///////////////////////////////////////////////////////////////////////
        // FRENCH
        ///////////////////////////////////////////////////////////////////////

        $fr_fr = new LanguageFrench();
        $fr_ca = new LanguageFrench();

        foreach ([$fr_fr, $fr_ca] as $fr) {
            self::assertRelationships('épouse', 'époux', [$i1m, $f1m, $i2f], $fr);
            self::assertRelationships('conjoint', 'conjoint', [$i9u, $f6, $i15u], $fr);
            self::assertRelationships('ex-époux', 'ex-épouse', [$i2f, $f2d, $i6m], $fr);
            self::assertRelationships('fiancé', 'fiancée', [$i10f, $f3e, $i3m], $fr);
            self::assertRelationships('fils', 'père', [$i1m, $f1m, $i3m], $fr);
            self::assertRelationships('fille', 'mère', [$i2f, $f1m, $i4f], $fr);
            self::assertRelationships('enfant', 'père', [$i1m, $f1m, $i5u], $fr);
            self::assertRelationships('grand frère', 'petite sœur', [$i4f, $f1m, $i3m], $fr);
            self::assertRelationships('petit frère/sœur', 'grand frère', [$i3m, $f1m, $i5u], $fr);
            self::assertRelationships('frère', 'sœur', [$i38f, $f7, $i11m], $fr);
            self::assertRelationships('frère/sœur', 'sœur', [$i8f, $f2d, $i9u], $fr);
            self::assertRelationships('mère adoptive', 'fils adoptif', [$i7ma, $f2d, $i2f], $fr);
            self::assertRelationships('père adoptif', 'fils adoptif', [$i7ma, $f2d, $i6m], $fr);
            self::assertRelationships('sœur adoptive', 'frère adoptif', [$i7ma, $f2d, $i8f], $fr);
            self::assertRelationships('mère d’accueil', 'fils accueilli', [$i39mo, $f14d, $i32f], $fr);
            self::assertRelationships('père d’accueil', 'fils accueilli', [$i39mo, $f14d, $i31m], $fr);
            self::assertRelationships('sœur d’accueil', 'frère accueilli', [$i39mo, $f14d, $i33f], $fr);
            self::assertRelationships('beau-père', 'belle-fille', [$i8f, $f2d, $i2f, $f1m, $i1m], $fr);
            self::assertRelationships('demi-frère', 'demi-frère/sœur', [$i9u, $f2d, $i2f, $f1m, $i3m], $fr);
            self::assertRelationship('quasi-sœur', [$i8f, $f2d, $i6m, $f13m, $i31m, $f14d, $i33f], $fr);
            self::assertRelationships('beau-père', 'gendre', [$i1m, $f1m, $i2f, $f5m, $i13m], $fr);
            self::assertRelationships('belle-mère', 'bru', [$i2f, $f1m, $i1m, $f4m, $i12f], $fr);
            self::assertRelationships('grand-père paternel', 'petit-fils', [$i3m, $f1m, $i1m, $f4m, $i11m], $fr);
            self::assertRelationships('grand-mère paternelle', 'petite-fille', [$i4f, $f1m, $i1m, $f4m, $i12f], $fr);
            self::assertRelationships('grand-père maternel', 'petit-fils', [$i3m, $f1m, $i2f, $f5m, $i13m], $fr);
            self::assertRelationships('grand-mère maternelle', 'petite-fille', [$i4f, $f1m, $i2f, $f5m, $i14f], $fr);
            self::assertRelationships('arrière-grand-père paternel', 'arrière-petit-fils', [$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i16m], $fr);
            self::assertRelationships('arrière-grand-mère paternelle', 'arrière-petite-fille', [$i4f, $f1m, $i1m, $f4m, $i11m, $f7, $i17f], $fr);
            self::assertRelationships('trisaïeul paternel', 'petit-fils au 3<sup>e</sup> degré', [$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m], $fr);
            self::assertRelationships('trisaïeule paternelle', 'petite-fille au 3<sup>e</sup> degré', [$i4f, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i19f], $fr);
            self::assertRelationships('grand-père paternel au 4<sup>e</sup> degré', 'petit-fils au 4<sup>e</sup> degré', [$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i20m], $fr);
            self::assertRelationships('grand-mère paternelle au 4<sup>e</sup> degré', 'petite-fille au 4<sup>e</sup> degré', [$i4f, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f], $fr);
            self::assertRelationships('oncle', 'neveu', [$i18m, $f9, $i21f, $f10, $i24m], $fr);
            self::assertRelationships('grand-oncle', 'petite-nièce', [$i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m], $fr);
            self::assertRelationships('arrière-grand-oncle', 'arrière-petit-neveu', [$i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m], $fr);
            self::assertRelationships('grand-oncle au 3<sup>e</sup> degré', 'petit-neveu au 3<sup>e</sup> degré', [$i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m], $fr);
            self::assertRelationships('grand-oncle au 4<sup>e</sup> degré', 'petite-nièce au 4<sup>e</sup> degré', [$i4f, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m], $fr);
            self::assertRelationships('cousine germaine', 'cousin germain', [$i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f], $fr);
            self::assertRelationships('cousin issu de germain', 'cousin issu de germain', [$i30m, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m], $fr);
            self::assertRelationships('cousine au 3<sup>e</sup> degré', 'cousin au 3<sup>e</sup> degré', [$i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m, $f15, $i34f], $fr);
            self::assertRelationships('grand-cousine', 'petit-cousin', [$i30m, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f], $fr);
            self::assertRelationships('petit-cousin', 'grand-cousine', [$i26f, $f11m, $i24m, $f10, $i21f, $f9, $i18m, $f8, $i30m], $fr);
            self::assertRelationships('cousin du 2<sup>e</sup> au 3<sup>e</sup> degré', 'cousin du 3<sup>e</sup> au 2<sup>e</sup> degré', [$i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m], $fr);
            self::assertRelationships('cousine du 3<sup>e</sup> au 2<sup>e</sup> degré', 'cousine du 2<sup>e</sup> au 3<sup>e</sup> degré', [$i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m, $f15, $i34f], $fr);
            // Compound relationships
            self::assertRelationships('ex-époux de l’épouse', 'époux de l’ex-épouse', [$i1m, $f1m, $i2f, $f2d, $i6m], $fr);
            self::assertRelationship('fiancée du petit-neveu au 4<sup>e</sup> degré', [$i24m, $f10, $i21f, $f9, $i18m, $f8, $i17f, $f7, $i11m, $f4m, $i1m, $f1m, $i3m, $f3e, $i10f], $fr);
            self::assertRelationship('épouse de l’arrière-petit-neveu', [$i24m, $f10, $i21f, $f9, $i18m, $f8, $i17f, $f7, $i11m, $f4m, $i12f], $fr);
            self::assertRelationship('conjoint de la petite-nièce', [$i24m, $f10, $i21f, $f9, $i18m, $f8, $i17f, $f7, $i16m], $fr);
            self::assertRelationship('conjointe du neveu', [$i24m, $f10, $i21f, $f9, $i18m, $f8, $i19f], $fr);
            self::assertRelationships('cousine germaine du conjoint', 'conjointe du cousin germain', [$i19f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f], $fr);
        }

        ///////////////////////////////////////////////////////////////////////
        // SLOVAK
        ///////////////////////////////////////////////////////////////////////

        $sk = new LanguageSlovakian();

        self::assertRelationships('manželka', 'manžel', [$i1m, $f1m, $i2f], $sk);
        self::assertRelationships('partner', 'partner', [$i9u, $f6, $i15u], $sk);
        self::assertRelationships('exmanžel', 'exmanželka', [$i2f, $f2d, $i6m], $sk);
        self::assertRelationships('snúbenica', 'snúbenec', [$i10f, $f3e, $i3m], $sk);
        self::assertRelationships('syn', 'otec', [$i1m, $f1m, $i3m], $sk);
        self::assertRelationships('dcéra', 'matka', [$i2f, $f1m, $i4f], $sk);
        self::assertRelationships('dieťa', 'otec', [$i1m, $f1m, $i5u], $sk);
        self::assertRelationships('brat', 'sestra', [$i4f, $f1m, $i3m], $sk);
        self::assertRelationships('súrodenec', 'brat', [$i3m, $f1m, $i5u], $sk);
        self::assertRelationships('brat', 'sestra', [$i8f, $f2d, $i7ma], $sk);
        self::assertRelationships('súrodenec', 'brat', [$i7ma, $f2d, $i9u], $sk);
        self::assertRelationships('matka', 'syn', [$i7ma, $f2d, $i2f], $sk);
        self::assertRelationships('manžel matky', 'dieťa manželky', [$i9u, $f2d, $i2f, $f1m, $i1m], $sk);
        self::assertRelationships('dcéra manželky', 'manželka matky', [$i2f, $f1m, $i2f, $f2d, $i8f], $sk);
        self::assertRelationships('dcéra manžela otca', 'dieťa manžela otca', [$i9u, $f2d, $i6m, $f13m, $i31m, $f14d, $i33f], $sk);
        self::assertRelationships('nevlastný brat', 'nevlastná sestra', [$i8f, $f2d, $i2f, $f1m, $i3m], $sk);
        self::assertRelationships('svokra', 'nevesta', [$i2f, $f1m, $i1m, $f4m, $i12f], $sk);
        self::assertRelationships('starý otec', 'vnuk', [$i3m, $f1m, $i1m, $f4m, $i11m], $sk);
        self::assertRelationships('stará matka', 'vnučka', [$i4f, $f1m, $i1m, $f4m, $i12f], $sk);
        self::assertRelationships('starý otec', 'vnuk', [$i3m, $f1m, $i2f, $f5m, $i13m], $sk);
        self::assertRelationships('stará matka', 'vnúča', [$i5u, $f1m, $i2f, $f5m, $i14f], $sk);
        self::assertRelationships('prastarý otec', 'pravnuk', [$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i16m], $sk);
        self::assertRelationships('prastarý otec', 'pravnučka', [$i4f, $f1m, $i1m, $f4m, $i11m, $f7, $i17f], $sk);
        self::assertRelationships('pra-pra-pra-prastarý otec', 'pravnuk dcéry', [$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m], $sk);
        self::assertRelationships('pra-pra-pra-prastará matka', 'pravnučka dcéry', [$i4f, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i19f], $sk);
        self::assertRelationships('pra ×4 prastarý otec', 'pravnuk vnučky', [$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i20m], $sk);
        self::assertRelationships('pra ×4 prastará matka', 'pravnučka vnučky', [$i4f, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f], $sk);
        self::assertRelationships('pra ×5 prastarý otec', 'pravnuk pravnučky', [$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i22m], $sk);
        self::assertRelationships('pra ×5 prastará matka', 'pravnučka pravnučky', [$i4f, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i23f], $sk);
        self::assertRelationships('teta', 'neter', [$i38f, $f7, $i17f, $f8, $i37f], $sk);
        self::assertRelationships('prastrýko', 'prasynovec', [$i30m, $f8, $i18m, $f9, $i21f, $f10, $i24m], $sk);
        self::assertRelationships('pra-prastrýko', 'pravnuk sestry', [$i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m], $sk);
        self::assertRelationships('synovec', 'ujo', [$i24m, $f10, $i21f, $f9, $i18m], $sk);
        self::assertRelationships('praneter', 'prastrýko', [$i24m, $f10, $i21f, $f9, $i18m, $f8, $i17f], $sk);
        self::assertRelationships('pravnuk sestry', 'pra-prastrýko', [$i24m, $f10, $i21f, $f9, $i18m, $f8, $i17f, $f7, $i11m], $sk);
        self::assertRelationships('sesternica', 'bratranec', [$i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f], $sk);
        self::assertRelationships('druhostupňový bratranec', 'druhostupňová sesternica', [$i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m], $sk);
        self::assertRelationships('sesternica otca', 'praneter otca', [$i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f], $sk);
        self::assertRelationships('sesternica z 3. kolena', 'bratranec z 3. kolena', [$i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m, $f15, $i34f], $sk);
        self::assertRelationships('druhostupňový bratranec matky', 'syn druhostupňovej sesternice', [$i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m], $sk);
        self::assertRelationships('pra-dcéra prastrýka', 'pravnuk tety', [$i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f], $sk);
        self::assertRelationships('bratranec zo 4. kolena', 'bratranec zo 4. kolena', [$i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m, $f15, $i34f, $f16, $i35m], $sk);
        self::assertRelationships('sesternica z 3. kolena otca', 'syn bratranca z 3. kolena', [$i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m, $f15, $i34f], $sk);
        self::assertRelationships('druhostupňový bratranec starej matky', 'vnuk druhostupňovej sesternice', [$i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f, $f12, $i29m], $sk);
        self::assertRelationships('pra-pra-dcéra prastrýka', 'pravnuk bratranca', [$i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i24m, $f11m, $i26f], $sk);        // Compound relationships
        self::assertRelationships('exmanžel manželky', 'manžel exmanželky', [$i1m, $f1m, $i2f, $f2d, $i6m], $sk);
    }

    /**
     * @param string                   $expected
     * @param array<Individual|Family> $nodes
     * @param ModuleLanguageInterface  $language
     */
    private static function assertRelationship(string $expected, array $nodes, ModuleLanguageInterface $language): void
    {
        $service = new RelationshipService();
        $actual  = $service->nameFromPath($nodes, $language);
        $path    = implode('-', array_map(static fn (GedcomRecord $record): string => $record->xref(), $nodes));
        $english = $service->nameFromPath($nodes, new LanguageEnglishUnitedStates());
        $message = 'Language: ' . $language->title() . PHP_EOL . 'Path: ' . $path . ' (' . $english . ')';

        self::assertSame($expected, $actual, $message);
    }

    /**
     * Test a relationship name in both directions
     *
     * @param string                   $fwd
     * @param string                   $rev
     * @param array<Individual|Family> $nodes
     * @param ModuleLanguageInterface  $language
     */
    private static function assertRelationships(string $fwd, string $rev, array $nodes, ModuleLanguageInterface $language): void
    {
        self::assertRelationship($fwd, $nodes, $language);
        self::assertRelationship($rev, array_reverse($nodes), $language);
    }
}

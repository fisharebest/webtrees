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
use Fisharebest\Webtrees\Module\LanguageEnglishUnitedStates;
use Fisharebest\Webtrees\Services\RelationshipService;

/**
 * Test the user functions
 *
 * @covers \Fisharebest\Webtrees\Relationship
 * @covers \Fisharebest\Webtrees\Services\RelationshipService
 * @covers \Fisharebest\Webtrees\Module\LanguageEnglishGreatBritain
 * @covers \Fisharebest\Webtrees\Module\ModuleLanguageTrait
 */
class RelationshipNamesTest extends TestCase
{
    /**
     * @return void
     */
    public function testRelationshipNames(): void
    {
        //                                            i22m===f10===i23f
        //                                                    |
        //                                              +-----+-----+
        //                                              |
        //                                i20m===f9===i21f
        //                                        |
        //                           i19f===f8===i18m
        //                                   |
        //                     i16m===f7===i17f
        //                            |
        //              i12f===f4m===i11m  i13m===f5m===i14f
        //                      |                  |
        //                     i1m===f1m==========i2f===f2d===i6m
        //                            |                  |
        //                        +---+---+          +---+---+
        //                        |   |   |          |   |   |
        //          i10f===f3e===i3m i4f i5u       i7ma i8f i9u===f6===i15u
        //
        // Individual suffixes - m(ale), f(emale), u(nknown), a(dopted)
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

        $individual_factory->method('make')->will($this->returnValueMap([
            'i1m'  => $i1m,
            'i2f'  => $i2f,
            'i3m'  => $i3m,
            'i4f'  => $i4f,
            'i5u'  => $i5u,
            'i6m'  => $i6m,
            'i7ma'  => $i7ma,
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
        ]));

        $f1m = new Family('f1m', "0 @f1m@ FAM\n1 MARR Y\n1 HUSB @i1m@\n1 WIFE @i2f@\n1 CHIL @i3m@\n1 CHIL @i4f@\n1 CHIL @i5u@", null, $tree);
        $f2d = new Family('f2d', "0 @f2d@ FAM\n1 DIV Y\n1 HUSB @i6m@\n1 WIFE @i2f@\n1 CHIL @i7ma@\n1 CHIL @i8f@\n1 CHIL @i9u@", null, $tree);
        $f3e = new Family('f3e', "0 @f3e@ FAM\n1 ENGA Y\n1 HUSB @i3m@\n1 WIFE @i10f@", null, $tree);
        $f4m = new Family('f4m', "0 @f4m@ FAM\n1 MARR Y\n1 HUSB @i11m@\n1 WIFE @i12f@\n1 CHIL @i1m@", null, $tree);
        $f5m = new Family('f5m', "0 @f5m@ FAM\n1 MARR Y\n1 HUSB @i13m@\n1 WIFE @i14f@\n1 CHIL @i2f@", null, $tree);
        $f6  = new Family('f6', "0 @f6@ FAM\n1 HUSB @i9u@\n1 WIFE @i15u@", null, $tree);
        $f7  = new Family('f7', "0 @f7@ FAM\n1 HUSB @i16m@\n1 WIFE @i17f@\n1 CHIL @i11m@", null, $tree);
        $f8  = new Family('f8', "0 @f8@ FAM\n1 HUSB @i18m@\n1 WIFE @i19f@\n1 CHIL @i17f@", null, $tree);
        $f9  = new Family('f9', "0 @f9@ FAM\n1 HUSB @i20m@\n1 WIFE @i21f@\n1 CHIL @i18m@", null, $tree);
        $f10 = new Family('f10', "0 @f10@ FAM\n1 HUSB @i22m@\n1 WIFE @i23f@\n1 CHIL @i21f@", null, $tree);

        $family_factory->method('make')->will($this->returnValueMap([
            'f1m' => $f1m,
            'f2d' => $f2d,
            'f3e' => $f3e,
            'f4m' => $f4m,
            'f5m' => $f5m,
            'f6'  => $f6,
            'f7'  => $f7,
            'f8'  => $f8,
            'f9'  => $f9,
            'f10'  => $f10,
        ]));

        $service = new RelationshipService();

        // ENGLISH
        $en_gb = new LanguageEnglishUnitedStates();
        // Static relationships
        self::assertSame('wife', $service->nameFromPath([$i1m, $f1m, $i2f], $en_gb));
        self::assertSame('husband', $service->nameFromPath([$i2f, $f1m, $i1m], $en_gb));
        self::assertSame('partner', $service->nameFromPath([$i9u, $f6, $i15u], $en_gb));
        self::assertSame('ex-husband', $service->nameFromPath([$i2f, $f2d, $i6m], $en_gb));
        self::assertSame('ex-wife', $service->nameFromPath([$i6m, $f2d, $i2f], $en_gb));
        self::assertSame('fiancé', $service->nameFromPath([$i10f, $f3e, $i3m], $en_gb));
        self::assertSame('fiancée', $service->nameFromPath([$i3m, $f3e, $i10f], $en_gb));
        self::assertSame('son', $service->nameFromPath([$i1m, $f1m, $i3m], $en_gb));
        self::assertSame('daughter', $service->nameFromPath([$i1m, $f1m, $i4f], $en_gb));
        self::assertSame('child', $service->nameFromPath([$i1m, $f1m, $i5u], $en_gb));
        self::assertSame('younger brother', $service->nameFromPath([$i4f, $f1m, $i3m], $en_gb));
        self::assertSame('elder sister', $service->nameFromPath([$i3m, $f1m, $i4f], $en_gb));
        self::assertSame('elder sibling', $service->nameFromPath([$i3m, $f1m, $i5u], $en_gb));
        self::assertSame('brother', $service->nameFromPath([$i8f, $f2d, $i7ma], $en_gb));
        self::assertSame('sister', $service->nameFromPath([$i7ma, $f2d, $i8f], $en_gb));
        self::assertSame('sibling', $service->nameFromPath([$i7ma, $f2d, $i9u], $en_gb));
        self::assertSame('adoptive-mother', $service->nameFromPath([$i7ma, $f2d, $i2f], $en_gb));
        self::assertSame('adoptive-father', $service->nameFromPath([$i7ma, $f2d, $i6m], $en_gb));
        self::assertSame('adopted-son', $service->nameFromPath([$i6m, $f2d, $i7ma], $en_gb));
        self::assertSame('paternal-grandfather', $service->nameFromPath([$i3m, $f1m, $i1m, $f4m, $i11m], $en_gb));
        self::assertSame('paternal-grandmother', $service->nameFromPath([$i3m, $f1m, $i1m, $f4m, $i12f], $en_gb));
        self::assertSame('maternal-grandfather', $service->nameFromPath([$i3m, $f1m, $i2f, $f5m, $i13m], $en_gb));
        self::assertSame('maternal-grandmother', $service->nameFromPath([$i3m, $f1m, $i2f, $f5m, $i14f], $en_gb));
        // Dynamic relationships
        self::assertSame('paternal great-grandfather', $service->nameFromPath([$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i16m], $en_gb));
        self::assertSame('paternal great-grandmother', $service->nameFromPath([$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i17f], $en_gb));
        self::assertSame('paternal great-great-grandfather', $service->nameFromPath([$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m], $en_gb));
        self::assertSame('paternal great-great-grandmother', $service->nameFromPath([$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i19f], $en_gb));
        self::assertSame('paternal great-great-great-grandfather', $service->nameFromPath([$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i20m], $en_gb));
        self::assertSame('paternal great-great-great-grandmother', $service->nameFromPath([$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f], $en_gb));
        self::assertSame('paternal great ×4 grandfather', $service->nameFromPath([$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i22m], $en_gb));
        self::assertSame('paternal great ×4 grandmother', $service->nameFromPath([$i3m, $f1m, $i1m, $f4m, $i11m, $f7, $i17f, $f8, $i18m, $f9, $i21f, $f10, $i23f], $en_gb));
        // Compound relationships
        self::assertSame('wife’s ex-husband', $service->nameFromPath([$i1m, $f1m, $i2f, $f2d, $i6m], $en_gb));

        // SLOVAK
    }
}

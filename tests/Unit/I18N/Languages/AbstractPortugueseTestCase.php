<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Tests\Unit\I18N\Languages;

use Fisharebest\Webtrees\I18N\Languages\Portuguese;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Portuguese::class)]
abstract class AbstractPortugueseTestCase extends AbstractLanguageTestCase
{
    public function testRelationships(): void
    {
        self::initFactories();

        $husband = self::male('h', "1 FAMS @fm@\n1 FAMC @fp@");
        $wife = self::female('w', "1 FAMS @fm@\n1 FAMS @fd@\n1 FAMC @fw@");
        $son = self::male('s', "1 FAMC @fm@\n1 BIRT\n2 DATE 2000");
        $daughter = self::female('d', "1 FAMC @fm@\n1 BIRT\n2 DATE 2001");
        $exHusband = self::male('ex', "1 FAMS @fd@");
        $adoptedSon = self::male('as', "1 FAMC @fd@\n2 PEDI adopted");
        $stepDaughter = self::female('sd', "1 FAMC @fd@");
        $fatherOfH = self::male('fh', "1 FAMS @fp@");
        $motherOfH = self::female('mh', "1 FAMS @fp@");
        $fatherOfW = self::male('fw_h', "1 FAMS @fw@");
        $motherOfW = self::female('fw_w', "1 FAMS @fw@");
        $brotherOfH = self::male('bh', "1 FAMC @fp@");
        $sisterOfH = self::female('sh', "1 FAMC @fp@");
        $nieceFromBro = self::female('nb', "1 FAMC @fbro@");
        $nephewFromBro = self::male('npb', "1 FAMC @fbro@");
        $cousinFemale = self::female('cf', "1 FAMC @fbro@");
        $cousinMale = self::male('cm', "1 FAMC @fbro@");
        $paternalGF = self::male('pgf', "1 FAMS @fgp@");
        $paternalGM = self::female('pgm', "1 FAMS @fgp@");
        $greatAunt = self::female('ga', "1 FAMC @fgp@");
        $greatUncle = self::male('gu', "1 FAMC @fgp@");
        $engaged = self::female('eng', "1 FAMS @fe@");
        $fiance = self::male('fan', "1 FAMS @fe@");

        $fm = self::family('fm', "0 @fm@ FAM\n1 MARR Y\n1 HUSB @h@\n1 WIFE @w@\n1 CHIL @s@\n1 CHIL @d@");
        $fd = self::family('fd', "0 @fd@ FAM\n1 DIV Y\n1 HUSB @ex@\n1 WIFE @w@\n1 CHIL @as@\n1 CHIL @sd@");
        $fp = self::family('fp', "0 @fp@ FAM\n1 HUSB @fh@\n1 WIFE @mh@\n1 CHIL @h@\n1 CHIL @bh@\n1 CHIL @sh@");
        $fw = self::family('fw', "0 @fw@ FAM\n1 HUSB @fw_h@\n1 WIFE @fw_w@\n1 CHIL @w@");
        $fbro = self::family('fbro', "0 @fbro@ FAM\n1 HUSB @bh@\n1 CHIL @nb@\n1 CHIL @npb@\n1 CHIL @cf@\n1 CHIL @cm@");
        $fgp = self::family('fgp', "0 @fgp@ FAM\n1 HUSB @pgf@\n1 WIFE @pgm@\n1 CHIL @fh@\n1 CHIL @ga@\n1 CHIL @gu@");
        $fe = self::family('fe', "0 @fe@ FAM\n1 ENGA Y\n1 HUSB @fan@\n1 WIFE @eng@");

        self::registerStubs(
            [$husband, $wife, $son, $daughter, $exHusband, $adoptedSon, $stepDaughter,
             $fatherOfH, $motherOfH, $fatherOfW, $motherOfW, $brotherOfH, $sisterOfH,
             $nieceFromBro, $nephewFromBro, $cousinFemale, $cousinMale,
             $paternalGF, $paternalGM, $greatAunt, $greatUncle, $engaged, $fiance],
            [$fm, $fd, $fp, $fw, $fbro, $fgp, $fe]
        );

        // Partners
        self::assertRelationshipNames('esposa', 'marido', [$husband, $fm, $wife]);
        self::assertRelationshipNames('ex-marido', 'ex-esposa', [$wife, $fd, $exHusband]);
        self::assertRelationshipNames('noiva', 'noivo', [$fiance, $fe, $engaged]);

        // Parents
        self::assertRelationshipNames('mãe', 'filho', [$son, $fm, $wife]);
        self::assertRelationshipNames('pai', 'filho', [$son, $fm, $husband]);
        self::assertRelationshipNames('mãe', 'filha', [$daughter, $fm, $wife]);

        // Adopted
        self::assertRelationshipNames('mãe adotiva', 'filho adotivo', [$adoptedSon, $fd, $wife]);

        // Siblings
        self::assertRelationshipNames('irmã mais nova', 'irmão mais velho', [$son, $fm, $daughter]);

        // Half-siblings
        self::assertRelationshipName('meio-irmão', [$stepDaughter, $fd, $wife, $fm, $son]);

        // Stepfamily
        self::assertRelationshipName('padrasto', [$stepDaughter, $fd, $wife, $fm, $husband]);
        self::assertRelationshipName('enteada', [$husband, $fm, $wife, $fd, $stepDaughter]);

        // In-laws
        self::assertRelationshipNames('sogra', 'genro', [$husband, $fm, $wife, $fw, $motherOfW]);
        self::assertRelationshipNames('sogro', 'genro', [$husband, $fm, $wife, $fw, $fatherOfW]);
        self::assertRelationshipName('nora', [$fatherOfH, $fp, $husband, $fm, $wife]);

        // Grandparents
        self::assertRelationshipNames('avó', 'neto', [$son, $fm, $husband, $fp, $motherOfH]);
        self::assertRelationshipNames('avô', 'neto', [$son, $fm, $husband, $fp, $fatherOfH]);
        self::assertRelationshipName('neta', [$fatherOfH, $fp, $husband, $fm, $daughter]);

        // Great-grandparents (dynamic) - n-1=2 → "tris"
        self::assertRelationshipName('trisavô', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGF]);
        self::assertRelationshipName('trisavó', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $paternalGM]);

        // Aunts and uncles
        self::assertRelationshipNames('tia', 'sobrinho', [$son, $fm, $husband, $fp, $sisterOfH]);
        self::assertRelationshipNames('tio', 'sobrinho', [$son, $fm, $husband, $fp, $brotherOfH]);

        // Nieces and nephews
        self::assertRelationshipName('sobrinha', [$husband, $fp, $brotherOfH, $fbro, $nieceFromBro]);
        self::assertRelationshipName('sobrinho', [$husband, $fp, $brotherOfH, $fbro, $nephewFromBro]);

        // Cousins
        self::assertRelationshipName('prima', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinFemale]);
        self::assertRelationshipName('primo', [$son, $fm, $husband, $fp, $brotherOfH, $fbro, $cousinMale]);

        // Great-aunt/uncle (dynamic) - n=2, great(n-1=1) → "bis"
        self::assertRelationshipName('bistia', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatAunt]);
        self::assertRelationshipName('bistio', [$son, $fm, $husband, $fp, $fatherOfH, $fgp, $greatUncle]);
    }
}

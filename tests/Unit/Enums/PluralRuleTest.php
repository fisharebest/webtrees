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

namespace Fisharebest\Webtrees\Tests\Unit\Enums;

use Fisharebest\Webtrees\Enums\PluralRule;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(PluralRule::class)]
class PluralRuleTest extends TestCase
{
    public function testNpluralsOneForm(): void
    {
        self::assertSame(1, PluralRule::OneForm->nplurals());
    }

    public function testNpluralsTwoForms(): void
    {
        self::assertSame(2, PluralRule::TwoFormsSingularForOne->nplurals());
        self::assertSame(2, PluralRule::TwoFormsPluralForMoreThanOne->nplurals());
        self::assertSame(2, PluralRule::TwoFormsMacedonian->nplurals());
        self::assertSame(2, PluralRule::TwoFormsTagalog->nplurals());
    }

    public function testNpluralsThreeForms(): void
    {
        self::assertSame(3, PluralRule::ThreeFormsSlavic->nplurals());
        self::assertSame(3, PluralRule::ThreeFormsCzechSlovak->nplurals());
        self::assertSame(3, PluralRule::ThreeFormsPolish->nplurals());
        self::assertSame(3, PluralRule::ThreeFormsRomanian->nplurals());
        self::assertSame(3, PluralRule::ThreeFormsLithuanian->nplurals());
        self::assertSame(3, PluralRule::ThreeFormsLatvian->nplurals());
    }

    public function testNpluralsFourForms(): void
    {
        self::assertSame(4, PluralRule::FourFormsSlovenian->nplurals());
    }

    public function testNpluralsSixForms(): void
    {
        self::assertSame(6, PluralRule::SixFormsArabic->nplurals());
        self::assertSame(6, PluralRule::SixFormsWelsh->nplurals());
    }

    /**
     * OneForm: always returns 0.
     */
    public function testOneForm(): void
    {
        $rule = PluralRule::OneForm;

        self::assertSame(0, $rule->plural(0));
        self::assertSame(0, $rule->plural(1));
        self::assertSame(0, $rule->plural(2));
        self::assertSame(0, $rule->plural(100));
    }

    /**
     * TwoFormsSingularForOne: n != 1 ? 1 : 0
     * English, German, Dutch, etc.
     */
    #[DataProvider('twoFormsSingularForOneProvider')]
    public function testTwoFormsSingularForOne(int $n, int $expected): void
    {
        self::assertSame($expected, PluralRule::TwoFormsSingularForOne->plural($n));
    }

    /**
     * @return array<string,list{int,int}>
     */
    public static function twoFormsSingularForOneProvider(): array
    {
        return [
            'zero is plural'   => [0, 1],
            'one is singular'  => [1, 0],
            'two is plural'    => [2, 1],
            'five is plural'   => [5, 1],
            'hundred is plural' => [100, 1],
        ];
    }

    /**
     * TwoFormsPluralForMoreThanOne: n > 1 ? 1 : 0
     * French, Hindi, Turkish, etc.
     */
    #[DataProvider('twoFormsPluralForMoreThanOneProvider')]
    public function testTwoFormsPluralForMoreThanOne(int $n, int $expected): void
    {
        self::assertSame($expected, PluralRule::TwoFormsPluralForMoreThanOne->plural($n));
    }

    /**
     * @return array<string,list{int,int}>
     */
    public static function twoFormsPluralForMoreThanOneProvider(): array
    {
        return [
            'zero is singular'  => [0, 0],
            'one is singular'   => [1, 0],
            'two is plural'     => [2, 1],
            'five is plural'    => [5, 1],
            'hundred is plural' => [100, 1],
        ];
    }

    /**
     * TwoFormsMacedonian: n==1 || n%10==1 ? 0 : 1
     */
    #[DataProvider('twoFormsMacedonianProvider')]
    public function testTwoFormsMacedonian(int $n, int $expected): void
    {
        self::assertSame($expected, PluralRule::TwoFormsMacedonian->plural($n));
    }

    /**
     * @return array<string,list{int,int}>
     */
    public static function twoFormsMacedonianProvider(): array
    {
        return [
            'zero is plural'        => [0, 1],
            'one is singular'       => [1, 0],
            'two is plural'         => [2, 1],
            'eleven is singular'    => [11, 0],  // n%10==1
            'twenty-one is singular' => [21, 0], // n%10==1
            'twelve is plural'      => [12, 1],
            'thirty-one is singular' => [31, 0], // n%10==1
            'hundred is plural'     => [100, 1],
            'hundred-one is singular' => [101, 0], // n%10==1
        ];
    }

    /**
     * TwoFormsTagalog: n != 1 && n != 2 && n != 3 && (n%10 == 4 || n%10 == 6 || n%10 == 9)
     */
    #[DataProvider('twoFormsTagalogProvider')]
    public function testTwoFormsTagalog(int $n, int $expected): void
    {
        self::assertSame($expected, PluralRule::TwoFormsTagalog->plural($n));
    }

    /**
     * @return array<string,list{int,int}>
     */
    public static function twoFormsTagalogProvider(): array
    {
        return [
            'zero'          => [0, 0],
            'one'           => [1, 0],
            'two'           => [2, 0],
            'three'         => [3, 0],
            'four'          => [4, 1],  // n%10==4, n!=1,2,3
            'five'          => [5, 0],
            'six'           => [6, 1],  // n%10==6, n!=1,2,3
            'seven'         => [7, 0],
            'nine'          => [9, 1],  // n%10==9, n!=1,2,3
            'ten'           => [10, 0],
            'fourteen'      => [14, 1], // n%10==4
            'sixteen'       => [16, 1], // n%10==6
            'nineteen'      => [19, 1], // n%10==9
            'twenty'        => [20, 0],
            'twenty-four'   => [24, 1], // n%10==4
        ];
    }

    /**
     * ThreeFormsSlavic: Russian, Ukrainian, Croatian, Serbian, Bosnian.
     * n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2
     */
    #[DataProvider('threeFormsSlavicProvider')]
    public function testThreeFormsSlavic(int $n, int $expected): void
    {
        self::assertSame($expected, PluralRule::ThreeFormsSlavic->plural($n));
    }

    /**
     * @return array<string,list{int,int}>
     */
    public static function threeFormsSlavicProvider(): array
    {
        return [
            'zero'              => [0, 2],
            'one'               => [1, 0],
            'two'               => [2, 1],
            'three'             => [3, 1],
            'four'              => [4, 1],
            'five'              => [5, 2],
            'ten'               => [10, 2],
            'eleven'            => [11, 2],  // exception: n%100==11
            'twelve'            => [12, 2],  // exception: n%100==12
            'thirteen'          => [13, 2],  // exception: n%100==13
            'fourteen'          => [14, 2],  // exception: n%100==14
            'twenty'            => [20, 2],
            'twenty-one'        => [21, 0],  // n%10==1, n%100!=11
            'twenty-two'        => [22, 1],  // n%10==2, n%100>=20
            'twenty-three'      => [23, 1],
            'twenty-four'       => [24, 1],
            'twenty-five'       => [25, 2],
            'hundred'           => [100, 2],
            'hundred-one'       => [101, 0],
            'hundred-two'       => [102, 1],
            'hundred-eleven'    => [111, 2], // exception: n%100==11
            'hundred-twelve'    => [112, 2], // exception: n%100==12
            'hundred-twenty-one' => [121, 0],
            'hundred-twenty-two' => [122, 1],
        ];
    }

    /**
     * ThreeFormsCzechSlovak: n==1 ? 0 : (n>=2 && n<=4) ? 1 : 2
     */
    #[DataProvider('threeFormsCzechSlovakProvider')]
    public function testThreeFormsCzechSlovak(int $n, int $expected): void
    {
        self::assertSame($expected, PluralRule::ThreeFormsCzechSlovak->plural($n));
    }

    /**
     * @return array<string,list{int,int}>
     */
    public static function threeFormsCzechSlovakProvider(): array
    {
        return [
            'zero'   => [0, 2],
            'one'    => [1, 0],
            'two'    => [2, 1],
            'three'  => [3, 1],
            'four'   => [4, 1],
            'five'   => [5, 2],
            'ten'    => [10, 2],
            'twenty' => [20, 2],
            'hundred' => [100, 2],
        ];
    }

    /**
     * ThreeFormsPolish: n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2
     */
    #[DataProvider('threeFormsPolishProvider')]
    public function testThreeFormsPolish(int $n, int $expected): void
    {
        self::assertSame($expected, PluralRule::ThreeFormsPolish->plural($n));
    }

    /**
     * @return array<string,list{int,int}>
     */
    public static function threeFormsPolishProvider(): array
    {
        return [
            'zero'              => [0, 2],
            'one'               => [1, 0],
            'two'               => [2, 1],
            'three'             => [3, 1],
            'four'              => [4, 1],
            'five'              => [5, 2],
            'twelve'            => [12, 2],  // n%100 in 10-19 range
            'thirteen'          => [13, 2],
            'fourteen'          => [14, 2],
            'twenty-two'        => [22, 1],  // n%10==2, n%100>=20
            'twenty-three'      => [23, 1],
            'twenty-four'       => [24, 1],
            'twenty-five'       => [25, 2],
            'hundred-two'       => [102, 1],
            'hundred-twelve'    => [112, 2], // n%100 in 10-19 range
            'hundred-twenty-two' => [122, 1],
        ];
    }

    /**
     * ThreeFormsRomanian: n==1 ? 0 : (n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2
     */
    #[DataProvider('threeFormsRomanianProvider')]
    public function testThreeFormsRomanian(int $n, int $expected): void
    {
        self::assertSame($expected, PluralRule::ThreeFormsRomanian->plural($n));
    }

    /**
     * @return array<string,list{int,int}>
     */
    public static function threeFormsRomanianProvider(): array
    {
        return [
            'zero'              => [0, 1],
            'one'               => [1, 0],
            'two'               => [2, 1],  // n%100==2, <20
            'nineteen'          => [19, 1], // n%100==19, <20
            'twenty'            => [20, 2], // n%100==20, not <20
            'hundred'           => [100, 2], // n%100==0
            'hundred-one'       => [101, 1], // n%100==1, but n!=1 so form 1
            'hundred-nineteen'  => [119, 1], // n%100==19, <20
            'hundred-twenty'    => [120, 2], // n%100==20
            'two-hundred'       => [200, 2], // n%100==0
        ];
    }

    /**
     * ThreeFormsLithuanian: n%10==1 && n%100!=11 ? 0 : n%10>=2 && (n%100<10 || n%100>=20) ? 1 : 2
     */
    #[DataProvider('threeFormsLithuanianProvider')]
    public function testThreeFormsLithuanian(int $n, int $expected): void
    {
        self::assertSame($expected, PluralRule::ThreeFormsLithuanian->plural($n));
    }

    /**
     * @return array<string,list{int,int}>
     */
    public static function threeFormsLithuanianProvider(): array
    {
        return [
            'zero'              => [0, 2],
            'one'               => [1, 0],   // n%10==1, n%100!=11
            'two'               => [2, 1],   // n%10>=2, n%100<10
            'nine'              => [9, 1],   // n%10>=2, n%100<10
            'ten'               => [10, 2],  // n%10==0
            'eleven'            => [11, 2],  // exception: n%100==11
            'twelve'            => [12, 2],  // n%100 in 10-19 range
            'nineteen'          => [19, 2],  // n%100 in 10-19 range
            'twenty'            => [20, 2],  // n%10==0
            'twenty-one'        => [21, 0],  // n%10==1, n%100!=11
            'twenty-two'        => [22, 1],  // n%10>=2, n%100>=20
            'thirty'            => [30, 2],  // n%10==0
            'thirty-one'        => [31, 0],  // n%10==1
            'hundred'           => [100, 2],
            'hundred-one'       => [101, 0],
            'hundred-eleven'    => [111, 2], // exception: n%100==11
            'hundred-twelve'    => [112, 2], // n%100 in 10-19
            'hundred-twenty-one' => [121, 0],
        ];
    }

    /**
     * ThreeFormsLatvian: n%10==1 && n%100!=11 ? 0 : n != 0 ? 1 : 2
     */
    #[DataProvider('threeFormsLatvianProvider')]
    public function testThreeFormsLatvian(int $n, int $expected): void
    {
        self::assertSame($expected, PluralRule::ThreeFormsLatvian->plural($n));
    }

    /**
     * @return array<string,list{int,int}>
     */
    public static function threeFormsLatvianProvider(): array
    {
        return [
            'zero'              => [0, 2],
            'one'               => [1, 0],  // n%10==1, n%100!=11
            'two'               => [2, 1],
            'ten'               => [10, 1],
            'eleven'            => [11, 1],  // exception: n%100==11, but n!=0 so form 1
            'twenty-one'        => [21, 0],  // n%10==1, n%100!=11
            'thirty-one'        => [31, 0],
            'hundred'           => [100, 1],
            'hundred-one'       => [101, 0],
            'hundred-eleven'    => [111, 1], // n%100==11 exception
        ];
    }

    /**
     * FourFormsSlovenian: n%100==1 ? 0 : n%100==2 ? 1 : n%100==3 || n%100==4 ? 2 : 3
     */
    #[DataProvider('fourFormsSlovenianProvider')]
    public function testFourFormsSlovenian(int $n, int $expected): void
    {
        self::assertSame($expected, PluralRule::FourFormsSlovenian->plural($n));
    }

    /**
     * @return array<string,list{int,int}>
     */
    public static function fourFormsSlovenianProvider(): array
    {
        return [
            'zero'              => [0, 3],
            'one'               => [1, 0],   // n%100==1
            'two'               => [2, 1],   // n%100==2
            'three'             => [3, 2],   // n%100==3
            'four'              => [4, 2],   // n%100==4
            'five'              => [5, 3],
            'hundred'           => [100, 3],
            'hundred-one'       => [101, 0], // n%100==1
            'hundred-two'       => [102, 1], // n%100==2
            'hundred-three'     => [103, 2], // n%100==3
            'hundred-four'      => [104, 2], // n%100==4
            'hundred-five'      => [105, 3],
            'two-hundred-one'   => [201, 0], // n%100==1
            'two-hundred-two'   => [202, 1], // n%100==2
        ];
    }

    /**
     * SixFormsArabic: n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 ? 4 : 5
     */
    #[DataProvider('sixFormsArabicProvider')]
    public function testSixFormsArabic(int $n, int $expected): void
    {
        self::assertSame($expected, PluralRule::SixFormsArabic->plural($n));
    }

    /**
     * @return array<string,list{int,int}>
     */
    public static function sixFormsArabicProvider(): array
    {
        return [
            'zero'              => [0, 0],
            'one'               => [1, 1],
            'two'               => [2, 2],
            'three'             => [3, 3],   // n%100>=3 && n%100<=10
            'ten'               => [10, 3],  // n%100>=3 && n%100<=10
            'eleven'            => [11, 4],  // n%100>=11
            'ninety-nine'       => [99, 4],  // n%100>=11
            'hundred'           => [100, 5], // n%100==0, not matched by 3 or 4
            'hundred-one'       => [101, 5], // n%100==1, not matched
            'hundred-two'       => [102, 5], // n%100==2, not matched
            'hundred-three'     => [103, 3], // n%100==3
            'hundred-ten'       => [110, 3], // n%100==10
            'hundred-eleven'    => [111, 4], // n%100==11
            'two-hundred'       => [200, 5], // n%100==0
        ];
    }

    /**
     * SixFormsWelsh: n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n==3 ? 3 : n==6 ? 4 : 5
     */
    #[DataProvider('sixFormsWelshProvider')]
    public function testSixFormsWelsh(int $n, int $expected): void
    {
        self::assertSame($expected, PluralRule::SixFormsWelsh->plural($n));
    }

    /**
     * @return array<string,list{int,int}>
     */
    public static function sixFormsWelshProvider(): array
    {
        return [
            'zero'    => [0, 0],
            'one'     => [1, 1],
            'two'     => [2, 2],
            'three'   => [3, 3],
            'four'    => [4, 5],
            'five'    => [5, 5],
            'six'     => [6, 4],
            'seven'   => [7, 5],
            'ten'     => [10, 5],
            'hundred' => [100, 5],
        ];
    }

    /**
     * Verify that plural() always returns an index less than nplurals().
     */
    public function testPluralResultIsWithinNpluralsRange(): void
    {
        $testNumbers = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15,
            19, 20, 21, 22, 23, 24, 25, 30, 31, 50, 99, 100, 101, 102, 103,
            104, 105, 110, 111, 112, 119, 120, 121, 122, 200, 201, 1000];

        foreach (PluralRule::cases() as $rule) {
            foreach ($testNumbers as $n) {
                $result = $rule->plural($n);
                self::assertGreaterThanOrEqual(0, $result, "{$rule->name}->plural($n) returned negative");
                self::assertLessThan($rule->nplurals(), $result, "{$rule->name}->plural($n) returned $result but nplurals is {$rule->nplurals()}");
            }
        }
    }
}

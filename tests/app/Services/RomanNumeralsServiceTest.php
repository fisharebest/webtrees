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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(RomanNumeralsService::class)]
class RomanNumeralsServiceTest extends TestCase
{
    private RomanNumeralsService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new RomanNumeralsService();
    }

    /**
     * @return array<string,array{number:int,roman:string}>
     */
    public static function romanNumeralData(): array
    {
        return [
            '1'    => ['number' => 1, 'roman' => 'I'],
            '4'    => ['number' => 4, 'roman' => 'IV'],
            '5'    => ['number' => 5, 'roman' => 'V'],
            '9'    => ['number' => 9, 'roman' => 'IX'],
            '10'   => ['number' => 10, 'roman' => 'X'],
            '14'   => ['number' => 14, 'roman' => 'XIV'],
            '40'   => ['number' => 40, 'roman' => 'XL'],
            '49'   => ['number' => 49, 'roman' => 'XLIX'],
            '50'   => ['number' => 50, 'roman' => 'L'],
            '90'   => ['number' => 90, 'roman' => 'XC'],
            '100'  => ['number' => 100, 'roman' => 'C'],
            '400'  => ['number' => 400, 'roman' => 'CD'],
            '500'  => ['number' => 500, 'roman' => 'D'],
            '900'  => ['number' => 900, 'roman' => 'CM'],
            '1000' => ['number' => 1000, 'roman' => 'M'],
            '1926' => ['number' => 1926, 'roman' => 'MCMXXVI'],
            '2024' => ['number' => 2024, 'roman' => 'MMXXIV'],
            '3999' => ['number' => 3999, 'roman' => 'MMMCMXCIX'],
        ];
    }

    #[DataProvider('romanNumeralData')]
    public function testNumberToRomanNumerals(int $number, string $roman): void
    {
        self::assertSame($roman, $this->service->numberToRomanNumerals($number));
    }

    #[DataProvider('romanNumeralData')]
    public function testRomanNumeralsToNumber(int $number, string $roman): void
    {
        self::assertSame($number, $this->service->romanNumeralsToNumber($roman));
    }

    public function testRomanNumeralsToNumberWithUpperCase(): void
    {
        self::assertSame(4, $this->service->romanNumeralsToNumber('IV'));
        self::assertSame(14, $this->service->romanNumeralsToNumber('XIV'));
    }

    public function testZeroReturnsZeroString(): void
    {
        self::assertSame('0', $this->service->numberToRomanNumerals(0));
    }
}

<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use PHPUnit\Framework\Attributes\CoversClass;

use function GregorianToJD;
use function mktime;

#[CoversClass(Timestamp::class)]
class TimestampTest extends TestCase
{
    public function testJulianDay(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame(2460296, GregorianToJD(12, 17, 2023));
        self::assertSame(2460296, $timestamp->julianDay());
    }

    public function testDiffForHumans(): void
    {
        $timestamp = new Timestamp(time(), 'UTC', 'en-US');

        self::assertSame('5 years ago', $timestamp->subtractYears(5)->diffForHumans());
        self::assertSame('5 months ago', $timestamp->subtractMonths(5)->diffForHumans());
        self::assertSame('5 days ago', $timestamp->subtractDays(5)->diffForHumans());
        self::assertSame('5 hours ago', $timestamp->subtractHours(5)->diffForHumans());
        self::assertSame('5 minutes ago', $timestamp->subtractMinutes(5)->diffForHumans());
        self::assertSame('5 seconds ago', $timestamp->subtractSeconds(5)->diffForHumans());

        $timestamp = new Timestamp(time(), 'UTC', 'fr_FR');

        self::assertSame('il y a 5 ans', $timestamp->subtractYears(5)->diffForHumans());
        self::assertSame('il y a 5 mois', $timestamp->subtractMonths(5)->diffForHumans());
        self::assertSame('il y a 5 jours', $timestamp->subtractDays(5)->diffForHumans());
        self::assertSame('il y a 5 heures', $timestamp->subtractHours(5)->diffForHumans());
        self::assertSame('il y a 5 minutes', $timestamp->subtractMinutes(5)->diffForHumans());
        self::assertSame('il y a 5 secondes', $timestamp->subtractSeconds(5)->diffForHumans());
    }

    public function testFormat(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame('17', $timestamp->format('d'));
        self::assertSame('Sun', $timestamp->format('D'));
        self::assertSame('17', $timestamp->format('j'));
        self::assertSame('Sunday', $timestamp->format('l'));
        self::assertSame('7', $timestamp->format('N'));
        self::assertSame('th', $timestamp->format('S'));
        self::assertSame('0', $timestamp->format('w'));
        self::assertSame('350', $timestamp->format('z'));
        self::assertSame('50', $timestamp->format('W'));
        self::assertSame('December', $timestamp->format('F'));
        self::assertSame('12', $timestamp->format('m'));
        self::assertSame('Dec', $timestamp->format('M'));
        self::assertSame('12', $timestamp->format('n'));
        self::assertSame('31', $timestamp->format('t'));
        self::assertSame('0', $timestamp->format('L'));
        self::assertSame('2023', $timestamp->format('o'));
        self::assertSame('2023', $timestamp->format('Y'));
        self::assertSame('23', $timestamp->format('y'));
        self::assertSame('pm', $timestamp->format('a'));
        self::assertSame('PM', $timestamp->format('A'));
        self::assertSame('723', $timestamp->format('B'));
        self::assertSame('4', $timestamp->format('g'));
        self::assertSame('16', $timestamp->format('G'));
        self::assertSame('04', $timestamp->format('h'));
        self::assertSame('16', $timestamp->format('H'));
        self::assertSame('21', $timestamp->format('i'));
        self::assertSame('19', $timestamp->format('s'));
        self::assertSame('000000', $timestamp->format('u'));
        self::assertSame('UTC', $timestamp->format('e'));
        self::assertSame('0', $timestamp->format('I'));
        self::assertSame('+0000', $timestamp->format('O'));
        self::assertSame('+00:00', $timestamp->format('P'));
        self::assertSame('UTC', $timestamp->format('T'));
        self::assertSame('0', $timestamp->format('Z'));
        self::assertSame('2023-12-17T16:21:19+00:00', $timestamp->format('c'));
        self::assertSame('Sun, 17 Dec 2023 16:21:19 +0000', $timestamp->format('r'));
        self::assertSame('1702830079', $timestamp->format('U'));
    }

    public function testIsoFormat(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame('12/17/2023', $timestamp->isoFormat('l'));
        self::assertSame('Dec 17, 2023', $timestamp->isoFormat('ll'));
        self::assertSame('Dec 17, 2023 4:21 PM', $timestamp->isoFormat('lll'));
        self::assertSame('Sun, Dec 17, 2023 4:21 PM', $timestamp->isoFormat('llll'));

        self::assertSame('12/17/2023', $timestamp->isoFormat('L'));
        self::assertSame('December 17, 2023', $timestamp->isoFormat('LL'));
        self::assertSame('December 17, 2023 4:21 PM', $timestamp->isoFormat('LLL'));
        self::assertSame('Sunday, December 17, 2023 4:21 PM', $timestamp->isoFormat('LLLL'));
    }

    public function testToDateTimeString(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
    }

    public function testCompare(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp1 = new Timestamp($time, 'UTC', 'en-US');

        $time = mktime(16, 21, 20, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp2 = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame(-1, $timestamp1->compare($timestamp2));
        self::assertSame(0, $timestamp1->compare($timestamp1));
        self::assertSame(1, $timestamp2->compare($timestamp1));
    }

    public function testAddSeconds(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        self::assertSame('2023-12-17 16:21:20', $timestamp->addSeconds(1)->toDateTimeString());
        self::assertSame('2023-12-17 16:21:18', $timestamp->addSeconds(-1)->toDateTimeString());
    }

    public function testAddMinutes(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        self::assertSame('2023-12-17 16:22:19', $timestamp->addMinutes(1)->toDateTimeString());
        self::assertSame('2023-12-17 16:20:19', $timestamp->addMinutes(-1)->toDateTimeString());
    }

    public function testAddHours(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        self::assertSame('2023-12-17 17:21:19', $timestamp->addHours(1)->toDateTimeString());
        self::assertSame('2023-12-17 15:21:19', $timestamp->addHours(-1)->toDateTimeString());
    }

    public function testAddDays(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        self::assertSame('2023-12-18 16:21:19', $timestamp->addDays(1)->toDateTimeString());
    }

    public function testAddMonths(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        self::assertSame('2024-01-17 16:21:19', $timestamp->addMonths(1)->toDateTimeString());
    }

    public function testAddYears(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        self::assertSame('2024-12-17 16:21:19', $timestamp->addYears(1)->toDateTimeString());
    }

    public function testSubtractSeconds(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        self::assertSame('2023-12-17 16:21:18', $timestamp->subtractSeconds(1)->toDateTimeString());
        self::assertSame('2023-12-17 16:21:20', $timestamp->subtractSeconds(-1)->toDateTimeString());
    }

    public function testSubtractMinutes(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        self::assertSame('2023-12-17 16:20:19', $timestamp->subtractMinutes(1)->toDateTimeString());
        self::assertSame('2023-12-17 16:22:19', $timestamp->subtractMinutes(-1)->toDateTimeString());
    }

    public function testSubtractHours(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        self::assertSame('2023-12-17 15:21:19', $timestamp->subtractHours(1)->toDateTimeString());
        self::assertSame('2023-12-17 17:21:19', $timestamp->subtractHours(-1)->toDateTimeString());
    }

    public function testSubtractDays(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        self::assertSame('2023-12-16 16:21:19', $timestamp->subtractDays(1)->toDateTimeString());
        self::assertSame('2023-12-18 16:21:19', $timestamp->subtractDays(-1)->toDateTimeString());
    }

    public function testSubtractMonths(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        self::assertSame('2023-11-17 16:21:19', $timestamp->subtractMonths(1)->toDateTimeString());
        self::assertSame('2024-01-17 16:21:19', $timestamp->subtractMonths(-1)->toDateTimeString());
    }

    public function testSubtractYears(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        self::assertSame('2022-12-17 16:21:19', $timestamp->subtractYears(1)->toDateTimeString());
        self::assertSame('2024-12-17 16:21:19', $timestamp->subtractYears(-1)->toDateTimeString());
    }

    public function testTimestamp(): void
    {
        $time = mktime(16, 21, 19, 12, 17, 2023);
        self::assertIsInt($time);
        $timestamp = new Timestamp($time, 'UTC', 'en-US');

        self::assertSame(1702830079, $timestamp->timestamp());
    }
}

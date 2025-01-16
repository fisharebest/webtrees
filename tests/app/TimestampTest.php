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

use function gregoriantojd;
use function mktime;

/**
 * Test harness for the class Timestamp
 *
 * @covers \Fisharebest\Webtrees\Timestamp
 */
class TimestampTest extends TestCase
{
    public function testJulianDay(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame(2460296, gregoriantojd(12, 17, 2023));
        $this->assertSame(2460296, $timestamp->julianDay());
    }

    public function testDiffForHumans(): void
    {
        $timestamp = new Timestamp(time(), 'UTC', 'en-US');

        $this->assertSame('5 years ago', $timestamp->subtractYears(5)->diffForHumans());
        $this->assertSame('5 months ago', $timestamp->subtractMonths(5)->diffForHumans());
        $this->assertSame('5 days ago', $timestamp->subtractDays(5)->diffForHumans());
        $this->assertSame('5 hours ago', $timestamp->subtractHours(5)->diffForHumans());
        $this->assertSame('5 minutes ago', $timestamp->subtractMinutes(5)->diffForHumans());
        $this->assertSame('5 seconds ago', $timestamp->subtractSeconds(5)->diffForHumans());

        $timestamp = new Timestamp(time(), 'UTC', 'fr_FR');

        $this->assertSame('il y a 5 ans', $timestamp->subtractYears(5)->diffForHumans());
        $this->assertSame('il y a 5 mois', $timestamp->subtractMonths(5)->diffForHumans());
        $this->assertSame('il y a 5 jours', $timestamp->subtractDays(5)->diffForHumans());
        $this->assertSame('il y a 5 heures', $timestamp->subtractHours(5)->diffForHumans());
        $this->assertSame('il y a 5 minutes', $timestamp->subtractMinutes(5)->diffForHumans());
        $this->assertSame('il y a 5 secondes', $timestamp->subtractSeconds(5)->diffForHumans());
    }

    public function testFormat(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame('17', $timestamp->format('d'));
        $this->assertSame('Sun', $timestamp->format('D'));
        $this->assertSame('17', $timestamp->format('j'));
        $this->assertSame('Sunday', $timestamp->format('l'));
        $this->assertSame('7', $timestamp->format('N'));
        $this->assertSame('th', $timestamp->format('S'));
        $this->assertSame('0', $timestamp->format('w'));
        $this->assertSame('350', $timestamp->format('z'));
        $this->assertSame('50', $timestamp->format('W'));
        $this->assertSame('December', $timestamp->format('F'));
        $this->assertSame('12', $timestamp->format('m'));
        $this->assertSame('Dec', $timestamp->format('M'));
        $this->assertSame('12', $timestamp->format('n'));
        $this->assertSame('31', $timestamp->format('t'));
        $this->assertSame('0', $timestamp->format('L'));
        $this->assertSame('2023', $timestamp->format('o'));
        $this->assertSame('2023', $timestamp->format('Y'));
        $this->assertSame('23', $timestamp->format('y'));
        $this->assertSame('pm', $timestamp->format('a'));
        $this->assertSame('PM', $timestamp->format('A'));
        $this->assertSame('723', $timestamp->format('B'));
        $this->assertSame('4', $timestamp->format('g'));
        $this->assertSame('16', $timestamp->format('G'));
        $this->assertSame('04', $timestamp->format('h'));
        $this->assertSame('16', $timestamp->format('H'));
        $this->assertSame('21', $timestamp->format('i'));
        $this->assertSame('19', $timestamp->format('s'));
        $this->assertSame('000000', $timestamp->format('u'));
        $this->assertSame('UTC', $timestamp->format('e'));
        $this->assertSame('0', $timestamp->format('I'));
        $this->assertSame('+0000', $timestamp->format('O'));
        $this->assertSame('+00:00', $timestamp->format('P'));
        $this->assertSame('UTC', $timestamp->format('T'));
        $this->assertSame('0', $timestamp->format('Z'));
        $this->assertSame('2023-12-17T16:21:19+00:00', $timestamp->format('c'));
        $this->assertSame('Sun, 17 Dec 2023 16:21:19 +0000', $timestamp->format('r'));
        $this->assertSame('1702830079', $timestamp->format('U'));
    }

    public function testIsoFormat(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame('12/17/2023', $timestamp->isoFormat('l'));
        $this->assertSame('Dec 17, 2023', $timestamp->isoFormat('ll'));
        $this->assertSame('Dec 17, 2023 4:21 PM', $timestamp->isoFormat('lll'));
        $this->assertSame('Sun, Dec 17, 2023 4:21 PM', $timestamp->isoFormat('llll'));

        $this->assertSame('12/17/2023', $timestamp->isoFormat('L'));
        $this->assertSame('December 17, 2023', $timestamp->isoFormat('LL'));
        $this->assertSame('December 17, 2023 4:21 PM', $timestamp->isoFormat('LLL'));
        $this->assertSame('Sunday, December 17, 2023 4:21 PM', $timestamp->isoFormat('LLLL'));
    }

    public function testToDateTimeString(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
    }

    public function testCompare(): void
    {
        $timestamp1 = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');
        $timestamp2 = new Timestamp(mktime(16, 21, 20, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame(-1, $timestamp1->compare($timestamp2));
        $this->assertSame(0, $timestamp1->compare($timestamp1));
        $this->assertSame(1, $timestamp2->compare($timestamp1));
    }

    public function testAddSeconds(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        $this->assertSame('2023-12-17 16:21:20', $timestamp->addSeconds(1)->toDateTimeString());
        $this->assertSame('2023-12-17 16:21:18', $timestamp->addSeconds(-1)->toDateTimeString());
    }

    public function testAddMinutes(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        $this->assertSame('2023-12-17 16:22:19', $timestamp->addMinutes(1)->toDateTimeString());
        $this->assertSame('2023-12-17 16:20:19', $timestamp->addMinutes(-1)->toDateTimeString());
    }

    public function testAddHours(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        $this->assertSame('2023-12-17 17:21:19', $timestamp->addHours(1)->toDateTimeString());
        $this->assertSame('2023-12-17 15:21:19', $timestamp->addHours(-1)->toDateTimeString());
    }

    public function testAddDays(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        $this->assertSame('2023-12-18 16:21:19', $timestamp->addDays(1)->toDateTimeString());
    }

    public function testAddMonths(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        $this->assertSame('2024-01-17 16:21:19', $timestamp->addMonths(1)->toDateTimeString());
    }

    public function testAddYears(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        $this->assertSame('2024-12-17 16:21:19', $timestamp->addYears(1)->toDateTimeString());
    }

    public function testSubtractSeconds(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        $this->assertSame('2023-12-17 16:21:18', $timestamp->subtractSeconds(1)->toDateTimeString());
        $this->assertSame('2023-12-17 16:21:20', $timestamp->subtractSeconds(-1)->toDateTimeString());
    }

    public function testSubtractMinutes(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        $this->assertSame('2023-12-17 16:20:19', $timestamp->subtractMinutes(1)->toDateTimeString());
        $this->assertSame('2023-12-17 16:22:19', $timestamp->subtractMinutes(-1)->toDateTimeString());
    }

    public function testSubtractHours(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        $this->assertSame('2023-12-17 15:21:19', $timestamp->subtractHours(1)->toDateTimeString());
        $this->assertSame('2023-12-17 17:21:19', $timestamp->subtractHours(-1)->toDateTimeString());
    }

    public function testSubtractDays(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        $this->assertSame('2023-12-16 16:21:19', $timestamp->subtractDays(1)->toDateTimeString());
        $this->assertSame('2023-12-18 16:21:19', $timestamp->subtractDays(-1)->toDateTimeString());
    }

    public function testSubtractMonths(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        $this->assertSame('2023-11-17 16:21:19', $timestamp->subtractMonths(1)->toDateTimeString());
        $this->assertSame('2024-01-17 16:21:19', $timestamp->subtractMonths(-1)->toDateTimeString());
    }

    public function testSubtractYears(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame('2023-12-17 16:21:19', $timestamp->toDateTimeString());
        $this->assertSame('2022-12-17 16:21:19', $timestamp->subtractYears(1)->toDateTimeString());
        $this->assertSame('2024-12-17 16:21:19', $timestamp->subtractYears(-1)->toDateTimeString());
    }

    public function testTimestamp(): void
    {
        $timestamp = new Timestamp(mktime(16, 21, 19, 12, 17, 2023), 'UTC', 'en-US');

        $this->assertSame(1702830079, $timestamp->timestamp());
    }
}

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

namespace Fisharebest\Webtrees\Date;

use Fisharebest\Webtrees\TestCase;

/**
 * Test harness for the class CalendarDate
 */
class CalendarDateTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Date\AbstractCalendarDate::ageDifference
     * @return void
     */
    public function testAgeDifference(): void
    {
        $date1 = new GregorianDate(['1900', 'FEB', '4']);
        $date2 = new GregorianDate(['1930', 'FEB', '3']);
        $this->assertSame([29, 11, 27], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1900', 'FEB', '4']);
        $date2 = new GregorianDate(['1900', 'JUN', '3']);
        $this->assertSame([0, 3, 27], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1900', 'FEB', '4']);
        $date2 = new GregorianDate(['1900', 'JUL', '3']);
        $this->assertSame([0, 4, 27], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1900', 'FEB', '4']);
        $date2 = new GregorianDate(['1900', 'AUG', '3']);
        $this->assertSame([0, 5, 27], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1900', 'FEB', '4']);
        $date2 = new GregorianDate(['1900', 'FEB', '7']);
        $this->assertSame([0, 0, 3], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1900', 'FEB', '4']);
        $date2 = new GregorianDate(['1900', 'FEB', '4']);
        $this->assertSame([0, 0, 0], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1900', 'FEB', '4']);
        $date2 = new GregorianDate(['1900', 'FEB', '3']);
        $this->assertSame([-1, 11, 27], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1930', 'FEB', '4']);
        $date2 = new GregorianDate(['1900', 'FEB', '3']);
        $this->assertSame([-31, 11, 27], $date1->ageDifference($date2));
    }

    /**
     * @covers \Fisharebest\Webtrees\Date\AbstractCalendarDate::ageDifference
     * @return void
     */
    public function testAgeDifferenceIncomplete(): void
    {
        $date1 = new GregorianDate(['', 'FEB', '4']);
        $date2 = new GregorianDate(['1900', 'FEB', '3']);
        $this->assertSame([-1, -1, -1], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1900', 'FEB', '4']);
        $date2 = new GregorianDate(['', 'FEB', '3']);
        $this->assertSame([-1, -1, -1], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1900', 'FEB', '']);
        $date2 = new GregorianDate(['1900', 'FEB', '3']);
        $this->assertSame([0, 0, 0], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1900', '', '']);
        $date2 = new GregorianDate(['1900', 'FEB', '3']);
        $this->assertSame([0, 0, 0], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1900', 'FEB', '3']);
        $date2 = new GregorianDate(['1900', 'FEB', '']);
        $this->assertSame([0, 0, 0], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1900', 'FEB', '3']);
        $date2 = new GregorianDate(['1900', '', '']);
        $this->assertSame([0, 0, 0], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1900', 'JAN', '']);
        $date2 = new GregorianDate(['1900', 'FEB', '4']);
        $this->assertSame([0, 1, 3], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1900', 'JAN', '']);
        $date2 = new GregorianDate(['1901', 'MAR', '4']);
        $this->assertSame([1, 2, 3], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1900', 'JAN', '4']);
        $date2 = new GregorianDate(['1900', 'FEB', '']);
        $this->assertSame([0, 0, 28], $date1->ageDifference($date2));
    }

    /**
     * @covers \Fisharebest\Webtrees\Date\AbstractCalendarDate::ageDifference
     * @return void
     */
    public function testAgeDifferenceOverlap(): void
    {
        $date1 = new GregorianDate(['1900', 'FEB', '4']);
        $date2 = new GregorianDate(['1900', 'FEB', '']);
        $this->assertSame([0, 0, 0], $date1->ageDifference($date2));

        $date1 = new GregorianDate(['1900', '', '']);
        $date2 = new GregorianDate(['1900', 'FEB', '3']);
        $this->assertSame([0, 0, 0], $date1->ageDifference($date2));
    }
}

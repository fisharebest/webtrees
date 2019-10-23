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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Age as WebtreesAge;

/**
 * Test the Age functions
 */
class AgeTest extends TestCase
{
    /**
     * Test that the class exists
     *
     * @return void
     */
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists('\Fisharebest\Webtrees\Age'));
    }

    /**
     * @dataProvider ageProvider
     * @covers \Fisharebest\Webtrees\Age::__construct
     * @covers \Fisharebest\Webtrees\Age::asText
     * @covers \Fisharebest\Webtrees\Age::extractNumber
     * @return void
     */
    public function testConstructor($ageTest, $ageAsText):void
    {
        $age = new Age($ageTest);
        $this->assertSame($ageAsText, $age->asText());
    }

    public function ageProvider()
    {
        return [
          ['stillborn','(stillborn)'],
          ['infant','(in infancy)'],
          ['child','(in childhood)'],
          ['6y 3m','(aged 6 years, 3 months)'],
          ['> 6y 3m 2d','(aged more than 6 years, 3 months, 2 days)'],
          ['< 6y 3m 2d','(aged less than 6 years, 3 months, 2 days)'],
          ['3w','(aged 3 weeks)'],
          ['6y','(aged 6)']
        ];
    }
}

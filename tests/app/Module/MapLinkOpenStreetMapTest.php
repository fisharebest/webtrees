<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use DOMDocument;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\TestCase;

/**
 * @covers \Fisharebest\Webtrees\Module\MapLinkOpenStreetMap
 * @covers \Fisharebest\Webtrees\Module\ModuleMapLinkTrait
 */
class MapLinkOpenStreetMapTest extends TestCase
{
    /**
     * @return void
     */
    public function testNoCoordinates(): void
    {
        $module = new MapLinkOpenStreetMap();

        $fact = $this->createMock(Fact::class);
        $fact->method('latitude')->willReturn(null);
        $fact->method('longitude')->willReturn(null);

        $html = $module->mapLink($fact);

        static::assertSame('', $html);
    }

    /**
     * @return void
     */
    public function testLink(): void
    {
        $module = new MapLinkOpenStreetMap();

        $record = $this->createMock(Individual::class);
        $record->method('fullName')->willReturn('FULL NAME');

        $fact = $this->createMock(Fact::class);
        $fact->method('latitude')->willReturn(54.321);
        $fact->method('longitude')->willReturn(-1.2345);
        $fact->method('label')->willReturn('LABEL');
        $fact->method('record')->willReturn($record);

        $html = $module->mapLink($fact);

        self::assertTrue((new DOMDocument())->loadHTML($html), 'HTML=' . $html);
    }
}

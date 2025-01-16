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

use function str_repeat;

/**
 * Test the site functions
 */
class SiteTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\Site
     */
    public function testDefault(): void
    {
        self::assertSame('', Site::getPreference('no-such-setting'));
        self::assertSame('UTC', Site::getPreference('TIMEZONE'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Site
     */
    public function testSetAndGetPreference(): void
    {
        Site::setPreference('setting', 'foo');

        self::assertSame('foo', Site::getPreference('setting'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Site
     */
    public function test2000CharacterLimit(): void
    {
        $too_long = str_repeat('x', 3000);
        $expected = str_repeat('x', 2000);

        Site::setPreference('setting', $too_long);

        self::assertSame($expected, Site::getPreference('setting'));
    }
}

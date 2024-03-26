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

namespace Fisharebest\Webtrees;

use PHPUnit\Framework\Attributes\CoversClass;

use function error_reporting;

#[CoversClass(Webtrees::class)]
class WebtreesTest extends TestCase
{
    public function testInit(): void
    {
        error_reporting(0);

        $webtrees = new Webtrees();
        $webtrees->bootstrap();

        // webtrees sets the error reporting level.
        self::assertNotSame(0, error_reporting());
        self::assertSame(Webtrees::ERROR_REPORTING, error_reporting());
    }
}

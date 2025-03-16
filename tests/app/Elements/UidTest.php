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

namespace Fisharebest\Webtrees\Elements;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AbstractElement::class)]
#[CoversClass(Uid::class)]
class UidTest extends AbstractElementTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        self::$element = new Uid('label');
    }

    public function testCanonical(): void
    {
        self::assertSame(
            'fef44ca3-ca75-43ed-9a05-f00591315274',
            self::$element->canonical('FEF44ca3ca7543ed9a05f00591315274'),
        );
    }
}

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

use Fisharebest\Webtrees\Enums\AccessLevel;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(AccessLevel::class)]
class AccessLevelTest extends TestCase
{
    /**
     * @return array<list<AccessLevel,AccessLevel,bool>>
     */
    public static function allowsProvider(): array
    {
        return [
            // Hidden is not a real user level.  It is used by the system to bypass privacy
            [AccessLevel::Hidden, AccessLevel::Hidden, true],
            [AccessLevel::Manager, AccessLevel::Hidden, true],
            [AccessLevel::Member, AccessLevel::Hidden, true],
            [AccessLevel::Public, AccessLevel::Hidden, true],
            // Real user levels cannot see hidden data
            [AccessLevel::Hidden, AccessLevel::Public, false],
            [AccessLevel::Hidden, AccessLevel::Member, false],
            [AccessLevel::Hidden, AccessLevel::Manager, false],
            // Managers can see all levels
            [AccessLevel::Manager, AccessLevel::Manager, true],
            [AccessLevel::Member, AccessLevel::Manager, true],
            [AccessLevel::Public, AccessLevel::Manager, true],
            // Members cannot see manager
            [AccessLevel::Manager, AccessLevel::Member, false],
            [AccessLevel::Member, AccessLevel::Member, true],
            [AccessLevel::Public, AccessLevel::Member, true],
            // Public can only see public
            [AccessLevel::Manager, AccessLevel::Public, false],
            [AccessLevel::Member, AccessLevel::Public, false],
            [AccessLevel::Public, AccessLevel::Public, true],

        ];
    }

    #[DataProvider('allowsProvider')]
    public function testAllows(AccessLevel $first, AccessLevel $second, bool $allows): void
    {
        $this->assertSame($allows, $first->allows($second));
        $this->assertSame(!$allows, $first->disallows($second));
    }

    public function testLabel(): void
    {
        self::assertNotSame('', AccessLevel::Hidden->label());
        self::assertNotSame('', AccessLevel::Manager->label());
        self::assertNotSame('', AccessLevel::Member->label());
        self::assertNotSame('', AccessLevel::Public->label());
    }
}

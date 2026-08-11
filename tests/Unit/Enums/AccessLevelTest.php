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

#[CoversClass(AccessLevel::class)]
class AccessLevelTest extends TestCase
{
    public function testManagerCanSeeManagerData(): void
    {
        self::assertTrue(AccessLevel::Manager->allows(AccessLevel::Manager));
    }

    public function testManagerCanSeeMemberData(): void
    {
        self::assertTrue(AccessLevel::Member->allows(AccessLevel::Manager));
    }

    public function testManagerCanSeePublicData(): void
    {
        self::assertTrue(AccessLevel::Public->allows(AccessLevel::Manager));
    }

    public function testManagerCannotSeeHiddenData(): void
    {
        self::assertFalse(AccessLevel::Hidden->allows(AccessLevel::Manager));
    }

    public function testMemberCannotSeeManagerData(): void
    {
        self::assertFalse(AccessLevel::Manager->allows(AccessLevel::Member));
    }

    public function testMemberCanSeeMemberData(): void
    {
        self::assertTrue(AccessLevel::Member->allows(AccessLevel::Member));
    }

    public function testMemberCanSeePublicData(): void
    {
        self::assertTrue(AccessLevel::Public->allows(AccessLevel::Member));
    }

    public function testMemberCannotSeeHiddenData(): void
    {
        self::assertFalse(AccessLevel::Hidden->allows(AccessLevel::Member));
    }

    public function testPublicCannotSeeManagerData(): void
    {
        self::assertFalse(AccessLevel::Manager->allows(AccessLevel::Public));
    }

    public function testPublicCannotSeeMemberData(): void
    {
        self::assertFalse(AccessLevel::Member->allows(AccessLevel::Public));
    }

    public function testPublicCanSeePublicData(): void
    {
        self::assertTrue(AccessLevel::Public->allows(AccessLevel::Public));
    }

    public function testPublicCannotSeeHiddenData(): void
    {
        self::assertFalse(AccessLevel::Hidden->allows(AccessLevel::Public));
    }

    public function testHiddenDisallowsEveryone(): void
    {
        self::assertFalse(AccessLevel::Hidden->allows(AccessLevel::Hidden));
        self::assertFalse(AccessLevel::Hidden->allows(AccessLevel::Manager));
        self::assertFalse(AccessLevel::Hidden->allows(AccessLevel::Member));
        self::assertFalse(AccessLevel::Hidden->allows(AccessLevel::Public));
    }

    public function testDisallowsIsInverseOfAllows(): void
    {
        foreach (AccessLevel::cases() as $data_level) {
            foreach (AccessLevel::cases() as $user_level) {
                self::assertSame(
                    !$data_level->allows($user_level),
                    $data_level->disallows($user_level),
                    $data_level->name . '->disallows(' . $user_level->name . ') should be inverse of allows()',
                );
            }
        }
    }

    public function testLabel(): void
    {
        self::assertNotSame('', AccessLevel::Hidden->label());
        self::assertNotSame('', AccessLevel::Manager->label());
        self::assertNotSame('', AccessLevel::Member->label());
        self::assertNotSame('', AccessLevel::Public->label());
    }
}

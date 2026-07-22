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
use Fisharebest\Webtrees\Enums\Restriction;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Restriction::class)]
class RestrictionTest extends TestCase
{
    public function testFromStringCanonicalValues(): void
    {
        self::assertSame(Restriction::None, Restriction::fromString('NONE'));
        self::assertSame(Restriction::NoneLocked, Restriction::fromString('NONE, LOCKED'));
        self::assertSame(Restriction::Privacy, Restriction::fromString('PRIVACY'));
        self::assertSame(Restriction::PrivacyLocked, Restriction::fromString('PRIVACY, LOCKED'));
        self::assertSame(Restriction::Confidential, Restriction::fromString('CONFIDENTIAL'));
        self::assertSame(Restriction::ConfidentialLocked, Restriction::fromString('CONFIDENTIAL, LOCKED'));
        self::assertSame(Restriction::Locked, Restriction::fromString('LOCKED'));
        self::assertSame(Restriction::Undefined, Restriction::fromString(''));
    }

    public function testFromStringIsCaseInsensitive(): void
    {
        self::assertSame(Restriction::None, Restriction::fromString('none'));
        self::assertSame(Restriction::Privacy, Restriction::fromString('privacy'));
        self::assertSame(Restriction::Confidential, Restriction::fromString('confidential'));
        self::assertSame(Restriction::Locked, Restriction::fromString('locked'));
        self::assertSame(Restriction::PrivacyLocked, Restriction::fromString('privacy, locked'));
    }

    public function testFromStringUnrecognizedReturnsUndefined(): void
    {
        self::assertSame(Restriction::Undefined, Restriction::fromString('UNKNOWN'));
        self::assertSame(Restriction::Undefined, Restriction::fromString('anything'));
    }

    public function testAccessLevel(): void
    {
        self::assertSame(AccessLevel::Public, Restriction::None->accessLevel());
        self::assertSame(AccessLevel::Public, Restriction::NoneLocked->accessLevel());
        self::assertSame(AccessLevel::Member, Restriction::Privacy->accessLevel());
        self::assertSame(AccessLevel::Member, Restriction::PrivacyLocked->accessLevel());
        self::assertSame(AccessLevel::Manager, Restriction::Confidential->accessLevel());
        self::assertSame(AccessLevel::Manager, Restriction::ConfidentialLocked->accessLevel());
        self::assertNull(Restriction::Undefined->accessLevel());
        self::assertNull(Restriction::Locked->accessLevel());
    }

    public function testIsLocked(): void
    {
        self::assertFalse(Restriction::Undefined->isLocked());
        self::assertFalse(Restriction::None->isLocked());
        self::assertTrue(Restriction::NoneLocked->isLocked());
        self::assertFalse(Restriction::Privacy->isLocked());
        self::assertTrue(Restriction::PrivacyLocked->isLocked());
        self::assertFalse(Restriction::Confidential->isLocked());
        self::assertTrue(Restriction::ConfidentialLocked->isLocked());
        self::assertTrue(Restriction::Locked->isLocked());
    }

    public function testIsUnlockedIsInverseOfIsLocked(): void
    {
        foreach (Restriction::cases() as $restriction) {
            self::assertSame(
                !$restriction->isLocked(),
                $restriction->isUnlocked(),
                $restriction->name . '->isUnlocked() should be inverse of isLocked()',
            );
        }
    }

    public function testLabel(): void
    {
        // Undefined has an empty label
        self::assertSame('', Restriction::Undefined->label());

        // All other restrictions have non-empty labels
        self::assertNotSame('', Restriction::None->label());
        self::assertNotSame('', Restriction::NoneLocked->label());
        self::assertNotSame('', Restriction::Privacy->label());
        self::assertNotSame('', Restriction::PrivacyLocked->label());
        self::assertNotSame('', Restriction::Confidential->label());
        self::assertNotSame('', Restriction::ConfidentialLocked->label());
        self::assertNotSame('', Restriction::Locked->label());
    }
}

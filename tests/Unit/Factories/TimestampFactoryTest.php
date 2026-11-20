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

namespace Fisharebest\Webtrees\Tests\Unit\Factories;

use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Factories\TimestampFactory;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TimestampFactory::class)]
class TimestampFactoryTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(TimestampFactory::class));
    }

    public function testFromStringAcceptsWholeAndFractionalSeconds(): void
    {
        self::createDatabase();

        $user = self::createStub(UserInterface::class);
        $user->method('getPreference')->willReturn('UTC');
        $factory = new TimestampFactory(new SystemClock());

        self::assertSame('2026-01-02 03:04:05.000000', $factory->fromString('2026-01-02 03:04:05', user: $user)->format('Y-m-d H:i:s.u'));
        self::assertSame('2026-01-02 03:04:05.123400', $factory->fromString('2026-01-02 03:04:05.1234', user: $user)->format('Y-m-d H:i:s.u'));
        self::assertSame('2026-01-02 03:04:05.123456', $factory->fromString('2026-01-02 03:04:05.123456', user: $user)->format('Y-m-d H:i:s.u'));
        self::assertSame('2026-01-02 03:04:05.123456', $factory->fromString('2026-01-02 03:04:05.1234560', user: $user)->format('Y-m-d H:i:s.u'));
    }
}

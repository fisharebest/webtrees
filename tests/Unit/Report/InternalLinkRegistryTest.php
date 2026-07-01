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

namespace Fisharebest\Webtrees\Tests\Unit\Report;

use Fisharebest\Webtrees\Report\InternalLinkRegistry;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InternalLinkRegistry::class)]
class InternalLinkRegistryTest extends TestCase
{
    public function testCreateAndSetDestination(): void
    {
        $internal_link_registry = new InternalLinkRegistry();

        $link_id = $internal_link_registry->create(3);

        self::assertSame(1, $link_id);
        self::assertTrue($internal_link_registry->has($link_id));
        self::assertSame(['page' => 3, 'y' => 0.0], $internal_link_registry->destination($link_id));

        $internal_link_registry->setDestination($link_id, 42.5, 3, 5);

        self::assertSame(['page' => 5, 'y' => 42.5], $internal_link_registry->destination($link_id));
    }

    public function testSetDestinationUsesCurrentPageWhenPageIsNotProvided(): void
    {
        $internal_link_registry = new InternalLinkRegistry();

        $link_id = $internal_link_registry->create(1);
        $internal_link_registry->setDestination($link_id, 12.0, 7);

        self::assertSame(['page' => 7, 'y' => 12.0], $internal_link_registry->destination($link_id));
    }

    public function testSetDestinationIgnoresUnknownLink(): void
    {
        $internal_link_registry = new InternalLinkRegistry();

        $internal_link_registry->setDestination(99, 10.0, 4, 2);

        self::assertFalse($internal_link_registry->has(99));
    }
}

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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Services\HomePageService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TreePageDefaultUpdate::class)]
class TreePageDefaultUpdateTest extends TestCase
{

    public function testClass(): void
    {
        self::assertTrue(class_exists(TreePageDefaultUpdate::class));
    }

    public function testHandleRedirectsToControlPanel(): void
    {
        $home_page_service = $this->createMock(HomePageService::class);
        $home_page_service->expects(self::once())
            ->method('updateTreeBlocks')
            ->with(-1, self::anything(), self::anything());

        $handler  = new TreePageDefaultUpdate($home_page_service);
        $request  = self::createRequest('POST', [], [
            ModuleBlockInterface::MAIN_BLOCKS => [],
            ModuleBlockInterface::SIDE_BLOCKS => [],
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}

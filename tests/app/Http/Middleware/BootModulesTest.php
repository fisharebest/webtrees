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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Module\WebtreesTheme;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

/**
 * Test the BootModules middleware.
 *
 * @covers \Fisharebest\Webtrees\Http\Middleware\BootModules
 */
class BootModulesTest extends TestCase
{
    public function testMiddleware(): void
    {
        $theme = new WebtreesTheme();

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response('It works!'));

        $module_service = $this->createMock(ModuleService::class);
        $module_service
            ->expects(self::once())
            ->method('bootModules')
            ->with($theme);

        $request    = self::createRequest();
        $middleware = new BootModules($module_service, $theme);
        $response   = $middleware->process($request, $handler);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('It works!', (string) $response->getBody());
    }
}

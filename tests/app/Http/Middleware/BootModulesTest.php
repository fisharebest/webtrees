<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Middleware;

use Fisharebest\Webtrees\Module\WebtreesTheme;
use Fisharebest\Webtrees\Module\XeneaTheme;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

/**
 * Test the BootModules middleware.
 *
 * @covers \Fisharebest\Webtrees\Http\Middleware\BootModules
 */
class BootModulesTest extends TestCase
{
    /**
     * @return void
     */
    public function testMiddleware(): void
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response());

        // Theme 1 (not default) is not booted.
        $theme1 = new class extends WebtreesTheme {
            public function boot()
            {
                throw new \Exception('Should not get here!');
            }
        };

        // Theme 2 (default) is booted.
        $theme2 = new class ($this) extends XeneaTheme {
            private $booted = false;
            private $test;

            public function __construct($test)
            {
                $this->test = $test;
            }

            public function boot()
            {
                $this->booted = true;
            }

            public function __destruct()
            {
                $this->test->assertTrue($this->booted);
            }
        };

        $module_service = $this->createMock(ModuleService::class);
        $module_service->method('all')->willReturn(new Collection([$theme1, $theme2]));

        $request    = self::createRequest();
        $middleware = new BootModules($module_service, $theme2);
        $response   = $middleware->process($request, $handler);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }
}

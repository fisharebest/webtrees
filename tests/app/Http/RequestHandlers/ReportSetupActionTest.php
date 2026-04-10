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

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ReportSetupAction::class)]
class ReportSetupActionTest extends TestCase
{

    public function testClass(): void
    {
        self::assertTrue(class_exists(ReportSetupAction::class));
    }

    public function testHandleRedirectsToGenerateWhenModuleFound(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('name')->willReturn('test');

        $user = self::createStub(UserInterface::class);

        $report = self::createStub(ModuleReportInterface::class);
        $report->method('name')->willReturn('test-report');
        // PRIV_PRIVATE allows guests — avoids HttpAccessDeniedException.
        $report->method('accessLevel')->willReturn(Auth::PRIV_PRIVATE);

        $module_service = self::createStub(ModuleService::class);
        $module_service->method('findByName')->willReturn($report);

        $handler  = new ReportSetupAction($module_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'destination' => 'view',
            'format'      => 'HTML',
            'varnames'    => [],
            'vars'        => [],
        ])
            ->withAttribute('tree', $tree)
            ->withAttribute('user', $user)
            ->withAttribute('report', 'test-report');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertStringContainsString('test-report', $response->getHeaderLine('location'));
    }

    public function testHandleRedirectsToListPageWhenModuleNotFound(): void
    {
        $tree = self::createStub(Tree::class);
        $tree->method('name')->willReturn('test');

        $user = self::createStub(UserInterface::class);

        $module_service = self::createStub(ModuleService::class);
        $module_service->method('findByName')->willReturn(null);

        $handler  = new ReportSetupAction($module_service);
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'destination' => 'view',
            'format'      => 'HTML',
            'varnames'    => [],
            'vars'        => [],
        ])
            ->withAttribute('tree', $tree)
            ->withAttribute('user', $user)
            ->withAttribute('report', 'nonexistent');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}

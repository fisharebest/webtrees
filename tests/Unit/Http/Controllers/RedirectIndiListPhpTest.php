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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Controllers;

use Fisharebest\Webtrees\Enums\HttpRequestMethod;
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\GuestUser;
use Fisharebest\Webtrees\Http\Controllers\RedirectIndiListPhp;
use Fisharebest\Webtrees\Http\Exceptions\HttpGoneException;
use Fisharebest\Webtrees\Module\IndividualListModule;
use Fisharebest\Webtrees\Module\ModuleListInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RedirectIndiListPhp::class)]
class RedirectIndiListPhpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::createDatabase();
    }

    public function testRedirect(): void
    {
        $tree = self::createStub(Tree::class);
        $tree
            ->method('name')
            ->willReturn('tree1');

        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects($this->once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $module = $this->createMock(IndividualListModule::class);
        $module
            ->expects($this->once())
            ->method('listUrl')
            ->willReturn('https://www.example.com');

        $module_service = $this->createMock(ModuleService::class);
        $module_service
            ->expects($this->once())
            ->method('findByComponent')
            ->with(ModuleListInterface::class, $tree, new GuestUser())
            ->willReturn(new Collection([$module]));

        $controller = new RedirectIndiListPhp(new GuestUser(), $module_service, $tree_service);

        $request = self::createRequest(HttpRequestMethod::GET->value, ['ged' => 'tree1']);

        $response = $controller->get($request);

        self::assertSame(HttpStatusCode::MovedPermanently->value, $response->getStatusCode());
        self::assertSame('https://www.example.com', $response->getHeaderLine('Location'));
    }

    public function testModuleDisabled(): void
    {
        $tree = self::createStub(Tree::class);
        $tree
            ->method('name')
            ->willReturn('tree1');

        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects($this->once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $module_service = $this->createMock(ModuleService::class);
        $module_service
            ->expects($this->once())
            ->method('findByComponent')
            ->with(ModuleListInterface::class, $tree, new GuestUser())
            ->willReturn(new Collection());

        $controller = new RedirectIndiListPhp(new GuestUser(), $module_service, $tree_service);

        $request = self::createRequest(HttpRequestMethod::GET->value, ['ged' => 'tree1']);

        $this->expectException(HttpGoneException::class);

        $controller->get($request);
    }

    public function testNoSuchTree(): void
    {
        $module_service = self::createStub(ModuleService::class);

        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects($this->once())
            ->method('all')
            ->willReturn(new Collection([]));

        $controller = new RedirectIndiListPhp(new GuestUser(), $module_service, $tree_service);

        $request = self::createRequest(HttpRequestMethod::GET->value, ['ged' => 'tree1']);

        $this->expectException(HttpGoneException::class);

        $controller->get($request);
    }
}

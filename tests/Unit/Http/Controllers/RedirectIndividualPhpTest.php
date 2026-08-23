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
use Fisharebest\Webtrees\Factories\IndividualFactory;
use Fisharebest\Webtrees\Http\Controllers\RedirectFamilyPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectIndividualPhp;
use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Fisharebest\Webtrees\Http\Exceptions\HttpGoneException;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RedirectIndividualPhp::class)]
class RedirectIndividualPhpTest extends TestCase
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

        $individual = self::createStub(Individual::class);
        $individual
            ->method('url')
            ->willReturn('https://www.example.com');

        $individual_factory = $this->createMock(IndividualFactory::class);
        $individual_factory
            ->expects($this->once())
            ->method('make')
            ->with('X123', $tree)
            ->willReturn($individual);

        Registry::individualFactory($individual_factory);

        $controller = new RedirectIndividualPhp($tree_service);

        $request = self::createRequest(HttpRequestMethod::GET->value, ['ged' => 'tree1', 'pid' => 'X123']);

        $response = $controller->get($request);

        self::assertSame(HttpStatusCode::MovedPermanently->value, $response->getStatusCode());
        self::assertSame('https://www.example.com', $response->getHeaderLine('Location'));
    }

    public function testNoSuchRecord(): void
    {
        $tree = self::createStub(Tree::class);

        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects($this->once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $controller = new RedirectIndividualPhp($tree_service);

        $request = self::createRequest(HttpRequestMethod::GET->value, ['ged' => 'tree1', 'pid' => 'X123']);

        $this->expectException(HttpGoneException::class);

        $controller->get($request);
    }

    public function testNoSuchTree(): void
    {
        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects($this->once())
            ->method('all')
            ->willReturn(new Collection([]));

        $controller = new RedirectIndividualPhp($tree_service);

        $request = self::createRequest(HttpRequestMethod::GET->value, ['ged' => 'tree1', 'pid' => 'X123']);

        $this->expectException(HttpGoneException::class);

        $controller->get($request);
    }

    public function testMissingXrefParameter(): void
    {
        $tree_service = self::createStub(TreeService::class);

        $controller = new RedirectFamilyPhp($tree_service);

        $request = self::createRequest(HttpRequestMethod::GET->value, ['ged' => 'tree1']);

        $this->expectException(HttpBadRequestException::class);

        $controller->get($request);
    }
}

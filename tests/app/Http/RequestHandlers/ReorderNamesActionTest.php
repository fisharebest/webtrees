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
use Fisharebest\Webtrees\Contracts\IndividualFactoryInterface;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ReorderNamesAction::class)]
class ReorderNamesActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(ReorderNamesAction::class));
    }

    public function testHandleReordersNamesAndRedirects(): void
    {
        $tree = $this->importTree('demo.ged');

        $name_fact1 = self::createStub(Fact::class);
        $name_fact1->method('id')->willReturn('name-1');
        $name_fact1->method('tag')->willReturn('INDI:NAME');
        $name_fact1->method('gedcom')->willReturn('1 NAME John /Doe/');

        $name_fact2 = self::createStub(Fact::class);
        $name_fact2->method('id')->willReturn('name-2');
        $name_fact2->method('tag')->willReturn('INDI:NAME');
        $name_fact2->method('gedcom')->willReturn('1 NAME Johann /Doe/');

        $other_fact = self::createStub(Fact::class);
        $other_fact->method('id')->willReturn('birt-1');
        $other_fact->method('tag')->willReturn('INDI:BIRT');
        $other_fact->method('gedcom')->willReturn('1 BIRT');

        $individual = $this->createMock(Individual::class);
        $individual->method('xref')->willReturn('X1');
        $individual->method('tree')->willReturn($tree);
        $individual->method('canEdit')->willReturn(true);
        $individual->method('canShow')->willReturn(true);
        $individual->method('url')->willReturn('https://webtrees.test/individual/X1');
        $individual->method('facts')->willReturn(new Collection([$name_fact1, $name_fact2, $other_fact]));
        $individual
            ->expects($this->once())
            ->method('updateRecord');

        $individual_factory = $this->createMock(IndividualFactoryInterface::class);
        $individual_factory
            ->expects($this->once())
            ->method('make')
            ->with('X1', $tree)
            ->willReturn($individual);

        Registry::individualFactory($individual_factory);

        $handler  = new ReorderNamesAction();
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            ['order' => ['name-2', 'name-1']],
            [],
            ['tree' => $tree, 'xref' => 'X1'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleWithUnknownIndividualThrowsNotFoundException(): void
    {
        $tree = $this->importTree('demo.ged');

        $individual_factory = $this->createMock(IndividualFactoryInterface::class);
        $individual_factory
            ->expects($this->once())
            ->method('make')
            ->with('X999', $tree)
            ->willReturn(null);

        Registry::individualFactory($individual_factory);

        $handler = new ReorderNamesAction();
        $request = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            ['order' => []],
            [],
            ['tree' => $tree, 'xref' => 'X999'],
        );

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }
}

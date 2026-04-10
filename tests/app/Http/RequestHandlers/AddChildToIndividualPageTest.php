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
use Fisharebest\Webtrees\Contracts\SurnameTraditionFactoryInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\SurnameTradition\SurnameTraditionInterface;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AddChildToIndividualPage::class)]
class AddChildToIndividualPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(AddChildToIndividualPage::class));
    }

    public function testHandleForMaleIndividual(): void
    {
        $tree = $this->importTree('demo.ged');

        $individual = self::createStub(Individual::class);
        $individual->method('xref')->willReturn('X1');
        $individual->method('tree')->willReturn($tree);
        $individual->method('canEdit')->willReturn(true);
        $individual->method('canShow')->willReturn(true);
        $individual->method('sex')->willReturn('M');
        $individual->method('fullName')->willReturn('John Smith');
        $individual->method('url')->willReturn('https://webtrees.test/individual/X1');

        $individual_factory = self::createStub(IndividualFactoryInterface::class);
        $individual_factory->method('make')->willReturn($individual);

        Registry::individualFactory($individual_factory);

        $surname_tradition = self::createStub(SurnameTraditionInterface::class);
        $surname_tradition->method('newChildNames')->willReturn(['1 NAME']);

        $surname_tradition_factory = self::createStub(SurnameTraditionFactoryInterface::class);
        $surname_tradition_factory->method('make')->willReturn($surname_tradition);

        Registry::surnameTraditionFactory($surname_tradition_factory);

        $gedcom_edit_service = $this->createMock(GedcomEditService::class);
        $gedcom_edit_service
            ->expects($this->once())
            ->method('newIndividualFacts')
            ->willReturn(new Collection());

        $handler  = new AddChildToIndividualPage($gedcom_edit_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X1'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleForFemaleIndividual(): void
    {
        $tree = $this->importTree('demo.ged');

        $individual = self::createStub(Individual::class);
        $individual->method('xref')->willReturn('X1');
        $individual->method('tree')->willReturn($tree);
        $individual->method('canEdit')->willReturn(true);
        $individual->method('canShow')->willReturn(true);
        $individual->method('sex')->willReturn('F');
        $individual->method('fullName')->willReturn('Jane Smith');
        $individual->method('url')->willReturn('https://webtrees.test/individual/X1');

        $individual_factory = self::createStub(IndividualFactoryInterface::class);
        $individual_factory->method('make')->willReturn($individual);

        Registry::individualFactory($individual_factory);

        $surname_tradition = self::createStub(SurnameTraditionInterface::class);
        $surname_tradition->method('newChildNames')->willReturn(['1 NAME']);

        $surname_tradition_factory = self::createStub(SurnameTraditionFactoryInterface::class);
        $surname_tradition_factory->method('make')->willReturn($surname_tradition);

        Registry::surnameTraditionFactory($surname_tradition_factory);

        $gedcom_edit_service = $this->createMock(GedcomEditService::class);
        $gedcom_edit_service
            ->expects($this->once())
            ->method('newIndividualFacts')
            ->willReturn(new Collection());

        $handler  = new AddChildToIndividualPage($gedcom_edit_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X1'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleWithUnknownIndividualThrowsNotFoundException(): void
    {
        $tree = $this->importTree('demo.ged');

        $individual_factory = self::createStub(IndividualFactoryInterface::class);
        $individual_factory->method('make')->willReturn(null);

        Registry::individualFactory($individual_factory);

        $gedcom_edit_service = self::createStub(GedcomEditService::class);

        $handler = new AddChildToIndividualPage($gedcom_edit_service);
        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X999'],
        );

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }
}

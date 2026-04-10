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

namespace Fisharebest\Webtrees\Module;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Factories\IndividualFactory;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CompactTreeChartModule::class)]
class CompactTreeChartModuleTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClassExists(): void
    {
        self::assertTrue(class_exists(CompactTreeChartModule::class));
    }

    public function testTitleIsNotEmpty(): void
    {
        $chart_service = self::createStub(ChartService::class);
        $module        = new CompactTreeChartModule($chart_service);

        self::assertNotEmpty($module->title());
    }

    public function testHandleReturnsOkResponseForValidIndividual(): void
    {
        $tree = $this->importTree('demo.ged');

        $individual = self::createStub(Individual::class);
        $individual->method('canShow')->willReturn(true);
        $individual->method('xref')->willReturn('X123');
        $individual->method('tree')->willReturn($tree);
        $individual->method('fullName')->willReturn('Test Person');

        // The view layout also calls the factory, so use atLeastOnce().
        $individual_factory = $this->createMock(IndividualFactory::class);
        $individual_factory->expects(self::atLeastOnce())
            ->method('make')
            ->willReturn($individual);
        Registry::individualFactory($individual_factory);

        $chart_service = self::createStub(ChartService::class);
        $module        = new CompactTreeChartModule($chart_service);

        $request = self::createRequest(RequestMethodInterface::METHOD_GET, [], [], [], [
            'tree' => $tree,
            'xref' => 'X123',
        ]);

        $response = $module->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleThrowsExceptionForNullIndividual(): void
    {
        $tree = $this->importTree('demo.ged');

        $individual_factory = $this->createMock(IndividualFactory::class);
        $individual_factory->expects(self::atLeastOnce())
            ->method('make')
            ->willReturn(null);
        Registry::individualFactory($individual_factory);

        $chart_service = self::createStub(ChartService::class);
        $module        = new CompactTreeChartModule($chart_service);

        $request = self::createRequest(RequestMethodInterface::METHOD_GET, [], [], [], [
            'tree' => $tree,
            'xref' => 'X999',
        ]);

        $this->expectException(HttpNotFoundException::class);

        $module->handle($request);
    }
}

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
use Fisharebest\Webtrees\Census\CensusOfUnitedStates1790;
use Fisharebest\Webtrees\Factories\CacheFactory;
use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CensusAssistantModule::class)]
class CensusAssistantModuleTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClassExists(): void
    {
        self::assertTrue(class_exists(CensusAssistantModule::class));
    }

    public function testPostCensusHeaderAcceptsOfferedCensus(): void
    {
        $request = self::createRequest(RequestMethodInterface::METHOD_POST)->withParsedBody([
            'census' => CensusOfUnitedStates1790::class,
        ]);

        $response = (new CensusAssistantModule())->postCensusHeaderAction($request);

        self::assertStringContainsString('wt-census-assistant-row', (string) $response->getBody());
    }

    public function testPostCensusHeaderRejectsNonCensusClass(): void
    {
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, params: [
            'census' => CacheFactory::class,
        ]);

        $this->expectException(HttpBadRequestException::class);

        (new CensusAssistantModule())->postCensusHeaderAction($request);
    }

    public function testPostCensusHeaderRejectsUnknownClass(): void
    {
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, params: [
            'census' => 'NonexistentCensus',
        ]);

        $this->expectException(HttpBadRequestException::class);

        (new CensusAssistantModule())->postCensusHeaderAction($request);
    }

    public function testPostCensusIndividualAcceptsOfferedCensus(): void
    {
        $tree = (new TreeService(new GedcomImportService()))->create('test-tree', 'Test tree');
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, params: [
            'census' => CensusOfUnitedStates1790::class,
        ], attributes: ['tree' => $tree]);

        $response = (new CensusAssistantModule())->postCensusIndividualAction($request);

        self::assertStringContainsString('wt-census-assistant-row', (string) $response->getBody());
    }

    public function testPostCensusIndividualRejectsNonCensusClass(): void
    {
        $tree = (new TreeService(new GedcomImportService()))->create('test-tree', 'Test tree');
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, params: [
            'census' => CacheFactory::class,
        ], attributes: ['tree' => $tree]);

        $this->expectException(HttpBadRequestException::class);

        (new CensusAssistantModule())->postCensusIndividualAction($request);
    }

    public function testUpdateCensusAssistantRejectsNonCensusClass(): void
    {
        $request = self::createRequest(RequestMethodInterface::METHOD_POST)->withParsedBody([
            'ca_title'       => '',
            'ca_place'       => '',
            'ca_citation'    => '',
            'ca_individuals' => ['xref' => []],
            'ca_notes'       => '',
            'ca_census'      => CacheFactory::class,
        ]);

        $this->expectException(HttpBadRequestException::class);

        (new CensusAssistantModule())->updateCensusAssistant($request, self::createStub(Individual::class), '', '', false);
    }
}

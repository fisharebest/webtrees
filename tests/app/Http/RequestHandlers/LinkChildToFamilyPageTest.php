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
use Fisharebest\Webtrees\Contracts\IndividualFactoryInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LinkChildToFamilyPage::class)]
class LinkChildToFamilyPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(LinkChildToFamilyPage::class));
    }

    public function testHandleReturnsOkForValidIndividual(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test', 'Test');

        $individual = self::createStub(Individual::class);
        $individual->method('xref')->willReturn('I1');
        $individual->method('tree')->willReturn($tree);
        $individual->method('canEdit')->willReturn(true);
        $individual->method('canShow')->willReturn(true);
        $individual->method('fullName')->willReturn('John Doe');
        $individual->method('url')->willReturn('https://webtrees.test/individual/I1');

        $individual_factory = self::createStub(IndividualFactoryInterface::class);
        $individual_factory
            ->method('make')
            ->willReturn($individual);

        Registry::individualFactory($individual_factory);

        $handler  = new LinkChildToFamilyPage();
        $request  = self::createRequest(
            attributes: ['tree' => $tree, 'xref' => 'I1'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleThrowsNotFoundForUnknownIndividual(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test2', 'Test 2');

        $individual_factory = self::createStub(IndividualFactoryInterface::class);
        $individual_factory
            ->method('make')
            ->willReturn(null);

        Registry::individualFactory($individual_factory);

        $handler = new LinkChildToFamilyPage();
        $request = self::createRequest(
            attributes: ['tree' => $tree, 'xref' => 'X999'],
        );

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }
}

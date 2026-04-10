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
use Fisharebest\Webtrees\Contracts\FamilyFactoryInterface;
use Fisharebest\Webtrees\Contracts\SurnameTraditionFactoryInterface;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\SurnameTradition\SurnameTraditionInterface;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AddSpouseToFamilyPage::class)]
class AddSpouseToFamilyPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(AddSpouseToFamilyPage::class));
    }

    public function testHandleReturnsAddWifePage(): void
    {
        $tree = $this->importTree('demo.ged');

        $spouse = self::createStub(Individual::class);

        $family = self::createStub(Family::class);
        $family->method('xref')->willReturn('F1');
        $family->method('tree')->willReturn($tree);
        $family->method('canEdit')->willReturn(true);
        $family->method('canShow')->willReturn(true);
        $family->method('fullName')->willReturn('Husband / Wife');
        $family->method('url')->willReturn('https://webtrees.test/family/F1');
        $family->method('spouses')->willReturn(new Collection([$spouse]));

        $family_factory = self::createStub(FamilyFactoryInterface::class);
        $family_factory->method('make')->willReturn($family);

        Registry::familyFactory($family_factory);

        $surname_tradition = self::createStub(SurnameTraditionInterface::class);
        $surname_tradition->method('newSpouseNames')->willReturn(['1 NAME']);

        $surname_tradition_factory = self::createStub(SurnameTraditionFactoryInterface::class);
        $surname_tradition_factory->method('make')->willReturn($surname_tradition);

        Registry::surnameTraditionFactory($surname_tradition_factory);

        $gedcom_edit_service = $this->createMock(GedcomEditService::class);
        $gedcom_edit_service
            ->expects($this->once())
            ->method('newIndividualFacts')
            ->willReturn(new Collection());
        $gedcom_edit_service
            ->expects($this->once())
            ->method('newFamilyFacts')
            ->willReturn(new Collection());

        $handler  = new AddSpouseToFamilyPage($gedcom_edit_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'F1', 'sex' => 'F'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleReturnsAddHusbandPage(): void
    {
        $tree = $this->importTree('demo.ged');

        $spouse = self::createStub(Individual::class);

        $family = self::createStub(Family::class);
        $family->method('xref')->willReturn('F1');
        $family->method('tree')->willReturn($tree);
        $family->method('canEdit')->willReturn(true);
        $family->method('canShow')->willReturn(true);
        $family->method('fullName')->willReturn('Husband / Wife');
        $family->method('url')->willReturn('https://webtrees.test/family/F1');
        $family->method('spouses')->willReturn(new Collection([$spouse]));

        $family_factory = self::createStub(FamilyFactoryInterface::class);
        $family_factory->method('make')->willReturn($family);

        Registry::familyFactory($family_factory);

        $surname_tradition = self::createStub(SurnameTraditionInterface::class);
        $surname_tradition->method('newSpouseNames')->willReturn(['1 NAME']);

        $surname_tradition_factory = self::createStub(SurnameTraditionFactoryInterface::class);
        $surname_tradition_factory->method('make')->willReturn($surname_tradition);

        Registry::surnameTraditionFactory($surname_tradition_factory);

        $gedcom_edit_service = $this->createMock(GedcomEditService::class);
        $gedcom_edit_service
            ->expects($this->once())
            ->method('newIndividualFacts')
            ->willReturn(new Collection());
        $gedcom_edit_service
            ->expects($this->once())
            ->method('newFamilyFacts')
            ->willReturn(new Collection());

        $handler  = new AddSpouseToFamilyPage($gedcom_edit_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'F1', 'sex' => 'M'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleWithNoSpousesUsesDefaultName(): void
    {
        $tree = $this->importTree('demo.ged');

        $family = self::createStub(Family::class);
        $family->method('xref')->willReturn('F1');
        $family->method('tree')->willReturn($tree);
        $family->method('canEdit')->willReturn(true);
        $family->method('canShow')->willReturn(true);
        $family->method('fullName')->willReturn('Family');
        $family->method('url')->willReturn('https://webtrees.test/family/F1');
        $family->method('spouses')->willReturn(new Collection());

        $family_factory = self::createStub(FamilyFactoryInterface::class);
        $family_factory->method('make')->willReturn($family);

        Registry::familyFactory($family_factory);

        $surname_tradition = self::createStub(SurnameTraditionInterface::class);
        $surname_tradition->method('defaultName')->willReturn('');

        $surname_tradition_factory = self::createStub(SurnameTraditionFactoryInterface::class);
        $surname_tradition_factory->method('make')->willReturn($surname_tradition);

        Registry::surnameTraditionFactory($surname_tradition_factory);

        $gedcom_edit_service = $this->createMock(GedcomEditService::class);
        $gedcom_edit_service
            ->expects($this->once())
            ->method('newIndividualFacts')
            ->willReturn(new Collection());
        $gedcom_edit_service
            ->expects($this->once())
            ->method('newFamilyFacts')
            ->willReturn(new Collection());

        $handler  = new AddSpouseToFamilyPage($gedcom_edit_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'F1', 'sex' => 'F'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleWithUnknownFamilyThrowsNotFoundException(): void
    {
        $tree = $this->importTree('demo.ged');

        $family_factory = self::createStub(FamilyFactoryInterface::class);
        $family_factory->method('make')->willReturn(null);

        Registry::familyFactory($family_factory);

        $gedcom_edit_service = self::createStub(GedcomEditService::class);

        $handler = new AddSpouseToFamilyPage($gedcom_edit_service);
        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X999', 'sex' => 'F'],
        );

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }
}

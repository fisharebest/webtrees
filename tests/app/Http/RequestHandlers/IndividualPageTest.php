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
use Fisharebest\Webtrees\Contracts\SlugFactoryInterface;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(IndividualPage::class)]
class IndividualPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(IndividualPage::class));
    }

    public function testHandleReturnsOkForVisibleIndividual(): void
    {
        $tree = $this->importTree('demo.ged');

        // Use real Date/Place objects to avoid TypeError in Age constructor.
        $birth_date  = new Date('');
        $death_date  = new Date('');
        $birth_place = new Place('', $tree);
        $death_place = new Place('', $tree);

        $individual = self::createStub(Individual::class);
        $individual->method('xref')->willReturn('I1');
        $individual->method('tree')->willReturn($tree);
        $individual->method('canShow')->willReturn(true);
        $individual->method('canEdit')->willReturn(false);
        $individual->method('fullName')->willReturn('John /Doe/');
        $individual->method('lifespan')->willReturn('(1900-1980)');
        $individual->method('url')->willReturn('https://webtrees.test/individual/I1');
        $individual->method('facts')->willReturn(new Collection());
        $individual->method('isDead')->willReturn(true);
        $individual->method('sex')->willReturn('M');
        $individual->method('sortName')->willReturn('DOE,JOHN');
        $individual->method('getBirthDate')->willReturn($birth_date);
        $individual->method('getDeathDate')->willReturn($death_date);
        $individual->method('getBirthPlace')->willReturn($birth_place);
        $individual->method('getDeathPlace')->willReturn($death_place);
        $individual->method('childFamilies')->willReturn(new Collection());
        $individual->method('spouseFamilies')->willReturn(new Collection());

        $individual_factory = self::createStub(IndividualFactoryInterface::class);
        $individual_factory->method('make')->willReturn($individual);

        Registry::individualFactory($individual_factory);

        $slug_factory = self::createStub(SlugFactoryInterface::class);
        $slug_factory->method('make')->willReturn('');

        Registry::slugFactory($slug_factory);

        $clipboard_service = $this->createMock(ClipboardService::class);
        $clipboard_service
            ->expects($this->once())
            ->method('pastableFacts')
            ->willReturn(new Collection());

        $module_service = $this->createMock(ModuleService::class);
        $module_service->method('findByComponent')->willReturn(new Collection());
        $module_service->method('findByInterface')->willReturn(new Collection());

        $user_service = self::createStub(UserService::class);

        $handler  = new IndividualPage($clipboard_service, $module_service, $user_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'I1', 'slug' => ''],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleRedirectsOnSlugMismatch(): void
    {
        $tree = $this->importTree('demo.ged');

        $individual = self::createStub(Individual::class);
        $individual->method('xref')->willReturn('I1');
        $individual->method('tree')->willReturn($tree);
        $individual->method('canShow')->willReturn(true);
        $individual->method('canEdit')->willReturn(false);
        $individual->method('url')->willReturn('https://webtrees.test/individual/I1/john-doe');

        $individual_factory = $this->createMock(IndividualFactoryInterface::class);
        $individual_factory
            ->expects($this->once())
            ->method('make')
            ->with('I1', $tree)
            ->willReturn($individual);

        Registry::individualFactory($individual_factory);

        $slug_factory = $this->createMock(SlugFactoryInterface::class);
        $slug_factory->method('make')->willReturn('john-doe');

        Registry::slugFactory($slug_factory);

        $clipboard_service = self::createStub(ClipboardService::class);
        $module_service = self::createStub(ModuleService::class);
        $user_service = self::createStub(UserService::class);

        $handler  = new IndividualPage($clipboard_service, $module_service, $user_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'I1', 'slug' => 'wrong-slug'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
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

        $clipboard_service = self::createStub(ClipboardService::class);
        $module_service = self::createStub(ModuleService::class);
        $user_service = self::createStub(UserService::class);

        $handler = new IndividualPage($clipboard_service, $module_service, $user_service);
        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X999', 'slug' => ''],
        );

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }
}

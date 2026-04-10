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
use Fisharebest\Webtrees\Contracts\SlugFactoryInterface;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FamilyPage::class)]
class FamilyPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(FamilyPage::class));
    }

    public function testHandleReturnsOkForVisibleFamily(): void
    {
        $tree = $this->importTree('demo.ged');

        $family = self::createStub(Family::class);
        $family->method('xref')->willReturn('F1');
        $family->method('tree')->willReturn($tree);
        $family->method('canShow')->willReturn(true);
        $family->method('canEdit')->willReturn(false);
        $family->method('fullName')->willReturn('Husband / Wife');
        $family->method('url')->willReturn('https://webtrees.test/family/F1');
        $family->method('facts')->willReturn(new Collection());
        $family->method('spouses')->willReturn(new Collection());
        $family->method('children')->willReturn(new Collection());

        $family_factory = self::createStub(FamilyFactoryInterface::class);
        $family_factory
            ->method('make')
            ->willReturn($family);

        Registry::familyFactory($family_factory);

        $slug_factory = self::createStub(SlugFactoryInterface::class);
        $slug_factory->method('make')->willReturn('');

        Registry::slugFactory($slug_factory);

        $clipboard_service = self::createStub(ClipboardService::class);
        $clipboard_service
            ->method('pastableFacts')
            ->willReturn(new Collection());

        $handler  = new FamilyPage($clipboard_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'F1', 'slug' => ''],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleRedirectsOnSlugMismatch(): void
    {
        $tree = $this->importTree('demo.ged');

        $family = self::createStub(Family::class);
        $family->method('xref')->willReturn('F1');
        $family->method('tree')->willReturn($tree);
        $family->method('canShow')->willReturn(true);
        $family->method('canEdit')->willReturn(false);
        $family->method('fullName')->willReturn('Husband / Wife');
        $family->method('url')->willReturn('https://webtrees.test/family/F1/husband-wife');

        $family_factory = self::createStub(FamilyFactoryInterface::class);
        $family_factory
            ->method('make')
            ->willReturn($family);

        Registry::familyFactory($family_factory);

        $slug_factory = self::createStub(SlugFactoryInterface::class);
        $slug_factory->method('make')->willReturn('husband-wife');

        Registry::slugFactory($slug_factory);

        $clipboard_service = self::createStub(ClipboardService::class);

        $handler  = new FamilyPage($clipboard_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'F1', 'slug' => 'wrong-slug'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
    }

    public function testHandleWithUnknownFamilyThrowsNotFoundException(): void
    {
        $tree = $this->importTree('demo.ged');

        $family_factory = self::createStub(FamilyFactoryInterface::class);
        $family_factory
            ->method('make')
            ->willReturn(null);

        Registry::familyFactory($family_factory);

        $clipboard_service = self::createStub(ClipboardService::class);

        $handler = new FamilyPage($clipboard_service);
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

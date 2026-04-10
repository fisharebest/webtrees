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
use Fisharebest\Webtrees\Contracts\MediaFactoryInterface;
use Fisharebest\Webtrees\Contracts\SlugFactoryInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MediaPage::class)]
class MediaPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(MediaPage::class));
    }

    public function testHandleReturnsOkForVisibleMedia(): void
    {
        $tree = $this->importTree('demo.ged');

        $media = self::createStub(Media::class);
        $media->method('xref')->willReturn('M1');
        $media->method('tree')->willReturn($tree);
        $media->method('canShow')->willReturn(true);
        $media->method('canEdit')->willReturn(false);
        $media->method('fullName')->willReturn('Test Media');
        $media->method('url')->willReturn('https://webtrees.test/media/M1');
        $media->method('facts')->willReturn(new Collection());
        $media->method('mediaFiles')->willReturn(new Collection());

        $media_factory = $this->createMock(MediaFactoryInterface::class);
        $media_factory
            ->expects($this->once())
            ->method('make')
            ->with('M1', $tree)
            ->willReturn($media);

        Registry::mediaFactory($media_factory);

        $slug_factory = $this->createMock(SlugFactoryInterface::class);
        $slug_factory->method('make')->willReturn('');

        Registry::slugFactory($slug_factory);

        $clipboard_service = $this->createMock(ClipboardService::class);
        $clipboard_service
            ->expects($this->once())
            ->method('pastableFacts')
            ->willReturn(new Collection());

        $linked_record_service = $this->createMock(LinkedRecordService::class);
        $linked_record_service->method('linkedFamilies')->willReturn(new Collection());
        $linked_record_service->method('linkedIndividuals')->willReturn(new Collection());
        $linked_record_service->method('linkedLocations')->willReturn(new Collection());
        $linked_record_service->method('linkedNotes')->willReturn(new Collection());
        $linked_record_service->method('linkedSources')->willReturn(new Collection());

        $handler  = new MediaPage($clipboard_service, $linked_record_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'M1', 'slug' => ''],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleRedirectsOnSlugMismatch(): void
    {
        $tree = $this->importTree('demo.ged');

        $media = self::createStub(Media::class);
        $media->method('xref')->willReturn('M1');
        $media->method('tree')->willReturn($tree);
        $media->method('canShow')->willReturn(true);
        $media->method('canEdit')->willReturn(false);
        $media->method('url')->willReturn('https://webtrees.test/media/M1/test-media');

        $media_factory = $this->createMock(MediaFactoryInterface::class);
        $media_factory
            ->expects($this->once())
            ->method('make')
            ->with('M1', $tree)
            ->willReturn($media);

        Registry::mediaFactory($media_factory);

        $slug_factory = $this->createMock(SlugFactoryInterface::class);
        $slug_factory->method('make')->willReturn('test-media');

        Registry::slugFactory($slug_factory);

        $clipboard_service = self::createStub(ClipboardService::class);
        $linked_record_service = self::createStub(LinkedRecordService::class);

        $handler  = new MediaPage($clipboard_service, $linked_record_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'M1', 'slug' => 'wrong-slug'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
    }

    public function testHandleWithUnknownMediaThrowsNotFoundException(): void
    {
        $tree = $this->importTree('demo.ged');

        $media_factory = $this->createMock(MediaFactoryInterface::class);
        $media_factory
            ->expects($this->once())
            ->method('make')
            ->with('X999', $tree)
            ->willReturn(null);

        Registry::mediaFactory($media_factory);

        $clipboard_service = self::createStub(ClipboardService::class);
        $linked_record_service = self::createStub(LinkedRecordService::class);

        $handler = new MediaPage($clipboard_service, $linked_record_service);
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

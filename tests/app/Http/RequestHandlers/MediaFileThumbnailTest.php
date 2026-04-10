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
use Fisharebest\Webtrees\Contracts\MediaFactoryInterface;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MediaFileThumbnail::class)]
class MediaFileThumbnailTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(MediaFileThumbnail::class));
    }

    public function testHandleReturnsNotFoundImageWhenMediaIsNull(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test', 'Test');

        $media_factory = $this->createMock(MediaFactoryInterface::class);
        $media_factory
            ->expects($this->once())
            ->method('make')
            ->with('X999', $tree)
            ->willReturn(null);

        Registry::mediaFactory($media_factory);

        $handler  = new MediaFileThumbnail();
        $request  = self::createRequest(
            query: ['xref' => 'X999', 'fact_id' => 'abc', 'w' => '100', 'h' => '100', 'fit' => 'contain'],
            attributes: ['tree' => $tree],
        );
        $response = $handler->handle($request);

        // When media is null, returns a replacement "not found" image
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleReturnsForbiddenImageWhenNotVisible(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test2', 'Test 2');

        $media = self::createStub(Media::class);
        $media->method('xref')->willReturn('M1');
        $media->method('tree')->willReturn($tree);
        $media->method('canShow')->willReturn(false);

        $media_factory = $this->createMock(MediaFactoryInterface::class);
        $media_factory
            ->expects($this->once())
            ->method('make')
            ->with('M1', $tree)
            ->willReturn($media);

        Registry::mediaFactory($media_factory);

        $handler  = new MediaFileThumbnail();
        $request  = self::createRequest(
            query: ['xref' => 'M1', 'fact_id' => 'abc', 'w' => '100', 'h' => '100', 'fit' => 'contain'],
            attributes: ['tree' => $tree],
        );
        $response = $handler->handle($request);

        // When canShow() is false, returns a replacement "forbidden" image
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}

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
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ReorderMediaFilesPage::class)]
class ReorderMediaFilesPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(ReorderMediaFilesPage::class));
    }

    public function testHandleReturnsOkForMediaWithMultipleFiles(): void
    {
        $tree = $this->importTree('demo.ged');

        $file1 = self::createStub(MediaFile::class);
        $file2 = self::createStub(MediaFile::class);

        $media = self::createStub(Media::class);
        $media->method('xref')->willReturn('M1');
        $media->method('tree')->willReturn($tree);
        $media->method('canEdit')->willReturn(true);
        $media->method('canShow')->willReturn(true);
        $media->method('fullName')->willReturn('Test Media');
        $media->method('url')->willReturn('https://webtrees.test/media/M1');
        $media->method('mediaFiles')->willReturn(new Collection([$file1, $file2]));

        $media_factory = $this->createMock(MediaFactoryInterface::class);
        $media_factory
            ->expects($this->once())
            ->method('make')
            ->with('M1', $tree)
            ->willReturn($media);

        Registry::mediaFactory($media_factory);

        $handler  = new ReorderMediaFilesPage();
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'M1'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleRedirectsForMediaWithSingleFile(): void
    {
        $tree = $this->importTree('demo.ged');

        $file1 = self::createStub(MediaFile::class);

        $media = self::createStub(Media::class);
        $media->method('xref')->willReturn('M1');
        $media->method('tree')->willReturn($tree);
        $media->method('canEdit')->willReturn(true);
        $media->method('canShow')->willReturn(true);
        $media->method('fullName')->willReturn('Test Media');
        $media->method('url')->willReturn('https://webtrees.test/media/M1');
        $media->method('mediaFiles')->willReturn(new Collection([$file1]));

        $media_factory = $this->createMock(MediaFactoryInterface::class);
        $media_factory
            ->expects($this->once())
            ->method('make')
            ->with('M1', $tree)
            ->willReturn($media);

        Registry::mediaFactory($media_factory);

        $handler  = new ReorderMediaFilesPage();
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'M1'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
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

        $handler = new ReorderMediaFilesPage();
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

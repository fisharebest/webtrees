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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ReorderMediaFilesAction::class)]
class ReorderMediaFilesActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(ReorderMediaFilesAction::class));
    }

    public function testHandleReordersMediaFilesAndRedirects(): void
    {
        $tree = $this->importTree('demo.ged');

        $file_fact1 = self::createStub(Fact::class);
        $file_fact1->method('id')->willReturn('file-1');
        $file_fact1->method('tag')->willReturn('OBJE:FILE');
        $file_fact1->method('gedcom')->willReturn('1 FILE photo.jpg');

        $file_fact2 = self::createStub(Fact::class);
        $file_fact2->method('id')->willReturn('file-2');
        $file_fact2->method('tag')->willReturn('OBJE:FILE');
        $file_fact2->method('gedcom')->willReturn('1 FILE scan.png');

        $other_fact = self::createStub(Fact::class);
        $other_fact->method('id')->willReturn('note-1');
        $other_fact->method('tag')->willReturn('OBJE:NOTE');
        $other_fact->method('gedcom')->willReturn('1 NOTE A note');

        $media = $this->createMock(Media::class);
        $media->method('xref')->willReturn('M1');
        $media->method('tree')->willReturn($tree);
        $media->method('canEdit')->willReturn(true);
        $media->method('canShow')->willReturn(true);
        $media->method('url')->willReturn('https://webtrees.test/media/M1');
        $media->method('facts')->willReturn(new Collection([$file_fact1, $file_fact2, $other_fact]));
        $media
            ->expects($this->once())
            ->method('updateRecord');

        $media_factory = $this->createMock(MediaFactoryInterface::class);
        $media_factory
            ->expects($this->once())
            ->method('make')
            ->with('M1', $tree)
            ->willReturn($media);

        Registry::mediaFactory($media_factory);

        $handler  = new ReorderMediaFilesAction();
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            ['order' => ['file-2', 'file-1']],
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

        $handler = new ReorderMediaFilesAction();
        $request = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            ['order' => []],
            [],
            ['tree' => $tree, 'xref' => 'X999'],
        );

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }
}

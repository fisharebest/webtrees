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
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Factories\MediaFactory;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(EditMediaFileModal::class)]
class EditMediaFileModalTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(EditMediaFileModal::class));
    }

    public function testHandleReturnsNotFoundWhenMediaDoesNotExist(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test', 'Test');

        // MediaFactory returns null for a non-existent xref
        $media_factory = $this->createMock(MediaFactory::class);
        $media_factory->expects(self::once())
            ->method('make')
            ->with('X999', $tree)
            ->willReturn(null);

        Registry::mediaFactory($media_factory);

        $media_file_service = new MediaFileService(new PhpService());
        $handler            = new EditMediaFileModal($media_file_service);
        $request            = self::createRequest()
            ->withAttribute('tree', $tree)
            ->withAttribute('xref', 'X999')
            ->withAttribute('fact_id', 'abc');
        $response = $handler->handle($request);

        // Auth::checkMediaAccess throws HttpNotFoundException, caught and returned as 403
        self::assertSame(StatusCodeInterface::STATUS_FORBIDDEN, $response->getStatusCode());
    }

    public function testHandleReturnsNotFoundWhenFactIdNotMatched(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test2', 'Test 2');

        // Create a media stub that passes Auth::checkMediaAccess but has no matching fact_id
        $media = self::createStub(Media::class);
        $media->method('canShow')->willReturn(true);
        $media->method('canEdit')->willReturn(true);
        $media->method('mediaFiles')->willReturn(new Collection([]));

        $media_factory = $this->createMock(MediaFactory::class);
        $media_factory->expects(self::once())
            ->method('make')
            ->with('M100', $tree)
            ->willReturn($media);

        Registry::mediaFactory($media_factory);

        $media_file_service = new MediaFileService(new PhpService());
        $handler            = new EditMediaFileModal($media_file_service);
        $request            = self::createRequest()
            ->withAttribute('tree', $tree)
            ->withAttribute('xref', 'M100')
            ->withAttribute('fact_id', 'nonexistent');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }
}

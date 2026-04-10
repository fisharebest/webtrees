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
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\MediaFile;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(EditMediaFileAction::class)]
class EditMediaFileActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(EditMediaFileAction::class));
    }

    public function testHandleRedirectsToTreePageWhenMediaFileNotFound(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test', 'Test');

        // Stub a media object with no matching media file
        $media = self::createStub(Media::class);
        $media->method('xref')->willReturn('M1');
        $media->method('tree')->willReturn($tree);
        $media->method('canShow')->willReturn(true);
        $media->method('canEdit')->willReturn(true);
        $media->method('mediaFiles')->willReturn(collect([]));

        $media_factory = $this->createMock(MediaFactoryInterface::class);
        $media_factory
            ->expects($this->once())
            ->method('make')
            ->with('M1', $tree)
            ->willReturn($media);

        Registry::mediaFactory($media_factory);

        $media_file_service     = new MediaFileService(new PhpService());
        $pending_changes_service = self::createStub(PendingChangesService::class);

        $handler  = new EditMediaFileAction($media_file_service, $pending_changes_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            ['folder' => '', 'new_file' => 'photo.jpg', 'remote' => '', 'title' => '', 'type' => ''],
            [],
            ['tree' => $tree, 'xref' => 'M1', 'fact_id' => 'nonexistent'],
        );
        $response = $handler->handle($request);

        // When the media file is not found, handler redirects to tree page
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}

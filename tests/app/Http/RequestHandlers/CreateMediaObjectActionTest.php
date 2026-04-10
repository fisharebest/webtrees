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
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreateMediaObjectAction::class)]
class CreateMediaObjectActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(CreateMediaObjectAction::class));
    }

    public function testHandleReturnsErrorWhenUploadFails(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test', 'Test');

        // uploadFile returns empty string when no file was uploaded
        $media_file_service = $this->createMock(MediaFileService::class);
        $media_file_service
            ->expects($this->once())
            ->method('uploadFile')
            ->willReturn('');

        $pending_changes_service = self::createStub(PendingChangesService::class);

        $handler  = new CreateMediaObjectAction($media_file_service, $pending_changes_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            ['media-note' => '', 'title' => 'Test', 'type' => '', 'restriction' => ''],
            [],
            ['tree' => $tree],
        );
        $response = $handler->handle($request);

        // Returns 406 NOT_ACCEPTABLE when upload file is empty
        self::assertSame(StatusCodeInterface::STATUS_NOT_ACCEPTABLE, $response->getStatusCode());
    }
}

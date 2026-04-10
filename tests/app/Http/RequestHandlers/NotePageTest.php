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
use Fisharebest\Webtrees\Contracts\NoteFactoryInterface;
use Fisharebest\Webtrees\Contracts\SlugFactoryInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(NotePage::class)]
class NotePageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(NotePage::class));
    }

    public function testHandleReturnsOkForVisibleNote(): void
    {
        $tree = $this->importTree('demo.ged');

        $note = self::createStub(Note::class);
        $note->method('xref')->willReturn('N1');
        $note->method('tree')->willReturn($tree);
        $note->method('canShow')->willReturn(true);
        $note->method('canEdit')->willReturn(false);
        $note->method('fullName')->willReturn('Test Note');
        $note->method('url')->willReturn('https://webtrees.test/note/N1');
        $note->method('facts')->willReturn(new Collection());

        $note_factory = $this->createMock(NoteFactoryInterface::class);
        $note_factory
            ->expects($this->once())
            ->method('make')
            ->with('N1', $tree)
            ->willReturn($note);

        Registry::noteFactory($note_factory);

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
        $linked_record_service->method('linkedMedia')->willReturn(new Collection());
        $linked_record_service->method('linkedRepositories')->willReturn(new Collection());
        $linked_record_service->method('linkedSources')->willReturn(new Collection());
        $linked_record_service->method('linkedSubmitters')->willReturn(new Collection());

        $handler  = new NotePage($clipboard_service, $linked_record_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'N1', 'slug' => ''],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleRedirectsOnSlugMismatch(): void
    {
        $tree = $this->importTree('demo.ged');

        $note = self::createStub(Note::class);
        $note->method('xref')->willReturn('N1');
        $note->method('tree')->willReturn($tree);
        $note->method('canShow')->willReturn(true);
        $note->method('canEdit')->willReturn(false);
        $note->method('url')->willReturn('https://webtrees.test/note/N1/test-note');

        $note_factory = $this->createMock(NoteFactoryInterface::class);
        $note_factory
            ->expects($this->once())
            ->method('make')
            ->with('N1', $tree)
            ->willReturn($note);

        Registry::noteFactory($note_factory);

        $slug_factory = $this->createMock(SlugFactoryInterface::class);
        $slug_factory->method('make')->willReturn('test-note');

        Registry::slugFactory($slug_factory);

        $clipboard_service = self::createStub(ClipboardService::class);
        $linked_record_service = self::createStub(LinkedRecordService::class);

        $handler  = new NotePage($clipboard_service, $linked_record_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'N1', 'slug' => 'wrong-slug'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
    }

    public function testHandleWithUnknownNoteThrowsNotFoundException(): void
    {
        $tree = $this->importTree('demo.ged');

        $note_factory = $this->createMock(NoteFactoryInterface::class);
        $note_factory
            ->expects($this->once())
            ->method('make')
            ->with('X999', $tree)
            ->willReturn(null);

        Registry::noteFactory($note_factory);

        $clipboard_service = self::createStub(ClipboardService::class);
        $linked_record_service = self::createStub(LinkedRecordService::class);

        $handler = new NotePage($clipboard_service, $linked_record_service);
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

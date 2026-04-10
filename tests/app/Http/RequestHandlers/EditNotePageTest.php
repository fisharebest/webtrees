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
use Fisharebest\Webtrees\Contracts\NoteFactoryInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(EditNotePage::class)]
class EditNotePageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(EditNotePage::class));
    }

    public function testHandleReturnsOkForValidNote(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test', 'Test');

        $note = self::createStub(Note::class);
        $note->method('xref')->willReturn('N1');
        $note->method('tree')->willReturn($tree);
        $note->method('canShow')->willReturn(true);
        $note->method('canEdit')->willReturn(true);
        $note->method('fullName')->willReturn('Test Note');
        $note->method('url')->willReturn('https://webtrees.test/note/N1');

        $note_factory = $this->createMock(NoteFactoryInterface::class);
        $note_factory
            ->expects($this->once())
            ->method('make')
            ->with('N1', $tree)
            ->willReturn($note);

        Registry::noteFactory($note_factory);

        $handler  = new EditNotePage();
        $request  = self::createRequest(
            attributes: ['tree' => $tree, 'xref' => 'N1'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleThrowsNotFoundForUnknownNote(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test2', 'Test 2');

        $note_factory = $this->createMock(NoteFactoryInterface::class);
        $note_factory
            ->expects($this->once())
            ->method('make')
            ->with('X999', $tree)
            ->willReturn(null);

        Registry::noteFactory($note_factory);

        $handler = new EditNotePage();
        $request = self::createRequest(
            attributes: ['tree' => $tree, 'xref' => 'X999'],
        );

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }
}

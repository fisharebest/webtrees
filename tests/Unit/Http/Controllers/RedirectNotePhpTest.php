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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Controllers;

use Fisharebest\Webtrees\Enums\HttpRequestMethod;
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Factories\NoteFactory;
use Fisharebest\Webtrees\Http\Controllers\RedirectNotePhp;
use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Fisharebest\Webtrees\Http\Exceptions\HttpGoneException;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RedirectNotePhp::class)]
class RedirectNotePhpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::createDatabase();
    }

    public function testRedirect(): void
    {
        $tree = self::createStub(Tree::class);
        $tree
            ->method('name')
            ->willReturn('tree1');

        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects($this->once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $note = self::createStub(Note::class);
        $note
            ->method('url')
            ->willReturn('https://www.example.com');

        $note_factory = $this->createMock(NoteFactory::class);
        $note_factory
            ->expects($this->once())
            ->method('make')
            ->with('X123', $tree)
            ->willReturn($note);

        Registry::noteFactory($note_factory);

        $controller = new RedirectNotePhp($tree_service);

        $request = self::createRequest(HttpRequestMethod::GET->value, ['ged' => 'tree1', 'nid' => 'X123']);

        $response = $controller->get($request);

        self::assertSame(HttpStatusCode::MovedPermanently->value, $response->getStatusCode());
        self::assertSame('https://www.example.com', $response->getHeaderLine('Location'));
    }

    public function testNoSuchRecord(): void
    {
        $tree = self::createStub(Tree::class);

        $tree_service = $this->createMock(TreeService::class);
        $tree_service
            ->expects($this->once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $controller = new RedirectNotePhp($tree_service);

        $request = self::createRequest(HttpRequestMethod::GET->value, ['ged' => 'tree1', 'nid' => 'X123']);

        $this->expectException(HttpGoneException::class);

        $controller->get($request);
    }

    public function testMissingXrefParameter(): void
    {
        $tree_service = self::createStub(TreeService::class);

        $controller = new RedirectNotePhp($tree_service);

        $request = self::createRequest(HttpRequestMethod::GET->value, ['ged' => 'tree1']);

        $this->expectException(HttpBadRequestException::class);

        $controller->get($request);
    }
}

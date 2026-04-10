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
use Fisharebest\Webtrees\Contracts\SlugFactoryInterface;
use Fisharebest\Webtrees\Contracts\SubmitterFactoryInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SubmitterPage::class)]
class SubmitterPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(SubmitterPage::class));
    }

    public function testHandleReturnsOkForVisibleSubmitter(): void
    {
        $tree = $this->importTree('demo.ged');

        $submitter = self::createStub(Submitter::class);
        $submitter->method('xref')->willReturn('U1');
        $submitter->method('tree')->willReturn($tree);
        $submitter->method('canShow')->willReturn(true);
        $submitter->method('canEdit')->willReturn(false);
        $submitter->method('fullName')->willReturn('Test Submitter');
        $submitter->method('url')->willReturn('https://webtrees.test/submitter/U1');
        $submitter->method('facts')->willReturn(new Collection());

        $submitter_factory = $this->createMock(SubmitterFactoryInterface::class);
        $submitter_factory
            ->expects($this->once())
            ->method('make')
            ->with('U1', $tree)
            ->willReturn($submitter);

        Registry::submitterFactory($submitter_factory);

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

        $handler  = new SubmitterPage($clipboard_service, $linked_record_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'U1', 'slug' => ''],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleRedirectsOnSlugMismatch(): void
    {
        $tree = $this->importTree('demo.ged');

        $submitter = self::createStub(Submitter::class);
        $submitter->method('xref')->willReturn('U1');
        $submitter->method('tree')->willReturn($tree);
        $submitter->method('canShow')->willReturn(true);
        $submitter->method('canEdit')->willReturn(false);
        $submitter->method('url')->willReturn('https://webtrees.test/submitter/U1/test-submitter');

        $submitter_factory = $this->createMock(SubmitterFactoryInterface::class);
        $submitter_factory
            ->expects($this->once())
            ->method('make')
            ->with('U1', $tree)
            ->willReturn($submitter);

        Registry::submitterFactory($submitter_factory);

        $slug_factory = $this->createMock(SlugFactoryInterface::class);
        $slug_factory->method('make')->willReturn('test-submitter');

        Registry::slugFactory($slug_factory);

        $clipboard_service = self::createStub(ClipboardService::class);
        $linked_record_service = self::createStub(LinkedRecordService::class);

        $handler  = new SubmitterPage($clipboard_service, $linked_record_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'U1', 'slug' => 'wrong-slug'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
    }

    public function testHandleWithUnknownSubmitterThrowsNotFoundException(): void
    {
        $tree = $this->importTree('demo.ged');

        $submitter_factory = $this->createMock(SubmitterFactoryInterface::class);
        $submitter_factory
            ->expects($this->once())
            ->method('make')
            ->with('X999', $tree)
            ->willReturn(null);

        Registry::submitterFactory($submitter_factory);

        $clipboard_service = self::createStub(ClipboardService::class);
        $linked_record_service = self::createStub(LinkedRecordService::class);

        $handler = new SubmitterPage($clipboard_service, $linked_record_service);
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

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
use Fisharebest\Webtrees\Contracts\RepositoryFactoryInterface;
use Fisharebest\Webtrees\Contracts\SlugFactoryInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RepositoryPage::class)]
class RepositoryPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(RepositoryPage::class));
    }

    public function testHandleReturnsOkForVisibleRepository(): void
    {
        $tree = $this->importTree('demo.ged');

        $repository = self::createStub(Repository::class);
        $repository->method('xref')->willReturn('R1');
        $repository->method('tree')->willReturn($tree);
        $repository->method('canShow')->willReturn(true);
        $repository->method('canEdit')->willReturn(false);
        $repository->method('fullName')->willReturn('Test Repository');
        $repository->method('url')->willReturn('https://webtrees.test/repository/R1');
        $repository->method('facts')->willReturn(new Collection());

        $repository_factory = $this->createMock(RepositoryFactoryInterface::class);
        $repository_factory
            ->expects($this->once())
            ->method('make')
            ->with('R1', $tree)
            ->willReturn($repository);

        Registry::repositoryFactory($repository_factory);

        $slug_factory = $this->createMock(SlugFactoryInterface::class);
        $slug_factory->method('make')->willReturn('');

        Registry::slugFactory($slug_factory);

        $clipboard_service = $this->createMock(ClipboardService::class);
        $clipboard_service
            ->expects($this->once())
            ->method('pastableFacts')
            ->willReturn(new Collection());

        $linked_record_service = $this->createMock(LinkedRecordService::class);
        $linked_record_service
            ->expects($this->once())
            ->method('linkedSources')
            ->willReturn(new Collection());

        $handler  = new RepositoryPage($clipboard_service, $linked_record_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'R1', 'slug' => ''],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleRedirectsOnSlugMismatch(): void
    {
        $tree = $this->importTree('demo.ged');

        $repository = self::createStub(Repository::class);
        $repository->method('xref')->willReturn('R1');
        $repository->method('tree')->willReturn($tree);
        $repository->method('canShow')->willReturn(true);
        $repository->method('canEdit')->willReturn(false);
        $repository->method('url')->willReturn('https://webtrees.test/repository/R1/test-repo');

        $repository_factory = $this->createMock(RepositoryFactoryInterface::class);
        $repository_factory
            ->expects($this->once())
            ->method('make')
            ->with('R1', $tree)
            ->willReturn($repository);

        Registry::repositoryFactory($repository_factory);

        $slug_factory = $this->createMock(SlugFactoryInterface::class);
        $slug_factory->method('make')->willReturn('test-repo');

        Registry::slugFactory($slug_factory);

        $clipboard_service = self::createStub(ClipboardService::class);
        $linked_record_service = self::createStub(LinkedRecordService::class);

        $handler  = new RepositoryPage($clipboard_service, $linked_record_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'R1', 'slug' => 'wrong-slug'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
    }

    public function testHandleWithUnknownRepositoryThrowsNotFoundException(): void
    {
        $tree = $this->importTree('demo.ged');

        $repository_factory = $this->createMock(RepositoryFactoryInterface::class);
        $repository_factory
            ->expects($this->once())
            ->method('make')
            ->with('X999', $tree)
            ->willReturn(null);

        Registry::repositoryFactory($repository_factory);

        $clipboard_service = self::createStub(ClipboardService::class);
        $linked_record_service = self::createStub(LinkedRecordService::class);

        $handler = new RepositoryPage($clipboard_service, $linked_record_service);
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

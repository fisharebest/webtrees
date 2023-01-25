<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Factories\SourceFactory;
use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\RedirectSourcePhp
 */
class RedirectSourcePhpTest extends TestCase
{
    /**
     * @return void
     */
    public function testRedirect(): void
    {
        $tree = $this->createStub(Tree::class);
        $tree
            ->method('name')
            ->willReturn('tree1');

        $tree_service = $this->createStub(TreeService::class);
        $tree_service
            ->expects(self::once())
            ->method('all')
            ->willReturn(new Collection(['tree1' => $tree]));

        $source = $this->createStub(Source::class);
        $source
            ->method('url')
            ->willReturn('https://www.example.com');

        $source_factory = $this->createStub(SourceFactory::class);
        $source_factory
            ->expects(self::once())
            ->method('make')
            ->with('X123', $tree)
            ->willReturn($source);

        Registry::sourceFactory($source_factory);

        $handler = new RedirectSourcePhp($tree_service);

        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            ['ged' => 'tree1', 'sid' => 'X123']
        );

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
        self::assertSame('https://www.example.com', $response->getHeaderLine('Location'));
    }

    /**
     * @return void
     */
    public function testNoSuchRecord(): void
    {
        $tree = $this->createStub(Tree::class);

        $tree_service = $this->createStub(TreeService::class);
        $tree_service
            ->expects(self::once())
            ->method('all')
            ->willReturn(new Collection([$tree]));

        $handler = new RedirectSourcePhp($tree_service);

        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            ['ged' => 'tree1', 'sid' => 'X123']
        );

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }

    /**
     * @return void
     */
    public function testMissingTreeParameter(): void
    {
        $tree_service = $this->createStub(TreeService::class);

        $handler = new RedirectSourcePhp($tree_service);

        $request = self::createRequest(RequestMethodInterface::METHOD_GET, ['sid' => 'X123']);

        $this->expectException(HttpBadRequestException::class);

        $handler->handle($request);
    }

    /**
     * @return void
     */
    public function testMissingXrefParameter(): void
    {
        $tree_service = $this->createStub(TreeService::class);

        $handler = new RedirectSourcePhp($tree_service);

        $request = self::createRequest(RequestMethodInterface::METHOD_GET, ['ged' => 'tree1']);

        $this->expectException(HttpBadRequestException::class);

        $handler->handle($request);
    }
}

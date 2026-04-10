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
use Fisharebest\Webtrees\Contracts\FamilyFactoryInterface;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ReorderChildrenPage::class)]
class ReorderChildrenPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(ReorderChildrenPage::class));
    }

    public function testHandleReturnsReorderPage(): void
    {
        $tree = $this->importTree('demo.ged');

        $family = self::createStub(Family::class);
        $family->method('xref')->willReturn('F1');
        $family->method('tree')->willReturn($tree);
        $family->method('canEdit')->willReturn(true);
        $family->method('canShow')->willReturn(true);
        $family->method('fullName')->willReturn('Husband / Wife');
        $family->method('url')->willReturn('https://webtrees.test/family/F1');

        $family_factory = self::createStub(FamilyFactoryInterface::class);
        $family_factory
            ->method('make')
            ->willReturn($family);

        Registry::familyFactory($family_factory);

        $handler  = new ReorderChildrenPage();
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'F1'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleWithUnknownFamilyThrowsNotFoundException(): void
    {
        $tree = $this->importTree('demo.ged');

        $family_factory = self::createStub(FamilyFactoryInterface::class);
        $family_factory
            ->method('make')
            ->willReturn(null);

        Registry::familyFactory($family_factory);

        $handler = new ReorderChildrenPage();
        $request = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [],
            [],
            [],
            ['tree' => $tree, 'xref' => 'X999'],
        );

        $this->expectException(HttpNotFoundException::class);

        $handler->handle($request);
    }
}

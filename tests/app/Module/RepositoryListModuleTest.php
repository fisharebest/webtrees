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

namespace Fisharebest\Webtrees\Module;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RepositoryListModule::class)]
class RepositoryListModuleTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(RepositoryListModule::class));
    }

    public function testTitleIsNotEmpty(): void
    {
        $module = new RepositoryListModule();

        self::assertNotEmpty($module->title());
    }

    public function testHandleReturnsOkResponseWhenAccessGranted(): void
    {
        $tree   = $this->importTree('demo.ged');
        $module = new RepositoryListModule();
        $module->setName('repository_list');

        // Grant public access for this module (default is PRIV_USER).
        DB::table('module_privacy')->insert([
            'module_name'  => 'repository_list',
            'gedcom_id'    => $tree->id(),
            'interface'    => ModuleListInterface::class,
            'access_level' => Auth::PRIV_PRIVATE,
        ]);

        $request = self::createRequest(RequestMethodInterface::METHOD_GET, [], [], [], [
            'tree' => $tree,
        ]);

        $response = $module->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testHandleDeniesAccessToGuest(): void
    {
        $tree   = $this->importTree('demo.ged');
        $module = new RepositoryListModule();
        $module->setName('repository_list');

        $request = self::createRequest(RequestMethodInterface::METHOD_GET, [], [], [], [
            'tree' => $tree,
        ]);

        $this->expectException(HttpAccessDeniedException::class);

        $module->handle($request);
    }
}

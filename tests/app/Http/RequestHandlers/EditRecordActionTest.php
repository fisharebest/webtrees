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
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(EditRecordAction::class)]
class EditRecordActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(EditRecordAction::class));
    }

    public function testHandleUpdatesRecordAndRedirects(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('test', 'Test');

        // checkRecordAccess($record, true) requires edit permission.
        $user = (new UserService())->create('editor', 'Editor', 'editor@example.com', 'secret');
        $tree->setUserPreference($user, UserInterface::PREF_TREE_ROLE, UserInterface::ROLE_MANAGER);
        Auth::login($user);

        $gedcom_import_service->importRecord("0 @S1@ SOUR\n1 TITL Test Source", $tree, false);

        $gedcom_edit_service = $this->createMock(GedcomEditService::class);
        $gedcom_edit_service
            ->expects($this->once())
            ->method('editLinesToGedcom')
            ->willReturn("\n1 TITL Updated Source");

        $handler  = new EditRecordAction($gedcom_edit_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_POST,
            [],
            ['levels' => ['1'], 'tags' => ['TITL'], 'values' => ['Updated Source']],
            [],
            ['tree' => $tree, 'xref' => 'S1'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}

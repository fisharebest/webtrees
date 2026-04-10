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
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AddSpouseToFamilyAction::class)]
class AddSpouseToFamilyActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(AddSpouseToFamilyAction::class));
    }

    /**
     * When the family XREF does not exist, Auth::checkFamilyAccess(null) throws HttpNotFoundException.
     */
    public function testHandleThrowsNotFoundForMissingFamily(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');

        $gedcom_edit_service = $this->createMock(GedcomEditService::class);
        $gedcom_edit_service->expects(self::never())->method('editLinesToGedcom');

        $handler = new AddSpouseToFamilyAction($gedcom_edit_service);
        $request = self::createRequest(
            method: RequestMethodInterface::METHOD_POST,
            params: [
                'ilevels' => [], 'itags' => [], 'ivalues' => [],
                'flevels' => [], 'ftags' => [], 'fvalues' => [],
            ],
            attributes: ['tree' => $tree, 'xref' => 'X_NONEXISTENT'],
        );

        $this->expectException(HttpNotFoundException::class);
        $handler->handle($request);
    }
}

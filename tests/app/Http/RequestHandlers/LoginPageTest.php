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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\User;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\LoginPage
 */
class LoginPageTest extends TestCase
{
    /**
     * @return void
     */
    public function testLoginPage(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $request               = self::createRequest();
        $handler               = new LoginPage($tree_service);
        $response              = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testLoginPageAlreadyLoggedIn(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $user                  = $this->createMock(User::class);
        $request               = self::createRequest()->withAttribute('user', $user);
        $handler               = new LoginPage($tree_service);
        $response              = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}

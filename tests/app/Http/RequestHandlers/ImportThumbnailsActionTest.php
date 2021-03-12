<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;

/**
 * Test ImportThumbnailsAction class.
 *
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\ImportThumbnailsAction
 */
class ImportThumbnailsActionTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testWebtrees1ThumbnailsAction(): void
    {
        $tree_service            = new TreeService();
        $pending_changes_service = new PendingChangesService();
        $handler                 = new ImportThumbnailsAction($pending_changes_service, $tree_service);
        $request                 = self::createRequest()
            ->withParsedBody(['thumbnail' => 'foo', 'action' => '', 'xref' => [], 'ged' => []]);
        $response                = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}

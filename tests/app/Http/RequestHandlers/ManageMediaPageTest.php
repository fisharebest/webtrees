<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\TestCase;

/**
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\ManageMediaPage
 */
class ManageMediaPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testIndex(): void
    {
        $media_file_service = new MediaFileService();
        $handler            = new ManageMediaPage($media_file_service);
        $request            = self::createRequest();
        $response           = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}

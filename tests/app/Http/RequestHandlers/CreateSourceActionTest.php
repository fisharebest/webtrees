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
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreateSourceAction::class)]
class CreateSourceActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(CreateSourceAction::class));
    }

    /**
     * Creating a source with mandatory title returns STATUS_OK JSON.
     */
    public function testHandleCreatesSourceWithTitleOnly(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');

        $user_service = new UserService();
        $user         = $user_service->create('testuser', 'Test User', 'test@example.com', 'secret');
        Auth::login($user);

        $handler  = new CreateSourceAction();
        $request  = self::createRequest(
            method: RequestMethodInterface::METHOD_POST,
            params: [
                'source-title'        => 'Census 1901',
                'source-abbreviation' => '',
                'source-author'       => '',
                'source-publication'  => '',
                'source-repository'   => '',
                'source-call-number'  => '',
                'source-text'         => '',
                'restriction'         => '',
            ],
            attributes: ['tree' => $tree],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $body = (string) $response->getBody();
        self::assertStringContainsString('"value":', $body);
        self::assertStringContainsString('"html":', $body);
    }

    /**
     * Creating a source with all optional fields returns STATUS_OK JSON.
     */
    public function testHandleCreatesSourceWithAllFields(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');

        $user_service = new UserService();
        $user         = $user_service->create('testuser2', 'Test User', 'test2@example.com', 'secret');
        Auth::login($user);

        $handler  = new CreateSourceAction();
        $request  = self::createRequest(
            method: RequestMethodInterface::METHOD_POST,
            params: [
                'source-title'        => 'Census 1901',
                'source-abbreviation' => 'C1901',
                'source-author'       => 'Census Office',
                'source-publication'  => 'HMSO',
                'source-repository'   => '',
                'source-call-number'  => '',
                'source-text'         => 'Full census text',
                'restriction'         => 'confidential',
            ],
            attributes: ['tree' => $tree],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}

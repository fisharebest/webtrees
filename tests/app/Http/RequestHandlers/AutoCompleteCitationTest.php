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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

// AutoCompleteCitation uses DB queries internally (not SearchService) and requires
// a real tree + source record.  We therefore need the database layer.
#[CoversClass(AutoCompleteCitation::class)]
class AutoCompleteCitationTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(AutoCompleteCitation::class));
    }

    /**
     * A citation search with a valid source and query returns STATUS_OK JSON.
     */
    public function testHandleReturnsJsonResponse(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $search_service        = new SearchService($tree_service);

        // Default SOUR privacy is 'privacy' (members only) — log in as member.
        $user = (new UserService())->create('test', 'Test', 'test@example.com', 'secret');
        $tree->setUserPreference($user, UserInterface::PREF_TREE_ROLE, UserInterface::ROLE_MEMBER);
        Auth::login($user);

        // Import a source and an individual citing that source with a PAGE value.
        $gedcom_import_service->importRecord(
            "0 @S1@ SOUR\n1 TITL Test Source",
            $tree,
            false,
        );
        $gedcom_import_service->importRecord(
            "0 @I1@ INDI\n1 NAME John /Doe/\n1 SOUR @S1@\n2 PAGE Page 42",
            $tree,
            false,
        );

        $handler  = new AutoCompleteCitation($search_service);
        $request  = self::createRequest(
            query: ['query' => 'Page', 'extra' => 'S1'],
            attributes: ['tree' => $tree],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertStringContainsString('application/json', $response->getHeaderLine('content-type'));
    }

    /**
     * The response must include a cache-control header.
     */
    public function testResponseIncludesCacheHeader(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $search_service        = new SearchService($tree_service);

        // Default SOUR privacy is 'privacy' (members only) — log in as member.
        $user = (new UserService())->create('test2', 'Test', 'test2@example.com', 'secret');
        $tree->setUserPreference($user, UserInterface::PREF_TREE_ROLE, UserInterface::ROLE_MEMBER);
        Auth::login($user);

        $gedcom_import_service->importRecord(
            "0 @S1@ SOUR\n1 TITL Test Source",
            $tree,
            false,
        );

        $handler  = new AutoCompleteCitation($search_service);
        $request  = self::createRequest(
            query: ['query' => 'anything', 'extra' => 'S1'],
            attributes: ['tree' => $tree],
        );
        $response = $handler->handle($request);

        self::assertNotEmpty($response->getHeaderLine('cache-control'));
    }

    /**
     * When no records cite the source, the result set is an empty JSON array.
     */
    public function testEmptyResultForUnmatchedQuery(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $search_service        = new SearchService($tree_service);

        // Default SOUR privacy is 'privacy' (members only) — log in as member.
        $user = (new UserService())->create('test3', 'Test', 'test3@example.com', 'secret');
        $tree->setUserPreference($user, UserInterface::PREF_TREE_ROLE, UserInterface::ROLE_MEMBER);
        Auth::login($user);

        $gedcom_import_service->importRecord(
            "0 @S1@ SOUR\n1 TITL Test Source",
            $tree,
            false,
        );

        $handler  = new AutoCompleteCitation($search_service);
        $request  = self::createRequest(
            query: ['query' => 'nonexistent-citation-text', 'extra' => 'S1'],
            attributes: ['tree' => $tree],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $body = (string) $response->getBody();
        self::assertSame('[]', $body);
    }
}

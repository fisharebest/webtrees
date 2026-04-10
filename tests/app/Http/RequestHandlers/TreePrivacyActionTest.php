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
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TreePrivacyAction::class)]
class TreePrivacyActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(TreePrivacyAction::class));
    }

    public function testHandleRedirectsAfterSave(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('priv-act', 'Privacy Action');

        $handler  = new TreePrivacyAction();
        $request  = self::createRequest('POST', [], [
            'delete'                    => [],
            'xref'                      => [],
            'tag_type'                  => [],
            'resn'                      => [],
            'HIDE_LIVE_PEOPLE'          => '1',
            'KEEP_ALIVE_YEARS_BIRTH'    => '0',
            'KEEP_ALIVE_YEARS_DEATH'    => '0',
            'MAX_ALIVE_AGE'             => '120',
            'REQUIRE_AUTHENTICATION'    => '0',
            'SHOW_DEAD_PEOPLE'          => '2',
            'SHOW_LIVING_NAMES'         => '2',
            'SHOW_PRIVATE_RELATIONSHIPS' => '1',
        ], [], ['tree' => $tree]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}

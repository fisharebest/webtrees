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
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PasteFact::class)]
class PasteFactTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(PasteFact::class));
    }

    public function testHandlePastesFactAndRedirects(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('paste-fact', 'Paste Fact');

        $user_service = new UserService();
        $user         = $user_service->create('testuser', 'Test User', 'test@example.com', 'secret');
        Auth::login($user);

        // Create an individual record for the paste target
        $tree->createIndividual("0 @@ INDI\n1 NAME Test /User/\n1 SEX M");

        $clipboard_service = $this->createMock(ClipboardService::class);
        $clipboard_service->expects(self::once())
            ->method('pasteFact');

        $handler  = new PasteFact($clipboard_service);
        $request  = self::createRequest('POST', [], ['fact_id' => 'some-fact-id'], [], [
            'tree' => $tree,
            'xref' => 'X1',
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}

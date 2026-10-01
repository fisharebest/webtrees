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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\Controllers\AutoCompleteCitation;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ServerRequestInterface;

use function json_decode;

use const JSON_THROW_ON_ERROR;

#[CoversClass(AutoCompleteCitation::class)]
class AutoCompleteCitationTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(AutoCompleteCitation::class));
    }

    public function testFindsPagesInLinkedRecords(): void
    {
        self::createDatabase();

        $tree         = $this->importTree('demo.ged');
        $tree_service = new TreeService(new GedcomImportService());
        $request      = self::createRequest(attributes: ['tree' => $tree]);
        Registry::container()->set(ServerRequestInterface::class, $request);

        $user = (new UserService(new SystemClock()))->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $controller = new AutoCompleteCitation(new SearchService($tree_service));
        $response   = $controller->get($tree, 'Elizabeth', 'X1102');

        $pages = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        self::assertContains(['value' => 'https://en.wikipedia.org/wiki/Elizabeth_II'], $pages);
    }
}

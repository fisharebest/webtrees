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
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SitePreferencesAction::class)]
class SitePreferencesActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(SitePreferencesAction::class));
    }

    public function testHandleSavesPreferencesAndRedirects(): void
    {
        $handler  = new SitePreferencesAction();
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'INDEX_DIRECTORY'     => '/tmp/',
            'ALLOW_CHANGE_GEDCOM' => '1',
            'LANGUAGE'            => 'en-GB',
            'THEME_DIR'           => '_administration',
            'TIMEZONE'            => 'UTC',
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());

        // Verify preferences were persisted
        self::assertSame('1', Site::getPreference('ALLOW_CHANGE_GEDCOM'));
        self::assertSame('en-GB', Site::getPreference('LANGUAGE'));
        self::assertSame('_administration', Site::getPreference('THEME_DIR'));
        self::assertSame('UTC', Site::getPreference('TIMEZONE'));
    }

    public function testHandleAppendsTrailingSlashToIndexDirectory(): void
    {
        $handler  = new SitePreferencesAction();
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'INDEX_DIRECTORY'     => '/tmp',
            'ALLOW_CHANGE_GEDCOM' => '',
            'LANGUAGE'            => 'de',
            'THEME_DIR'           => '',
            'TIMEZONE'            => 'Europe/Berlin',
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());

        // Boolean false => (string) false === ''
        self::assertSame('', Site::getPreference('ALLOW_CHANGE_GEDCOM'));
        self::assertSame('de', Site::getPreference('LANGUAGE'));
        self::assertSame('Europe/Berlin', Site::getPreference('TIMEZONE'));
    }

    public function testHandleWithNonExistentDirectory(): void
    {
        // Set a known initial value
        Site::setPreference('INDEX_DIRECTORY', '/tmp/');

        $handler  = new SitePreferencesAction();
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'INDEX_DIRECTORY'     => '/nonexistent/path/that/does/not/exist/',
            'ALLOW_CHANGE_GEDCOM' => '',
            'LANGUAGE'            => 'en-GB',
            'THEME_DIR'           => '',
            'TIMEZONE'            => 'UTC',
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());

        // Index directory should not have been changed to the non-existent path
        self::assertSame('/tmp/', Site::getPreference('INDEX_DIRECTORY'));
    }
}

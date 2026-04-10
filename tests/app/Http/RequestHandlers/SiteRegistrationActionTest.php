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

#[CoversClass(SiteRegistrationAction::class)]
class SiteRegistrationActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(SiteRegistrationAction::class));
    }

    public function testHandleSavesPreferences(): void
    {
        $handler  = new SiteRegistrationAction();
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'WELCOME_TEXT_AUTH_MODE'   => '0',
            'WELCOME_TEXT_AUTH_MODE_4' => 'Custom welcome text',
            'USE_REGISTRATION_MODULE'  => '1',
            'SHOW_REGISTER_CAUTION'    => '1',
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertSame('0', Site::getPreference('WELCOME_TEXT_AUTH_MODE'));
        self::assertSame('1', Site::getPreference('USE_REGISTRATION_MODULE'));
        self::assertSame('1', Site::getPreference('SHOW_REGISTER_CAUTION'));
    }

    public function testHandleDisablesRegistration(): void
    {
        $handler  = new SiteRegistrationAction();
        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'WELCOME_TEXT_AUTH_MODE'   => '1',
            'WELCOME_TEXT_AUTH_MODE_4' => '',
            'USE_REGISTRATION_MODULE'  => '0',
            'SHOW_REGISTER_CAUTION'    => '0',
        ]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertSame('', Site::getPreference('USE_REGISTRATION_MODULE'));
        self::assertSame('', Site::getPreference('SHOW_REGISTER_CAUTION'));
    }
}

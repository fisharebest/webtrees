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

use Fisharebest\Webtrees\Services\MigrationService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Services\ServerCheckService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionMethod;

#[CoversClass(SetupWizard::class)]
class SetupWizardTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(SetupWizard::class));
    }

    /**
     * userData() must prefer the POST body, then fall back to request
     * attributes (carrying values that ReadConfigIni loaded from
     * config.ini.php), then to the hard-coded defaults. The form field
     * `baseurl` reads from the config.ini.php key `base_url`; all other DB
     * keys share the same name in both representations.
     */
    public function testUserDataPrefersBodyThenAttributesThenDefaults(): void
    {
        $wizard = new SetupWizard(
            self::createStub(MigrationService::class),
            self::createStub(ModuleService::class),
            self::createStub(PhpService::class),
            self::createStub(ServerCheckService::class),
            self::createStub(UserService::class),
        );

        $request = self::createRequest(
            params: [
                'dbhost' => 'body-host',
            ],
        )
            ->withAttribute('dbhost', 'attr-host')
            ->withAttribute('dbuser', 'attr-user')
            ->withAttribute('base_url', 'https://example.test');

        $method = new ReflectionMethod($wizard, 'userData');
        $data   = $method->invoke($wizard, $request);

        // POST body wins.
        self::assertSame('body-host', $data['dbhost']);
        // Attribute fills in where the body is silent.
        self::assertSame('attr-user', $data['dbuser']);
        // baseurl form field maps to base_url config.ini.php key.
        self::assertSame('https://example.test', $data['baseurl']);
        // Untouched fields fall back to the hard-coded defaults.
        self::assertSame('', $data['dbpass']);
        self::assertSame('wt_', $data['tblpfx']);
    }
}

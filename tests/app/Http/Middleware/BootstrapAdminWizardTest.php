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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\RequestHandlers\SetupWizard;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

#[CoversClass(BootstrapAdminWizard::class)]
class BootstrapAdminWizardTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * Without a dbhost request attribute the middleware must not intervene.
     * That attribute is set by the ReadConfigIni middleware only when
     * config.ini.php exists; its absence means we are in a fresh install
     * where the setup wizard is already routed by ReadConfigIni itself.
     */
    public function testPassThroughWhenNoConfigIniLoaded(): void
    {
        $expected = response('handler ran');
        $handler  = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($expected);

        $request    = self::createRequest();
        $wizard     = self::createStub(SetupWizard::class);
        $middleware = new BootstrapAdminWizard($wizard);

        $response = $middleware->process($request, $handler);

        self::assertSame('handler ran', (string) $response->getBody());
    }

    /**
     * config.ini.php is present and an administrator already exists — the
     * regular handler stack must run unchanged.
     */
    public function testPassThroughWhenAdministratorExists(): void
    {
        (new UserService())
            ->create('admin', 'Admin', 'admin@example.test', 'secret')
            ->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');

        $expected = response('handler ran');
        $handler  = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($expected);

        $request    = self::createRequest()->withAttribute('dbhost', 'localhost');
        $wizard     = self::createStub(SetupWizard::class);
        $middleware = new BootstrapAdminWizard($wizard);

        $response = $middleware->process($request, $handler);

        self::assertSame('handler ran', (string) $response->getBody());
    }

    /**
     * config.ini.php is present but the user table is empty — the middleware
     * must hand the request to the setup wizard so the operator can finish
     * the install by filling in only the admin account.
     */
    public function testDivertToSetupWizardWhenAdministratorMissing(): void
    {
        $expected = response('wizard ran');
        $wizard   = self::createMock(SetupWizard::class);
        $wizard->expects(self::once())
            ->method('handle')
            ->willReturnCallback(function ($request) use ($expected): ResponseInterface {
                $body = $request->getParsedBody();

                self::assertIsArray($body);
                self::assertSame(5, $body['step']);

                return $expected;
            });

        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response('handler ran'));

        $request    = self::createRequest()->withAttribute('dbhost', 'localhost');
        $middleware = new BootstrapAdminWizard($wizard);

        $response = $middleware->process($request, $handler);

        self::assertSame('wizard ran', (string) $response->getBody());
    }

    /**
     * A non-administrator user (e.g. a moderator left behind after deleting
     * the admin account) must not count: the middleware still diverts to the
     * wizard so the install completes cleanly.
     */
    public function testDivertWhenOnlyNonAdministratorsExist(): void
    {
        (new UserService())
            ->create('user', 'User', 'user@example.test', 'secret');
        // No PREF_IS_ADMINISTRATOR preference set.

        $expected = response('wizard ran');
        $wizard   = self::createStub(SetupWizard::class);
        $wizard->method('handle')->willReturn($expected);

        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response('handler ran'));

        $request    = self::createRequest()->withAttribute('dbhost', 'localhost');
        $middleware = new BootstrapAdminWizard($wizard);

        $response = $middleware->process($request, $handler);

        self::assertSame('wizard ran', (string) $response->getBody());
    }
}

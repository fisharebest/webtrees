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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Middleware;

use Fisharebest\Webtrees\Factories\LanguageFactory;
use Fisharebest\Webtrees\Http\Middleware\ReadConfigIni;
use Fisharebest\Webtrees\Http\RequestHandlers\SetupWizard;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\MigrationService;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Services\ServerCheckService;
use Fisharebest\Webtrees\Tests\TestCase;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Clock\Clock;

#[CoversClass(ReadConfigIni::class)]
class ReadConfigIniTest extends TestCase
{
    public function testMissingBaseUrlThrowsException(): void
    {
        $uri_factory     = Registry::container()->get(UriFactoryInterface::class);
        $uri             = $uri_factory->createUri('https://example.com/');
        $request_factory = Registry::container()->get(ServerRequestFactoryInterface::class);
        $request         = $request_factory->createServerRequest('GET', $uri);
        $handler         = self::createStub(RequestHandlerInterface::class);
        $setup_wizard    = Registry::container()->get(SetupWizard::class);
        $middleware      = new ReadConfigIni($setup_wizard);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('data/config.ini.php does not define base_url');

        $middleware->process($request, $handler);
    }
}

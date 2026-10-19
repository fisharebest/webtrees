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

use Fisharebest\Webtrees\Http\Middleware\UseTheme;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Module\MinimalTheme;
use Fisharebest\Webtrees\Module\WebtreesTheme;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

#[CoversClass(UseTheme::class)]
class UseThemeTest extends TestCase
{
    private const string IPHONE = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1';

    private const string DESKTOP = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36';

    private ServerRequestInterface|null $handled = null;

    public function testDefaultTheme(): void
    {
        $this->setSitePreferences('');

        self::assertSame('webtrees', $this->process(self::DESKTOP)->name());
        self::assertFalse($this->handled?->getAttribute(UseTheme::ATTRIBUTE_MOBILE));
    }

    public function testMobileDeviceUsesMobileTheme(): void
    {
        $this->setSitePreferences('1');

        self::assertSame('minimal', $this->process(self::IPHONE)->name());
        self::assertTrue($this->handled?->getAttribute(UseTheme::ATTRIBUTE_MOBILE));
        self::assertSame('minimal', Session::get('theme-mobile'));
    }

    public function testClientHintOverridesUserAgent(): void
    {
        $this->setSitePreferences('1');

        self::assertSame('webtrees', $this->process(self::IPHONE, [], '?0')->name());
        self::assertSame('minimal', $this->process(self::DESKTOP, [], '?1')->name());
    }

    public function testDetectionCanBeDisabled(): void
    {
        $this->setSitePreferences('0');

        self::assertSame('webtrees', $this->process(self::IPHONE)->name());
    }

    public function testUrlParameterIsRememberedInSession(): void
    {
        $this->setSitePreferences('0');

        self::assertSame('minimal', $this->process(self::DESKTOP, ['mobile' => '1'])->name());
        self::assertSame('minimal', $this->process(self::DESKTOP)->name());
        self::assertSame('webtrees', $this->process(self::DESKTOP, ['mobile' => '0'])->name());
        self::assertSame('webtrees', $this->process(self::IPHONE)->name());
    }

    public function testMobileFallsBackToDefaultTheme(): void
    {
        $this->setSitePreferences('1', '');

        self::assertSame('webtrees', $this->process(self::IPHONE)->name());
    }

    public function testUserThemeForMobileDevices(): void
    {
        $this->setSitePreferences('1', '');
        Session::put('theme', 'webtrees');
        Session::put('theme-mobile', 'minimal');

        self::assertSame('minimal', $this->process(self::IPHONE)->name());
        self::assertSame('webtrees', $this->process(self::DESKTOP)->name());
    }

    private function setSitePreferences(string $detect, string $mobile_theme = 'minimal'): void
    {
        Site::$preferences = [
            'THEME_DIR'           => 'webtrees',
            'THEME_DIR_MOBILE'    => $mobile_theme,
            'THEME_MOBILE_DETECT' => $detect,
        ];
    }

    /**
     * @param array<string,string> $query
     */
    private function process(string $user_agent, array $query = [], string $client_hint = ''): ModuleThemeInterface
    {
        $webtrees = new WebtreesTheme();
        $webtrees->setName('webtrees');
        $minimal = new MinimalTheme();
        $minimal->setName('minimal');

        $module_service = self::createStub(ModuleService::class);
        $module_service->method('findByInterface')->willReturn(new Collection([$webtrees, $minimal]));

        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturnCallback(function (ServerRequestInterface $request) {
            $this->handled = $request;

            return response();
        });

        $request = self::createRequest(query: $query)->withHeader('User-Agent', $user_agent);

        if ($client_hint !== '') {
            $request = $request->withHeader('Sec-CH-UA-Mobile', $client_hint);
        }

        $middleware = new UseTheme($module_service);
        $middleware->process($request, $handler);

        $theme = $this->handled?->getAttribute('theme');
        self::assertInstanceOf(ModuleThemeInterface::class, $theme);

        return $theme;
    }
}

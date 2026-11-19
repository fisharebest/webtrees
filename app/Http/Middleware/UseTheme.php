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

use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Module\WebtreesTheme;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Site;
use Generator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function preg_match;

class UseTheme implements MiddlewareInterface
{
    // Request attribute, true when the theme for mobile devices is being used.
    public const string ATTRIBUTE_MOBILE = 'mobile-theme';

    // Heuristic for phones and tablets, when the client does not send Sec-CH-UA-Mobile.
    private const string MOBILE_USER_AGENT = '/Mobi|Android|iPhone|iPad|iPod|Tablet|Kindle|Silk/i';

    private ModuleService $module_service;

    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $mobile      = $this->useMobileTheme($request);
        $session_key = $mobile ? 'theme-mobile' : 'theme';

        foreach ($this->themes($mobile) as $theme) {
            if ($theme instanceof ModuleThemeInterface) {
                Registry::container()->set(ModuleThemeInterface::class, $theme);
                $request = $request
                    ->withAttribute('theme', $theme)
                    ->withAttribute(self::ATTRIBUTE_MOBILE, $mobile);
                Session::put($session_key, $theme->name());
                break;
            }
        }

        return $handler->handle($request);
    }

    /**
     * A URL parameter (?mobile=1 or ?mobile=0) overrides the device detection for the rest of the session.
     */
    private function useMobileTheme(ServerRequestInterface $request): bool
    {
        $mobile = $request->getQueryParams()['mobile'] ?? null;

        if ($mobile === '1' || $mobile === '0') {
            Session::put('theme-mode', $mobile === '1' ? 'mobile' : 'desktop');
        }

        $mode = Session::get('theme-mode');

        if ($mode === 'mobile' || $mode === 'desktop') {
            return $mode === 'mobile';
        }

        return Site::getPreference('THEME_MOBILE_DETECT') === '1' && $this->isMobileDevice($request);
    }

    private function isMobileDevice(ServerRequestInterface $request): bool
    {
        $client_hint = $request->getHeaderLine('Sec-CH-UA-Mobile');

        if ($client_hint !== '') {
            return $client_hint === '?1';
        }

        return preg_match(self::MOBILE_USER_AGENT, $request->getHeaderLine('User-Agent')) === 1;
    }

    /**
     * The theme can be chosen in various ways.
     *
     * @return Generator<ModuleThemeInterface|null>
     */
    private function themes(bool $mobile): Generator
    {
        $themes = $this->module_service->findByInterface(ModuleThemeInterface::class);

        if ($mobile) {
            // Last theme used on a mobile device
            yield $themes
                ->first(static fn (ModuleThemeInterface $module): bool => $module->name() === Session::get('theme-mobile'));

            // Default for site on mobile devices
            yield $themes
                ->first(static fn (ModuleThemeInterface $module): bool => $module->name() === Site::getPreference('THEME_DIR_MOBILE'));
        }

        // Last theme used
        yield $themes
            ->first(static fn (ModuleThemeInterface $module): bool => $module->name() === Session::get('theme'));

        // Default for site
        yield $themes
            ->first(static fn (ModuleThemeInterface $module): bool => $module->name() === Site::getPreference('THEME_DIR'));

        // Default for application
        yield new WebtreesTheme();
    }
}

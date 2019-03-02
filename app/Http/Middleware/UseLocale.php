<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Middleware;

use Closure;
use Fisharebest\Localization\Locale as WebtreesLocale;
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Middleware to set a global theme.
 */
class UseLocale implements MiddlewareInterface
{
    /** @var Tree|null */
    private $tree;

    /**
     * UseTheme constructor.
     *
     * @param Tree|null $tree
     */
    public function __construct(?Tree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Select a locale
        define('WT_LOCALE', I18N::init('', $this->tree));
        Session::put('locale', WT_LOCALE);

        app()->instance(LocaleInterface::class, WebtreesLocale::create(WT_LOCALE));

        return $next($request);
    }
}

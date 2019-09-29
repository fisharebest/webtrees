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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

/**
 * Middleware to generate a response when no route was matched.
 */
class NoRouteFound implements MiddlewareInterface
{
    use ViewResponseTrait;

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Tree|null $tree */
        $tree = app(Tree::class);

        // The tree exists, we have access to it, and it is fully imported.
        if ($tree instanceof Tree && $tree->getPreference('imported') === '1') {
            return redirect(route('tree-page', ['ged' => $tree->name()]));
        }

        // Not logged in?
        if (!Auth::check()) {
            return redirect(route('login', ['url' => $request->getAttribute('request_uri')]));
        }

        // No tree or tree not imported?
        if (Auth::isAdmin()) {
            return redirect(route('admin-trees'));
        }

        return $this->viewResponse('errors/no-tree-access', ['title' => '']);
    }
}

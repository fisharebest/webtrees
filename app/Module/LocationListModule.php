<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LocationListModule extends AbstractModule implements ModuleListInterface, RequestHandlerInterface
{
    use ModuleListTrait;

    protected const string ROUTE_URL = '/tree/{tree}/location-list';

    /** @var int The default access level for this module.  It can be changed in the control panel. */
    protected int $access_level = Auth::PRIV_USER;

    /**
     * Initialization.
     *
     * @return void
     */
    public function boot(): void
    {
        Registry::routeFactory()->routeMap()
            ->get(static::class, static::ROUTE_URL, $this);
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/list */
        return I18N::translate('Locations');
    }

    public function description(): string
    {
        /* I18N: Description of the “Locations” module */
        return I18N::translate('A list of locations.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function listMenuClass(): string
    {
        return 'menu-list-loc';
    }

    /**
     * @return array<string>
     */
    public function listUrlAttributes(): array
    {
        return [];
    }

    /**
     * @param Tree $tree
     *
     * @return bool
     */
    public function listIsEmpty(Tree $tree): bool
    {
        return !DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Location::RECORD_TYPE)
            ->exists();
    }

    /**
     * @param Tree                                      $tree
     * @param array<bool|int|string|array<string>|null> $parameters
     *
     * @return string
     */
    public function listUrl(Tree $tree, array $parameters = []): string
    {
        $parameters['tree'] = $tree->name();

        return route(static::class, $parameters);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();
        $user = Validator::attributes($request)->user();

        Auth::checkComponentAccess($this, ModuleListInterface::class, $tree, $user);

        $locations = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Location::RECORD_TYPE)
            ->get()
            ->map(Registry::locationFactory()->mapper($tree))
            ->filter(GedcomRecord::accessFilter());

        return $this->viewResponse('modules/location-list/page', [
            'locations' => $locations,
            'title'     => I18N::translate('Locations'),
            'tree'      => $tree,
        ]);
    }
}

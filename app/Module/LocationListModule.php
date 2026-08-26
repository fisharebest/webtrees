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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Enums\AccessLevel;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LocationListModule extends AbstractModule implements ModuleListInterface
{
    use ModuleListTrait;

    protected const string ROUTE_URL = '/tree/{tree}/location-list';

    protected AccessLevel $access_level = AccessLevel::Member;

    /**
     * Initialization.
     */
    public function boot(): void
    {
        Registry::routeFactory()->routeMap()->add(static::ROUTE_URL, static::class);
    }

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

    public function listIsEmpty(Tree $tree): bool
    {
        return !DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Location::RECORD_TYPE)
            ->exists();
    }

    /**
     * @param array<bool|int|string|array<string>|null> $parameters
     */
    public function listUrl(Tree $tree, array $parameters = []): string
    {
        $parameters['tree'] = $tree->name();

        return route(static::class, $parameters);
    }

    public function get(ServerRequestInterface $request): ResponseInterface
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

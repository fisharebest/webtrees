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

namespace Fisharebest\Webtrees\Statistics\Repository;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\FamilyTreeFavoritesModule;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\UserFavoritesModule;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;

use function count;

class FavoritesRepository
{
    private Tree $tree;

    private ModuleService $module_service;

    public function __construct(Tree $tree, ModuleService $module_service)
    {
        $this->tree           = $tree;
        $this->module_service = $module_service;
    }

    public function gedcomFavorites(): string
    {
        $module = $this->module_service
            ->findByInterface(FamilyTreeFavoritesModule::class)
            ->first();

        if ($module instanceof FamilyTreeFavoritesModule) {
            return $module->getBlock($this->tree, 0, ModuleBlockInterface::CONTEXT_EMBED);
        }

        return '';
    }

    public function userFavorites(): string
    {
        $module = $this->module_service
            ->findByInterface(UserFavoritesModule::class)
            ->first();

        if ($module instanceof UserFavoritesModule) {
            return $module->getBlock($this->tree, 0, ModuleBlockInterface::CONTEXT_EMBED);
        }

        return '';
    }

    public function totalGedcomFavorites(): string
    {
        $count  = 0;
        $module = $this->module_service
            ->findByInterface(FamilyTreeFavoritesModule::class)
            ->first();

        if ($module instanceof FamilyTreeFavoritesModule) {
            $count = count($module->getFavorites($this->tree));
        }

        return I18N::number($count);
    }

    public function totalUserFavorites(): string
    {
        $count  = 0;
        $module = $this->module_service
            ->findByInterface(UserFavoritesModule::class)
            ->first();

        if ($module instanceof UserFavoritesModule) {
            $count = count($module->getFavorites($this->tree, Auth::user()));
        }

        return I18N::number($count);
    }
}

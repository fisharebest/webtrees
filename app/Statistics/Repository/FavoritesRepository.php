<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

namespace Fisharebest\Webtrees\Statistics\Repository;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Module\FamilyTreeFavoritesModule;
use Fisharebest\Webtrees\Module\UserFavoritesModule;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\FavoritesRepositoryInterface;
use Fisharebest\Webtrees\Tree;

/**
 * A repository providing methods for favorites related statistics.
 */
class FavoritesRepository implements FavoritesRepositoryInterface
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * @inheritDoc
     */
    public function gedcomFavorites(): string
    {
        $module = Module::findByClass(FamilyTreeFavoritesModule::class);

        if ($module instanceof FamilyTreeFavoritesModule) {
            return $module->getBlock($this->tree, 0);
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function userFavorites(): string
    {
        $module = Module::findByClass(UserFavoritesModule::class);

        if ($module instanceof UserFavoritesModule) {
            return $module->getBlock($this->tree, 0);
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function totalGedcomFavorites(): string
    {
        $count  = 0;
        $module = Module::findByClass(FamilyTreeFavoritesModule::class);

        if ($module instanceof FamilyTreeFavoritesModule) {
            $count = \count($module->getFavorites($this->tree));
        }

        return I18N::number($count);
    }

    /**
     * @inheritDoc
     */
    public function totalUserFavorites(): string
    {
        $count  = 0;
        $module = Module::findByClass(UserFavoritesModule::class);

        if ($module instanceof UserFavoritesModule) {
            $count = \count($module->getFavorites($this->tree, Auth::user()));
        }

        return I18N::number($count);
    }
}

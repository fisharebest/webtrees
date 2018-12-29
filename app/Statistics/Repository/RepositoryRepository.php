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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Helper\Percentage;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\RepositoryRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Statistics submodule providing all REPOSITORY related methods.
 */
class RepositoryRepository implements RepositoryRepositoryInterface
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
     * Count the number of repositories.
     *
     * @return int
     */
    private function totalRepositoriesQuery(): int
    {
        return DB::table('other')
            ->where('o_file', '=', $this->tree->id())
            ->where('o_type', '=', 'REPO')
            ->count();
    }

    /**
     * Count the number of repositories
     *
     * @return string
     */
    public function totalRepositories(): string
    {
        return I18N::number($this->totalRepositoriesQuery());
    }

    /**
     * Show the total number of repositories as a percentage.
     *
     * @return string
     */
    public function totalRepositoriesPercentage(): string
    {
        $percentageHelper = new Percentage($this->tree);
        return $percentageHelper->getPercentage($this->totalRepositoriesQuery(), 'all');
    }
}

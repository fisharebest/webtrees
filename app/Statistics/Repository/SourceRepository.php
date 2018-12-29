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
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\SourceRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Statistics submodule providing all SOURCE related methods.
 */
class SourceRepository implements SourceRepositoryInterface
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
     * Count the total number of sources.
     *
     * @return int
     */
    public function totalSourcesQuery(): int
    {
        return DB::table('sources')
            ->where('s_file', '=', $this->tree->id())
            ->count();
    }

    /**
     * Count the total number of sources.
     *
     * @return string
     */
    public function totalSources(): string
    {
        return I18N::number($this->totalSourcesQuery());
    }

    /**
     * Show the number of sources as a percentage.
     *
     * @return string
     */
    public function totalSourcesPercentage(): string
    {
        $percentageHelper = new Percentage($this->tree);
        return $percentageHelper->getPercentage($this->totalSourcesQuery(), 'all');
    }
}

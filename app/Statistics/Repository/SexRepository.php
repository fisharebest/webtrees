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
use Fisharebest\Webtrees\Statistics\Google\ChartSex;
use Fisharebest\Webtrees\Statistics\Helper\Percentage;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\SexRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Statistics submodule providing all SEX related methods.
 */
class SexRepository implements SexRepositoryInterface
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * @var Percentage
     */
    private $percentageHelper;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree             = $tree;
        $this->percentageHelper = new Percentage($tree);
    }

    /**
     * Count the number of males.
     *
     * @return int
     */
    private function totalSexMalesQuery(): int
    {
        return DB::table('individuals')
            ->where('i_file', '=', $this->tree->id())
            ->where('i_sex', '=', 'M')
            ->count();
    }

    /**
     * Count the number of females.
     *
     * @return int
     */
    private function totalSexFemalesQuery(): int
    {
        return DB::table('individuals')
            ->where('i_file', '=', $this->tree->id())
            ->where('i_sex', '=', 'F')
            ->count();
    }

    /**
     * Count the number of individuals with unknown sex.
     *
     * @return int
     */
    private function totalSexUnknownQuery(): int
    {
        return DB::table('individuals')
            ->where('i_file', '=', $this->tree->id())
            ->where('i_sex', '=', 'U')
            ->count();
    }

    /**
     * Count the number of males.
     *
     * @return string
     */
    public function totalSexMales(): string
    {
        return I18N::number($this->totalSexMalesQuery());
    }

    /**
     * Count the number of males
     *
     * @return string
     */
    public function totalSexMalesPercentage(): string
    {
        return $this->percentageHelper->getPercentage($this->totalSexMalesQuery(), 'individual');
    }

    /**
     * Count the number of females.
     *
     * @return string
     */
    public function totalSexFemales(): string
    {
        return I18N::number($this->totalSexFemalesQuery());
    }

    /**
     * Count the number of females.
     *
     * @return string
     */
    public function totalSexFemalesPercentage(): string
    {
        return $this->percentageHelper->getPercentage($this->totalSexFemalesQuery(), 'individual');
    }

    /**
     * Count the number of individuals with unknown sex.
     *
     * @return string
     */
    public function totalSexUnknown(): string
    {
        return I18N::number($this->totalSexUnknownQuery());
    }

    /**
     * Count the number of individuals with unknown sex.
     *
     * @return string
     */
    public function totalSexUnknownPercentage(): string
    {
        return $this->percentageHelper->getPercentage($this->totalSexUnknownQuery(), 'individual');
    }

    /**
     * Generate a chart showing sex distribution.
     *
     * @param string|null $size
     * @param string|null $color_female
     * @param string|null $color_male
     * @param string|null $color_unknown
     *
     * @return string
     */
    public function chartSex(
        string $size          = null,
        string $color_female  = null,
        string $color_male    = null,
        string $color_unknown = null
    ): string {
        $tot_m = $this->totalSexMalesQuery();
        $tot_f = $this->totalSexFemalesQuery();
        $tot_u = $this->totalSexUnknownQuery();

        return (new ChartSex($this->tree))
            ->chartSex($tot_m, $tot_f, $tot_u, $size, $color_female, $color_male, $color_unknown);
    }
}

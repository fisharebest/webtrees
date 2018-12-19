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

namespace Fisharebest\Webtrees\Statistics;

use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Tree;

/**
 * A selection of pre-formatted statistical queries.
 *
 * These are primarily used for embedded keywords on HTML blocks, but
 * are also used elsewhere in the code.
 */
class BirthPlaces
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * @var Places
     */
    private $places;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree   = $tree;
        $this->places = new Places($tree);
    }

    /**
     * A list of common birth places.
     *
     * @return array
     */
    private function getList(): array
    {
        $places = $this->places->statsPlaces('INDI', 'BIRT');
        $top10  = [];
        $i      = 0;

        arsort($places);

        foreach ($places as $place => $count) {
            $tmp     = new Place($place, $this->tree);
            $top10[] = [
                'place' => $tmp,
                'count' => $count,
            ];

            ++$i;

            if ($i === 10) {
                break;
            }
        }

        return $top10;
    }

    public function __toString(): string
    {
        $records = $this->getList();

        return view(
            'statistics/other/top10-list',
            [
                'records' => $records,
            ]
        );
    }
}

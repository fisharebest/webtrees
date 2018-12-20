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

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Statistics\Helper\Percentage;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class Deceased
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * @var Percentage
     */
    private $percentage;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree       = $tree;
        $this->percentage = new Percentage($tree);
    }

    /**
     * Count the number of dead individuals.
     *
     * @return int
     */
    public function totalDeceasedQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(*) FROM `##individuals` WHERE i_file = :tree_id AND i_gedcom REGEXP '\\n1 ("
            . implode('|', Gedcom::DEATH_EVENTS) . ")'"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
    }

    /**
     * Count the number of dead individuals.
     *
     * @return string
     */
    public function totalDeceasedPercentage(): string
    {
        return $this->percentage->getPercentage($this->totalDeceasedQuery(), 'individual');
    }
}

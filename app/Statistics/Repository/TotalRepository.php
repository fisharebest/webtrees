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
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\TotalRepositoryInterface;
use Fisharebest\Webtrees\Tree;

/**
 * Statistics submodule providing all BROWSER related methods.
 */
class TotalRepository implements TotalRepositoryInterface
{
    /**
     * @var IndividualRepository
     */
    private $individualRepository;

    /**
     * @var FamilyRepository
     */
    private $familyRepository;

    /**
     * @var SourceRepository
     */
    private $sourceRepository;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->individualRepository = new IndividualRepository($tree);
        $this->familyRepository     = new FamilyRepository($tree);
        $this->sourceRepository     = new SourceRepository($tree);
    }

    /**
     * How many GEDCOM records exist in the tree.
     *
     * @return string
     */
    public function totalRecords(): string
    {
        return I18N::number($this->individualRepository->totalIndividualsQuery()
            + $this->familyRepository->totalFamiliesQuery()
            + $this->sourceRepository->totalSourcesQuery());
    }
}

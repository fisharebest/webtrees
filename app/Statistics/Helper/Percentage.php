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

namespace Fisharebest\Webtrees\Statistics\Helper;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Individual;
use Fisharebest\Webtrees\Statistics\FamilyRepository;
use Fisharebest\Webtrees\Statistics\Source;
use Fisharebest\Webtrees\Statistics\Note;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class Percentage
{
    /**
     * @var Individual
     */
    private $individual;

    /**
     * @var FamilyRepository
     */
    private $family;

    /**
     * @var Source
     */
    private $source;

    /**
     * @var Note
     */
    private $note;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->individual = new Individual($tree);
        $this->family     = new FamilyRepository($tree);
        $this->source     = new Source($tree);
        $this->note       = new Note($tree);
    }

    /**
     * Convert totals into percentages.
     *
     * @param int    $total
     * @param string $type
     *
     * @return string
     */
    public function getPercentage(int $total, string $type): string
    {
        switch ($type) {
            case 'individual':
                $count = $this->individual->totalIndividualsQuery();
                break;

            case 'family':
                $count = $this->family->totalFamiliesQuery();
                break;

            case 'source':
                $count = $this->source->totalSourcesQuery();
                break;

            case 'note':
                $count = $this->note->totalNotesQuery();
                break;

            case 'all':
            default:
                $count = $this->individual->totalIndividualsQuery()
                        + $this->family->totalFamiliesQuery()
                        + $this->source->totalSourcesQuery();
                break;
        }

        return I18N::percentage($total / $count, 1);
    }
}

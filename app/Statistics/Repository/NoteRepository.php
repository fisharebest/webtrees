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
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\NoteRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Statistics submodule providing all NOTE related methods.
 */
class NoteRepository implements NoteRepositoryInterface
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
     * Count the number of notes.
     *
     * @return int
     */
    public function totalNotesQuery(): int
    {
        return DB::table('other')
            ->where('o_file', '=', $this->tree->id())
            ->where('o_type', '=', 'NOTE')
            ->count();
    }

    /**
     * Count the number of notes.
     *
     * @return string
     */
    public function totalNotes(): string
    {
        return I18N::number($this->totalNotesQuery());
    }

    /**
     * Show the number of notes as a percentage.
     *
     * @return string
     */
    public function totalNotesPercentage(): string
    {
        $percentageHelper = new Percentage($this->tree);
        return $percentageHelper->getPercentage($this->totalNotesQuery(), 'all');
    }
}

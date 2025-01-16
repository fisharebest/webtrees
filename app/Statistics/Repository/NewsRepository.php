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
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\NewsRepositoryInterface;
use Fisharebest\Webtrees\Tree;

/**
 * A repository providing methods for news related statistics.
 */
class NewsRepository implements NewsRepositoryInterface
{
    private Tree $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function totalUserJournal(): string
    {
        $number = DB::table('news')
            ->where('user_id', '=', Auth::id())
            ->count();

        return I18N::number($number);
    }

    public function totalGedcomNews(): string
    {
        $number = DB::table('news')
            ->where('gedcom_id', '=', $this->tree->id())
            ->count();

        return I18N::number($number);
    }
}

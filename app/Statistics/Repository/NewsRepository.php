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
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\NewsRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use PDOException;

/**
 * Statistics submodule providing all NEWS related methods.
 */
class NewsRepository implements NewsRepositoryInterface
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
     * How many blog entries exist for this user.
     *
     * @return string
     */
    public function totalUserJournal(): string
    {
        try {
            $number = (int) Database::prepare("SELECT COUNT(*) FROM `##news` WHERE user_id = ?")
                ->execute([Auth::id()])
                ->fetchOne();
        } catch (PDOException $ex) {
            // The module may not be installed, so the table may not exist.
            $number = 0;
        }

        return I18N::number($number);
    }

    /**
     * How many news items exist for this tree.
     *
     * @return string
     */
    public function totalGedcomNews(): string
    {
        try {
            $number = (int) Database::prepare("SELECT COUNT(*) FROM `##news` WHERE gedcom_id = ?")
                ->execute([$this->tree->id()])
                ->fetchOne();
        } catch (PDOException $ex) {
            // The module may not be installed, so the table may not exist.
            $number = 0;
        }

        return I18N::number($number);
    }
}

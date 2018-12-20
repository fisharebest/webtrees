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
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Helper\Sql;
use Fisharebest\Webtrees\Tree;

/**
 *
 */
class FamilyRepository
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
     * Count the total families.
     *
     * @return int
     */
    public function totalFamiliesQuery(): int
    {
        return (int) Database::prepare(
            "SELECT COUNT(*) FROM `##families` WHERE f_file = :tree_id"
        )->execute([
            'tree_id' => $this->tree->id(),
        ])->fetchOne();
    }

    /**
     * General query on family.
     *
     * @param string $type
     *
     * @return string
     */
    public function familyQuery($type): string
    {
        $rows = $this->runSql(
            " SELECT f_numchil AS tot, f_id AS id" .
            " FROM `##families`" .
            " WHERE" .
            " f_file={$this->tree->id()}" .
            " AND f_numchil = (" .
            "  SELECT max( f_numchil )" .
            "  FROM `##families`" .
            "  WHERE f_file ={$this->tree->id()}" .
            " )" .
            " LIMIT 1"
        );

        if (!isset($rows[0])) {
            return '';
        }

        $row    = $rows[0];
        $family = Family::getInstance($row->id, $this->tree);

        if (!$family) {
            return '';
        }

        switch ($type) {
            default:
            case 'full':
                if ($family->canShow()) {
                    $result = $family->formatList();
                } else {
                    $result = I18N::translate('This information is private and cannot be shown.');
                }
                break;
            case 'size':
                $result = I18N::number((int) $row->tot);
                break;
            case 'name':
                $result = '<a href="' . e($family->url()) . '">' . $family->getFullName() . '</a>';
                break;
        }

        return $result;
    }

    /**
     * Run an SQL query and cache the result.
     *
     * @param string $sql
     *
     * @return \stdClass[]
     */
    private function runSql($sql): array
    {
        return Sql::runSql($sql);
    }
}

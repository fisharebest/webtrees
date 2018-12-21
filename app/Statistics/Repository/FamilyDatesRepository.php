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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Helper\Sql;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\FamilyDatesRepositoryInterface;
use Fisharebest\Webtrees\Tree;

/**
 * Statistics submodule providing all methods related to family dates (birth, death, marriage, divorce).
 */
class FamilyDatesRepository implements FamilyDatesRepositoryInterface
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

    /**
     * Birth and Death
     *
     * @param string $type
     * @param string $life_dir
     * @param string $birth_death
     *
     * @return string
     */
    private function mortalityQuery($type, $life_dir, $birth_death): string
    {
        if ($birth_death === 'MARR') {
            $query_field = "'MARR'";
        } elseif ($birth_death === 'DIV') {
            $query_field = "'DIV'";
        } elseif ($birth_death === 'BIRT') {
            $query_field = "'BIRT'";
        } else {
            $query_field = "'DEAT'";
        }

        if ($life_dir === 'ASC') {
            $dmod = 'MIN';
        } else {
            $dmod = 'MAX';
        }

        $rows = $this->runSql(
            "SELECT d_year, d_type, d_fact, d_gid" .
            " FROM `##dates`" .
            " WHERE d_file={$this->tree->id()} AND d_fact IN ({$query_field}) AND d_julianday1=(" .
            " SELECT {$dmod}( d_julianday1 )" .
            " FROM `##dates`" .
            " WHERE d_file={$this->tree->id()} AND d_fact IN ({$query_field}) AND d_julianday1<>0 )" .
            " LIMIT 1"
        );

        if (!isset($rows[0])) {
            return '';
        }

        $row    = $rows[0];
        $result = '';

        switch ($type) {
            default:
            case 'full':
                $record = GedcomRecord::getInstance($row->d_gid, $this->tree);

                if ($record && $record->canShow()) {
                    $result = $record->formatList();
                } else {
                    $result = I18N::translate('This information is private and cannot be shown.');
                }

                break;

            case 'year':
                if ($row->d_year < 0) {
                    $row->d_year = abs($row->d_year) . ' B.C.';
                }

                $date   = new Date($row->d_type . ' ' . $row->d_year);
                $result = $date->display();
                break;

            case 'name':
                $record = GedcomRecord::getInstance($row->d_gid, $this->tree);

                if ($record) {
                    $result = '<a href="' . e($record->url()) . '">' . $record->getFullName() . '</a>';
                }

                break;

            case 'place':
                $record = GedcomRecord::getInstance($row->d_gid, $this->tree);
                $fact   = null;

                if ($record) {
                    $fact = $record->getFirstFact($row->d_fact);
                }

                if ($fact) {
                    $result = FunctionsPrint::formatFactPlace($fact, true, true, true);
                } else {
                    $result = I18N::translate('Private');
                }

                break;
        }

        return $result;
    }

    /**
     * Find the earliest birth.
     *
     * @return string
     */
    public function firstBirth(): string
    {
        return $this->mortalityQuery('full', 'ASC', 'BIRT');
    }

    /**
     * Find the earliest birth year.
     *
     * @return string
     */
    public function firstBirthYear(): string
    {
        return $this->mortalityQuery('year', 'ASC', 'BIRT');
    }

    /**
     * Find the name of the earliest birth.
     *
     * @return string
     */
    public function firstBirthName(): string
    {
        return $this->mortalityQuery('name', 'ASC', 'BIRT');
    }

    /**
     * Find the earliest birth place.
     *
     * @return string
     */
    public function firstBirthPlace(): string
    {
        return $this->mortalityQuery('place', 'ASC', 'BIRT');
    }

    /**
     * Find the latest birth.
     *
     * @return string
     */
    public function lastBirth(): string
    {
        return $this->mortalityQuery('full', 'DESC', 'BIRT');
    }

    /**
     * Find the latest birth year.
     *
     * @return string
     */
    public function lastBirthYear(): string
    {
        return $this->mortalityQuery('year', 'DESC', 'BIRT');
    }

    /**
     * Find the latest birth name.
     *
     * @return string
     */
    public function lastBirthName(): string
    {
        return $this->mortalityQuery('name', 'DESC', 'BIRT');
    }

    /**
     * Find the latest birth place.
     *
     * @return string
     */
    public function lastBirthPlace(): string
    {
        return $this->mortalityQuery('place', 'DESC', 'BIRT');
    }

    /**
     * Find the earliest death.
     *
     * @return string
     */
    public function firstDeath(): string
    {
        return $this->mortalityQuery('full', 'ASC', 'DEAT');
    }

    /**
     * Find the earliest death year.
     *
     * @return string
     */
    public function firstDeathYear(): string
    {
        return $this->mortalityQuery('year', 'ASC', 'DEAT');
    }

    /**
     * Find the earliest death name.
     *
     * @return string
     */
    public function firstDeathName(): string
    {
        return $this->mortalityQuery('name', 'ASC', 'DEAT');
    }

    /**
     * Find the earliest death place.
     *
     * @return string
     */
    public function firstDeathPlace(): string
    {
        return $this->mortalityQuery('place', 'ASC', 'DEAT');
    }

    /**
     * Find the latest death.
     *
     * @return string
     */
    public function lastDeath(): string
    {
        return $this->mortalityQuery('full', 'DESC', 'DEAT');
    }

    /**
     * Find the latest death year.
     *
     * @return string
     */
    public function lastDeathYear(): string
    {
        return $this->mortalityQuery('year', 'DESC', 'DEAT');
    }

    /**
     * Find the latest death name.
     *
     * @return string
     */
    public function lastDeathName(): string
    {
        return $this->mortalityQuery('name', 'DESC', 'DEAT');
    }

    /**
     * Find the place of the latest death.
     *
     * @return string
     */
    public function lastDeathPlace(): string
    {
        return $this->mortalityQuery('place', 'DESC', 'DEAT');
    }

    /**
     * Find the earliest marriage.
     *
     * @return string
     */
    public function firstMarriage(): string
    {
        return $this->mortalityQuery('full', 'ASC', 'MARR');
    }

    /**
     * Find the year of the earliest marriage.
     *
     * @return string
     */
    public function firstMarriageYear(): string
    {
        return $this->mortalityQuery('year', 'ASC', 'MARR');
    }

    /**
     * Find the names of spouses of the earliest marriage.
     *
     * @return string
     */
    public function firstMarriageName(): string
    {
        return $this->mortalityQuery('name', 'ASC', 'MARR');
    }

    /**
     * Find the place of the earliest marriage.
     *
     * @return string
     */
    public function firstMarriagePlace(): string
    {
        return $this->mortalityQuery('place', 'ASC', 'MARR');
    }

    /**
     * Find the latest marriage.
     *
     * @return string
     */
    public function lastMarriage(): string
    {
        return $this->mortalityQuery('full', 'DESC', 'MARR');
    }

    /**
     * Find the year of the latest marriage.
     *
     * @return string
     */
    public function lastMarriageYear(): string
    {
        return $this->mortalityQuery('year', 'DESC', 'MARR');
    }

    /**
     * Find the names of spouses of the latest marriage.
     *
     * @return string
     */
    public function lastMarriageName(): string
    {
        return $this->mortalityQuery('name', 'DESC', 'MARR');
    }

    /**
     * Find the location of the latest marriage.
     *
     * @return string
     */
    public function lastMarriagePlace(): string
    {
        return $this->mortalityQuery('place', 'DESC', 'MARR');
    }

    /**
     * Find the earliest divorce.
     *
     * @return string
     */
    public function firstDivorce(): string
    {
        return $this->mortalityQuery('full', 'ASC', 'DIV');
    }

    /**
     * Find the year of the earliest divorce.
     *
     * @return string
     */
    public function firstDivorceYear(): string
    {
        return $this->mortalityQuery('year', 'ASC', 'DIV');
    }

    /**
     * Find the names of individuals in the earliest divorce.
     *
     * @return string
     */
    public function firstDivorceName(): string
    {
        return $this->mortalityQuery('name', 'ASC', 'DIV');
    }

    /**
     * Find the location of the earliest divorce.
     *
     * @return string
     */
    public function firstDivorcePlace(): string
    {
        return $this->mortalityQuery('place', 'ASC', 'DIV');
    }

    /**
     * Find the latest divorce.
     *
     * @return string
     */
    public function lastDivorce(): string
    {
        return $this->mortalityQuery('full', 'DESC', 'DIV');
    }

    /**
     * Find the year of the latest divorce.
     *
     * @return string
     */
    public function lastDivorceYear(): string
    {
        return $this->mortalityQuery('year', 'DESC', 'DIV');
    }

    /**
     * Find the names of the individuals in the latest divorce.
     *
     * @return string
     */
    public function lastDivorceName(): string
    {
        return $this->mortalityQuery('name', 'DESC', 'DIV');
    }

    /**
     * Find the location of the latest divorce.
     *
     * @return string
     */
    public function lastDivorcePlace(): string
    {
        return $this->mortalityQuery('place', 'DESC', 'DIV');
    }
}

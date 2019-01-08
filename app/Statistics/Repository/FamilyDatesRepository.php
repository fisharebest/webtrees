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
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\FamilyDatesRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;

/**
 * A repository providing methods for family dates related statistics (birth, death, marriage, divorce).
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
            $query_field = 'MARR';
        } elseif ($birth_death === 'DIV') {
            $query_field = 'DIV';
        } elseif ($birth_death === 'BIRT') {
            $query_field = 'BIRT';
        } else {
            $query_field = 'DEAT';
        }

        if ($life_dir === 'ASC') {
            $dmod = 'MIN';
        } else {
            $dmod = 'MAX';
        }

        $row = DB::table('dates')
            ->select(['d_year', 'd_type', 'd_fact', 'd_gid'])
            ->where('d_file', '=', $this->tree->id())
            ->where('d_fact', '=', $query_field)
            ->where('d_julianday1', '=', function (Builder $query) use ($dmod, $query_field) {
                $query->selectRaw($dmod . '(d_julianday1)')
                    ->from('dates')
                    ->where('d_file', '=', $this->tree->id())
                    ->where('d_fact', '=', $query_field)
                    ->where('d_julianday1', '<>', 0);
            })
            ->first();

        if (!$row) {
            return '';
        }

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
     * @inheritDoc
     */
    public function firstBirth(): string
    {
        return $this->mortalityQuery('full', 'ASC', 'BIRT');
    }

    /**
     * @inheritDoc
     */
    public function firstBirthYear(): string
    {
        return $this->mortalityQuery('year', 'ASC', 'BIRT');
    }

    /**
     * @inheritDoc
     */
    public function firstBirthName(): string
    {
        return $this->mortalityQuery('name', 'ASC', 'BIRT');
    }

    /**
     * @inheritDoc
     */
    public function firstBirthPlace(): string
    {
        return $this->mortalityQuery('place', 'ASC', 'BIRT');
    }

    /**
     * @inheritDoc
     */
    public function lastBirth(): string
    {
        return $this->mortalityQuery('full', 'DESC', 'BIRT');
    }

    /**
     * @inheritDoc
     */
    public function lastBirthYear(): string
    {
        return $this->mortalityQuery('year', 'DESC', 'BIRT');
    }

    /**
     * @inheritDoc
     */
    public function lastBirthName(): string
    {
        return $this->mortalityQuery('name', 'DESC', 'BIRT');
    }

    /**
     * @inheritDoc
     */
    public function lastBirthPlace(): string
    {
        return $this->mortalityQuery('place', 'DESC', 'BIRT');
    }

    /**
     * @inheritDoc
     */
    public function firstDeath(): string
    {
        return $this->mortalityQuery('full', 'ASC', 'DEAT');
    }

    /**
     * @inheritDoc
     */
    public function firstDeathYear(): string
    {
        return $this->mortalityQuery('year', 'ASC', 'DEAT');
    }

    /**
     * @inheritDoc
     */
    public function firstDeathName(): string
    {
        return $this->mortalityQuery('name', 'ASC', 'DEAT');
    }

    /**
     * @inheritDoc
     */
    public function firstDeathPlace(): string
    {
        return $this->mortalityQuery('place', 'ASC', 'DEAT');
    }

    /**
     * @inheritDoc
     */
    public function lastDeath(): string
    {
        return $this->mortalityQuery('full', 'DESC', 'DEAT');
    }

    /**
     * @inheritDoc
     */
    public function lastDeathYear(): string
    {
        return $this->mortalityQuery('year', 'DESC', 'DEAT');
    }

    /**
     * @inheritDoc
     */
    public function lastDeathName(): string
    {
        return $this->mortalityQuery('name', 'DESC', 'DEAT');
    }

    /**
     * @inheritDoc
     */
    public function lastDeathPlace(): string
    {
        return $this->mortalityQuery('place', 'DESC', 'DEAT');
    }

    /**
     * @inheritDoc
     */
    public function firstMarriage(): string
    {
        return $this->mortalityQuery('full', 'ASC', 'MARR');
    }

    /**
     * @inheritDoc
     */
    public function firstMarriageYear(): string
    {
        return $this->mortalityQuery('year', 'ASC', 'MARR');
    }

    /**
     * @inheritDoc
     */
    public function firstMarriageName(): string
    {
        return $this->mortalityQuery('name', 'ASC', 'MARR');
    }

    /**
     * @inheritDoc
     */
    public function firstMarriagePlace(): string
    {
        return $this->mortalityQuery('place', 'ASC', 'MARR');
    }

    /**
     * @inheritDoc
     */
    public function lastMarriage(): string
    {
        return $this->mortalityQuery('full', 'DESC', 'MARR');
    }

    /**
     * @inheritDoc
     */
    public function lastMarriageYear(): string
    {
        return $this->mortalityQuery('year', 'DESC', 'MARR');
    }

    /**
     * @inheritDoc
     */
    public function lastMarriageName(): string
    {
        return $this->mortalityQuery('name', 'DESC', 'MARR');
    }

    /**
     * @inheritDoc
     */
    public function lastMarriagePlace(): string
    {
        return $this->mortalityQuery('place', 'DESC', 'MARR');
    }

    /**
     * @inheritDoc
     */
    public function firstDivorce(): string
    {
        return $this->mortalityQuery('full', 'ASC', 'DIV');
    }

    /**
     * @inheritDoc
     */
    public function firstDivorceYear(): string
    {
        return $this->mortalityQuery('year', 'ASC', 'DIV');
    }

    /**
     * @inheritDoc
     */
    public function firstDivorceName(): string
    {
        return $this->mortalityQuery('name', 'ASC', 'DIV');
    }

    /**
     * @inheritDoc
     */
    public function firstDivorcePlace(): string
    {
        return $this->mortalityQuery('place', 'ASC', 'DIV');
    }

    /**
     * @inheritDoc
     */
    public function lastDivorce(): string
    {
        return $this->mortalityQuery('full', 'DESC', 'DIV');
    }

    /**
     * @inheritDoc
     */
    public function lastDivorceYear(): string
    {
        return $this->mortalityQuery('year', 'DESC', 'DIV');
    }

    /**
     * @inheritDoc
     */
    public function lastDivorceName(): string
    {
        return $this->mortalityQuery('name', 'DESC', 'DIV');
    }

    /**
     * @inheritDoc
     */
    public function lastDivorcePlace(): string
    {
        return $this->mortalityQuery('place', 'DESC', 'DIV');
    }
}

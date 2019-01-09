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
use Illuminate\Database\Eloquent\Model;
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
     * @param string $fact
     * @param string $operation
     *
     * @return Model|object|static|null
     */
    private function mortalityQuery(string $fact, string $operation)
    {
        return DB::table('dates')
            ->select(['d_gid as id', 'd_year as year', 'd_fact AS fact', 'd_type AS type'])
            ->where('d_file', '=', $this->tree->id())
            ->where('d_fact', '=', $fact)
            ->where('d_julianday1', '=', function (Builder $query) use ($operation, $fact) {
                $query->selectRaw($operation . '(d_julianday1)')
                    ->from('dates')
                    ->where('d_file', '=', $this->tree->id())
                    ->where('d_fact', '=', $fact)
                    ->where('d_julianday1', '<>', 0);
            })
            ->first();
    }

    /**
     * Returns the formatted year of the first/last occuring event.
     *
     * @param string $type      The fact to query
     * @param string $operation The sorting operation
     *
     * @return string
     */
    private function getFirstLastEvent(string $type, string $operation): string
    {
        $row = $this->mortalityQuery($type, $operation);

        if (!$row) {
            return '';
        }

        $record = GedcomRecord::getInstance($row->id, $this->tree);

        if ($record && $record->canShow()) {
            return $record->formatList();
        }

        return I18N::translate('This information is private and cannot be shown.');
    }

    /**
     * @inheritDoc
     */
    public function firstBirth(): string
    {
        return $this->getFirstLastEvent('BIRT', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastBirth(): string
    {
        return $this->getFirstLastEvent('BIRT', 'MAX');
    }

    /**
     * @inheritDoc
     */
    public function firstDeath(): string
    {
        return $this->getFirstLastEvent('DEAT', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastDeath(): string
    {
        return $this->getFirstLastEvent('DEAT', 'MAX');
    }

    /**
     * @inheritDoc
     */
    public function firstMarriage(): string
    {
        return $this->getFirstLastEvent('MARR', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastMarriage(): string
    {
        return $this->getFirstLastEvent('MARR', 'MAX');
    }

    /**
     * @inheritDoc
     */
    public function firstDivorce(): string
    {
        return $this->getFirstLastEvent('DIV', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastDivorce(): string
    {
        return $this->getFirstLastEvent('DIV', 'MAX');
    }

    /**
     * Returns the formatted year of the first/last occuring event.
     *
     * @param string $type      The fact to query
     * @param string $operation The sorting operation
     *
     * @return string
     */
    private function getFirstLastEventYear(string $type, string $operation): string
    {
        $row = $this->mortalityQuery($type, $operation);

        if (!$row) {
            return '';
        }

        if ($row->year < 0) {
            $row->year = abs($row->year) . ' B.C.';
        }

        return (new Date($row->type . ' ' . $row->year))
            ->display();
    }

    /**
     * @inheritDoc
     */
    public function firstBirthYear(): string
    {
        return $this->getFirstLastEventYear('BIRT', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastBirthYear(): string
    {
        return $this->getFirstLastEventYear('BIRT', 'MAX');
    }

    /**
     * @inheritDoc
     */
    public function firstDeathYear(): string
    {
        return $this->getFirstLastEventYear('DEAT', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastDeathYear(): string
    {
        return $this->getFirstLastEventYear('DEAT', 'MAX');
    }

    /**
     * @inheritDoc
     */
    public function firstMarriageYear(): string
    {
        return $this->getFirstLastEventYear('MARR', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastMarriageYear(): string
    {
        return $this->getFirstLastEventYear('MARR', 'MAX');
    }

    /**
     * @inheritDoc
     */
    public function firstDivorceYear(): string
    {
        return $this->getFirstLastEventYear('DIV', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastDivorceYear(): string
    {
        return $this->getFirstLastEventYear('DIV', 'MAX');
    }

    /**
     * Returns the formatted name of the first/last occuring event.
     *
     * @param string $type      The fact to query
     * @param string $operation The sorting operation
     *
     * @return string
     */
    private function getFirstLastEventName(string $type, string $operation): string
    {
        $row = $this->mortalityQuery($type, $operation);

        if ($row) {
            $record = GedcomRecord::getInstance($row->id, $this->tree);

            if ($record) {
                return '<a href="' . e($record->url()) . '">' . $record->getFullName() . '</a>';
            }
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function firstBirthName(): string
    {
        return $this->getFirstLastEventName('BIRT', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastBirthName(): string
    {
        return $this->getFirstLastEventName('BIRT', 'MAX');
    }

    /**
     * @inheritDoc
     */
    public function firstDeathName(): string
    {
        return $this->getFirstLastEventName('DEAT', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastDeathName(): string
    {
        return $this->getFirstLastEventName('DEAT', 'MAX');
    }

    /**
     * @inheritDoc
     */
    public function firstMarriageName(): string
    {
        return $this->getFirstLastEventName('MARR', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastMarriageName(): string
    {
        return $this->getFirstLastEventName('MARR', 'MAX');
    }

    /**
     * @inheritDoc
     */
    public function firstDivorceName(): string
    {
        return $this->getFirstLastEventName('DIV', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastDivorceName(): string
    {
        return $this->getFirstLastEventName('DIV', 'MAX');
    }

    /**
     * Returns the formatted place of the first/last occuring event.
     *
     * @param string $type      The fact to query
     * @param string $operation The sorting operation
     *
     * @return string
     */
    private function getFirstLastEventPlace(string $type, string $operation): string
    {
        $row = $this->mortalityQuery($type, $operation);

        if ($row) {
            $record = GedcomRecord::getInstance($row->id, $this->tree);
            $fact   = null;

            if ($record) {
                $fact = $record->getFirstFact($row->fact);
            }

            if ($fact) {
                return FunctionsPrint::formatFactPlace($fact, true, true, true);
            }
        }

        return I18N::translate('Private');
    }

    /**
     * @inheritDoc
     */
    public function firstBirthPlace(): string
    {
        return $this->getFirstLastEventPlace('BIRT', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastBirthPlace(): string
    {
        return $this->getFirstLastEventPlace('BIRT', 'MAX');
    }

    /**
     * @inheritDoc
     */
    public function firstDeathPlace(): string
    {
        return $this->getFirstLastEventPlace('DEAT', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastDeathPlace(): string
    {
        return $this->getFirstLastEventPlace('DEAT', 'MAX');
    }

    /**
     * @inheritDoc
     */
    public function firstMarriagePlace(): string
    {
        return $this->getFirstLastEventPlace('MARR', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastMarriagePlace(): string
    {
        return $this->getFirstLastEventPlace('MARR', 'MAX');
    }

    /**
     * @inheritDoc
     */
    public function firstDivorcePlace(): string
    {
        return $this->getFirstLastEventPlace('DIV', 'MIN');
    }

    /**
     * @inheritDoc
     */
    public function lastDivorcePlace(): string
    {
        return $this->getFirstLastEventPlace('DIV', 'MAX');
    }
}

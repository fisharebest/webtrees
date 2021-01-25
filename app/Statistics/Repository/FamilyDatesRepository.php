<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\FamilyDatesRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use stdClass;

/**
 * A repository providing methods for family dates related statistics (birth, death, marriage, divorce).
 */
class FamilyDatesRepository implements FamilyDatesRepositoryInterface
{
    /**
     * Sorting directions.
     */
    private const SORT_MIN = 'MIN';
    private const SORT_MAX = 'MAX';

    /**
     * Event facts.
     */
    private const EVENT_BIRTH    = 'BIRT';
    private const EVENT_DEATH    = 'DEAT';
    private const EVENT_MARRIAGE = 'MARR';
    private const EVENT_DIVORCE  = 'DIV';

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
     * Returns the first/last event record for the given event fact.
     *
     * @param string $fact
     * @param string $operation
     *
     * @return stdClass|null
     */
    private function eventQuery(string $fact, string $operation): ?stdClass
    {
        return DB::table('dates')
            ->select(['d_gid as id', 'd_year as year', 'd_fact AS fact', 'd_type AS type'])
            ->where('d_file', '=', $this->tree->id())
            ->where('d_fact', '=', $fact)
            ->where('d_julianday1', '=', function (Builder $query) use ($operation, $fact): void {
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
        $row    = $this->eventQuery($type, $operation);
        $result = I18N::translate('This information is not available.');

        if ($row) {
            $record = Registry::gedcomRecordFactory()->make($row->id, $this->tree);

            if ($record && $record->canShow()) {
                $result = $record->formatList();
            } else {
                $result = I18N::translate('This information is private and cannot be shown.');
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function firstBirth(): string
    {
        return $this->getFirstLastEvent(self::EVENT_BIRTH, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastBirth(): string
    {
        return $this->getFirstLastEvent(self::EVENT_BIRTH, self::SORT_MAX);
    }

    /**
     * @return string
     */
    public function firstDeath(): string
    {
        return $this->getFirstLastEvent(self::EVENT_DEATH, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastDeath(): string
    {
        return $this->getFirstLastEvent(self::EVENT_DEATH, self::SORT_MAX);
    }

    /**
     * @return string
     */
    public function firstMarriage(): string
    {
        return $this->getFirstLastEvent(self::EVENT_MARRIAGE, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastMarriage(): string
    {
        return $this->getFirstLastEvent(self::EVENT_MARRIAGE, self::SORT_MAX);
    }

    /**
     * @return string
     */
    public function firstDivorce(): string
    {
        return $this->getFirstLastEvent(self::EVENT_DIVORCE, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastDivorce(): string
    {
        return $this->getFirstLastEvent(self::EVENT_DIVORCE, self::SORT_MAX);
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
        $row = $this->eventQuery($type, $operation);

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
     * @return string
     */
    public function firstBirthYear(): string
    {
        return $this->getFirstLastEventYear(self::EVENT_BIRTH, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastBirthYear(): string
    {
        return $this->getFirstLastEventYear(self::EVENT_BIRTH, self::SORT_MAX);
    }

    /**
     * @return string
     */
    public function firstDeathYear(): string
    {
        return $this->getFirstLastEventYear(self::EVENT_DEATH, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastDeathYear(): string
    {
        return $this->getFirstLastEventYear(self::EVENT_DEATH, self::SORT_MAX);
    }

    /**
     * @return string
     */
    public function firstMarriageYear(): string
    {
        return $this->getFirstLastEventYear(self::EVENT_MARRIAGE, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastMarriageYear(): string
    {
        return $this->getFirstLastEventYear(self::EVENT_MARRIAGE, self::SORT_MAX);
    }

    /**
     * @return string
     */
    public function firstDivorceYear(): string
    {
        return $this->getFirstLastEventYear(self::EVENT_DIVORCE, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastDivorceYear(): string
    {
        return $this->getFirstLastEventYear(self::EVENT_DIVORCE, self::SORT_MAX);
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
        $row = $this->eventQuery($type, $operation);

        if ($row) {
            $record = Registry::gedcomRecordFactory()->make($row->id, $this->tree);

            if ($record) {
                return '<a href="' . e($record->url()) . '">' . $record->fullName() . '</a>';
            }
        }

        return '';
    }

    /**
     * @return string
     */
    public function firstBirthName(): string
    {
        return $this->getFirstLastEventName(self::EVENT_BIRTH, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastBirthName(): string
    {
        return $this->getFirstLastEventName(self::EVENT_BIRTH, self::SORT_MAX);
    }

    /**
     * @return string
     */
    public function firstDeathName(): string
    {
        return $this->getFirstLastEventName(self::EVENT_DEATH, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastDeathName(): string
    {
        return $this->getFirstLastEventName(self::EVENT_DEATH, self::SORT_MAX);
    }

    /**
     * @return string
     */
    public function firstMarriageName(): string
    {
        return $this->getFirstLastEventName(self::EVENT_MARRIAGE, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastMarriageName(): string
    {
        return $this->getFirstLastEventName(self::EVENT_MARRIAGE, self::SORT_MAX);
    }

    /**
     * @return string
     */
    public function firstDivorceName(): string
    {
        return $this->getFirstLastEventName(self::EVENT_DIVORCE, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastDivorceName(): string
    {
        return $this->getFirstLastEventName(self::EVENT_DIVORCE, self::SORT_MAX);
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
        $row = $this->eventQuery($type, $operation);

        if ($row) {
            $record = Registry::gedcomRecordFactory()->make($row->id, $this->tree);
            $fact   = null;

            if ($record) {
                $fact = $record->facts([$row->fact])->first();
            }

            if ($fact instanceof Fact) {
                return FunctionsPrint::formatFactPlace($fact, true, true, true);
            }
        }

        return I18N::translate('This information is private and cannot be shown.');
    }

    /**
     * @return string
     */
    public function firstBirthPlace(): string
    {
        return $this->getFirstLastEventPlace(self::EVENT_BIRTH, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastBirthPlace(): string
    {
        return $this->getFirstLastEventPlace(self::EVENT_BIRTH, self::SORT_MAX);
    }

    /**
     * @return string
     */
    public function firstDeathPlace(): string
    {
        return $this->getFirstLastEventPlace(self::EVENT_DEATH, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastDeathPlace(): string
    {
        return $this->getFirstLastEventPlace(self::EVENT_DEATH, self::SORT_MAX);
    }

    /**
     * @return string
     */
    public function firstMarriagePlace(): string
    {
        return $this->getFirstLastEventPlace(self::EVENT_MARRIAGE, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastMarriagePlace(): string
    {
        return $this->getFirstLastEventPlace(self::EVENT_MARRIAGE, self::SORT_MAX);
    }

    /**
     * @return string
     */
    public function firstDivorcePlace(): string
    {
        return $this->getFirstLastEventPlace(self::EVENT_DIVORCE, self::SORT_MIN);
    }

    /**
     * @return string
     */
    public function lastDivorcePlace(): string
    {
        return $this->getFirstLastEventPlace(self::EVENT_DIVORCE, self::SORT_MAX);
    }
}

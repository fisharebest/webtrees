<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Report;

use Closure;
use Fisharebest\Database\Query\JoinOn;
use Fisharebest\Database\Query\Predicates;
use Fisharebest\Database\Query\QueryBuilder;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Comparators\FamilyComparator;
use Fisharebest\Webtrees\Comparators\GedcomRecordComparator;
use Fisharebest\Webtrees\Comparators\IndividualComparator;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\ChangeStatus;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;

use function addcslashes;
use function count;
use function end;
use function explode;
use function preg_match;
use function preg_replace;
use function str_replace;
use function str_starts_with;
use function strtr;
use function trim;
use function uasort;

/**
 * Builds record lists for the <List> element in report XML.
 *
 * Encapsulates the SQL query construction (with filter-to-SQL translation)
 * and the post-query PHP filtering and sorting that were previously inline
 * in ParserGenerate::listStartHandler().
 */
final class ListBuilder
{
    public function __construct(
        private readonly Tree $tree,
    ) {
    }

    /**
     * @param array<string,string> $attrs
     * @return array<GedcomRecord>
     */
    public function buildChangeList(
        array $attrs,
        string $sortby,
        string $gedrec,
        string $fact,
        string $desc,
        VariableTable $variables,
    ): array {
        $list = $this->queryPendingRecords($attrs, $gedrec, $fact, $desc, $variables);

        switch ($sortby) {
            case 'NAME':
                uasort($list, GedcomRecordComparator::byName(...));
                break;
            case 'CHAN':
                uasort($list, GedcomRecordComparator::byLastChange(...));
                break;
            default:
                break;
        }

        return $list;
    }

    /**
     * @param array<string,string> $attrs
     * @param Closure(string,bool):string $substitute
     * @return array<GedcomRecord>
     */
    public function buildIndividualList(
        array $attrs,
        string $sortby,
        Closure $substitute,
        string $gedrec,
        string $fact,
        string $desc,
        VariableTable $variables,
    ): array {
        $individuals = $this->queryIndividuals($attrs, $substitute, $gedrec, $fact, $desc, $variables);

        switch ($sortby) {
            case 'NAME':
                uasort($individuals, GedcomRecordComparator::byName(...));
                break;
            case 'CHAN':
                uasort($individuals, GedcomRecordComparator::byLastChange(...));
                break;
            case 'BIRT:DATE':
                uasort($individuals, IndividualComparator::byBirthDate(...));
                break;
            case 'DEAT:DATE':
                uasort($individuals, IndividualComparator::byDeathDate(...));
                break;
            default:
                break;
        }

        return $individuals;
    }

    /**
     * @param array<string,string> $attrs
     * @param Closure(string,bool):string $substitute
     * @return array<GedcomRecord>
     */
    public function buildFamilyList(
        array $attrs,
        string $sortby,
        Closure $substitute,
        string $gedrec,
        string $fact,
        string $desc,
        VariableTable $variables,
    ): array {
        $families = $this->queryFamilies($attrs, $sortby, $substitute, $gedrec, $fact, $desc, $variables);

        switch ($sortby) {
            case 'NAME':
                uasort($families, GedcomRecordComparator::byName(...));
                break;
            case 'CHAN':
                uasort($families, GedcomRecordComparator::byLastChange(...));
                break;
            case 'MARR:DATE':
                uasort($families, FamilyComparator::byMarriageDate(...));
                break;
            default:
                break;
        }

        return $families;
    }

    /**
     * Query pending changes (records awaiting approval).
     *
     * @param array<string,string> $attrs
     * @return array<GedcomRecord>
     */
    private function queryPendingRecords(
        array $attrs,
        string $gedrec,
        string $fact,
        string $desc,
        VariableTable $variables,
    ): array {
        $list = DB::query()
            ->select('*')
            ->from('change')
            ->whereInSubquery('change_id', DB::query()
                ->select(DB::max('change_id'))
                ->from('change')
                ->whereEqual('gedcom_id', $this->tree->id())
                ->whereEqual('status', ChangeStatus::Pending->value)
                ->groupBy('xref'))
            ->get(in: Collection::class)
            ->map(fn (object $row): GedcomRecord|null => Registry::gedcomRecordFactory()->make($row->xref, $this->tree, $row->new_gedcom !== '' ? $row->new_gedcom : $row->old_gedcom))
            ->filter()
            ->all();

        return $this->applyPhpFilters($list, $attrs, $gedrec, $fact, $desc, $variables);
    }

    /**
     * Query individuals with SQL-expressible filters applied at the database level.
     *
     * Filters that cannot be expressed in SQL are left in $attrs for later
     * PHP-side processing.
     *
     * @param array<string,string> $attrs
     * @param Closure(string,bool):string $substitute
     *
     * @return array<Individual>
     */
    private function queryIndividuals(
        array &$attrs,
        Closure $substitute,
        string $gedrec,
        string $fact,
        string $desc,
        VariableTable $variables,
    ): array {
        $query = DB::query()
            ->select('i_id AS xref', 'i_gedcom AS gedcom')
            ->distinct()
            ->from('individuals')
            ->whereEqual('i_file', $this->tree->id());

        foreach ($attrs as $attr => $value) {
            if (str_starts_with($attr, 'filter') && $value !== '') {
                $value = $substitute($value, false);

                if (preg_match('/^(\w+):DATE (LTE|GTE) (.+)$/', $value, $match)) {
                    $query = $query
                        ->join('dates AS ' . $attr, static fn (JoinOn $j): JoinOn => $j->on($attr . '.d_gid', 'individuals.i_id')->on($attr . '.d_file', 'individuals.i_file'))
                        ->whereEqual($attr . '.d_fact', $match[1]);

                    $date = new Date($match[3]);

                    if ($match[2] === 'LTE') {
                        $query = $query->whereLessThanOrEqual($attr . '.d_julianday2', $date->maximumJulianDay());
                    } else {
                        $query = $query->whereGreaterThanOrEqual($attr . '.d_julianday1', $date->minimumJulianDay());
                    }

                    unset($attrs[$attr]);
                } elseif (preg_match('/^NAME CONTAINS (.+)$/', $value, $match)) {
                    $query = $query->join('name AS ' . $attr, static fn (JoinOn $j): JoinOn => $j->on($attr . '.n_id', 'individuals.i_id')->on($attr . '.n_file', 'individuals.i_file'));
                    $names = explode(' ', $match[1]);
                    foreach ($names as $name) {
                        $query = $query->whereLike($attr . '.n_full', '%' . addcslashes($name, '\\%_') . '%');
                    }

                    unset($attrs[$attr]);
                } elseif (preg_match('/^LIKE \/(.+)\/$/', $value, $match)) {
                    $match[1] = str_replace('\n', "\n", $match[1]);
                    $query = $query->whereLike('i_gedcom', $match[1]);

                    unset($attrs[$attr]);
                } elseif (preg_match('/^(?:\w*):PLAC CONTAINS (.+)$/', $value, $match)) {
                    // Initial SQL filtering for performance; PHP filter still applies
                    $query = $query
                        ->join('placelinks AS ' . $attr . 'a', static fn (JoinOn $j): JoinOn => $j->on($attr . 'a.pl_file', 'individuals.i_file')->on($attr . 'a.pl_gid', 'individuals.i_id'))
                        ->join('places AS ' . $attr . 'b', static fn (JoinOn $j): JoinOn => $j->on($attr . 'b.p_file', $attr . 'a.pl_file')->on($attr . 'b.p_id', $attr . 'a.pl_p_id'))
                        ->whereLike($attr . 'b.p_place', '%' . addcslashes($match[1], '\\%_') . '%');
                } elseif (preg_match('/^(\w*):(\w+) CONTAINS (.+)$/', $value, $match)) {
                    // Initial SQL filtering for performance; PHP filter still applies
                    $match[3] = strtr($match[3], ['\\' => '\\\\', '%' => '\\%', '_' => '\\_', ' ' => '%']);
                    $like     = "%\n1 " . $match[1] . "%\n2 " . $match[2] . '%' . $match[3] . '%';
                    $query = $query->whereLike('i_gedcom', $like);
                } elseif (preg_match('/^(\w+) CONTAINS (.*)$/', $value, $match)) {
                    // Initial SQL filtering for performance; PHP filter still applies
                    $match[2] = strtr($match[2], ['\\' => '\\\\', '%' => '\\%', '_' => '\\_', ' ' => '%']);
                    $like     = "%\n1 " . $match[1] . '%' . $match[2] . '%';
                    $query = $query->whereLike('i_gedcom', $like);
                }
            }
        }

        $list = $query
            ->get(in: Collection::class)
            ->map(fn (object $row): Individual => Registry::individualFactory()->make($row->xref, $this->tree, $row->gedcom))
            ->filter()
            ->all();

        return $this->applyPhpFilters($list, $attrs, $gedrec, $fact, $desc, $variables);
    }

    /**
     * Query families with SQL-expressible filters applied at the database level.
     *
     * @param array<string,string> $attrs
     * @param Closure(string,bool):string $substitute
     *
     * @return array<Family>
     */
    private function queryFamilies(
        array &$attrs,
        string $sortby,
        Closure $substitute,
        string $gedrec,
        string $fact,
        string $desc,
        VariableTable $variables,
    ): array {
        $query = DB::query()
            ->select('f_id AS xref', 'f_gedcom AS gedcom')
            ->distinct()
            ->from('families')
            ->whereEqual('f_file', $this->tree->id());

        foreach ($attrs as $attr => $value) {
            if (str_starts_with($attr, 'filter') && $value !== '') {
                $value = $substitute($value, false);

                if (preg_match('/^(\w+):DATE (LTE|GTE) (.+)$/', $value, $match)) {
                    $query = $query
                        ->join('dates AS ' . $attr, static fn (JoinOn $j): JoinOn => $j->on($attr . '.d_gid', 'families.f_id')->on($attr . '.d_file', 'families.f_file'))
                        ->whereEqual($attr . '.d_fact', $match[1]);

                    $date = new Date($match[3]);

                    if ($match[2] === 'LTE') {
                        $query = $query->whereLessThanOrEqual($attr . '.d_julianday2', $date->maximumJulianDay());
                    } else {
                        $query = $query->whereGreaterThanOrEqual($attr . '.d_julianday1', $date->minimumJulianDay());
                    }

                    unset($attrs[$attr]);
                } elseif (preg_match('/^LIKE \/(.+)\/$/', $value, $match)) {
                    $match[1] = str_replace('\n', "\n", $match[1]);
                    $query = $query->whereLike('f_gedcom', $match[1]);

                    unset($attrs[$attr]);
                } elseif (preg_match('/^NAME CONTAINS (.*)$/', $value, $match)) {
                    if ($sortby === 'NAME' || $match[1] !== '') {
                        $query = $query
                            ->join('name AS ' . $attr, static fn (JoinOn $j): JoinOn => $j
                                ->on($attr . '.n_file', 'families.f_file')
                                ->onNested(static fn (JoinOn $j): JoinOn => $j
                                    ->on($attr . '.n_id', DB::column('families.f_husb'))
                                    ->orOn($attr . '.n_id', DB::column('families.f_wife'))));
                        if ($match[1] !== '') {
                            $names = explode(' ', $match[1]);
                            foreach ($names as $name) {
                                $query = $query->whereLike($attr . '.n_full', '%' . addcslashes($name, '\\%_') . '%');
                            }
                        }
                    }

                    unset($attrs[$attr]);
                } elseif (preg_match('/^(?:\w*):PLAC CONTAINS (.+)$/', $value, $match)) {
                    // Initial SQL filtering for performance; PHP filter still applies
                    $query = $query
                        ->join('placelinks AS ' . $attr . 'a', static fn (JoinOn $j): JoinOn => $j->on($attr . 'a.pl_file', 'families.f_file')->on($attr . 'a.pl_gid', 'families.f_id'))
                        ->join('places AS ' . $attr . 'b', static fn (JoinOn $j): JoinOn => $j->on($attr . 'b.p_file', $attr . 'a.pl_file')->on($attr . 'b.p_id', $attr . 'a.pl_p_id'))
                        ->whereLike($attr . 'b.p_place', '%' . addcslashes($match[1], '\\%_') . '%');
                } elseif (preg_match('/^(\w*):(\w+) CONTAINS (.+)$/', $value, $match)) {
                    // Initial SQL filtering for performance; PHP filter still applies
                    $match[3] = strtr($match[3], ['\\' => '\\\\', '%' => '\\%', '_' => '\\_', ' ' => '%']);
                    $like     = "%\n1 " . $match[1] . "%\n2 " . $match[2] . '%' . $match[3] . '%';
                    $query = $query->whereLike('f_gedcom', $like);
                } elseif (preg_match('/^(\w+) CONTAINS (.+)$/', $value, $match)) {
                    // Initial SQL filtering for performance; PHP filter still applies
                    $match[2] = strtr($match[2], ['\\' => '\\\\', '%' => '\\%', '_' => '\\_', ' ' => '%']);
                    $like     = "%\n1 " . $match[1] . '%' . $match[2] . '%';
                    $query = $query->whereLike('f_gedcom', $like);
                }
            }
        }

        $list = $query
            ->get(in: Collection::class)
            ->map(fn (object $row): Family => Registry::familyFactory()->make($row->xref, $this->tree, $row->gedcom))
            ->filter()
            ->all();

        return $this->applyPhpFilters($list, $attrs, $gedrec, $fact, $desc, $variables);
    }

    /**
     * Apply PHP-side filters that could not be expressed purely in SQL.
     *
     * Two categories of filter are processed:
     * - Regex-based CONTAINS filters (applied via preg_match on privatized GEDCOM)
     * - Comparison filters (GTE, LTE, equality) evaluated against extracted values
     *
     * @template T of GedcomRecord
     *
     * @param array<T>              $list
     * @param array<string,string>  $attrs
     * @param string                $gedrec
     * @param string                $fact
     * @param string                $desc
     * @param VariableTable         $variables
     *
     * @return array<T>
     */
    private function applyPhpFilters(array $list, array $attrs, string $gedrec, string $fact, string $desc, VariableTable $variables): array
    {
        $filters  = [];
        $filters2 = [];

        if (!isset($attrs['filter1']) || count($list) === 0) {
            return $list;
        }

        foreach ($attrs as $key => $value) {
            if (!preg_match("/filter(\d)/", $key)) {
                continue;
            }

            $condition = $value;
            if (preg_match("/@(\w+)/", $condition, $match)) {
                $id    = $match[1];
                $value = "''";
                if ($id === 'ID') {
                    if (preg_match('/0 @(.+)@/', $gedrec, $match)) {
                        $value = "'" . $match[1] . "'";
                    }
                } elseif ($id === 'fact') {
                    $value = "'" . $fact . "'";
                } elseif ($id === 'desc') {
                    $value = "'" . $desc . "'";
                } elseif (preg_match("/\d $id (.+)/", $gedrec, $match)) {
                    $value = "'" . str_replace('@', '', trim($match[1])) . "'";
                }
                $condition = preg_replace("/@$id/", $value, $condition);
            }

            if (preg_match("/([A-Z:]+)\s*([^\s]+)\s*(.+)/", $condition, $match)) {
                $tag  = trim($match[1]);
                $expr = trim($match[2]);
                $val  = trim($match[3]);
                if (preg_match("/\\$(\w+)/", $val, $match)) {
                    $val = $variables->get($match[1]);
                    $val = trim($val);
                }
                if ($val !== '') {
                    $searchstr = '';
                    $tags      = explode(':', $tag);

                    if (count($tags) > 1) {
                        $level = 1;
                        $t     = 'XXXX';
                        foreach ($tags as $current_tag) {
                            $t = $current_tag;
                            if ($searchstr !== '') {
                                $searchstr .= "[^\n]*(\n[2-9][^\n]*)*\n";
                            }
                            $searchstr .= $level . ' ' . $current_tag;
                            $level++;
                        }
                    } else {
                        $t         = $tag;
                        $searchstr = '1 ' . $tag;
                    }

                    switch ($expr) {
                        case 'CONTAINS':
                            if ($t === 'PLAC') {
                                $searchstr .= "[^\n]*[, ]*" . $val;
                            } else {
                                $searchstr .= "[^\n]*" . $val;
                            }
                            $filters[] = $searchstr;
                            break;
                        default:
                            $filters2[] = [
                                'tag'  => $tag,
                                'expr' => $expr,
                                'val'  => $val,
                            ];
                            break;
                    }
                }
            }
        }

        if ($filters !== []) {
            foreach ($list as $key => $record) {
                foreach ($filters as $filter) {
                    if (!preg_match('/' . $filter . '/i', $record->privatizeGedcom(Auth::accessLevel($this->tree)))) {
                        unset($list[$key]);
                        break;
                    }
                }
            }
        }

        if ($filters2 !== []) {
            $mylist = [];
            foreach ($list as $indi) {
                $key  = $indi->xref();
                $grec = $indi->privatizeGedcom(Auth::accessLevel($this->tree));
                $keep = true;
                foreach ($filters2 as $filter) {
                    if ($keep) {
                        $tag  = $filter['tag'];
                        $expr = $filter['expr'];
                        $val  = $filter['val'];
                        if ($val === "''") {
                            $val = '';
                        }
                        $tags = explode(':', $tag);
                        $t    = end($tags);
                        $v    = GedcomTextReader::getGedcomValue($tag, 1, $grec, $this->tree, $indi->tag());

                        switch ($expr) {
                            case 'GTE':
                                if ($t === 'DATE') {
                                    $date1 = new Date($v);
                                    $date2 = new Date($val);
                                    $keep  = (Date::compare($date1, $date2) >= 0);
                                } elseif ($val >= $v) {
                                    $keep = true;
                                }
                                break;
                            case 'LTE':
                                if ($t === 'DATE') {
                                    $date1 = new Date($v);
                                    $date2 = new Date($val);
                                    $keep  = (Date::compare($date1, $date2) <= 0);
                                } elseif ($val >= $v) {
                                    $keep = true;
                                }
                                break;
                            default:
                                if ($v === $val) {
                                    $keep = true;
                                } else {
                                    $keep = false;
                                }
                                break;
                        }
                    }
                }
                if ($keep) {
                    $mylist[$key] = $indi;
                }
            }
            $list = $mylist;
        }

        return $list;
    }
}

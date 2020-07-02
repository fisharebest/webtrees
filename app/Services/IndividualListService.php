<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;

/**
 * Find lists and counts of individuals with specified initials, names and surnames.
 */
class IndividualListService
{
    /** @var LocalizationService */
    private $localization_service;

    /** @var Tree */
    private $tree;

    /**
     * IndividualListService constructor.
     *
     * @param LocalizationService $localization_service
     * @param Tree                $tree
     */
    public function __construct(LocalizationService $localization_service, Tree $tree)
    {
        $this->localization_service = $localization_service;
        $this->tree                 = $tree;
    }

    /**
     * Restrict a query to individuals that are a spouse in a family record.
     *
     * @param bool    $fams
     * @param Builder $query
     */
    private function whereFamily(bool $fams, Builder $query): void
    {
        if ($fams) {
            $query->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_from', '=', 'n_id')
                    ->on('l_file', '=', 'n_file')
                    ->where('l_type', '=', 'FAMS');
            });
        }
    }

    /**
     * Restrict a query to include/exclude married names.
     *
     * @param bool    $marnm
     * @param Builder $query
     */
    private function whereMarriedName(bool $marnm, Builder $query): void
    {
        if (!$marnm) {
            $query->where('n_type', '<>', '_MARNM');
        }
    }

    /**
     * Get a list of initial surname letters.
     *
     * @param bool            $marnm if set, include married names
     * @param bool            $fams  if set, only consider individuals with FAMS records
     * @param LocaleInterface $locale
     *
     * @return int[]
     */
    public function surnameAlpha(bool $marnm, bool $fams, LocaleInterface $locale): array
    {
        $collation = $this->localization_service->collation($locale);

        $n_surn = $this->fieldWithCollation('n_surn', $collation);
        $alphas = [];

        $query = DB::table('name')->where('n_file', '=', $this->tree->id());

        $this->whereFamily($fams, $query);
        $this->whereMarriedName($marnm, $query);

        // Fetch all the letters in our alphabet, whether or not there
        // are any names beginning with that letter. It looks better to
        // show the full alphabet, rather than omitting rare letters such as X.
        foreach ($this->localization_service->alphabet($locale) as $letter) {
            $query2 = clone $query;

            $this->whereInitial($query2, 'n_surn', $letter, $locale);

            $alphas[$letter] = $query2->count();
        }

        // Now fetch initial letters that are not in our alphabet,
        // including "@" (for "@N.N.") and "" for no surname.
        $query2 = clone $query;
        foreach ($this->localization_service->alphabet($locale) as $n => $letter) {
            $query2->where($n_surn, 'NOT LIKE', $letter . '%');
        }

        $rows = $query2
            ->groupBy(['initial'])
            ->orderBy(new Expression("CASE initial WHEN '' THEN 1 ELSE 0 END"))
            ->orderBy(new Expression("CASE initial WHEN '@' THEN 1 ELSE 0 END"))
            ->orderBy('initial')
            ->pluck(new Expression('COUNT(*) AS aggregate'), new Expression('SUBSTR(n_surn, 1, 1) AS initial'));

        foreach ($rows as $alpha => $count) {
            $alphas[$alpha] = (int) $count;
        }

        $count_no_surname = $query->where('n_surn', '=', '')->count();

        if ($count_no_surname !== 0) {
            // Special code to indicate "no surname"
            $alphas[','] = $count_no_surname;
        }

        return $alphas;
    }

    /**
     * Get a list of initial given name letters for indilist.php and famlist.php
     *
     * @param string          $surn   if set, only consider people with this surname
     * @param string          $salpha if set, only consider surnames starting with this letter
     * @param bool            $marnm  if set, include married names
     * @param bool            $fams   if set, only consider individuals with FAMS records
     * @param LocaleInterface $locale
     *
     * @return int[]
     */
    public function givenAlpha(string $surn, string $salpha, bool $marnm, bool $fams, LocaleInterface $locale): array
    {
        $collation = $this->localization_service->collation($locale);

        $alphas = [];

        $query = DB::table('name')
            ->where('n_file', '=', $this->tree->id());

        $this->whereFamily($fams, $query);
        $this->whereMarriedName($marnm, $query);

        if ($surn !== '') {
            $n_surn = $this->fieldWithCollation('n_surn', $collation);
            $query->where($n_surn, '=', $surn);
        } elseif ($salpha === ',') {
            $query->where('n_surn', '=', '');
        } elseif ($salpha === '@') {
            $query->where('n_surn', '=', '@N.N.');
        } elseif ($salpha !== '') {
            $this->whereInitial($query, 'n_surn', $salpha, $locale);
        } else {
            // All surnames
            $query->whereNotIn('n_surn', ['', '@N.N.']);
        }

        // Fetch all the letters in our alphabet, whether or not there
        // are any names beginning with that letter. It looks better to
        // show the full alphabet, rather than omitting rare letters such as X
        foreach ($this->localization_service->alphabet($locale) as $letter) {
            $query2 = clone $query;

            $this->whereInitial($query2, 'n_givn', $letter, $locale);

            $alphas[$letter] = $query2->distinct()->count('n_id');
        }

        $rows = $query
            ->groupBy(['initial'])
            ->orderBy(new Expression("CASE initial WHEN '' THEN 1 ELSE 0 END"))
            ->orderBy(new Expression("CASE initial WHEN '@' THEN 1 ELSE 0 END"))
            ->orderBy('initial')
            ->pluck(new Expression('COUNT(*) AS aggregate'), new Expression('UPPER(SUBSTR(n_givn, 1, 1)) AS initial'));

        foreach ($rows as $alpha => $count) {
            $alphas[$alpha] = (int) $count;
        }

        return $alphas;
    }

    /**
     * Get a count of actual surnames and variants, based on a "root" surname.
     *
     * @param string          $surn   if set, only count people with this surname
     * @param string          $salpha if set, only consider surnames starting with this letter
     * @param bool            $marnm  if set, include married names
     * @param bool            $fams   if set, only consider individuals with FAMS records
     * @param LocaleInterface $locale
     *
     * @return int[][]
     */
    public function surnames(
        string $surn,
        string $salpha,
        bool $marnm,
        bool $fams,
        LocaleInterface $locale
    ): array {
        $collation = $this->localization_service->collation($locale);
        
        $query = DB::table('name')
            ->where('n_file', '=', $this->tree->id())
            ->select([
                new Expression('UPPER(n_surn /*! COLLATE ' . $collation . ' */) AS n_surn'),
                new Expression('n_surname /*! COLLATE utf8_bin */ AS n_surname'),
                new Expression('COUNT(*) AS total'),
            ]);

        $this->whereFamily($fams, $query);
        $this->whereMarriedName($marnm, $query);

        if ($surn !== '') {
            $query->where('n_surn', '=', $surn);
        } elseif ($salpha === ',') {
            $query->where('n_surn', '=', '');
        } elseif ($salpha === '@') {
            $query->where('n_surn', '=', '@N.N.');
        } elseif ($salpha !== '') {
            $this->whereInitial($query, 'n_surn', $salpha, $locale);
        } else {
            // All surnames
            $query->whereNotIn('n_surn', ['', '@N.N.']);
        }
        $query
            ->groupBy(['n_surn'])
            ->groupBy(['n_surname'])
            ->orderBy('n_surname');

        $list = [];

        foreach ($query->get() as $row) {
            $list[$row->n_surn][$row->n_surname] = (int) $row->total;
        }

        return $list;
    }

    /**
     * Fetch a list of individuals with specified names
     * To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
     * To search for names with no surnames, use $salpha=","
     *
     * @param string          $surn   if set, only fetch people with this surname
     * @param string          $salpha if set, only fetch surnames starting with this letter
     * @param string          $galpha if set, only fetch given names starting with this letter
     * @param bool            $marnm  if set, include married names
     * @param bool            $fams   if set, only fetch individuals with FAMS records
     * @param LocaleInterface $locale
     *
     * @return Individual[]
     */
    public function individuals(
        string $surn,
        string $salpha,
        string $galpha,
        bool $marnm,
        bool $fams,
        LocaleInterface $locale
    ): array {
        $collation = $this->localization_service->collation($locale);

        // Use specific collation for name fields.
        $n_givn = $this->fieldWithCollation('n_givn', $collation);
        $n_surn = $this->fieldWithCollation('n_surn', $collation);

        $query = DB::table('individuals')
            ->join('name', static function (JoinClause $join): void {
                $join
                    ->on('n_id', '=', 'i_id')
                    ->on('n_file', '=', 'i_file');
            })
            ->where('i_file', '=', $this->tree->id())
            ->select(['i_id AS xref', 'i_gedcom AS gedcom', 'n_givn', 'n_surn']);

        $this->whereFamily($fams, $query);
        $this->whereMarriedName($marnm, $query);

        if ($surn) {
            $query->where($n_surn, '=', $surn);
        } elseif ($salpha === ',') {
            $query->where($n_surn, '=', '');
        } elseif ($salpha === '@') {
            $query->where($n_surn, '=', '@N.N.');
        } elseif ($salpha) {
            $this->whereInitial($query, 'n_surn', $salpha, $locale);
        } else {
            // All surnames
            $query->whereNotIn($n_surn, ['', '@N.N.']);
        }
        if ($galpha) {
            $this->whereInitial($query, 'n_givn', $galpha, $locale);
        }

        $query
            ->orderBy(new Expression("CASE n_surn WHEN '@N.N.' THEN 1 ELSE 0 END"))
            ->orderBy($n_surn)
            ->orderBy(new Expression("CASE n_givn WHEN '@N.N.' THEN 1 ELSE 0 END"))
            ->orderBy($n_givn);

        $list = [];
        $rows = $query->get();

        foreach ($rows as $row) {
            $individual = Factory::individual()->make($row->xref, $this->tree, $row->gedcom);
            // The name from the database may be private - check the filtered list...
            foreach ($individual->getAllNames() as $n => $name) {
                if ($name['givn'] === $row->n_givn && $name['surn'] === $row->n_surn) {
                    $individual->setPrimaryName($n);
                    // We need to clone $individual, as we may have multiple references to the
                    // same individual in this list, and the "primary name" would otherwise
                    // be shared amongst all of them.
                    $list[] = clone $individual;
                    break;
                }
            }
        }

        return $list;
    }

    /**
     * Fetch a list of families with specified names
     * To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
     * To search for names with no surnames, use $salpha=","
     *
     * @param string          $surn   if set, only fetch people with this surname
     * @param string          $salpha if set, only fetch surnames starting with this letter
     * @param string          $galpha if set, only fetch given names starting with this letter
     * @param bool            $marnm  if set, include married names
     * @param LocaleInterface $locale
     *
     * @return Family[]
     */
    public function families($surn, $salpha, $galpha, $marnm, LocaleInterface $locale): array
    {
        $list = [];
        foreach ($this->individuals($surn, $salpha, $galpha, $marnm, true, $locale) as $indi) {
            foreach ($indi->spouseFamilies() as $family) {
                $list[$family->xref()] = $family;
            }
        }
        usort($list, GedcomRecord::nameComparator());

        return $list;
    }

    /**
     * Use MySQL-specific comments so we can run these queries on other RDBMS.
     *
     * @param string $field
     * @param string $collation
     *
     * @return Expression
     */
    private function fieldWithCollation(string $field, string $collation): Expression
    {
        return new Expression($field . ' /*! COLLATE ' . $collation . ' */');
    }

    /**
     * Modify a query to restrict a field to a given initial letter.
     * Take account of digraphs, equialent letters, etc.
     *
     * @param Builder         $query
     * @param string          $field
     * @param string          $letter
     * @param LocaleInterface $locale
     *
     * @return void
     */
    private function whereInitial(
        Builder $query,
        string $field,
        string $letter,
        LocaleInterface $locale
    ): void {
        $collation = $this->localization_service->collation($locale);

        // Use MySQL-specific comments so we can run these queries on other RDBMS.
        $field_with_collation = $this->fieldWithCollation($field, $collation);

        switch ($locale->languageTag()) {
            case 'cs':
                $this->whereInitialCzech($query, $field_with_collation, $letter);
                break;

            case 'da':
            case 'nb':
            case 'nn':
                $this->whereInitialNorwegian($query, $field_with_collation, $letter);
                break;

            case 'sv':
            case 'fi':
                $this->whereInitialSwedish($query, $field_with_collation, $letter);
                break;

            case 'hu':
                $this->whereInitialHungarian($query, $field_with_collation, $letter);
                break;

            case 'nl':
                $this->whereInitialDutch($query, $field_with_collation, $letter);
                break;

            default:
                $query->where($field_with_collation, 'LIKE', '\\' . $letter . '%');
        }
    }

    /**
     * @param Builder    $query
     * @param Expression $field
     * @param string     $letter
     */
    private function whereInitialCzech(Builder $query, Expression $field, string $letter): void
    {
        switch ($letter) {
            case 'C':
                $query->where($field, 'LIKE', 'C%')->where($field, 'NOT LIKE', 'CH%');
                break;

            default:
                $query->where($field, 'LIKE', '\\' . $letter . '%');
                break;
        }
    }

    /**
     * @param Builder    $query
     * @param Expression $field
     * @param string     $letter
     */
    private function whereInitialDutch(Builder $query, Expression $field, string $letter): void
    {
        switch ($letter) {
            case 'I':
                $query->where($field, 'LIKE', 'I%')->where($field, 'NOT LIKE', 'IJ%');
                break;

            default:
                $query->where($field, 'LIKE', '\\' . $letter . '%');
                break;
        }
    }

    /**
     * Hungarian has many digraphs and trigraphs, so exclude these from prefixes.
     *
     * @param Builder    $query
     * @param Expression $field
     * @param string     $letter
     */
    private function whereInitialHungarian(Builder $query, Expression $field, string $letter): void
    {
        switch ($letter) {
            case 'C':
                $query->where($field, 'LIKE', 'C%')->where($field, 'NOT LIKE', 'CS%');
                break;

            case 'D':
                $query->where($field, 'LIKE', 'D%')->where($field, 'NOT LIKE', 'DZ%');
                break;

            case 'DZ':
                $query->where($field, 'LIKE', 'DZ%')->where($field, 'NOT LIKE', 'DZS%');
                break;

            case 'G':
                $query->where($field, 'LIKE', 'G%')->where($field, 'NOT LIKE', 'GY%');
                break;

            case 'L':
                $query->where($field, 'LIKE', 'L%')->where($field, 'NOT LIKE', 'LY%');
                break;

            case 'N':
                $query->where($field, 'LIKE', 'N%')->where($field, 'NOT LIKE', 'NY%');
                break;

            case 'S':
                $query->where($field, 'LIKE', 'S%')->where($field, 'NOT LIKE', 'SZ%');
                break;

            case 'T':
                $query->where($field, 'LIKE', 'T%')->where($field, 'NOT LIKE', 'TY%');
                break;

            case 'Z':
                $query->where($field, 'LIKE', 'Z%')->where($field, 'NOT LIKE', 'ZS%');
                break;

            default:
                $query->where($field, 'LIKE', '\\' . $letter . '%');
                break;
        }
    }

    /**
     * In Norwegian and Danish, AA gets listed under Å, NOT A
     *
     * @param Builder    $query
     * @param Expression $field
     * @param string     $letter
     */
    private function whereInitialNorwegian(Builder $query, Expression $field, string $letter): void
    {
        switch ($letter) {
            case 'A':
                $query->where($field, 'LIKE', 'A%')->where($field, 'NOT LIKE', 'AA%');
                break;

            case 'Å':
                $query->where(static function (Builder $query) use ($field): void {
                    $query
                        ->where($field, 'LIKE', 'Å%')
                        ->orWhere($field, 'LIKE', 'AA%');
                });
                break;

            default:
                $query->where($field, 'LIKE', '\\' . $letter . '%');
                break;
        }
    }

    /**
     * In Swedish and Finnish, AA gets listed under A, NOT Å (even though Swedish collation says they should).
     *
     * @param Builder    $query
     * @param Expression $field
     * @param string     $letter
     */
    private function whereInitialSwedish(Builder $query, Expression $field, string $letter): void
    {
        switch ($letter) {
            case 'Å':
                $query
                    ->where($field, 'LIKE', 'Å%')
                    ->where($field, 'NOT LIKE', 'AA%');
                break;

            default:
                $query->where($field, 'LIKE', '\\' . $letter . '%');
                break;
        }
    }
}

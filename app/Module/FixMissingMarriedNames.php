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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

use function array_merge;
use function array_unique;
use function assert;
use function implode;
use function in_array;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function str_replace;
use function view;

/**
 * Class FixMissingMarriedNames
 */
class FixMissingMarriedNames extends AbstractModule implements ModuleDataFixInterface
{
    use ModuleDataFixTrait;

    /** @var DataFixService */
    private $data_fix_service;

    /**
     * FixMissingDeaths constructor.
     *
     * @param DataFixService $data_fix_service
     */
    public function __construct(DataFixService $data_fix_service)
    {
        $this->data_fix_service = $data_fix_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Add married names');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of a “Data fix” module */
        return I18N::translate('You can make it easier to search for married women by recording their married name. However not all women take their husband’s surname, so beware of introducing incorrect information into your database.');
    }

    /**
     * Options form.
     *
     * @param Tree $tree
     *
     * @return string
     */
    public function fixOptions(Tree $tree): string
    {
        $options = [
            'replace' => I18N::translate('Wife’s surname replaced by husband’s surname'),
            'add'     => I18N::translate('Wife’s maiden surname becomes new given name'),
        ];

        $selected = 'replace';

        return view('modules/fix-add-marr-names/options', [
            'options'  => $options,
            'selected' => $selected,
        ]);
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function individualsToFix(Tree $tree, array $params): ?Collection
    {
        // No DB querying possible?  Select all.
        return DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->where('i_sex', '=', 'F')
            ->pluck('i_id');
    }

    /**
     * Does a record need updating?
     *
     * @param GedcomRecord         $record
     * @param array<string,string> $params
     *
     * @return bool
     */
    public function doesRecordNeedUpdate(GedcomRecord $record, array $params): bool
    {
        assert($record instanceof Individual);
        $gedcom = $record->gedcom();

        return preg_match('/^1 SEX F/m', $gedcom) && preg_match('/^1 NAME /m', $gedcom) && $this->surnamesToAdd($record);
    }

    /**
     * Show the changes we would make
     *
     * @param GedcomRecord         $record
     * @param array<string,string> $params
     *
     * @return string
     */
    public function previewUpdate(GedcomRecord $record, array $params): string
    {
        $old = $record->gedcom();
        $new = $this->updateGedcom($record, $params);

        return $this->data_fix_service->gedcomDiff($record->tree(), $old, $new);
    }

    /**
     * Fix a record
     *
     * @param GedcomRecord         $record
     * @param array<string,string> $params
     *
     * @return void
     */
    public function updateRecord(GedcomRecord $record, array $params): void
    {
        $record->updateRecord($this->updateGedcom($record, $params), false);
    }

    /**
     * @param GedcomRecord         $record
     * @param array<string,string> $params
     *
     * @return string
     */
    private function updateGedcom(GedcomRecord $record, array $params): string
    {
        assert($record instanceof Individual);

        $old_gedcom = $record->gedcom();
        $tree       = $record->tree();

        $SURNAME_TRADITION = $tree->getPreference('SURNAME_TRADITION');

        preg_match('/^1 NAME (.*)/m', $old_gedcom, $match);
        $wife_name     = $match[1];
        $married_names = [];
        foreach ($this->surnamesToAdd($record) as $surname) {
            switch ($params['surname']) {
                case 'add':
                    $married_names[] = "\n2 _MARNM " . str_replace('/', '', $wife_name) . ' /' . $surname . '/';
                    break;
                case 'replace':
                    if ($SURNAME_TRADITION === 'polish') {
                        $surname = preg_replace([
                            '/ski$/',
                            '/cki$/',
                            '/dzki$/',
                        ], [
                            'ska',
                            'cka',
                            'dzka',
                        ], $surname);
                    }
                    $married_names[] = "\n2 _MARNM " . preg_replace('!/.*/!', '/' . $surname . '/', $wife_name);
                    break;
            }
        }

        return preg_replace('/(^1 NAME .*([\r\n]+[2-9].*)*)/m', '\\1' . implode('', $married_names), $old_gedcom, 1);
    }


    /**
     * Generate a list of married surnames that are not already present.
     *
     * @param Individual $record
     *
     * @return string[]
     */
    private function surnamesToAdd(Individual $record): array
    {
        $tree   = $record->tree();

        $wife_surnames    = $this->surnames($record);
        $husb_surnames    = [];
        $missing_surnames = [];

        foreach ($record->spouseFamilies() as $family) {
            $famrec = $family->gedcom();

            if (preg_match('/^1 MARR/m', $famrec) && preg_match('/^1 HUSB @(.+)@/m', $famrec, $hmatch)) {
                $spouse = Registry::individualFactory()->make($hmatch[1], $tree);

                if ($spouse instanceof Individual) {
                    $husb_surnames = array_unique(array_merge($husb_surnames, $this->surnames($spouse)));
                }
            }
        }

        foreach ($husb_surnames as $husb_surname) {
            if (!in_array($husb_surname, $wife_surnames, true)) {
                $missing_surnames[] = $husb_surname;
            }
        }

        return $missing_surnames;
    }

    /**
     * Extract a list of surnames from a GEDCOM record.
     *
     * @param GedcomRecord $record
     *
     * @return string[]
     */
    private function surnames(GedcomRecord $record): array
    {
        $gedcom = $record->gedcom();

        if (preg_match_all('/^(?:1 NAME|2 _MARNM) .*\/(.+)\//m', $gedcom, $match)) {
            return $match[1];
        }

        return [];
    }
}

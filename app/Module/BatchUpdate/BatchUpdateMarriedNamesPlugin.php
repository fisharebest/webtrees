<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\Module\BatchUpdate;

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class BatchUpdateMarriedNamesPlugin Batch Update plugin: add missing 2 _MARNM records
 */
class BatchUpdateMarriedNamesPlugin extends BatchUpdateBasePlugin
{
    /** @var string User option: add or replace husband’s surname */
    private $surname;

    /**
     * User-friendly name for this plugin.
     *
     * @return string
     */
    public function getName(): string
    {
        return I18N::translate('Add missing married names');
    }

    /**
     * Description / help-text for this plugin.
     *
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('You can make it easier to search for married women by recording their married name. However not all women take their husband’s surname, so beware of introducing incorrect information into your database.');
    }

    /**
     * Does this record need updating?
     *
     * @param GedcomRecord $record
     *
     * @return bool
     */
    public function doesRecordNeedUpdate(GedcomRecord $record): bool
    {
        $gedcom = $record->gedcom();

        return preg_match('/^1 SEX F/m', $gedcom) && preg_match('/^1 NAME /m', $gedcom) && $this->surnamesToAdd($record);
    }

    /**
     * Apply any updates to this record
     *
     * @param GedcomRecord $record
     *
     * @return string
     */
    public function updateRecord(GedcomRecord $record): string
    {
        $old_gedcom = $record->gedcom();
        $tree       = $record->tree();

        $SURNAME_TRADITION = $tree->getPreference('SURNAME_TRADITION');

        preg_match('/^1 NAME (.*)/m', $old_gedcom, $match);
        $wife_name     = $match[1];
        $married_names = [];
        foreach ($this->surnamesToAdd($record) as $surname) {
            switch ($this->surname) {
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

        $new_gedcom = preg_replace('/(^1 NAME .*([\r\n]+[2-9].*)*)/m', '\\1' . implode('', $married_names), $old_gedcom, 1);

        return $new_gedcom;
    }

    /**
     * Generate a list of married surnames that are not already present.
     *
     * @param GedcomRecord $record
     *
     * @return string[]
     */
    private function surnamesToAdd(GedcomRecord $record): array
    {
        $gedcom = $record->gedcom();
        $tree   = $record->tree();

        $wife_surnames    = $this->surnames($record);
        $husb_surnames    = [];
        $missing_surnames = [];

        preg_match_all('/^1 FAMS @(.+)@/m', $gedcom, $fmatch);

        foreach ($fmatch[1] as $famid) {
            $family = Family::getInstance($famid, $tree);
            $famrec = $family->gedcom();

            if (preg_match('/^1 MARR/m', $famrec) && preg_match('/^1 HUSB @(.+)@/m', $famrec, $hmatch)) {
                $spouse = Individual::getInstance($hmatch[1], $tree);

                if ($spouse instanceof Individual) {
                    $husb_surnames = array_unique(array_merge($husb_surnames, $this->surnames($spouse)));
                }
            }
        }

        foreach ($husb_surnames as $husb_surname) {
            if (!in_array($husb_surname, $wife_surnames)) {
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

    /**
     * Process the user-supplied options.
     *
     * @param ServerRequestInterface $request
     *
     * @return void
     */
    public function getOptions(ServerRequestInterface $request): void
    {
        parent::getOptions($request);

        $this->surname = $request->get('surname', 'replace');
    }

    /**
     * Generate a form to ask the user for options.
     *
     * @return string
     */
    public function getOptionsForm(): string
    {
        return
            '<div class="row form-group">' .
            '<label class="col-sm-3 col-form-label">' . I18N::translate('Surname option') . '</label>' .
            '<div class="col-sm-9">' .
            '<select class="form-control" name="surname">' .
            '<option value="replace" ' .
            ($this->surname == 'replace' ? 'selected' : '') .
            '">' . I18N::translate('Wife’s surname replaced by husband’s surname') . '</option><option value="add" ' .
            ($this->surname == 'add' ? 'selected' : '') .
            '">' . I18N::translate('Wife’s maiden surname becomes new given name') . '</option>' .
            '</select>' .
            '</div></div>' .
            parent::getOptionsForm();
    }
}

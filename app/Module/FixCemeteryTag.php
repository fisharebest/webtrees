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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

use function preg_match;

/**
 * Class FixCemeteryTag
 */
class FixCemeteryTag extends AbstractModule implements ModuleDataFixInterface
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
        return I18N::translate('Convert CEME tags to GEDCOM 5.5.1');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of a “Data fix” module */
        return I18N::translate('Replace cemetery tags with burial places.');
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
            'ADDR' => I18N::translate('Address'),
            'PLAC' => I18N::translate('Place'),
        ];

        $selected = 'ADDR';

        return view('modules/fix-ceme-tag/options', [
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
            ->where(static function (Builder $query): void {
                $query
                    ->where('i_gedcom', 'LIKE', "%\n2 CEME%")
                    ->orWhere('i_gedcom', 'LIKE', "%\n3 CEME%");
            })
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
        return $record->facts(['BURI'], false, null, true)
            ->filter(static function (Fact $fact): bool {
                return preg_match('/\n[23] CEME/', $fact->gedcom()) === 1;
            })
            ->isNotEmpty();
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
        $old = [];
        $new = [];

        foreach ($record->facts(['BURI'], false, null, true) as $fact) {
            $old[] = $fact->gedcom();
            $new[] = $this->updateGedcom($fact, $params);
        }

        $old = implode("\n", $old);
        $new = implode("\n", $new);

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
        foreach ($record->facts(['BURI'], false, null, true) as $fact) {
            $record->updateFact($fact->id(), $this->updateGedcom($fact, $params), false);
        }
    }

    /**
     * @param Fact                 $fact
     * @param array<string,string> $params
     *
     * @return string
     */
    private function updateGedcom(Fact $fact, array $params): string
    {
        $gedcom = $fact->gedcom();

        if (preg_match('/\n\d CEME ?(.+)(?:\n\d PLOT ?(.+))?/', $gedcom, $match)) {
            $ceme = $match[1];
            $plot = $match[2] ?? '';

            // Merge PLOT with CEME
            if ($plot !== '') {
                $ceme = $plot . ', ' . $ceme;
            }

            // Remove CEME/PLOT
            $gedcom = strtr($gedcom, [$match[0] => '']);

            // Add PLAC/ADDR
            $convert = $params['convert'];

            if (strpos($gedcom, "\n2 " . $convert . ' ') === false) {
                $gedcom .= "\n2 " . $convert . ' ' . $ceme;
            } else {
                $gedcom = strtr($gedcom, ["\n2 " . $convert . ' ' => "\n2 " . $convert . ' ' . $ceme . ', ']);
            }
        }

        return $gedcom;
    }
}

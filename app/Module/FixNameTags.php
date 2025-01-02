<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Elements\NameType;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

use function array_keys;
use function implode;
use function preg_match;
use function str_replace;

class FixNameTags extends AbstractModule implements ModuleDataFixInterface
{
    use ModuleDataFixTrait;

    // https://legacyfamilytree.se/WEB_US/user_defined_gedcom_tags.htm
    private const array CONVERT = [
        '_ADPN'  => NameType::VALUE_ADOPTED,
        '_AKA'   => NameType::VALUE_AKA,
        '_AKAN'  => NameType::VALUE_AKA,
        '_BIRN'  => NameType::VALUE_BIRTH,
        '_CENN'  => '', // Census name
        '_CURN'  => '', // Currently known as
        '_FARN'  => NameType::VALUE_ESTATE,
        '_FKAN'  => NameType::VALUE_AKA, // Formerly known as
        '_GERN'  => '', // German name
        '_HEB'   => '', // Hebrew name
        '_HEBN'  => '', // Hebrew name
        '_INDN'  => '', // Indian name
        '_MARNM' => NameType::VALUE_MARRIED,
        '_OTHN'  => NameType::VALUE_AKA, // Other name
        '_RELN'  => NameType::VALUE_RELIGIOUS,
        '_SHON'  => NameType::VALUE_AKA, // Short name
        '_SLDN'  => NameType::VALUE_AKA, // Soldier name
    ];

    private DataFixService $data_fix_service;

    /**
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
        return I18N::translate('Convert %s tags to GEDCOM 5.5.1', 'INDI:NAME:_XXX');
    }

    public function description(): string
    {
        /* I18N: Description of a “Data fix” module */
        return I18N::translate('Some genealogy software stores all names in a single name record, using custom tags such as _MARNM and _AKA. An alternative is to create a new name record for each name.');
    }

    /**
     * XREFs of media records that might need fixing.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<int,string>
     */
    public function individualsToFix(Tree $tree, array $params): Collection
    {
        return $this->individualsToFixQuery($tree, $params)
            ->where(static function (Builder $query): void {
                foreach (array_keys(self::CONVERT) as $tag) {
                    $query->orWhere('i_gedcom', 'LIKE', "%\n2 " . $tag . ' %');
                }
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
        $tags = implode('|', array_keys(self::CONVERT));

        return preg_match('/\n1 NAME.*(?:\n[2-9] .*)*\n2 (' . $tags . ')/', $record->gedcom()) === 1;
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
        $diffs = [];

        foreach ($record->facts(['NAME'], false, null, true) as $name) {
            $old = $name->gedcom();
            $new = $this->updateGedcom($name);

            if ($old !== $new) {
                $diffs[] = $this->data_fix_service->gedcomDiff($record->tree(), $old, $new);
            }
        }

        return implode('<hr>', $diffs);
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
        $names = $record->facts(['NAME'], false, null, true);

        foreach ($names as $name) {
            $old = $name->gedcom();
            $new = $this->updateGedcom($name);

            if ($old !== $new) {
                $record->updateFact($name->id(), $new, false);
            }
        }
    }

    /**
     * @param Fact $fact
     *
     * @return string
     */
    private function updateGedcom(Fact $fact): string
    {
        $gedcom    = $fact->gedcom();
        $converted = '';

        $tags = implode('|', array_keys(self::CONVERT));

        while (preg_match('/\n2 (' . $tags . ') (.+)((?:\n[3-9].*)*)/', $gedcom, $match)) {
            $type = self::CONVERT[$match[1]];
            if ($type !== '') {
                $type = "\n2 TYPE " . $type;
            }
            $gedcom = str_replace($match[0], '', $gedcom);

            $subtags = strtr($match[3], [
                "\n3" => "\n2",
                "\n4" => "\n3",
                "\n5" => "\n4",
                "\n6" => "\n5",
            ]);
            $converted .= "\n1 NAME " . $match[2] . $type . $subtags;
        }

        return $gedcom . $converted;
    }
}

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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

use function array_map;
use function explode;
use function implode;
use function strpos;
use function strtoupper;
use function trim;
use function view;

/**
 * EVENTS_RECORDED := {Size=1:90}
 * [<EVENT_ATTRIBUTE_TYPE> | <EVENTS_RECORDED>, <EVENT_ATTRIBUTE_TYPE>]
 * An enumeration of the different kinds of events that were recorded in a
 * particular source. Each enumeration is separated by a comma. Such as a
 * parish register of births, deaths, and marriages would be BIRT, DEAT, MARR.
 */
class EventsRecorded extends AbstractElement
{
    protected const SUBTAGS = [
        'DATE' => '0:1',
        'PLAC' => '0:1',
    ];

    protected const EVENTS_RECORDED = [
        'INDI:ADOP',
        'INDI:BAPM',
        'INDI:BARM',
        'INDI:BASM',
        'INDI:BIRT',
        'INDI:BLES',
        'INDI:BURI',
        'INDI:CAST',
        'INDI:CHR',
        'INDI:CENS',
        'INDI:CHRA',
        'INDI:CONF',
        'INDI:CREM',
        'INDI:DEAT',
        'INDI:DSCR',
        'INDI:EDUC',
        'INDI:EMIG',
        'INDI:FCOM',
        'INDI:GRAD',
        'INDI:IDNO',
        'INDI:IMMI',
        'INDI:NATI',
        'INDI:NATU',
        'INDI:NCHI',
        'INDI:NMR',
        'INDI:OCCU',
        'INDI:ORDN',
        'INDI:PROB',
        'INDI:PROP',
        'INDI:RELI',
        'INDI:RESI',
        'INDI:RETI',
        'INDI:SSN',
        'INDI:TITL',
        'INDI:WILL',
        'FAM:ANUL',
        'FAM:DIV',
        'FAM:DIVF',
        'FAM:ENGA',
        'FAM:MARB',
        'FAM:MARC',
        'FAM:MARL',
        'FAM:MARS',
        'FAM:MARR',
    ];

    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        $value = strtoupper(strtr(parent::canonical($value), [' ' => ',']));

        while (strpos($value, ',,') !== false) {
            $value = strtr($value, [',,' => ',']);
        }

        return trim($value, ',');
    }

    /**
     * An edit control for this data.
     *
     * @param string $id
     * @param string $name
     * @param string $value
     * @param Tree   $tree
     *
     * @return string
     */
    public function edit(string $id, string $name, string $value, Tree $tree): string
    {
        $factory = Registry::elementFactory();

        $options = Collection::make(self::EVENTS_RECORDED)
            ->mapWithKeys(static function (string $tag) use ($factory): array {
                return [explode(':', $tag)[1] => $factory->make($tag)->label()];
            })
            ->sort()
            ->all();

        $id2 = Uuid::uuid4()->toString();

        // Our form element name contains "[]", and multiple selections would create multiple values.
        $hidden = '<input type="hidden" id="' . e($id) . '" name="' . e($name) . '" value="' . e($value) . '" />';
        // Combine them into a single value.
        // The change event doesn't seem to fire for select2 controls, so use form.submit instead.
        $js = 'document.getElementById("' . $id2 . '").form.addEventListener("submit", function () { document.getElementById("' . $id . '").value = Array.from(document.getElementById("' . $id2 . '").selectedOptions).map(x => x.value).join(","); });';

        return view('components/select', [
            'class'    => 'select2',
            'name'     => '',
            'id'       => $id2,
            'options'  => $options,
            'selected' => explode(',', strtr($value, [' ' => ''])),
        ]) . $hidden . '<script>' . $js . '</script>';
    }

    /**
     * Display the value of this type of element.
     *
     * @param string $value
     * @param Tree   $tree
     *
     * @return string
     */
    public function value(string $value, Tree $tree): string
    {
        $tags = explode(',', $this->canonical($value));

        $events = array_map(static function (string $tag): string {
            foreach (['INDI', 'FAM'] as $record_type) {
                $element = Registry::elementFactory()->make($record_type . ':' . $tag);

                if (!$element instanceof UnknownElement) {
                    return $element->label();
                }
            }

            return e($tag);
        }, $tags);

        return implode(I18N::$list_separator, $events);
    }
}

<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Statistics\Google;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Statistics\Service\ColorService;
use Fisharebest\Webtrees\Tree;

use function count;
use function htmlspecialchars_decode;
use function strip_tags;
use function view;

/**
 * A chart showing the largest families (Families with most children).
 */
class ChartFamilyLargest
{
    private Tree $tree;

    private ColorService $color_service;

    /**
     * @param ColorService $color_service
     * @param Tree         $tree
     */
    public function __construct(ColorService $color_service, Tree $tree)
    {
        $this->tree          = $tree;
        $this->color_service = $color_service;
    }

    /**
     * Returns the related database records.
     *
     * @param int $total
     *
     * @return array<object>
     */
    private function queryRecords(int $total): array
    {
        $query = DB::table('families')
            ->select(['f_numchil AS total', 'f_id AS id'])
            ->where('f_file', '=', $this->tree->id())
            ->orderBy('total', 'desc')
            ->limit($total);

        return $query->get()->all();
    }

    /**
     * Create a chart of the largest families.
     *
     * @param string|null $color_from
     * @param string|null $color_to
     * @param int         $total
     *
     * @return string
     */
    public function chartLargestFamilies(
        string|null $color_from = null,
        string|null $color_to = null,
        int $total = 10
    ): string {
        $color_from ??= 'ffffff';
        $color_to ??= '84beff';

        $data = [
            [
                I18N::translate('Type'),
                I18N::translate('Total')
            ],
        ];

        foreach ($this->queryRecords($total) as $record) {
            $family = Registry::familyFactory()->make($record->id, $this->tree);

            if ($family instanceof Family && $family->canShow()) {
                $data[] = [
                    htmlspecialchars_decode(strip_tags($family->fullName())),
                    $record->total
                ];
            }
        }

        $colors = $this->color_service->interpolateRgb($color_from, $color_to, count($data) - 1);

        return view('statistics/other/charts/pie', [
            'title'    => I18N::translate('Largest families'),
            'data'     => $data,
            'colors'   => $colors,
            'language' => I18N::languageTag(),
        ]);
    }
}

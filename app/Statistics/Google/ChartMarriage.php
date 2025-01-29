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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Service\CenturyService;
use Fisharebest\Webtrees\Statistics\Service\ColorService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use stdClass;

use function count;
use function view;

/**
 * A chart showing the marriages by century.
 */
class ChartMarriage
{
    private Tree $tree;

    private CenturyService $century_service;

    private ColorService $color_service;

    public function __construct(CenturyService $century_service, ColorService $color_service, Tree $tree)
    {
        $this->tree            = $tree;
        $this->century_service = $century_service;
        $this->color_service   = $color_service;
    }

    /**
     * Returns the related database records.
     *
     * @return Collection<array-key,stdClass>
     */
    private function queryRecords(): Collection
    {
        return DB::table('dates')
            ->selectRaw('ROUND((d_year + 49) / 100, 0) AS century')
            ->selectRaw('COUNT(*) AS total')
            ->where('d_file', '=', $this->tree->id())
            ->where('d_year', '<>', 0)
            ->where('d_fact', '=', 'MARR')
            ->whereIn('d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->groupBy(['century'])
            ->orderBy('century')
            ->get()
            ->map(static function (object $row): object {
                return (object) [
                    'century' => (int) $row->century,
                    'total'   => (float) $row->total,
                ];
            });
    }

    public function chartMarriage(?string $color_from = null, ?string $color_to = null): string
    {
        $color_from ??= 'ffffff';
        $color_to ??= '84beff';

        $data = [
            [
                I18N::translate('Century'),
                I18N::translate('Total')
            ],
        ];

        foreach ($this->queryRecords() as $record) {
            $data[] = [
                $this->century_service->centuryName($record->century),
                $record->total
            ];
        }

        $colors = $this->color_service->interpolateRgb($color_from, $color_to, count($data) - 1);

        return view('statistics/other/charts/pie', [
            'title'    => I18N::translate('Marriages by century'),
            'data'     => $data,
            'colors'   => $colors,
            'language' => I18N::languageTag(),
        ]);
    }
}

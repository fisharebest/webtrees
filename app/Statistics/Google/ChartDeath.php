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

namespace Fisharebest\Webtrees\Statistics\Google;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Statistics\Service\CenturyService;
use Fisharebest\Webtrees\Statistics\Service\ColorService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use stdClass;

use function app;
use function count;

/**
 * A chart showing the deaths by century.
 */
class ChartDeath
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * @var ModuleThemeInterface
     */
    private $theme;

    /**
     * @var CenturyService
     */
    private $century_service;

    /**
     * @var ColorService
     */
    private $color_service;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree            = $tree;
        $this->theme           = app(ModuleThemeInterface::class);
        $this->century_service = new CenturyService();
        $this->color_service   = new ColorService();
    }

    /**
     * Returns the related database records.
     *
     * @return Collection<stdClass>
     */
    private function queryRecords(): Collection
    {
        return DB::table('dates')
            ->selectRaw('ROUND((d_year + 49) / 100) AS century')
            ->selectRaw('COUNT(*) AS total')
            ->where('d_file', '=', $this->tree->id())
            ->where('d_year', '<>', 0)
            ->where('d_fact', '=', 'DEAT')
            ->whereIn('d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->groupBy(['century'])
            ->orderBy('century')
            ->get()
            ->map(static function (stdClass $row): stdClass {
                return (object) [
                    'century' => (int) $row->century,
                    'total'   => (float) $row->total,
                ];
            });
    }

    /**
     * Create a chart of death places.
     *
     * @param string|null $color_from
     * @param string|null $color_to
     *
     * @return string
     */
    public function chartDeath(string $color_from = null, string $color_to = null): string
    {
        $chart_color1 = (string) $this->theme->parameter('distribution-chart-no-values');
        $chart_color2 = (string) $this->theme->parameter('distribution-chart-high-values');
        $color_from   = $color_from ?? $chart_color1;
        $color_to     = $color_to   ?? $chart_color2;

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
            'title'    => I18N::translate('Deaths by century'),
            'data'     => $data,
            'colors'   => $colors,
            'language' => I18N::languageTag(),
        ]);
    }
}

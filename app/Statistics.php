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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\SurnameTradition\PolishSurnameTradition;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;

use function app;
use function array_merge;
use function array_keys;
use function array_shift;
use function array_sum;
use function count;
use function e;
use function htmlspecialchars_decode;
use function implode;
use function in_array;
use function preg_replace;
use function round;
use function strip_tags;
use function strpos;
use function substr;
use function view;

/**
 * A selection of pre-formatted statistical queries.
 * These are primarily used for embedded keywords on HTML blocks, but
 * are also used elsewhere in the code.
 */
class Statistics
{
    private Tree $tree;

    private ModuleService $module_service;

    private UserService $user_service;

    private StatisticsData $data;

    private StatisticsFormat $format;

    public function __construct(
        ModuleService $module_service,
        Tree $tree,
        UserService $user_service
    ) {
        $this->module_service = $module_service;
        $this->user_service   = $user_service;
        $this->tree           = $tree;
        $this->data           = new StatisticsData($tree, $user_service);
        $this->format         = new StatisticsFormat();
    }

    public function ageBetweenSpousesFM(string $limit = '10'): string
    {
        return $this->data->ageBetweenSpousesFM((int) $limit);
    }

    public function ageBetweenSpousesFMList(string $limit = '10'): string
    {
        return $this->data->ageBetweenSpousesFMList((int) $limit);
    }

    public function ageBetweenSpousesMF(string $limit = '10'): string
    {
        return $this->data->ageBetweenSpousesMF((int) $limit);
    }

    public function ageBetweenSpousesMFList(string $limit = '10'): string
    {
        return $this->data->ageBetweenSpousesMFList((int) $limit);
    }

    public function averageChildren(): string
    {
        return I18N::number($this->data->averageChildrenPerFamily(), 2);
    }

    public function averageLifespan(string $show_years = '0'): string
    {
        $days = $this->data->averageLifespanDays('ALL');

        return $show_years ? $this->format->age($days) : I18N::number((int) ($days / 365.25));
    }

    public function averageLifespanFemale(string $show_years = '0'): string
    {
        $days = $this->data->averageLifespanDays('F');

        return $show_years ? $this->format->age($days) : I18N::number((int) ($days / 365.25));
    }

    public function averageLifespanMale(string $show_years = '0'): string
    {
        $days = $this->data->averageLifespanDays('M');

        return $show_years ? $this->format->age($days) : I18N::number((int) ($days / 365.25));
    }

    public function browserDate(): string
    {
        return Registry::timestampFactory()->now()->format(strtr(I18N::dateFormat(), ['%' => '']));
    }

    public function browserTime(): string
    {
        return Registry::timestampFactory()->now()->format(strtr(I18N::timeFormat(), ['%' => '']));
    }

    public function browserTimezone(): string
    {
        return Registry::timestampFactory()->now()->format('T');
    }

    /**
     * Create any of the other blocks.
     * Use as #callBlock:block_name#
     *
     * @param string ...$params
     */
    public function callBlock(string $block = '', ...$params): string
    {
        $module = $this->module_service
            ->findByComponent(ModuleBlockInterface::class, $this->tree, Auth::user())
            ->first(static fn (ModuleInterface $module): bool => $module->name() === $block && $module->name() !== 'html');

        if ($module === null) {
            return '';
        }

        // Build the config array
        $cfg = [];
        foreach ($params as $config) {
            $bits = explode('=', $config);

            if (count($bits) < 2) {
                continue;
            }

            $v       = array_shift($bits);
            $cfg[$v] = implode('=', $bits);
        }

        return $module->getBlock($this->tree, 0, ModuleBlockInterface::CONTEXT_EMBED, $cfg);
    }

    public function chartCommonGiven(string $color1 = 'ffffff', string $color2 = '84beff', string $limit = '7'): string
    {
        $given = $this->data->commonGivenNames('ALL', 1, (int) $limit)->all();

        if ($given === []) {
            return I18N::translate('This information is not available.');
        }

        $tot = 0;
        foreach ($given as $count) {
            $tot += $count;
        }

        $data = [
            [
                I18N::translate('Name'),
                I18N::translate('Total'),
            ],
        ];

        foreach ($given as $name => $count) {
            $data[] = [$name, $count];
        }

        $count_all_names = $this->data->commonGivenNames('ALL', 1, PHP_INT_MAX)->sum();

        $data[] = [
            I18N::translate('Other'),
            $count_all_names - $tot,
        ];

        $colors = $this->format->interpolateRgb($color1, $color2, count($data) - 1);

        return view('statistics/other/charts/pie', [
            'title'    => null,
            'data'     => $data,
            'colors'   => $colors,
            'language' => I18N::languageTag(),
        ]);
    }

    public function chartCommonSurnames(
        string $color1 = 'ffffff',
        string $color2 = '84beff',
        string $limit = '10'
    ): string {
        $all_surnames = $this->data->commonSurnames((int) $limit, 0, 'count');

        if ($all_surnames === []) {
            return I18N::translate('This information is not available.');
        }

        $surname_tradition = Registry::surnameTraditionFactory()
            ->make($this->tree->getPreference('SURNAME_TRADITION'));

        $tot = 0;
        foreach ($all_surnames as $surnames) {
            $tot += array_sum($surnames);
        }

        $data = [
            [
                I18N::translate('Name'),
                I18N::translate('Total')
            ],
        ];

        foreach ($all_surnames as $surns) {
            $max_name  = 0;
            $count_per = 0;
            $top_name  = '';

            foreach ($surns as $spfxsurn => $count) {
                $per = $count;
                $count_per += $per;

                // select most common surname from all variants
                if ($per > $max_name) {
                    $max_name = $per;
                    $top_name = $spfxsurn;
                }
            }

            if ($surname_tradition instanceof PolishSurnameTradition) {
                // Most common surname should be in male variant (Kowalski, not Kowalska)
                $top_name = preg_replace(
                    [
                        '/ska$/',
                        '/cka$/',
                        '/dzka$/',
                        '/żka$/',
                    ],
                    [
                        'ski',
                        'cki',
                        'dzki',
                        'żki',
                    ],
                    $top_name
                );
            }

            $data[] = [(string) $top_name, $count_per];
        }

        $data[] = [
            I18N::translate('Other'),
            $this->data->countIndividuals() - $tot
        ];

        $colors = $this->format->interpolateRgb($color1, $color2, count($data) - 1);

        return view('statistics/other/charts/pie', [
            'title'    => null,
            'data'     => $data,
            'colors'   => $colors,
            'language' => I18N::languageTag(),
        ]);
    }

    public function chartDistribution(
        string $chart_shows = 'world',
        string $chart_type = '',
        string $surname = ''
    ): string {
        return $this->data->chartDistribution($chart_shows, $chart_type, $surname);
    }

    public function chartFamsWithSources(string $color1 = 'c2dfff', string $color2 = '84beff'): string
    {
        $total_families              = $this->data->countFamilies();
        $total_families_with_sources = $this->data->countFamiliesWithSources();

        $data = [
            [I18N::translate('Without sources'), $total_families - $total_families_with_sources],
            [I18N::translate('With sources'), $total_families_with_sources],
        ];

        return $this->format->pieChart(
            $data,
            [$color1, $color2],
            I18N::translate('Families with sources'),
            I18N::translate('Type'),
            I18N::translate('Total'),
            true
        );
    }

    public function chartIndisWithSources(string $color1 = 'c2dfff', string $color2 = '84beff'): string
    {
        $total_individuals              = $this->data->countIndividuals();
        $total_individuals_with_sources = $this->data->countIndividualsWithSources();

        $data = [
            [I18N::translate('Without sources'), $total_individuals - $total_individuals_with_sources],
            [I18N::translate('With sources'), $total_individuals_with_sources],
        ];

        return $this->format->pieChart(
            $data,
            [$color1, $color2],
            I18N::translate('Individuals with sources'),
            I18N::translate('Type'),
            I18N::translate('Total'),
            true
        );
    }

    public function chartLargestFamilies(
        string $color1 = 'ffffff',
        string $color2 = '84beff',
        string $limit = '7'
    ): string {
        $data = DB::table('families')
            ->select(['f_numchil AS total', 'f_id AS id'])
            ->where('f_file', '=', $this->tree->id())
            ->orderBy('total', 'desc')
            ->limit((int) $limit)
            ->get()
            ->map(fn (object $row): array => [
                htmlspecialchars_decode(strip_tags(Registry::familyFactory()->make($row->id, $this->tree)->fullName())),
                (int) $row->total,
            ])
            ->all();

        return $this->format->pieChart(
            $data,
            $this->format->interpolateRgb($color1, $color2, count($data)),
            I18N::translate('Largest families'),
            I18N::translate('Family'),
            I18N::translate('Children')
        );
    }

    public function chartMedia(string $color1 = 'ffffff', string $color2 = '84beff'): string
    {
        $data = $this->data->countMediaByType();

        return $this->format->pieChart(
            $data,
            $this->format->interpolateRgb($color1, $color2, count($data)),
            I18N::translate('Media by type'),
            I18N::translate('Type'),
            I18N::translate('Total'),
        );
    }

    public function chartMortality(string $color_living = '#ffffff', string $color_dead = '#cccccc'): string
    {
        $total_living = $this->data->countIndividualsLiving();
        $total_dead   = $this->data->countIndividualsDeceased();

        $data = [
            [I18N::translate('Century'), I18N::translate('Total')],
        ];

        if ($total_living > 0 || $total_dead > 0) {
            $data[] = [I18N::translate('Living'), $total_living];
            $data[] = [I18N::translate('Dead'), $total_dead];
        }

        $colors = $this->format->interpolateRgb($color_living, $color_dead, count($data) - 1);

        return view('statistics/other/charts/pie', [
            'title'            => null,
            'data'             => $data,
            'colors'           => $colors,
            'labeledValueText' => 'percentage',
            'language'         => I18N::languageTag(),
        ]);
    }

    public function chartNoChildrenFamilies(): string
    {
        $data = [
            [
                I18N::translate('Century'),
                I18N::translate('Total'),
            ],
        ];

        $records = DB::table('families')
            ->selectRaw('ROUND((d_year + 49) / 100, 0) AS century')
            ->selectRaw('COUNT(*) AS total')
            ->join('dates', static function (JoinClause $join): void {
                $join->on('d_file', '=', 'f_file')
                    ->on('d_gid', '=', 'f_id');
            })
            ->where('f_file', '=', $this->tree->id())
            ->where('f_numchil', '=', 0)
            ->where('d_fact', '=', 'MARR')
            ->whereIn('d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->groupBy(['century'])
            ->orderBy('century')
            ->get()
            ->map(static fn (object $row): object => (object) [
                'century' => (int) $row->century,
                'total'   => (int) $row->total,
            ])
            ->all();

        $total = 0;

        foreach ($records as $record) {
            $total += $record->total;

            $data[] = [
                $this->format->century($record->century),
                $record->total,
            ];
        }

        $families_with_no_children = $this->data->countFamiliesWithNoChildren();

        if ($families_with_no_children - $total > 0) {
            $data[] = [
                I18N::translateContext('unknown century', 'Unknown'),
                $families_with_no_children - $total,
            ];
        }

        $chart_title   = I18N::translate('Number of families without children');
        $chart_options = [
            'title'    => $chart_title,
            'subtitle' => '',
            'legend'   => [
                'position' => 'none',
            ],
            'vAxis'    => [
                'title' => I18N::translate('Total families'),
            ],
            'hAxis'    => [
                'showTextEvery' => 1,
                'slantedText'   => false,
                'title'         => I18N::translate('Century'),
            ],
            'colors'   => [
                '#84beff',
            ],
        ];

        return view('statistics/other/charts/column', [
            'data'          => $data,
            'chart_options' => $chart_options,
            'chart_title'   => $chart_title,
            'language'      => I18N::languageTag(),
        ]);
    }

    public function chartSex(
        string $color_female = '#ffd1dc',
        string $color_male = '#84beff',
        string $color_unknown = '#777777',
        string $color_other = '#777777'
    ): string {
        $data = [
            [I18N::translate('Males'), $this->data->countIndividualsBySex('M')],
            [I18N::translate('Females'), $this->data->countIndividualsBySex('F')],
            [I18N::translate('Unknown'), $this->data->countIndividualsBySex('U')],
            [I18N::translate('Other'), $this->data->countIndividualsBySex('X')],
        ];

        return $this->format->pieChart(
            $data,
            [$color_male, $color_female, $color_unknown, $color_other],
            I18N::translate('Sex'),
            I18N::translate('Sex'),
            I18N::translate('Total'),
            true
        );
    }

    public function commonBirthPlacesList(string $limit = '10'): string
    {
        return view('statistics/other/top10-list', ['records' => $this->data->countPlacesForIndividuals('BIRT', (int) $limit)]);
    }

    public function commonCountriesList(string $limit = '10'): string
    {
        return view('statistics/other/top10-list', ['records' => $this->data->countCountries((int) $limit)]);
    }

    public function commonDeathPlacesList(string $limit = '10'): string
    {
        return view('statistics/other/top10-list', ['records' => $this->data->countPlacesForIndividuals('DEAT', (int) $limit)]);
    }

    public function commonGiven(string $threshold = '1', string $limit = '10'): string
    {
        return $this->data->commonGivenNames('ALL', (int) $threshold, (int) $limit)
            ->mapWithKeys(static fn (int $value, $key): array => [
                $key => '<bdi>' . e($key) . '</bdi>',
            ])
            ->implode(I18N::$list_separator);
    }

    public function commonGivenFemale(string $threshold = '1', string $limit = '10'): string
    {
        return $this->data->commonGivenNames('F', (int) $threshold, (int) $limit)
            ->mapWithKeys(static fn (int $value, $key): array => [
                $key => '<bdi>' . e($key) . '</bdi>',
            ])
            ->implode(I18N::$list_separator);
    }

    public function commonGivenFemaleList(string $threshold = '1', string $limit = '10'): string
    {
        return view('lists/given-names-list', [
            'given_names' => $this->data->commonGivenNames('F', (int) $threshold, (int) $limit)->all(),
            'show_totals' => false,
        ]);
    }

    public function commonGivenFemaleListTotals(string $threshold = '1', string $limit = '10'): string
    {
        return view('lists/given-names-list', [
            'given_names' => $this->data->commonGivenNames('F', (int) $threshold, (int) $limit)->all(),
            'show_totals' => true,
        ]);
    }

    public function commonGivenFemaleTable(string $threshold = '1', string $limit = '10'): string
    {
        return view('lists/given-names-table', [
            'given_names' => $this->data->commonGivenNames('F', (int) $threshold, (int) $limit)->all(),
            'order'       => [[1, 'desc']],
        ]);
    }

    public function commonGivenFemaleTotals(string $threshold = '1', string $limit = '10'): string
    {
        return $this->data->commonGivenNames('F', (int) $threshold, (int) $limit)
            ->mapWithKeys(static fn (int $value, $key): array => [
                $key => '<bdi>' . e($key) . '</bdi> (' . I18N::number($value) . ')',
            ])
            ->implode(I18N::$list_separator);
    }

    public function commonGivenList(string $threshold = '1', string $limit = '10'): string
    {
        return view('lists/given-names-list', [
            'given_names' => $this->data->commonGivenNames('ALL', (int) $threshold, (int) $limit)->all(),
            'show_totals' => false,
        ]);
    }

    public function commonGivenListTotals(string $threshold = '1', string $limit = '10'): string
    {
        return view('lists/given-names-list', [
            'given_names' => $this->data->commonGivenNames('ALL', (int) $threshold, (int) $limit)->all(),
            'show_totals' => true,
        ]);
    }

    public function commonGivenMale(string $threshold = '1', string $limit = '10'): string
    {
        return $this->data->commonGivenNames('M', (int) $threshold, (int) $limit)
            ->mapWithKeys(static fn (int $value, $key): array => [
                $key => '<bdi>' . e($key) . '</bdi>',
            ])
            ->implode(I18N::$list_separator);
    }

    public function commonGivenMaleList(string $threshold = '1', string $limit = '10'): string
    {
        return view('lists/given-names-list', [
            'given_names' => $this->data->commonGivenNames('M', (int) $threshold, (int) $limit)->all(),
            'show_totals' => false,
        ]);
    }

    public function commonGivenMaleListTotals(string $threshold = '1', string $limit = '10'): string
    {
        return view('lists/given-names-list', [
            'given_names' => $this->data->commonGivenNames('M', (int) $threshold, (int) $limit)->all(),
            'show_totals' => true,
        ]);
    }

    public function commonGivenMaleTable(string $threshold = '1', string $limit = '10'): string
    {
        return view('lists/given-names-table', [
            'given_names' => $this->data->commonGivenNames('M', (int) $threshold, (int) $limit)->all(),
            'order'       => [[1, 'desc']],
        ]);
    }

    public function commonGivenMaleTotals(string $threshold = '1', string $limit = '10'): string
    {
        return $this->data->commonGivenNames('M', (int) $threshold, (int) $limit)
            ->mapWithKeys(static fn (int $value, $key): array => [
                $key => '<bdi>' . e($key) . '</bdi> (' . I18N::number($value) . ')',
            ])
            ->implode(I18N::$list_separator);
    }

    public function commonGivenOther(string $threshold = '1', string $limit = '10'): string
    {
        return $this->data->commonGivenNames('X', (int) $threshold, (int) $limit)
            ->mapWithKeys(static fn (int $value, $key): array => [
                $key => '<bdi>' . e($key) . '</bdi>',
            ])
            ->implode(I18N::$list_separator);
    }

    public function commonGivenOtherList(string $threshold = '1', string $limit = '10'): string
    {
        return view('lists/given-names-list', [
            'given_names' => $this->data->commonGivenNames('X', (int) $threshold, (int) $limit)->all(),
            'show_totals' => false,
        ]);
    }

    public function commonGivenOtherListTotals(string $threshold = '1', string $limit = '10'): string
    {
        return view('lists/given-names-list', [
            'given_names' => $this->data->commonGivenNames('X', (int) $threshold, (int) $limit)->all(),
            'show_totals' => true,
        ]);
    }

    public function commonGivenOtherTable(string $threshold = '1', string $limit = '10'): string
    {
        return view('lists/given-names-table', [
            'given_names' => $this->data->commonGivenNames('X', (int) $threshold, (int) $limit)->all(),
            'order'       => [[1, 'desc']],
        ]);
    }

    public function commonGivenOtherTotals(string $threshold = '1', string $limit = '10'): string
    {
        return $this->data->commonGivenNames('X', (int) $threshold, (int) $limit)
            ->mapWithKeys(static fn (int $value, $key): array => [
                $key => '<bdi>' . e($key) . '</bdi> (' . I18N::number($value) . ')',
            ])
            ->implode(I18N::$list_separator);
    }

    public function commonGivenTable(string $threshold = '1', string $limit = '10'): string
    {
        return view('lists/given-names-table', [
            'given_names' => $this->data->commonGivenNames('ALL', (int) $threshold, (int) $limit)->all(),
            'order'       => [[1, 'desc']],
        ]);
    }

    public function commonGivenTotals(string $threshold = '1', string $limit = '10'): string
    {
        return $this->data->commonGivenNames('ALL', (int) $threshold, (int) $limit)
            ->mapWithKeys(static fn (int $value, $key): array => [
                $key => '<bdi>' . e($key) . '</bdi> (' . I18N::number($value) . ')',
            ])
            ->implode(I18N::$list_separator);
    }

    public function commonGivenUnknown(string $threshold = '1', string $limit = '10'): string
    {
        return $this->data->commonGivenNames('U', (int) $threshold, (int) $limit)
            ->mapWithKeys(static fn (int $value, $key): array => [
                $key => '<bdi>' . e($key) . '</bdi>',
            ])
            ->implode(I18N::$list_separator);
    }

    public function commonGivenUnknownList(string $threshold = '1', string $limit = '10'): string
    {
        return view('lists/given-names-list', [
            'given_names' => $this->data->commonGivenNames('U', (int) $threshold, (int) $limit)->all(),
            'show_totals' => false,
        ]);
    }

    public function commonGivenUnknownListTotals(string $threshold = '1', string $limit = '10'): string
    {
        return view('lists/given-names-list', [
            'given_names' => $this->data->commonGivenNames('U', (int) $threshold, (int) $limit)->all(),
            'show_totals' => true,
        ]);
    }

    public function commonGivenUnknownTable(string $threshold = '1', string $limit = '10'): string
    {
        return view('lists/given-names-table', [
            'given_names' => $this->data->commonGivenNames('U', (int) $threshold, (int) $limit)->all(),
            'order'       => [[1, 'desc']],
        ]);
    }

    public function commonGivenUnknownTotals(string $threshold = '1', string $limit = '10'): string
    {
        return $this->data->commonGivenNames('U', (int) $threshold, (int) $limit)
            ->mapWithKeys(static fn (int $value, $key): array => [
                $key => '<bdi>' . e($key) . '</bdi> (' . I18N::number($value) . ')',
            ])
            ->implode(I18N::$list_separator);
    }

    public function commonMarriagePlacesList(string $limit = '10'): string
    {
        return view('statistics/other/top10-list', ['records' => $this->data->countPlacesForFamilies('MARR', (int) $limit)]);
    }

    public function commonSurnames(string $threshold = '1', string $limit = '10', string $sort = 'alpha'): string
    {
        return $this->data->commonSurnamesQuery('nolist', false, (int) $threshold, (int) $limit, $sort);
    }

    public function commonSurnamesList(string $threshold = '1', string $limit = '10', string $sort = 'alpha'): string
    {
        return $this->data->commonSurnamesQuery('list', false, (int) $threshold, (int) $limit, $sort);
    }

    public function commonSurnamesListTotals(string $threshold = '1', string $limit = '10', string $sort = 'count'): string
    {
        return $this->data->commonSurnamesQuery('list', true, (int) $threshold, (int) $limit, $sort);
    }

    public function commonSurnamesTotals(string $threshold = '1', string $limit = '10', string $sort = 'count'): string
    {
        return $this->data->commonSurnamesQuery('nolist', true, (int) $threshold, (int) $limit, $sort);
    }

    public function contactGedcom(): string
    {
        $user_id = (int) $this->tree->getPreference('CONTACT_USER_ID');
        $user    = $this->user_service->find($user_id);

        if ($user instanceof User) {
            $request = app(ServerRequestInterface::class);

            return $this->user_service->contactLink($user, $request);
        }

        return '';
    }

    public function contactWebmaster(): string
    {
        $user_id = (int) $this->tree->getPreference('WEBMASTER_USER_ID');
        $user    = $this->user_service->find($user_id);

        if ($user instanceof User) {
            $request = app(ServerRequestInterface::class);

            return $this->user_service->contactLink($user, $request);
        }

        return '';
    }

    public function embedTags(string $text): string
    {
        return strtr($text, $this->getTags($text));
    }

    public function firstBirth(): string
    {
        return $this->data->firstEventRecord(['BIRT'], true);
    }

    public function firstBirthName(): string
    {
        return $this->data->firstEventName(['BIRT'], true);
    }

    public function firstBirthPlace(): string
    {
        return $this->data->firstEventPlace(['BIRT'], true);
    }

    public function firstBirthYear(): string
    {
        return $this->data->firstEventYear(['BIRT'], true);
    }

    public function firstDeath(): string
    {
        return $this->data->firstEventRecord(['DEAT'], true);
    }

    public function firstDeathName(): string
    {
        return $this->data->firstEventName(['DEAT'], true);
    }

    public function firstDeathPlace(): string
    {
        return $this->data->firstEventPlace(['DEAT'], true);
    }

    public function firstDeathYear(): string
    {
        return $this->data->firstEventYear(['DEAT'], true);
    }

    public function firstDivorce(): string
    {
        return $this->data->firstEventRecord(['DIV'], true);
    }

    public function firstDivorceName(): string
    {
        return $this->data->firstEventName(['DIV'], true);
    }

    public function firstDivorcePlace(): string
    {
        return $this->data->firstEventPlace(['DIV'], true);
    }

    public function firstDivorceYear(): string
    {
        return $this->data->firstEventYear(['DIV'], true);
    }

    public function firstEvent(): string
    {
        return $this->data->firstEventRecord([], true);
    }

    public function firstEventName(): string
    {
        return $this->data->firstEventName([], true);
    }

    public function firstEventPlace(): string
    {
        return $this->data->firstEventPlace([], true);
    }

    public function firstEventType(): string
    {
        return $this->data->firstEventType([], true);
    }

    public function firstEventYear(): string
    {
        return $this->data->firstEventYear([], true);
    }

    public function firstMarriage(): string
    {
        return $this->data->firstEventRecord(['MARR'], true);
    }

    public function firstMarriageName(): string
    {
        return $this->data->firstEventName(['MARR'], true);
    }

    public function firstMarriagePlace(): string
    {
        return $this->data->firstEventPlace(['MARR'], true);
    }

    public function firstMarriageYear(): string
    {
        return $this->data->firstEventYear(['MARR'], true);
    }

    public function gedcomCreatedSoftware(): string
    {
        $head = Registry::headerFactory()->make('HEAD', $this->tree);

        if ($head instanceof Header) {
            $sour = $head->facts(['SOUR'])->first();

            if ($sour instanceof Fact) {
                return $sour->attribute('NAME');
            }
        }

        return '';
    }

    public function gedcomCreatedVersion(): string
    {
        $head = Registry::headerFactory()->make('HEAD', $this->tree);

        if ($head instanceof Header) {
            $sour = $head->facts(['SOUR'])->first();

            if ($sour instanceof Fact) {
                $version = $sour->attribute('VERS');

                if (str_contains($version, 'Family Tree Maker ')) {
                    $p       = strpos($version, '(') + 1;
                    $p2      = strpos($version, ')');
                    $version = substr($version, $p, $p2 - $p);
                }

                // Fix EasyTree version
                if ($sour->value() === 'EasyTree') {
                    $version = substr($version, 1);
                }

                return $version;
            }
        }

        return '';
    }

    public function gedcomDate(): string
    {
        $head = Registry::headerFactory()->make('HEAD', $this->tree);

        if ($head instanceof Header) {
            $fact = $head->facts(['DATE'])->first();

            if ($fact instanceof Fact) {
                try {
                    return Registry::timestampFactory()->fromString($fact->value(), 'j M Y')->isoFormat('LL');
                } catch (InvalidArgumentException $ex) {
                    // HEAD:DATE invalid.
                }
            }
        }

        return '';
    }

    public function gedcomFavorites(): string
    {
        return $this->callBlock('gedcom_favorites');
    }

    public function gedcomFilename(): string
    {
        return $this->tree->name();
    }

    public function gedcomRootId(): string
    {
        return $this->tree->getPreference('PEDIGREE_ROOT_ID');
    }

    public function gedcomTitle(): string
    {
        return e($this->tree->title());
    }

    public function gedcomUpdated(): string
    {
        $row = DB::table('change')
            ->where('gedcom_id', '=', $this->tree->id())
            ->where('status', '=', 'accepted')
            ->orderBy('change_id', 'DESC')
            ->select(['change_time'])
            ->first();

        if ($row === null) {
            return $this->gedcomDate();
        }

        return Registry::timestampFactory()->fromString($row->change_time)->isoFormat('LL');
    }

    public function getAllTagsTable(): string
    {
        try {
            $class = new ReflectionClass($this);

            $public_methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

            $exclude = ['embedTags', 'getAllTagsTable'];

            $examples = Collection::make($public_methods)
                ->filter(static fn (ReflectionMethod $method): bool => !in_array($method->getName(), $exclude, true))
                ->filter(static fn(ReflectionMethod $method): bool => $method->getReturnType() instanceof ReflectionNamedType && $method->getReturnType()->getName() === 'string')
                ->sort(static fn (ReflectionMethod $x, ReflectionMethod $y): int => $x->getName() <=> $y->getName())
                ->map(function (ReflectionMethod $method): string {
                    $tag = $method->getName();

                    return '<dt>#' . $tag . '#</dt><dd>' . $this->$tag() . '</dd>';
                });

            return '<dl>' . $examples->implode('') . '</dl>';
        } catch (ReflectionException $ex) {
            return $ex->getMessage();
        }
    }

    public function getCommonSurname(): string
    {
        $top_surname = $this->data->commonSurnames(1, 0, 'count');

        return implode(I18N::$list_separator, array_keys(array_shift($top_surname) ?? []));
    }

    /**
     * @return array<string,string>
     */
    private function getTags(string $text): array
    {
        $tags    = [];
        $matches = [];

        preg_match_all('/#([^#\n]+)(?=#)/', $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $params = explode(':', $match[1]);
            $method = array_shift($params);

            if (method_exists($this, $method)) {
                $tags[$match[0] . '#'] = $this->$method(...$params);
            }
        }

        return $tags;
    }

    public function hitCount(): string
    {
        return $this->format->hitCount($this->data->countHits('index.php', 'gedcom:' . $this->tree->id()));
    }

    public function hitCountFam(string $xref = ''): string
    {
        return $this->format->hitCount($this->data->countHits('family.php', $xref));
    }

    public function hitCountIndi(string $xref = ''): string
    {
        return $this->format->hitCount($this->data->countHits('individual.php', $xref));
    }

    public function hitCountNote(string $xref = ''): string
    {
        return $this->format->hitCount($this->data->countHits('note.php', $xref));
    }

    public function hitCountObje(string $xref = ''): string
    {
        return $this->format->hitCount($this->data->countHits('mediaviewer.php', $xref));
    }

    public function hitCountRepo(string $xref = ''): string
    {
        return $this->format->hitCount($this->data->countHits('repo.php', $xref));
    }

    public function hitCountSour(string $xref = ''): string
    {
        return $this->format->hitCount($this->data->countHits('source.php', $xref));
    }

    public function hitCountUser(): string
    {
        return $this->format->hitCount($this->data->countHits('index.php', 'user:' . Auth::id()));
    }

    public function largestFamily(): string
    {
        $family = $this->data->familiesWithTheMostChildren(1)[0]->family ?? null;

        if ($family === null) {
            return $this->format->missing();
        }

        return $family->formatList();
    }

    public function largestFamilyName(): string
    {
        return $this->format->record($this->data->familiesWithTheMostChildren(1)[0]->family ?? null);
    }

    public function largestFamilySize(): string
    {
        return I18N::number($this->data->familiesWithTheMostChildren(1)[0]->children ?? 0);
    }

    public function lastBirth(): string
    {
        return $this->data->firstEventRecord(['BIRT'], false);
    }

    public function lastBirthName(): string
    {
        return $this->data->firstEventName(['BIRT'], false);
    }

    public function lastBirthPlace(): string
    {
        return $this->data->firstEventPlace(['BIRT'], false);
    }

    public function lastBirthYear(): string
    {
        return $this->data->firstEventYear(['BIRT'], false);
    }

    public function lastDeath(): string
    {
        return $this->data->firstEventRecord(['DEAT'], false);
    }

    public function lastDeathName(): string
    {
        return $this->data->firstEventName(['DEAT'], false);
    }

    public function lastDeathPlace(): string
    {
        return $this->data->firstEventPlace(['DEAT'], false);
    }

    public function lastDeathYear(): string
    {
        return $this->data->firstEventYear(['DEAT'], false);
    }

    public function lastDivorce(): string
    {
        return $this->data->firstEventRecord(['DIV'], false);
    }

    public function lastDivorceName(): string
    {
        return $this->data->firstEventName(['DIV'], true);
    }

    public function lastDivorcePlace(): string
    {
        return $this->data->firstEventPlace(['DIV'], true);
    }

    public function lastDivorceYear(): string
    {
        return $this->data->firstEventYear(['DIV'], true);
    }

    public function lastEvent(): string
    {
        return $this->data->firstEventRecord([], false);
    }

    public function lastEventName(): string
    {
        return $this->data->firstEventName([], false);
    }

    public function lastEventPlace(): string
    {
        return $this->data->firstEventPlace([], false);
    }

    public function lastEventType(): string
    {
        return $this->data->firstEventType([], false);
    }

    public function lastEventYear(): string
    {
        return $this->data->firstEventYear([], false);
    }

    public function lastMarriage(): string
    {
        return $this->data->firstEventRecord(['MARR'], false);
    }

    public function lastMarriageName(): string
    {
        return $this->data->firstEventName(['MARR'], false);
    }

    public function lastMarriagePlace(): string
    {
        return $this->data->firstEventPlace(['MARR'], false);
    }

    public function lastMarriageYear(): string
    {
        return $this->data->firstEventYear(['MARR'], false);
    }

    public function latestUserFullName(): string
    {
        $user = $this->user_service->find($this->data->latestUserId()) ?? Auth::user();

        return e($user->realName());
    }

    public function latestUserId(): string
    {
        $user = $this->user_service->find($this->data->latestUserId()) ?? Auth::user();

        return (string) $user->id();
    }

    public function latestUserLoggedin(?string $yes = null, ?string $no = null): string
    {
        if ($this->data->isUserLoggedIn($this->data->latestUserId())) {
            return $yes ?? I18N::translate('Yes');
        }

        return $no ?? I18N::translate('No');
    }

    public function latestUserName(): string
    {
        $user = $this->user_service->find($this->data->latestUserId()) ?? Auth::user();

        return e($user->userName());
    }

    public function latestUserRegDate(?string $format = null): string
    {
        $format    ??= I18N::dateFormat();
        $user      = $this->user_service->find($this->data->latestUserId()) ?? Auth::user();
        $timestamp = (int) $user->getPreference(UserInterface::PREF_TIMESTAMP_REGISTERED);

        if ($timestamp === 0) {
            return I18N::translate('Never');
        }

        return Registry::timestampFactory()->make($timestamp)->format(strtr($format, ['%' => '']));
    }

    public function latestUserRegTime(?string $format = null): string
    {
        $format    ??= I18N::timeFormat();
        $user      = $this->user_service->find($this->data->latestUserId()) ?? Auth::user();
        $timestamp = (int) $user->getPreference(UserInterface::PREF_TIMESTAMP_REGISTERED);

        if ($timestamp === 0) {
            return I18N::translate('Never');
        }

        return Registry::timestampFactory()->make($timestamp)->format(strtr($format, ['%' => '']));
    }

    public function longestLife(): string
    {
        $row = $this->data->longlifeQuery('ALL');

        if ($row === null) {
            return '';
        }

        return $row->individual->formatList();
    }

    public function longestLifeAge(): string
    {
        $row = $this->data->longlifeQuery('ALL');

        if ($row === null) {
            return '';
        }

        return I18N::number((int) ($row->days / 365.25));
    }

    public function longestLifeFemale(): string
    {
        $row = $this->data->longlifeQuery('F');

        if ($row === null) {
            return '';
        }

        return $row->individual->formatList();
    }

    public function longestLifeFemaleAge(): string
    {
        $row = $this->data->longlifeQuery('F');

        if ($row === null) {
            return '';
        }

        return I18N::number((int) ($row->days / 365.25));
    }

    public function longestLifeFemaleName(): string
    {
        return $this->format->record($this->data->longlifeQuery('F')->individual ?? null);
    }

    public function longestLifeMale(): string
    {
        $row = $this->data->longlifeQuery('M');

        if ($row === null) {
            return '';
        }

        return $row->individual->formatList();
    }

    public function longestLifeMaleAge(): string
    {
        $row = $this->data->longlifeQuery('M');

        if ($row === null) {
            return '';
        }

        return I18N::number((int) ($row->days / 365.25));
    }

    public function longestLifeMaleName(): string
    {
        return $this->format->record($this->data->longlifeQuery('M')->individual ?? null);
    }

    public function longestLifeName(): string
    {
        return $this->format->record($this->data->longlifeQuery('ALL')->individual ?? null);
    }

    public function minAgeOfMarriage(): string
    {
        return $this->data->ageOfMarriageQuery('age', 'ASC', 1);
    }

    public function minAgeOfMarriageFamilies(string $limit = '10'): string
    {
        return $this->data->ageOfMarriageQuery('nolist', 'ASC', (int) $limit);
    }

    public function minAgeOfMarriageFamiliesList(string $limit = '10'): string
    {
        return $this->data->ageOfMarriageQuery('list', 'ASC', (int) $limit);
    }

    public function minAgeOfMarriageFamily(): string
    {
        return $this->data->ageOfMarriageQuery('name', 'ASC', 1);
    }

    public function noChildrenFamilies(): string
    {
        return I18N::number($this->data->countFamiliesWithNoChildren());
    }

    public function noChildrenFamiliesList(string $type = 'list'): string
    {
        return $this->data->noChildrenFamiliesList($type);
    }

    public function oldestFather(): string
    {
        return $this->data->parentsQuery('full', 'DESC', 'M', false);
    }

    public function oldestFatherAge(string $show_years = '0'): string
    {
        return $this->data->parentsQuery('age', 'DESC', 'M', (bool) $show_years);
    }

    public function oldestFatherName(): string
    {
        return $this->data->parentsQuery('name', 'DESC', 'M', false);
    }

    public function oldestMarriageFemale(): string
    {
        return $this->data->marriageQuery('full', 'DESC', 'F', false);
    }

    public function oldestMarriageFemaleAge(string $show_years = '0'): string
    {
        return $this->data->marriageQuery('age', 'DESC', 'F', (bool) $show_years);
    }

    public function oldestMarriageFemaleName(): string
    {
        return $this->data->marriageQuery('name', 'DESC', 'F', false);
    }

    public function oldestMarriageMale(): string
    {
        return $this->data->marriageQuery('full', 'DESC', 'M', false);
    }

    public function oldestMarriageMaleAge(string $show_years = '0'): string
    {
        return $this->data->marriageQuery('age', 'DESC', 'M', (bool) $show_years);
    }

    public function oldestMarriageMaleName(): string
    {
        return $this->data->marriageQuery('name', 'DESC', 'M', false);
    }

    public function oldestMother(): string
    {
        return $this->data->parentsQuery('full', 'DESC', 'F', false);
    }

    public function oldestMotherAge(string $show_years = '0'): string
    {
        return $this->data->parentsQuery('age', 'DESC', 'F', (bool) $show_years);
    }

    public function oldestMotherName(): string
    {
        return $this->data->parentsQuery('name', 'DESC', 'F', false);
    }

    public function serverDate(): string
    {
        return Registry::timestampFactory()->now(new SiteUser())->format(strtr(I18N::dateFormat(), ['%' => '']));
    }

    public function serverTime(): string
    {
        return Registry::timestampFactory()->now(new SiteUser())->format(strtr(I18N::timeFormat(), ['%' => '']));
    }

    public function serverTime24(): string
    {
        return Registry::timestampFactory()->now(new SiteUser())->format('G:i');
    }

    public function serverTimezone(): string
    {
        return Registry::timestampFactory()->now(new SiteUser())->format('T');
    }

    public function statsAge(): string
    {
        $records = $this->data->statsAge();

        $out = [];

        foreach ($records as $record) {
            $out[$record->century][$record->sex] = $record->age;
        }

        $data = [
            [
                I18N::translate('Century'),
                I18N::translate('Males'),
                I18N::translate('Females'),
                I18N::translate('Average age'),
            ]
        ];

        foreach ($out as $century => $values) {
            $female_age  = $values['F'] ?? 0;
            $male_age    = $values['M'] ?? 0;
            $average_age = ($female_age + $male_age) / 2.0;

            $data[] = [
                $this->format->century($century),
                round($male_age, 1),
                round($female_age, 1),
                round($average_age, 1),
            ];
        }

        $chart_title   = I18N::translate('Average age related to death century');
        $chart_options = [
            'title' => $chart_title,
            'subtitle' => I18N::translate('Average age at death'),
            'vAxis' => [
                'title' => I18N::translate('Age'),
            ],
            'hAxis' => [
                'showTextEvery' => 1,
                'slantedText'   => false,
                'title'         => I18N::translate('Century'),
            ],
            'colors' => [
                '#84beff',
                '#ffd1dc',
                '#ff0000',
            ],
        ];

        return view('statistics/other/charts/combo', [
            'data'          => $data,
            'chart_options' => $chart_options,
            'chart_title'   => $chart_title,
            'language'      => I18N::languageTag(),
        ]);
    }

    public function statsBirth(string $color1 = 'ffffff', string $color2 = '84beff'): string
    {
        $data   = $this->data->countEventsByCentury('BIRT');
        $colors = $this->format->interpolateRgb($color1, $color2, count($data));

        return $this->format->pieChart(
            $data,
            $colors,
            I18N::translate('Births by century'),
            I18N::translate('Century'),
            I18N::translate('Total'),
        );
    }

    public function statsChildren(): string
    {
        $records = DB::table('families')
            ->selectRaw('AVG(f_numchil) AS total')
            ->selectRaw('ROUND((d_year + 49) / 100, 0) AS century')
            ->join('dates', static function (JoinClause $join): void {
                $join->on('d_file', '=', 'f_file')
                    ->on('d_gid', '=', 'f_id');
            })
            ->where('f_file', '=', $this->tree->id())
            ->where('d_julianday1', '<>', 0)
            ->where('d_fact', '=', 'MARR')
            ->whereIn('d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->groupBy(['century'])
            ->orderBy('century')
            ->get()
            ->map(static fn (object $row): object => (object) [
                'century' => (int) $row->century,
                'total'   => (float) $row->total,
            ]);

        $data = [
            [
                I18N::translate('Century'),
                I18N::translate('Average number'),
            ],
        ];

        foreach ($records as $record) {
            $data[] = [
                $this->format->century($record->century),
                round($record->total, 2),
            ];
        }

        $chart_title   = I18N::translate('Average number of children per family');
        $chart_options = [
            'title'    => $chart_title,
            'subtitle' => '',
            'legend'   => [
                'position' => 'none',
            ],
            'vAxis'    => [
                'title' => I18N::translate('Number of children'),
            ],
            'hAxis'    => [
                'showTextEvery' => 1,
                'slantedText'   => false,
                'title'         => I18N::translate('Century'),
            ],
            'colors'   => [
                '#84beff',
            ],
        ];

        return view('statistics/other/charts/column', [
            'data'          => $data,
            'chart_options' => $chart_options,
            'chart_title'   => $chart_title,
            'language'      => I18N::languageTag(),
        ]);
    }

    /**
     * @return array<object{f_numchil:int,total:int}>
     */
    public function statsChildrenQuery(int $year1 = 0, int $year2 = 0): array
    {
        return $this->data->statsChildrenQuery($year1, $year2);
    }

    public function statsDeath(string $color1 = 'ffffff', string $color2 = '84beff'): string
    {
        $data   = $this->data->countEventsByCentury('DEAT');
        $colors = $this->format->interpolateRgb($color1, $color2, count($data));

        return $this->format->pieChart(
            $data,
            $colors,
            I18N::translate('Births by century'),
            I18N::translate('Century'),
            I18N::translate('Total'),
        );
    }

    public function statsDiv(string $color1 = 'ffffff', string $color2 = '84beff'): string
    {
        $data   = $this->data->countEventsByCentury('DIV');
        $colors = $this->format->interpolateRgb($color1, $color2, count($data));

        return $this->format->pieChart(
            $data,
            $colors,
            I18N::translate('Divorces by century'),
            I18N::translate('Century'),
            I18N::translate('Total'),
        );
    }

    public function statsMarr(string $color1 = 'ffffff', string $color2 = '84beff'): string
    {
        $data   = $this->data->countEventsByCentury('MARR');
        $colors = $this->format->interpolateRgb($color1, $color2, count($data));

        return $this->format->pieChart(
            $data,
            $colors,
            I18N::translate('Marriages by century'),
            I18N::translate('Century'),
            I18N::translate('Total'),
        );
    }

    public function statsMarrAge(): string
    {
        $prefix = DB::connection()->getTablePrefix();

        $out = [];

        $male = DB::table('dates as married')
            ->select([
                new Expression('AVG(' . $prefix . 'married.d_julianday2 - ' . $prefix . 'birth.d_julianday1 - 182.5) / 365.25 AS age'),
                new Expression('ROUND((' . $prefix . 'married.d_year + 49) / 100, 0) AS century'),
                new Expression("'M' as sex"),
            ])
            ->join('families as fam', static function (JoinClause $join): void {
                $join->on('fam.f_id', '=', 'married.d_gid')
                    ->on('fam.f_file', '=', 'married.d_file');
            })
            ->join('dates as birth', static function (JoinClause $join): void {
                $join->on('birth.d_gid', '=', 'fam.f_husb')
                    ->on('birth.d_file', '=', 'fam.f_file');
            })
            ->whereIn('married.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->where('married.d_file', '=', $this->tree->id())
            ->where('married.d_fact', '=', 'MARR')
            ->where('married.d_julianday1', '>', new Expression($prefix . 'birth.d_julianday1'))
            ->whereIn('birth.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->where('birth.d_fact', '=', 'BIRT')
            ->where('birth.d_julianday1', '<>', 0)
            ->groupBy(['century', 'sex']);

        $female = DB::table('dates as married')
            ->select([
                new Expression('ROUND(AVG(' . $prefix . 'married.d_julianday2 - ' . $prefix . 'birth.d_julianday1 - 182.5) / 365.25, 1) AS age'),
                new Expression('ROUND((' . $prefix . 'married.d_year + 49) / 100, 0) AS century'),
                new Expression("'F' as sex"),
            ])
            ->join('families as fam', static function (JoinClause $join): void {
                $join->on('fam.f_id', '=', 'married.d_gid')
                    ->on('fam.f_file', '=', 'married.d_file');
            })
            ->join('dates as birth', static function (JoinClause $join): void {
                $join->on('birth.d_gid', '=', 'fam.f_wife')
                    ->on('birth.d_file', '=', 'fam.f_file');
            })
            ->whereIn('married.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->where('married.d_file', '=', $this->tree->id())
            ->where('married.d_fact', '=', 'MARR')
            ->where('married.d_julianday1', '>', new Expression($prefix . 'birth.d_julianday1'))
            ->whereIn('birth.d_type', ['@#DGREGORIAN@', '@#DJULIAN@'])
            ->where('birth.d_fact', '=', 'BIRT')
            ->where('birth.d_julianday1', '<>', 0)
            ->groupBy(['century', 'sex']);

        $records = $male->unionAll($female)
            ->orderBy('century')
            ->get()
            ->map(static fn (object $row): object => (object) [
                'age'     => (float) $row->age,
                'century' => (int) $row->century,
                'sex'     => $row->sex,
            ]);


        foreach ($records as $record) {
            $out[$record->century][$record->sex] = $record->age;
        }

        $data = [
            [
                I18N::translate('Century'),
                I18N::translate('Males'),
                I18N::translate('Females'),
                I18N::translate('Average age'),
            ],
        ];

        foreach ($out as $century => $values) {
            $female_age  = $values['F'] ?? 0;
            $male_age    = $values['M'] ?? 0;
            $average_age = ($female_age + $male_age) / 2.0;

            $data[] = [
                $this->format->century($century),
                round($male_age, 1),
                round($female_age, 1),
                round($average_age, 1),
            ];
        }

        $chart_title   = I18N::translate('Average age in century of marriage');
        $chart_options = [
            'title'    => $chart_title,
            'subtitle' => I18N::translate('Average age at marriage'),
            'vAxis'    => [
                'title' => I18N::translate('Age'),
            ],
            'hAxis'    => [
                'showTextEvery' => 1,
                'slantedText'   => false,
                'title'         => I18N::translate('Century'),
            ],
            'colors'   => [
                '#84beff',
                '#ffd1dc',
                '#ff0000',
            ],
        ];

        return view('statistics/other/charts/combo', [
            'data'          => $data,
            'chart_options' => $chart_options,
            'chart_title'   => $chart_title,
            'language'      => I18N::languageTag(),
        ]);
    }

    /**
     * @return array<object{f_id:string,d_gid:string,age:int}>
     */
    public function statsMarrAgeQuery(string $sex, int $year1 = 0, int $year2 = 0): array
    {
        return $this->data->statsMarrAgeQuery($sex, $year1, $year2);
    }

    public function topAgeBetweenSiblings(): string
    {
        return $this->data->topAgeBetweenSiblings();
    }

    public function topAgeBetweenSiblingsFullName(): string
    {
        return $this->data->topAgeBetweenSiblingsFullName();
    }

    public function topAgeBetweenSiblingsList(string $limit = '10', string $one = '0'): string
    {
        return $this->data->topAgeBetweenSiblingsList((int) $limit, (bool) $one);
    }

    public function topAgeBetweenSiblingsName(): string
    {
        $row = $this->data->maximumAgeBetweenSiblings(1)[0] ?? null;

        if ($row === null) {
            return $this->format->missing();
        }

        return
            $this->format->record($row->child2) . ' ' .
            I18N::translate('and') . ' ' .
            $this->format->record($row->child1) . ' ' .
            '<a href="' . e($row->family->url()) . '">[' . I18N::translate('View this family') . ']</a>';
    }

    public function topAgeOfMarriage(): string
    {
        return $this->data->ageOfMarriageQuery('age', 'DESC', 1);
    }

    public function topAgeOfMarriageFamilies(string $limit = '10'): string
    {
        return $this->data->ageOfMarriageQuery('nolist', 'DESC', (int) $limit);
    }

    public function topAgeOfMarriageFamiliesList(string $limit = '10'): string
    {
        return $this->data->ageOfMarriageQuery('list', 'DESC', (int) $limit);
    }

    public function topAgeOfMarriageFamily(): string
    {
        return $this->data->ageOfMarriageQuery('name', 'DESC', 1);
    }

    public function topTenLargestFamily(string $limit = '10'): string
    {
        return $this->data->topTenLargestFamily((int) $limit);
    }

    public function topTenLargestFamilyList(string $limit = '10'): string
    {
        return $this->data->topTenLargestFamilyList((int) $limit);
    }

    public function topTenLargestGrandFamily(string $limit = '10'): string
    {
        return $this->data->topTenLargestGrandFamily((int) $limit);
    }

    public function topTenLargestGrandFamilyList(string $limit = '10'): string
    {
        return $this->data->topTenLargestGrandFamilyList((int) $limit);
    }

    public function topTenOldest(string $limit = '10'): string
    {
        $records = $this->data->topTenOldestQuery('ALL', (int) $limit)
            ->map(fn (object $row): array => [
                'person' => $row->individual,
                'age'    => $this->format->age($row->days),
            ])
            ->all();

        return view('statistics/individuals/top10-nolist', [
            'records' => $records,
        ]);
    }

    public function topTenOldestAlive(string $limit = '10'): string
    {
        if (!Auth::isMember($this->tree)) {
            return I18N::translate('This information is private and cannot be shown.');
        }

        $records = $this->data->topTenOldestAliveQuery('ALL', (int) $limit)
            ->map(fn (Individual $individual): array => [
                'person' => $individual,
                'age'    => $this->format->age(Registry::timestampFactory()->now()->julianDay() - $individual->getBirthDate()->minimumJulianDay()),
            ])
            ->all();

        return view('statistics/individuals/top10-nolist', [
            'records' => $records,
        ]);
    }

    public function topTenOldestFemale(string $limit = '10'): string
    {
        $records = $this->data->topTenOldestQuery('F', (int) $limit)
            ->map(fn (object $row): array => [
                'person' => $row->individual,
                'age'    => $this->format->age($row->days),
            ])
            ->all();

        return view('statistics/individuals/top10-nolist', [
            'records' => $records,
        ]);
    }

    public function topTenOldestFemaleAlive(string $limit = '10'): string
    {
        if (!Auth::isMember($this->tree)) {
            return I18N::translate('This information is private and cannot be shown.');
        }

        $records = $this->data->topTenOldestAliveQuery('F', (int) $limit)
            ->map(fn (Individual $individual): array => [
                'person' => $individual,
                'age'    => $this->format->age(Registry::timestampFactory()->now()->julianDay() - $individual->getBirthDate()->minimumJulianDay()),
            ])
            ->all();

        return view('statistics/individuals/top10-nolist', [
            'records' => $records,
        ]);
    }

    public function topTenOldestFemaleList(string $limit = '10'): string
    {
        $records = $this->data->topTenOldestQuery('F', (int) $limit)
            ->map(fn (object $row): array => [
                'person' => $row->individual,
                'age'    => $this->format->age($row->days),
            ])
            ->all();

        return view('statistics/individuals/top10-list', [
            'records' => $records,
        ]);
    }

    public function topTenOldestFemaleListAlive(string $limit = '10'): string
    {
        if (!Auth::isMember($this->tree)) {
            return I18N::translate('This information is private and cannot be shown.');
        }

        $records = $this->data->topTenOldestAliveQuery('F', (int) $limit)
            ->map(fn (Individual $individual): array => [
                'person' => $individual,
                'age'    => $this->format->age(Registry::timestampFactory()->now()->julianDay() - $individual->getBirthDate()->minimumJulianDay()),
            ])
            ->all();

        return view('statistics/individuals/top10-list', [
            'records' => $records,
        ]);
    }

    public function topTenOldestList(string $limit = '10'): string
    {
        $records = $this->data->topTenOldestQuery('ALL', (int) $limit)
            ->map(fn (object $row): array => [
                'person' => $row->individual,
                'age'    => $this->format->age($row->days),
            ])
            ->all();

        return view('statistics/individuals/top10-list', [
            'records' => $records,
        ]);
    }

    public function topTenOldestListAlive(string $limit = '10'): string
    {
        if (!Auth::isMember($this->tree)) {
            return I18N::translate('This information is private and cannot be shown.');
        }

        $records = $this->data->topTenOldestAliveQuery('ALL', (int) $limit)
            ->map(fn (Individual $individual): array => [
                'person' => $individual,
                'age'    => $this->format->age(Registry::timestampFactory()->now()->julianDay() - $individual->getBirthDate()->minimumJulianDay()),
            ])
            ->all();

        return view('statistics/individuals/top10-list', [
            'records' => $records,
        ]);
    }

    public function topTenOldestMale(string $limit = '10'): string
    {
        $records = $this->data->topTenOldestQuery('M', (int) $limit)
            ->map(fn (object $row): array => [
                'person' => $row->individual,
                'age'    => $this->format->age($row->days),
            ])
            ->all();

        return view('statistics/individuals/top10-nolist', [
            'records' => $records,
        ]);
    }

    public function topTenOldestMaleAlive(string $limit = '10'): string
    {
        if (!Auth::isMember($this->tree)) {
            return I18N::translate('This information is private and cannot be shown.');
        }

        $records = $this->data->topTenOldestAliveQuery('M', (int) $limit)
            ->map(fn (Individual $individual): array => [
                'person' => $individual,
                'age'    => $this->format->age(Registry::timestampFactory()->now()->julianDay() - $individual->getBirthDate()->minimumJulianDay()),
            ])
            ->all();

        return view('statistics/individuals/top10-nolist', [
            'records' => $records,
        ]);
    }

    public function topTenOldestMaleList(string $limit = '10'): string
    {
        $records = $this->data->topTenOldestQuery('M', (int) $limit)
            ->map(fn (object $row): array => [
                'person' => $row->individual,
                'age'    => $this->format->age($row->days),
            ])
            ->all();

        return view('statistics/individuals/top10-list', [
            'records' => $records,
        ]);
    }

    public function topTenOldestMaleListAlive(string $limit = '10'): string
    {
        if (!Auth::isMember($this->tree)) {
            return I18N::translate('This information is private and cannot be shown.');
        }

        $records = $this->data->topTenOldestAliveQuery('M', (int) $limit)
            ->map(fn (Individual $individual): array => [
                'person' => $individual,
                'age'    => $this->format->age(Registry::timestampFactory()->now()->julianDay() - $individual->getBirthDate()->minimumJulianDay()),
            ])
            ->all();

        return view('statistics/individuals/top10-list', [
            'records' => $records,
        ]);
    }

    public function totalAdmins(): string
    {
        return I18N::number($this->user_service->administrators()->count());
    }

    public function totalBirths(): string
    {
        return I18N::number($this->data->countIndividualsWithEvents(['BIRT']));
    }

    public function totalChildren(): string
    {
        return I18N::number($this->data->countChildren());
    }

    public function totalDeaths(): string
    {
        return I18N::number($this->data->countIndividualsWithEvents(['DEAT']));
    }

    public function totalDeceased(): string
    {
        return I18N::number($this->data->countIndividualsDeceased());
    }

    public function totalDeceasedPercentage(): string
    {
        return $this->format->percentage(
            $this->data->countIndividualsDeceased(),
            $this->data->countIndividuals()
        );
    }

    public function totalDivorces(): string
    {
        return I18N::number($this->data->countFamiliesWithEvents(['DIV']));
    }

    public function totalEvents(): string
    {
        return I18N::number($this->data->countOtherEvents(['CHAN']));
    }

    public function totalEventsBirth(): string
    {
        return I18N::number($this->data->countAllEvents(Gedcom::BIRTH_EVENTS));
    }

    public function totalEventsDeath(): string
    {
        return I18N::number($this->data->countAllEvents(Gedcom::DEATH_EVENTS));
    }

    public function totalEventsDivorce(): string
    {
        return I18N::number($this->data->countAllEvents(Gedcom::DIVORCE_EVENTS));
    }

    public function totalEventsMarriage(): string
    {
        return I18N::number($this->data->countAllEvents(Gedcom::MARRIAGE_EVENTS));
    }

    public function totalEventsOther(): string
    {
        return I18N::number($this->data->countOtherEvents(array_merge(
            ['CHAN'],
            Gedcom::BIRTH_EVENTS,
            Gedcom::DEATH_EVENTS,
            Gedcom::MARRIAGE_EVENTS,
            Gedcom::DIVORCE_EVENTS,
        )));
    }

    public function totalFamilies(): string
    {
        return I18N::number($this->data->countFamilies());
    }

    public function totalFamiliesPercentage(): string
    {
        return $this->format->percentage(
            $this->data->countFamilies(),
            $this->data->countAllRecords()
        );
    }

    public function totalFamsWithSources(): string
    {
        return I18N::number($this->data->countFamiliesWithSources());
    }

    public function totalFamsWithSourcesPercentage(): string
    {
        return $this->format->percentage(
            $this->data->countFamiliesWithSources(),
            $this->data->countFamilies()
        );
    }

    public function totalGedcomFavorites(): string
    {
        return I18N::number($this->data->countTreeFavorites());
    }

    public function totalGivennames(string ...$names): string
    {
        return I18N::number($this->data->countGivenNames($names));
    }

    public function totalIndisWithSources(): string
    {
        return I18N::number($this->data->countIndividualsWithSources());
    }

    public function totalIndisWithSourcesPercentage(): string
    {
        return $this->format->percentage(
            $this->data->countIndividualsWithSources(),
            $this->data->countIndividuals()
        );
    }

    public function totalIndividuals(): string
    {
        return I18N::number($this->data->countIndividuals());
    }

    public function totalIndividualsPercentage(): string
    {
        return $this->format->percentage(
            $this->data->countIndividuals(),
            $this->data->countAllRecords()
        );
    }

    public function totalLiving(): string
    {
        return I18N::number($this->data->countIndividualsLiving());
    }

    public function totalLivingPercentage(): string
    {
        return $this->format->percentage(
            $this->data->countIndividualsLiving(),
            $this->data->countIndividuals()
        );
    }

    public function totalMarriages(): string
    {
        return I18N::number($this->data->countFamiliesWithEvents(['MARR']));
    }

    public function totalMarriedFemales(): string
    {
        return I18N::number($this->data->countMarriedFemales());
    }

    public function totalMarriedMales(): string
    {
        return I18N::number($this->data->countMarriedMales());
    }

    public function totalMedia(): string
    {
        return I18N::number($this->data->countMedia());
    }

    public function totalMediaAudio(): string
    {
        return I18N::number($this->data->countMedia('audio'));
    }

    public function totalMediaBook(): string
    {
        return I18N::number($this->data->countMedia('book'));
    }

    public function totalMediaCard(): string
    {
        return I18N::number($this->data->countMedia('card'));
    }

    public function totalMediaCertificate(): string
    {
        return I18N::number($this->data->countMedia('certificate'));
    }

    public function totalMediaCoatOfArms(): string
    {
        return I18N::number($this->data->countMedia('coat'));
    }

    public function totalMediaDocument(): string
    {
        return I18N::number($this->data->countMedia('document'));
    }

    public function totalMediaElectronic(): string
    {
        return I18N::number($this->data->countMedia('electronic'));
    }

    public function totalMediaFiche(): string
    {
        return I18N::number($this->data->countMedia('fiche'));
    }

    public function totalMediaFilm(): string
    {
        return I18N::number($this->data->countMedia('film'));
    }

    public function totalMediaMagazine(): string
    {
        return I18N::number($this->data->countMedia('magazine'));
    }

    public function totalMediaManuscript(): string
    {
        return I18N::number($this->data->countMedia('manuscript'));
    }

    public function totalMediaMap(): string
    {
        return I18N::number($this->data->countMedia('map'));
    }

    public function totalMediaNewspaper(): string
    {
        return I18N::number($this->data->countMedia('newspaper'));
    }

    public function totalMediaOther(): string
    {
        return I18N::number($this->data->countMedia('other'));
    }

    public function totalMediaPainting(): string
    {
        return I18N::number($this->data->countMedia('painting'));
    }

    public function totalMediaPhoto(): string
    {
        return I18N::number($this->data->countMedia('photo'));
    }

    public function totalMediaTombstone(): string
    {
        return I18N::number($this->data->countMedia('tombstone'));
    }

    public function totalMediaUnknown(): string
    {
        return I18N::number($this->data->countMedia(''));
    }

    public function totalMediaVideo(): string
    {
        return I18N::number($this->data->countMedia('video'));
    }

    public function totalNonAdmins(): string
    {
        return I18N::number($this->user_service->all()->count() - $this->user_service->administrators()->count());
    }

    public function totalNotes(): string
    {
        return I18N::number($this->data->countNotes());
    }

    public function totalNotesPercentage(): string
    {
        return $this->format->percentage(
            $this->data->countNotes(),
            $this->data->countAllRecords()
        );
    }

    public function totalPlaces(): string
    {
        return I18N::number($this->data->countAllPlaces());
    }

    public function totalRecords(): string
    {
        return I18N::number($this->data->countAllRecords());
    }

    public function totalRepositories(): string
    {
        return I18N::number($this->data->countRepositories());
    }

    public function totalRepositoriesPercentage(): string
    {
        return $this->format->percentage(
            $this->data->countRepositories(),
            $this->data->countAllRecords()
        );
    }

    public function totalSexFemales(): string
    {
        return I18N::number($this->data->countIndividualsBySex('F'));
    }

    public function totalSexFemalesPercentage(): string
    {
        return $this->format->percentage(
            $this->data->countIndividualsBySex('F'),
            $this->data->countIndividuals()
        );
    }

    public function totalSexMales(): string
    {
        return I18N::number($this->data->countIndividualsBySex('M'));
    }

    public function totalSexMalesPercentage(): string
    {
        return $this->format->percentage(
            $this->data->countIndividualsBySex('M'),
            $this->data->countIndividuals()
        );
    }

    public function totalSexOther(): string
    {
        return I18N::number($this->data->countIndividualsBySex('X'));
    }

    public function totalSexOtherPercentage(): string
    {
        return $this->format->percentage(
            $this->data->countIndividualsBySex('X'),
            $this->data->countIndividuals()
        );
    }

    public function totalSexUnknown(): string
    {
        return I18N::number($this->data->countIndividualsBySex('U'));
    }

    public function totalSexUnknownPercentage(): string
    {
        return $this->format->percentage(
            $this->data->countIndividualsBySex('U'),
            $this->data->countIndividuals()
        );
    }

    public function totalSources(): string
    {
        return I18N::number($this->data->countSources());
    }

    public function totalSourcesPercentage(): string
    {
        return $this->format->percentage(
            $this->data->countSources(),
            $this->data->countAllRecords()
        );
    }

    public function totalSurnames(string ...$names): string
    {
        return I18N::number($this->data->countSurnames($names));
    }

    public function totalTreeNews(): string
    {
        return I18N::number($this->data->countTreeNews());
    }

    public function totalUserFavorites(): string
    {
        return I18N::number($this->data->countUserfavorites());
    }

    public function totalUserJournal(): string
    {
        return I18N::number($this->data->countUserJournal());
    }

    public function totalUserMessages(): string
    {
        return I18N::number($this->data->countUserMessages());
    }

    public function totalUsers(): string
    {
        return I18N::number($this->user_service->all()->count());
    }

    public function userFavorites(): string
    {
        return $this->callBlock('user_favorites');
    }

    public function userFullName(): string
    {
        return Auth::check() ? '<bdi>' . e(Auth::user()->realName()) . '</bdi>' : '';
    }

    public function userId(): string
    {
        return (string) Auth::id();
    }

    public function userName(string $visitor_text = ''): string
    {
        if (Auth::check()) {
            return e(Auth::user()->userName());
        }

        if ($visitor_text === '') {
            return I18N::translate('Visitor');
        }

        return e($visitor_text);
    }

    public function usersLoggedIn(): string
    {
        return $this->data->usersLoggedIn();
    }

    public function usersLoggedInList(): string
    {
        return $this->data->usersLoggedInList();
    }

    public function webtreesVersion(): string
    {
        return Webtrees::VERSION;
    }

    public function youngestFather(): string
    {
        return $this->data->parentsQuery('full', 'ASC', 'M', false);
    }

    public function youngestFatherAge(string $show_years = '0'): string
    {
        return $this->data->parentsQuery('age', 'ASC', 'M', (bool) $show_years);
    }

    public function youngestFatherName(): string
    {
        return $this->data->parentsQuery('name', 'ASC', 'M', false);
    }

    public function youngestMarriageFemale(): string
    {
        return $this->data->marriageQuery('full', 'ASC', 'F', false);
    }

    public function youngestMarriageFemaleAge(string $show_years = '0'): string
    {
        return $this->data->marriageQuery('age', 'ASC', 'F', (bool) $show_years);
    }

    public function youngestMarriageFemaleName(): string
    {
        return $this->data->marriageQuery('name', 'ASC', 'F', false);
    }

    public function youngestMarriageMale(): string
    {
        return $this->data->marriageQuery('full', 'ASC', 'M', false);
    }

    public function youngestMarriageMaleAge(string $show_years = '0'): string
    {
        return $this->data->marriageQuery('age', 'ASC', 'M', (bool) $show_years);
    }

    public function youngestMarriageMaleName(): string
    {
        return $this->data->marriageQuery('name', 'ASC', 'M', false);
    }

    public function youngestMother(): string
    {
        return $this->data->parentsQuery('full', 'ASC', 'F', false);
    }

    public function youngestMotherAge(string $show_years = '0'): string
    {
        return $this->data->parentsQuery('age', 'ASC', 'F', (bool) $show_years);
    }

    public function youngestMotherName(): string
    {
        return $this->data->parentsQuery('name', 'ASC', 'F', false);
    }
}

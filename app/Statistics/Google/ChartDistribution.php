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
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\IndividualRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Service\CountryService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;

use function preg_match;
use function preg_quote;
use function view;

/**
 * A chart showing the distribution of different events on a map.
 */
class ChartDistribution
{
    private Tree $tree;

    private CountryService $country_service;

    private IndividualRepositoryInterface $individual_repository;

    /**
     * @var array<string>
     */
    private array $country_to_iso3166;

    /**
     * @param Tree                          $tree
     * @param CountryService                $country_service
     * @param IndividualRepositoryInterface $individual_repository
     */
    public function __construct(
        Tree $tree,
        CountryService $country_service,
        IndividualRepositoryInterface $individual_repository
    ) {
        $this->tree                  = $tree;
        $this->country_service       = $country_service;
        $this->individual_repository = $individual_repository;

        // Get the country names for each language
        $this->country_to_iso3166 = $this->getIso3166Countries();
    }

    /**
     * Returns the country names for each language.
     *
     * @return array<string>
     */
    private function getIso3166Countries(): array
    {
        // Get the country names for each language
        $country_to_iso3166 = [];

        $current_language = I18N::languageTag();

        foreach (I18N::activeLocales() as $locale) {
            I18N::init($locale->languageTag());

            $countries = $this->country_service->getAllCountries();

            foreach ($this->country_service->iso3166() as $three => $two) {
                $country_to_iso3166[$three]             = $two;
                $country_to_iso3166[$countries[$three]] = $two;
            }
        }

        I18N::init($current_language);

        return $country_to_iso3166;
    }

    /**
     * Returns the data structure required by google geochart.
     *
     * @param array<int> $places
     *
     * @return array<int,array<int|string|array<string,string>>>
     */
    private function createChartData(array $places): array
    {
        $data = [
            [
                I18N::translate('Country'),
                I18N::translate('Total'),
            ],
        ];

        // webtrees uses 3-letter country codes and localised country names, but google uses 2 letter codes.
        foreach ($places as $country => $count) {
            $data[] = [
                [
                    'v' => $country,
                    'f' => $this->country_service->mapTwoLetterToName($country),
                ],
                $count
            ];
        }

        return $data;
    }

    /**
     * @param Tree   $tree
     *
     * @return array<int>
     */
    private function countIndividualsByCountry(Tree $tree): array
    {
        $rows = DB::table('places')
            ->where('p_file', '=', $tree->id())
            ->where('p_parent_id', '=', 0)
            ->join('placelinks', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'p_file')
                    ->on('pl_p_id', '=', 'p_id');
            })
            ->join('individuals', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'i_file')
                    ->on('pl_gid', '=', 'i_id');
            })
            ->groupBy('p_place')
            ->pluck(new Expression('COUNT(*) AS total'), 'p_place');

        $totals = [];

        foreach ($rows as $country => $count) {
            $country_code = $this->country_to_iso3166[$country] ?? null;

            if ($country_code !== null) {
                $totals[$country_code] = $count + ($totals[$country_code] ?? 0);
            }
        }

        return $totals;
    }

    /**
     * @param Tree   $tree
     * @param string $surname
     *
     * @return array<int>
     */
    private function countSurnamesByCountry(Tree $tree, string $surname): array
    {
        $rows =
            DB::table('places')
                ->where('p_file', '=', $tree->id())
                ->where('p_parent_id', '=', 0)
                ->join('placelinks', static function (JoinClause $join): void {
                    $join
                        ->on('pl_file', '=', 'p_file')
                        ->on('pl_p_id', '=', 'p_id');
                })
                ->join('name', static function (JoinClause $join): void {
                    $join
                        ->on('n_file', '=', 'pl_file')
                        ->on('n_id', '=', 'pl_gid');
                })
                ->where('n_surn', '=', $surname)
                ->groupBy('p_place')
                ->pluck(new Expression('COUNT(*) AS total'), 'p_place');

        $totals = [];

        foreach ($rows as $country => $count) {
            $country_code = $this->country_to_iso3166[$country] ?? null;

            if ($country_code !== null) {
                $totals[$country_code] = $count + ($totals[$country_code] ?? 0);
            }
        }

        return $totals;
    }

    /**
     * @param Tree   $tree
     * @param string $fact
     *
     * @return array<int>
     */
    private function countFamilyEventsByCountry(Tree $tree, string $fact): array
    {
        $query = DB::table('places')
            ->where('p_file', '=', $tree->id())
            ->where('p_parent_id', '=', 0)
            ->join('placelinks', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'p_file')
                    ->on('pl_p_id', '=', 'p_id');
            })
            ->join('families', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'f_file')
                    ->on('pl_gid', '=', 'f_id');
            })
            ->select(['p_place AS place', 'f_gedcom AS gedcom']);

        return $this->filterEventPlaces($query, $fact);
    }

    /**
     * @param Tree   $tree
     * @param string $fact
     *
     * @return array<int>
     */
    private function countIndividualEventsByCountry(Tree $tree, string $fact): array
    {
        $query = DB::table('places')
            ->where('p_file', '=', $tree->id())
            ->where('p_parent_id', '=', 0)
            ->join('placelinks', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'p_file')
                    ->on('pl_p_id', '=', 'p_id');
            })
            ->join('individuals', static function (JoinClause $join): void {
                $join
                    ->on('pl_file', '=', 'i_file')
                    ->on('pl_gid', '=', 'i_id');
            })
            ->select(['p_place AS place', 'i_gedcom AS gedcom']);

        return $this->filterEventPlaces($query, $fact);
    }

    /**
     * @param Builder $query
     * @param string  $fact
     *
     * @return array<int>
     */
    private function filterEventPlaces(Builder $query, string $fact): array
    {
        $totals = [];

        foreach ($query->cursor() as $row) {
            $country_code = $this->country_to_iso3166[$row->place] ?? null;

            if ($country_code !== null) {
                $place_regex = '/\n1 ' . $fact . '(?:\n[2-9].*)*\n2 PLAC.*[, ]' . preg_quote($row->place, '(?:\n|$)/i') . '\n/';

                if (preg_match($place_regex, $row->gedcom) === 1) {
                    $totals[$country_code] = 1 + ($totals[$country_code] ?? 0);
                }
            }
        }

        return $totals;
    }

    /**
     * Create a chart showing where events occurred.
     *
     * @param string $chart_shows The type of chart map to show
     * @param string $chart_type  The type of chart to show
     * @param string $surname     The surname for surname based distribution chart
     *
     * @return string
     */
    public function chartDistribution(
        string $chart_shows = 'world',
        string $chart_type = '',
        string $surname = ''
    ): string {
        switch ($chart_type) {
            case 'surname_distribution_chart':
                $chart_title = I18N::translate('Surname distribution chart') . ': ' . $surname;
                $surname     = $surname ?: $this->individual_repository->getCommonSurname();
                $data        = $this->createChartData($this->countSurnamesByCountry($this->tree, $surname));
                break;

            case 'birth_distribution_chart':
                $chart_title = I18N::translate('Birth by country');
                $data        = $this->createChartData($this->countIndividualEventsByCountry($this->tree, 'BIRT'));
                break;

            case 'death_distribution_chart':
                $chart_title = I18N::translate('Death by country');
                $data        = $this->createChartData($this->countIndividualEventsByCountry($this->tree, 'DEAT'));
                break;

            case 'marriage_distribution_chart':
                $chart_title = I18N::translate('Marriage by country');
                $data        = $this->createChartData($this->countFamilyEventsByCountry($this->tree, 'MARR'));
                break;

            case 'indi_distribution_chart':
            default:
                $chart_title = I18N::translate('Individual distribution chart');
                $data        = $this->createChartData($this->countIndividualsByCountry($this->tree));
                break;
        }

        return view('statistics/other/charts/geo', [
            'chart_title'  => $chart_title,
            'chart_color2' => '84beff',
            'chart_color3' => 'c3dfff',
            'region'       => $chart_shows,
            'data'         => $data,
            'language'     => I18N::languageTag(),
        ]);
    }
}

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

namespace Fisharebest\Webtrees\Statistics\Google;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Statistics\Repository\IndividualRepository;
use Fisharebest\Webtrees\Statistics\Repository\PlaceRepository;
use Fisharebest\Webtrees\Statistics\Service\CountryService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use stdClass;

use function app;
use function array_key_exists;

/**
 * A chart showing the distribution of different events on a map.
 */
class ChartDistribution
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
     * @var CountryService
     */
    private $country_service;

    /**
     * @var IndividualRepository
     */
    private $individualRepository;

    /**
     * @var PlaceRepository
     */
    private $placeRepository;

    /**
     * @var string[]
     */
    private $country_to_iso3166;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree                 = $tree;
        $this->theme                = app(ModuleThemeInterface::class);
        $this->country_service      = new CountryService();
        $this->individualRepository = new IndividualRepository($tree);
        $this->placeRepository      = new PlaceRepository($tree);

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
     * @param array $places
     *
     * @return array
     */
    private function createChartData(array $places): array
    {
        $data = [
            [
                I18N::translate('Country'),
                I18N::translate('Total'),
            ],
        ];

        // webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
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
     * Returns the google geochart data for birth fact.
     *
     * @return array
     */
    private function getBirthChartData(): array
    {
        // Count how many people were born in each country
        $surn_countries = [];
        $b_countries    = $this->placeRepository->statsPlaces('INDI', 'BIRT', 0, true);

        foreach ($b_countries as $country => $count) {
            // Consolidate places (Germany, DEU => DE)
            if (array_key_exists($country, $this->country_to_iso3166)) {
                $country_code = $this->country_to_iso3166[$country];

                if (array_key_exists($country_code, $surn_countries)) {
                    $surn_countries[$country_code] += $count;
                } else {
                    $surn_countries[$country_code] = $count;
                }
            }
        }

        return $this->createChartData($surn_countries);
    }

    /**
     * Returns the google geochart data for death fact.
     *
     * @return array
     */
    private function getDeathChartData(): array
    {
        // Count how many people were death in each country
        $surn_countries = [];
        $d_countries    = $this->placeRepository->statsPlaces('INDI', 'DEAT', 0, true);

        foreach ($d_countries as $country => $count) {
            // Consolidate places (Germany, DEU => DE)
            if (array_key_exists($country, $this->country_to_iso3166)) {
                $country_code = $this->country_to_iso3166[$country];

                if (array_key_exists($country_code, $surn_countries)) {
                    $surn_countries[$country_code] += $count;
                } else {
                    $surn_countries[$country_code] = $count;
                }
            }
        }

        return $this->createChartData($surn_countries);
    }

    /**
     * Returns the google geochart data for marriages.
     *
     * @return array
     */
    private function getMarriageChartData(): array
    {
        // Count how many families got marriage in each country
        $surn_countries = [];
        $m_countries    = $this->placeRepository->statsPlaces('FAM');

        // webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
        foreach ($m_countries as $place) {
            // Consolidate places (Germany, DEU => DE)
            if (array_key_exists($place->country, $this->country_to_iso3166)) {
                $country_code = $this->country_to_iso3166[$place->country];

                if (array_key_exists($country_code, $surn_countries)) {
                    $surn_countries[$country_code] += $place->tot;
                } else {
                    $surn_countries[$country_code] = $place->tot;
                }
            }
        }

        return $this->createChartData($surn_countries);
    }

    /**
     * Returns the related database records.
     *
     * @param string $surname
     *
     * @return stdClass[]
     */
    private function queryRecords(string $surname): array
    {
        $query = DB::table('individuals')
            ->select(['i_gedcom'])
            ->join('name', static function (JoinClause $join): void {
                $join->on('n_id', '=', 'i_id')
                    ->on('n_file', '=', 'i_file');
            })
            ->where('n_file', '=', $this->tree->id())
            ->where(new Expression('n_surn /*! COLLATE ' . I18N::collation() . ' */'), '=', $surname);

        return $query->get()->all();
    }

    /**
     * Returns the google geochart data for surnames.
     *
     * @param string $surname The surname used to create the chart
     *
     * @return array
     */
    private function getSurnameChartData(string $surname): array
    {
        if ($surname === '') {
            $surname = $this->individualRepository->getCommonSurname();
        }

        // Count how many people are events in each country
        $surn_countries = [];
        $records        = $this->queryRecords($surname);

        foreach ($records as $row) {
            /** @var string[][] $matches */
            if (preg_match_all('/^2 PLAC (?:.*, *)*(.*)/m', $row->i_gedcom, $matches)) {
                // webtrees uses 3 letter country codes and localised country names,
                // but google uses 2 letter codes.
                foreach ($matches[1] as $country) {
                    // Consolidate places (Germany, DEU => DE)
                    if (array_key_exists($country, $this->country_to_iso3166)) {
                        $country_code = $this->country_to_iso3166[$country];

                        if (array_key_exists($country_code, $surn_countries)) {
                            $surn_countries[$country_code]++;
                        } else {
                            $surn_countries[$country_code] = 1;
                        }
                    }
                }
            }
        }

        return $this->createChartData($surn_countries);
    }

    /**
     * Returns the google geochart data for individuals.
     *
     * @return array
     */
    private function getIndivdualChartData(): array
    {
        // Count how many people have events in each country
        $surn_countries = [];
        $a_countries    = $this->placeRepository->statsPlaces('INDI');

        // webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
        foreach ($a_countries as $place) {
            // Consolidate places (Germany, DEU => DE)
            if (array_key_exists($place->country, $this->country_to_iso3166)) {
                $country_code = $this->country_to_iso3166[$place->country];

                if (array_key_exists($country_code, $surn_countries)) {
                    $surn_countries[$country_code] += $place->tot;
                } else {
                    $surn_countries[$country_code] = $place->tot;
                }
            }
        }

        return $this->createChartData($surn_countries);
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
                $data        = $this->getSurnameChartData($surname);
                break;

            case 'birth_distribution_chart':
                $chart_title = I18N::translate('Birth by country');
                $data        = $this->getBirthChartData();
                break;

            case 'death_distribution_chart':
                $chart_title = I18N::translate('Death by country');
                $data        = $this->getDeathChartData();
                break;

            case 'marriage_distribution_chart':
                $chart_title = I18N::translate('Marriage by country');
                $data        = $this->getMarriageChartData();
                break;

            case 'indi_distribution_chart':
            default:
                $chart_title = I18N::translate('Individual distribution chart');
                $data        = $this->getIndivdualChartData();
                break;
        }

        $chart_color2 = $this->theme->parameter('distribution-chart-high-values');
        $chart_color3 = $this->theme->parameter('distribution-chart-low-values');

        return view('statistics/other/charts/geo', [
            'chart_title'  => $chart_title,
            'chart_color2' => $chart_color2,
            'chart_color3' => $chart_color3,
            'region'       => $chart_shows,
            'data'         => $data,
            'language'     => I18N::languageTag(),
        ]);
    }
}

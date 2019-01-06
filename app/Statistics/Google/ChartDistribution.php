<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Helper\Country;
use Fisharebest\Webtrees\Statistics\AbstractGoogle;
use Fisharebest\Webtrees\Statistics\Repository\PlaceRepository;
use Fisharebest\Webtrees\Statistics\Repository\IndividualRepository;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;

/**
 * Create a chart showing where events occurred.
 */
class ChartDistribution extends AbstractGoogle
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * @var Country
     */
    private $countryHelper;

    /**
     * @var IndividualRepository
     */
    private $individualRepository;

    /**
     * @var PlaceRepository
     */
    private $placeRepository;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree                 = $tree;
        $this->countryHelper        = new Country();
        $this->individualRepository = new IndividualRepository($tree);
        $this->placeRepository      = new PlaceRepository($tree);
    }

    /**
     * Create a chart showing where events occurred.
     *
     * @param int    $tot_pl      The total number of places
     * @param string $chart_shows
     * @param string $chart_type
     * @param string $surname
     *
     * @return string
     */
    public function chartDistribution(
        int $tot_pl,
        string $chart_shows = 'world',
        string $chart_type  = '',
        string $surname     = ''
    ): string {
        $WT_STATS_CHART_COLOR1 = Theme::theme()->parameter('distribution-chart-no-values');
        $WT_STATS_CHART_COLOR2 = Theme::theme()->parameter('distribution-chart-high-values');
        $WT_STATS_CHART_COLOR3 = Theme::theme()->parameter('distribution-chart-low-values');
        $WT_STATS_MAP_X        = Theme::theme()->parameter('distribution-chart-x');
        $WT_STATS_MAP_Y        = Theme::theme()->parameter('distribution-chart-y');

        if ($tot_pl === 0) {
            return '';
        }

        $countries = $this->countryHelper->getAllCountries();

        // Get the country names for each language
        $country_to_iso3166 = [];
        foreach (I18N::activeLocales() as $locale) {
            I18N::init($locale->languageTag());

            foreach ($this->countryHelper->iso3166() as $three => $two) {
                $country_to_iso3166[$three]             = $two;
                $country_to_iso3166[$countries[$three]] = $two;
            }
        }

        I18N::init(WT_LOCALE);

        switch ($chart_type) {
            case 'surname_distribution_chart':
                if ($surname === '') {
                    $surname = $this->individualRepository->getCommonSurname();
                }
                $chart_title = I18N::translate('Surname distribution chart') . ': ' . $surname;
                // Count how many people are events in each country
                $surn_countries = [];

                $rows = Database::prepare(
                    "SELECT i_gedcom" .
                    " FROM `##individuals`" .
                    " JOIN `##name` ON n_id = i_id AND n_file = i_file" .
                    " WHERE n_file = :tree_id" .
                    " AND n_surn COLLATE :collate = :surname"
                )->execute([
                    'tree_id' => $this->tree->id(),
                    'collate' => I18N::collation(),
                    'surname' => $surname,
                ])->fetchAll();

                foreach ($rows as $row) {
                    if (preg_match_all('/^2 PLAC (?:.*, *)*(.*)/m', $row->i_gedcom, $matches)) {
                        // webtrees uses 3 letter country codes and localised country names,
                        // but google uses 2 letter codes.
                        foreach ($matches[1] as $country) {
                            if (array_key_exists($country, $country_to_iso3166)) {
                                if (array_key_exists($country_to_iso3166[$country], $surn_countries)) {
                                    $surn_countries[$country_to_iso3166[$country]]++;
                                } else {
                                    $surn_countries[$country_to_iso3166[$country]] = 1;
                                }
                            }
                        }
                    }
                }

                break;

            case 'birth_distribution_chart':
                $chart_title = I18N::translate('Birth by country');
                // Count how many people were born in each country
                $surn_countries = [];
                $b_countries    = $this->placeRepository->statsPlaces('INDI', 'BIRT', 0, true);
                foreach ($b_countries as $place => $count) {
                    $country = $place;
                    if (array_key_exists($country, $country_to_iso3166)) {
                        if (!isset($surn_countries[$country_to_iso3166[$country]])) {
                            $surn_countries[$country_to_iso3166[$country]] = $count;
                        } else {
                            $surn_countries[$country_to_iso3166[$country]] += $count;
                        }
                    }
                }
                break;

            case 'death_distribution_chart':
                $chart_title = I18N::translate('Death by country');
                // Count how many people were death in each country
                $surn_countries = [];
                $d_countries    = $this->placeRepository->statsPlaces('INDI', 'DEAT', 0, true);
                foreach ($d_countries as $place => $count) {
                    $country = $place;
                    if (array_key_exists($country, $country_to_iso3166)) {
                        if (!isset($surn_countries[$country_to_iso3166[$country]])) {
                            $surn_countries[$country_to_iso3166[$country]] = $count;
                        } else {
                            $surn_countries[$country_to_iso3166[$country]] += $count;
                        }
                    }
                }
                break;

            case 'marriage_distribution_chart':
                $chart_title = I18N::translate('Marriage by country');
                // Count how many families got marriage in each country
                $surn_countries = [];
                $m_countries    = $this->placeRepository->statsPlaces('FAM');
                // webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
                foreach ($m_countries as $place) {
                    $country = $place->country;
                    if (array_key_exists($country, $country_to_iso3166)) {
                        if (!isset($surn_countries[$country_to_iso3166[$country]])) {
                            $surn_countries[$country_to_iso3166[$country]] = $place->tot;
                        } else {
                            $surn_countries[$country_to_iso3166[$country]] += $place->tot;
                        }
                    }
                }
                break;

            case 'indi_distribution_chart':
            default:
                $chart_title = I18N::translate('Individual distribution chart');
                // Count how many people have events in each country
                $surn_countries = [];
                $a_countries    = $this->placeRepository->statsPlaces('INDI');
                // webtrees uses 3 letter country codes and localised country names, but google uses 2 letter codes.
                foreach ($a_countries as $place) {
                    $country = $place->country;
                    if (array_key_exists($country, $country_to_iso3166)) {
                        if (!isset($surn_countries[$country_to_iso3166[$country]])) {
                            $surn_countries[$country_to_iso3166[$country]] = $place->tot;
                        } else {
                            $surn_countries[$country_to_iso3166[$country]] += $place->tot;
                        }
                    }
                }
                break;
        }

        $chart_url = 'https://chart.googleapis.com/chart?cht=t&amp;chtm=' . $chart_shows;
        $chart_url .= '&amp;chco=' . $WT_STATS_CHART_COLOR1 . ',' . $WT_STATS_CHART_COLOR3 . ',' . $WT_STATS_CHART_COLOR2; // country colours
        $chart_url .= '&amp;chf=bg,s,ECF5FF'; // sea colour
        $chart_url .= '&amp;chs=' . $WT_STATS_MAP_X . 'x' . $WT_STATS_MAP_Y;
        $chart_url .= '&amp;chld=' . implode('', array_keys($surn_countries)) . '&amp;chd=s:';

        foreach ($surn_countries as $count) {
            $chart_url .= substr(self::GOOGLE_CHART_ENCODING, (int) ($count / max($surn_countries) * 61), 1);
        }

        return view(
            'statistics/other/chart-distribution',
            [
                'chart_title'           => $chart_title,
                'chart_url'             => $chart_url,
                'WT_STATS_CHART_COLOR1' => $WT_STATS_CHART_COLOR1,
                'WT_STATS_CHART_COLOR2' => $WT_STATS_CHART_COLOR2,
                'WT_STATS_CHART_COLOR3' => $WT_STATS_CHART_COLOR3,
            ]
        );
    }
}

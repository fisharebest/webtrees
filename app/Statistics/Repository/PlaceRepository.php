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

namespace Fisharebest\Webtrees\Statistics\Repository;

use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Statistics\Google\ChartDistribution;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\PlaceRepositoryInterface;
use Fisharebest\Webtrees\Statistics\Service\CountryService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use stdClass;

use function array_key_exists;

/**
 * A repository providing methods for place related statistics.
 */
class PlaceRepository implements PlaceRepositoryInterface
{
    /**
     * @var Tree
     */
    private $tree;

    /**
     * @var CountryService
     */
    private $country_service;

    /**
     * BirthPlaces constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree          = $tree;
        $this->country_service = new CountryService();
    }

    /**
     * Places
     *
     * @param string $fact
     * @param string $what
     * @param bool   $country
     *
     * @return int[]
     */
    private function queryFactPlaces(string $fact, string $what = 'ALL', bool $country = false): array
    {
        $rows = [];

        if ($what === 'INDI') {
            $rows = DB::table('individuals')->select(['i_gedcom as tree'])->where(
                'i_file',
                '=',
                $this->tree->id()
            )->where(
                'i_gedcom',
                'LIKE',
                "%\n2 PLAC %"
            )->get()->all();
        } elseif ($what === 'FAM') {
            $rows = DB::table('families')->select(['f_gedcom as tree'])->where(
                'f_file',
                '=',
                $this->tree->id()
            )->where(
                'f_gedcom',
                'LIKE',
                "%\n2 PLAC %"
            )->get()->all();
        }

        $placelist = [];

        foreach ($rows as $row) {
            if (preg_match('/\n1 ' . $fact . '(?:\n[2-9].*)*\n2 PLAC (.+)/', $row->tree, $match)) {
                if ($country) {
                    $tmp   = explode(Gedcom::PLACE_SEPARATOR, $match[1]);
                    $place = end($tmp);
                } else {
                    $place = $match[1];
                }

                if (isset($placelist[$place])) {
                    ++$placelist[$place];
                } else {
                    $placelist[$place] = 1;
                }
            }
        }

        return $placelist;
    }

    /**
     * Query places.
     *
     * @param string $what
     * @param string $fact
     * @param int    $parent
     * @param bool   $country
     *
     * @return int[]|stdClass[]
     */
    public function statsPlaces(string $what = 'ALL', string $fact = '', int $parent = 0, bool $country = false): array
    {
        if ($fact) {
            return $this->queryFactPlaces($fact, $what, $country);
        }

        $query = DB::table('places')
            ->join('placelinks', static function (JoinClause $join): void {
                $join->on('pl_file', '=', 'p_file')
                    ->on('pl_p_id', '=', 'p_id');
            })
            ->where('p_file', '=', $this->tree->id());

        if ($parent > 0) {
            // Used by placehierarchy map modules
            $query->select(['p_place AS place'])
                ->selectRaw('COUNT(*) AS tot')
                ->where('p_id', '=', $parent)
                ->groupBy(['place']);
        } else {
            $query->select(['p_place AS country'])
                ->selectRaw('COUNT(*) AS tot')
                ->where('p_parent_id', '=', 0)
                ->groupBy(['country'])
                ->orderByDesc('tot')
                ->orderBy('country');
        }

        if ($what === 'INDI') {
            $query->join('individuals', static function (JoinClause $join): void {
                $join->on('pl_file', '=', 'i_file')
                    ->on('pl_gid', '=', 'i_id');
            });
        } elseif ($what === 'FAM') {
            $query->join('families', static function (JoinClause $join): void {
                $join->on('pl_file', '=', 'f_file')
                    ->on('pl_gid', '=', 'f_id');
            });
        }

        return $query->get()->all();
    }

    /**
     * Get the top 10 places list.
     *
     * @param array<string,int> $places
     *
     * @return array<array<string,mixed>>
     */
    private function getTop10Places(array $places): array
    {
        $top10 = [];
        $i     = 0;

        arsort($places);

        foreach ($places as $place => $count) {
            $tmp     = new Place($place, $this->tree);
            $top10[] = [
                'place' => $tmp,
                'count' => $count,
            ];

            ++$i;

            if ($i === 10) {
                break;
            }
        }

        return $top10;
    }

    /**
     * Renders the top 10 places list.
     *
     * @param array<string,int> $places
     *
     * @return string
     */
    private function renderTop10(array $places): string
    {
        $top10Records = $this->getTop10Places($places);

        return view(
            'statistics/other/top10-list',
            [
                'records' => $top10Records,
            ]
        );
    }

    /**
     * A list of common birth places.
     *
     * @return string
     */
    public function commonBirthPlacesList(): string
    {
        $places = $this->queryFactPlaces('BIRT', 'INDI');
        return $this->renderTop10($places);
    }

    /**
     * A list of common death places.
     *
     * @return string
     */
    public function commonDeathPlacesList(): string
    {
        $places = $this->queryFactPlaces('DEAT', 'INDI');
        return $this->renderTop10($places);
    }

    /**
     * A list of common marriage places.
     *
     * @return string
     */
    public function commonMarriagePlacesList(): string
    {
        $places = $this->queryFactPlaces('MARR', 'FAM');
        return $this->renderTop10($places);
    }

    /**
     * A list of common countries.
     *
     * @return string
     */
    public function commonCountriesList(): string
    {
        $countries = $this->statsPlaces();

        if ($countries === []) {
            return '';
        }

        $top10 = [];
        $i     = 1;

        // Get the country names for each language
        $country_names = [];
        $old_language = I18N::languageTag();

        foreach (I18N::activeLocales() as $locale) {
            I18N::init($locale->languageTag());
            $all_countries = $this->country_service->getAllCountries();
            foreach ($all_countries as $country_code => $country_name) {
                $country_names[$country_name] = $country_code;
            }
        }

        I18N::init($old_language);

        $all_db_countries = [];
        foreach ($countries as $place) {
            $country = trim($place->country);
            if (array_key_exists($country, $country_names)) {
                if (isset($all_db_countries[$country_names[$country]][$country])) {
                    $all_db_countries[$country_names[$country]][$country] += (int) $place->tot;
                } else {
                    $all_db_countries[$country_names[$country]][$country] = (int) $place->tot;
                }
            }
        }

        // get all the user’s countries names
        $all_countries = $this->country_service->getAllCountries();

        foreach ($all_db_countries as $country_code => $country) {
            foreach ($country as $country_name => $tot) {
                $tmp     = new Place($country_name, $this->tree);

                $top10[] = [
                    'place' => $tmp,
                    'count' => $tot,
                    'name'  => $all_countries[$country_code],
                ];
            }

            if ($i++ === 10) {
                break;
            }
        }

        return view(
            'statistics/other/top10-list',
            [
                'records' => $top10,
            ]
        );
    }

    /**
     * Count total places.
     *
     * @return int
     */
    private function totalPlacesQuery(): int
    {
        return DB::table('places')
            ->where('p_file', '=', $this->tree->id())
            ->count();
    }

    /**
     * Count total places.
     *
     * @return string
     */
    public function totalPlaces(): string
    {
        return I18N::number($this->totalPlacesQuery());
    }

    /**
     * Create a chart showing where events occurred.
     *
     * @param string $chart_shows
     * @param string $chart_type
     * @param string $surname
     *
     * @return string
     */
    public function chartDistribution(
        string $chart_shows = 'world',
        string $chart_type = '',
        string $surname = ''
    ): string {
        return (new ChartDistribution($this->tree))
            ->chartDistribution($chart_shows, $chart_type, $surname);
    }
}

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

namespace Fisharebest\Webtrees\Statistics\Repository;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Statistics\Google\ChartDistribution;
use Fisharebest\Webtrees\Statistics\Helper\Country;
use Fisharebest\Webtrees\Statistics\Helper\Sql;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\PlaceRepositoryInterface;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Statistics submodule providing all PLACE related methods.
 */
class PlaceRepository implements PlaceRepositoryInterface
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
     * BirthPlaces constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree          = $tree;
        $this->countryHelper = new Country();
    }

    /**
     * Run an SQL query and cache the result.
     *
     * @param string $sql
     *
     * @return \stdClass[]
     */
    private function runSql($sql): array
    {
        return Sql::runSql($sql);
    }

    /**
     * Places
     *
     * @param string $what
     * @param string $fact
     * @param int    $parent
     * @param bool   $country
     *
     * @return int[]|\stdClass[]
     */
    public function statsPlaces(string $what = 'ALL', string $fact = '', int $parent = 0, bool $country = false): array
    {
        if ($fact) {
            if ($what === 'INDI') {
                $rows = Database::prepare(
                    "SELECT i_gedcom AS ged FROM `##individuals` WHERE i_file = :tree_id AND i_gedcom LIKE '%\n2 PLAC %'"
                )->execute([
                    'tree_id' => $this->tree->id(),
                ])->fetchAll();
            } elseif ($what === 'FAM') {
                $rows = Database::prepare(
                    "SELECT f_gedcom AS ged FROM `##families` WHERE f_file = :tree_id AND f_gedcom LIKE '%\n2 PLAC %'"
                )->execute([
                    'tree_id' => $this->tree->id(),
                ])->fetchAll();
            }

            $placelist = [];

            foreach ($rows as $row) {
                if (preg_match('/\n1 ' . $fact . '(?:\n[2-9].*)*\n2 PLAC (.+)/', $row->ged, $match)) {
                    if ($country) {
                        $tmp   = explode(Place::GEDCOM_SEPARATOR, $match[1]);
                        $place = end($tmp);
                    } else {
                        $place = $match[1];
                    }
                    if (!isset($placelist[$place])) {
                        $placelist[$place] = 1;
                    } else {
                        $placelist[$place]++;
                    }
                }
            }

            return $placelist;
        }

        if ($parent > 0) {
            // used by placehierarchy googlemap module
            if ($what === 'INDI') {
                $join = " JOIN `##individuals` ON pl_file = i_file AND pl_gid = i_id";
            } elseif ($what === 'FAM') {
                $join = " JOIN `##families` ON pl_file = f_file AND pl_gid = f_id";
            } else {
                $join = "";
            }
            $rows = $this->runSql(
                " SELECT" .
                " p_place AS place," .
                " COUNT(*) AS tot" .
                " FROM" .
                " `##places`" .
                " JOIN `##placelinks` ON pl_file=p_file AND p_id=pl_p_id" .
                $join .
                " WHERE" .
                " p_id={$parent} AND" .
                " p_file={$this->tree->id()}" .
                " GROUP BY place"
            );

            return $rows;
        }

        if ($what === 'INDI') {
            $join = " JOIN `##individuals` ON pl_file = i_file AND pl_gid = i_id";
        } elseif ($what === 'FAM') {
            $join = " JOIN `##families` ON pl_file = f_file AND pl_gid = f_id";
        } else {
            $join = "";
        }

        $rows = $this->runSql(
            " SELECT" .
            " p_place AS country," .
            " COUNT(*) AS tot" .
            " FROM" .
            " `##places`" .
            " JOIN `##placelinks` ON pl_file=p_file AND p_id=pl_p_id" .
            $join .
            " WHERE" .
            " p_file={$this->tree->id()}" .
            " AND p_parent_id='0'" .
            " GROUP BY country ORDER BY tot DESC, country ASC"
        );

        return $rows;
    }

    /**
     * Get the top 10 places list.
     *
     * @param array $places
     *
     * @return array
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
     * @param array $places
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
        $places = $this->statsPlaces('INDI', 'BIRT');
        return $this->renderTop10($places);
    }

    /**
     * A list of common death places.
     *
     * @return string
     */
    public function commonDeathPlacesList(): string
    {
        $places = $this->statsPlaces('INDI', 'DEAT');
        return $this->renderTop10($places);
    }

        /**
     * A list of common marriage places.
     *
     * @return string
     */
    public function commonMarriagePlacesList(): string
    {
        $places = $this->statsPlaces('FAM', 'MARR');
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

        if (empty($countries)) {
            return '';
        }

        $top10 = [];
        $i     = 1;

        // Get the country names for each language
        $country_names = [];
        foreach (I18N::activeLocales() as $locale) {
            I18N::init($locale->languageTag());
            $all_countries = $this->countryHelper->getAllCountries();
            foreach ($all_countries as $country_code => $country_name) {
                $country_names[$country_name] = $country_code;
            }
        }

        I18N::init(WT_LOCALE);

        $all_db_countries = [];
        foreach ($countries as $place) {
            $country = trim($place->country);
            if (array_key_exists($country, $country_names)) {
                if (!isset($all_db_countries[$country_names[$country]][$country])) {
                    $all_db_countries[$country_names[$country]][$country] = (int) $place->tot;
                } else {
                    $all_db_countries[$country_names[$country]][$country] += (int) $place->tot;
                }
            }
        }
        // get all the user’s countries names
        $all_countries = $this->countryHelper->getAllCountries();

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
        string $chart_type  = '',
        string $surname     = ''
    ): string {
        $tot_pl = $this->totalPlacesQuery();

        return (new ChartDistribution($this->tree))
            ->chartDistribution($tot_pl, $chart_shows, $chart_type, $surname);
    }
}

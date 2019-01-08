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

namespace Fisharebest\Webtrees;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Generate markup and AJAX responses for SELECT2 queries.
 *
 * Note that the single space in the title attributes is necessary to prevent
 * select2 from copying the (raw HTML) of the value into the title.
 *
 * @link https://stackoverflow.com/questions/35500508/how-to-disable-the-title-in-select2
 *
 * @link https://select2.github.io/
 */
class Select2 extends Html
{
    // Send this many results with each request.
    public const RESULTS_PER_PAGE = 20;

    // Don't send queries with fewer than this many characters
    private const MINIMUM_INPUT_LENGTH = '1';

    // Don't send queries until this many milliseconds.
    private const DELAY = '350';

    /**
     * Select2 configuration that is common to all searches.
     *
     * @return string[]
     */
    private static function commonConfig(): array
    {
        return [
            'autocomplete'                    => 'off',
            'class'                           => 'form-control select2',
            'data-ajax--delay'                => self::DELAY,
            'data-ajax--minimum-input-length' => self::MINIMUM_INPUT_LENGTH,
            'data-ajax--type'                 => 'POST',
            'data-allow-clear'                => 'true',
            'data-placeholder'                => '',
        ];
    }

    /**
     * Select2 configuration for a family lookup.
     *
     * @param Tree $tree
     *
     * @return string[]
     */
    public static function familyConfig(Tree $tree): array
    {
        $url = route('select2-family', ['ged' => $tree->name()]);

        return self::commonConfig() + ['data-ajax--url' => $url];
    }

    /**
     * Select2 configuration for a flag icon lookup.
     *
     * @return string[]
     */
    public static function flagConfig(): array
    {
        return self::commonConfig() + ['data-ajax--url' => route('select2-flag')];
    }

    /**
     * Format a flag icon for display in a Select2 control.
     *
     * @param string $flag
     *
     * @return string
     */
    public static function flagValue($flag): string
    {
        return '<img src="' . Webtrees::MODULES_PATH . 'googlemap/places/flags/' . $flag . '"> ' . $flag;
    }

    /**
     * Look up a flag icon.
     *
     * @param int    $page  Skip this number of pages.  Starts with zero.
     * @param string $query Search terms.
     *
     * @return mixed[]
     */
    public static function flagSearch(int $page, string $query): array
    {
        $offset    = $page * self::RESULTS_PER_PAGE;
        $more      = false;
        $results   = [];
        $directory = WT_ROOT . Webtrees::MODULES_PATH . 'googlemap/places/flags/';
        $di        = new RecursiveDirectoryIterator($directory);
        $it        = new RecursiveIteratorIterator($di);

        $flag_files = [];
        foreach ($it as $file) {
            $file_path = substr($file->getPathname(), strlen($directory));
            if ($file->getExtension() === 'png' && stripos($file_path, $query) !== false) {
                if ($offset > 0) {
                    // Skip results
                    $offset--;
                } elseif (count($flag_files) >= self::RESULTS_PER_PAGE) {
                    $more = true;
                    break;
                } else {
                    $flag_files[] = $file_path;
                }
            }
        }

        foreach ($flag_files as $flag_file) {
            $results[] = [
                'id'    => $flag_file,
                'text'  => self::flagValue($flag_file),
                'title' => ' ',
            ];
        }

        return [
            'results'    => $results,
            'pagination' => [
                'more' => $more,
            ],
        ];
    }

    /**
     * Select2 configuration for an individual lookup.
     *
     * @param Tree $tree
     *
     * @return string[]
     */
    public static function individualConfig(Tree $tree): array
    {
        $url = route('select2-individual', ['ged' => $tree->name()]);

        return self::commonConfig() + ['data-ajax--url' => $url];
    }

    /**
     * Select2 configuration for a media object lookup.
     *
     * @param Tree $tree
     *
     * @return string[]
     */
    public static function mediaObjectConfig(Tree $tree): array
    {
        $url = route('select2-media', ['ged' => $tree->name()]);

        return self::commonConfig() + ['data-ajax--url' => $url];
    }

    /**
     * Select2 configuration for a note.
     *
     * @param Tree $tree
     *
     * @return string[]
     */
    public static function noteConfig(Tree $tree): array
    {
        $url = route('select2-note', ['ged' => $tree->name()]);

        return self::commonConfig() + ['data-ajax--url' => $url];
    }

    /**
     * Select2 configuration for a note.
     *
     * @param Tree $tree
     *
     * @return string[]
     */
    public static function placeConfig(Tree $tree): array
    {
        $url = route('select2-place', ['ged' => $tree->name()]);

        return self::commonConfig() + ['data-ajax--url' => $url];
    }

    /**
     * Look up a place name.
     *
     * @param Tree   $tree   Search this tree.
     * @param int    $page   Skip this number of pages.  Starts with zero.
     * @param string $query  Search terms.
     * @param bool   $create if true, include the query in the results so it can be created.
     *
     * @return mixed[]
     */
    public static function placeSearch(Tree $tree, int $page, string $query, bool $create): array
    {
        $offset  = $page * self::RESULTS_PER_PAGE;
        $results = [];
        $found   = false;

        // Do not filter by privacy. Place names on their own do not identify individuals.
        foreach (Place::findPlaces($query, $tree) as $place) {
            $place_name = $place->getGedcomName();
            if ($place_name === $query) {
                $found = true;
            }
            $results[] = [
                'id'    => $place_name,
                'text'  => $place_name,
                'title' => ' ',
            ];
        }

        // No place found? Use an external gazetteer
        if (empty($results) && $tree->getPreference('GEONAMES_ACCOUNT')) {
            $url =
                "http://api.geonames.org/searchJSON" .
                "?name_startsWith=" . urlencode($query) .
                "&lang=" . WT_LOCALE .
                "&fcode=CMTY&fcode=ADM4&fcode=PPL&fcode=PPLA&fcode=PPLC" .
                "&style=full" .
                "&username=" . $tree->getPreference('GEONAMES_ACCOUNT');
            // try to use curl when file_get_contents not allowed
            if (ini_get('allow_url_fopen')) {
                $json   = file_get_contents($url);
                $places = json_decode($json, true);
            } elseif (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $json   = curl_exec($ch);
                $places = json_decode($json, true);
                curl_close($ch);
            } else {
                $places = [];
            }
            if (isset($places['geonames']) && is_array($places['geonames'])) {
                foreach ($places['geonames'] as $k => $place) {
                    $place_name = $place['name'] . ', ' . $place['adminName2'] . ', ' . $place['adminName1'] . ', ' . $place['countryName'];
                    if ($place_name === $query) {
                        $found = true;
                    }
                    $results[] = [
                        'id'    => $place_name,
                        'text'  => $place_name,
                        'title' => ' ',
                    ];
                }
            }
        }

        // Include the query term in the results.  This allows the user to select a
        // place that doesn't already exist in the database.
        if (!$found && $create) {
            array_unshift($results, [
                'id'   => $query,
                'text' => $query,
            ]);
        }

        $more    = count($results) > $offset + self::RESULTS_PER_PAGE;
        $results = array_slice($results, $offset, self::RESULTS_PER_PAGE);

        return [
            'results'    => $results,
            'pagination' => [
                'more' => $more,
            ],
        ];
    }

    /**
     * Select2 configuration for a repository lookup.
     *
     * @param Tree $tree
     *
     * @return string[]
     */
    public static function repositoryConfig(Tree $tree): array
    {
        $url = route('select2-repository', ['ged' => $tree->name()]);

        return self::commonConfig() + ['data-ajax--url' => $url];
    }

    /**
     * Select2 configuration for a source lookup.
     *
     * @param Tree $tree
     *
     * @return string[]
     */
    public static function sourceConfig(Tree $tree): array
    {
        $url = route('select2-source', ['ged' => $tree->name()]);

        return self::commonConfig() + ['data-ajax--url' => $url];
    }

    /**
     * Select2 configuration for a submitter lookup.
     *
     * @param Tree $tree
     *
     * @return string[]
     */
    public static function submitterConfig(Tree $tree): array
    {
        $url = route('select2-submitter', ['ged' => $tree->name()]);

        return self::commonConfig() + ['data-ajax--url' => $url];
    }
}

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

namespace Fisharebest\Webtrees\Http\Controllers;

use FilesystemIterator;
use Fisharebest\Webtrees\Place;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for the autocomplete callbacks
 */
class AutocompleteController extends BaseController {
	/**

	/**
	 * Autocomplete for media folders.
	 *
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function folder(Request $request): JsonResponse {
		$tree     = $request->attributes->get('tree');
		$query    = $request->get('query', '');
		$folder   = WT_DATA_DIR . $tree->getPreference('MEDIA_DIRECTORY', '');
		$flags    = FilesystemIterator::FOLLOW_SYMLINKS;
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder, $flags));
		$folders  = [];

		// Iterator finds media/foo/. but not media/foo ??
		foreach ($iterator as $iteration) {
			if ($iteration->getFileName() === '.') {
				$path = dirname(substr($iteration->getPathName(), strlen($folder)));
				if ($query === '' || stripos($path, $query) !== false) {
					$folders[] = ['value' => $path];
				}
			}
		}

		return new JsonResponse($folders);
	}

	/**

	/**
	 * Autocomplete for place names.
	 *
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function place(Request $request): JsonResponse {
		$tree  = $request->attributes->get('tree');
		$query = $request->get('query');
		$data  = [];

		foreach (Place::findPlaces($query, $tree) as $place) {
			$data[] = ['value' => $place->getGedcomName()];
		}

		if (empty($data) && $tree->getPreference('GEONAMES_ACCOUNT')) {
			// No place found? Use an external gazetteer
			$url =
				"http://api.geonames.org/searchJSON" .
				"?name_startsWith=" . urlencode($query) .
				"&lang=" . WT_LOCALE .
				"&fcode=CMTY&fcode=ADM4&fcode=PPL&fcode=PPLA&fcode=PPLC" .
				"&style=full" .
				"&username=" . $tree->getPreference('GEONAMES_ACCOUNT');

			// try to use curl when file_get_contents not allowed
			if (ini_get('allow_url_fopen')) {
				$json = file_get_contents($url);
			} elseif (function_exists('curl_init')) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$json = curl_exec($ch);
				curl_close($ch);
			} else {
				return new JsonResponse([]);
			}

			$places = json_decode($json, true);
			if (isset($places['geonames']) && is_array($places['geonames'])) {
				foreach ($places['geonames'] as $k => $place) {
					$data[] = ['value' => $place['name'] . ', ' . $place['adminName2'] . ', ' . $place['adminName1'] . ', ' . $place['countryName']];
				}
			}
		}

		return new JsonResponse($data);
	}
}

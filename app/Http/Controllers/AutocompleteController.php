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
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Select2;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for the autocomplete callbacks
 */
class AutocompleteController extends AbstractBaseController {
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
	 * Autocomplete for source citations.
	 *
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function page(Request $request): JsonResponse {
		$tree  = $request->attributes->get('tree');
		$query = $request->get('query', '');
		$xref  = $request->get('extra', '');

		$source = Source::getInstance($xref, $tree);

		$this->checkSourceAccess($source);

		$pages = [];

		// Escape the query for MySQL and PHP, converting spaces to wildcards.
		$like_query  = strtr($query, ['_' => '\\_', '%' => '\\%', ' ' => '%']);
		$regex_query = preg_quote(strtr($query, [' ' => '.+']), '/');

		$regex_xref = preg_quote($xref, '/');

		// Fetch all individuals with a link to this source
		$rows = Database::prepare(
			"SELECT SQL_CACHE i_id AS xref, i_gedcom AS gedcom" .
			" FROM `##individuals`" .
			" JOIN `##link ON i_file = l_file AND i_from = i_id AND i_to = :xref AND i_type = 'SOUR'" .
			" WHERE i_gedcom LIKE CONCAT('%\n_ SOUR @', :xref, '@%', REPLACE(:term, ' ', '%'), '%')" .
			" AND   i_file = :tree_id"
		)->execute([
			'xref'    => $xref,
			'term'    => $like_query,
			'tree_id' => $tree->getTreeId(),
		])->fetchAll();

		// Filter for privacy
		foreach ($rows as $row) {
			$individual = Individual::getInstance($row->xref, $tree, $row->gedcom);
			if (preg_match('/\n1 SOUR @' . $regex_xref . '@(?:\n[2-9].*)*\n2 PAGE (.*' . $regex_query . '.*)/i', $individual->getGedcom(), $match)) {
				$pages[] = $match[1];
			}
			if (preg_match('/\n2 SOUR @' . $xref . '@(?:\n[3-9].*)*\n3 PAGE (.*' . $regex_query . '.*)/i', $individual->getGedcom(), $match)) {
				$pages[] = $match[1];
			}
		}
		// Fetch all data, regardless of privacy
		$rows = Database::prepare(
			"SELECT SQL_CACHE f_id AS xref, f_gedcom AS gedcom" .
			" FROM `##families`" .
			" WHERE f_gedcom LIKE CONCAT('%\n_ SOUR @', :xref, '@%', REPLACE(:term, ' ', '%'), '%') AND f_file = :tree_id"
		)->execute([
			'xref'    => $xref,
			'term'    => $query,
			'tree_id' => $tree->getTreeId(),
		])->fetchAll();
		// Filter for privacy
		foreach ($rows as $row) {
			$family = Family::getInstance($row->xref, $tree, $row->gedcom);
			if (preg_match('/\n1 SOUR @' . $xref . '@(?:\n[2-9].*)*\n2 PAGE (.*' . str_replace(' ', '.+', preg_quote($query, '/')) . '.*)/i', $family->getGedcom(), $match)) {
				$pages[] = $match[1];
			}
			if (preg_match('/\n2 SOUR @' . $xref . '@(?:\n[3-9].*)*\n3 PAGE (.*' . str_replace(' ', '.+', preg_quote($query, '/')) . '.*)/i', $family->getGedcom(), $match)) {
				$pages[] = $match[1];
			}
		}
		// array_unique() converts the keys from integer to string, which breaks
		// the JSON encoding - so need to call array_values() to convert them
		// back into integers.
		$pages = array_values(array_unique($pages));
		echo json_encode($pages);

		return new JsonResponse($pages);
	}

	/**
	 *
	 * /**
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

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function select2Family(Request $request): JsonResponse {
		/** @var Tree $tree */
		$tree  = $request->attributes->get('tree');

		$page  = (int) $request->get('page');
		$query = $request->get('q');

		return new JsonResponse(Select2::familySearch($tree, $page, $query));
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function select2Flag(Request $request): JsonResponse {
		$page  = $request->get('page');
		$query = (int) $request->get('q');

		return new JsonResponse(Select2::flagSearch($page, $query));
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function select2Individual(Request $request): JsonResponse {
		/** @var Tree $tree */
		$tree  = $request->attributes->get('tree');

		$page  = (int) $request->get('page');
		$query = $request->get('q');

		return new JsonResponse(Select2::individualSearch($tree, $page, $query));
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function select2Media(Request $request): JsonResponse {
		/** @var Tree $tree */
		$tree  = $request->attributes->get('tree');

		$page  = (int) $request->get('page');
		$query = $request->get('q');

		return new JsonResponse(Select2::mediaObjectSearch($tree, $page, $query));
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function select2Note(Request $request): JsonResponse {
		/** @var Tree $tree */
		$tree  = $request->attributes->get('tree');

		$page  = (int) $request->get('page');
		$query = $request->get('q');

		return new JsonResponse(Select2::noteSearch($tree, $page, $query));
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function select2Place(Request $request): JsonResponse {
		/** @var Tree $tree */
		$tree  = $request->attributes->get('tree');

		$page  = (int) $request->get('page');
		$query = $request->get('q');

		return new JsonResponse(Select2::placeSearch($tree, $page, $query, true));
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function select2Repository(Request $request): JsonResponse {
		/** @var Tree $tree */
		$tree  = $request->attributes->get('tree');

		$page  = (int) $request->get('page');
		$query = $request->get('q');

		return new JsonResponse(Select2::repositorySearch($tree, $page, $query));
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function select2Source(Request $request): JsonResponse {
		/** @var Tree $tree */
		$tree  = $request->attributes->get('tree');

		$page  = (int) $request->get('page');
		$query = $request->get('q');

		return new JsonResponse(Select2::sourceSearch($tree, $page, $query));
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function select2Submitter(Request $request): JsonResponse {
		/** @var Tree $tree */
		$tree  = $request->attributes->get('tree');

		$page  = (int) $request->get('page');
		$query = $request->get('q');

		return new JsonResponse(Select2::submitterSearch($tree, $page, $query));
	}
}

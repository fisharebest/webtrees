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

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Soundex;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Search for (and optionally replace) genealogy data
 */
class SearchController extends AbstractBaseController {
	const DEFAULT_ADVANCED_FIELDS = [
		'NAME:GIVN',
		'NAME:SURN',
		'BIRT:DATE',
		'BIRT:PLAC',
		'FAMS:MARR:DATE',
		'FAMS:MARR:PLAC',
		'DEAT:DATE',
		'DEAT:PLAC',
		'FAMC:HUSB:NAME:GIVN',
		'FAMC:HUSB:NAME:SURN',
		'FAMC:WIFE:NAME:GIVN',
		'FAMC:WIFE:NAME:SURN',
	];

	const OTHER_ADVANCED_FIELDS = [
		'ADOP:DATE', 'ADOP:PLAC',
		'AFN',
		'BAPL:DATE', 'BAPL:PLAC',
		'BAPM:DATE', 'BAPM:PLAC',
		'BARM:DATE', 'BARM:PLAC',
		'BASM:DATE', 'BASM:PLAC',
		'BLES:DATE', 'BLES:PLAC',
		'BURI:DATE', 'BURI:PLAC',
		'CAST',
		'CENS:DATE', 'CENS:PLAC',
		'CHAN:DATE', 'CHAN:_WT_USER',
		'CHR:DATE', 'CHR:PLAC',
		'CREM:DATE', 'CREM:PLAC',
		'DSCR',
		'EMAIL',
		'EMIG:DATE', 'EMIG:PLAC',
		'ENDL:DATE', 'ENDL:PLAC',
		'EVEN', 'EVEN:TYPE', 'EVEN:DATE', 'EVEN:PLAC',
		'FACT', 'FACT:TYPE',
		'FAMS:CENS:DATE', 'FAMS:CENS:PLAC',
		'FAMS:DIV:DATE',
		'FAMS:NOTE',
		'FAMS:SLGS:DATE', 'FAMS:SLGS:PLAC',
		'FAX',
		'FCOM:DATE', 'FCOM:PLAC',
		'IMMI:DATE', 'IMMI:PLAC',
		'NAME:NICK', 'NAME:_MARNM', 'NAME:_HEB', 'NAME:ROMN',
		'NATI',
		'NATU:DATE', 'NATU:PLAC',
		'NOTE',
		'OCCU',
		'ORDN:DATE', 'ORDN:PLAC',
		'REFN',
		'RELI',
		'RESI', 'RESI:DATE', 'RESI:PLAC',
		'SLGC:DATE', 'SLGC:PLAC',
		'TITL',
		'_BRTM:DATE', '_BRTM:PLAC',
		'_MILI',
	];

	/**
	 * The "omni-search" box in the header.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function quick(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$query = $request->get('query', '');

		// Was the search query an XREF in the current tree?
		// If so, go straight to it.
		$record = GedcomRecord::getInstance($query, $tree);

		if ($record !== null && $record->canShow()) {
			return new RedirectResponse($record->url());
		} else {
			return $this->general($request);
		}
	}

	/**
	 * The standard search.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function general(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$query = $request->get('query', '');

		// What type of records to search?
		$search_individuals  = (bool) $request->get('search_individuals');
		$search_families     = (bool) $request->get('search_families');
		$search_repositories = (bool) $request->get('search_repositories');
		$search_sources      = (bool) $request->get('search_sources');
		$search_notes        = (bool) $request->get('search_notes');

		// Default to individuals only
		if (!$search_individuals && !$search_families && !$search_repositories && !$search_sources && !$search_notes) {
			$search_individuals = true;
		}

		// What to search for?
		$search_terms = $this->extractSearchTerms($query);

		// What trees to seach?
		if (Site::getPreference('ALLOW_CHANGE_GEDCOM') === '1') {
			$all_trees = Tree::getAll();
		} else {
			$all_trees = [$tree];
		}

		$search_tree_names = (array) $request->get('search_trees', []);

		$search_trees = array_filter($all_trees, function (Tree $tree) use ($search_tree_names) {
			return in_array($tree->getName(), $search_tree_names);
		});

		if (empty($search_trees)) {
			$search_trees = [$tree];
		}

		// Force to be zero-indexed.
		$search_trees = array_values($search_trees);

		// Do the search
		if ($search_individuals && !empty($search_terms)) {
			$individuals = $this->searchIndividuals($search_terms, $search_trees);
		} else {
			$individuals = [];
		}

		if ($search_families && !empty($search_terms)) {
			$families = array_unique(array_merge(
				$this->searchFamilies($search_terms, $search_trees),
				$this->searchFamilyNames($search_terms, $search_trees)
			));
		} else {
			$families = [];
		}

		if ($search_repositories && !empty($search_terms)) {
			$repositories = $this->searchRepositories($search_terms, $search_trees);
		} else {
			$repositories = [];
		}

		if ($search_sources && !empty($search_terms)) {
			$sources = $this->searchSources($search_terms, $search_trees);
		} else {
			$sources = [];
		}

		if ($search_notes && !empty($search_terms)) {
			$notes = $this->searchNotes($search_terms, $search_trees);
		} else {
			$notes = [];
		}

		// If only 1 item is returned, automatically forward to that item
		if (count($individuals) === 1 && empty($families) && empty($sources) && empty($notes)) {
			return new RedirectResponse($individuals[0]->url());
		}

		if (empty($individuals) && count($families) === 1 && empty($sources) && empty($notes)) {
			return new RedirectResponse($sources[0]->url());
		}

		if (empty($individuals) && empty($families) && count($sources) === 1 && empty($notes)) {
			return new RedirectResponse($families[0]->url());
		}

		if (empty($individuals) && empty($families) && empty($sources) && count($notes) === 1) {
			return new RedirectResponse($notes[0]->url());
		}

		$title = I18N::translate('General search');

		return $this->viewResponse('search-general-page', [
			'all_trees'           => $all_trees,
			'families'            => $families,
			'individuals'         => $individuals,
			'notes'               => $notes,
			'query'               => $query,
			'repositories'        => $repositories,
			'search_families'     => $search_families,
			'search_individuals'  => $search_individuals,
			'search_notes'        => $search_notes,
			'search_repositories' => $search_repositories,
			'search_sources'      => $search_sources,
			'search_trees'        => $search_trees,
			'sources'             => $sources,
			'title'               => $title,
		]);
	}

	/**
	 * The phonetic search.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function phonetic(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$firstname = $request->get('firstname', '');
		$lastname  = $request->get('lastname', '');
		$place     = $request->get('place', '');
		$soundex   = $request->get('soundex', 'Russell');

		// What trees to seach?
		if (Site::getPreference('ALLOW_CHANGE_GEDCOM') === '1') {
			$all_trees = Tree::getAll();
		} else {
			$all_trees = [$tree];
		}

		$search_tree_names = (array) $request->get('search_trees', []);

		$search_trees = array_filter($all_trees, function (Tree $tree) use ($search_tree_names) {
			return in_array($tree->getName(), $search_tree_names);
		});

		if (empty($search_trees)) {
			$search_trees = [$tree];
		}

		// Force to be zero-indexed.
		$search_trees = array_values($search_trees);

		if ($firstname !== '' || $lastname !== '' || $place !== '') {
			$individuals = $this->searchIndividualsPhonetic($soundex, $lastname, $firstname, $place, $search_trees);
		} else {
			$individuals = [];
		}

		$title = I18N::translate('Phonetic search');

		return $this->viewResponse('search-phonetic-page', [
			'all_trees'    => $all_trees,
			'firstname'    => $firstname,
			'individuals'  => $individuals,
			'lastname'     => $lastname,
			'place'        => $place,
			'search_trees' => $search_trees,
			'soundex'      => $soundex,
			'title'        => $title,
		]);
	}

	/**
	 * Search and replace.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function replace(Request $request): Response {
		$search  = $request->get('search', '');
		$replace = $request->get('replace', '');
		$context = $request->get('context', '');

		if ($context !== 'name' && $context !== 'place') {
			$context = 'all';
		}

		$title = I18N::translate('Search and replace');

		return $this->viewResponse('search-replace-page', [
			'context' => $context,
			'replace' => $replace,
			'search'  => $search,
			'title'   => $title,
		]);
	}

	/**
	 * Search and replace.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function replaceAction(Request $request): RedirectResponse {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$search  = $request->get('search', '');
		$replace = $request->get('replace', '');
		$context = $request->get('context', '');

		switch ($context) {
			case 'all':
				$records = $this->searchIndividuals([$search], [$tree]);
				$count   = $this->replaceRecords($records, $search, $replace);
				FlashMessages::addMessage(I18N::plural('%s individual has been updated.', '%s individuals have been updated.', $count, I18N::number($count)));

				$records = $this->searchFamilies([$search], [$tree]);
				$count   = $this->replaceRecords($records, $search, $replace);
				FlashMessages::addMessage(I18N::plural('%s family has been updated.', '%s families have been updated.', $count, I18N::number($count)));

				$records = $this->searchRepositories([$search], [$tree]);
				$count   = $this->replaceRecords($records, $search, $replace);
				FlashMessages::addMessage(I18N::plural('%s repository has been updated.', '%s repositories have been updated.', $count, I18N::number($count)));

				$records = $this->searchSources([$search], [$tree]);
				$count   = $this->replaceRecords($records, $search, $replace);
				FlashMessages::addMessage(I18N::plural('%s source has been updated.', '%s sources have been updated.', $count, I18N::number($count)));

				$records = $this->searchNotes([$search], [$tree]);
				$count   = $this->replaceRecords($records, $search, $replace);
				FlashMessages::addMessage(I18N::plural('%s note has been updated.', '%s notes have been updated.', $count, I18N::number($count)));
				break;

			case 'name':
				$adv_name_tags = preg_split("/[\s,;: ]+/", $tree->getPreference('ADVANCED_NAME_FACTS'));
				$name_tags     = array_unique(array_merge(['NAME', 'NPFX', 'GIVN', 'SPFX', 'SURN', 'NSFX', '_MARNM', '_AKA'], $adv_name_tags));

				$records = $this->searchIndividuals([$search], [$tree]);
				$count   = $this->replaceIndividualNames($records, $search, $replace, $name_tags);
				FlashMessages::addMessage(I18N::plural('%s individual has been updated.', '%s individuals have been updated.', $count, I18N::number($count)));
				break;

			case 'place':
				$records = $this->searchIndividuals([$search], [$tree]);
				$count   = $this->replacePlaces($records, $search, $replace);
				FlashMessages::addMessage(I18N::plural('%s individual has been updated.', '%s individuals have been updated.', $count, I18N::number($count)));

				$records = $this->searchFamilies([$search], [$tree]);
				$count   = $this->replacePlaces($records, $search, $replace);
				FlashMessages::addMessage(I18N::plural('%s family has been updated.', '%s families have been updated.', $count, I18N::number($count)));
				break;
		}

		$url = route('search-replace', [
			'search'  => $search,
			'replace' => $replace,
			'context' => $context,
			'ged'     => $tree->getName(),
		]);

		return new RedirectResponse($url);
	}

	/**
	 * A structured search.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function advanced(Request $request): Response {
		$default_fields = array_fill_keys(self::DEFAULT_ADVANCED_FIELDS, '');

		$fields      = $request->get('fields', $default_fields);
		$modifiers   = $request->get('modifiers', []);
		$other_field = $request->get('other_field', '');
		$other_value = $request->get('other_value', '');

		if ($other_field !== '' && $other_value !== '') {
			$fields[$other_field] = $other_value;
		}

		$other_fields = $this->otherFields($fields);
		$date_options = $this->dateOptions();
		$name_options = $this->nameOptions();

		if (!empty(array_filter($fields))) {
			$individuals = $this->searchIndividualsAdvanced($fields, $modifiers);
		} else {
			$individuals = [];
		}

		$title = I18N::translate('Advanced search');

		return $this->viewResponse('search-advanced-page', [
			'date_options' => $date_options,
			'fields'       => $fields,
			'individuals'  => $individuals,
			'modifiers'    => $modifiers,
			'name_options' => $name_options,
			'other_fields' => $other_fields,
			'title'        => $title,
		]);
	}

	/**
	 * @param GedcomRecord[] $records
	 * @param string         $search
	 * @param string         $replace
	 * @param string[]       $name_tags
	 *
	 * @return int
	 */
	private function replaceIndividualNames(array $records, string $search, string $replace, array $name_tags): int {
		$pattern     = '/(\n\d (?:' . implode('|', $name_tags) . ') (?:.*))' . preg_quote($search, '/') . '/i';
		$replacement = '$1' . $replace;
		$count       = 0;

		foreach ($records as $record) {
			$old_gedcom = $record->getGedcom();
			$new_gedcom = preg_replace($pattern, $replacement, $old_gedcom);

			if ($new_gedcom !== $old_gedcom) {
				$record->updateRecord($new_gedcom, true);
				$count++;
			}
		}

		return $count;
	}

	/**
	 * @param GedcomRecord[] $records
	 * @param string         $search
	 * @param string         $replace
	 *
	 * @return int
	 */
	private function replacePlaces(array $records, string $search, string $replace): int {
		$pattern     = '/(\n\d PLAC\b.* )' . preg_quote($search, '/') . '([,\n])/i';
		$replacement = '$1' . $replace . '$2';
		$count       = 0;

		foreach ($records as $record) {
			$old_gedcom = $record->getGedcom();
			$new_gedcom = preg_replace($pattern, $replacement, $old_gedcom);

			if ($new_gedcom !== $old_gedcom) {
				$record->updateRecord($new_gedcom, true);
				$count++;
			}
		}

		return $count;
	}

	/**
	 * @param GedcomRecord[] $records
	 * @param string         $search
	 * @param string         $replace
	 *
	 * @return int
	 */
	private function replaceRecords(array $records, string $search, string $replace): int {
		$count = 0;
		$query = preg_quote($search, '/');

		foreach ($records as $record) {
			$old_record = $record->getGedcom();
			$new_record = preg_replace('/(\n\d [A-Z0-9_]+ )' . $query . '/i', '$1' . $replace, $old_record);

			if ($new_record !== $old_record) {
				$record->updateRecord($new_record, true);
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Extra search fields to add to the advanced search
	 *
	 * @param string[] $fields
	 *
	 * @return string[]
	 */
	private function otherFields(array $fields): array {
		$unused = array_diff(self::OTHER_ADVANCED_FIELDS, array_keys($fields));

		$other_fileds = [];

		foreach ($unused as $tag) {
			$other_fileds[$tag] = GedcomTag::getLabel($tag);
		}

		return $other_fileds;
	}

	/**
	 * For the advanced search
	 *
	 * @return string[]
	 */
	private function dateOptions(): array {
		return [
			0  => I18N::translate('Exact date'),
			2  => I18N::plural('±%s year', '±%s years', 2, I18N::number(2)),
			5  => I18N::plural('±%s year', '±%s years', 5, I18N::number(5)),
			10 => I18N::plural('±%s year', '±%s years', 10, I18N::number(10)),
		];
	}

	/**
	 * For the advanced search
	 *
	 * @return string[]
	 */
	private function nameOptions(): array {
		return [
			'EXACT'    => I18N::translate('Exact'),
			'BEGINS'   => I18N::translate('Begins with'),
			'CONTAINS' => I18N::translate('Contains'),
			'SDX'      => I18N::translate('Sounds like'),
		];
	}

	/**
	 * Convert the query into an array of search terms
	 *
	 * @param string $query
	 *
	 * @return string[]
	 */
	private function extractSearchTerms(string $query): array {
		$search_terms = [];

		// Words in double quotes stay together
		while (preg_match('/"([^"]+)"/', $query, $match)) {
			$search_terms[] = trim($match[1]);
			$query          = str_replace($match[0], '', $query);
		}

		// Other words get treated separately
		while (preg_match('/[\S]+/', $query, $match)) {
			$search_terms[] = trim($match[0]);
			$query          = str_replace($match[0], '', $query);
		}

		return $search_terms;
	}

	/**
	 * @param string[] $search_terms
	 * @param Tree[]   $search_trees
	 *
	 * @return Family[]
	 */
	private function searchFamilies(array $search_terms, array $search_trees): array {
		// Convert the query into a regular expression
		$queryregex = [];

		$sql  = "SELECT f_id AS xref, f_file AS gedcom_id, f_gedcom AS gedcom FROM `##families` WHERE 1";
		$args = [];

		foreach ($search_terms as $n => $q) {
			$queryregex[]          = preg_quote(I18N::strtoupper($q), '/');
			$sql                   .= " AND f_gedcom COLLATE :collate_" . $n . " LIKE CONCAT('%', :query_" . $n . ", '%')";
			$args['collate_' . $n] = I18N::collation();
			$args['query_' . $n]   = Database::escapeLike($q);
		}

		$sql .= " AND f_file IN (";
		foreach ($search_trees as $n => $tree) {
			$sql                   .= $n ? ', ' : '';
			$sql                   .= ':tree_id_' . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";

		$list = [];
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			// SQL may have matched on private data or gedcom tags, so check again against privatized data.
			$record = Family::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
			// Ignore non-genealogy data
			$gedrec = preg_replace('/\n\d (_UID|_WT_USER|FILE|FORM|TYPE|CHAN|RESN) .*/', '', $record->getGedcom());
			// Ignore links and tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . '( @' . WT_REGEX_XREF . '@)?/', '', $gedrec);
			// Ignore tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . ' ?/', '', $gedrec);
			// Re-apply the filtering
			$gedrec = I18N::strtoupper($gedrec);
			foreach ($queryregex as $regex) {
				if (!preg_match('/' . $regex . '/', $gedrec)) {
					continue 2;
				}
			}
			$list[] = $record;
		}
		$list = array_filter($list, function (Family $x) {
			return $x->canShowName();
		});

		return $list;
	}

	/**
	 * @param string[] $search_terms
	 * @param Tree[]   $search_trees
	 *
	 * @return Family[]
	 */
	private function searchFamilyNames(array $search_terms, array $search_trees): array {
		$sql  =
			"SELECT DISTINCT f_id AS xref, f_file AS gedcom_id, f_gedcom AS gedcom" .
			" FROM `##families`" .
			" LEFT JOIN `##name` husb ON f_husb = husb.n_id AND f_file = husb.n_file" .
			" LEFT JOIN `##name` wife ON f_wife = wife.n_id AND f_file = wife.n_file" .
			" WHERE 1";
		$args = [];

		foreach ($search_terms as $n => $q) {
			$sql                        .= " AND (husb.n_full COLLATE :husb_collate_" . $n . " LIKE CONCAT('%', :husb_query_" . $n . ", '%') OR wife.n_full COLLATE :wife_collate_" . $n . " LIKE CONCAT('%', :wife_query_" . $n . ", '%'))";
			$args['husb_collate_' . $n] = I18N::collation();
			$args['husb_query_' . $n]   = Database::escapeLike($q);
			$args['wife_collate_' . $n] = I18N::collation();
			$args['wife_query_' . $n]   = Database::escapeLike($q);
		}

		$sql .= " AND f_file IN (";

		foreach ($search_trees as $n => $tree) {
			$sql                   .= $n ? ", " : "";
			$sql                   .= ":tree_id_" . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";

		$list = [];
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			$list[] = Family::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
		}

		$list = array_filter($list, function (Family $x) use ($search_terms) {
			$name = I18N::strtolower(strip_tags($x->getFullName()));
			foreach ($search_terms as $q) {
				if (stripos($name, I18N::strtolower($q)) === false) {
					return false;
				}
			}

			return true;
		});

		return $list;
	}

	/**
	 * @param string[] $search_terms
	 * @param Tree[]   $search_trees
	 *
	 * @return Individual[]
	 */
	private function searchIndividuals(array $search_terms, array $search_trees): array {
		// Convert the query into a regular expression
		$queryregex = [];

		$sql  = "SELECT i_id AS xref, i_file AS gedcom_id, i_gedcom AS gedcom FROM `##individuals` WHERE 1";
		$args = [];

		foreach ($search_terms as $n => $q) {
			$queryregex[]          = preg_quote(I18N::strtoupper($q), '/');
			$sql                   .= " AND i_gedcom COLLATE :collate_" . $n . " LIKE CONCAT('%', :query_" . $n . ", '%')";
			$args['collate_' . $n] = I18N::collation();
			$args['query_' . $n]   = Database::escapeLike($q);
		}

		$sql .= " AND i_file IN (";
		foreach ($search_trees as $n => $tree) {
			$sql                   .= $n ? ", " : "";
			$sql                   .= ":tree_id_" . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";

		$list = [];
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			// SQL may have matched on private data or gedcom tags, so check again against privatized data.
			$record = Individual::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
			// Ignore non-genealogy data
			$gedrec = preg_replace('/\n\d (_UID|_WT_USER|FILE|FORM|TYPE|CHAN|RESN) .*/', '', $record->getGedcom());
			// Ignore links and tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . '( @' . WT_REGEX_XREF . '@)?/', '', $gedrec);
			// Re-apply the filtering
			$gedrec = I18N::strtoupper($gedrec);
			foreach ($queryregex as $regex) {
				if (!preg_match('/' . $regex . '/', $gedrec)) {
					continue 2;
				}
			}
			$list[] = $record;
		}
		$list = array_filter($list, function (Individual $x) {
			return $x->canShowName();
		});

		return $list;
	}

	/**
	 * @param string[] $fields
	 * @param string[] $modifiers
	 *
	 * @return Individual[]
	 */
	private function searchIndividualsAdvanced(array $fields, array $modifiers): array {
		$fields = array_filter($fields);

		// Dynamic SQL query, plus bind variables
		$sql  = 'SELECT DISTINCT ind.i_id AS xref, ind.i_gedcom AS gedcom FROM `##individuals` ind';
		$bind = [];

		// Join the following tables
		$father_name   = false;
		$mother_name   = false;
		$spouse_family = false;
		$indi_name     = false;
		$indi_date     = false;
		$fam_date      = false;
		$indi_plac     = false;
		$fam_plac      = false;

		foreach ($fields as $field_name => $field_value) {
			if ($field_value !== '') {
				if (substr($field_name, 0, 14) === 'FAMC:HUSB:NAME') {
					$father_name = true;
				} elseif (substr($field_name, 0, 14) === 'FAMC:WIFE:NAME') {
					$mother_name = true;
				} elseif (substr($field_name, 0, 4) === 'NAME') {
					$indi_name = true;
				} elseif (strpos($field_name, ':DATE') !== false) {
					if (substr($field_name, 0, 4) === 'FAMS') {
						$fam_date      = true;
						$spouse_family = true;
					} else {
						$indi_date = true;
					}
				} elseif (strpos($field_name, ':PLAC') !== false) {
					if (substr($field_name, 0, 4) === 'FAMS') {
						$fam_plac      = true;
						$spouse_family = true;
					} else {
						$indi_plac = true;
					}
				} elseif ($field_name === 'FAMS:NOTE') {
					$spouse_family = true;
				}
			}
		}

		if ($father_name || $mother_name) {
			$sql .= " JOIN `##link`   l_1 ON (l_1.l_file=ind.i_file AND l_1.l_from=ind.i_id AND l_1.l_type='FAMC')";
		}
		if ($father_name) {
			$sql .= " JOIN `##link`   l_2 ON (l_2.l_file=ind.i_file AND l_2.l_from=l_1.l_to AND l_2.l_type='HUSB')";
			$sql .= " JOIN `##name`   f_n ON (f_n.n_file=ind.i_file AND f_n.n_id  =l_2.l_to)";
		}
		if ($mother_name) {
			$sql .= " JOIN `##link`   l_3 ON (l_3.l_file=ind.i_file AND l_3.l_from=l_1.l_to AND l_3.l_type='WIFE')";
			$sql .= " JOIN `##name`   m_n ON (m_n.n_file=ind.i_file AND m_n.n_id  =l_3.l_to)";
		}
		if ($spouse_family) {
			$sql .= " JOIN `##link`     l_4 ON (l_4.l_file=ind.i_file AND l_4.l_from=ind.i_id AND l_4.l_type='FAMS')";
			$sql .= " JOIN `##families` fam ON (fam.f_file=ind.i_file AND fam.f_id  =l_4.l_to)";
		}
		if ($indi_name) {
			$sql .= " JOIN `##name`   i_n ON (i_n.n_file=ind.i_file AND i_n.n_id=ind.i_id)";
		}
		if ($indi_date) {
			$sql .= " JOIN `##dates`  i_d ON (i_d.d_file=ind.i_file AND i_d.d_gid=ind.i_id)";
		}
		if ($fam_date) {
			$sql .= " JOIN `##dates`  f_d ON (f_d.d_file=ind.i_file AND f_d.d_gid=fam.f_id)";
		}
		if ($indi_plac) {
			$sql .= " JOIN `##placelinks`   i_pl ON (i_pl.pl_file=ind.i_file AND i_pl.pl_gid =ind.i_id)";
			$sql .= " JOIN (" .
				"SELECT CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place) AS place, p1.p_id AS id, p1.p_file AS file" .
				" FROM      `##places` AS p1" .
				" LEFT JOIN `##places` AS p2 ON (p1.p_parent_id=p2.p_id)" .
				" LEFT JOIN `##places` AS p3 ON (p2.p_parent_id=p3.p_id)" .
				" LEFT JOIN `##places` AS p4 ON (p3.p_parent_id=p4.p_id)" .
				" LEFT JOIN `##places` AS p5 ON (p4.p_parent_id=p5.p_id)" .
				" LEFT JOIN `##places` AS p6 ON (p5.p_parent_id=p6.p_id)" .
				" LEFT JOIN `##places` AS p7 ON (p6.p_parent_id=p7.p_id)" .
				" LEFT JOIN `##places` AS p8 ON (p7.p_parent_id=p8.p_id)" .
				" LEFT JOIN `##places` AS p9 ON (p8.p_parent_id=p9.p_id)" .
				") AS i_p ON (i_p.file  =ind.i_file AND i_pl.pl_p_id= i_p.id)";
		}
		if ($fam_plac) {
			$sql .= " JOIN `##placelinks`   f_pl ON (f_pl.pl_file=ind.i_file AND f_pl.pl_gid =fam.f_id)";
			$sql .= " JOIN (" .
				"SELECT CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place) AS place, p1.p_id AS id, p1.p_file AS file" .
				" FROM      `##places` AS p1" .
				" LEFT JOIN `##places` AS p2 ON (p1.p_parent_id=p2.p_id)" .
				" LEFT JOIN `##places` AS p3 ON (p2.p_parent_id=p3.p_id)" .
				" LEFT JOIN `##places` AS p4 ON (p3.p_parent_id=p4.p_id)" .
				" LEFT JOIN `##places` AS p5 ON (p4.p_parent_id=p5.p_id)" .
				" LEFT JOIN `##places` AS p6 ON (p5.p_parent_id=p6.p_id)" .
				" LEFT JOIN `##places` AS p7 ON (p6.p_parent_id=p7.p_id)" .
				" LEFT JOIN `##places` AS p8 ON (p7.p_parent_id=p8.p_id)" .
				" LEFT JOIN `##places` AS p9 ON (p8.p_parent_id=p9.p_id)" .
				") AS f_p ON (f_p.file  =ind.i_file AND f_pl.pl_p_id= f_p.id)";
		}

		// Add the where clause
		$sql    .= " WHERE ind.i_file=?";
		$bind[] = $this->tree()->getTreeId();

		foreach ($fields as $field_name => $field_value) {
			$parts = preg_split('/:/', $field_name . '::::');
			if ($parts[0] === 'NAME') {
				// NAME:*
				switch ($parts[1]) {
					case 'GIVN':
						switch ($modifiers[$field_name]) {
							case 'EXACT':
								$sql    .= " AND i_n.n_givn=?";
								$bind[] = $field_value;
								break;
							case 'BEGINS':
								$sql    .= " AND i_n.n_givn LIKE CONCAT(?, '%')";
								$bind[] = $field_value;
								break;
							case 'CONTAINS':
								$sql    .= " AND i_n.n_givn LIKE CONCAT('%', ?, '%')";
								$bind[] = $field_value;
								break;
							case 'SDX_STD':
								$sdx = Soundex::russell($field_value);
								if ($sdx !== null) {
									$sdx = explode(':', $sdx);
									foreach ($sdx as $k => $v) {
										$sdx[$k] = "i_n.n_soundex_givn_std LIKE CONCAT('%', ?, '%')";
										$bind[]  = $v;
									}
									$sql .= ' AND (' . implode(' OR ', $sdx) . ')';
								} else {
									// No phonetic content? Use a substring match
									$sql    .= " AND i_n.n_givn LIKE CONCAT('%', ?, '%')";
									$bind[] = $field_value;
								}
								break;
							case 'SDX': // SDX uses DM by default.
							case 'SDX_DM':
								$sdx = Soundex::daitchMokotoff($field_value);
								if ($sdx !== '') {
									$sdx = explode(':', $sdx);
									foreach ($sdx as $k => $v) {
										$sdx[$k] = "i_n.n_soundex_givn_dm LIKE CONCAT('%', ?, '%')";
										$bind[]  = $v;
									}
									$sql .= ' AND (' . implode(' OR ', $sdx) . ')';
								} else {
									// No phonetic content? Use a substring match
									$sql    .= " AND i_n.n_givn LIKE CONCAT('%', ?, '%')";
									$bind[] = $field_value;
								}
								break;
						}
						break;
					case 'SURN':
						switch ($modifiers[$field_name]) {
							case 'EXACT':
								$sql    .= " AND i_n.n_surname=?";
								$bind[] = $field_value;
								break;
							case 'BEGINS':
								$sql    .= " AND i_n.n_surname LIKE CONCAT(?, '%')";
								$bind[] = $field_value;
								break;
							case 'CONTAINS':
								$sql    .= " AND i_n.n_surname LIKE CONCAT('%', ?, '%')";
								$bind[] = $field_value;
								break;
							case 'SDX_STD':
								$sdx = Soundex::russell($field_value);
								if ($sdx !== null) {
									$sdx = explode(':', $sdx);
									foreach ($sdx as $k => $v) {
										$sdx[$k] = "i_n.n_soundex_surn_std LIKE CONCAT('%', ?, '%')";
										$bind[]  = $v;
									}
									$sql .= " AND (" . implode(' OR ', $sdx) . ")";
								} else {
									// No phonetic content? Use a substring match
									$sql    .= " AND i_n.n_surn LIKE CONCAT('%', ?, '%')";
									$bind[] = $field_value;
								}
								break;
							case 'SDX': // SDX uses DM by default.
							case 'SDX_DM':
								$sdx = Soundex::daitchMokotoff($field_value);
								if ($sdx !== '') {
									$sdx = explode(':', $sdx);
									foreach ($sdx as $k => $v) {
										$sdx[$k] = "i_n.n_soundex_surn_dm LIKE CONCAT('%', ?, '%')";
										$bind[]  = $v;
									}
									$sql .= " AND (" . implode(' OR ', $sdx) . ")";
									break;
								} else {
									// No phonetic content? Use a substring match
									$sql    .= " AND i_n.n_surn LIKE CONCAT('%', ?, '%')";
									$bind[] = $field_value;
								}
						}
						break;
					case 'NICK':
					case '_MARNM':
					case '_HEB':
					case '_AKA':
						$sql    .= " AND i_n.n_type=? AND i_n.n_full LIKE CONCAT('%', ?, '%')";
						$bind[] = $parts[1];
						$bind[] = $field_value;
						break;
				}
			} elseif ($parts[1] === 'DATE') {
				// *:DATE
				$date = new Date($field_value);
				if ($date->isOK()) {
					$delta  = 365 * ($modifiers[$field_name] ?? 0);
					$sql    .= " AND i_d.d_fact=? AND i_d.d_julianday1>=? AND i_d.d_julianday2<=?";
					$bind[] = $parts[0];
					$bind[] = $date->minimumJulianDay() - $delta;
					$bind[] = $date->maximumJulianDay() + $delta;
				}
			} elseif ($parts[0] === 'FAMS' && $parts[2] === 'DATE') {
				// FAMS:*:DATE
				$date = new Date($field_value);
				if ($date->isOK()) {
					$delta  = 365 * $modifiers[$field_name];
					$sql    .= " AND f_d.d_fact=? AND f_d.d_julianday1>=? AND f_d.d_julianday2<=?";
					$bind[] = $parts[1];
					$bind[] = $date->minimumJulianDay() - $delta;
					$bind[] = $date->maximumJulianDay() + $delta;
				}
			} elseif ($parts[1] === 'PLAC') {
				// *:PLAC
				// SQL can only link a place to a person/family, not to an event.
				$sql    .= " AND i_p.place LIKE CONCAT('%', ?, '%')";
				$bind[] = $field_value;
			} elseif ($parts[0] === 'FAMS' && $parts[2] === 'PLAC') {
				// FAMS:*:PLAC
				// SQL can only link a place to a person/family, not to an event.
				$sql    .= " AND f_p.place LIKE CONCAT('%', ?, '%')";
				$bind[] = $field_value;
			} elseif ($parts[0] === 'FAMC' && $parts[2] === 'NAME') {
				$table = $parts[1] === 'HUSB' ? 'f_n' : 'm_n';
				// NAME:*
				switch ($parts[3]) {
					case 'GIVN':
						switch ($modifiers[$field_name]) {
							case 'EXACT':
								$sql    .= " AND {$table}.n_givn=?";
								$bind[] = $field_value;
								break;
							case 'BEGINS':
								$sql    .= " AND {$table}.n_givn LIKE CONCAT(?, '%')";
								$bind[] = $field_value;
								break;
							case 'CONTAINS':
								$sql    .= " AND {$table}.n_givn LIKE CONCAT('%', ?, '%')";
								$bind[] = $field_value;
								break;
							case 'SDX_STD':
								$sdx = Soundex::russell($field_value);
								if ($sdx !== null) {
									$sdx = explode(':', $sdx);
									foreach ($sdx as $k => $v) {
										$sdx[$k] = "{$table}.n_soundex_givn_std LIKE CONCAT('%', ?, '%')";
										$bind[]  = $v;
									}
									$sql .= ' AND (' . implode(' OR ', $sdx) . ')';
								} else {
									// No phonetic content? Use a substring match
									$sql    .= " AND {$table}.n_givn = LIKE CONCAT('%', ?, '%')";
									$bind[] = $field_value;
								}
								break;
							case 'SDX': // SDX uses DM by default.
							case 'SDX_DM':
								$sdx = Soundex::daitchMokotoff($field_value);
								if ($sdx !== '') {
									$sdx = explode(':', $sdx);
									foreach ($sdx as $k => $v) {
										$sdx[$k] = "{$table}.n_soundex_givn_dm LIKE CONCAT('%', ?, '%')";
										$bind[]  = $v;
									}
									$sql .= ' AND (' . implode(' OR ', $sdx) . ')';
									break;
								} else {
									// No phonetic content? Use a substring match
									$sql    .= " AND {$table}.n_givn = LIKE CONCAT('%', ?, '%')";
									$bind[] = $field_value;
								}
						}
						break;
					case 'SURN':
						switch ($modifiers[$field_name]) {
							case 'EXACT':
								$sql    .= " AND {$table}.n_surname=?";
								$bind[] = $field_value;
								break;
							case 'BEGINS':
								$sql    .= " AND {$table}.n_surname LIKE CONCAT(?, '%')";
								$bind[] = $field_value;
								break;
							case 'CONTAINS':
								$sql    .= " AND {$table}.n_surname LIKE CONCAT('%', ?, '%')";
								$bind[] = $field_value;
								break;
							case 'SDX_STD':
								$sdx = Soundex::russell($field_value);
								if ($sdx !== null) {
									$sdx = explode(':', $sdx);
									foreach ($sdx as $k => $v) {
										$sdx[$k] = "{$table}.n_soundex_surn_std LIKE CONCAT('%', ?, '%')";
										$bind[]  = $v;
									}
									$sql .= ' AND (' . implode(' OR ', $sdx) . ')';
								} else {
									// No phonetic content? Use a substring match
									$sql    .= " AND {$table}.n_surn = LIKE CONCAT('%', ?, '%')";
									$bind[] = $field_value;
								}
								break;
							case 'SDX': // SDX uses DM by default.
							case 'SDX_DM':
								$sdx = Soundex::daitchMokotoff($field_value);
								if ($sdx !== '') {
									$sdx = explode(':', $sdx);
									foreach ($sdx as $k => $v) {
										$sdx[$k] = "{$table}.n_soundex_surn_dm LIKE CONCAT('%', ?, '%')";
										$bind[]  = $v;
									}
									$sql .= ' AND (' . implode(' OR ', $sdx) . ')';
								} else {
									// No phonetic content? Use a substring match
									$sql    .= " AND {$table}.n_surn = LIKE CONCAT('%', ?, '%')";
									$bind[] = $field_value;
								}
								break;
						}
						break;
				}
			} elseif ($parts[0] === 'FAMS') {
				// e.g. searches for occupation, religion, note, etc.
				$sql    .= " AND fam.f_gedcom REGEXP CONCAT('\n[0-9] ', ?, '(.*\n[0-9] CONT)* [^\n]*', ?)";
				$bind[] = $parts[1];
				$bind[] = $field_value;
			} elseif ($parts[1] === 'TYPE') {
				// e.g. FACT:TYPE or EVEN:TYPE
				$sql    .= " AND ind.i_gedcom REGEXP CONCAT('\n1 ', ?, '.*(\n[2-9] .*)*\n2 TYPE .*', ?)";
				$bind[] = $parts[0];
				$bind[] = $field_value;
			} else {
				// e.g. searches for occupation, religion, note, etc.
				$sql    .= " AND ind.i_gedcom REGEXP CONCAT('\n[0-9] ', ?, '(.*\n[0-9] CONT)* [^\n]*', ?)";
				$bind[] = $parts[0];
				$bind[] = $field_value;
			}
		}

		$rows = Database::prepare($sql)->execute($bind)->fetchAll();

		$individuals = [];

		foreach ($rows as $row) {
			$person = Individual::getInstance($row->xref, $this->tree(), $row->gedcom);
			// Check for XXXX:PLAC fields, which were only partially matched by SQL
			foreach ($fields as $field_name => $field_value) {
				if (preg_match('/^(' . WT_REGEX_TAG . '):PLAC$/', $field_name, $match)) {
					if (!preg_match('/\n1 ' . $match[1] . '(\n[2-9].*)*\n2 PLAC .*' . preg_quote($field_value, '/') . '/i', $person->getGedcom())) {
						continue 2;
					}
				}
			}
			$individuals[] = $person;
		}

		return $individuals;
	}

	/**
	 * @param string $soundex
	 * @param string $lastname
	 * @param string $firstname
	 * @param string $place
	 * @param Tree[] $search_trees
	 *
	 * @return Individual[]
	 */
	private function searchIndividualsPhonetic(string $soundex, string $lastname, string $firstname, string $place, array $search_trees): array {
		$givn_sdx = '';
		$surn_sdx = '';
		$plac_sdx = '';

		switch ($soundex) {
			case 'Russell':
				$givn_sdx = Soundex::russell($firstname);
				$surn_sdx = Soundex::russell($lastname);
				$plac_sdx = Soundex::russell($place);
				break;
			case 'DaitchM':
				$givn_sdx = Soundex::daitchMokotoff($firstname);
				$surn_sdx = Soundex::daitchMokotoff($lastname);
				$plac_sdx = Soundex::daitchMokotoff($place);
				break;
		}

		// Nothing to search for? Return nothing.
		if ($givn_sdx === '' && $surn_sdx === '' && $plac_sdx === '') {
			return [];
		}

		$sql  = "SELECT DISTINCT i_id AS xref, i_file AS gedcom_id, i_gedcom AS gedcom FROM `##individuals`";
		$args = [];

		if ($place !== '') {
			$sql .= " JOIN `##placelinks` ON pl_file = i_file AND pl_gid = i_id";
			$sql .= " JOIN `##places` ON p_file = pl_file AND pl_p_id = p_id";
		}
		if ($firstname !== '' || $lastname !== '') {
			$sql .= " JOIN `##name` ON i_file=n_file AND i_id=n_id";
		}
		$sql .= " AND i_file IN (";

		foreach ($search_trees as $n => $tree) {
			$sql                   .= $n ? ", " : "";
			$sql                   .= ":tree_id_" . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";

		if ($givn_sdx !== '') {
			$sql      .= " AND (";
			$givn_sdx = explode(':', $givn_sdx);
			foreach ($givn_sdx as $n => $sdx) {
				$sql .= $n > 0 ? " OR " : "";
				switch ($soundex) {
					case 'Russell':
						$sql .= "n_soundex_givn_std LIKE CONCAT('%', :given_name_" . $n . ", '%')";
						break;
					case 'DaitchM':
						$sql .= "n_soundex_givn_dm LIKE CONCAT('%', :given_name_" . $n . ", '%')";
						break;
				}
				$args['given_name_' . $n] = $sdx;
			}
			$sql .= ")";
		}

		if ($surn_sdx !== '') {
			$sql      .= " AND (";
			$surn_sdx = explode(':', $surn_sdx);
			foreach ($surn_sdx as $n => $sdx) {
				$sql .= $n ? " OR " : "";
				switch ($soundex) {
					case 'Russell':
						$sql .= "n_soundex_surn_std LIKE CONCAT('%', :surname_" . $n . ", '%')";
						break;
					case 'DaitchM':
						$sql .= "n_soundex_surn_dm LIKE CONCAT('%', :surname_" . $n . ", '%')";
						break;
				}
				$args['surname_' . $n] = $sdx;
			}
			$sql .= ")";
		}

		if ($plac_sdx !== '') {
			$sql      .= " AND (";
			$plac_sdx = explode(':', $plac_sdx);
			foreach ($plac_sdx as $n => $sdx) {
				$sql .= $n ? " OR " : "";
				switch ($soundex) {
					case 'Russell':
						$sql .= "p_std_soundex LIKE CONCAT('%', :place_" . $n . ", '%')";
						break;
					case 'DaitchM':
						$sql .= "p_dm_soundex LIKE CONCAT('%', :place_" . $n . ", '%')";
						break;
				}
				$args['place_' . $n] = $sdx;
			}
			$sql .= ")";
		}

		$list = [];
		$rows = Database::prepare($sql)->execute($args)->fetchAll();

		foreach ($rows as $row) {
			$list[] = Individual::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
		}

		$list = array_filter($list, function (Individual $x) {
			return $x->canShowName();
		});

		return $list;
	}

	/**
	 * @param string[] $search_terms
	 * @param Tree[]   $search_trees
	 *
	 * @return Note[]
	 */
	private function searchNotes(array $search_terms, array $search_trees): array {
		// Convert the query into a regular expression
		$queryregex = [];

		$sql  = "SELECT o_id AS xref, o_file AS gedcom_id, o_gedcom AS gedcom FROM `##other` WHERE o_type = 'NOTE'";
		$args = [];

		foreach ($search_terms as $n => $q) {
			$queryregex[]          = preg_quote(I18N::strtoupper($q), '/');
			$sql                   .= " AND o_gedcom COLLATE :collate_" . $n . " LIKE CONCAT('%', :query_" . $n . ", '%')";
			$args['collate_' . $n] = I18N::collation();
			$args['query_' . $n]   = Database::escapeLike($q);
		}

		$sql .= " AND o_file IN (";
		foreach ($search_trees as $n => $tree) {
			$sql                   .= $n ? ", " : "";
			$sql                   .= ":tree_id_" . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";

		$list = [];
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			// SQL may have matched on private data or gedcom tags, so check again against privatized data.
			$record = Note::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
			// Ignore non-genealogy data
			$gedrec = preg_replace('/\n\d (_UID|_WT_USER|FILE|FORM|TYPE|CHAN|RESN) .*/', '', $record->getGedcom());
			// Ignore links and tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . '( @' . WT_REGEX_XREF . '@)?/', '', $gedrec);
			// Ignore tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . ' ?/', '', $gedrec);
			// Re-apply the filtering
			$gedrec = I18N::strtoupper($gedrec);
			foreach ($queryregex as $regex) {
				if (!preg_match('/' . $regex . '/', $gedrec)) {
					continue 2;
				}
			}
			$list[] = $record;
		}
		$list = array_filter($list, function (Note $x) {
			return $x->canShowName();
		});

		return $list;
	}

	/**
	 * @param string[] $search_terms
	 * @param Tree[]   $search_trees
	 *
	 * @return Repository[]
	 */
	private function searchRepositories(array $search_terms, array $search_trees): array {
		// Convert the query into a regular expression
		$queryregex = [];

		$sql  = "SELECT o_id AS xref, o_file AS gedcom_id, o_gedcom AS gedcom FROM `##other` WHERE o_type = 'REPO'";
		$args = [];

		foreach ($search_terms as $n => $q) {
			$queryregex[]          = preg_quote(I18N::strtoupper($q), '/');
			$sql                   .= " AND o_gedcom COLLATE :collate_" . $n . " LIKE CONCAT('%', :query_" . $n . ", '%')";
			$args['collate_' . $n] = I18N::collation();
			$args['query_' . $n]   = Database::escapeLike($q);
		}

		$sql .= " AND o_file IN (";
		foreach ($search_trees as $n => $tree) {
			$sql                   .= $n ? ", " : "";
			$sql                   .= ":tree_id_" . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";

		$list = [];
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			// SQL may have matched on private data or gedcom tags, so check again against privatized data.
			$record = Repository::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
			// Ignore non-genealogy data
			$gedrec = preg_replace('/\n\d (_UID|_WT_USER|FILE|FORM|TYPE|CHAN|RESN) .*/', '', $record->getGedcom());
			// Ignore links and tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . '( @' . WT_REGEX_XREF . '@)?/', '', $gedrec);
			// Ignore tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . ' ?/', '', $gedrec);
			// Re-apply the filtering
			$gedrec = I18N::strtoupper($gedrec);
			foreach ($queryregex as $regex) {
				if (!preg_match('/' . $regex . '/', $gedrec)) {
					continue 2;
				}
			}
			$list[] = $record;
		}
		$list = array_filter($list, function (Repository $x) {
			return $x->canShowName();
		});

		return $list;
	}

	/**
	 * @param string[] $search_terms
	 * @param Tree[]   $search_trees
	 *
	 * @return Source[]
	 */
	private function searchSources(array $search_terms, array $search_trees): array {
		// Convert the query into a regular expression
		$queryregex = [];

		$sql  = "SELECT s_id AS xref, s_file AS gedcom_id, s_gedcom AS gedcom FROM `##sources` WHERE 1";
		$args = [];

		foreach ($search_terms as $n => $q) {
			$queryregex[]          = preg_quote(I18N::strtoupper($q), '/');
			$sql                   .= " AND s_gedcom COLLATE :collate_" . $n . " LIKE CONCAT('%', :query_" . $n . ", '%')";
			$args['collate_' . $n] = I18N::collation();
			$args['query_' . $n]   = Database::escapeLike($q);
		}

		$sql .= " AND s_file IN (";
		foreach ($search_trees as $n => $tree) {
			$sql                   .= $n ? ", " : "";
			$sql                   .= ":tree_id_" . $n;
			$args['tree_id_' . $n] = $tree->getTreeId();
		}
		$sql .= ")";

		$list = [];
		$rows = Database::prepare($sql)->execute($args)->fetchAll();
		foreach ($rows as $row) {
			// SQL may have matched on private data or gedcom tags, so check again against privatized data.
			$record = Source::getInstance($row->xref, Tree::findById($row->gedcom_id), $row->gedcom);
			// Ignore non-genealogy data
			$gedrec = preg_replace('/\n\d (_UID|_WT_USER|FILE|FORM|TYPE|CHAN|RESN) .*/', '', $record->getGedcom());
			// Ignore links and tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . '( @' . WT_REGEX_XREF . '@)?/', '', $gedrec);
			// Ignore tags
			$gedrec = preg_replace('/\n\d ' . WT_REGEX_TAG . ' ?/', '', $gedrec);
			// Re-apply the filtering
			$gedrec = I18N::strtoupper($gedrec);
			foreach ($queryregex as $regex) {
				if (!preg_match('/' . $regex . '/', $gedrec)) {
					continue 2;
				}
			}
			$list[] = $record;
		}
		$list = array_filter($list, function (Source $x) {
			return $x->canShowName();
		});

		return $list;
	}
}

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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Search for (and optionally replace) genealogy data
 */
class SearchController extends AbstractBaseController
{
    private const DEFAULT_ADVANCED_FIELDS = [
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

    private const OTHER_ADVANCED_FIELDS = [
        'ADOP:DATE',
        'ADOP:PLAC',
        'AFN',
        'BAPL:DATE',
        'BAPL:PLAC',
        'BAPM:DATE',
        'BAPM:PLAC',
        'BARM:DATE',
        'BARM:PLAC',
        'BASM:DATE',
        'BASM:PLAC',
        'BLES:DATE',
        'BLES:PLAC',
        'BURI:DATE',
        'BURI:PLAC',
        'CAST',
        'CENS:DATE',
        'CENS:PLAC',
        'CHAN:DATE',
        'CHAN:_WT_USER',
        'CHR:DATE',
        'CHR:PLAC',
        'CREM:DATE',
        'CREM:PLAC',
        'DSCR',
        'EMAIL',
        'EMIG:DATE',
        'EMIG:PLAC',
        'ENDL:DATE',
        'ENDL:PLAC',
        'EVEN',
        'EVEN:TYPE',
        'EVEN:DATE',
        'EVEN:PLAC',
        'FACT',
        'FACT:TYPE',
        'FAMS:CENS:DATE',
        'FAMS:CENS:PLAC',
        'FAMS:DIV:DATE',
        'FAMS:NOTE',
        'FAMS:SLGS:DATE',
        'FAMS:SLGS:PLAC',
        'FAX',
        'FCOM:DATE',
        'FCOM:PLAC',
        'IMMI:DATE',
        'IMMI:PLAC',
        'NAME:NICK',
        'NAME:_MARNM',
        'NAME:_HEB',
        'NAME:ROMN',
        'NATI',
        'NATU:DATE',
        'NATU:PLAC',
        'NOTE',
        'OCCU',
        'ORDN:DATE',
        'ORDN:PLAC',
        'REFN',
        'RELI',
        'RESI',
        'RESI:DATE',
        'RESI:PLAC',
        'SLGC:DATE',
        'SLGC:PLAC',
        'TITL',
        '_BRTM:DATE',
        '_BRTM:PLAC',
        '_MILI',
    ];

    /** @var SearchService */
    private $search_service;

    /**
     * SearchController constructor.
     *
     * @param SearchService $search_service
     */
    public function __construct(SearchService $search_service)
    {
        $this->search_service = $search_service;
    }

    /**
     * The "omni-search" box in the header.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function quick(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $query = $request->get('query', '');

        // Was the search query an XREF in the current tree?
        // If so, go straight to it.
        $record = GedcomRecord::getInstance($query, $tree);

        if ($record !== null && $record->canShow()) {
            return redirect($record->url());
        }

        return $this->general($request, $tree);
    }

    /**
     * The standard search.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function general(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
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

        $search_trees = array_filter($all_trees, static function (Tree $tree) use ($search_tree_names): bool {
            return in_array($tree->name(), $search_tree_names);
        });

        if (empty($search_trees)) {
            $search_trees = [$tree];
        }

        // Do the search
        if ($search_individuals && !empty($search_terms)) {
            $individuals = $this->search_service->searchIndividuals($search_trees, $search_terms);
        } else {
            $individuals = new Collection();
        }

        if ($search_families && !empty($search_terms)) {
            $tmp1 = $this->search_service->searchFamilies($search_trees, $search_terms);
            $tmp2 = $this->search_service->searchFamilyNames($search_trees, $search_terms);

            $families = $tmp1->merge($tmp2)->unique();
        } else {
            $families = new Collection();
        }

        if ($search_repositories && !empty($search_terms)) {
            $repositories = $this->search_service->searchRepositories($search_trees, $search_terms);
        } else {
            $repositories = new Collection();
        }

        if ($search_sources && !empty($search_terms)) {
            $sources = $this->search_service->searchSources($search_trees, $search_terms);
        } else {
            $sources = new Collection();
        }

        if ($search_notes && !empty($search_terms)) {
            $notes = $this->search_service->searchNotes($search_trees, $search_terms);
        } else {
            $notes = new Collection();
        }

        // If only 1 item is returned, automatically forward to that item
        if ($individuals->count() === 1 && $families->isEmpty() && $sources->isEmpty() && $notes->isEmpty()) {
            return redirect($individuals->first()->url());
        }

        if ($individuals->isEmpty() && $families->count() === 1 && $sources->isEmpty() && $notes->isEmpty()) {
            return redirect($families->first()->url());
        }

        if ($individuals->isEmpty() && $families->isEmpty() && $sources->count() === 1 && $notes->isEmpty()) {
            return redirect($sources->first()->url());
        }

        if ($individuals->isEmpty() && $families->isEmpty() && $sources->isEmpty() && $notes->count() === 1) {
            return redirect($notes->first()->url());
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
     * Convert the query into an array of search terms
     *
     * @param string $query
     *
     * @return string[]
     */
    private function extractSearchTerms(string $query): array
    {
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
     * The phonetic search.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function phonetic(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
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

        $search_trees = array_filter($all_trees, static function (Tree $tree) use ($search_tree_names): bool {
            return in_array($tree->name(), $search_tree_names);
        });

        if (empty($search_trees)) {
            $search_trees = [$tree];
        }

        $individuals = $this->search_service->searchIndividualsPhonetic($soundex, $lastname, $firstname, $place, $search_trees);

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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function replace(ServerRequestInterface $request): ResponseInterface
    {
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
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function replaceAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $search  = $request->get('search', '');
        $replace = $request->get('replace', '');
        $context = $request->get('context', '');

        switch ($context) {
            case 'all':
                $records = $this->search_service->searchIndividuals([$tree], [$search]);
                $count   = $this->replaceRecords($records, $search, $replace);
                FlashMessages::addMessage(I18N::plural('%s individual has been updated.', '%s individuals have been updated.', $count, I18N::number($count)));

                $records = $this->search_service->searchFamilies([$tree], [$search]);
                $count   = $this->replaceRecords($records, $search, $replace);
                FlashMessages::addMessage(I18N::plural('%s family has been updated.', '%s families have been updated.', $count, I18N::number($count)));

                $records = $this->search_service->searchRepositories([$tree], [$search]);
                $count   = $this->replaceRecords($records, $search, $replace);
                FlashMessages::addMessage(I18N::plural('%s repository has been updated.', '%s repositories have been updated.', $count, I18N::number($count)));

                $records = $this->search_service->searchSources([$tree], [$search]);
                $count   = $this->replaceRecords($records, $search, $replace);
                FlashMessages::addMessage(I18N::plural('%s source has been updated.', '%s sources have been updated.', $count, I18N::number($count)));

                $records = $this->search_service->searchNotes([$tree], [$search]);
                $count   = $this->replaceRecords($records, $search, $replace);
                FlashMessages::addMessage(I18N::plural('%s note has been updated.', '%s notes have been updated.', $count, I18N::number($count)));
                break;

            case 'name':
                $adv_name_tags = preg_split("/[\s,;: ]+/", $tree->getPreference('ADVANCED_NAME_FACTS'));
                $name_tags     = array_unique(array_merge([
                    'NAME',
                    'NPFX',
                    'GIVN',
                    'SPFX',
                    'SURN',
                    'NSFX',
                    '_MARNM',
                    '_AKA',
                ], $adv_name_tags));

                $records = $this->search_service->searchIndividuals([$tree], [$search]);
                $count   = $this->replaceIndividualNames($records, $search, $replace, $name_tags);
                FlashMessages::addMessage(I18N::plural('%s individual has been updated.', '%s individuals have been updated.', $count, I18N::number($count)));
                break;

            case 'place':
                $records = $this->search_service->searchIndividuals([$tree], [$search]);
                $count   = $this->replacePlaces($records, $search, $replace);
                FlashMessages::addMessage(I18N::plural('%s individual has been updated.', '%s individuals have been updated.', $count, I18N::number($count)));

                $records = $this->search_service->searchFamilies([$tree], [$search]);
                $count   = $this->replacePlaces($records, $search, $replace);
                FlashMessages::addMessage(I18N::plural('%s family has been updated.', '%s families have been updated.', $count, I18N::number($count)));
                break;
        }

        $url = route('search-replace', [
            'search'  => $search,
            'replace' => $replace,
            'context' => $context,
            'ged'     => $tree->name(),
        ]);

        return redirect($url);
    }

    /**
     * @param Collection $records
     * @param string     $search
     * @param string     $replace
     *
     * @return int
     */
    private function replaceRecords(Collection $records, string $search, string $replace): int
    {
        $count = 0;
        $query = preg_quote($search, '/');

        foreach ($records as $record) {
            $old_record = $record->gedcom();
            $new_record = preg_replace('/(\n\d [A-Z0-9_]+ )' . $query . '/i', '$1' . $replace, $old_record);

            if ($new_record !== $old_record) {
                $record->updateRecord($new_record, true);
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param Collection $records
     * @param string     $search
     * @param string     $replace
     * @param string[]   $name_tags
     *
     * @return int
     */
    private function replaceIndividualNames(Collection $records, string $search, string $replace, array $name_tags): int
    {
        $pattern     = '/(\n\d (?:' . implode('|', $name_tags) . ') (?:.*))' . preg_quote($search, '/') . '/i';
        $replacement = '$1' . $replace;
        $count       = 0;

        foreach ($records as $record) {
            $old_gedcom = $record->gedcom();
            $new_gedcom = preg_replace($pattern, $replacement, $old_gedcom);

            if ($new_gedcom !== $old_gedcom) {
                $record->updateRecord($new_gedcom, true);
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param Collection $records
     * @param string     $search
     * @param string     $replace
     *
     * @return int
     */
    private function replacePlaces(Collection $records, string $search, string $replace): int
    {
        $pattern     = '/(\n\d PLAC\b.* )' . preg_quote($search, '/') . '([,\n])/i';
        $replacement = '$1' . $replace . '$2';
        $count       = 0;

        foreach ($records as $record) {
            $old_gedcom = $record->gedcom();
            $new_gedcom = preg_replace($pattern, $replacement, $old_gedcom);

            if ($new_gedcom !== $old_gedcom) {
                $record->updateRecord($new_gedcom, true);
                $count++;
            }
        }

        return $count;
    }

    /**
     * A structured search.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function advanced(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
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
            $individuals = $this->search_service->searchIndividualsAdvanced([$tree], $fields, $modifiers);
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
     * Extra search fields to add to the advanced search
     *
     * @param string[] $fields
     *
     * @return string[]
     */
    private function otherFields(array $fields): array
    {
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
    private function dateOptions(): array
    {
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
    private function nameOptions(): array
    {
        return [
            'EXACT'    => I18N::translate('Exact'),
            'BEGINS'   => I18N::translate('Begins with'),
            'CONTAINS' => I18N::translate('Contains'),
            'SDX'      => I18N::translate('Sounds like'),
        ];
    }
}

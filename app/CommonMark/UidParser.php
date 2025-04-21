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

namespace Fisharebest\Webtrees\CommonMark;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\SearchService;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use Illuminate\Support\Collection;

/**
 * Convert UIDs within markdown text to links
 */
class UidParser implements InlineParserInterface
{
    private Tree $tree;
    private SearchService $search_service;

    /**
     * @param Tree $tree Match UIDs in this tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
        $this->search_service = new SearchService(new TreeService(new GedcomImportService($this->tree)));
    }

    /**
     * We are only interested in text that begins with '@'.
     *
     * @return InlineParserMatch
     */
    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('#(' . Gedcom::REGEX_UID . ')(:.*)?#');
    }

    private function firstMatchingUid(String $pUid, Collection $pCollection)
    {
        foreach ($pCollection as $recTmp) {
            if (preg_match('/\n1 _?UID ' . $pUid . '$/', $recTmp->gedcom())) {
                return $recTmp;
                break;
            }
        }
        return null;
    }

    /**
     * @param InlineParserContext $inlineContext
     *
     * @return bool
     */
    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();
        $subm = $inlineContext->getSubMatches();
        #[$uid, $linkText] = $inlineContext->getSubMatches();
        #$uid = [$subm[0], 'UID ' . $subm[0]];
        $uid = [$subm[0]];
        $firstFoundRecord = null;
        if (isset($subm[1])) {
            #Remove the initial colon
            $linkText = substr($subm[1], 1);
        } else {
            $linkText = '';
        }

        // Do the search
        $result = new Collection();

        // Log search requests for visitors
        if (Auth::id() === null) {
            Log::addSearchLog('General: ' . $query, $search_trees->all());
        }

        $result = $this->search_service->searchIndividuals([$this->tree], $uid);
        $firstFoundRecord = $this->firstMatchingUid($uid[0], $result);

        if ($firstFoundRecord === null) {
            $result = $this->search_service->searchFamilies([$this->tree], $uid);
            $firstFoundRecord = $this->firstMatchingUid($uid[0], $result);

            if ($firstFoundRecord === null) {
                $result = $this->search_service->searchFamilyNames([$this->tree], $uid);
                $firstFoundRecord = $this->firstMatchingUid($uid[0], $result);

                if ($firstFoundRecord === null) {
                    $result = $this->search_service->searchRepositories([$this->tree], $uid);
                    $firstFoundRecord = $this->firstMatchingUid($uid[0], $result);

                    if ($firstFoundRecord === null) {
                        $result = $this->search_service->searchSources([$this->tree], $uid);
                        $firstFoundRecord = $this->firstMatchingUid($uid[0], $result);

                        if ($firstFoundRecord === null) {
                            $result = $this->search_service->searchNotes([$this->tree], $uid);
                            $firstFoundRecord = $this->firstMatchingUid($uid[0], $result);

                            if ($firstFoundRecord === null) {
                                $result = $this->search_service->searchLocations([$this->tree], $uid);
                                $firstFoundRecord = $this->firstMatchingUid($uid[0], $result);

                                if ($firstFoundRecord === null) {
                                    $result = $this->search_service->searchMedia([$this->tree], $uid);
                                    $firstFoundRecord = $this->firstMatchingUid($uid[0], $result);
                                }
                            }
                        }
                    }
                }
            }
        }


        if ($firstFoundRecord === null) {
            return false;
        } else {

            $record = $firstFoundRecord;
    
            if ($record instanceof GedcomRecord) {
                $cursor->advanceBy($inlineContext->getFullMatchLength());

            $inlineContext->getContainer()->appendChild(new XrefNode($record));

            return true;
        }

        return false;
    }
}

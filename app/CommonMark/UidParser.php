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
        #This one permits escaping a # sign inside the text to be shown in the link and is ungreedy
        # so there can be more than one reference in one line
        return InlineParserMatch::regex('#(' . Gedcom::REGEX_UID . ')(?::(.*(?:(?!\\#).)*?))?#');

        // TESTED IN PHP 7.4
        //
        // #                  literal # (start ancor)
        // (                  start 1st capturing group
        // Gedcom::REGEX_UID  regex to match an UID
        // )                  end 1st capturing group
        // (?:                begin 1st non-capturing group
        //   :                a literal :
        //   (                start 2nd capturing group (without first :)
        //     .*             any character zero or more times
        //     (?:            begin 2nd non-capturing group
        //       (?!          begin negative lookahead
        //         \\#        literal text sequence \#
        //       )            end negative lookahead
        //       .            any single character   <- consumes one character if not followed by \#
        //     )              end 2nd non-capturing group
        //     *?             repeat 0 or more times 2nd non-capturing group NON GREEDY <- all characters before last # permiting \#
        //   )                end 2nd capturing group
        // )                  end 1st non-capturing group
        // ?                  2nd capturing group can be there or not
        // #                  literal # (end ancor)
    }

    private function firstMatchingUidRecord(String $pUid, Collection $pCollection): ?Object
    {
        foreach ($pCollection as $recTmp) {
            $regexTmp = '/\n1 _?UID ' . $pUid . '(:?\n|$)/';
            if (preg_match($regexTmp, $recTmp->gedcom())) {
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
        $uid = [$subm[0]];
        $firstFoundRecord = null;
        if (isset($subm[1])) {
            #Unescape character's #

            #NOTE: It should use only one \\, but it's using two because it get's escaped again in the regex processing.
            $linkText = preg_replace("/\\\\#/", "#", $subm[1]);
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
        $firstFoundRecord = $this->firstMatchingUidRecord($uid[0], $result);

        if ($firstFoundRecord === null) {
            $result = $this->search_service->searchFamilies([$this->tree], $uid);
            $firstFoundRecord = $this->firstMatchingUidRecord($uid[0], $result);

            if ($firstFoundRecord === null) {
                $result = $this->search_service->searchFamilyNames([$this->tree], $uid);
                $firstFoundRecord = $this->firstMatchingUidRecord($uid[0], $result);

                if ($firstFoundRecord === null) {
                    $result = $this->search_service->searchRepositories([$this->tree], $uid);
                    $firstFoundRecord = $this->firstMatchingUidRecord($uid[0], $result);

                    if ($firstFoundRecord === null) {
                        $result = $this->search_service->searchSources([$this->tree], $uid);
                        $firstFoundRecord = $this->firstMatchingUidRecord($uid[0], $result);

                        if ($firstFoundRecord === null) {
                            $result = $this->search_service->searchNotes([$this->tree], $uid);
                            $firstFoundRecord = $this->firstMatchingUidRecord($uid[0], $result);

                            if ($firstFoundRecord === null) {
                                $result = $this->search_service->searchLocations([$this->tree], $uid);
                                $firstFoundRecord = $this->firstMatchingUidRecord($uid[0], $result);

                                if ($firstFoundRecord === null) {
                                    $result = $this->search_service->searchMedia([$this->tree], $uid);
                                    $firstFoundRecord = $this->firstMatchingUidRecord($uid[0], $result);
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

                $inlineContext->getContainer()->appendChild(new UidNode($record, $linkText));

                return true;
            }

            return false;
        }
    }
}

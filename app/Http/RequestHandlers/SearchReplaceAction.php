<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Search and replace genealogy data
 */
class SearchReplaceAction implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private SearchService $search_service;

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
     * Search and replace.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree    = Validator::attributes($request)->tree();
        $search  = Validator::parsedBody($request)->string('search');
        $replace = Validator::parsedBody($request)->string('replace');
        $context = Validator::parsedBody($request)->string('context');

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
                $name_tags = Registry::elementFactory()->make('INDI:NAME')->subtags();
                $name_tags = array_map(static fn (string $tag): string => '2 ' . $tag, $name_tags);
                $name_tags[] = '1 NAME';

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

        $url = route(SearchReplacePage::class, [
            'search'  => $search,
            'replace' => $replace,
            'context' => $context,
            'tree'    => $tree->name(),
        ]);

        return redirect($url);
    }

    /**
     * @param Collection<int,GedcomRecord> $records
     * @param string                       $search
     * @param string                       $replace
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
     * @param Collection<int,GedcomRecord> $records
     * @param string                       $search
     * @param string                       $replace
     * @param array<string>                $name_tags
     *
     * @return int
     */
    private function replaceIndividualNames(Collection $records, string $search, string $replace, array $name_tags): int
    {
        $pattern     = '/(\n(?:' . implode('|', $name_tags) . ') .*)' . preg_quote($search, '/') . '/i';
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
     * @param Collection<int,GedcomRecord> $records
     * @param string                       $search
     * @param string                       $replace
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
}

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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function preg_match_all;
use function preg_replace;
use function response;
use function sprintf;

final class DeleteRecord implements RequestHandlerInterface
{
    public function __construct(
        private readonly LinkedRecordService $linked_record_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::attributes($request)->isXref()->string('xref');
        $record = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record = Auth::checkRecordAccess($record, true);

        if (Auth::isEditor($record->tree()) && $record->canShow() && $record->canEdit()) {
            // Delete links to this record
            foreach ($this->linked_record_service->allLinkedRecords($record) as $linker) {
                $old_gedcom = $linker->gedcom();
                $new_gedcom = $this->removeLinks($old_gedcom, $record->xref());
                if ($old_gedcom !== $new_gedcom) {
                    // If we have removed a link from a family to an individual, and it now has only one member and no genealogy facts
                    if (
                        $linker instanceof Family &&
                        preg_match('/\n1 (ANUL|CENS|DIV|DIVF|ENGA|MAR[BCLRS]|RESI|EVEN)/', $new_gedcom, $match) !== 1 &&
                        preg_match_all('/\n1 (HUSB|WIFE|CHIL) @(' . Gedcom::REGEX_XREF . ')@/', $new_gedcom, $match) === 1
                    ) {
                        // Delete the family
                        /* I18N: %s is the name of a family group, e.g. “Husband name + Wife name” */
                        FlashMessages::addMessage(I18N::translate('The family “%s” has been deleted because it only has one member.', $linker->fullName()));
                        $linker->deleteRecord();
                        // Delete the remaining link to this family
                        $relict = Registry::gedcomRecordFactory()->make($match[2][0], $tree);
                        if ($relict instanceof Individual) {
                            $relict_gedcom = $this->removeLinks($relict->gedcom(), $linker->xref());
                            $relict->updateRecord($relict_gedcom, false);
                            /* I18N: %s are names of records, such as sources, repositories or individuals */
                            FlashMessages::addMessage(I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', sprintf('<a href="%1$s" class="alert-link">%2$s</a>', e($relict->url()), $relict->fullName()), $linker->fullName()));
                        }
                    } else {
                        // Remove links from $linker to $record
                        /* I18N: %s are names of records, such as sources, repositories or individuals */
                        FlashMessages::addMessage(I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', sprintf('<a href="%1$s" class="alert-link">%2$s</a>', e($linker->url()), $linker->fullName()), $record->fullName()));
                        $linker->updateRecord($new_gedcom, false);
                    }
                }
            }
            // Delete the record itself
            $record->deleteRecord();
        }

        return response();
    }

    /**
     * Remove all links from $gedrec to $xref, and any sub-tags.
     *
     * @param string $gedrec
     * @param string $xref
     *
     * @return string
     */
    private function removeLinks(string $gedrec, string $xref): string
    {
        $gedrec = preg_replace('/\n1 ' . Gedcom::REGEX_TAG . ' @' . $xref . '@(\n[2-9].*)*/', '', $gedrec);
        $gedrec = preg_replace('/\n2 ' . Gedcom::REGEX_TAG . ' @' . $xref . '@(\n[3-9].*)*/', '', $gedrec);
        $gedrec = preg_replace('/\n3 ' . Gedcom::REGEX_TAG . ' @' . $xref . '@(\n[4-9].*)*/', '', $gedrec);
        $gedrec = preg_replace('/\n4 ' . Gedcom::REGEX_TAG . ' @' . $xref . '@(\n[5-9].*)*/', '', $gedrec);
        $gedrec = preg_replace('/\n5 ' . Gedcom::REGEX_TAG . ' @' . $xref . '@(\n[6-9].*)*/', '', $gedrec);

        return $gedrec;
    }
}

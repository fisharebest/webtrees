<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function response;

final class PendingChangesAcceptRecord
{
    public function __construct(
        private PendingChangesService $pending_changes_service,
    ) {
    }

    public function post(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref   = Validator::attributes($request)->isXref()->string('xref', '');
        $record = Registry::gedcomRecordFactory()->make($xref, $tree);

        if ($record instanceof GedcomRecord) {
            if ($record->isPendingDeletion()) {
                /* I18N: %s is the name of a genealogy record */
                FlashMessages::addMessage(I18N::translate('“%s” has been deleted.', $record->fullName()));
            } else {
                /* I18N: %s is the name of a genealogy record */
                FlashMessages::addMessage(I18N::translate('The changes to “%s” have been accepted.', $record->fullName()));
            }

            $this->pending_changes_service->acceptRecord($record);
        }

        return response();
    }
}

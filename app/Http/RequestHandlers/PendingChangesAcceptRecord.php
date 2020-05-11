<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function response;

/**
 * Accept pending changes for a record.
 */
class PendingChangesAcceptRecord implements RequestHandlerInterface
{
    /** @var PendingChangesService */
    private $pending_changes_service;

    /**
     * @param PendingChangesService $pending_changes_service
     */
    public function __construct(PendingChangesService $pending_changes_service)
    {
        $this->pending_changes_service = $pending_changes_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref   = $request->getAttribute('xref') ?? '';
        $record = Factory::gedcomRecord()->make($xref, $tree);

        if ($record) {
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

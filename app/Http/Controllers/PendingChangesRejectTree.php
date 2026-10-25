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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;

use function e;
use function response;

final class PendingChangesRejectTree
{
    public function __construct(
        private PendingChangesService $pending_changes_service,
    ) {
    }

    public function post(Tree $tree): ResponseInterface
    {
        $this->pending_changes_service->rejectTree($tree);

        FlashMessages::addMessage(I18N::translate('The changes to “%s” have been rejected.', e($tree->title())));

        return response();
    }
}

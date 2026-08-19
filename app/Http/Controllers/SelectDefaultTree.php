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
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;

use function e;
use function redirect;
use function route;

final class SelectDefaultTree
{
    public function post(Tree $tree): ResponseInterface
    {
        Site::setPreference('DEFAULT_GEDCOM', $tree->name());

        /* I18N: %s is the name of a family tree */
        $message = I18N::translate('The family tree “%s” will be shown to visitors when they first arrive at this website.', e($tree->title()));

        FlashMessages::addMessage($message, 'success');

        $url = route(ManageTrees::class, ['tree' => $tree->name()]);

        return redirect($url);
    }
}

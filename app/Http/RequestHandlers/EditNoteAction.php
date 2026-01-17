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
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class EditNoteAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();
        $xref = Validator::attributes($request)->isXref()->string('xref');
        $note = Registry::noteFactory()->make($xref, $tree);
        $note = Auth::checkNoteAccess($note, true);
        $NOTE = Validator::parsedBody($request)->string('NOTE');

        // Convert HTML line endings to GEDCOM continuations
        $NOTE = strtr($NOTE, ["\r\n" => "\n1 CONT "]);

        // "\" and "$" are significant in preg replacement strings, so escape them.
        $NOTE = str_replace(['\\', '$'], ['\\\\', '\\$'], $NOTE);

        $gedrec = preg_replace(
            '/^0 @' . $note->xref() . '@ NOTE.*(\n1 CONT.*)*/',
            '0 @' . $note->xref() . '@ NOTE ' . $NOTE,
            $note->gedcom()
        );

        $note->updateRecord($gedrec, true);

        return redirect($note->url());
    }
}

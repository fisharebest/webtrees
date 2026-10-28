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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validate;
use Psr\Http\Message\ResponseInterface;

final class CreateSubmitter
{
    public function __construct(
        private Validate $validate,
    ) {
    }

    public function get(Tree $tree): ResponseInterface
    {
        return response(view('modals/create-submitter', [
            'tree' => $tree,
        ]));
    }

    public function post(
        Tree $tree,
        string $name,
        string $address,
        string $email,
        string $phone,
        string $restriction,
    ): ResponseInterface {
        $this->validate->notEmpty($name, 'name');

        $name        = Registry::elementFactory()->make('SUBM:NAME')->canonical($name);
        $address     = Registry::elementFactory()->make('SUBM:ADDR')->canonical($address);
        $email       = Registry::elementFactory()->make('SUBM:EMAIL')->canonical($email);
        $phone       = Registry::elementFactory()->make('SUBM:PHON')->canonical($phone);
        $restriction = Registry::elementFactory()->make('SUBM:RESN')->canonical($restriction);

        $gedcom = "0 @@ SUBM\n1 NAME " . strtr($name, ["\n" => "\n2 CONT "]);

        if ($address !== '') {
            $gedcom .= "\n1 ADDR " . strtr($address, ["\n" => "\n2 CONT "]);
        }

        if ($email !== '') {
            $gedcom .= "\n1 EMAIL " . strtr($email, ["\n" => "\n2 CONT "]);
        }

        if ($phone !== '') {
            $gedcom .= "\n1 PHON " . strtr($phone, ["\n" => "\n2 CONT "]);
        }

        if ($restriction !== '') {
            $gedcom .= "\n1 RESN " . strtr($restriction, ["\n" => "\n2 CONT "]);
        }

        $record = $tree->createRecord($gedcom);

        // value and text are for autocomplete
        // html is for interactive modals
        return response([
            'value' => '@' . $record->xref() . '@',
            'text'  => view('selects/submitter', ['submitter' => $record]),
            'html'  => view('modals/record-created', [
                'title' => I18N::translate('The submitter has been created'),
                'name'  => $record->fullName(),
                'url'   => $record->url(),
            ]),
        ]);
    }
}

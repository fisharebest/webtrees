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
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_key_exists;
use function route;

final class EditRecordPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    public function __construct(
        private readonly GedcomEditService $gedcom_edit_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree           = Validator::attributes($request)->tree();
        $xref           = Validator::attributes($request)->isXref()->string('xref');
        $record         = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record         = Auth::checkRecordAccess($record, true);
        $include_hidden = Validator::queryParams($request)->boolean('include_hidden', false);
        $can_edit_raw   = Auth::isAdmin() || $tree->getPreference('SHOW_GEDCOM_RECORD') === '1';
        $subtags        = Registry::elementFactory()->make($record->tag())->subtags();

        $gedcom = $this->gedcom_edit_service->insertMissingRecordSubtags($record, $include_hidden);
        $hidden = $this->gedcom_edit_service->insertMissingRecordSubtags($record, true);

        if ($gedcom === $hidden) {
            $hidden_url = '';
        } else {
            $hidden_url = route(self::class, [
                'include_hidden'  => true,
                'tree'    => $tree->name(),
                'xref'    => $xref,
            ]);
        }

        return $this->viewResponse('edit/edit-record', [
            'can_edit_raw' => $can_edit_raw,
            'gedcom'       => $gedcom,
            'has_chan'     => array_key_exists('CHAN', $subtags),
            'hidden_url'   => $hidden_url,
            'record'       => $record,
            'title'        => $record->fullName(),
            'tree'         => $tree,
        ]);
    }
}

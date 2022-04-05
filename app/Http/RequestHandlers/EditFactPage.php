<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

/**
 * Edit a fact.
 */
class EditFactPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private GedcomEditService $gedcom_edit_service;

    /**
     * AddNewFact constructor.
     *
     * @param GedcomEditService $gedcom_edit_service
     */
    public function __construct(GedcomEditService $gedcom_edit_service)
    {
        $this->gedcom_edit_service = $gedcom_edit_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree           = Validator::attributes($request)->tree();
        $xref           = Validator::attributes($request)->isXref()->string('xref');
        $fact_id        = Validator::attributes($request)->string('fact_id');
        $include_hidden = Validator::queryParams($request)->boolean('include_hidden', false);

        $record = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record = Auth::checkRecordAccess($record, true);

        // Find the fact to edit
        $fact = $record->facts()->first(fn (Fact $fact): bool => $fact->id() === $fact_id && $fact->canEdit());

        if ($fact === null) {
            return redirect($record->url());
        }

        $can_edit_raw = Auth::isAdmin() || $tree->getPreference('SHOW_GEDCOM_RECORD');

        $gedcom = $this->gedcom_edit_service->insertMissingFactSubtags($fact, $include_hidden);
        $hidden = $this->gedcom_edit_service->insertMissingFactSubtags($fact, true);
        $url    = Validator::queryParams($request)->isLocalUrl()->string('url', $record->url());

        if ($gedcom === $hidden) {
            $hidden_url = '';
        } else {
            $hidden_url = route(self::class, [
                'fact_id' => $fact_id,
                'include_hidden'  => true,
                'tree'    => $tree->name(),
                'url'     => $url,
                'xref'    => $xref,
            ]);
        }

        $title = $record->fullName() . ' - ' . $fact->label();

        return $this->viewResponse('edit/edit-fact', [
            'can_edit_raw' => $can_edit_raw,
            'fact'         => $fact,
            'gedcom'       => $gedcom,
            'hidden_url'   => $hidden_url,
            'title'        => $title,
            'tree'         => $tree,
            'url'          => $url,
        ]);
    }
}

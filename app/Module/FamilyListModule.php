<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class FamilyListModule
 */
class FamilyListModule extends IndividualListModule
{
    protected const ROUTE_URL = '/tree/{tree}/family-list';

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/list */
        return I18N::translate('Families');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Families” module */
        return I18N::translate('A list of families.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function listMenuClass(): string
    {
        return 'menu-list-fam';
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();
        $user = Validator::attributes($request)->user();

        Auth::checkComponentAccess($this, ModuleListInterface::class, $tree, $user);

        $surname_param = Validator::queryParams($request)->string('surname', '');
        $surname       = I18N::strtoupper(I18N::language()->normalize($surname_param));

        $params = [
            'alpha'               => Validator::queryParams($request)->string('alpha', ''),
            'falpha'              => Validator::queryParams($request)->string('falpha', ''),
            'show'                => Validator::queryParams($request)->string('show', 'surn'),
            'show_all'            => Validator::queryParams($request)->string('show_all', 'no'),
            'show_all_firstnames' => Validator::queryParams($request)->string('show_all_firstnames', 'no'),
            'show_marnm'          => Validator::queryParams($request)->string('show_marnm', ''),
            'surname'             => $surname,
        ];

        if ($surname_param !== $surname) {
            return Registry::responseFactory()->redirectUrl($this->listUrl($tree, $params), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        return $this->createResponse($tree, $user, $params, true);
    }
}

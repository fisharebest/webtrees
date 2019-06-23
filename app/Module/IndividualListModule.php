<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\Controllers\ListController;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\IndividualListService;
use Fisharebest\Webtrees\Services\LocalizationService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class IndividualListModule
 */
class IndividualListModule extends AbstractModule implements ModuleListInterface
{
    use ModuleListTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/list */
        return I18N::translate('Individuals');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “IndividualListModule” module */
        return I18N::translate('A list of individuals.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function listMenuClass(): string
    {
        return 'menu-list-indi';
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function getListAction(ServerRequestInterface $request, Tree $tree, UserInterface $user): ResponseInterface
    {
        Auth::checkComponentAccess($this, 'list', $tree, $user);
      
        $listController = new ListController(app(IndividualListService::class), app(LocalizationService::class));
        return $listController->individualList($request, $tree, $user, $this);
    }

    /**
     * @return string[]
     */
    public function listUrlAttributes(): array
    {
        return [];
    }
}

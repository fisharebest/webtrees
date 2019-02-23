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
use Fisharebest\Webtrees\Http\Controllers\BranchesController;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Auth;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BranchesListModule
 */
class BranchesListModule extends AbstractModule implements ModuleListInterface
{
    use ModuleListTrait;

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/list */
        return I18N::translate('Branches');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “BranchesListModule” module */
        return I18N::translate('A list of branches of a family.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function listMenuClass(): string
    {
        return 'menu-branches';
    }
    
    public function listUrl(Tree $tree, array $parameters = []): string
    {
        return route('module', [
                'module' => $this->name(),
                'action' => 'Page',
                'ged'    => $tree->name(),
        ] + $parameters);
    }
    
    public function getPageAction(Request $request, Tree $tree, UserInterface $user): Response
    {
        Auth::checkComponentAccess($this, 'list', $tree, $user);
      
        $listController = new BranchesController(app(ModuleService::class));
        return $listController->page($request);
    }
    
    public function getListAction(Request $request, Tree $tree, UserInterface $user): Response
    {
        Auth::checkComponentAccess($this, 'list', $tree, $user);
      
        $listController = new BranchesController(app(ModuleService::class));
        return $listController->list($request, $tree, $user);
    }
    
    public function listUrlAttributes(): array
    {
        return [];
    }
}

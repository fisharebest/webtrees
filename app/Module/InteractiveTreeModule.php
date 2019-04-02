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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Exceptions\IndividualAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\IndividualNotFoundException;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module\InteractiveTree\TreeView;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class InteractiveTreeModule
 * Tip : you could change the number of generations loaded before ajax calls both in individual page and in treeview page to optimize speed and server load
 */
class InteractiveTreeModule extends AbstractModule implements ModuleChartInterface, ModuleTabInterface
{
    use ModuleChartTrait;
    use ModuleTabTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Interactive tree');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Interactive tree” module */
        return I18N::translate('An interactive tree, showing all the ancestors and descendants of an individual.');
    }

    /**
     * The default position for this tab.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultTabOrder(): int
    {
        return 7;
    }

    /** {@inheritdoc} */
    public function getTabContent(Individual $individual): string
    {
        $treeview = new TreeView('tvTab');

        [$html, $js] = $treeview->drawViewport($individual, 3);

        return view('modules/interactive-tree/tab', [
            'html'         => $html,
            'js'           => $js,
            'treeview_css' => $this->css(),
            'treeview_js'  => $this->js(),
        ]);
    }

    /**
     * @return string
     */
    public function css(): string
    {
        return Webtrees::MODULES_PATH . $this->name() . '/css/treeview.css';
    }

    /**
     * @return string
     */
    public function js(): string
    {
        return Webtrees::MODULES_PATH . $this->name() . '/js/treeview.js';
    }

    /** {@inheritdoc} */
    public function hasTabContent(Individual $individual): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isGrayedOut(Individual $individual): bool
    {
        return false;
    }

    /** {@inheritdoc} */
    public function canLoadAjax(): bool
    {
        return true;
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-tree';
    }

    /**
     * Return a menu item for this chart - for use in individual boxes.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function chartBoxMenu(Individual $individual): ?Menu
    {
        return $this->chartMenu($individual);
    }

    /**
     * The title for a specific instance of this chart.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function chartTitle(Individual $individual): string
    {
        /* I18N: %s is an individual’s name */
        return I18N::translate('Interactive tree of %s', $individual->fullName());
    }

    /**
     * The URL for this chart.
     *
     * @param Individual $individual
     * @param string[]   $parameters
     *
     * @return string
     */
    public function chartUrl(Individual $individual, array $parameters = []): string
    {
        return route('module', [
                'module' => $this->name(),
                'action' => 'Chart',
                'xref'   => $individual->xref(),
                'ged'    => $individual->tree()->name(),
            ] + $parameters);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function getChartAction(ServerRequestInterface $request, Tree $tree, UserInterface $user): ResponseInterface
    {
        $xref = $request->get('xref', '');

        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);
        Auth::checkComponentAccess($this, 'chart', $tree, $user);

        $tv = new TreeView('tv');

        [$html, $js] = $tv->drawViewport($individual, 4);

        return $this->viewResponse('interactive-tree-page', [
            'html'       => $html,
            'individual' => $individual,
            'js'         => $js,
            'title'      => $this->chartTitle($individual),
            'tree'       => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function getDetailsAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $pid        = $request->get('pid', Gedcom::REGEX_XREF);
        $individual = Individual::getInstance($pid, $tree);

        if ($individual === null) {
            throw new IndividualNotFoundException();
        }

        if (!$individual->canShow()) {
            throw new IndividualAccessDeniedException();
        }

        $instance = $request->get('instance', '');
        $treeview = new TreeView($instance);

        return response($treeview->getDetails($individual));
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function getPersonsAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $q        = $request->get('q', '');
        $instance = $request->get('instance', '');
        $treeview = new TreeView($instance);

        return response($treeview->getPersons($tree, $q));
    }
}

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

use Fisharebest\Webtrees\Exceptions\IndividualAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\IndividualNotFoundException;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module\InteractiveTree\TreeView;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Webtrees;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InteractiveTreeModule
 * Tip : you could change the number of generations loaded before ajax calls both in individual page and in treeview page to optimize speed and server load
 */
class InteractiveTreeModule extends AbstractModule implements ModuleInterface, ModuleChartInterface, ModuleTabInterface
{
    use ModuleChartTrait;
    use ModuleTabTrait;

    /** {@inheritdoc} */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Interactive tree');
    }

    /** {@inheritdoc} */
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
    public function defaultTabOrder(): int {
        return 90;
    }

    /** {@inheritdoc} */
    public function getTabContent(Individual $individual): string
    {
        $treeview = new TreeView('tvTab');
        [$html, $js] = $treeview->drawViewport($individual, 3);

        return view('modules/tree/tab', [
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
        return Webtrees::MODULES_PATH . $this->getName() . '/css/treeview.css';
    }

    /**
     * @return string
     */
    public function js(): string
    {
        return Webtrees::MODULES_PATH . $this->getName() . '/js/treeview.js';
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
    public function canLoadAjax():  bool
    {
        return true;
    }

    /**
     * Return a menu item for this chart.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function getChartMenu(Individual $individual): ?Menu
    {
        return new Menu(
            $this->title(),
            route('module', [
                'module' => $this->getName(),
                'action' => 'Treeview',
                'xref'   => $individual->xref(),
                'ged'    => $individual->tree()->name(),
            ]),
            'menu-chart-tree',
            ['rel' => 'nofollow']
        );
    }

    /**
     * Return a menu item for this chart - for use in individual boxes.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function getBoxChartMenu(Individual $individual): ?Menu
    {
        return $this->getChartMenu($individual);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function getTreeviewAction(Request $request, Tree $tree): Response
    {
        $xref = $request->get('xref', '');

        $individual = Individual::getInstance($xref, $tree);

        if ($individual === null) {
            throw new IndividualNotFoundException();
        }

        if (!$individual->canShow()) {
            throw new IndividualAccessDeniedException();
        }

        $tv = new TreeView('tv');

        [$html, $js] = $tv->drawViewport($individual, 4);

        $title = I18N::translate('Interactive tree of %s', $individual->getFullName());

        return $this->viewResponse('interactive-tree-page', [
            'title'      => $title,
            'individual' => $individual,
            'js'         => $js,
            'html'       => $html,
            'tree'       => $tree,
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function getDetailsAction(Request $request, Tree $tree): Response
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

        return new Response($treeview->getDetails($individual));
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function getPersonsAction(Request $request, Tree $tree): Response
    {
        $q        = $request->get('q', '');
        $instance = $request->get('instance', '');
        $treeview = new TreeView($instance);

        return new Response($treeview->getPersons($tree, $q));
    }
}

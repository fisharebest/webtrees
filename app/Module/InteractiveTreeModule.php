<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Exceptions\IndividualAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\IndividualNotFoundException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module\InteractiveTree\TreeView;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InteractiveTreeModule
 * Tip : you could change the number of generations loaded before ajax calls both in individual page and in treeview page to optimize speed and server load
 */
class InteractiveTreeModule extends AbstractModule implements ModuleTabInterface, ModuleChartInterface
{
    /** {@inheritdoc} */
    public function getTitle()
    {
        /* I18N: Name of a module */
        return I18N::translate('Interactive tree');
    }

    /** {@inheritdoc} */
    public function getDescription()
    {
        /* I18N: Description of the “Interactive tree” module */
        return I18N::translate('An interactive tree, showing all the ancestors and descendants of an individual.');
    }

    /** {@inheritdoc} */
    public function defaultTabOrder()
    {
        return 68;
    }

    /** {@inheritdoc} */
    public function getTabContent(Individual $individual)
    {
        $treeview = new TreeView('tvTab');
        list($html, $js) = $treeview->drawViewport($individual, 3);

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
        return WT_MODULES_DIR . $this->getName() . '/css/treeview.css';
    }

    /**
     * @return string
     */
    public function js(): string
    {
        return WT_MODULES_DIR . $this->getName() . '/js/treeview.js';
    }

    /** {@inheritdoc} */
    public function hasTabContent(Individual $individual)
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isGrayedOut(Individual $individual)
    {
        return false;
    }

    /** {@inheritdoc} */
    public function canLoadAjax()
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
    public function getChartMenu(Individual $individual)
    {
        return new Menu(
            $this->getTitle(),
            route('module', [
                'module' => $this->getName(),
                'action' => 'Treeview',
                'xref'   => $individual->getXref(),
                'ged'    => $individual->getTree()->getName(),
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
    public function getBoxChartMenu(Individual $individual)
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
        $xref = $request->get('xref');

        $individual = Individual::getInstance($xref, $tree);

        if ($individual === null) {
            throw new IndividualNotFoundException();
        }

        if (!$individual->canShow()) {
            throw new IndividualAccessDeniedException();
        }

        $tv = new TreeView('tv');

        list($html, $js) = $tv->drawViewport($individual, 4);

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
        $pid        = $request->get('pid', WT_REGEX_XREF);
        $individual = Individual::getInstance($pid, $tree);

        if ($individual === null) {
            throw new IndividualNotFoundException();
        }

        if (!$individual->canShow()) {
            throw new IndividualAccessDeniedException();
        }

        $instance = $request->get('instance');
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
        $q        = $request->get('q');
        $instance = $request->get('instance');
        $treeview = new TreeView($instance);

        return new Response($treeview->getPersons($tree, $q));
    }
}

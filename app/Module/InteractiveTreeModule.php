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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module\InteractiveTree\TreeView;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class InteractiveTreeModule
 * Tip : you could change the number of generations loaded before ajax calls both in individual page and in treeview page to optimize speed and server load
 */
class InteractiveTreeModule extends AbstractModule implements ModuleTabInterface, ModuleChartInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */
			I18N::translate('Interactive tree');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Interactive tree” module */
			I18N::translate('An interactive tree, showing all the ancestors and descendants of an individual.');
	}

	/** {@inheritdoc} */
	public function defaultTabOrder() {
		return 68;
	}

	/** {@inheritdoc} */
	public function getTabContent(Individual $individual) {
		$treeview = new TreeView('tvTab');
		list($html, $js) = $treeview->drawViewport($individual, 3);

		return view('tabs/treeview', [
			'html'         => $html,
			'js'           => $js,
			'treeview_css' => WT_MODULES_DIR . $this->getName() . '/css/treeview.css',
			'treeview_js'  => WT_MODULES_DIR . $this->getName() . '/js/treeview.js',
		]);
	}

	/** {@inheritdoc} */
	public function hasTabContent(Individual $individual) {
		return true;
	}

	/** {@inheritdoc} */
	public function isGrayedOut(Individual $individual) {
		return false;
	}

	/** {@inheritdoc} */
	public function canLoadAjax() {
		return true;
	}

	/**
	 * Return a menu item for this chart.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu|null
	 */
	public function getChartMenu(Individual $individual) {
		return new Menu(
			$this->getTitle(),
			e(route('module', ['module' => $this->getName(), 'action' => 'Treeview', 'xref' => $individual->getXref(), 'ged' => $individual->getTree()->getName()])),
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
	public function getBoxChartMenu(Individual $individual) {
		return $this->getChartMenu($individual);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function getTreeviewAction(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref = $request->get('xref');

		$individual = Individual::getInstance($xref, $tree);
		$tv         = new TreeView('tv');

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
	 *
	 * @return Response
	 */
	public function getDetailsAction(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$pid        = $request->get('pid', WT_REGEX_XREF);
		$individual = Individual::getInstance($pid, $tree);

		if ($individual && $individual->canShow()) {
			$instance = $request->get('instance');
			$treeview = new TreeView($instance);

			return new Response($treeview->getDetails($individual));
		} else {
			throw new NotFoundHttpException;
		}
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function getPersonsAction(Request $request): Response {
		$q  = $request->get('q');
		$instance = $request->get('instance');
		$treeview = new TreeView($instance);

		return new Response($treeview->getPersons($q));
	}
}

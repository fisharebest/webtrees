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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RelationshipsChartModule
 */
class RelationshipsChartModule extends AbstractModule implements ModuleConfigInterface, ModuleChartInterface {
	/** It would be more correct to use PHP_INT_MAX, but this isn't friendly in URLs */
	const UNLIMITED_RECURSION = 99;

	/** By default new trees allow unlimited recursion */
	const DEFAULT_RECURSION = self::UNLIMITED_RECURSION;

	/** By default new trees search for all relationships (not via ancestors) */
	const DEFAULT_ANCESTORS = 0;

	/**
	 * How should this module be labelled on tabs, menus, etc.?
	 *
	 * @return string
	 */
	public function getTitle() {
		return /* I18N: Name of a module/chart */
			I18N::translate('Relationships');
	}

	/**
	 * A sentence describing what this module does.
	 *
	 * @return string
	 */
	public function getDescription() {
		return /* I18N: Description of the “RelationshipsChart” module */
			I18N::translate('A chart displaying relationships between two individuals.');
	}

	/**
	 * What is the default access level for this module?
	 *
	 * Some modules are aimed at admins or managers, and are not generally shown to users.
	 *
	 * @return int
	 */
	public function defaultAccessLevel() {
		return Auth::PRIV_PRIVATE;
	}

	/**
	 * Return a menu item for this chart.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu|null
	 */
	public function getChartMenu(Individual $individual) {
		$tree     = $individual->getTree();
		$gedcomid = $tree->getUserPreference(Auth::user(), 'gedcomid', '');

		if ($gedcomid !== '') {
			return new Menu(
				I18N::translate('Relationship to me'),
				e(route('relationships', ['xref1' => $gedcomid, 'xref2' => $individual->getXref(), 'ged' => $individual->getTree()->getName()])),
				'menu-chart-relationship',
				['rel' => 'nofollow']
			);
		} else {
			return new Menu(
				I18N::translate('Relationships'),
				e(route('relationships', ['xref1' => $individual->getXref(), 'ged' => $individual->getTree()->getName()])),
				'menu-chart-relationship',
				['rel' => 'nofollow']
			);
		}
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
	 * The URL to a page where the user can modify the configuration of this module.
	 *
	 * @return string
	 */
	public function getConfigLink() {
		return route('module', ['module' => $this->getName(), 'action' => 'Admin']);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function getAdminAction(Request $request): Response {
		$this->layout = 'layouts/administration';

		return $this->viewResponse('modules/relationships_chart/config', [
			'all_trees'         => Tree::getAll(),
			'ancestors_options' => $this->ancestorsOptions(),
			'default_ancestors' => self::DEFAULT_ANCESTORS,
			'default_recursion' => self::DEFAULT_RECURSION,
			'recursion_options' => $this->recursionOptions(),
			'title'             => I18N::translate('Chart preferences') . ' — ' . $this->getTitle(),
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function postAdminAction(Request $request): RedirectResponse {
		foreach (Tree::getAll() as $tree) {
			$tree->setPreference('RELATIONSHIP_RECURSION', $request->get('relationship-recursion-' . $tree->getTreeId()));
			$tree->setPreference('RELATIONSHIP_ANCESTORS', $request->get('relationship-ancestors-' . $tree->getTreeId()));
		}

		FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been updated.', $this->getTitle()), 'success');

		return new RedirectResponse($this->getConfigLink());
	}

	/**
	 * Possible options for the ancestors option
	 */
	private function ancestorsOptions() {
		return [
			0 => I18N::translate('Find any relationship'),
			1 => I18N::translate('Find relationships via ancestors'),
		];
	}

	/**
	 * Possible options for the recursion option
	 */
	private function recursionOptions() {
		return [
			0                         => I18N::translate('none'),
			1                         => I18N::number(1),
			2                         => I18N::number(2),
			3                         => I18N::number(3),
			self::UNLIMITED_RECURSION => I18N::translate('unlimited'),
		];
	}
}

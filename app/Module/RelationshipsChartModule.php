<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Individual;

/**
 * Class RelationshipsChartModule
 */
class RelationshipsChartModule extends AbstractModule implements ModuleChartInterface {
	/**
	 * How should this module be labelled on tabs, menus, etc.?
	 *
	 * @return string
	 */
	public function getTitle() {
		return /* I18N: Name of a module/chart */ I18N::translate('Relationships');
	}

	/**
	 * A sentence describing what this module does.
	 *
	 * @return string
	 */
	public function getDescription() {
		return /* I18N: Description of the “RelationshipsChart” module */ I18N::translate('A chart displaying relationships between two individuals.');
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
	 * @return Menu|null
	 */
	public function getChartMenu(Individual $individual) {
		$tree     = $individual->getTree();
		$gedcomid = $tree->getUserPreference(Auth::user(), 'gedcomid');

		if ($gedcomid) {
			return new Menu(
				I18N::translate('Relationship to me'),
				'relationship.php?pid1=' . $gedcomid . '&amp;pid2=' . $individual->getXref() . '&amp;ged=' . $tree->getNameUrl(),
				'menu-chart-relationship',
				array('rel' => 'nofollow')
			);
		} else {
			return new Menu(
				I18N::translate('Relationships'),
				'relationship.php?pid1=' . $individual->getXref() . '&amp;ged=' . $tree->getNameUrl(),
				'menu-chart-relationship',
				array('rel' => 'nofollow')
			);
		}
	}

	/**
	 * Return a menu item for this chart - for use in individual boxes.
	 *
	 * @return Menu|null
	 */
	public function getBoxChartMenu(Individual $individual) {
		return $this->getChartMenu($individual);
	}
}

<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

/**
 * Class BirthReportModule
 */
class BirthReportModule extends Module implements ModuleReportInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		// This text also appears in the .XML file - update both together
		return /* I18N: Name of a module/report */ I18N::translate('Births');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		// This text also appears in the .XML file - update both together
		return /* I18N: Description of the “Births” module */ I18N::translate('A report of individuals who were born in a given time or place.');
	}

	/** {@inheritdoc} */
	public function defaultAccessLevel() {
		return WT_PRIV_PUBLIC;
	}

	/** {@inheritdoc} */
	public function getReportMenus() {
		$menus = array();
		$menu = new Menu(
			$this->getTitle(),
			'reportengine.php?ged=' . WT_GEDURL . '&amp;action=setup&amp;report=' . WT_MODULES_DIR . $this->getName() . '/report.xml',
			'menu-report-' . $this->getName()
		);
		$menus[] = $menu;

		return $menus;
	}
}

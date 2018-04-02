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

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;

/**
 * Class FamilyNavigatorModule
 */
class FamilyNavigatorModule extends AbstractModule implements ModuleSidebarInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module/sidebar */ I18N::translate('Family navigator');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Family navigator” module */ I18N::translate('A sidebar showing an individual’s close families and relatives.');
	}

	/** {@inheritdoc} */
	public function defaultSidebarOrder() {
		return 20;
	}

	/** {@inheritdoc} */
	public function hasSidebarContent(Individual $individual) {
		return true;
	}

	/** {@inheritdoc} */
	public function getSidebarAjaxContent() {
		return '';
	}

	/**
	 * Load this sidebar synchronously.
	 *
	 * @param Individual $individual
	 *
	 * @return string
	 */
	public function getSidebarContent(Individual $individual) {
		return view('sidebars/family-navigator', ['individual' => $individual]);
	}
}

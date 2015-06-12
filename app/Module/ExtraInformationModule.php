<?php
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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\I18N;

/**
 * Class ExtraInformationModule
 * A sidebar to show non-genealogy information about an individual
 */
class ExtraInformationModule extends AbstractModule implements ModuleSidebarInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module/sidebar */ I18N::translate('Extra information');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Extra information” module */ I18N::translate('A sidebar showing non-genealogy information about an individual.');
	}

	/** {@inheritdoc} */
	public function defaultSidebarOrder() {
		return 10;
	}

	/** {@inheritdoc} */
	public function hasSidebarContent() {
		return true;
	}

	/**
	 * Load this sidebar synchronously.
	 *
	 * @return string
	 */
	public function getSidebarContent() {
		global $controller;

		$indifacts = array();
		// The individual’s own facts
		foreach ($controller->record->getFacts() as $fact) {
			if (self::showFact($fact)) {
				$indifacts[] = $fact;
			}
		}

		ob_start();
		if (!$indifacts) {
			echo I18N::translate('There are no facts for this individual.');
		} else {
			foreach ($indifacts as $fact) {
				FunctionsPrintFacts::printFact($fact, $controller->record);
			}
		}

		return strip_tags(ob_get_clean(), '<a><div><span>');
	}

	/** {@inheritdoc} */
	public function getSidebarAjaxContent() {
		return '';
	}

	/**
	 * Does this module display a particular fact
	 *
	 * @param Fact $fact
	 *
	 * @return bool
	 */
	public static function showFact(Fact $fact) {
		switch ($fact->getTag()) {
		case 'AFN':
		case 'CHAN':
		case 'IDNO':
		case 'REFN':
		case 'RFN':
		case 'RIN':
		case 'SSN':
		case '_UID':
			return true;
		default:
			return false;
		}
	}
}

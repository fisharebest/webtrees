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

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;

/**
 * Class SourcesTabModule
 */
class SourcesTabModule extends AbstractModule implements ModuleTabInterface {
	/** @var Fact[] All facts belonging to this source. */
	private $facts;

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Sources');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Sources” module */ I18N::translate('A tab showing the sources linked to an individual.');
	}

	/** {@inheritdoc} */
	public function defaultTabOrder() {
		return 30;
	}

	/** {@inheritdoc} */
	public function hasTabContent(Individual $individual) {
		return $individual->canedit() || $this->getFactsWithSources($individual);
	}

	/** {@inheritdoc} */
	public function isGrayedOut(Individual $individual) {
		return !$this->getFactsWithSources($individual);
	}

	/** {@inheritdoc} */
	public function getTabContent(Individual $individual) {
		return view('tabs/sources', [
			'can_edit'   => $individual->canEdit(),
			'individual' => $individual,
			'facts'      => $this->getFactsWithSources($individual),
		]);
	}

	/**
	 * Get all the facts for an individual which contain sources.
	 *
	 * @param Individual $individual
	 *
	 * @return Fact[]
	 */
	private function getFactsWithSources(Individual $individual) {
		if ($this->facts === null) {
			$facts = $individual->getFacts();
			foreach ($individual->getSpouseFamilies() as $family) {
				if ($family->canShow()) {
					foreach ($family->getFacts() as $fact) {
						$facts[] = $fact;
					}
				}
			}
			$this->facts = [];
			foreach ($facts as $fact) {
				if (preg_match('/(?:^1|\n\d) SOUR/', $fact->getGedcom())) {
					$this->facts[] = $fact;
				}
			}
			Functions::sortFacts($this->facts);
		}

		return $this->facts;
	}

	/** {@inheritdoc} */
	public function canLoadAjax() {
		return false;
	}
}

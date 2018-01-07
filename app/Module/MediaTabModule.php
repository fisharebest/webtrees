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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\I18N;

/**
 * Class MediaTabModule
 */
class MediaTabModule extends AbstractModule implements ModuleTabInterface {
	/** @var  Fact[] A list of facts with media objects. */
	private $facts;

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Media');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Media” module */ I18N::translate('A tab showing the media objects linked to an individual.');
	}

	/** {@inheritdoc} */
	public function defaultTabOrder() {
		return 50;
	}

	/** {@inheritdoc} */
	public function hasTabContent() {
		global $WT_TREE;

		return Auth::isEditor($WT_TREE) || $this->getFactsWithMedia();
	}

	/** {@inheritdoc} */
	public function isGrayedOut() {
		return !$this->getFactsWithMedia();
	}

	/** {@inheritdoc} */
	public function getTabContent() {
		ob_start();
		echo '<table class="table wt-facts-table">';
		echo '<tbody>';
		foreach ($this->getFactsWithMedia() as $fact) {
			if ($fact->getTag() == 'OBJE') {
				FunctionsPrintFacts::printMainMedia($fact, 1);
			} else {
				for ($i = 2; $i < 4; ++$i) {
					FunctionsPrintFacts::printMainMedia($fact, $i);
				}
			}
		}
		?>
		</tbody>
		</table>
		<?php
		if (!$this->getFactsWithMedia()) {
			echo '<p>', I18N::translate('There are no media objects for this individual.'), '</p>';
		}

		return '<div id="' . $this->getName() . '_content">' . ob_get_clean() . '</div>';
	}

	/**
	 * Get all the facts for an individual which contain media objects.
	 *
	 * @return Fact[]
	 */
	private function getFactsWithMedia() {
		global $controller;

		if ($this->facts === null) {
			$facts = $controller->record->getFacts();
			foreach ($controller->record->getSpouseFamilies() as $family) {
				if ($family->canShow()) {
					foreach ($family->getFacts() as $fact) {
						$facts[] = $fact;
					}
				}
			}
			$this->facts = [];
			foreach ($facts as $fact) {
				if (preg_match('/(?:^1|\n\d) OBJE @' . WT_REGEX_XREF . '@/', $fact->getGedcom())) {
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

	/** {@inheritdoc} */
	public function getPreLoadContent() {
		return '';
	}
}

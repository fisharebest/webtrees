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
	const TTL = "<div class='flyout2'>%s</div>";
	const LNK = "<div class='flyout3' data-href='%s'>%s</div>";
	const MSG = "<div class='flyout4'>(%s)</div>"; // class flyout4 not used in standard themes

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
		global $controller;

		$controller->addInlineJavascript('
			$("#sb_family_nav_content")
				.on("click", ".flyout a", function() {
					return false;
				})
				.on("click", ".flyout3", function() {
					window.location.href = $(this).data("href");
					return false;
				});
		');

		ob_start();

		?>
		<div id="sb_family_nav_content">
			<div class="nav_content">

		<?php
		//-- parent families -------------------------------------------------------------
		foreach ($individual->getChildFamilies() as $family) {
			$this->drawFamily($individual, $family, $individual->getChildFamilyLabel($family));
		}
		//-- step parents ----------------------------------------------------------------
		foreach ($individual->getChildStepFamilies() as $family) {
			$this->drawFamily($individual, $family, $individual->getStepFamilyLabel($family));
		}
		//-- spouse and children --------------------------------------------------
		foreach ($individual->getSpouseFamilies() as $family) {
			$this->drawFamily($individual, $family, $individual->getSpouseFamilyLabel($family));
		}
		//-- step children ----------------------------------------------------------------
		foreach ($individual->getSpouseStepFamilies() as $family) {
			$this->drawFamily($individual, $family, $family->getFullName());
		}
		?>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Format a family.
	 *
	 * @param Family $family
	 * @param string $title
	 */
	private function drawFamily(Individual $individual, Family $family, $title) {
		global $controller;

		?>
		<table class="table table-sm wt-facts-table">
		<caption class="text-center">
			<a class="famnav_title" href="<?= e($family->url()) ?>">
				<?= $title ?>
			</a>
		</caption>
		<tbody>
			<?php
				foreach ($family->getSpouses() as $spouse) {
					$icon = $individual === $spouse ? '<i class="icon-selected"></i>' : '';
					$menu = new Menu($icon . Functions::getCloseRelationshipName($individual, $spouse));
					$menu->addSubmenu(new Menu($this->getParents($spouse)));
					?>
					<tr class="text-center wt-parent wt-gender-<?= $spouse->getSex() ?>">
						<th scope="row">
							<ul class="nav">
								<?= $menu->bootstrap4() ?>
							</ul>
						</th>
						<td>
							<?php if ($spouse->canShow()): ?>
							<a class="famnav_link" href="<?= e($spouse->url()) ?>">
								<?= $spouse->getFullName() ?>
							</a>
							<div class="small">
								<?= $spouse->getLifeSpan() ?>
							</div>
							<?php else: ?>
								<?= $spouse->getFullName() ?>
							<?php endif ?>
						</td>
					</tr>
				<?php
				}

				foreach ($family->getChildren() as $child) {
					$icon = $individual === $child ? '<i class="icon-selected"></i>' : '';
					$menu = new Menu($icon . Functions::getCloseRelationshipName($individual, $child));
					$menu->addSubmenu(new Menu($this->getFamily($child)));
					?>
					<tr class="text-center wt-child wt-gender-<?= $child->getSex() ?>">
						<th scope="row">
							<ul class="nav">
								<?= $menu->bootstrap4() ?>
							</ul>
						</th>
						<td>
							<?php if ($child->canShow()): ?>
							<a class="famnav_link" href="<?= e($child->url()) ?>">
								<?= $child->getFullName() ?>
							</a>
							<div class="small">
								<?= $child->getLifeSpan() ?>
							</div>
							<?php else: ?>
								<?= $child->getFullName() ?>
							<?php endif ?>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Format an individual.
	 *
	 * @param      $person
	 * @param bool $showUnknown
	 *
	 * @return string
	 */
	private function getHTML($person, $showUnknown = false) {
		if ($person instanceof Individual) {
			return sprintf(self::LNK, e($person->url()), $person->getFullName());
		} elseif ($showUnknown) {
			return sprintf(self::MSG, I18N::translate('unknown'));
		} else {
			return '';
		}
	}

	/**
	 * Forat the parents of an individual.
	 *
	 * @param Individual $person
	 *
	 * @return string
	 */
	private function getParents(Individual $person) {
		$father = null;
		$mother = null;
		$html   = sprintf(self::TTL, I18N::translate('Parents'));
		$family = $person->getPrimaryChildFamily();
		if ($person->canShowName() && $family !== null) {
			$father = $family->getHusband();
			$mother = $family->getWife();
			$html .= $this->getHTML($father) .
					 $this->getHTML($mother);

			// Can only have a step parent if one & only one parent found at this point
			if ($father instanceof Individual xor $mother instanceof Individual) {
				$stepParents = '';
				foreach ($person->getChildStepFamilies() as $family) {
					if (!$father instanceof Individual) {
						$stepParents .= $this->getHTML($family->getHusband());
					} else {
						$stepParents .= $this->getHTML($family->getWife());
					}
				}
				if ($stepParents) {
					$relationship = $father instanceof Individual ?
						I18N::translateContext('father’s wife', 'step-mother') : I18N::translateContext('mother’s husband', 'step-father');
					$html .= sprintf(self::TTL, $relationship) . $stepParents;
				}
			}
		}
		if (!($father instanceof Individual || $mother instanceof Individual)) {
			$html .= sprintf(self::MSG, I18N::translateContext('unknown family', 'unknown'));
		}

		return $html;
	}

	/**
	 * Format a family.
	 *
	 * @param Individual $person
	 *
	 * @return string
	 */
	private function getFamily(Individual $person) {
		$html = '';
		if ($person->canShowName()) {
			foreach ($person->getSpouseFamilies() as $family) {
				$spouse = $family->getSpouse($person);
				$html .= $this->getHTML($spouse, true);
				$children = $family->getChildren();
				if (count($children) > 0) {
					$html .= "<ul class='clist'>";
					foreach ($children as $child) {
						$html .= '<li>' . $this->getHTML($child) . '</li>';
					}
					$html .= '</ul>';
				}
			}
		}
		if (!$html) {
			$html = sprintf(self::MSG, I18N::translate('none'));
		}

		return sprintf(self::TTL, I18N::translate('Family')) . $html;
	}
}

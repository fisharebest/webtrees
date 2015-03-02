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
 * Class FamilyNavigatorModule
 */
class FamilyNavigatorModule extends Module implements ModuleSidebarInterface {

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
	public function hasSidebarContent() {
		return !Auth::isSearchEngine();
	}

	/** {@inheritdoc} */
	public function getSidebarAjaxContent() {
		return '';
	}

	/** {@inheritdoc} */
	public function getSidebarContent() {
		global $controller;

		$controller->addInlineJavascript('
			jQuery("#sb_family_nav_content")
				.on("click", ".flyout a", function() {
					return false;
				})
				.on("click", ".flyout3", function() {
					window.location.href = jQuery(this).data("href");
					return false;
				});
		');

		ob_start();

		?>
		<div id="sb_family_nav_content">
			<table class="nav_content">

		<?php
		//-- parent families -------------------------------------------------------------
		foreach ($controller->record->getChildFamilies() as $family) {
			$this->drawFamily($family, $controller->record->getChildFamilyLabel($family));
		}
		//-- step parents ----------------------------------------------------------------
		foreach ($controller->record->getChildStepFamilies() as $family) {
			$this->drawFamily($family, $controller->record->getStepFamilyLabel($family));
		}
		//-- spouse and children --------------------------------------------------
		foreach ($controller->record->getSpouseFamilies() as $family) {
			$this->drawFamily($family, $controller->record->getSpouseFamilyLabel($family));
		}
		//-- step children ----------------------------------------------------------------
		foreach ($controller->record->getSpouseStepFamilies() as $family) {
			$this->drawFamily($family, $family->getFullName());
		}
		?>
			</table>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * @param Family $family
	 * @param string $title
	 */
	private function drawFamily(Family $family, $title) {
		global $controller, $SHOW_PRIVATE_RELATIONSHIPS;

		?>
		<tr>
			<td class="center" colspan="2">
				<a class="famnav_title" href="<?php echo $family->getHtmlUrl(); ?>">
					<?php echo $title; ?>
				</a>
			</td>
		</tr>
		<?php
		$access_level = $SHOW_PRIVATE_RELATIONSHIPS ? WT_PRIV_HIDE : WT_USER_ACCESS_LEVEL;
		$facts = array_merge($family->getFacts('HUSB', false, $access_level), $family->getFacts('WIFE', false, $access_level));
		foreach ($facts as $fact) {
			$spouse = $fact->getTarget();
			if ($spouse instanceof Individual) {
				$menu = new Menu(get_close_relationship_name($controller->record, $spouse));
				$menu->addClass('', 'submenu flyout');
				$menu->addSubmenu(new Menu($this->getParents($spouse)));
				?>
				<tr>
					<td class="facts_label">
						<?php echo $menu->getMenu(); ?>
					</td>
					<td class="center <?php echo $controller->getPersonStyle($spouse); ?> nam">
						<a class="famnav_link" href="<?php echo $spouse->getHtmlUrl(); ?>">
							<?php echo $spouse->getFullName(); ?>
						</a>
						<div class="font9">
							<?php echo $spouse->getLifeSpan(); ?>
						</div>
					</td>
				</tr>
				<?php
			}
		}

		foreach ($family->getFacts('CHIL', false, $access_level) as $fact) {
			$child = $fact->getTarget();
			if ($child instanceof Individual) {
				$menu = new Menu(get_close_relationship_name($controller->record, $child));
				$menu->addClass('', 'submenu flyout');
				$menu->addSubmenu(new Menu($this->getFamily($child)));
				?>
				<tr>
					<td class="facts_label">
						<?php echo $menu->getMenu(); ?>
					</td>
					<td class="center <?php echo $controller->getPersonStyle($child); ?> nam">
						<a class="famnav_link" href="<?php echo $child->getHtmlUrl(); ?>">
							<?php echo $child->getFullName(); ?>
						</a>
						<div class="font9">
							<?php echo $child->getLifeSpan(); ?>
						</div>
					</td>
				</tr>
				<?php
			}
		}
	}

	/**
	 * @param         $person
	 * @param boolean $showUnknown
	 *
	 * @return string
	 */
	private function getHTML($person, $showUnknown = false) {
		if ($person instanceof Individual) {
			return sprintf(self::LNK, $person->getHtmlUrl(), $person->getFullName());
		} elseif ($showUnknown) {
			return sprintf(self::MSG, I18N::translate('unknown'));
		} else {
			return '';
		}
	}

	/**
	 * @param Individual $person
	 *
	 * @return string
	 */
	private function getParents(Individual $person) {
		$father = null;
		$mother = null;
		$html = sprintf(self::TTL, I18N::translate('Parents'));
		$family = $person->getPrimaryChildFamily();
		if (!Auth::isSearchEngine() && $person->canShowName() && $family !== null) {
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
						I18N::translateContext("father’s wife", "step-mother") : I18N::translateContext("mother’s husband", "step-father");
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
	 * @param Individual $person
	 *
	 * @return string
	 */
	private function getFamily(Individual $person) {
		$html = '';
		if ($person->canShowName() && !Auth::isSearchEngine()) {
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

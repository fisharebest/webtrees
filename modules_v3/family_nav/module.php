<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

class family_nav_WT_Module extends WT_Module implements WT_Module_Sidebar {

	CONST TTL = "<div class='flyout2'>%s</div>";
	CONST LNK = "<div class='flyout3' data-href='%s'>%s</div>";
	CONST MSG = "<div class='flyout4'>(%s)</div>"; // class flyout4 not used in standard themes

	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module/sidebar */ WT_I18N::translate('Family navigator');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Family navigator” module */ WT_I18N::translate('A sidebar showing an individual’s close families and relatives.');
	}

	// Implement WT_Module_Sidebar
	public function defaultSidebarOrder() {
		return 20;
	}

	// Implement WT_Module_Sidebar
	public function hasSidebarContent() {
		global $SEARCH_SPIDER;

		return !$SEARCH_SPIDER;
	}

	// Implement WT_Module_Sidebar
	public function getSidebarAjaxContent() {
		return '';
	}

	// Implement WT_Module_Sidebar
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

	private function isPerson($person) {
		return $person instanceof WT_Individual;
	}

	private function drawFamily(WT_Family $family, $title) {
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
		foreach($facts as $fact) {
			$spouse = $fact->getTarget();
			if ($this->isPerson($spouse)) {
				$menu = new WT_Menu(get_close_relationship_name($controller->record, $spouse));
				$menu->addClass('', 'submenu flyout');
				$menu->addSubMenu(new WT_Menu($this->getParents($spouse)));
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
			if ($this->isPerson($child)) {
				$menu = new WT_Menu(get_close_relationship_name($controller->record, $child));
				$menu->addClass('', 'submenu flyout');
				$menu->addSubMenu(new WT_Menu($this->getFamily($child)));
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

	private function getHTML($person, $showUnknown=false) {
		if ($this->isPerson($person)) {
			return sprintf(self::LNK, $person->getHtmlUrl(), $person->getFullName());
		} elseif ($showUnknown) {
			return sprintf(self::MSG, WT_I18N::translate('unknown'));
		} else {
			return '';
		}
	}

	private function getParents($person) {
		global $SEARCH_SPIDER;

		$father = null;
		$mother = null;
		$html = sprintf(self::TTL, WT_I18N::translate('Parents'));
		$family = $person->getPrimaryChildFamily();
		if (!$SEARCH_SPIDER && $person->canShowName() && $family !== null) {
			$father = $family->getHusband($person);
			$mother = $family->getWife($person);
			$html .= $this->getHTML($father) .
					 $this->getHTML($mother);

			// Can only have a step parent if one & only one parent found at this point
			if ($this->isPerson($father) xor $this->isPerson($mother)) {
				$stepParents = '';
				foreach ($person->getChildStepFamilies() as $family) {
					if (!$this->isPerson($father)) {
						$stepParents .= $this->getHTML($family->getHusband($person));
					} else {
						$stepParents .= $this->getHTML($family->getWife($person));
					}
				}
				if($stepParents) {
					$relationship = $this->isPerson($father) ?
						WT_I18N::translate_c("father’s wife", "step-mother") :
						WT_I18N::translate_c("mother’s husband", "step-father");
					$html .= sprintf(self::TTL, $relationship) . $stepParents;
				}
			}
		}
		if(!($this->isPerson($father) || $this->isPerson($mother))) {
			$html .= sprintf(self::MSG,  WT_I18N::translate_c('unknown family', 'unknown'));
		}
		return $html;
	}

	private function getFamily($person) {
		global $SEARCH_SPIDER;

		$html = '';
		if ($person->canShowName() && !$SEARCH_SPIDER) {
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
			$html = sprintf(self::MSG, WT_I18N::translate('none'));
		}
		return sprintf(self::TTL, WT_I18N::translate('Family')) . $html;
	}

}

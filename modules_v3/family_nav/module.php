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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class family_nav_WT_Module extends WT_Module implements WT_Module_Sidebar {
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
	public function getSidebarContent() {
		global $controller;

		ob_start();

		?>
		<div id="sb_family_nav_content">
			<table class="nav_content">

		<?php

		//-- parent families -------------------------------------------------------------
		foreach ($controller->record->getChildFamilies() as $family) { ?>
				<tr>
					<td class="center" colspan="2">
						<a class="famnav_title" href="<?php echo $family->getHtmlUrl(); ?>">
							<?php echo $controller->record->getChildFamilyLabel($family); ?>
						</a>
					</td>
				</tr>
			<?php
			$this->drawFamily($controller->record, $family);
		}

		//-- step parents ----------------------------------------------------------------
		foreach ($controller->record->getChildStepFamilies() as $family) { ?>
				<tr>
					<td class="center" colspan="2">
						<a class="famnav_title" href="<?php echo $family->getHtmlUrl(); ?>">
							<?php echo $controller->record->getStepFamilyLabel($family); ?>
						</a>
					</td>
				</tr>
			<?php
			$this->drawFamily($controller->record, $family);
		}

		//-- spouse and children --------------------------------------------------
		foreach ($controller->record->getSpouseFamilies() as $family) { ?>
				<tr>
					<td class="center" colspan="2">
						<a class="famnav_title" href="<?php echo $family->getHtmlUrl(); ?>">
							<?php echo WT_I18N::translate('Immediate Family'); ?>
						</a>
					</td>
				</tr>
			<?php
			$this->drawFamily($controller->record, $family);
		}
		//-- step children ----------------------------------------------------------------
		foreach ($controller->record->getSpouseStepFamilies() as $family) { ?>
				<tr>
					<td class="center" colspan="2">
						<a class="famnav_title" href="<?php echo $family->getHtmlUrl(); ?>">
							<?php echo $family->getFullName(); ?>
						</a>
					</td>
				</tr>
			<?php
			$this->drawFamily($controller->record, $family);
		}
		?>
			</table>
		</div>
		<?php

		return ob_get_clean();
	}

	private function drawFamily(WT_Individual $root, WT_Family $family) {
		global $controller;
		global $spouselinks, $parentlinks;
		global $SHOW_PRIVATE_RELATIONSHIPS;

		if ($SHOW_PRIVATE_RELATIONSHIPS) {
			$access_level = WT_PRIV_HIDE;
		} else {
			$access_level = WT_USER_ACCESS_LEVEL;
		}

		foreach ($family->getFacts('HUSB', false, $access_level) as $fact) {
			$spouse = $fact->getTarget();
			if ($spouse instanceof WT_Individual) {
				$menu = new WT_Menu(get_close_relationship_name($root, $spouse));
				$menu->addClass('', 'submenu flyout2');
				$submenu = new WT_Menu($this->print_pedigree_person_nav($spouse) . $parentlinks);
				$menu->addSubMenu($submenu);
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

		foreach ($family->getFacts('WIFE', false, $access_level) as $fact) {
			$spouse = $fact->getTarget();
			if ($spouse instanceof WT_Individual) {
				$menu = new WT_Menu(get_close_relationship_name($root, $spouse));
				$menu->addClass('', 'submenu flyout2');
				$submenu = new WT_Menu($this->print_pedigree_person_nav($spouse) . $parentlinks);
				$menu->addSubMenu($submenu);
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
			if ($child instanceof WT_Individual) {
				$menu = new WT_Menu(get_close_relationship_name($root, $child));
				$menu->addClass('', 'submenu flyout2');
				$submenu = new WT_Menu($this->print_pedigree_person_nav($child) . $spouselinks);
				$menu->addSubMenu($submenu);
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

	// Implement WT_Module_Sidebar
	public function getSidebarAjaxContent() {
		return '';
	}

	function print_pedigree_person_nav($person) {
		global $SEARCH_SPIDER, $spouselinks, $parentlinks, $step_parentlinks;

		$persons		= false;
		$person_step	= false;
		$person_parent	= false;
		$natdad			= false;
		$natmom			= false;

		$spouselinks      = '';
		$parentlinks      = '';
		$step_parentlinks = '';

		if ($person->canShowName() && !$SEARCH_SPIDER) {
			//-- draw a box for the family flyout
			$parentlinks      .= '<div class="flyout4">' . WT_I18N::translate('Parents') . '</div>';
			$step_parentlinks .= '<div class="flyout4">' . WT_I18N::translate('Parents') . '</div>';
			$spouselinks      .= '<div class="flyout4">' . WT_I18N::translate('Family' ) . '</div>';

			//-- parent families --------------------------------------
			$fams = $person->getChildFamilies();
			foreach ($fams as $family) {

				if (!is_null($family)) {
					$husb = $family->getHusband($person);
					$wife = $family->getWife($person);
					$children = $family->getChildren();

					// Husband ------------------------------
					if ($husb || $children) {
						if ($husb) {
							$person_parent = true;
							$parentlinks .=
								'<a class="flyout3" href="' . $husb->getHtmlUrl() . '">' .
									$husb->getFullName() .
								'</a>';
							$natdad = true;
						}
					}

					// Wife ------------------------------
					if ($wife || $children) {
						if ($wife) {
							$person_parent = true;
							$parentlinks .=
								'<a class="flyout3" href="' . $wife->getHtmlUrl() . '">' .
									$wife->getFullName() .
								'</a>';
							$natmom = true;
						}
					}
				}
			}

			//-- step families -----------------------------------------
			$fams = $person->getChildStepFamilies();
			foreach ($fams as $family) {
				if (!is_null($family)) {
					$husb = $family->getHusband($person);
					$wife = $family->getWife($person);
					$children = $family->getChildren();

					if (!$natdad) {
						// Husband -----------------------
						if ($husb || $children) {
							if ($husb) {
								$person_step = true;
								$parentlinks .=
									'<a class="flyout3" href="' . $husb->getHtmlUrl() . '">' .
										$husb->getFullName() .
									'</a>';
							}
						}
					}

					if (!$natmom) {
						// Wife ----------------------------
						if ($wife || $children) {
							if ($wife) {
								$person_step=true;
								$parentlinks .=
									'<a class="flyout3" href="' . $wife->getHtmlUrl() . '">' .
										$wife->getFullName() .
									'</a>';
							}
						}
					}
				}
			}

			// Spouse Families -------------------------------------- @var $family Family
			foreach ($person->getSpouseFamilies() as $family) {

				// Spouse ------------------------------
				$spouse = $family->getSpouse($person);
				if ($spouse) {
					$persons = true;
					$spouselinks .=
						'<a class="flyout3" href="' . $spouse->getHtmlUrl() . '">' .
							$spouse->getFullName() .
						'</a>';
				}

				// Children ------------------------------   @var $child Person
				$children = $family->getChildren();
				if ($children) {
					$persons=true;
					$spouselinks .= '<ul class="clist">';
					foreach ($children as $child) {
						$spouselinks .=
							'<li>' .
								'<a class="flyout3" href="' . $child->getHtmlUrl() . '">' .
									$child->getFullName() .
								'</a>' .
							'</li>';
					}
					$spouselinks .= '</ul>';
				}
			}
			if (!$persons) {
				$spouselinks .= '(' . WT_I18N::translate('none') . ')';
			}
			if (!$person_parent) {
				$parentlinks .= '(' . WT_I18N::translate_c('unknown family', 'unknown') . ')';
			}
			if (!$person_step) {
				$step_parentlinks .= '(' . WT_I18N::translate_c('unknown family', 'unknown') . ')';
			}
		}
	}
}

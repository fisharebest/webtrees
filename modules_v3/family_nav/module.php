<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

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
		return true;
	}

	// Implement WT_Module_Sidebar
	public function getSidebarContent() {
		global $controller;
		global $spouselinks, $parentlinks;

		ob_start();

		echo '<div id="sb_family_nav_content"><table class="nav_content">';

		//-- parent families -------------------------------------------------------------
		foreach ($controller->record->getChildFamilies() as $family) {
			$people = $controller->buildFamilyList($family, 'parents');
			echo '<tr><td style="padding-bottom:4px;" class="center" colspan="2">';
			echo '<a class="famnav_link" href="' . $family->getHtmlUrl() . '">';
			echo '<b>' . $controller->record->getChildFamilyLabel($family) . '</b>';
			echo '</a>';
			echo '</td></tr>';
			if (isset($people['husb'])) {
				$menu = new WT_Menu(get_relationship_name(get_relationship($controller->record, $people['husb'])));
				$menu->addClass('', 'submenu flyout2');
				$submenu = new WT_Menu($this->print_pedigree_person_nav($people['husb']) . $parentlinks);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people['husb']), ' nam">';
				echo '<a class="famnav_link" href="' . $people["husb"]->getHtmlUrl() . '">';
				echo $people['husb']->getFullName();
				echo '</a>';
				echo '<div class="font9">' . $people['husb']->getLifeSpan() . '</div>';
				echo '</td></tr>';
			}

			if (isset($people['wife'])) {
				$menu = new WT_Menu(get_relationship_name(get_relationship($controller->record, $people['wife'])));
				$menu->addClass('', 'submenu flyout2');
				$submenu = new WT_Menu($this->print_pedigree_person_nav($people['wife']) . $parentlinks);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people['wife']), ' nam">';
				echo '<a class="famnav_link" href="' . $people['wife']->getHtmlUrl() . '">';
				echo $people['wife']->getFullName();
				echo '</a>';
				echo '<div class="font9">' . $people['wife']->getLifeSpan() . '</div>';
				echo '</td></tr>';
			}

			foreach ($people['children'] as $child) {
				if ($controller->record->equals($child)) {
					$menu = new WT_Menu('<i class="icon-selected"></i>');
				} else {
					$menu = new WT_Menu(get_relationship_name(get_relationship($controller->record, $child)));
				}
				$menu->addClass('', 'submenu flyout2');
				$submenu = new WT_Menu($this->print_pedigree_person_nav($child) . $spouselinks);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label" style="width:75px;">';
				echo $menu->getMenu();
				echo '</td><td class="center ', $controller->getPersonStyle($child), ' nam">';
				echo '<a class="famnav_link" href="' . $child->getHtmlUrl() . '">';
				echo $child->getFullName();
				echo '</a>';
				echo '<div class="font9">' . $child->getLifeSpan() . '</div>';
				echo '</td></tr>';
			}
		}

		//-- step parents ----------------------------------------------------------------
		foreach ($controller->record->getChildStepFamilies() as $family) {
			$people = $controller->buildFamilyList($family, 'step-parents');
			echo '<tr><td><br></td><td></td></tr>';
			echo '<tr><td style="padding-bottom: 4px;" class="center" colspan="2">';
			echo '<a class="famnav_link" href="' . $family->getHtmlUrl() . '">';
			echo '<b>' . $controller->record->getStepFamilyLabel($family) . '</b>';
			echo '</a>';
			echo '</td></tr>';

			if (isset($people['husb']) ) {
				$menu = new WT_Menu(get_relationship_name(get_relationship($controller->record, $people['husb'])));
				$menu->addClass('', 'submenu flyout2');
				$submenu = new WT_Menu($this->print_pedigree_person_nav($people['husb']) . $parentlinks);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people["husb"]), ' nam">';
				echo '<a class="famnav_link" href="' . $people['husb']->getHtmlUrl() . '">';
				echo $people['husb']->getFullName();
				echo '</a>';
				echo '<div class="font9">' . $people['husb']->getLifeSpan() . '</div>';
				echo '</td></tr>';
			}

			if (isset($people['wife']) ) {
				$menu = new WT_Menu(get_relationship_name(get_relationship($controller->record, $people['wife'])));
				$menu->addClass('', 'submenu flyout2');
				$submenu = new WT_Menu($this->print_pedigree_person_nav($people['wife']) . $parentlinks);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people['wife']), ' nam">';
				echo '<a class="famnav_link" href="' . $people['wife']->getHtmlUrl() . '">';
				echo $people['wife']->getFullName();
				echo '</a>';
				echo '<div class="font9">' . $people['wife']->getLifeSpan() . '</div>';
				echo '</td></tr>';
			}
			foreach ($people['children'] as $child) {
				$menu = new WT_Menu(get_relationship_name(get_relationship($controller->record, $child)));
				$menu->addClass('', 'submenu flyout2');
				$submenu = new WT_Menu($this->print_pedigree_person_nav($child) . $spouselinks);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($child), ' nam">';
				echo '<a class="famnav_link" href="' . $child->getHtmlUrl() . '">';
				echo $child->getFullName();
				echo '</a>';
				echo '<div class="font9">' . $child->getLifeSpan() . '</div>';
				echo '</td></tr>';
			}
		}

		//-- spouse and children --------------------------------------------------
		foreach ($controller->record->getSpouseFamilies() as $family) {
			echo '<tr><td><br></td><td></td></tr>';
			echo '<tr><td style="padding-bottom: 4px;" class="center" colspan="2">';
			echo '<a class="famnav_link" href="' . $family->getHtmlUrl() . '">';
			echo '<b>' . WT_I18N::translate('Immediate Family') . '</b>';
			echo '</a>';
			echo '</td></tr>';
			$people = $controller->buildFamilyList($family, 'spouse');
			if (isset($people['husb'])) {
				if ($controller->record->equals($people['husb'])) {
					$menu = new WT_Menu('<i class="icon-selected"></i>');
				} else {
					$menu = new WT_Menu(get_relationship_name(get_relationship($controller->record, $people['husb'])));
				}
				$menu->addClass('', 'submenu flyout2');
				$submenu = new WT_Menu($this->print_pedigree_person_nav($people['husb']) . $parentlinks);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people['husb']), ' nam">';
				echo '<a class="famnav_link" href="' . $people['husb']->getHtmlUrl() . '">';
				echo $people['husb']->getFullName();
				echo '</a>';
				echo '<div class="font9">' . $people['husb']->getLifeSpan() . '</div>';
				echo '</td></tr>';
			}

			if (isset($people['wife'])) {
				if ($controller->record->equals($people['wife'])) {
					$menu = new WT_Menu('<i class="icon-selected"></i>');
				} else {
					$menu = new WT_Menu(get_relationship_name(get_relationship($controller->record, $people['wife'])));
				}
				$menu->addClass('', 'submenu flyout2');
				$submenu = new WT_Menu($this->print_pedigree_person_nav($people['wife']) . $parentlinks);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people['wife']), ' nam">';
				echo '<a class="famnav_link" href="' . $people['wife']->getHtmlUrl() . '">';
				echo $people['wife']->getFullName();
				echo '</a>';
				echo '<div class="font9">' . $people['wife']->getLifeSpan() . '</div>';
				echo '</td></tr>';
			}

			foreach ($people['children'] as $child) {
				$menu = new WT_Menu(get_relationship_name(get_relationship($controller->record, $child)));
				$menu->addClass('', 'submenu flyout2');
				$submenu = new WT_Menu($this->print_pedigree_person_nav($child) . $spouselinks);
				$menu->addSubmenu($submenu);
				echo '<tr><td class="facts_label" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($child), ' nam">';
				echo '<a class="famnav_link" href="' . $child->getHtmlUrl() . '">';
				echo $child->getFullName();
				echo '</a>';
				echo '<div class="font9">' . $child->getLifeSpan() . '</div>';
				echo '</td></tr>';
			}
		}
		//-- step children ----------------------------------------------------------------
		foreach ($controller->record->getSpouseStepFamilies() as $family) {
			$people = $controller->buildFamilyList($family, 'step-children');
			echo '<tr><td><br></td><td></td></tr>';
			echo '<tr><td style="padding-bottom: 4px;" class="center" colspan="2">';
			echo '<a class="famnav_link" href="' . $family->getHtmlUrl() . '">';
			echo '<b>' . $family->getFullName() . '</b>';
			echo '</a>';
			echo '</td></tr>';

			if (isset($people['husb']) ) {
				$menu = new WT_Menu(get_relationship_name(get_relationship($controller->record, $people['husb'])));
				$menu->addClass('', 'submenu flyout2');
				$submenu = new WT_Menu($this->print_pedigree_person_nav($people['husb']) . $parentlinks);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people['husb']), ' nam">';
				echo '<a class="famnav_link" href="' . $people['husb']->getHtmlUrl() . '">';
				echo $people['husb']->getFullName();
				echo '</a>';
				echo '<div class="font9">' . $people['husb']->getLifeSpan() . '</div>';
				echo '</td></tr>';
			}

			if (isset($people['wife']) ) {
				$menu = new WT_Menu(get_relationship_name(get_relationship($controller->record, $people['wife'])));
				$menu->addClass('', 'submenu flyout2');
				$submenu = new WT_Menu($this->print_pedigree_person_nav($people['wife']) . $parentlinks);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people['wife']), ' nam">';
				echo '<a class="famnav_link" href="' . $people['wife']->getHtmlUrl() . '">';
				echo $people['wife']->getFullName();
				echo '</a>';
				echo '<div class="font9">' . $people['wife']->getLifeSpan() . '</div>';
				echo '</td></tr>';
			}
			foreach ($people['children'] as $child) {
				$menu = new WT_Menu(get_relationship_name(get_relationship($controller->record, $child)));
				$menu->addClass('', 'submenu flyout2');
				$submenu = new WT_Menu($this->print_pedigree_person_nav($child) . $spouselinks);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($child), ' nam">';
				echo '<a class="famnav_link" href="' . $child->getHtmlUrl() . '">';
				echo $child->getFullName();
				echo '</a>';
				echo '<div class="font9">' . $child->getLifeSpan() . '</div>';
				echo '</td></tr>';
			}
		}

		echo '</table></div>';

		return ob_get_clean();
	}

	// Implement WT_Module_Sidebar
	public function getSidebarAjaxContent() {
		return '';
	}

	function print_pedigree_person_nav($person) {
		global $SEARCH_SPIDER;

		global $spouselinks, $parentlinks, $step_parentlinks;

		$persons = '';
		$person_step = '';
		$person_parent = '';
		$natdad = '';
		$natmom = '';

		$tmp = array('M'=>'','F'=>'F', 'U'=>'NN');
		$isF = $tmp[$person->getSex()];
		$spouselinks      = '';
		$parentlinks      = '';
		$step_parentlinks = '';

		if ($person->canShowName() && !$SEARCH_SPIDER) {
			//-- draw a box for the family flyout
			$parentlinks      .= '<div class="flyout4"><b>' . WT_I18N::translate('Parents') . '</b></div>';
			$step_parentlinks .= '<div class="flyout4"><b>' . WT_I18N::translate('Parents') . '</b></div>';
			$spouselinks      .= '<div class="flyout4"><b>' . WT_I18N::translate('Family' ) . '</b></div>';

			$persons       = '';
			$person_parent = '';
			$person_step   = '';

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
							$person_parent = 'Yes';
							$parentlinks .= '<a class="flyout3" href="' . $husb->getHtmlUrl() . '">';
							$parentlinks .= $husb->getFullName();
							$parentlinks .= '</a>';
							$parentlinks .= '<br>';
							$natdad = 'yes';
						}
					}

					// Wife ------------------------------
					if ($wife || $children) {
						if ($wife) {
							$person_parent = 'Yes';
							$parentlinks .= '<a class="flyout3" href="' . $wife->getHtmlUrl() . '">';
							$parentlinks .= $wife->getFullName();
							$parentlinks .= '</a>';
							$parentlinks .= '<br>';
							$natmom = 'yes';
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

					if ($natdad != 'yes') {
						// Husband -----------------------
						if ($husb || $children) {
							if ($husb) {
								$person_step = 'Yes';
								$parentlinks .= '<a class="flyout3" href="' . $husb->getHtmlUrl() . '">';
								$parentlinks .= $husb->getFullName();
								$parentlinks .= '</a>';
								$parentlinks .= '<br>';
							}
						}
					}

					if ($natmom != 'yes') {
						// Wife ----------------------------
						if ($wife || $children) {
							if ($wife) {
								$person_step='Yes';
								$parentlinks .= '<a class="flyout3" href="' . $wife->getHtmlUrl() . '">';
								$parentlinks .= $wife->getFullName();
								$parentlinks .= '</a>';
								$parentlinks .= '<br>';
							}
						}
					}
				}
			}

			// Spouse Families -------------------------------------- @var $family Family
			foreach ($person->getSpouseFamilies() as $family) {
				$spouse = $family->getSpouse($person);
				$children = $family->getChildren();

				// Spouse ------------------------------
				if ($spouse || $children) {
					if ($spouse) {
						$spouselinks .= '<a class="flyout3" href="' . $spouse->getHtmlUrl() . '">';
						$spouselinks .= $spouse->getFullName();
						$spouselinks .= '</a>';
						$spouselinks .= '<br>';
						if ($spouse->getFullName() != '') {
							$persons = 'Yes';
						}
					}
				}

				// Children ------------------------------   @var $child Person
				foreach ($children as $child) {
					$persons='Yes';
					$spouselinks .= '<ul class="clist">';
					$spouselinks .= '<li class="flyout3">';
					$spouselinks .= '<a href="' . $child->getHtmlUrl() . '">';
					$spouselinks .= $child->getFullName();
					$spouselinks .= '</a>';
					$spouselinks .= '</li>';
					$spouselinks .= '</ul>';
				}
			}
			if ($persons != 'Yes') {
				$spouselinks .= '(' . WT_I18N::translate('none') . ')';
			}
			if ($person_parent != 'Yes') {
				$parentlinks .= '(' . WT_I18N::translate_c('unknown family', 'unknown') . ')';
			}
			if ($person_step != 'Yes') {
				$step_parentlinks .= '(' . WT_I18N::translate_c('unknown family', 'unknown') . ')';
			}
		}
	}
}

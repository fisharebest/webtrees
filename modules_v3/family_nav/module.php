<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
		return /* I18N: Description of the "Family navigator" module */ WT_I18N::translate('A sidebar showing an individual’s close families and relatives.');
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
		return '<div id="sb_family_nav_content">'.$this->getTabContent().'</div>';
	}

	// Implement WT_Module_Sidebar
	public function getSidebarAjaxContent() {
		return '';
	}

	// TODO: This function isn't part of the WT_Module_Tab interface, as
	// this module no longer provides a tab.
	public function getTabContent() {
		global $controller;

		$out = '';
		ob_start();

		global $show_full, $edit, $GEDCOM, $pid;
		$show_full="1";

		// =====================================================================

		// Start Family Nav Table ----------------------------
		echo '<table class="nav_content">';
		global $spouselinks, $parentlinks;

		$personcount=0;
		$families = $controller->record->getChildFamilies();

		//-- parent families -------------------------------------------------------------
		foreach ($families as $famid=>$family) {
			$label = $controller->record->getChildFamilyLabel($family);
			$people = $controller->buildFamilyList($family, "parents");
			echo '<tr><td style="padding-bottom:4px;" class="center" colspan="2">';
			echo '<a class="famnav_link" href="' . $family->getHtmlUrl() . '">';
			echo '<b>' . $label . '</b>';
			echo '</a>';
			echo '</td></tr>';
			if (isset($people["husb"])) {
				$menu = new WT_Menu("&nbsp;" . $people["husb"]->getLabel());
				$menu->addClass('', 'submenu flyout2');
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["husb"]->getXref(), 2, 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new WT_Menu($slabel);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label nowrap" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people["husb"]), ' nam">';
				echo "<a class=\"famnav_link\" href=\"".$people["husb"]->getHtmlUrl()."\">";
				echo $people["husb"]->getFullName();
				echo "</a>";
				echo "<div class=\"font9\">" . $people["husb"]->getLifeSpan() . "</div>";
				echo '</td></tr>';
			}

			if (isset($people["wife"])) {
				$menu = new WT_Menu("&nbsp;" . $people["wife"]->getLabel());
				$menu->addClass('', 'submenu flyout2');
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["wife"]->getXref(), 2, 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new WT_Menu($slabel);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label nowrap" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people["wife"]), ' nam">';
				echo "<a class=\"famnav_link\" href=\"".$people["wife"]->getHtmlUrl()."\">";
				echo $people["wife"]->getFullName();
				echo "</a>";
				echo "<div class=\"font9\">" . $people["wife"]->getLifeSpan() . "</div>";
				echo '</td></tr>';
			}

			if (isset($people["children"])) {
				foreach ($people["children"] as $key=>$child) {
					if ($pid != $child->getXref()) {
						$menu = new WT_Menu("&nbsp;" . $child->getLabel());
						$menu->addClass('', 'submenu flyout2');
						$slabel  = "</a>".$this->print_pedigree_person_nav($child->getXref(), 2, 0, $personcount++);
						$slabel .= $spouselinks."<a>";
						$submenu = new WT_Menu($slabel);
						$menu->addSubMenu($submenu);
					}
					echo '<tr><td class="facts_label nowrap" style="width:75px;">';
					if ($pid == $child->getXref() ) {
						echo $child->getLabel();
					} else {
						echo $menu->getMenu();
					}
					echo '</td><td class="center ', $controller->getPersonStyle($child), ' nam">';
					if ($pid == $child->getXref()) {
						echo "<a class=\"famnav_link\" href=\"#\">";
						echo $child->getFullName();
						echo "</a>";
						echo "<div class=\"font9\">".$child->getLifeSpan() . "</div>";
					} else {
						echo "<a class=\"famnav_link\" href=\"".$child->getHtmlUrl()."\">";
						echo $child->getFullName();
						echo "</a>";
						echo "<div class=\"font9\">" . $child->getLifeSpan() . "</div>";
					}
					echo '</td></tr>';
				}
			}
		}

		//-- step parents ----------------------------------------------------------------
		foreach ($controller->record->getChildStepFamilies() as $famid=>$family) {
			$label = $controller->record->getStepFamilyLabel($family);
			$people = $controller->buildFamilyList($family, "step-parents");
			if ($people) {
				echo "<tr><td><br></td><td></td></tr>";
			}
			echo '<tr><td style="padding-bottom: 4px;" class="center" colspan="2">';
			echo "<a class=\"famnav_link\" href=\"".$family->getHtmlUrl()."\">";
			echo "<b>".$label."</b>";
			echo "</a>";
			echo '</td></tr>';

			if (isset($people["husb"]) ) {
				$menu = new WT_Menu();
				if ($people["husb"]->getLabel() == ".") {
					$menu->addLabel(WT_I18N::translate_c('mother\'s husband', 'step-father'));
				} else {
					$menu->addLabel($people["husb"]->getLabel());
				}
				$menu->addClass('', 'submenu flyout2');
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["husb"]->getXref(), 2, 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new WT_Menu($slabel);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label nowrap" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people["husb"]), ' nam">';
				echo "<a class=\"famnav_link\" href=\"".$people["husb"]->getHtmlUrl()."\">";
				echo $people["husb"]->getFullName();
				echo "</a>";
				echo "<div class=\"font9\">" . $people["husb"]->getLifeSpan() . "</div>";
				echo '</td></tr>';
			}

			if (isset($people["wife"]) ) {
				$menu = new WT_Menu();
				if ($people["wife"]->getLabel() == ".") {
					$menu->addLabel(WT_I18N::translate_c('father\'s wife', 'step-mother'));
				} else {
					$menu->addLabel($people["wife"]->getLabel());
				}
				$menu->addClass('', 'submenu flyout2');
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["wife"]->getXref(), 2, 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new WT_Menu($slabel);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label nowrap" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people["wife"]), ' nam">';
				echo "<a class=\"famnav_link\" href=\"".$people["wife"]->getHtmlUrl()."\">";
				echo $people["wife"]->getFullName();
				echo "</a>";
				echo "<div class=\"font9\">" . $people["wife"]->getLifeSpan() . "</div>";
				echo '</td></tr>';
			}
			if (isset($people["children"])) {
				foreach ($people["children"] as $key=>$child) {
					$menu = new WT_Menu($child->getLabel());
					$menu->addClass('', 'submenu flyout2');
					$slabel  = "</a>".$this->print_pedigree_person_nav($child->getXref(), 2, 0, $personcount++);
					$slabel .= $spouselinks."<a>";
					$submenu = new WT_Menu($slabel);
					$menu->addSubMenu($submenu);
					echo '<tr><td class="facts_label nowrap" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($child), ' nam">';
					echo "<a class=\"famnav_link\" href=\"".$child->getHtmlUrl()."\">";
					echo $child->getFullName();
					echo "</a>";
					echo "<div class=\"font9\">" . $child->getLifeSpan() . "</div>";
					echo '</td></tr>';
				}
			}
		}

		//-- spouse and children --------------------------------------------------
		$families = $controller->record->getSpouseFamilies();
		foreach ($families as $family) {
			echo "<tr><td><br></td><td></td></tr>";
			echo '<tr><td style="padding-bottom: 4px;" class="center" colspan="2">';
			echo "<a class=\"famnav_link\" href=\"".$family->getHtmlUrl()."\">";
			echo "<b>".WT_I18N::translate('Immediate Family')."</b>";
			echo "</a>";
			echo '</td></tr>';
			$people = $controller->buildFamilyList($family, "spouse");
			if (isset($people["husb"]) && $controller->record->equals($people["husb"])) {
				$spousetag = 'WIFE';
			} else {
				$spousetag = 'HUSB';
			}
			if (isset($people["husb"]) && $spousetag == 'HUSB') {
				$menu = new WT_Menu("&nbsp;" . $people["husb"]->getLabel());
				$menu->addClass('', 'submenu flyout2');
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["husb"]->getXref(), 2, 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new WT_Menu($slabel);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label nowrap" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people["husb"]), ' nam">';
				if ($pid == $people["husb"]->getXref()) {
					echo "<a class=\"famnav_link\" href=\"#\">";
					echo $people["husb"]->getFullName();
					echo "</a>";
					echo "<div class=\"font9\">" . $people["husb"]->getLifeSpan() . "</div>";
				} else {
					echo "<a class=\"famnav_link\" href=\"".$people["husb"]->getHtmlUrl()."\">";
					echo $people["husb"]->getFullName();
					echo "</a>";
					echo "<div class=\"font9\">" . $people["husb"]->getLifeSpan() . "</div>";
				}
				echo '</td></tr>';
			}

			if (isset($people["wife"]) && $spousetag == 'WIFE') {
				$menu = new WT_Menu("&nbsp;" . $people["wife"]->getLabel());
				$menu->addClass('', 'submenu flyout2');
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["wife"]->getXref(), 2, 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new WT_Menu($slabel);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label nowrap" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people["wife"]), ' nam">';
				if ($pid == $people["wife"]->getXref()) {
					echo "<a class=\"famnav_link\" href=\"#\">";
					echo $people["wife"]->getFullName();
					echo "</a>";
					echo "<div class=\"font9\">" . $people["wife"]->getLifeSpan() . "</div>";
				} else {
					echo "<a class=\"famnav_link\" href=\"".$people["wife"]->getHtmlUrl()."\">";
					echo $people["wife"]->getFullName();
					echo "</a>";
					echo "<div class=\"font9\">" . $people["wife"]->getLifeSpan() . "</div>";
				}
				echo '</td></tr>';
			}

			if (isset($people["children"])) {
				foreach ($people["children"] as $key=>$child) {
					$menu = new WT_Menu("&nbsp;" . $child->getLabel());
					$menu->addClass('', 'submenu flyout2');
					$slabel = "</a>".$this->print_pedigree_person_nav($child->getXref(), 2, 0, $personcount++);
					$slabel .= $spouselinks."<a>";
					$submenu = new WT_Menu($slabel);
					$menu->addSubmenu($submenu);
					echo '<tr><td class="facts_label nowrap" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($child), ' nam">';
					echo "<a class=\"famnav_link\" href=\"".$child->getHtmlUrl()."\">";
					echo $child->getFullName();
					echo "</a>";
					echo "<div class=\"font9\">" . $child->getLifeSpan() . "</div>";
					echo '</td></tr>';
				}
			}

		}
		//-- step children ----------------------------------------------------------------
		foreach ($controller->record->getSpouseStepFamilies() as $famid=>$family) {
			$label = $family->getFullName();
			$people = $controller->buildFamilyList($family, "step-children");
			if ($people) {
				echo "<tr><td><br></td><td></td></tr>";
			}
			echo '<tr><td style="padding-bottom: 4px;" class="center" colspan="2">';
			echo "<a class=\"famnav_link\" href=\"".$family->getHtmlUrl()."\">";
			echo "<b>".$label."</b>";
			echo "</a>";
			echo '</td></tr>';

			if (isset($people["husb"]) ) {
				$menu = new WT_Menu($people["husb"]->getLabel());
				$menu->addClass('', 'submenu flyout2');
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["husb"]->getXref(), 2, 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new WT_Menu($slabel);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label nowrap" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people["husb"]), ' nam">';
				echo "<a class=\"famnav_link\" href=\"".$people["husb"]->getHtmlUrl()."\">";
				echo $people["husb"]->getFullName();
				echo "</a>";
				echo "<div class=\"font9\">" . $people["husb"]->getLifeSpan() . "</div>";
				echo '</td></tr>';
			}

			if (isset($people["wife"]) ) {
				$menu = new WT_Menu($people["wife"]->getLabel());
				$menu->addClass('', 'submenu flyout2');
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["wife"]->getXref(), 2, 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new WT_Menu($slabel);
				$menu->addSubMenu($submenu);
				echo '<tr><td class="facts_label nowrap" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($people["wife"]), ' nam">';
				echo "<a class=\"famnav_link\" href=\"".$people["wife"]->getHtmlUrl()."\">";
				echo $people["wife"]->getFullName();
				echo "</a>";
				echo "<div class=\"font9\">" . $people["wife"]->getLifeSpan() . "</div>";
				echo '</td></tr>';
			}
			if (isset($people["children"])) {
				foreach ($people["children"] as $key=>$child) {
					$menu = new WT_Menu($child->getLabel());
					$menu->addClass('', 'submenu flyout2');
					$slabel  = "</a>".$this->print_pedigree_person_nav($child->getXref(), 2, 0, $personcount++);
					$slabel .= $spouselinks."<a>";
					$submenu = new WT_Menu($slabel);
					$menu->addSubMenu($submenu);
					echo '<tr><td class="facts_label nowrap" style="width:75px;">', $menu->getMenu(), '</td><td class="center ', $controller->getPersonStyle($child), ' nam">';
					echo "<a class=\"famnav_link\" href=\"".$child->getHtmlUrl()."\">";
					echo $child->getFullName();
					echo "</a>";
					echo "<div class=\"font9\">" . $child->getLifeSpan() . "</div>";
					echo '</td></tr>';
				}
			}
		}

		echo "</table>";

		// -----------------------------------------------------------------------------
		// }
		// -----------------------------------------------------------------------------
		// End Family Nav Table
		// -----------------------------------------------------------------------------

		$out .= ob_get_contents();
		ob_end_clean();
		return $out;

	} // End public function getTabContent()

	function print_pedigree_person_nav($pid, $style=1, $count=0, $personcount="1") {
		global $HIDE_LIVE_PEOPLE, $SHOW_LIVING_NAMES, $SCRIPT_NAME, $GEDCOM;
		global $SHOW_HIGHLIGHT_IMAGES, $bwidth, $bheight, $PEDIGREE_FULL_DETAILS, $SHOW_PEDIGREE_PLACES;
		global $DEFAULT_PEDIGREE_GENERATIONS, $OLD_PGENS, $talloffset, $PEDIGREE_LAYOUT, $MEDIA_DIRECTORY;
		global $ABBREVIATE_CHART_LABELS;
		global $chart_style, $box_width, $generations, $show_spouse, $show_full;
		global $CHART_BOX_TAGS, $SHOW_LDS_AT_GLANCE, $PEDIGREE_SHOW_GENDER;
		global $SEARCH_SPIDER;

		global $spouselinks, $parentlinks, $step_parentlinks, $persons, $person_step, $person_parent, $tabno, $spousetag;
		global $natdad, $natmom;

		if ($style != 2) $style=1;
		if (empty($show_full)) $show_full = 0;
		if (empty($PEDIGREE_FULL_DETAILS)) $PEDIGREE_FULL_DETAILS = 0;

		if (!isset($OLD_PGENS)) $OLD_PGENS = $DEFAULT_PEDIGREE_GENERATIONS;
		if (!isset($talloffset)) $talloffset = $PEDIGREE_LAYOUT;

		$person=WT_Person::getInstance($pid);
		if ($pid==false || empty($person)) {
			$spouselinks  = false;
			$parentlinks  = false;
			$step_parentlinks = false;
		}

		$tmp=array('M'=>'','F'=>'F', 'U'=>'NN');
		$isF=$tmp[$person->getSex()];
		$spouselinks = "";
		$parentlinks = "";
		$step_parentlinks   = "";
		$disp=$person->canDisplayDetails();

		if ($person->canDisplayName() && !$SEARCH_SPIDER) {
			//-- draw a box for the family flyout
			$parentlinks .= "<span class=\"flyout4\"><b>".WT_I18N::translate('Parents')."</b></span><br>";
			$step_parentlinks .= "<span class=\"flyout4\"><b>".WT_I18N::translate('Parents')."</b></span><br>";
			$spouselinks .= "<span class=\"flyout4\"><b>".WT_I18N::translate('Family')."</b></span><br>";

			$persons = "";
			$person_parent = "";
			$person_step = "";

			//-- parent families --------------------------------------
			$fams = $person->getChildFamilies();
			foreach ($fams as $famid=>$family) {

				if (!is_null($family)) {
					$husb = $family->getHusband($person);
					$wife = $family->getWife($person);
					// $spouse = $family->getSpouse($person);
					$children = $family->getChildren();
					$num = count($children);

					// Husband ------------------------------
					if ($husb || $num>0) {
						if ($husb) {
							$person_parent="Yes";
							$parentlinks .= "<a class=\"flyout3\" href=\"".$husb->getHtmlUrl()."\">";
							$parentlinks .= "&nbsp;".$husb->getFullName();
							$parentlinks .= "</a>";
							$parentlinks .= "<br>";
							$natdad = "yes";
						}
					}

					// Wife ------------------------------
					if ($wife || $num>0) {
						if ($wife) {
							$person_parent="Yes";
							$parentlinks .= "<a class=\"flyout3\" href=\"".$wife->getHtmlUrl()."\">";
							$parentlinks .= "&nbsp;".$wife->getFullName();
							$parentlinks .= "</a>";
							$parentlinks .= "<br>";
							$natmom = "yes";
						}
					}
				}
			}

			//-- step families -----------------------------------------
			$fams = $person->getChildStepFamilies();
			foreach ($fams as $famid=>$family) {
				if (!is_null($family)) {
					$husb = $family->getHusband($person);
					$wife = $family->getWife($person);
					// $spouse = $family->getSpouse($person);
					$children = $family->getChildren();
					$num = count($children);

					if ($natdad == "yes") {
					} else {
						// Husband -----------------------
						if ($husb || $num>0) {
							if ($husb) {
								$person_step="Yes";
								$parentlinks .= "<a class=\"flyout3\" href=\"".$husb->getHtmlUrl()."\">";
								$parentlinks .= "&nbsp;".$husb->getFullName();
								$parentlinks .= "</a>";
								$parentlinks .= "<br>";
							}
						}
					}

					if ($natmom != "yes") {
						// Wife ----------------------------
						if ($wife || $num>0) {
							if ($wife) {
								$person_step="Yes";
								$parentlinks .= "<a class=\"flyout3\" href=\"".$wife->getHtmlUrl()."\">";
								$parentlinks .= "&nbsp;".$wife->getFullName();
								$parentlinks .= "</a>";
								$parentlinks .= "<br>";
							}
						}
					}
				}
			}

			// Spouse Families -------------------------------------- @var $family Family
			foreach ($person->getSpouseFamilies() as $family) {
				$spouse = $family->getSpouse($person);
				$children = $family->getChildren();
				$num = count($children);

				// Spouse ------------------------------
				if ($spouse || $num>0) {
					if ($spouse) {
						$spouselinks .= "<a class=\"flyout3\" href=\"".$spouse->getHtmlUrl()."\">";
						$spouselinks .= "&nbsp;".$spouse->getFullName();
						$spouselinks .= "</a>";
						$spouselinks .= "<br>";
						if ($spouse->getFullName() != "") {
							$persons = "Yes";
						}
					}
				}

				// Children ------------------------------   @var $child Person
				$hasChildren = false;
				foreach ($children as $c=>$child) {
					if ($child) {
						if (!$hasChildren) {
							$hasChildren = true;
						}
						$persons="Yes";
						$spouselinks .= "<ul class=\"clist\">";
						$spouselinks .= "<li class=\"flyout3\">";
						$spouselinks .= "<a href=\"".$child->getHtmlUrl()."\">";
						$spouselinks .= $child->getFullName();
						$spouselinks .= "</a>";
						$spouselinks .= "</li>";
						$spouselinks .= "</ul>";
					}
				}
			}
			if ($persons != "Yes") {
				$spouselinks  .= "&nbsp;(".WT_I18N::translate('none').")";
			}
			if ($person_parent != "Yes") {
				$parentlinks .= "&nbsp;(".WT_I18N::translate_c('unknown family', 'unknown').")";
			}
			if ($person_step != "Yes") {
				$step_parentlinks .= "&nbsp;(".WT_I18N::translate_c('unknown family', 'unknown').")";
			}
		}
	}
}

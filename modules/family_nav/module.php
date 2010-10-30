<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2009 John Finlay
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Modules
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/classes/class_module.php';
require_once WT_ROOT.'includes/functions/functions_charts.php';
require_once WT_ROOT.'includes/controllers/individual_ctrl.php';

class family_nav_WT_Module extends WT_Module implements WT_Module_Sidebar {
	// Extend WT_Module
	public function getTitle() {
		return i18n::translate('Family Navigator');
	}

	// Extend WT_Module
	public function getDescription() {
		return i18n::translate('Adds a tab to the individual page which displays a family navigator on the individual page.');
	}

	// Implement WT_Module_Sidebar
	public function defaultSidebarOrder() {
		return 10;
	}

	// Implement WT_Module_Sidebar
	public function hasSidebarContent() {
		return true;
	}

	// Implement WT_Module_Sidebar
	public function getSidebarContent() {
		global $WT_IMAGES;

		$out = '<div id="sb_family_nav_content">';

		if ($this->controller) {
			$root = null;
			if ($this->controller->pid) {
				$root = Person::getInstance($this->controller->pid);
			}
			else if ($this->controller->famid) {
				$fam = Family::getInstance($this->controller->famid);
				if ($fam) $root = $fam->getHusband();
				if (!$root) $root = $fam->getWife();
			}
			if ($root!=null) {
				$this->controller = new IndividualController();
				$this->controller->indi=$root;
				$this->controller->pid=$root->getXref();
				$this->setController($this->controller);
				$out .= $this->getTabContent();
			}
		}
		$out .= '</div>';
		return $out;
	}

	// Implement WT_Module_Sidebar
	public function getSidebarAjaxContent() {
		return "";
	}

	// TODO: These functions aren't really part of the WT_Module_Tab interface, as
	// this module no longer provides a tab.
	public function hasTabContent() {
		return true;
	}
	public function getTabContent() {
		$out = '';
		ob_start();
		// -----------------------------------------------------------------------------
		// Function Family Nav for PHPGedView - called by individual_ctrl.php
		// -----------------------------------------------------------------------------
		// function family_nav() {
		// ------------------------------------------------------------------------------

		global $edit, $tabno, $GEDCOM, $pid;
		$edit=$edit;
		global $show_full, $tabno;
		$show_full="1";

		// =====================================================================

		echo WT_JS_START;
		echo 'function familyNavLoad(url) {
			window.location = url+"#"+jQuery("#tabs li:eq("+jQuery("#tabs").tabs("option", "selected")+") a").attr("title");
			return false;
		}
		';
		echo WT_JS_END;

	 // Start Family Nav Table ----------------------------
		echo "<table class=\"nav_content\" cellpadding=\"0\">";
		global $WT_IMAGES, $spouselinks, $parentlinks, $DeathYr, $BirthYr, $TEXT_DIRECTION;

		$personcount=0;
		$families = $this->controller->indi->getChildFamilies();

		//-- parent families -------------------------------------------------------------
		foreach ($families as $famid=>$family) {
			$label = $this->controller->indi->getChildFamilyLabel($family);
			$people = $this->controller->buildFamilyList($family, "parents");
			$styleadd = "";
			?>
			<tr>
				<td style="padding-bottom:4px;" align="center" colspan="2">
				<?php
				echo "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"family.php?famid=".$famid."\" onclick=\"return familyNavLoad('family.php?famid=".$famid."');\">";
				echo "<b>".$label."</b>";
				echo "</a>";
				?>
				</td>
			</tr>
			<?php
			if (isset($people["husb"])) {
				$menu = new Menu("&nbsp;" . $people["husb"]->getLabel());
				// $menu->addClass("", "", "submenu");
				if ($TEXT_DIRECTION=="ltr") {
					$menu->addClass("", "", "submenu flyout2");
				} else {
					$menu->addClass("", "", "submenu flyout2rtl");
				}
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["husb"]->getXref(), 2, 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; } else { $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
				if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; } else { $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php echo $styleadd; ?>" nowrap="nowrap" style="width:75px;">
						<?php echo $menu->getMenu(); ?>
					</td>
					<td align="center" class="<?php echo $this->controller->getPersonStyle($people["husb"]); ?> nam">
						<?php
						echo "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".$people["husb"]->getHtmlUrl()."\" onclick=\"return familyNavLoad('".$people['husb']->getHtmlUrl()."');\">";
						echo PrintReady($people["husb"]->getFullName());
						echo "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						echo "</a>";
						?>
					</td>
				</tr>
				<?php
			}

			if (isset($people["wife"])) {
				$menu = new Menu("&nbsp;" . $people["wife"]->getLabel());
				//$menu->addClass("", "", "submenu");
				if ($TEXT_DIRECTION=="ltr") {
					$menu->addClass("", "", "submenu flyout2");
				} else {
					$menu->addClass("", "", "submenu flyout2rtl");
				}
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["wife"]->getXref(), 2, 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; } else { $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
				if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; } else { $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php echo $styleadd; ?>" nowrap="nowrap" style="width:75px;">
						<?php echo $menu->getMenu(); ?>
					</td>
					<td align="center" class="<?php echo $this->controller->getPersonStyle($people["wife"]); ?> nam">
						<?php
						echo "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".$people["wife"]->getHtmlUrl()."\" onclick=\"return familyNavLoad('".$people['wife']->getHtmlUrl()."');\">";
						echo PrintReady($people["wife"]->getFullName());
						echo "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						echo "</a>";
						?>
					</td>
				</tr>
				<?php
			}

			if (isset($people["children"])) {
				$elderdate = $family->getMarriageDate();
				foreach ($people["children"] as $key=>$child) {
				if ($pid == $child->getXref()) {
				} else {
					$menu = new Menu("&nbsp;" . $child->getLabel());
					//$menu->addClass("", "", "submenu");
					if ($TEXT_DIRECTION=="ltr") {
						$menu->addClass("", "", "submenu flyout2");
					} else {
						$menu->addClass("", "", "submenu flyout2rtl");
					}
					$slabel  = "</a>".$this->print_pedigree_person_nav($child->getXref(), 2, 0, $personcount++);
					$slabel .= $spouselinks."<a>";
					$submenu = new Menu($slabel);
					$menu->addSubMenu($submenu);
				}
				if (PrintReady($child->getDeathYear()) == 0) { $DeathYr = ""; } else { $DeathYr = PrintReady($child->getDeathYear()); }
				if (PrintReady($child->getBirthYear()) == 0) { $BirthYr = ""; } else { $BirthYr = PrintReady($child->getBirthYear()); }

					?>
					<tr>
						<td class="facts_label<?php echo $styleadd; ?>" nowrap="nowrap" style="width:75px;">
						<?php
						if ($pid == $child->getXref() ) {
							echo $child->getLabel();
						} else {
							echo $menu->getMenu();
						}
						?>
						</td>
						<td align="center" class="<?php echo $this->controller->getPersonStyle($child); ?> nam">
							<?php
							if ($pid == $child->getXref()) {
								echo "<span style=\"font: 12px tahoma, arial, helvetica, sans-serif;\">".PrintReady($child->getFullName())."</span>";
								echo "<br /><span style=\"font:9px tahoma, arial, helvetica, sans-serif;\">" . $BirthYr . " - " . $DeathYr . "</span>";
							} else {
								echo "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".$child->getHtmlUrl()."\" onclick=\"return familyNavLoad('".$child->getHtmlUrl()."');\">";
								echo PrintReady($child->getFullName());
								echo "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
								echo "</a>";
							}
							?>
						</td>
					</tr>
					<?php
					$elderdate = $child->getBirthDate();
				}
			}
		}

		//-- step families ----------------------------------------------------------------
		foreach ($this->controller->indi->getStepFamilies() as $famid=>$family) {
			$label = $this->controller->indi->getStepFamilyLabel($family);
			$people = $this->controller->buildFamilyList($family, "step");
			if ($people) {
				echo "<tr><td><br /></td><td></td></tr>";
			}
			$styleadd = "";
			$elderdate = "";
			?>
			<tr>
				<td style="padding-bottom: 4px;" align="center" colspan="2">
				<?php
				echo "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\"href=\"family.php?famid=".$famid."\" onclick=\"return familyNavLoad('family.php?famid=".$famid."');\">";
				echo "<b>".$label."</b>";
				echo "</a>";
				?>
				</td>
			</tr>
			<?php

			//if (isset($people["husb"]) && $people["husb"]->getLabel() == ".") {
			if (isset($people["husb"]) ) {
				$menu = new Menu();
				if ($people["husb"]->getLabel() == ".") {
					$menu->addLabel(i18n::translate('Step-Father'));
				} else {
					$menu->addLabel($people["husb"]->getLabel());
				}
				//$menu->addClass("", "", "submenu");
				if ($TEXT_DIRECTION=="ltr") {
					$menu->addClass("", "", "submenu flyout2");
				} else {
					$menu->addClass("", "", "submenu flyout2rtl");
				}
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["husb"]->getXref(), 2, 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; } else { $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
				if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; } else { $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
				?>

				<tr>
					<td class="facts_label<?php echo $styleadd; ?>"  nowrap="nowrap" style="width:75px;">
						<?php echo $menu->getMenu(); ?>
					</td>
					<td align="center" class="<?php echo $this->controller->getPersonStyle($people["husb"]); ?> nam">
						<?php
						echo "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".$people["husb"]->getHtmlUrl()."\" onclick=\"return familyNavLoad('".$people['husb']->getHtmlUrl()."');\">";
						echo PrintReady($people["husb"]->getFullName());
						echo "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						echo "</a>";
						?>
					</td>
				</tr>
				<?php
				$elderdate = $people["husb"]->getBirthDate();
			}

			$styleadd = "";
			//if (isset($people["wife"]) && $people["wife"]->getLabel() == ".") {
			if (isset($people["wife"]) ) {
				$menu = new Menu();
				if ($people["wife"]->getLabel() == ".") {
					$menu->addLabel(i18n::translate('Step-Mother'));
				} else {
					$menu->addLabel($people["wife"]->getLabel());
				}
				//$menu->addClass("", "", "submenu");
				if ($TEXT_DIRECTION=="ltr") {
					$menu->addClass("", "", "submenu flyout2");
				} else {
					$menu->addClass("", "", "submenu flyout2rtl");
				}
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["wife"]->getXref(), 2, 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; } else { $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
				if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; } else { $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php echo $styleadd; ?>" nowrap="nowrap" style="width:75px;">
						<?php echo $menu->getMenu(); ?>
					</td>
					<td align="center" class="<?php echo $this->controller->getPersonStyle($people["wife"]); ?> nam">
						<?php
						echo "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".$people["wife"]->getHtmlUrl()."\" onclick=\"return familyNavLoad('".$people['wife']->getHtmlUrl()."');\">";
						echo PrintReady($people["wife"]->getFullName());
						echo "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						echo "</a>";
						?>
					</td>
				</tr>
				<?php
			}

			$styleadd = "";
			if (isset($people["children"])) {
				$elderdate = $family->getMarriageDate();
				foreach ($people["children"] as $key=>$child) {
					$menu = new Menu($child->getLabel());
					//$menu->addClass("", "", "submenu");
					if ($TEXT_DIRECTION=="ltr") {
						$menu->addClass("", "", "submenu flyout2");
					} else {
						$menu->addClass("", "", "submenu flyout2rtl");
					}
					$slabel  = "</a>".$this->print_pedigree_person_nav($child->getXref(), 2, 0, $personcount++);
					$slabel .= $spouselinks."<a>";
					$submenu = new Menu($slabel);
					$menu->addSubMenu($submenu);

					if (PrintReady($child->getDeathYear()) == 0) { $DeathYr = ""; } else { $DeathYr = PrintReady($child->getDeathYear()); }
					if (PrintReady($child->getBirthYear()) == 0) { $BirthYr = ""; } else { $BirthYr = PrintReady($child->getBirthYear()); }
					?>
					<tr>
						<td class="facts_label<?php echo $styleadd; ?>" nowrap="nowrap" style="width:75px;">
							<?php echo $menu->getMenu(); ?>
						</td>
						<td align="center" class="<?php echo $this->controller->getPersonStyle($child); ?> nam">
							<?php
							echo "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".$child->getHtmlUrl()."\" onclick=\"return familyNavLoad('".$child->getHtmlUrl()."');\">";
							echo PrintReady($child->getFullName());
							echo "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
							echo "</a>";
							?>
						</td>
					</tr>
					<?php
					//$elderdate = $child->getBirthDate();
				}
			}
		}

		//-- spouse and children --------------------------------------------------
		$families = $this->controller->indi->getSpouseFamilies();
		foreach ($families as $famid=>$family) {
		echo "<tr><td><br /></td><td></td></tr>";
		?>
			<tr>
				<td style="padding-bottom: 4px;" align="center" colspan="2">
				<?php
				echo "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"family.php?famid=".$famid."\" onclick=\"return familyNavLoad('family.php?famid=".$famid."');\">";
				echo "<b>".i18n::translate('Immediate Family')."</b>";
				echo "</a>";
				?>
				</td>
			</tr>
		<?php

			//$personcount = 0;
			$people = $this->controller->buildFamilyList($family, "spouse");
			if ($this->controller->indi->equals($people["husb"])) {
				$spousetag = 'WIFE';
			} else {
				$spousetag = 'HUSB';
			}
			$styleadd = "";
			if (isset($people["husb"]) && $spousetag == 'HUSB') {
				$menu = new Menu("&nbsp;" . $people["husb"]->getLabel());
				//$menu->addClass("", "", "submenu");
				if ($TEXT_DIRECTION=="ltr") {
					$menu->addClass("", "", "submenu flyout2");
				} else {
					$menu->addClass("", "", "submenu flyout2rtl");
				}
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["husb"]->getXref(), 2, 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; } else { $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
				if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; } else { $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php echo $styleadd; ?>" nowrap="nowrap" style="width:75px;">
						<?php echo $menu->getMenu(); ?>
					</td>
					<td align="center" class="<?php echo $this->controller->getPersonStyle($people["husb"]); ?> nam">
						<?php
						if ($pid == $people["husb"]->getXref()) {
							echo PrintReady($people["husb"]->getFullName());
							echo "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						} else {
							echo "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".$people["husb"]->getHtmlUrl()."\" onclick=\"return familyNavLoad('".$people['husb']->getHtmlUrl()."');\">";
							echo PrintReady($people["husb"]->getFullName());
							echo "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
							echo "</a>";
						}
						?>
					</td>
				</tr>
				<?php
			}

			if (isset($people["wife"]) && $spousetag == 'WIFE') {
				$menu = new Menu("&nbsp;" . $people["wife"]->getLabel());
				//$menu->addClass("", "", "submenu");
				if ($TEXT_DIRECTION=="ltr") {
					$menu->addClass("", "", "submenu flyout2");
				} else {
					$menu->addClass("", "", "submenu flyout2rtl");
				}
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["wife"]->getXref(), 2, 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; } else { $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
				if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; } else { $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php echo $styleadd; ?>" nowrap="nowrap" style="width:75px;">
						<?php echo $menu->getMenu(); ?>
					</td>
					<td align="center" class="<?php echo $this->controller->getPersonStyle($people["wife"]); ?> nam">
						<?php
						if ($pid == $people["wife"]->getXref()) {
							echo PrintReady($people["wife"]->getFullName());
							echo "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						} else {
							echo "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".$people["wife"]->getHtmlUrl()."\" onclick=\"return familyNavLoad('".$people['wife']->getHtmlUrl()."');\">";
							echo PrintReady($people["wife"]->getFullName());
							echo "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
							echo "</a>";
						}
						?>
					</td>
				</tr>
				<?php
			}

			$styleadd = "";
			if (isset($people["children"])) {
				foreach ($people["children"] as $key=>$child) {
					$menu = new Menu("&nbsp;" . $child->getLabel());
					//$menu->addClass("", "", "submenu");
					if ($TEXT_DIRECTION=="ltr") {
						$menu->addClass("", "", "submenu flyout2");
					} else {
						$menu->addClass("", "", "submenu flyout2rtl");
					}
					$slabel = "</a>".$this->print_pedigree_person_nav($child->getXref(), 2, 0, $personcount++);
					$slabel .= $spouselinks."<a>";
					$submenu = new Menu($slabel);
					$menu->addSubmenu($submenu);

					if (PrintReady($child->getDeathYear()) == 0) { $DeathYr = ""; } else { $DeathYr = PrintReady($child->getDeathYear()); }
					if (PrintReady($child->getBirthYear()) == 0) { $BirthYr = ""; } else { $BirthYr = PrintReady($child->getBirthYear()); }
					?>
					<tr>
						<td class="facts_label<?php echo $styleadd; ?>" nowrap="nowrap" style="width:75px;">
							<?php echo $menu->getMenu(); ?>
						</td>
						<td align="center" class="<?php echo $this->controller->getPersonStyle($child); ?> nam">
							<?php
							echo "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".$child->getHtmlUrl()."\" onclick=\"return familyNavLoad('".$child->getHtmlUrl()."');\">";
							echo PrintReady($child->getFullName());
							echo "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
							echo "</a>";
							?>
						</td>
					</tr>
					<?php
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
	global $HIDE_LIVE_PEOPLE, $SHOW_LIVING_NAMES, $ZOOM_BOXES, $LINK_ICONS, $SCRIPT_NAME, $GEDCOM;
	global $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES, $bwidth, $bheight, $PEDIGREE_FULL_DETAILS, $SHOW_PEDIGREE_PLACES;
	global $TEXT_DIRECTION, $DEFAULT_PEDIGREE_GENERATIONS, $OLD_PGENS, $talloffset, $PEDIGREE_LAYOUT, $MEDIA_DIRECTORY;
	global $WT_IMAGES, $ABBREVIATE_CHART_LABELS, $USE_MEDIA_VIEWER;
	global $chart_style, $box_width, $generations, $show_spouse, $show_full;
	global $CHART_BOX_TAGS, $SHOW_LDS_AT_GLANCE, $PEDIGREE_SHOW_GENDER;
	global $SEARCH_SPIDER;

	global $spouselinks, $parentlinks, $step_parentlinks, $persons, $person_step, $person_parent, $tabno, $theme_name, $spousetag;
	global $natdad, $natmom;

	if ($style != 2) $style=1;
	if (empty($show_full)) $show_full = 0;
	if (empty($PEDIGREE_FULL_DETAILS)) $PEDIGREE_FULL_DETAILS = 0;

	if (!isset($OLD_PGENS)) $OLD_PGENS = $DEFAULT_PEDIGREE_GENERATIONS;
	if (!isset($talloffset)) $talloffset = $PEDIGREE_LAYOUT;

	$person=Person::getInstance($pid);
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

	if ($person->canDisplayName()) {
		if (empty($SEARCH_SPIDER)) {
			if ($LINK_ICONS!="disabled") {
				//-- draw a box for the family flyout
				$parentlinks .= "<span class=\"flyout4\"><b>".i18n::translate('Parents')."</b></span><br />";
				$step_parentlinks .= "<span class=\"flyout4\"><b>".i18n::translate('Parents')."</b></span><br />";
				$spouselinks .= "<span class=\"flyout4\"><b>".i18n::translate('Family')."</b></span><br />";

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
							if ($TEXT_DIRECTION=="ltr") {
								$title = i18n::translate('Family book chart').": ".$famid;
							} else {
								$title = $famid." :".i18n::translate('Family book chart');
							}
							if ($husb) {
								$person_parent="Yes";
								if ($TEXT_DIRECTION=="ltr") {
									$title = i18n::translate('Individual information').": ".$husb->getXref();
								} else {
									$title = $husb->getXref()." :".i18n::translate('Individual information');
								}
								$parentlinks .= "<a id=\"phusb\" href=\"".$husb->getHtmlUrl()."\" onclick=\"return familyNavLoad('".$husb->getHtmlUrl()."');\">";
								$parentlinks .= "&nbsp;".PrintReady($husb->getFullName());
								$parentlinks .= "</a>";
								$parentlinks .= "<br />";
								$natdad = "yes";
							}
						}

						// Wife ------------------------------
						if ($wife || $num>0) {
							if ($TEXT_DIRECTION=="ltr") {
								$title = i18n::translate('Family book chart').": ".$famid;
							} else {
								$title = $famid." :".i18n::translate('Family book chart');
							}
							if ($wife) {
								$person_parent="Yes";
								if ($TEXT_DIRECTION=="ltr") {
									$title = i18n::translate('Individual information').": ".$wife->getXref();
								} else {
									$title = $wife->getXref()." :".i18n::translate('Individual information');
								}
								$parentlinks .= "<a id=\"pwife\" href=\"".$wife->getHtmlUrl()."\" onclick=\"return familyNavLoad('".$wife->getHtmlUrl()."');\">";
								$parentlinks .= "&nbsp;".PrintReady($wife->getFullName());
								$parentlinks .= "</a>";
								$parentlinks .= "<br />";
								$natmom = "yes";
							}
						}
					}
				}

				//-- step families -----------------------------------------
				$fams = $person->getStepFamilies();
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
								if ($TEXT_DIRECTION=="ltr") {
									$title = i18n::translate('Family book chart').": ".$famid;
								} else {
									$title = $famid." :".i18n::translate('Family book chart');
								}
								if ($husb) {
									$person_step="Yes";
									if ($TEXT_DIRECTION=="ltr") {
										$title = i18n::translate('Individual information').": ".$husb->getXref();
									} else {
										$title = $husb->getXref()." :".i18n::translate('Individual information');
									}
									$parentlinks .= "<a id=\"shusb\" href=\"".$husb->getHtmlUrl()."\" onclick=\"return familyNavLoad('".$husb->getHtmlUrl()."');\">";
									$parentlinks .= "&nbsp;".PrintReady($husb->getFullName());
									$parentlinks .= "</a>";
									$parentlinks .= "<br />";
								}
							}
						}

						if ($natmom == "yes") {
						} else {
							// Wife ----------------------------
							if ($wife || $num>0) {
								if ($TEXT_DIRECTION=="ltr") {
									$title = i18n::translate('Family book chart').": ".$famid;
								} else {
									$title = $famid." :".i18n::translate('Family book chart');
								}
								if ($wife) {
									$person_step="Yes";
									if ($TEXT_DIRECTION=="ltr") {
										$title = i18n::translate('Individual information').": ".$wife->getXref();
									} else {
										$title = $wife->getXref()." :".i18n::translate('Individual information');
									}
									$parentlinks .= "<a id=\"swife\" href=\"".$wife->getHtmlUrl()."\" onclick=\"return familyNavLoad('".$wife->getHtmlUrl()."');\">";
									$parentlinks .= "&nbsp;".PrintReady($wife->getFullName());
									$parentlinks .= "</a>";
									$parentlinks .= "<br />";
								}
							}
						}
					}
				}

				// Spouse Families -------------------------------------- @var $family Family
				$fams = $person->getSpouseFamilies();
				foreach ($fams as $famid=>$family) {
					if (!is_null($family)) {
						$spouse = $family->getSpouse($person);
						$children = $family->getChildren();
						$num = count($children);

						// Spouse ------------------------------
						if ($spouse || $num>0) {
							if ($TEXT_DIRECTION=="ltr") {
								$title = i18n::translate('Family book chart').": ".$famid;
							} else {
								$title = $famid." :".i18n::translate('Family book chart');
							}
							if ($spouse) {
								if ($TEXT_DIRECTION=="ltr") {
									$title = i18n::translate('Individual information').": ".$spouse->getXref();
								} else {
									$title = $spouse->getXref()." :".i18n::translate('Individual information');
								}
								$spouselinks .= "<a id=\"spouse\" href=\"".$spouse->getHtmlUrl()."\" onclick=\"return familyNavLoad('".$spouse->getHtmlUrl()."');\">";
								$spouselinks .= "&nbsp;".PrintReady($spouse->getFullName());
								$spouselinks .= "</a>";
								$spouselinks .= "<br />";
								if ($spouse->getFullName() != "") {
									$persons = "Yes";
								}
							}
						}

						// Children ------------------------------   @var $child Person
						$hasChildren = 'No';
						foreach ($children as $c=>$child) {
							if ($child) {
								if ($hasChildren == 'No') {
									$hasChildren = 'Yes';
									$spouselinks .= "<ul class=\"clist ".$TEXT_DIRECTION."\">";
								}
								$persons="Yes";
								$title = i18n::translate('Individual information').": ".$child->getXref();
								$spouselinks .= "<li id=\"flyout3\">";
								$spouselinks .= "<a href=\"".$child->getHtmlUrl()."\" onclick=\"return familyNavLoad('".$child->getHtmlUrl()."');\">";
								$spouselinks .= PrintReady($child->getFullName());
								$spouselinks .= "</a>";
							}
						}
						if ($hasChildren == 'Yes') {
							$spouselinks .= "</ul>";
						} else {
							$spouselinks .= "<img src=\"images/small/childless.gif\" alt=\"".i18n::translate('This family remained childless')."\" height=\"15\" align=\"middle\"/> ".i18n::translate('This family remained childless')."<br />";
						}
					}
				}

				if ($persons != "Yes") {
					$spouselinks  .= "&nbsp;(".i18n::translate('none').")";
				}
				if ($person_parent != "Yes") {
					$parentlinks .= "&nbsp;(".i18n::translate_c('unknown family', 'unknown').")";
				}
				if ($person_step != "Yes") {
					$step_parentlinks .= "&nbsp;(".i18n::translate_c('unknown family', 'unknown').")";
				}
			}
		}
	}
}
}

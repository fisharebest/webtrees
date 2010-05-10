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
 * @version $Id: class_media.php 5451 2009-05-05 22:15:34Z fisharebest $
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
		global $WT_IMAGE_DIR, $WT_IMAGES;

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

	public function getLinkUrl(&$person) {
		
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

		global $edit, $tabno, $mediacnt, $GEDCOM, $pid;
		$edit=$edit;
		global $show_full, $tabno;
		$show_full="1";

		// =====================================================================

		echo WT_JS_START;
		echo 'function familyNavLoad(url) {
			window.location = url+"&tab="+jQuery("#tabs li:eq("+jQuery("#tabs").tabs("option", "selected")+") a").attr("title");
			return false;
		}
		';
		echo WT_JS_END;
		
	 // Start Family Nav Table ----------------------------
		echo "<table class=\"nav_content\" cellpadding=\"0\">"; 
		global $SHOW_ID_NUMBERS, $WT_IMAGE_DIR, $WT_IMAGES, $WT_MENUS_AS_LISTS;
		global $spouselinks, $parentlinks, $DeathYr, $BirthYr;
		global $TEXT_DIRECTION;

		$personcount=0;
		$families = $this->controller->indi->getChildFamilies();

		//-- parent families -------------------------------------------------------------
		foreach($families as $famid=>$family) {
			$label = $this->controller->indi->getChildFamilyLabel($family);
			$people = $this->controller->buildFamilyList($family, "parents");
			$styleadd = "";
			?>
			<tr>
				<td style="padding-bottom:4px;" align="center" colspan="2">
				<?php
				echo "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"family.php?famid=".$famid."\" onclick=\"return familyNavLoad('family.php?famid=".$famid."');\">";
				echo "<b>".$label."&nbsp;&nbsp;</b><span class=\"age\">(".$famid.")</span>";
				echo "</a>";
				?>
				</td>
			</tr>
			<?php
			if (isset($people["husb"])) {
				$menu = new Menu("&nbsp;" . $people["husb"]->getLabel() . "&nbsp;". "\n");
				// $menu->addClass("", "", "submenu");
				if ($TEXT_DIRECTION=="ltr") { 
					$menu->addClass("", "", "submenu flyout2");
				}else{
					$menu->addClass("", "", "submenu flyout2rtl");
				}
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["husb"]->getXref(), 2, !$this->controller->isPrintPreview(), 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
				if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap" style="width:75px;">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
							$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
					</td>
					<td align="center" class="<?php print $this->controller->getPersonStyle($people["husb"]);?> nam">
						<?php
						print "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".encode_url($people["husb"]->getLinkUrl())."\" onclick=\"return familyNavLoad('".encode_url($people['husb']->getLinkUrl())."');\">";
						print PrintReady($people["husb"]->getFullName());
						print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						print "</a>";
						?>
					</td>
				</tr>
				<?php
			}

			if (isset($people["wife"])) {
				$menu = new Menu("&nbsp;" . $people["wife"]->getLabel() . "&nbsp;". "\n");
				//$menu->addClass("", "", "submenu");
				if ($TEXT_DIRECTION=="ltr") { 
					$menu->addClass("", "", "submenu flyout2");
				}else{
					$menu->addClass("", "", "submenu flyout2rtl");
				}
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["wife"]->getXref(), 2, !$this->controller->isPrintPreview(), 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
				if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap" style="width:75px;">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
							$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
					</td>
					<td align="center" class="<?php print $this->controller->getPersonStyle($people["wife"]); ?> nam">
						<?php
						print "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".encode_url($people["wife"]->getLinkUrl())."\" onclick=\"return familyNavLoad('".encode_url($people['wife']->getLinkUrl())."');\">";
						print PrintReady($people["wife"]->getFullName());
						print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						print "</a>";
						?>
					</td>
				</tr>
				<?php
			}

			if (isset($people["children"])) {
				$elderdate = $family->getMarriageDate();
				foreach($people["children"] as $key=>$child) {
				if ($pid == $child->getXref() ){
				}else{
					$menu = new Menu("&nbsp;" . $child->getLabel() . "\n");
				//	$menu->addClass("", "", "submenu");
					if ($TEXT_DIRECTION=="ltr") { 
						$menu->addClass("", "", "submenu flyout2");
					}else{
						$menu->addClass("", "", "submenu flyout2rtl");
					}
					$slabel  = "</a>".$this->print_pedigree_person_nav($child->getXref(), 2, !$this->controller->isPrintPreview(), 0, $personcount++);
					$slabel .= $spouselinks."<a>";
					$submenu = new Menu($slabel);
					$menu->addSubMenu($submenu);
				}
				if (PrintReady($child->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($child->getDeathYear()); }
				if (PrintReady($child->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($child->getBirthYear()); }

					?>
					<tr>
						<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap" style="width:75px;">
						<?php
						if ($pid == $child->getXref() ) {
							print $child->getLabel();
						}else{
							if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
								$menu->printMenu();
							if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						}
						?>
						</td>
						<td align="center" class="<?php print $this->controller->getPersonStyle($child); ?> nam">
							<?php
							if ($pid == $child->getXref()) {
								print "<span style=\"font: 12px tahoma, arial, helvetica, sans-serif;\">".PrintReady($child->getFullName())."</span>";
								print "<br /><span style=\"font:9px tahoma, arial, helvetica, sans-serif;\">" . $BirthYr . " - " . $DeathYr . "</span>";
							}else{
								print "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".encode_url($child->getLinkUrl())."\" onclick=\"return familyNavLoad('".encode_url($child->getLinkUrl())."');\">";
								print PrintReady($child->getFullName());
								print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
								print "</a>";
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
		foreach($this->controller->indi->getStepFamilies() as $famid=>$family) {
			$label = $this->controller->indi->getStepFamilyLabel($family);
			$people = $this->controller->buildFamilyList($family, "step");
			if ($people){
				echo "<tr><td><br /></td><td></td></tr>";
			}
			$styleadd = "";
			$elderdate = "";
			?>
			<tr>
				<td style="padding-bottom: 4px;" align="center" colspan="2">
				<?php				 
				echo "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\"href=\"family.php?famid=".$famid."\" onclick=\"return familyNavLoad('family.php?famid=".$famid."');\">";
				echo "<b>".$label."&nbsp;&nbsp;</b><span class=\"age\">(".$famid.")</span>";
				echo "</a>";
				?>
				</td>
			</tr>
			<?php

			//if (isset($people["husb"]) && $people["husb"]->getLabel() == ".") {
			if (isset($people["husb"]) ) {
				$menu = new Menu();
				if ($people["husb"]->getLabel() == ".") {
					$menu->addLabel(i18n::translate('Step-Father')."\n");
				}else{
					$menu->addLabel($people["husb"]->getLabel()."\n");
				}
				//$menu->addClass("", "", "submenu");
				if ($TEXT_DIRECTION=="ltr") { 
					$menu->addClass("", "", "submenu flyout2");
				}else{
					$menu->addClass("", "", "submenu flyout2rtl");
				}
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["husb"]->getXref(), 2, !$this->controller->isPrintPreview(), 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
				if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
				?>

				<tr>
					<td class="facts_label<?php print $styleadd; ?>"  nowrap="nowrap" style="width:75px;">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
						$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
					</td>
					<td align="center" class="<?php print $this->controller->getPersonStyle($people["husb"]); ?> nam">
						<?php
						print "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".encode_url($people["husb"]->getLinkUrl())."\" onclick=\"return familyNavLoad('".encode_url($people['husb']->getLinkUrl())."');\">";
						print PrintReady($people["husb"]->getFullName());
						print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						print "</a>";
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
					$menu->addLabel(i18n::translate('Step-Mother')."\n");
				}else{
					$menu->addLabel($people["wife"]->getLabel()."\n");
				}
				//$menu->addClass("", "", "submenu");
				if ($TEXT_DIRECTION=="ltr") { 
					$menu->addClass("", "", "submenu flyout2");
				}else{
					$menu->addClass("", "", "submenu flyout2rtl");
				}
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["wife"]->getXref(), 2, !$this->controller->isPrintPreview(), 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
				if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap" style="width:75px;">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
							$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
					</td>
					<td align="center" class="<?php print $this->controller->getPersonStyle($people["wife"]); ?> nam">
						<?php
						print "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".encode_url($people["wife"]->getLinkUrl())."\" onclick=\"return familyNavLoad('".encode_url($people['wife']->getLinkUrl())."');\">";
						print PrintReady($people["wife"]->getFullName());
						print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						print "</a>";
						?>
					</td>
				</tr>
				<?php
			}

			$styleadd = "";
			if (isset($people["children"])) {
				$elderdate = $family->getMarriageDate();
				foreach($people["children"] as $key=>$child) {
					$menu = new Menu($child->getLabel()."\n");
					//$menu->addClass("", "", "submenu");
					if ($TEXT_DIRECTION=="ltr") { 
						$menu->addClass("", "", "submenu flyout2");
					}else{
						$menu->addClass("", "", "submenu flyout2rtl");
					}
					$slabel  = "</a>".$this->print_pedigree_person_nav($child->getXref(), 2, !$this->controller->isPrintPreview(), 0, $personcount++);
					$slabel .= $spouselinks."<a>";
					$submenu = new Menu($slabel);
					$menu->addSubMenu($submenu);

					if (PrintReady($child->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($child->getDeathYear()); }
					if (PrintReady($child->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($child->getBirthYear()); }
					?>
					<tr>
						<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap" style="width:75px;">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
							$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
						</td>
						<td align="center" class="<?php print $this->controller->getPersonStyle($child); ?> nam">
							<?php
							print "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".encode_url($child->getLinkUrl())."\" onclick=\"return familyNavLoad('".encode_url($child->getLinkUrl())."');\">";
							print PrintReady($child->getFullName());
							print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
							print "</a>";
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
		foreach($families as $famid=>$family) {
		echo "<tr><td><br /></td><td></td></tr>";
		?>
			<tr>
				<td style="padding-bottom: 4px;" align="center" colspan="2">
				<?php
				echo "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"family.php?famid=".$famid."\" onclick=\"return familyNavLoad('family.php?famid=".$famid."');\">";
				echo "<b>".i18n::translate('Immediate Family')."&nbsp;&nbsp;</b><span class=\"age\">(".$famid.")</span>";
				echo "</a>";
				//echo "<a href=\"family.php?famid=".$famid."\">";
				//echo "<b>".i18n::translate('Immediate Family')."&nbsp;&nbsp;</b><span class=\"age\">(".$famid.")</span>";
				//echo "</a>";
				?>
				</td>
			</tr>
		<?php

			//$personcount = 0;
			$people = $this->controller->buildFamilyList($family, "spouse");
			if ($this->controller->indi->equals($people["husb"])){
				$spousetag = 'WIFE';
			}else{
				$spousetag = 'HUSB';
			}
			$styleadd = "";
			if ( isset($people["husb"]) && $spousetag == 'HUSB' ) {
				$menu = new Menu("&nbsp;" . $people["husb"]->getLabel() . "&nbsp;". "\n");
				//$menu->addClass("", "", "submenu");
				if ($TEXT_DIRECTION=="ltr") { 
					$menu->addClass("", "", "submenu flyout2");
				}else{
					$menu->addClass("", "", "submenu flyout2rtl");
				}
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["husb"]->getXref(), 2, !$this->controller->isPrintPreview(), 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
				if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap" style="width:75px;">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
						$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
					</td>
					<td align="center" class="<?php print $this->controller->getPersonStyle($people["husb"]); ?> nam">
						<?php
						if ($pid == $people["husb"]->getXref()) {
							print PrintReady($people["husb"]->getFullName());
							print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						}else{
							print "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".encode_url($people["husb"]->getLinkUrl())."\" onclick=\"return familyNavLoad('".encode_url($people['husb']->getLinkUrl())."');\">";
							print PrintReady($people["husb"]->getFullName());
							print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
							print "</a>";
						}
						?>
					</td>
				</tr>
				<?php
			}

			if ( isset($people["wife"]) && $spousetag == 'WIFE') {
				$menu = new Menu("&nbsp;" . $people["wife"]->getLabel() . "&nbsp;". "\n");
				//$menu->addClass("", "", "submenu");
				if ($TEXT_DIRECTION=="ltr") { 
					$menu->addClass("", "", "submenu flyout2");
				}else{
					$menu->addClass("", "", "submenu flyout2rtl");
				}
				$slabel  = "</a>".$this->print_pedigree_person_nav($people["wife"]->getXref(), 2, !$this->controller->isPrintPreview(), 0, $personcount++);
				$slabel .= $parentlinks."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
				if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap" style="width:75px;">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
						$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
					</td>
					<td align="center" class="<?php print $this->controller->getPersonStyle($people["wife"]); ?> nam">
						<?php
						if ($pid == $people["wife"]->getXref()) {
							print PrintReady($people["wife"]->getFullName());
							print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						}else{
							print "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".encode_url($people["wife"]->getLinkUrl())."\" onclick=\"return familyNavLoad('".encode_url($people['wife']->getLinkUrl())."');\">";
							print PrintReady($people["wife"]->getFullName());
							print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
							print "</a>";
						}
						?>
					</td>
				</tr>
				<?php
			}

			$styleadd = "";
			if (isset($people["children"])) {
				foreach($people["children"] as $key=>$child) {
					$menu = new Menu("&nbsp;" . $child->getLabel() . "&nbsp;". "\n");
					//$menu->addClass("", "", "submenu");
					if ($TEXT_DIRECTION=="ltr") { 
						$menu->addClass("", "", "submenu flyout2");
					}else{
						$menu->addClass("", "", "submenu flyout2rtl");
					}
					$slabel = "</a>".$this->print_pedigree_person_nav($child->getXref(), 2, !$this->controller->isPrintPreview(), 0, $personcount++);
					$slabel .= $spouselinks."<a>";
					$submenu = new Menu($slabel);
					$menu->addSubmenu($submenu);

					if (PrintReady($child->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($child->getDeathYear()); }
					if (PrintReady($child->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($child->getBirthYear()); }
					?>
					<tr>
						<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap" style="width:75px;">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
						$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
						</td>
						<td align="center" class="<?php print $this->controller->getPersonStyle($child); ?> nam">
							<?php
							print "<a style=\"font:12px tahoma, arial, helvetica, sans-serif; padding:0px; width:100%;\" href=\"".encode_url($child->getLinkUrl())."\" onclick=\"return familyNavLoad('".encode_url($child->getLinkUrl())."');\">";
							print PrintReady($child->getFullName());
							print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
							print "</a>";
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

function print_pedigree_person_nav($pid, $style=1, $show_famlink=true, $count=0, $personcount="1") {
	global $HIDE_LIVE_PEOPLE, $SHOW_LIVING_NAMES, $ZOOM_BOXES, $LINK_ICONS, $SCRIPT_NAME, $GEDCOM;
	global $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES, $bwidth, $bheight, $PEDIGREE_FULL_DETAILS, $SHOW_ID_NUMBERS, $SHOW_PEDIGREE_PLACES;
	global $TEXT_DIRECTION, $DEFAULT_PEDIGREE_GENERATIONS, $OLD_PGENS, $talloffset, $PEDIGREE_LAYOUT, $MEDIA_DIRECTORY;
	global $WT_IMAGE_DIR, $WT_IMAGES, $ABBREVIATE_CHART_LABELS, $USE_MEDIA_VIEWER;
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
		if ($show_famlink && (empty($SEARCH_SPIDER))) {
			if ($LINK_ICONS!="disabled") {
				//-- draw a box for the family flyout
				$parentlinks 		.= "<span class=\"flyout4\"><b>".i18n::translate('Parents')."</b></span><br />";
				$step_parentlinks 	.= "<span class=\"flyout4\"><b>".i18n::translate('Parents')."</b></span><br />";
				$spouselinks 		.= "<span class=\"flyout4\"><b>".i18n::translate('Family')."</b></span><br />";
				
				$persons 			 = "";
				$person_parent 		 = "";
				$person_step 		 = "";



				//-- parent families --------------------------------------
				$fams = $person->getChildFamilies();
				foreach($fams as $famid=>$family) {

					if (!is_null($family)) {
						$husb = $family->getHusband($person);
						$wife = $family->getWife($person);
						// $spouse = $family->getSpouse($person);
						$children = $family->getChildren();
						$num = count($children);

						// Husband ------------------------------
						if ($husb || $num>0) {
							if ($TEXT_DIRECTION=="ltr") {
								$title = i18n::translate('Family Book Chart').": ".$famid;
							}else{
								$title = $famid." :".i18n::translate('Family Book Chart');
							}
							if ($husb) {
								$person_parent="Yes";
								if ($TEXT_DIRECTION=="ltr") {
									$title = i18n::translate('Individual Information').": ".$husb->getXref();
								}else{
									$title = $husb->getXref()." :".i18n::translate('Individual Information');
								}
								$parentlinks .= "<a id=\"phusb\" href=\"".encode_url($husb->getLinkUrl())."\" onclick=\"return familyNavLoad('".encode_url($husb->getLinkUrl())."');\">";
								$parentlinks .= "&nbsp;".PrintReady($husb->getFullName());
								$parentlinks .= "</a>";
								$parentlinks .= "<br />";
								$natdad = "yes";
							}
						}

						// Wife ------------------------------
						if ($wife || $num>0) {
							if ($TEXT_DIRECTION=="ltr") {
								$title = i18n::translate('Family Book Chart').": ".$famid;
							}else{
								$title = $famid." :".i18n::translate('Family Book Chart');
							}
							if ($wife) {
								$person_parent="Yes";
								if ($TEXT_DIRECTION=="ltr") {
									$title = i18n::translate('Individual Information').": ".$wife->getXref();
								}else{
									$title = $wife->getXref()." :".i18n::translate('Individual Information');
								}
								$parentlinks .= "<a id=\"pwife\" href=\"".encode_url($wife->getLinkUrl())."\" onclick=\"return familyNavLoad('".encode_url($wife->getLinkUrl())."');\">";
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
				foreach($fams as $famid=>$family) {
					if (!is_null($family)) {
						$husb = $family->getHusband($person);
						$wife = $family->getWife($person);
						// $spouse = $family->getSpouse($person);
						$children = $family->getChildren();
						$num = count($children);

						if ($natdad == "yes") {
						}else{
							// Husband -----------------------
							if ($husb || $num>0) {
								if ($TEXT_DIRECTION=="ltr") {
									$title = i18n::translate('Family Book Chart').": ".$famid;
								}else{
									$title = $famid." :".i18n::translate('Family Book Chart');
								}
								if ($husb) {
									$person_step="Yes";
									if ($TEXT_DIRECTION=="ltr") {
										$title = i18n::translate('Individual Information').": ".$husb->getXref();
									}else{
										$title = $husb->getXref()." :".i18n::translate('Individual Information');
									}
									$parentlinks .= "<a id=\"shusb\" href=\"".encode_url($husb->getLinkUrl())."\" onclick=\"return familyNavLoad('".encode_url($husb->getLinkUrl())."');\">";
									$parentlinks .= "&nbsp;".PrintReady($husb->getFullName());
									$parentlinks .= "</a>";
									$parentlinks .= "<br />";
								}
							}
						}

						if ($natmom == "yes") {
						}else{
							// Wife ----------------------------
							if ($wife || $num>0) {
								if ($TEXT_DIRECTION=="ltr") {
									$title = i18n::translate('Family Book Chart').": ".$famid;
								}else{
									$title = $famid." :".i18n::translate('Family Book Chart');
								}
								if ($wife) {
									$person_step="Yes";
									if ($TEXT_DIRECTION=="ltr") {
										$title = i18n::translate('Individual Information').": ".$wife->getXref();
									}else{
										$title = $wife->getXref()." :".i18n::translate('Individual Information');
									}
									$parentlinks .= "<a id=\"swife\" href=\"".encode_url($wife->getLinkUrl())."\" onclick=\"return familyNavLoad('".encode_url($wife->getLinkUrl())."');\">";
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
				foreach($fams as $famid=>$family) {
					if (!is_null($family)) {
						$spouse = $family->getSpouse($person);
						$children = $family->getChildren();
						$num = count($children);

						// Spouse ------------------------------
						if ($spouse || $num>0) {
							if ($TEXT_DIRECTION=="ltr") {
								$title = i18n::translate('Family Book Chart').": ".$famid;
							}else{
								$title = $famid." :".i18n::translate('Family Book Chart');
							}
							if ($spouse) {
								if ($TEXT_DIRECTION=="ltr") {
									$title = i18n::translate('Individual Information').": ".$spouse->getXref();
								}else{
									$title = $spouse->getXref()." :".i18n::translate('Individual Information');
								}
								$spouselinks .= "<a id=\"spouse\" href=\"".encode_url($spouse->getLinkUrl())."\" onclick=\"return familyNavLoad('".encode_url($spouse->getLinkUrl())."');\">";
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
						foreach($children as $c=>$child) {
							if ($child) {
								if ($hasChildren == 'No') {
									$hasChildren = 'Yes';
									$spouselinks .= "\n<ul class=\"clist ".$TEXT_DIRECTION."\">";
								}
								$persons="Yes";
								$title = i18n::translate('Individual Information').": ".$child->getXref();
								$spouselinks .= "\n<li id=\"flyout3\">";
								$spouselinks .= "<a href=\"".encode_url($child->getLinkUrl())."\" onclick=\"return familyNavLoad('".encode_url($child->getLinkUrl())."');\">";
								$spouselinks .= PrintReady($child->getFullName());
								$spouselinks .= "</a>";
							}
						}
						if ($hasChildren == 'Yes') {
							$spouselinks .= "\n</ul>";
						} else {
							$spouselinks .= '<br />';
						}
					}
				}
				
				if ($persons != "Yes") {
					$spouselinks  .= "&nbsp;(".i18n::translate('None').")\n\t\t";
				}
				if ($person_parent != "Yes") {
					$parentlinks .= "&nbsp;(".i18n::translate('unknown').")\n\t\t";
				}
				if ($person_step != "Yes") {
					$step_parentlinks .= "&nbsp;(".i18n::translate('unknown').")\n\t\t";
				}
			}
		}
	}
}

}

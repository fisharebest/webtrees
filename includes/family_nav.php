<?php
/**
* Family Navigator for webtrees
*
* Display immediate family members table for fast navigation
* ( Currently used with Facts and Details tab, and Album Tab pages )
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2007 to 2008  PGV Development Team.  All rights reserved.
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
* @subpackage Includes
* @version $Id$
* @author Brian Holland
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_FAMILY_NAV_PHP', '');

// -----------------------------------------------------------------------------
// Function Family Nav for webtrees - called by individual_ctrl.php
// -----------------------------------------------------------------------------
// function family_nav() {
// ------------------------------------------------------------------------------

global $edit, $tabno, $mediacnt, $GEDCOM, $pid;
$edit=$edit;
global $show_full, $tabno;
$show_full="1";

// Gets current clicked tab to set $tabno -----------
if (isset($_COOKIE['lastclick'])) {
	$tabno=$_COOKIE['lastclick']-1;
}else{
	$tabno=0;
}


// Debug only -----------------------------------------
// echo "Lastclick =" . $_COOKIE['lastclick'];
//echo "<br />";
//print "TAB =" . $tabno;

// =====================================================================

//     Start Family Nav Table ----------------------------
	echo "<table class=\"facts_table\" width='230' cellpadding=\"0\">";
		global $SHOW_ID_NUMBERS, $WT_IMAGE_DIR, $WT_IMAGES, $WT_MENUS_AS_LISTS;
		global $spouselinks, $parentlinks, $DeathYr, $BirthYr;
		global $TEXT_DIRECTION;

		$personcount=0;
		$families = $this->indi->getChildFamilies();

		//-- parent families -------------------------------------------------------------
		foreach($families as $famid=>$family) {
			$label = $this->indi->getChildFamilyLabel($family);
			$people = $this->buildFamilyList($family, "parents");
			$styleadd = "";
			?>
			<tr>
				<td style="padding-bottom: 4px;" align="center" colspan="2">
				<?php
				echo '<a href="', encode_url($family->getLinkUrl()), '">';
				//echo "<b>", i18n::translate('Parents Family') , "&nbsp;&nbsp;(", $famid, ")</b>";
				echo "<b>", i18n::translate('Parents Family'), "&nbsp;&nbsp;</b><span class=\"age\">(", $famid, ")</span>";
				echo "</a>";
				?>
				</td>
			</tr>
			<?php
			if (isset($people["husb"])) {
				$menu = new Menu("&nbsp;" . $people["husb"]->getLabel() . "&nbsp;". "\n");
				if ($TEXT_DIRECTION=="ltr") {
					$menu->addClass("", "", "submenu flyout");
				}else{
					$menu->addClass("", "", "submenu flyoutrtl");
				}
				$slabel  = "</a>".print_pedigree_person_nav($people["husb"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
				$slabel .= PrintReady($parentlinks)."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
				if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
						$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
					</td>
					<td align="center" class="<?php print $this->getPersonStyle($people["husb"]); ?>">
						<?php
						print "<a href=\"".encode_url($people["husb"]->getLinkUrl()."&amp;tab={$tabno}")."\">";
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
				if ($TEXT_DIRECTION=="ltr") {
					$menu->addClass("", "", "submenu flyout");
				}else{
					$menu->addClass("", "", "submenu flyoutrtl");
				}
				$slabel  = "</a>".print_pedigree_person_nav($people["wife"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
				$slabel .= PrintReady($parentlinks)."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
				if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php print $styleadd; ?>">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
						$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
					</td>
					<td align="center" class="<?php print $this->getPersonStyle($people["wife"]); ?>">
						<?php
						print "<a href=\"".encode_url($people["wife"]->getLinkUrl()."&amp;tab={$tabno}")."\">";
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
					$menu = new Menu($child->getLabel() . "\n");
					if ($TEXT_DIRECTION=="ltr") {
						$menu->addClass("", "", "submenu flyout");
					}else{
						$menu->addClass("", "", "submenu flyoutrtl");
					}
					$slabel  = "</a>".print_pedigree_person_nav($child->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
					$slabel .= PrintReady($spouselinks)."<a>";
					$submenu = new Menu($slabel);
					$menu->addSubMenu($submenu);
				}
				if (PrintReady($child->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($child->getDeathYear()); }
				if (PrintReady($child->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($child->getBirthYear()); }

					?>
					<tr>
						<td class="facts_label<?php print $styleadd; ?>">
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
						<td align="center" class="<?php print $this->getPersonStyle($child); ?>">
							<?php
							if ($pid == $child->getXref()) {
								print PrintReady($child->getFullName());
								print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
							}else{
								print "<a href=\"".encode_url($child->getLinkUrl()."&amp;tab={$tabno}")."\">";
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
		foreach($this->indi->getStepFamilies() as $famid=>$family) {
			$label = $this->indi->getStepFamilyLabel($family);
			$people = $this->buildFamilyList($family, "step");
			if ($people){
				echo "<tr><td><br /></td><td></td></tr>";
			}
			$styleadd = "";
			$elderdate = "";
			?>
			<tr>
				<td style="padding-bottom: 4px;" align="center" colspan="2">
				<?php
				echo '<a href="', encode_url($family->getLinkUrl()), '">';
				echo "<b>", i18n::translate('Step-Parent Family'), "&nbsp;&nbsp;</b><span class=\"age\">(", $famid, ")</span>";
				echo "</a>";
				?>
				</td>
			</tr>
			<?php

			//if (isset($people["husb"]) && $people["husb"]->getLabel() == ".") {
			if (isset($people["husb"]) ) {
				$menu = new Menu();
				if ($people["husb"]->getLabel() == ".") {
					$menu->addLabel("&nbsp;" . i18n::translate('Step-Father') . "&nbsp;". "\n");
				}else{
					$menu->addLabel("&nbsp;" . $people["husb"]->getLabel() . "&nbsp;". "\n");
				}
				if ($TEXT_DIRECTION=="ltr") {
					$menu->addClass("", "", "submenu flyout");
				}else{
					$menu->addClass("", "", "submenu flyoutrtl");
				}
				$slabel  = "</a>".print_pedigree_person_nav($people["husb"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
				$slabel .= PrintReady($parentlinks)."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
				if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
				?>

				<tr>
					<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
						$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
					</td>
					<td align="center" class="<?php print $this->getPersonStyle($people["husb"]); ?>" >
						<?php
						print "<a href=\"".encode_url($people["husb"]->getLinkUrl()."&amp;tab={$tabno}")."\">";
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
					$menu->addLabel("&nbsp;" . i18n::translate('Step-Mother') . "&nbsp;". "\n");
				}else{
					$menu->addLabel("&nbsp;" . $people["wife"]->getLabel() . "&nbsp;". "\n");
				}
				if ($TEXT_DIRECTION=="ltr") {
					$menu->addClass("", "", "submenu flyout");
				}else{
					$menu->addClass("", "", "submenu flyoutrtl");
				}
				$slabel  = "</a>".print_pedigree_person_nav($people["wife"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
				$slabel .= PrintReady($parentlinks)."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
				if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
						$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
					</td>
					<td align="center" class="<?php print $this->getPersonStyle($people["wife"]); ?>">
						<?php
						print "<a href=\"".encode_url($people["wife"]->getLinkUrl()."&amp;tab={$tabno}")."\">";
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
					$menu = new Menu($child->getLabel() . "\n");
					if ($TEXT_DIRECTION=="ltr") {
						$menu->addClass("", "", "submenu flyout");
					}else{
						$menu->addClass("", "", "submenu flyoutrtl");
					}
					$slabel  = "</a>".print_pedigree_person_nav($child->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
					$slabel .= PrintReady($spouselinks)."<a>";
					$submenu = new Menu($slabel);
					$menu->addSubMenu($submenu);

					if (PrintReady($child->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($child->getDeathYear()); }
					if (PrintReady($child->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($child->getBirthYear()); }
					?>
					<tr>
						<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
						$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
						</td>
						<td align="center" class="<?php print $this->getPersonStyle($child); ?>">
							<?php
							print "<a href=\"".encode_url($child->getLinkUrl()."&amp;tab={$tabno}")."\">";
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
		$families = $this->indi->getSpouseFamilies();
		foreach($families as $famid=>$family) {
		echo "<tr><td><br /></td><td></td></tr>";
		?>
			<tr>
				<td style="padding-bottom: 4px;" align="center" colspan="2">
				<?php
				echo '<a href="', encode_url($family->getLinkUrl()), '">';
				echo "<b>", i18n::translate('Immediate Family'), "&nbsp;&nbsp;</b><span class=\"age\">(", $famid, ")</span>";
				echo "</a>";
				?>
				</td>
			</tr>
		<?php

			//$personcount = 0;
			$people = $this->buildFamilyList($family, "spouse");
			if ($this->indi->equals($people["husb"])){
				$spousetag = 'WIFE';
			}else{
				$spousetag = 'HUSB';
			}
			$styleadd = "";
			if ( isset($people["husb"]) && $spousetag == 'HUSB' ) {
				$menu = new Menu("&nbsp;" . $people["husb"]->getLabel() . "&nbsp;". "\n");
				if ($TEXT_DIRECTION=="ltr") {
					$menu->addClass("", "", "submenu flyout");
				}else{
					$menu->addClass("", "", "submenu flyoutrtl");
				}
				$slabel  = "</a>".print_pedigree_person_nav($people["husb"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
				$slabel .= PrintReady($parentlinks)."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
				if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
						$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
					</td>
					<td align="center" class="<?php print $this->getPersonStyle($people["husb"]); ?>">
						<?php
						if ($pid == $people["husb"]->getXref()) {
							print PrintReady($people["husb"]->getFullName());
							print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						}else{
							print "<a href=\"".encode_url($people["husb"]->getLinkUrl()."&amp;tab={$tabno}")."\">";
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
				if ($TEXT_DIRECTION=="ltr") {
					$menu->addClass("", "", "submenu flyout");
				}else{
					$menu->addClass("", "", "submenu flyoutrtl");
				}
				$slabel  = "</a>".print_pedigree_person_nav($people["wife"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
				$slabel .= PrintReady($parentlinks)."<a>";
				$submenu = new Menu($slabel);
				$menu->addSubMenu($submenu);

				if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
				if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
				?>
				<tr>
					<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
						$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
					</td>
					<td align="center" class="<?php print $this->getPersonStyle($people["wife"]); ?>">
						<?php
						if ($pid == $people["wife"]->getXref()) {
							print PrintReady($people["wife"]->getFullName());
							print "<font size=\"1\"><br />" . $BirthYr . " - " . $DeathYr . "</font>";
						}else{
							print "<a href=\"".encode_url($people["wife"]->getLinkUrl()."&amp;tab={$tabno}")."\">";
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
					if ($TEXT_DIRECTION=="ltr") {
						$menu->addClass("", "", "submenu flyout");
					}else{
						$menu->addClass("", "", "submenu flyoutrtl");
					}
					$slabel = "</a>".print_pedigree_person_nav($child->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++);
					$slabel .= PrintReady($spouselinks)."<a>";
					$submenu = new Menu($slabel);
					$menu->addSubmenu($submenu);

					if (PrintReady($child->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($child->getDeathYear()); }
					if (PrintReady($child->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($child->getBirthYear()); }
					?>
					<tr>
						<td class="facts_label<?php print $styleadd; ?>" nowrap="nowrap">
						<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
						$menu->printMenu();
						if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
						?>
						</td>
						<td align="center" class="<?php print $this->getPersonStyle($child); ?>">
							<?php
							print "<a href=\"".encode_url($child->getLinkUrl()."&amp;tab={$tabno}")."\">";
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

// ==================================================================
require_once WT_ROOT.'includes/functions/functions_charts.php';
/**
* print the information for an individual chart box
*
* find and print a given individuals information for a pedigree chart
* @param string $pid the Gedcom Xref ID of the   to print
* @param int $style the style to print the box in, 1 for smaller boxes, 2 for larger boxes
* @param boolean $show_famlink set to true to show the icons for the popup links and the zoomboxes
* @param int $count on some charts it is important to keep a count of how many boxes were printed
*/
function print_pedigree_person_nav($pid, $style=1, $show_famlink=true, $count=0, $personcount="1") {
	global $HIDE_LIVE_PEOPLE, $SHOW_LIVING_NAMES, $ZOOM_BOXES, $LINK_ICONS, $GEDCOM;
	global $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES, $bwidth, $bheight, $PEDIGREE_FULL_DETAILS, $SHOW_ID_NUMBERS, $SHOW_PEDIGREE_PLACES;
	global $CONTACT_EMAIL, $CONTACT_METHOD, $TEXT_DIRECTION, $DEFAULT_PEDIGREE_GENERATIONS, $OLD_PGENS, $talloffset, $PEDIGREE_LAYOUT, $MEDIA_DIRECTORY;
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

	$tmp=array('M'=>'', 'F'=>'F', 'U'=>'NN');
	$isF=$tmp[$person->getSex()];
	$spouselinks = "";
	$parentlinks = "";
	$step_parentlinks   = "";
	$disp=$person->canDisplayDetails();

	if ($person->canDisplayName()) {
		if ($show_famlink && (empty($SEARCH_SPIDER))) {
			if ($LINK_ICONS!="disabled") {
				//-- draw a box for the family popup
				$spouselinks .= "<span class=\"flyout\"><b>".i18n::translate('Family')."</b></span><br />";
				$parentlinks .= "<span class=\"flyout\"><b>".i18n::translate('Parents')."</b></span><br />";
				$step_parentlinks .= "<span class=\"flyout\"><b>".i18n::translate('Parents')."</b></span><br />";
				$persons       = "";
				$person_parent = "";
				$person_step   = "";



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
								$parentlinks .= "<a href=\"".encode_url($husb->getLinkUrl()."&amp;tab={$tabno}")."\">";
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
								$parentlinks .= "<a href=\"".encode_url($wife->getLinkUrl()."&amp;tab={$tabno}")."\">";
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
									$parentlinks .= "<a href=\"".encode_url($husb->getLinkUrl()."&amp;tab={$tabno}")."\">";
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
									$parentlinks .= "<a href=\"".encode_url($wife->getLinkUrl()."&amp;tab={$tabno}")."\">";
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
								$spouselinks .= "<a href=\"".encode_url($spouse->getLinkUrl()."&amp;tab={$tabno}")."\">";
								$spouselinks .= "&nbsp;".PrintReady($spouse->getFullName());
								$spouselinks .= "</a>";
								if ($spouse->getFullName() != "") {
									$persons = "Yes";
								}
							}
						}
						$spouselinks .= "<ul class=\"clist ".$TEXT_DIRECTION."\">\n";
						// Children ------------------------------   @var $child Person
						foreach($children as $c=>$child) {
							if ($child) {
								$persons="Yes";
									$title = i18n::translate('Individual Information').": ".$child->getXref();
									$spouselinks .= "<li>";
									$spouselinks .= "<a href=\"".encode_url($child->getLinkUrl()."&amp;tab={$tabno}")."\">";
									$spouselinks .= PrintReady($child->getFullName());
									$spouselinks .= "</a>";
									$spouselinks .= "</li>\n";
							}
						}
						$spouselinks .= "</ul>";
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
// ==============================================================
?>

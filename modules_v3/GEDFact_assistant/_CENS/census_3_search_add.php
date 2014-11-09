<?php
// Census Assistant Control module for webtrees
//
// Census Search and Add Area File
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2007 to 2010 PGV Development Team.
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
?>

	<table id="navenclose">
		<tr>
			<td class="descriptionbox"><?php echo WT_I18N::translate('Add individuals'); ?></td>
		</tr>
		<tr>
			<td class="optionbox" >
				<script>
					function findindi(persid) {
						var findInput = document.getElementById('personid');
							txt = findInput.value;
						if (txt=="") {
							alert("<?php echo WT_I18N::translate('You must enter a name'); ?>");
						} else {
							var win02 = window.open(
								"module.php?mod=GEDFact_assistant&mod_action=_CENS/census_3_find&callback=paste_id&action=filter&filter="+txt, "win02", "resizable=1, menubar=0, scrollbars=1, top=180, left=600, height=400, width=450 ");
							if (window.focus) {win02.focus();}
						}
					}
				</script>
				<?php
				echo "<input id=personid type=\"text\" size=\"20\" style=\"color: #000000;\" value=\"\">";
				echo "<a href=\"#\" onclick=\"findindi()\">" ;
				echo "&nbsp;&nbsp;".WT_I18N::translate('Search');
				echo '</a>';
				?>
			</td>
		</tr>
		<tr>
			<td>
				<br>
			</td>
		</tr>

				<?php
				//-- Add Family Members to Census  -------------------------------------------
				global $spouselinks, $parentlinks, $DeathYr, $BirthYr;
				?>

				<tr>
					<td>
						<table class="fact_table">
							<tr>
								<td colspan="3" class="descriptionbox">
								<?php
								// Header text with "Head" button =================================================
								$headImg  = '<i class="headimg vmiddle icon-button_head"></i>';
								$headImg2 = '<i class="headimg2 vmiddle icon-button_head" title="'.WT_I18N::translate('Click to choose individual as head of family.').'"></i>';
								echo WT_I18N::translate('Click %s to choose individual as head of family.', $headImg);
								?>
								</td>
							</tr>

					<?php

					//-- Parents Family ---------------------------------------------------

					//-- Build Parents Family --------------------------------------
					$families = $person->getChildFamilies();
					foreach ($families as $family) {
						$marrdate = $family->getMarriageDate();

						//-- Get Parents Children’s Name, DOB, DOD --------------------------
						$chBLDarray = Array();
						foreach ($family->getChildren() as $child) {
							$chnam   = $child->getAllNames();
							$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
							$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
							$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
							$chfulln = WT_Filter::escapeHtml($chfulln); // Child’s Full Name
							$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2; // Child’s Date of Birth (Julian)
							$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2; // Child’s Date of Death (Julian)
							$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
							array_push($chBLDarray, $chBLD);
						}

						//-- Parents Husband -------------------
						if ($family->getHusband()) {

							//-- Parents Husbands Parents --------------------------------------
							$gparent=$family->getHusband();
							foreach ($gparent->getChildFamilies() as $cfamily) {
								$phusb = $cfamily->getHusband();
								$pwife = $cfamily->getWife();
								if ($phusb) { $HusbFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $HusbMBP = $pwife->getBirthPlace(); }
							}

							//-- Parents Husbands Details --------------------------------------
							$married = WT_Date::Compare($censdate, $marrdate);
							$nam     = $gparent->getAllNames();
							$fulln   = rtrim($nam[0]['givn'],'*') . ' ' . $nam[0]['surname'];
							$fulln   = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln   = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn    = rtrim($nam[0]['givn'],'*');
							$surn    = $nam[0]['surname'];
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*') . ' ' . $nam[$i]['surname'];
								}
							}
							$label = get_close_relationship_name($person, $gparent);
							$menu = new WT_Menu($label);
							print_pedigree_person_nav_cens($gparent->getXref(), $label, $censdate);
							$submenu = new WT_Menu($parentlinks);
							$menu->addSubMenu($submenu);

							?>
							<tr>
								<td align="left" class="linkcell optionbox" width="25%">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value" style="text-decoration:none;" >
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=".$gparent->getXref()."&amp;gedcom=".WT_GEDURL."\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<a href='#' onclick='insertRowToTable("<?php
											echo $gparent->getXref() ; // pid = PID
										?>", "<?php
											echo WT_Filter::escapeHtml($fulln); // nam = Full Name
										?>", "<?php
											if (isset($fulmn)) {
												echo WT_Filter::escapeHtml($fulln); // mnam = Full Married Name
											} else {
												echo WT_Filter::escapeHtml($fulln); // mnam = Full Name
											}
										?>", "<?php
											if ($person === $gparent) {
												echo 'head';
											} else {
												echo WT_Filter::escapeHtml($label);
											}
										?>", "<?php
											echo $gparent->getSex(); // gend = Gender
										?>", "<?php
											if ($married>=0) {
												echo "M"; // cond = Condition (Married)
											} else {
												echo "S"; // cond = Condition (Single)
											}
										?>", "<?php
											if ($marrdate) {
												echo ($marrdate->minJD()+$marrdate->maxJD())/2; // dom = Date of Marriage (Julian)
											}
										?>", "<?php
											echo ($gparent->getBirthDate()->minJD()+$gparent->getBirthDate()->maxJD())/2; // dob = Date of Birth (Julian)
										?>", "<?php
											echo $censyear-$gparent->getbirthyear(); // age = Census Date minus YOB
										?>", "<?php
											echo ($gparent->getDeathDate()->minJD()+$gparent->getDeathDate()->maxJD())/2; // dod = Date of Death (Julian)
										?>", "<?php
											echo ""; // occu = Occupation
										?>", "<?php
											echo WT_Filter::escapeHtml($gparent->getBirthPlace()); //  birthpl = Husband Place of Birth
										?>", "<?php
											if (isset($HusbFBP)) {
												echo WT_Filter::escapeHtml($HusbFBP); // fbirthpl = Husband Father’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Husband Father’s Place of Birth Not known
											}
										?>", "<?php
											if (isset($HusbMBP)) {
												echo WT_Filter::escapeHtml($HusbMBP); // mbirthpl = Husband Mother’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Husband Mother’s Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $gparent->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											}
										?>");'>
										<?php
											echo $gparent->getFullName();  // Full Name (Link)
										?>
									</a>
								</td>
							</tr>
							<?php
						}

						//-- Parents Wife ---------------------------------------------------------
						if ($family->getWife()) {

							//-- Parents Wifes Parent Family ---------------------------
							$gparent=$family->getWife();
							$cfamily = null;
							foreach ($gparent->getChildFamilies() as $cfamily) {
								$phusb = $cfamily->getHusband();
								$pwife = $cfamily->getWife();
								if ($phusb) { $WifeFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $WifeMBP = $pwife->getBirthPlace(); }
							}

							//-- Wifes Details --------------------------------------
							$married = WT_Date::Compare($censdate, $marrdate);
							$nam     = $gparent->getAllNames();
							$fulln   = rtrim($nam[0]['givn'],'*') . ' ' . $nam[0]['surname'];
							$fulln   = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln   = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn    = rtrim($nam[0]['givn'],'*');
							$surn    = $nam[0]['surname'];
							$husbnam = null;
							// Get wifes married name if available
							if ($cfamily && $cfamily->getHusband()) {
								$husbnams = $cfamily->getHusband()->getAllNames();
								if ($husbnams[0]['surname']=="@N.N." || $husbnams[0]['surname']=="") {
									// if Husband or his name is not known then use wifes birth name
									$husbnam = $nam[0]['surname'];
								} else {
									$husbnam = $husbnams[0]['surname'];
								}
							}
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*') . ' ' . $husbnam;
								}
							}
							$label = get_close_relationship_name($person, $gparent);
							$menu = new WT_Menu($label);
							print_pedigree_person_nav_cens($gparent->getXref(), $label, $censyear);
							$submenu = new WT_Menu($parentlinks);
							$menu->addSubMenu($submenu);
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=".$gparent->getXref()."&amp;gedcom=".WT_GEDURL."\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<a href='#' onclick='insertRowToTable("<?php
											echo $gparent->getXref();
										?>", "<?php
											echo WT_Filter::escapeHtml($fulln); // nam = Full Name
										?>", "<?php
											if (isset($fulmn)) {
												echo WT_Filter::escapeHtml($fulmn); // mnam = Full Married Name
											} else {
												echo WT_Filter::escapeHtml($fulln); // mnam = Full Name
											}
										?>", "<?php
											if ($person === $gparent) {
												echo 'head';
											} else {
												echo WT_Filter::escapeHtml($label);
											}
										?>", "<?php
											echo $gparent->getSex(); // gend = Gender
										?>", "<?php
											if ($married>=0 && isset($nam[1])) {
												echo "M"; // cond = Condition (Married)
											} else {
												echo "S"; // cond = Condition (Single)
											}
										?>", "<?php
											if ($marrdate) {
												echo ($marrdate->minJD()+$marrdate->maxJD())/2; // dom = Date of Marriage (Julian)
											}
										?>", "<?php
											echo ($gparent->getBirthDate()->minJD()+$gparent->getBirthDate()->maxJD())/2;    // dob = Date of Birth (Julian)
										?>", "<?php
											echo $censyear-$gparent->getbirthyear(); // age = Census Date minus YOB
										?>", "<?php
											echo ($gparent->getDeathDate()->minJD()+$gparent->getDeathDate()->maxJD())/2; // dod = Date of Death (Julian)
										?>", "<?php
											echo ""; // occu = Occupation
										?>", "<?php
											echo WT_Filter::escapeHtml($gparent->getBirthPlace()); //  birthpl = Wife Place of Birth
										?>", "<?php
											if (isset($WifeFBP)) {
												echo WT_Filter::escapeHtml($WifeFBP); // fbirthpl = Wife Father’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Wife Father’s Place of Birth Not known
											}
										?>", "<?php
											if (isset($WifeMBP)) {
												echo WT_Filter::escapeHtml($WifeMBP); // mbirthpl = Wife Mother’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Wife Mother’s Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $gparent->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											}
										?>");'>
										<?php
											echo $gparent->getFullName();  // Full Name (Link)
										?>
									</a>
								</td>
							</tr>
							<?php
						}

						//-- Parents Children -------------------

						//-- Parent’s Children’s Details --------------------------------------
						foreach ($family->getChildren() as $child) {

							// Get Child’s Children’s Name DOB DOD ----
							$chBLDarray=Array();
							foreach ($child->getSpouseFamilies() as $childfamily) {
								$chchildren = $childfamily->getChildren();
								foreach ($chchildren as $chchild) {
									$chnam   = $chchild->getAllNames();
									$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
									$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
									$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
									$chfulln = WT_Filter::escapeHtml($chfulln); // Child’s Full Name// Child’s Full Name
									$chdob   = ($chchild->getBirthDate()->minJD()+$chchild->getBirthDate()->maxJD())/2; // Child’s Date of Birth (Julian)
									$chdod   = ($chchild->getDeathDate()->minJD()+$chchild->getDeathDate()->maxJD())/2; // Child’s Date of Death (Julian)
									$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
									array_push($chBLDarray, $chBLD);
								}
							}

							// Get child’s marriage status ----
							$married="";
							$marrdate="";
							foreach ($child->getSpouseFamilies() as $childfamily) {
								$marrdate=$childfamily->getMarriageDate();
								$married = WT_Date::Compare($censdate, $marrdate);
							}
							$nam   = $child->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*') . ' ' . $nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn  = rtrim($nam[0]['givn'],'*');
							$surn  = $nam[0]['surname'];
							$chfulmn=null;
							$chnam = $child->getAllNames();
							for ($i=0; $i<count($nam); $i++) {
								if ($chnam[$i]['type']=='_MARNM') {
									$chfulmn = rtrim($chnam[$i]['givn'],'*') . ' ' . $chnam[$i]['surname'];
								}
							}
							$label = get_close_relationship_name($person, $child);
							$menu = new WT_Menu($label);
							print_pedigree_person_nav_cens($child->getXref(), $label, $censyear);
							$submenu = new WT_Menu($spouselinks);
							$menu->addSubMenu($submenu);

							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=".$child->getXref()."&amp;gedcom=".WT_GEDURL."\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<?php
									if (($child->canShow())) {
										?>
										<a href='#' onclick='insertRowToTable("<?php
												echo $child->getXref(); // pid = PID
											?>", "<?php
												echo WT_Filter::escapeHtml($fulln); // nam = Full Name
											?>", "<?php
												if (isset($chfulmn)) {
													echo WT_Filter::escapeHtml($chfulmn); // mnam = Full Married Name
												} else {
													echo WT_Filter::escapeHtml($fulln); // mnam = Full Name
												}
											?>", "<?php
											if ($person === $child) {
												echo 'head';
											} else {
												echo WT_Filter::escapeHtml($label);
											}
											?>", "<?php
												echo $child->getSex(); // gend = Gender
											?>", "<?php
												if ($married>0) {
													echo "M"; // cond = Condition (Married)
												} else if ($married<0 || ($married=="0") ) {
													echo "S"; // cond = Condition (Single)
												}
											?>", "<?php
												if ($marrdate) {
													echo ($marrdate->minJD()+$marrdate->maxJD())/2; // dom = Date of Marriage (Julian)
												}
											?>", "<?php
												echo ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2; // dob = Date of Birth (Julian)
											?>", "<?php
												echo $censyear-$child->getbirthyear(); // age = Census Date minus YOB
											?>", "<?php
												echo ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2; // dod = Date of Death (Julian)
											?>", "<?php
												echo ""; // occu = Occupation
											?>", "<?php
												echo WT_Filter::escapeHtml($child->getBirthPlace()); //  birthpl = Child Place of Birt
											?>", "<?php
												if ($family->getHusband()) {
													echo WT_Filter::escapeHtml($family->getHusband()->getBirthPlace()); // fbirthpl = Child Father’s Place of Birth
												} else {
													echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Child Father’s Place of Birth Not known
												}
											?>", "<?php
												if ($family->getWife()) {
													echo WT_Filter::escapeHtml($family->getWife()->getBirthPlace()); // mbirthpl = Child Mother’s Place of Birth
												} else {
													echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Child Mother’s Place of Birth Not known
												}
											?>", "<?php
												if (isset($chBLDarray) && $child->getSex()=="F") {
													$chBLDarray = implode("::", $chBLDarray);
													echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
												}
											?>");'>
											<?php
												echo $child->getFullName(); // Full Name (Link)
											?>
										</a>
										<?php
									} else {
											echo WT_I18N::translate('Private');
									}
									?>
								</td>
							</tr>
							<?php
						}
					}

					//-- Step families ---------------------------------------------------------

					//-- Build step families ---------------------------------------------------
					foreach ($person->getChildStepFamilies() as $family) {
						$marrdate = $family->getMarriageDate();

						//-- Get Children’s Name, DOB, DOD --------------------------
						$chBLDarray = Array();
						foreach ($family->getChildren() as $child) {
							$chnam   = $child->getAllNames();
							$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
							$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
							$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
							$chfulln = WT_Filter::escapeHtml($chfulln); // Child’s Full Name
							$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2; // Child’s Date of Birth (Julian)
							$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2; // Child’s Date of Death (Julian)
							$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
							array_push($chBLDarray, $chBLD);
						}

						// Step Husband -----------------------------
						if ($family->getHusband()) {

							//-- Step Husbands Parent Family --------------------------------------
							$gparent=$family->getHusband();
							foreach ($gparent->getChildFamilies() as $cfamily) {
								$phusb = $cfamily->getHusband();
								$pwife = $cfamily->getWife();
								if ($phusb) { $HusbFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $HusbMBP = $pwife->getBirthPlace(); }
							}

							//-- Step Husbands Details --------------------------------------
							$married = WT_Date::Compare($censdate, $marrdate);
							$nam   = $gparent->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*') . ' ' . $nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn  = rtrim($nam[0]['givn'],'*');
							$surn  = $nam[0]['surname'];
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*') . ' ' . $nam[$i]['surname'];
								}
							}
							$label = get_close_relationship_name($person, $gparent);
							$menu = new WT_Menu($label);
							print_pedigree_person_nav_cens($gparent->getXref(), $label, $censyear);
							$submenu = new WT_Menu($parentlinks);
							$menu->addSubMenu($submenu);
							if ($gparent->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $gparent->getDeathYear(); }
							if ($gparent->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $gparent->getBirthYear(); }
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=".$gparent->getXref()."&amp;gedcom=".WT_GEDURL."\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<a href='#' onclick='insertRowToTable("<?php
											echo $gparent->getXref(); // pid = PID
										?>", "<?php
											echo WT_Filter::escapeHtml($fulln); // nam = Full Name
										?>", "<?php
											if (isset($fulmn)) {
												echo WT_Filter::escapeHtml($fulln); // mnam = Full Married Name
											} else {
												echo WT_Filter::escapeHtml($fulln); // mnam = Full Name
											}
										?>", "<?php
											if ($person === $gparent) {
												echo 'head';
											} else {
												echo WT_Filter::escapeHtml($label);
											}
										?>", "<?php
											echo $gparent->getSex(); // gend = Gender
										?>", "<?php
											if ($married>=0) {
												echo "M"; // cond = Condition (Married)
											} else {
												echo "S"; // cond = Condition (Single)
											}
										?>", "<?php
											if ($marrdate) {
												echo ($marrdate->minJD()+$marrdate->maxJD())/2; // dom = Date of Marriage (Julian)
											}
										?>", "<?php
											echo ($gparent->getBirthDate()->minJD()+$gparent->getBirthDate()->maxJD())/2; // dob = Date of Birth (Julian)
										?>", "<?php
											echo $censyear-$gparent->getbirthyear(); // age = Census Date minus YOB
										?>", "<?php
											echo ($gparent->getDeathDate()->minJD()+$gparent->getDeathDate()->maxJD())/2; // dod = Date of Death (Julian)
										?>", "<?php
											echo ""; // occu = Occupation
										?>", "<?php
											echo WT_Filter::escapeHtml($gparent->getBirthPlace()); //  birthpl = Step Husband Place of Birth
										?>", "<?php
											if (isset($HusbFBP)) {
												echo WT_Filter::escapeHtml($HusbFBP); // fbirthpl = Step Husband Father’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Step Husband Father’s Place of Birth Not known
											}
										?>", "<?php
											if (isset($HusbMBP)) {
												echo WT_Filter::escapeHtml($HusbMBP); // mbirthpl = Step Husband Mother’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Step Husband Mother’s Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $gparent->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											}
										?>");'>
										<?php
											echo $gparent->getFullName();  // Full Name (Link)
										?>
									</a>
								</td>
							</tr>
							<?php
						}

						// Step Wife -------------------
						if ($family->getWife()) {

							//-- Step Wifes Parent Family --------------------------------------
							$gparent=$family->getWife();
							$cfamily = null;
							foreach ($gparent->getChildFamilies() as $cfamily) {
								$phusb = $cfamily->getHusband();
								$pwife = $cfamily->getWife();
								if ($phusb) { $WifeFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $WifeMBP = $pwife->getBirthPlace(); }
							}

							//-- Step Wifes Details --------------------------------------
							$married = WT_Date::Compare($censdate, $marrdate);
							$nam   = $gparent->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*') . ' ' . $nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn  = rtrim($nam[0]['givn'],'*');
							$surn  = $nam[0]['surname'];
							$husbnam = null;
							// Get wifes married name if available
							if ($cfamily && $cfamily->getHusband()) {
								$husbnams = $cfamily->getHusband()->getAllNames();
								if ($husbnams[0]['surname']=="@N.N." || $husbnams[0]['surname']=="") {
									// if Husband or his name is not known then use wifes birth name
									$husbnam = $nam[0]['surname'];
								} else {
									$husbnam = $husbnams[0]['surname'];
								}
							}
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*') . ' ' . $husbnam;
								}
							}
							$label = get_close_relationship_name($person, $gparent);
							$menu = new WT_Menu($label);
							print_pedigree_person_nav_cens($gparent->getXref(), $label, $censyear);
							$submenu = new WT_Menu($parentlinks);
							$menu->addSubMenu($submenu);
							if ($gparent->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $gparent->getDeathYear(); }
							if ($gparent->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $gparent->getBirthYear(); }
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=".$gparent->getXref()."&amp;gedcom=".WT_GEDURL."\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<a href='#' onclick='insertRowToTable("<?php
											echo $gparent->getXref() ; // pid = PID
										?>", "<?php
											echo WT_Filter::escapeHtml($fulln); // nam = Full Name
										?>", "<?php
											if (isset($fulmn)) {
												echo WT_Filter::escapeHtml($fulmn); // mnam = Full Married Name
											} else {
												echo WT_Filter::escapeHtml($fulln); // mnam = Full Name
											}
										?>", "<?php
											if ($person ===$gparent) {
												echo 'head';
											} else {
												echo WT_Filter::escapeHtml($label);
											}
										?>", "<?php
											echo $gparent->getSex(); // gend = Gender
										?>", "<?php
											if ($married>=0 && isset($nam[1])) {
												echo "M"; // cond = Condition (Married)
											} else {
												echo "S"; // cond = Condition (Single)
											}
										?>", "<?php
											if ($marrdate) {
												echo ($marrdate->minJD()+$marrdate->maxJD())/2; // dom = Date of Marriage (Julian)
											}
										?>", "<?php
											echo ($gparent->getBirthDate()->minJD()+$gparent->getBirthDate()->maxJD())/2; // dob = Date of Birth (Julian)
										?>", "<?php
											echo $censyear-$gparent->getbirthyear(); // age = Census Date minus YOB
										?>", "<?php
											echo ($gparent->getDeathDate()->minJD()+$gparent->getDeathDate()->maxJD())/2; // dod = Date of Death (Julian)
										?>", "<?php
											echo ""; // occu = Occupation
										?>", "<?php
											echo WT_Filter::escapeHtml($gparent->getBirthPlace()); //  birthpl = Step Wife Place of Birth
										?>", "<?php
											if (isset($WifeFBP)) {
												echo WT_Filter::escapeHtml($WifeFBP); // fbirthpl = Step Wife Father’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Step Wife Father’s Place of Birth Not known
											}
										?>", "<?php
											if (isset($WifeMBP)) {
												echo WT_Filter::escapeHtml($WifeMBP); // mbirthpl = Step Wife Mother’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Step Wife Mother’s Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $gparent->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											}
										?>");'>
										<?php
											echo $gparent->getFullName();  // Full Name (Link)
										?>
									</a>
								</td>
							</tr>
							<?php
						}

						// Step Children ---------------------
						foreach ($family->getChildren() as $child) {

							// Get Child’s Children
							$chBLDarray=Array();
							foreach ($child->getSpouseFamilies() as $childfamily) {
								$chchildren = $childfamily->getChildren();
								foreach ($chchildren as $chchild) {
									$chnam   = $chchild->getAllNames();
									$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
									$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
									$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
									$chfulln = WT_Filter::escapeHtml($chfulln); // Child’s Full Name
									$chdob   = ($chchild->getBirthDate()->minJD()+$chchild->getBirthDate()->maxJD())/2; // Child’s Date of Birth (Julian)
									$chdod   = ($chchild->getDeathDate()->minJD()+$chchild->getDeathDate()->maxJD())/2; // Child’s Date of Death (Julian)
									$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
									array_push($chBLDarray, $chBLD);
								}
							}

							$nam   = $child->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*') . ' ' . $nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn  = rtrim($nam[0]['givn'],'*');
							$surn  = $nam[0]['surname'];
							$chfulmn=null;
							$chnam = $child->getAllNames();
							for ($i=0; $i<count($nam); $i++) {
								if ($chnam[$i]['type']=='_MARNM') {
									$chfulmn = rtrim($chnam[$i]['givn'],'*') . ' ' . $chnam[$i]['surname'];
								}
							}
							$label = get_close_relationship_name($person, $child);
							$menu = new WT_Menu($label);
							print_pedigree_person_nav_cens($child->getXref(), $label, $censyear);
							$submenu = new WT_Menu($spouselinks);
							$menu->addSubMenu($submenu);
							if ($child->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $child->getDeathYear(); }
							if ($child->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $child->getBirthYear(); }
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=".$child->getXref()."&amp;gedcom=".WT_GEDURL."\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<?php
									if (($child->canShow())) {
									?>
									<a href='#' onclick='insertRowToTable("<?php
											echo $child->getXref() ; // pid = PID
										?>", "<?php
											echo WT_Filter::escapeHtml($fulln); // nam = Full Name
										?>", "<?php
											if (isset($chfulmn)) {
												echo WT_Filter::escapeHtml($chfulmn); // mnam = Full Married Name
											} else {
												echo WT_Filter::escapeHtml($fulln); // mnam = Full Name
											}
										?>", "<?php
											if ($person === $child) {
												echo 'head';
											} else {
												echo WT_Filter::escapeHtml($label);
											}
										?>", "<?php
											echo $child->getSex(); // gend = Gender
										?>", "<?php
											echo ""; // cond = Condition (Married or Single)
										?>", "<?php
										if ($marrdate) {
											echo ($marrdate->minJD()+$marrdate->maxJD())/2; // dom = Date of Marriage (Julian)
										}
										?>", "<?php
											echo ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2; // dob = Date of Birth (Julian)
										?>", "<?php
											echo $censyear-$child->getbirthyear(); // age = Census Date minus YOB
										?>", "<?php
											echo ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2; // dod = Date of Death (Julian)
										?>", "<?php
											echo ""; // occu = Occupation
										?>", "<?php
											echo WT_Filter::escapeHtml($child->getBirthPlace()); //  birthpl = Child Place of Birth
										?>", "<?php
											if ($family->getHusband()) {
												echo WT_Filter::escapeHtml($family->getHusband()->getBirthPlace()); // fbirthpl = Child Father’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Child Father’s Place of Birth Not known
											}
										?>", "<?php
											if ($family->getWife()) {
												echo WT_Filter::escapeHtml($family->getWife()->getBirthPlace()); // mbirthpl = Child Mother’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Child Mother’s Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $child->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											}
										?>");'>
										<?php
											echo $child->getFullName();  // Full Name (Link)
										?>
									</a>
									<?php
									} else {
										echo WT_I18N::translate('Private');
									}
									?>
								</td>
							</tr>
							<?php
						}
					}

					echo "<tr><td><br></td></tr>";

					//-- Build Spouse Family ---------------------------------------------------
					foreach ($person->getSpouseFamilies() as $family) {
						$marrdate = $family->getMarriageDate();

						//-- Get Children’s Name, DOB, DOD --------------------------
						$chBLDarray = Array();
						foreach ($family->getChildren() as $child) {
							$chnam   = $child->getAllNames();
							$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
							$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
							$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
							$chfulln = WT_Filter::escapeHtml($chfulln); // Child’s Full Name
							$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2; // Child’s Date of Birth (Julian)
							$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2; // Child’s Date of Death (Julian)
							$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
							array_push($chBLDarray, $chBLD);
						}

						//-- Spouse Husband ---------------------------------------------------
						if ($family->getHusband()) {

							//-- Spouse Husbands Parents --------------------------------------
							$gparent=$family->getHusband();
							foreach ($gparent->getChildFamilies() as $cfamily) {
								$phusb = $cfamily->getHusband();
								$pwife = $cfamily->getWife();
								if ($phusb) { $HusbFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $HusbMBP = $pwife->getBirthPlace(); }
							}

							//-- Spouse Husbands Details --------------------------------------
							$married = WT_Date::Compare($censdate, $marrdate);
							$nam     = $gparent->getAllNames();
							$fulln   = rtrim($nam[0]['givn'],'*') . ' ' . $nam[0]['surname'];
							$fulln   = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln   = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn    = rtrim($nam[0]['givn'],'*');
							$surn    = $nam[0]['surname'];
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*') . ' ' . $nam[$i]['surname'];
								}
							}
							$label = get_close_relationship_name($person, $gparent);
							$menu = new WT_Menu($label);
							print_pedigree_person_nav_cens($gparent->getXref(), $label, $censyear);
							$submenu = new WT_Menu($parentlinks);
							$menu->addSubMenu($submenu);
							if ($gparent->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $gparent->getDeathYear(); }
							if ($gparent->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $gparent->getBirthYear(); }
							?>
							<tr class="fact_value">
								<td align="left" class="linkcell optionbox nowrap">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=".$gparent->getXref()."&amp;gedcom=".WT_GEDURL."\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<a href='#' onclick='insertRowToTable("<?php
											echo $gparent->getXref() ; // pid = PID
										?>", "<?php
											echo WT_Filter::escapeHtml($fulln); // nam = Full Name
										?>", "<?php
											if (isset($fulmn)) {
												echo WT_Filter::escapeHtml($fulln); // mnam = Full Married Name
											} else {
												echo WT_Filter::escapeHtml($fulln); // mnam = Full Name
											}
										?>", "<?php
											if ($person === $gparent) {
												echo 'head';
											} else {
												echo WT_Filter::escapeHtml($label);
											}
										?>", "<?php
											echo $gparent->getSex(); // gend = Gender
										?>", "<?php
											if ($married>=0) {
												echo "M"; // cond = Condition (Married)
											} else {
												echo "S"; // cond = Condition (Single)
											}
										?>", "<?php
											if ($marrdate) {
												echo ($marrdate->minJD()+$marrdate->maxJD())/2; // dom = Date of Marriage (Julian)
											}
										?>", "<?php
											echo ($gparent->getBirthDate()->minJD()+$gparent->getBirthDate()->maxJD())/2; // dob = Date of Birth (Julian)
										?>", "<?php
											echo $censyear-$gparent->getbirthyear(); // age = Census Date minus YOB
										?>", "<?php
											echo ($gparent->getDeathDate()->minJD()+$gparent->getDeathDate()->maxJD())/2; // dod = Date of Death (Julian)
										?>", "<?php
											echo ""; // occu = Occupation
										?>", "<?php
											echo WT_Filter::escapeHtml($gparent->getBirthPlace()); //  birthpl = Husband Place of Birth
										?>", "<?php
											if (isset($HusbFBP)) {
												echo WT_Filter::escapeHtml($HusbFBP); // fbirthpl = Husband Father’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Husband Father’s Place of Birth Not known
											}
										?>", "<?php
											if (isset($HusbMBP)) {
												echo WT_Filter::escapeHtml($HusbMBP); // mbirthpl = Husband Mother’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Husband Mother’s Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $gparent->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											}
										?>");'>
										<?php
											echo $gparent->getFullName();  // Full Name (Link)
										?>
									</a>
								</td>
							<tr>
							<?php
						}

						//-- Spouse Wife -----------------------------------------------------
						if ($family->getWife()) {

							//-- Spouse Wifes Parents --------------------------------------
							$gparent=$family->getWife();
							$cfamily = null;
							foreach ($gparent->getChildFamilies() as $cfamily) {
								$husb = $cfamily->getHusband();
								$wife = $cfamily->getWife();
								if ($husb) { $WifeFBP = $husb->getBirthPlace(); }
								if ($wife) { $WifeMBP = $wife->getBirthPlace(); }
							}

							//-- Spouse Wifes Details --------------------------------------
							$married = WT_Date::Compare($censdate, $marrdate);
							$nam     = $gparent->getAllNames();
							$fulln   = rtrim($nam[0]['givn'],'*') . ' ' . $nam[0]['surname'];
							$fulln   = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln   = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn    = rtrim($nam[0]['givn'],'*');
							$surn    = $nam[0]['surname'];
							$husbnam = null;
							// Get wifes married name if available
							if ($cfamily && $cfamily->getHusband()) {
								$husbnams = $cfamily->getHusband()->getAllNames();
								if ($husbnams[0]['surname']=="@N.N." || $husbnams[0]['surname']=="") {
									// if Husband or his name is not known then use wifes birth name
									$husbnam = $nam[0]['surname'];
								} else {
									$husbnam = $husbnams[0]['surname'];
								}
							}
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*') . ' ' . $husbnam;
								}
							}
							$label = get_close_relationship_name($person, $gparent);
							$menu = new WT_Menu($label);
							print_pedigree_person_nav_cens($gparent->getXref(), $label, $censyear);
							$submenu = new WT_Menu($parentlinks);
							$menu->addSubMenu($submenu);
							if ($gparent->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $gparent->getDeathYear(); }
							if ($gparent->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $gparent->getBirthYear(); }
							?>
							<tr>
								<td align="left" class="linkcell optionbox nowrap">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=".$gparent->getXref()."&amp;gedcom=".WT_GEDURL."\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<a href='#' onclick='insertRowToTable("<?php
											echo $gparent->getXref() ; // pid = PID
									?>", "<?php
										echo WT_Filter::escapeHtml($fulln); // nam = Full Name
									?>", "<?php
										if (isset($fulmn)) {
											echo WT_Filter::escapeHtml($fulmn); // mnam = Full Married Name
										} else {
											echo WT_Filter::escapeHtml($fulln); // mnam = Full Name
										}
									?>", "<?php
										if ($person === $gparent) {
											echo 'head';
										} else {
											echo WT_Filter::escapeHtml($label);
										}
									?>", "<?php
										echo $gparent->getSex(); // gend = Gender
									?>", "<?php
										if ($married>=0 && isset($nam[1])) {
											echo "M"; // cond = Condition (Married)
										} else {
											echo "S"; // cond = Condition (Single)
										}
									?>", "<?php
										if ($marrdate) {
											echo ($marrdate->minJD()+$marrdate->maxJD())/2; // dom = Date of Marriage (Julian)
										}
									?>", "<?php
										echo ($gparent->getBirthDate()->minJD()+$gparent->getBirthDate()->maxJD())/2; // dob = Date of Birth (Julian)
									?>", "<?php
										echo $censyear-$gparent->getbirthyear(); // age = Census Date minus YOB
									?>", "<?php
										echo ($gparent->getDeathDate()->minJD()+$gparent->getDeathDate()->maxJD())/2; // dod = Date of Death (Julian)
									?>", "<?php
										echo ""; // occu = Occupation
									?>", "<?php
										echo WT_Filter::escapeHtml($gparent->getBirthPlace()); //  birthpl = Wife Place of Birth
									?>", "<?php
										if (isset($WifeFBP)) {
											echo WT_Filter::escapeHtml($WifeFBP); // fbirthpl = Wife Father’s Place of Birth
										} else {
											echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Wife Father’s Place of Birth Not known
										}
									?>", "<?php
										if (isset($WifeMBP)) {
											echo WT_Filter::escapeHtml($WifeMBP); // mbirthpl = Wife Mother’s Place of Birth
										} else {
											echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Wife Mother’s Place of Birth Not known
										}
									?>", "<?php
										if (isset($chBLDarray) && $gparent->getSex()=="F") {
											$chBLDarray = implode("::", $chBLDarray);
											echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
										}
									?>");'>
									<?php
										echo $gparent->getFullName();  // Full Name (Link)
									?>
									</a>
								</td>
							<tr> <?php
						}

						// Spouse Children
						foreach ($family->getChildren() as $child) {

							// Get Spouse child’s marriage status
							$married="";
							$marrdate="";
							foreach ($child->getSpouseFamilies() as $childfamily) {
								$marrdate=$childfamily->getMarriageDate();
								$married = WT_Date::Compare($censdate, $marrdate);
							}

							// Get Child’s Children
							$chBLDarray=Array();
							foreach ($child->getSpouseFamilies() as $childfamily) {
								$chchildren = $childfamily->getChildren();
								foreach ($chchildren as $chchild) {
									$chnam   = $chchild->getAllNames();
									$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
									$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
									$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
									$chfulln = WT_Filter::escapeHtml($chfulln); // Child’s Full Name// Child’s Full Name
									$chdob   = ($chchild->getBirthDate()->minJD()+$chchild->getBirthDate()->maxJD())/2; // Child’s Date of Birth (Julian)
									$chdod   = ($chchild->getDeathDate()->minJD()+$chchild->getDeathDate()->maxJD())/2; // Child’s Date of Death (Julian)
									$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
									array_push($chBLDarray, $chBLD);
								}
							}

							// Get Spouse child’s details
							$nam   = $child->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*') . ' ' . $nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn  = rtrim($nam[0]['givn'],'*');
							$surn  = $nam[0]['surname'];
							$chfulmn=null;
							$chnam = $child->getAllNames();
							for ($i=0; $i<count($nam); $i++) {
								if ($chnam[$i]['type']=='_MARNM') {
									$chfulmn = rtrim($chnam[$i]['givn'],'*') . ' ' . $chnam[$i]['surname'];
								}
							}
							$label = get_close_relationship_name($person, $child);
							$menu = new WT_Menu($label);
							print_pedigree_person_nav_cens($child->getXref(), $label, $censyear);
							$submenu = new WT_Menu($spouselinks);
							$menu->addSubmenu($submenu);
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=".$child->getXref()."&amp;gedcom=".WT_GEDURL."\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<?php
									if (($child->canShow())) {
									?>
									<a href='#' onclick='insertRowToTable("<?php
											echo $child->getXref() ; // pid = PID
										?>", "<?php
											echo WT_Filter::escapeHtml($fulln); // nam = Full Name
										?>", "<?php
											if (isset($chfulmn)) {
												echo WT_Filter::escapeHtml($chfulmn); // mnam = Full Married Name
											} else {
												echo WT_Filter::escapeHtml($fulln); // mnam = Full Name
											}
										?>", "<?php
											if ($person === $child) {
												echo 'head';
											} else {
												echo WT_Filter::escapeHtml($label);
											}
										?>", "<?php
											echo $child->getSex(); // gend = Gender
										?>", "<?php
											if ($married>0) {
												echo "M"; // cond = Condition (Married)
											} else if ($married<0 || ($married=="0") ) {
												echo "S"; // cond = Condition (Single)
											} else {
												echo ""; // cond = Condition (Not Known)
											}
										?>", "<?php
											if ($marrdate) {
												echo ($marrdate->minJD()+$marrdate->maxJD())/2; // dom = Date of Marriage (Julian)
											}
										?>", "<?php
											echo ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2; // dob = Date of Birth (Julian)
										?>", "<?php
											echo $censyear-$child->getbirthyear(); //  age = Census Date minus YOB
										?>", "<?php
											echo ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2; // dod = Date of Death (Julian)
										?>", "<?php
											echo ""; // occu = Occupation
										?>", "<?php
											echo WT_Filter::escapeHtml($child->getBirthPlace()); //  birthpl = Child Place of Birth
										?>", "<?php
											if ($family->getHusband()) {
												echo WT_Filter::escapeHtml($family->getHusband()->getBirthPlace()); // fbirthpl = Child Father’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Child Father’s Place of Birth Not known
											}
										?>", "<?php
											if ($family->getWife()) {
												echo WT_Filter::escapeHtml($family->getWife()->getBirthPlace()); // mbirthpl = Child Mother’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Child Mother’s Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $child->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											}
										?>");'>
										<?php
											echo $child->getFullName();  // Full Name (Link)
										?>
									</a>
									<?php
								}
								?>
								</td>
							</tr>
							<?php
						}

					echo "<tr><td><br></td></tr>";
					}
					?>

						</table>
					<br><br><br></td>
				</tr>
			</table>
<?php

/**
 * print the information for an individual chart box
 *
 * Find and print a given individuals information for a pedigree chart
 *
 * @param string $pid      The Gedcom Xref ID of the individual to print
 * @param string $currpid
 * @param string $censyear
 *
 * @return void
 */
function print_pedigree_person_nav_cens($pid, $currpid, $censyear) {
	global $PEDIGREE_FULL_DETAILS;
	global $TEXT_DIRECTION, $DEFAULT_PEDIGREE_GENERATIONS, $OLD_PGENS, $talloffset, $PEDIGREE_LAYOUT;
	global $show_full;
	global $spouselinks, $parentlinks, $step_parentlinks, $persons, $person_step, $person_parent;
	global $natdad, $natmom, $censyear;

	if (empty($show_full)) $show_full = 0;
	if (empty($PEDIGREE_FULL_DETAILS)) $PEDIGREE_FULL_DETAILS = 0;

	if (!isset($OLD_PGENS)) $OLD_PGENS = $DEFAULT_PEDIGREE_GENERATIONS;
	if (!isset($talloffset)) $talloffset = $PEDIGREE_LAYOUT;

	$person=WT_Individual::getInstance($pid);

	$tmp = array('M'=>'','F'=>'F', 'U'=>'NN');
	$isF = $tmp[$person->getSex()];
	$spouselinks      = '';
	$parentlinks      = '';
	$step_parentlinks = '';

	if ($person->canShowName()) {
		//-- draw a box for the family popup

		if ($TEXT_DIRECTION=="rtl") {
			$spouselinks .= "<table class=\"rtlnav person_box$isF\"><tr><td align=\"right\" style=\"font-size:10px;font-weight:normal;\" class=\"name2 nowrap\">";
			$spouselinks .= "<b>" . WT_I18N::translate('Family') . "</b> (" .$person->getFullName(). ")<br>";
			$parentlinks .= "<table class=\"rtlnav person_box$isF\"><tr><td align=\"right\" style=\"font-size:10px;font-weight:normal;\" class=\"name2 nowrap\">";
			$parentlinks .= "<b>" . WT_I18N::translate('Parents') . "</b> (" .$person->getFullName(). ")<br>";
			$step_parentlinks .= "<table class=\"rtlnav person_box$isF\"><tr><td align=\"right\" style=\"font-size:10px;font-weight:normal;\" class=\"name2 nowrap\">";
			$step_parentlinks .= "<b>" . WT_I18N::translate('Parents') . "</b> (" .$person->getFullName(). ")<br>";
		} else {
			$spouselinks .= "<table class=\"ltrnav person_box$isF\"><tr><td align=\"left\" style=\"font-size:10px;font-weight:normal;\" class=\"name2 nowrap\">";
			$spouselinks .= "<b>" . WT_I18N::translate('Family') . "</b> (" .$person->getFullName(). ")<br>";
			$parentlinks .= "<table class=\"ltrnav person_box$isF\"><tr><td align=\"left\" style=\"font-size:10px;font-weight:normal;\" class=\"name2 nowrap\">";
			$parentlinks .= "<b>" . WT_I18N::translate('Parents') . "</b> (" .$person->getFullName(). ")<br>";
			$step_parentlinks .= "<table class=\"ltrnav person_box$isF\"><tr><td align=\"left\" style=\"font-size:10px;font-weight:normal;\" class=\"name2 nowrap\">";
			$step_parentlinks .= "<b>" . WT_I18N::translate('Parents') . "</b> (" .$person->getFullName(). ")<br>";
		}

		$persons       = '';
		$person_parent = '';
		$person_step   = '';

		// Parent families
		foreach ($person->getChildFamilies() as $family) {
			$husb = $family->getHusband();
			$wife = $family->getWife();
			$children = $family->getChildren();

			// Get Parent Children’s Name, DOB, DOD
			$marrdate = $family->getMarriageDate();
			if ($children) {
				$chBLDarray = Array();
				foreach ($children as $child) {
					$chnam   = $child->getAllNames();
					$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
					$chfulln = str_replace('"', "", $chfulln); // Must remove quotes completely here
					$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
					$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
					$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2;
					$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2;
					$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
					array_push($chBLDarray, $chBLD);
				}
			}

			// Parent Husband
			if ($husb || $children) {
				if ($husb) {
					// Parent Husbands Parents
					$gparent=WT_Individual::getInstance($husb->getXref());
					$parfams = $gparent->getChildFamilies();
					foreach ($parfams as $pfamily) {
						$phusb = $pfamily->getHusband();
						$pwife = $pfamily->getWife();
						if ($phusb) { $pHusbFBP = $phusb->getBirthPlace(); }
						if ($pwife) { $pHusbMBP = $pwife->getBirthPlace(); }
					}

					// Parent Husbands Details
					$person_parent = 'Yes';
					if ($husb->canShowName()) {
						$nam   = $husb->getAllNames();
						$fulln = rtrim($nam[0]['givn'],'*') . ' ' . $nam[0]['surname'];
						$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
						$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
						for ($i=0; $i<count($nam); $i++) {
							if ($nam[$i]['type']=='_MARNM') {
								$fulmn = rtrim($nam[$i]['givn'],'*') . ' ' . $nam[$i]['surname'];
							}
						}
						$parentlinks .= "<a class=\"linka\" href=\"#\" onclick=\"insertRowToTable(";
						$parentlinks .= "'".$husb->getXref()."',"; // pid = PID
						$parentlinks .= "'".WT_Filter::escapeHtml(strip_tags($fulln))."',"; // nam = Name
						if (isset($fulmn)) {
							$parentlinks .= "'".WT_Filter::escapeHtml(strip_tags($fulln))."',"; // mnam = Full Married Name
						} else {
							$parentlinks .= "'".WT_Filter::escapeHtml(strip_tags($fulln))."',"; // mnam = Full Name
						}
						if ($currpid=="Wife" || $currpid=="Husband") {
							$parentlinks .= "'Father in Law',"; // label = 1st Gen Male Relationship
						} else {
							$parentlinks .= "'Grand-Father',"; // label = 2st Gen Male Relationship
						}
						$parentlinks .= "'".$husb->getSex()."',"; // sex = Gender
						$parentlinks .= "''".","; // cond = Condition (Married etc)
						if ($marrdate) {
							$parentlinks .= "'".(($marrdate->minJD()+$marrdate->maxJD())/2)."',"; // dom = Date of Marriage (Julian)
						}
						$parentlinks .= "'".(($husb->getBirthDate()->minJD()+$husb->getBirthDate()->maxJD())/2)."',"; // dob = Date of Birth
						if ($husb->getbirthyear()>=1) {
							$parentlinks .= "'".($censyear-$husb->getbirthyear())."',"; // age =  Census Year - Year of Birth
						} else {
							$parentlinks .= "''".","; // age =  Undefined
						}
						$parentlinks .= "'".(($husb->getDeathDate()->minJD()+$husb->getDeathDate()->maxJD())/2)."',"; // dod = Date of Death
						$parentlinks .= "''".","; // occu  = Occupation
						$parentlinks .= "'".WT_Filter::escapeHtml($husb->getBirthPlace())."'".","; // birthpl = Individuals Birthplace
						if (isset($pHusbFBP)) {
							$parentlinks .= "'".WT_Filter::escapeHtml($pHusbFBP)."'".","; // fbirthpl = Fathers Birthplace
						} else {
							$parentlinks .= "'UNK, UNK, UNK, UNK'".","; // fbirthpl = Fathers Birthplace
						}
						if (isset($pHusbMBP)) {
							$parentlinks .= "'".WT_Filter::escapeHtml($pHusbMBP)."'".","; // mbirthpl = Mothers Birthplace
						} else {
							$parentlinks .= "'UNK, UNK, UNK, UNK'".","; // mbirthpl = Mothers Birthplace
						}
						if (isset($chBLDarray) && $husb->getSex()=="F") {
							$chBLDarray = implode("::", $chBLDarray);
							$parentlinks .= "'".$chBLDarray."'"; // Array of Children (name, birthdate, deathdate)
						} else {
							$parentlinks .= "''";
						}
						$parentlinks .= ");\">";
						$parentlinks .= $husb->getFullName();
						$parentlinks .= "</a>";
					} else {
						$parentlinks .= WT_I18N::translate('Private');
					}
					$natdad = "yes";
				}
			}

			// Parent Wife
			if ($wife || $children) {
				if ($wife) {
					// Parent Wifes Parents
					$gparent = WT_Individual::getInstance($wife->getXref());
					$parfams = $gparent->getChildFamilies();
					foreach ($parfams as $pfamily) {
						$pwhusb = $pfamily->getHusband();
						$pwwife = $pfamily->getWife();
						if ($pwhusb) { $pWifeFBP = $pwhusb->getBirthPlace(); }
						if ($pwwife) { $pWifeMBP = $pwwife->getBirthPlace(); }
					}

					// Parent Wifes Details
					$person_parent="Yes";
					if ($wife->canShowName()) {
						$nam   = $wife->getAllNames();
						$fulln = rtrim($nam[0]['givn'],'*') . ' ' . $nam[0]['surname'];
						$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
						$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);

						for ($i=0; $i<count($nam); $i++) {
							if ($nam[$i]['type']=='_MARNM') {
								$fulmn = rtrim($nam[$i]['givn'],'*') . ' ' . $nam[$i]['surname'];
							}
						}

						$parentlinks .= "<a class=\"linka\" href=\"#\" onclick=\"insertRowToTable(";
						$parentlinks .= "'".$wife->getXref()."',"; // pid = PID
						$parentlinks .= "'".WT_Filter::escapeHtml(strip_tags($fulln))."',";
						if (isset($fulmn)) {
							$parentlinks .= "'".WT_Filter::escapeHtml(strip_tags($fulmn))."',";
						} else {
							$parentlinks .= "'".WT_Filter::escapeHtml(strip_tags($fulln))."',";
						}
						if ($currpid=="Wife" || $currpid=="Husband") {
							$parentlinks .= "'Mother in Law',"; // label = 1st Gen Female Relationship
						} else {
							$parentlinks .= "'Grand-Mother',"; // label = 2st Gen Female Relationship
						}
						$parentlinks .= "'".$wife->getSex()."',"; // sex = Gender
						$parentlinks .= "''".","; // cond = Condition (Married etc)
						if ($marrdate) {
							$parentlinks .= "'".(($marrdate->minJD()+$marrdate->maxJD())/2)."',"; // dom = Date of Marriage (Julian)
						}
						$parentlinks .= "'".(($wife->getBirthDate()->minJD()+$wife->getBirthDate()->maxJD())/2)."',"; // dob = Date of Birth
						if ($wife->getbirthyear()>=1) {
							$parentlinks .= "'".($censyear-$wife->getbirthyear())."',"; // age =  Census Year - Year of Birth
						} else {
							$parentlinks .= "''".","; // age =  Undefined
						}
						$parentlinks .= "'".(($wife->getDeathDate()->minJD()+$wife->getDeathDate()->maxJD())/2)."',"; // dod = Date of Death
						$parentlinks .= "''".","; // occu  = Occupation
						$parentlinks .= "'".WT_Filter::escapeHtml($wife->getBirthPlace())."'".","; // birthpl = Individuals Birthplace
						if (isset($pWifeFBP)) {
							$parentlinks .= "'".WT_Filter::escapeHtml($pWifeFBP)."'".","; // fbirthpl = Fathers Birthplace
						} else {
							$parentlinks .= "'UNK, UNK, UNK, UNK'".","; // fbirthpl = Fathers Birthplace Not Known
						}
						if (isset($pWifeMBP)) {
							$parentlinks .= "'".WT_Filter::escapeHtml($pWifeMBP)."'".","; // mbirthpl = Mothers Birthplace
						} else {
							$parentlinks .= "'UNK, UNK, UNK, UNK'".","; // mbirthpl = Mothers Birthplace Not Known
						}
						if (isset($chBLDarray) && $wife->getSex()=="F") {
							$chBLDarray = implode("::", $chBLDarray);
							$parentlinks .= "'".$chBLDarray."'"; // Array of Children (name, birthdate, deathdate)
						} else {
							$parentlinks .= "''";
						}
						$parentlinks .= ");\">";
						$parentlinks .= $wife->getFullName();
						$parentlinks .= "</a>";
					} else {
						$parentlinks .= WT_I18N::translate('Private');
					}
					$natmom = 'yes';
				}
			}
		}

		// Step families
		foreach ($person->getChildStepFamilies() as $family) {
			$husb = $family->getHusband();
			$wife = $family->getWife();
			$children = $family->getChildren();
			$marrdate = $family->getMarriageDate();

			// Get StepParent’s Children’s Name, DOB, DOD
			if (isset($children)) {
				$chBLDarray = Array();
				foreach ($children as $child) {
					$chnam   = $child->getAllNames();
					$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
					$chfulln = str_replace('"', "", $chfulln); // Must remove quotes completely here
					$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
					$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
					$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2;
					$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2;
					$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
					array_push($chBLDarray, $chBLD);
				}
			}

			// Step Husband
			if ($natdad == 'yes') {
			} else {
				if (($husb || $children) && $husb !== $person) {
					if ($husb) {
						// Step Husbands Parents
						$gparent=WT_Individual::getInstance($husb->getXref());
						$parfams = $gparent->getChildFamilies();
						foreach ($parfams as $pfamily) {
							$phusb = $pfamily->getHusband();
							$pwife = $pfamily->getWife();
							if ($phusb) { $pHusbFBP = $phusb->getBirthPlace(); }
							if ($pwife) { $pHusbMBP = $pwife->getBirthPlace(); }
						}
						//-- Step Husband Details ------------------------------
						$person_step = 'Yes';
						if ($husb->canShowName()) {
							$nam   = $husb->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*') . ' ' . $nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*') . ' ' . $nam[$i]['surname'];
								}
							}
							$parentlinks .= "<a class=\"linka\" href=\"#\" onclick=\"insertRowToTable(";
							$parentlinks .= "'".$husb->getXref()."',"; // pid = PID
							$parentlinks .= "'".WT_Filter::escapeHtml(strip_tags($fulln))."',"; // nam = Name
							if (isset($fulmn)) {
								$parentlinks .= "'".WT_Filter::escapeHtml(strip_tags($fulln))."',"; // mnam = Full Married Name
							} else {
								$parentlinks .= "'".WT_Filter::escapeHtml(strip_tags($fulln))."',"; // mnam = Full Name
							}
							if ($currpid=="Wife" || $currpid=="Husband") {
								$parentlinks .= "'Step Father-in-Law',"; // label = 1st Gen Male Relationship
							} else {
								$parentlinks .= "'Step Grand-Father',"; // label = 2st Gen Male Relationship
							}
							$parentlinks .= "'".$husb->getSex()."',"; // sex = Gender
							$parentlinks .= "''".","; // cond = Condition (Married etc)
							if ($marrdate) {
								$parentlinks .= "'".(($marrdate->minJD()+$marrdate->maxJD())/2)."',"; // dom = Date of Marriage (Julian)
							}
							$parentlinks .= "'".(($husb->getBirthDate()->minJD()+$husb->getBirthDate()->maxJD())/2)."',"; // dob = Date of Birth
							if ($husb->getbirthyear()>=1) {
								$parentlinks .= "'".($censyear-$husb->getbirthyear())."',"; // age =  Census Year - Year of Birth
							} else {
								$parentlinks .= "''".","; // age =  Undefined
							}
							$parentlinks .= "'".(($husb->getDeathDate()->minJD()+$husb->getDeathDate()->maxJD())/2)."',"; // dod = Date of Death
							$parentlinks .= "''".","; // occu  = Occupation
							$parentlinks .= "'".WT_Filter::escapeHtml($husb->getBirthPlace())."'".","; // birthpl = Individuals Birthplace
							if (isset($pHusbFBP)) {
								$parentlinks .= "'".WT_Filter::escapeHtml($pHusbFBP)."'".","; // fbirthpl = Fathers Birthplace
							} else {
								$parentlinks .= "'UNK, UNK, UNK, UNK'".","; // fbirthpl = Fathers Birthplace
							}
							if (isset($pHusbMBP)) {
								$parentlinks .= "'".WT_Filter::escapeHtml($pHusbMBP)."'".","; // mbirthpl = Mothers Birthplace
							} else {
								$parentlinks .= "'UNK, UNK, UNK, UNK'".","; // mbirthpl = Mothers Birthplace
							}
							if (isset($chBLDarray) && $husb->getSex()=="F") {
								$chBLDarray = implode("::", $chBLDarray);
								$parentlinks .= "'".$chBLDarray."'"; // Array of Children (name, birthdate, deathdate)
							} else {
								$parentlinks .= "''";
							}
							$parentlinks .= ");\">";
							$parentlinks .= $husb->getFullName(); // Full Name (Link)
							$parentlinks .= "</a>";
						} else {
							$parentlinks .= WT_I18N::translate('Private');
						}
					}
				}
			}

			// Step Wife
			if ($natmom == 'yes') {
			} else {
				// Wife
				if ($wife || $children) {
					if ($wife) {
						// Step Wifes Parents
						$gparent=WT_Individual::getInstance($wife->getXref());
						$parfams = $gparent->getChildFamilies();
						foreach ($parfams as $pfamily) {
							$pwhusb = $pfamily->getHusband();
							$pwwife = $pfamily->getWife();
							if ($pwhusb) { $pWifeFBP = $pwhusb->getBirthPlace(); }
							if ($pwwife) { $pWifeMBP = $pwwife->getBirthPlace(); }
						}
						// Step Wife Details
						$person_step = 'Yes';
						if ($wife->canShowName()) {
							$nam   = $wife->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*') . ' ' . $nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);

							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*') . ' ' . $nam[$i]['surname'];
								}
							}

							$parentlinks .= "<a class=\"linka\" href=\"#\" onclick=\"insertRowToTable(";
							$parentlinks .= "'".$wife->getXref()."',"; // pid = PID
							$parentlinks .= "'".WT_Filter::escapeHtml(strip_tags($fulln))."',"; // nam = Name
							if (isset($fulmn)) {
								$parentlinks .= "'".WT_Filter::escapeHtml(strip_tags($fulmn))."',"; // mnam = Full Married Name
							} else {
								$parentlinks .= "'".WT_Filter::escapeHtml(strip_tags($fulln))."',"; // mnam = Full Name
							}
							if ($currpid=="Wife" || $currpid=="Husband") {
								$parentlinks .= "'Step Mother-in-Law',"; // label = 1st Gen Female Relationship
							} else {
								$parentlinks .= "'Step Grand-Mother',"; // label = 2st Gen Female Relationship
							}
							$parentlinks .= "'".$wife->getSex()."',"; // sex = Gender
							$parentlinks .= "''".","; // cond = Condition (Married etc)
							if ($marrdate) {
								$parentlinks .= "'".(($marrdate->minJD()+$marrdate->maxJD())/2)."',"; // dom = Date of Marriage (Julian)
							}
							$parentlinks .= "'".(($wife->getBirthDate()->minJD()+$wife->getBirthDate()->maxJD())/2)."',"; // dob = Date of Birth
							if ($wife->getbirthyear()>=1) {
								$parentlinks .= "'".($censyear-$wife->getbirthyear())."',"; // age =  Census Year - Year of Birth
							} else {
								$parentlinks .= "''".","; // age =  Undefined
							}
							$parentlinks .= "'".(($wife->getDeathDate()->minJD()+$wife->getDeathDate()->maxJD())/2)."',"; // dod = Date of Death
							$parentlinks .= "''".","; // occu  = Occupation
							$parentlinks .= "'".WT_Filter::escapeHtml($wife->getBirthPlace())."'".","; // birthpl = Individuals Birthplace
							if (isset($pWifeFBP)) {
								$parentlinks .= "'".WT_Filter::escapeHtml($pWifeFBP)."'".","; // fbirthpl = Fathers Birthplace
							} else {
								$parentlinks .= "'UNK, UNK, UNK, UNK'".","; // fbirthpl = Fathers Birthplace Not Known
							}
							if (isset($pWifeMBP)) {
								$parentlinks .= "'".WT_Filter::escapeHtml($pWifeMBP)."'".","; // mbirthpl = Mothers Birthplace
							} else {
								$parentlinks .= "'UNK, UNK, UNK, UNK'".","; // mbirthpl = Mothers Birthplace Not Known
							}
							if (isset($chBLDarray) && $wife->getSex()=="F") {
								$chBLDarray = implode("::", $chBLDarray);
								$parentlinks .= "'".$chBLDarray."'"; // Array of Children (name, birthdate, deathdate)
							} else {
								$parentlinks .= "''";
							}
							$parentlinks .= ");\">";
							$parentlinks .= $wife->getFullName(); // Full Name (Link)
							$parentlinks .= "</a>";
						} else {
							$parentlinks .= WT_I18N::translate('Private');
						}
					}
				}
			}
		}

		// Spouse Families
		foreach ($person->getSpouseFamilies() as $family) {
			$spouse = $family->getSpouse($person);
			$children = $family->getChildren();

			//-- Get Spouse’s Children’s Name, DOB, DOD --------------------------
			$marrdate = $family->getMarriageDate();
			$is_wife = $family->getWife();
			if (isset($children)) {
				$chBLDarray = Array();
				foreach ($children as $child) {
					$chnam   = $child->getAllNames();
					$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
					$chfulln = str_replace('"', "", $chfulln); // Must remove quotes completely here
					$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
					$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln); // Child’s Full Name
					$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2; // Child’s Date of Birth (Julian)
					$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2; // Child’s Date of Death (Julian)
					$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
					array_push($chBLDarray, $chBLD);
				}
			}

			// Spouse
			if ($spouse || $children) {
				if ($spouse) {

					// Spouse Parents
					$gparent=WT_Individual::getInstance($spouse->getXref());
					$spousefams = $gparent->getChildFamilies();
					foreach ($spousefams as $pfamily) {
						$phusb = $pfamily->getHusband();
						$pwife = $pfamily->getWife();
						if ($phusb) { $pSpouseFBP = $phusb->getBirthPlace(); }
						if ($pwife) { $pSpouseMBP = $pwife->getBirthPlace(); }
					}

					// Spouse Details
					if ($spouse->canShowName()) {
						$nam   = $spouse->getAllNames();
						$fulln = rtrim($nam[0]['givn'],'*') . ' ' . $nam[0]['surname'];
						$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
						$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);

						// If spouse is a wife, then get her married name or default to her birth name
						for ($i=0; $i<count($nam); $i++) {
							if ($nam[$i]['type']=='_MARNM' && $is_wife) {
								$fulmn = rtrim($nam[$i]['givn'],'*') . ' ' . $nam[$i]['surname'];
							} else {
								$fulmn = $fulln;
							}
						}

						$spouselinks .= "<a href=\"#\" onclick=\"insertRowToTable(";
						$spouselinks .= "'".$spouse->getXref()."',"; // pid = PID
						$spouselinks .= "'".WT_Filter::escapeHtml(strip_tags($fulln))."',";
						if (isset($fulmn)) {
							$spouselinks .= "'".WT_Filter::escapeHtml(strip_tags($fulmn))."',";
						} else {
							$spouselinks .= "'".WT_Filter::escapeHtml(strip_tags($fulln))."',";
						}
						if ($currpid=="Son" || $currpid=="Daughter") {
							if ($spouse->getSex()=="M") {
								$spouselinks .= "'Son in Law',"; // label = Male Relationship
							} else {
								$spouselinks .= "'Daughter in Law',"; // label = Female Relationship
							}
						} else {
							if ($spouse->getSex()=="M") {
								$spouselinks .= "'Brother in Law',"; // label = Male Relationship
							} else {
								$spouselinks .= "'Sister in Law',"; // label = Female Relationship
							}
						}
						$spouselinks .= "'".$spouse->getSex()."',"; // sex = Gender
						$spouselinks .= "''".","; // cond = Condition (Married etc)
						if ($marrdate) {
							$spouselinks .= "'".(($marrdate->minJD()+$marrdate->maxJD())/2)."',";
						}
						$spouselinks .= "'".(($spouse->getBirthDate()->minJD()+$spouse->getBirthDate()->maxJD())/2)."',";
						if ($spouse->getbirthyear()>=1) {
							$spouselinks .= "'".($censyear-$spouse->getbirthyear())."',"; // age =  Census Year - Year of Birth
						} else {
							$spouselinks .= "''".","; // age =  Undefined
						}
						$spouselinks .= "'".(($spouse->getDeathDate()->minJD()+$spouse->getDeathDate()->maxJD())/2)."',"; // dod = Date of Death
						$spouselinks .= "''".","; // occu  = Occupation
						$spouselinks .= "'".WT_Filter::escapeHtml($spouse->getBirthPlace())."'".","; // birthpl = Individuals Birthplace
						if (isset($pSpouseFBP)) {
							$spouselinks .= "'".WT_Filter::escapeHtml($pSpouseFBP)."'".","; // fbirthpl = Fathers Birthplace
						} else {
							$spouselinks .= "'UNK, UNK, UNK, UNK'".","; // fbirthpl = Fathers Birthplace Not Known
						}
						if (isset($pSpouseMBP)) {
							$spouselinks .= "'".WT_Filter::escapeHtml($pSpouseMBP)."'".","; // mbirthpl = Mothers Birthplace
						} else {
							$spouselinks .= "'UNK, UNK, UNK, UNK'".","; // mbirthpl = Mothers Birthplace Not Known
						}
						if (isset($chBLDarray) && $spouse->getSex()=="F") {
							$chBLDarray = implode("::", $chBLDarray);
							$spouselinks .= "'".$chBLDarray."'"; // Array of Children (name, birthdate, deathdate)
						} else {
							$spouselinks .= "''";
						}
						$spouselinks .= ");\">";
						$spouselinks .= $spouse->getFullName(); // Full Name
						$spouselinks .= "</a>";
					} else {
						$spouselinks .= WT_I18N::translate('Private');
					}
					$spouselinks .= "</a>";
					if ($spouse->getFullName() != "") {
						$persons = "Yes";
					}
				}
			}

			// Children
			$spouselinks .= "<ul class=\"clist\">";
			foreach ($children as $child) {
				$persons="Yes";

				// Child’s Parents
				$gparent=WT_Individual::getInstance($child->getXref());
				foreach ($gparent->getChildFamilies() as $family) {
					$husb = $family->getHusband();
					$wife = $family->getWife();
					if ($husb) { $ChildFBP = $husb->getBirthPlace(); }
					if ($wife) { $ChildMBP = $wife->getBirthPlace(); }
				}

				// Child’s Children
				$chBLDarray=Array();
				foreach ($child->getSpouseFamilies() as $childfamily) {
					$chchildren = $childfamily->getChildren();
					foreach ($chchildren as $chchild) {
						$chnam   = $chchild->getAllNames();
						$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
						$chfulln = str_replace('"', "", $chfulln); // Must remove quotes completely here
						$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
						$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln); // Child’s Full Name
						$chdob   = ($chchild->getBirthDate()->minJD()+$chchild->getBirthDate()->maxJD())/2; // Child’s Date of Birth (Julian)
						$chdod   = ($chchild->getDeathDate()->minJD()+$chchild->getDeathDate()->maxJD())/2; // Child’s Date of Death (Julian)
						$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
						array_push($chBLDarray, $chBLD);
					}
				}

				// Child’s marriage status
				$marrdate = '';
				$chhusbnam = null;
				foreach ($child->getSpouseFamilies() as $childfamily) {
					$marrdate = $childfamily->getMarriageDate();
					if ($childfamily->getHusband()) {
						$chhusbnam = $childfamily->getHusband()->getAllNames();
					}
				}
				// Childs Details -------------------------
				$spouselinks .= '<li>';
				if ($child->canShowName()) {
					$nam   = $child->getAllNames();
					$fulln = rtrim($nam[0]['givn'],'*') . ' ' . $nam[0]['surname'];
					$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
					$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);

					// Get childs married name if available
					$chfulmn=null;
					$chnam = $child->getAllNames();
					if ($chhusbnam[0]['surname']=="@N.N." || $chhusbnam[0]['surname']=="") {
						// if Husband or his name is not known then use wifes birth name
						$husbnam = $nam[0]['surname'];
					} else {
						$husbnam = $chhusbnam[0]['surname'];
					}
					for ($i=0; $i<count($nam); $i++) {
						if ($chnam[$i]['type']=='_MARNM') {
							$chfulmn = rtrim($chnam[$i]['givn'],'*') . ' ' . $husbnam;
						}
					}

					$spouselinks .= "<a href=\"#\" onclick=\"insertRowToTable(";
					$spouselinks .= "'".$child->getXref()."',";
					$spouselinks .= "'".WT_Filter::escapeHtml(strip_tags($fulln))."',"; // nam = Name
					if (isset($chfulmn)) {
						$spouselinks .= "'".WT_Filter::escapeHtml(strip_tags($chfulmn))."',"; // mnam = Full Married Name
					} else {
						$spouselinks .= "'".WT_Filter::escapeHtml(strip_tags($fulln))."',"; // mnam = Full Name
					}
					if ($currpid=="Son" || $currpid=="Daughter") {
						if ($child->getSex()=="M") {
							$spouselinks .= "'Grand-Son',"; // label = Male Relationship
						} else {
							$spouselinks .= "'Grand-Daughter',"; // label = Female Relationship
						}
					} else {
						if ($child->getSex()=="M") {
							$spouselinks .= "'Nephew',"; // label = Male Relationship
						} else {
							$spouselinks .= "'Niece',"; // label = Female Relationship
						}
					}
					$spouselinks .= "'".$child->getSex()."',"; // sex = Gender
					$spouselinks .= "''".","; // cond = Condition (Married etc)
					if ($marrdate) {
						$spouselinks .= "'".(($marrdate->minJD()+$marrdate->maxJD())/2)."',"; // dom = Date of Marriage (Julian)
					} else {
						$spouselinks .= "'nm'".",";
					}
					$spouselinks .= "'".(($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2)."',"; // dob = Date of Birth
					if ($child->getbirthyear()>=1) {
						$spouselinks .= "'".($censyear-$child->getbirthyear())."',"; // age =  Census Year - Year of Birth
					} else {
						$spouselinks .= "''".","; // age =  Undefined
					}
					$spouselinks .= "'".(($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2)."',"; // dod = Date of Death
					$spouselinks .= "''".","; // occu  = Occupation
					$spouselinks .= "'".WT_Filter::escapeHtml($child->getBirthPlace())."'".","; // birthpl = Individuals Birthplace
					if (isset($ChildFBP)) {
						$spouselinks .= "'".WT_Filter::escapeHtml($ChildFBP)."'".","; // fbirthpl = Fathers Birthplace
					} else {
						$spouselinks .= "'UNK, UNK, UNK, UNK'".","; // fbirthpl = Fathers Birthplace Not Known
					}
					if (isset($ChildMBP)) {
						$spouselinks .= "'".WT_Filter::escapeHtml($ChildMBP)."'".","; // mbirthpl = Mothers Birthplace
					} else {
						$spouselinks .= "'UNK, UNK, UNK, UNK'".","; // mbirthpl = Mothers Birthplace Not Known
					}
					if (isset($chBLDarray) && $child->getSex()=="F") {
						$chBLDarray = implode("::", $chBLDarray);
						$spouselinks .= "'".$chBLDarray."'"; // Array of Children (name, birthdate, deathdate)
					} else {
						$spouselinks .= "''";
					}
					$spouselinks .= ");\">";
					$spouselinks .= $child->getFullName();
					$spouselinks .= "</a>";
					$spouselinks .= "</li>";
				} else {
					$spouselinks .= WT_I18N::translate('Private');
				}
			}
			$spouselinks .= "</ul>";
		}
		if ($persons != 'Yes') {
			$spouselinks  .= '(' . WT_I18N::translate('none') . ')</td></tr></table>';
		} else {
			$spouselinks  .= '</td></tr></table>';
		}

		if ($person_parent != 'Yes') {
			$parentlinks .= '(' . WT_I18N::translate_c('unknown family', 'unknown') . ')</td></tr></table>';
		} else {
			$parentlinks .= '</td></tr></table>';
		}

		if ($person_step != 'Yes') {
			$step_parentlinks .= '(' . WT_I18N::translate_c('unknown family', 'unknown') . ')</td></tr></table>';
		} else {
			$step_parentlinks .= '</td></tr></table>';
		}
	}
}

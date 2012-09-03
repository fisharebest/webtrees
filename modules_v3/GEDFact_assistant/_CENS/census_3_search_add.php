<?php
// Census Assistant Control module for webtrees
//
// Census Search and Add Area File
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2007 to 2010  PGV Development Team.  All rights reserved.
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
?>


	<table id="navenclose" class="optionbox" width="100%">
		<?php

		//-- Search Function ------------------------------------------------------------
		?>
		<tr>
			<td align="center" class="descriptionbox"><font size=1><?php echo WT_I18N::translate('Add people'); ?></font></td>
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
								"module.php?mod=GEDFact_assistant&mod_action=_CENS/census_3_find&callback=paste_id&action=filter&filter="+txt, "win02", "resizable=1, menubar=0, scrollbars=1, top=180, left=600, HEIGHT=400, WIDTH=450 ");
							if (window.focus) {win02.focus();}
						}
					}
				</script>
				<?php
				echo "<input id=personid type=\"text\" size=\"20\" STYLE=\"color: #000000;\" value=\"\">";
				echo "<a href=\"#\" onclick=\"findindi()\">" ;
				echo "&nbsp;<font size=\"2\">&nbsp;".WT_I18N::translate('Search')."</font>";
				echo '</a>';
				?>
			</td>
		</tr>
		<tr>
			<td style="border: 0px solid transparent;">
				<br>
			</td>
		</tr>

				<?php
				//-- Add Family Members to Census  -------------------------------------------
				global $spouselinks, $parentlinks, $DeathYr, $BirthYr;
				?>

				<tr>
					<td align="center" style="border: 0px solid transparent;">
						<table width="100%" class="fact_table" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan=3 class="descriptionbox">
								<font size=1>
								<?php
								// Header text with "Head" button =================================================
								$headImg  = '<i class="headimg vmiddle icon-button_head"></i>';
								$headImg2 = '<i class="headimg2 vmiddle icon-button_head" title="'.WT_I18N::translate('Click to choose person as Head of family.').'"></i>';
								echo WT_I18N::translate('Click %s to choose person as Head of family.', $headImg);
								?>
								</font>
								</td>
							</tr>

							<tr>
								<td>
									<font size=1><br></font>
								</td>
							</tr>

					<?php

					//-- Parents Family ---------------------------------------------------

					//-- Build Parents Family --------------------------------------
					$personcount=0;
					$families = $this->record->getChildFamilies();
					foreach ($families as $famid=>$family) {
						$label = $this->record->getChildFamilyLabel($family);
						$people = $this->buildFamilyList($family, "parents", false);
						$marrdate = $family->getMarriageDate();

						//-- Get Parents Children's Name, DOB, DOD --------------------------
						if (isset($people["children"])) {
							$chBLDarray = Array();
							foreach ($people["children"] as $child) {
								$chnam   = $child->getAllNames();
								$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
								$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
								$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
								$chfulln = addslashes($chfulln); // Child's Full Name
								$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2; // Child's Date of Birth (Julian)
								$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2; // Child's Date of Death (Julian)
								$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
								array_push($chBLDarray, $chBLD);
							}
						}

						//-- Parents Husband -------------------
						if (isset($people["husb"])) {

							//-- Parents Husbands Parents --------------------------------------
							$gparent=WT_Person::getInstance($people["husb"]->getXref());
							$fams = $gparent->getChildFamilies();
							foreach ($fams as $famid=>$family) {
								if (!is_null($family)) {
									$phusb = $family->getHusband($gparent);
									$pwife = $family->getWife($gparent);
								}
								if ($phusb) { $HusbFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $HusbMBP = $pwife->getBirthPlace(); }
							}

							//-- Parents Husbands Details --------------------------------------
							$married = WT_Date::Compare($censdate, $marrdate);
							$nam     = $people["husb"]->getAllNames();
							$fulln   = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln   = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln   = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn    = rtrim($nam[0]['givn'],'*');
							$surn    = $nam[0]['surname'];
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*')."&nbsp;".$nam[$i]['surname'];
								}									
							}
							$menu = new WT_Menu($people["husb"]->getLabel());
							$slabel  = print_pedigree_person_nav2($people["husb"]->getXref(), 2, 0, $personcount++, $people["husb"]->getLabel(), $censdate);
							$slabel .= $parentlinks;
							$submenu = new WT_Menu($slabel);
							$menu->addSubMenu($submenu);

							?>
							<tr>
								<td align="left" class="linkcell optionbox" width="25%">
									<font size=1>
										<?php echo $menu->getMenu(); ?>
									</font>
								</td>
								<td align="left" class="facts_value" style="text-decoration:none;" >
									<font size=1>
										<?php
										echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;pid=".$people["husb"]->getXref()."&amp;gedcom=".WT_GEDURL."\">";
										echo $headImg2;
										echo "</a>";
										?>
									</font>
								</td>
								<td align="left" class="facts_value nowrap">
									<font size=1>
									<?php
									if (($people["husb"]->canDisplayDetails())) {
									?>
									<a href='javaScript:insertRowToTable("<?php
											echo $people["husb"]->getXref() ; // pid = PID
										?>", "<?php
											echo addslashes($fulln); // nam = Full Name
										?>", "<?php
											if (isset($fulmn)) {
												echo addslashes($fulln); // mnam = Full Married Name
											} else {
												echo addslashes($fulln); // mnam = Full Name
											}
										?>", "<?php
											echo $people["husb"]->getLabel(); // label = Relationship
										?>", "<?php
											echo $people["husb"]->getSex(); // gend = Gender
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
											echo ($people["husb"]->getBirthDate()->minJD()+$people["husb"]->getBirthDate()->maxJD())/2; // dob = Date of Birth (Julian)
										?>", "<?php
											echo $censyear-$people["husb"]->getbirthyear(); // age = Census Date minus YOB
										?>", "<?php
											echo ($people["husb"]->getDeathDate()->minJD()+$people["husb"]->getDeathDate()->maxJD())/2; // dod = Date of Death (Julian)
										?>", "<?php
											echo ""; // occu = Occupation
										?>", "<?php
											echo htmlspecialchars($people["husb"]->getBirthPlace(), ENT_QUOTES); //  birthpl = Husband Place of Birth
										?>", "<?php
											if (isset($HusbFBP)) {
												echo htmlspecialchars($HusbFBP, ENT_QUOTES); // fbirthpl = Husband Father's Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Husband Father's Place of Birth Not known
											}
										?>", "<?php
											if (isset($HusbMBP)) {
												echo htmlspecialchars($HusbMBP, ENT_QUOTES); // mbirthpl = Husband Mother's Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Husband Mother's Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $people["husb"]->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											}
										?>");'>
										<?php
											echo $people["husb"]->getFullName();  // Full Name (Link)
										?>
									</a>
									<?php
									} else {
										echo WT_I18N::translate('Private');
									}
									?>
									</font>
								</td>
							</tr>
							<?php
						}

						//-- Parents Wife ---------------------------------------------------------
						if (isset($people["wife"])) {

							//-- Parents Wifes Parent Family ---------------------------
							$gparent=WT_Person::getInstance($people["wife"]->getXref());
							$fams = $gparent->getChildFamilies();
							foreach ($fams as $famid=>$family) {
								if (!is_null($family)) {
									$phusb = $family->getHusband($gparent);
									$pwife = $family->getWife($gparent);
								}
								if ($phusb) { $WifeFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $WifeMBP = $pwife->getBirthPlace(); }
							}

							//-- Wifes Details --------------------------------------
							$married = WT_Date::Compare($censdate, $marrdate);
							$nam     = $people["wife"]->getAllNames();
							$fulln   = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln   = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln   = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn    = rtrim($nam[0]['givn'],'*');
							$surn    = $nam[0]['surname'];
							$husbnam = null;
							// Get wifes married name if available
							if (isset($people["husb"])) {
								$husbnams = $people["husb"]->getAllNames();
								if ($husbnams[0]['surname']=="@N.N." || $husbnams[0]['surname']=="") {
									// if Husband or his name is not known then use wifes birth name
									$husbnam = $nam[0]['surname'];
								} else {
									$husbnam = $husbnams[0]['surname'];
								}
							}
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*')."&nbsp;".$husbnam;
								}									
							}
							$menu = new WT_Menu($people["wife"]->getLabel());
							$slabel  = print_pedigree_person_nav2($people["wife"]->getXref(), 2, 0, $personcount++, $people["wife"]->getLabel(), $censyear);
							$slabel .= $parentlinks;
							$submenu = new WT_Menu($slabel);
							$menu->addSubMenu($submenu);
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<font size=1>
										<?php echo $menu->getMenu(); ?>
									</font>
								</td>
								<td align="left" class="facts_value">
									<font size=1>
										<?php
										echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;pid=".$people["wife"]->getXref()."&amp;gedcom=".WT_GEDURL."\">";
										echo $headImg2;
										echo "</a>";
										?>
									</font>
								</td>
								<td align="left" class="facts_value nowrap">
									<font size=1>
									<?php
									if (($people["wife"]->canDisplayDetails())) {
										?>
									<a href='javaScript:insertRowToTable2("<?php
											echo $people["wife"]->getXref() ;  // pid = PID
										?>", "<?php
											echo addslashes($fulln); // nam = Full Name
										?>", "<?php
											if (isset($fulmn)) {
												echo addslashes($fulmn); // mnam = Full Married Name
											} else {
												echo addslashes($fulln); // mnam = Full Name
											}
										?>", "<?php
											echo $people["wife"]->getLabel(); // label = Relationship
										?>", "<?php
											echo $people["wife"]->getSex(); // gend = Gender
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
											echo ($people["wife"]->getBirthDate()->minJD()+$people["wife"]->getBirthDate()->maxJD())/2;    // dob = Date of Birth (Julian)
										?>", "<?php
											echo $censyear-$people["wife"]->getbirthyear(); // age = Census Date minus YOB
										?>", "<?php
											echo ($people["wife"]->getDeathDate()->minJD()+$people["wife"]->getDeathDate()->maxJD())/2; // dod = Date of Death (Julian)
										?>", "<?php
											echo ""; // occu = Occupation
										?>", "<?php
											echo htmlspecialchars($people["wife"]->getBirthPlace(), ENT_QUOTES); //  birthpl = Wife Place of Birth
										?>", "<?php
											if (isset($WifeFBP)) {
												echo htmlspecialchars($WifeFBP, ENT_QUOTES); // fbirthpl = Wife Father's Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Wife Father's Place of Birth Not known
											}
										?>", "<?php
											if (isset($WifeMBP)) {
												echo htmlspecialchars($WifeMBP, ENT_QUOTES); // mbirthpl = Wife Mother's Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Wife Mother's Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $people["wife"]->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											}
										?>");'>
										<?php
											echo $people["wife"]->getFullName();  // Full Name (Link)
										?>
									</a>
									<?php
									} else {
										echo WT_I18N::translate('Private');
									}
									?>
									</font>
								</td>
							</tr>
							<?php
						}

						//-- Parents Children -------------------
						if (isset($people["children"])) {

							//-- Parent's Children's Details --------------------------------------
							$elderdate = $family->getMarriageDate();
							foreach ($people["children"] as $child) {

								// Get Child's Children's Name DOB DOD ----
								$chBLDarray=Array();
								foreach ($child->getSpouseFamilies() as $childfamily) {
									$chchildren = $childfamily->getChildren();
									foreach ($chchildren as $chchild) {
										$chnam   = $chchild->getAllNames();
										$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
										$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
										$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
										$chfulln = addslashes($chfulln); // Child's Full Name// Child's Full Name
										$chdob   = ($chchild->getBirthDate()->minJD()+$chchild->getBirthDate()->maxJD())/2; // Child's Date of Birth (Julian)
										$chdod   = ($chchild->getDeathDate()->minJD()+$chchild->getDeathDate()->maxJD())/2; // Child's Date of Death (Julian)
										$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
										array_push($chBLDarray, $chBLD);
									}
								}

								// Get child's marriage status ----
								$married="";
								$marrdate="";
								foreach ($child->getSpouseFamilies() as $childfamily) {
									$marrdate=$childfamily->getMarriageDate();
									$married = WT_Date::Compare($censdate, $marrdate);
								}
								$nam   = $child->getAllNames();
								$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
								$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
								$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
								$givn  = rtrim($nam[0]['givn'],'*');
								$surn  = $nam[0]['surname'];
								$chfulmn=null;								
								$chnam = $child->getAllNames();
								for ($i=0; $i<count($nam); $i++) {
									if ($chnam[$i]['type']=='_MARNM') {
										$chfulmn = rtrim($chnam[$i]['givn'],'*')."&nbsp;".$chnam[$i]['surname'];									
									}									
								}

								$menu = new WT_Menu($child->getLabel());
								$slabel  = print_pedigree_person_nav2($child->getXref(), 2, 0, $personcount++, $child->getLabel(), $censyear);
								$slabel .= $spouselinks;
								$submenu = new WT_Menu($slabel);
								$menu->addSubMenu($submenu);

								if ($child->getXref()==$pid) {
									//Only print Head of Family in Immediate Family Block
								} else {
									?>
									<tr>
										<td align="left" class="linkcell optionbox">
											<font size=1>
											<?php
											if ($child->getXref()==$pid) {
												echo $child->getLabel();
											} else {
												echo $menu->getMenu();
											}
											?>
											</font>
										</td>
										<td align="left" class="facts_value">
											<font size=1>
												<?php
												echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;pid=".$child->getXref()."&amp;gedcom=".WT_GEDURL."\">";
												echo $headImg2;
												echo "</a>";
												?>
											</font>
										</td>
										<td align="left" class="facts_value nowrap">
											<font size=1>
											<?php
											if (($child->canDisplayDetails())) {
												?>
												<a href='javaScript:insertRowToTable("<?php
														echo $child->getXref(); // pid = PID
													?>", "<?php
														echo addslashes($fulln); // nam = Full Name
													?>", "<?php
														if (isset($chfulmn)) {
															echo addslashes($chfulmn); // mnam = Full Married Name
														} else {
															echo addslashes($fulln); // mnam = Full Name
														}
													?>", "<?php
														if ($child->getXref()==$pid) {
															echo "Head"; // label = Head
														} else {
															echo $child->getLabel(); // label = Relationship
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
														echo htmlspecialchars($child->getBirthPlace(), ENT_QUOTES); //  birthpl = Child Place of Birt
													?>", "<?php
														if (isset($people["husb"])) {
															echo htmlspecialchars($people["husb"]->getBirthPlace(), ENT_QUOTES); // fbirthpl = Child Father's Place of Birth
														} else {
															echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Child Father's Place of Birth Not known
														}
													?>", "<?php
														if (isset($people["wife"])) {
															echo htmlspecialchars($people["wife"]->getBirthPlace(), ENT_QUOTES); // mbirthpl = Child Mother's Place of Birth
														} else {
															echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Child Mother's Place of Birth Not known
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
											</font>
										</td>
									</tr>
									<?php
								}
							}
							$elderdate = $child->getBirthDate(false);
						}
					}

					//-- Step families ---------------------------------------------------------

					//-- Build step families ---------------------------------------------------
					foreach ($this->record->getChildStepFamilies() as $famid=>$family) {
						$label = $this->record->getStepFamilyLabel($family);
						$people = $this->buildFamilyList($family, "step-parents", false);
						if ($people) {
							echo "<tr><td><br></td><td></td></tr>";
						}
						$marrdate = $family->getMarriageDate();

						//-- Get Children's Name, DOB, DOD --------------------------
						if (isset($people["children"])) {
							$chBLDarray = Array();
							foreach ($people["children"] as $child) {
								$chnam   = $child->getAllNames();
								$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
								$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
								$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
								$chfulln = addslashes($chfulln); // Child's Full Name
								$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2; // Child's Date of Birth (Julian)
								$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2; // Child's Date of Death (Julian)
								$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
								array_push($chBLDarray, $chBLD);
							}
						}

						// Step Husband -----------------------------
						$elderdate = "";
						if (isset($people["husb"])) {

							//-- Step Husbands Parent Family --------------------------------------
							$gparent=WT_Person::getInstance($people["husb"]->getXref());
							$fams = $gparent->getChildFamilies();
							foreach ($fams as $famid=>$family) {
								if (!is_null($family)) {
									$phusb = $family->getHusband($gparent);
									$pwife = $family->getWife($gparent);
								}
								if ($phusb) { $HusbFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $HusbMBP = $pwife->getBirthPlace(); }
							}

							//-- Step Husbands Details --------------------------------------
							$married = WT_Date::Compare($censdate, $marrdate);
							$nam   = $people["husb"]->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn  = rtrim($nam[0]['givn'],'*');
							$surn  = $nam[0]['surname'];
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*')."&nbsp;".$nam[$i]['surname'];
								}									
							}
							$menu = new WT_Menu();
							if ($people["husb"]->getLabel() == ".") {
								$menu->addLabel(WT_I18N::translate_c('mother\'s husband', 'step-father'));
							} else {
								$menu->addLabel($people["husb"]->getLabel());
							}
							$slabel  = print_pedigree_person_nav2($people["husb"]->getXref(), 2, 0, $personcount++, $people["husb"]->getLabel(), $censyear);
							$slabel .= $parentlinks;
							$submenu = new WT_Menu($slabel);
							$menu->addSubMenu($submenu);
							if ($people["husb"]->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $people["husb"]->getDeathYear(); }
							if ($people["husb"]->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $people["husb"]->getBirthYear(); }
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<font size=1>
										<?php echo $menu->getMenu(); ?>
									</font>
								</td>
								<td align="left" class="facts_value">
									<font size=1>
										<?php
										echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;pid=".$people["husb"]->getXref()."&amp;gedcom=".WT_GEDURL."\">";
										echo $headImg2;
										echo "</a>";
										?>
									</font>
								</td>
								<td align="left" class="facts_value nowrap">
									<font size=1>
									<?php
									if (($people["husb"]->canDisplayDetails())) {
									?>
									<a href='javaScript:insertRowToTable("<?php
											echo $people["husb"]->getXref(); // pid = PID
										?>", "<?php
											echo addslashes($fulln); // nam = Full Name
										?>", "<?php
											if (isset($fulmn)) {
												echo addslashes($fulln); // mnam = Full Married Name
											} else {
												echo addslashes($fulln); // mnam = Full Name
											}
										?>", "<?php
										if ($people["husb"]->getLabel() == ".") {
											echo WT_I18N::translate_c('mother\'s husband', 'step-father'); // label = Relationship
										} else {
											echo $people["husb"]->getLabel(); // label = Relationship
										}
										?>", "<?php
											echo $people["husb"]->getSex(); // gend = Gender
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
											echo ($people["husb"]->getBirthDate()->minJD()+$people["husb"]->getBirthDate()->maxJD())/2; // dob = Date of Birth (Julian)
										?>", "<?php
											echo $censyear-$people["husb"]->getbirthyear(); // age = Census Date minus YOB
										?>", "<?php
											echo ($people["husb"]->getDeathDate()->minJD()+$people["husb"]->getDeathDate()->maxJD())/2; // dod = Date of Death (Julian)
										?>", "<?php
											echo ""; // occu = Occupation
										?>", "<?php
											echo htmlspecialchars($people["husb"]->getBirthPlace(), ENT_QUOTES); //  birthpl = Step Husband Place of Birth
										?>", "<?php
											if (isset($HusbFBP)) {
												echo htmlspecialchars($HusbFBP, ENT_QUOTES); // fbirthpl = Step Husband Father's Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Step Husband Father's Place of Birth Not known
											}
										?>", "<?php
											if (isset($HusbMBP)) {
												echo htmlspecialchars($HusbMBP, ENT_QUOTES); // mbirthpl = Step Husband Mother's Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Step Husband Mother's Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $people["husb"]->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											}
										?>");'>
										<?php
											echo $people["husb"]->getFullName();  // Full Name (Link)
										?>
									</a>
									<?php
									} else {
										echo WT_I18N::translate('Private');
									}
									?>
									</font>
								</td>
							</tr>
							<?php
							$elderdate = $people["husb"]->getBirthDate(false);
						}

						// Step Wife -------------------
						if (isset($people["wife"])) {

							//-- Step Wifes Parent Family --------------------------------------
							$gparent=WT_Person::getInstance($people["wife"]->getXref());
							$fams = $gparent->getChildFamilies();
							foreach ($fams as $famid=>$family) {
								if (!is_null($family)) {
									$phusb = $family->getHusband($gparent);
									$pwife = $family->getWife($gparent);
								}
								if ($phusb) { $WifeFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $WifeMBP = $pwife->getBirthPlace(); }
							}

							//-- Step Wifes Details --------------------------------------
							$married = WT_Date::Compare($censdate, $marrdate);
							$nam   = $people["wife"]->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn  = rtrim($nam[0]['givn'],'*');
							$surn  = $nam[0]['surname'];
							$husbnam = null;
							// Get wifes married name if available
							if (isset($people["husb"])) {
								$husbnams = $people["husb"]->getAllNames();
								if ($husbnams[0]['surname']=="@N.N." || $husbnams[0]['surname']=="") {
									// if Husband or his name is not known then use wifes birth name
									$husbnam = $nam[0]['surname'];
								} else {
									$husbnam = $husbnams[0]['surname'];
								}
							}
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*')."&nbsp;".$husbnam;
								}									
							}
							$menu = new WT_Menu();
							if ($people["wife"]->getLabel() == ".") {
								$menu->addLabel(WT_I18N::translate_c('father\'s wife', 'step-mother'));
							} else {
								$menu->addLabel($people["wife"]->getLabel());
							}
							$slabel  = print_pedigree_person_nav2($people["wife"]->getXref(), 2, 0, $personcount++, $people["wife"]->getLabel(), $censyear);
							$slabel .= $parentlinks;
							$submenu = new WT_Menu($slabel);
							$menu->addSubMenu($submenu);
							if ($people["wife"]->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $people["wife"]->getDeathYear(); }
							if ($people["wife"]->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $people["wife"]->getBirthYear(); }
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<font size=1>
										<?php echo $menu->getMenu(); ?>
									</font>
								</td>
								<td align="left" class="facts_value">
									<font size=1>
										<?php
										echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;pid=".$people["wife"]->getXref()."&amp;gedcom=".WT_GEDURL."\">";
										echo $headImg2;
										echo "</a>";
										?>
									</font>
								</td>
								<td align="left" class="facts_value nowrap">
									<font size=1>
									<?php
									if (($people["wife"]->canDisplayDetails())) {
									?>
									<a href='javaScript:insertRowToTable("<?php
											echo $people["wife"]->getXref() ; // pid = PID
										?>", "<?php
											echo addslashes($fulln); // nam = Full Name
										?>", "<?php
											if (isset($fulmn)) {
												echo addslashes($fulmn); // mnam = Full Married Name
											} else {
												echo addslashes($fulln); // mnam = Full Name
											}
										?>", "<?php
										if ($people["wife"]->getLabel() == ".") {
											echo WT_I18N::translate_c('father\'s wife', 'step-mother'); // label = Relationship
										} else {
											echo $people["wife"]->getLabel(); // label = Relationship
										}
										?>", "<?php
											echo $people["wife"]->getSex(); // gend = Gender
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
											echo ($people["wife"]->getBirthDate()->minJD()+$people["wife"]->getBirthDate()->maxJD())/2; // dob = Date of Birth (Julian)
										?>", "<?php
											echo $censyear-$people["wife"]->getbirthyear(); // age = Census Date minus YOB
										?>", "<?php
											echo ($people["wife"]->getDeathDate()->minJD()+$people["wife"]->getDeathDate()->maxJD())/2; // dod = Date of Death (Julian)
										?>", "<?php
											echo ""; // occu = Occupation
										?>", "<?php
											echo htmlspecialchars($people["wife"]->getBirthPlace(), ENT_QUOTES); //  birthpl = Step Wife Place of Birth
										?>", "<?php
											if (isset($WifeFBP)) {
												echo htmlspecialchars($WifeFBP, ENT_QUOTES); // fbirthpl = Step Wife Father's Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Step Wife Father's Place of Birth Not known
											}
										?>", "<?php
											if (isset($WifeMBP)) {
												echo htmlspecialchars($WifeMBP, ENT_QUOTES); // mbirthpl = Step Wife Mother's Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Step Wife Mother's Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $people["wife"]->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											}
										?>");'>
										<?php
											echo $people["wife"]->getFullName();  // Full Name (Link)
										?>
									</a>
									<?php
									} else {
										echo WT_I18N::translate('Private');
									}
									?>
									</font>
								</td>
							</tr>
							<?php
						}

						// Step Children ---------------------
						if (isset($people["children"])) {
							$elderdate = $family->getMarriageDate();
							foreach ($people["children"] as $child) {

								// Get Child's Children
								$chBLDarray=Array();
								foreach ($child->getSpouseFamilies() as $childfamily) {
									$chchildren = $childfamily->getChildren();
									foreach ($chchildren as $chchild) {
										$chnam   = $chchild->getAllNames();
										$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
										$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
										$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
										$chfulln = addslashes($chfulln); // Child's Full Name
										$chdob   = ($chchild->getBirthDate()->minJD()+$chchild->getBirthDate()->maxJD())/2; // Child's Date of Birth (Julian)
										$chdod   = ($chchild->getDeathDate()->minJD()+$chchild->getDeathDate()->maxJD())/2; // Child's Date of Death (Julian)
										$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
										array_push($chBLDarray, $chBLD);
									}
								}

								$nam   = $child->getAllNames();
								$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
								$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
								$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
								$givn  = rtrim($nam[0]['givn'],'*');
								$surn  = $nam[0]['surname'];
								$chfulmn=null;
								$chnam = $child->getAllNames();
								for ($i=0; $i<count($nam); $i++) {
									if ($chnam[$i]['type']=='_MARNM') {
										$chfulmn = rtrim($chnam[$i]['givn'],'*')."&nbsp;".$chnam[$i]['surname'];									
									}									
								}
								$menu = new WT_Menu($child->getLabel());
								$slabel  = print_pedigree_person_nav2($child->getXref(), 2, 0, $personcount++, $child->getLabel(), $censyear);
								$slabel .= $spouselinks;
								$submenu = new WT_Menu($slabel);
								$menu->addSubMenu($submenu);
								if ($child->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $child->getDeathYear(); }
								if ($child->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $child->getBirthYear(); }
								?>
								<tr>
									<td align="left" class="linkcell optionbox">
										<font size=1>
											<?php echo $menu->getMenu(); ?>
										</font>
									</td>
									<td align="left" class="facts_value">
										<font size=1>
											<?php
										echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;pid=".$child->getXref()."&amp;gedcom=".WT_GEDURL."\">";
											echo $headImg2;
											echo "</a>";
											?>
										</font>
									</td>
									<td align="left" class="facts_value nowrap">
										<font size=1>
										<?php
										if (($child->canDisplayDetails())) {
										?>
										<a href='javaScript:insertRowToTable("<?php
												echo $child->getXref() ; // pid = PID
											?>", "<?php
												echo addslashes($fulln); // nam = Full Name
											?>", "<?php
												if (isset($chfulmn)) {
													echo addslashes($chfulmn); // mnam = Full Married Name
												} else {
													echo addslashes($fulln); // mnam = Full Name
												}
											?>", "<?php
												echo $child->getLabel(); // label = Relationship
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
												echo htmlspecialchars($child->getBirthPlace(), ENT_QUOTES); //  birthpl = Child Place of Birth
											?>", "<?php
												if (isset($people["husb"])) {
													echo htmlspecialchars($people["husb"]->getBirthPlace(), ENT_QUOTES); // fbirthpl = Child Father's Place of Birth
												} else {
													echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Child Father's Place of Birth Not known
												}
											?>", "<?php
												if (isset($people["wife"])) {
													echo htmlspecialchars($people["wife"]->getBirthPlace(), ENT_QUOTES); // mbirthpl = Child Mother's Place of Birth
												} else {
													echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Child Mother's Place of Birth Not known
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
										</font>
									</td>
								</tr>
								<?php
								//$elderdate = $child->getBirthDate(false);
							}
						}
					}

					echo "<tr><td><font size=1><br></font></td></tr>";

					//-- Build Spouse Family ---------------------------------------------------
					$families = $this->record->getSpouseFamilies();
					//$personcount = 0;
					foreach ($families as $family) {
						$people = $this->buildFamilyList($family, "spouse", false);
						if ($this->record->equals($people["husb"])) {
							$spousetag = 'WIFE';
						} else {
							$spousetag = 'HUSB';
						}
						$marrdate = $family->getMarriageDate();

						//-- Get Children's Name, DOB, DOD --------------------------
						if (isset($people["children"])) {
							$chBLDarray = Array();
							foreach ($people["children"] as $child) {
								$chnam   = $child->getAllNames();
								$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
								$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
								$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
								$chfulln = addslashes($chfulln); // Child's Full Name
								$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2; // Child's Date of Birth (Julian)
								$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2; // Child's Date of Death (Julian)
								$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
								array_push($chBLDarray, $chBLD);
							}
						}

						//-- Spouse Husband ---------------------------------------------------
						if (isset($people["husb"])) {

							//-- Spouse Husbands Parents --------------------------------------
							$gparent=WT_Person::getInstance($people["husb"]->getXref());
							$fams = $gparent->getChildFamilies();
							foreach ($fams as $family) {
								if (!is_null($family)) {
									$phusb = $family->getHusband($gparent);
									$pwife = $family->getWife($gparent);
								}
								if ($phusb) { $HusbFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $HusbMBP = $pwife->getBirthPlace(); }
							}

							//-- Spouse Husbands Details --------------------------------------
							$married = WT_Date::Compare($censdate, $marrdate);
							$nam     = $people["husb"]->getAllNames();
							$fulln   = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln   = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln   = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn    = rtrim($nam[0]['givn'],'*');
							$surn    = $nam[0]['surname'];
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*')."&nbsp;".$nam[$i]['surname'];
								}									
							}
							$menu = new WT_Menu($people["husb"]->getLabel());
							$slabel  = print_pedigree_person_nav2($people["husb"]->getXref(), 2, 0, $personcount++, $people["husb"]->getLabel(), $censyear);
							$slabel .= $parentlinks;
							$submenu = new WT_Menu($slabel);
							$menu->addSubMenu($submenu);
							if ($people["husb"]->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $people["husb"]->getDeathYear(); }
							if ($people["husb"]->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $people["husb"]->getBirthYear(); }
							?>
							<tr class="fact_value">
								<td align="left" class="linkcell optionbox nowrap">
									<font size=1>
										<?php
										if ($people["husb"]->getXref()==$pid) {
											echo "&nbsp" .($people["husb"]->getLabel());
										} else {
											echo $menu->getMenu();
										}
										?>
									</font>
								</td>
								<td align="left" class="facts_value">
									<font size=1>
										<?php
										echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;pid=".$people["husb"]->getXref()."&amp;gedcom=".WT_GEDURL."\">";
										echo $headImg2;
										echo "</a>";
										?>
									</font>
								</td>
								<td align="left" class="facts_value nowrap">
									<font size=1>
									<?php
									if (($people["husb"]->canDisplayDetails())) {
									?>
									<a href='javaScript:insertRowToTable("<?php
											echo $people["husb"]->getXref() ; // pid = PID
										?>", "<?php
											echo addslashes($fulln); // nam = Full Name
										?>", "<?php
											if (isset($fulmn)) {
												echo addslashes($fulln); // mnam = Full Married Name
											} else {
												echo addslashes($fulln); // mnam = Full Name
											}
										?>", "<?php
											if ($people["husb"]->getXref()==$pid) {
												echo "Head"; // label = Relationship
											} else {
												echo $people["husb"]->getLabel(); // label = Relationship
											}
										?>", "<?php
											echo $people["husb"]->getSex(); // gend = Gender
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
											echo ($people["husb"]->getBirthDate()->minJD()+$people["husb"]->getBirthDate()->maxJD())/2; // dob = Date of Birth (Julian)
										?>", "<?php
											echo $censyear-$people["husb"]->getbirthyear(); // age = Census Date minus YOB
										?>", "<?php
											echo ($people["husb"]->getDeathDate()->minJD()+$people["husb"]->getDeathDate()->maxJD())/2; // dod = Date of Death (Julian)
										?>", "<?php
											echo ""; // occu = Occupation
										?>", "<?php
											echo htmlspecialchars($people["husb"]->getBirthPlace(), ENT_QUOTES); //  birthpl = Husband Place of Birth
										?>", "<?php
											if (isset($HusbFBP)) {
												echo htmlspecialchars($HusbFBP, ENT_QUOTES); // fbirthpl = Husband Father's Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Husband Father's Place of Birth Not known
											}
										?>", "<?php
											if (isset($HusbMBP)) {
												echo htmlspecialchars($HusbMBP, ENT_QUOTES); // mbirthpl = Husband Mother's Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Husband Mother's Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $people["husb"]->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											}
										?>");'>
										<?php
											echo $people["husb"]->getFullName();  // Full Name (Link)
										?>
									</a>
									<?php
									} else {
										echo WT_I18N::translate('Private');
										}
										?>
									</font>
								</td>
							<tr>
							<?php
						}

						//-- Spouse Wife -----------------------------------------------------
						if (isset($people["wife"])) {

							//-- Spouse Wifes Parents --------------------------------------
							$gparent=WT_Person::getInstance($people["wife"]->getXref());
							$fams = $gparent->getChildFamilies();
							foreach ($fams as $family) {
								if (!is_null($family)) {
									$husb = $family->getHusband($gparent);
									$wife = $family->getWife($gparent);
								}
								if ($husb) { $WifeFBP = $husb->getBirthPlace(); }
								if ($wife) { $WifeMBP = $wife->getBirthPlace(); }
							}

							//-- Spouse Wifes Details --------------------------------------
							$married = WT_Date::Compare($censdate, $marrdate);
							$nam     = $people["wife"]->getAllNames();
							$fulln   = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							//$fulln   = str_replace('"', '\"', $fulln);
							$fulln   = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln   = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn    = rtrim($nam[0]['givn'],'*');
							$surn    = $nam[0]['surname'];
							$husbnam = null;
							// Get wifes married name if available
							if (isset($people["husb"])) {
								$husbnams = $people["husb"]->getAllNames();
								if ($husbnams[0]['surname']=="@N.N." || $husbnams[0]['surname']=="") {
									// if Husband or his name is not known then use wifes birth name
									$husbnam = $nam[0]['surname'];
								} else {
									$husbnam = $husbnams[0]['surname'];
								}
							}
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*')."&nbsp;".$husbnam;
								}									
							}
							$menu = new WT_Menu($people["wife"]->getLabel());
							$slabel  = print_pedigree_person_nav2($people["wife"]->getXref(), 2, 0, $personcount++, $people["wife"]->getLabel(), $censyear);
							$slabel .= $parentlinks;
							$submenu = new WT_Menu($slabel);
							$menu->addSubMenu($submenu);
							if ($people["wife"]->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $people["wife"]->getDeathYear(); }
							if ($people["wife"]->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $people["wife"]->getBirthYear(); }
							?>
							<tr>
								<td align="left" class="linkcell optionbox nowrap">
									<font size=1>
										<?php
										if ($people["wife"]->getXref()==$pid) {
											echo "&nbsp" .($people["wife"]->getLabel());
										} else {
											echo $menu->getMenu();
										}
										?>
									</font>
								</td>
								<td align="left" class="facts_value">
									<font size=1>
										<?php
										echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;pid=".$people["wife"]->getXref()."&amp;gedcom=".WT_GEDURL."\">";
										echo $headImg2;
										echo "</a>";
										?>
									</font>
								</td>
								<td align="left" class="facts_value nowrap">
									<font size=1>
									<?php
									if (($people["wife"]->canDisplayDetails())) {
									?>
										<a href='javaScript:insertRowToTable("<?php
												echo $people["wife"]->getXref() ; // pid = PID
										?>", "<?php
											echo addslashes($fulln); // nam = Full Name
										?>", "<?php
											if (isset($fulmn)) {
												echo addslashes($fulmn); // mnam = Full Married Name
											} else {
												echo addslashes($fulln); // mnam = Full Name
											}
										?>", "<?php
											if ($people["wife"]->getXref()==$pid) {
												echo "Head"; // label = Head
											} else {
												echo $people["wife"]->getLabel(); // label = Relationship
											}
										?>", "<?php
											echo $people["wife"]->getSex(); // gend = Gender
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
											echo ($people["wife"]->getBirthDate()->minJD()+$people["wife"]->getBirthDate()->maxJD())/2; // dob = Date of Birth (Julian)
										?>", "<?php
											echo $censyear-$people["wife"]->getbirthyear(); // age = Census Date minus YOB
										?>", "<?php
											echo ($people["wife"]->getDeathDate()->minJD()+$people["wife"]->getDeathDate()->maxJD())/2; // dod = Date of Death (Julian)
										?>", "<?php
											echo ""; // occu = Occupation
										?>", "<?php
											echo htmlspecialchars($people["wife"]->getBirthPlace(), ENT_QUOTES); //  birthpl = Wife Place of Birth
										?>", "<?php
											if (isset($WifeFBP)) {
												echo htmlspecialchars($WifeFBP, ENT_QUOTES); // fbirthpl = Wife Father's Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Wife Father's Place of Birth Not known
											}
										?>", "<?php
											if (isset($WifeMBP)) {
												echo htmlspecialchars($WifeMBP, ENT_QUOTES); // mbirthpl = Wife Mother's Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Wife Mother's Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $people["wife"]->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											}
										?>");'>
										<?php
											echo $people["wife"]->getFullName();  // Full Name (Link)
										?>
										</a>
										<?php
									} else {
										echo WT_I18N::translate('Private');
									}
									?>
									</font>
								</td>
							<tr> <?php
						}

						// Spouse Children
						foreach ($people["children"] as $child) {

							// Get Spouse child's marriage status
							$married="";
							$marrdate="";
							foreach ($child->getSpouseFamilies() as $childfamily) {
								$marrdate=$childfamily->getMarriageDate();
								$married = WT_Date::Compare($censdate, $marrdate);
							}

							// Get Child's Children
							$chBLDarray=Array();
							foreach ($child->getSpouseFamilies() as $childfamily) {
								$chchildren = $childfamily->getChildren();
								foreach ($chchildren as $chchild) {
									$chnam   = $chchild->getAllNames();
									$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
									$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
									$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
									$chfulln = addslashes($chfulln); // Child's Full Name// Child's Full Name
									$chdob   = ($chchild->getBirthDate()->minJD()+$chchild->getBirthDate()->maxJD())/2; // Child's Date of Birth (Julian)
									$chdod   = ($chchild->getDeathDate()->minJD()+$chchild->getDeathDate()->maxJD())/2; // Child's Date of Death (Julian)
									$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
									array_push($chBLDarray, $chBLD);
								}
							}

							// Get Spouse child's details							
							$nam   = $child->getAllNames();							
							$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn  = rtrim($nam[0]['givn'],'*');
							$surn  = $nam[0]['surname'];							
							$chfulmn=null;
							$chnam = $child->getAllNames();
							for ($i=0; $i<count($nam); $i++) {
								if ($chnam[$i]['type']=='_MARNM') {
									$chfulmn = rtrim($chnam[$i]['givn'],'*')."&nbsp;".$chnam[$i]['surname'];									
								}									
							}
							$menu = new WT_Menu($child->getLabel());
							$slabel = print_pedigree_person_nav2($child->getXref(), 2, 0, $personcount++, $child->getLabel(), $censyear);
							$slabel .= $spouselinks;
							$submenu = new WT_Menu($slabel);
							$menu->addSubmenu($submenu);
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<font size=1>
										<?php echo $menu->getMenu(); ?>
									</font>
								</td>
								<td align="left" class="facts_value">
									<font size=1>
										<?php
										echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;pid=".$child->getXref()."&amp;gedcom=".WT_GEDURL."\">";
										echo $headImg2;
										echo "</a>";
										?>
									</font>
								</td>
								<td align="left" class="facts_value nowrap">
									<font size=1>
									<?php
									if (($child->canDisplayDetails())) {
									?>
									<a href='javaScript:insertRowToTable("<?php
											echo $child->getXref() ; // pid = PID
										?>", "<?php
											echo addslashes($fulln); // nam = Full Name
										?>", "<?php
											if (isset($chfulmn)) {
												echo addslashes($chfulmn); // mnam = Full Married Name
											} else {
												echo addslashes($fulln); // mnam = Full Name
											}
										?>", "<?php
											echo $child->getLabel(); // label = Relationship
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
											echo htmlspecialchars($child->getBirthPlace(), ENT_QUOTES); //  birthpl = Child Place of Birth
										?>", "<?php
											if (isset($people["husb"])) {
												echo htmlspecialchars($people["husb"]->getBirthPlace(), ENT_QUOTES); // fbirthpl = Child Father's Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Child Father's Place of Birth Not known
											}
										?>", "<?php
											if (isset($people["wife"])) {
												echo htmlspecialchars($people["wife"]->getBirthPlace(), ENT_QUOTES); // mbirthpl = Child Mother's Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Child Mother's Place of Birth Not known
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
									</font>
								</td>
							</tr>
							<?php
						}

					echo "<tr><td><font size=1><br></font></td></tr>";
					}
					?>

						</table>
					<br><br><br>&nbsp;</td>
				</tr>
			</table>

<?php
// ==================================================================

/**
 * print the information for an individual chart box
 *
 * find and print a given individuals information for a pedigree chart
 * @param string $pid the Gedcom Xref ID of the   to print
 * @param int $style the style to print the box in, 1 for smaller boxes, 2 for larger boxes
 * @param boolean $show_famlink set to true to show the icons for the popup links and the zoomboxes
 * @param int $count on some charts it is important to keep a count of how many boxes were printed
 */

function print_pedigree_person_nav2($pid, $style=1, $count=0, $personcount="1", $currpid, $censyear) {
	global $HIDE_LIVE_PEOPLE, $SHOW_LIVING_NAMES, $SCRIPT_NAME;
	global $SHOW_HIGHLIGHT_IMAGES, $bwidth, $bheight, $PEDIGREE_FULL_DETAILS, $SHOW_PEDIGREE_PLACES;
	global $TEXT_DIRECTION, $DEFAULT_PEDIGREE_GENERATIONS, $OLD_PGENS, $talloffset, $PEDIGREE_LAYOUT, $MEDIA_DIRECTORY;
	global $ABBREVIATE_CHART_LABELS, $USE_MEDIA_VIEWER;
	global $chart_style, $box_width, $generations, $show_spouse, $show_full;
	global $CHART_BOX_TAGS, $SHOW_LDS_AT_GLANCE, $PEDIGREE_SHOW_GENDER;
	global $SEARCH_SPIDER;

	global $spouselinks, $parentlinks, $step_parentlinks, $persons, $person_step, $person_parent, $tabno, $spousetag;
	global $natdad, $natmom, $censyear, $censdate;
	// global $pHusbFBP, $pHusbMBP, $pWifeFBP, $pWifeMBP;
	// global $phusb, $pwife, $pwhusb, $pwwife;

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

		$persons       = "";
		$person_parent = "";
		$person_step   = "";

		//-- Parent families --------------------------------------
		$fams = $person->getChildFamilies();
		foreach ($fams as $family) {
			$marrdate = $family->getMarriageDate();
			$married  = WT_Date::Compare($censdate, $marrdate);

			if (!is_null($family)) {
				$husb = $family->getHusband($person);
				$wife = $family->getWife($person);
				$children = $family->getChildren();
				$num = count($children);
				$marrdate = $family->getMarriageDate();

				//-- Get Parent Children's Name, DOB, DOD --------------------------
				if (isset($children)) {
					$chBLDarray = Array();
					foreach ($children as $child) {
						$chnam   = $child->getAllNames();
						$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
						$chfulln = str_replace('"', "", $chfulln); // Must remove quotes completely here
						$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
						$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln); // Child's Full Name
						$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2; // Child's Date of Birth (Julian)
						$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2; // Child's Date of Death (Julian)
						$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
						array_push($chBLDarray, $chBLD);
					}
				}

				//-- Parent Husband ------------------------------
				if ($husb || $num>0) {
					if ($husb) {
						//-- Parent Husbands Parents ----------------------
						$gparent=WT_Person::getInstance($husb->getXref());
						$parfams = $gparent->getChildFamilies();
						foreach ($parfams as $pfamily) {
							if (!is_null($pfamily)) {
								$phusb = $pfamily->getHusband($gparent);
								$pwife = $pfamily->getWife($gparent);
							}
							if ($phusb) { $pHusbFBP = $phusb->getBirthPlace(); }
							if ($pwife) { $pHusbMBP = $pwife->getBirthPlace(); }
						}
						//-- Parent Husbands Details ----------------------
						$person_parent="Yes";
						$tmp=$husb->getXref();
						if ($husb->canDisplayName()) {
							$nam   = $husb->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn  = rtrim($nam[0]['givn'],'*');
							$surn  = $nam[0]['surn'];
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*')."&nbsp;".$nam[$i]['surname'];
								}									
							}
							$parentlinks .= "<a class=\"linka\" href=\"#\" onclick=\"insertRowToTable(";
							$parentlinks .= "'".$husb->getXref()."',"; // pid = PID
							$parentlinks .= "'".addslashes(strip_tags($fulln))."',"; // nam = Name
							if (isset($fulmn)) {
								$parentlinks .= "'".addslashes(strip_tags($fulln))."',"; // mnam = Full Married Name
							} else {
								$parentlinks .= "'".addslashes(strip_tags($fulln))."',"; // mnam = Full Name
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
							$parentlinks .= "'".htmlspecialchars($husb->getBirthPlace(), ENT_QUOTES)."'".","; // birthpl = Individuals Birthplace
							if (isset($pHusbFBP)) {
								$parentlinks .= "'".htmlspecialchars($pHusbFBP, ENT_QUOTES)."'".","; // fbirthpl = Fathers Birthplace
							} else {
								$parentlinks .= "'UNK, UNK, UNK, UNK'".","; // fbirthpl = Fathers Birthplace
							}
							if (isset($pHusbMBP)) {
								$parentlinks .= "'".htmlspecialchars($pHusbMBP, ENT_QUOTES)."'".","; // mbirthpl = Mothers Birthplace
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
						$natdad = "yes";
					}
				}

				//-- Parent Wife ------------------------------
				if ($wife || $num>0) {
					if ($wife) {
						//-- Parent Wifes Parents ----------------------
						$gparent=WT_Person::getInstance($wife->getXref());
						$parfams = $gparent->getChildFamilies();
						foreach ($parfams as $pfamily) {
							if (!is_null($pfamily)) {
								$pwhusb = $pfamily->getHusband($gparent);
								$pwwife = $pfamily->getWife($gparent);
							}
							if ($pwhusb) { $pWifeFBP = $pwhusb->getBirthPlace(); }
							if ($pwwife) { $pWifeMBP = $pwwife->getBirthPlace(); }
						}
						//-- Parent Wifes Details ----------------------
						$person_parent="Yes";
						$tmp=$wife->getXref();
						if ($wife->canDisplayName()) {
							$married = WT_Date::Compare($censdate, $marrdate);
							$nam   = $wife->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn  = rtrim($nam[0]['givn'],'*');
							$surn  = $nam[0]['surname'];
							$husbnam = null;
							
							// Get wifes married name if available
							if (isset($husb)) {
								$husbnams = $husb->getAllNames();
								if ($husbnams[0]['surname']=="@N.N." || $husbnams[0]['surname']=="") {
									// Husband or his name is not known
								} else {
									$husbnam = $husb->getAllNames();
								}
							}
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM') {
									$fulmn = rtrim($nam[$i]['givn'],'*')."&nbsp;".$nam[$i]['surname'];
								}									
							}
							
							$parentlinks .= "<a class=\"linka\" href=\"#\" onclick=\"insertRowToTable(";
							$parentlinks .= "'".$wife->getXref()."',"; // pid = PID
							$parentlinks .= "'".addslashes(strip_tags($fulln))."',"; // nam = Name
							if (isset($fulmn)) {
								$parentlinks .= "'".addslashes(strip_tags($fulmn))."',"; // mnam = Full Married Name
							} else {
								$parentlinks .= "'".addslashes(strip_tags($fulln))."',"; // mnam = Full Name
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
							$parentlinks .= "'".htmlspecialchars($wife->getBirthPlace(), ENT_QUOTES)."'".","; // birthpl = Individuals Birthplace
							if (isset($pWifeFBP)) {
								$parentlinks .= "'".htmlspecialchars($pWifeFBP, ENT_QUOTES)."'".","; // fbirthpl = Fathers Birthplace
							} else {
								$parentlinks .= "'UNK, UNK, UNK, UNK'".","; // fbirthpl = Fathers Birthplace Not Known
							}
							if (isset($pWifeMBP)) {
								$parentlinks .= "'".htmlspecialchars($pWifeMBP, ENT_QUOTES)."'".","; // mbirthpl = Mothers Birthplace
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
						$natmom = "yes";
					}
				}
			}
		}

		//-- Step families -----------------------------------------
		$fams = $person->getChildStepFamilies();
		foreach ($fams as $family) {
			$marrdate = $family->getMarriageDate();
			$married  = WT_Date::Compare($censdate, $marrdate);
			if (!is_null($family)) {
				$husb = $family->getHusband($person);
				$wife = $family->getWife($person);
				$children = $family->getChildren();
				$num = count($children);
				$marrdate = $family->getMarriageDate();

				//-- Get StepParent's Children's Name, DOB, DOD --------------------------
				if (isset($children)) {
					$chBLDarray = Array();
					foreach ($children as $child) {
						$chnam   = $child->getAllNames();
						$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
						$chfulln = str_replace('"', "", $chfulln); // Must remove quotes completely here
						$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
						$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln); // Child's Full Name
						$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2; // Child's Date of Birth (Julian)
						$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2; // Child's Date of Death (Julian)
						$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
						array_push($chBLDarray, $chBLD);
					}
				}

				//-- Step Husband --------------------------------------
				if ($natdad == "yes") {
				} else {
					if (($husb || $num>0) && $husb->getLabel() != ".") {
						if ($husb) {
							//-- Step Husbands Parents -----------------------------
							$gparent=WT_Person::getInstance($husb->getXref());
							$parfams = $gparent->getChildFamilies();
							foreach ($parfams as $pfamily) {
								if (!is_null($pfamily)) {
									$phusb = $pfamily->getHusband($gparent);
									$pwife = $pfamily->getWife($gparent);
								}
								if ($phusb) { $pHusbFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $pHusbMBP = $pwife->getBirthPlace(); }
							}
							//-- Step Husband Details ------------------------------
							$person_step="Yes";
							$tmp=$husb->getXref();
							if ($husb->canDisplayName()) {
								$nam   = $husb->getAllNames();
								$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
								$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
								$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
								//$fulln = strip_tags($husb->getFullName());
								$givn  = rtrim($nam[0]['givn'],'*');
								$surn  = $nam[0]['surname'];
								for ($i=0; $i<count($nam); $i++) {
									if ($nam[$i]['type']=='_MARNM') {
										$fulmn = rtrim($nam[$i]['givn'],'*')."&nbsp;".$nam[$i]['surname'];
									}									
								}
								$parentlinks .= "<a class=\"linka\" href=\"#\" onclick=\"insertRowToTable(";
								$parentlinks .= "'".$husb->getXref()."',"; // pid = PID
								$parentlinks .= "'".addslashes(strip_tags($fulln))."',"; // nam = Name
								if (isset($fulmn)) {
									$parentlinks .= "'".addslashes(strip_tags($fulln))."',"; // mnam = Full Married Name
								} else {
									$parentlinks .= "'".addslashes(strip_tags($fulln))."',"; // mnam = Full Name
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
								$parentlinks .= "'".htmlspecialchars($husb->getBirthPlace(), ENT_QUOTES)."'".","; // birthpl = Individuals Birthplace
								if (isset($pHusbFBP)) {
									$parentlinks .= "'".htmlspecialchars($pHusbFBP, ENT_QUOTES)."'".","; // fbirthpl = Fathers Birthplace
								} else {
									$parentlinks .= "'UNK, UNK, UNK, UNK'".","; // fbirthpl = Fathers Birthplace
								}
								if (isset($pHusbMBP)) {
									$parentlinks .= "'".htmlspecialchars($pHusbMBP, ENT_QUOTES)."'".","; // mbirthpl = Mothers Birthplace
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

				//-- Step Wife ----------------------------------------
				if ($natmom == "yes") {
				} else {
					if ($wife || $num>0) {
						if ($wife) {
							//-- Step Wifes Parents ---------------------------
							$gparent=WT_Person::getInstance($wife->getXref());
							$parfams = $gparent->getChildFamilies();
							foreach ($parfams as $pfamily) {
								if (!is_null($pfamily)) {
									$pwhusb = $pfamily->getHusband($gparent);
									$pwwife = $pfamily->getWife($gparent);
								}
								if ($pwhusb) { $pWifeFBP = $pwhusb->getBirthPlace(); }
								if ($pwwife) { $pWifeMBP = $pwwife->getBirthPlace(); }
							}
							//-- Step Wife Details ------------------------------
							$person_step="Yes";
							$tmp=$wife->getXref();
							if ($wife->canDisplayName()) {
								$married = WT_Date::Compare($censdate, $marrdate);
								$nam   = $wife->getAllNames();
								$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
								$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
								$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
								//$fulln = strip_tags($wife->getFullName());
								$givn  = rtrim($nam[0]['givn'],'*');
								$surn  = $nam[0]['surname'];
								$husbnam = null;
								
								// Get wifes married name if available
								if (isset($husb)) {
									$husbnams = $husb->getAllNames();
									if ($husbnams[0]['surname']=="@N.N." || $husbnams[0]['surname']=="") {
										// Husband or his name is not known
									} else {
										$husbnam = $husb->getAllNames();
									}
								}
								for ($i=0; $i<count($nam); $i++) {
									if ($nam[$i]['type']=='_MARNM') {
										$fulmn = rtrim($nam[$i]['givn'],'*')."&nbsp;".$nam[$i]['surname'];
									}									
								}
								
								$parentlinks .= "<a class=\"linka\" href=\"#\" onclick=\"insertRowToTable(";
								$parentlinks .= "'".$wife->getXref()."',"; // pid = PID
								$parentlinks .= "'".addslashes(strip_tags($fulln))."',"; // nam = Name
								if (isset($fulmn)) {
									$parentlinks .= "'".addslashes(strip_tags($fulmn))."',"; // mnam = Full Married Name
								} else {
									$parentlinks .= "'".addslashes(strip_tags($fulln))."',"; // mnam = Full Name
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
								$parentlinks .= "'".htmlspecialchars($wife->getBirthPlace(), ENT_QUOTES)."'".","; // birthpl = Individuals Birthplace
								if (isset($pWifeFBP)) {
									$parentlinks .= "'".htmlspecialchars($pWifeFBP, ENT_QUOTES)."'".","; // fbirthpl = Fathers Birthplace
								} else {
									$parentlinks .= "'UNK, UNK, UNK, UNK'".","; // fbirthpl = Fathers Birthplace Not Known
								}
								if (isset($pWifeMBP)) {
									$parentlinks .= "'".htmlspecialchars($pWifeMBP, ENT_QUOTES)."'".","; // mbirthpl = Mothers Birthplace
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
		}

		// Spouse Families ------------------------------------------
		$fams = $person->getSpouseFamilies();
		foreach ($fams as $family) {
			if (!is_null($family)) {
				$spouse = $family->getSpouse($person);
				$children = $family->getChildren();
				$num = count($children);
				$marrdate = $family->getMarriageDate();
				$married  = WT_Date::Compare($censdate, $marrdate);						
				$is_wife = $family->getWife();
				
				//-- Get Spouse's Children's Name, DOB, DOD --------------------------
				if (isset($children)) {
					$chBLDarray = Array();
					foreach ($children as $child) {
						$chnam   = $child->getAllNames();
						$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
						$chfulln = str_replace('"', "", $chfulln); // Must remove quotes completely here
						$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
						$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln); // Child's Full Name
						$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2; // Child's Date of Birth (Julian)
						$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2; // Child's Date of Death (Julian)
						$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
						array_push($chBLDarray, $chBLD);
					}
				}
				
				//-- Spouse -----------------------------------------
				if ($spouse || $num>0) {
					if ($spouse) {

						//-- Spouse Parents -----------------------------
						$gparent=WT_Person::getInstance($spouse->getXref());
						$spousefams = $gparent->getChildFamilies();
						foreach ($spousefams as $pfamily) {
							if (!is_null($pfamily)) {
								$phusb = $pfamily->getHusband($gparent);
								$pwife = $pfamily->getWife($gparent);
							}
							if ($phusb) { $pSpouseFBP = $phusb->getBirthPlace(); }
							if ($pwife) { $pSpouseMBP = $pwife->getBirthPlace(); }
						}

						//-- Spouse Details -----------------------------
						$tmp=$spouse->getXref();
						if ($spouse->canDisplayName()) {
							$married = WT_Date::Compare($censdate, $marrdate);
							$nam   = $spouse->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn  = rtrim($nam[0]['givn'],'*');
							$surn  = $nam[0]['surname'];
							
							// If spouse is a wife, then get her married name or default to her birth name
							for ($i=0; $i<count($nam); $i++) {
								if ($nam[$i]['type']=='_MARNM' && $is_wife) {
									$fulmn = rtrim($nam[$i]['givn'],'*')."&nbsp;".$nam[$i]['surname'];
								} else {
									$fulmn = $fulln;
								}
							}

							$spouselinks .= "<a href=\"#\" onclick=\"insertRowToTable(";
							$spouselinks .= "'".$spouse->getXref()."',"; // pid = PID
							$spouselinks .= "'".addslashes(strip_tags($fulln))."',"; // nam = Name
							if (isset($fulmn)) {
								$spouselinks .= "'".addslashes(strip_tags($fulmn))."',"; // mnam = Full Married Name
							} else {
								$spouselinks .= "'".addslashes(strip_tags($fulln))."',"; // mnam = Full Name
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
								$spouselinks .= "'".(($marrdate->minJD()+$marrdate->maxJD())/2)."',"; // dom = Date of Marriage (Julian)
							}
							$spouselinks .= "'".(($spouse->getBirthDate()->minJD()+$spouse->getBirthDate()->maxJD())/2)."',"; // dob = Date of Birth
							if ($spouse->getbirthyear()>=1) {
								$spouselinks .= "'".($censyear-$spouse->getbirthyear())."',"; // age =  Census Year - Year of Birth
							} else {
								$spouselinks .= "''".","; // age =  Undefined
							}
							$spouselinks .= "'".(($spouse->getDeathDate()->minJD()+$spouse->getDeathDate()->maxJD())/2)."',"; // dod = Date of Death
							$spouselinks .= "''".","; // occu  = Occupation
							$spouselinks .= "'".htmlspecialchars($spouse->getBirthPlace(), ENT_QUOTES)."'".","; // birthpl = Individuals Birthplace
							if (isset($pSpouseFBP)) {
								$spouselinks .= "'".htmlspecialchars($pSpouseFBP, ENT_QUOTES)."'".","; // fbirthpl = Fathers Birthplace
							} else {
								$spouselinks .= "'UNK, UNK, UNK, UNK'".","; // fbirthpl = Fathers Birthplace Not Known
							}
							if (isset($pSpouseMBP)) {
								$spouselinks .= "'".htmlspecialchars($pSpouseMBP, ENT_QUOTES)."'".","; // mbirthpl = Mothers Birthplace
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
							$spouselinks .= $spouse->getFullName(); // Full Name (Link)
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

				// Children -------------------------------------
				$spouselinks .= "<ul class=\"clist\">";
				foreach ($children as $c=>$child) {
					$cpid = $child->getXref();
					if ($child) {
						$persons="Yes";

						//-- Childs Parents ---------------------
						$gparent=WT_Person::getInstance($child->getXref());
						$fams = $gparent->getChildFamilies();
						$chfams = $gparent->getSpouseFamilies();
						foreach ($fams as $family) {
							if (!is_null($family)) {
								$husb = $family->getHusband($gparent);
								$wife = $family->getWife($gparent);
							}
							if ($husb) { $ChildFBP = $husb->getBirthPlace(); }
							if ($wife) { $ChildMBP = $wife->getBirthPlace(); }
						}

						// Get Child's Children
						$chBLDarray=Array();
						foreach ($child->getSpouseFamilies() as $childfamily) {
							$chchildren = $childfamily->getChildren();
							foreach ($chchildren as $chchild) {
								$chnam   = $chchild->getAllNames();
								$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
								$chfulln = str_replace('"', "", $chfulln); // Must remove quotes completely here
								$chfulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $chfulln);
								$chfulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $chfulln); // Child's Full Name
								$chdob   = ($chchild->getBirthDate()->minJD()+$chchild->getBirthDate()->maxJD())/2; // Child's Date of Birth (Julian)
								$chdod   = ($chchild->getDeathDate()->minJD()+$chchild->getDeathDate()->maxJD())/2; // Child's Date of Death (Julian)
								$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
								array_push($chBLDarray, $chBLD);
							}
						}

						// Get Childs marriage status ------------
						$married="";
						$marrdate="";
						$chhusbnam=null;
						foreach ($child->getSpouseFamilies() as $childfamily) {
							$marrdate=$childfamily->getMarriageDate();
							$married = WT_Date::Compare($censdate, $marrdate);
							if ($childfamily->getHusband()) {
								$chhusbnam = $childfamily->getHusband()->getAllNames();
							}
						}
						// Childs Details -------------------------
						$spouselinks .= "<li>";
						if ($child->canDisplayName()) {
							$nam   = $child->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".WT_I18N::translate('unknown').")", $fulln);
							$givn  = rtrim($nam[0]['givn'],'*');
							$surn  = $nam[0]['surname'];
							$husbnam = null;
							
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
									$chfulmn = rtrim($chnam[$i]['givn'],'*')."&nbsp;".$husbnam;									
								}									
							}

							$spouselinks .= "<a href=\"#\" onclick=\"insertRowToTable(";
							$spouselinks .= "'".$child->getXref()."',"; // pid = PID
							$spouselinks .= "'".addslashes(strip_tags($fulln))."',"; // nam = Name
							if (isset($chfulmn)) {
								$spouselinks .= "'".addslashes(strip_tags($chfulmn))."',"; // mnam = Full Married Name
							} else {
								$spouselinks .= "'".addslashes(strip_tags($fulln))."',"; // mnam = Full Name
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
							$spouselinks .= "'".htmlspecialchars($child->getBirthPlace(), ENT_QUOTES)."'".","; // birthpl = Individuals Birthplace
							if (isset($ChildFBP)) {
								$spouselinks .= "'".htmlspecialchars($ChildFBP, ENT_QUOTES)."'".","; // fbirthpl = Fathers Birthplace
							} else {
								$spouselinks .= "'UNK, UNK, UNK, UNK'".","; // fbirthpl = Fathers Birthplace Not Known
							}
							if (isset($ChildMBP)) {
								$spouselinks .= "'".htmlspecialchars($ChildMBP, ENT_QUOTES)."'".","; // mbirthpl = Mothers Birthplace
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
							$spouselinks .= $child->getFullName(); // Full Name (Link)
							$spouselinks .= "</a>";
							$spouselinks .= "</li>";
						} else {
							$spouselinks .= WT_I18N::translate('Private');
						}
					}
				}
				$spouselinks .= "</ul>";
			}
		}
		if ($persons != "Yes") {
			$spouselinks  .= "(" . WT_I18N::translate('none') . ")</td></tr></table>";
		} else {
			$spouselinks  .= "</td></tr></table>";
		}

		if ($person_parent != "Yes") {
			$parentlinks .= "(" . WT_I18N::translate_c('unknown family', 'unknown') . ")</td></tr></table>";
		} else {
			$parentlinks .= "</td></tr></table>";
		}

		if ($person_step != "Yes") {
			$step_parentlinks .= "(" . WT_I18N::translate_c('unknown family', 'unknown') . ")</td></tr></table>";
		} else {
			$step_parentlinks .= "</td></tr></table>";
		}
	}
}

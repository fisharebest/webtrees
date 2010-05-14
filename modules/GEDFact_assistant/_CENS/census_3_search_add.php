<?php
/**
 * Census Assistant Control module for phpGedView
 *
 * Census Search and Add Area File
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2007 to 2010  PGV Development Team.  All rights reserved.
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
 * @subpackage Module
 * @version $Id$
 * @author Brian Holland
 */
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
			<td align="center" class="descriptionbox"><font size=1>Search for People to add:</font></td>
		</tr>
		<tr>
			<td class="optionbox" >
				<script>
					function findindi(persid) {
						var findInput = document.getElementById('personid');
							txt = findInput.value;
						if (txt=="") {
							alert("<?php echo i18n::translate('You must enter a name'); ?>");
						}else{
							var win02 = window.open(
								"module.php?mod=GEDFact_assistant&mod_action=_CENS/census_3_find&callback=paste_id&action=filter&type=indi&multiple=&filter="+txt, "win02", "resizable=1, menubar=0, scrollbars=1, top=180, left=600, HEIGHT=400, WIDTH=450 ");
							if (window.focus) {win02.focus();}
						}
					}
				</script>
				<?php
				print "<input id=personid type=\"text\" size=\"20\" STYLE=\"color: #000000;\" value=\"\" />";
				print "<a href=\"javascript: onclick=findindi()\">" ;
				print "&nbsp;<font size=\"2\">&nbsp;Find</font>";
				print '</a>';
				?>
			</td>
		</tr>
		<tr>
			<td style="border: 0px solid transparent;">
				<br />
			</td>
		</tr>

				<?php
				//-- Add Family Members to Census  -------------------------------------------
				global $SHOW_ID_NUMBERS, $WT_IMAGE_DIR, $WT_IMAGES, $WT_MENUS_AS_LISTS;
				global $spouselinks, $parentlinks, $DeathYr, $BirthYr;
				global $TEXT_DIRECTION, $GEDCOM; 
				// echo "CENS = " . $censyear;
				?>
				
				<tr>
					<td align="center" style="border: 0px solid transparent;">
						<table width="100%" class="fact_table" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan=3 class="descriptionbox">
								<font size=1>
								<?php
								// Header text with "Head" button =================================================
								if (isset($WT_IMAGES["head"]["button"])) {
									$headImg  = "<img class=\"headimg vmiddle\" src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["head"]["button"]."\" />";
									$headImg2 = "<img class=\"headimg2 vmiddle\" src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["head"]["button"]."\" alt=\"".i18n::translate('Click to choose person as Head of family.')."\" title=\"".i18n::translate('Click to choose person as Head of family.')."\" />";
								} else {
									$headImg  = "<img class=\"headimg vmiddle\" src=\"images/buttons/head.gif\" />";
									$headImg2 = "<img class=\"headimg2 vmiddle\" src=\"images/buttons/head.gif\" alt=\"".i18n::translate('Click to choose person as Head of family.')."\" title=\"".i18n::translate('Click to choose person as Head of family.')."\" />";
								}
								global $tempStringHead;
								$tempStringHead = PrintReady($headImg);
								echo i18n::translate('Click %s to choose person as Head of family.', $tempStringHead);
								?>
								</font>
								</td>
							</tr>

							<tr>
								<td>
									<font size=1><br /></font>
								</td>
							</tr>
							
					<?php

					//-- Parents Family ---------------------------------------------------

					//-- Build Parents Family --------------------------------------
					$personcount=0;
					$families = $this->indi->getChildFamilies();
					foreach($families as $famid=>$family) {
						$label = $this->indi->getChildFamilyLabel($family);
						$people = $this->buildFamilyList($family, "parents");
						$marrdate = $family->getMarriageDate();

						//-- Get Parents Children's Name, DOB, DOD --------------------------
						if (isset($people["children"])) {
							$chBLDarray = Array();
							foreach ($people["children"] as $key=>$child) {
								$chnam   = $child->getAllNames();
								$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
								$chfulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $chfulln);
								$chfulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $chfulln);
								$chfulln = addslashes($chfulln);													// Child's Full Name
								$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2;		// Child's Date of Birth (Julian)
								$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2;		// Child's Date of Death (Julian)
								$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
								array_push($chBLDarray, $chBLD);
							}
						}

						//-- Parents Husband -------------------
						$styleadd = "";
						if (isset($people["husb"])) {

							//-- Parents Husbands Parents --------------------------------------
							$gparent=Person::getInstance($people["husb"]->getXref());
							$fams = $gparent->getChildFamilies();
							foreach($fams as $famid=>$family) {
								if (!is_null($family)) {
									$phusb = $family->getHusband($gparent);
									$pwife = $family->getWife($gparent);
								}
								if ($phusb) { $HusbFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $HusbMBP = $pwife->getBirthPlace(); }
							}

							//-- Parents Husbands Details --------------------------------------
							$married = GedcomDate::Compare($censdate, $marrdate);
							$nam     = $people["husb"]->getAllNames();
							$fulln   = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln   = str_replace("@N.N.", "(".i18n::translate('unknown').")", $fulln);
							$fulln   = str_replace("@P.N.", "(".i18n::translate('unknown').")", $fulln);
							$givn    = rtrim($nam[0]['givn'],'*');
							$surn    = $nam[0]['surname'];
							if (isset($nam[1])) {
								$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
								$marn  = $nam[1]['surname'];
							}
							$menu = new Menu($people["husb"]->getLabel()."\n");
							$slabel  = print_pedigree_person_nav2($people["husb"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++, $people["husb"]->getLabel(), $censdate);
							$slabel .= $parentlinks;
							$submenu = new Menu($slabel);
							$menu->addSubMenu($submenu);
							
							?>
							<tr>
								<td align="left" class="linkcell optionbox" width="25%">
									<font size=1>
										<?php 
										//  print $people["husb"]->getLabel();
										$menu->printMenu();
										?>
									</font>
								</td>
								<td align="left" class="facts_value" style="text-decoration:none;" >
									<font size=1>
										<?php 
										print "<a href=\"".encode_url("edit_interface.php?action=addnewnote_assisted&noteid=newnote&pid=".$people["husb"]->getXref()."&gedcom={$GEDCOM}")."\">";
										echo $headImg2;
										print "</a>";
										?>
									</font>
								</td>
								<td align="left" class="facts_value" nowrap="nowrap">
									<font size=1>
									<?php
									if ( ($people["husb"]->canDisplayDetails()) ) {
									?>
									<a href='javaScript:insertRowToTable("<?php 
											print PrintReady($people["husb"]->getXref()) ;								 // pid = PID
										?>", "<?php 
											echo addslashes($fulln);													 // nam = Full Name
										?>", "<?php 
											if (isset($nam[1])){
												echo addslashes($fulmn);												 // mnam = Full Married Name
											}else{
												echo addslashes($fulln);												 // mnam = Full Name
											}
										?>", "<?php
											print PrintReady($people["husb"]->getLabel());								 // label = Relationship
										?>", "<?php
											print PrintReady($people["husb"]->getSex());								 // gend = Gender
										?>", "<?php
											if ($married>=0){
												echo "M";																 // cond = Condition (Married)
											}else{
												echo "S";																 // cond = Condition (Single)
											}
										?>", "<?php
											if ($marrdate) {
												echo ($marrdate->minJD()+$marrdate->maxJD())/2;	 						 // dom = Date of Marriage (Julian)
											}
										?>", "<?php
											echo ($people["husb"]->getBirthDate()->minJD()+$people["husb"]->getBirthDate()->maxJD())/2;	 // dob = Date of Birth (Julian)
										?>", "<?php
											print PrintReady($censyear-$people["husb"]->getbirthyear());				 // age = Census Date minus YOB
										?>", "<?php
											echo ($people["husb"]->getDeathDate()->minJD()+$people["husb"]->getDeathDate()->maxJD())/2;	 // dod = Date of Death (Julian)
										?>", "<?php
											print "";																	 // occu = Occupation
										?>", "<?php
											print PrintReady($people["husb"]->getBirthPlace());							 //  birthpl = Husband Place of Birth
										?>", "<?php
											if (isset($HusbFBP)) {
												print PrintReady($HusbFBP);												 // fbirthpl = Husband Father's Place of Birth
											} else {
												print PrintReady('UNK, UNK, UNK, UNK');									 // fbirthpl = Husband Father's Place of Birth Not known
											}
										?>", "<?php
											if (isset($HusbMBP)) {
												print PrintReady($HusbMBP);												 // mbirthpl = Husband Mother's Place of Birth
											} else {
												print PrintReady('UNK, UNK, UNK, UNK');									 // mbirthpl = Husband Mother's Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $people["husb"]->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo PrintReady($chBLDarray);											 // Array of Children (name, birthdate, deathdate)
											} else {
												echo PrintReady('');
											}
										?>");'>
										<?php
											print PrintReady($people["husb"]->getFullName()); 							 // Full Name (Link)
										?> 
									</a> 
									<?php print "\n" ;
									}else{
										print i18n::translate('Private');
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
							$gparent=Person::getInstance($people["wife"]->getXref());
							$fams = $gparent->getChildFamilies();
							foreach($fams as $famid=>$family) {
								if (!is_null($family)) {
									$phusb = $family->getHusband($gparent);
									$pwife = $family->getWife($gparent);
								}
								if ($phusb) { $WifeFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $WifeMBP = $pwife->getBirthPlace(); }
							}

							//-- Wifes Details --------------------------------------
							$married = GedcomDate::Compare($censdate, $marrdate);
							$nam     = $people["wife"]->getAllNames();
							$fulln   = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln   = str_replace("@N.N.", "(".i18n::translate('unknown').")", $fulln);
							$fulln   = str_replace("@P.N.", "(".i18n::translate('unknown').")", $fulln);
							$givn    = rtrim($nam[0]['givn'],'*');
							$surn    = $nam[0]['surname'];
							// Get wifes married name if available
							if (isset($people["husb"])) {
								$husbnams = $people["husb"]->getAllNames();
								if ($husbnams[0]['surname']=="@N.N." || $husbnams[0]['surname']=="") {
									// Husband or his name is not known
								} else {
									$husbnam = $people["husb"]->getAllNames();
								}
							}
							if (isset($nam[1]) && isset($husbnam)) {
								$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$husbnam[0]['surname'];
							}else{
								$fulmn = $fulln;
							}
							$menu = new Menu($people["wife"]->getLabel()."\n");
							$slabel  = print_pedigree_person_nav2($people["wife"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++, $people["wife"]->getLabel(), $censyear);
							$slabel .= $parentlinks;
							$submenu = new Menu($slabel);
							$menu->addSubMenu($submenu);
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<font size=1>
									<?php 
									//print $people["wife"]->getLabel(); 
									$menu->printMenu(); 
									?>
									</font>
								</td>
								<td align="left" class="facts_value">
									<font size=1>
										<?php 
										print "<a href=\"".encode_url("edit_interface.php?action=addnewnote_assisted&noteid=newnote&pid=".$people["wife"]->getXref()."&gedcom={$GEDCOM}")."\">";
										print $headImg2;
										print "</a>";
										?>
									</font>
								</td>
								<td align="left" class="facts_value" nowrap="nowrap">
									<font size=1>
									<?php
									if ( ($people["wife"]->canDisplayDetails()) ) {
										?>
									<a href='javaScript:insertRowToTable("<?php
											print $people["wife"]->getXref() ; 											 // pid = PID
										?>", "<?php 
											echo addslashes($fulln);													 // nam = Full Name
										?>", "<?php 
											if (isset($nam[1])){
												echo addslashes($fulmn);												 // mnam = Full Married Name
											}else{
												echo addslashes($fulln);												 // mnam = Full Name
											}
										?>", "<?php
											print PrintReady($people["wife"]->getLabel());								 // label = Relationship
										?>", "<?php
											print PrintReady($people["wife"]->getSex());								 // gend = Gender
										?>", "<?php
											if ($married>=0 && isset($nam[1])){
												echo "M";																 // cond = Condition (Married)
											}else{
												echo "S";																 // cond = Condition (Single)
											}
										?>", "<?php
											if ($marrdate) {
												echo ($marrdate->minJD()+$marrdate->maxJD())/2;	 						 // dom = Date of Marriage (Julian)
											}
										?>", "<?php
											echo ($people["wife"]->getBirthDate()->minJD()+$people["wife"]->getBirthDate()->maxJD())/2;    // dob = Date of Birth (Julian)
										?>", "<?php
											print PrintReady($censyear-$people["wife"]->getbirthyear());				 // age = Census Date minus YOB
										?>", "<?php
											echo ($people["wife"]->getDeathDate()->minJD()+$people["wife"]->getDeathDate()->maxJD())/2;	 // dod = Date of Death (Julian)
										?>", "<?php
											print "";																	 // occu = Occupation
										?>", "<?php
											print PrintReady($people["wife"]->getBirthPlace());							 //  birthpl = Wife Place of Birth 
										?>", "<?php
											if (isset($WifeFBP)) {
												print PrintReady($WifeFBP);												 // fbirthpl = Wife Father's Place of Birth 
											} else {
												print PrintReady('UNK, UNK, UNK, UNK');									 // fbirthpl = Wife Father's Place of Birth Not known
											}
										?>", "<?php
											if (isset($WifeMBP)) {
												print PrintReady($WifeMBP);												 // mbirthpl = Wife Mother's Place of Birth
											} else {
												print PrintReady('UNK, UNK, UNK, UNK');									 // mbirthpl = Wife Mother's Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $people["wife"]->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo PrintReady($chBLDarray);											 // Array of Children (name, birthdate, deathdate)
											} else {
												echo PrintReady('');
											}
										?>");'>
										<?php 
											print PrintReady($people["wife"]->getFullName()); 							 // Full Name (Link)
										?>
									</a> 
									<?php print "\n" ;
									}else{
										print i18n::translate('Private');
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
							foreach($people["children"] as $key=>$child) {

								// Get Child's Children's Name DOB DOD ----
								$chBLDarray=Array();
								foreach ($child->getSpouseFamilies() as $childfamily) {
									$chchildren = $childfamily->getChildren();
									foreach ($chchildren as $key=>$chchild) {
										$chnam   = $chchild->getAllNames();
										$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
										$chfulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $chfulln);
										$chfulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $chfulln);
										$chfulln = addslashes($chfulln);														// Child's Full Name// Child's Full Name
										$chdob   = ($chchild->getBirthDate()->minJD()+$chchild->getBirthDate()->maxJD())/2;		// Child's Date of Birth (Julian)
										$chdod   = ($chchild->getDeathDate()->minJD()+$chchild->getDeathDate()->maxJD())/2;		// Child's Date of Death (Julian)
										$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
										array_push($chBLDarray, $chBLD);
									}
								}

								// Get child's marriage status ----
								$married="";
								$marrdate="";
								foreach ($child->getSpouseFamilies() as $childfamily) {
									$marrdate=$childfamily->getMarriageDate();
									$married = GedcomDate::Compare($censdate, $marrdate);
								}
								$nam   = $child->getAllNames();
								$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
								$fulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $fulln);
								$fulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $fulln);
								$givn  = rtrim($nam[0]['givn'],'*');
								$surn  = $nam[0]['surname'];
								if (isset($nam[1])) {
									$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
									$marn  = $nam[1]['surname'];
								}
								
								$menu = new Menu($child->getLabel()."\n");
								$slabel  = print_pedigree_person_nav2($child->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++, $child->getLabel(), $censyear);
								$slabel .= $spouselinks;
								$submenu = new Menu($slabel);
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
												print $child->getLabel();
											}else{
												$menu->printMenu();
											}
											?>
											</font>
										</td>
										<td align="left" class="facts_value">
											<font size=1>
												<?php 
												print "<a href=\"".encode_url("edit_interface.php?action=addnewnote_assisted&noteid=newnote&pid=".$child->getXref()."&gedcom={$GEDCOM}")."\">";
												print $headImg2;
												print "</a>";
												?>
											</font>
										</td>
										<td align="left" class="facts_value" nowrap="nowrap">
											<font size=1>
											<?php
											if ( ($child->canDisplayDetails()) ) {
												?>
												<a href='javaScript:insertRowToTable("<?php 
														print $child->getXref();											 // pid = PID
													?>", "<?php 
														echo addslashes($fulln);											 // nam = Full Name
													?>", "<?php 
														if (isset($nam[1])){
															echo addslashes($fulmn);										 // mnam = Full Married Name
														}else{
															echo addslashes($fulln);										 // mnam = Full Name
														}
													?>", "<?php
														if ($child->getXref()==$pid) {
															print "Head";													 // label = Head
														}else{
															print PrintReady($child->getLabel());							 // label = Relationship
														}
													?>", "<?php
														print PrintReady($child->getSex());									 // gend = Gender
													?>", "<?php
														if ($married>0) {
															echo "M";														 // cond = Condition (Married)
														} else if ($married<0 || ($married=="0") ) {
															echo "S";														 // cond = Condition (Single)
														} else {
															echo "";														 // cond = Condition (Not Known)
														}
													?>", "<?php
														if ($marrdate) {
															echo ($marrdate->minJD()+$marrdate->maxJD())/2;	 				 // dom = Date of Marriage (Julian)
														}
													?>", "<?php
														echo ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2;	// dob = Date of Birth (Julian)
													?>", "<?php
														print PrintReady($censyear-$child->getbirthyear());					 // age = Census Date minus YOB
													?>", "<?php
														echo ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2;	 // dod = Date of Death (Julian)
													?>", "<?php
														print "";															 // occu = Occupation
													?>", "<?php
														print PrintReady($child->getBirthPlace());							 //  birthpl = Child Place of Birt
													?>", "<?php
														if (isset($people["husb"])) {
															print PrintReady($people["husb"]->getBirthPlace());					 // fbirthpl = Child Father's Place of Birth 
														} else {
															print PrintReady('UNK, UNK, UNK, UNK');								 // fbirthpl = Child Father's Place of Birth Not known
														}
													?>", "<?php
														if (isset($people["wife"])) {
															print PrintReady($people["wife"]->getBirthPlace());					 // mbirthpl = Child Mother's Place of Birth 
														} else {
															print PrintReady('UNK, UNK, UNK, UNK');								 // mbirthpl = Child Mother's Place of Birth Not known
														}
													?>", "<?php
														if (isset($chBLDarray) && $child->getSex()=="F") {
															$chBLDarray = implode("::", $chBLDarray);
															echo PrintReady($chBLDarray);										 // Array of Children (name, birthdate, deathdate)
														} else {
															echo PrintReady('');
														}
													?>");'>
													<?php
														print PrintReady($child->getFullName());							 // Full Name (Link)
													?>
												</a>
												<?php print "\n" ;
											}else{
													print i18n::translate('Private');
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
					foreach($this->indi->getStepFamilies() as $famid=>$family) {
						$label = $this->indi->getStepFamilyLabel($family);
						$people = $this->buildFamilyList($family, "step");
						if ($people){
							echo "<tr><td><br /></td><td></td></tr>";
						}
						$marrdate = $family->getMarriageDate();

						//-- Get Children's Name, DOB, DOD --------------------------
						if (isset($people["children"])) {
							$chBLDarray = Array();
							foreach ($people["children"] as $key=>$child) {
								$chnam   = $child->getAllNames();
								$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
								$chfulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $chfulln);
								$chfulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $chfulln);
								$chfulln = addslashes($chfulln);													// Child's Full Name
								$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2;		// Child's Date of Birth (Julian)
								$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2;		// Child's Date of Death (Julian)
								$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
								array_push($chBLDarray, $chBLD);
							}
						}

						// Step Husband -----------------------------
						$styleadd = "";
						$elderdate = "";
						if (isset($people["husb"])) {

							//-- Step Husbands Parent Family --------------------------------------
							$gparent=Person::getInstance($people["husb"]->getXref());
							$fams = $gparent->getChildFamilies();
							foreach($fams as $famid=>$family) {
								if (!is_null($family)) {
									$phusb = $family->getHusband($gparent);
									$pwife = $family->getWife($gparent);
								}
								if ($phusb) { $HusbFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $HusbMBP = $pwife->getBirthPlace(); }
							}

							//-- Step Husbands Details --------------------------------------
							$married = GedcomDate::Compare($censdate, $marrdate);
							$nam   = $people["husb"]->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $fulln);
							$givn  = rtrim($nam[0]['givn'],'*');
							$surn  = $nam[0]['surname'];
							if (isset($nam[1])) {
								$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
								$marn  = $nam[1]['surname'];
							}
							$menu = new Menu();
							if ($people["husb"]->getLabel() == ".") {
								$menu->addLabel(i18n::translate('Step-Father')."\n");
							}else{
								$menu->addLabel($people["husb"]->getLabel()."\n");
							}
							$slabel  = print_pedigree_person_nav2($people["husb"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++, $people["husb"]->getLabel(), $censyear);
							$slabel .= $parentlinks;
							$submenu = new Menu($slabel);
							$menu->addSubMenu($submenu);
							if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
							if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<font size=1>
									<?php 
									$menu->printMenu(); 
									?>
									</font>
								</td>
								<td align="left" class="facts_value">
									<font size=1>
										<?php 
										print "<a href=\"".encode_url("edit_interface.php?action=addnewnote_assisted&noteid=newnote&pid=".$people["husb"]->getXref()."&gedcom={$GEDCOM}")."\">";
										print $headImg2;
										print "</a>";
										?>
									</font>
								</td>
								<td align="left" class="facts_value" nowrap="nowrap">
									<font size=1>
									<?php
									if ( ($people["husb"]->canDisplayDetails()) ) {
									?>
									<a href='javaScript:insertRowToTable("<?php
											print PrintReady($people["husb"]->getXref());									 // pid = PID
										?>", "<?php 
											echo addslashes($fulln);														 // nam = Full Name
										?>", "<?php 
											if (isset($nam[1])){
												echo addslashes($fulmn);													 // mnam = Full Married Name
											}else{
												echo addslashes($fulln);													 // mnam = Full Name
											}
										?>", "<?php
										if ($people["husb"]->getLabel() == ".") {
											print PrintReady(i18n::translate('Step-Father'));											 // label = Relationship
										}else{
											print PrintReady($people["husb"]->getLabel());									 // label = Relationship
										}
										?>", "<?php
											print PrintReady($people["husb"]->getSex());									 // gend = Gender
										?>", "<?php
											if ($married>=0){
												echo "M";																	 // cond = Condition (Married)
											}else{
												echo "S";																	 // cond = Condition (Single)
											}
										?>", "<?php
											if ($marrdate) {
												echo ($marrdate->minJD()+$marrdate->maxJD())/2;	 							 // dom = Date of Marriage (Julian)
											}
										?>", "<?php
											echo ($people["husb"]->getBirthDate()->minJD()+$people["husb"]->getBirthDate()->maxJD())/2;	 // dob = Date of Birth (Julian)
										?>", "<?php
											print PrintReady($censyear-$people["husb"]->getbirthyear());					 // age = Census Date minus YOB
										?>", "<?php
											echo ($people["husb"]->getDeathDate()->minJD()+$people["husb"]->getDeathDate()->maxJD())/2;	 // dod = Date of Death (Julian)
										?>", "<?php
											print "";																		 // occu = Occupation
										?>", "<?php
											print PrintReady($people["husb"]->getBirthPlace());								 //  birthpl = Step Husband Place of Birth 
										?>", "<?php
											if (isset($HusbFBP)) {
												print PrintReady($HusbFBP);													 // fbirthpl = Step Husband Father's Place of Birth
											} else {
												print PrintReady('UNK, UNK, UNK, UNK');										 // fbirthpl = Step Husband Father's Place of Birth Not known
											}
										?>", "<?php
											if (isset($HusbMBP)) {
												print PrintReady($HusbMBP);													 // mbirthpl = Step Husband Mother's Place of Birth
											} else {
												print PrintReady('UNK, UNK, UNK, UNK');										 // mbirthpl = Step Husband Mother's Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $people["husb"]->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo PrintReady($chBLDarray);												 // Array of Children (name, birthdate, deathdate)
											} else {
												echo PrintReady('');
											}
										?>");'>
										<?php 
											print PrintReady($people["husb"]->getFullName()); 								 // Full Name (Link)
										?> 
									</a> 
									<?php print "\n" ;
									}else{
										print i18n::translate('Private');
									}
									?>
									</font>
								</td>
							</tr>
							<?php
							$elderdate = $people["husb"]->getBirthDate(false);
						}

						// Step Wife -------------------
						$styleadd = "";
						if (isset($people["wife"])) {

							//-- Step Wifes Parent Family --------------------------------------
							$gparent=Person::getInstance($people["wife"]->getXref());
							$fams = $gparent->getChildFamilies();
							foreach($fams as $famid=>$family) {
								if (!is_null($family)) {
									$phusb = $family->getHusband($gparent);
									$pwife = $family->getWife($gparent);
								}
								if ($phusb) { $WifeFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $WifeMBP = $pwife->getBirthPlace(); }
							}

							//-- Step Wifes Details --------------------------------------
							$married = GedcomDate::Compare($censdate, $marrdate);
							$nam   = $people["wife"]->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $fulln);
							$givn  = rtrim($nam[0]['givn'],'*');
							$surn  = $nam[0]['surname'];
							// Get wifes married name if available
							if (isset($people["husb"])){
								$husbnams = $people["husb"]->getAllNames();
								if ($husbnams[0]['surname']=="@N.N." || $husbnams[0]['surname']=="") {
									// Husband or his name is not known
								} else {
									$husbnam = $people["husb"]->getAllNames();
								}
							}
							if (isset($nam[1]) && isset($husbnam)) {
								$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$husbnam[0]['surname'];
							}else{
								$fulmn = $fulln;
							}
							$menu = new Menu();
							if ($people["wife"]->getLabel() == ".") {
								$menu->addLabel(i18n::translate('Step-Mother')."\n");
							}else{
								$menu->addLabel($people["wife"]->getLabel()."\n");
							}
							$slabel  = print_pedigree_person_nav2($people["wife"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++, $people["wife"]->getLabel(), $censyear);
							$slabel .= $parentlinks;
							$submenu = new Menu($slabel);
							$menu->addSubMenu($submenu);
							if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
							if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<font size=1>
									<?php 
									$menu->printMenu(); 
									?>
									</font>
								</td>
								<td align="left" class="facts_value">
									<font size=1>
										<?php 
										print "<a href=\"".encode_url("edit_interface.php?action=addnewnote_assisted&noteid=newnote&pid=".$people["wife"]->getXref()."&gedcom={$GEDCOM}")."\">";
										print $headImg2;
										print "</a>";
										?>
									</font>
								</td>
								<td align="left" class="facts_value" nowrap="nowrap">
									<font size=1>
									<?php
									if ( ($people["wife"]->canDisplayDetails()) ) {
									?>
									<a href='javaScript:insertRowToTable("<?php
											print PrintReady($people["wife"]->getXref()) ;									 // pid = PID
										?>", "<?php 
											echo addslashes($fulln);														 // nam = Full Name
										?>", "<?php 
											if (isset($nam[1])){
												echo addslashes($fulmn);													 // mnam = Full Married Name
											}else{
												echo addslashes($fulln);													 // mnam = Full Name
											}
										?>", "<?php
										if ($people["wife"]->getLabel() == ".") {
											print PrintReady(i18n::translate('Step-Mother'));											 // label = Relationship
										}else{
											print PrintReady($people["wife"]->getLabel());									 // label = Relationship
										}
										?>", "<?php
											print PrintReady($people["wife"]->getSex());									 // gend = Gender
										?>", "<?php
											if ($married>=0 && isset($nam[1])){
												echo "M";																	 // cond = Condition (Married)
											}else{
												echo "S";																	 // cond = Condition (Single)
											}
										?>", "<?php
											if ($marrdate) {
												echo ($marrdate->minJD()+$marrdate->maxJD())/2;	 							 // dom = Date of Marriage (Julian)
											}
										?>", "<?php
											echo ($people["wife"]->getBirthDate()->minJD()+$people["wife"]->getBirthDate()->maxJD())/2;	 // dob = Date of Birth (Julian)
										?>", "<?php
											print PrintReady($censyear-$people["wife"]->getbirthyear());					 // age = Census Date minus YOB
										?>", "<?php
											echo ($people["wife"]->getDeathDate()->minJD()+$people["wife"]->getDeathDate()->maxJD())/2;	 // dod = Date of Death (Julian)
										?>", "<?php
											print "";																		 // occu = Occupation
										?>", "<?php
											print PrintReady($people["wife"]->getBirthPlace());								 //  birthpl = Step Wife Place of Birth
										?>", "<?php
											if (isset($WifeFBP)) {
												print PrintReady($WifeFBP);													 // fbirthpl = Step Wife Father's Place of Birth 
											} else {
												print PrintReady('UNK, UNK, UNK, UNK');										 // fbirthpl = Step Wife Father's Place of Birth Not known
											}
										?>", "<?php
											if (isset($WifeMBP)) {
												print PrintReady($WifeMBP);													 // mbirthpl = Step Wife Mother's Place of Birth
											} else {
												print PrintReady('UNK, UNK, UNK, UNK');										 // mbirthpl = Step Wife Mother's Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $people["wife"]->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo PrintReady($chBLDarray);												 // Array of Children (name, birthdate, deathdate)
											} else {
												echo PrintReady('');
											}
										?>");'>
										<?php 
											print PrintReady($people["wife"]->getFullName()); 								 // Full Name (Link)
										?>
									</a> 
									<?php print "\n" ;
									}else{
										print i18n::translate('Private');
									}
									?>
									</font>
								</td>
							</tr>
							<?php
						}

						// Step Children ---------------------
						$styleadd = "";
						if (isset($people["children"])) {
							$elderdate = $family->getMarriageDate();
							foreach($people["children"] as $key=>$child) {
							
								// Get Child's Children
								$chBLDarray=Array();
								foreach ($child->getSpouseFamilies() as $childfamily) {
									$chchildren = $childfamily->getChildren();
									foreach ($chchildren as $key=>$chchild) {
										$chnam   = $chchild->getAllNames();
										$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
										$chfulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $chfulln);
										$chfulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $chfulln);
										$chfulln = addslashes($chfulln);														// Child's Full Name
										$chdob   = ($chchild->getBirthDate()->minJD()+$chchild->getBirthDate()->maxJD())/2;		// Child's Date of Birth (Julian)
										$chdod   = ($chchild->getDeathDate()->minJD()+$chchild->getDeathDate()->maxJD())/2;		// Child's Date of Death (Julian)
										$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
										array_push($chBLDarray, $chBLD);
									}
								}
							
								$nam   = $child->getAllNames();
								$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
								$fulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $fulln);
								$fulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $fulln);
								$givn  = rtrim($nam[0]['givn'],'*');
								$surn  = $nam[0]['surname'];
								if (isset($nam[1])) {
									$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
									$marn  = $nam[1]['surname'];
								}
								$menu = new Menu($child->getLabel()."\n");
								$slabel  = print_pedigree_person_nav2($child->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++, $child->getLabel(), $censyear);
								$slabel .= $spouselinks;
								$submenu = new Menu($slabel);
								$menu->addSubMenu($submenu);
								if (PrintReady($child->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($child->getDeathYear()); }
								if (PrintReady($child->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($child->getBirthYear()); }
								?>
								<tr>
									<td align="left" class="linkcell optionbox">
										<font size=1>
										<?php 
										$menu->printMenu(); 
										?>
										</font>
									</td>
									<td align="left" class="facts_value">
										<font size=1>
											<?php
										print "<a href=\"".encode_url("edit_interface.php?action=addnewnote_assisted&noteid=newnote&pid=".$child->getXref()."&gedcom={$GEDCOM}")."\">";
											print $headImg2;
											print "</a>";
											?>
										</font>
									</td>
									<td align="left" class="facts_value" nowrap="nowrap">
										<font size=1>
										<?php
										if ( ($child->canDisplayDetails()) ) {
										?>
										<a href='javaScript:insertRowToTable("<?php
												print PrintReady($child->getXref()) ;										 // pid = PID
											?>", "<?php 
												echo addslashes($fulln);													 // nam = Full Name
											?>", "<?php 
												if (isset($nam[1])){
													echo addslashes($fulmn);												 // mnam = Full Married Name
												}else{
													echo addslashes($fulln);												 // mnam = Full Name
												}
											?>", "<?php
												print PrintReady($child->getLabel());										 // label = Relationship
											?>", "<?php
												print PrintReady($child->getSex());											 // gend = Gender
											?>", "<?php
												print "";																	 // cond = Condition (Married or Single)
											?>", "<?php
											if ($marrdate) {
												echo ($marrdate->minJD()+$marrdate->maxJD())/2;	 							 // dom = Date of Marriage (Julian)
											}
											?>", "<?php
												echo ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2;	 // dob = Date of Birth (Julian)
											?>", "<?php
												print PrintReady($censyear-$child->getbirthyear());							 // age = Census Date minus YOB
											?>", "<?php
												echo ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2;	 // dod = Date of Death (Julian)
											?>", "<?php
												print "";																	 // occu = Occupation
											?>", "<?php
												print PrintReady($child->getBirthPlace());									 //  birthpl = Child Place of Birth 
											?>", "<?php
												if (isset($people["husb"])) {
													print PrintReady($people["husb"]->getBirthPlace());						 // fbirthpl = Child Father's Place of Birth
												} else {
													print PrintReady('UNK, UNK, UNK, UNK');									 // fbirthpl = Child Father's Place of Birth Not known
												}
											?>", "<?php
												if (isset($people["wife"])) {
													print PrintReady($people["wife"]->getBirthPlace());						 // mbirthpl = Child Mother's Place of Birth 
												} else {
													print PrintReady('UNK, UNK, UNK, UNK');									 // mbirthpl = Child Mother's Place of Birth Not known
												}
											?>", "<?php
												if (isset($chBLDarray) && $child->getSex()=="F") {
													$chBLDarray = implode("::", $chBLDarray);
													echo PrintReady($chBLDarray);											 // Array of Children (name, birthdate, deathdate)
												} else {
													echo PrintReady('');
												}
											?>");'>
											<?php 
												print PrintReady($child->getFullName()); 									 // Full Name (Link)
											?> 
										</a> 
										<?php print "\n" ;
										}else{
											print i18n::translate('Private');
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

					print "<tr><td><font size=1><br /></font></td></tr>";

					//-- Build Spouse Family ---------------------------------------------------
					$families = $this->indi->getSpouseFamilies();
					//$personcount = 0;
					foreach($families as $famid=>$family) {
						$people = $this->buildFamilyList($family, "spouse");
						if ($this->indi->equals($people["husb"])) {
							$spousetag = 'WIFE';
						}else{
							$spousetag = 'HUSB';
						}
						$marrdate = $family->getMarriageDate();
						
						//-- Get Children's Name, DOB, DOD --------------------------
						if (isset($people["children"])) {
							$chBLDarray = Array();
							foreach ($people["children"] as $key=>$child) {
								$chnam   = $child->getAllNames();
								$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
								$chfulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $chfulln);
								$chfulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $chfulln);
								$chfulln = addslashes($chfulln);													// Child's Full Name
								$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2;		// Child's Date of Birth (Julian)
								$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2;		// Child's Date of Death (Julian)
								$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
								array_push($chBLDarray, $chBLD);
							}
						}

						//-- Spouse Husband ---------------------------------------------------
						if ( isset($people["husb"])) {

							//-- Spouse Husbands Parents --------------------------------------
							$gparent=Person::getInstance($people["husb"]->getXref());
							$fams = $gparent->getChildFamilies();
							foreach($fams as $famid=>$family) {
								if (!is_null($family)) {
									$phusb = $family->getHusband($gparent);
									$pwife = $family->getWife($gparent);
								}
								if ($phusb) { $HusbFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $HusbMBP = $pwife->getBirthPlace(); }
							}

							//-- Spouse Husbands Details --------------------------------------
							$married = GedcomDate::Compare($censdate, $marrdate);
							$nam     = $people["husb"]->getAllNames();
							$fulln   = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln   = str_replace("@N.N.", "(".i18n::translate('unknown').")", $fulln);
							$fulln   = str_replace("@P.N.", "(".i18n::translate('unknown').")", $fulln);
							$givn    = rtrim($nam[0]['givn'],'*');
							$surn    = $nam[0]['surname'];
							if (isset($nam[1])) {
								$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
								$marn  = $nam[1]['surname'];
							}
							$menu = new Menu($people["husb"]->getLabel()."\n");
							$slabel  = print_pedigree_person_nav2($people["husb"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++, $people["husb"]->getLabel(), $censyear);
							$slabel .= $parentlinks;
							$submenu = new Menu($slabel);
							$menu->addSubMenu($submenu);
							if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
							if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
							?>
							<tr class="fact_value">
								<td align="left" nowrap="nowrap" class="linkcell optionbox<?php print $styleadd; ?>">
									<font size=1>
										<?php
										if ($people["husb"]->getXref()==$pid) {
											print "&nbsp" .($people["husb"]->getLabel());
										}else{
											$menu->printMenu();
										}
										?>
									</font>
								</td>
								<td align="left" class="facts_value">
									<font size=1>
										<?php
										print "<a href=\"".encode_url("edit_interface.php?action=addnewnote_assisted&noteid=newnote&pid=".$people["husb"]->getXref()."&gedcom={$GEDCOM}")."\">";
										print $headImg2;
										print "</a>";
										?>
									</font>
								</td>
								<td align="left" class="facts_value" nowrap="nowrap">
									<font size=1>
									<?php
									if ( ($people["husb"]->canDisplayDetails()) ) {
									?>
									<a href='javaScript:insertRowToTable("<?php
											print $people["husb"]->getXref() ;											 // pid = PID
										?>", "<?php 
											echo addslashes($fulln);													 // nam = Full Name
										?>", "<?php 
											if (isset($nam[1])){
												echo addslashes($fulmn);												 // mnam = Full Married Name
											}else{
												echo addslashes($fulln);												 // mnam = Full Name
											}
										?>", "<?php
											if ($people["husb"]->getXref()==$pid) {
												print "Head";															 // label = Relationship
											}else{
												print $people["husb"]->getLabel();										 // label = Relationship
											}
										?>", "<?php
											print PrintReady($people["husb"]->getSex());								 // gend = Gender
										?>", "<?php
											if ($married>=0){
												echo "M";																 // cond = Condition (Married)
											}else{
												echo "S";																 // cond = Condition (Single)
											}
										?>", "<?php
											if ($marrdate) {
												echo ($marrdate->minJD()+$marrdate->maxJD())/2;	 						 // dom = Date of Marriage (Julian)
											}
										?>", "<?php
											echo ($people["husb"]->getBirthDate()->minJD()+$people["husb"]->getBirthDate()->maxJD())/2;	 // dob = Date of Birth (Julian)
										?>", "<?php
											print PrintReady($censyear-$people["husb"]->getbirthyear());				 // age = Census Date minus YOB
										?>", "<?php
											echo ($people["husb"]->getDeathDate()->minJD()+$people["husb"]->getDeathDate()->maxJD())/2;	 // dod = Date of Death (Julian)
										?>", "<?php
											print "";																	 // occu = Occupation
										?>", "<?php
											print PrintReady($people["husb"]->getBirthPlace());							 //  birthpl = Husband Place of Birth 
										?>", "<?php
											if (isset($HusbFBP)) {
												print PrintReady($HusbFBP);												 // fbirthpl = Husband Father's Place of Birth
											} else {
												print PrintReady('UNK, UNK, UNK, UNK');									 // fbirthpl = Husband Father's Place of Birth Not known
											}
										?>", "<?php
											if (isset($HusbMBP)) {
												print PrintReady($HusbMBP);												 // mbirthpl = Husband Mother's Place of Birth
											} else {
												print PrintReady('UNK, UNK, UNK, UNK');									 // mbirthpl = Husband Mother's Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $people["husb"]->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo PrintReady($chBLDarray);											 // Array of Children (name, birthdate, deathdate)
											} else {
												echo PrintReady('');
											}
										?>");'>
										<?php 
											print PrintReady($people["husb"]->getFullName()); 							 // Full Name (Link)
										?> 
									</a>
									<?php print "\n" ;
									}else{
										print i18n::translate('Private');
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
							$gparent=Person::getInstance($people["wife"]->getXref());
							$fams = $gparent->getChildFamilies();
							foreach($fams as $famid=>$family) {
								if (!is_null($family)) {
									$husb = $family->getHusband($gparent);
									$wife = $family->getWife($gparent);
								}
								if ($husb) { $WifeFBP = $husb->getBirthPlace(); }
								if ($wife) { $WifeMBP = $wife->getBirthPlace(); }
							}

							//-- Spouse Wifes Details --------------------------------------
							$married = GedcomDate::Compare($censdate, $marrdate);
							$nam     = $people["wife"]->getAllNames();
							$fulln   = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							//$fulln   = str_replace('"', '\"', $fulln);
							$fulln   = str_replace("@N.N.", "(".i18n::translate('unknown').")", $fulln);
							$fulln   = str_replace("@P.N.", "(".i18n::translate('unknown').")", $fulln);
							$givn    = rtrim($nam[0]['givn'],'*');
							$surn    = $nam[0]['surname'];
							// Get wifes married name if available
							if (isset($people["husb"])){
								$husbnams = $people["husb"]->getAllNames();
								if ($husbnams[0]['surname']=="@N.N." || $husbnams[0]['surname']=="") {
									// Husband or his name is not known
								} else {
									$husbnam = $people["husb"]->getAllNames();
								}
							}
							if (isset($nam[1]) && isset($husbnam)) {
								$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$husbnam[0]['surname'];
								//$fulmn   = str_replace('"', '\"', $fulmn);
							}else{
								$fulmn = $fulln;
								//$fulmn   = str_replace('"', '\"', $fulmn);
							}
							$menu = new Menu($people["wife"]->getLabel()."\n");
							$slabel  = print_pedigree_person_nav2($people["wife"]->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++, $people["wife"]->getLabel(), $censyear);
							$slabel .= $parentlinks;
							$submenu = new Menu($slabel);
							$menu->addSubMenu($submenu);
							if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; }else{ $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
							if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; }else{ $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
							?>
							<tr>
								<td align="left" nowrap="nowrap" class="linkcell optionbox<?php print $styleadd; ?>">
									<font size=1>
										<?php
										if ($people["wife"]->getXref()==$pid) {
											print "&nbsp" .($people["wife"]->getLabel());
										}else{
											$menu->printMenu();
										}
										?>
									</font>
								</td>
								<td align="left" class="facts_value">
									<font size=1>
										<?php 
										print "<a href=\"".encode_url("edit_interface.php?action=addnewnote_assisted&noteid=newnote&pid=".$people["wife"]->getXref()."&gedcom={$GEDCOM}")."\">";
										print $headImg2;
										print "</a>";
										?>
									</font>
								</td>
								<td align="left" class="facts_value" nowrap="nowrap">
									<font size=1>
									<?php
									if ( ($people["wife"]->canDisplayDetails()) ) {
									?>
										<a href='javaScript:insertRowToTable("<?php 
												print $people["wife"]->getXref() ;										 // pid = PID
										?>", "<?php 
											echo addslashes($fulln);													 // nam = Full Name
										?>", "<?php 
											if (isset($nam[1])){
												echo addslashes($fulmn);												 // mnam = Full Married Name
											}else{
												echo addslashes($fulln);												 // mnam = Full Name
											}
										?>", "<?php
											if ($people["wife"]->getXref()==$pid) {
												print "Head";															 // label = Head
											}else{
												print PrintReady($people["wife"]->getLabel());							 // label = Relationship
											}
										?>", "<?php
											print PrintReady($people["wife"]->getSex());								 // gend = Gender
										?>", "<?php
											if ($married>=0 && isset($nam[1])){
												echo "M";																 // cond = Condition (Married)
											}else{
												echo "S";																 // cond = Condition (Single)
											}
										?>", "<?php
											if ($marrdate) {
												echo ($marrdate->minJD()+$marrdate->maxJD())/2;	 						 // dom = Date of Marriage (Julian)
											}
										?>", "<?php
											echo ($people["wife"]->getBirthDate()->minJD()+$people["wife"]->getBirthDate()->maxJD())/2;	 // dob = Date of Birth (Julian)
										?>", "<?php
											print PrintReady($censyear-$people["wife"]->getbirthyear());				 // age = Census Date minus YOB
										?>", "<?php
											echo ($people["wife"]->getDeathDate()->minJD()+$people["wife"]->getDeathDate()->maxJD())/2;	 // dod = Date of Death (Julian)
										?>", "<?php
											print "";																	 // occu = Occupation
										?>", "<?php
											print PrintReady($people["wife"]->getBirthPlace());							 //  birthpl = Wife Place of Birth 
										?>", "<?php
											if (isset($WifeFBP)) {
												print PrintReady($WifeFBP);												 // fbirthpl = Wife Father's Place of Birth
											} else {
												print PrintReady('UNK, UNK, UNK, UNK');									 // fbirthpl = Wife Father's Place of Birth Not known
											}
										?>", "<?php
											if (isset($WifeMBP)) {
												print PrintReady($WifeMBP);												 // mbirthpl = Wife Mother's Place of Birth
											} else {
												print PrintReady('UNK, UNK, UNK, UNK');									 // mbirthpl = Wife Mother's Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $people["wife"]->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo PrintReady($chBLDarray);											 // Array of Children (name, birthdate, deathdate)
											} else {
												echo PrintReady('');
											}
										?>");'>
										<?php 
											print PrintReady($people["wife"]->getFullName()); 							 // Full Name (Link)
										?>
										</a>
										<?php print "\n" ;
									}else{
										print i18n::translate('Private');
									}
									?>
									</font>
								</td>
							<tr> <?php
						}
							
						// Spouse Children
						foreach($people["children"] as $key=>$child) {

							// Get Spouse child's marriage status
							$married="";
							$marrdate="";
							foreach ($child->getSpouseFamilies() as $childfamily) {
								$marrdate=$childfamily->getMarriageDate();
								$married = GedcomDate::Compare($censdate, $marrdate);
							}

							// Get Child's Children
							$chBLDarray=Array();
							foreach ($child->getSpouseFamilies() as $childfamily) {
								$chchildren = $childfamily->getChildren();
								foreach ($chchildren as $key=>$chchild) {
									$chnam   = $chchild->getAllNames();
									$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
									$chfulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $chfulln);
									$chfulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $chfulln);
									$chfulln = addslashes($chfulln);														// Child's Full Name// Child's Full Name
									$chdob   = ($chchild->getBirthDate()->minJD()+$chchild->getBirthDate()->maxJD())/2;		// Child's Date of Birth (Julian)
									$chdod   = ($chchild->getDeathDate()->minJD()+$chchild->getDeathDate()->maxJD())/2;		// Child's Date of Death (Julian)
									$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
									array_push($chBLDarray, $chBLD);
								}
							}

							// Get Spouse child's details
							$nam   = $child->getAllNames();
							$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
							$fulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $fulln);
							$fulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $fulln);
							$givn  = rtrim($nam[0]['givn'],'*');
							$surn  = $nam[0]['surname'];
							if (isset($nam[1])) {
								$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
								$marn  = $nam[1]['surname'];
							}else{
								$fulmn = $fulln;
								$marn  = $surn;
							}
							$menu = new Menu($child->getLabel()."\n");
							$slabel = print_pedigree_person_nav2($child->getXref(), 2, !$this->isPrintPreview(), 0, $personcount++, $child->getLabel(), $censyear);
							$slabel .= $spouselinks;
							$submenu = new Menu($slabel);
							$menu->addSubmenu($submenu);
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<font size=1>
									<?php if ($WT_MENUS_AS_LISTS) echo "<ul>\n";
									$menu->printMenu();
									if ($WT_MENUS_AS_LISTS) echo "</ul>\n";
									?>
									</font>
								</td>
								<td align="left" class="facts_value">
									<font size=1>
										<?php
										print "<a href=\"".encode_url("edit_interface.php?action=addnewnote_assisted&noteid=newnote&pid=".$child->getXref()."&gedcom={$GEDCOM}")."\">";
										print $headImg2;
										print "</a>";
										?>
									</font>
								</td>
								<td align="left" class="facts_value" nowrap="nowrap">
									<font size=1>
									<?php
									if ( ($child->canDisplayDetails()) ) {
									?>
									<a href='javaScript:insertRowToTable("<?php 
											print $child->getXref() ;													 // pid = PID
										?>", "<?php 
											echo addslashes($fulln);													 // nam = Full Name
										?>", "<?php 
											if (isset($nam[1])){
												echo addslashes($fulmn);												 // mnam = Full Married Name
											}else{
												echo addslashes($fulln);												 // mnam = Full Name
											}
										?>", "<?php
											print PrintReady($child->getLabel());										 // label = Relationship
										?>", "<?php
											print PrintReady($child->getSex());											 // gend = Gender
										?>", "<?php
											if ($married>0) {
												echo "M";																 // cond = Condition (Married)
											} else if ($married<0 || ($married=="0") ) {
												echo "S";																 // cond = Condition (Single)
											} else {
												echo "";																 // cond = Condition (Not Known)
											}
										?>", "<?php
											if ($marrdate) {
												echo ($marrdate->minJD()+$marrdate->maxJD())/2;	 						 // dom = Date of Marriage (Julian)
											}
										?>", "<?php
											echo ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2;	 // dob = Date of Birth (Julian)
										?>", "<?php
											print PrintReady($censyear-$child->getbirthyear());							 //  age = Census Date minus YOB
										?>", "<?php
											echo ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2;	 // dod = Date of Death (Julian)
										?>", "<?php
											print "";																	 // occu = Occupation
										?>", "<?php
											print PrintReady($child->getBirthPlace());									 //  birthpl = Child Place of Birth 
										?>", "<?php
											if (isset($people["husb"])) {
												print PrintReady($people["husb"]->getBirthPlace());						 // fbirthpl = Child Father's Place of Birth 
											} else {
												print PrintReady('UNK, UNK, UNK, UNK');									 // fbirthpl = Child Father's Place of Birth Not known
											}
										?>", "<?php
											if (isset($people["wife"])) {
												print PrintReady($people["wife"]->getBirthPlace());						 // mbirthpl = Child Mother's Place of Birth
											} else {
												print PrintReady('UNK, UNK, UNK, UNK');									 // mbirthpl = Child Mother's Place of Birth Not known
											}
										?>", "<?php
											if (isset($chBLDarray) && $child->getSex()=="F") {
												$chBLDarray = implode("::", $chBLDarray);
												echo PrintReady($chBLDarray);											 // Array of Children (name, birthdate, deathdate)
											} else {
												echo PrintReady('');
											}
										?>");'>
										<?php 
											print PrintReady($child->getFullName()); 									 // Full Name (Link)
										?>
									</a>
									<?php print "\n" ;
								}else{
									print i18n::translate('Private');
								}
								?>
									</font>
								</td>
							</tr>
							<?php
						} 
						
					print "<tr><td><font size=1><br /></font></td></tr>";
					}
					?>
						
						</table>
					<br /><br /><br />&nbsp;</td>
				</tr>
			</table>
			
<?php
// ==================================================================
require_once 'includes/functions/functions_charts.php';
/**
 * print the information for an individual chart box
 *
 * find and print a given individuals information for a pedigree chart
 * @param string $pid	the Gedcom Xref ID of the   to print
 * @param int $style	the style to print the box in, 1 for smaller boxes, 2 for larger boxes
 * @param boolean $show_famlink	set to true to show the icons for the popup links and the zoomboxes
 * @param int $count	on some charts it is important to keep a count of how many boxes were printed
 */

function print_pedigree_person_nav2($pid, $style=1, $show_famlink=true, $count=0, $personcount="1", $currpid, $censyear) {
	global $HIDE_LIVE_PEOPLE, $SHOW_LIVING_NAMES, $ZOOM_BOXES, $LINK_ICONS, $SCRIPT_NAME, $GEDCOM;
	global $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES, $bwidth, $bheight, $PEDIGREE_FULL_DETAILS, $SHOW_ID_NUMBERS, $SHOW_PEDIGREE_PLACES;
	global $TEXT_DIRECTION, $DEFAULT_PEDIGREE_GENERATIONS, $OLD_PGENS, $talloffset, $PEDIGREE_LAYOUT, $MEDIA_DIRECTORY;
	global $WT_IMAGE_DIR, $WT_IMAGES, $ABBREVIATE_CHART_LABELS, $USE_MEDIA_VIEWER;
	global $chart_style, $box_width, $generations, $show_spouse, $show_full;
	global $CHART_BOX_TAGS, $SHOW_LDS_AT_GLANCE, $PEDIGREE_SHOW_GENDER;
	global $SEARCH_SPIDER;
	
	global $spouselinks, $parentlinks, $step_parentlinks, $persons, $person_step, $person_parent, $tabno, $theme_name, $spousetag;
	global $natdad, $natmom, $censyear, $censdate;
	// global $pHusbFBP, $pHusbMBP, $pWifeFBP, $pWifeMBP;
	// global $phusb, $pwife, $pwhusb, $pwwife;

	if ($style != 2) $style=1;
	if (empty($show_full)) $show_full = 0;
	if (empty($PEDIGREE_FULL_DETAILS)) $PEDIGREE_FULL_DETAILS = 0;

	if (!isset($OLD_PGENS)) $OLD_PGENS = $DEFAULT_PEDIGREE_GENERATIONS;
	if (!isset($talloffset)) $talloffset = $PEDIGREE_LAYOUT;

	$person=Person::getInstance($pid);
	if ($pid==false || empty($person)) {
		$spouselinks 		= false;
		$parentlinks 		= false;
		$step_parentlinks	= false;
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
				//-- draw a box for the family popup
				
				if ($TEXT_DIRECTION=="rtl") {
				$spouselinks .= "\n\t\t\t<table class=\"rtlnav person_box$isF\"><tr><td align=\"right\" style=\"font-size:10px;font-weight:normal;\" class=\"name2\" nowrap=\"nowrap\">";
				$spouselinks .= "<b>" . i18n::translate('Family') . "</b> (" .$person->getFullName(). ")<br />";
				$parentlinks .= "\n\t\t\t<table class=\"rtlnav person_box$isF\"><tr><td align=\"right\" style=\"font-size:10px;font-weight:normal;\" class=\"name2\" nowrap=\"nowrap\">";
				$parentlinks .= "<b>" . i18n::translate('Parents') . "</b> (" .$person->getFullName(). ")<br />";
				$step_parentlinks .= "\n\t\t\t<table class=\"rtlnav person_box$isF\"><tr><td align=\"right\" style=\"font-size:10px;font-weight:normal;\" class=\"name2\" nowrap=\"nowrap\">";
				$step_parentlinks .= "<b>" . i18n::translate('Parents') . "</b> (" .$person->getFullName(). ")<br />";
				}else{
				$spouselinks .= "\n\t\t\t<table class=\"ltrnav person_box$isF\"><tr><td align=\"left\" style=\"font-size:10px;font-weight:normal;\" class=\"name2\" nowrap=\"nowrap\">";
				$spouselinks .= "<b>" . i18n::translate('Family') . "</b> (" .$person->getFullName(). ")<br />";
				$parentlinks .= "\n\t\t\t<table class=\"ltrnav person_box$isF\"><tr><td align=\"left\" style=\"font-size:10px;font-weight:normal;\" class=\"name2\" nowrap=\"nowrap\">";
				$parentlinks .= "<b>" . i18n::translate('Parents') . "</b> (" .$person->getFullName(). ")<br />";
				$step_parentlinks .= "\n\t\t\t<table class=\"ltrnav person_box$isF\"><tr><td align=\"left\" style=\"font-size:10px;font-weight:normal;\" class=\"name2\" nowrap=\"nowrap\">";
				$step_parentlinks .= "<b>" . i18n::translate('Parents') . "</b> (" .$person->getFullName(). ")<br />";
				}

				$persons       = "";
				$person_parent = "";
				$person_step   = "";
				
				//-- Parent families --------------------------------------
				$fams = $person->getChildFamilies();
				foreach($fams as $famid=>$family) {
					$marrdate = $family->getMarriageDate();
					$married  = GedcomDate::Compare($censdate, $marrdate);
					
					if (!is_null($family)) {
						$husb = $family->getHusband($person);
						$wife = $family->getWife($person);
						$children = $family->getChildren();
						$num = count($children);
						$marrdate = $family->getMarriageDate();

						//-- Get Parent Children's Name, DOB, DOD --------------------------
						if (isset($children)) {
							$chBLDarray = Array();
							foreach ($children as $key=>$child) {
								$chnam   = $child->getAllNames();
								$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
								$chfulln = str_replace('"', "", $chfulln);											// Must remove quotes completely here
								$chfulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $chfulln);
								$chfulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $chfulln);			// Child's Full Name
								$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2;		// Child's Date of Birth (Julian)
								$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2;		// Child's Date of Death (Julian)
								$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
								array_push($chBLDarray, $chBLD);
							}
						}

						//-- Parent Husband ------------------------------
						if ($husb || $num>0) {
							if ($TEXT_DIRECTION=="ltr") { 
								$title = i18n::translate('Family book chart').": ".$famid;
							}else{
								$title = $famid." :".i18n::translate('Family book chart');
							}
							if ($husb) {
								//-- Parent Husbands Parents ----------------------
								$gparent=Person::getInstance($husb->getXref());
								$parfams = $gparent->getChildFamilies();
								foreach($parfams as $famid=>$pfamily) {
									if (!is_null($pfamily)) {
										$phusb = $pfamily->getHusband($gparent);
										$pwife = $pfamily->getWife($gparent);
									}
									if ($phusb) { $pHusbFBP = $phusb->getBirthPlace(); }
									if ($pwife) { $pHusbMBP = $pwife->getBirthPlace(); }
								}
								//-- Parent Husbands Details ----------------------
								$person_parent="Yes";
								if ($TEXT_DIRECTION=="ltr") { 
									$title = i18n::translate('Individual information').": ".$husb->getXref();
								}else{
									$title = $husb->getXref()." :".i18n::translate('Individual information');
								}
								$tmp=$husb->getXref();
								if ($husb->canDisplayName()) {
									$nam   = $husb->getAllNames();
									$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
									$fulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $fulln);
									$fulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $fulln);
									$givn  = rtrim($nam[0]['givn'],'*');
									$surn  = $nam[0]['surn'];
									if (isset($nam[1]) ) {
										$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surn'];
										$marn  = $nam[1]['surn'];
									}
									$parentlinks .= "<a class=\"linka\" href=\"javascript:insertRowToTable(";
									$parentlinks .= "'".PrintReady($husb->getXref())."',";							// pid		=	PID
									$parentlinks .=	"'".addslashes(strip_tags($fulln))."',";						// nam		=	Name
									if (isset($nam[1])){
										$parentlinks .= "'".addslashes(strip_tags($fulmn))."',";					// mnam		=	Full Married Name
									} else {
										$parentlinks .= "'".addslashes(strip_tags($fulln))."',";	 				// mnam		=	Full Name
									}
									if ($currpid=="Wife" || $currpid=="Husband") {
										$parentlinks .= "'Father in Law',";											// label	=	1st Gen Male Relationship
									}else{
										$parentlinks .= "'Grand-Father',";											// label	=	2st Gen Male Relationship
									}
									$parentlinks .= "'".PrintReady($husb->getSex())."',";							// sex	=	Gender
									$parentlinks .= "''".",";														// cond	=	Condition (Married etc)
									if ($marrdate) {
										$parentlinks .= "'".(($marrdate->minJD()+$marrdate->maxJD())/2)."',";		// dom = Date of Marriage (Julian)
									}
									$parentlinks .= "'".(($husb->getBirthDate()->minJD()+$husb->getBirthDate()->maxJD())/2)."',";	// dob	=	Date of Birth
									if ($husb->getbirthyear()>=1) {
										$parentlinks .=	"'".PrintReady($censyear-$husb->getbirthyear())."',";		// age	= 	Census Year - Year of Birth
									} else {
										$parentlinks .= "''".",";													// age	= 	Undefined
									}
									$parentlinks .= "'".(($husb->getDeathDate()->minJD()+$husb->getDeathDate()->maxJD())/2)."',";	// dod	=	Date of Death
									$parentlinks .= "''".",";														// occu 	=	Occupation
									$parentlinks .= "'".PrintReady($husb->getBirthPlace())."'".",";					// birthpl	=	Individuals Birthplace
									if (isset($pHusbFBP)) {
										$parentlinks .= "'".$pHusbFBP."'".",";										// fbirthpl	=	Fathers Birthplace
									} else {
										$parentlinks .= "'UNK, UNK, UNK, UNK'".",";									// fbirthpl	=	Fathers Birthplace
									}
									if (isset($pHusbMBP)) {
										$parentlinks .= "'".$pHusbMBP."'".",";										// mbirthpl	=	Mothers Birthplace
									} else {
										$parentlinks .= "'UNK, UNK, UNK, UNK'".",";									// mbirthpl	=	Mothers Birthplace
									}
									if (isset($chBLDarray) && $husb->getSex()=="F") {
										$chBLDarray = implode("::", $chBLDarray);
										$parentlinks .= "'".$chBLDarray."'";										// Array of Children (name, birthdate, deathdate)
									} else {
										$parentlinks .= "''";
									}
									$parentlinks .= ");\">";
									$parentlinks .= PrintReady($husb->getFullName());								// Full Name (Link)
									$parentlinks .= "</a>";
								}else{
									$parentlinks .= i18n::translate('Private');
								}
								$parentlinks .= "\n";
								$natdad = "yes";
							}
						}

						//-- Parent Wife ------------------------------
						if ($wife || $num>0) {
							if ($TEXT_DIRECTION=="ltr") { 
								$title = i18n::translate('Family book chart').": ".$famid;
							}else{
								$title = $famid." :".i18n::translate('Family book chart');
							}
							if ($wife) {
								//-- Parent Wifes Parents ----------------------
								$gparent=Person::getInstance($wife->getXref());
								$parfams = $gparent->getChildFamilies();
								foreach($parfams as $famid=>$pfamily) {
									if (!is_null($pfamily)) {
										$pwhusb = $pfamily->getHusband($gparent);
										$pwwife = $pfamily->getWife($gparent);
									}
									if ($pwhusb) { $pWifeFBP = $pwhusb->getBirthPlace(); }
									if ($pwwife) { $pWifeMBP = $pwwife->getBirthPlace(); }
								}
								//-- Parent Wifes Details ----------------------
								$person_parent="Yes";
								if ($TEXT_DIRECTION=="ltr") { 
									$title = i18n::translate('Individual information').": ".$wife->getXref();
								} else {
									$title = $wife->getXref()." :".i18n::translate('Individual information');
								}
								$tmp=$wife->getXref();
								if ($wife->canDisplayName()) {
									$married = GedcomDate::Compare($censdate, $marrdate);
									$nam   = $wife->getAllNames();
									$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
									$fulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $fulln);
									$fulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $fulln);
									$givn  = rtrim($nam[0]['givn'],'*');
									$surn  = $nam[0]['surname'];
									// Get wifes married name if available
									if (isset($husb)){
										$husbnams = $husb->getAllNames();
										if ($husbnams[0]['surname']=="@N.N." || $husbnams[0]['surname']=="") {
											// Husband or his name is not known
										} else {
											$husbnam = $husb->getAllNames();
										}
									}
									if (isset($nam[1]) && isset($husbnam)) {
										$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$husbnam[0]['surname'];
									}else{
										$fulmn = $fulln;
									}
									$parentlinks .= "<a class=\"linka\" href=\"javascript:insertRowToTable(";
									$parentlinks .=	"'".PrintReady($wife->getXref())."',";							// pid		=	PID
									$parentlinks .=	"'".addslashes(strip_tags($fulln))."',";						// nam		=	Name
									if (isset($nam[1])){
										$parentlinks .= "'".addslashes(strip_tags($fulmn))."',";					// mnam		=	Full Married Name
									} else {
										$parentlinks .= "'".addslashes(strip_tags($fulln))."',";					// mnam		=	Full Name
									}
									if ($currpid=="Wife" || $currpid=="Husband") {
										$parentlinks .= "'Mother in Law',";											// label	=	1st Gen Female Relationship
									} else {
										$parentlinks .= "'Grand-Mother',";											// label	=	2st Gen Female Relationship
									}
									$parentlinks .=	"'".PrintReady($wife->getSex())."',";							// sex		=	Gender
									$parentlinks .=	"''".",";														// cond		=	Condition (Married etc)
									if ($marrdate) {
										$parentlinks .= "'".(($marrdate->minJD()+$marrdate->maxJD())/2)."',";		// dom = Date of Marriage (Julian)
									}
									$parentlinks .= "'".(($wife->getBirthDate()->minJD()+$wife->getBirthDate()->maxJD())/2)."',";	// dob	=	Date of Birth
									if ($wife->getbirthyear()>=1) {
										$parentlinks .=	"'".PrintReady($censyear-$wife->getbirthyear())."',";		// age		= 	Census Year - Year of Birth
									} else {
										$parentlinks .=	"''".",";													// age		= 	Undefined
									}
									$parentlinks .= "'".(($wife->getDeathDate()->minJD()+$wife->getDeathDate()->maxJD())/2)."',";	// dod	=	Date of Death
									$parentlinks .=	"''".",";														// occu 	=	Occupation
									$parentlinks .= "'".PrintReady($wife->getBirthPlace())."'".",";					// birthpl	=	Individuals Birthplace
									if (isset($pWifeFBP)) {
										$parentlinks .= "'".$pWifeFBP."'".",";										// fbirthpl	=	Fathers Birthplace
									} else {
										$parentlinks .= "'UNK, UNK, UNK, UNK'".",";									// fbirthpl	=	Fathers Birthplace Not Known
									}
									if (isset($pWifeMBP)) {
										$parentlinks .= "'".$pWifeMBP."'".",";										// mbirthpl	=	Mothers Birthplace
									} else {
										$parentlinks .= "'UNK, UNK, UNK, UNK'".",";									// mbirthpl	=	Mothers Birthplace Not Known
									}
									if (isset($chBLDarray) && $wife->getSex()=="F") {
										$chBLDarray = implode("::", $chBLDarray);
										$parentlinks .= "'".$chBLDarray."'";										// Array of Children (name, birthdate, deathdate)
									} else {
										$parentlinks .= "''";
									}
									$parentlinks .=	");\">";
									$parentlinks .= PrintReady($wife->getFullName());								// Full Name (Link)
									$parentlinks .= "</a>";
								}else{
									$parentlinks .= i18n::translate('Private');
								}
								$parentlinks .= "\n";
								$natmom = "yes";
							}
						}
					}
				}

				//-- Step families -----------------------------------------
				$fams = $person->getStepFamilies();
				foreach($fams as $famid=>$family) {
					$marrdate = $family->getMarriageDate();
					$married  = GedcomDate::Compare($censdate, $marrdate);
					if (!is_null($family)) {
						$husb = $family->getHusband($person);
						$wife = $family->getWife($person);
						$children = $family->getChildren();
						$num = count($children);
						$marrdate = $family->getMarriageDate();

						//-- Get StepParent's Children's Name, DOB, DOD --------------------------
						if (isset($children)) {
							$chBLDarray = Array();
							foreach ($children as $key=>$child) {
								$chnam   = $child->getAllNames();
								$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
								$chfulln = str_replace('"', "", $chfulln);											// Must remove quotes completely here
								$chfulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $chfulln);
								$chfulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $chfulln);			// Child's Full Name
								$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2;		// Child's Date of Birth (Julian)
								$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2;		// Child's Date of Death (Julian)
								$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
								array_push($chBLDarray, $chBLD);
							}
						}

						//-- Step Husband --------------------------------------
						if ($natdad == "yes") {
						}else{
							if ( ($husb || $num>0) && $husb->getLabel() != "." ) {
								if ($TEXT_DIRECTION=="ltr") { 
									$title = i18n::translate('Family book chart').": ".$famid;
								}else{
									$title = $famid." :".i18n::translate('Family book chart');
								}
								if ($husb) {
									//-- Step Husbands Parents -----------------------------
									$gparent=Person::getInstance($husb->getXref());
									$parfams = $gparent->getChildFamilies();
									foreach($parfams as $famid=>$pfamily) {
										if (!is_null($pfamily)) {
											$phusb = $pfamily->getHusband($gparent);
											$pwife = $pfamily->getWife($gparent);
										}
										if ($phusb) { $pHusbFBP = $phusb->getBirthPlace(); }
										if ($pwife) { $pHusbMBP = $pwife->getBirthPlace(); }
									}
									//-- Step Husband Details ------------------------------
									$person_step="Yes";
									if ($TEXT_DIRECTION=="ltr") {
										$title = i18n::translate('Individual information').": ".$husb->getXref();
									}else{
										$title = $husb->getXref()." :".i18n::translate('Individual information');
									}
									$tmp=$husb->getXref();
									if ($husb->canDisplayName()) {
										$nam   = $husb->getAllNames();
										$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
										$fulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $fulln);
										$fulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $fulln);
										//$fulln = strip_tags($husb->getFullName());
										$givn  = rtrim($nam[0]['givn'],'*');
										$surn  = $nam[0]['surname'];
										if (isset($nam[1])) {
											$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
											$marn  = $nam[1]['surname'];
										}
									$parentlinks .= "<a class=\"linka\" href=\"javascript:insertRowToTable(";
									$parentlinks .= "'".PrintReady($husb->getXref())."',";							// pid		=	PID
									$parentlinks .=	"'".addslashes(strip_tags($fulln))."',";						// nam		=	Name
									if (isset($nam[1])){
										$parentlinks .= "'".addslashes(strip_tags($fulmn))."',";					// mnam		=	Full Married Name
									} else {
										$parentlinks .= "'".addslashes(strip_tags($fulln))."',";					// mnam		=	Full Name
									}
									if ($currpid=="Wife" || $currpid=="Husband") {
										$parentlinks .= "'Step Father-in-Law',";									// label	=	1st Gen Male Relationship
									}else{
										$parentlinks .= "'Step Grand-Father',";										// label	=	2st Gen Male Relationship
									}
									$parentlinks .= "'".PrintReady($husb->getSex())."',";							// sex	=	Gender
									$parentlinks .= "''".",";														// cond	=	Condition (Married etc)
									if ($marrdate) {
										$parentlinks .= "'".(($marrdate->minJD()+$marrdate->maxJD())/2)."',";		// dom = Date of Marriage (Julian)
									}
									$parentlinks .= "'".(($husb->getBirthDate()->minJD()+$husb->getBirthDate()->maxJD())/2)."',";	// dob	=	Date of Birth
									if ($husb->getbirthyear()>=1) {
										$parentlinks .=	"'".PrintReady($censyear-$husb->getbirthyear())."',";		// age	= 	Census Year - Year of Birth
									} else {
										$parentlinks .= "''".",";													// age	= 	Undefined
									}
									$parentlinks .= "'".(($husb->getDeathDate()->minJD()+$husb->getDeathDate()->maxJD())/2)."',";	// dod	=	Date of Death
									$parentlinks .= "''".",";														// occu 	=	Occupation
									$parentlinks .= "'".PrintReady($husb->getBirthPlace())."'".",";					// birthpl	=	Individuals Birthplace
									if (isset($pHusbFBP)) {
										$parentlinks .= "'".$pHusbFBP."'".",";										// fbirthpl	=	Fathers Birthplace
									} else {
										$parentlinks .= "'UNK, UNK, UNK, UNK'".",";									// fbirthpl	=	Fathers Birthplace
									}
									if (isset($pHusbMBP)) {
										$parentlinks .= "'".$pHusbMBP."'".",";										// mbirthpl	=	Mothers Birthplace
									} else {
										$parentlinks .= "'UNK, UNK, UNK, UNK'".",";									// mbirthpl	=	Mothers Birthplace
									}
									if (isset($chBLDarray) && $husb->getSex()=="F") {
										$chBLDarray = implode("::", $chBLDarray);
										$parentlinks .= "'".$chBLDarray."'";										// Array of Children (name, birthdate, deathdate)
									} else {
										$parentlinks .= "''";
									}
									$parentlinks .= ");\">";
									$parentlinks .= PrintReady($husb->getFullName());								// Full Name (Link)
									$parentlinks .= "</a>";
									}else{
										$parentlinks .= i18n::translate('Private');
									}
									$parentlinks .= "\n";
								}
							}
						}
						
						//-- Step Wife ----------------------------------------
						if ($natmom == "yes") {
						}else{
							if ($wife || $num>0) {
								if ($TEXT_DIRECTION=="ltr") {
									$title = i18n::translate('Family book chart').": ".$famid;
								}else{
									$title = $famid." :".i18n::translate('Family book chart');
								}
								if ($wife) {
									//-- Step Wifes Parents ---------------------------
									$gparent=Person::getInstance($wife->getXref());
									$parfams = $gparent->getChildFamilies();
									foreach($parfams as $famid=>$pfamily) {
										if (!is_null($pfamily)) {
											$pwhusb = $pfamily->getHusband($gparent);
											$pwwife = $pfamily->getWife($gparent);
										}
										if ($pwhusb) { $pWifeFBP = $pwhusb->getBirthPlace(); }
										if ($pwwife) { $pWifeMBP = $pwwife->getBirthPlace(); }
									}
									//-- Step Wife Details ------------------------------
									$person_step="Yes";
									if ($TEXT_DIRECTION=="ltr") {
										$title = i18n::translate('Individual information').": ".$wife->getXref();
									}else{
										$title = $wife->getXref()." :".i18n::translate('Individual information');
									}
									$tmp=$wife->getXref();
									if ($wife->canDisplayName()) {
										$married = GedcomDate::Compare($censdate, $marrdate);
										$nam   = $wife->getAllNames();
										$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
										$fulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $fulln);
										$fulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $fulln);
										//$fulln = strip_tags($wife->getFullName());
										$givn  = rtrim($nam[0]['givn'],'*');
										$surn  = $nam[0]['surname'];
									// Get wifes married name if available
									if (isset($husb)){
										$husbnams = $husb->getAllNames();
										if ($husbnams[0]['surname']=="@N.N." || $husbnams[0]['surname']=="") {
											// Husband or his name is not known
										} else {
											$husbnam = $husb->getAllNames();
										}
									}
									if (isset($nam[1]) && isset($husbnam)) {
										$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$husbnam[0]['surname'];
									}else{
										$fulmn = $fulln;
									}
									$parentlinks .= "<a class=\"linka\" href=\"javascript:insertRowToTable(";
									$parentlinks .=	"'".PrintReady($wife->getXref())."',";							// pid		=	PID
									$parentlinks .=	"'".addslashes(strip_tags($fulln))."',";						// nam		=	Name
									if (isset($nam[1])){
										$parentlinks .= "'".addslashes(strip_tags($fulmn))."',";					// mnam		=	Full Married Name
									} else {
										$parentlinks .= "'".addslashes(strip_tags($fulln))."',";					// mnam		=	Full Name
									}
									if ($currpid=="Wife" || $currpid=="Husband") {
										$parentlinks .= "'Step Mother-in-Law',";									// label	=	1st Gen Female Relationship
									} else {
										$parentlinks .= "'Step Grand-Mother',";										// label	=	2st Gen Female Relationship
									}
									$parentlinks .=	"'".PrintReady($wife->getSex())."',";							// sex		=	Gender
									$parentlinks .=	"''".",";														// cond		=	Condition (Married etc)
									if ($marrdate) {
										$parentlinks .= "'".(($marrdate->minJD()+$marrdate->maxJD())/2)."',";		// dom = Date of Marriage (Julian)
									}
									$parentlinks .= "'".(($wife->getBirthDate()->minJD()+$wife->getBirthDate()->maxJD())/2)."',";	// dob	=	Date of Birth
									if ($wife->getbirthyear()>=1) {
										$parentlinks .=	"'".PrintReady($censyear-$wife->getbirthyear())."',";		// age		= 	Census Year - Year of Birth
									} else {
										$parentlinks .=	"''".",";													// age		= 	Undefined
									}
									$parentlinks .= "'".(($wife->getDeathDate()->minJD()+$wife->getDeathDate()->maxJD())/2)."',";	// dod	=	Date of Death
									$parentlinks .=	"''".",";														// occu 	=	Occupation
									$parentlinks .= "'".PrintReady($wife->getBirthPlace())."'".",";					// birthpl	=	Individuals Birthplace
									if (isset($pWifeFBP)) {
										$parentlinks .= "'".$pWifeFBP."'".",";										// fbirthpl	=	Fathers Birthplace
									} else {
										$parentlinks .= "'UNK, UNK, UNK, UNK'".",";									// fbirthpl	=	Fathers Birthplace Not Known
									}
									if (isset($pWifeMBP)) {
										$parentlinks .= "'".$pWifeMBP."'".",";										// mbirthpl	=	Mothers Birthplace
									} else {
										$parentlinks .= "'UNK, UNK, UNK, UNK'".",";									// mbirthpl	=	Mothers Birthplace Not Known
									}
									if (isset($chBLDarray) && $wife->getSex()=="F") {
										$chBLDarray = implode("::", $chBLDarray);
										$parentlinks .= "'".$chBLDarray."'";										// Array of Children (name, birthdate, deathdate)
									} else {
										$parentlinks .= "''";
									}
									$parentlinks .=	");\">";
									$parentlinks .= PrintReady($wife->getFullName());								// Full Name (Link)
									$parentlinks .= "</a>";
									}else{
										$parentlinks .= i18n::translate('Private');
									}
									$parentlinks .= "\n";
								}
							}
						}
					}
				}

				// Spouse Families ------------------------------------------
				$fams = $person->getSpouseFamilies();
				foreach($fams as $famid=>$family) {
					if (!is_null($family)) {
						$spouse = $family->getSpouse($person);
						$children = $family->getChildren();
						$num = count($children);
						$marrdate = $family->getMarriageDate();
						$married  = GedcomDate::Compare($censdate, $marrdate);

						//-- Get Spouse's Children's Name, DOB, DOD --------------------------
						if (isset($children)) {
							$chBLDarray = Array();
							foreach ($children as $key=>$child) {
								$chnam   = $child->getAllNames();
								$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
								$chfulln = str_replace('"', "", $chfulln);											// Must remove quotes completely here
								$chfulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $chfulln);
								$chfulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $chfulln);			// Child's Full Name
								$chdob   = ($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2;		// Child's Date of Birth (Julian)
								$chdod   = ($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2;		// Child's Date of Death (Julian)
								$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
								array_push($chBLDarray, $chBLD);
							}
						}
						//-- Spouse -----------------------------------------
						if ($spouse || $num>0) {
							if ($TEXT_DIRECTION=="ltr") {
								$title = i18n::translate('Family book chart').": ".$famid;
							}else{
								$title = $famid." :".i18n::translate('Family book chart');
							}
							if ($spouse) {
							
								//-- Spouse Parents -----------------------------
								$gparent=Person::getInstance($spouse->getXref());
								$spousefams = $gparent->getChildFamilies();
								foreach($spousefams as $famid=>$pfamily) {
									if (!is_null($pfamily)) {
										$phusb = $pfamily->getHusband($gparent);
										$pwife = $pfamily->getWife($gparent);
									}
									if ($phusb) { $pSpouseFBP = $phusb->getBirthPlace(); }
									if ($pwife) { $pSpouseMBP = $pwife->getBirthPlace(); }
								}

								//-- Spouse Details -----------------------------
								if ($TEXT_DIRECTION=="ltr") { 
									$title = i18n::translate('Individual information').": ".$spouse->getXref();
								}else{
									$title = $spouse->getXref()." :".i18n::translate('Individual information');
								}
								$tmp=$spouse->getXref();
								if ($spouse->canDisplayName()) {
									$married = GedcomDate::Compare($censdate, $marrdate);
									$nam   = $spouse->getAllNames();
									$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
									$fulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $fulln);
									$fulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $fulln);
									$givn  = rtrim($nam[0]['givn'],'*');
									$surn  = $nam[0]['surname'];
									if (isset($nam[1])) {
										$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
										$marn  = $nam[1]['surname'];
									}
									$spouselinks .= "<a href=\"javascript:insertRowToTable(";
									$spouselinks .=	"'".PrintReady($spouse->getXref())."',";						// pid		=	PID
									$spouselinks .=	"'".addslashes(strip_tags($fulln))."',";						// nam		=	Name
									if (isset($nam[1])){
										$spouselinks .= "'".addslashes(strip_tags($fulmn))."',";					// mnam		=	Full Married Name
									} else {
										$spouselinks .= "'".addslashes(strip_tags($fulln))."',";					// mnam		=	Full Name
									}
									if ($currpid=="Son" || $currpid=="Daughter") {
										if ($spouse->getSex()=="M") {
											$spouselinks .=	"'Son in Law',";										// label	=	Male Relationship
										}else{
											$spouselinks .=	"'Daughter in Law',";									// label	=	Female Relationship
										}
									} else {
										if ($spouse->getSex()=="M") {
											$spouselinks .=	"'Brother in Law',";									// label	=	Male Relationship
										} else {
											$spouselinks .=	"'Sister in Law',";										// label	=	Female Relationship
										}
									}
									$spouselinks .=	"'".PrintReady($spouse->getSex())."',";							// sex		=	Gender
									$spouselinks .=	"''".",";														// cond		=	Condition (Married etc)
									if ($marrdate) {
										$spouselinks .= "'".(($marrdate->minJD()+$marrdate->maxJD())/2)."',";		// dom = Date of Marriage (Julian)
									}
									$spouselinks .= "'".(($spouse->getBirthDate()->minJD()+$spouse->getBirthDate()->maxJD())/2)."',";	// dob	=	Date of Birth
									if ($spouse->getbirthyear()>=1) {
										$spouselinks .=	"'".PrintReady($censyear-$spouse->getbirthyear())."',";		// age		= 	Census Year - Year of Birth
									} else {
										$spouselinks .=	"''".",";													// age		= 	Undefined
									}
									$spouselinks .= "'".(($spouse->getDeathDate()->minJD()+$spouse->getDeathDate()->maxJD())/2)."',";	// dod	=	Date of Death
									$spouselinks .=	"''".",";														// occu 	=	Occupation
									$spouselinks .= "'".PrintReady($spouse->getBirthPlace())."'".",";				// birthpl	=	Individuals Birthplace
									if (isset($pSpouseFBP)) {
										$spouselinks .= "'".$pSpouseFBP."'".",";									// fbirthpl	=	Fathers Birthplace
									} else {
										$spouselinks .= "'UNK, UNK, UNK, UNK'".",";									// fbirthpl	=	Fathers Birthplace Not Known
									}
									if (isset($pSpouseMBP)) {
										$spouselinks .= "'".$pSpouseMBP."'".",";									// mbirthpl	=	Mothers Birthplace
									} else {
										$spouselinks .= "'UNK, UNK, UNK, UNK'".",";									// mbirthpl	=	Mothers Birthplace Not Known
									}
									if (isset($chBLDarray) && $spouse->getSex()=="F") {
										$chBLDarray = implode("::", $chBLDarray);
										$spouselinks .= "'".$chBLDarray."'";										// Array of Children (name, birthdate, deathdate)
									} else {
										$spouselinks .= "''";
									}
									$spouselinks .=	");\">";
									$spouselinks .= PrintReady($spouse->getFullName());								// Full Name (Link)
									$spouselinks .= "</a>";
								}else{
									$spouselinks .= i18n::translate('Private');
								}
								$spouselinks .= "</a>\n";
								if ($spouse->getFullName() != "") {
									$persons = "Yes";
								}
							}
						}
						
						// Children -------------------------------------
						$spouselinks .= "<ul class=\"clist ".$TEXT_DIRECTION."\">\n";
						foreach($children as $c=>$child) {
							$cpid = $child->getXref();
							if ($child) {
								$persons="Yes";
								
								//-- Childs Parents ---------------------
								$gparent=Person::getInstance($child->getXref());
								$fams = $gparent->getChildFamilies();
								$chfams = $gparent->getSpouseFamilies();
								foreach($fams as $famid=>$family) {
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
									foreach ($chchildren as $key=>$chchild) {
										$chnam   = $chchild->getAllNames();
										$chfulln = rtrim($chnam[0]['givn'],'*')." ".$chnam[0]['surname'];
										$chfulln = str_replace('"', "", $chfulln);												// Must remove quotes completely here
										$chfulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $chfulln);
										$chfulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $chfulln);				// Child's Full Name
										$chdob   = ($chchild->getBirthDate()->minJD()+$chchild->getBirthDate()->maxJD())/2;		// Child's Date of Birth (Julian)
										$chdod   = ($chchild->getDeathDate()->minJD()+$chchild->getDeathDate()->maxJD())/2;		// Child's Date of Death (Julian)
										$chBLD   = ($chfulln.", ".$chdob.", ".$chdod);
										array_push($chBLDarray, $chBLD);
									}
								}

								// Get Childs marriage status ------------
								$married="";
								$marrdate="";
								foreach ($child->getSpouseFamilies() as $childfamily) {
									$marrdate=$childfamily->getMarriageDate();
									$married = GedcomDate::Compare($censdate, $marrdate);
									if ($childfamily->getHusband()) {
										$chhusbnam = $childfamily->getHusband()->getAllNames();
										$ChHusbName = $chhusbnam[0]['surname'];
									}
								}
								
								// Childs Details -------------------------
								$title = i18n::translate('Individual information').": ".$cpid;
								// $spouselinks .= "\n\t\t\t\to&nbsp;&nbsp;";
								$spouselinks .= "<li>\n";
								if ($child->canDisplayName()) {
									$nam   = $child->getAllNames();
									$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
									$fulln = str_replace("@N.N.", "(".i18n::translate('unknown').")", $fulln);
									$fulln = str_replace("@P.N.", "(".i18n::translate('unknown').")", $fulln);
									$givn  = rtrim($nam[0]['givn'],'*');
									$surn  = $nam[0]['surname'];
									if (isset($nam[1]) && isset($ChHusbName)) {
										$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$ChHusbName;
									}
								
									$spouselinks .= "<a href=\"javascript:insertRowToTable(";
									$spouselinks .=	"'".PrintReady($child->getXref())."',";						// pid		=	PID
									$spouselinks .=	"'".addslashes(strip_tags($fulln))."',";					// nam		=	Name
									if (isset($nam[1])){
										$spouselinks .= "'".addslashes(strip_tags($fulmn))."',";				// mnam		=	Full Married Name
									} else {
										$spouselinks .= "'".addslashes(strip_tags($fulln))."',";				// mnam		=	Full Name
									}
									if ($currpid=="Son" || $currpid=="Daughter") {
										if ($child->getSex()=="M") {
											$spouselinks .=	"'Grand-Son',";										// label	=	Male Relationship
										}else{
											$spouselinks .=	"'Grand-Daughter',";								// label	=	Female Relationship
										}
									}else{
										if ($child->getSex()=="M") {
											$spouselinks .=	"'Nephew',";										// label	=	Male Relationship
										}else{
											$spouselinks .=	"'Niece',";											// label	=	Female Relationship
										}
									}
									$spouselinks .=	"'".PrintReady($child->getSex())."',";						// sex		=	Gender
									$spouselinks .=	"''".",";													// cond		=	Condition (Married etc)
									if ($marrdate) {
										$spouselinks .= "'".(($marrdate->minJD()+$marrdate->maxJD())/2)."',";	// dom = Date of Marriage (Julian)
									} else {
										$spouselinks .=	"'nm'".",";
									}
									$spouselinks .= "'".(($child->getBirthDate()->minJD()+$child->getBirthDate()->maxJD())/2)."',";	 // dob	=	Date of Birth
									if ($child->getbirthyear()>=1) {
										$spouselinks .=	"'".PrintReady($censyear-$child->getbirthyear())."',";	// age		= 	Census Year - Year of Birth
									}else{
										$spouselinks .=	"''".",";												// age		= 	Undefined
									}
									$spouselinks .= "'".(($child->getDeathDate()->minJD()+$child->getDeathDate()->maxJD())/2)."',";	 // dod	=	Date of Death
									$spouselinks .=	"''".",";													// occu 	=	Occupation
									$spouselinks .= "'".PrintReady($child->getBirthPlace())."'".",";			// birthpl	=	Individuals Birthplace
									if (isset($ChildFBP)) {
										$spouselinks .= "'".$ChildFBP."'".",";									// fbirthpl	=	Fathers Birthplace
									} else {
										$spouselinks .= "'UNK, UNK, UNK, UNK'".",";								// fbirthpl	=	Fathers Birthplace Not Known
									}
									if (isset($ChildMBP)) {
										$spouselinks .= "'".$ChildMBP."'".",";									// mbirthpl	=	Mothers Birthplace
									} else {
										$spouselinks .= "'UNK, UNK, UNK, UNK'".",";								// mbirthpl	=	Mothers Birthplace Not Known
									}
									if (isset($chBLDarray) && $child->getSex()=="F") {
										$chBLDarray = implode("::", $chBLDarray);
										$spouselinks .= "'".$chBLDarray."'";									// Array of Children (name, birthdate, deathdate)
									} else {
										$spouselinks .= "''";
									}
									$spouselinks .=	");\">";
									$spouselinks .= PrintReady($child->getFullName());							// Full Name (Link)
									$spouselinks .= "</a>";
									$spouselinks .= "</li>\n";
								}else{ 
									$spouselinks .= i18n::translate('Private');
								}
							}
						}
						$spouselinks .= "</ul>\n";
					}
				}
				?>
				
				<?php if ($theme_name=="Xenea" || $theme_name=="Standard" || $theme_name=="Wood" || $theme_name=="Ocean") { ?>
				<style type="text/css" rel="stylesheet">
					a:hover .name2 { color: #222222; }
				</style>
				<?php } ?>
				
				<?php
				if ($persons != "Yes") {
					$spouselinks  .= "(" . i18n::translate('None') . ")</td></tr></table>\n\t\t";
				}else{
					$spouselinks  .= "</td></tr></table>\n\t\t";
				}
				
				if ($person_parent != "Yes") {
					$parentlinks .= "(" . i18n::translate('unknown') . ")</td></tr></table>\n\t\t";
				}else{
					$parentlinks .= "</td></tr></table>\n\t\t";
				}
				
				if ($person_step != "Yes") {
					$step_parentlinks .= "(" . i18n::translate('unknown') . ")</td></tr></table>\n\t\t";
				}else{
					$step_parentlinks .= "</td></tr></table>\n\t\t";
				}
			}
		}
	}
}
// ==============================================================
?>

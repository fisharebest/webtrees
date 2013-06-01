<?php
// Media Link Assistant Control module for webtrees
//
// Media Link information about an individual
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2008  PGV Development Team.  All rights reserved.
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

global $summary, $censyear, $censdate;

$pid = safe_get('pid');

$year = "1901";
$censevent  = new WT_Event("1 CENS\n2 DATE 03 MAR".$year."", null, 0);
$censdate   = $censevent->getDate();
$censyear   = $censdate->date1->y;
$ctry       = "UK";
$married=-1;


// Test to see if Base pid is filled in ============================
if ($pid=="") {
	echo "<br><br>";
	echo "<b><font color=\"red\">YOU MUST enter a Base individual ID to be able to \"ADD\" Individual Links</font></b>";
	echo "<br><br>";
} else {

	$person=WT_Person::getInstance($pid);
	if ($person->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $person->getDeathYear(); }
	if ($person->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $person->getBirthYear(); }
	$currpid=$pid;

	echo '<div id="media-links">';
	echo '<table class="facts_table center">';
	echo '<tr><td class="topbottombar" colspan="1">';
	echo '<b>', WT_I18N::translate('Family navigator'), '</b>';
	echo '</td></tr>';
	echo '<tr>';
	echo '<td valign="top">';
	//-- Search  and Add Family Members Area =========================================
	?>
	<table class="outer_nav center">
		<?php
	
		//-- Search Function ------------------------------------------------------------
		?>
		<tr>
			<td class="descriptionbox font9 center"><?php echo WT_I18N::translate('Search for People to add to Add Links list.'); ?></td>
		</tr>
		<tr>
			<td id="srch" class="optionbox center">
				<script>
				var enter_name = "<?php echo WT_I18N::translate('You must enter a name'); ?>";
					function findindi(persid) {
						var findInput = document.getElementById('personid');
						txt = findInput.value;
						if (txt=="") {
								alert(enter_name);
						} else {
							var win02 = window.open(
								"module.php?mod=GEDFact_assistant&mod_action=media_3_find&callback=paste_id&action=filter&type=indi&multiple=&filter="+txt, "win02", "resizable=1, menubar=0, scrollbars=1, top=180, left=600, HEIGHT=600, WIDTH=450 ");
							if (window.focus) {
								win02.focus();
							}
						}
					}
				</script>
				<?php
				echo '<input id="personid" type="text" value="">';
				echo '<a href="#" onclick="onclick=findindi()">' ;
				echo '&nbsp;<font size="2">&nbsp;', WT_I18N::translate('Search'), '</font>';
				echo '</a>';
				?>
			</td>
		</tr>
		<tr>
			<td class="transparent;">
				<br>
			</td>
		</tr>
	
		<?php
		//-- Add Family Members to Census  -------------------------------------------
		global $spouselinks, $parentlinks, $DeathYr, $BirthYr, $censyear, $censdate;
		?>
		<tr>
		 <td align="center"class="transparent;">
		   <table width="100%" class="fact_table" cellspacing="0" border="0">
			<tr>
				<td align="center" colspan=3 class="descriptionbox wrap font9">
					<?php
					// Header text with "Head" button =================================================
					$headImg  = '<i class="headimg vmiddle icon-button_head"></i>';
					$headImg2 = '<i class="headimg2 vmiddle icon-button_head" title="'.WT_I18N::translate('Click to choose person as Head of family.').'"></i>';
					echo WT_I18N::translate('Click %s to choose person as Head of family.', $headImg);
					?>
					<br><br>
					<?php echo WT_I18N::translate('Click Name to add person to Add Links List.'); ?>
				</td>
			</tr>
	
			<tr>
				<td class="font9">
					<br>
				</td>
			</tr>
	
			<?php
			//-- Build Parent Family ---------------------------------------------------
			$personcount=0;
			$families = $this->record->getChildFamilies();
			foreach ($families as $family) {
				$label = $this->record->getChildFamilyLabel($family);
				$people = $this->buildFamilyList($family, "parents");
				$marrdate = $family->getMarriageDate();
	
				// Husband -------------------
				if (isset($people["husb"])) {
					$married   = WT_Date::Compare($censdate, $marrdate);
					$fulln = strip_tags($people['husb']->getFullName());
					$menu = new WT_Menu("&nbsp;" . $people["husb"]->getLabel());
					$slabel  = print_pedigree_person_nav2($people["husb"]->getXref(), 2, 0, $personcount++, $currpid, $censyear);
					$slabel .= $parentlinks;
					$submenu = new WT_Menu($slabel);
					$menu->addSubMenu($submenu);
	
	
					echo '<tr>';
						// Define width of Left (Label) column -------
						?>
						<td width=75 align="left" class="optionbox">
							<font size=1>
								<?php echo $menu->getMenu(); ?>
							</font>
						</td>
						<td align="left" class="facts_value" >
							<font size=1>
								<?php
								echo "<a href=\"edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$people["husb"]->getXref()."&amp;gedcom=".WT_GEDURL."\">";
								echo $headImg2;
								echo "</a>";
								?>
							</font>
						</td>
						<td align="left" class="facts_value">
							<font size=1>
							<?php
							if (($people["husb"]->canDisplayDetails())) {
							?>
							<a href='#' onclick='opener.insertRowToTable("<?php
									echo $people["husb"]->getXref() ; // pid = PID
								?>", "<?php
									echo $fulln;
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
									echo $people["husb"]->getbirthyear(); // yob = Year of Birth
								?>", "<?php
									echo $censyear-$people["husb"]->getbirthyear(); //  age = Census Date minus YOB
								?>", "<?php
									echo "Y"; // YMD
								?>", "<?php
									echo ""; // occu = Occupation
								?>", "<?php
									echo $people["husb"]->getcensbirthplace(); //  birthpl = Census Place of Birth
								?>");'><?php
									 echo $people["husb"]->getFullName(); // Name
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
	
				if (isset($people["wife"])) {
					$married   = WT_Date::Compare($censdate, $marrdate);
					$fulln = strip_tags($people['wife']->getFullName());
					$menu = new WT_Menu("&nbsp;" . $people["wife"]->getLabel());
					$slabel  = print_pedigree_person_nav2($people["wife"]->getXref(), 2, 0, $personcount++, $currpid, $censyear);
					$slabel .= $parentlinks;
					$submenu = new WT_Menu($slabel);
					$menu->addSubMenu($submenu);
					?>
					<tr>
						<td width=75 align="left" class="optionbox">
							<font size=1>
								<?php echo $menu->getMenu(); ?>
							</font>
						</td>
						<td align="left" class="facts_value">
							<font size=1>
								<?php
								echo "<a href=\"edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$people["wife"]->getXref()."&amp;gedcom=".WT_GEDURL."\">";
								echo $headImg2;
								echo "</a>";
								?>
							</font>
						</td>
						<td align="left" class="facts_value">
							<font size=1>
							<?php
							if (($people["wife"]->canDisplayDetails())) {
								?>
							<a href='#' onclick='opener.insertRowToTable("<?php
									echo $people["wife"]->getXref() ; // pid = PID
								?>", "<?php
									echo $fulln;
									?>", "<?php
									echo $people["wife"]->getLabel(); // label = Relationship
								?>", "<?php
									echo $people["wife"]->getSex(); // gend = Gender
								?>", "<?php
									if ($married>=0) {
										echo "M"; // cond = Condition (Married)
									} else {
										echo "S"; // cond = Condition (Single)
									}
									?>", "<?php
									echo $people["wife"]->getbirthyear(); // yob = Year of Birth
								?>", "<?php
									echo $censyear-$people["wife"]->getbirthyear(); //  age = Census Date minus YOB
								?>", "<?php
									echo "Y"; // YMD
								?>", "<?php
									echo ""; // occu = Occupation
								?>", "<?php
									echo $people["wife"]->getcensbirthplace(); //  birthpl = Census Place of Birth
								?>");'>
								<?php
									echo $people["wife"]->getFullName(); // Full Name
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
	
				if (isset($people["children"])) {
					$elderdate = $family->getMarriageDate();
					foreach ($people["children"] as $key=>$child) {
						// Get child's marriage status
						$married="";
						foreach ($child->getSpouseFamilies() as $childfamily) {
							$tmp=$childfamily->getMarriageDate();
							$married = WT_Date::Compare($censdate, $tmp);
						}
						$fulln = strip_tags($child->getFullName());
						$menu = new WT_Menu("&nbsp;" . $child->getLabel());
						$slabel  = print_pedigree_person_nav2($child->getXref(), 2, 0, $personcount++, $currpid, $censyear);
						$slabel .= $spouselinks;
						$submenu = new WT_Menu($slabel);
						$menu->addSubMenu($submenu);
	
						if ($child->getXref()==$pid) {
							//Only print Head of Family in Immediate Family Block
						} else {
							?>
							<tr>
								<td width=75 align="left" class="optionbox">
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
										echo "<a href=\"edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$child->getXref()."&amp;gedcom=".WT_GEDURL."\">";
										echo $headImg2;
										echo "</a>";
										?>
									</font>
								</td>
								<td align="left" class="facts_value">
									<font size=1>
									<?php
									if (($child->canDisplayDetails())) {
										?>
										<a href='#' onclick='opener.insertRowToTable("<?php
												echo $child->getXref() ; // pid = PID
											?>", "<?php
												echo $fulln;
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
												} else {
													echo ""; // cond = Condition (Not Known)
												}
											?>", "<?php
												echo $child->getbirthyear(); // yob = Year of Birth
											?>", "<?php
												echo $censyear-$child->getbirthyear(); // age = Census Date minus YOB
											?>", "<?php
												echo "Y"; // YMD
											?>", "<?php
												echo ""; // occu = Occupation
											?>", "<?php
												echo $child->getcensbirthplace(); // birthpl = Census Place of Birth
											?>");'><?php
												echo $child->getFullName(); // Full Name
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
	
			//-- Build step families ----------------------------------------------------------------
			foreach ($this->record->getChildStepFamilies() as $family) {
				$label = $this->record->getStepFamilyLabel($family);
				$people = $this->buildFamilyList($family, "step-parents");
				if ($people) {
					echo "<tr><td><br></td><td></td></tr>";
				}
				$marrdate = $family->getMarriageDate();
	
				// Husband -----------------------------
				$elderdate = "";
				if (isset($people["husb"]) ) {
					$married   = WT_Date::Compare($censdate, $marrdate);
					$fulln = strip_tags($people['husb']->getFullName());
					$menu = new WT_Menu();
					if ($people["husb"]->getLabel() == ".") {
						$menu->addLabel("&nbsp;" . WT_I18N::translate_c('mother\'s husband', 'step-father'));
					} else {
						$menu->addLabel("&nbsp;" . $people["husb"]->getLabel());
					}
					$slabel  = print_pedigree_person_nav2($people["husb"]->getXref(), 2, 0, $personcount++, $currpid, $censyear);
					$slabel .= $parentlinks;
					$submenu = new WT_Menu($slabel);
					$menu->addSubMenu($submenu);
					if ($people["husb"]->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $people["husb"]->getDeathYear(); }
					if ($people["husb"]->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $people["husb"]->getBirthYear(); }
					?>
					<tr>
						<td width=75 align="left" class="optionbox">
							<font size=1>
								<?php echo $menu->getMenu(); ?>
							</font>
						</td>
						<td align="left" class="facts_value">
							<font size=1>
								<?php
								echo "<a href=\"edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$people["husb"]->getXref()."&amp;gedcom=".WT_GEDURL."\">";
								echo $headImg2;
								echo "</a>";
								?>
							</font>
						</td>
						<td align="left" class="facts_value">
							<font size=1>
							<?php
							if (($people["husb"]->canDisplayDetails())) {
								?>
								<a href='#' onclick='opener.insertRowToTable("<?php
									echo $people["husb"]->getXref() ; // pid = PID
								?>", "<?php
									echo $fulln;
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
									echo $people["husb"]->getbirthyear(); // yob = Year of Birth
								?>", "<?php
									echo $censyear-$people["husb"]->getbirthyear(); //  age = Census Date minus YOB
								?>", "<?php
									echo "Y"; // YMD
								?>", "<?php
									echo ""; // occu = Occupation
								?>", "<?php
									echo $people["husb"]->getcensbirthplace(); //  birthpl = Census Place of Birth
								?>");'>
								<?php echo $people["husb"]->getFullName(); // Name
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
	
				// Wife -------------------
				if (isset($people["wife"]) ) {
					$married   = WT_Date::Compare($censdate, $marrdate);
					$fulln = strip_tags($people['wife']->getFullName());
					$menu = new WT_Menu();
					if ($people["husb"]->getLabel() == ".") {
						$menu->addLabel("&nbsp;" . WT_I18N::translate_c('father\'s wife', 'step-mother'));
					} else {
						$menu->addLabel("&nbsp;" . $people["wife"]->getLabel());
					}
					$slabel  = print_pedigree_person_nav2($people["wife"]->getXref(), 2, 0, $personcount++, $currpid, $censyear);
					$slabel .= $parentlinks;
					$submenu = new WT_Menu($slabel);
					$menu->addSubMenu($submenu);
					if ($people["wife"]->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $people["wife"]->getDeathYear(); }
					if ($people["wife"]->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $people["wife"]->getBirthYear(); }
					?>
					<tr>
						<td width=75 align="left" class="optionbox">
							<font size=1>
								<?php echo $menu->getMenu(); ?>
							</font>
						</td>
						<td align="left" class="facts_value">
							<font size=1>
								<?php
								echo "<a href=\"edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$people["wife"]->getXref()."&amp;gedcom=".WT_GEDURL."\">";
								echo $headImg2;
								echo "</a>";
								?>
							</font>
						</td>
						<td align="left" class="facts_value">
							<font size=1>
							<?php
							if (($people["wife"]->canDisplayDetails())) {
							?>
							<a href='#' onclick='opener.insertRowToTable("<?php
									echo $people["wife"]->getXref() ; // pid = PID
								?>", "<?php
									echo $fulln;
								?>", "<?php
								if ($people["wife"]->getLabel() == ".") {
									echo WT_I18N::translate_c('father\'s wife', 'step-mother'); // label = Relationship
								} else {
									echo $people["wife"]->getLabel(); // label = Relationship
								}
								?>", "<?php
									echo $people["wife"]->getSex(); // gend = Gender
								?>", "<?php
									if ($married>=0) {
										echo "M"; // cond = Condition (Married)
									} else {
										echo "S"; // cond = Condition (Single)
									}
									?>", "<?php
									echo $people["wife"]->getbirthyear(); // yob = Year of Birth
								?>", "<?php
									echo $censyear-$people["wife"]->getbirthyear(); //  age = Census Date minus YOB
								?>", "<?php
									echo "Y"; // YMD
								?>", "<?php
									echo ""; // occu = Occupation
								?>", "<?php
									echo $people["wife"]->getcensbirthplace(); //  birthpl = Census Place of Birth
								?>");'>
								<?php
									echo $people["wife"]->getFullName(); // Full Name
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
	
				// Children ---------------------
				if (isset($people["children"])) {
					$elderdate = $family->getMarriageDate();
					foreach ($people["children"] as $key=>$child) {
						$fulln = strip_tags($child->getFullName());
						$menu = new WT_Menu("&nbsp;" . $child->getLabel());
						$slabel  = print_pedigree_person_nav2($child->getXref(), 2, 0, $personcount++, $currpid, $censyear);
						$slabel .= $spouselinks;
						$submenu = new WT_Menu($slabel);
						$menu->addSubMenu($submenu); if ($child->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $child->getDeathYear(); }
						if ($child->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $child->getBirthYear(); }
						?>
						<tr>
							<td width=75 align="left" class="optionbox">
								<font size=1>
									<?php echo $menu->getMenu(); ?>
								</font>
							</td>
							<td align="left" class="facts_value" >
								<font size=1>
								<?php
								echo "<a href=\"edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$child->getXref()."&amp;gedcom=".WT_GEDURL."\">";
								echo $headImg2;
								echo "</a>";
								?>
								</font>
							</td>
							<td align="left" class="facts_value">
								<font size=1>
								<?php
								if (($child->canDisplayDetails())) {
								?>
								<a href='#' onclick='opener.insertRowToTable("<?php
									echo $child->getXref() ; // pid = PID
									?>", "<?php
										echo $fulln;
									?>", "<?php
										echo $child->getLabel(); // label = Relationship
									?>", "<?php
										echo $child->getSex(); // gend = Gender
									?>", "<?php
										echo ""; // cond = Condition (Married or Single)
									?>", "<?php
										echo $child->getbirthyear(); // yob = Year of Birth
									?>", "<?php
										echo $censyear-$child->getbirthyear(); //  age = Census Date minus YOB
									?>", "<?php
										echo "Y"; // YMD
									?>", "<?php
										echo ""; // occu = Occupation
									?>", "<?php
										echo $child->getcensbirthplace(); //  birthpl = Census Place of Birth
									?>");'>
										<?php echo $child->getFullName(); // Name
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
				$people = $this->buildFamilyList($family, "spouse");
				$marrdate = $family->getMarriageDate();
	
				// Husband -------------------
				if (isset($people["husb"])) {
					$married   = WT_Date::Compare($censdate, $marrdate);
					$fulln = strip_tags($people['husb']->getFullName());
					$menu = new WT_Menu("&nbsp;" . $people["husb"]->getLabel());
					$slabel  = print_pedigree_person_nav2($people["husb"]->getXref(), 2, 0, $personcount++, $currpid, $censyear);
					$slabel .= $parentlinks;
					$submenu = new WT_Menu($slabel);
					$menu->addSubMenu($submenu);
					if ($people["husb"]->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $people["husb"]->getDeathYear(); }
					if ($people["husb"]->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $people["husb"]->getBirthYear(); }
					?>
					<tr class="fact_value">
						<td width=75 align="left" class="optionbox">
							<font size=1>
								<?php
								if ($people["husb"]->getXref()==$pid) {
									echo "&nbsp" .($people["husb"]->getLabel())." ".WT_I18N::translate('Head of Household:');
								} else {
									echo $menu->getMenu();
								}
								?>
							</font>
						</td>
						<td align="left" class="facts_value" >
							<font size=1>
								<?php
								echo "<a href=\"edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$people["husb"]->getXref()."&amp;gedcom=".WT_GEDURL."\">";
								echo $headImg2;
								echo "</a>";
								?>
							</font>
						</td>
						<td align="left" class="facts_value" >
							<font size=1>
							<?php
							if (($people["husb"]->canDisplayDetails())) {
							?>
							<a href='#' onclick='opener.insertRowToTable("<?php
									echo $people["husb"]->getXref() ; // pid = PID
								?>", "<?php
									echo $fulln;
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
									echo $people["husb"]->getbirthyear(); // yob = Year of Birth
								?>", "<?php
									echo $censyear-$people["husb"]->getbirthyear(); //  age = Census Date minus YOB
								?>", "<?php
									echo "Y"; // YMD
								?>", "<?php
									echo ""; // occu = Occupation
								?>", "<?php
									echo $people["husb"]->getcensbirthplace(); //  birthpl = Census Place of Birth
								?>");'>
								<?php
									echo $people["husb"]->getFullName(); // Name
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
	
	
				// Wife -------------------
				if (isset($people["wife"])) {
					$married = WT_Date::Compare($censdate, $marrdate);
					$fulln = strip_tags($people['wife']->getFullName());
					$menu = new WT_Menu("&nbsp;" . $people["wife"]->getLabel());
					$slabel  = print_pedigree_person_nav2($people["wife"]->getXref(), 2, 0, $personcount++, $currpid, $censyear);
					$slabel .= $parentlinks;
					$submenu = new WT_Menu($slabel);
					$menu->addSubMenu($submenu);
					if ($people["wife"]->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $people["wife"]->getDeathYear(); }
					if ($people["wife"]->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $people["wife"]->getBirthYear(); }
					?>
					<tr>
						<td width=75 align="left" class="optionbox">
							<font size=1>
								<?php
								if ($people["wife"]->getXref()==$pid) {
									echo "&nbsp" .($people["wife"]->getLabel())." ".WT_I18N::translate('Head of Household:');
								} else {
									echo $menu->getMenu();
								}
								?>
							</font>
						</td>
						<td align="left" class="facts_value">
							<font size=1>
								<?php
								echo "<a href=\"edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$people["wife"]->getXref()."&amp;gedcom=".WT_GEDURL."\">";
								echo $headImg2;
								echo "</a>";
								?>
							</font>
						</td>
						<td align="left" class="facts_value">
							<font size=1>
							<?php
							if (($people["wife"]->canDisplayDetails())) {
							?>
								<a href='#' onclick='opener.insertRowToTable("<?php
										echo $people["wife"]->getXref() ; // pid = PID
								?>", "<?php
										echo $fulln;
								?>", "<?php
									if ($people["wife"]->getXref()==$pid) {
										echo "Head"; // label = Head
									} else {
										echo $people["wife"]->getLabel(); // label = Relationship
									}
								?>", "<?php
										echo $people["wife"]->getSex(); // gend = Gender
								?>", "<?php
									if ($married>=0) {
										echo "M"; // cond = Condition (Married)
									} else {
										echo "S"; // cond = Condition (Single)
									}
								?>", "<?php
									echo $people["wife"]->getbirthyear(); // yob = Year of Birth
								?>", "<?php
									echo $censyear-$people["wife"]->getbirthyear(); //  age = Census Date minus YOB
								?>", "<?php
									echo "Y";  // YMD
								?>", "<?php
									echo ""; // occu = Occupation
								?>", "<?php
									echo $people["wife"]->getcensbirthplace(); //  birthpl = Census Place of Birth
								?>");'>
									<?php
									echo $people["wife"]->getFullName(); // Full Name
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
	
				// Children
				foreach ($people["children"] as $key=>$child) {
						// Get child's marriage status
						$married="";
						foreach ($child->getSpouseFamilies() as $childfamily) {
							$tmp=$childfamily->getMarriageDate();
							$married = WT_Date::Compare($censdate, $tmp);
						}
						$fulln = strip_tags($child->getFullName());
						$menu = new WT_Menu("&nbsp;" . $child->getLabel());
						$slabel = print_pedigree_person_nav2($child->getXref(), 2, 0, $personcount++, $child->getLabel(), $censyear);
						$slabel .= $spouselinks;
						$submenu = new WT_Menu($slabel);
						$menu->addSubmenu($submenu);
						?>
					<tr>
						<td width=75 align="left" class="optionbox" >
							<font size=1>
								<?php echo $menu->getMenu(); ?>
							</font>
						</td>
						<td align="left" class="facts_value">
							<font size=1>
								<?php
								echo "<a href=\"edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$child->getXref()."&amp;gedcom=".WT_GEDURL."\">";
								echo $headImg2;
								echo "</a>";
								?>
							</font>
						</td>
						<td align="left" class="facts_value">
							<font size=1>
							<?php
							if (($child->canDisplayDetails())) {
							?>
							<a href='#' onclick='opener.insertRowToTable("<?php
									echo $child->getXref() ; // pid = PID
								?>", "<?php
									echo $fulln; // nam = Full Name
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
									echo $child->getbirthyear(); // yob = Year of Birth
								?>", "<?php
									echo $censyear-$child->getbirthyear(); //  age = Census Date minus YOB
								?>", "<?php
									echo "Y"; // YMD
								?>", "<?php
									echo ""; // occu = Occupation
								?>", "<?php
									echo $child->getcensbirthplace(); //  birthpl = Census Place of Birth
								?>");'>
									<?php
									echo $child->getFullName(); // Full Name
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
			?>
	
			</table>
		</td>
	  </tr>
	</table>
	<?php
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '</div>';// close "media-links"

} // End IF test for Base pid

/**
 * print the information for an individual chart box
 *
 * find and print a given individuals information for a pedigree chart
 * @param string $pid the Gedcom Xref ID of the   to print
 * @param int $style the style to print the box in, 1 for smaller boxes, 2 for larger boxes
 * @param int $count on some charts it is important to keep a count of how many boxes were printed
 */
function print_pedigree_person_nav2($pid, $style=1, $count=0, $personcount="1", $currpid, $censyear) {
	global $HIDE_LIVE_PEOPLE, $SHOW_LIVING_NAMES;
	global $SHOW_HIGHLIGHT_IMAGES, $bwidth, $bheight, $PEDIGREE_FULL_DETAILS, $SHOW_PEDIGREE_PLACES;
	global $TEXT_DIRECTION, $DEFAULT_PEDIGREE_GENERATIONS, $OLD_PGENS, $talloffset, $PEDIGREE_LAYOUT, $MEDIA_DIRECTORY;
	global $ABBREVIATE_CHART_LABELS;
	global $chart_style, $box_width, $generations, $show_spouse, $show_full;
	global $CHART_BOX_TAGS, $SHOW_LDS_AT_GLANCE, $PEDIGREE_SHOW_GENDER;
	global $SEARCH_SPIDER;

	global $spouselinks, $parentlinks, $step_parentlinks, $persons, $person_step, $person_parent, $tabno;
	global $natdad, $natmom, $censyear, $censdate;

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
			$spouselinks .= "<table id=\"flyoutFamRTL\" class=\"person_box$isF\"><tr><td class=\"name2 font9 rtl\">";
			$spouselinks .= "<b>" . WT_I18N::translate('Family') . "</b> (" .$person->getFullName(). ")<br>";
			$parentlinks .= "<table id=\"flyoutParRTL\" class=\"person_box$isF\"><tr><td class=\"name2 font9 rtl\">";
			$parentlinks .= "<b>" . WT_I18N::translate('Parents') . "</b> (" .$person->getFullName(). ")<br>";
			$step_parentlinks .= "<table id=\"flyoutStepRTL\" class=\"person_box$isF\"><tr><td class=\"name2 font9 rtl\">";
			$step_parentlinks .= "<b>" . WT_I18N::translate('Parents') . "</b> (" .$person->getFullName(). ")<br>";
		} else {
			$spouselinks .= "<table id=\"flyoutFam\" class=\"person_box$isF\"><tr><td class=\"name2 font9 ltr\">";
			$spouselinks .= "<b>" . WT_I18N::translate('Family') . "</b> (" .$person->getFullName(). ")<br>";
			$parentlinks .= "<table id=\"flyoutPar\" class=\"person_box$isF\"><tr><td class=\"name2 font9 ltr\">";
			$parentlinks .= "<b>" . WT_I18N::translate('Parents') . "</b> (" .$person->getFullName(). ")<br>";
			$step_parentlinks .= "<table id=\"flyoutStep\" class=\"person_box$isF\"><tr><td class=\"name2 font9 ltr\">";
			$step_parentlinks .= "<b>" . WT_I18N::translate('Parents') . "</b> (" .$person->getFullName(). ")<br>";
		}
		$persons       = '';
		$person_parent = '';
		$person_step   = '';

		//-- parent families --------------------------------------
		foreach ($person->getChildFamilies() as $family) {

			if (!is_null($family)) {
				$husb = $family->getHusband($person);
				$wife = $family->getWife($person);
				// $spouse = $family->getSpouse($person);
				$children = $family->getChildren();
				$num = count($children);
				$marrdate = $family->getMarriageDate();

				// Husband ------------------------------
				if ($husb || $num>0) {
					if ($husb) {
						$person_parent="Yes";
						$tmp=$husb->getXref();
						if ($husb->canDisplayName()) {
							$fulln = strip_tags($husb->getFullName());
							$parentlinks .= "<a href=\"#\" onclick=\"opener.insertRowToTable(";
							$parentlinks .= "'".$husb->getXref()."', "; // pid = PID
							$parentlinks .= "'".$fulln."', "; // nam = Name
							if ($currpid=="Wife" || $currpid=="Husband") {
								$parentlinks .= "'Father in Law', "; // label = 1st Gen Male Relationship
							} else {
								$parentlinks .= "'Grand-Father', "; // label = 2st Gen Male Relationship
							}
							$parentlinks .= "'".$husb->getSex()."', "; // sex = Gender
							$parentlinks .= "''".", "; // cond = Condition (Married etc)
							$parentlinks .= "'".$husb->getbirthyear()."', "; // yob = Year of Birth
							if ($husb->getbirthyear()>=1) {
								$parentlinks .= "'".($censyear-$husb->getbirthyear())."', "; // age =  Census Year - Year of Birth
							} else {
								$parentlinks .= "'', "; // age =  Undefined
							}
							$parentlinks .= "'Y', "; // Y/M/D = Age in Years/Months/Days
							$parentlinks .= "'', "; // occu  = Occupation
							$parentlinks .= "'".$husb->getcensbirthplace()."'"; // birthpl = Birthplace
							$parentlinks .= ");\">";
							$parentlinks .= $husb->getFullName();
							$parentlinks .= "</a>";

						} else {
							$parentlinks .= WT_I18N::translate('Private');
						}
						$natdad = "yes";
					}
				}

				// Wife ------------------------------
				if ($wife || $num>0) {
					if ($wife) {
						$person_parent="Yes";
						$tmp=$wife->getXref();
						if ($wife->canDisplayName()) {
							$married = WT_Date::Compare($censdate, $marrdate);
							$fulln = strip_tags($wife->getFullName());
							$parentlinks .= "<a href=\"#\" onclick=\"opener.insertRowToTable(";
							$parentlinks .= "'".$wife->getXref()."',"; // pid = PID
							$parentlinks .= "'".$fulln."',"; // nam = Full Name
							if ($currpid=="Wife" || $currpid=="Husband") {
								$parentlinks .= "'Mother in Law',"; // label = 1st Gen Female Relationship
							} else {
								$parentlinks .= "'Grand-Mother',"; // label = 2st Gen Female Relationship
							}
							$parentlinks .= "'".$wife->getSex()."',"; // sex = Gender
							$parentlinks .= "''".","; // cond = Condition (Married etc)
							$parentlinks .= "'".$wife->getbirthyear()."',"; // yob = Year of Birth
							if ($wife->getbirthyear()>=1) {
								$parentlinks .= "'".($censyear-$wife->getbirthyear())."',"; // age =  Census Year - Year of Birth
							} else {
								$parentlinks .= "''".","; // age =  Undefined
							}
							$parentlinks .= "'Y'".","; // Y/M/D = Age in Years/Months/Days
							$parentlinks .= "''".","; // occu  = Occupation
							$parentlinks .= "'".$wife->getcensbirthplace()."'"; // birthpl = Birthplace
							$parentlinks .= ");\">";
							$parentlinks .= $wife->getFullName(); // Full Name
							$parentlinks .= "</a>";
						} else {
							$parentlinks .= WT_I18N::translate('Private');
						}
						$parentlinks .= "<br>";
						$natmom = "yes";
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
				// $spouse = $family->getSpouse($person);
				$children = $family->getChildren();
				$num = count($children);
				$marrdate = $family->getMarriageDate();

				if ($natdad == "yes") {
				} else {
					// Husband -----------------------
					if (($husb || $num>0) && $husb->getLabel() != ".") {
						if ($husb) {
							$person_step="Yes";
							$tmp=$husb->getXref();
							if ($husb->canDisplayName()) {
								$fulln = strip_tags($husb->getFullName());
								$parentlinks .= "<a href=\"individual.php?pid={$tmp}&amp;tab={$tabno}&amp;gedcom=".WT_GEDURL."\">";
								$parentlinks .= $husb->getFullName();
								$parentlinks .= "</a>";
							} else {
								$parentlinks .= WT_I18N::translate('Private');
							}
							$parentlinks .= "<br>";
						}
					}
				}

				if ($natmom == "yes") {
				} else {
					// Wife ----------------------------
					if ($wife || $num>0) {
						if ($wife) {
							$person_step="Yes";
							$tmp=$wife->getXref();
							if ($wife->canDisplayName()) {
								$married = WT_Date::Compare($censdate, $marrdate);
								$fulln = addslashes($wife->getFullName());
								$parentlinks .= "<a href=\"individual.php?pid={$tmp}&amp;tab={$tabno}&amp;gedcom=".WT_GEDURL."\">";
								$parentlinks .= $wife->getFullName();
								$parentlinks .= "</a>";
							} else {
								$parentlinks .= WT_I18N::translate('Private');
							}
							$parentlinks .= "<br>";
						}
					}
				}
			}
		}

		// Spouse Families -------------------------------------- @var $family Family
		foreach ($person->getSpouseFamilies() as $family) {
			if (!is_null($family)) {
				$spouse = $family->getSpouse($person);
				$children = $family->getChildren();
				$num = count($children);
				$marrdate = $family->getMarriageDate();

				// Spouse ------------------------------
				if ($spouse || $num>0) {
					if ($spouse) {
						$tmp=$spouse->getXref();
						if ($spouse->canDisplayName()) {
							$married = WT_Date::Compare($censdate, $marrdate);
							$fulln = strip_tags($spouse->getFullName());
							$spouselinks .= "<a href=\"#\" onclick=\"opener.insertRowToTable(";
							$spouselinks .= "'".$spouse->getXref()."',"; // pid = PID
							$spouselinks .= "'".strip_tags($spouse->getFullName())."',"; // Full Name
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
								$spouselinks .= "'".$spouse->getbirthyear()."',"; // yob = Year of Birth
								if ($spouse->getbirthyear()>=1) {
									$spouselinks .= "'".($censyear-$spouse->getbirthyear())."',"; // age =  Census Year - Year of Birth
								} else {
									$spouselinks .= "''".","; // age =  Undefined
								}
								$spouselinks .= "'Y'".","; // Y/M/D = Age in Years/Months/Days
								$spouselinks .= "''".","; // occu  = Occupation
								$spouselinks .= "'".$spouse->getcensbirthplace()."'"; // birthpl = Birthplace
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

				// Children ------------------------------   @var $child Person
				$spouselinks .= "<div id='spouseFam'>";
				$spouselinks .= "<ul class=\"clist\">";
				foreach ($children as $c=>$child) {
					if ($child) {
						$persons="Yes";
						if ($child->canDisplayName()) {
							$fulln = strip_tags($child->getFullName());
							$spouselinks .= "<li>";
							$spouselinks .= "<a href=\"#\" onclick=\"opener.insertRowToTable(";
							$spouselinks .= "'".$child->getXref()."',"; // pid = PID
							$spouselinks .= "'".$fulln."',"; // nam = Name
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
									$spouselinks .= "'Niece',"; // label  = Female Relationship
								}
							}
							$spouselinks .= "'".$child->getSex()."',"; // sex = Gender
							$spouselinks .= "''".","; // cond = Condition (Married etc)
							$spouselinks .= "'".$child->getbirthyear()."',"; // yob = Year of Birth
							if ($child->getbirthyear()>=1) {
								$spouselinks .= "'".($censyear-$child->getbirthyear())."',"; // age =  Census Year - Year of Birth
							} else {
								$spouselinks .= "''".","; // age =  Undefined
							}
							$spouselinks .= "'Y'".","; // Y/M/D = Age in Years/Months/Days
							$spouselinks .= "''".","; // occu  = Occupation
							$spouselinks .= "'".$child->getcensbirthplace()."'"; // birthpl = Birthplace
							$spouselinks .= ");\">";
							$spouselinks .= $child->getFullName(); // Full Name
							$spouselinks .= "</a>";
							} else {
								$spouselinks .= WT_I18N::translate('Private');
							}
							$spouselinks .= "</li>";
					}
				}
				$spouselinks .= "</ul>";
				$spouselinks .= "</div>";
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

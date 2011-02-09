<?php
/**
 * Media Link Assistant Control module for webtrees
 *
 * Media Link information about an individual
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
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
 * @version $Id$
 * @author Brian Holland
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
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
							"module.php?mod=GEDFact_assistant&mod_action=_MEDIA/media_3_find&callback=paste_id&action=filter&type=indi&multiple=&filter="+txt, "win02", "resizable=1, menubar=0, scrollbars=1, top=180, left=600, HEIGHT=600, WIDTH=450 ");
						if (window.focus) {
							win02.focus();
						}
					}
				}
			</script>
			<?php
			echo '<input id="personid" type="text" value="" />';
			echo '<a href="javascript: onclick=findindi()">' ;
			echo '&nbsp;<font size="2">&nbsp;', WT_I18N::translate('Search'), '</font>';
			echo '</a>';
			?>
		</td>
	</tr>
	<tr>
		<td class="transparent;">
			<br />
		</td>
	</tr>

	<?php
	//-- Add Family Members to Census  -------------------------------------------
	global $WT_IMAGES, $spouselinks, $parentlinks, $DeathYr, $BirthYr, $TEXT_DIRECTION, $censyear, $censdate;
	// echo "CENS = " . $censyear;
	?>
	<tr>
	 <td align="center"class="transparent;">
	   <table width="100%" class="fact_table" cellspacing="0" border="0">
		<tr>
			<td align="center" colspan=3 class="descriptionbox wrap font9">
				<?php
				// Header text with "Head" button =================================================
				$headImg  = "<img class=\"headimg vmiddle\" src=\"".$WT_IMAGES["button_head"]."\" />";
				$headImg2 = "<img class=\"headimg2 vmiddle\" src=\"".$WT_IMAGES["button_head"]."\" alt=\"".WT_I18N::translate('Click to choose person as Head of family.')."\" title=\"".WT_I18N::translate('Click to choose person as Head of family.')."\" />";
				global $tempStringHead;
				echo WT_I18N::translate('Click %s to choose person as Head of family.', $headImg);
				?>
				<br /><br />
				<?php echo WT_I18N::translate('Click Name to add person to Add Links List.'); ?>
			</td>
		</tr>

		<tr>
			<td class="font9">
				<br />
			</td>
		</tr>

		<?php
		//-- Build Parent Family ---------------------------------------------------
		$personcount=0;
		$families = $this->indi->getChildFamilies();
		foreach ($families as $family) {
			$label = $this->indi->getChildFamilyLabel($family);
			$people = $this->buildFamilyList($family, "parents");
			$marrdate = $family->getMarriageDate();

			// Husband -------------------
			$styleadd = "";
			if (isset($people["husb"])) {
				$married   = WT_Date::Compare($censdate, $marrdate);
				$nam   = $people["husb"]->getAllNames();
				$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
				$givn  = rtrim($nam[0]['givn'],'*');
				$surn  = $nam[0]['surname'];
				if (isset($nam[1])) {
					$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
					$marn  = $nam[1]['surname'];
				}
				$menu = new WT_Menu("&nbsp;" . $people["husb"]->getLabel());
				// $menu->addClass("", "", "submenu");
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
						<a href='javaScript:opener.insertRowToTable("<?php
								echo PrintReady($people["husb"]->getXref()) ; // pid = PID
							?>", "<?php
							// echo PrintReady($people["husb"]->getFullName()); // nam = Name
								echo PrintReady($fulln);
							?>", "<?php
								echo PrintReady($people["husb"]->getLabel()); // label = Relationship
							?>", "<?php
								echo PrintReady($people["husb"]->getSex()); // gend = Gender
							?>", "<?php
								if ($married>=0) {
									echo "M"; // cond = Condition (Married)
								} else {
									echo "S"; // cond = Condition (Single)
								}
							?>", "<?php
								echo PrintReady($people["husb"]->getbirthyear()); // yob = Year of Birth
							?>", "<?php
								echo PrintReady($censyear-$people["husb"]->getbirthyear()); //  age = Census Date minus YOB
							?>", "<?php
								echo "Y"; // YMD
							?>", "<?php
								echo ""; // occu = Occupation
							?>", "<?php
								echo PrintReady($people["husb"]->getcensbirthplace()); //  birthpl = Census Place of Birth
							?>");'><?php
								 echo PrintReady($people["husb"]->getFullName()); // Name
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
				$nam = $people["wife"]->getAllNames();
				$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
				$givn  = rtrim($nam[0]['givn'],'*');
				$surn  = $nam[0]['surname'];
				if (isset($nam[1])) {
					$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
					$marn  = $nam[1]['surname'];
				} else {
					$fulmn = $fulln;
					$marn  = $surn;
				}

				$menu = new WT_Menu("&nbsp;" . $people["wife"]->getLabel());
				//$menu->addClass("", "", "submenu");
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
						<a href='javaScript:opener.insertRowToTable("<?php
								echo $people["wife"]->getXref() ; // pid = PID
							?>", "<?php
							// if ($married>=0 && isset($nam[1])) {
							// echo PrintReady($fulmn); // nam = Married Name
							// } else {
									//echo PrintReady($people["wife"]->getFullName()); // nam = Name
									echo PrintReady($fulln);
							// }
								?>", "<?php
								echo PrintReady($people["wife"]->getLabel()); // label = Relationship
							?>", "<?php
								echo PrintReady($people["wife"]->getSex()); // gend = Gender
							?>", "<?php
								if ($married>=0 && isset($nam[1])) {
									echo "M"; // cond = Condition (Married)
								} else {
									echo "S"; // cond = Condition (Single)
								}
								?>", "<?php
								echo PrintReady($people["wife"]->getbirthyear()); // yob = Year of Birth
							?>", "<?php
								echo PrintReady($censyear-$people["wife"]->getbirthyear()); //  age = Census Date minus YOB
							?>", "<?php
								echo "Y"; // YMD
							?>", "<?php
								echo ""; // occu = Occupation
							?>", "<?php
								echo PrintReady($people["wife"]->getcensbirthplace()); //  birthpl = Census Place of Birth
							?>");'>
							<?php
							//if ($married>=0 && isset($nam[1])) {
							// echo PrintReady($fulmn); // Full Married Name
							//} else {
								echo PrintReady($people["wife"]->getFullName()); // Full Name
							//}
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
					$nam   = $child->getAllNames();
					$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
					$givn  = rtrim($nam[0]['givn'],'*');
					$surn  = $nam[0]['surname'];
					if (isset($nam[1])) {
						$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
						$marn  = $nam[1]['surname'];
					}

					$menu = new WT_Menu("&nbsp;" . $child->getLabel());
					//$menu->addClass("", "", "submenu");
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
									<a href='javaScript:opener.insertRowToTable("<?php
											echo $child->getXref() ; // pid = PID
										?>", "<?php
										//if ($married>=0 && isset($nam[1])) {
										// echo PrintReady($fulmn); // nam = Married Name
										//} else {
											//echo PrintReady($child->getFullName()); // nam = Full Name
											echo PrintReady($fulln);
										//}
											?>", "<?php
											if ($child->getXref()==$pid) {
												echo "Head"; // label = Head
											} else {
												echo PrintReady($child->getLabel()); // label = Relationship
											}
										?>", "<?php
											echo PrintReady($child->getSex()); // gend = Gender
										?>", "<?php
											if ($married>0) {
												echo "M"; // cond = Condition (Married)
											} else if ($married<0 || ($married=="0") ) {
												echo "S"; // cond = Condition (Single)
											} else {
												echo ""; // cond = Condition (Not Known)
											}
										?>", "<?php
											echo PrintReady($child->getbirthyear()); // yob = Year of Birth
										?>", "<?php
											echo PrintReady($censyear-$child->getbirthyear()); // age = Census Date minus YOB
										?>", "<?php
											echo "Y"; // YMD
										?>", "<?php
											echo ""; // occu = Occupation
										?>", "<?php
											echo PrintReady($child->getcensbirthplace()); // birthpl = Census Place of Birth
										?>");'><?php
										// if ($married>=0 && isset($nam[1])) {
										// echo PrintReady($fulmn); // Full Married Name
										// } else {
												echo PrintReady($child->getFullName()); // Full Name
										// }
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
		foreach ($this->indi->getChildStepFamilies() as $family) {
			$label = $this->indi->getStepFamilyLabel($family);
			$people = $this->buildFamilyList($family, "step-parents");
			if ($people) {
				echo "<tr><td><br /></td><td></td></tr>";
			}
			$marrdate = $family->getMarriageDate();

			// Husband -----------------------------
			$styleadd = "";
			$elderdate = "";
			if (isset($people["husb"]) ) {
				$married   = WT_Date::Compare($censdate, $marrdate);
				$nam   = $people["husb"]->getAllNames();
				$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
				$givn  = rtrim($nam[0]['givn'],'*');
				$surn  = $nam[0]['surname'];
				if (isset($nam[1])) {
					$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
					$marn  = $nam[1]['surname'];
				}
				$menu = new WT_Menu();
				if ($people["husb"]->getLabel() == ".") {
					$menu->addLabel("&nbsp;" . WT_I18N::translate('Step-Father'));
				} else {
					$menu->addLabel("&nbsp;" . $people["husb"]->getLabel());
				}
				//$menu->addClass("", "", "submenu");
				$slabel  = print_pedigree_person_nav2($people["husb"]->getXref(), 2, 0, $personcount++, $currpid, $censyear);
				$slabel .= $parentlinks;
				$submenu = new WT_Menu($slabel);
				$menu->addSubMenu($submenu);
				if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; } else { $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
				if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; } else { $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
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
							<a href='javaScript:opener.insertRowToTable("<?php
								echo PrintReady($people["husb"]->getXref()) ; // pid = PID
							?>", "<?php
								//echo PrintReady($people["husb"]->getFullName()); // nam = Name
								echo PrintReady($fulln);
							?>", "<?php
							if ($people["husb"]->getLabel() == ".") {
								echo PrintReady(WT_I18N::translate('Step-Father')); // label = Relationship
							} else {
								echo PrintReady($people["husb"]->getLabel()); // label = Relationship
							}
							?>", "<?php
								echo PrintReady($people["husb"]->getSex()); // gend = Gender
							?>", "<?php
								if ($married>=0) {
									echo "M"; // cond = Condition (Married)
								} else {
									echo "S"; // cond = Condition (Single)
								}
							?>", "<?php
								echo PrintReady($people["husb"]->getbirthyear()); // yob = Year of Birth
							?>", "<?php
								echo PrintReady($censyear-$people["husb"]->getbirthyear()); //  age = Census Date minus YOB
							?>", "<?php
								echo "Y"; // YMD
							?>", "<?php
								echo ""; // occu = Occupation
							?>", "<?php
								echo PrintReady($people["husb"]->getcensbirthplace()); //  birthpl = Census Place of Birth
							?>");'>
							<?php echo PrintReady($people["husb"]->getFullName()); // Name
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
			$styleadd = "";
			if (isset($people["wife"]) ) {
				$married   = WT_Date::Compare($censdate, $marrdate);
				$nam   = $people["wife"]->getAllNames();
				$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
				$givn  = rtrim($nam[0]['givn'],'*');
				$surn  = $nam[0]['surname'];
				if (isset($nam[1])) {
					$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
					$marn  = $nam[1]['surname'];
				}
				$menu = new WT_Menu();
				if ($people["husb"]->getLabel() == ".") {
					$menu->addLabel("&nbsp;" . WT_I18N::translate('Step-Mother'));
				} else {
					$menu->addLabel("&nbsp;" . $people["wife"]->getLabel());
				}
				//$menu->addClass("", "", "submenu");
				$slabel  = print_pedigree_person_nav2($people["wife"]->getXref(), 2, 0, $personcount++, $currpid, $censyear);
				$slabel .= $parentlinks;
				$submenu = new WT_Menu($slabel);
				$menu->addSubMenu($submenu);
				if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; } else { $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
				if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; } else { $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
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
						<a href='javaScript:opener.insertRowToTable("<?php
								echo PrintReady($people["wife"]->getXref()) ; // pid = PID
							?>", "<?php
							// if ($married>=0 && isset($nam[1])) {
							// echo PrintReady($fulmn); // nam = Married Name
							// } else {
									//echo PrintReady($people["wife"]->getFullName()); // nam = Full Name
									echo PrintReady($fulln);
							// }
							?>", "<?php
							if ($people["wife"]->getLabel() == ".") {
								echo PrintReady(WT_I18N::translate('Step-Mother')); // label = Relationship
							} else {
								echo PrintReady($people["wife"]->getLabel()); // label = Relationship
							}
							?>", "<?php
								echo PrintReady($people["wife"]->getSex()); // gend = Gender
							?>", "<?php
								if ($married>=0 && isset($nam[1])) {
									echo "M"; // cond = Condition (Married)
								} else {
									echo "S"; // cond = Condition (Single)
								}
								?>", "<?php
								echo PrintReady($people["wife"]->getbirthyear()); // yob = Year of Birth
							?>", "<?php
								echo PrintReady($censyear-$people["wife"]->getbirthyear()); //  age = Census Date minus YOB
							?>", "<?php
								echo "Y"; // YMD
							?>", "<?php
								echo ""; // occu = Occupation
							?>", "<?php
								echo PrintReady($people["wife"]->getcensbirthplace()); //  birthpl = Census Place of Birth
							?>");'>
							<?php
							//if ($married>=0 && isset($nam[1])) {
							// echo PrintReady($fulmn); // Full Married Name
							//} else {
								echo PrintReady($people["wife"]->getFullName()); // Full Name
							//}
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
			$styleadd = "";
			if (isset($people["children"])) {
				$elderdate = $family->getMarriageDate();
				foreach ($people["children"] as $key=>$child) {
					$nam   = $child->getAllNames();
					$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
					$givn  = rtrim($nam[0]['givn'],'*');
					$surn  = $nam[0]['surname'];
					if (isset($nam[1])) {
						$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
						$marn  = $nam[1]['surname'];
					}
					$menu = new WT_Menu("&nbsp;" . $child->getLabel());
					//$menu->addClass("", "", "submenu");
					$slabel  = print_pedigree_person_nav2($child->getXref(), 2, 0, $personcount++, $currpid, $censyear);
					$slabel .= $spouselinks;
					$submenu = new WT_Menu($slabel);
					$menu->addSubMenu($submenu); if (PrintReady($child->getDeathYear()) == 0) { $DeathYr = ""; } else { $DeathYr = PrintReady($child->getDeathYear()); }
					if (PrintReady($child->getBirthYear()) == 0) { $BirthYr = ""; } else { $BirthYr = PrintReady($child->getBirthYear()); }
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
							<a href='javaScript:opener.insertRowToTable("<?php
								echo PrintReady($child->getXref()) ; // pid = PID
								?>", "<?php
									//echo PrintReady($child->getFullName()); // nam = Name
									echo PrintReady($fulln);
								?>", "<?php
									echo PrintReady($child->getLabel()); // label = Relationship
								?>", "<?php
									echo PrintReady($child->getSex()); // gend = Gender
								?>", "<?php
									echo ""; // cond = Condition (Married or Single)
								?>", "<?php
									echo PrintReady($child->getbirthyear()); // yob = Year of Birth
								?>", "<?php
									echo PrintReady($censyear-$child->getbirthyear()); //  age = Census Date minus YOB
								?>", "<?php
									echo "Y"; // YMD
								?>", "<?php
									echo ""; // occu = Occupation
								?>", "<?php
									echo PrintReady($child->getcensbirthplace()); //  birthpl = Census Place of Birth
								?>");'>
									<?php echo PrintReady($child->getFullName()); // Name
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

		echo "<tr><td><font size=1><br /></font></td></tr>";

		//-- Build Spouse Family ---------------------------------------------------
		$families = $this->indi->getSpouseFamilies();
		//$personcount = 0;
		foreach ($families as $family) {
			$people = $this->buildFamilyList($family, "spouse");
			if ($this->indi->equals($people["husb"])) {
				$spousetag = 'WIFE';
			} else {
				$spousetag = 'HUSB';
			}
			$marrdate = $family->getMarriageDate();

			// Husband -------------------
			if (isset($people["husb"])) {
				$married   = WT_Date::Compare($censdate, $marrdate);
				$nam   = $people["husb"]->getAllNames();
				$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
				$givn  = rtrim($nam[0]['givn'],'*');
					$surn  = $nam[0]['surname'];
				if (isset($nam[1])) {
					$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
					$marn  = $nam[1]['surname'];
				}
				$menu = new WT_Menu("&nbsp;" . $people["husb"]->getLabel());
				//$menu->addClass("", "", "submenu");
				$slabel  = print_pedigree_person_nav2($people["husb"]->getXref(), 2, 0, $personcount++, $currpid, $censyear);
				$slabel .= $parentlinks;
				$submenu = new WT_Menu($slabel);
				$menu->addSubMenu($submenu);
				if (PrintReady($people["husb"]->getDeathYear()) == 0) { $DeathYr = ""; } else { $DeathYr = PrintReady($people["husb"]->getDeathYear()); }
				if (PrintReady($people["husb"]->getBirthYear()) == 0) { $BirthYr = ""; } else { $BirthYr = PrintReady($people["husb"]->getBirthYear()); }
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
						<a href='javaScript:opener.insertRowToTable("<?php
								echo $people["husb"]->getXref() ; // pid = PID
							?>", "<?php
								//echo PrintReady($people["husb"]->getFullName()); // nam = Name
								echo PrintReady($fulln);
							?>", "<?php
								if ($people["husb"]->getXref()==$pid) {
									echo "Head"; // label = Relationship
								} else {
									echo $people["husb"]->getLabel(); // label = Relationship
								}
							?>", "<?php
								echo PrintReady($people["husb"]->getSex()); // gend = Gender
							?>", "<?php
								if ($married>=0) {
									echo "M"; // cond = Condition (Married)
								} else {
									echo "S"; // cond = Condition (Single)
								}
							?>", "<?php
								echo PrintReady($people["husb"]->getbirthyear()); // yob = Year of Birth
							?>", "<?php
								echo PrintReady($censyear-$people["husb"]->getbirthyear()); //  age = Census Date minus YOB
							?>", "<?php
								echo "Y"; // YMD
							?>", "<?php
								echo ""; // occu = Occupation
							?>", "<?php
								echo PrintReady($people["husb"]->getcensbirthplace()); //  birthpl = Census Place of Birth
							?>");'>
							<?php
								echo PrintReady($people["husb"]->getFullName()); // Name
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
			//if (isset($people["wife"]) && $spousetag == 'WIFE') {
			if (isset($people["wife"])) {
				$married = WT_Date::Compare($censdate, $marrdate);
				$nam   = $people["wife"]->getAllNames();
				$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
				$givn  = rtrim($nam[0]['givn'],'*');
				$surn  = $nam[0]['surname'];
				if (isset($nam[1])) {
					$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
					$marn  = $nam[1]['surname'];
				} else {
					$fulmn = $fulln;
					$marn  = $surn;
				}
				$menu = new WT_Menu("&nbsp;" . $people["wife"]->getLabel());
				//$menu->addClass("", "", "submenu");
				$slabel  = print_pedigree_person_nav2($people["wife"]->getXref(), 2, 0, $personcount++, $currpid, $censyear);
				$slabel .= $parentlinks;
				$submenu = new WT_Menu($slabel);
				$menu->addSubMenu($submenu);
				if (PrintReady($people["wife"]->getDeathYear()) == 0) { $DeathYr = ""; } else { $DeathYr = PrintReady($people["wife"]->getDeathYear()); }
				if (PrintReady($people["wife"]->getBirthYear()) == 0) { $BirthYr = ""; } else { $BirthYr = PrintReady($people["wife"]->getBirthYear()); }
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
							<a href='javaScript:opener.insertRowToTable("<?php
									echo $people["wife"]->getXref() ; // pid = PID
							?>", "<?php
							// if ($married>=0 && isset($nam[1])) {
							// echo PrintReady($fulmn); // nam = Full Married Name
							// } else {
									//echo PrintReady($people["wife"]->getFullName()); // nam = Full Name
									echo PrintReady($fulln);
							// }
							?>", "<?php
								if ($people["wife"]->getXref()==$pid) {
									echo "Head"; // label = Head
								} else {
									echo PrintReady($people["wife"]->getLabel()); // label = Relationship
								}
							?>", "<?php
								echo PrintReady($people["wife"]->getSex()); // gend = Gender
							?>", "<?php
								if ($married>=0 && isset($nam[1])) {
									echo "M"; // cond = Condition (Married)
								} else {
									echo "S"; // cond = Condition (Single)
								}
							?>", "<?php
								echo PrintReady($people["wife"]->getbirthyear()); // yob = Year of Birth
							?>", "<?php
								echo PrintReady($censyear-$people["wife"]->getbirthyear()); //  age = Census Date minus YOB
							?>", "<?php
								echo "Y";  // YMD
							?>", "<?php
								echo ""; // occu = Occupation
							?>", "<?php
								echo PrintReady($people["wife"]->getcensbirthplace()); //  birthpl = Census Place of Birth
							?>");'>
								<?php
								//if ($married>=0 && isset($nam[1])) {
								// echo PrintReady($fulmn); // Full Married Name
								//} else {
									echo PrintReady($people["wife"]->getFullName()); // Full Name
								//}
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
					$nam   = $child->getAllNames();
					$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
					$givn  = rtrim($nam[0]['givn'],'*');
					$surn  = $nam[0]['surname'];
					if (isset($nam[1])) {
						$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
						$marn  = $nam[1]['surname'];
					} else {
						$fulmn = $fulln;
						$marn  = $surn;
					}
					$menu = new WT_Menu("&nbsp;" . $child->getLabel());
					//$menu->addClass("", "", "submenu");
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
						<a href='javaScript:opener.insertRowToTable("<?php
								echo $child->getXref() ; // pid = PID
							?>", "<?php
							// if ($married>0 && isset($nam[1])) {
							// echo PrintReady($fulmn); // nam = Full Married Name
							// } else {
									// echo PrintReady($child->getFullName()); // nam = Full Name
									echo PrintReady($fulln); // nam = Full Name
							// }
							?>", "<?php
								echo PrintReady($child->getLabel()); // label = Relationship
							?>", "<?php
								echo PrintReady($child->getSex()); // gend = Gender
							?>", "<?php
								if ($married>0) {
									echo "M"; // cond = Condition (Married)
								} else if ($married<0 || ($married=="0") ) {
									echo "S"; // cond = Condition (Single)
								} else {
									echo ""; // cond = Condition (Not Known)
								}
							?>", "<?php
								echo PrintReady($child->getbirthyear()); // yob = Year of Birth
							?>", "<?php
								echo PrintReady($censyear-$child->getbirthyear()); //  age = Census Date minus YOB
							?>", "<?php
								echo "Y"; // YMD
							?>", "<?php
								echo ""; // occu = Occupation
							?>", "<?php
								echo PrintReady($child->getcensbirthplace()); //  birthpl = Census Place of Birth
							?>");'>
								<?php
							// if ($married>=0 && isset($nam[1])) {
							// echo PrintReady($fulmn); // Full Married Name
							// } else {
									echo PrintReady($child->getFullName()); // Full Name
							// }
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
			echo "<tr><td><font size=1><br /></font></td></tr>";
		}
		?>

		</table>
	</td>
  </tr>
</table>
<?php
// ==================================================================
require_once WT_ROOT.'includes/functions/functions_charts.php';
/**
 * print the information for an individual chart box
 *
 * find and print a given individuals information for a pedigree chart
 * @param string $pid the Gedcom Xref ID of the   to print
 * @param int $style the style to print the box in, 1 for smaller boxes, 2 for larger boxes
 * @param int $count on some charts it is important to keep a count of how many boxes were printed
 */
function print_pedigree_person_nav2($pid, $style=1, $count=0, $personcount="1", $currpid, $censyear) {
	global $HIDE_LIVE_PEOPLE, $SHOW_LIVING_NAMES, $ZOOM_BOXES, $LINK_ICONS;
	global $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES, $bwidth, $bheight, $PEDIGREE_FULL_DETAILS, $SHOW_PEDIGREE_PLACES;
	global $TEXT_DIRECTION, $DEFAULT_PEDIGREE_GENERATIONS, $OLD_PGENS, $talloffset, $PEDIGREE_LAYOUT, $MEDIA_DIRECTORY;
	global $WT_IMAGES, $ABBREVIATE_CHART_LABELS, $USE_MEDIA_VIEWER;
	global $chart_style, $box_width, $generations, $show_spouse, $show_full;
	global $CHART_BOX_TAGS, $SHOW_LDS_AT_GLANCE, $PEDIGREE_SHOW_GENDER;
	global $SEARCH_SPIDER;

	global $spouselinks, $parentlinks, $step_parentlinks, $persons, $person_step, $person_parent, $tabno, $theme_name, $spousetag;
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

	if ($person->canDisplayName()) {
		if (empty($SEARCH_SPIDER)) {
			if ($LINK_ICONS!="disabled") {
				//-- draw a box for the family popup
				if ($TEXT_DIRECTION=="rtl") {
				$spouselinks .= "<table id=\"flyoutFamRTL\" class=\"person_box$isF\"><tr><td class=\"name2 font9 rtl\">";
				$spouselinks .= "<b>" . WT_I18N::translate('Family') . "</b> (" .$person->getFullName(). ")<br />";
				$parentlinks .= "<table id=\"flyoutParRTL\" class=\"person_box$isF\"><tr><td class=\"name2 font9 rtl\">";
				$parentlinks .= "<b>" . WT_I18N::translate('Parents') . "</b> (" .$person->getFullName(). ")<br />";
				$step_parentlinks .= "<table id=\"flyoutStepRTL\" class=\"person_box$isF\"><tr><td class=\"name2 font9 rtl\">";
				$step_parentlinks .= "<b>" . WT_I18N::translate('Parents') . "</b> (" .$person->getFullName(). ")<br />";
				} else {
				$spouselinks .= "<table id=\"flyoutFam\" class=\"person_box$isF\"><tr><td class=\"name2 font9 ltr\">";
				$spouselinks .= "<b>" . WT_I18N::translate('Family') . "</b> (" .$person->getFullName(). ")<br />";
				$parentlinks .= "<table id=\"flyoutPar\" class=\"person_box$isF\"><tr><td class=\"name2 font9 ltr\">";
				$parentlinks .= "<b>" . WT_I18N::translate('Parents') . "</b> (" .$person->getFullName(). ")<br />";
				$step_parentlinks .= "<table id=\"flyoutStep\" class=\"person_box$isF\"><tr><td class=\"name2 font9 ltr\">";
				$step_parentlinks .= "<b>" . WT_I18N::translate('Parents') . "</b> (" .$person->getFullName(). ")<br />";
				}
				$persons       = "";
				$person_parent = "";
				$person_step   = "";

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
							if ($TEXT_DIRECTION=="ltr") {
								$title = WT_I18N::translate('Family book chart').": ".$family->getXref();
							} else {
								$title = $family->getXref()." :".WT_I18N::translate('Family book chart');
							}
							if ($husb) {
								$person_parent="Yes";
								if ($TEXT_DIRECTION=="ltr") {
									$title = WT_I18N::translate('Individual information').": ".$husb->getXref();
								} else {
									$title = $husb->getXref()." :".WT_I18N::translate('Individual information');
								}
								$tmp=$husb->getXref();
								if ($husb->canDisplayName()) {
									$nam   = $husb->getAllNames();
									// $fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surn'];
									$fulln = $husb->getFullName();
									$givn  = rtrim($nam[0]['givn'],'*');
									$surn  = $nam[0]['surn'];
									if (isset($nam[1]) ) {
										$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surn'];
										$marn  = $nam[1]['surn'];
									}
									$parentlinks .= "<a href=\"javascript:opener.insertRowToTable(";
									$parentlinks .= "'".PrintReady($husb->getXref())."', "; // pid = PID
									$parentlinks .= "'".PrintReady($fulln)."', "; // nam = Name
									if ($currpid=="Wife" || $currpid=="Husband") {
										$parentlinks .= "'Father in Law', "; // label = 1st Gen Male Relationship
									} else {
										$parentlinks .= "'Grand-Father', "; // label = 2st Gen Male Relationship
									}
									$parentlinks .= "'".PrintReady($husb->getSex())."', "; // sex = Gender
									$parentlinks .= "''".", "; // cond = Condition (Married etc)
									$parentlinks .= "'".PrintReady($husb->getbirthyear())."', "; // yob = Year of Birth
									if ($husb->getbirthyear()>=1) {
										$parentlinks .= "'".PrintReady($censyear-$husb->getbirthyear())."', "; // age =  Census Year - Year of Birth
									} else {
										$parentlinks .= "''".", "; // age =  Undefined
									}
									$parentlinks .= "'Y'".", "; // Y/M/D = Age in Years/Months/Days
									$parentlinks .= "''".", "; // occu  = Occupation
									$parentlinks .= "'".PrintReady($husb->getcensbirthplace())."'"; // birthpl = Birthplace
									$parentlinks .= ");\">";
									$parentlinks .= PrintReady($husb->getFullName());
									$parentlinks .= "</a>";

								} else {
									$parentlinks .= WT_I18N::translate('Private');
								}
								$natdad = "yes";
							}
						}

						// Wife ------------------------------
						if ($wife || $num>0) {
							if ($TEXT_DIRECTION=="ltr") {
								$title = WT_I18N::translate('Family book chart').": ".$family->getXref();
							} else {
								$title = $family->getXref()." :".WT_I18N::translate('Family book chart');
							}
							if ($wife) {
								$person_parent="Yes";
								if ($TEXT_DIRECTION=="ltr") {
									$title = WT_I18N::translate('Individual information').": ".$wife->getXref();
								} else {
									$title = $wife->getXref()." :".WT_I18N::translate('Individual information');
								}
								$tmp=$wife->getXref();
								if ($wife->canDisplayName()) {
									$married = WT_Date::Compare($censdate, $marrdate);
									$nam   = $wife->getAllNames();
									$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
									$givn  = rtrim($nam[0]['givn'],'*');
									$surn  = $nam[0]['surname'];
									if (isset($nam[1])) {
										//$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
										$fulmn = $nam[1]['fullNN'];
										$marn  = $nam[1]['surname'];
									}
									$parentlinks .= "<a href=\"javascript:opener.insertRowToTable(";
									$parentlinks .= "'".PrintReady($wife->getXref())."',"; // pid = PID
									// $parentlinks .= "'".PrintReady($fulln)."',"; // nam = Name

									//if ($married>=0 && isset($nam[1])) {
									// $parentlinks .= "'".PrintReady($fulmn)."',"; // nam = Full Married Name
									//} else {
										$parentlinks .= "'".PrintReady($fulln)."',"; // nam = Full Name
									//}

									if ($currpid=="Wife" || $currpid=="Husband") {
										$parentlinks .= "'Mother in Law',"; // label = 1st Gen Female Relationship
									} else {
										$parentlinks .= "'Grand-Mother',"; // label = 2st Gen Female Relationship
									}
									$parentlinks .= "'".PrintReady($wife->getSex())."',"; // sex = Gender
									$parentlinks .= "''".","; // cond = Condition (Married etc)
									$parentlinks .= "'".PrintReady($wife->getbirthyear())."',"; // yob = Year of Birth
									if ($wife->getbirthyear()>=1) {
										$parentlinks .= "'".PrintReady($censyear-$wife->getbirthyear())."',"; // age =  Census Year - Year of Birth
									} else {
										$parentlinks .= "''".","; // age =  Undefined
									}
									$parentlinks .= "'Y'".","; // Y/M/D = Age in Years/Months/Days
									$parentlinks .= "''".","; // occu  = Occupation
									$parentlinks .= "'".PrintReady($wife->getcensbirthplace())."'"; // birthpl = Birthplace
									//$parentlinks .= ");\"><div id='wifePar'>";
									$parentlinks .= ");\">";
									//if ($married>=0 && isset($nam[1])) {
									// $parentlinks .= $fulmn; // Full Married Name
									//} else {
										$parentlinks .= PrintReady($wife->getFullName()); // Full Name
									//}
									// $parentlinks .= "</div></a>";
									$parentlinks .= "</a>";
								} else {
									$parentlinks .= WT_I18N::translate('Private');
								}
								$parentlinks .= "<br />";
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
								if ($TEXT_DIRECTION=="ltr") {
									$title = WT_I18N::translate('Family book chart').": ".$family->getXref();
								} else {
									$title = $family->getXref()." :".WT_I18N::translate('Family book chart');
								}
								if ($husb) {
									$person_step="Yes";
									if ($TEXT_DIRECTION=="ltr") {
										$title = WT_I18N::translate('Individual information').": ".$husb->getXref();
									} else {
										$title = $husb->getXref()." :".WT_I18N::translate('Individual information');
									}
									$tmp=$husb->getXref();
									if ($husb->canDisplayName()) {
										$nam   = $husb->getAllNames();
										$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
										//$fulln = $husb->getFullName();
										$givn  = rtrim($nam[0]['givn'],'*');
										$surn  = $nam[0]['surname'];
										if (isset($nam[1])) {
											$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
											$marn  = $nam[1]['surname'];
										}

										$parentlinks .= "<a href=\"individual.php?pid={$tmp}&amp;tab={$tabno}&amp;gedcom=".WT_GEDURL."\">";
										$parentlinks .= PrintReady($husb->getFullName());
										$parentlinks .= "</a>";
									} else {
										$parentlinks .= WT_I18N::translate('Private');
									}
									$parentlinks .= "<br />";
								}
							}
						}

						if ($natmom == "yes") {
						} else {
							// Wife ----------------------------
							if ($wife || $num>0) {
								if ($TEXT_DIRECTION=="ltr") {
									$title = WT_I18N::translate('Family book chart').": ".$family->getXref();
								} else {
									$title = $family->getXref()." :".WT_I18N::translate('Family book chart');
								}
								if ($wife) {
									$person_step="Yes";
									if ($TEXT_DIRECTION=="ltr") {
										$title = WT_I18N::translate('Individual information').": ".$wife->getXref();
									} else {
										$title = $wife->getXref()." :".WT_I18N::translate('Individual information');
									}
									$tmp=$wife->getXref();
									if ($wife->canDisplayName()) {
										$married = WT_Date::Compare($censdate, $marrdate);
										$nam   = $wife->getAllNames();
										$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
										$givn  = rtrim($nam[0]['givn'],'*');
										$surn  = $nam[0]['surname'];
										if (isset($nam[1])) {
											$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
											$marn  = $nam[1]['surname'];
										}
										$parentlinks .= "<a href=\"individual.php?pid={$tmp}&amp;tab={$tabno}&amp;gedcom=".WT_GEDURL."\">";
										$parentlinks .= PrintReady($wife->getFullName());
										$parentlinks .= "</a>";
									} else {
										$parentlinks .= WT_I18N::translate('Private');
									}
									$parentlinks .= "<br />";
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
							if ($TEXT_DIRECTION=="ltr") {
								$title = WT_I18N::translate('Family book chart').": ".$family->getXref();
							} else {
								$title = $family->getXref()." :".WT_I18N::translate('Family book chart');
							}
							if ($spouse) {
								if ($TEXT_DIRECTION=="ltr") {
									$title = WT_I18N::translate('Individual information').": ".$spouse->getXref();
								} else {
									$title = $spouse->getXref()." :".WT_I18N::translate('Individual information');
								}
								$tmp=$spouse->getXref();
								if ($spouse->canDisplayName()) {
									$married = WT_Date::Compare($censdate, $marrdate);
									$nam   = $spouse->getAllNames();
									$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
									$givn  = rtrim($nam[0]['givn'],'*');
									$surn  = $nam[0]['surname'];
									if (isset($nam[1])) {
										$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
										$marn  = $nam[1]['surname'];
									}
									$spouselinks .= "<a href=\"javascript:opener.insertRowToTable(";
									$spouselinks .= "'".PrintReady($spouse->getXref())."',"; // pid = PID
									//$spouselinks .= "'".PrintReady($fulln)."',"; // nam = Name
									//if ($married>=0 && isset($nam[1])) {
									// $spouselinks .= "'".PrintReady($fulmn)."',"; // Full Married Name
									//} else {
										$spouselinks .= "'".PrintReady($spouse->getFullName())."',"; // Full Name
									//}
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
										$spouselinks .= "'".PrintReady($spouse->getSex())."',"; // sex = Gender
										$spouselinks .= "''".","; // cond = Condition (Married etc)
										$spouselinks .= "'".PrintReady($spouse->getbirthyear())."',"; // yob = Year of Birth
										if ($spouse->getbirthyear()>=1) {
											$spouselinks .= "'".PrintReady($censyear-$spouse->getbirthyear())."',"; // age =  Census Year - Year of Birth
										} else {
											$spouselinks .= "''".","; // age =  Undefined
										}
										$spouselinks .= "'Y'".","; // Y/M/D = Age in Years/Months/Days
										$spouselinks .= "''".","; // occu  = Occupation
										$spouselinks .= "'".PrintReady($spouse->getcensbirthplace())."'"; // birthpl = Birthplace
										$spouselinks .= ");\">";
										// $spouselinks .= PrintReady($fulln);
										//if ($married>=0 && isset($nam[1])) {
										// $spouselinks .= "'".PrintReady($fulmn)."',"; // Full Married Name
										//} else {
											$spouselinks .= PrintReady($spouse->getFullName()); // Full Name
										//}
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
						$spouselinks .= "<ul class=\"clist ".$TEXT_DIRECTION."\">";
						foreach ($children as $c=>$child) {
							$cpid = $child->getXref();
							if ($child) {
								$persons="Yes";
									$title = WT_I18N::translate('Individual information').": ".$cpid;
									if ($child->canDisplayName()) {
										$nam   = $child->getAllNames();
										$fulln = rtrim($nam[0]['givn'],'*')."&nbsp;".$nam[0]['surname'];
										$givn  = rtrim($nam[0]['givn'],'*');
										$surn  = $nam[0]['surname'];
										if (isset($nam[1])) {
											$fulmn = rtrim($nam[1]['givn'],'*')."&nbsp;".$nam[1]['surname'];
											$marn  = $nam[1]['surname'];
										}
										$spouselinks .= "<li>";
										$spouselinks .= "<a href=\"javascript:opener.insertRowToTable(";
										$spouselinks .= "'".PrintReady($child->getXref())."',"; // pid = PID
										//$spouselinks .= "'".PrintReady($child->getFullName())."',"; // nam = Name
										$spouselinks .= "'".PrintReady($fulln)."',"; // nam = Name
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
									$spouselinks .= "'".PrintReady($child->getSex())."',"; // sex = Gender
									$spouselinks .= "''".","; // cond = Condition (Married etc)
									$spouselinks .= "'".PrintReady($child->getbirthyear())."',"; // yob = Year of Birth
									if ($child->getbirthyear()>=1) {
										$spouselinks .= "'".PrintReady($censyear-$child->getbirthyear())."',"; // age =  Census Year - Year of Birth
									} else {
										$spouselinks .= "''".","; // age =  Undefined
									}
									$spouselinks .= "'Y'".","; // Y/M/D = Age in Years/Months/Days
									$spouselinks .= "''".","; // occu  = Occupation
									$spouselinks .= "'".PrintReady($child->getcensbirthplace())."'"; // birthpl = Birthplace
									$spouselinks .= ");\">";
									$spouselinks .= PrintReady($child->getFullName()); // Full Name
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
	}
}

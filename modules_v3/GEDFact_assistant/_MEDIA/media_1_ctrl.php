<?php
// Media Link Assistant Control module for webtrees
//
// Media Link information about an individual
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2008 PGV Development Team.
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

// Test to see if Base pid is filled in ============================
if ($pid=="") {
	echo "<br><br>";
	echo "<b>YOU MUST enter a Base individual ID to be able to \"ADD\" Individual Links</b>";
	echo "<br><br>";
} else {

	$person=WT_Individual::getInstance($pid);
	if ($person->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $person->getDeathYear(); }
	if ($person->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $person->getBirthYear(); }

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
		<tr>
			<td class="descriptionbox center"><?php echo WT_I18N::translate('Search for individuals to add to add Links list.'); ?></td>
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
				echo '<a href="#" onclick="onclick=findindi()"> ', WT_I18N::translate('Search'), '</a>';
				?>
			</td>
		</tr>

		<?php
		//-- Add Family Members to Census  -------------------------------------------
		global $spouselinks, $parentlinks;
		?>
		<tr>
		 <td align="center">
		   <table width="100%" class="fact_table" cellspacing="0" border="0">
			<tr>
				<td align="center" colspan=3 class="descriptionbox wrap">
					<?php
					// Header text with "Head" button =================================================
					$headImg  = '<i class="headimg vmiddle icon-button_head"></i>';
					$headImg2 = '<i class="headimg2 vmiddle icon-button_head" title="'.WT_I18N::translate('Click to choose individual as head of family.').'"></i>';
					echo WT_I18N::translate('Click %s to choose individual as head of family.', $headImg);
					?>
					<br><br>
					<?php echo WT_I18N::translate('Click name to add individual to add links list.'); ?>
				</td>
			</tr>

			<tr>
				<td>
					<br>
				</td>
			</tr>

			<?php
			//-- Build Parent Family ---------------------------------------------------
			$families = $person->getChildFamilies();
			foreach ($families as $family) {
				$label = $person->getChildFamilyLabel($family);

				$people=array(
					'husb'    =>$family->getHusband(),
					'wife'    =>$family->getWife(),
					'children'=>$family->getChildren(),
				);

				$marrdate = $family->getMarriageDate();

				// Husband -------------------
				if ($people["husb"]) {
					$fulln = strip_tags($people['husb']->getFullName());
					$menu = new WT_Menu($headImg, "edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$people["husb"]->getXref()."&amp;gedcom=".WT_GEDURL);
					print_pedigree_person_nav_media($people["husb"]->getXref());
					$slabel = $parentlinks;
					$submenu = new WT_Menu($slabel);
					$menu->addSubMenu($submenu);


					echo '<tr>';
						// Define width of Left (Label) column -------
						?>
						<td class="facts_value">
							<?php echo $menu->getMenu(); ?>
						</td>
						<td class="facts_value">
							<?php
							if (($people["husb"]->canShow())) {
							?>
							<a href='#' onclick='opener.insertRowToTable("<?php
									echo $people["husb"]->getXref() ; // pid = PID
								?>", "<?php
									echo htmlentities($fulln);
								?>", "", "", "", "", "", "", "", "");'><?php
								 echo $people["husb"]->getFullName(); // Name
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

				if ($people["wife"]) {
					$fulln =strip_tags($people['wife']->getFullName());
					$menu = new WT_Menu($headImg, "edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$people["wife"]->getXref()."&amp;gedcom=".WT_GEDURL);
					print_pedigree_person_nav_media($people["wife"]->getXref());
					$slabel = $parentlinks;
					$submenu = new WT_Menu($slabel);
					$menu->addSubMenu($submenu);
					?>
					<tr>
						<td class="facts_value">
							<?php echo $menu->getMenu(); ?>
						</td>
						<td class="facts_value">
							<?php
							if (($people["wife"]->canShow())) {
								?>
							<a href='#' onclick='opener.insertRowToTable("<?php
									echo $people["wife"]->getXref() ; // pid = PID
								?>", "<?php
									echo htmlentities($fulln);
									?>", "", "", "", "", "", "", "", "");'>
								<?php
									echo $people["wife"]->getFullName(); // Full Name
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

				if ($people["children"]) {
					$elderdate = $family->getMarriageDate();
					foreach ($people["children"] as $key=>$child) {
						$fulln =strip_tags($child->getFullName());
						$menu = new WT_Menu($headImg, "edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$child->getXref()."&amp;gedcom=".WT_GEDURL);
						print_pedigree_person_nav_media($child->getXref());
						$slabel = $spouselinks;
						$submenu = new WT_Menu($slabel);
						$menu->addSubMenu($submenu);

						if ($child->getXref()==$pid) {
							//Only print Head of Family in Immediate Family Block
						} else {
							?>
							<tr>
								<td class="facts_value">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td class="facts_value">
									<?php
									if (($child->canShow())) {
										?>
										<a href='#' onclick='opener.insertRowToTable("<?php
												echo $child->getXref() ; // pid = PID
											?>", "<?php
												echo htmlentities($fulln);
												?>", "", "", "", "", "", "", "", "");'><?php
												echo $child->getFullName(); // Full Name
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
					$elderdate = $child->getBirthDate(false);
				}
			}

			//-- Build step families ----------------------------------------------------------------
			foreach ($person->getChildStepFamilies() as $family) {
				$label = $person->getStepFamilyLabel($family);

				$people=array(
					'husb'    =>$family->getHusband(),
					'wife'    =>$family->getWife(),
					'children'=>$family->getChildren(),
				);

				if ($people) {
					echo "<tr><td><br></td><td></td></tr>";
				}
				$marrdate = $family->getMarriageDate();

				// Husband -----------------------------
				$elderdate = "";
				if ($people["husb"]) {
					$fulln =strip_tags($people['husb']->getFullName());
					$menu = new WT_Menu($headImg, "edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$people["husb"]->getXref()."&amp;gedcom=".WT_GEDURL);
					print_pedigree_person_nav_media($people["husb"]->getXref());
					$slabel = $parentlinks;
					$submenu = new WT_Menu($slabel);
					$menu->addSubMenu($submenu);
					if ($people["husb"]->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $people["husb"]->getDeathYear(); }
					if ($people["husb"]->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $people["husb"]->getBirthYear(); }
					?>
					<tr>
						<td class="facts_value">
							<?php echo $menu->getMenu(); ?>
						</td>
						<td class="facts_value">
							<?php
							if (($people["husb"]->canShow())) {
								?>
								<a href='#' onclick='opener.insertRowToTable("<?php
									echo $people["husb"]->getXref() ; // pid = PID
								?>", "<?php
									echo htmlentities($fulln);
								?>", "", "", "", "", "", "", "", "");'>
								<?php echo $people["husb"]->getFullName(); // Name
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
					$elderdate = $people["husb"]->getBirthDate(false);
				}

				// Wife -------------------
				if ($people["wife"]) {
					$fulln =strip_tags($people['wife']->getFullName());
					$menu = new WT_Menu($headImg, "edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$people["wife"]->getXref()."&amp;gedcom=".WT_GEDURL);
					print_pedigree_person_nav_media($people["wife"]->getXref());
					$slabel = $parentlinks;
					$submenu = new WT_Menu($slabel);
					$menu->addSubMenu($submenu);
					if ($people["wife"]->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $people["wife"]->getDeathYear(); }
					if ($people["wife"]->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $people["wife"]->getBirthYear(); }
					?>
					<tr>
						<td class="facts_value">
							<?php echo $menu->getMenu(); ?>
						</td>
						<td class="facts_value">
							<?php
							if (($people["wife"]->canShow())) {
							?>
							<a href='#' onclick='opener.insertRowToTable("<?php
									echo $people["wife"]->getXref() ; // pid = PID
								?>", "<?php
									echo htmlentities($fulln);
								?>", "", "", "", "", "", "", "", "");'>
								<?php
									echo $people["wife"]->getFullName(); // Full Name
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

				// Children ---------------------
				if ($people["children"]) {
					$elderdate = $family->getMarriageDate();
					foreach ($people["children"] as $key=>$child) {
						$fulln =strip_tags($child->getFullName());
						$menu = new WT_Menu($headImg, "edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$child->getXref()."&amp;gedcom=".WT_GEDURL);
						print_pedigree_person_nav_media($child->getXref());
						$slabel = $spouselinks;
						$submenu = new WT_Menu($slabel);
						$menu->addSubMenu($submenu); if ($child->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $child->getDeathYear(); }
						if ($child->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $child->getBirthYear(); }
						?>
						<tr>
							<td class="facts_value">
								<?php echo $menu->getMenu(); ?>
							</td>
							<td class="facts_value">
								<?php
								if (($child->canShow())) {
								?>
								<a href='#' onclick='opener.insertRowToTable("<?php
									echo $child->getXref() ; // pid = PID
									?>", "<?php
										echo htmlentities($fulln);
									?>", "", "", "", "", "", "", "", "");'>
									<?php
										echo $child->getFullName(); // Name
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
			}

			echo "<tr><td><br></td></tr>";

			//-- Build Spouse Family ---------------------------------------------------
			$families = $person->getSpouseFamilies();
			foreach ($families as $family) {

				$people=array(
					'husb'    =>$family->getHusband(),
					'wife'    =>$family->getWife(),
					'children'=>$family->getChildren(),
				);

				$marrdate = $family->getMarriageDate();

				// Husband -------------------
				if ($people["husb"]) {
					$fulln =strip_tags($people['husb']->getFullName());
					$menu = new WT_Menu($headImg, "edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$people["husb"]->getXref()."&amp;gedcom=".WT_GEDURL);
					print_pedigree_person_nav_media($people["husb"]->getXref());
					$slabel = $parentlinks;
					$submenu = new WT_Menu($slabel);
					$menu->addSubMenu($submenu);
					if ($people["husb"]->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $people["husb"]->getDeathYear(); }
					if ($people["husb"]->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $people["husb"]->getBirthYear(); }
					?>
					<tr class="fact_value">
						<td class="facts_value">
							<?php echo $menu->getMenu(); ?>
						</td>
						<td class="facts_value" >
							<?php
							if (($people["husb"]->canShow())) {
							?>
							<a href='#' onclick='opener.insertRowToTable("<?php
									echo $people["husb"]->getXref() ; // pid = PID
								?>", "<?php
									echo htmlentities($fulln);
								?>", "", "", "", "", "", "", "", "");'>
								<?php
									echo $people["husb"]->getFullName(); // Name
								?>
							</a>
							<?php
							} else {
								echo WT_I18N::translate('Private');
								}
								?>
						</td>
					<tr>
					<?php
				}


				// Wife -------------------
				if ($people["wife"]) {
					$fulln =strip_tags($people['wife']->getFullName());
					$menu = new WT_Menu($headImg, "edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$people["wife"]->getXref()."&amp;gedcom=".WT_GEDURL);
					print_pedigree_person_nav_media($people["wife"]->getXref());
					$slabel = $parentlinks;
					$submenu = new WT_Menu($slabel);
					$menu->addSubMenu($submenu);
					if ($people["wife"]->getDeathYear() == 0) { $DeathYr = ""; } else { $DeathYr = $people["wife"]->getDeathYear(); }
					if ($people["wife"]->getBirthYear() == 0) { $BirthYr = ""; } else { $BirthYr = $people["wife"]->getBirthYear(); }
					?>
					<tr>
						<td class="facts_value">
							<?php echo $menu->getMenu(); ?>
						</td>
						<td class="facts_value">
							<?php
							if (($people["wife"]->canShow())) {
							?>
								<a href='#' onclick='opener.insertRowToTable("<?php
										echo $people["wife"]->getXref() ; // pid = PID
								?>", "<?php
										echo htmlentities($fulln);
								?>", "", "", "", "", "", "", "", "");'>
									<?php
									echo $people["wife"]->getFullName(); // Full Name
									?>
								</a>
								<?php
							} else {
								echo WT_I18N::translate('Private');
							}
							?>
						</td>
					<tr> <?php
				}

				// Children
				foreach ($people["children"] as $key=>$child) {
						$fulln =strip_tags($child->getFullName());
						$menu = new WT_Menu($headImg, "edit_interface.php?action=addmedia_links&amp;noteid=newnote&amp;pid=".$child->getXref()."&amp;gedcom=".WT_GEDURL);
						print_pedigree_person_nav_media($child->getXref());
						$slabel = $spouselinks;
						$submenu = new WT_Menu($slabel);
						$menu->addSubmenu($submenu);
						?>
					<tr>
						<td class="facts_value" >
							<?php echo $menu->getMenu(); ?>
						</td>
						<td class="facts_value">
							<?php
							if (($child->canShow())) {
							?>
							<a href='#' onclick='opener.insertRowToTable("<?php
									echo $child->getXref() ; // pid = PID
								?>", "<?php
									echo htmlentities($fulln); // nam = Full Name
								?>", "", "", "", "", "", "", "", "");'>
									<?php
									echo $child->getFullName(); // Full Name
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
 * Find and print a given individuals information for a pedigree chart
 *
 * @param string $pid The Gedcom Xref ID of the individual to print
 *
 * @return void
 */
function print_pedigree_person_nav_media($pid) {
	global $spouselinks, $parentlinks, $step_parentlinks, $persons, $person_step, $person_parent;
	global $natdad, $natmom;

	$person=WT_Individual::getInstance($pid);

	$tmp = array('M'=>'','F'=>'F', 'U'=>'NN');
	$isF = $tmp[$person->getSex()];
	$spouselinks      = '';
	$parentlinks      = '';
	$step_parentlinks = '';

	if ($person->canShowName()) {
		//-- draw a box for the family popup

		$spouselinks .= "<table id=\"flyoutFam\" class=\"person_box$isF\"><tr><td class=\"name2 ltr\">";
		$spouselinks .= "<b>" . WT_I18N::translate('Family') . "</b> (" .$person->getFullName(). ")<br>";
		$parentlinks .= "<table id=\"flyoutPar\" class=\"person_box$isF\"><tr><td class=\"name2 ltr\">";
		$parentlinks .= "<b>" . WT_I18N::translate('Parents') . "</b> (" .$person->getFullName(). ")<br>";
		$step_parentlinks .= "<table id=\"flyoutStep\" class=\"person_box$isF\"><tr><td class=\"name2 ltr\">";
		$step_parentlinks .= "<b>" . WT_I18N::translate('Parents') . "</b> (" .$person->getFullName(). ")<br>";

		$persons       = '';
		$person_parent = '';
		$person_step   = '';

		// Parent families
		foreach ($person->getChildFamilies() as $family) {
			$husb = $family->getHusband();
			$wife = $family->getWife();
			$children = $family->getChildren();

			// Parent Husband
			if ($husb || $children) {
				if ($husb) {
					// Parent Husbands Details
					$person_parent = 'Yes';
					if ($husb->canShowName()) {
						$fulln =strip_tags($husb->getFullName());
						$parentlinks .= "<a href=\"#\" onclick=\"opener.insertRowToTable(";
						$parentlinks .= "'".$husb->getXref()."', "; // pid = PID
						$parentlinks .= "'".htmlentities($fulln)."', "; // nam = Name
						$parentlinks .= "'',";
						$parentlinks .= "'',";
						$parentlinks .= "'',";
						$parentlinks .= "'',";
						$parentlinks .= "'',";
						$parentlinks .= "'',";
						$parentlinks .= "'',";
						$parentlinks .= "''";
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
					// Parent Wifes Details
					$person_parent="Yes";
					if ($wife->canShowName()) {
						$fulln = strip_tags($wife->getFullName());
						$parentlinks .= "<a href=\"#\" onclick=\"opener.insertRowToTable(";
						$parentlinks .= "'".$wife->getXref()."',"; // pid = PID
						$parentlinks .= "'".htmlentities($fulln)."',";
						$parentlinks .= "'',";
						$parentlinks .= "'',";
						$parentlinks .= "'',";
						$parentlinks .= "'',";
						$parentlinks .= "'',";
						$parentlinks .= "'',";
						$parentlinks .= "'',";
						$parentlinks .= "''";
						$parentlinks .= ");\">";
						$parentlinks .= $wife->getFullName();
						$parentlinks .= "</a>";
					} else {
						$parentlinks .= WT_I18N::translate('Private');
					}
					$parentlinks .= '<br>';
					$natmom = 'yes';
				}
			}
		}

		// Step families
		foreach ($person->getChildStepFamilies() as $family) {
			$husb = $family->getHusband();
			$wife = $family->getWife();
			$children = $family->getChildren();

			// Step Husband
			if ($natdad == 'yes') {
			} else {
				// Husband
				if (($husb || $children) && $husb->getLabel() != ".") {
					if ($husb) {
						//-- Step Husband Details ------------------------------
						$person_step = 'Yes';
						if ($husb->canShowName()) {
							$parentlinks .= "<a href=\"individual.php?pid=" . $husb->getXref() . "&amp;&amp;gedcom=".WT_GEDURL."\">";
							$parentlinks .= $husb->getFullName();
							$parentlinks .= "</a>";
						} else {
							$parentlinks .= WT_I18N::translate('Private');
						}
						$parentlinks .= '<br>';
					}
				}
			}

			// Step Wife
			if ($natmom == 'yes') {
			} else {
				// Wife
				if ($wife || $children) {
					if ($wife) {
						// Step Wife Details
						$person_step = 'Yes';
						if ($wife->canShowName()) {
							$parentlinks .= '<a href="individual.php?pid=' . $wife->getXref() . '&amp;gedcom=' . WT_GEDURL . '">' . $wife->getFullName() . '</a>';
						} else {
							$parentlinks .= WT_I18N::translate('Private');
						}
						$parentlinks .= '<br>';
					}
				}
			}
		}

		// Spouse Families
		foreach ($person->getSpouseFamilies() as $family) {
			$spouse = $family->getSpouse($person);
			$children = $family->getChildren();

			// Spouse
			if ($spouse && $spouse->canShowName()) {
				$spouselinks .= "<a href=\"#\" onclick=\"opener.insertRowToTable(";
				$spouselinks .= "'".$spouse->getXref()."',"; // pid = PID
				$spouselinks .= "'".strip_tags($spouse->getFullName())."',";
				$spouselinks .= "'',";
				$spouselinks .= "'',";
				$spouselinks .= "'',";
				$spouselinks .= "'',";
				$spouselinks .= "'',";
				$spouselinks .= "'',";
				$spouselinks .= "'',";
				$spouselinks .= "''";
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

			// Children
			$spouselinks .= "<div id='spouseFam'>";
			$spouselinks .= "<ul class=\"clist\">";
			foreach ($children as $child) {
				$persons="Yes";
				if ($child->canShowName()) {
					$fulln =strip_tags($child->getFullName());
					$spouselinks .= "<li>";
					$spouselinks .= "<a href=\"#\" onclick=\"opener.insertRowToTable(";
					$spouselinks .= "'".$child->getXref()."',";
					$spouselinks .= "'".htmlentities($fulln)."',"; // nam = Name
					$spouselinks .= "'',";
					$spouselinks .= "'',";
					$spouselinks .= "'',";
					$spouselinks .= "'',";
					$spouselinks .= "'',";
					$spouselinks .= "'',";
					$spouselinks .= "'',";
					$spouselinks .= "''";
					$spouselinks .= ");\">";
					$spouselinks .= $child->getFullName();
					$spouselinks .= "</a>";
				} else {
					$spouselinks .= WT_I18N::translate('Private');
				}
				$spouselinks .= "</li>";
			}
			$spouselinks .= "</ul>";
			$spouselinks .= "</div>";
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

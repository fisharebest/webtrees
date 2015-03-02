<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

?>

	<table id="navenclose">
		<tr>
			<td class="descriptionbox"><?php echo I18N::translate('Add individuals'); ?></td>
		</tr>
		<tr>
			<td class="optionbox" >
				<script>
					function findindi() {
						var findInput = document.getElementById('personid');
						var txt = findInput.value;
						if (txt === "") {
							alert("<?php echo I18N::translate('You must enter a name'); ?>");
						} else {
							var win02 = window.open(
								"module.php?mod=GEDFact_assistant&mod_action=_CENS/census_3_find&callback=paste_id&action=filter&filter="+txt, "win02", "resizable=1, menubar=0, scrollbars=1, top=180, left=600, height=400, width=450 ");
							if (window.focus) {
								win02.focus();
							}
						}
					}
				</script>
				<?php
				echo "<input id=personid type=\"text\" size=\"20\" style=\"color: #000000;\" value=\"\">";
				echo "<a href=\"#\" onclick=\"findindi()\">";
				echo "&nbsp;&nbsp;" . I18N::translate('Search');
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
								$headImg2 = '<i class="headimg2 vmiddle icon-button_head" title="' . I18N::translate('Click to choose individual as head of family.') . '"></i>';
								echo I18N::translate('Click %s to choose individual as head of family.', $headImg);
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
							$chfulln = strip_tags($chnam[0]['full']); // Child’s Full Name
							$chdob   = $child->getBirthDate()->JD(); // Child’s Date of Birth (Julian)
							$chdod   = $child->getDeathDate()->JD(); // Child’s Date of Death (Julian)
							$chBLD   = $chfulln . ', ' . $chdob . ', ' . $chdod;
							array_push($chBLDarray, $chBLD);
						}

						//-- Parents Husband -------------------
						if ($family->getHusband()) {

							//-- Parents Husbands Parents --------------------------------------
							$gparent = $family->getHusband();
							foreach ($gparent->getChildFamilies() as $cfamily) {
								$phusb = $cfamily->getHusband();
								$pwife = $cfamily->getWife();
								if ($phusb) { $HusbFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $HusbMBP = $pwife->getBirthPlace(); }
							}

							//-- Parents Husbands Details --------------------------------------
							$married = Date::compare($censdate, $marrdate);
							$nam     = $gparent->getAllNames();
							$fulln   = strip_tags($nam[0]['full']);
							$fulmn   = $fulln;
							foreach ($nam as $n) {
								if ($n['type'] === '_MARNM') {
									$fulmn = strip_tags($n['full']);
								}
							}
							$label = get_close_relationship_name($person, $gparent);
							$menu = new Menu($label);
							print_pedigree_person_nav_cens($gparent->getXref(), $label, $censdate);
							$submenu = new Menu($parentlinks);
							$menu->addSubmenu($submenu);

							?>
							<tr>
								<td align="left" class="linkcell optionbox" width="25%">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value" style="text-decoration:none;" >
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=" . $gparent->getXref() . "&amp;gedcom=" . WT_GEDURL . "\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<a href='#' onclick='return insertRowToTable(
										"<?php echo $gparent->getXref(); ?>",
										"<?php echo $fulln; ?>",
										"<?php echo $fulmn; ?>",
										"<?php echo $person === $gparent ? 'head': Filter::escapeHtml($label); ?>",
										"<?php echo $gparent->getSex(); ?>",
										"<?php echo $married >= 0 ? 'M' : 'S'; ?>",
										"<?php echo $marrdate->JD(); ?>",
										"<?php echo $gparent->getBirthDate()->JD(); ?>",
										"<?php echo $censyear - $gparent->getbirthyear(); ?>",
										"<?php echo $gparent->getDeathDate()->JD(); ?>",
										"",
										"<?php echo Filter::escapeHtml($gparent->getBirthPlace()); ?>",
										"<?php if (isset($HusbFBP)) {
												echo Filter::escapeHtml($HusbFBP); // fbirthpl = Husband Father’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Husband Father’s Place of Birth Not known
											} ?>",
										"<?php if (isset($HusbMBP)) {
												echo Filter::escapeHtml($HusbMBP); // mbirthpl = Husband Mother’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Husband Mother’s Place of Birth Not known
											} ?>",
										"<?php
											if (isset($chBLDarray) && $gparent->getSex() === 'F') {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											}
										?>"
									);'>
										<?php echo $gparent->getFullName(); ?>
									</a>
								</td>
							</tr>
							<?php
						}

						//-- Parents Wife ---------------------------------------------------------
						if ($family->getWife()) {

							//-- Parents Wifes Parent Family ---------------------------
							$gparent = $family->getWife();
							$cfamily = null;
							foreach ($gparent->getChildFamilies() as $cfamily) {
								$phusb = $cfamily->getHusband();
								$pwife = $cfamily->getWife();
								if ($phusb) { $WifeFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $WifeMBP = $pwife->getBirthPlace(); }
							}

							//-- Wifes Details --------------------------------------
							$married = Date::compare($censdate, $marrdate);
							$nam     = $gparent->getAllNames();
							$fulln   = strip_tags($nam[0]['full']);
							$fulmn   = $fulln;
							foreach ($nam as $n) {
								if ($n['type'] === '_MARNM') {
									$fulmn = strip_tags($n['full']);
								}
							}
							$label = get_close_relationship_name($person, $gparent);
							$menu = new Menu($label);
							print_pedigree_person_nav_cens($gparent->getXref(), $label, $censyear);
							$submenu = new Menu($parentlinks);
							$menu->addSubmenu($submenu);
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=" . $gparent->getXref() . "&amp;gedcom=" . WT_GEDURL . "\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<a href='#' onclick='return insertRowToTable(
										"<?php echo $gparent->getXref(); ?>",
										"<?php echo $fulln; ?>",
										"<?php echo $fulmn; ?>",
										"<?php echo $person === $gparent ? 'head': Filter::escapeHtml($label); ?>",
										"<?php echo $gparent->getSex(); ?>",
										"<?php echo $married >= 0 && isset($nam[1]) ? 'M' : 'S'; ?>",
										"<?php echo $marrdate->JD(); ?>",
										"<?php echo $gparent->getBirthDate()->JD(); ?>",
										"<?php echo $censyear - $gparent->getbirthyear(); ?>",
										"<?php echo $gparent->getDeathDate()->JD(); ?>",
										"",
										"<?php echo Filter::escapeHtml($gparent->getBirthPlace()); ?>",
										"<?php if (isset($WifeFBP)) {
												echo Filter::escapeHtml($WifeFBP); // fbirthpl = Wife Father’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Wife Father’s Place of Birth Not known
											} ?>",
										"<?php if (isset($WifeMBP)) {
												echo Filter::escapeHtml($WifeMBP); // mbirthpl = Wife Mother’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Wife Mother’s Place of Birth Not known
											} ?>",
										"<?php if (isset($chBLDarray) && $gparent->getSex() === 'F') {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											} ?>"
									);'>
										<?php echo $gparent->getFullName(); ?>
									</a>
								</td>
							</tr>
							<?php
						}

						//-- Parents Children -------------------
						//-- Parent’s Children’s Details --------------------------------------
						foreach ($family->getChildren() as $child) {
							// Get Child’s Children’s Name DOB DOD ----
							$chBLDarray = Array();
							foreach ($child->getSpouseFamilies() as $childfamily) {
								$chchildren = $childfamily->getChildren();
								foreach ($chchildren as $chchild) {
									$chnam   = $chchild->getAllNames();
									$chfulln = strip_tags($chnam[0]['full']); // Child’s Full Name
									$chdob   = $chchild->getBirthDate()->JD(); // Child’s Date of Birth (Julian)
									$chdod   = $chchild->getDeathDate()->JD(); // Child’s Date of Death (Julian)
									$chBLD   = $chfulln . ', ' . $chdob . ', ' . $chdod;
									array_push($chBLDarray, $chBLD);
								}
							}

							// Get child’s marriage status ----
							$married  = '';
							$marrdate = '';
							foreach ($child->getSpouseFamilies() as $childfamily) {
								$marrdate = $childfamily->getMarriageDate();
								$married = Date::compare($censdate, $marrdate);
							}
							$nam   = $child->getAllNames();
							$fulln = strip_tags($nam[0]['full']);
							$fulmn = $fulln;
							foreach ($nam as $n) {
								if ($n['type'] === '_MARNM') {
									$fulmn = strip_tags($n['full']);
								}
							}
							$label = get_close_relationship_name($person, $child);
							$menu = new Menu($label);
							print_pedigree_person_nav_cens($child->getXref(), $label, $censyear);
							$submenu = new Menu($spouselinks);
							$menu->addSubmenu($submenu);

							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=" . $child->getXref() . "&amp;gedcom=" . WT_GEDURL . "\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<a href='#' onclick='return insertRowToTable(
										"<?php echo $child->getXref(); ?>",
										"<?php echo $fulln; ?>",
										"<?php echo $fulmn; ?>",
										"<?php echo $person === $child ? 'head' : Filter::escapeHtml($label); ?>",
										"<?php echo $child->getSex(); ?>",
										"<?php echo $married >= 0 ? 'M' : 'S'; ?>",
										"<?php echo $marrdate ? $marrdate->JD() : ''; ?>",
										"<?php echo $child->getBirthDate()->JD(); ?>",
										"<?php echo $censyear - $child->getbirthyear(); ?>",
										"<?php echo $child->getDeathDate()->JD(); ?>",
										"",
										"<?php echo Filter::escapeHtml($child->getBirthPlace()); ?>",
										"<?php if ($family->getHusband()) {
												echo Filter::escapeHtml($family->getHusband()->getBirthPlace()); // fbirthpl = Child Father’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Child Father’s Place of Birth Not known
											} ?>",
										"<?php if ($family->getWife()) {
												echo Filter::escapeHtml($family->getWife()->getBirthPlace()); // mbirthpl = Child Mother’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Child Mother’s Place of Birth Not known
											} ?>",
										"<?php if (isset($chBLDarray) && $child->getSex() === 'F') {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											} ?>"
									);'>
										<?php echo $child->getFullName(); ?>
									</a>
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
							$chfulln = strip_tags($chnam[0]['full']); // Child’s Full Name
							$chdob   = $child->getBirthDate()->JD(); // Child’s Date of Birth (Julian)
							$chdod   = $child->getDeathDate()->JD(); // Child’s Date of Death (Julian)
							$chBLD   = $chfulln . ', ' . $chdob . ', ' . $chdod;
							array_push($chBLDarray, $chBLD);
						}

						// Step Husband -----------------------------
						if ($family->getHusband()) {

							//-- Step Husbands Parent Family --------------------------------------
							$gparent = $family->getHusband();
							foreach ($gparent->getChildFamilies() as $cfamily) {
								$phusb = $cfamily->getHusband();
								$pwife = $cfamily->getWife();
								if ($phusb) {
									$HusbFBP = $phusb->getBirthPlace();
								}
								if ($pwife) {
									$HusbMBP = $pwife->getBirthPlace();
								}
							}

							//-- Step Husbands Details --------------------------------------
							$married = Date::compare($censdate, $marrdate);
							$nam   = $gparent->getAllNames();
							$fulln = strip_tags($nam[0]['full']);
							$fulmn = $fulln;
							foreach ($nam as $n) {
								if ($n['type'] === '_MARNM') {
									$fulmn = strip_tags($n['full']);
								}
							}
							$label = get_close_relationship_name($person, $gparent);
							$menu = new Menu($label);
							print_pedigree_person_nav_cens($gparent->getXref(), $label, $censyear);
							$submenu = new Menu($parentlinks);
							$menu->addSubmenu($submenu);
							if ($gparent->getDeathYear() == 0) {
								$DeathYr = '';
							} else {
								$DeathYr = $gparent->getDeathYear();
							}
							if ($gparent->getBirthYear() == 0) {
								$BirthYr = '';
							} else {
								$BirthYr = $gparent->getBirthYear();
							}
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=" . $gparent->getXref() . "&amp;gedcom=" . WT_GEDURL . "\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<a href='#' onclick='return insertRowToTable(
										"<?php echo $gparent->getXref(); ?>",
										"<?php echo $fulln; ?>",
										"<?php echo $fulmn; ?>",
										"<?php echo $person === $gparent ? 'head': Filter::escapeHtml($label); ?>",
										"<?php echo $gparent->getSex(); ?>",
										"<?php echo $married >= 0 ? 'M': 'S'; ?>",
										"<?php echo $marrdate ? $marrdate->JD() : ''; ?>",
										"<?php echo $gparent->getBirthDate()->JD(); ?>",
										"<?php echo $censyear - $gparent->getbirthyear(); ?>",
										"<?php echo $gparent->getDeathDate()->JD();?>",
										"",
										"<?php echo Filter::escapeHtml($gparent->getBirthPlace()); ?>",
										"<?php if (isset($HusbFBP)) {
												echo Filter::escapeHtml($HusbFBP); // fbirthpl = Step Husband Father’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Step Husband Father’s Place of Birth Not known
											} ?>",
										"<?php if (isset($HusbMBP)) {
												echo Filter::escapeHtml($HusbMBP); // mbirthpl = Step Husband Mother’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Step Husband Mother’s Place of Birth Not known
											} ?>",
										"<?php if (isset($chBLDarray) && $gparent->getSex() === 'F') {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											} ?>"
									);'>
										<?php echo $gparent->getFullName(); ?>
									</a>
								</td>
							</tr>
							<?php
						}

						// Step Wife -------------------
						if ($family->getWife()) {

							//-- Step Wifes Parent Family --------------------------------------
							$gparent = $family->getWife();
							$cfamily = null;
							foreach ($gparent->getChildFamilies() as $cfamily) {
								$phusb = $cfamily->getHusband();
								$pwife = $cfamily->getWife();
								if ($phusb) { $WifeFBP = $phusb->getBirthPlace(); }
								if ($pwife) { $WifeMBP = $pwife->getBirthPlace(); }
							}

							//-- Step Wifes Details --------------------------------------
							$married = Date::compare($censdate, $marrdate);
							$nam     = $gparent->getAllNames();
							$fulln   = strip_tags($nam[0]['full']);
							$fulmn   = $fulln;
							foreach ($nam as $n) {
								if ($n['type'] === '_MARNM') {
									$fulmn = strip_tags($n['full']);
								}
							}

							$label = get_close_relationship_name($person, $gparent);
							$menu = new Menu($label);
							print_pedigree_person_nav_cens($gparent->getXref(), $label, $censyear);
							$submenu = new Menu($parentlinks);
							$menu->addSubmenu($submenu);
							if ($gparent->getDeathYear() == 0) { $DeathYr = ''; } else { $DeathYr = $gparent->getDeathYear(); }
							if ($gparent->getBirthYear() == 0) { $BirthYr = ''; } else { $BirthYr = $gparent->getBirthYear(); }
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=" . $gparent->getXref() . "&amp;gedcom=" . WT_GEDURL . "\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<a href='#' onclick='return insertRowToTable(
										"<?php echo $gparent->getXref(); ?>",
										"<?php echo $fulln; ?>",
										"<?php echo $fulmn; ?>",
										"<?php echo $person === $gparent ? 'head': Filter::escapeHtml($label); ?>",
										"<?php echo $gparent->getSex(); ?>",
										"<?php echo $married >= 0 && isset($nam[1]) ? 'M': 'S'; ?>",
										"<?php echo $marrdate ? $marrdate->JD() : ''; ?>",
										"<?php echo $gparent->getBirthDate()->JD(); ?>",
										"<?php echo $censyear - $gparent->getbirthyear(); ?>",
										"<?php echo $gparent->getDeathDate()->JD(); ?>",
										"",
										"<?php echo Filter::escapeHtml($gparent->getBirthPlace());  ?>",
										"<?php if (isset($WifeFBP)) {
												echo Filter::escapeHtml($WifeFBP); // fbirthpl = Step Wife Father’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Step Wife Father’s Place of Birth Not known
											} ?>",
										"<?php if (isset($WifeMBP)) {
												echo Filter::escapeHtml($WifeMBP); // mbirthpl = Step Wife Mother’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Step Wife Mother’s Place of Birth Not known
											} ?>",
										"<?php if (isset($chBLDarray) && $gparent->getSex() === 'F') {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											} ?>"
									);'>
										<?php echo $gparent->getFullName(); ?>
									</a>
								</td>
							</tr>
							<?php
						}

						// Step Children ---------------------
						foreach ($family->getChildren() as $child) {

							// Get Child’s Children
							$chBLDarray = Array();
							foreach ($child->getSpouseFamilies() as $childfamily) {
								$chchildren = $childfamily->getChildren();
								foreach ($chchildren as $chchild) {
									$chnam   = $chchild->getAllNames();
									$chfulln = strip_tags($chnam[0]['full']); // Child’s Full Name
									$chdob   = $chchild->getBirthDate()->JD(); // Child’s Date of Birth (Julian)
									$chdod   = $chchild->getDeathDate()->JD(); // Child’s Date of Death (Julian)
									$chBLD   = $chfulln . ', ' . $chdob . ', ' . $chdod;
									array_push($chBLDarray, $chBLD);
								}
							}

							$nam   = $child->getAllNames();
							$fulln   = strip_tags($nam[0]['full']);
							$fulmn   = $fulln;
							foreach ($nam as $n) {
								if ($n['type'] === '_MARNM') {
									$fulmn = strip_tags($n['full']);
								}
							}
							$label = get_close_relationship_name($person, $child);
							$menu = new Menu($label);
							print_pedigree_person_nav_cens($child->getXref(), $label, $censyear);
							$submenu = new Menu($spouselinks);
							$menu->addSubmenu($submenu);
							if ($child->getDeathYear() == 0) {
								$DeathYr = '';
							} else {
								$DeathYr = $child->getDeathYear();
							}
							if ($child->getBirthYear() == 0) {
								$BirthYr = '';
							} else {
								$BirthYr = $child->getBirthYear();
							}
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=" . $child->getXref() . "&amp;gedcom=" . WT_GEDURL . "\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<a href='#' onclick='return insertRowToTable(
										"<?php echo $child->getXref(); ?>",
										"<?php echo $fulln; ?>",
										"<?php echo $fulmn; ?>",
										"<?php echo $person === $child ? 'head' : Filter::escapeHtml($label); ?>",
										"<?php echo $child->getSex(); ?>",
										"",
										"<?php echo $marrdate ? $marrdate->JD() : ''; ?>",
										"<?php echo $child->getBirthDate()->JD(); ?>",
										"<?php echo $censyear - $child->getbirthyear(); ?>",
										"<?php echo $child->getDeathDate()->JD(); ?>",
										"",
										"<?php echo Filter::escapeHtml($child->getBirthPlace()); ?>",
										"<?php if ($family->getHusband()) {
												echo Filter::escapeHtml($family->getHusband()->getBirthPlace()); // fbirthpl = Child Father’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Child Father’s Place of Birth Not known
											} ?>",
										"<?php if ($family->getWife()) {
												echo Filter::escapeHtml($family->getWife()->getBirthPlace()); // mbirthpl = Child Mother’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Child Mother’s Place of Birth Not known
											} ?>",
										"<?php
											if (isset($chBLDarray) && $child->getSex() === 'F') {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											}
										?>"
									);'>
										<?php echo $child->getFullName(); ?>
									</a>
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
							$chfulln = strip_tags($chnam[0]['full']); // Child’s Full Name
							$chdob   = $child->getBirthDate()->JD(); // Child’s Date of Birth (Julian)
							$chdod   = $child->getDeathDate()->JD(); // Child’s Date of Death (Julian)
							$chBLD   = $chfulln . ', ' . $chdob . ', ' . $chdod;
							array_push($chBLDarray, $chBLD);
						}

						//-- Spouse Husband ---------------------------------------------------
						if ($family->getHusband()) {

							//-- Spouse Husbands Parents --------------------------------------
							$gparent = $family->getHusband();
							foreach ($gparent->getChildFamilies() as $cfamily) {
								$phusb = $cfamily->getHusband();
								$pwife = $cfamily->getWife();
								if ($phusb) {
									$HusbFBP = $phusb->getBirthPlace();
								}
								if ($pwife) {
									$HusbMBP = $pwife->getBirthPlace();
								}
							}

							//-- Spouse Husbands Details --------------------------------------
							$married = Date::compare($censdate, $marrdate);
							$nam     = $gparent->getAllNames();
							$fulln   = strip_tags($nam[0]['full']);
							$fulmn   = $fulln;
							foreach ($nam as $n) {
								if ($n['type'] === '_MARNM') {
									$fulmn = strip_tags($n['full']);
								}
							}
							$label = get_close_relationship_name($person, $gparent);
							$menu = new Menu($label);
							print_pedigree_person_nav_cens($gparent->getXref(), $label, $censyear);
							$submenu = new Menu($parentlinks);
							$menu->addSubmenu($submenu);
							if ($gparent->getDeathYear() == 0) {
								$DeathYr = '';
							} else {
								$DeathYr = $gparent->getDeathYear();
							}
							if ($gparent->getBirthYear() == 0) {
								$BirthYr = '';
							} else {
								$BirthYr = $gparent->getBirthYear();
							}
							?>
							<tr class="fact_value">
								<td align="left" class="linkcell optionbox nowrap">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=" . $gparent->getXref() . "&amp;gedcom=" . WT_GEDURL . "\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<a href='#' onclick='return insertRowToTable(
										"<?php echo $gparent->getXref(); ?>",
										"<?php echo $fulln; ?>",
										"<?php echo $fulmn; ?>",
										"<?php echo $person === $gparent ? 'head' : Filter::escapeHtml($label); ?>",
										"<?php echo $gparent->getSex(); ?>",
										"<?php echo $married >= 0 ? 'M' : 'S'; ?>",
										"<?php echo $marrdate ? $marrdate->JD() : ''; ?>",
										"<?php echo $gparent->getBirthDate()->JD(); ?>",
										"<?php echo $censyear - $gparent->getbirthyear(); ?>",
										"<?php echo $gparent->getDeathDate()->JD(); ?>",
										"",
										"<?php echo Filter::escapeHtml($gparent->getBirthPlace());  ?>",
										"<?php if (isset($HusbFBP)) {
												echo Filter::escapeHtml($HusbFBP); // fbirthpl = Husband Father’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Husband Father’s Place of Birth Not known
											} ?>",
										"<?php if (isset($HusbMBP)) {
												echo Filter::escapeHtml($HusbMBP); // mbirthpl = Husband Mother’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Husband Mother’s Place of Birth Not known
											} ?>",
										"<?php if (isset($chBLDarray) && $gparent->getSex() === 'F') {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											} ?>"
									);'>
										<?php echo $gparent->getFullName(); ?>
									</a>
								</td>
							<tr>
							<?php
						}

						//-- Spouse Wife -----------------------------------------------------
						if ($family->getWife()) {

							//-- Spouse Wifes Parents --------------------------------------
							$gparent = $family->getWife();
							$cfamily = null;
							foreach ($gparent->getChildFamilies() as $cfamily) {
								$husb = $cfamily->getHusband();
								$wife = $cfamily->getWife();
								if ($husb) {
									$WifeFBP = $husb->getBirthPlace();
								}
								if ($wife) {
									$WifeMBP = $wife->getBirthPlace();
								}
							}

							//-- Spouse Wifes Details --------------------------------------
							$married = Date::compare($censdate, $marrdate);
							$nam     = $gparent->getAllNames();
							$fulln   = strip_tags($nam[0]['full']);
							$fulmn   = $fulln;
							foreach ($nam as $n) {
								if ($n['type'] === '_MARNM') {
									$fulmn = strip_tags($n['full']);
								}
							}
							$label = get_close_relationship_name($person, $gparent);
							$menu = new Menu($label);
							print_pedigree_person_nav_cens($gparent->getXref(), $label, $censyear);
							$submenu = new Menu($parentlinks);
							$menu->addSubmenu($submenu);
							if ($gparent->getDeathYear() == 0) {
								$DeathYr = '';
							} else {
								$DeathYr = $gparent->getDeathYear();
							}
							if ($gparent->getBirthYear() == 0) {
								$BirthYr = '';
							} else {
								$BirthYr = $gparent->getBirthYear();
							}
							?>
							<tr>
								<td align="left" class="linkcell optionbox nowrap">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=" . $gparent->getXref() . "&amp;gedcom=" . WT_GEDURL . "\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<a href='#' onclick='return insertRowToTable(
										"<?php echo $gparent->getXref(); ?>",
										"<?php echo $fulln; ?>",
										"<?php echo $fulmn; ?>",
										"<?php echo $person === $gparent ? 'head' : Filter::escapeHtml($label); ?>",
										"<?php echo $gparent->getSex(); ?>",
										"<?php echo $married >= 0 ? 'M' : 'S'; ?>",
										"<?php echo $marrdate ? $marrdate->JD() : ''; ?>",
										"<?php echo $gparent->getBirthDate()->JD(); ?>",
										"<?php echo $censyear - $gparent->getbirthyear(); ?>",
										"<?php echo $gparent->getDeathDate()->JD(); ?>",
										"",
										"<?php echo Filter::escapeHtml($gparent->getBirthPlace()); ?>",
										"<?php if (isset($WifeFBP)) {
											echo Filter::escapeHtml($WifeFBP); // fbirthpl = Wife Father’s Place of Birth
										} else {
											echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Wife Father’s Place of Birth Not known
										} ?>",
										"<?php if (isset($WifeMBP)) {
											echo Filter::escapeHtml($WifeMBP); // mbirthpl = Wife Mother’s Place of Birth
										} else {
											echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Wife Mother’s Place of Birth Not known
										} ?>",
										"<?php if (isset($chBLDarray) && $gparent->getSex() === 'F') {
											$chBLDarray = implode("::", $chBLDarray);
											echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
										} ?>"
									);'>
										<?php echo $gparent->getFullName(); ?>
									</a>
								</td>
							<tr> <?php
						}

						// Spouse Children
						foreach ($family->getChildren() as $child) {

							// Get Spouse child’s marriage status
							$married  = '';
							$marrdate = '';
							foreach ($child->getSpouseFamilies() as $childfamily) {
								$marrdate = $childfamily->getMarriageDate();
								$married  = Date::compare($censdate, $marrdate);
							}

							// Get Child’s Children
							$chBLDarray = Array();
							foreach ($child->getSpouseFamilies() as $childfamily) {
								$chchildren = $childfamily->getChildren();
								foreach ($chchildren as $chchild) {
									$chnam   = $chchild->getAllNames();
									$chfulln = strip_tags($chnam[0]['full']); // Child’s Full Name// Child’s Full Name
									$chdob   = $chchild->getBirthDate()->JD(); // Child’s Date of Birth (Julian)
									$chdod   = $chchild->getDeathDate()->JD(); // Child’s Date of Death (Julian)
									$chBLD   = $chfulln . ', ' . $chdob . ', ' . $chdod;
									array_push($chBLDarray, $chBLD);
								}
							}

							// Get Spouse child’s details
							$nam   = $child->getAllNames();
							$fulln = strip_tags($nam[0]['full']);
							$fulmn = $fulln;
							foreach ($nam as $n) {
								if ($n['type'] === '_MARNM') {
									$fulmn = strip_tags($n['full']);
								}
							}
							$label = get_close_relationship_name($person, $child);
							$menu = new Menu($label);
							print_pedigree_person_nav_cens($child->getXref(), $label, $censyear);
							$submenu = new Menu($spouselinks);
							$menu->addSubmenu($submenu);
							?>
							<tr>
								<td align="left" class="linkcell optionbox">
									<?php echo $menu->getMenu(); ?>
								</td>
								<td align="left" class="facts_value">
									<?php
									echo "<a href=\"edit_interface.php?action=addnewnote_assisted&amp;noteid=newnote&amp;xref=" . $child->getXref() . "&amp;gedcom=" . WT_GEDURL . "\">";
									echo $headImg2;
									echo "</a>";
									?>
								</td>
								<td align="left" class="facts_value nowrap">
									<?php
									if (($child->canShow())) {
									?>
									<a href='#' onclick='return insertRowToTable(
										"<?php echo $child->getXref(); ?>",
										"<?php echo $fulln; ?>",
										"<?php echo $fulmn; ?>",
										"<?php echo $person === $child ? 'head' : Filter::escapeHtml($label); ?>",
										"<?php echo $child->getSex(); ?>",
										"<?php echo $married >= 0 ? 'M' : 'S'; ?>",
										"<?php echo $marrdate ? $marrdate->JD() : ''; ?>",
										"<?php echo $child->getBirthDate()->JD(); ?>",
										"<?php echo $censyear - $child->getbirthyear(); ?>",
										"<?php echo $child->getDeathDate()->JD(); ?>",
										"",
										"<?php echo Filter::escapeHtml($child->getBirthPlace()); ?>",
										"<?php if ($family->getHusband()) {
												echo Filter::escapeHtml($family->getHusband()->getBirthPlace()); // fbirthpl = Child Father’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // fbirthpl = Child Father’s Place of Birth Not known
											} ?>",
										"<?php if ($family->getWife()) {
												echo Filter::escapeHtml($family->getWife()->getBirthPlace()); // mbirthpl = Child Mother’s Place of Birth
											} else {
												echo 'UNK, UNK, UNK, UNK'; // mbirthpl = Child Mother’s Place of Birth Not known
											} ?>",
										"<?php if (isset($chBLDarray) && $child->getSex() === 'F') {
												$chBLDarray = implode("::", $chBLDarray);
												echo $chBLDarray; // Array of Children (name, birthdate, deathdate)
											} ?>"
									);'>
										<?php echo $child->getFullName(); ?>
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
	global $spouselinks, $parentlinks, $step_parentlinks, $persons, $person_step, $person_parent;
	global $natdad, $natmom, $censyear;

	$person = Individual::getInstance($pid);

	$tmp = array('M'=>'', 'F'=>'F', 'U'=>'NN');
	$isF = $tmp[$person->getSex()];
	$spouselinks      = '';
	$parentlinks      = '';
	$step_parentlinks = '';

	if ($person->canShowName()) {
		//-- draw a box for the family popup

		if (I18N::direction() === 'rtl') {
			$spouselinks .= "<table class=\"rtlnav person_box$isF\"><tr><td align=\"right\" style=\"font-size:10px;font-weight:normal;\" class=\"name2 nowrap\">";
			$spouselinks .= "<b>" . I18N::translate('Family') . "</b> (" . $person->getFullName() . ")<br>";
			$parentlinks .= "<table class=\"rtlnav person_box$isF\"><tr><td align=\"right\" style=\"font-size:10px;font-weight:normal;\" class=\"name2 nowrap\">";
			$parentlinks .= "<b>" . I18N::translate('Parents') . "</b> (" . $person->getFullName() . ")<br>";
			$step_parentlinks .= "<table class=\"rtlnav person_box$isF\"><tr><td align=\"right\" style=\"font-size:10px;font-weight:normal;\" class=\"name2 nowrap\">";
			$step_parentlinks .= "<b>" . I18N::translate('Parents') . "</b> (" . $person->getFullName() . ")<br>";
		} else {
			$spouselinks .= "<table class=\"ltrnav person_box$isF\"><tr><td align=\"left\" style=\"font-size:10px;font-weight:normal;\" class=\"name2 nowrap\">";
			$spouselinks .= "<b>" . I18N::translate('Family') . "</b> (" . $person->getFullName() . ")<br>";
			$parentlinks .= "<table class=\"ltrnav person_box$isF\"><tr><td align=\"left\" style=\"font-size:10px;font-weight:normal;\" class=\"name2 nowrap\">";
			$parentlinks .= "<b>" . I18N::translate('Parents') . "</b> (" . $person->getFullName() . ")<br>";
			$step_parentlinks .= "<table class=\"ltrnav person_box$isF\"><tr><td align=\"left\" style=\"font-size:10px;font-weight:normal;\" class=\"name2 nowrap\">";
			$step_parentlinks .= "<b>" . I18N::translate('Parents') . "</b> (" . $person->getFullName() . ")<br>";
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
					$chfulln = strip_tags($chnam[0]['full']);
					$chdob   = $child->getBirthDate()->julianDay();
					$chdod   = $child->getDeathDate()->julianDay();
					$chBLD   = $chfulln . ', ' . $chdob . ', ' . $chdod;
					array_push($chBLDarray, $chBLD);
				}
			}

			// Parent Husband
			if ($husb || $children) {
				if ($husb) {
					// Parent Husbands Parents
					$gparent = Individual::getInstance($husb->getXref());
					$parfams = $gparent->getChildFamilies();
					foreach ($parfams as $pfamily) {
						$phusb = $pfamily->getHusband();
						$pwife = $pfamily->getWife();
						if ($phusb) { $pHusbFBP = $phusb->getBirthPlace(); }
						if ($pwife) { $pHusbMBP = $pwife->getBirthPlace(); }
					}

					// Parent Husbands Details
					$person_parent = 'Yes';
					$nam   = $husb->getAllNames();
					$fulln = strip_tags($nam[0]['full']);
					$fulmn = $fulln;
					foreach ($nam as $n) {
						if ($n['type'] === '_MARNM') {
							$fulmn = strip_tags($n['full']);
						}
					}
					$parentlinks .= "<a class=\"linka\" href=\"#\" onclick=\"return insertRowToTable(";
					$parentlinks .= "'" . $husb->getXref() . "',"; // pid = PID
					$parentlinks .= "'" . $fulln . "',";
					$parentlinks .= "'" . $fulmn . "',";
					if ($currpid === 'Wife' || $currpid === 'Husband') {
						$parentlinks .= "'Father in Law',"; // label = 1st Gen Male Relationship
					} else {
						$parentlinks .= "'Grand-Father',"; // label = 2st Gen Male Relationship
					}
					$parentlinks .= "'" . $husb->getSex() . "',";
					$parentlinks .= "'',";
					$parentlinks .= "'" . $marrdate->julianDay() . "',";
					$parentlinks .= "'" . $husb->getBirthDate()->julianDay() . "',";
					if ($husb->getbirthyear() >= 1) {
						$parentlinks .= "'" . ($censyear - $husb->getbirthyear()) . "',"; // age =  Census Year - Year of Birth
					} else {
						$parentlinks .= "''" . ","; // age =  Undefined
					}
					$parentlinks .= "'" . $husb->getDeathDate()->julianDay() . "',";
					$parentlinks .= "'',"; // occu  = Occupation
					$parentlinks .= "'" . Filter::escapeHtml($husb->getBirthPlace()) . "'" . ","; // birthpl = Individuals Birthplace
					if (isset($pHusbFBP)) {
						$parentlinks .= "'" . Filter::escapeHtml($pHusbFBP) . "'" . ","; // fbirthpl = Fathers Birthplace
					} else {
						$parentlinks .= "'UNK, UNK, UNK, UNK'" . ","; // fbirthpl = Fathers Birthplace
					}
					if (isset($pHusbMBP)) {
						$parentlinks .= "'" . Filter::escapeHtml($pHusbMBP) . "'" . ","; // mbirthpl = Mothers Birthplace
					} else {
						$parentlinks .= "'UNK, UNK, UNK, UNK'" . ","; // mbirthpl = Mothers Birthplace
					}
					if (isset($chBLDarray) && $husb->getSex() === 'F') {
						$chBLDarray = implode("::", $chBLDarray);
						$parentlinks .= "'" . $chBLDarray . "'"; // Array of Children (name, birthdate, deathdate)
					} else {
						$parentlinks .= "''";
					}
					$parentlinks .= ");\">";
					$parentlinks .= $husb->getFullName();
					$parentlinks .= "</a>";
					$natdad = 'yes';
				}
			}

			// Parent Wife
			if ($wife || $children) {
				if ($wife) {
					// Parent Wifes Parents
					$gparent = Individual::getInstance($wife->getXref());
					$parfams = $gparent->getChildFamilies();
					foreach ($parfams as $pfamily) {
						$pwhusb = $pfamily->getHusband();
						$pwwife = $pfamily->getWife();
						if ($pwhusb) { $pWifeFBP = $pwhusb->getBirthPlace(); }
						if ($pwwife) { $pWifeMBP = $pwwife->getBirthPlace(); }
					}

					// Parent Wifes Details
					$person_parent = 'Yes';
					$nam           = $wife->getAllNames();
					$fulln         = strip_tags($nam[0]['full']);
					$fulmn         = $fulln;
					foreach ($nam as $n) {
						if ($n['type'] === '_MARNM') {
							$fulmn = strip_tags($n['full']);
						}
					}
					$parentlinks .= "<a class=\"linka\" href=\"#\" onclick=\"return insertRowToTable(";
					$parentlinks .= "'" . $wife->getXref() . "',"; // pid = PID
					$parentlinks .= "'" . $fulln . "',";
					$parentlinks .= "'" . $fulmn . "',";
					if ($currpid === 'Wife' || $currpid === 'Husband') {
						$parentlinks .= "'Mother in Law',"; // label = 1st Gen Female Relationship
					} else {
						$parentlinks .= "'Grand-Mother',"; // label = 2st Gen Female Relationship
					}
					$parentlinks .= "'" . $wife->getSex() . "',"; // sex = Gender
					$parentlinks .= "''" . ","; // cond = Condition (Married etc)
					if ($marrdate) {
						$parentlinks .= "'" . (($marrdate->minimumJulianDay() + $marrdate->maximumJulianDay()) / 2) . "',"; // dom = Date of Marriage (Julian)
					}
					$parentlinks .= "'" . (($wife->getBirthDate()->minimumJulianDay() + $wife->getBirthDate()->maximumJulianDay()) / 2) . "',"; // dob = Date of Birth
					if ($wife->getbirthyear() >= 1) {
						$parentlinks .= "'" . ($censyear - $wife->getbirthyear()) . "',"; // age =  Census Year - Year of Birth
					} else {
						$parentlinks .= "''" . ","; // age =  Undefined
					}
					$parentlinks .= "'" . (($wife->getDeathDate()->minimumJulianDay() + $wife->getDeathDate()->maximumJulianDay()) / 2) . "',"; // dod = Date of Death
					$parentlinks .= "''" . ","; // occu  = Occupation
					$parentlinks .= "'" . Filter::escapeHtml($wife->getBirthPlace()) . "'" . ","; // birthpl = Individuals Birthplace
					if (isset($pWifeFBP)) {
						$parentlinks .= "'" . Filter::escapeHtml($pWifeFBP) . "'" . ","; // fbirthpl = Fathers Birthplace
					} else {
						$parentlinks .= "'UNK, UNK, UNK, UNK'" . ","; // fbirthpl = Fathers Birthplace Not Known
					}
					if (isset($pWifeMBP)) {
						$parentlinks .= "'" . Filter::escapeHtml($pWifeMBP) . "'" . ","; // mbirthpl = Mothers Birthplace
					} else {
						$parentlinks .= "'UNK, UNK, UNK, UNK'" . ","; // mbirthpl = Mothers Birthplace Not Known
					}
					if (isset($chBLDarray) && $wife->getSex() === 'F') {
						$chBLDarray = implode("::", $chBLDarray);
						$parentlinks .= "'" . $chBLDarray . "'"; // Array of Children (name, birthdate, deathdate)
					} else {
						$parentlinks .= "''";
					}
					$parentlinks .= ");\">";
					$parentlinks .= $wife->getFullName();
					$parentlinks .= "</a>";
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
					$chfulln = strip_tags($chnam[0]['full']);
					$chdob   = $child->getBirthDate()->julianDay();
					$chdod   = $child->getDeathDate()->julianDay();
					$chBLD   = $chfulln . ', ' . $chdob . ', ' . $chdod;
					array_push($chBLDarray, $chBLD);
				}
			}

			// Step Husband
			if ($natdad === 'yes') {
			} else {
				if (($husb || $children) && $husb !== $person) {
					if ($husb) {
						// Step Husbands Parents
						$gparent = Individual::getInstance($husb->getXref());
						$parfams = $gparent->getChildFamilies();
						foreach ($parfams as $pfamily) {
							$phusb = $pfamily->getHusband();
							$pwife = $pfamily->getWife();
							if ($phusb) { $pHusbFBP = $phusb->getBirthPlace(); }
							if ($pwife) { $pHusbMBP = $pwife->getBirthPlace(); }
						}
						//-- Step Husband Details ------------------------------
						$person_step = 'Yes';
						$nam   = $husb->getAllNames();
						$fulln   = strip_tags($nam[0]['full']);
						$fulmn   = $fulln;
						foreach ($nam as $n) {
							if ($n['type'] === '_MARNM') {
								$fulmn = strip_tags($n['full']);
							}
						}
						$parentlinks .= "<a class=\"linka\" href=\"#\" onclick=\"return insertRowToTable(";
						$parentlinks .= "'" . $husb->getXref() . "',"; // pid = PID
						$parentlinks .= "'" . Filter::escapeHtml(strip_tags($fulln)) . "',"; // nam = Name
						$parentlinks .= "'" . Filter::escapeHtml(strip_tags($fulmn)) . "',"; // nam = Name
						if ($currpid === 'Wife' || $currpid === 'Husband') {
							$parentlinks .= "'Step Father-in-Law',"; // label = 1st Gen Male Relationship
						} else {
							$parentlinks .= "'Step Grand-Father',"; // label = 2st Gen Male Relationship
						}
						$parentlinks .= "'" . $husb->getSex() . "',"; // sex = Gender
						$parentlinks .= "''" . ","; // cond = Condition (Married etc)
						if ($marrdate) {
							$parentlinks .= "'" . (($marrdate->minimumJulianDay() + $marrdate->maximumJulianDay()) / 2) . "',"; // dom = Date of Marriage (Julian)
						}
						$parentlinks .= "'" . (($husb->getBirthDate()->minimumJulianDay() + $husb->getBirthDate()->maximumJulianDay()) / 2) . "',"; // dob = Date of Birth
						if ($husb->getbirthyear() >= 1) {
							$parentlinks .= "'" . ($censyear - $husb->getbirthyear()) . "',"; // age =  Census Year - Year of Birth
						} else {
							$parentlinks .= "''" . ","; // age =  Undefined
						}
						$parentlinks .= "'" . (($husb->getDeathDate()->minimumJulianDay() + $husb->getDeathDate()->maximumJulianDay()) / 2) . "',"; // dod = Date of Death
						$parentlinks .= "''" . ","; // occu  = Occupation
						$parentlinks .= "'" . Filter::escapeHtml($husb->getBirthPlace()) . "'" . ","; // birthpl = Individuals Birthplace
						if (isset($pHusbFBP)) {
							$parentlinks .= "'" . Filter::escapeHtml($pHusbFBP) . "'" . ","; // fbirthpl = Fathers Birthplace
						} else {
							$parentlinks .= "'UNK, UNK, UNK, UNK'" . ","; // fbirthpl = Fathers Birthplace
						}
						if (isset($pHusbMBP)) {
							$parentlinks .= "'" . Filter::escapeHtml($pHusbMBP) . "'" . ","; // mbirthpl = Mothers Birthplace
						} else {
							$parentlinks .= "'UNK, UNK, UNK, UNK'" . ","; // mbirthpl = Mothers Birthplace
						}
						if (isset($chBLDarray) && $husb->getSex() === 'F') {
							$chBLDarray = implode("::", $chBLDarray);
							$parentlinks .= "'" . $chBLDarray . "'"; // Array of Children (name, birthdate, deathdate)
						} else {
							$parentlinks .= "''";
						}
						$parentlinks .= ");\">";
						$parentlinks .= $husb->getFullName(); // Full Name (Link)
						$parentlinks .= "</a>";
					}
				}
			}

			// Step Wife
			if ($natmom === 'yes') {
			} else {
				// Wife
				if ($wife || $children) {
					if ($wife) {
						// Step Wifes Parents
						$gparent = Individual::getInstance($wife->getXref());
						$parfams = $gparent->getChildFamilies();
						foreach ($parfams as $pfamily) {
							$pwhusb = $pfamily->getHusband();
							$pwwife = $pfamily->getWife();
							if ($pwhusb) { $pWifeFBP = $pwhusb->getBirthPlace(); }
							if ($pwwife) { $pWifeMBP = $pwwife->getBirthPlace(); }
						}
						// Step Wife Details
						$person_step = 'Yes';
						$nam   = $wife->getAllNames();
						$fulln   = strip_tags($nam[0]['full']);
						$fulmn   = $fulln;
						foreach ($nam as $n) {
							if ($n['type'] === '_MARNM') {
								$fulmn = strip_tags($n['full']);
							}
						}
						$parentlinks .= "<a class=\"linka\" href=\"#\" onclick=\"return insertRowToTable(";
						$parentlinks .= "'" . $wife->getXref() . "',"; // pid = PID
						$parentlinks .= "'" . $fulln . "',"; // nam = Name
						$parentlinks .= "'" . $fulmn . "',"; // nam = Name
						if ($currpid === 'Wife' || $currpid === 'Husband') {
							$parentlinks .= "'Step Mother-in-Law',"; // label = 1st Gen Female Relationship
						} else {
							$parentlinks .= "'Step Grand-Mother',"; // label = 2st Gen Female Relationship
						}
						$parentlinks .= "'" . $wife->getSex() . "',"; // sex = Gender
						$parentlinks .= "''" . ","; // cond = Condition (Married etc)
						if ($marrdate) {
							$parentlinks .= "'" . (($marrdate->minimumJulianDay() + $marrdate->maximumJulianDay()) / 2) . "',"; // dom = Date of Marriage (Julian)
						}
						$parentlinks .= "'" . (($wife->getBirthDate()->minimumJulianDay() + $wife->getBirthDate()->maximumJulianDay()) / 2) . "',"; // dob = Date of Birth
						if ($wife->getbirthyear() >= 1) {
							$parentlinks .= "'" . ($censyear - $wife->getbirthyear()) . "',"; // age =  Census Year - Year of Birth
						} else {
							$parentlinks .= "''" . ","; // age =  Undefined
						}
						$parentlinks .= "'" . (($wife->getDeathDate()->minimumJulianDay() + $wife->getDeathDate()->maximumJulianDay()) / 2) . "',"; // dod = Date of Death
						$parentlinks .= "''" . ","; // occu  = Occupation
						$parentlinks .= "'" . Filter::escapeHtml($wife->getBirthPlace()) . "'" . ","; // birthpl = Individuals Birthplace
						if (isset($pWifeFBP)) {
							$parentlinks .= "'" . Filter::escapeHtml($pWifeFBP) . "'" . ","; // fbirthpl = Fathers Birthplace
						} else {
							$parentlinks .= "'UNK, UNK, UNK, UNK'" . ","; // fbirthpl = Fathers Birthplace Not Known
						}
						if (isset($pWifeMBP)) {
							$parentlinks .= "'" . Filter::escapeHtml($pWifeMBP) . "'" . ","; // mbirthpl = Mothers Birthplace
						} else {
							$parentlinks .= "'UNK, UNK, UNK, UNK'" . ","; // mbirthpl = Mothers Birthplace Not Known
						}
						if (isset($chBLDarray) && $wife->getSex() === 'F') {
							$chBLDarray = implode("::", $chBLDarray);
							$parentlinks .= "'" . $chBLDarray . "'"; // Array of Children (name, birthdate, deathdate)
						} else {
							$parentlinks .= "''";
						}
						$parentlinks .= ");\">";
						$parentlinks .= $wife->getFullName(); // Full Name (Link)
						$parentlinks .= "</a>";
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
			if (isset($children)) {
				$chBLDarray = Array();
				foreach ($children as $child) {
					$chnam   = $child->getAllNames();
					$chfulln = strip_tags($chnam[0]['full']); // Child’s Full Name
					$chdob   = $child->getBirthDate()->julianDay(); // Child’s Date of Birth (Julian)
					$chdod   = $child->getDeathDate()->julianDay(); // Child’s Date of Death (Julian)
					$chBLD   = $chfulln . ', ' . $chdob . ', ' . $chdod;
					array_push($chBLDarray, $chBLD);
				}
			}

			// Spouse
			if ($spouse || $children) {
				if ($spouse) {

					// Spouse Parents
					$gparent = Individual::getInstance($spouse->getXref());
					$spousefams = $gparent->getChildFamilies();
					foreach ($spousefams as $pfamily) {
						$phusb = $pfamily->getHusband();
						$pwife = $pfamily->getWife();
						if ($phusb) { $pSpouseFBP = $phusb->getBirthPlace(); }
						if ($pwife) { $pSpouseMBP = $pwife->getBirthPlace(); }
					}

					// Spouse Details
					$nam   = $spouse->getAllNames();
					$fulln   = strip_tags($nam[0]['full']);
					$fulmn   = $fulln;
					foreach ($nam as $n) {
						if ($n['type'] === '_MARNM') {
							$fulmn = strip_tags($n['full']);
						}
					}
					$spouselinks .= "<a href=\"#\" onclick=\"return insertRowToTable(";
					$spouselinks .= "'" . $spouse->getXref() . "',"; // pid = PID
					$spouselinks .= "'" . $fulln . "',";
					$spouselinks .= "'" . $fulmn . "',";
					if ($currpid === 'Son' || $currpid === 'Daughter') {
						if ($spouse->getSex() === 'M') {
							$spouselinks .= "'Son in Law',"; // label = Male Relationship
						} else {
							$spouselinks .= "'Daughter in Law',"; // label = Female Relationship
						}
					} else {
						if ($spouse->getSex() === 'M') {
							$spouselinks .= "'Brother in Law',"; // label = Male Relationship
						} else {
							$spouselinks .= "'Sister in Law',"; // label = Female Relationship
						}
					}
					$spouselinks .= "'" . $spouse->getSex() . "',"; // sex = Gender
					$spouselinks .= "''" . ","; // cond = Condition (Married etc)
					if ($marrdate) {
						$spouselinks .= "'" . (($marrdate->minimumJulianDay() + $marrdate->maximumJulianDay()) / 2) . "',";
					}
					$spouselinks .= "'" . (($spouse->getBirthDate()->minimumJulianDay() + $spouse->getBirthDate()->maximumJulianDay()) / 2) . "',";
					if ($spouse->getbirthyear() >= 1) {
						$spouselinks .= "'" . ($censyear - $spouse->getbirthyear()) . "',"; // age =  Census Year - Year of Birth
					} else {
						$spouselinks .= "''" . ","; // age =  Undefined
					}
					$spouselinks .= "'" . (($spouse->getDeathDate()->minimumJulianDay() + $spouse->getDeathDate()->maximumJulianDay()) / 2) . "',"; // dod = Date of Death
					$spouselinks .= "''" . ","; // occu  = Occupation
					$spouselinks .= "'" . Filter::escapeHtml($spouse->getBirthPlace()) . "'" . ","; // birthpl = Individuals Birthplace
					if (isset($pSpouseFBP)) {
						$spouselinks .= "'" . Filter::escapeHtml($pSpouseFBP) . "'" . ","; // fbirthpl = Fathers Birthplace
					} else {
						$spouselinks .= "'UNK, UNK, UNK, UNK'" . ","; // fbirthpl = Fathers Birthplace Not Known
					}
					if (isset($pSpouseMBP)) {
						$spouselinks .= "'" . Filter::escapeHtml($pSpouseMBP) . "'" . ","; // mbirthpl = Mothers Birthplace
					} else {
						$spouselinks .= "'UNK, UNK, UNK, UNK'" . ","; // mbirthpl = Mothers Birthplace Not Known
					}
					if (isset($chBLDarray) && $spouse->getSex() === 'F') {
						$chBLDarray = implode("::", $chBLDarray);
						$spouselinks .= "'" . $chBLDarray . "'"; // Array of Children (name, birthdate, deathdate)
					} else {
						$spouselinks .= "''";
					}
					$spouselinks .= ");\">";
					$spouselinks .= $spouse->getFullName(); // Full Name
					$spouselinks .= "</a>";
					if ($spouse->getFullName() != "") {
						$persons = 'Yes';
					}
				}
			}

			// Children
			$spouselinks .= '<ul class="clist">';
			foreach ($children as $child) {
				$persons = 'Yes';

				// Child’s Parents
				$gparent = Individual::getInstance($child->getXref());
				foreach ($gparent->getChildFamilies() as $family) {
					$husb = $family->getHusband();
					$wife = $family->getWife();
					if ($husb) { $ChildFBP = $husb->getBirthPlace(); }
					if ($wife) { $ChildMBP = $wife->getBirthPlace(); }
				}

				// Child’s Children
				$chBLDarray = Array();
				foreach ($child->getSpouseFamilies() as $childfamily) {
					$chchildren = $childfamily->getChildren();
					foreach ($chchildren as $chchild) {
						$chnam   = $chchild->getAllNames();
						$chfulln = strip_tags($chnam[0]['full']); // Child’s Full Name
						$chdob   = $chchild->getBirthDate()->julianDay(); // Child’s Date of Birth (Julian)
						$chdod   = $chchild->getDeathDate()->julianDay(); // Child’s Date of Death (Julian)
						$chBLD   = $chfulln . ', ' . $chdob . ', ' . $chdod;
						array_push($chBLDarray, $chBLD);
					}
				}

				// Child’s marriage status
				$marrdate = '';
				$chhusbnam = null;
				foreach ($child->getSpouseFamilies() as $childfamily) {
					$marrdate = $childfamily->getMarriageDate();
				}
				// Childs Details -------------------------
				$spouselinks .= '<li>';
				$nam     = $child->getAllNames();
				$fulln   = strip_tags($nam[0]['full']);
				$fulmn   = $fulln;
				foreach ($nam as $n) {
					if ($n['type'] === '_MARNM') {
						$fulmn = strip_tags($n['full']);
					}
				}
				$spouselinks .= "<a href=\"#\" onclick=\"return insertRowToTable(";
				$spouselinks .= "'" . $child->getXref() . "',";
				$spouselinks .= "'" . $fulln . "',";
				$spouselinks .= "'" . $fulmn . "',";
				if ($currpid === 'Son' || $currpid === 'Daughter') {
					if ($child->getSex() === 'M') {
						$spouselinks .= "'Grand-Son',"; // label = Male Relationship
					} else {
						$spouselinks .= "'Grand-Daughter',"; // label = Female Relationship
					}
				} else {
					if ($child->getSex() === 'M') {
						$spouselinks .= "'Nephew',"; // label = Male Relationship
					} else {
						$spouselinks .= "'Niece',"; // label = Female Relationship
					}
				}
				$spouselinks .= "'" . $child->getSex() . "',"; // sex = Gender
				$spouselinks .= "''" . ","; // cond = Condition (Married etc)
				if ($marrdate) {
					$spouselinks .= "'" . (($marrdate->minimumJulianDay() + $marrdate->maximumJulianDay()) / 2) . "',"; // dom = Date of Marriage (Julian)
				} else {
					$spouselinks .= "'nm'" . ",";
				}
				$spouselinks .= "'" . (($child->getBirthDate()->minimumJulianDay() + $child->getBirthDate()->maximumJulianDay()) / 2) . "',"; // dob = Date of Birth
				if ($child->getbirthyear() >= 1) {
					$spouselinks .= "'" . ($censyear - $child->getbirthyear()) . "',"; // age =  Census Year - Year of Birth
				} else {
					$spouselinks .= "''" . ","; // age =  Undefined
				}
				$spouselinks .= "'" . (($child->getDeathDate()->minimumJulianDay() + $child->getDeathDate()->maximumJulianDay()) / 2) . "',"; // dod = Date of Death
				$spouselinks .= "''" . ","; // occu  = Occupation
				$spouselinks .= "'" . Filter::escapeHtml($child->getBirthPlace()) . "'" . ","; // birthpl = Individuals Birthplace
				if (isset($ChildFBP)) {
					$spouselinks .= "'" . Filter::escapeHtml($ChildFBP) . "'" . ","; // fbirthpl = Fathers Birthplace
				} else {
					$spouselinks .= "'UNK, UNK, UNK, UNK'" . ","; // fbirthpl = Fathers Birthplace Not Known
				}
				if (isset($ChildMBP)) {
					$spouselinks .= "'" . Filter::escapeHtml($ChildMBP) . "'" . ","; // mbirthpl = Mothers Birthplace
				} else {
					$spouselinks .= "'UNK, UNK, UNK, UNK'" . ","; // mbirthpl = Mothers Birthplace Not Known
				}
				if (isset($chBLDarray) && $child->getSex() === 'F') {
					$chBLDarray = implode("::", $chBLDarray);
					$spouselinks .= "'" . $chBLDarray . "'"; // Array of Children (name, birthdate, deathdate)
				} else {
					$spouselinks .= "''";
				}
				$spouselinks .= ");\">";
				$spouselinks .= $child->getFullName();
				$spouselinks .= "</a>";
				$spouselinks .= "</li>";
			}
			$spouselinks .= '</ul>';
		}
		if ($persons !== 'Yes') {
			$spouselinks  .= '(' . I18N::translate('none') . ')</td></tr></table>';
		} else {
			$spouselinks  .= '</td></tr></table>';
		}

		if ($person_parent !== 'Yes') {
			$parentlinks .= '(' . I18N::translateContext('unknown family', 'unknown') . ')</td></tr></table>';
		} else {
			$parentlinks .= '</td></tr></table>';
		}

		if ($person_step !== 'Yes') {
			$step_parentlinks .= '(' . I18N::translateContext('unknown family', 'unknown') . ')</td></tr></table>';
		} else {
			$step_parentlinks .= '</td></tr></table>';
		}
	}
}

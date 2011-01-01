<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
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
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class relatives_WT_Module extends WT_Module implements WT_Module_Tab {
	// Extend WT_Module
	public function getTitle() {
		return WT_I18N::translate('Relatives');
	}

	// Extend WT_Module
	public function getDescription() {
		return WT_I18N::translate('Adds a tab to the individual page which displays the families and close relatives of an individual.');
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 20;
	}

	function printFamilyHeader($url, $label) {
		global $WT_IMAGES, $SEARCH_SPIDER;

		echo '<table>
			<tr>';
			if (isset($WT_IMAGES["cfamily"])) {
				echo '<td><img src="', $WT_IMAGES["cfamily"], '" border="0" class="icon" alt="" /></td>';
			}
			echo '<td><span class="subheaders">', $label, '</span>';
			if (empty($SEARCH_SPIDER)) {
				echo ' - <a href="', $url, '">', WT_I18N::translate('View Family'), '</a>';
			 }
			echo '</td>
			</tr>
		</table>';
	}

	/**
	* print parents informations
	* @param Family family
	* @param Array people
	* @param String family type
	* @return html table rows
	*/
	function printParentsRows(&$family, &$people, $type) {
		global $personcount, $WT_IMAGES, $SHOW_PEDIGREE_PLACES;

		$elderdate = "";
		//-- new father/husband
		$styleadd = "";
		if (isset($people["newhusb"])) {
			$styleadd = "red";
			?>
			<tr>
				<td class="facts_labelblue"><?php echo $people["newhusb"]->getLabel(); ?></td>
				<td class="<?php echo $this->controller->getPersonStyle($people["newhusb"]); ?>">
					<?php print_pedigree_person($people["newhusb"]->getXref(), 2, 0, $personcount++); ?>
				</td>
			</tr>
			<?php
			$elderdate = $people["newhusb"]->getBirthDate();
		}
		//-- father/husband
		if (isset($people["husb"])) {
			?>
			<tr>
				<td class="facts_label<?php echo $styleadd; ?>"><?php echo $people["husb"]->getLabel(); ?></td>
				<td class="<?php echo $this->controller->getPersonStyle($people["husb"]); ?>">
					<?php print_pedigree_person($people["husb"]->getXref(), 2, 0, $personcount++); ?>
				</td>
			</tr>
			<?php
			$elderdate = $people["husb"]->getBirthDate();
		}
		//-- missing father
		if ($type=="parents" && !isset($people["husb"]) && !isset($people["newhusb"])) {
			if ($this->controller->indi->canEdit()) {
				?>
				<tr>
					<td class="facts_label"><?php echo WT_I18N::translate('Add a new father'); ?></td>
					<td class="facts_value"><a href="javascript <?php echo WT_I18N::translate('Add a new father'); ?>" onclick="return addnewparentfamily('<?php echo $this->controller->pid; ?>', 'HUSB', '<?php echo $family->getXref(); ?>');"><?php echo WT_I18N::translate('Add a new father'); ?></a><?php echo help_link('edit_add_parent'); ?></td>
				</tr>
				<?php
			}
		}
		//-- missing husband
		if ($type=="spouse" && $this->controller->indi->equals($people["wife"]) && !isset($people["husb"]) && !isset($people["newhusb"])) {
			if ($this->controller->indi->canEdit()) {
				?>
				<tr>
					<td class="facts_label"><?php echo WT_I18N::translate('Add husband'); ?></td>
					<td class="facts_value"><a href="javascript:;" onclick="return addnewspouse('<?php echo $family->getXref(); ?>', 'HUSB');"><?php echo WT_I18N::translate('Add a husband to this family'); ?></a></td>
				</tr>
				<?php
			}
		}
		//-- new mother/wife
		$styleadd = "";
		if (isset($people["newwife"])) {
			$styleadd = "red";
			?>
			<tr>
				<td class="facts_labelblue"><?php echo $people["newwife"]->getLabel($elderdate); ?></td>
				<td class="<?php echo $this->controller->getPersonStyle($people["newwife"]); ?>">
					<?php print_pedigree_person($people["newwife"]->getXref(), 2, 0, $personcount++); ?>
				</td>
			</tr>
			<?php
		}
		//-- mother/wife
		if (isset($people["wife"])) {
			?>
			<tr>
				<td class="facts_label<?php echo $styleadd; ?>"><?php echo $people["wife"]->getLabel($elderdate); ?></td>
				<td class="<?php echo $this->controller->getPersonStyle($people["wife"]); ?>">
					<?php print_pedigree_person($people["wife"]->getXref(), 2, 0, $personcount++); ?>
				</td>
			</tr>
			<?php
		}
		//-- missing mother
		if ($type=="parents" && !isset($people["wife"]) && !isset($people["newwife"])) {
			if ($this->controller->indi->canEdit()) {
				?>
				<tr>
					<td class="facts_label"><?php echo WT_I18N::translate('Add a new mother'); ?></td>
					<td class="facts_value"><a href="javascript:;" onclick="return addnewparentfamily('<?php echo $this->controller->pid; ?>', 'WIFE', '<?php echo $family->getXref(); ?>');"><?php echo WT_I18N::translate('Add a new mother'); ?></a><?php echo help_link('edit_add_parent'); ?></td>
				</tr>
				<?php
			}
		}
		//-- missing wife
		if ($type=="spouse" && $this->controller->indi->equals($people["husb"]) && !isset($people["wife"]) && !isset($people["newwife"])) {
			if ($this->controller->indi->canEdit()) {
				?>
				<tr>
					<td class="facts_label"><?php echo WT_I18N::translate('Add wife'); ?></td>
					<td class="facts_value"><a href="javascript:;" onclick="return addnewspouse('<?php echo $family->getXref(); ?>', 'WIFE');"><?php echo WT_I18N::translate('Add a wife to this family'); ?></a></td>
				</tr>
				<?php
			}
		}
		//-- marriage row
		if ($family->getMarriageRecord()!="" || WT_USER_CAN_EDIT) {
			$styleadd = "";
			$date = $family->getMarriageDate();
			$place = $family->getMarriagePlace();
			$famid = $family->getXref();
			if (!$date && $this->controller->show_changes && ($famrec = find_updated_record($famid))!==null) {
				$marrrec = get_sub_record(1, "1 MARR", $famrec);
				if ($marrrec!=$family->getMarriageRecord()) {
					$date = new WT_Date(get_gedcom_value("MARR:DATE", 1, $marrrec, '', false));
					$place = get_gedcom_value("MARR:PLAC", 1, $marrrec, '', false);
					$styleadd = "blue";
				}
			}
			?>
			<tr>
				<td class="facts_label"><br />
				</td>
				<td class="facts_value<?php echo $styleadd; ?>">
					<?php //echo "<span class=\"details_label\">".translate_fact('NCHI').": </span>".$family->getNumberOfChildren()."<br />"; ?>
					<?php $marr_type = strtoupper($family->getMarriageType());
					if ($marr_type=='CIVIL' || $marr_type=='PARTNERS' || $marr_type=='RELIGIOUS' || $marr_type=='UNKNOWN') {
						$marr_fact = translate_fact("MARR_".$marr_type);
					} else if ($marr_type) {
						$marr_fact = translate_fact("MARR").' '.$family->getMarriageType();
					} else {
						$marr_fact = translate_fact("MARR");
					}
					if ($date && $date->isOK() || $place) {
						echo '<span class="details_label">', $marr_fact, ': </span>';
						if ($date) {
							echo $date->Display(false);
							if (!empty($place)) echo ' -- ';
						}
						if (!empty($place)) {
							$html='';
							$levels = explode(',', $place);
							$tempURL = "placelist.php?action=show&amp;";
							foreach (array_reverse($levels) as $pindex=>$ppart) {
								$tempURL .= "parent[{$pindex}]=".rawurlencode($ppart).'&amp;';
							}
							$tempURL .= 'level='.count($levels);
							$html .= '<a href="'.$tempURL.'"> ';
							for ($level=0; $level<$SHOW_PEDIGREE_PLACES; $level++) {
								if (!empty($levels[$level])) {
									if ($level>0) {
										$html.=", ";
									}
									$html.=PrintReady($levels[$level]);
								}
							}
							$html.='</a>';
							echo $html;
						}
					} else if (get_sub_record(1, "1 _NMR", find_family_record($famid, WT_GED_ID))) {
						$husb = $family->getHusband();
						$wife = $family->getWife();
						if (empty($wife) && !empty($husb)) echo translate_fact('_NMR', $husb);
						else if (empty($husb) && !empty($wife)) echo translate_fact('_NMR', $wife);
						else echo translate_fact('_NMR');
					} else if (get_sub_record(1, "1 _NMAR", find_family_record($famid, WT_GED_ID))) {
						$husb = $family->getHusband();
						$wife = $family->getWife();
						if (empty($wife) && !empty($husb)) echo translate_fact('_NMAR', $husb);
						else if (empty($husb) && !empty($wife)) echo translate_fact('_NMAR', $wife);
						else echo translate_fact('_NMAR');
					} else if ($family->getMarriageRecord()=="" && $this->controller->indi->canEdit()) {
						echo "<a href=\"#\" onclick=\"return add_new_record('".$famid."', 'MARR');\">".WT_I18N::translate('Add marriage details')."</a>";
					} else {
						$factdetail = explode(' ', trim($family->getMarriageRecord()));
						if (isset($factdetail) && count($factdetail) == 3) {
							if (strtoupper($factdetail[2]) == "Y") {
								echo '<span class="details_label">', $marr_fact, ': </span>', WT_I18N::translate('Yes');
							} else if (strtoupper($factdetail[2]) == "N") {
								echo '<span class="details_label">', $marr_fact, ': </span>', WT_I18N::translate('No');
							}
						} else {
							echo '<span class="details_label">', $marr_fact, '</span>';
						}
					}
					?>
				</td>
			</tr>
			<?php
		}
	}

	/**
	* print children informations
	* @param Family family
	* @param Array people
	* @param String family type
	* @return html table rows
	*/
	function printChildrenRows(&$family, &$people, $type) {
		global $personcount, $WT_IMAGES;

		$elderdate = $family->getMarriageDate();
		$key=0;
		foreach ($people["children"] as $child) {
			$label = $child->getLabel();
			$styleadd = "";
			?>
			<tr>
				<td class="facts_label<?php echo $styleadd; ?>"><?php if ($styleadd=="red") echo $child->getLabel(); else echo $child->getLabel($elderdate, $key+1); ?></td>
				<td class="<?php echo $this->controller->getPersonStyle($child); ?>">
				<?php
				print_pedigree_person($child->getXref(), 2, 0, $personcount++);
				?>
				</td>
			</tr>
			<?php
			$elderdate = $child->getBirthDate();
			++$key;
		}
		foreach ($people["newchildren"] as $child) {
			$label = $child->getLabel();
			$styleadd = "blue";
			?>
			<tr>
				<td class="facts_label<?php echo $styleadd; ?>"><?php if ($styleadd=="red") echo $child->getLabel(); else echo $child->getLabel($elderdate, $key+1); ?></td>
				<td class="<?php echo $this->controller->getPersonStyle($child); ?>">
				<?php
				print_pedigree_person($child->getXref(), 2, 0, $personcount++);
				?>
				</td>
			</tr>
			<?php
			$elderdate = $child->getBirthDate();
			++$key;
		}
		foreach ($people["delchildren"] as $child) {
			$label = $child->getLabel();
			$styleadd = "red";
			?>
			<tr>
				<td class="facts_label<?php echo $styleadd; ?>"><?php if ($styleadd=="red") echo $child->getLabel(); else echo $child->getLabel($elderdate, $key+1); ?></td>
				<td class="<?php echo $this->controller->getPersonStyle($child); ?>">
				<?php
				print_pedigree_person($child->getXref(), 2, 0, $personcount++);
				?>
				</td>
			</tr>
			<?php
			$elderdate = $child->getBirthDate();
			++$key;
		}
		if (isset($family) && $this->controller->indi->canEdit()) {
			if ($type == "spouse") {
				$child_u = WT_I18N::translate('Add a son or daughter');
				$child_m = WT_I18N::translate('Son');
				$child_f = WT_I18N::translate('Daughter');
			}
			else {
				$child_u = WT_I18N::translate('Add a brother or sister');
				$child_m = WT_I18N::translate('Brother');
				$child_f = WT_I18N::translate('Sister');
			}
		?>
			<tr>
				<td class="facts_label">
					<?php if (WT_USER_CAN_EDIT && isset($people["children"][1])) { ?>
					<a href="javascript:;" onclick="reorder_children('<?php echo $family->getXref(); ?>');tabswitch(5);"><img src="images/topdown.gif" alt="" border="0" /> <?php echo WT_I18N::translate('Re-order children'); ?></a>
					<?php } ?>
				</td>
				<td class="facts_value">
					<a href="javascript:;" onclick="return addnewchild('<?php echo $family->getXref(); ?>');"><?php echo $child_u; ?></a>
					<span style='white-space:nowrap;'>
						<a href="javascript:;" onclick="return addnewchild('<?php echo $family->getXref(); ?>','M');"><?php echo WT_Person::sexImage('M', 'small', '', $child_m); ?></a>
						<a href="javascript:;" onclick="return addnewchild('<?php echo $family->getXref(); ?>','F');"><?php echo WT_Person::sexImage('F', 'small', '', $child_f); ?></a>
					</span>
					<?php
						if ($type=='spouse') {
							echo help_link('add_son_daughter');
						} else {
							echo help_link('add_sibling');
						}
					?>
				</td>
			</tr>
			<?php
		}
	}

	// Implement WT_Module_Tab
	public function getTabContent() {
		global $WT_IMAGES, $SHOW_AGE_DIFF, $GEDCOM, $ABBREVIATE_CHART_LABELS, $show_full, $personcount;

		if (isset($show_full)) $saved_show_full = $show_full; // We always want to see full details here
		$show_full = 1;

		$saved_ABBREVIATE_CHART_LABELS = $ABBREVIATE_CHART_LABELS;
		$ABBREVIATE_CHART_LABELS = false; // Override GEDCOM configuration

		ob_start();
		?>
		<table class="facts_table"><tr><td style="width:20%; padding:4px"></td><td class="descriptionbox rela">
		<input id="checkbox_elder" type="checkbox" onclick="toggleByClassName('DIV', 'elderdate');" <?php if ($SHOW_AGE_DIFF) echo "checked=\"checked\""; ?>/>
		<label for="checkbox_elder"><?php echo WT_I18N::translate('Show date differences'), help_link('age_differences'); ?></label>
		</td></tr></table>
		<?php
		$personcount=0;
		$families = $this->controller->indi->getChildFamilies();
		if (count($families)==0) {
			if ($this->controller->indi->canEdit()) {
				?>
				<table class="facts_table">
					<tr>
						<td class="facts_value"><a href="javascript:;" onclick="return addnewparent('<?php echo $this->controller->pid; ?>', 'HUSB');"><?php echo WT_I18N::translate('Add a new father'); ?></a><?php echo help_link('edit_add_parent'); ?></td>
					</tr>
					<tr>
						<td class="facts_value"><a href="javascript:;" onclick="return addnewparent('<?php echo $this->controller->pid; ?>', 'WIFE');"><?php echo WT_I18N::translate('Add a new mother'); ?></a><?php echo help_link('edit_add_parent'); ?></td>
					</tr>
				</table>
				<?php
			}
		}
		//-- parent families
		foreach ($families as $famid=>$family) {
			$people = $this->controller->buildFamilyList($family, "parents");
			$this->printFamilyHeader($family->getHtmlUrl(), $this->controller->indi->getChildFamilyLabel($family));
			?>
			<table class="facts_table">
				<?php
				$this->printParentsRows($family, $people, "parents");
				$this->printChildrenRows($family, $people, "parents");
				?>
			</table>
		<?php
		}

		//-- step families
		foreach ($this->controller->indi->getStepFamilies() as $famid=>$family) {
			$people = $this->controller->buildFamilyList($family, "step");
			$this->printFamilyHeader($family->getHtmlUrl(), $this->controller->indi->getStepFamilyLabel($family));
			?>
			<table class="facts_table">
				<?php
				$this->printParentsRows($family, $people, "step");
				$this->printChildrenRows($family, $people, "step");
				?>
			</table>
		<?php
		}

		//-- spouses and children
		$families = $this->controller->indi->getSpouseFamilies();
		foreach ($families as $famid=>$family) {
			$people = $this->controller->buildFamilyList($family, "spouse");
			$this->printFamilyHeader($family->getHtmlUrl(), $this->controller->indi->getSpouseFamilyLabel($family));
			?>
			<table class="facts_table">
				<?php
				$this->printParentsRows($family, $people, "spouse");
				$this->printChildrenRows($family, $people, "spouse");
				?>
			</table>
		<?php
		}

		?>
		<script type="text/javascript">
		<!--
			<?php if (!$SHOW_AGE_DIFF) echo "toggleByClassName('DIV', 'elderdate');"; ?>
		//-->
		</script>
		<br />
		<?php
		if ($this->controller->indi->canEdit()) {
		?>
		<table class="facts_table">
		<?php if (count($families)>1) { ?>
			<tr>
				<td class="facts_value">
				<a href="javascript:;" onclick="return reorder_families('<?php echo $this->controller->pid; ?>');"><?php echo WT_I18N::translate('Reorder families'); ?></a>
				<?php echo help_link('reorder_families'); ?>
				</td>
			</tr>
		<?php } ?>
			<tr>
				<td class="facts_value">
				<a href="javascript:;" onclick="return add_famc('<?php echo $this->controller->pid; ?>');"><?php echo WT_I18N::translate('Link this person to an existing family as a child'); ?></a>
				<?php echo help_link('link_child'); ?>
				</td>
			</tr>
			<?php if ($this->controller->indi->getSex()!="F") { ?>
			<tr>
				<td class="facts_value">
				<a href="javascript:;" onclick="return addspouse('<?php echo $this->controller->pid; ?>','WIFE');"><?php echo WT_I18N::translate('Add a new wife'); ?></a>
				<?php echo help_link('add_wife'); ?>
				</td>
			</tr>
			<tr>
				<td class="facts_value">
				<a href="javascript:;" onclick="return linkspouse('<?php echo $this->controller->pid; ?>','WIFE');"><?php echo WT_I18N::translate('Add a wife using an existing person'); ?></a>
				<?php echo help_link('link_new_wife'); ?>
				</td>
			</tr>
			<tr>
				<td class="facts_value">
				<a href="javascript:;" onclick="return add_fams('<?php echo $this->controller->pid; ?>','HUSB');"><?php echo WT_I18N::translate('Link this person to an existing family as a husband'); ?></a>
				<?php echo help_link('link_new_husb'); ?>
				</td>
			</tr>
			<?php }
			if ($this->controller->indi->getSex()!="M") { ?>
			<tr>
				<td class="facts_value">
				<a href="javascript:;" onclick="return addspouse('<?php echo $this->controller->pid; ?>','HUSB');"><?php echo WT_I18N::translate('Add a new husband'); ?></a>
				<?php echo help_link('add_husband'); ?>
				</td>
			</tr>
			<tr>
				<td class="facts_value">
				<a href="javascript:;" onclick="return linkspouse('<?php echo $this->controller->pid; ?>','HUSB');"><?php echo WT_I18N::translate('Add a husband using an existing person'); ?></a>
				<?php echo help_link('link_husband'); ?>
				</td>
			</tr>
			<tr>
				<td class="facts_value">
				<a href="javascript:;" onclick="return add_fams('<?php echo $this->controller->pid; ?>','WIFE');"><?php echo WT_I18N::translate('Link this person to an existing family as a wife'); ?></a>
				<?php echo help_link('link_wife'); ?>
				</td>
			</tr>
			<?php } ?>
<?php if (WT_USER_CAN_ACCEPT) { // NOTE this function is restricted to ACCEPTORS because another bug prevents pending changes being shown on the close relatives tab of the indi page. Once that bug is fixed, this function can be opened up to all! ?>
			<tr>
				<td class="facts_value">
				<a href="javascript:;" onclick="return addopfchild('<?php echo $this->controller->pid; ?>','U');"><?php echo WT_I18N::translate('Add a child to create a one-parent family'); ?></a>
				<?php echo help_link('add_opf_child'); ?>
				</td>
			</tr>
<?php } ?>
		</table>
		<?php } ?>
		<br />
		<?php

		$ABBREVIATE_CHART_LABELS = $saved_ABBREVIATE_CHART_LABELS; // Restore GEDCOM configuration
		unset($show_full);
		if (isset($saved_show_full)) $show_full = $saved_show_full;

		return '<div id="'.$this->getName().'_content">'.ob_get_clean().'</div>';
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		return true;
	}
	// Implement WT_Module_Tab
	public function canLoadAjax() {
		return true;
	}

	// Implement WT_Module_Tab
	public function getPreLoadContent() {
		return '';
	}

	// Implement WT_Module_Tab
	public function getJSCallback() {
		return '';
	}
}

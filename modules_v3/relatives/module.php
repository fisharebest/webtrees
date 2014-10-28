<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

class relatives_WT_Module extends WT_Module implements WT_Module_Tab {
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Families');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Families” module */ WT_I18N::translate('A tab showing the close relatives of an individual.');
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 20;
	}

	static function ageDifference(WT_Date $prev, WT_Date $next, $child_number=0) {
		if ($prev->isOK() && $next->isOK()) {
			$days = $next->MaxJD() - $prev->MinJD();
			if ($days<0) {
				// Show warning triangle if dates in reverse order
				$diff = '<i class="icon-warning"></i> ';
			} elseif ($child_number>1 && $days>1 && $days<240) {
				// Show warning triangle if children born too close together
				$diff = '<i class="icon-warning"></i> ';
			} else {
				$diff = '';
			}

			$months = round($days * 12 / 365.25); // Approximate - we do not know the calendar
			if (abs($months)==12 || abs($months)>=24) {
				$diff .= WT_I18N::plural('%d year', '%d years', round($months / 12), round($months / 12));
			} elseif ($months!=0) {
				$diff .= WT_I18N::plural('%d month', '%d months', $months, $months);
			}

			return '<div class="elderdate age">' . $diff . '</div>';
		} else {
			return '';
		}
	}

	// print parents informations
	function printFamily(WT_Family $family, $type, $label) {
		global $controller;
		global $SHOW_PRIVATE_RELATIONSHIPS;

		if ($SHOW_PRIVATE_RELATIONSHIPS) {
			$access_level = WT_PRIV_HIDE;
		} else {
			$access_level = WT_USER_ACCESS_LEVEL;
		}

		?>
		<table>
			<tr>
				<td>
					<i class="icon-cfamily"></i>
				</td>
				<td>
					<span class="subheaders"> <?php echo $label; ?> </span> -
					<a href="<?php echo $family->getHtmlUrl() ; ?>"><?php echo WT_I18N::translate('View family'); ?></a>
				</td>
			</tr>
		</table>
		<table class="facts_table">
		<?php

		///// HUSB /////
		$found = false;
		foreach ($family->getFacts('HUSB', false, $access_level) as $fact) {
			$found |= !$fact->isPendingDeletion();
			$person = $fact->getTarget();
			if ($person instanceof WT_Individual) {
				if ($fact->isPendingAddition()) {
					$class = 'facts_label new';
				} elseif ($fact->isPendingDeletion()) {
					$class = 'facts_label old';
				} else {
					$class = 'facts_label';
				}
				?>
					<tr>
					<td class="<?php echo $class; ?>">
						<?php echo get_close_relationship_name($controller->record, $person); ?>
					</td>
					<td class="<?php echo $controller->getPersonStyle($person); ?>">
						<?php print_pedigree_person($person, 2); ?>
					</td>
					</tr>
				<?php
			}
		}
		if (!$found && $family->canEdit()) {
			?>
			<tr>
				<td class="facts_label">&nbsp;</td>
				<td class="facts_value"><a href="#" onclick="return add_spouse_to_family('<?php echo $family->getXref(); ?>', 'HUSB');"><?php echo WT_I18N::translate('Add a husband to this family'); ?></a></td>
			</tr>
			<?php
		}

		///// WIFE /////
		$found = false;
		foreach ($family->getFacts('WIFE', false, $access_level) as $fact) {
			$person = $fact->getTarget();
			if ($person instanceof WT_Individual) {
				$found |= !$fact->isPendingDeletion();
				if ($fact->isPendingAddition()) {
					$class = 'facts_label new';
				} elseif ($fact->isPendingDeletion()) {
					$class = 'facts_label old';
				} else {
					$class = 'facts_label';
				}
				?>
				<tr>
					<td class="<?php echo $class; ?>">
						<?php echo get_close_relationship_name($controller->record, $person); ?>
					</td>
					<td class="<?php echo $controller->getPersonStyle($person); ?>">
						<?php print_pedigree_person($person, 2); ?>
					</td>
				</tr>
				<?php
			}
		}
		if (!$found && $family->canEdit()) {
			?>
			<tr>
				<td class="facts_label">&nbsp;</td>
				<td class="facts_value"><a href="#" onclick="return add_spouse_to_family('<?php echo $family->getXref(); ?>', 'WIFE');"><?php echo WT_I18N::translate('Add a wife to this family'); ?></a></td>
			</tr>
			<?php
		}

		///// MARR /////
		$found = false;
		$prev = new WT_Date('');
		foreach ($family->getFacts(WT_EVENTS_MARR) as $fact) {
			$found |= !$fact->isPendingDeletion();
			if ($fact->isPendingAddition()) {
				$class = ' new';
			} elseif ($fact->isPendingDeletion()) {
				$class = ' old';
			} else {
				$class = '';
			}
			?>
			<tr>
				<td class="facts_label">
					&nbsp;
				</td>
				<td class="facts_value<?php echo $class; ?>">
					<?php echo WT_Gedcom_Tag::getLabelValue($fact->getTag(), $fact->getDate()->display() . ' — ' . $fact->getPlace()->getFullName()); ?>
				</td>
			</tr>
			<?php
			if (!$prev->isOK() && $fact->getDate()->isOK()) {
				$prev = $fact->getDate();
			}
		}
		if (!$found && $family->canShow() && $family->canEdit()) {
			// Add a new marriage
			?>
			<tr>
				<td class="facts_label">
					&nbsp;
				</td>
				<td class="facts_value">
					<a href="#" onclick="return add_new_record('<?php echo $family->getXref(); ?>', 'MARR');">
						<?php echo WT_I18N::translate('Add marriage details'); ?>
					</a>
				</td>
			</tr>
			<?php
		}

		///// CHIL /////
		$child_number = 0;
		foreach ($family->getFacts('CHIL', false, $access_level) as $fact) {
			$person = $fact->getTarget();
			if ($person instanceof WT_Individual) {
				if ($fact->isPendingAddition()) {
					$child_number++;
					$class = 'facts_label new';
				} elseif ($fact->isPendingDeletion()) {
					$class = 'facts_label old';
				} else {
					$child_number++;
					$class = 'facts_label';
				}
				$next = new WT_Date('');
				foreach ($person->getFacts(WT_EVENTS_BIRT) as $bfact) {
					if ($bfact->getDate()->isOK()) {
						$next=$bfact->getDate();
						break;
					}
				}
				?>
				<tr>
					<td class="<?php echo $class; ?>">
						<?php echo self::ageDifference($prev, $next, $child_number); ?>
						<?php echo get_close_relationship_name($controller->record, $person); ?>
					</td>
					<td class="<?php echo $controller->getPersonStyle($person); ?>">
						<?php print_pedigree_person($person, 2); ?>
					</td>
				</tr>
				<?php
				$prev = $next;
			}
		}
		// Re-order children / add a new child
		if ($family->canEdit()) {
			if ($type == 'FAMS') {
				$add_child_text = WT_I18N::translate('Add a new son or daughter');
			} else {
				$add_child_text = WT_I18N::translate('Add a new brother or sister');
			}
			?>
			<tr>
				<td class="facts_label">
					<?php if (count($family->getChildren())>1) { ?>
					<a href="#" onclick="reorder_children('<?php echo $family->getXref(); ?>');tabswitch(5);"><i class="icon-media-shuffle"></i> <?php echo WT_I18N::translate('Re-order children'); ?></a>
					<?php } ?>
				</td>
				<td class="facts_value">
					<a href="#" onclick="return add_child_to_family('<?php echo $family->getXref(); ?>');"><?php echo $add_child_text; ?></a>
					<span style='white-space:nowrap;'>
						<a href="#" class="icon-sex_m_15x15" onclick="return add_child_to_family('<?php echo $family->getXref(); ?>','M');"></a>
						<a href="#" class="icon-sex_f_15x15" onclick="return add_child_to_family('<?php echo $family->getXref(); ?>','F');"></a>
					</span>
				</td>
			</tr>
			<?php
		}

		echo '</table>';

		return;
	}

	// Implement WT_Module_Tab
	public function getTabContent() {
		global $SHOW_AGE_DIFF, $show_full, $controller;

		if (isset($show_full)) $saved_show_full = $show_full; // We always want to see full details here
		$show_full = 1;

		ob_start();
		?>
		<table class="facts_table"><tr><td class="descriptionbox rela">
		<input id="checkbox_elder" type="checkbox" onclick="jQuery('div.elderdate').toggle();" <?php if ($SHOW_AGE_DIFF) echo "checked=\"checked\""; ?>>
		<label for="checkbox_elder"><?php echo WT_I18N::translate('Show date differences'); ?></label>
		</td></tr></table>
		<?php
		$families = $controller->record->getChildFamilies();
		if (!$families && $controller->record->canEdit()) {
			?>
			<table class="facts_table">
				<tr>
					<td class="facts_value"><a href="#" onclick="return add_parent_to_individual('<?php echo $controller->record->getXref(); ?>', 'M');"><?php echo WT_I18N::translate('Add a new father'); ?></td>
				</tr>
				<tr>
					<td class="facts_value"><a href="#" onclick="return add_parent_to_individual('<?php echo $controller->record->getXref(); ?>', 'F');"><?php echo WT_I18N::translate('Add a new mother'); ?></a></td>
				</tr>
			</table>
			<?php
		}

		// parents
		foreach ($families as $family) {
			$this->printFamily($family, 'FAMC', $controller->record->getChildFamilyLabel($family));
		}

		// step-parents
		foreach ($controller->record->getChildStepFamilies() as $family) {
			$this->printFamily($family, 'FAMC', $controller->record->getStepFamilyLabel($family));
		}

		// spouses
		$families = $controller->record->getSpouseFamilies();
		foreach ($families as $family) {
			$this->printFamily($family, 'FAMS', $controller->record->getSpouseFamilyLabel($family));
		}

		// step-children
		foreach ($controller->record->getSpouseStepFamilies() as $family) {
			$this->printFamily($family, 'FAMS', $family->getFullName());
		}

		if (!$SHOW_AGE_DIFF) {
			echo '<script>jQuery("DIV.elderdate").toggle();</script>';
		}

		if ($controller->record->canEdit()) {
		?>
		<br><table class="facts_table">
		<?php
			if (count($families)>1) { ?>
			<tr>
				<td class="facts_value">
				<a href="#" onclick="return reorder_families('<?php echo $controller->record->getXref(); ?>');"><?php echo WT_I18N::translate('Re-order families'); ?></a>
				</td>
			</tr>
		<?php } ?>
			<tr>
				<td class="facts_value">
				<a href="#" onclick="return add_famc('<?php echo $controller->record->getXref(); ?>');"><?php echo WT_I18N::translate('Link this individual to an existing family as a child'); ?></a>
				</td>
			</tr>
			<?php if ($controller->record->getSex()!="F") { ?>
			<tr>
				<td class="facts_value">
				<a href="#" onclick="return add_spouse_to_individual('<?php echo $controller->record->getXref(); ?>','WIFE');"><?php echo WT_I18N::translate('Add a new wife'); ?></a>
				</td>
			</tr>
			<tr>
				<td class="facts_value">
				<a href="#" onclick="return linkspouse('<?php echo $controller->record->getXref(); ?>','WIFE');"><?php echo WT_I18N::translate('Add a wife using an existing individual'); ?></a>
				</td>
			</tr>
			<?php }
			if ($controller->record->getSex()!="M") { ?>
			<tr>
				<td class="facts_value">
				<a href="#" onclick="return add_spouse_to_individual('<?php echo $controller->record->getXref(); ?>','HUSB');"><?php echo WT_I18N::translate('Add a new husband'); ?></a>
				</td>
			</tr>
			<tr>
				<td class="facts_value">
				<a href="#" onclick="return linkspouse('<?php echo $controller->record->getXref(); ?>','HUSB');"><?php echo WT_I18N::translate('Add a husband using an existing individual'); ?></a>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td class="facts_value">
				<a href="#" onclick="return add_child_to_individual('<?php echo $controller->record->getXref(); ?>','U');"><?php echo WT_I18N::translate('Add a child to create a one-parent family'); ?></a>
				</td>
			</tr>
		</table>
		<?php } ?>
		<br>
		<?php

		unset($show_full);
		if (isset($saved_show_full)) $show_full = $saved_show_full;

		return '<div id="'.$this->getName().'_content">'.ob_get_clean().'</div>';
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		return true;
	}
	// Implement WT_Module_Tab
	public function isGrayedOut() {
		return false;
	}
	// Implement WT_Module_Tab
	public function canLoadAjax() {
		global $SEARCH_SPIDER;

		return !$SEARCH_SPIDER; // Search engines cannot use AJAX
	}

	// Implement WT_Module_Tab
	public function getPreLoadContent() {
		return '';
	}
}

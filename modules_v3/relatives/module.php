<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class relatives_WT_Module extends WT_Module implements WT_Module_Tab {
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Families');

		// Keep these deleted translations for a while - we may want them again... 
		WT_I18N::translate('Add a new spouse');
		WT_I18N::translate('Add a new parent');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Families” module */ WT_I18N::translate('A tab showing the close relatives of an individual.');
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 20;
	}

	// print parents informations
	function printFamily(WT_Family $family, $type, $label) {
		global $controller;
		global $personcount; // TODO: use a unique id instead?
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
		foreach ($family->getFacts('HUSB', $access_level) as $fact) {
			$found |= !$fact->isOld();
			$person = $fact->getTarget();
			if ($person instanceof WT_Individual) {
				if ($fact->isNew()) {
					$class = 'facts_label new';
				} elseif ($fact->isOld()) {
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
						<?php print_pedigree_person($person, 2, 0, $personcount++); ?>
					</td>
					</tr>
				<?php
			}
		}
		if (!$found && $family->canEdit()) {
			?>
			<tr>
				<td class="facts_label"><?php echo WT_I18N::translate('Add husband'); ?></td>
				<td class="facts_value"><a href="#" onclick="return add_spouse_to_family('<?php echo $family->getXref(); ?>', 'HUSB');"><?php echo WT_I18N::translate('Add a husband to this family'); ?></a></td>
			</tr>
			<?php
		}

		///// MARR /////
		$found = false;
		foreach ($family->getFacts(WT_EVENTS_MARR) as $fact) {
			$found |= !$fact->isOld();
			if ($fact->isNew()) {
				$class = 'facts_label new';
			} elseif ($fact->isOld()) {
				$class = 'facts_label old';
			} else {
				$class = 'facts_label';
			}
			?>
			<tr>
				<td class="facts_label">
					&nbsp;
				</td>
				<td class="facts_value">
					<?php echo WT_Gedcom_Tag::getLabelValue($fact->getTag(), $fact->getPlace() . ' — ' . $fact->getDate()->Display(false)); ?>
				</td>
			</tr>
			<?php
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

		///// WIFE /////
		$found = false;
		foreach ($family->getFacts('WIFE', $access_level) as $fact) {
			$person = $fact->getTarget();
			if ($person instanceof WT_Individual) {
				$found |= !$fact->isOld();
				if ($fact->isNew()) {
					$class = 'facts_label new';
				} elseif ($fact->isOld()) {
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
						<?php print_pedigree_person($person, 2, 0, $personcount++); ?>
					</td>
				</tr>
				<?php
			}
		}
		if (!$found && $family->canEdit()) {
			?>
			<tr>
				<td class="facts_label"><?php echo WT_I18N::translate('Add wife'); ?></td>
				<td class="facts_value"><a href="#" onclick="return add_spouse_to_family('<?php echo $family->getXref(); ?>', 'WIFE');"><?php echo WT_I18N::translate('Add a wife to this family'); ?></a></td>
			</tr>
			<?php
		}

		///// CHIL /////
		foreach ($family->getFacts('CHIL', $access_level) as $fact) {
			$person = $fact->getTarget();
			if ($person instanceof WT_Individual) {
				if ($fact->isNew()) {
					$class = 'facts_label new';
				} elseif ($fact->isOld()) {
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
						<?php print_pedigree_person($person, 2, 0, $personcount++); ?>
					</td>
				</tr>
				<?php
			}
		}
		// Re-order children / add a new child
		if ($family->canEdit()) {
			if ($type == 'FAMS') {
				$child_u = WT_I18N::translate('Add a new son or daughter');
				$child_m = WT_I18N::translate('son');
				$child_f = WT_I18N::translate('daughter');
			} else {
				$child_u = WT_I18N::translate('Add a new brother or sister');
				$child_m = WT_I18N::translate('brother');
				$child_f = WT_I18N::translate('sister');
			}
			?>
			<tr>
				<td class="facts_label">
					<?php if (count($family->getChildren())>1) { ?>
					<a href="#" onclick="reorder_children('<?php echo $family->getXref(); ?>');tabswitch(5);"><i class="icon-media-shuffle"></i> <?php echo WT_I18N::translate('Re-order children'); ?></a>
					<?php } ?>
				</td>
				<td class="facts_value">
					<a href="#" onclick="return add_child_to_family('<?php echo $family->getXref(); ?>');"><?php echo $child_u; ?></a>
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
		global $SHOW_AGE_DIFF, $GEDCOM, $ABBREVIATE_CHART_LABELS, $show_full, $personcount, $controller;

		if (isset($show_full)) $saved_show_full = $show_full; // We always want to see full details here
		$show_full = 1;

		$saved_ABBREVIATE_CHART_LABELS = $ABBREVIATE_CHART_LABELS;
		$ABBREVIATE_CHART_LABELS = false; // Override GEDCOM configuration

		ob_start();
		?>
		<table class="facts_table"><tr><td class="descriptionbox rela">
		<input id="checkbox_elder" type="checkbox" onclick="jQuery('div.elderdate').toggle();" <?php if ($SHOW_AGE_DIFF) echo "checked=\"checked\""; ?>>
		<label for="checkbox_elder"><?php echo WT_I18N::translate('Show date differences'); ?></label>
		</td></tr></table>
		<?php
		$personcount=0;
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

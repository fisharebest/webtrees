<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;

/**
 * Class RelativesTabModule
 */
class RelativesTabModule extends AbstractModule implements ModuleTabInterface {
	/**
	 * How should this module be labelled on tabs, menus, etc.?
	 *
	 * @return string
	 */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Families');
	}

	/**
	 * A sentence describing what this module does.
	 *
	 * @return string
	 */
	public function getDescription() {
		return /* I18N: Description of the “Families” module */ I18N::translate('A tab showing the close relatives of an individual.');
	}

	/**
	 * The user can re-arrange the tab order, but until they do, this
	 * is the order in which tabs are shown.
	 *
	 * @return int
	 */
	public function defaultTabOrder() {
		return 20;
	}

	/**
	 * Display the age difference between marriages and the births of children.
	 *
	 * @param Date $prev
	 * @param Date $next
	 * @param int  $child_number
	 *
	 * @return string
	 */
	private static function ageDifference(Date $prev, Date $next, $child_number = 0) {
		if ($prev->isOK() && $next->isOK()) {
			$days = $next->maximumJulianDay() - $prev->minimumJulianDay();
			if ($days < 0) {
				// Show warning triangle if dates in reverse order
				$diff = '<i class="icon-warning"></i> ';
			} elseif ($child_number > 1 && $days > 1 && $days < 240) {
				// Show warning triangle if children born too close together
				$diff = '<i class="icon-warning"></i> ';
			} else {
				$diff = '';
			}

			$months = round($days * 12 / 365.25); // Approximate - we do not know the calendar
			if (abs($months) == 12 || abs($months) >= 24) {
				$diff .= I18N::plural('%s year', '%s years', round($months / 12), I18N::number(round($months / 12)));
			} elseif ($months != 0) {
				$diff .= I18N::plural('%s month', '%s months', $months, I18N::number($months));
			}

			return '<div class="elderdate age">' . $diff . '</div>';
		} else {
			return '';
		}
	}

	/**
	 * Print a family group.
	 *
	 * @param Family $family
	 * @param string $type
	 * @param string $label
	 */
	private function printFamily(Family $family, $type, $label) {
		global $controller;

		if ($family->getTree()->getPreference('SHOW_PRIVATE_RELATIONSHIPS')) {
			$access_level = Auth::PRIV_HIDE;
		} else {
			$access_level = Auth::accessLevel($family->getTree());
		}

		?>
		<table>
			<tr>
				<td>
					<i class="icon-cfamily"></i>
				</td>
				<td>
					<span class="subheaders"> <?php echo $label; ?></span>
					<a class="noprint" href="<?php echo $family->getHtmlUrl(); ?>"> - <?php echo I18N::translate('View the family'); ?></a>
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
			if ($person instanceof Individual) {
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
						<?php echo Functions::getCloseRelationshipName($controller->record, $person); ?>
					</td>
					<td class="<?php echo $controller->getPersonStyle($person); ?>">
						<?php echo Theme::theme()->individualBoxLarge($person); ?>
					</td>
					</tr>
				<?php
			}
		}
		if (!$found && $family->canEdit()) {
			?>
			<tr>
				<td class="facts_label"></td>
				<td class="facts_value"><a href="#" onclick="return add_spouse_to_family('<?php echo $family->getXref(); ?>', 'HUSB');"><?php echo I18N::translate('Add a husband to this family'); ?></a></td>
			</tr>
			<?php
		}

		///// WIFE /////
		$found = false;
		foreach ($family->getFacts('WIFE', false, $access_level) as $fact) {
			$person = $fact->getTarget();
			if ($person instanceof Individual) {
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
						<?php echo Functions::getCloseRelationshipName($controller->record, $person); ?>
					</td>
					<td class="<?php echo $controller->getPersonStyle($person); ?>">
						<?php echo Theme::theme()->individualBoxLarge($person); ?>
					</td>
				</tr>
				<?php
			}
		}
		if (!$found && $family->canEdit()) {
			?>
			<tr>
				<td class="facts_label"></td>
				<td class="facts_value"><a href="#" onclick="return add_spouse_to_family('<?php echo $family->getXref(); ?>', 'WIFE');"><?php echo I18N::translate('Add a wife to this family'); ?></a></td>
			</tr>
			<?php
		}

		///// MARR /////
		$found = false;
		$prev  = new Date('');
		foreach ($family->getFacts(WT_EVENTS_MARR . '|' . WT_EVENTS_DIV) as $fact) {
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
				</td>
				<td class="facts_value<?php echo $class; ?>">
					<?php echo GedcomTag::getLabelValue($fact->getTag(), $fact->getDate()->display() . ' — ' . $fact->getPlace()->getFullName()); ?>
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
				</td>
				<td class="facts_value">
					<a href="#" onclick="return add_new_record('<?php echo $family->getXref(); ?>', 'MARR');">
						<?php echo I18N::translate('Add marriage details'); ?>
					</a>
				</td>
			</tr>
			<?php
		}

		///// CHIL /////
		$child_number = 0;
		foreach ($family->getFacts('CHIL', false, $access_level) as $fact) {
			$person = $fact->getTarget();
			if ($person instanceof Individual) {
				if ($fact->isPendingAddition()) {
					$child_number++;
					$class = 'facts_label new';
				} elseif ($fact->isPendingDeletion()) {
					$class = 'facts_label old';
				} else {
					$child_number++;
					$class = 'facts_label';
				}
				$next = new Date('');
				foreach ($person->getFacts(WT_EVENTS_BIRT, true) as $bfact) {
					if ($bfact->getDate()->isOK()) {
						$next = $bfact->getDate();
						break;
					}
				}
				?>
				<tr>
					<td class="<?php echo $class; ?>">
						<?php echo self::ageDifference($prev, $next, $child_number); ?>
						<?php echo Functions::getCloseRelationshipName($controller->record, $person); ?>
					</td>
					<td class="<?php echo $controller->getPersonStyle($person); ?>">
						<?php echo Theme::theme()->individualBoxLarge($person); ?>
					</td>
				</tr>
				<?php
				$prev = $next;
			}
		}
		// Re-order children / add a new child
		if ($family->canEdit()) {
			if ($type == 'FAMS') {
				$add_child_text = I18N::translate('Add a son or daughter');
			} else {
				$add_child_text = I18N::translate('Add a brother or sister');
			}
			?>
			<tr class="noprint">
				<td class="facts_label">
					<?php if (count($family->getChildren()) > 1) { ?>
					<a href="#" onclick="reorder_children('<?php echo $family->getXref(); ?>');tabswitch(5);"><i class="icon-media-shuffle"></i> <?php echo I18N::translate('Re-order children'); ?></a>
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

	/** {@inheritdoc} */
	public function getTabContent() {
		global $WT_TREE, $show_full, $controller;

		if (isset($show_full)) {
			$saved_show_full = $show_full;
		}
		// We always want to see full details here
		$show_full = 1;

		ob_start();
		?>
		<table class="facts_table"><tr class="noprint"><td class="descriptionbox rela">
		<input id="checkbox_elder" type="checkbox" onclick="jQuery('div.elderdate').toggle();" <?php echo $WT_TREE->getPreference('SHOW_AGE_DIFF') ? 'checked' : ''; ?>>
		<label for="checkbox_elder"><?php echo I18N::translate('Show date differences'); ?></label>
		</td></tr></table>
		<?php
		$families = $controller->record->getChildFamilies();
		if (!$families && $controller->record->canEdit()) {
			?>
			<table class="facts_table">
				<tr>
					<td class="facts_value"><a href="#" onclick="return add_parent_to_individual('<?php echo $controller->record->getXref(); ?>', 'M');"><?php echo I18N::translate('Add a father'); ?></td>
				</tr>
				<tr>
					<td class="facts_value"><a href="#" onclick="return add_parent_to_individual('<?php echo $controller->record->getXref(); ?>', 'F');"><?php echo I18N::translate('Add a mother'); ?></a></td>
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
			$this->printFamily($family, 'FAMS', $controller->getSpouseFamilyLabel($family, $controller->record));
		}

		// step-children
		foreach ($controller->record->getSpouseStepFamilies() as $family) {
			$this->printFamily($family, 'FAMS', $family->getFullName());
		}

		if (!$WT_TREE->getPreference('SHOW_AGE_DIFF')) {
			echo '<script>jQuery("DIV.elderdate").toggle();</script>';
		}

		if ($controller->record->canEdit()) {
		?>
		<br><table class="facts_table noprint">
		<?php
			if (count($families) > 1) { ?>
			<tr>
				<td class="facts_value">
				<a href="#" onclick="return reorder_families('<?php echo $controller->record->getXref(); ?>');"><?php echo I18N::translate('Re-order families'); ?></a>
				</td>
			</tr>
		<?php } ?>
			<tr>
				<td class="facts_value">
				<a href="#" onclick="return add_famc('<?php echo $controller->record->getXref(); ?>');"><?php echo I18N::translate('Link this individual to an existing family as a child'); ?></a>
				</td>
			</tr>
			<?php if ($controller->record->getSex() != "F") { ?>
			<tr>
				<td class="facts_value">
				<a href="#" onclick="return add_spouse_to_individual('<?php echo $controller->record->getXref(); ?>','WIFE');"><?php echo I18N::translate('Add a wife'); ?></a>
				</td>
			</tr>
			<tr>
				<td class="facts_value">
				<a href="#" onclick="return linkspouse('<?php echo $controller->record->getXref(); ?>','WIFE');"><?php echo I18N::translate('Add a wife using an existing individual'); ?></a>
				</td>
			</tr>
			<?php }
			if ($controller->record->getSex() != "M") { ?>
			<tr>
				<td class="facts_value">
				<a href="#" onclick="return add_spouse_to_individual('<?php echo $controller->record->getXref(); ?>','HUSB');"><?php echo I18N::translate('Add a husband'); ?></a>
				</td>
			</tr>
			<tr>
				<td class="facts_value">
				<a href="#" onclick="return linkspouse('<?php echo $controller->record->getXref(); ?>','HUSB');"><?php echo I18N::translate('Add a husband using an existing individual'); ?></a>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td class="facts_value">
				<a href="#" onclick="return add_child_to_individual('<?php echo $controller->record->getXref(); ?>','U');"><?php echo I18N::translate('Add a child to create a one-parent family'); ?></a>
				</td>
			</tr>
		</table>
		<?php } ?>
		<br>
		<?php

		unset($show_full);
		if (isset($saved_show_full)) {
			$show_full = $saved_show_full;
		}

		return '<div id="' . $this->getName() . '_content">' . ob_get_clean() . '</div>';
	}

	/** {@inheritdoc} */
	public function hasTabContent() {
		return true;
	}
	/** {@inheritdoc} */
	public function isGrayedOut() {
		return false;
	}
	/** {@inheritdoc} */
	public function canLoadAjax() {
		return !Auth::isSearchEngine(); // Search engines cannot use AJAX
	}

	/** {@inheritdoc} */
	public function getPreLoadContent() {
		return '';
	}
}

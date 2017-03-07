<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
					<span class="subheaders"> <?= $label ?></span>
					<a href="<?= $family->getHtmlUrl() ?>"> - <?= I18N::translate('View this family') ?></a>
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
					<td class="<?= $class ?>">
						<?= Functions::getCloseRelationshipName($controller->record, $person) ?>
					</td>
					<td class="<?= $controller->getPersonStyle($person) ?>">
						<?= Theme::theme()->individualBoxLarge($person) ?>
					</td>
					</tr>
				<?php
			}
		}
		if (!$found && $family->canEdit()) {
			?>
			<tr>
				<td class="facts_label"></td>
				<td class="facts_value">
					<a href="edit_interface.php?action=add_spouse_to_family&amp;ged=<?= $family->getTree()->getNameHtml() ?>&amp;xref=<?= $family->getXref() ?>&amp;famtag=HUSB">
						<?= I18N::translate('Add a husband to this family') ?>
					</a>
					</td>
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
					<td class="<?= $class ?>">
						<?= Functions::getCloseRelationshipName($controller->record, $person) ?>
					</td>
					<td class="<?= $controller->getPersonStyle($person) ?>">
						<?= Theme::theme()->individualBoxLarge($person) ?>
					</td>
				</tr>
				<?php
			}
		}
		if (!$found && $family->canEdit()) {
			?>
			<tr>
				<td class="facts_label"></td>
				<td class="facts_value">
					<a href="edit_interface.php?action=add_spouse_to_family&amp;ged=<?= $family->getTree()->getNameHtml() ?>&amp;xref=<?= $family->getXref() ?>&amp;famtag=WIFE">
						<?= I18N::translate('Add a wife to this family') ?>
					</a>
				</td>
			</tr>
			<?php
		}

		///// MARR /////
		$found = false;
		$prev  = new Date('');
		foreach ($family->getFacts(WT_EVENTS_MARR . '|' . WT_EVENTS_DIV, true) as $fact) {
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
				<td class="facts_value<?= $class ?>">
					<?= GedcomTag::getLabelValue($fact->getTag(), $fact->getDate()->display() . ' — ' . $fact->getPlace()->getFullName()) ?>
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
					<a href="edit_interface.php?action=add&amp;ged=<?= $family->getTree()->getNameHtml() ?>&amp;xref=<?= $family->getXref() ?>&amp;fact=MARR">
						<?= I18N::translate('Add marriage details') ?>
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
					<td class="<?= $class ?>">
						<?= self::ageDifference($prev, $next, $child_number) ?>
						<?= Functions::getCloseRelationshipName($controller->record, $person) ?>
					</td>
					<td class="<?= $controller->getPersonStyle($person) ?>">
						<?= Theme::theme()->individualBoxLarge($person) ?>
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
			<tr>
				<td class="facts_label">
					<?php if (count($family->getChildren()) > 1): ?>
					<a href="edit_interface.php?action=reorder_children&amp;ged=<?= $family->getTree()->getNameHtml() ?>&amp;xref=<?= $family->getXref() ?>">
						<i class="icon-media-shuffle"></i> <?= I18N::translate('Re-order children') ?>
					</a>
					<?php endif; ?>
				</td>
				<td class="facts_value">
					<a href="edit_interface.php?action=add_child_to_family&amp;ged=<?= $family->getTree()->getNameHtml() ?>&amp;xref=<?= $family->getXref() ?>&amp;gender=U">
						<?= $add_child_text ?>
					</a>
					<span style='white-space:nowrap;'>
						<a href="edit_interface.php?action=add_child_to_family&amp;ged=<?= $family->getTree()->getNameHtml() ?>&amp;xref=<?= $family->getXref() ?>&amp;gender=M" class="icon-sex_m_15x15"></a>
						<a href="edit_interface.php?action=add_child_to_family&amp;ged=<?= $family->getTree()->getNameHtml() ?>&amp;xref=<?= $family->getXref() ?>&amp;gender=F" class="icon-sex_f_15x15"></a>
					</span>
				</td>
			</tr>
			<?php
		}

		echo '</table>';
	}

	/** {@inheritdoc} */
	public function getTabContent() {
		global $controller;

		ob_start();
		?>
		<table class="facts_table">
			<tr>
				<td class="descriptionbox rela">
					<label>
						<input id="show-date-differences" type="checkbox" checked>
						<?= I18N::translate('Date differences') ?>
					</label>
				</td>
			</tr>
		</table>
		<?php
		$families = $controller->record->getChildFamilies();
		if (!$families && $controller->record->canEdit()) {
			?>
			<table class="facts_table">
				<tr>
					<td class="facts_value">
						<a href="edit_interface.php?action=add_parent_to_individual&amp;ged=<?= $controller->record->getTree()->getNameHtml() ?>&amp;xref=<?= $controller->record->getXref() ?>&amp;gender=M">
							<?= I18N::translate('Add a father') ?>
						</a>
					</td>
				</tr>
				<tr>
					<td class="facts_value">
						<a href="edit_interface.php?action=add_parent_to_individual&amp;ged=<?= $controller->record->getTree()->getNameHtml() ?>&amp;xref=<?= $controller->record->getXref() ?>&amp;gender=F">
							<?= I18N::translate('Add a mother') ?>
						</a>
					</td>
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

		if ($controller->record->canEdit()) {
		?>
		<br><table class="facts_table">
		<?php
			if (count($families) > 1) { ?>
			<tr>
				<td class="facts_value">
					<a href="edit_interface.php?action=reorder_fams&amp;ged=<?= $controller->record->getTree()->getNameHtml() ?>&amp;xref=<?= $controller->record->getXref() ?>">
					<?= I18N::translate('Re-order families') ?>
					</a>
				</td>
			</tr>
		<?php } ?>
			<tr>
				<td class="facts_value">
				<a href="edit_interface.php?action=addfamlink&amp;ged=<?= $controller->record->getTree()->getNameHtml() ?>&amp;xref=<?= $controller->record->getXref() ?>"><?= I18N::translate('Link this individual to an existing family as a child') ?></a>
				</td>
			</tr>
			<?php if ($controller->record->getSex() !== 'F') { ?>
			<tr>
				<td class="facts_value">
				<a href="edit_interface.php?action=add_spouse_to_individual&amp;ged=<?= $controller->record->getTree()->getNameHtml() ?>&amp;xref=<?= $controller->record->getXref() ?>&amp;sex=F"><?= I18N::translate('Add a wife') ?></a>
				</td>
			</tr>
			<tr>
				<td class="facts_value">
				<a href="edit_interface.php?action=linkspouse&amp;ged=<?= $controller->record->getTree()->getNameHtml() ?>&amp;xref=<?= $controller->record->getXref() ?>&amp;famtag=WIFE"><?= I18N::translate('Add a wife using an existing individual') ?></a>
				</td>
			</tr>
			<?php }
			if ($controller->record->getSex() !== 'M') { ?>
			<tr>
				<td class="facts_value">
				<a href="edit_interface.php?action=add_spouse_to_individual&amp;ged=<?= $controller->record->getTree()->getNameHtml() ?>&amp;xref=<?= $controller->record->getXref() ?>&amp;sex=M"><?= I18N::translate('Add a husband') ?></a>
				</td>
			</tr>
			<tr>
				<td class="facts_value">
				<a href="edit_interface.php?action=linkspouse&amp;ged=<?= $controller->record->getTree()->getNameHtml() ?>&amp;xref=<?= $controller->record->getXref() ?>&amp;famtag=HUSB"><?= I18N::translate('Add a husband using an existing individual') ?></a>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td class="facts_value">
					<a href="edit_interface.php?action=add_child_to_individual&amp;ged=<?= $controller->record->getTree()->getNameHtml() ?>&amp;xref=<?= $controller->record->getXref() ?>&amp;gender=U">
						<?= I18N::translate('Add a child to create a one-parent family') ?>
					</a>
				</td>
			</tr>
		</table>
		<?php } ?>
		<br>
		<script>
			//persistent_toggle("show-date-differences", ".elderdate");
		</script>
		<?php

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
		return false;
	}

	/** {@inheritdoc} */
	public function getPreLoadContent() {
		return '';
	}
}

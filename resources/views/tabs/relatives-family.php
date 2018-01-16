<?php use Fisharebest\Webtrees\Date; ?>
<?php use Fisharebest\Webtrees\Functions\Functions; ?>
<?php use Fisharebest\Webtrees\GedcomTag; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Individual; ?>
<?php use Fisharebest\Webtrees\Theme; ?>

<table class="table table-sm wt-facts-table">
	<caption>
		<i class="icon-cfamily"></i>
		<a href="<?= e($family->url()) ?>"><?= $label ?></a>
	</caption>

	<tbody>
		<?php
		$found = false;
		foreach ($family->getFacts('HUSB', false, $fam_access_level) as $fact) {
			$found |= !$fact->isPendingDeletion();
			$person = $fact->getTarget();
			if ($person instanceof Individual) {
				$row_class = 'wt-gender-' . $person->getSex();
				if ($fact->isPendingAddition()) {
					$row_class .= ' new';
				} elseif ($fact->isPendingDeletion()) {
					$row_class .= ' old';
				}
				?>
				<tr class="<?= $row_class ?>">
					<th scope="row">
						<?= $individual === $person ? '<i class="icon-selected"></i>' : '' ?>
						<?= Functions::getCloseRelationshipName($individual, $person) ?>
					</th>
					<td class="border-0 p-0">
						<?= Theme::theme()->individualBoxLarge($person) ?>
					</td>
				</tr>
				<?php
			}
		}
		if (!$found && $family->canEdit()) {
			?>
			<tr>
				<th scope="row"></th>
				<td>
					<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add_spouse_to_family', 'ged' => $family->getTree()->getName(), 'xref' => $family->getXref(), 'famtag' => 'HUSB'])) ?>">
						<?= I18N::translate('Add a husband to this family') ?>
					</a>
				</td>
			</tr>
			<?php
		}

		$found = false;
		foreach ($family->getFacts('WIFE', false, $fam_access_level) as $fact) {
			$person = $fact->getTarget();
			if ($person instanceof Individual) {
				$found |= !$fact->isPendingDeletion();
				$row_class = 'wt-gender-' . $person->getSex();
				if ($fact->isPendingAddition()) {
					$row_class .= ' new';
				} elseif ($fact->isPendingDeletion()) {
					$row_class .= ' old';
				}
				?>

				<tr class="<?= $row_class ?>">
					<th scope="row">
						<?= $individual === $person ? '<i class="icon-selected"></i>' : '' ?>
						<?= Functions::getCloseRelationshipName($individual, $person) ?>
					</th>
					<td class="border-0 p-0">
						<?= Theme::theme()->individualBoxLarge($person) ?>
					</td>
				</tr>
				<?php
			}
		}
		if (!$found && $family->canEdit()) { ?>
			<tr>
				<th scope="row"></th>
				<td>
					<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add_spouse_to_family', 'ged' => $family->getTree()->getName(), 'xref' => $family->getXref(), 'famtag' => 'WIFE'])) ?>">
						<?= I18N::translate('Add a wife to this family') ?>
					</a>
				</td>
			</tr>

			<?php } ?>

		<?php
		///// MARR /////
		$found = false;
		$prev  = new Date('');
		foreach ($family->getFacts(WT_EVENTS_MARR . '|' . WT_EVENTS_DIV, true) as $fact) {
			$found |= !$fact->isPendingDeletion();
			if ($fact->isPendingAddition()) {
				$row_class = 'new';
			} elseif ($fact->isPendingDeletion()) {
				$row_class = 'old';
			} else {
				$row_class = '';
			}
			?>

			<tr class="<?= $row_class ?>">
				<th scope="row">
				</th>
				<td>
					<?= GedcomTag::getLabelValue($fact->getTag(), $fact->getDate()->display() . ' — ' . $fact->getPlace()->getFullName()) ?>
				</td>
			</tr>

			<?php
			if (!$prev->isOK() && $fact->getDate()->isOK()) {
				$prev = $fact->getDate();
			}
		}

		if (!$found && $family->canShow() && $family->canEdit()) {
			?>
			<tr>
				<th scope="row">
				</th>
				<td>
					<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add', 'ged' => $family->getTree()->getName(), 'xref' => $family->getXref(), 'fact' => 'MARR'])) ?>">
						<?= I18N::translate('Add marriage details') ?>
					</a>
				</td>
			</tr>
			<?php
		}

		///// CHIL /////
		$child_number = 0;
		foreach ($family->getFacts('CHIL', false, $fam_access_level) as $fact) {
			$person = $fact->getTarget();
			if ($person instanceof Individual) {
				$row_class = 'wt-gender-' . $person->getSex();
				if ($fact->isPendingAddition()) {
					$child_number++;
					$row_class .= ' new';
				} elseif ($fact->isPendingDeletion()) {
					$row_class .= ' old';
				} else {
					$child_number++;
				}
				$next = new Date('');
				foreach ($person->getFacts(WT_EVENTS_BIRT, true) as $bfact) {
					if ($bfact->getDate()->isOK()) {
						$next = $bfact->getDate();
						break;
					}
				}
				?>

				<tr class="<?= $row_class ?>">
					<th scope="row">
						<?php if ($individual === $person): ?>
							<i class="icon-selected"></i>
						<?php endif ?>

						<?php if ($prev->isOK() && $next->isOK()): ?>
							<div class="elderdate age">
								<?php $days = $next->maximumJulianDay() - $prev->minimumJulianDay(); ?>
								<?php if ($days < 0 || $child_number > 1 && $days > 1 && $days < 240): ?>
									<i class="icon-warning"></i>
								<?php endif ?>

								<?php $months = round($days / 30); ?>
								<?php if (abs($months) === 12 || abs($months) >= 24): ?>
									<?= I18N::plural('%s year', '%s years', round($months / 12), I18N::number(round($months / 12))) ?>
								<?php elseif ($months !== 0): ?>
									<?= I18N::plural('%s month', '%s months', $months, I18N::number($months)) ?>
								<?php endif ?>
							</div>
						<?php endif ?>

						<?= Functions::getCloseRelationshipName($individual, $person) ?>
					</th>
					<td class="border-0 p-0">
						<?= Theme::theme()->individualBoxLarge($person) ?>
					</td>
				</tr>
				<?php
				$prev = $next;
			}
		} ?>

		<?php if ($family->canEdit()): ?>
			<tr>
				<th scope="row">
					<?php if (count($family->getChildren()) > 1): ?>
						<a href="<?= e(Html::url('edit_interface.php', ['action' => 'reorder-children', 'ged' => $family->getTree()->getName(), 'xref' => $family->getXref()])) ?>">
							<i class="icon-media-shuffle"></i> <?= I18N::translate('Re-order children') ?>
						</a>
					<?php endif; ?>
				</th>
				<td>
					<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add_child_to_family', 'ged' => $family->getTree()->getName(), 'xref' => $family->getXref(), 'gender' => 'U'])) ?>">
						<?php if ($type == 'FAMS'): ?>
							<?= I18N::translate('Add a son or daughter') ?>
						<?php else: ?>
							<?= I18N::translate('Add a brother or sister') ?>
						<?php endif ?>
					</a>

					<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add_child_to_family', 'ged' => $family->getTree()->getName(), 'xref' => $family->getXref(), 'gender' => 'M'])) ?>" class="icon-sex_m_15x15"></a>

					<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add_child_to_family', 'ged' => $family->getTree()->getName(), 'xref' => $family->getXref(), 'gender' => 'F'])) ?>" class="icon-sex_f_15x15"></a>
				</td>
			</tr>
		<?php endif ?>
	</tbody>
</table>

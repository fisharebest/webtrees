<?php use Fisharebest\Webtrees\Functions\Functions; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<table class="table table-sm wt-facts-table wt-family-navigator-family">
	<caption class="text-center wt-family-navigator-family-heading">
		<a href="<?= e($family->url()) ?>">
			<?= $title ?>
		</a>
	</caption>
	<tbody>
		<?php foreach ($family->getSpouses() as $spouse): ?>
			<tr class="text-center wt-family-navigator-parent wt-gender-<?= $spouse->getSex() ?>">
				<th class="text-nowrap align-middle wt-family-navigator-label" scope="row">
					<?php if ($spouse === $individual): ?>
						<?= Functions::getCloseRelationshipName($individual, $spouse) ?>
						<i class="icon-selected"></i>
					<?php elseif ($spouse->getPrimaryChildFamily() !== null): ?>
					<div class="dropdown">
						<a class="dropdown-toggle" href="#" role="button" id="dropdown-<?= e($spouse->getXref()) ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<?= Functions::getCloseRelationshipName($individual, $spouse) ?>
						</a>

						<div class="dropdown-menu wt-family-navigator-dropdown">
							<div class="dropdown-header wt-family-navigator-dropdown-heading">
								<?= I18N::translate('Parents') ?>
							</div>
							<?php foreach ($spouse->getPrimaryChildFamily()->getSpouses() as $parent): ?>
								<a class="dropdown-item" href="<?= e($parent->url()) ?>">
									<?= $parent->getFullName() ?>
								</a>
							<?php endforeach ?>
						</div>
					</div>
					<?php else: ?>
						<?= Functions::getCloseRelationshipName($individual, $spouse) ?>
					<?php endif ?>
				</th>

				<td class="wt-family-navigator-name">
					<?php if ($spouse->canShow()): ?>
						<a href="<?= e($spouse->url()) ?>">
							<?= $spouse->getFullName() ?>
						</a>
						<div class="small">
							<?= $spouse->getLifeSpan() ?>
						</div>
					<?php else: ?>
						<?= $spouse->getFullName() ?>
					<?php endif ?>
				</td>
			</tr>
		<?php endforeach ?>

		<?php foreach ($family->getChildren() as $child): ?>
			<tr class="text-center wt-family-navigator-child wt-gender-<?= $child->getSex() ?>">
				<th class="text-nowrap align-middle" scope="row">
					<?php if ($child === $individual): ?>
						<?= Functions::getCloseRelationshipName($individual, $child) ?>
						<i class="icon-selected"></i>
					<?php elseif ($child->getSpouseFamilies() !== []): ?>
						<div class="dropdown">
							<a class="dropdown-toggle" href="#" role="button" id="dropdown-<?= e($child->getXref()) ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<?= Functions::getCloseRelationshipName($individual, $child) ?>
							</a>

							<div class="dropdown-menu">
								<?php foreach ($child->getSpouseFamilies() as $n => $in_laws): ?>
									<?php if ($n > 0) : ?>
										<div class="dropdown-divider"></div>
									<?php endif ?>
									<div class="dropdown-header wt-family-navigator-dropdown-heading">
										<?= I18N::translate('Family') ?>
									</div>

									<?php foreach ($in_laws->getSpouses() as $sibling_in_law): ?>
										<?php if ($sibling_in_law !== $child): ?>
											<a class="dropdown-item" href="<?= e($sibling_in_law->url()) ?>">
												<?= $sibling_in_law->getFullName() ?>
											</a>
										<?php endif ?>
									<?php endforeach ?>

								<ul>
									<?php foreach ($in_laws->getChildren() as $child_in_law): ?>
									<li>
										<a class="dropdown-item" href="<?= e($child_in_law->url()) ?>">
											<?= $child_in_law->getFullName() ?>
										</a>
									</li>
									<?php endforeach ?>
								</ul>
								<?php endforeach ?>
							</div>
						</div>
					<?php else: ?>
						<?= Functions::getCloseRelationshipName($individual, $child) ?>
					<?php endif ?>
				</th>

				<td>
					<?php if ($child->canShow()): ?>
						<a  href="<?= e($child->url()) ?>">
							<?= $child->getFullName() ?>
						</a>
						<div class="small">
							<?= $child->getLifeSpan() ?>
						</div>
					<?php else: ?>
						<?= $child->getFullName() ?>
					<?php endif ?>
				</td>
			</tr>
			<?php endforeach ?>
	</tbody>
</table>

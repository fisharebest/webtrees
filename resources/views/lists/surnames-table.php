<?php use Fisharebest\Webtrees\I18N; ?>

<table class="table table-bordered table-sm datatables wt-table-surname" data-info="false" data-paging="false" data-searching="false" data-state-save="true" data-order="<?= e(json_encode($order ?? [[1, 'desc']])) ?>">
	<caption class="sr-only">
		<?= I18N::translate('Surnames') ?>
	</caption>
	<thead>
		<tr>
			<th>
				<?= I18N::translate('Surname') ?>
			</th>
			<th>
				<?php if ($route == 'family-list'):?>
					<?= I18N::translate('Spouses') ?>
				<?php else: ?>
					<?= I18N::translate('Individuals') ?>
				<?php endif ?>
			</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($surnames as $surn => $surns): ?>
			<tr>
				<td data-sort="<?= e($surn) ?>">
					<!-- Multiple surname variants, e.g. von Groot, van Groot, van der Groot, etc. -->
					<?php foreach ($surns as $spfxsurn => $indis): ?>
						<?php if ($spfxsurn): ?>
							<?php if ($surn !== ''): ?>
								<a href="<?= route($route, ['surname' => $surn, 'ged' => $tree->getName()]) ?>" dir="auto">
									<?= e($spfxsurn) ?>
								</a>
							<?php else: ?>
								<a href="<?= route($route, ['alpha' => ',', 'ged' => $tree->getName()]) ?>" dir="auto">
									<?= e($spfxsurn) ?>
								</a>
							<?php endif ?>
						<?php else: ?>
							<!-- No surname, but a value from "2 SURN"? A common workaround for toponyms, etc. -->
							<a href="<?= route($route, ['surname' => $surn, 'ged' => $tree->getName()]) ?>" dir="auto"><?= e($surn) ?></a>
						<?php endif ?>
						<br>
					<?php endforeach ?>
				</td>

				<td class="text-center" data-sort="<?= array_sum(array_map(function(array $x) { return count($x); }, $surns)) ?>">
					<?php foreach ($surns as $indis): ?>
						<?= I18N::number(count($indis)) ?>
						<br>
					<?php endforeach ?>

					<?php if (count($surns) > 1): ?>
						<?= I18N::number(array_sum(array_map(function(array $x) { return count($x); }, $surns))) ?>
					<?php endif ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>

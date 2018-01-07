<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsCharts; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Theme; ?>

<h2 class="wt-page-title">
	<?= $family->getFullName() ?>
</h2>

<table id="family-table" class="w-100" role="presentation">
	<tr style="vertical-align:top;">
		<td style="width: <?= Theme::theme()->parameter('chart-box-x') + 30 ?>px;">
			<?php FunctionsCharts::printFamilyChildren($family) ?>
		</td>
		<td>
			<table class="w-100" role="presentation">
				<tr>
					<td class="subheaders"><?= I18N::translate('Parents') ?></td>
					<td class="subheaders"><?= I18N::translate('Grandparents') ?></td>
				</tr>
				<tr>
					<td colspan="2">
						<?php FunctionsCharts::printFamilyParents($family) ?>
						<?php if (Auth::isEditor($family->getTree())): ?>
							<?php if ($family->getHusband() === null): ?>
								<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add_spouse_to_family', 'ged=' => $family->getTree()->getName(), 'xref' => $family->getXref(), 'famtag' => 'HUSB'])) ?>>
									<?= I18N::translate('Add a father') ?>
								</a>
								<br>
							<?php endif ?>
							<?php if ($family->getWife() === null): ?>
								<a href="<?= e(Html::url('edit_interface.php', ['action' => 'add_spouse_to_family', 'ged=' => $family->getTree()->getName(), 'xref' => $family->getXref(), 'famtag' => 'WIFE'])) ?>>
									<?= I18N::translate('Add a mother') ?>
								</a>
								<br>
							<?php endif ?>
						<?php endif ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<span class="subheaders"><?= I18N::translate('Family group information') ?></span>
						<?php if ($family->canShow()): ?>
							<table class="table wt-facts-table">
								<?= $facts ?>
							</table>
						<?php else: ?>
							<p>
								<?= I18N::translate('The details of this family are private.') ?>
							</p>
						<?php endif ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?= view('modals/ajax') ?>

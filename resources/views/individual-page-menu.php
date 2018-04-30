<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="dropdown wt-page-menu">
	<button class="btn btn-primary dropdown-toggle wt-page-menu-button" type="button" id="page-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<?= FontAwesome::decorativeIcon('edit') ?>
		<?= I18N::translate('edit') ?>
	</button>
	<div class="dropdown-menu dropdown-menu-right wt-page-menu-items" aria-labelledby="page-menu">
		<?php if ($count_sex === 0): ?>
			<a class="dropdown-item menu-indi-editraw" href="<?= e(Html::url('edit_interface.php', ['action' => 'add', 'fact' => 'SEX', 'ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref()])) ?>">
				<?= I18N::translate('Edit the gender') ?>
			</a>

		<?php endif ?>

		<a class="dropdown-item menu-indi-editraw" href="<?= e(Html::url('edit_interface.php', ['action' => 'addname', 'ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref()])) ?>">
			<?= I18N::translate('Add a name') ?>
		</a>

		<?php if ($count_names > 1): ?>
			<a class="dropdown-item menu-indi-editraw" href="<?= e(route('reorder-names', ['ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref()])) ?>">
				<?= I18N::translate('Re-order names') ?>
			</a>
		<?php endif ?>

		<?php if (empty($individual->getFacts('SEX'))): ?>
			<a class="dropdown-item menu-indi-editraw" href="<?= e(Html::url('edit_interface.php', ['action' => 'add', 'fact' => 'SEX', 'ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref()])) ?>">
				<?= I18N::translate('Edit the gender') ?>
			</a>
		<?php endif ?>


		<a class="dropdown-item menu-indi-del" href="#" onclick="return delete_record('<?= I18N::translate('Are you sure you want to delete “%s”?', strip_tags($individual->getFullName())) ?>', '<?= e($individual->getXref()) ?>', '<?= e($individual->getTree()->getName()) ?>');">
			<?= I18N::translate('Delete') ?>
		</a>

		<?php if (Auth::isAdmin() || $individual->getTree()->getPreference('SHOW_GEDCOM_RECORD')): ?>
			<div class="dropdown-divider"></div>

			<a class="dropdown-item menu-indi-editraw" href="<?= e(route('edit-raw-record', ['ged' => $individual->getTree()->getName(), 'xref' => $individual->getXref()])) ?>">
				<?= I18N::translate('Edit the raw GEDCOM') ?>
			</a>
			<?php endif ?>
	</div>
</div>

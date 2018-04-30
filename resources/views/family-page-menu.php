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
		<a class="dropdown-item menu-fam-change" href="<?= e(Html::url('edit_interface.php', ['action' => 'changefamily', 'ged' => $record->getTree()->getName(), 'xref' => $record->getXref(), 'gender' => 'U'])) ?>">
			<?= I18N::translate('Change family members') ?>
		</a>

		<a class="dropdown-item menu-fam-addchil" href="<?= e(Html::url('edit_interface.php', ['action' => 'add_child_to_family', 'ged' => $record->getTree()->getName(), 'xref' => $record->getXref()])) ?>">
			<?= I18N::translate('Add a child to this family') ?>
		</a>

		<?php if ($record->getNumberOfChildren() > 1): ?>
			<a class="dropdown-item menu-fam-orderchil" href="<?= e(route('reorder-children', ['ged' => $record->getTree()->getName(), 'xref' => $record->getXref()])) ?>">
				<?= I18N::translate('Re-order children') ?>
			</a>
		<?php endif ?>

		<div class="dropdown-divider"></div>

		<a class="dropdown-item menu-fam-del" href="#" onclick="return delete_record('<?= I18N::translate('Deleting the family will unlink all of the individuals from each other but will leave the individuals in place. Are you sure you want to delete this family?') ?>', '<?= e($record->getXref()) ?>', '<?= e($record->getXref()) ?>', '<?= e($record->getTree()->getName()) ?>');">
			<?= I18N::translate('Delete') ?>
		</a>

		<?php if (Auth::isAdmin() || $record->getTree()->getPreference('SHOW_GEDCOM_RECORD')): ?>
			<div class="dropdown-divider"></div>

			<a class="dropdown-item menu-fam-editraw" href="<?= e(route('edit-raw-record', ['ged' => $record->getTree()->getName(), 'xref' => $record->getXref()])) ?>">
				<?= I18N::translate('Edit the raw GEDCOM') ?>
			</a>
			<?php endif ?>
	</div>
</div>

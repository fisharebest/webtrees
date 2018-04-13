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
		<a class="dropdown-item menu-indi-del" href="#" onclick="return delete_record('<?= I18N::translate('Are you sure you want to delete “%s”?', strip_tags($record->getFullName())) ?>', '<?= e($record->getXref()) ?>', '<?= e($record->getTree()->getName()) ?>');">
			<?= I18N::translate('Delete') ?>
		</a>

		<?php if (Auth::isAdmin() || $record->getTree()->getPreference('SHOW_GEDCOM_RECORD')): ?>
			<div class="dropdown-divider"></div>

			<a class="dropdown-item menu-indi-editraw" href="<?= e(route('edit-raw-record', ['ged' => $record->getTree()->getName(), 'xref' => $record->getXref()])) ?>">
				<?= I18N::translate('Edit the raw GEDCOM') ?>
			</a>
			<?php endif ?>
	</div>
</div>

<?php if ($record->canEdit()): ?>
<?php endif ?>


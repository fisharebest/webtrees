<?php use Fisharebest\Webtrees\Auth; ?>
<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Module; ?>

<div class="dropdown wt-page-menu">
	<button class="btn btn-primary dropdown-toggle wt-page-menu-button" type="button" id="page-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<?= FontAwesome::decorativeIcon('edit') ?>
		<?= I18N::translate('edit') ?>
	</button>
	<div class="dropdown-menu dropdown-menu-right wt-page-menu-items" aria-labelledby="page-menu">

		<?php if (Module::getModuleByName('GEDFact_assistant')): ?>
			<a class="dropdown-item menu-obje-link" href="#" onclick="return ilinkitem('<?= e($record->getXref()) ?>','manage','<?= e($record->getTree()->getName()) ?>');">
				<?= I18N::translate('Manage the links') ?>
			</a>
		<?php else: ?>
			<a class=dropdown-item menu-obje-link-indi" href="#" onclick="return ilinkitem('<?= e($record->getXref()) ?>','person','<?= e($record->getTree()->getName()) ?>');">
				<?= I18N::translate('Link this media object to an individual') ?>
			</a>
			<a class="dropdown-item menu-obje-link-fam" href="#" onclick="return ilinkitem('<?= e($record->getXref()) ?>','family','<?= e($record->getTree()->getName()) ?>');">
				<?= I18N::translate('Link this media object to a family') ?>
			</a>
			<a class="dropdown-item menu-obje-link-sour" href="#" onclick="return ilinkitem('<?= e($record->getXref()) ?>','source','<?= e($record->getTree()->getName()) ?>');">
				<?= I18N::translate('Link this media object to a source') ?>
			</a>
		<?php endif ?>

		<a class="dropdown-item menu-obje-del" href="#" onclick="return delete_record('<?= I18N::translate('Are you sure you want to delete “%s”?', strip_tags($record->getFullName())) ?>', '<?= e($record->getXref()) ?>', '<?= e($record->getTree()->getName()) ?>');">
			<?= I18N::translate('Delete') ?>
		</a>

		<?php if (Auth::isAdmin() || $record->getTree()->getPreference('SHOW_GEDCOM_RECORD')): ?>
			<div class="dropdown-divider"></div>

			<a class="dropdown-item menu-obje-editraw" href="<?= e(route('edit-raw-record', ['ged' => $record->getTree()->getName(), 'xref' => $record->getXref()])) ?>">
				<?= I18N::translate('Edit the raw GEDCOM') ?>
			</a>
			<?php endif ?>
	</div>
</div>

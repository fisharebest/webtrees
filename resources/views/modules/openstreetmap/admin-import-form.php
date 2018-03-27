<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= view('admin/breadcrumbs', ['links' => $breadcrumbs]) ?>

<h3><?= $title ?></h3>
<form id="upload_form" method="post" enctype="multipart/form-data" action="<?=
    e(route('admin-module',[
        'module' => $module,
        'action' => 'AdminImport',
    ])) ?>">
	<?= csrf_field() ?>
	<input type="hidden" name="parent_id" value="<?= $parent_id ?>">
	<input type="hidden" name="inactive" value="<?= $inactive ?>">
	<input type="hidden" name="MAX_FILE_SIZE" value="131072">

	<!-- Server file -->
	<div class="row form-group">
		<label class="col-form-label col-sm-4" for="serverfile">
			<?= I18N::translate('A file on the server') ?>
		</label>
		<div class="col-sm-8">
			<div class="input-group" dir="ltr">
				<div class="input-group-prepend">
							<span class="input-group-text">
								<?= WT_MODULES_DIR . $module . '/extra/' ?>
							</span>
				</div>
				<select id="serverfile" name="serverfile" class="form-control">
                    <option class='custom-select' selected disabled value=''><?= I18N::translate('choose a file&hellip;') ?></option>
                    <?php foreach($files as $file): ?>
                        <option value="<?= e($file) ?>"><?= $file ?></option>
                    <?php endforeach ?>
				</select>
			</div>
		</div>
	</div>

	<!-- local file -->
	<div class="row form-group">
		<label class="col-form-label col-sm-4" for="localfile">
			<?= I18N::translate('A file on your computer') ?>
		</label>
		<div class="col-sm-8">
			<input id="localfile" type="file" name="localfile" class="form-control-file">
		</div>
	</div>

	<!-- CLEAR DATABASE -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-4">
				<?= I18N::translate('Delete all existing geographic data before importing the file.') ?>
			</legend>
			<div class="col-sm-8">
				<?= Bootstrap4::radioButtons(
					'cleardatabase',
					[I18N::translate('no'), I18N::translate('yes')],
					'0',
					true
				) ?>
			</div>
		</div>
	</fieldset>

	<!-- Import options -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-4" for="import-options">
				<?= I18N::translate('Import Options.') ?>
			</legend>
			<div class="col-sm-8">
				<?= Bootstrap4::select(
					[
						'addupdate' => I18N::translate('Add new, and update existing records'),
						'add'	   => I18N::translate('Only add new records'),
						'update'	=> I18N::translate('Only update existing records'),
					],
					'0',
					['id' => 'import-options', 'name' => 'import-options']
				) ?>
			</div>
		</div>
	</fieldset>

	<!-- SAVE BUTTON -->
	<div class="row form-group">
		<div class="offset-sm-4 col-sm-8">
			<button type="submit" class="btn btn-primary">
				<i class="fas fa-check"></i>
				<?= I18N::translate('continue') ?>
			</button>
		</div>
	</div>
</form>

<?php View::push('javascript') ?>
	<script>
		$('#upload_form').on('submit', function(e) {
			let self = this;
			e.preventDefault();
			if($('input[name="cleardatabase"]:checked').val() === '1') {
				if (!confirm('<?= I18N::translate('Really delete all geographic data?') ?> ')) {
					return false;
				}
			}
			self.submit();
		});
	</script>
<?php View::endpush() ?>

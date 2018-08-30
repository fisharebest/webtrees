<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('components/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-media') => I18N::translate('Manage media'), $title]]) ?>

<h1><?= $title ?></h1>

<p>
	<?= I18N::translate('Upload one or more media files from your local computer. Media files can be pictures, video, audio, or other formats.') ?>
	<?= I18N::translate('Maximum upload size: ') ?><?= e($filesize) ?>
</p>

<form name="uploadmedia" enctype="multipart/form-data" method="post">
	<?= csrf_field() ?>

	<?php for ($i = 1; $i <= $max_upload_files; $i++): ?>
		<h2><?= I18N::translate('Media file') ?> <?= I18N::number($i) ?></h2>

		<div class="form-group row">
			<label class="form-control-label col-sm-3" for="mediafile<?= e($i) ?>">
				<?= I18N::translate('Media file to upload') ?>
				<span class="sr-only"><?= e($i) ?></span>
			</label>
			<div class="col-sm-9">
				<input class="form-control-file" id="mediafile<?= e($i) ?>" name="mediafile<?= e($i) ?>" type="file">
			</div>
		</div>

		<div class="form-group row">
			<label class="form-control-label col-sm-3" for="filename<?= e($i) ?>">
				<?= I18N::translate('Filename on server') ?>
				<span class="sr-only"><?= e($i) ?></span>
			</label>
			<div class="col-sm-9">
				<input class="form-control" id="filename<?= e($i) ?>" name="filename<?= e($i) ?>" type="text">
				<p class="small text-muted">
					<?= I18N::translate('Leave this entry blank to keep the original filename') ?>
				</p>
			</div>
		</div>

		<div class="form-group row">
			<label class="form-control-label col-sm-3" for="folder_list<?= e($i) ?>">
				<?= I18N::translate('Folder name on server') ?>
				<span class="sr-only"><?= e($i) ?></span>
			</label>
			<div class="col-sm-9">
				<select class="form-control" id="folder_list<?= e($i) ?>"name="folder<?= e($i) ?>">
					<?php foreach ($media_folders as $media_folder): ?>
						<option value="<?= e($media_folder) ?>">
							<?= e($media_folder) ?>
						</option>
					<?php endforeach ?>
				</select>
			</div>
		</div>
	<?php endfor ?>

	<button class="btn btn-primary" type="submit">
		<?= /* I18N: A button label. */ I18N::translate('upload') ?>
	</button>
</form>

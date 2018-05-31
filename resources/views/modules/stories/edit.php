<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-modules') => I18N::translate('Modules'), route('module', ['module' => 'stories', 'action' => 'Admin', 'ged' => $tree->getName()]) => I18N::translate('Stories'), $title]]) ?>

<h1><?= $title ?></h1>

<form class="form-horizontal" method="post" action="<?= e(route('module', ['module' => 'stories', 'action' => 'AdminEdit', 'block_id' => $block_id, 'ged' => $tree->getName()])) ?>">
	<?= csrf_field() ?>

	<div class="row form-group">
		<label for="xref" class="col-sm-3 col-form-label">
			<?= I18N::translate('Individual') ?>
		</label>
		<div class="col-sm-9">
			<?= FunctionsEdit::formControlIndividual($tree, $individual, ['id' => 'xref', 'name' => 'xref']) ?>
		</div>
	</div>

	<div class="row form-group">
		<label for="story-title" class="col-sm-3 col-form-label">
			<?= I18N::translate('Story title') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" name="story_title" id="story-title" value="<?= e($story_title) ?>">
		</div>
	</div>

	<div class="row form-group">
		<label for="story-body" class="col-sm-3 col-form-label">
			<?= I18N::translate('Story') ?>
		</label>
		<div class="col-sm-9">
			<textarea name="story_body" id="story-body" class="html-edit form-control" rows="10"><?= e($story_body) ?></textarea>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label">
			<?= I18N::translate('Show this block for which languages') ?>
		</label>
		<div class="col-sm-9">
			<?= FunctionsEdit::editLanguageCheckboxes('languages', $languages) ?>
		</div>
	</div>

	<div class="row form-group">
		<div class="offset-sm-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<i class="fas fa-check"></i>
				<?= I18N::translate('save') ?>
			</button>

			<a href="<?= e(route('module', ['module' => 'stories', 'action' => 'Admin', 'ged' => $tree->getName()])) ?>" class="btn btn-secondary">
				<i class="fas fa-times"></i>
				<?= I18N::translate('cancel') ?>
			</a>
		</div>
	</div>

</form>

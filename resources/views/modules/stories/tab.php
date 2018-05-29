<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="wt-stories-tab py-4">
	<?php foreach ($stories as $story): ?>
		<div class="story_title descriptionbox center rela">
			<?= e($story->title) ?>
		</div>
		<div class="story_body optionbox">
			<?= $story->story_body ?>
		</div>
		<?php if ($is_admin): ?>
			<div class="story_edit">
				<a href="<?= e(route('module', ['module' => 'stories', 'action' => 'AdminEdit', 'block_id' => $story->block_id, 'ged' => $tree->getName()])) ?>">
					<?= I18N::translate('Edit the story') ?>
				</a>
			</div>
		<?php endif ?>
	<?php endforeach ?>

	<?php if ($is_admin && empty($stories)): ?>
		<div>
			<a href="<?= e(route('module', ['module' => 'stories', 'action' => 'AdminEdit', 'xref' => $individual->getXref(), 'ged' => $tree->getName()])) ?>">
				<?= I18N::translate('Add a story') ?>
			</a>
		</div>
	<?php endif ?>
</div>

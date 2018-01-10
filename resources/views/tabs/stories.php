<?php use Fisharebest\Webtrees\Html; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div class="wt-stories-tab py-4">
	<?php foreach ($stories as $story): ?>
		<div class="story_title descriptionbox center rela">
			<?= e($story->title) ?>
		</div>
		<div class="story_body optionbox">
			<?= $story->body ?>
		</div>
		<?php if ($is_editor): ?>
			<div class="story_edit">
				<a href="<?= e(Html::url('module.php', ['mod' => 'stories', 'mod_action' => 'admin_edit', 'block_id' => $story->block_id])) ?>">
					<?= I18N::translate('Edit the story') ?>
				</a>
			</div>
		<?php endif ?>
	<?php endforeach ?>

	<?php if ($is_manager && empty($stories)): ?>
		<div>
			<a href="<?= e(Html::url('module.php', ['mod' => 'stories', 'mod_action' => 'admin_edit', 'xref' => $individual->getXref()])) ?>">
				<?= I18N::translate('Add a story') ?>
			</a>
		</div>
	<?php endif ?>
</div>

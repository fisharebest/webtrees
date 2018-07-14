<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<div class="wt-page-content">
	<?= view('lists/notes-table', ['notes' => $notes, 'tree' => $tree]) ?>
</div>

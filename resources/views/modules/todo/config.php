<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<p>
	<?= I18N::translate('Research tasks are special events, added to individuals in your family tree, which identify the need for further research. You can use them as a reminder to check facts against more reliable sources, to obtain documents or photographs, to resolve conflicting information, etc.') ?>
	<?= I18N::translate('To create new research tasks, you must first add “research task” to the list of facts and events in the family tree’s preferences.') ?>
	<?= I18N::translate('Research tasks are stored using the custom GEDCOM tag “_TODO”. Other genealogy applications may not recognize this tag.') ?>
</p>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="show_other">
		<?= I18N::translate('Show research tasks that are assigned to other users') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::radioButtons('show_other', FunctionsEdit::optionsNoYes(), $show_other, true) ?>
	</div>
</div>


<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="show_unassigned">
		<?= I18N::translate('Show research tasks that are not assigned to any user') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::radioButtons('show_unassigned', FunctionsEdit::optionsNoYes(), $show_unassigned, true) ?>
	</div>
</div>

<div class="form-group row">
	<label class="col-sm-3 col-form-label" for="show_future">
		<?= I18N::translate('Show research tasks that have a date in the future') ?>
	</label>
	<div class="col-sm-9">
		<?= Bootstrap4::radioButtons('show_future', FunctionsEdit::optionsNoYes(), $show_future, true) ?>
	</div>
</div>

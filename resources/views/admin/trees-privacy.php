<?php use Fisharebest\Webtrees\Bootstrap4; ?>
<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\I18N; ?>
<?php use Fisharebest\Webtrees\Site; ?>
<?php use Fisharebest\Webtrees\View; ?>

<?= view('admin/breadcrumbs', ['links' => [route('admin-control-panel') => I18N::translate('Control panel'), route('admin-trees') => I18N::translate('Manage family trees'), $title]]) ?>

<h1><?= $title ?></h1>

<form method="POST">
	<input type="hidden" name="route" value="tree-privacy">
	<input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">
	<?= csrf_field() ?>

	<!-- REQUIRE_AUTHENTICATION -->
	<div class="row form-group">
		<div class="col-form-label col-sm-4">
			<label>
				<?= /* I18N: A configuration setting */ I18N::translate('Show the family tree') ?>
			</label>
			<div class="hidden-xs">
				<span class="badge visitors"><?= I18N::translate('visitors') ?></span>
				<span class="badge members"><?= I18N::translate('members') ?></span>
			</div>
		</div>
		<div class="col-sm-8">
			<?= Bootstrap4::select(['0' => I18N::translate('Show to visitors'), '1' => I18N::translate('Show to members')], $tree->getPreference('REQUIRE_AUTHENTICATION'), ['id' => 'REQUIRE_AUTHENTICATION', 'name' => 'REQUIRE_AUTHENTICATION']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Family tree” configuration setting */ I18N::translate('Enabling this option will force all visitors to sign in before they can view any data on the website.') ?>
			</p>
			<?php if (Site::getPreference('USE_REGISTRATION_MODULE') === '1'): ?>
				<p class="small text-muted">
					<?= I18N::translate('If visitors can not see the family tree, they will not be able to sign up for an account. You will need to add their account manually.') ?>
				</p>
			<?php endif ?>
		</div>
	</div>

	<!-- SHOW_DEAD_PEOPLE -->
	<div class="row form-group">
		<div class="col-form-label col-sm-4">
			<label for="SHOW_DEAD_PEOPLE">
				<?= /* I18N: A configuration setting */ I18N::translate('Show dead individuals') ?>
			</label>
			<div class="hidden-xs">
				<span class="badge visitors"><?= I18N::translate('visitors') ?></span>
				<span class="badge members"><?= I18N::translate('members') ?></span>
			</div>
		</div>
		<div class="col-sm-8">
			<?= Bootstrap4::select(array_slice(FunctionsEdit::optionsAccessLevels(), 0, 2, true), $tree->getPreference('SHOW_DEAD_PEOPLE'), ['id' => 'SHOW_DEAD_PEOPLE', 'name' => 'SHOW_DEAD_PEOPLE']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Show dead individuals” configuration setting */ I18N::translate('Set the privacy access level for all dead individuals.') ?>
			</p>
		</div>
	</div>


	<!-- MAX_ALIVE_AGE -->
	<div class="row form-group">
		<label class="col-form-label col-sm-4" for="MAX_ALIVE_AGE">
			<?= I18N::translate('Age at which to assume an individual is dead') ?>
		</label>
		<div class="col-sm-8">
			<input
				class="form-control"
				id="MAX_ALIVE_AGE"
				maxlength="5"
				name="MAX_ALIVE_AGE"
				type="text"
				value="<?= e($tree->getPreference('MAX_ALIVE_AGE')) ?>"
			>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Age at which to assume an individual is dead” configuration setting */ I18N::translate('If this individual has any events other than death, burial, or cremation more recent than this number of years, they are considered to be “alive”. Children’s birth dates are considered to be such events for this purpose.') ?>
			</p>
		</div>
	</div>

	<!-- HIDE_LIVE_PEOPLE -->
	<fieldset class="form-group">
		<div class="row">
			<div class="col-sm-4">
				<legend class="col-form-label">
					<?= /* I18N: A configuration setting */ I18N::translate('Show living individuals') ?>
					<div class="hidden-xs">
						<span class="badge visitors"><?= I18N::translate('visitors') ?></span>
						<span class="badge members"><?= I18N::translate('members') ?></span>
					</div>
				</legend>
			</div>
			<div class="col-sm-8">
				<?= Bootstrap4::select(['0' => I18N::translate('Show to visitors'), '1' => I18N::translate('Show to members')], $tree->getPreference('HIDE_LIVE_PEOPLE'), ['id' => 'HIDE_LIVE_PEOPLE', 'name' => 'HIDE_LIVE_PEOPLE']) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Show living individuals” configuration setting */ I18N::translate('If you show living individuals to visitors, all other privacy restrictions are ignored. Do this only if all the data in your tree is public.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- KEEP_ALIVE_YEARS_BIRTH / KEEP_ALIVE_YEARS_DEATH -->
	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-4">
				<?= /* I18N: A configuration setting. …who were born in the last XX years or died in the 	last YY years */ I18N::translate('Extend privacy to dead individuals') ?>
			</legend>
			<div class="col-sm-8">
				<?php
				echo
					/* I18N: Extend privacy to dead individuals who were… */ I18N::translate(
					'born in the last %1$s years or died in the last %2$s years',
					'<input type="text" name="KEEP_ALIVE_YEARS_BIRTH" value="' . $tree->getPreference('KEEP_ALIVE_YEARS_BIRTH') . '" size="5" maxlength="3">',
					'<input type="text" name="KEEP_ALIVE_YEARS_DEATH" value="' . $tree->getPreference('KEEP_ALIVE_YEARS_DEATH') . '" size="5" maxlength="3">'
				) ?>
				<p class="small text-muted">
					<?= /* I18N: Help text for the “Extend privacy to dead individuals” configuration setting */ I18N::translate('In some countries, privacy laws apply not only to living individuals, but also to those who have died recently. This option will allow you to extend the privacy rules for living individuals to those who were born or died within a specified number of years. Leave these values empty to disable this feature.') ?>
				</p>
			</div>
		</div>
	</fieldset>

	<!-- SHOW_LIVING_NAMES -->
	<div class="row form-group">
		<div class="col-form-label col-sm-4">
			<label for="SHOW_LIVING_NAMES">
				<?= /* I18N: A configuration setting */ I18N::translate('Show names of private individuals') ?>
			</label>
			<div class="hidden-xs">
				<span class="badge visitors"><?= I18N::translate('visitors') ?></span>
				<span class="badge members"><?= I18N::translate('members') ?></span>
				<span class="badge managers"><?= I18N::translate('managers') ?></span>
			</div>
		</div>
		<div class="col-sm-8">
			<?= Bootstrap4::select(array_slice(FunctionsEdit::optionsAccessLevels(), 0, 3, true), $tree->getPreference('SHOW_LIVING_NAMES'), ['id' => 'SHOW_LIVING_NAMES', 'name' => 'SHOW_LIVING_NAMES']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Show names of private individuals” configuration setting */ I18N::translate('This option will show the names (but no other details) of private individuals. Individuals are private if they are still alive or if a privacy restriction has been added to their individual record. To hide a specific name, add a privacy restriction to that name record.') ?>
			</p>
		</div>
	</div>

	<!-- SHOW_PRIVATE_RELATIONSHIPS -->
	<div class="row form-group">
		<div class="col-form-label col-sm-4">
			<label for="SHOW_PRIVATE_RELATIONSHIPS">
				<?= /* I18N: A configuration setting */ I18N::translate('Show private relationships') ?>
			</label>
			<div class="hidden-xs">
				<span class="badge visitors"><?= I18N::translate('visitors') ?></span>
				<span class="badge members"><?= I18N::translate('members') ?></span>
			</div>
		</div>
		<div class="col-sm-8">
			<?= Bootstrap4::select(['0' => I18N::translate('Hide from everyone'), '1' => I18N::translate('Show to visitors')], $tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS'), ['id' => 'SHOW_PRIVATE_RELATIONSHIPS', 'name' => 'SHOW_PRIVATE_RELATIONSHIPS']) ?>
			<p class="small text-muted">
				<?= /* I18N: Help text for the “Show private relationships” configuration setting */ I18N::translate('This option will retain family links in private records. This means that you will see empty “private” boxes on the pedigree chart and on other charts with private individuals.') ?>
			</p>
		</div>
	</div>
	<h2><?= /* I18N: Privacy restrictions are set by RESN tags in GEDCOM. */ I18N::translate('Privacy restrictions') ?></h2>
	<p>
		<?= /* I18N: Privacy restrictions are RESN tags in GEDCOM. */ I18N::translate('You can set the access for a specific record, fact, or event by adding a restriction to it. If a record, fact, or event does not have a restriction, the following default restrictions will be used.') ?>
	</p>

	<script id="new-resn-template" type="text/html">
		<tr>
			<td>
				<select class="form-control" id="record-type">
					<option value="individual"><?= I18N::translate('Individual') ?></option>
					<option value="family"><?= I18N::translate('Family') ?></option>
					<option value="source"><?= I18N::translate('Source') ?></option>
					<option value="repository"><?= I18N::translate('Repository') ?></option>
					<option value="note"><?= I18N::translate('Note') ?></option>
					<option value="media"><?= I18N::translate('Media object') ?></option>
				</select>
				<span class="select-record select-individual">
					<?= FunctionsEdit::formControlIndividual($tree, null, ['name' => 'xref', 'class' => 'form-control', 'style' => 'width:100%;']) ?>
				</span>
				<span class="select-record select-family d-none">
					<?= FunctionsEdit::formControlFamily($tree, null, ['name' => 'xref', 'class' => 'form-control', 'style' => 'width:100%;', 'disabled' => true]) ?>
				</span>
				<span class="select-record select-source d-none">
					<?= FunctionsEdit::formControlSource($tree, null, ['name' => 'xref', 'class' => 'form-control', 'style' => 'width:100%;', 'disabled' => true]) ?>
				</span>
				<span class="select-record select-repository d-none">
					<?= FunctionsEdit::formControlRepository($tree, null, ['name' => 'xref', 'class' => 'form-control', 'style' => 'width:100%;', 'disabled' => true]) ?>
				</span>
				<span class="select-record select-note d-none">
					<?= FunctionsEdit::formControlNote($tree, null, ['name' => 'xref', 'class' => 'form-control', 'style' => 'width:100%;', 'disabled' => true]) ?>
				</span>
				<span class="select-record select-media d-none">
					<?= FunctionsEdit::formControlMediaObject($tree, null, ['name' => 'xref', 'class' => 'form-control', 'style' => 'width:100%;', 'disabled' => true]) ?>
				</span>
				<input data-autocomplete-type="IFSRO" id="xref" maxlength="20" name="xref[]" type="text">
			</td>
			<td>
				<?= Bootstrap4::select($all_tags, '', ['name' => 'tag_type[]']) ?>
			</td>
			<td>
				<?= Bootstrap4::select($privacy_constants, 'privacy', ['name' => 'resn[]']) ?>
			</td>
			<td>
			</td>
		</tr>
	</script>

	<table class="table table-bordered table-sm table-hover" id="default-resn">
		<caption class="sr-only">
			<?= I18N::translate('Privacy restrictions - these apply to records and facts that do not contain a GEDCOM RESN tag') ?>
		</caption>
		<thead>
			<tr>
				<th>
					<?= I18N::translate('Record') ?>
				</th>
				<th>
					<?= I18N::translate('Fact or event') ?>
				</th>
				<th>
					<?= I18N::translate('Access level') ?>
				</th>
				<th>
					<button class="btn btn-primary" id="add-resn" type="button">
						<i class="fas fa-plus"></i>
						<?= /* I18N: A button label. */ I18N::translate('add') ?>
					</button>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($privacy_restrictions as $privacy_restriction): ?>
				<tr>
					<td>
						<?php if ($privacy_restriction->record): ?>
							<a href="<?= e($privacy_restriction->record->url()) ?>"><?= $privacy_restriction->record->getFullName() ?></a>
						<?php elseif ($privacy_restriction->xref): ?>
							<div class="text-danger">
								<?= $privacy_restriction->xref ?> — <?= I18N::translate('this record does not exist') ?>
							</div>
						<?php else: ?>
							<div class="text-muted">
								<?= I18N::translate('All records') ?>
							</div>
						<?php endif ?>
					</td>
					<td>
						<?php if ($privacy_restriction->tag_label): ?>
							<?= $privacy_restriction->tag_label ?>
						<?php else: ?>
							<div class="text-muted">
								<?= I18N::translate('All facts and events') ?>
							</div>
						<?php endif ?>
					</td>
					<td>
						<?= FunctionsEdit::optionsRestrictions(false)[$privacy_restriction->resn] ?>
					</td>
					<td>
						<label for="delete-<?= $privacy_restriction->default_resn_id ?>">
							<input id="delete-<?= $privacy_restriction->default_resn_id ?>" name="delete[]" type="checkbox" value="<?= $privacy_restriction->default_resn_id ?>">
							<?= I18N::translate('Delete') ?>
						</label>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>

	<div class="row form-group">
		<div class="offset-sm-4 col-sm-8">
			<button type="submit" class="btn btn-primary">
				<i class="fas fa-check">
				<?= I18N::translate('save') ?>
			</button>
			<a class="btn btn-secondary" href="<?= route('admin-trees', ['ged' => $tree->getName()]) ?>">
				<i class="fas times">
				<?= I18N::translate('cancel') ?>
			</a>
			<!-- Coming soon
			<div class="form-check">
				<?php if ($count_trees > 1): ?>
				<label>
					<input type="checkbox" name="all_trees">
					<?= /* I18N: Label for checkbox */ I18N::translate('Apply these preferences to all family trees') ?>
				</label>
				<?php endif ?>
			</div>
			<div class="form-check">
				<label>
					<input type="checkbox" name="new_trees">
					<?= /* I18N: Label for checkbox */ I18N::translate('Apply these preferences to new family trees') ?>
				</label>
			</div>
		</div>
		-->
		</div>

</form>

<?php View::push('javascript') ?>
<script>
  'use strict';

  /**
   * Hide/show the feedback labels for a privacy option.
   *
   * @param sel    the control to change
   * @param who    "visitors", "members" or "managers"
   * @param access true or false
   */
  function setPrivacyFeedback (sel, who, access) {
    var formGroup = $(sel).closest('.form-group');

    if (access) {
      $('.' + who, formGroup).addClass('badge-success').removeClass('badge-secondary');
      $('.' + who + ' i', formGroup).addClass('fa-check').removeClass('fa-times');
    } else {
      $('.' + who, formGroup).addClass('badge-secondary').removeClass('badge-success');
      $('.' + who + ' i', formGroup).addClass('fa-times').removeClass('fa-check');
    }
  }

  /**
   * Update all the privacy feedback labels.
   */
  function updatePrivacyFeedback () {
    var requireAuthentication = parseInt($('[name=REQUIRE_AUTHENTICATION]').val(), 10);
    var showDeadPeople = parseInt($('[name=SHOW_DEAD_PEOPLE]').val(), 10);
    var hideLivePeople = parseInt($('[name=HIDE_LIVE_PEOPLE]').val(), 10);
    var showLivingNames = parseInt($('[name=SHOW_LIVING_NAMES]').val(), 10);
    var showPrivateRelationships = parseInt($('[name=SHOW_PRIVATE_RELATIONSHIPS]').val(), 10);

    setPrivacyFeedback('[name=REQUIRE_AUTHENTICATION]', 'visitors', requireAuthentication === 0);
    setPrivacyFeedback('[name=REQUIRE_AUTHENTICATION]', 'members', true);

    setPrivacyFeedback('[name=SHOW_DEAD_PEOPLE]', 'visitors', requireAuthentication === 0 && (showDeadPeople >= 2 || hideLivePeople === 0));
    setPrivacyFeedback('[name=SHOW_DEAD_PEOPLE]', 'members', showDeadPeople >= 1 || hideLivePeople === 0);

    setPrivacyFeedback('[name=HIDE_LIVE_PEOPLE]', 'visitors', requireAuthentication === 0 && hideLivePeople === 0);
    setPrivacyFeedback('[name=HIDE_LIVE_PEOPLE]', 'members', true);

    setPrivacyFeedback('[name=SHOW_LIVING_NAMES]', 'visitors', requireAuthentication === 0 && showLivingNames >= 2);
    setPrivacyFeedback('[name=SHOW_LIVING_NAMES]', 'members', showLivingNames >= 1);
    setPrivacyFeedback('[name=SHOW_LIVING_NAMES]', 'managers', showLivingNames >= 0);

    setPrivacyFeedback('[name=SHOW_PRIVATE_RELATIONSHIPS]', 'visitors', requireAuthentication === 0 && showPrivateRelationships >= 1);
    setPrivacyFeedback('[name=SHOW_PRIVATE_RELATIONSHIPS]', 'members', showPrivateRelationships >= 1);
  }

  // Activate the privacy feedback labels.
  updatePrivacyFeedback();
  $('[name=REQUIRE_AUTHENTICATION], [name=HIDE_LIVE_PEOPLE], [name=SHOW_DEAD_PEOPLE], [name=SHOW_LIVING_NAMES], [name=SHOW_PRIVATE_RELATIONSHIPS]').on('change', function () {
    updatePrivacyFeedback();
  });

  // Mute a line when it is marked for deletion
  $("#default-resn").on("click", "input[type=checkbox]", function() {
    if ($(this).prop("checked")) {
      $($(this).closest("tr").addClass("text-muted"));
    } else {
      $($(this).closest("tr").removeClass("text-muted"));
    }
  });

  // Add a new row to the table
  $("#add-resn").on("click", function() {
    $("#default-resn tbody").prepend($("#new-resn-template").html());
  });
</script>
<?php View::endpush() ?>

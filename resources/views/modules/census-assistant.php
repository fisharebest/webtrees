<?php use Fisharebest\Webtrees\Functions\FunctionsEdit; ?>
<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<div id="census-assistant-link" hidden>
	<a href="#">
		<?= I18N::translate('Create a shared note using the census assistant') ?>
	</a>
</div>

<div id="census-assistant" hidden>
	<input type="hidden" name="ca_census" id="census-assistant-class">
	<div class="form-group">
		<div class="input-group">
			<div class="input-group-prepend">
				<label class="input-group-text" for="census-assistant-title">
					<?= I18N::translate('Title') ?>
				</label>
			</div>
			<input class="form-control" id="census-assistant-title" name="ca_title" value="">
		</div>
	</div>

	<div class="row">
		<div class="form-group col-sm-6">
			<div class="input-group">
				<div class="input-group-prepend">
					<label class="input-group-addon" for="census-assistant-citation">
						<?= I18N::translate('Citation') ?>
					</label>
				</div>
				<input class="form-control" id="census-assistant-citation" name="ca_citation">
			</div>
		</div>

		<div class="form-group col-sm-6">
			<div class="input-group">
				<div class="input-group-prepend">
					<label class="input-group-text" for="census-assistant-place">
						<?= I18N::translate('Place') ?>
					</label>
				</div>
				<input class="form-control" id="census-assistant-place" name="ca_place">
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text">
					<?= I18N::translate('Individuals') ?>
				</span>
			</div>
			<?= FunctionsEdit::formControlIndividual($individual->getTree(), $individual, ['id' => 'census-assistant-individual', 'style' => 'width:100%']) ?>
			<span class="input-group-btn">
						<button type="button" class="btn btn-primary" id="census-assistant-add">
							<?= FontAwesome::semanticIcon('add', I18N::translate('Add')) ?>
						</button>
					</span>
			<span class="input-group-btn">
						<button type="button" class="btn btn-primary" id="census-assistant-head"
						        title="<?= I18N::translate('Head of household') ?>">
							<?= FontAwesome::semanticIcon('individual', I18N::translate('Head of household')) ?>
						</button>
					</span>
		</div>
	</div>

	<table class="table table-bordered table-small table-responsive wt-census-assistant-table"
	       id="census-assistant-table">
		<thead class="wt-census-assistant-header"></thead>
		<tbody class="wt-census-assistant-body"></tbody>
	</table>

	<div class="form-group">
		<div class="input-group">
			<div class="input-group-prepend">
				<label class="input-group-text" for="census-assistant-notes">
					<?= I18N::translate('Notes') ?>
				</label>
			</div>
			<input class="form-control" id="census-assistant-notes" name="ca_notes">
		</div>
	</div>
</div>

<script>
  // When a census date/place is selected, activate the census-assistant
  function censusAssistantSelect() {
    var censusAssistantLink = document.querySelector('#census-assistant-link');
    var censusAssistant     = document.querySelector('#census-assistant');
    var censusOption        = this.options[this.selectedIndex];
    var census              = censusOption.dataset.census;
    var censusPlace         = censusOption.dataset.place;
    var censusYear          = censusOption.value.substr(-4);

    if (censusOption.value !== '') {
      censusAssistantLink.removeAttribute('hidden');
    } else {
      censusAssistantLink.setAttribute('hidden', '');
    }

    censusAssistant.setAttribute('hidden', '');
    document.querySelector('#census-assistant-class').value = census;
    document.querySelector('#census-assistant-title').value = censusYear + ' ' + censusPlace + ' - <?= I18N::translate('Census transcript') ?> - <?= strip_tags($individual->getFullName()) ?> - <?= I18N::translate('Household') ?>';

    fetch('module.php?mod=GEDFact_assistant&mod_action=census-header&census=' + census)
      .then(function (response) {
        return response.text();
      })
      .then(function (text) {
        document.querySelector('#census-assistant-table thead').innerHTML = text;
        document.querySelector('#census-assistant-table tbody').innerHTML = '';
      });
  }

  // When the census assistant is activated, show the input fields
  function censusAssistantLink() {
    document.querySelector('#census-selector').setAttribute('hidden', '');
    this.setAttribute('hidden', '');
    document.getElementById('census-assistant').removeAttribute('hidden');
    // Set the current individual as the head of household.
    censusAssistantHead();

    return false;
  }

  // Add the currently selected individual to the census
  function censusAssistantAdd() {
    var censusSelector = document.querySelector('#census-selector');
    var census         = censusSelector.options[censusSelector.selectedIndex].dataset.census;
    var indi_selector  = document.querySelector('#census-assistant-individual');
    var xref           = indi_selector.options[indi_selector.selectedIndex].value;
    var headTd         = document.querySelector('#census-assistant-table td');
    var head           = headTd === null ? xref : headTd.innerHTML;

    fetch('module.php?mod=GEDFact_assistant&mod_action=census-individual&census=' + census + '&xref=' + xref + '&head=' + head, {credentials: 'same-origin'})
      .then(function (response) {
        return response.text();
      })
      .then(function (text) {
        document.querySelector('#census-assistant-table tbody').innerHTML += text;
      });

    return false;
  }

  // Set the currently selected individual as the head of household
  function censusAssistantHead() {
    var censusSelector = document.querySelector('#census-selector');
    var census         = censusSelector.options[censusSelector.selectedIndex].dataset.census;
    var indi_selector  = document.querySelector('#census-assistant-individual');
    var xref           = indi_selector.options[indi_selector.selectedIndex].value;

    fetch('module.php?mod=GEDFact_assistant&mod_action=census-individual&census=' + census + '&xref=' + xref + '&head=' + xref, {credentials: 'same-origin'})
      .then(function (response) {
        return response.text();
      })
      .then(function (text) {
        document.querySelector('#census-assistant-table tbody').innerHTML = text;
      });

    return false;
  }

  document.querySelector('#census-selector').addEventListener('change', censusAssistantSelect);
  document.querySelector('#census-assistant-link').addEventListener('click', censusAssistantLink);
  document.querySelector('#census-assistant-add').addEventListener('click', censusAssistantAdd);
  document.querySelector('#census-assistant-head').addEventListener('click', censusAssistantHead);
</script>

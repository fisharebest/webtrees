<?php use Fisharebest\Webtrees\GedcomTag; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<h2 class="wt-page-title">
	<?= $title ?>
</h2>

<script>
  var numfields = <?= count($fields) ?>;

  /**
   * add a row to the table of fields
   */
  function addFields() {
    var tbl         = document.getElementById('div-holder');
    var trow        = document.createElement('div');
    trow.className	= 'row form-group';
    var label       = document.createElement('div');
    label.className = 'col-sm-3 wt-page-options-label py-1';
    var sel         = document.createElement('select');
    sel.className	= 'form-control form-control-sm';
    sel.name        = 'fields[' + numfields + ']';
    sel.rownum      = numfields;
    sel.onchange    = function () {
      showDate(this, this.rownum);
    };

    // all of the field options
		<?php foreach ($other_fields as $field => $label) { ?>
    opt       = document.createElement('option');
    opt.value = <?= json_encode($field) ?>;
    opt.text  = <?= json_encode($label) ?>;
    sel.options.add(opt);
		<?php } ?>
    label.appendChild(sel);
    trow.appendChild(label);
    // create the new value cell
    var val			= document.createElement('div');
    val.id			= 'vcell' + numfields;
    val.className	= 'col-sm-9 wt-page-options-value form-row py-1';

    var inp			= document.createElement('input');
    inp.name		= 'values[' + numfields + ']';
    inp.className	= 'form-control form-control-sm col-9';
    inp.type		= 'text';
    inp.id			= 'value' + numfields;
    inp.tabindex	= numfields + 1;
    val.appendChild(inp);
    trow.appendChild(val);
    var lastRow = tbl.lastChild.previousSibling;

    tbl.insertBefore(trow, lastRow.nextSibling);
    numfields++;
  }

  /**
   * add the date options selection
   */
  function showDate(sel, row) {
    var type = sel.options[sel.selectedIndex].value;
    var pm   = document.getElementById('plusminus' + row);
    if (!type.match("DATE$")) {
      // if it is not a date do not show the date
      if (pm) pm.parentNode.removeChild(pm);
      return;
    }
    // if it is a date and the plusminus is already show, then leave
    if (pm) return;
    var elm			= document.getElementById('vcell' + row);
    var sel			= document.createElement('select');
    sel.id			= 'plusminus' + row;
    sel.name		= 'plusminus[' + row + ']';
    sel.className	= 'form-control form-control-sm col-3';
    var opt			= document.createElement('option');
    opt.value		= '';
    opt.text		= '<?= I18N::translate('Exact date') ?>';
    sel.appendChild(opt);
    opt				= document.createElement('option');
    opt.value		= '';
    opt.text		= '<?= I18N::plural('±%s year', '±%s years', 2, I18N::number(2)) ?>';
    sel.appendChild(opt);
    opt				= document.createElement('option');
    opt.value		= '5';
    opt.text		= '<?= I18N::plural('±%s year', '±%s years', 5, I18N::number(5)) ?>';
    sel.appendChild(opt);
    opt				= document.createElement('option');
    opt.value		= '10';
    opt.text		= '<?= I18N::plural('±%s year', '±%s years', 10, I18N::number(10)) ?>';
    sel.appendChild(opt);
    var spc = document.createTextNode(' ');
    elm.appendChild(spc);
    elm.appendChild(sel);
  }
</script>

<div id="advanced-search-page">
	<form class="wt-page-options wt-page-options-search-advanced hidden-print mb-4">
		<input type="hidden" name="route" value="search-advanced">
		<div id="div-holder">
			<?php foreach ($fields as $field_name => $field_value): ?>
				<?php if (substr($field_name, 0, 5) !== 'FAMC:'): ?>
					<?= view('search-advanced-field', ['field_name' => $field_name, 'field_value' => $field_value, 'modifier' => $modifiers[$field_name] ?? '']) ?>
					<div class="row form-group">
					<label class="col-sm-3 col-form-label wt-page-options-label" for="field-<?= e($field_name) ?>">
						<?= GedcomTag::getLabel(preg_replace('/:(SDX|BEGINS|EXACT|CONTAINS)$/', '', $field_name)) ?>
					</label>
					<div class="col-sm-9 wt-page-options-value form-row mx-0">
						<input class="form-control form-control-sm col-9" type="text" id="field-<?= e($field_name) ?>" name="fields[]"
							value="<?= e($field_value) ?>">
						<?php if (preg_match('/^NAME:/', $field_name) > 0): ?>
							<select class="form-control form-control-sm col-3" name="fields[<?= $i ?>]">
								<option value="<?= $currentField ?>:EXACT" <?php if (preg_match('/:EXACT$/', $currentFieldSearch) > 0) echo 'selected' ?>>
									<?= I18N::translate('Exact') ?>
								</option>
								<option value="<?= $currentField ?>:BEGINS" <?php if (preg_match('/:BEGINS$/', $currentFieldSearch) > 0) echo 'selected' ?>>
									<?= I18N::translate('Begins with') ?>
								</option>
								<option value="<?= $currentField ?>:CONTAINS" <?php if (preg_match('/:CONTAINS$/', $currentFieldSearch) > 0) echo 'selected' ?>>
									<?= I18N::translate('Contains') ?>
								</option>
								<option value="<?= $currentField ?>:SDX" <?php if (preg_match('/:SDX$/', $currentFieldSearch) > 0) echo 'selected' ?>><?= I18N::translate('Sounds like') ?>
								</option>
							</select>
						<?php else: ?>
							<input type="hidden" name="fields[<?= $i ?>]" value="<?= $fields[$i] ?>">
						<?php endif ?>
						<?php if (preg_match('/:DATE$/', $currentFieldSearch) > 0): ?>
							<select class="form-control form-control-sm col-3" name="plusminus[<?= $i ?>]">
								<option value="">
									<?= I18N::translate('Exact date') ?>
								</option>
								<option value="2" <?php if (!empty($controller->plusminus[$i]) && $controller->plusminus[$i] == 2) echo 'selected' ?>>
									<?= I18N::plural('±%s year', '±%s years', 2, I18N::number(2)) ?>
								</option>
								<option value="5" <?php if (!empty($controller->plusminus[$i]) && $controller->plusminus[$i] == 5) echo 'selected' ?>>
									<?= I18N::plural('±%s year', '±%s years', 5, I18N::number(5)) ?>
								</option>
								<option value="10" <?php if (!empty($controller->plusminus[$i]) && $controller->plusminus[$i] == 10) echo 'selected' ?>>
									<?= I18N::plural('±%s year', '±%s years', 10, I18N::number(10)) ?>
								</option>
							</select>
						<?php endif ?>
					</div>
					<?php
					//-- relative fields
					if ($i == 0 && $fct > 4) {
						$j = $fct;
						// Get the current options for Father’s and Mother’s name searches
						$fatherGivnOption = 'SDX';
						$fatherSurnOption = 'SDX';
						$motherGivnOption = 'SDX';
						$motherSurnOption = 'SDX';
						for ($k = 0; $k < $fct; $k++) {
							$searchField  = $field[$k];
							$searchOption = substr($searchField, 20); // Assume we have something like "FAMC:HUSB:NAME:GIVN:foo"
							switch (substr($searchField, 0, 20)) {
								case 'FAMC:HUSB:NAME:GIVN:':
									$fatherGivnOption = $searchOption;
									break;
								case 'FAMC:HUSB:NAME:SURN:':
									$fatherSurnOption = $searchOption;
									break;
								case 'FAMC:WIFE:NAME:GIVN:':
									$motherGivnOption = $searchOption;
									break;
								case 'FAMC:WIFE:NAME:SURN:':
									$motherSurnOption = $searchOption;
									break;
							}
						}
					}
					?>
				</div>
				<?php endif ?>
			<?php endforeach ?>
			<!--  father -->
			<div class="row form-group wt-page-options-label">
				<div class="form-row mx-auto">
					<?= I18N::translate('Father') ?>
				</div>
			</div>
			<div class="row form-group">
				<label class="col-sm-3 col-form-label wt-page-options-label">
					<?= I18N::translate('Given names') ?>
				</label>
				<div class="col-sm-9 wt-page-options-value form-row mx-0">
					<input class="form-control form-control-sm col-9" type="text" name="values[<?= $j ?>]" value="<?= $values[array_search('FAMC:HUSB:NAME:GIVN:' . $fatherGivnOption, $fields)] ?? '' ?>">
					<select class="form-control form-control-sm col-3" name="fields[<?= $j ?>]">
						<option value="FAMC:HUSB:NAME:GIVN:EXACT" <?php if ($fatherGivnOption == 'EXACT') echo 'selected' ?>>
							<?= I18N::translate('Exact') ?>
						</option>
						<option value="FAMC:HUSB:NAME:GIVN:BEGINS" <?php if ($fatherGivnOption == 'BEGINS') echo 'selected' ?>>
							<?= I18N::translate('Begins with') ?>
						</option>
						<option value="FAMC:HUSB:NAME:GIVN:CONTAINS" <?php if ($fatherGivnOption == 'CONTAINS') echo 'selected' ?>>
							<?= I18N::translate('Contains') ?>
						</option>
						<option value="FAMC:HUSB:NAME:GIVN:SDX" <?php if ($fatherGivnOption == 'SDX') echo 'selected' ?>>
							<?= I18N::translate('Sounds like') ?>
						</option>
					</select>
				</div>
			</div>
			<?php $j++ ?>
			<div class="row form-group">
				<label class="col-sm-3 col-form-label wt-page-options-label">
					<?= I18N::translate('Surname') ?>
				</label>
				<div class="col-sm-9 wt-page-options-value form-row mx-0">
					<input class="form-control form-control-sm col-9" type="text" name="values[<?= $j ?>]" value="<?= $values[array_search('FAMC:HUSB:NAME:SURN:' . $fatherSurnOption, $fields)] ?? '' ?>">
					<select class="form-control form-control-sm col-3" name="fields[<?= $j ?>]">
						<option value="FAMC:HUSB:NAME:SURN:EXACT" <?php if ($fatherSurnOption == 'EXACT') echo 'selected' ?>>
							<?= I18N::translate('Exact') ?>
						</option>
						<option value="FAMC:HUSB:NAME:SURN:BEGINS" <?php if ($fatherSurnOption == 'BEGINS') echo 'selected' ?>>
							<?= I18N::translate('Begins with') ?>
						</option>
						<option value="FAMC:HUSB:NAME:SURN:CONTAINS" <?php if ($fatherSurnOption == 'CONTAINS') echo 'selected' ?>>
							<?= I18N::translate('Contains') ?>
						</option>
						<option value="FAMC:HUSB:NAME:SURN:SDX" <?php if ($fatherSurnOption == 'SDX') echo 'selected' ?>>
							<?= I18N::translate('Sounds like') ?>
						</option>
					</select>
				</div>
			</div>
			<!--  mother -->
			<?php $j++ ?>
			<div class="row form-group wt-page-options-label">
				<div class="form-row mx-auto">
					<?= I18N::translate('Mother') ?>
				</div>
			</div>
			<div class="row form-group">
				<label class="col-sm-3 col-form-label wt-page-options-label">
					<?= I18N::translate('Given names') ?>
				</label>
				<div class="col-sm-9 wt-page-options-value form-row mx-0">
					<input class="form-control form-control-sm col-9" type="text" name="values[<?= $j ?>]" value="<?= $values[array_search('FAMC:WIFE:NAME:GIVN:' . $motherGivnOption, $fields)] ?? '' ?>">
					<select class="form-control form-control-sm col-3" name="fields[<?= $j ?>]">
						<option value="FAMC:WIFE:NAME:GIVN:EXACT" <?php if ($motherGivnOption == 'EXACT') echo 'selected' ?>>
							<?= I18N::translate('Exact') ?>
						</option>
						<option value="FAMC:WIFE:NAME:GIVN:BEGINS" <?php if ($motherGivnOption == 'BEGINS') echo 'selected' ?>>
							<?= I18N::translate('Begins with') ?>
						</option>
						<option value="FAMC:WIFE:NAME:GIVN:CONTAINS" <?php if ($motherGivnOption == 'CONTAINS') echo 'selected' ?>>
							<?= I18N::translate('Contains') ?>
						</option>
						<option value="FAMC:WIFE:NAME:GIVN:SDX" <?php if ($motherGivnOption == 'SDX') echo 'selected' ?>>
							<?= I18N::translate('Sounds like') ?>
						</option>
					</select>
				</div>
			</div>
			<?php $j++ ?>
			<div class="row form-group">
				<label class="col-sm-3 col-form-label wt-page-options-label">
					<?= I18N::translate('Surname') ?>
				</label>
				<div class="col-sm-9 wt-page-options-value form-row mx-0">
					<input class="form-control form-control-sm col-9" type="text" name="values[<?= $j ?>]" value="<?= $values[array_search('FAMC:WIFE:NAME:GIVN:' . $motherGivnOption, $fields)] ?? '' ?>">
					<select class="form-control form-control-sm col-3" name="fields[<?= $j ?>]">
						<option value="FAMC:WIFE:NAME:GIVN:EXACT" <?php if ($motherGivnOption == 'EXACT') echo 'selected' ?>>
							<?= I18N::translate('Exact') ?>
						</option>
						<option value="FAMC:WIFE:NAME:GIVN:BEGINS" <?php if ($motherGivnOption == 'BEGINS') echo 'selected' ?>>
							<?= I18N::translate('Begins with') ?>
						</option>
						<option value="FAMC:WIFE:NAME:GIVN:CONTAINS" <?php if ($motherGivnOption == 'CONTAINS') echo 'selected' ?>>
							<?= I18N::translate('Contains') ?>
						</option>
						<option value="FAMC:WIFE:NAME:GIVN:SDX" <?php if ($motherGivnOption == 'SDX') echo 'selected' ?>>
							<?= I18N::translate('Sounds like') ?>
						</option>
					</select>
				</div>
			</div>
			<?php $j++ ?>
			<div class="row form-group my-3">
				<div class="form-row mx-auto">
					<a href="#" onclick="addFields();return false;"><?= I18N::translate('Add more fields') ?></a>
				</div>
			</div>
		</div>
		<?php $j++ ?>
		<div class="row form-group my-3">
			<div class="form-row mx-auto">
				<input type="submit" class="btn btn-primary" value="<?=  /* I18N: A button label. */ I18N::translate('search') ?>">
			</div>
		</div>
	</form>
</div>

<?php if (true): ?>
	<?php if (empty($individuals)): ?>
		<div class="alert alert-info row">
			<?= I18N::translate('No results found.') ?>
		</div>
	<?php else: ?>
		<?= view('search-results', ['individuals' => $individuals, 'search_families' => false, 'search_individuals' => true, 'search_notes' => false, 'search_sources' => false, 'search_repositories' => false]) ?>
	<?php endif ?>
<?php endif ?>


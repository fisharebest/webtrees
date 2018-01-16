<?php use Fisharebest\Webtrees\FontAwesome; ?>
<?php use Fisharebest\Webtrees\I18N; ?>

<script>
  /**
   * This function moves the selected option up in the given select list
   *
   * @param section_name the name of the select to move the options
   */
  function move_up_block(section_name) {
    var section_select = document.getElementById(section_name);
    if (section_select) {
      if (section_select.selectedIndex <= 0) {
        return false;
      }
      var index = section_select.selectedIndex;
      var temp = new Option(section_select.options[index-1].text, section_select.options[index-1].value);
      section_select.options[index-1] = new Option(section_select.options[index].text, section_select.options[index].value);
      section_select.options[index] = temp;
      section_select.selectedIndex = index-1;
    }

    return false;
  }

  /**
   * This function moves the selected option down in the given select list
   *
   * @param section_name the name of the select to move the options
   */
  function move_down_block(section_name) {
    var section_select = document.getElementById(section_name);
    if (section_select) {
      if (section_select.selectedIndex < 0) {
        return false;
      }
      if (section_select.selectedIndex >= section_select.length - 1) {
        return false;
      }
      var index = section_select.selectedIndex;
      var temp = new Option(section_select.options[index + 1].text, section_select.options[index + 1].value);
      section_select.options[index + 1] = new Option(section_select.options[index].text, section_select.options[index].value);
      section_select.options[index] = temp;
      section_select.selectedIndex = index + 1;
    }

    return false;
  }

  /**
   * This function moves the selected option down in the given select list
   *
   * @param from_column the name of the select to move the option from
   * @param to_column the name of the select to remove the option to
   */
  function move_left_right_block(from_column, to_column) {
    var to_select = document.getElementById(to_column);
    var from_select = document.getElementById(from_column);
    if ((to_select) && (from_select)) {
      var add_option = from_select.options[from_select.selectedIndex];
      if (to_column !== "available_select") {
        to_select.options[to_select.length] = new Option(add_option.text, add_option.value);
      }
      if (from_column !== "available_select") {
        from_select.options[from_select.selectedIndex] = null; //remove from list
      }
    }

    return false;
  }
  /**
   * Select Options Javascript function
   *
   * This function selects all the options in the multiple select lists
   */
  function select_options() {
    var section_select = document.getElementById("main_select");
    var i;
    if (section_select) {
      for (i = 0; i < section_select.length; i++) {
        section_select.options[i].selected=true;
      }
    }
    section_select = document.getElementById("side_select");
    if (section_select) {
      for (i = 0; i < section_select.length; i++) {
        section_select.options[i].selected=true;
      }
    }
    return true;
  }

  /**
   * Show Block Description Javascript function
   *
   * This function shows a description for the selected option
   *
   * @param list_name the name of the select to get the option from
   */
  function show_description(list_name) {
    var list_select = document.getElementById(list_name);
    var instruct = document.getElementById("instructions");
    if (block_descr[list_select.options[list_select.selectedIndex].value] && instruct) {
      instruct.innerHTML = block_descr[list_select.options[list_select.selectedIndex].value];
    } else {
      instruct.innerHTML = block_descr["advice1"];
    }
    if (list_name === "main_select") {
      document.getElementById("available_select").selectedIndex = -1;
      document.getElementById("side_select").selectedIndex = -1;
    }
    if (list_name === "available_select") {
      document.getElementById("main_select").selectedIndex = -1;
      document.getElementById("side_select").selectedIndex = -1;
    }
    if (list_name === "side_select") {
      document.getElementById("main_select").selectedIndex = -1;
      document.getElementById("available_select").selectedIndex = -1;
    }
  }
  var block_descr = { advice1: "&nbsp;"};
	<?php foreach ($all_blocks as $block_name => $block): ?>
  block_descr[<?= json_encode($block_name) ?>] = <?= json_encode($block->getDescription()) ?>;
	<?php endforeach ?>
</script>

<h2><?= $title ?></h2>

<p>
	<?= I18N::translate('Select a block and use the arrows to move it.') ?>
</p>

<form name="config_setup" method="post" action="<?= e($url_save) ?>" onsubmit="select_options();" >
	<?= csrf_field() ?>
	<table border="1" id="change_blocks">
		<thead>
			<tr>
				<th class="descriptionbox center vmiddle" colspan="2">
					<label for="main_select">
						<?= I18N::translate('Main section blocks') ?>
					</label>
				</th>
				<th class="descriptionbox center vmiddle" colspan="3">
					<label for="available_select">
						<?= I18N::translate('Available blocks') ?>
					</label>
				</th>
				<th class="descriptionbox center vmiddle" colspan="2">
					<label for="side_select">
						<?= I18N::translate('Right section blocks') ?>
					</label>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="optionbox center vmiddle">
					<?= FontAwesome::linkIcon('arrow-up', I18N::translate('Move up'), ['class' => 'btn btn-link', 'href' => '#', 'onclick' => 'return move_up_block("main_select");']) ?>
					<br>
					<?= FontAwesome::linkIcon('arrow-end', I18N::translate('Move right'), ['class' => 'btn btn-link', 'href' => '#', 'onclick' => 'return move_left_right_block("main_select", "side_select");']) ?>
					<br>
					<?= FontAwesome::linkIcon('delete', I18N::translate('Remove'), ['class' => 'btn btn-link', 'href' => '#', 'onclick' => 'return move_left_right_block("main_select", "available_select");']) ?>
					<br>
					<?= FontAwesome::linkIcon('arrow-down', I18N::translate('Move down'), ['class' => 'btn btn-link', 'href' => '#', 'onclick' => 'return move_down_block("main_select");']) ?>
				</td>
				<td class="optionbox center">
					<select multiple="multiple" id="main_select" name="main[]" size="10" onchange="show_description('main_select');">
						<?php foreach ($main_blocks as $block_id => $block): ?>
							<option value="<?= $block_id ?>">
								<?= $all_blocks[$block->getName()]->getTitle() ?>
							</option>
						<?php endforeach ?>
					</select>
				</td>
				<td class="optionbox center vmiddle">
					<?= FontAwesome::linkIcon('arrow-start', I18N::translate('Add'), ['class' => 'btn btn-link', 'href' => '#', 'onclick' => 'return move_left_right_block("available_select", "main_select");']) ?>
				</td>
				<td class="optionbox center">
					<select multiple id="available_select" size="10" onchange="show_description('available_select');">
						<?php foreach ($all_blocks as $block_name => $block): ?>
							<option value="<?= $block_name ?>">
								<?= $block->getTitle() ?>
							</option>
						<?php endforeach ?>
					</select>
				</td>
				<td class="optionbox center vmiddle">
					<?= FontAwesome::linkIcon('arrow-end', I18N::translate('Add'), ['class' => 'btn btn-link', 'href' => '#', 'onclick' => 'return move_left_right_block("available_select", "side_select");']) ?>
				</td>
				<td class="optionbox center">
					<select multiple="multiple" id="side_select" name="side[]" size="10" onchange="show_description('side_select');">
						<?php foreach ($side_blocks as $block_id => $block): ?>
							<option value="<?= $block_id ?>">
								<?= $all_blocks[$block->getName()]->getTitle() ?>
							</option>
						<?php endforeach ?>
					</select>
				</td>
				<td class="optionbox center vmiddle">
					<?= FontAwesome::linkIcon('arrow-up', I18N::translate('Move up'), ['class' => 'btn btn-link', 'href' => '#', 'onclick' => 'return move_up_block("side_select");']) ?>
					<br>
					<?= FontAwesome::linkIcon('arrow-start', I18N::translate('Move left'), ['class' => 'btn btn-link', 'href' => '#', 'onclick' => 'return move_left_right_block("side_select", "main_select");']) ?>
					<br>
					<?= FontAwesome::linkIcon('delete', I18N::translate('Remove'), ['class' => 'btn btn-link', 'href' => '#', 'onclick' => 'return move_left_right_block("side_select", "available_select");']) ?>
					<br>
					<?= FontAwesome::linkIcon('arrow-down', I18N::translate('Move down'), ['class' => 'btn btn-link', 'href' => '#', 'onclick' => 'return move_down_block("side_select");']) ?>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td class="descriptionbox wrap" colspan="7">
					<div id="instructions">
						&nbsp;
					</div>
				</td>
			</tr>
			<tr>
				<td class="topbottombar" colspan="4">
					<?php if ($can_reset): ?>
						<label>
							<input type="checkbox" name="default">
							<?= I18N::translate('Restore the default block layout') ?>
						</label>
					<?php endif ?>
				</td>
				<td class="topbottombar" colspan="3">
					<button type="submit" class="btn btn-primary">
						<?= FontAwesome::decorativeIcon('save') ?>
						<?= I18N::translate('save') ?>
					</button>
					<a class="btn btn-secondary" href="<?= e($url_cancel) ?>">
						<?= FontAwesome::decorativeIcon('cancel') ?>
						<?= I18N::translate('cancel') ?>
					</a>
				</td>
			</tr>
		</tfoot>
	</table>
</form>


<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsDb;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

$controller = new PageController;

// Only one of $user_id and $gedcom_id should be set
$user_id   = Filter::get('user_id', WT_REGEX_INTEGER, Filter::post('user_id', WT_REGEX_INTEGER));
$gedcom_id = Filter::get('gedcom_id', WT_REGEX_INTEGER, Filter::post('gedcom_id', WT_REGEX_INTEGER));
if ($user_id) {
	$gedcom_id = null;
	if ($user_id < 0) {
		$controller->setPageTitle(I18N::translate('Set the default blocks for new users'));
		$can_reset = false;
	} else {
		$controller->setPageTitle(I18N::translate('Change the “My page” blocks'));
		$can_reset = true;
	}
} else {
	if ($gedcom_id < 0) {
		$controller->setPageTitle(I18N::translate('Set the default blocks for new family trees'));
		$can_reset = false;
	} else {
		$controller->setPageTitle(I18N::translate('Change the “Home page” blocks'));
		$can_reset = true;
	}
}

// Only an admin can edit the "default" page
// Only managers can edit the "home page"
// Only a user or an admin can edit a user’s "my page"
if (
	$gedcom_id < 0 && !Auth::isAdmin() ||
	$gedcom_id > 0 && !Auth::isManager(Tree::findById($gedcom_id)) ||
	$user_id && Auth::id() != $user_id && !Auth::isAdmin()
) {
	header('Location: ' . WT_BASE_URL);

	return;
}

$action = Filter::get('action');

if ($can_reset && Filter::post('default') === '1') {
	if ($user_id) {
		$defaults = FunctionsDb::getUserBlocks(-1);
	} else {
		$defaults = FunctionsDb::getTreeBlocks(-1);
	}
	$main  = $defaults['main'];
	$right = $defaults['side'];
} else {
	if (isset($_REQUEST['main'])) {
		$main = $_REQUEST['main'];
	} else {
		$main = [];
	}

	if (isset($_REQUEST['side'])) {
		$side = $_REQUEST['side'];
	} else {
		$side = [];
	}
}

$all_blocks = [];
foreach (Module::getActiveBlocks($WT_TREE) as $name => $block) {
	if ($user_id && $block->isUserBlock() || $gedcom_id && $block->isGedcomBlock()) {
		$all_blocks[$name] = $block;
	}
}

if ($user_id) {
	$blocks = FunctionsDb::getUserBlocks($user_id);
} else {
	$blocks = FunctionsDb::getTreeBlocks($gedcom_id);
}

if ($action === 'update') {
	foreach (['main', 'side'] as $location) {
		if ($location === 'main') {
			$new_blocks = $main;
		} else {
			$new_blocks = $side;
		}
		foreach ($new_blocks as $order => $block_name) {
			if (is_numeric($block_name)) {
				// existing block
				Database::prepare("UPDATE `##block` SET block_order=? WHERE block_id=?")->execute([$order, $block_name]);
				// existing block moved location
				Database::prepare("UPDATE `##block` SET location=? WHERE block_id=?")->execute([$location, $block_name]);
			} else {
				// new block
				if ($user_id) {
					Database::prepare("INSERT INTO `##block` (user_id, location, block_order, module_name) VALUES (?, ?, ?, ?)")->execute([$user_id, $location, $order, $block_name]);
				} else {
					Database::prepare("INSERT INTO `##block` (gedcom_id, location, block_order, module_name) VALUES (?, ?, ?, ?)")->execute([$gedcom_id, $location, $order, $block_name]);
				}
			}
		}
		// deleted blocks
		foreach ($blocks[$location] as $block_id => $block_name) {
			if (!in_array($block_id, $main) && !in_array($block_id, $side)) {
				Database::prepare("DELETE FROM `##block_setting` WHERE block_id=?")->execute([$block_id]);
				Database::prepare("DELETE FROM `##block`         WHERE block_id=?")->execute([$block_id]);
			}
		}
	}
	if ($user_id < 0 || $gedcom_id < 0 ) {
		header('Location: ' . WT_BASE_URL . 'admin.php');
	} elseif ($user_id > 0) {
		header('Location: ' . WT_BASE_URL . 'index.php?ctype=user&ged=' . $WT_TREE->getNameUrl());
	} else {
		header('Location: ' . WT_BASE_URL . 'index.php?ctype=gedcom&ged=' . $WT_TREE->getNameUrl());
	}

	return;
}

$controller->pageHeader();

?>

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
    block_descr["<?= $block_name ?>"] = "<?= Filter::escapeJs($block->getDescription()) ?>";
  <?php endforeach ?>
</script>

<h2><?= $controller->getPageTitle() ?></h2>

<p>
	<?= I18N::translate('Select a block and use the arrows to move it.') ?>
</p>

<form name="config_setup" method="post" action="index_edit.php?action=update" onsubmit="select_options();" >
	<input type="hidden" name="user_id"   value="<?= $user_id ?>">
	<input type="hidden" name="gedcom_id" value="<?= $gedcom_id ?>">
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
						<?php foreach ($blocks['main'] as $block_id => $block_name): ?>
							<option value="<?= $block_id ?>">
								<?= $all_blocks[$block_name]->getTitle() ?>
							</option>
						<?php endforeach ?>
					</select>
				</td>
				<td class="optionbox center vmiddle">
					<?= FontAwesome::linkIcon('arrow-start', I18N::translate('Add'), ['class' => 'btn btn-link', 'href' => '#', 'onclick' => 'return move_left_right_block("available_select", "main_select");']) ?>
				</td>
				<td class="optionbox center">
					<select id="available_select" name="available[]" size="10" onchange="show_description('available_select');">
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
						<?php foreach ($blocks['side'] as $block_id => $block_name): ?>
							<option value="<?= $block_id ?>">
								<?= $all_blocks[$block_name]->getTitle() ?>
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
						<input type="checkbox" name="default" value="1">
						<?= I18N::translate('Restore the default block layout') ?>
					</label>
				<?php endif ?>
				</td>
				<td class="topbottombar" colspan="3">
					<button type="submit" class="btn btn-primary">
						<?= I18N::translate('save') ?>
						<?= FontAwesome::decorativeIcon('save') ?>
					</button>
				</td>
			</tr>
		</tfoot>
	</table>
</form>

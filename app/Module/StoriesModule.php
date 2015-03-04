<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

/**
 * Class StoriesModule
 */
class StoriesModule extends Module implements ModuleTabInterface, ModuleConfigInterface, ModuleMenuInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Stories');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Stories” module */ I18N::translate('Add narrative stories to individuals in the family tree.');
	}

	/** {@inheritdoc} */
	public function modAction($mod_action) {
		switch ($mod_action) {
		case 'admin_edit':
			$this->edit();
			break;
		case 'admin_delete':
			$this->delete();
			$this->config();
			break;
		case 'admin_config':
			$this->config();
			break;
		case 'show_list':
			$this->showList();
			break;
		default:
			http_response_code(404);
		}
	}

	/** {@inheritdoc} */
	public function getConfigLink() {
		return 'module.php?mod=' . $this->getName() . '&amp;mod_action=admin_config';
	}

	/** {@inheritdoc} */
	public function defaultTabOrder() {
		return 55;
	}

	/** {@inheritdoc} */
	public function getTabContent() {
		global $controller;

		$block_ids =
			Database::prepare(
				"SELECT block_id" .
				" FROM `##block`" .
				" WHERE module_name=?" .
				" AND xref=?" .
				" AND gedcom_id=?"
			)->execute(array(
				$this->getName(),
				$controller->record->getXref(),
				WT_GED_ID
			))->fetchOneColumn();

		$html = '';
		foreach ($block_ids as $block_id) {
			// Only show this block for certain languages
			$languages = get_block_setting($block_id, 'languages');
			if (!$languages || in_array(WT_LOCALE, explode(',', $languages))) {
				$html .= '<div class="story_title descriptionbox center rela">' . get_block_setting($block_id, 'title') . '</div>';
				$html .= '<div class="story_body optionbox">' . get_block_setting($block_id, 'story_body') . '</div>';
				if (WT_USER_CAN_EDIT) {
					$html .= '<div class="story_edit"><a href="module.php?mod=' . $this->getName() . '&amp;mod_action=admin_edit&amp;block_id=' . $block_id . '">';
					$html .= I18N::translate('Edit story') . '</a></div>';
				}
			}
		}
		if (WT_USER_GEDCOM_ADMIN && !$html) {
			$html .= '<div class="news_title center">' . $this->getTitle() . '</div>';
			$html .= '<div><a href="module.php?mod=' . $this->getName() . '&amp;mod_action=admin_edit&amp;xref=' . $controller->record->getXref() . '">';
			$html .= I18N::translate('Add a story') . '</a></div><br>';
		}

		return $html;
	}

	/** {@inheritdoc} */
	public function hasTabContent() {
		return $this->getTabContent() <> '';
	}

	/** {@inheritdoc} */
	public function isGrayedOut() {
		global $controller;

		$count_of_stories =
			Database::prepare(
				"SELECT COUNT(block_id)" .
				" FROM `##block`" .
				" WHERE module_name=?" .
				" AND xref=?" .
				" AND gedcom_id=?"
			)->execute(array(
				$this->getName(),
				$controller->record->getXref(),
				WT_GED_ID
			))->fetchOne();

		return $count_of_stories == 0;
	}

	/** {@inheritdoc} */
	public function canLoadAjax() {
		return false;
	}

	/** {@inheritdoc} */
	public function getPreLoadContent() {
		return '';
	}

	/**
	 * Show and process a form to edit a story.
	 */
	private function edit() {
		if (WT_USER_CAN_EDIT) {
			if (Filter::postBool('save') && Filter::checkCsrf()) {
				$block_id = Filter::postInteger('block_id');
				if ($block_id) {
					Database::prepare(
						"UPDATE `##block` SET gedcom_id=?, xref=? WHERE block_id=?"
					)->execute(array(Filter::postInteger('gedcom_id'), Filter::post('xref', WT_REGEX_XREF), $block_id));
				} else {
					Database::prepare(
						"INSERT INTO `##block` (gedcom_id, xref, module_name, block_order) VALUES (?, ?, ?, ?)"
					)->execute(array(
						Filter::postInteger('gedcom_id'),
						Filter::post('xref', WT_REGEX_XREF),
						$this->getName(),
						0
					));
					$block_id = Database::getInstance()->lastInsertId();
				}
				set_block_setting($block_id, 'title', Filter::post('title'));
				set_block_setting($block_id, 'story_body', Filter::post('story_body'));
				$languages = Filter::postArray('lang', null, array_keys(I18N::installedLanguages()));
				set_block_setting($block_id, 'languages', implode(',', $languages));
				$this->config();
			} else {
				$block_id = Filter::getInteger('block_id');

				$controller = new PageController;
				if ($block_id) {
					$controller->setPageTitle(I18N::translate('Edit story'));
					$title      = get_block_setting($block_id, 'title');
					$story_body = get_block_setting($block_id, 'story_body');
					$xref       = Database::prepare(
						"SELECT xref FROM `##block` WHERE block_id=?"
					)->execute(array($block_id))->fetchOne();
				} else {
					$controller->setPageTitle(I18N::translate('Add a story'));
					$title      = '';
					$story_body = '';
					$xref       = Filter::get('xref', WT_REGEX_XREF);
				}
				$controller
					->pageHeader()
					->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
					->addInlineJavascript('autocomplete();');
				if (Module::getModuleByName('ckeditor')) {
					CkeditorModule::enableEditor($controller);
				}

				?>
				<ol class="breadcrumb small">
					<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
					<li><a href="admin_modules.php"><?php echo I18N::translate('Module administration'); ?></a></li>
					<li><a href="module.php?mod=<?php echo $this->getName(); ?>&mod_action=admin_config"><?php echo $this->getTitle(); ?></a></li>
					<li class="active"><?php echo $controller->getPageTitle(); ?></li>
				</ol>

				<h1><?php echo $controller->getPageTitle(); ?></h1>
				<?php

				echo '<form name="story" method="post" action="module.php?mod=', $this->getName(), '&amp;mod_action=admin_edit">';
				echo Filter::getCsrf();
				echo '<input type="hidden" name="save" value="1">';
				echo '<input type="hidden" name="block_id" value="', $block_id, '">';
				echo '<input type="hidden" name="gedcom_id" value="', WT_GED_ID, '">';
				echo '<table id="story_module">';
				echo '<tr><th>';
				echo I18N::translate('Story title');
				echo '</th></tr><tr><td><textarea name="title" rows="1" cols="90" tabindex="2">', Filter::escapeHtml($title), '</textarea></td></tr>';
				echo '<tr><th>';
				echo I18N::translate('Story');
				echo '</th></tr><tr><td>';
				echo '<textarea name="story_body" class="html-edit" rows="10" cols="90" tabindex="2">', Filter::escapeHtml($story_body), '</textarea>';
				echo '</td></tr>';
				echo '</table><table id="story_module2">';
				echo '<tr>';
				echo '<th>', I18N::translate('Individual'), '</th>';
				echo '<th>', I18N::translate('Show this block for which languages?'), '</th>';
				echo '</tr>';
				echo '<tr>';
				echo '<td class="optionbox">';
				echo '<input data-autocomplete-type="INDI" type="text" name="xref" id="pid" size="4" value="' . $xref . '">';
				echo print_findindi_link('pid');
				if ($xref) {
					$person = Individual::getInstance($xref);
					if ($person) {
						echo ' ', $person->formatList('span');
					}
				}
				echo '</td>';
				$languages = explode(',', get_block_setting($block_id, 'languages'));
				echo '<td class="optionbox">';
				echo edit_language_checkboxes('lang', $languages);
				echo '</td></tr></table>';
				echo '<p><input type="submit" value="', I18N::translate('save'), '" tabindex="5">';
				echo '</p>';
				echo '</form>';
			}
		} else {
			header('Location: ' . WT_BASE_URL);
		}
	}

	/**
	 * Respond to a request to delete a story.
	 */
	private function delete() {
		if (WT_USER_CAN_EDIT) {
			$block_id = Filter::getInteger('block_id');

			Database::prepare(
				"DELETE FROM `##block_setting` WHERE block_id=?"
			)->execute(array($block_id));

			Database::prepare(
				"DELETE FROM `##block` WHERE block_id=?"
			)->execute(array($block_id));
		} else {
			header('Location: ' . WT_BASE_URL);
			exit;
		}
	}

	/**
	 * The admin view - list, create, edit, delete stories.
	 */
	private function config() {
		$controller = new PageController;
		$controller
			->restrictAccess(WT_USER_GEDCOM_ADMIN)
			->setPageTitle($this->getTitle())
			->pageHeader()
			->addExternalJavascript(WT_JQUERY_DATATABLES_JS_URL)
			->addExternalJavascript(WT_DATATABLES_BOOTSTRAP_JS_URL)
			->addInlineJavascript('
				jQuery("#story_table").dataTable({
					' . I18N::datatablesI18N() . ',
					autoWidth: false,
					paging: true,
					pagingType: "full_numbers",
					lengthChange: true,
					filter: true,
					info: true,
					sorting: [[0,"asc"]],
					columns: [
						/* 0-name */ null,
						/* 1-NAME */ null,
						/* 2-NAME */ { sortable:false },
						/* 3-NAME */ { sortable:false }
					]
				});
			');

		$stories = Database::prepare(
			"SELECT block_id, xref" .
			" FROM `##block` b" .
			" WHERE module_name=?" .
			" AND gedcom_id=?" .
			" ORDER BY xref"
		)->execute(array($this->getName(), WT_GED_ID))->fetchAll();

		?>
		<ol class="breadcrumb small">
			<li><a href="admin.php"><?php echo I18N::translate('Control panel'); ?></a></li>
			<li><a href="admin_modules.php"><?php echo I18N::translate('Module administration'); ?></a></li>
			<li class="active"><?php echo $controller->getPageTitle(); ?></li>
		</ol>

		<h1><?php echo $controller->getPageTitle(); ?></h1>

		<form class="form form-inline">
			<label for="ged" class="sr-only">
				<?php echo I18N::translate('Family tree'); ?>
			</label>
			<input type="hidden" name="mod" value="<?php echo  $this->getName(); ?>">
			<input type="hidden" name="mod_action" value="admin_config">
			<?php echo select_edit_control('ged', Tree::getNameList(), null, WT_GEDCOM, 'class="form-control"'); ?>
			<input type="submit" class="btn btn-primary" value="<?php echo I18N::translate('show'); ?>">
		</form>

		<p>
			<a href="module.php?mod=<?php echo $this->getName(); ?>&amp;mod_action=admin_edit" class="btn btn-default">
				<i class="fa fa-plus"></i>
				<?php echo I18N::translate('Add a story'); ?>
			</a>
		</p>

		<table class="table table-bordered table-condensed">
			<thead>
				<tr>
					<th><?php echo I18N::translate('Story title'); ?></th>
					<th><?php echo I18N::translate('Individual'); ?></th>
					<th><?php echo I18N::translate('Edit'); ?></th>
					<th><?php echo I18N::translate('Delete'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($stories as $story): ?>
				<tr>
					<td>
						<?php echo Filter::escapeHtml(get_block_setting($story->block_id, 'title')); ?>
					</td>
					<td>
						<?php if ($indi = Individual::getInstance($story->xref)): ?>
						<a href="<?php echo $indi->getHtmlUrl(); ?>#stories">
							<?php echo $indi->getFullName(); ?>
						</a>
						<?php else: ?>
							<?php echo $story->xref; ?>
						<?php endif; ?>
						</td>
						<td>
							<a href="module.php?mod=<?php echo $this->getName(); ?>&amp;mod_action=admin_edit&amp;block_id=<?php echo $story->block_id; ?>">
								<div class="icon-edit">&nbsp;</div>
							</a>
						</td>
						<td>
							<a
								href="module.php?mod=<?php echo $this->getName(); ?>&amp;mod_action=admin_delete&amp;block_id=<?php echo $story->block_id; ?>"
								onclick="return confirm('<?php echo I18N::translate('Are you sure you want to delete this story?'); ?>');"
							>
								<div class="icon-delete">&nbsp;</div>
							</a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Show the list of stories
	 */
	private function showList() {
		global $controller;

		$controller = new PageController;
		$controller
			->setPageTitle($this->getTitle())
			->pageHeader()
			->addExternalJavascript(WT_JQUERY_DATATABLES_JS_URL)
			->addInlineJavascript('
				jQuery("#story_table").dataTable({
					dom: \'<"H"pf<"dt-clear">irl>t<"F"pl>\',
					' . I18N::datatablesI18N() . ',
					autoWidth: false,
					paging: true,
					pagingType: "full_numbers",
					lengthChange: true,
					filter: true,
					info: true,
					jQueryUI: true,
					sorting: [[0,"asc"]],
					columns: [
						/* 0-name */ null,
						/* 1-NAME */ null
					]
				});
			');

		$stories = Database::prepare(
			"SELECT block_id, xref" .
			" FROM `##block` b" .
			" WHERE module_name=?" .
			" AND gedcom_id=?" .
			" ORDER BY xref"
		)->execute(array($this->getName(), WT_GED_ID))->fetchAll();

		echo '<h2 class="center">', I18N::translate('Stories'), '</h2>';
		if (count($stories) > 0) {
			echo '<table id="story_table" class="width100">';
			echo '<thead><tr>
				<th>', I18N::translate('Story title'), '</th>
				<th>', I18N::translate('Individual'), '</th>
				</tr></thead>
				<tbody>';
			foreach ($stories as $story) {
				$indi        = Individual::getInstance($story->xref);
				$story_title = get_block_setting($story->block_id, 'title');
				$languages   = get_block_setting($story->block_id, 'languages');
				if (!$languages || in_array(WT_LOCALE, explode(',', $languages))) {
					if ($indi) {
						if ($indi->canShow()) {
							echo '<tr><td><a href="' . $indi->getHtmlUrl() . '#stories">' . $story_title . '</a></td><td><a href="' . $indi->getHtmlUrl() . '#stories">' . $indi->getFullName() . '</a></td></tr>';
						}
					} else {
						echo '<tr><td>', $story_title, '</td><td class="error">', $story->xref, '</td></tr>';
					}
				}
			}
			echo '</tbody></table>';
		}
	}

	/** {@inheritdoc} */
	public function defaultMenuOrder() {
		return 30;
	}

	/** {@inheritdoc} */
	public function defaultAccessLevel() {
		return WT_PRIV_HIDE;
	}

	/** {@inheritdoc} */
	public function getMenu() {
		if (Auth::isSearchEngine()) {
			return null;
		}

		$menu = new Menu($this->getTitle(), 'module.php?mod=' . $this->getName() . '&amp;mod_action=show_list', 'menu-story');

		return $menu;
	}
}

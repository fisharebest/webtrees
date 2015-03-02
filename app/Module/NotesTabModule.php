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
 * Class NotesTabModule
 */
class NotesTabModule extends Module implements ModuleTabInterface {
	private $facts;

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Notes');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Notes” module */ I18N::translate('A tab showing the notes attached to an individual.');
	}

	/** {@inheritdoc} */
	public function defaultTabOrder() {
		return 40;
	}

	/** {@inheritdoc} */
	public function hasTabContent() {
		return WT_USER_CAN_EDIT || $this->getFactsWithNotes();
	}

	/** {@inheritdoc} */
	public function isGrayedOut() {
		return !$this->getFactsWithNotes();
	}

	/** {@inheritdoc} */
	public function getTabContent() {
		global $WT_TREE, $controller;

		ob_start();
		echo '<table class="facts_table">';
		?>
		<tr>
			<td colspan="2" class="descriptionbox rela">
				<input id="checkbox_note2" type="checkbox" <?php echo $WT_TREE->getPreference('SHOW_LEVEL2_NOTES') ? 'checked' : ''; ?> onclick="jQuery('tr.row_note2').toggle();">
				<label for="checkbox_note2"><?php echo I18N::translate('Show all notes'); ?></label>
				<?php echo help_link('show_fact_sources'); ?>
			</td>
		</tr>
		<?php
		foreach ($this->getFactsWithNotes() as $fact) {
			if ($fact->getTag() == 'NOTE') {
				print_main_notes($fact, 1);
			} else {
				for ($i = 2; $i < 4; ++$i) {
					print_main_notes($fact, $i);
				}
			}
		}
		if (!$this->getFactsWithNotes()) {
			echo '<tr><td id="no_tab4" colspan="2" class="facts_value">', I18N::translate('There are no notes for this individual.'), '</td></tr>';
		}

		// New note link
		if ($controller->record->canEdit()) {
			?>
			<tr>
				<td class="facts_label">
					<?php echo GedcomTag::getLabel('NOTE'); ?>
				</td>
				<td class="facts_value">
					<a href="#" onclick="add_new_record('<?php echo $controller->record->getXref(); ?>','NOTE'); return false;">
						<?php echo I18N::translate('Add a new note'); ?>
					</a>
					<?php echo help_link('add_note'); ?>
				</td>
			</tr>
			<tr>
				<td class="facts_label">
					<?php echo GedcomTag::getLabel('SHARED_NOTE'); ?>
				</td>
				<td class="facts_value">
					<a href="#" onclick="add_new_record('<?php echo $controller->record->getXref(); ?>','SHARED_NOTE'); return false;">
						<?php echo I18N::translate('Add a new shared note'); ?>
					</a>
					<?php echo help_link('add_shared_note'); ?>
				</td>
			</tr>
		<?php
		}
		?>
		</table>
		<?php
		if (!$WT_TREE->getPreference('SHOW_LEVEL2_NOTES')) {
			echo '<script>jQuery("tr.row_note2").toggle();</script>';
		}

		return '<div id="' . $this->getName() . '_content">' . ob_get_clean() . '</div>';
	}

	/**
	 * Get all the facts for an individual which contain notes.
	 *
	 * @return Fact[]
	 */
	private function getFactsWithNotes() {
		global $controller;

		if ($this->facts === null) {
			$facts = $controller->record->getFacts();
			foreach ($controller->record->getSpouseFamilies() as $family) {
				if ($family->canShow()) {
					foreach ($family->getFacts() as $fact) {
						$facts[] = $fact;
					}
				}
			}
			$this->facts = array();
			foreach ($facts as $fact) {
				if (preg_match('/(?:^1|\n\d) NOTE/', $fact->getGedcom())) {
					$this->facts[] = $fact;
				}
			}
			sort_facts($this->facts);
		}

		return $this->facts;
	}

	/** {@inheritdoc} */
	public function canLoadAjax() {
		return !Auth::isSearchEngine(); // Search engines cannot use AJAX
	}

	/** {@inheritdoc} */
	public function getPreLoadContent() {
		return '';
	}
}

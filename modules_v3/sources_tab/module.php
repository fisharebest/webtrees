<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

/**
 * Class sources_tab_WT_Module
 */
class sources_tab_WT_Module extends WT_Module implements WT_Module_Tab {
	private $facts;

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Sources');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Sources” module */ WT_I18N::translate('A tab showing the sources linked to an individual.');
	}

	/** {@inheritdoc} */
	public function defaultTabOrder() {
		return 30;
	}

	/** {@inheritdoc} */
	public function hasTabContent() {
		return WT_USER_CAN_EDIT || $this->getFactsWithSources();
	}

	/** {@inheritdoc} */
	public function isGrayedOut() {
		return !$this->getFactsWithSources();
	}

	/** {@inheritdoc} */
	public function getTabContent() {
		global $SHOW_LEVEL2_NOTES, $controller;

		ob_start();
		?>
		<table class="facts_table">
			<tr>
				<td colspan="2" class="descriptionbox rela">
				<input id="checkbox_sour2" type="checkbox" <?php if ($SHOW_LEVEL2_NOTES) echo " checked=\"checked\""; ?> onclick="jQuery('tr.row_sour2').toggle();">
				<label for="checkbox_sour2"><?php echo WT_I18N::translate('Show all sources'), help_link('show_fact_sources'); ?></label>
				</td>
			</tr>
			<?php
			foreach ($this->getFactsWithSources() as $fact) {
				if ($fact->getTag() == 'SOUR') {
					print_main_sources($fact, 1);
				} else {
					print_main_sources($fact, 2);
				}
			}
			if (!$this->getFactsWithSources()) {
				echo '<tr><td id="no_tab4" colspan="2" class="facts_value">', WT_I18N::translate('There are no source citations for this individual.'), '</td></tr>';
			}

			// New Source Link
			if ($controller->record->canEdit()) {
				?>
				<tr>
					<td class="facts_label">
						<?php echo WT_Gedcom_Tag::getLabel('SOUR'); ?>
					</td>
					<td class="facts_value">
						<a href="#" onclick="add_new_record('<?php echo $controller->record->getXref(); ?>','SOUR'); return false;">
							<?php echo WT_I18N::translate('Add a new source citation'); ?>
						</a>
						<?php echo help_link('add_source'); ?>
					</td>
				</tr>
			<?php
			}
			?>
		</table>
		<?php
		if (!$SHOW_LEVEL2_NOTES) {
			echo '<script>jQuery("tr.row_sour2").toggle();</script>';
		}

		return '<div id="' . $this->getName() . '_content">' . ob_get_clean() . '</div>';
	}

	/**
	 * Get all the facts for an individual which contain sources.
	 *
	 * @return WT_Fact[]
	 */
	private function getFactsWithSources() {
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
				if (preg_match('/(?:^1|\n\d) SOUR/', $fact->getGedcom())) {
					$this->facts[] = $fact;
				}
			}
			sort_facts($this->facts);
		}

		return $this->facts;
	}

	/** {@inheritdoc} */
	public function canLoadAjax() {
		global $SEARCH_SPIDER;

		return !$SEARCH_SPIDER; // Search engines cannot use AJAX
	}

	/** {@inheritdoc} */
	public function getPreLoadContent() {
		return '';
	}
}

<?php
// Classes and libraries for module system
//
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

class media_WT_Module extends WT_Module implements WT_Module_Tab {
	private $facts;

	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Media');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Media” module */ WT_I18N::translate('A tab showing the media objects linked to an individual.');
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 50;
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		return WT_USER_CAN_EDIT || $this->getFactsWithMedia();
	}

	// Implement WT_Module_Tab
	public function isGrayedOut() {
		return !$this->getFactsWithMedia();
	}

	// Implement WT_Module_Tab
	public function getTabContent() {
		global $WT_TREE, $controller;

		ob_start();
		echo '<table class="facts_table">';
		foreach ($this->getFactsWithMedia() as $fact) {
			if ($fact->getTag() == 'OBJE') {
				print_main_media($fact, 1);
			} else {
				for ($i = 2; $i < 4; ++$i) {
					print_main_media($fact, $i);
				}
			}
		}
		if (!$this->getFactsWithMedia()) {
			echo '<tr><td id="no_tab4" colspan="2" class="facts_value">', WT_I18N::translate('There are no media objects for this individual.'), '</td></tr>';
		}
		// New media link
		if ($controller->record->canEdit() && $WT_TREE->getPreference('MEDIA_UPLOAD') >= WT_USER_ACCESS_LEVEL) {
			?>
			<tr>
				<td class="facts_label">
					<?php echo WT_Gedcom_Tag::getLabel('OBJE'); ?>
				</td>
				<td class="facts_value">
					<a href="#" onclick="window.open('addmedia.php?action=showmediaform&amp;linktoid=<?php echo $controller->record->getXref(); ?>&amp;ged=<?php echo WT_GEDURL; ?>', '_blank', edit_window_specs); return false;">
						<?php echo WT_I18N::translate('Add a new media object'); ?>
					</a>
					<?php echo help_link('OBJE'); ?>
					<br>
					<a href="#" onclick="window.open('inverselink.php?linktoid=<?php echo $controller->record->getXref(); ?>&amp;ged=<?php echo WT_GEDURL; ?>&amp;linkto=person', '_blank', find_window_specs); return false;">
						<?php echo WT_I18N::translate('Link to an existing media object'); ?>
					</a>
				</td>
			</tr>
		<?php
		}
		?>
		</table>
		<?php
		return '<div id="' . $this->getName() . '_content">' . ob_get_clean() . '</div>';
	}

	/**
	 * Get all the facts for an individual which contain media objects.
	 *
	 * @return WT_Fact[]
	 */
	private function getFactsWithMedia() {
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
				if (preg_match('/(?:^1|\n\d) OBJE @' . WT_REGEX_XREF . '@/', $fact->getGedcom())) {
					$this->facts[] = $fact;
				}
			}
			sort_facts($this->facts);
		}

		return $this->facts;
	}

	// Implement WT_Module_Tab
	public function canLoadAjax() {
		global $SEARCH_SPIDER;

		return !$SEARCH_SPIDER; // Search engines cannot use AJAX
	}

	// Implement WT_Module_Tab
	public function getPreLoadContent() {
		return '';
	}
}

<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class notes_WT_Module extends WT_Module implements WT_Module_Tab {
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('Notes');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Notes” module */ WT_I18N::translate('A tab showing the notes attached to an individual.');
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 40;
	}

	protected $noteCount = null;

	// Implement WT_Module_Tab
	public function getTabContent() {
		global $SHOW_LEVEL2_NOTES, $NAV_NOTES, $controller;

		ob_start();
		?>
		<table class="facts_table">
			<tr>
				<td colspan="2" class="descriptionbox rela">
					<input id="checkbox_note2" type="checkbox" <?php if ($SHOW_LEVEL2_NOTES) echo ' checked="checked"'; ?> onclick="jQuery('tr.row_note2').toggle();">
					<label for="checkbox_note2"><?php echo WT_I18N::translate('Show all notes'); ?></label>
					<?php echo help_link('show_fact_sources'); ?>
				</td>
			</tr>
		<?php
		$globalfacts = $controller->getGlobalFacts();
		foreach ($globalfacts as $key => $event) {
			$fact = $event->getTag();
			if ($fact=='NAME') {
				print_main_notes($event, 2, $controller->record->getXref(), true);
			}
		}
		$otherfacts = $controller->getOtherFacts();
		foreach ($otherfacts as $key => $event) {
			$fact = $event->getTag();
			if ($fact=='NOTE') {
				print_main_notes($event, 1, $controller->record->getXref());
			}
		}
		// 2nd to 5th level notes/sources
		$controller->record->add_family_facts(false);
		foreach ($controller->getIndiFacts() as $key => $factrec) {
			for ($i=2; $i<6; $i++) {
				print_main_notes($factrec, $i, $controller->record->getXref(), true);
			}
		}
		if ($this->get_note_count()==0) {
			echo '<tr><td id="no_tab2" colspan="2" class="facts_value">', WT_I18N::translate('There are no Notes for this individual.'), '</td></tr>';
		}
		//-- New Note Link
		if ($controller->record->canEdit()) {
			?>
		<tr>
			<td class="facts_label"><?php echo WT_Gedcom_Tag::getLabel('NOTE'); ?></td>
			<td class="facts_value">
				<a href="#" onclick="add_new_record('<?php echo $controller->record->getXref(); ?>','NOTE'); return false;">
					<?php echo WT_I18N::translate('Add a new note'); ?>
				</a>
				<?php echo help_link('add_note'); ?>
			</td>
		</tr>
		<tr>
			<td class="facts_label"><?php echo WT_Gedcom_Tag::getLabel('SHARED_NOTE'); ?></td>
			<td class="facts_value">
				<a href="#" onclick="add_new_record('<?php echo $controller->record->getXref(); ?>','SHARED_NOTE'); return false;">
					<?php echo WT_I18N::translate('Add a new shared note'); ?>
				</a>
				<?php echo help_link('add_shared_note'); ?>
			</td>
		</tr>
		<?php
		}
		?>
		</table>
		<br>
		<?php
		if (!$SHOW_LEVEL2_NOTES)  {
			echo '<script>jQuery("tr.row_note2").toggle();</script>';
		}
		return '<div id="'.$this->getName().'_content">'.ob_get_clean().'</div>';
	}

	function get_note_count() {
		global $controller;

		if ($this->noteCount===null) {
			$ct = preg_match_all("/\d NOTE /", $controller->record->getGedcomRecord(), $match, PREG_SET_ORDER);
			foreach ($controller->record->getSpouseFamilies() as $sfam)
			$ct += preg_match("/\d NOTE /", $sfam->getGedcomRecord());
			$this->noteCount = $ct;
		}
		return $this->noteCount;
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		return WT_USER_CAN_EDIT || $this->get_note_count()>0;
	}
	// Implement WT_Module_Tab
	public function isGrayedOut() {
		return $this->get_note_count()==0;
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

	// Implement WT_Module_Tab
	public function getJSCallback() {
		return '';
	}
}

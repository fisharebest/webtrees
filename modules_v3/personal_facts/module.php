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

class personal_facts_WT_Module extends WT_Module implements WT_Module_Tab {
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module/tab on the individual page. */ WT_I18N::translate('Facts and events');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the "Facts and events" module */ WT_I18N::translate('A tab showing the facts and events of an individual.');
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 10;
	}

	// Implement WT_Module_Tab
	public function isGrayedOut() {
		return false;
	}
	
	// Implement WT_Module_Tab
	public function getTabContent() {
		global $EXPAND_RELATIVES_EVENTS, $controller;
		$EXPAND_HISTO_EVENTS = false;

		echo '<script>';
		if (!$EXPAND_RELATIVES_EVENTS) {
			echo "jQuery('tr.row_rela').toggle();";
		}
		if (!$EXPAND_HISTO_EVENTS) {
			echo "jQuery('tr.row_histo').toggle();";
		}
		echo '</script>';

		//-- only need to add family facts on this tab
		if (!isset($controller->skipFamilyFacts)) {
			$controller->record->add_family_facts();
		}

		ob_start();
		echo '<table class="facts_table">';
		$indifacts = $controller->getIndiFacts();
		if (count($indifacts)==0) {
			echo '<tr><td colspan="2" class="facts_value">', WT_I18N::translate('There are no Facts for this individual.'), '</td></tr>';
		}
		if (!isset($controller->skipFamilyFacts)) {
			echo '<tr id="row_top"><td colspan="2" class="descriptionbox rela"><input id="checkbox_rela_facts" type="checkbox"';
			if ($EXPAND_RELATIVES_EVENTS) {
				echo ' checked="checked"';
			}
			echo 'onclick="jQuery(\'tr.row_rela\').toggle();"><label for="checkbox_rela_facts">', WT_I18N::translate('Events of close relatives'), '</label>';
			if (file_exists(get_site_setting('INDEX_DIRECTORY').'histo.'.WT_LOCALE.'.php')) {
				echo ' <input id="checkbox_histo" type="checkbox"';
				if ($EXPAND_HISTO_EVENTS) {
					echo ' checked="checked"';
				}
				echo 'onclick="jQuery(\'tr.row_histo\').toggle();"><label for="checkbox_histo">', WT_I18N::translate('Historical facts'), '</label>';
			}
			echo '</td></tr>';
		}
		foreach ($indifacts as $fact) {
			if ($fact->getFamilyId()) {
				// Print all family facts
				print_fact($fact, $controller->record);
			} else {
				// Individual/reference facts (e.g. CHAN, IDNO, RFN, AFN, REFN, RIN, _UID) can be shown in the sidebar
				if (!in_array($fact->getTag(), WT_Gedcom_Tag::getReferenceFacts()) || !array_key_exists('extra_info', WT_Module::getActiveSidebars())) {
					print_fact($fact, $controller->record);
				}
			}
		}
		//-- new fact link
		if ($controller->record->canEdit()) {
			print_add_new_fact($controller->record->getXref(), $indifacts, 'INDI');
		}
		echo '</table>';
		return '<div id="'.$this->getName().'_content">'.ob_get_clean().'</div>';
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		return true;
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

<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
// $Id: module.php 11574 2011-05-22 22:56:28Z nigel $

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
		global $FACT_COUNT, $EXPAND_RELATIVES_EVENTS;

		/*if (isset($_COOKIE['row_rela'])) $EXPAND_RELATIVES_EVENTS = ($_COOKIE['row_rela']);
		if (isset($_COOKIE['row_histo'])) $EXPAND_HISTO_EVENTS = ($_COOKIE['row_histo']);
		else*/ $EXPAND_HISTO_EVENTS = false;

		//-- only need to add family facts on this tab
		if (!isset($this->controller->skipFamilyFacts)) {
			$this->controller->indi->add_family_facts();
		}

		ob_start();
		?>
		<table class="facts_table" style="margin-top:-2px;" cellpadding="0">
		<?php if (!$this->controller->indi->canDisplayDetails()) {
			echo '<tr><td class="facts_value" colspan="2">';
			print_privacy_error();
			echo '</td></tr>';
		} else {
			$indifacts = $this->controller->getIndiFacts();
			if (count($indifacts)==0) { ?>
				<tr>
					<td id="no_tab1" colspan="2" class="facts_value"><?php echo WT_I18N::translate('There are no Facts for this individual.'); ?>
					</td>
				</tr>
			<?php }
			if (!isset($this->controller->skipFamilyFacts)) {
			?>
			<tr id="row_top">
				<td colspan="2" class="descriptionbox rela">
					<input id="checkbox_rela_facts" type="checkbox" <?php if ($EXPAND_RELATIVES_EVENTS) echo ' checked="checked"'; ?> onclick="jQuery('tr.row_rela').toggle();" />
					<label for="checkbox_rela_facts"><?php echo WT_I18N::translate('Events of close relatives'); ?></label>
					<?php if (file_exists(get_site_setting('INDEX_DIRECTORY').'histo.'.WT_LOCALE.'.php')) { ?>
						<input id="checkbox_histo" type="checkbox" <?php if ($EXPAND_HISTO_EVENTS) echo ' checked="checked"'; ?> onclick="jQuery('tr.row_histo').toggle();" />
						<label for="checkbox_histo"><?php echo WT_I18N::translate('Historical facts'); ?></label>
					<?php } ?>
				</td>
			</tr>
			<?php
			}
			$yetdied=false;
			foreach ($indifacts as $fact) {
				if (strstr(WT_EVENTS_DEAT, $fact->getTag()) && $fact->getParentObject()->getXref()==$this->controller->indi->getXref()) {
					$yetdied = true;
				}
				if (!is_null($fact->getFamilyId())) {
					if (!$yetdied) {
						print_fact($fact, $this->controller->indi);
					}
				} else {
					//$reftags = array ('CHAN', 'IDNO', 'RFN', 'AFN', 'REFN', 'RIN', '_UID');// list of tags used in "Extra information" sidebar module
					if (!in_array($fact->getTag(), WT_Gedcom_Tag::getReferenceFacts()) || !array_key_exists('extra_info', WT_Module::getActiveSidebars())) {
						print_fact($fact, $this->controller->indi);
					}
				}
				$FACT_COUNT++;
			}
		}
		//-- new fact link
		if ($this->controller->indi->canEdit()) {
			print_add_new_fact($this->controller->pid, $indifacts, 'INDI');
		}
		echo '</table><br />';
		echo WT_JS_START;
		if (!$EXPAND_RELATIVES_EVENTS) {
			echo "jQuery('tr.row_rela').toggle();";
		}
		if (!$EXPAND_HISTO_EVENTS) {
			echo "jQuery('tr.row_histo').toggle();";
		}
		echo WT_JS_END;
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

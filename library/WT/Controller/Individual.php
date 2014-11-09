<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team. All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;
use WT\User;

require_once WT_ROOT.'includes/functions/functions_print_facts.php';

/**
 * Class WT_Controller_Individual - Controller for the individual page
 */
class WT_Controller_Individual extends WT_Controller_GedcomRecord {
	public $name_count = 0;
	public $total_names = 0;

	public $tabs;

	/**
	 * Startup activity
	 */
	function __construct() {
		global $USE_RIN;

		$xref         = WT_Filter::get('pid', WT_REGEX_XREF);
		$this->record = WT_Individual::getInstance($xref);

		if (!$this->record && $USE_RIN) {
			$rin          = find_rin_id($xref);
			$this->record = WT_Individual::getInstance($rin);
		}

		parent::__construct();

		$this->tabs = WT_Module::getActiveTabs();

		// If we can display the details, add them to the page header
		if ($this->record && $this->record->canShow()) {
			$this->setPageTitle($this->record->getFullName() . ' ' . $this->record->getLifespan());
		}
	}

	/**
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return WT_Individual
	 */
	public function getSignificantIndividual() {
		if ($this->record) {
			return $this->record;
		}
		return parent::getSignificantIndividual();
	}

	/**
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return WT_Family
	 */
	public function getSignificantFamily() {
		if ($this->record) {
			foreach ($this->record->getChildFamilies() as $family) {
				return $family;
			}
			foreach ($this->record->getSpouseFamilies() as $family) {
				return $family;
			}
		}
		return parent::getSignificantFamily();
	}

	/**
	 * Handle AJAX requests - to generate the tab content
	 */
	public function ajaxRequest() {
		global $SEARCH_SPIDER;

		// Search engines should not make AJAX requests
		if ($SEARCH_SPIDER) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			exit;
		}

		// Initialise tabs
		$tab=WT_Filter::get('module');

		// A request for a non-existant tab?
		if (array_key_exists($tab, $this->tabs)) {
			$mod=$this->tabs[$tab];
		} else {
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			exit;
		}

		header("Content-Type: text/html; charset=UTF-8"); // AJAX calls do not have the meta tag headers and need this set
		header("X-Robots-Tag: noindex,follow"); // AJAX pages should not show up in search results, any links can be followed though

		Zend_Session::writeClose();

		echo $mod->getTabContent();

		if (WT_DEBUG_SQL) {
			echo WT_DB::getQueryLog();
		}
	}

	/**
	 * print information for a name record
	 *
	 * @param WT_Fact $event the event object
	 */
	public function printNameRecord(WT_Fact $event) {
		global $WT_TREE;

		$factrec = $event->getGedcom();

		// Create a dummy record, so we can extract the formatted NAME value from the event.
		$dummy=new WT_Individual(
			'xref',
			"0 @xref@ INDI\n1 DEAT Y\n".$factrec,
			null,
			WT_GED_ID
		);
		$all_names=$dummy->getAllNames();
		$primary_name=$all_names[0];

		$this->name_count++;
		if ($this->name_count >1) { echo '<h3 class="name_two">',$dummy->getFullName(), '</h3>'; } //Other names accordion element
		echo '<div class="indi_name_details';
		if ($event->isPendingDeletion()) {
			echo ' old';
		}
		if ($event->isPendingAddition()) {
			echo ' new';
		}
		echo '">';

		echo '<div class="name1">';
		echo '<dl><dt class="label">', WT_I18N::translate('Name'), '</dt>';
		$dummy->setPrimaryName(0);
		echo '<dd class="field">', $dummy->getFullName();
		if ($this->name_count == 1) {
			if (Auth::isAdmin()) {
				$user = User::findByGenealogyRecord($WT_TREE, $this->record);
				if ($user) {
					echo '<span> - <a class="warning" href="admin_users.php?filter=' . WT_Filter::escapeHtml($user->getUserName()) . '">' . WT_Filter::escapeHtml($user->getUserName()) . '</a></span>';
				}
			}
		}
		if ($this->record->canEdit() && !$event->isPendingDeletion()) {
			echo "<div class=\"deletelink\"><a class=\"deleteicon\" href=\"#\" onclick=\"return delete_fact('".WT_I18N::translate('Are you sure you want to delete this fact?')."', '".$this->record->getXref()."', '".$event->getFactId()."');\" title=\"".WT_I18N::translate('Delete this name')."\"><span class=\"link_text\">".WT_I18N::translate('Delete this name')."</span></a></div>";
			echo "<div class=\"editlink\"><a href=\"#\" class=\"editicon\" onclick=\"edit_name('".$this->record->getXref()."', '".$event->getFactId()."'); return false;\" title=\"".WT_I18N::translate('Edit name')."\"><span class=\"link_text\">".WT_I18N::translate('Edit name')."</span></a></div>";
		}
		echo '</dd>';
		echo '</dl>';
		echo '</div>';
		$ct = preg_match_all('/\n2 (\w+) (.*)/', $factrec, $nmatch, PREG_SET_ORDER);
		for ($i=0; $i<$ct; $i++) {
			echo '<div>';
				$fact = $nmatch[$i][1];
				if ($fact!='SOUR' && $fact!='NOTE' && $fact!='SPFX') {
					echo '<dl><dt class="label">', WT_Gedcom_Tag::getLabel($fact, $this->record), '</dt>';
					echo '<dd class="field">'; // Before using dir="auto" on this field, note that Gecko treats this as an inline element but WebKit treats it as a block element
					if (isset($nmatch[$i][2])) {
							$name = WT_Filter::escapeHtml($nmatch[$i][2]);
							$name = str_replace('/', '', $name);
							$name=preg_replace('/(\S*)\*/', '<span class="starredname">\\1</span>', $name);
							switch ($fact) {
							case 'TYPE':
								echo WT_Gedcom_Code_Name::getValue($name, $this->record);
								break;
							case 'SURN':
								// The SURN field is not necessarily the surname.
								// Where it is not a substring of the real surname, show it after the real surname.
								$surname = WT_Filter::escapeHtml($primary_name['surname']);
								if (strpos($primary_name['surname'], str_replace(',', ' ', $nmatch[$i][2]))!==false) {
									echo '<span dir="auto">' . $surname . '</span>';
								} else {
									echo WT_I18N::translate('%1$s (%2$s)', '<span dir="auto">' . $surname . '</span>', '<span dir="auto">' . $name . '</span>');
								}
								break;
							default:
								echo '<span dir="auto">' . $name . '</span>';
								break;
							}
						}
					echo '</dd>';
					echo '</dl>';
				}
			echo '</div>';
		}
		if (preg_match("/\n2 SOUR/", $factrec)) {
			echo '<div id="indi_sour" class="clearfloat">', print_fact_sources($factrec, 2), '</div>';
		}
		if (preg_match("/\n2 NOTE/", $factrec)) {
			echo '<div id="indi_note" class="clearfloat">', print_fact_notes($factrec, 2), '</div>';
		}
		echo '</div>';
	}

	/**
	 * print information for a sex record
	 *
	 * @param WT_Fact $event the Event object
	 */
	public function printSexRecord(WT_Fact $event) {
		$sex = $event->getValue();
		if (empty($sex)) $sex = 'U';
		echo '<span id="sex" class="';
		if ($event->isPendingDeletion()) {
			echo 'old ';
		}
		if ($event->isPendingAddition()) {
			echo 'new ';
		}
		switch ($sex) {
		case 'M':
			echo 'male_gender"';
			if ($event->canEdit()) {
				echo ' title="', WT_I18N::translate('Male'), ' - ', WT_I18N::translate('Edit'), '"';
				echo ' onclick="edit_record(\''.$this->record->getXref().'\', \''.$event->getFactId().'\'); return false;">';
			 } else {
				echo ' title="', WT_I18N::translate('Male'), '">';
			 }
			break;
		case 'F':
			echo 'female_gender"';
			if ($event->canEdit()) {
				echo ' title="', WT_I18N::translate('Female'), ' - ', WT_I18N::translate('Edit'), '"';
				echo ' onclick="edit_record(\''.$this->record->getXref().'\', \''.$event->getFactId().'\'); return false;">';
			 } else {
				echo ' title="', WT_I18N::translate('Female'), '">';
			 }
			break;
		case 'U':
			echo 'unknown_gender"';
			if ($event->canEdit()) {
				echo ' title="', WT_I18N::translate_c('unknown gender', 'Unknown'), ' - ', WT_I18N::translate('Edit'), '"';
				echo ' onclick="edit_record(\''.$this->record->getXref().'\', \''.$event->getFactId().'\'); return false;">';
			 } else {
				echo ' title="', WT_I18N::translate_c('unknown gender', 'Unknown'), '">';
			 }
			break;
		}
		echo '</span>';
	}
	/**
	 * get edit menu
	 */
	function getEditMenu() {
		global $WT_TREE;

		$SHOW_GEDCOM_RECORD = $WT_TREE->getPreference('SHOW_GEDCOM_RECORD');

		if (!$this->record || $this->record->isPendingDeletion()) {
			return null;
		}
		// edit menu
		$menu = new WT_Menu(WT_I18N::translate('Edit'), '#', 'menu-indi');
		$menu->addLabel($menu->label, 'down');

		// What behaviour shall we give the main menu?  If we leave it blank, the framework
		// will copy the first submenu - which may be edit-raw or delete.
		// As a temporary solution, make it edit the name
		$menu->addOnclick("return false;");
		if (WT_USER_CAN_EDIT) {
			foreach ($this->record->getFacts() as $fact) {
				if ($fact->getTag()=='NAME' && $fact->canEdit())
					$menu->addOnclick("return edit_name('".$this->record->getXref() . "', '" . $fact->getFactId() . "');");
					break;
			}

			$submenu = new WT_Menu(WT_I18N::translate('Add a new name'), '#', 'menu-indi-addname');
			$submenu->addOnclick("return add_name('".$this->record->getXref()."');");
			$menu->addSubmenu($submenu);

			$has_sex_record = false;
			$submenu = new WT_Menu(WT_I18N::translate('Edit gender'), '#', 'menu-indi-editsex');
			foreach ($this->record->getFacts() as $fact) {
				if ($fact->getTag()=='SEX' && $fact->canEdit()) {
					$submenu->addOnclick("return edit_record('" . $this->record->getXref() . "', '" . $fact->getFactId() . "');");
					$has_sex_record = true;
					break;
				}
			}
			if (!$has_sex_record) {
				$submenu->addOnclick("return add_new_record('" . $this->record->getXref() . "', 'SEX');");
			}
			$menu->addSubmenu($submenu);

			if (count($this->record->getSpouseFamilies())>1) {
				$submenu = new WT_Menu(WT_I18N::translate('Re-order families'), '#', 'menu-indi-orderfam');
				$submenu->addOnclick("return reorder_families('".$this->record->getXref()."');");
				$menu->addSubmenu($submenu);
			}
		}

		// delete
		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Delete'), '#', 'menu-indi-del');
			$submenu->addOnclick("return delete_individual('".WT_I18N::translate('Are you sure you want to delete “%s”?', WT_Filter::escapeJs(strip_tags($this->record->getFullName())))."', '".$this->record->getXref()."');");
			$menu->addSubmenu($submenu);
		}

		// edit raw
		if (Auth::isAdmin() || WT_USER_CAN_EDIT && $SHOW_GEDCOM_RECORD) {
			$submenu = new WT_Menu(WT_I18N::translate('Edit raw GEDCOM'), '#', 'menu-indi-editraw');
			$submenu->addOnclick("return edit_raw('" . $this->record->getXref() . "');");
			$menu->addSubmenu($submenu);
		}

		// add to favorites
		if (array_key_exists('user_favorites', WT_Module::getActiveModules())) {
			$submenu = new WT_Menu(
				/* I18N: Menu option.  Add [the current page] to the list of favorites */ WT_I18N::translate('Add to favorites'),
				'#',
				'menu-indi-addfav'
			);
			$submenu->addOnclick("jQuery.post('module.php?mod=user_favorites&amp;mod_action=menu-add-favorite',{xref:'".$this->record->getXref()."'},function(){location.reload();})");
			$menu->addSubmenu($submenu);
		}

		return $menu;
	}

	/**
	 * get the person box stylesheet class for the given person
	 *
	 * @param WT_Individual $person
	 *
	 * @return string returns 'person_box', 'person_boxF', or 'person_boxNN'
	 */
	function getPersonStyle($person) {
		switch($person->getSex()) {
			case 'M':
				$class = 'person_box';
				break;
			case 'F':
				$class = 'person_boxF';
				break;
			default:
				$class = 'person_boxNN';
				break;
		}
		if ($person->isPendingDeletion()) {
			$class .= ' old';
		} elseif ($person->isPendingAddtion()) {
			$class .= ' new';
		}
		return $class;
	}

	/**
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return string
	 */
	public function getSignificantSurname() {
		if ($this->record) {
			list($surn) = explode(',', $this->record->getSortname());
			return $surn;
		} else {
			return '';
		}
	}

	/**
	 * Get the contents of sidebar.
	 *
	 * @return string
	 */
	public function getSideBarContent() {
		global $controller;

		$html='';
		$active=0;
		$n=0;
		foreach (WT_Module::getActiveSidebars() as $mod) {
			if ($mod->hasSidebarContent()) {
				$html.='<h3 id="'.$mod->getName().'"><a href="#">'.$mod->getTitle().'</a></h3>';
				$html.='<div id="sb_content_'.$mod->getName().'">'.$mod->getSidebarContent().'</div>';
				// The family navigator should be opened by default
				if ($mod->getName()=='family_nav') {
					$active=$n;
				}
				++$n;
			}
		}

		if ($html) {
			$controller
				->addInlineJavascript('
				jQuery("#sidebarAccordion").accordion({
					active:' . $active . ',
					heightStyle: "content",
					collapsible: true,
				});
			');

			return '<div id="sidebar"><div id="sidebarAccordion">' . $html . '</div></div>';
		} else {
			return '';
		}
	}
}

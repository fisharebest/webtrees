<?php
// Controller for the individual page
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/functions/functions_print_facts.php';
require_once WT_ROOT.'includes/functions/functions_import.php';

class WT_Controller_Individual extends WT_Controller_GedcomRecord {
	var $name_count = 0;
	var $total_names = 0;
	var $SEX_COUNT = 0;
	var $Fam_Navigator = 'YES';
	var $NAME_LINENUM = null;
	var $SEX_LINENUM = null;
	var $globalfacts = null;
	public $tabs;

	function __construct() {
		global $USE_RIN, $MAX_ALIVE_AGE, $SEARCH_SPIDER;
		global $DEFAULT_PIN_STATE, $DEFAULT_SB_CLOSED_STATE;
		global $Fam_Navigator;

		$xref = safe_GET_xref('pid');

		$gedrec = find_person_record($xref, WT_GED_ID);

		if ($USE_RIN && $gedrec==false) {
			$xref = find_rin_id($xref);
			$gedrec = find_person_record($xref, WT_GED_ID);
		}
		if (empty($gedrec)) {
			$gedrec = "0 @".$xref."@ INDI\n";
		}

		if (find_person_record($xref, WT_GED_ID) || find_updated_record($xref, WT_GED_ID)!==null) {
				$this->record = new WT_Person($gedrec);
				$this->record->ged_id=WT_GED_ID; // This record is from a file
		} else if (!$this->record) {
			parent::__construct();
			return;
		}

		//-- if the user can edit and there are changes then get the new changes
		if (WT_USER_CAN_EDIT || WT_USER_CAN_ACCEPT) {
			$newrec = find_updated_record($xref, WT_GED_ID);
			if (!empty($newrec)) {
				$diff_record = new WT_Person($newrec);
				$diff_record->setChanged(true);
				$this->record->diffMerge($diff_record);
			}
		}

		$this->tabs=WT_Module::getActiveTabs();

		// Our parent needs $this->record
		parent::__construct();

		// If we can display the details, add them to the page header		
		if ($this->record && $this->record->canDisplayDetails()) {
			$this->setPageTitle($this->record->getFullName().' '.$this->record->getLifespan());
		}
	}

	// Get significant information from this page, to allow other pages such as
	// charts and reports to initialise with the same records
	public function getSignificantIndividual() {
		if ($this->record) {
			return $this->record;
		}
		return parent::getSignificantIndividual();
	}
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

	// Handle AJAX requests - to generate the tab content
	public function ajaxRequest() {
		global $SEARCH_SPIDER;

		// Search engines should not make AJAX requests
		if ($SEARCH_SPIDER) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			exit;
		}

		// Initialise tabs
		$tab=safe_GET('module');

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
	* Called from the individual information page
	* @see individual.php
	* @param Event $event the event object
	*/
	function print_name_record(WT_Event $event) {

		if (!$event->canShow()) {
			return false;
		}
		$factrec = $event->getGedComRecord();
		$linenum = $event->getLineNumber();

		// Create a dummy record, so we can extract the formatted NAME value from the event.
		$dummy=new WT_Person('0 @'.$event->getParentObject()->getXref()."@ INDI\n1 DEAT Y\n".$factrec);
		$all_names=$dummy->getAllNames();
		$primary_name=$all_names[0];
		
		$this->name_count++;
		if ($this->name_count >1) { echo '<h3 class="name_two">',$dummy->getFullName(), '</h3>'; } //Other names accordion element
		echo '<div class="indi_name_details"';
		if ($event->getIsOld()) {
			echo " class=\"namered\"";
		}
		if ($event->getIsNew()) {
			echo " class=\"nameblue\"";
		}
		echo ">";

		echo '<div class="name1">';
		echo '<dl><dt class="label">', WT_I18N::translate('Name'), '</dt>';
		$dummy->setPrimaryName(0);
		echo '<dd class="field">', $dummy->getFullName();
		if ($this->name_count == 1) {
			if (WT_USER_IS_ADMIN) {
				$user_id=get_user_from_gedcom_xref(WT_GED_ID, $this->record->getXref());
				if ($user_id) {
					$user_name=get_user_name($user_id);
					echo '<span> - <a class="warning" href="admin_users.php?filter='.$user_name.'">'.$user_name.'</a></span>';
				}
			}
		}
		if ($this->record->canEdit() && !$event->getIsOld()) {
			echo "<div class=\"deletelink\"><a class=\"font9 deleteicon\" href=\"#\" onclick=\"return delete_fact('".$this->record->getXref()."', ".$linenum.", '', '".WT_I18N::translate('Are you sure you want to delete this fact?')."');\" title=\"".WT_I18N::translate('Delete name')."\"><span class=\"link_text\">".WT_I18N::translate('Delete name')."</span></a></div>";
			echo "<div class=\"editlink\"><a href=\"#\" class=\"font9 editicon\" onclick=\"edit_name('".$this->record->getXref()."', ".$linenum."); return false;\" title=\"".WT_I18N::translate('Edit name')."\"><span class=\"link_text\">".WT_I18N::translate('Edit name')."</span></a></div>";
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
							$name = htmlspecialchars($nmatch[$i][2]);
							$name = str_replace('/', '', $name);
							$name=preg_replace('/(\S*)\*/', '<span class="starredname">\\1</span>', $name);
							switch ($fact) {
							case 'TYPE':
								echo WT_Gedcom_Code_Name::getValue($name, $this->record);
								break;
							case 'SURN':
								// The SURN field is not necessarily the surname.
								// Where it is not a substring of the real surname, show it after the real surname.
								if (strpos($primary_name['surname'], str_replace(',', ' ', $name))!==false) {
									echo $primary_name['surname'];
								} else {
									echo WT_I18N::translate('%1$s (%2$s)', $primary_name['surname'], $name);
								}
								break;
							default:
								echo $name;
								break;
							}
						}
					echo '</dd>';
					echo '</dl>';
				}
			echo '</div>';
		}
		if (preg_match("/\d (NOTE)|(SOUR)/", $factrec)>0) {
			// -- find sources for this name
			echo '<div id="indi_note" class="clearfloat">';
				print_fact_sources($factrec, 2);
				//-- find the notes for this name
				print_fact_notes($factrec, 2);
			echo '</div>';
		}
		echo '</div>';
	}

	/**
	* print information for a sex record
	*
	* Called from the individual information page
	* @see individual.php
	* @param Event $event the Event object
	*/
	function print_sex_record(WT_Event $event) {
		global $sex;

		if (!$event->canShow()) return false;
		$factrec = $event->getGedComRecord();
		$sex = $event->getDetail();
		if (empty($sex)) $sex = 'U';
		echo '<span id="sex"';
			echo ' class="';
			if ($event->getIsOld()) {
				echo 'namered ';
			}
			if ($event->getIsNew()) {
				echo 'nameblue ';
			}
			switch ($sex) {
			case 'M':
				echo 'male_gender"';
				if ($this->record->canEdit() && !$event->getIsOld()) {
					echo ' title="', WT_I18N::translate('Male'), ' - ', WT_I18N::translate('Edit'), '"';
					echo ' onclick="edit_record(\''.$this->record->getXref().'\', '.$event->getLineNumber().'); return false;">';
				 } else {
					echo ' title="', WT_I18N::translate('Male'), '">';
				 }
				break;
			case 'F':
				echo 'female_gender"';
				if ($this->record->canEdit() && !$event->getIsOld()) {
					echo ' title="', WT_I18N::translate('Female'), ' - ', WT_I18N::translate('Edit'), '"';
					echo ' onclick="edit_record(\''.$this->record->getXref().'\', '.$event->getLineNumber().'); return false;">';
				 } else {
					echo ' title="', WT_I18N::translate('Female'), '">';
				 }
				break;
			case 'U':
				echo 'unknown_gender"';
				if ($this->record->canEdit() && !$event->getIsOld()) {
					echo ' title="', WT_I18N::translate_c('unknown gender', 'Unknown'), ' - ', WT_I18N::translate('Edit'), '"';
					echo ' onclick="edit_record(\''.$this->record->getXref().'\', '.$event->getLineNumber().'); return false;">';
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
		$SHOW_GEDCOM_RECORD=get_gedcom_setting(WT_GED_ID, 'SHOW_GEDCOM_RECORD');

		if (!$this->record || $this->record->isMarkedDeleted()) {
			return null;
		}
		// edit menu
		$menu = new WT_Menu(WT_I18N::translate('Edit'), '#', 'menu-indi');
		$menu->addLabel($menu->label, 'down');

		$this->getGlobalFacts(); // sets NAME_LINENUM and SEX_LINENUM.  individual.php doesn't do it early enough for us....

		// What behaviour shall we give the main menu?  If we leave it blank, the framework
		// will copy the first submenu - which may be edit-raw or delete.
		// As a temporary solution, make it edit the name
		if (WT_USER_CAN_EDIT && $this->NAME_LINENUM) {
			$menu->addOnclick("return edit_name('".$this->record->getXref()."', ".$this->NAME_LINENUM.");");
		} else {
			$menu->addOnclick("return false;");
		}

		if (WT_USER_CAN_EDIT) {
			//--make sure the totals are correct
			$submenu = new WT_Menu(WT_I18N::translate('Add new Name'), '#', 'menu-indi-addname');
			$submenu->addOnclick("return add_name('".$this->record->getXref()."');");
			$menu->addSubmenu($submenu);

			if ($this->SEX_COUNT<2) {
				$submenu = new WT_Menu(WT_I18N::translate('Edit gender'), '#', 'menu-indi-editsex');
				if ($this->SEX_LINENUM=="new") {
					$submenu->addOnclick("return add_new_record('".$this->record->getXref()."', 'SEX');");
				} else {
					$submenu->addOnclick("return edit_record('".$this->record->getXref()."', ".$this->SEX_LINENUM.");");
				}
				$menu->addSubmenu($submenu);
			}

			if (count($this->record->getSpouseFamilies())>1) {
				$submenu = new WT_Menu(WT_I18N::translate('Re-order families'), '#', 'menu-indi-orderfam');
				$submenu->addOnclick("return reorder_families('".$this->record->getXref()."');");
				$menu->addSubmenu($submenu);
			}
		}

		// edit/view raw gedcom
		if (WT_USER_IS_ADMIN || $SHOW_GEDCOM_RECORD) {
			$submenu = new WT_Menu(WT_I18N::translate('Edit raw GEDCOM record'), '#', 'menu-indi-editraw');
			$submenu->addOnclick("return edit_raw('".$this->record->getXref()."');");
			$menu->addSubmenu($submenu);
		} elseif ($SHOW_GEDCOM_RECORD) {
			$submenu = new WT_Menu(WT_I18N::translate('View GEDCOM Record'), '#', 'menu-indi-viewraw');
			if (WT_USER_CAN_EDIT) {
				$submenu->addOnclick("return show_gedcom_record('new');");
			} else {
				$submenu->addOnclick("return show_gedcom_record();");
			}
			$menu->addSubmenu($submenu);
		}

		// delete
		if (WT_USER_CAN_EDIT) {
			$submenu = new WT_Menu(WT_I18N::translate('Delete'), '#', 'menu-indi-del');
			$submenu->addOnclick("if (confirm('".addslashes(WT_I18N::translate('Are you sure you want to delete “%s”?', strip_tags($this->record->getFullName())))."')) jQuery.post('action.php',{action:'delete-individual',xref:'".$this->record->getXref()."'},function(){location.reload();})");
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
	* get global facts
	* global facts are NAME and SEX
	* @return array return the array of global facts
	*/
	function getGlobalFacts() {
		if ($this->globalfacts==null) {
			$this->globalfacts = $this->record->getGlobalFacts();
			foreach ($this->globalfacts as $key => $value) {
				$fact = $value->getTag();
				if ($fact=="SEX") {
					$this->SEX_COUNT++;
					$this->SEX_LINENUM = $value->getLineNumber();
				}
				if ($fact=="NAME") {
					$this->total_names++;
					if ($this->NAME_LINENUM==null && !$value->getIsOld()) {
						// This is the "primary" name and is edited from the menu
						// Subsequent names get their own edit links
						$this->NAME_LINENUM = $value->getLineNumber();
					}
				}
			}
		}
		return $this->globalfacts;
	}
	/**
	* get the individual facts shown on tab 1
	* @return array
	*/
	function getIndiFacts() {
		$indifacts = $this->record->getIndiFacts();
		sort_facts($indifacts);
		return $indifacts;
	}
	/**
	* get the other facts shown on tab 2
	* @return array
	*/
	function getOtherFacts() {
		$otherfacts = $this->record->getOtherFacts();
		return $otherfacts;
	}

	/**
	* get the person box stylesheet class
	* for the given person
	* @param Person $person
	* @return string returns 'person_box', 'person_boxF', or 'person_boxNN'
	*/
	function getPersonStyle($person) {
		$sex = $person->getSex();
		switch($sex) {
			case "M":
				$isf = "";
				break;
			case "F":
				$isf = "F";
				break;
			default:
				$isf = "NN";
				break;
		}
		return "person_box".$isf;
	}

	/**
	* build an array of Person that will be used to build a list
	* of family members on the families tab
	* @param Family $family the family we are building for
	* @return array an array of Person that will be used to iterate through on the indivudal.php page
	*/
	function buildFamilyList($family, $type, $include_pedi=true) {
		$labels = array();
		switch ($type) {
		case 'parents':
			$labels["parent" ] = get_relationship_name_from_path('par', null, null);
			$labels["mother" ] = get_relationship_name_from_path('mot', null, null);
			$labels["father" ] = get_relationship_name_from_path('fat', null, null);
			$labels["sibling"] = get_relationship_name_from_path('sib', null, null);
			$labels["sister" ] = get_relationship_name_from_path('sis', null, null);
			$labels["brother"] = get_relationship_name_from_path('bro', null, null);
			break;
		case 'step-parents':
			$labels["parent" ] = get_relationship_name_from_path('parspo', null, null);
			$labels["mother" ] = get_relationship_name_from_path('fatwif', null, null);
			$labels["father" ] = get_relationship_name_from_path('mothus', null, null);
			$labels["sibling"] = get_relationship_name_from_path('parchi', null, null);
			$labels["sister" ] = get_relationship_name_from_path('pardau', null, null);
			$labels["brother"] = get_relationship_name_from_path('parson', null, null);
			break;
		case 'spouse':
			if ($family->isNotMarried()) {
				$labels["parent"] = /* I18N: A life partner - like a spouse, but not married */  WT_I18N::translate_c('MALE/FEMALE', 'partner');
				$labels["mother"] = /* I18N: A life partner - like a wife, but not married */    WT_I18N::translate_c('FEMALE',      'partner');
				$labels["father"] = /* I18N: A life partner - like a husband, but not married */ WT_I18N::translate_c('MALE',        'partner');
			} elseif ($family->isDivorced()) {
				$labels["parent"] = /* I18N: A previous spouse, now divorced  */ WT_I18N::translate('ex-spouse');
				$labels["mother"] = /* I18N: A previous wife, now divorced    */ WT_I18N::translate('ex-wife');
				$labels["father"] = /* I18N: A previous husband, now divorced */ WT_I18N::translate('ex-husband');
			} else {
				$marr_rec = $family->getMarriageRecord();
				if (!empty($marr_rec)) {
					$type = $family->getMarriageType();
					if (empty($type) || stristr($type, "partner")===false) {
						$labels["parent"] = get_relationship_name_from_path('spo', null, null);
						$labels["mother"] = get_relationship_name_from_path('wif', null, null);
						$labels["father"] = get_relationship_name_from_path('hus', null, null);
					} else {
						$labels["parent"] = WT_I18N::translate_c('MALE/FEMALE', 'partner');
						$labels["mother"] = WT_I18N::translate_c('FEMALE',      'partner');
						$labels["father"] = WT_I18N::translate_c('MALE',        'partner');
					}
				} else {
					$labels["parent"] = get_relationship_name_from_path('spo', null, null);
					$labels["mother"] = get_relationship_name_from_path('wif', null, null);
					$labels["father"] = get_relationship_name_from_path('hus', null, null);
				}
			}
			$labels["sibling"] = get_relationship_name_from_path('chi', null, null);
			$labels["sister" ] = get_relationship_name_from_path('dau', null, null);
			$labels["brother"] = get_relationship_name_from_path('son', null, null);
			break;
		case 'step-children':
			if ($this->record->equals($family->getHusband())) {
				$labels["parent"] = '';
				$labels["mother"] = '';
				$labels["father"] = get_relationship_name_from_path('hus', null, null);
			} else {
				$labels["parent"] = '';
				$labels["mother"] = get_relationship_name_from_path('wif', null, null);
				$labels["father"] = '';
			}
			$labels["sibling"] = WT_I18N::translate_c('spouses\'s child',    'step-child');
			$labels["sister" ] = WT_I18N::translate_c('spouses\'s daughter', 'step-daughter');
			$labels["brother"] = WT_I18N::translate_c('spouses\'s son',      'step-son');
			break;
		}
		$newhusb = null;
		$newwife = null;
		$newchildren = array();
		$delchildren = array();
		$children = array();
		$husb = null;
		$wife = null;
		if (!$family->getChanged()) {
			$husb = $family->getHusband();
			$wife = $family->getWife();
			$children = $family->getChildren();
		}
		//-- step families : set the label for the common parent
		if ($type=="step-parents") {
			$fams = $this->record->getChildFamilies();
			foreach ($fams as $key=>$fam) {
				if ($fam->hasParent($husb)) $labels["father"] = get_relationship_name_from_path('fat', null, null);
				if ($fam->hasParent($wife)) $labels["mother"] = get_relationship_name_from_path('mot', null, null);
			}
		}
		//-- set the label for the husband
		if (!is_null($husb)) {
			$label = $labels["parent"];
			$sex = $husb->getSex();
			if ($sex=="F") {
				$label = $labels["mother"];
			}
			if ($sex=="M") {
				$label = $labels["father"];
			}
			if ($husb->getXref()==$this->record->getXref()) {
				$label = '<i class="icon-selected"></i>';
			}
			$husb->setLabel($label);
		}
		//-- set the label for the wife
		if (!is_null($wife)) {
			$label = $labels["parent"];
			$sex = $wife->getSex();
			if ($sex=="F") {
				$label = $labels["mother"];
			}
			if ($sex=="M") {
				$label = $labels["father"];
			}
			if ($wife->getXref()==$this->record->getXref()) {
				$label = '<i class="icon-selected"></i>';
			}
			$wife->setLabel($label);
		}
		if (WT_USER_CAN_EDIT || WT_USER_CAN_ACCEPT) {
			$newfamily = $family->getUpdatedFamily();
			if (!is_null($newfamily)) {
				$newhusb = $newfamily->getHusband();
				//-- check if the husband in the family has changed
				if (!is_null($newhusb) && !$newhusb->equals($husb)) {
					$label = $labels["parent"];
					$sex = $newhusb->getSex();
					if ($sex=="F") {
						$label = $labels["mother"];
					}
					if ($sex=="M") {
						$label = $labels["father"];
					}
					if ($newhusb->getXref()==$this->record->getXref()) {
						$label = '<i class="icon-selected"></i>';
					}
					$newhusb->setLabel($label);
				}
				else $newhusb = null;
				$newwife = $newfamily->getWife();
				//-- check if the wife in the family has changed
				if (!is_null($newwife) && !$newwife->equals($wife)) {
					$label = $labels["parent"];
					$sex = $newwife->getSex();
					if ($sex=="F") {
						$label = $labels["mother"];
					}
					if ($sex=="M") {
						$label = $labels["father"];
					}
					if ($newwife->getXref()==$this->record->getXref()) {
						$label = '<i class="icon-selected"></i>';
					}
					$newwife->setLabel($label);
				}
				else $newwife = null;
				//-- check for any new children
				$merged_children = array();
				$new_children = $newfamily->getChildren();
				$num = count($children);
				for ($i=0; $i<$num; $i++) {
					$child = $children[$i];
					if (!is_null($child)) {
						$found = false;
						foreach ($new_children as $key=>$newchild) {
							if (!is_null($newchild)) {
								if ($child->equals($newchild)) {
									$found = true;
									break;
								}
							}
						}
						if (!$found) $delchildren[] = $child;
						else $merged_children[] = $child;
					}
				}
				foreach ($new_children as $key=>$newchild) {
					if (!is_null($newchild)) {
						$found = false;
						foreach ($children as $key1=>$child) {
							if (!is_null($child)) {
								if ($child->equals($newchild)) {
									$found = true;
									break;
								}
							}
						}
						if (!$found) $newchildren[] = $newchild;
					}
				}
				$children = $merged_children;
			}
		}
		//-- set the labels for the children
		$num = count($children);
		for ($i=0; $i<$num; $i++) {
			if (!is_null($children[$i])) {
				$label = $labels["sibling"];
				$sex = $children[$i]->getSex();
				if ($sex=="F") {
					$label = $labels["sister"];
				}
				if ($sex=="M") {
					$label = $labels["brother"];
				}
				if ($children[$i]->getXref()==$this->record->getXref()) {
					$label = '<i class="icon-selected"></i>';
				}
				if ($include_pedi==true) {
					$famcrec = get_sub_record(1, "1 FAMC @".$family->getXref()."@", $children[$i]->getGedcomRecord());
					$pedi = get_gedcom_value("PEDI", 2, $famcrec);
					if ($pedi) {
						$label.='<br>('.WT_Gedcom_Code_Pedi::getValue($pedi, $children[$i]).')';
					}
				}
				$children[$i]->setLabel($label);
			}
		}
		$num = count($newchildren);
		for ($i=0; $i<$num; $i++) {
			$label = $labels["sibling"];
			$sex = $newchildren[$i]->getSex();
			if ($sex=="F") {
				$label = $labels["sister"];
			}
			if ($sex=="M") {
				$label = $labels["brother"];
			}
			if ($newchildren[$i]->getXref()==$this->record->getXref()) {
				$label = '<i class="icon-selected"></i>';
			}
			if ($include_pedi==true) {
				$pedi = $newchildren[$i]->getChildFamilyPedigree($family->getXref());
				if ($pedi) {
					$label.='<br>('.WT_Gedcom_Code_Pedi::getValue($pedi, $newchildren[$i]).')';
				}
			}
			$newchildren[$i]->setLabel($label);
		}
		$num = count($delchildren);
		for ($i=0; $i<$num; $i++) {
				$label = $labels["sibling"];
			$sex = $delchildren[$i]->getSex();
			if ($sex=="F") {
				$label = $labels["sister"];
			}
			if ($sex=="M") {
				$label = $labels["brother"];
			}
			if ($delchildren[$i]->getXref()==$this->record->getXref()) {
				$label = '<i class="icon-selected"></i>';
			}
			if ($include_pedi==true) {
				$pedi = $delchildren[$i]->getChildFamilyPedigree($family->getXref());
				if ($pedi) {
					$label.='<br>('.WT_Gedcom_Code_Pedi::getValue($pedi, $delchildren[$i]).')';
				}
			}
			$delchildren[$i]->setLabel($label);
			
		}

		$people = array();
		if (!is_null($newhusb)) $people['newhusb'] = $newhusb;
		if (!is_null($husb)) $people['husb'] = $husb;
		if (!is_null($newwife)) $people['newwife'] = $newwife;
		if (!is_null($wife)) $people['wife'] = $wife;
		$people['children'] = $children;
		$people['newchildren'] = $newchildren;
		$people['delchildren'] = $delchildren;
		return $people;
	}

	// Get significant information from this page, to allow other pages such as
	// charts and reports to initialise with the same records
	public function getSignificantSurname() {
		if ($this->record) {
			list($surn, $givn)=explode(',', $this->record->getSortname());
			return $surn;
		} else {
			return '';
		}
	}

	// Get the contents of sidebar.
	// TODO?? - only load one block immediately - load the others by AJAX.
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

		$controller
			->addInlineJavascript('
				jQuery("#sidebarAccordion").accordion({
					active:' . $active . ',
					heightStyle: "content",
					collapsible: true,
					icons:{ "header": "ui-icon-triangle-1-s", "headerSelected": "ui-icon-triangle-1-n" }
				});
			');

		return '<div id="sidebar"><div id="sidebarAccordion">'.$html.'</div></div>';
	}

// -----------------------------------------------------------------------------
// Functions for GedFact Assistant
// -----------------------------------------------------------------------------
	/**
	* include GedFact controller
	*/
	function census_assistant() {
		require WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/_CENS/census_1_ctrl.php';
	}
	function medialink_assistant() {
		require WT_ROOT.WT_MODULES_DIR.'GEDFact_assistant/_MEDIA/media_1_ctrl.php';
	}
// -----------------------------------------------------------------------------
// End GedFact Assistant Functions
// -----------------------------------------------------------------------------
}

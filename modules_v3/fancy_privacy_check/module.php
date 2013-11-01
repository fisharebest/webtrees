<?php
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class fancy_privacy_check_WT_Module extends WT_Module implements WT_Module_Sidebar {
	
	public function __construct() {
		// Load any local user translations
		if (is_dir(WT_MODULES_DIR.$this->getName().'/language')) {
			if (file_exists(WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.mo')) {
				Zend_Registry::get('Zend_Translate')->addTranslation(
					new Zend_Translate('gettext', WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.mo', WT_LOCALE)
				);
			}
			if (file_exists(WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.php')) {
				Zend_Registry::get('Zend_Translate')->addTranslation(
					new Zend_Translate('array', WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.php', WT_LOCALE)
				);
			}
			if (file_exists(WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.csv')) {
				Zend_Registry::get('Zend_Translate')->addTranslation(
					new Zend_Translate('csv', WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.csv', WT_LOCALE)
				);
			}
		}
	}
	
	// Extend WT_Module
	public function getTitle() {
		return /* Name of a module (not translatable) */ 'Fancy Privacy Check';
	}
	
	public function getSidebarTitle() {
		return /* Title used in the sidebar */ 'Privacy Check';
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the "Extra information" module */ WT_I18N::translate('A sidebar tool to show the privacy status of an individual.');
	}

	// Implement WT_Module_Sidebar
	public function defaultSidebarOrder() {
		return 15;
	}

	// Implement WT_Module_Sidebar
	public function hasSidebarContent() {
		return true;
	}

	// Implement WT_Module_Sidebar
	public function getSidebarContent() {
		// code based on similar in function_print_list.php
		global $controller, $MAX_ALIVE_AGE, $SHOW_EST_LIST_DATES, $SEARCH_SPIDER;	
		$SHOW_EST_LIST_DATES = get_gedcom_setting(WT_GED_ID, 'SHOW_EST_LIST_DATES');
		
		$html = $this->includeCss();
		
		$controller->addInlineJavascript('			
			jQuery(document).ajaxSend(function(){
				jQuery("#'.$this->getName().' a").text("'.$this->getSidebarTitle().'");
			});
		');	 
		
		$html .= '<dl id="privacy_status">';
		if ($death_dates=$controller->record->getAllDeathDates()) {
			$html .= '<dt>' .WT_I18N::translate('Dead').help_link('privacy_status',$this->getName()). '</dt>';
			foreach ($death_dates as $num=>$death_date) {
				if ($num) {
					$html .= ' | ';
				}
				$html .= '<dd>' .WT_I18N::translate('Death recorded as %s', $death_date->Display(!$SEARCH_SPIDER)). '</dd>';
			}
		} else {
			$death_date=$controller->record->getEstimatedDeathDate();
			if (!$death_date && $SHOW_EST_LIST_DATES) {
				$html .= '<dt>' .WT_I18N::translate('Presumed dead').help_link('privacy_status',$this->getName()). '</dt>';
				$html .= '<dd>' .WT_I18N::translate('An estimated death date has been calculated as %s', $death_date->Display(!$SEARCH_SPIDER)). '</dd>';
			} else if ($controller->record->isDead()) {
				$html .= '<dt>' .WT_I18N::translate('Presumed dead').help_link('privacy_status',$this->getName()). '</dt>';
				$html .= '<dd>' .$this->fpc_isDead(). '</dd>';
			} else {
				$html .= '<dt>' .WT_I18N::translate('Living').help_link('privacy_status',$this->getName()). '</dt>';
			}
			$death_dates[0]=new WT_Date('');
		}
		$html .= '</dl>';
		return $html;
	}
	
	// Implement WT_Module_Sidebar
	public function getSidebarAjaxContent() {
		return false;
	}
	
	// This is a copy, with modifications, of the function isDead() in /library/WT/Individual.php
	// It is VERY important that the parameters used in both are identical.
	private function fpc_isDead() {
		global $MAX_ALIVE_AGE, $SEARCH_SPIDER;
		$controller=new WT_Controller_Individual();
		
		// "1 DEAT Y" or "1 DEAT/2 DATE" or "1 DEAT/2 PLAC"
		if (preg_match('/\n1 (?:'.WT_EVENTS_DEAT.')(?: Y|(?:\n[2-9].+)*\n2 (DATE|PLAC) )/', $controller->record->getGedcom())) {
			return WT_I18N::translate('Death is recorded with an unknown date.');
		}
		
		// If any event occured more than $MAX_ALIVE_AGE years ago, then assume the individual is dead
		if (preg_match_all('/\n2 DATE (.+)/', $controller->record->getGedcom(), $date_matches)) {
			foreach ($date_matches[1] as $date_match) {
				$date=new WT_Date($date_match);
				if ($date->isOK() && $date->MaxJD() <= WT_CLIENT_JD - 365*$MAX_ALIVE_AGE) {
					return WT_I18N::translate('An event occurred in this person\'s life more than %s years ago<br> %s', $MAX_ALIVE_AGE, $date->Display(!$SEARCH_SPIDER));
				}
			}
			// The individual has one or more dated events.  All are less than $MAX_ALIVE_AGE years ago.
			// If one of these is a birth, the individual must be alive.
			if (preg_match('/\n1 BIRT(?:\n[2-9].+)*\n2 DATE /', $controller->record->getGedcom())) {
				return false;
			}
		}
		
		// If we found no conclusive dates then check the dates of close relatives.

		// Check parents (birth and adopted)
		foreach ($controller->record->getChildFamilies(WT_PRIV_HIDE) as $family) {
			foreach ($family->getSpouses(WT_PRIV_HIDE) as $parent) {
				// Assume parents are no more than 45 years older than their children
				preg_match_all('/\n2 DATE (.+)/', $parent->getGedcom(), $date_matches);
				foreach ($date_matches[1] as $date_match) {
					$date=new WT_Date($date_match);
					if ($date->isOK() && $date->MaxJD() <= WT_CLIENT_JD - 365*($MAX_ALIVE_AGE+45)) {
						return WT_I18N::translate('A parent with a birth date of %s is more than 45 years older than this person.', $date->Display(!$SEARCH_SPIDER));
					}
				}
			}
		}
		
		// Check spouses
		foreach ($controller->record->getSpouseFamilies(WT_PRIV_HIDE) as $family) {
			preg_match_all('/\n2 DATE (.+)/', $family->getGedcom(), $date_matches);
			foreach ($date_matches[1] as $date_match) {
				$date=new WT_Date($date_match);
				// Assume marriage occurs after age of 10
				if ($date->isOK() && $date->MaxJD() <= WT_CLIENT_JD - 365*($MAX_ALIVE_AGE-10)) {
					WT_I18N::translate('A marriage with a date of %s suggests they were born at least 10 years earlier than that.', $date->Display(!$SEARCH_SPIDER));
				}
			}
			// Check spouse dates
			$spouse = $family->getSpouse(WT_Individual::getInstance($controller->record->getXref()), WT_PRIV_HIDE);
			if ($spouse) {
				preg_match_all('/\n2 DATE (.+)/', $spouse->getGedcom(), $date_matches);
				foreach ($date_matches[1] as $date_match) {
					$date = new WT_Date($date_match);
					// Assume max age difference between spouses of 40 years
					if ($date->isOK() && $date->MaxJD() <= WT_CLIENT_JD - 365*($MAX_ALIVE_AGE+40)) {
						return WT_I18N::translate('A spouse with a date of %s is more than 40 years older than this person.', $date->Display(!$SEARCH_SPIDER));
					}
				}
			}
			// Check child dates
			foreach ($family->getChildren(WT_PRIV_HIDE) as $child) {
				preg_match_all('/\n2 DATE (.+)/', $child->getGedcom(), $date_matches);
				// Assume children born after age of 15
				foreach ($date_matches[1] as $date_match) {
					$date=new WT_Date($date_match);
					if ($date->isOK() && $date->MaxJD() <= WT_CLIENT_JD - 365*($MAX_ALIVE_AGE-15)) {
						return WT_I18N::translate('A child with a birth date of %s suggests this person was born at least 15 years earlier than that.', $date->Display(!$SEARCH_SPIDER));
					}
				}
				// Check grandchildren
				foreach ($child->getSpouseFamilies(WT_PRIV_HIDE) as $child_family) {
					foreach ($child_family->getChildren(WT_PRIV_HIDE) as $grandchild) {
						preg_match_all('/\n2 DATE (.+)/', $grandchild->getGedcom(), $date_matches);
						// Assume grandchildren born after age of 30
						foreach ($date_matches[1] as $date_match) {
							$date=new WT_Date($date_match);
							if ($date->isOK() && $date->MaxJD() <= WT_CLIENT_JD - 365*($MAX_ALIVE_AGE-30)) {
								return WT_I18N::translate('A grandchild with a birth date of %s suggests this person was born at least 30 years earlier than that.', $date->Display(!$SEARCH_SPIDER));
							}
						}
					}
				}
			}
		}
		return '';
	}
	
	// Implement the css stylesheet for this module	
	private function includeCss() {
		return $this->getScript(WT_MODULES_DIR.$this->getName().'/style.css');	
	}	
	
	private function getScript($css) {
		return
			'<script>
				if (document.createStyleSheet) {
					document.createStyleSheet("'.$css.'"); // For Internet Explorer
				} else {
					var newSheet=document.createElement("link");
					newSheet.setAttribute("rel","stylesheet");
					newSheet.setAttribute("type","text/css");
					newSheet.setAttribute("href","'.$css.'");
					document.getElementsByTagName("head")[0].appendChild(newSheet);
				}
			</script>';
	}	
}
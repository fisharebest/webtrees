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

class personal_facts_WT_Module extends WT_Module implements WT_Module_Tab {
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module/tab on the individual page. */ WT_I18N::translate('Facts and events');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Facts and events” module */ WT_I18N::translate('A tab showing the facts and events of an individual.');
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

		$indifacts = array();
		// The individual’s own facts
		foreach ($controller->record->getFacts() as $fact) {
			switch ($fact->getTag()) {
			case 'SEX':
			case 'NAME':
			case 'SOUR':
			case 'OBJE':
			case 'NOTE':
			case 'FAMC':
			case 'FAMS':
				break;
			default:
				if (!array_key_exists('extra_info', WT_Module::getActiveSidebars()) || !extra_info_WT_Module::showFact($fact)) {
					$indifacts[] = $fact;
				}
				break;
			}
		}

		// Add spouse-family facts
		foreach ($controller->record->getSpouseFamilies() as $family) {
			foreach ($family->getFacts() as $fact) {
				switch ($fact->getTag()) {
				case 'SOUR':
				case 'NOTE':
				case 'OBJE':
				case 'CHAN':
				case '_UID':
				case 'RIN':
				case 'HUSB':
				case 'WIFE':
				case 'CHIL':
					break;
				default:
					$indifacts[] = $fact;
					break;
				}
			}
			$spouse = $family->getSpouse($controller->record);
			if ($spouse) {
				foreach (self::spouseFacts($controller->record, $spouse) as $fact) {
					$indifacts[] = $fact;
				}
			}
			foreach (self::childFacts($controller->record, $family, '_CHIL', '') as $fact) {
				$indifacts[] = $fact;
			}
		}

		foreach (self::parentFacts($controller->record, 1) as $fact) {
			$indifacts[] = $fact;
		}
		foreach (self::historicalFacts($controller->record) as $fact) {
			$indifacts[] = $fact;
		}
		foreach (self::associateFacts($controller->record) as $fact) {
			$indifacts[] = $fact;
		}

		sort_facts($indifacts);

		ob_start();

		echo '<table class="facts_table">';
		echo '<tbody>';
		if (!$indifacts) {
			echo '<tr><td colspan="2" class="facts_value">', WT_I18N::translate('There are no facts for this individual.'), '</td></tr>';
		}

		echo '<tr><td colspan="2" class="descriptionbox rela"><form action="?"><input id="checkbox_rela_facts" type="checkbox"';
		if ($EXPAND_RELATIVES_EVENTS) {
			echo ' checked="checked"';
		}
		echo ' onclick="jQuery(\'tr.rela\').toggle();"><label for="checkbox_rela_facts">', WT_I18N::translate('Events of close relatives'), '</label>';
		if (file_exists(WT_Site::getPreference('INDEX_DIRECTORY').'histo.'.WT_LOCALE.'.php')) {
			echo ' <input id="checkbox_histo" type="checkbox"';
			if ($EXPAND_HISTO_EVENTS) {
				echo ' checked="checked"';
			}
			echo ' onclick="jQuery(\'tr.histo\').toggle();"><label for="checkbox_histo">', WT_I18N::translate('Historical facts'), '</label>';
		}
		echo '</form></td></tr>';

		foreach ($indifacts as $fact) {
			print_fact($fact, $controller->record);
		}

		//-- new fact link
		if ($controller->record->canEdit()) {
			print_add_new_fact($controller->record->getXref(), $indifacts, 'INDI');
		}
		echo '</tbody>';
		echo '</table>';

		if (!$EXPAND_RELATIVES_EVENTS) {
			echo '<script>jQuery("tr.rela").toggle();</script>';
		}
		if (!$EXPAND_HISTO_EVENTS) {
			echo '<script>jQuery("tr.histo").toggle();</script>';
		}


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

	/**
	 * Spouse facts that are shown on an individual’s page.
	 *
	 * @param WT_Individual $individual Show events that occured during the lifetime of this individual
	 * @param WT_Individual $spouse     Show events of this individual
	 *
	 * @return WT_Fact[]
	 */
	private static function spouseFacts(WT_Individual $individual, WT_Individual $spouse) {
		global $SHOW_RELATIVES_EVENTS;

		$facts = array();
		if (strstr($SHOW_RELATIVES_EVENTS, '_DEAT_SPOU')) {
			// Only include events between birth and death
			$birt_date = $individual->getEstimatedBirthDate();
			$deat_date = $individual->getEstimatedDeathDate();

			foreach ($spouse->getFacts(WT_EVENTS_DEAT) as $fact) {

				$fact_date = $fact->getDate();
				if ($fact_date->isOK() && WT_Date::Compare($birt_date, $fact_date)<=0 && WT_Date::Compare($fact_date, $deat_date)<=0) {
					// Convert the event to a close relatives event.
					$rela_fact = clone($fact);
					$rela_fact->setTag('_'.$fact->getTag().'_SPOU');
					$facts[] = $rela_fact;
				}
			}
		}

		return $facts;
	}

	private static function childFacts(WT_Individual $person, WT_Family $family, $option, $relation) {
		global $controller, $SHOW_RELATIVES_EVENTS;

		$facts = array();

		// Only include events between birth and death
		$birt_date = $controller->record->getEstimatedBirthDate();
		$deat_date = $controller->record->getEstimatedDeathDate();

		// Deal with recursion.
		switch ($option) {
		case '_CHIL':
			// Add grandchildren
			foreach ($family->getChildren() as $child) {
				foreach ($child->getSpouseFamilies() as $cfamily) {
					switch ($child->getSex()) {
					case 'M':
						foreach (self::childFacts($person, $cfamily, '_GCHI', 'son') as $fact) {
							$facts[] = $fact;
						}
						break;
					case 'F':
						foreach (self::childFacts($person, $cfamily, '_GCHI', 'dau') as $fact) {
							$facts[] = $fact;
						}
						break;
					default:
						foreach (self::childFacts($person, $cfamily, '_GCHI', 'chi') as $fact) {
							$facts[] = $fact;
						}
						break;
					}
				}
			}
			break;
		}

		// For each child in the family
		foreach ($family->getChildren() as $child) {
			if ($child->getXref()==$person->getXref()) {
				// We are not our own sibling!
				continue;
			}
			// add child’s birth
			if (strpos($SHOW_RELATIVES_EVENTS, '_BIRT'.str_replace('_HSIB', '_SIBL', $option))!==false) {
				foreach ($child->getFacts(WT_EVENTS_BIRT) as $fact) {
					$sgdate=$fact->getDate();
					// Always show _BIRT_CHIL, even if the dates are not known
					if ($option=='_CHIL' || $sgdate->isOK() && WT_Date::Compare($birt_date, $sgdate)<=0 && WT_Date::Compare($sgdate, $deat_date)<=0) {
						if ($option=='_GCHI' && $relation=='dau') {
							// Convert the event to a close relatives event.
							$rela_fact = clone($fact);
							$rela_fact->setTag('_' . $fact->getTag() . '_GCH1');
							$facts[] = $rela_fact;
						} elseif ($option=='_GCHI' && $relation=='son') {
							// Convert the event to a close relatives event.
							$rela_fact = clone($fact);
							$rela_fact->setTag('_' . $fact->getTag() . '_GCH2');
							$facts[] = $rela_fact;
						} else {
							// Convert the event to a close relatives event.
							$rela_fact = clone($fact);
							$rela_fact->setTag('_' . $fact->getTag() . $option);
							$facts[] = $rela_fact;
						}
					}
				}
			}
			// add child’s death
			if (strpos($SHOW_RELATIVES_EVENTS, '_DEAT'.str_replace('_HSIB', '_SIBL', $option))!==false) {
				foreach ($child->getFacts(WT_EVENTS_DEAT) as $fact) {
					$sgdate=$fact->getDate();
					if ($sgdate->isOK() && WT_Date::Compare($birt_date, $sgdate)<=0 && WT_Date::Compare($sgdate, $deat_date)<=0) {
						if ($option=='_GCHI' && $relation=='dau') {
							// Convert the event to a close relatives event.
							$rela_fact = clone($fact);
							$rela_fact->setTag('_' . $fact->getTag() . '_GCH1');
							$facts[] = $rela_fact;
						} elseif ($option=='_GCHI' && $relation=='son') {
							// Convert the event to a close relatives event.
							$rela_fact = clone($fact);
							$rela_fact->setTag('_' . $fact->getTag() . '_GCH2');
							$facts[] = $rela_fact;
						} else {
							// Convert the event to a close relatives event.
							$rela_fact = clone($fact);
							$rela_fact->setTag('_' . $fact->getTag() . $option);
							$facts[] = $rela_fact;
						}
					}
				}
			}
			// add child’s marriage
			if (strstr($SHOW_RELATIVES_EVENTS, '_MARR'.str_replace('_HSIB', '_SIBL', $option))) {
				foreach ($child->getSpouseFamilies() as $sfamily) {
					foreach ($sfamily->getFacts(WT_EVENTS_MARR) as $fact) {
						$sgdate=$fact->getDate();
						if ($sgdate->isOK() && WT_Date::Compare($birt_date, $sgdate)<=0 && WT_Date::Compare($sgdate, $deat_date)<=0) {
							if ($option=='_GCHI' && $relation=='dau') {
								// Convert the event to a close relatives event.
								$rela_fact = clone($fact);
								$rela_fact->setTag('_' . $fact->getTag() . '_GCH1');
								$facts[] = $rela_fact;
							} elseif ($option=='_GCHI' && $relation=='son') {
								// Convert the event to a close relatives event.
								$rela_fact = clone($fact);
								$rela_fact->setTag('_' . $fact->getTag() . '_GCH2');
								$facts[] = $rela_fact;
							} else {
								// Convert the event to a close relatives event.
								$rela_fact = clone($fact);
								$rela_fact->setTag('_' . $fact->getTag() . $option);
								$facts[] = $rela_fact;
							}
						}
					}
				}
			}
		}

		return $facts;
	}

	private static function parentFacts(WT_Individual $person, $sosa) {
		global $controller, $SHOW_RELATIVES_EVENTS;

		$facts = array();

		// Only include events between birth and death
		$birt_date = $controller->record->getEstimatedBirthDate();
		$deat_date = $controller->record->getEstimatedDeathDate();

		if ($sosa == 1) {
			foreach ($person->getChildFamilies() as $family) {
				// Add siblings
				foreach (self::childFacts($person, $family, '_SIBL', '') as $fact) {
					$facts[] = $fact;
				}
				foreach ($family->getSpouses() as $spouse) {
					foreach ($spouse->getSpouseFamilies() as $sfamily) {
						if ($family !== $sfamily) {
							// Add half-siblings
							foreach (self::childFacts($person, $sfamily, '_HSIB', '') as $fact) {
								$facts[] = $fact;
							}
						}
					}
					// Add grandparents
					foreach (self::parentFacts($spouse, $spouse->getSex()=='F' ? 3 : 2) as $fact) {
						$facts[] = $fact;
					}
				}
			}

			if (strstr($SHOW_RELATIVES_EVENTS, '_MARR_PARE')) {
				// add father/mother marriages
				foreach ($person->getChildFamilies() as $sfamily) {
					foreach ($sfamily->getFacts(WT_EVENTS_MARR) as $fact) {
						if ($fact->getDate()->isOK() && WT_Date::Compare($birt_date, $fact->getDate())<=0 && WT_Date::Compare($fact->getDate(), $deat_date)<=0) {
							// marriage of parents (to each other)
							$rela_fact = clone($fact);
							$rela_fact->setTag('_'.$fact->getTag().'_FAMC');
							$facts[] = $rela_fact;
						}
					}
				}
				foreach ($person->getChildStepFamilies() as $sfamily) {
					foreach ($sfamily->getFacts(WT_EVENTS_MARR) as $fact) {
						if ($fact->getDate()->isOK() && WT_Date::Compare($birt_date, $fact->getDate())<=0 && WT_Date::Compare($fact->getDate(), $deat_date)<=0) {
							// marriage of a parent (to another spouse)
							// Convert the event to a close relatives event
							$rela_fact = clone($fact);
							$rela_fact->setTag('_'.$fact->getTag().'_PARE');
							$facts[] = $rela_fact;
						}
					}
				}
			}
		}

		foreach ($person->getChildFamilies() as $family) {
			foreach ($family->getSpouses() as $parent) {
				if (strstr($SHOW_RELATIVES_EVENTS, '_DEAT'.($sosa==1 ? '_PARE' : '_GPAR'))) {
					foreach ($parent->getFacts(WT_EVENTS_DEAT) as $fact) {
						if ($fact->getDate()->isOK() && WT_Date::Compare($birt_date, $fact->getDate())<=0 && WT_Date::Compare($fact->getDate(), $deat_date)<=0) {
							switch ($sosa) {
							case 1:
								// Convert the event to a close relatives event.
								$rela_fact = clone($fact);
								$rela_fact->setTag('_'.$fact->getTag().'_PARE');
								$facts[] = $rela_fact;
								break;
							case 2:
								// Convert the event to a close relatives event
								$rela_fact = clone($fact);
								$rela_fact->setTag('_'.$fact->getTag().'_GPA1');
								$facts[] = $rela_fact;
								break;
							case 3:
								// Convert the event to a close relatives event
								$rela_fact = clone($fact);
								$rela_fact->setTag('_'.$fact->getTag().'_GPA2');
								$facts[] = $rela_fact;
								break;
							}
						}
					}
				}
			}
		}

		return $facts;
	}

	private static function historicalFacts(WT_Individual $person) {
		global $SHOW_RELATIVES_EVENTS;

		$facts = array();

		if ($SHOW_RELATIVES_EVENTS) {
			// Only include events between birth and death
			$birt_date = $person->getEstimatedBirthDate();
			$deat_date = $person->getEstimatedDeathDate();

			if (file_exists(WT_Site::getPreference('INDEX_DIRECTORY') . 'histo.' . WT_LOCALE . '.php')) {
				require WT_Site::getPreference('INDEX_DIRECTORY') . 'histo.' . WT_LOCALE . '.php';
				foreach ($histo as $hist) {
					// Earlier versions of the WIKI encouraged people to use HTML entities,
					// rather than UTF8 encoding.
					$hist = html_entity_decode($hist, ENT_QUOTES, 'UTF-8');

					$fact = new WT_Fact($hist, $person, 'histo');
					$sdate = $fact->getDate();
					if ($sdate->isOK() && WT_Date::Compare($birt_date, $sdate)<=0 && WT_Date::Compare($sdate, $deat_date)<=0) {
						$facts[] = $fact;
					}
				}
			}
		}

		return $facts;
	}

	private static function associateFacts(WT_Individual $person) {
		$facts = array();

		$associates=array_merge(
			$person->linkedIndividuals('ASSO'),
			$person->linkedIndividuals('_ASSO'),
			$person->linkedFamilies('ASSO'),
			$person->linkedFamilies('_ASSO')
		);
		foreach ($associates as $associate) {
			foreach ($associate->getFacts() as $fact) {
				$arec = $fact->getAttribute('_ASSO');
				if (!$arec) {
					$arec = $fact->getAttribute('ASSO');
				}
				if ($arec && trim($arec, '@') === $person->getXref()) {
					// Extract the important details from the fact
					$factrec='1 '.$fact->getTag();
					if (preg_match('/\n2 DATE .*/', $fact->getGedcom(), $match)) {
						$factrec.=$match[0];
					}
					if (preg_match('/\n2 PLAC .*/', $fact->getGedcom(), $match)) {
						$factrec.=$match[0];
					}
					if ($associate instanceof WT_Family) {
						foreach ($associate->getSpouses() as $spouse) {
							$factrec.="\n2 _ASSO @".$spouse->getXref().'@';
						}
					} else {
						$factrec.="\n2 _ASSO @".$associate->getXref().'@';
						// CHR/BAPM events are commonly used.  Generate the reverse relationship
						if (preg_match('/^(?:BAPM|CHR)$/', $fact->getTag()) && preg_match('/2 _?ASSO @('.$person->getXref().')@\n3 RELA god(?:parent|mother|father)/', $fact->getGedcom())) {
							switch ($associate->getSex()) {
							case 'M':
								$factrec .= "\n3 RELA godson";
								break;
							case 'F':
								$factrec .= "\n3 RELA goddaughter";
								break;
							default:
								$factrec .= "\n3 RELA godchild";
								break;
							}
						}
					}
					$facts[] = new WT_Fact($factrec, $associate, 'asso');
				}
			}
		}

		return $facts;
	}
}

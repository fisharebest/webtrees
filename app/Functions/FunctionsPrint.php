<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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
namespace Fisharebest\Webtrees\Functions;

use Fisharebest\Webtrees\Controller\SearchController;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeStat;
use Fisharebest\Webtrees\GedcomCode\GedcomCodeTemp;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Module\CensusAssistantModule;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use Rhumsaa\Uuid\Uuid;

/**
 * Class FunctionsPrint - common functions
 */
class FunctionsPrint {
	/**
	 * print the information for an individual chart box
	 *
	 * find and print a given individuals information for a pedigree chart
	 *
	 * @param Individual $person The person to print
	 * @param int $show_full The style to print the box in, 0 for smaller boxes, 1 for larger boxes
	 */
	public static function printPedigreePerson(Individual $person = null, $show_full = 1) {

		switch ($show_full) {
		case 0:
			if ($person) {
				echo Theme::theme()->individualBoxSmall($person);
			} else {
				echo Theme::theme()->individualBoxSmallEmpty();
			}
			break;
		case 1:
			if ($person) {
				echo Theme::theme()->individualBox($person);
			} else {
				echo Theme::theme()->individualBoxEmpty();
			}
			break;
		}
	}

	/**
	 * print a note record
	 *
	 * @param string $text
	 * @param int $nlevel the level of the note record
	 * @param string $nrec the note record to print
	 * @param bool $textOnly Don't print the "Note: " introduction
	 *
	 * @return string
	 */
	public static function printNoteRecord($text, $nlevel, $nrec, $textOnly = false) {
		global $WT_TREE;

		$text .= Functions::getCont($nlevel, $nrec);

		// Check if shared note (we have already checked that it exists)
		if (preg_match('/^0 @(' . WT_REGEX_XREF . ')@ NOTE/', $nrec, $match)) {
			$note  = Note::getInstance($match[1], $WT_TREE);
			$label = 'SHARED_NOTE';
			// If Census assistant installed, allow it to format the note
			if (Module::getModuleByName('GEDFact_assistant')) {
				$html = CensusAssistantModule::formatCensusNote($note);
			} else {
				$html = Filter::formatText($note->getNote(), $WT_TREE);
			}
		} else {
			$note  = null;
			$label = 'NOTE';
			$html  = Filter::formatText($text, $WT_TREE);
		}

		if ($textOnly) {
			return strip_tags($text);
		}

		if (strpos($text, "\n") === false) {
			// A one-line note? strip the block-level tags, so it displays inline
			return GedcomTag::getLabelValue($label, strip_tags($html, '<a><strong><em>'));
		} elseif ($WT_TREE->getPreference('EXPAND_NOTES')) {
			// A multi-line note, and we're expanding notes by default
			return GedcomTag::getLabelValue($label, $html);
		} else {
			// A multi-line note, with an expand/collapse option
			$element_id = Uuid::uuid4();
			// NOTE: class "note-details" is (currently) used only by some third-party themes
			if ($note) {
				$first_line = '<a href="' . $note->getHtmlUrl() . '">' . $note->getFullName() . '</a>';
			} else {
				list($text) = explode("\n", strip_tags($html));
				$first_line = strlen($text) > 100 ? mb_substr($text, 0, 100) . I18N::translate('…') : $text;
			}

			return
				'<div class="fact_NOTE"><span class="label">' .
				'<a href="#" onclick="expand_layer(\'' . $element_id . '\'); return false;"><i id="' . $element_id . '_img" class="icon-plus"></i></a> ' . GedcomTag::getLabel($label) . ':</span> ' . '<span id="' . $element_id . '-alt">' . $first_line . '</span>' .
				'</div>' .
				'<div class="note-details" id="' . $element_id . '" style="display:none">' . $html . '</div>';
		}
	}

	/**
	 * Print all of the notes in this fact record
	 *
	 * @param string $factrec The factrecord to print the notes from
	 * @param int $level The level of the factrecord
	 * @param bool $textOnly Don't print the "Note: " introduction
	 *
	 * @return string HTML
	 */
	public static function printFactNotes($factrec, $level, $textOnly = false) {
		global $WT_TREE;

		$data          = '';
		$previous_spos = 0;
		$nlevel        = $level + 1;
		$ct            = preg_match_all("/$level NOTE (.*)/", $factrec, $match, PREG_SET_ORDER);
		for ($j = 0; $j < $ct; $j++) {
			$spos1 = strpos($factrec, $match[$j][0], $previous_spos);
			$spos2 = strpos($factrec . "\n$level", "\n$level", $spos1 + 1);
			if (!$spos2) {
				$spos2 = strlen($factrec);
			}
			$previous_spos = $spos2;
			$nrec          = substr($factrec, $spos1, $spos2 - $spos1);
			if (!isset($match[$j][1])) {
				$match[$j][1] = '';
			}
			if (!preg_match('/@(.*)@/', $match[$j][1], $nmatch)) {
				$data .= self::printNoteRecord($match[$j][1], $nlevel, $nrec, $textOnly);
			} else {
				$note = Note::getInstance($nmatch[1], $WT_TREE);
				if ($note) {
					if ($note->canShow()) {
						$noterec = $note->getGedcom();
						$nt      = preg_match("/0 @$nmatch[1]@ NOTE (.*)/", $noterec, $n1match);
						$data .= self::printNoteRecord(($nt > 0) ? $n1match[1] : "", 1, $noterec, $textOnly);
						if (!$textOnly) {
							if (strpos($noterec, '1 SOUR') !== false) {
								$data .= FunctionsPrintFacts::printFactSources($noterec, 1);
							}
						}
					}
				} else {
					$data = '<div class="fact_NOTE"><span class="label">' . I18N::translate('Note') . '</span>: <span class="field error">' . $nmatch[1] . '</span></div>';
				}
			}
			if (!$textOnly) {
				if (strpos($factrec, "$nlevel SOUR") !== false) {
					$data .= "<div class=\"indent\">";
					$data .= FunctionsPrintFacts::printFactSources($nrec, $nlevel);
					$data .= "</div>";
				}
			}
		}

		return $data;
	}

	/**
	 * Print a link for a popup help window.
	 *
	 * @param string $help_topic
	 * @param string $module
	 *
	 * @return string
	 */
	public static function helpLink($help_topic, $module = '') {
		return '<span class="icon-help" onclick="helpDialog(\'' . $help_topic . '\',\'' . $module . '\'); return false;">&nbsp;</span>';
	}

	/**
	 * Print an external help link to the wiki site.
	 *
	 * @deprecated - nothing should be so complicated that it needs lengthy instructions!
	 *
	 * @param string $topic
	 *
	 * @return string
	 */
	public static function wikiHelpLink($topic) {
		return '<a class="help icon-wiki" href="' . WT_WEBTREES_WIKI . $topic . '" title="' . I18N::translate('webtrees wiki') . '"></a>';
	}

	/**
	 * When a user has searched for text, highlight any matches in
	 * the displayed string.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function highlightSearchHits($string) {
		global $controller;

		if ($controller instanceof SearchController && $controller->query) {
			// TODO: when a search contains multiple words, we search independently.
			// e.g. searching for "FOO BAR" will find records containing both FOO and BAR.
			// However, we only highlight the original search string, not the search terms.
			// The controller needs to provide its "query_terms" array.
			$regex = array();
			foreach (array($controller->query) as $search_term) {
				$regex[] = preg_quote($search_term, '/');
			}
			// Match these strings, provided they do not occur inside HTML tags
			$regex = '(' . implode('|', $regex) . ')(?![^<]*>)';

			return preg_replace('/' . $regex . '/i', '<span class="search_hit">$1</span>', $string);
		} else {
			return $string;
		}
	}

	/**
	 * Format age of parents in HTML
	 *
	 * @param Individual $person child
	 * @param Date $birth_date
	 *
	 * @return string HTML
	 */
	public static function formatParentsAges(Individual $person, Date $birth_date) {
		$html     = '';
		$families = $person->getChildFamilies();
		// Multiple sets of parents (e.g. adoption) cause complications, so ignore.
		if ($birth_date->isOK() && count($families) == 1) {
			$family = current($families);
			foreach ($family->getSpouses() as $parent) {
				if ($parent->getBirthDate()->isOK()) {
					$sex      = $parent->getSexImage();
					$age      = Date::getAge($parent->getBirthDate(), $birth_date, 2);
					$deatdate = $parent->getDeathDate();
					switch ($parent->getSex()) {
					case 'F':
						// Highlight mothers who die in childbirth or shortly afterwards
						if ($deatdate->isOK() && $deatdate->maximumJulianDay() < $birth_date->minimumJulianDay() + 90) {
							$html .= ' <span title="' . GedcomTag::getLabel('_DEAT_PARE', $parent) . '" class="parentdeath">' . $sex . $age . '</span>';
						} else {
							$html .= ' <span title="' . I18N::translate('Mother’s age') . '">' . $sex . $age . '</span>';
						}
						break;
					case 'M':
						// Highlight fathers who die before the birth
						if ($deatdate->isOK() && $deatdate->maximumJulianDay() < $birth_date->minimumJulianDay()) {
							$html .= ' <span title="' . GedcomTag::getLabel('_DEAT_PARE', $parent) . '" class="parentdeath">' . $sex . $age . '</span>';
						} else {
							$html .= ' <span title="' . I18N::translate('Father’s age') . '">' . $sex . $age . '</span>';
						}
						break;
					default:
						$html .= ' <span title="' . I18N::translate('Parent’s age') . '">' . $sex . $age . '</span>';
						break;
					}
				}
			}
			if ($html) {
				$html = '<span class="age">' . $html . '</span>';
			}
		}

		return $html;
	}

	/**
	 * Print fact DATE/TIME
	 *
	 * @param Fact $event event containing the date/age
	 * @param GedcomRecord $record the person (or couple) whose ages should be printed
	 * @param bool $anchor option to print a link to calendar
	 * @param bool $time option to print TIME value
	 *
	 * @return string
	 */
	public static function formatFactDate(Fact $event, GedcomRecord $record, $anchor, $time) {
		global $pid;

		$factrec = $event->getGedcom();
		$html    = '';
		// Recorded age
		if (preg_match('/\n2 AGE (.+)/', $factrec, $match)) {
			$fact_age = $match[1];
		} else {
			$fact_age = '';
		}
		if (preg_match('/\n2 HUSB\n3 AGE (.+)/', $factrec, $match)) {
			$husb_age = $match[1];
		} else {
			$husb_age = '';
		}
		if (preg_match('/\n2 WIFE\n3 AGE (.+)/', $factrec, $match)) {
			$wife_age = $match[1];
		} else {
			$wife_age = '';
		}

		// Calculated age
		if (preg_match('/\n2 DATE (.+)/', $factrec, $match)) {
			$date = new Date($match[1]);
			$html .= ' ' . $date->display($anchor);
			// time
			if ($time && preg_match('/\n3 TIME (.+)/', $factrec, $match)) {
				$html .= ' – <span class="date">' . $match[1] . '</span>';
			}
			$fact = $event->getTag();
			if ($record instanceof Individual) {
				if ($fact === 'BIRT' && $record->getTree()->getPreference('SHOW_PARENTS_AGE')) {
					// age of parents at child birth
					$html .= self::formatParentsAges($record, $date);
				} elseif ($fact !== 'BIRT' && $fact !== 'CHAN' && $fact !== '_TODO') {
					// age at event
					$birth_date = $record->getBirthDate();
					// Can't use getDeathDate(), as this also gives BURI/CREM events, which
					// wouldn't give the correct "days after death" result for people with
					// no DEAT.
					$death_event = $record->getFirstFact('DEAT');
					if ($death_event) {
						$death_date = $death_event->getDate();
					} else {
						$death_date = new Date('');
					}
					$ageText = '';
					if ((Date::compare($date, $death_date) <= 0 || !$record->isDead()) || $fact == 'DEAT') {
						// Before death, print age
						$age = Date::getAgeGedcom($birth_date, $date);
						// Only show calculated age if it differs from recorded age
						if ($age != '') {
							if (
								$fact_age != '' && $fact_age != $age ||
								$fact_age == '' && $husb_age == '' && $wife_age == '' ||
								$husb_age != '' && $record->getSex() == 'M' && $husb_age != $age ||
								$wife_age != '' && $record->getSex() == 'F' && $wife_age != $age
							) {
								if ($age != "0d") {
									$ageText = '(' . I18N::translate('Age') . ' ' . FunctionsDate::getAgeAtEvent($age, false) . ')';
								}
							}
						}
					}
					if ($fact != 'DEAT' && Date::compare($date, $death_date) >= 0) {
						// After death, print time since death
						$age = FunctionsDate::getAgeAtEvent(Date::getAgeGedcom($death_date, $date), true);
						if ($age != '') {
							if (Date::getAgeGedcom($death_date, $date) == "0d") {
								$ageText = '(' . I18N::translate('on the date of death') . ')';
							} else {
								$ageText = '(' . $age . ' ' . I18N::translate('after death') . ')';
								// Family events which occur after death are probably errors
								if ($event->getParent() instanceof Family) {
									$ageText .= '<i class="icon-warning"></i>';
								}
							}
						}
					}
					if ($ageText) {
						$html .= ' <span class="age">' . $ageText . '</span>';
					}
				}
			} elseif ($record instanceof Family) {
				$indi = Individual::getInstance($pid, $record->getTree());
				if ($indi) {
					$birth_date = $indi->getBirthDate();
					$death_date = $indi->getDeathDate();
					$ageText    = '';
					if (Date::compare($date, $death_date) <= 0) {
						$age = Date::getAgeGedcom($birth_date, $date);
						// Only show calculated age if it differs from recorded age
						if ($age != '' && $age > 0) {
							if (
								$fact_age != '' && $fact_age != $age ||
								$fact_age == '' && $husb_age == '' && $wife_age == '' ||
								$husb_age != '' && $indi->getSex() == 'M' && $husb_age != $age ||
								$wife_age != '' && $indi->getSex() == 'F' && $wife_age != $age
							) {
								$ageText = '(' . I18N::translate('Age') . ' ' . FunctionsDate::getAgeAtEvent($age, false) . ')';
							}
						}
					}
					if ($ageText) {
						$html .= ' <span class="age">' . $ageText . '</span>';
					}
				}
			}
		} else {
			// 1 DEAT Y with no DATE => print YES
			// 1 BIRT 2 SOUR @S1@ => print YES
			// 1 DEAT N is not allowed
			// It is not proper GEDCOM form to use a N(o) value with an event tag to infer that it did not happen.
			$factdetail = explode(' ', trim($factrec));
			if (isset($factdetail) && (count($factdetail) == 3 && strtoupper($factdetail[2]) == 'Y') || (count($factdetail) == 4 && $factdetail[2] == 'SOUR')) {
				$html .= I18N::translate('yes');
			}
		}
		// print gedcom ages
		foreach (array(GedcomTag::getLabel('AGE') => $fact_age, GedcomTag::getLabel('HUSB') => $husb_age, GedcomTag::getLabel('WIFE') => $wife_age) as $label => $age) {
			if ($age != '') {
				$html .= ' <span class="label">' . $label . ':</span> <span class="age">' . FunctionsDate::getAgeAtEvent($age, false) . '</span>';
			}
		}

		return $html;
	}

	/**
	 * print fact PLACe TEMPle STATus
	 *
	 * @param Fact $event gedcom fact record
	 * @param bool $anchor to print a link to placelist
	 * @param bool $sub_records to print place subrecords
	 * @param bool $lds to print LDS TEMPle and STATus
	 *
	 * @return string HTML
	 */
	public static function formatFactPlace(Fact $event, $anchor = false, $sub_records = false, $lds = false) {
		if ($anchor) {
			// Show the full place name, for facts/events tab
			$html = '<a href="' . $event->getPlace()->getURL() . '">' . $event->getPlace()->getFullName() . '</a>';
		} else {
			// Abbreviate the place name, for chart boxes
			return ' - ' . $event->getPlace()->getShortName();
		}

		if ($sub_records) {
			$placerec = Functions::getSubRecord(2, '2 PLAC', $event->getGedcom());
			if (!empty($placerec)) {
				if (preg_match_all('/\n3 (?:_HEB|ROMN) (.+)/', $placerec, $matches)) {
					foreach ($matches[1] as $match) {
						$wt_place = new Place($match, $event->getParent()->getTree());
						$html .= ' - ' . $wt_place->getFullName();
					}
				}
				$map_lati = "";
				$cts      = preg_match('/\d LATI (.*)/', $placerec, $match);
				if ($cts > 0) {
					$map_lati = $match[1];
					$html .= '<br><span class="label">' . GedcomTag::getLabel('LATI') . ': </span>' . $map_lati;
				}
				$map_long = '';
				$cts      = preg_match('/\d LONG (.*)/', $placerec, $match);
				if ($cts > 0) {
					$map_long = $match[1];
					$html .= ' <span class="label">' . GedcomTag::getLabel('LONG') . ': </span>' . $map_long;
				}
				if ($map_lati && $map_long) {
					$map_lati = trim(strtr($map_lati, "NSEW,�", " - -. ")); // S5,6789 ==> -5.6789
					$map_long = trim(strtr($map_long, "NSEW,�", " - -. ")); // E3.456� ==> 3.456
					$html .= ' <a rel="nofollow" href="https://maps.google.com/maps?q=' . $map_lati . ',' . $map_long . '" class="icon-googlemaps" title="' . I18N::translate('Google Maps™') . '"></a>';
					$html .= ' <a rel="nofollow" href="https://www.bing.com/maps/?lvl=15&cp=' . $map_lati . '~' . $map_long . '" class="icon-bing" title="' . I18N::translate('Bing Maps™') . '"></a>';
					$html .= ' <a rel="nofollow" href="https://www.openstreetmap.org/#map=15/' . $map_lati . '/' . $map_long . '" class="icon-osm" title="' . I18N::translate('OpenStreetMap™') . '"></a>';
				}
				if (preg_match('/\d NOTE (.*)/', $placerec, $match)) {
					$html .= '<br>' . self::printFactNotes($placerec, 3);
				}
			}
		}
		if ($lds) {
			if (preg_match('/2 TEMP (.*)/', $event->getGedcom(), $match)) {
				$html .= '<br>' . I18N::translate('LDS temple') . ': ' . GedcomCodeTemp::templeName($match[1]);
			}
			if (preg_match('/2 STAT (.*)/', $event->getGedcom(), $match)) {
				$html .= '<br>' . I18N::translate('Status') . ': ' . GedcomCodeStat::statusName($match[1]);
				if (preg_match('/3 DATE (.*)/', $event->getGedcom(), $match)) {
					$date = new Date($match[1]);
					$html .= ', ' . GedcomTag::getLabel('STAT:DATE') . ': ' . $date->display();
				}
			}
		}

		return $html;
	}

	/**
	 * Check for facts that may exist only once for a certain record type.
	 * If the fact already exists in the second array, delete it from the first one.
	 *
	 * @param string[] $uniquefacts
	 * @param Fact[] $recfacts
	 * @param string $type
	 *
	 * @return string[]
	 */
	public static function checkFactUnique($uniquefacts, $recfacts, $type) {
		foreach ($recfacts as $factarray) {
			$fact = false;
			if (is_object($factarray)) {
				$fact = $factarray->getTag();
			} else {
				if ($type === 'SOUR' || $type === 'REPO') {
					$factrec = $factarray[0];
				}
				if ($type === 'FAM' || $type === 'INDI') {
					$factrec = $factarray[1];
				}

				$ft = preg_match("/1 (\w+)(.*)/", $factrec, $match);
				if ($ft > 0) {
					$fact = trim($match[1]);
				}
			}
			if ($fact !== false) {
				$key = array_search($fact, $uniquefacts);
				if ($key !== false) {
					unset($uniquefacts[$key]);
				}
			}
		}

		return $uniquefacts;
	}

	/**
	 * Print a new fact box on details pages
	 *
	 * @param string $id the id of the person, family, source etc the fact will be added to
	 * @param array $usedfacts an array of facts already used in this record
	 * @param string $type the type of record INDI, FAM, SOUR etc
	 */
	public static function printAddNewFact($id, $usedfacts, $type) {
		global $WT_TREE;

		// -- Add from clipboard
		if (is_array(Session::get('clipboard'))) {
			$newRow = true;
			foreach (array_reverse(Session::get('clipboard'), true) as $fact_id => $fact) {
				if ($fact["type"] == $type || $fact["type"] == 'all') {
					if ($newRow) {
						$newRow = false;
						echo '<tr class="noprint"><td class="descriptionbox">';
						echo I18N::translate('Add from clipboard'), '</td>';
						echo '<td class="optionbox wrap"><form method="get" name="newFromClipboard" action="?" onsubmit="return false;">';
						echo '<select id="newClipboardFact">';
					}
					echo '<option value="', Filter::escapeHtml($fact_id), '">', GedcomTag::getLabel($fact['fact']);
					// TODO use the event class to store/parse the clipboard events
					if (preg_match('/^2 DATE (.+)/m', $fact['factrec'], $match)) {
						$tmp = new Date($match[1]);
						echo '; ', $tmp->minimumDate()->format('%Y');
					}
					if (preg_match('/^2 PLAC ([^,\n]+)/m', $fact['factrec'], $match)) {
						echo '; ', $match[1];
					}
					echo '</option>';
				}
			}
			if (!$newRow) {
				echo '</select>';
				echo '&nbsp;&nbsp;<input type="button" value="', I18N::translate('Add'), "\" onclick=\"return paste_fact('$id', '#newClipboardFact');\"> ";
				echo '</form></td></tr>', "\n";
			}
		}

		// -- Add from pick list
		switch ($type) {
		case "INDI":
			$addfacts    = preg_split("/[, ;:]+/", $WT_TREE->getPreference('INDI_FACTS_ADD'), -1, PREG_SPLIT_NO_EMPTY);
			$uniquefacts = preg_split("/[, ;:]+/", $WT_TREE->getPreference('INDI_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
			$quickfacts  = preg_split("/[, ;:]+/", $WT_TREE->getPreference('INDI_FACTS_QUICK'), -1, PREG_SPLIT_NO_EMPTY);
			break;
		case "FAM":
			$addfacts    = preg_split("/[, ;:]+/", $WT_TREE->getPreference('FAM_FACTS_ADD'), -1, PREG_SPLIT_NO_EMPTY);
			$uniquefacts = preg_split("/[, ;:]+/", $WT_TREE->getPreference('FAM_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
			$quickfacts  = preg_split("/[, ;:]+/", $WT_TREE->getPreference('FAM_FACTS_QUICK'), -1, PREG_SPLIT_NO_EMPTY);
			break;
		case "SOUR":
			$addfacts    = preg_split("/[, ;:]+/", $WT_TREE->getPreference('SOUR_FACTS_ADD'), -1, PREG_SPLIT_NO_EMPTY);
			$uniquefacts = preg_split("/[, ;:]+/", $WT_TREE->getPreference('SOUR_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
			$quickfacts  = preg_split("/[, ;:]+/", $WT_TREE->getPreference('SOUR_FACTS_QUICK'), -1, PREG_SPLIT_NO_EMPTY);
			break;
		case "NOTE":
			$addfacts    = preg_split("/[, ;:]+/", $WT_TREE->getPreference('NOTE_FACTS_ADD'), -1, PREG_SPLIT_NO_EMPTY);
			$uniquefacts = preg_split("/[, ;:]+/", $WT_TREE->getPreference('NOTE_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
			$quickfacts  = preg_split("/[, ;:]+/", $WT_TREE->getPreference('NOTE_FACTS_QUICK'), -1, PREG_SPLIT_NO_EMPTY);
			break;
		case "REPO":
			$addfacts    = preg_split("/[, ;:]+/", $WT_TREE->getPreference('REPO_FACTS_ADD'), -1, PREG_SPLIT_NO_EMPTY);
			$uniquefacts = preg_split("/[, ;:]+/", $WT_TREE->getPreference('REPO_FACTS_UNIQUE'), -1, PREG_SPLIT_NO_EMPTY);
			$quickfacts  = preg_split("/[, ;:]+/", $WT_TREE->getPreference('REPO_FACTS_QUICK'), -1, PREG_SPLIT_NO_EMPTY);
			break;
		default:
			return;
		}
		$addfacts            = array_merge(self::checkFactUnique($uniquefacts, $usedfacts, $type), $addfacts);
		$quickfacts          = array_intersect($quickfacts, $addfacts);
		$translated_addfacts = array();
		foreach ($addfacts as $addfact) {
			$translated_addfacts[$addfact] = GedcomTag::getLabel($addfact);
		}
		uasort($translated_addfacts, function ($x, $y) {
			return I18N::strcasecmp(I18N::translate($x), I18N::translate($y));
		});
		echo '<tr class="noprint"><td class="descriptionbox">';
		echo I18N::translate('Fact or event');
		echo '</td>';
		echo '<td class="optionbox wrap">';
		echo '<form method="get" name="newfactform" action="?" onsubmit="return false;">';
		echo '<select id="newfact" name="newfact">';
		echo '<option value="" disabled selected>' . I18N::translate('&lt;select&gt;') . '</option>';
		foreach ($translated_addfacts as $fact => $fact_name) {
			echo '<option value="', $fact, '">', $fact_name, '</option>';
		}
		if ($type == 'INDI' || $type == 'FAM') {
			echo '<option value="FACT">', I18N::translate('Custom fact'), '</option>';
			echo '<option value="EVEN">', I18N::translate('Custom event'), '</option>';
		}
		echo '</select>';
		echo '<input type="button" value="', I18N::translate('Add'), '" onclick="add_record(\'' . $id . '\', \'newfact\');">';
		echo '<span class="quickfacts">';
		foreach ($quickfacts as $fact) {
			echo '<a href="#" onclick="add_new_record(\'' . $id . '\', \'' . $fact . '\');return false;">', GedcomTag::getLabel($fact), '</a>';
		}
		echo '</span></form>';
		echo '</td></tr>';
	}

	/**
	 * javascript declaration for calendar popup
	 */
	public static function initializeCalendarPopup() {
		global $controller;

		$controller->addInlineJavascript('
			cal_setMonthNames(
				"' . I18N::translateContext('NOMINATIVE', 'January') . '",
				"' . I18N::translateContext('NOMINATIVE', 'February') . '",
				"' . I18N::translateContext('NOMINATIVE', 'March') . '",
				"' . I18N::translateContext('NOMINATIVE', 'April') . '",
				"' . I18N::translateContext('NOMINATIVE', 'May') . '",
				"' . I18N::translateContext('NOMINATIVE', 'June') . '",
				"' . I18N::translateContext('NOMINATIVE', 'July') . '",
				"' . I18N::translateContext('NOMINATIVE', 'August') . '",
				"' . I18N::translateContext('NOMINATIVE', 'September') . '",
				"' . I18N::translateContext('NOMINATIVE', 'October') . '",
				"' . I18N::translateContext('NOMINATIVE', 'November') . '",
				"' . I18N::translateContext('NOMINATIVE', 'December') . '"
			)
			cal_setDayHeaders(
				"' . I18N::translate('Sun') . '",
				"' . I18N::translate('Mon') . '",
				"' . I18N::translate('Tue') . '",
				"' . I18N::translate('Wed') . '",
				"' . I18N::translate('Thu') . '",
				"' . I18N::translate('Fri') . '",
				"' . I18N::translate('Sat') . '"
			)
			cal_setWeekStart(' . I18N::firstDay() . ');
		');
	}

	/**
	 * HTML link to find an individual.
	 *
	 * @param string $element_id
	 * @param string $indiname
	 * @param Tree $tree
	 *
	 * @return string
	 */
	public static function printFindIndividualLink($element_id, $indiname = '', $tree = null) {
		global $WT_TREE;

		if ($tree === null) {
			$tree = $WT_TREE;
		}

		return '<a href="#" onclick="findIndi(document.getElementById(\'' . $element_id . '\'), document.getElementById(\'' . $indiname . '\'), \'' . $tree->getNameHtml() . '\'); return false;" class="icon-button_indi" title="' . I18N::translate('Find an individual') . '"></a>';
	}

	/**
	 * HTML link to find a place.
	 *
	 * @param string $element_id
	 *
	 * @return string
	 */
	public static function printFindPlaceLink($element_id) {
		return '<a href="#" onclick="findPlace(document.getElementById(\'' . $element_id . '\'), WT_GEDCOM); return false;" class="icon-button_place" title="' . I18N::translate('Find a place') . '"></a>';
	}

	/**
	 * HTML link to find a family.
	 *
	 * @param string $element_id
	 *
	 * @return string
	 */
	public static function printFindFamilyLink($element_id) {
		return '<a href="#" onclick="findFamily(document.getElementById(\'' . $element_id . '\'), WT_GEDCOM); return false;" class="icon-button_family" title="' . I18N::translate('Find a family') . '"></a>';
	}

	/**
	 * HTML link to open the special character window.
	 *
	 * @param string $element_id
	 *
	 * @return string
	 */
	public static function printSpecialCharacterLink($element_id) {
		return '<span onclick="findSpecialChar(document.getElementById(\'' . $element_id . '\')); if (window.updatewholename) { updatewholename(); } return false;" class="icon-button_keyboard" title="' . I18N::translate('Find a special character') . '"></span>';
	}

	/**
	 * HTML element to insert a value from a list.
	 *
	 * @param string $element_id
	 * @param string[] $choices
	 */
	public static function printAutoPasteLink($element_id, $choices) {
		echo '<small>';
		foreach ($choices as $choice) {
			echo '<span onclick="document.getElementById(\'', $element_id, '\').value=';
			echo '\'', $choice, '\';';
			echo " return false;\">", $choice, '</span> ';
		}
		echo '</small>';
	}

	/**
	 * HTML link to find a source.
	 *
	 * @param string $element_id
	 * @param string $sourcename
	 *
	 * @return string
	 */
	public static function printFindSourceLink($element_id, $sourcename = '') {
		return '<a href="#" onclick="findSource(document.getElementById(\'' . $element_id . '\'), document.getElementById(\'' . $sourcename . '\'), WT_GEDCOM); return false;" class="icon-button_source" title="' . I18N::translate('Find a source') . '"></a>';
	}

	/**
	 * HTML link to find a note.
	 *
	 * @param string $element_id
	 * @param string $notename
	 *
	 * @return string
	 */
	public static function printFindNoteLink($element_id, $notename = '') {
		return '<a href="#" onclick="findnote(document.getElementById(\'' . $element_id . '\'), document.getElementById(\'' . $notename . '\'), \'WT_GEDCOM\'); return false;" class="icon-button_find" title="' . I18N::translate('Find a shared note') . '"></a>';
	}

	/**
	 * HTML link to find a repository.
	 *
	 * @param string $element_id
	 *
	 * @return string
	 */
	public static function printFindRepositoryLink($element_id) {
		return '<a href="#" onclick="findRepository(document.getElementById(\'' . $element_id . '\'), WT_GEDCOM); return false;" class="icon-button_repository" title="' . I18N::translate('Find a repository') . '"></a>';
	}

	/**
	 * HTML link to find a media object.
	 *
	 * @param string $element_id
	 * @param string $choose
	 *
	 * @return string
	 */
	public static function printFindMediaLink($element_id, $choose = '') {
		return '<a href="#" onclick="findMedia(document.getElementById(\'' . $element_id . '\'), \'' . $choose . '\', WT_GEDCOM); return false;" class="icon-button_media" title="' . I18N::translate('Find a media object') . '"></a>';
	}

	/**
	 * HTML link to find a fact.
	 *
	 * @param string $element_id
	 *
	 * @return string
	 */
	public static function printFindFactLink($element_id) {
		return '<a href="#" onclick="findFact(document.getElementById(\'' . $element_id . '\'), WT_GEDCOM); return false;" class="icon-button_find_facts" title="' . I18N::translate('Find a fact or event') . '"></a>';
	}

	/**
	 * Summary of LDS ordinances.
	 *
	 * @param Individual $individual
	 *
	 * @return string
	 */
	public static function getLdsSummary(Individual $individual) {
		$BAPL = $individual->getFacts('BAPL') ? 'B' : '_';
		$ENDL = $individual->getFacts('ENDL') ? 'E' : '_';
		$SLGC = $individual->getFacts('SLGC') ? 'C' : '_';
		$SLGS = '_';

		foreach ($individual->getSpouseFamilies() as $family) {
			if ($family->getFacts('SLGS')) {
				$SLGS = '';
			}
		}

		return $BAPL . $ENDL . $SLGS . $SLGC;
	}
}

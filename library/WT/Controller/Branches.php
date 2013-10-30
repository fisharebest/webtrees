<?php
// Controller for the branches list
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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

class WT_Controller_Branches extends WT_Controller_Page {
	private $surname;
	private $soundex_std;
	private $soundex_dm;
	private $individuals = array(); // Everyone with the selected surname
	private $ancestors   = array(); // Ancestors of the root person - for SOSA numbers

	public function __construct() {
		parent::__construct();

		$this->surname     = WT_Filter::get('surname');
		$this->soundex_std = WT_Filter::getBool('soundex_std');
		$this->soundex_dm  = WT_Filter::getBool('soundex_dm');

		if ($this->surname) {
			$this->setPageTitle(/* I18N: %s is a surname */ WT_I18N::translate('Branches of the %s family', WT_Filter::escapeHtml($this->surname)));
			$this->loadIndividuals();
			$this->loadAncestors(WT_Individual::getInstance(WT_USER_GEDCOM_ID), 1);
		} else {
			$this->setPageTitle(/* I18N: Branches of a family tree */ WT_I18N::translate('Branches'));
		}
	}

	// Page parameters
	public function getSurname() {
		return $this->surname;
	}

	public function getSoundexStd() {
		return $this->soundex_std;
	}

	public function getSoundexDm() {
		return $this->soundex_dm;
	}

	// Fetch all individuals with a matching surname
	private function loadIndividuals() {
		$sql=
			"SELECT DISTINCT i_id AS xref, i_file AS gedcom_id, i_gedcom AS gedcom".
			" FROM `##individuals`".
			" JOIN `##name` ON (i_id=n_id AND i_file=n_file)".
			" WHERE n_file=?".
			" AND n_type!=?".
			" AND (n_surn=? OR n_surname=?";
		$args=array(WT_GED_ID, '_MARNM', $this->surname, $this->surname);
		if ($this->soundex_std) {
			foreach (explode(':', WT_Soundex::soundex_std($surn)) as $value) {
				$sql .= " OR n_soundex_surn_std LIKE CONCAT('%', ?, '%')";
				$args[]=$value;
			}
		}
		if ($this->soundex_dm) {
			foreach (explode(':', WT_Soundex::soundex_dm($surn)) as $value) {
				$sql .= " OR n_soundex_surn_dm LIKE CONCAT('%', ?, '%')";
				$args[]=$value;
			}
		}
		$sql .= ')';
		$rows = WT_DB::prepare($sql)->execute($args)->fetchAll();
		$this->individuals = array();
		foreach ($rows as $row) {
			$this->individuals[] = WT_Individual::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
		}
		// Sort by birth date, oldest first
		usort($this->individuals, array('WT_Individual', 'CompareBirtDate'));
	}

	private function loadAncestors(WT_Individual $ancestor, $sosa) {
		if ($ancestor) {
			$this->ancestors[$sosa] = $ancestor;
			foreach ($ancestor->getChildFamilies() as $family) {
				foreach ($family->getSpouses() as $parent) {
					$this->loadAncestors($parent, $sosa * 2 + ($parent->getSex() == 'F'));
				}
			}
		}
	}

	// List individuals without an ancestor (with the same surname)
	public function getPatriarchsHtml() {
		$html = '';
		foreach ($this->individuals as $individual) {
			foreach ($individual->getChildFamilies() as $family) {
				foreach ($family->getSpouses() as $parent) {
					if (in_array($parent, $this->individuals, true)) {
						continue 3;
					}
				}
			}
			$html .= $this->getDescendantsHtml($individual);
		}
		return $html;
	}

	// Generate a recursive list of descendants of an individual.
	// If parents are specified, we can also show the pedigree (adopted, etc.).
	private function getDescendantsHtml(WT_Individual $individual, WT_Family $parents=null) {
		// A person has many names.  Select the one that matches the searched surname
		$person_name = '';
		foreach ($individual->getAllNames() as $n=>$name) {
			list($surn1) = explode(",", $name['sort']);
			if (
				// one name is a substring of the other
				stripos($surn1, $this->surname) !== false ||
				stripos($this->surname, $surn1) !== false ||
				// one name sounds like the other
				$this->soundex_std && WT_Soundex::compare(WT_Soundex::soundex_std($surn1), WT_Soundex::soundex_std($this->surname)) ||
				$this->soundex_dm  && WT_Soundex::compare(WT_Soundex::soundex_dm ($surn1), WT_Soundex::soundex_dm ($this->surname))
			) {
				$person_name = $name['full'];
				break;
			}
		}

		// No matching name?  Typically children with a different surname.  The tree stops here.
		if (!$person_name) {
			return '<li title="' . strip_tags($individual->getFullName()) . '">' . $individual->getSexImage() . '…</li>';
		}

		// Is this individual one of our ancestors?
		$sosa = array_search($individual, $this->ancestors);
		if ($sosa) {
			$sosa_class = 'search_hit';
			$sosa_html  = ' <a class="details1 ' . $individual->getBoxStyle() . '" title="' . WT_I18N::translate('Sosa') . '" href="relationship.php?pid2=' . WT_USER_ROOT_ID . '&amp;pid1=' . $individual->getXref() . '">' . $sosa . '</a>' . self::sosaGeneration($sosa);
		} else {
			$sosa_class = '';
			$sosa_html  = '';
		}
		
		// Generate HTML for this individual, and all their descendants
		$indi_html = $individual->getSexImage() . '<a class="' . $sosa_class . '" href="' . $individual->getHtmlUrl() . '">' . $person_name . '</a> ' .  $individual->getLifeSpan() . $sosa_html;

		// If this is not a birth pedigree (e.g. an adoption), highlight it
		if ($parents) {
			$pedi = '';
			foreach ($individual->getFacts('FAMC') as $fact) {
				if ($fact->getTarget() === $parents) {
					$pedi = $fact->getAttribute('PEDI');
					break;
				}
			}
			if ($pedi && $pedi != 'birth') {
				$indi_html = '<span class="red">'.WT_Gedcom_Code_Pedi::getValue($pedi, $individual).'</span> ' . $indi_html;
			}
		}
		
		// spouses and children
		$spouse_families = $individual->getSpouseFamilies();
		if ($spouse_families) {
			$fam_html = '';
			foreach ($spouse_families as $family) {
				$fam_html .= $indi_html; // Repeat the individual details for each spouse.

				$spouse = $family->getSpouse($individual);
				if ($spouse) {
					$sosa = array_search($spouse->getXref(), $this->ancestors);
					if ($sosa) {
						$sosa_class = 'search_hit';
						$sosa_html  = ' <a class="details1 ' . $spouse->getBoxStyle() . '" title="' . WT_I18N::translate('Sosa') . '" href="relationship.php?pid2=' . WT_USER_ROOT_ID . '&amp;pid1=' . $spouse->getXref() . '"> '.$sosa.' </a>' . self::sosaGeneration($sosa2);
					} else {
						$sosa_class = '';
						$sosa_html  = '';
					}
					$marriage_year = $family->getMarriageYear();
					if ($marriage_year) {
						$fam_html .= ' <a href="' . $family->getHtmlUrl() . '" title="' . strip_tags($family->getMarriageDate()->Display()) . '"><i class="icon-rings"></i>' . $marriage_year . '</a>';
					} elseif ($family->getFirstFact('MARR')) {
						$fam_html .= ' <a href="' . $family->getHtmlUrl() . '" title="' . WT_Gedcom_Tag::getLabel('MARR') . '"><i class="icon-rings"></i></a>';
					} elseif ($family->getFirstFact('_NMR')) {
						$fam_html .= ' <a href="' . $family->getHtmlUrl() . '" title="' . WT_Gedcom_Tag::getLabel('_NMR') . '"><i class="icon-rings"></i></a>';
					}
					$fam_html .= ' ' . $spouse->getSexImage() . '<a class="' . $sosa_class . '" href="' . $spouse->getHtmlUrl() . '">' . $spouse->getFullName() . '</a> ' . $spouse->getLifeSpan() . ' ' . $sosa_html;
				}

				$fam_html .= '<ol>';
				foreach ($family->getChildren() as $child) {
					$fam_html .= $this->getDescendantsHtml($child, $family);
				}
				$fam_html .= '</ol>';
			}
			return '<li>' . $fam_html . '</li>';
		} else {
			// No spouses - just show the individual
			return '<li>' . $indi_html . '</li>';
		}
	}

	private static function sosaGeneration($sosa) {
		$generation = (int)log($sosa, 2) + 1;
		return '<sup title="' . WT_I18N::translate('Generation') . '">' . $generation . '</sup>';
	}
}

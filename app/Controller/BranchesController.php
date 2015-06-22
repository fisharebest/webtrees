<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
namespace Fisharebest\Webtrees\Controller;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\GedcomCode\GedcomCodePedi;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Soundex;

/**
 * Controller for the branches list
 */
class BranchesController extends PageController {
	/** @var string Generate the branches for this surname */
	private $surname;

	/** @var bool Whether to use Standard phonetic matching */
	private $soundex_std;

	/** @var bool Whether to use Daitch-Mokotov phonetic matching */
	private $soundex_dm;

	/** @var Individual[] Everyone with the selected surname */
	private $individuals = array();

	/** @var Individual[] Ancestors of the root person - for SOSA numbers */
	private $ancestors = array();

	/**
	 * Create a branches list controller
	 */
	public function __construct() {
		global $WT_TREE;

		parent::__construct();

		$this->surname     = Filter::get('surname');
		$this->soundex_std = Filter::getBool('soundex_std');
		$this->soundex_dm  = Filter::getBool('soundex_dm');

		if ($this->surname) {
			$this->setPageTitle(/* I18N: %s is a surname */
				I18N::translate('Branches of the %s family', Filter::escapeHtml($this->surname)));
			$this->loadIndividuals();
			$self = Individual::getInstance($WT_TREE->getUserPreference(Auth::user(), 'gedcomid'), $WT_TREE);
			if ($self) {
				$this->loadAncestors($self, 1);
			}
		} else {
			$this->setPageTitle(/* I18N: Branches of a family tree */ I18N::translate('Branches'));
		}
	}

	/**
	 * The surname to be used on this page.
	 *
	 * @return null|string
	 */
	public function getSurname() {
		return $this->surname;
	}

	/**
	 * Should we use Standard phonetic matching
	 *
	 * @return bool
	 */
	public function getSoundexStd() {
		return $this->soundex_std;
	}

	/**
	 * Should we use Daitch-Mokotov phonetic matching
	 *
	 * @return bool
	 */
	public function getSoundexDm() {
		return $this->soundex_dm;
	}

	/**
	 * Fetch all individuals with a matching surname
	 */
	private function loadIndividuals() {
		global $WT_TREE;

		$sql =
			"SELECT DISTINCT i_id AS xref, i_gedcom AS gedcom" .
			" FROM `##individuals`" .
			" JOIN `##name` ON (i_id=n_id AND i_file=n_file)" .
			" WHERE n_file = ?" .
			" AND n_type != ?" .
			" AND (n_surn = ? OR n_surname = ?";
		$args = array($WT_TREE->getTreeId(), '_MARNM', $this->surname, $this->surname);
		if ($this->soundex_std) {
			$sdx = Soundex::russell($this->surname);
			if ($sdx !== null) {
				foreach (explode(':', $sdx) as $value) {
					$sql .= " OR n_soundex_surn_std LIKE CONCAT('%', ?, '%')";
					$args[] = $value;
				}
			}
		}
		if ($this->soundex_dm) {
			$sdx = Soundex::daitchMokotoff($this->surname);
			if ($sdx !== null) {
				foreach (explode(':', $sdx) as $value) {
					$sql .= " OR n_soundex_surn_dm LIKE CONCAT('%', ?, '%')";
					$args[] = $value;
				}
			}
		}
		$sql .= ')';
		$rows              = Database::prepare($sql)->execute($args)->fetchAll();
		$this->individuals = array();
		foreach ($rows as $row) {
			$this->individuals[] = Individual::getInstance($row->xref, $WT_TREE, $row->gedcom);
		}
		// Sort by birth date, oldest first
		usort($this->individuals, '\Fisharebest\Webtrees\Individual::compareBirthDate');
	}

	/**
	 * Load the ancestors of an individual, so we can highlight them in the list
	 *
	 * @param Individual $ancestor
	 * @param int        $sosa
	 */
	private function loadAncestors(Individual $ancestor, $sosa) {
		if ($ancestor) {
			$this->ancestors[$sosa] = $ancestor;
			foreach ($ancestor->getChildFamilies() as $family) {
				foreach ($family->getSpouses() as $parent) {
					$this->loadAncestors($parent, $sosa * 2 + ($parent->getSex() == 'F' ? 1 : 0));
				}
			}
		}
	}

	/**
	 * For each individual with no ancestors, list their descendants.
	 *
	 * @return string
	 */
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

	/**
	 * Generate a recursive list of descendants of an individual.
	 * If parents are specified, we can also show the pedigree (adopted, etc.).
	 *
	 * @param Individual  $individual
	 * @param Family|null $parents
	 *
	 * @return string
	 */
	private function getDescendantsHtml(Individual $individual, Family $parents = null) {
		// A person has many names.  Select the one that matches the searched surname
		$person_name = '';
		foreach ($individual->getAllNames() as $name) {
			list($surn1) = explode(",", $name['sort']);
			if (// one name is a substring of the other
				stripos($surn1, $this->surname) !== false ||
				stripos($this->surname, $surn1) !== false ||
				// one name sounds like the other
				$this->soundex_std && Soundex::compare(Soundex::russell($surn1), Soundex::russell($this->surname)) ||
				$this->soundex_dm && Soundex::compare(Soundex::daitchMokotoff($surn1), Soundex::daitchMokotoff($this->surname))
			) {
				$person_name = $name['full'];
				break;
			}
		}

		// No matching name?  Typically children with a different surname.  The branch stops here.
		if (!$person_name) {
			return '<li title="' . strip_tags($individual->getFullName()) . '">' . $individual->getSexImage() . '…</li>';
		}

		// Is this individual one of our ancestors?
		$sosa = array_search($individual, $this->ancestors, true);
		if ($sosa) {
			$sosa_class = 'search_hit';
			$sosa_html  = ' <a class="details1 ' . $individual->getBoxStyle() . '" title="' . I18N::translate('Sosa') . '" href="relationship.php?pid2=' . $this->ancestors[1]->getXref() . '&amp;pid1=' . $individual->getXref() . '">' . $sosa . '</a>' . self::sosaGeneration($sosa);
		} else {
			$sosa_class = '';
			$sosa_html  = '';
		}

		// Generate HTML for this individual, and all their descendants
		$indi_html = $individual->getSexImage() . '<a class="' . $sosa_class . '" href="' . $individual->getHtmlUrl() . '">' . $person_name . '</a> ' . $individual->getLifeSpan() . $sosa_html;

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
				$indi_html = '<span class="red">' . GedcomCodePedi::getValue($pedi, $individual) . '</span> ' . $indi_html;
			}
		}

		// spouses and children
		$spouse_families = $individual->getSpouseFamilies();
		if ($spouse_families) {
			usort($spouse_families, '\Fisharebest\Webtrees\Family::compareMarrDate');
			$fam_html = '';
			foreach ($spouse_families as $family) {
				$fam_html .= $indi_html; // Repeat the individual details for each spouse.

				$spouse = $family->getSpouse($individual);
				if ($spouse) {
					$sosa = array_search($spouse, $this->ancestors, true);
					if ($sosa) {
						$sosa_class = 'search_hit';
						$sosa_html  = ' <a class="details1 ' . $spouse->getBoxStyle() . '" title="' . I18N::translate('Sosa') . '" href="relationship.php?pid2=' . $this->ancestors[1]->getXref() . '&amp;pid1=' . $spouse->getXref() . '"> ' . $sosa . ' </a>' . self::sosaGeneration($sosa);
					} else {
						$sosa_class = '';
						$sosa_html  = '';
					}
					$marriage_year = $family->getMarriageYear();
					if ($marriage_year) {
						$fam_html .= ' <a href="' . $family->getHtmlUrl() . '" title="' . strip_tags($family->getMarriageDate()->display()) . '"><i class="icon-rings"></i>' . $marriage_year . '</a>';
					} elseif ($family->getFirstFact('MARR')) {
						$fam_html .= ' <a href="' . $family->getHtmlUrl() . '" title="' . GedcomTag::getLabel('MARR') . '"><i class="icon-rings"></i></a>';
					} elseif ($family->getFirstFact('_NMR')) {
						$fam_html .= ' <a href="' . $family->getHtmlUrl() . '" title="' . GedcomTag::getLabel('_NMR') . '"><i class="icon-rings"></i></a>';
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

	/**
	 * Convert a SOSA number into a generation number.  e.g. 8 = great-grandfather = 3 generations
	 *
	 * @param int $sosa
	 *
	 * @return string
	 */
	private static function sosaGeneration($sosa) {
		$generation = (int) log($sosa, 2) + 1;

		return '<sup title="' . I18N::translate('Generation') . '">' . $generation . '</sup>';
	}
}

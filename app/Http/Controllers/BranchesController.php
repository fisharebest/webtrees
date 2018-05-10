<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomCode\GedcomCodePedi;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Soundex;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Find all branches of families with a given surname.
 */
class BranchesController extends AbstractBaseController {
	/**
	 * A form to request the page parameters.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function page(Request $request): Response {
		$surname     = $request->get('surname', '');
		$soundex_std = (bool) $request->get('soundex_std');
		$soundex_dm  = (bool) $request->get('soundex_dm');

		if ($surname !== '') {
			$title = /* I18N: %s is a surname */
				I18N::translate('Branches of the %s family', e($surname));
		} else {
			$title = /* I18N: Branches of a family tree */
				I18N::translate('Branches');
		}

		return $this->viewResponse('branches-page', [
			'soundex_dm'  => $soundex_dm,
			'soundex_std' => $soundex_std,
			'surname'     => $surname,
			'title'       => $title,
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function list(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		/** @var User $user */
		$user = $request->attributes->get('user');

		$soundex_dm  = (bool) $request->get('soundex_dm');
		$soundex_std = (bool) $request->get('soundex_std');
		$surname     = $request->get('surname', '');

		// Highlight direct-line ancestors of this individual.
		$self = Individual::getInstance($tree->getUserPreference($user, 'gedcomid'), $tree);

		if ($surname !== '') {
			$individuals = $this->loadIndividuals($surname, $soundex_dm, $soundex_std);
		} else {
			$individuals = [];
		}

		if ($self !== null) {
			$ancestors = $this->allAncestors($self);
		} else {
			$ancestors = [];
		}

		// @TODO - convert this to use views
		$html = view('branches-list', [
			'branches' => $this->getPatriarchsHtml($individuals, $ancestors, $surname, $soundex_dm, $soundex_std),
		]);

		return new Response($html);
	}

	/**
	 * Find all ancestors of an individual, indexed by the Sosa-Stradonitz number.
	 *
	 * @param Individual $individual
	 *
	 * @return Individual[]
	 */
	protected function allAncestors(Individual $individual): array {
	    /** @var Individual[] $ancestors */
        $ancestors = [
            1 => $individual,
        ];

        do {
            $sosa = key($ancestors);

			$family = $ancestors[$sosa]->getPrimaryChildFamily();

			if ($family !== null) {
                if ($family->getHusband() !== null) {
                    $ancestors[$sosa * 2] = $family->getHusband();
                }
                if ($family->getWife() !== null) {
                    $ancestors[$sosa * 2 + 1] = $family->getWife();
                }
            }
		} while (next($ancestors));

		return $ancestors;
	}

	/**
	 * Fetch all individuals with a matching surname
	 *
	 * @param string $surname
	 * @param bool   $soundex_dm
	 * @param bool   $soundex_std
	 *
	 * @return Individual[]
	 */
	private function loadIndividuals(string $surname, bool $soundex_dm, bool $soundex_std): array {
		$sql =
			"SELECT DISTINCT i_id AS xref, i_gedcom AS gedcom" .
			" FROM `##individuals`" .
			" JOIN `##name` ON (i_id=n_id AND i_file=n_file)" .
			" WHERE n_file = ?" .
			" AND n_type != ?" .
			" AND (n_surn = ? OR n_surname = ?";

		$args = [
			$this->tree()->getTreeId(),
			'_MARNM',
			$surname,
			$surname,
		];
		if ($soundex_std) {
			$sdx = Soundex::russell($surname);
			if ($sdx !== '') {
				foreach (explode(':', $sdx) as $value) {
					$sql    .= " OR n_soundex_surn_std LIKE CONCAT('%', ?, '%')";
					$args[] = $value;
				}
			}
		}

		if ($soundex_dm) {
			$sdx = Soundex::daitchMokotoff($surname);
			if ($sdx !== '') {
				foreach (explode(':', $sdx) as $value) {
					$sql    .= " OR n_soundex_surn_dm LIKE CONCAT('%', ?, '%')";
					$args[] = $value;
				}
			}
		}
		$sql .= ')';

		$rows = Database::prepare($sql)->execute($args)->fetchAll();

		$individuals = [];
		foreach ($rows as $row) {
			$individuals[] = Individual::getInstance($row->xref, $this->tree(), $row->gedcom);
		}

		usort($individuals, '\Fisharebest\Webtrees\Individual::compareBirthDate');

		return $individuals;
	}

	/**
	 * For each individual with no ancestors, list their descendants.
	 *
	 * @param Individual[] $individuals
	 * @param Individual[] $ancestors
	 * @param string       $surname
	 * @param bool         $soundex_dm
	 * @param bool         $soundex_std
	 *
	 * @return string
	 */
	public function getPatriarchsHtml(array $individuals, array $ancestors, string $surname, bool $soundex_dm, bool $soundex_std): string {
		$html = '';
		foreach ($individuals as $individual) {
			foreach ($individual->getChildFamilies() as $family) {
				foreach ($family->getSpouses() as $parent) {
					if (in_array($parent, $individuals, true)) {
						continue 3;
					}
				}
			}
			$html .= $this->getDescendantsHtml($individuals, $ancestors, $surname, $soundex_dm, $soundex_std, $individual, null);
		}

		return $html;
	}

	/**
	 * Generate a recursive list of descendants of an individual.
	 * If parents are specified, we can also show the pedigree (adopted, etc.).
	 *
	 * @param array       $individuals
	 * @param array       $ancestors
	 * @param string      $surname
	 * @param bool        $soundex_dm
	 * @param bool        $soundex_std
	 * @param Individual  $individual
	 * @param Family|null $parents
	 *
	 * @return string
	 */
	private function getDescendantsHtml(array $individuals, array $ancestors, string $surname, bool $soundex_dm, bool $soundex_std, Individual $individual, Family $parents = null) {
		// A person has many names. Select the one that matches the searched surname
		$person_name = '';
		foreach ($individual->getAllNames() as $name) {
			list($surn1) = explode(',', $name['sort']);
			if (// one name is a substring of the other
				stripos($surn1, $surname) !== false ||
				stripos($surname, $surn1) !== false ||
				// one name sounds like the other
				$soundex_std && Soundex::compare(Soundex::russell($surn1), Soundex::russell($surname)) ||
				$soundex_dm && Soundex::compare(Soundex::daitchMokotoff($surn1), Soundex::daitchMokotoff($surname))
			) {
				$person_name = $name['full'];
				break;
			}
		}

		// No matching name? Typically children with a different surname. The branch stops here.
		if (!$person_name) {
			return '<li title="' . strip_tags($individual->getFullName()) . '">' . $individual->getSexImage() . '…</li>';
		}

		// Is this individual one of our ancestors?
		$sosa = array_search($individual, $ancestors, true);
		if ($sosa !== false) {
			$sosa_class = 'search_hit';
			$sosa_html  = ' <a class="details1 ' . $individual->getBoxStyle() . '" title="' . I18N::translate('Sosa') . '" href="' . e(route('relationships', [
					'xref1' => $individual->getXref(),
					'xref2' => $ancestors[1]->getXref(),
					'ged'   => $individual->getTree()->getName(),
				])) . '" rel="nofollow">' . $sosa . '</a>' . self::sosaGeneration($sosa);
		} else {
			$sosa_class = '';
			$sosa_html  = '';
		}

		// Generate HTML for this individual, and all their descendants
		$indi_html = $individual->getSexImage() . '<a class="' . $sosa_class . '" href="' . e($individual->url()) . '">' . $person_name . '</a> ' . $individual->getLifeSpan() . $sosa_html;

		// If this is not a birth pedigree (e.g. an adoption), highlight it
		if ($parents) {
			$pedi = '';
			foreach ($individual->getFacts('FAMC') as $fact) {
				if ($fact->getTarget() === $parents) {
					$pedi = $fact->getAttribute('PEDI');
					break;
				}
			}
			if ($pedi !== '' && $pedi !== 'birth') {
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
					$sosa = array_search($spouse, $ancestors, true);
					if ($sosa) {
						$sosa_class = 'search_hit';
						$sosa_html  = ' <a class="details1 ' . $spouse->getBoxStyle() . '" title="' . I18N::translate('Sosa') . '" href="' . e(route('relationships', [
								'xref2' => $ancestors[1]->getXref(),
								'ged'   => $individual->getTree()->getName(),
							])) . '" rel="nofollow"> ' . $sosa . ' </a>' . self::sosaGeneration($sosa);
					} else {
						$sosa_class = '';
						$sosa_html  = '';
					}
					$marriage_year = $family->getMarriageYear();
					if ($marriage_year) {
						$fam_html .= ' <a href="' . e($family->url()) . '" title="' . strip_tags($family->getMarriageDate()->display()) . '"><i class="icon-rings"></i>' . $marriage_year . '</a>';
					} elseif ($family->getFirstFact('MARR')) {
						$fam_html .= ' <a href="' . e($family->url()) . '" title="' . I18N::translate('Marriage') . '"><i class="icon-rings"></i></a>';
					} else {
						$fam_html .= ' <a href="' . e($family->url()) . '" title="' . I18N::translate('Not married') . '"><i class="icon-rings"></i></a>';
					}
					$fam_html .= ' ' . $spouse->getSexImage() . '<a class="' . $sosa_class . '" href="' . e($spouse->url()) . '">' . $spouse->getFullName() . '</a> ' . $spouse->getLifeSpan() . ' ' . $sosa_html;
				}

				$fam_html .= '<ol>';
				foreach ($family->getChildren() as $child) {
					$fam_html .= $this->getDescendantsHtml($individuals, $ancestors, $surname, $soundex_dm, $soundex_std, $child, $family);
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
	 * Convert a SOSA number into a generation number. e.g. 8 = great-grandfather = 3 generations
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

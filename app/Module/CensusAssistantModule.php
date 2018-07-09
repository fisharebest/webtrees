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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Census\Census;
use Fisharebest\Webtrees\Census\CensusInterface;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsDb;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CensusAssistantModule
 */
class CensusAssistantModule extends AbstractModule {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */
			I18N::translate('Census assistant');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Census assistant” module */
			I18N::translate('An alternative way to enter census transcripts and link them to individuals.');
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function getCensusHeaderAction(Request $request): Response {
		$census = $request->get('census');

		$html = $this->censusTableHeader(new $census);

		return new Response($html);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function getCensusIndividualAction(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$census = $request->get('census');

		$individual = Individual::getInstance($request->get('xref'), $tree);
		$head       = Individual::getInstance($request->get('head'), $tree);
		$html       = $this->censusTableRow(new $census, $individual, $head);

		return new Response($html);
	}

	/**
	 * @param Individual $individual
	 *
	 * @return string
	 */
	public function createCensusAssistant(Individual $individual) {
		return view('modules/census-assistant', [
			'individual' => $individual,
		]);
	}

	/**
	 * @param Individual $individual
	 * @param string     $fact_id
	 * @param string     $newged
	 * @param bool       $keep_chan
	 *
	 * @return string
	 */
	public function updateCensusAssistant(Individual $individual, $fact_id, $newged, $keep_chan) {
		$ca_title       = Filter::post('ca_title');
		$ca_place       = Filter::post('ca_place');
		$ca_citation    = Filter::post('ca_citation');
		$ca_individuals = Filter::postArray('ca_individuals');
		$ca_notes       = Filter::post('ca_notes');
		$ca_census      = Filter::post('ca_census', 'Fisharebest\\\\Webtrees\\\\Census\\\\CensusOf[A-Za-z0-9]+');

		if ($ca_census !== '' && !empty($ca_individuals)) {
			$census = new $ca_census;

			$note_text   = $this->createNoteText($census, $ca_title, $ca_place, $ca_citation, $ca_individuals, $ca_notes);
			$note_gedcom = '0 @new@ NOTE ' . str_replace("\n", "\n1 CONT ", $note_text);
			$note        = $individual->getTree()->createRecord($note_gedcom);

			$newged .= "\n2 NOTE @" . $note->getXref() . '@';

			// Add the census fact to the rest of the household
			foreach (array_keys($ca_individuals) as $xref) {
				if ($xref !== $individual->getXref()) {
					Individual::getInstance($xref, $individual->getTree())
						->updateFact($fact_id, $newged, !$keep_chan);
				}
			}
		}

		return $newged;
	}

	/**
	 * @param CensusInterface $census
	 * @param string          $ca_title
	 * @param string          $ca_place
	 * @param string          $ca_citation
	 * @param string[][]      $ca_individuals
	 * @param string          $ca_notes
	 *
	 * @return string
	 */
	private function createNoteText(CensusInterface $census, $ca_title, $ca_place, $ca_citation, $ca_individuals, $ca_notes) {
		$text = $ca_title . "\n" . $ca_citation . "\n" . $ca_place . "\n\n";

		foreach ($census->columns() as $n => $column) {
			if ($n === 0) {
				$text .= "\n";
			} else {
				$text .= ' | ';
			}
			$text .= $column->abbreviation();
		}

		foreach ($census->columns() as $n => $column) {
			if ($n === 0) {
				$text .= "\n";
			} else {
				$text .= ' | ';
			}
			$text .= '-----';
		}

		foreach ($ca_individuals as $xref => $columns) {
			$text .= "\n" . implode(' | ', $columns);
		}

		return $text . "\n\n" . $ca_notes;
	}

	/**
	 * Generate an HTML row of data for the census header
	 * Add prefix cell (store XREF and drag/drop)
	 * Add suffix cell (delete button)
	 *
	 * @param CensusInterface $census
	 *
	 * @return string
	 */
	public static function censusTableHeader(CensusInterface $census) {
		$html = '';
		foreach ($census->columns() as $column) {
			$html .= '<th class="wt-census-assistant-field" title="' . $column->title() . '">' . $column->abbreviation() . '</th>';
		}

		return '<tr class="wt-census-assistant-row"><th hidden></th>' . $html . '<th></th></tr>';
	}

	/**
	 * Generate an HTML row of data for the census
	 * Add prefix cell (store XREF and drag/drop)
	 * Add suffix cell (delete button)
	 *
	 * @param CensusInterface $census
	 *
	 * @return string
	 */
	public static function censusTableEmptyRow(CensusInterface $census) {
		return '<tr class="wt-census-assistant-row"><td hidden></td>' . str_repeat('<td class="wt-census-assistant-field"><input type="text" class="form-control wt-census-assistant-form-control"></td>', count($census->columns())) . '<td><a class="icon-remove" href="#" title="' . I18N::translate('Remove') . '"></a></td></tr>';
	}

	/**
	 * Generate an HTML row of data for the census
	 * Add prefix cell (store XREF and drag/drop)
	 * Add suffix cell (delete button)
	 *
	 * @param CensusInterface $census
	 * @param Individual      $individual
	 * @param Individual      $head
	 *
	 * @return string
	 */
	public static function censusTableRow(CensusInterface $census, Individual $individual, Individual $head) {
		$html = '';
		foreach ($census->columns() as $column) {
			$html .= '<td class="wt-census-assistant-field"><input class="form-control wt-census-assistant-form-control" type="text" value="' . $column->generate($individual, $head) . '" name="ca_individuals[' . $individual->getXref() . '][]"></td>';
		}

		return '<tr class="wt-census-assistant-row"><td class="wt-census-assistant-field" hidden>' . $individual->getXref() . '</td>' . $html . '<td class="wt-census-assistant-field"><a class="icon-remove" href="#" title="' . I18N::translate('Remove') . '"></a></td></tr>';
	}
}

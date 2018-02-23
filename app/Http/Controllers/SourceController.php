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

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the source page.
 */
class SourceController extends AbstractBaseController {
	/**
	 * Show a repository's page.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function show(Request $request): Response {
		/** @var Tree $tree */
		$tree   = $request->attributes->get('tree');
		$xref   = $request->get('xref');
		$record = Source::getInstance($xref, $tree);

		$this->checkSourceAccess($record, false);

		return $this->viewResponse('source-page', [
			'facts'         => $this->facts($record),
			'families'      => $record->linkedFamilies('SOUR'),
			'individuals'   => $record->linkedIndividuals('SOUR'),
			'meta_robots'   => 'index,follow',
			'notes'         => $record->linkedNotes('SOUR'),
			'media_objects' => $record->linkedMedia('SOUR'),
			'source'        => $record,
			'title'         => $record->getFullName(),
		]);
	}

	/**
	 * @param Source $record
	 *
	 * @return array
	 */
	private function facts(Source $record): array {
		$facts = $record->getFacts();

		usort(
			$facts,
			function (Fact $x, Fact $y) {
				static $order = [
					'TITL' => 0,
					'ABBR' => 1,
					'AUTH' => 2,
					'DATA' => 3,
					'PUBL' => 4,
					'TEXT' => 5,
					'NOTE' => 6,
					'OBJE' => 7,
					'REFN' => 8,
					'RIN'  => 9,
					'_UID' => 10,
					'CHAN' => 11,
				];

				return
					(array_key_exists($x->getTag(), $order) ? $order[$x->getTag()] : PHP_INT_MAX)
					-
					(array_key_exists($y->getTag(), $order) ? $order[$y->getTag()] : PHP_INT_MAX);
			}
		);

		return $facts;
	}
}

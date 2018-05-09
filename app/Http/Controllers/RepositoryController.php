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
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the repository page.
 */
class RepositoryController extends AbstractBaseController {
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
		$record = Repository::getInstance($xref, $tree);

		$this->checkRepositoryAccess($record, false);

		return $this->viewResponse('repository-page', [
			'facts'       => $this->facts($record),
			'meta_robots' => 'index,follow',
			'repository'  => $record,
			'sources'     => $record->linkedSources('REPO'),
			'title'       => $record->getFullName(),
		]);
	}

	/**
	 * @param Repository $record
	 *
	 * @return array
	 */
	private function facts(Repository $record): array {
		$facts = $record->getFacts();

		usort(
			$facts,
			function (Fact $x, Fact $y) {
				static $order = [
					'NAME' => 0,
					'ADDR' => 1,
					'NOTE' => 2,
					'WWW'  => 3,
					'REFN' => 4,
					'RIN'  => 5,
					'_UID' => 6,
					'CHAN' => 7,
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

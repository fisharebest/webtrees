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
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PlaceHierarchyController
 */
class PlaceHierarchyController extends AbstractBaseController {
	const MAP_MODULE = 'openstreetmap';

	/**
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
	public function show(Request $request): Response {
		$action     = $request->query->get('action', 'hierarchy');
		$parent     = $request->query->get('parent', []);
		$tree       = $request->attributes->get('tree');
		$fqpn       = implode(Place::GEDCOM_SEPARATOR, array_reverse($parent));
		$place      = new Place($fqpn, $tree);
		$content    = '';
		$osm_module = Module::getModuleByName(self::MAP_MODULE);
		$method     = 'assets';
		$showmap    = $osm_module &&
			(bool)$osm_module->getPreference('place_hierarchy') &&
			method_exists($osm_module, $method) &&
			strpos($action, 'hierarchy') === 0;
		$data       = null;
		$note       = false;

		if ($showmap) {
			$note    = true;
			$content .= view('modules/openstreetmap/map',
				[
					'assets' => $osm_module->$method(),
					'module' => self::MAP_MODULE,
					'ref'    => $fqpn,
					'type'   => 'placelist',
				]
			);
		}

		switch ($action) {
			case 'list':
				$nextaction      = ['hierarchy' => I18N::translate('Show place hierarchy')];
				$content         .= view('place-list', $this->getList($tree));
				break;
			case 'hierarchy':
			case 'hierarchy-e':
				$nextaction      = ['list' => I18N::translate('Show all places in a list')];
				$data            = $this->getHierarchy($tree, $place, $parent);
				$content         .= (null === $data || $showmap) ? '' : view('place-hierarchy', $data);
				if (null === $data || $action === 'hierarchy-e') {
					$content .= view('place-events', $this->getEvents($tree, $place));
				}
				break;
			default:
				throw new NotFoundHttpException('Invalid action');
		}

		$breadcrumbs = $this->breadcrumbs($place);

		return $this->viewResponse(
			'places-page',
			[
				'title'           => I18N::translate('Places'),
				'note'            => $note,
				'tree'            => $tree,
				'current'         => $breadcrumbs['current'],
				'breadcrumbs'     => $breadcrumbs['breadcrumbs'],
				'parent'          => $parent,
				'place'           => $fqpn,
				'content'         => $content,
				'showeventslink'  => null !== $data && !$place->isEmpty() && $action !== 'hierarchy-e',
				'nextaction'      => $nextaction,
			]
		);
	}

	/**
	 * @param $tree
	 * @return array
	 * @throws \Exception
	 */
	private function getList(Tree $tree) {
		$list_places = Place::allPlaces($tree);
		$numfound    = count($list_places);
		$divisor     = $numfound > 20 ? 3 : 2;

		return
			[
				'columns' => array_chunk($list_places, (int)ceil($numfound / $divisor)),
			];
	}

	/**
	 * @param Tree $tree
	 * @param Place $place
	 * @param string $parent[]
	 * @return array|null
	 * @throws \Exception
	 */
	private function getHierarchy($tree, $place, $parent) {
		$child_places = $place->getChildPlaces();
		$numfound     = count($child_places);

		if ($numfound > 0) {
			$divisor = $numfound > 20 ? 3 : 2;

			return
				[
					'tree'      => $tree,
					'col_class' => "w-" . ($divisor === 2 ? "25" : "50"),
					'columns'   => array_chunk($child_places, (int)ceil($numfound / $divisor)),
					'place'     => $place,
					'parent'    => $parent,
				];
		} else {
			return null;
		}
	}

	/**
	 * @param Tree $tree
	 * @param Place $place
	 * @return array
	 * @throws \Exception
	 */
	private function getEvents($tree, $place) {
		$indilist = [];
		$famlist  = [];

		$xrefs = Database::prepare(
			"SELECT DISTINCT pl_gid FROM `##placelinks` WHERE pl_p_id=:id AND pl_file=:gedcom"
		)
			->execute([
				'id'     => $place->getPlaceId(),
				'gedcom' => $tree->getTreeId(),
			]
			)->fetchOneColumn();

		foreach ($xrefs as $xref) {
			$record = GedcomRecord::getInstance($xref, $tree);
			if ($record && $record->canShow()) {
				if ($record instanceof Individual) {
					$indilist[] = $record;
				}
				if ($record instanceof Family) {
					$famlist[] = $record;
				}
			}
		}

		return
			[
				'indilist' => $indilist,
				'famlist'  => $famlist,
			];
	}

	/**
	 * @param Place $place
	 * @return array
	 */
	private function breadcrumbs($place) {
		$breadcrumbs = [];
		if (!$place->isEmpty()) {
			$breadcrumbs[] = $place;
			$parent_place  = $place->getParentPlace();
			while (!$parent_place->isEmpty()) {
				$breadcrumbs[] = $parent_place;
				$parent_place  = $parent_place->getParentPlace();
			}
			$breadcrumbs = array_reverse($breadcrumbs);
			$current     = array_pop($breadcrumbs);
		} else {
			$current = '';
		}

		return ['breadcrumbs' => $breadcrumbs, 'current' => $current];
	}
}

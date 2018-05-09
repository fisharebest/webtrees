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
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the media page.
 */
class MediaController extends AbstractBaseController {
	/**
	 * Show a repository's page.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function show(Request $request): Response {
		/** @var Tree $tree */
		$tree  = $request->attributes->get('tree');
		$xref  = $request->get('xref');
		$media = Media::getInstance($xref, $tree);

		$this->checkMediaAccess($media);

		return $this->viewResponse('media-page', [
			'families'    => $media->linkedFamilies('OBJE'),
			'facts'       => $this->facts($media),
			'individuals' => $media->linkedIndividuals('OBJE'),
			'media'       => $media,
			'meta_robots' => 'index,follow',
			'notes'       => $media->linkedNotes('OBJE'),
			'sources'     => $media->linkedSources('OBJE'),
			'title'       => $media->getFullName(),
		]);
	}

	/**
	 * @param Media $record
	 *
	 * @return array
	 */
	private function facts(Media $record): array {
		$facts = $record->getFacts();

		array_filter($facts, function (Fact $fact) {
			return $fact->getTag() !== 'FILE';
		});

		return $facts;
	}
}

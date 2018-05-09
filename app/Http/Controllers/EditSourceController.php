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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for edit forms and responses.
 */
class EditSourceController extends AbstractBaseController {
	/**
	 * Show a form to create a new source.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function createSource(Request $request): Response {
		return new Response(view('modals/create-source'));
	}

	/**
	 * Process a form to create a new source.
	 *
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function createSourceAction(Request $request): JsonResponse {
		/** @var Tree $tree */
		$tree                = $request->attributes->get('tree');
		$title               = $request->get('source-title', '');
		$abbreviation        = $request->get('source-abbreviation', '');
		$author              = $request->get('source-author', '');
		$publication         = $request->get('source-publication', '');
		$repository          = $request->get('source-repository', '');
		$call_number         = $request->get('source-call-number', '');
		$text                = $request->get('source-text', '');
		$privacy_restriction = $request->get('privacy-restriction', '');
		$edit_restriction    = $request->get('edit-restriction', '');

		// Fix whitespace
		$title        = trim(preg_replace('/\s+/', ' ', $title));
		$abbreviation = trim(preg_replace('/\s+/', ' ', $abbreviation));
		$author       = trim(preg_replace('/\s+/', ' ', $author));
		$publication  = trim(preg_replace('/\s+/', ' ', $publication));
		$repository   = trim(preg_replace('/\s+/', ' ', $repository));
		$call_number  = trim(preg_replace('/\s+/', ' ', $call_number));

		// Convert line endings to GEDDCOM continuations
		$text = str_replace(["\r\n", "\r", "\n"], "\n1 CONT ", $text);

		$gedcom = "0 @XREF@ SOUR\n\n1 TITL " . $title;

		if ($abbreviation !== '') {
			$gedcom .= "\n1 ABBR " . $abbreviation;
		}

		if ($author !== '') {
			$gedcom .= "\n1 AUTH " . $author;
		}

		if ($publication !== '') {
			$gedcom .= "\n1 PUBL " . $publication;
		}

		if ($text !== '') {
			$gedcom .= "\n1 TEXT " . $text;
		}

		if ($repository !== '') {
			$gedcom .= "\n1 REPO @" . $repository . '@';

			if ($call_number !== '') {
				$gedcom .= "\n2 CALN " . $call_number;
			}
		}

		if (in_array($privacy_restriction, ['none', 'privacy', 'confidential'])) {
			$gedcom .= "\n1 RESN " . $privacy_restriction;
		}

		if (in_array($edit_restriction, ['locked'])) {
			$gedcom .= "\n1 RESN " . $edit_restriction;
		}

		$record = $tree->createRecord($gedcom);

		// id and text are for select2 / autocomplete
		// html is for interactive modals
		return new JsonResponse([
			'id' => $record->getXref(),
			'text' => view('selects/source', [
				'source' => $record,
			]),
			'html' => view('modals/record-created', [
				'title' => I18N::translate('The source has been created'),
				'name'  => $record->getFullName(),
				'url'   => $record->url(),
			])
		]);
	}
}

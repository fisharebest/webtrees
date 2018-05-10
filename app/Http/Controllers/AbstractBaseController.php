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

use Fisharebest\Webtrees\Controller\PageController as LegacyBaseController;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Common functions for all controllers
 *
 * The "Legacy" base controller was used to inject Javascript into responses.
 * Once this is updated, we can remove it.
 */
abstract class AbstractBaseController extends LegacyBaseController {
	protected $layout = 'layouts/default';

	/**
	 * @param Family|null $family
	 * @param bool|null   $edit
	 *
	 * @throws NotFoundHttpException
	 * @throws AccessDeniedHttpException
	 */
	protected function checkFamilyAccess(Family $family = null, $edit = false) {
		if ($family === null) {
			throw new NotFoundHttpException(I18N::translate('This family does not exist or you do not have permission to view it.'));
		}

		if (!$family->canShow() || $edit && (!$family->canEdit() || $family->isPendingDeletion())) {
			throw new AccessDeniedHttpException(I18N::translate('This family does not exist or you do not have permission to view it.'));
		}
	}

	/**
	 * @param Individual|null $individual
	 * @param bool|null       $edit
	 *
	 * @throws NotFoundHttpException
	 * @throws AccessDeniedHttpException
	 */
	protected function checkIndividualAccess(Individual $individual = null, $edit = false) {
		if ($individual === null) {
			throw new NotFoundHttpException(I18N::translate('This individual does not exist or you do not have permission to view it.'));
		}

		if (!$individual->canShow() || $edit && (!$individual->canEdit() || $individual->isPendingDeletion())) {
			throw new AccessDeniedHttpException(I18N::translate('This individual does not exist or you do not have permission to view it.'));
		}
	}

	/**
	 * @param Media|null $media
	 * @param bool|null  $edit
	 *
	 * @throws NotFoundHttpException
	 * @throws AccessDeniedHttpException
	 */
	protected function checkMediaAccess(Media $media = null, $edit = false) {
		if ($media === null) {
			throw new NotFoundHttpException(I18N::translate('This media object does not exist or you do not have permission to view it.'));
		}

		if (!$media->canShow() || $edit && (!$media->canEdit() || $media->isPendingDeletion())) {
			throw new AccessDeniedHttpException(I18N::translate('This media object does not exist or you do not have permission to view it.'));
		}
	}

	/**
	 * @param Note|null $note
	 * @param bool|null $edit
	 *
	 * @throws NotFoundHttpException
	 * @throws AccessDeniedHttpException
	 */
	protected function checkNoteAccess(Note $note = null, $edit = false) {
		if ($note === null) {
			throw new NotFoundHttpException(I18N::translate('This note does not exist or you do not have permission to view it.'));
		}

		if (!$note->canShow() || $edit && (!$note->canEdit() || $note->isPendingDeletion())) {
			throw new AccessDeniedHttpException(I18N::translate('This note does not exist or you do not have permission to view it.'));
		}
	}

	/**
	 * @param GedcomRecord|null $record
	 * @param bool|null         $edit
	 *
	 * @throws NotFoundHttpException
	 * @throws AccessDeniedHttpException
	 */
	protected function checkRecordAccess(GedcomRecord $record = null, $edit = false) {
		if ($record === null) {
			throw new NotFoundHttpException(I18N::translate('This record does not exist or you do not have permission to view it.'));
		}

		if (!$record->canShow() || $edit && (!$record->canEdit() || $record->isPendingDeletion())) {
			throw new AccessDeniedHttpException(I18N::translate('This record does not exist or you do not have permission to view it.'));
		}
	}

	/**
	 * @param Repository|null $repository
	 * @param bool|null       $edit
	 *
	 * @throws NotFoundHttpException
	 * @throws AccessDeniedHttpException
	 */
	protected function checkRepositoryAccess(Repository $repository = null, $edit = false) {
		if ($repository === null) {
			throw new NotFoundHttpException(I18N::translate('This repository does not exist or you do not have permission to view it.'));
		}

		if (!$repository->canShow() || $edit && (!$repository->canEdit() || $repository->isPendingDeletion())) {
			throw new AccessDeniedHttpException(I18N::translate('This repository does not exist or you do not have permission to view it.'));
		}
	}

	/**
	 * @param Source|null $source
	 * @param bool|null   $edit
	 *
	 * @throws NotFoundHttpException
	 * @throws AccessDeniedHttpException
	 */
	protected function checkSourceAccess(Source $source = null, $edit = false) {
		if ($source === null) {
			throw new NotFoundHttpException(I18N::translate('This source does not exist or you do not have permission to view it.'));
		}

		if (!$source->canShow() || $edit && (!$source->canEdit() || $source->isPendingDeletion())) {
			throw new AccessDeniedHttpException(I18N::translate('This source does not exist or you do not have permission to view it.'));
		}
	}

	/**
	 * Create a response object from a view.
	 *
	 * @param string  $view_name
	 * @param mixed[] $view_data
	 * @param int     $status
	 *
	 * @return Response
	 */
	protected function viewResponse($view_name, $view_data, $status = Response::HTTP_OK): Response {
		// Make the view's data available to the layout.
		$layout_data = $view_data;
		$layout_data['javascript'] = $this->getJavascript();

		// Render the view
		$layout_data['content'] = view($view_name, $view_data);

		// Insert the view into the layout
		$html = view($this->layout, $layout_data);

		return new Response($html, $status);
	}
}

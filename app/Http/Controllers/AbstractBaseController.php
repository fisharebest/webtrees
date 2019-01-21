<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

use Fisharebest\Webtrees\Exceptions\FamilyAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\FamilyNotFoundException;
use Fisharebest\Webtrees\Exceptions\IndividualAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\IndividualNotFoundException;
use Fisharebest\Webtrees\Exceptions\MediaAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\MediaNotFoundException;
use Fisharebest\Webtrees\Exceptions\NoteAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\NoteNotFoundException;
use Fisharebest\Webtrees\Exceptions\RecordAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\RecordNotFoundException;
use Fisharebest\Webtrees\Exceptions\RepositoryAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\RepositoryNotFoundException;
use Fisharebest\Webtrees\Exceptions\SourceAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\SourceNotFoundException;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Common functions for all controllers
 */
abstract class AbstractBaseController
{
    /** @var string */
    protected $layout = 'layouts/default';

    /**
     * @param Family|null $family
     * @param bool|null   $edit
     *
     * @return void
     * @throws FamilyNotFoundException
     * @throws FamilyAccessDeniedException
     */
    protected function checkFamilyAccess(Family $family = null, $edit = false)
    {
        if ($family === null) {
            throw new FamilyNotFoundException();
        }

        if (!$family->canShow() || $edit && (!$family->canEdit() || $family->isPendingDeletion())) {
            throw new FamilyAccessDeniedException();
        }
    }

    /**
     * @param Individual|null $individual
     * @param bool|null       $edit
     *
     * @return void
     * @throws IndividualNotFoundException
     * @throws IndividualAccessDeniedException
     */
    protected function checkIndividualAccess(Individual $individual = null, $edit = false)
    {
        if ($individual === null) {
            throw new IndividualNotFoundException();
        }

        if (!$individual->canShow() || $edit && (!$individual->canEdit() || $individual->isPendingDeletion())) {
            throw new IndividualAccessDeniedException();
        }
    }

    /**
     * @param Media|null $media
     * @param bool|null  $edit
     *
     * @return void
     * @throws MediaNotFoundException
     * @throws MediaNotFoundException
     */
    protected function checkMediaAccess(Media $media = null, $edit = false)
    {
        if ($media === null) {
            throw new MediaNotFoundException();
        }

        if (!$media->canShow() || $edit && (!$media->canEdit() || $media->isPendingDeletion())) {
            throw new MediaAccessDeniedException();
        }
    }

    /**
     * @param Note|null $note
     * @param bool|null $edit
     *
     * @return void
     * @throws NoteNotFoundException
     * @throws NoteAccessDeniedException
     */
    protected function checkNoteAccess(Note $note = null, $edit = false)
    {
        if ($note === null) {
            throw new NoteNotFoundException();
        }

        if (!$note->canShow() || $edit && (!$note->canEdit() || $note->isPendingDeletion())) {
            throw new NoteAccessDeniedException();
        }
    }

    /**
     * @param GedcomRecord|null $record
     * @param bool|null         $edit
     *
     * @return void
     * @throws RecordNotFoundException
     * @throws RecordAccessDeniedException
     */
    protected function checkRecordAccess(GedcomRecord $record = null, $edit = false)
    {
        if ($record === null) {
            throw new RecordNotFoundException();
        }

        if (!$record->canShow() || $edit && (!$record->canEdit() || $record->isPendingDeletion())) {
            throw new RecordAccessDeniedException();
        }
    }

    /**
     * @param Repository|null $repository
     * @param bool|null       $edit
     *
     * @return void
     * @throws RepositoryNotFoundException
     * @throws RepositoryAccessDeniedException
     */
    protected function checkRepositoryAccess(Repository $repository = null, $edit = false)
    {
        if ($repository === null) {
            throw new RepositoryNotFoundException();
        }

        if (!$repository->canShow() || $edit && (!$repository->canEdit() || $repository->isPendingDeletion())) {
            throw new RepositoryAccessDeniedException();
        }
    }

    /**
     * @param Source|null $source
     * @param bool|null   $edit
     *
     * @return void
     * @throws SourceNotFoundException
     * @throws SourceAccessDeniedException
     */
    protected function checkSourceAccess(Source $source = null, $edit = false)
    {
        if ($source === null) {
            throw new SourceNotFoundException();
        }

        if (!$source->canShow() || $edit && (!$source->canEdit() || $source->isPendingDeletion())) {
            throw new SourceAccessDeniedException();
        }
    }

    /**
     * Check that a module is enabled for a tree.
     *
     * @param Tree   $tree
     * @param string $class_name
     *
     * @throws NotFoundHttpException
     * @return ModuleChartInterface
     */
    protected function checkModuleIsActive(Tree $tree, string $class_name): ModuleChartInterface
    {
        $module = Module::activeCharts($tree)
            ->filter(function (ModuleChartInterface $module) use ($class_name): bool {
                return $module instanceof $class_name;
            })
            ->first();

        if (!$module instanceof $class_name) {
            throw new NotFoundHttpException(I18N::translate('The module “%s” has been disabled.', $module));
        }

        return $module;
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
    protected function viewResponse($view_name, $view_data, $status = Response::HTTP_OK): Response
    {
        // Make the view's data available to the layout.
        $layout_data = $view_data;

        // Render the view
        $layout_data['content'] = view($view_name, $view_data);

        // Insert the view into the layout
        $html = view($this->layout, $layout_data);

        return new Response($html, $status);
    }
}

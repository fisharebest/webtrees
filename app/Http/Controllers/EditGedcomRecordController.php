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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\CensusAssistantModule;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function assert;

/**
 * Controller for edit forms and responses.
 */
class EditGedcomRecordController extends AbstractEditController
{
    /** @var ClipboardService */
    private $clipboard_service;

    /** @var ModuleService */
    private $module_service;

    /**
     * EditGedcomRecordController constructor.
     *
     * @param ClipboardService $clipboard_service
     * @param ModuleService    $module_service
     */
    public function __construct(ClipboardService $clipboard_service, ModuleService $module_service)
    {
        $this->clipboard_service = $clipboard_service;
        $this->module_service    = $module_service;
    }

    /**
     * Copy a fact to the clipboard.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function copyFact(ServerRequestInterface $request): ResponseInterface
    {
        $tree    = $request->getAttribute('tree');
        $xref    = $request->getAttribute('xref');
        $fact_id = $request->getAttribute('fact_id');
        $record  = GedcomRecord::getInstance($xref, $tree);

        Auth::checkRecordAccess($record, true);

        foreach ($record->facts() as $fact) {
            if ($fact->id() === $fact_id) {
                $this->clipboard_service->copyFact($fact);

                FlashMessages::addMessage(I18N::translate('The record has been copied to the clipboard.'));
                break;
            }
        }

        return response();
    }

    /**
     * Delete a fact.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function deleteFact(ServerRequestInterface $request): ResponseInterface
    {
        $tree    = $request->getAttribute('tree');
        $xref    = $request->getAttribute('xref');
        $fact_id = $request->getAttribute('fact_id');
        $record  = GedcomRecord::getInstance($xref, $tree);

        Auth::checkRecordAccess($record, true);

        foreach ($record->facts() as $fact) {
            if ($fact->id() == $fact_id && $fact->canEdit()) {
                $record->deleteFact($fact_id, true);
                break;
            }
        }

        return response();
    }

    /**
     * Delete a record.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function deleteRecord(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = $request->getAttribute('tree');
        $xref   = $request->getAttribute('xref');
        $record = GedcomRecord::getInstance($xref, $tree);

        Auth::checkRecordAccess($record, true);

        if ($record && Auth::isEditor($record->tree()) && $record->canShow() && $record->canEdit()) {
            // Delete links to this record
            foreach ($record->linkingRecords() as $linker) {
                $old_gedcom = $linker->gedcom();
                $new_gedcom = $this->removeLinks($old_gedcom, $record->xref());
                if ($old_gedcom !== $new_gedcom) {
                    // If we have removed a link from a family to an individual, and it has only one member
                    if (preg_match('/^0 @' . Gedcom::REGEX_XREF . '@ FAM/', $new_gedcom) && preg_match_all('/\n1 (HUSB|WIFE|CHIL) @(' . Gedcom::REGEX_XREF . ')@/', $new_gedcom, $match) == 1) {
                        // Delete the family
                        $family = GedcomRecord::getInstance($xref, $tree);
                        /* I18N: %s is the name of a family group, e.g. “Husband name + Wife name” */
                        FlashMessages::addMessage(I18N::translate('The family “%s” has been deleted because it only has one member.', $family->fullName()));
                        $family->deleteRecord();
                        // Delete any remaining link to this family
                        if ($match) {
                            $relict     = GedcomRecord::getInstance($match[2][0], $tree);
                            $new_gedcom = $relict->gedcom();
                            $new_gedcom = $this->removeLinks($new_gedcom, $linker->xref());
                            $relict->updateRecord($new_gedcom, false);
                            /* I18N: %s are names of records, such as sources, repositories or individuals */
                            FlashMessages::addMessage(I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', $relict->fullName(), $family->fullName()));
                        }
                    } else {
                        // Remove links from $linker to $record
                        /* I18N: %s are names of records, such as sources, repositories or individuals */
                        FlashMessages::addMessage(I18N::translate('The link from “%1$s” to “%2$s” has been deleted.', $linker->fullName(), $record->fullName()));
                        $linker->updateRecord($new_gedcom, false);
                    }
                }
            }
            // Delete the record itself
            $record->deleteRecord();
        }

        return response();
    }

    /**
     * Paste a fact from the clipboard into a record.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function pasteFact(ServerRequestInterface $request): ResponseInterface
    {
        $tree    = $request->getAttribute('tree');
        $xref    = $request->getParsedBody()['xref'];
        $fact_id = $request->getParsedBody()['fact_id'];
        $record  = GedcomRecord::getInstance($xref, $tree);

        Auth::checkRecordAccess($record, true);

        $this->clipboard_service->pasteFact($fact_id, $record);

        return redirect($record->url());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function editRawFact(ServerRequestInterface $request): ResponseInterface
    {
        $tree    = $request->getAttribute('tree');
        $xref    = $request->getQueryParams()['xref'];
        $fact_id = $request->getQueryParams()['fact_id'];
        $record  = GedcomRecord::getInstance($xref, $tree);

        Auth::checkRecordAccess($record, true);

        $title = I18N::translate('Edit the raw GEDCOM') . ' - ' . $record->fullName();

        foreach ($record->facts() as $fact) {
            if (!$fact->isPendingDeletion() && $fact->id() === $fact_id) {
                return $this->viewResponse('edit/raw-gedcom-fact', [
                    'fact'    => $fact,
                    'title'   => $title,
                ]);
            }
        }

        return redirect($record->url());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function editRawFactAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree    = $request->getAttribute('tree');
        $xref    = $request->getParsedBody()['xref'];
        $fact_id = $request->getParsedBody()['fact_id'];
        $gedcom  = $request->getParsedBody()['gedcom'];

        $record = GedcomRecord::getInstance($xref, $tree);

        // Cleanup the client’s bad editing?
        $gedcom = preg_replace('/[\r\n]+/', "\n", $gedcom); // Empty lines
        $gedcom = trim($gedcom); // Leading/trailing spaces

        Auth::checkRecordAccess($record, true);

        foreach ($record->facts() as $fact) {
            if (!$fact->isPendingDeletion() && $fact->id() === $fact_id && $fact->canEdit()) {
                $record->updateFact($fact_id, $gedcom, false);
                break;
            }
        }

        return redirect($record->url());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function editRawRecord(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = $request->getAttribute('tree');
        $xref   = $request->getQueryParams()['xref'];
        $record = GedcomRecord::getInstance($xref, $tree);

        Auth::checkRecordAccess($record, true);

        $title = I18N::translate('Edit the raw GEDCOM') . ' - ' . $record->fullName();

        return $this->viewResponse('edit/raw-gedcom-record', [
            'record'  => $record,
            'title'   => $title,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function editRawRecordAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree     = $request->getAttribute('tree');
        $xref     = $request->getParsedBody()['xref'];
        $facts    = $request->getParsedBody()['fact'] ?? [];
        $fact_ids = $request->getParsedBody()['fact_id'] ?? [];
        $record   = GedcomRecord::getInstance($xref, $tree);

        Auth::checkRecordAccess($record, true);

        $gedcom = '0 @' . $record->xref() . '@ ' . $record::RECORD_TYPE;

        // Retain any private facts
        foreach ($record->facts([], false, Auth::PRIV_HIDE) as $fact) {
            if (!in_array($fact->id(), $fact_ids, true) && !$fact->isPendingDeletion()) {
                $gedcom .= "\n" . $fact->gedcom();
            }
        }
        // Append the updated facts
        foreach ($facts as $fact) {
            $gedcom .= "\n" . $fact;
        }

        // Empty lines and MSDOS line endings.
        $gedcom = preg_replace('/[\r\n]+/', "\n", $gedcom);
        $gedcom = trim($gedcom);

        $record->updateRecord($gedcom, false);

        return redirect($record->url());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function addFact(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree, new InvalidArgumentException());

        $xref = $request->getQueryParams()['xref'];
        $fact = $request->getQueryParams()['fact'];

        $record = GedcomRecord::getInstance($xref, $tree);
        Auth::checkRecordAccess($record, true);

        $title = $record->fullName() . ' - ' . GedcomTag::getLabel($fact, $record);

        return $this->viewResponse('edit/add-fact', [
            'fact'   => $fact,
            'record' => $record,
            'title'  => $title,
            'tree'   => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function editFact(ServerRequestInterface $request): ResponseInterface
    {
        $tree    = $request->getAttribute('tree');
        $xref    = $request->getQueryParams()['xref'];
        $fact_id = $request->getQueryParams()['fact_id'];

        $record = GedcomRecord::getInstance($xref, $tree);
        Auth::checkRecordAccess($record, true);

        // Find the fact to edit
        $edit_fact = null;
        foreach ($record->facts() as $fact) {
            if ($fact->id() === $fact_id && $fact->canEdit()) {
                $edit_fact = $fact;
                break;
            }
        }
        if ($edit_fact === null) {
            throw new NotFoundHttpException();
        }

        $can_edit_raw = Auth::isAdmin() || $tree->getPreference('SHOW_GEDCOM_RECORD');

        $title = $record->fullName() . ' - ' . GedcomTag::getLabel($edit_fact->getTag());

        return $this->viewResponse('edit/edit-fact', [
            'can_edit_raw' => $can_edit_raw,
            'edit_fact'    => $edit_fact,
            'record'       => $record,
            'title'        => $title,
            'tree'         => $tree,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function updateFact(ServerRequestInterface $request): ResponseInterface
    {
        $tree    = $request->getAttribute('tree');
        $xref    = $request->getParsedBody()['xref'];
        $fact_id = $request->getParsedBody()['fact_id'] ?? '';

        $record = GedcomRecord::getInstance($xref, $tree);
        Auth::checkRecordAccess($record, true);

        $keep_chan = (bool) ($request->getParsedBody()['keep_chan'] ?? false);

        $this->glevels = $request->getParsedBody()['glevels'];
        $this->tag     = $request->getParsedBody()['tag'];
        $this->text    = $request->getParsedBody()['text'];
        $this->islink  = $request->getParsedBody()['islink'];

        // If the fact has a DATE or PLAC, then delete any value of Y
        if ($this->text[0] === 'Y') {
            foreach ($this->tag as $n => $value) {
                if ($this->glevels[$n] == 2 && ($value === 'DATE' || $value === 'PLAC') && $this->text[$n] !== '') {
                    $this->text[0] = '';
                    break;
                }
            }
        }

        $newged = '';

        $NAME = $request->getParsedBody()['NAME'] ?? '';

        if ($NAME !== '') {
            $newged     .= "\n1 NAME " . $NAME;
            $name_facts = [
                'TYPE',
                'NPFX',
                'GIVN',
                'NICK',
                'SPFX',
                'SURN',
                'NSFX',
            ];
            foreach ($name_facts as $name_fact) {
                $NAME_FACT = $request->getParsedBody()[$name_fact] ?? '';
                if ($NAME_FACT !== '') {
                    $newged .= "\n2 " . $name_fact . ' ' . $NAME_FACT;
                }
            }
        }

        $newged = $this->handleUpdates($newged);

        // Add new names after existing names
        if ($NAME !== '') {
            preg_match_all('/[_0-9A-Z]+/', $tree->getPreference('ADVANCED_NAME_FACTS'), $match);
            $name_facts = array_unique(array_merge(['_MARNM'], $match[0]));
            foreach ($name_facts as $name_fact) {
                $NAME_FACT = $request->getParsedBody()[$name_fact] ?? '';
                // Ignore advanced facts that duplicate standard facts.
                if ($NAME_FACT !== '' && !in_array($name_fact, ['TYPE', 'NPFX', 'GIVN', 'NICK', 'SPFX', 'SURN', 'NSFX'], true)) {
                    $newged .= "\n2 " . $name_fact . ' ' . $NAME_FACT;
                }
            }
        }

        $newged = trim($newged); // Remove leading newline

        $census_assistant = $this->module_service->findByInterface(CensusAssistantModule::class)->first();
        if ($census_assistant instanceof CensusAssistantModule && $record instanceof Individual) {
            $newged = $census_assistant->updateCensusAssistant($request, $record, $fact_id, $newged, $keep_chan);
        }

        $record->updateFact($fact_id, $newged, !$keep_chan);

        // For the GEDFact_assistant module
        $pid_array = $request->getParsedBody()['pid_array'] ?? '';
        if ($pid_array !== '') {
            foreach (explode(',', $pid_array) as $pid) {
                if ($pid !== $xref) {
                    $indi = Individual::getInstance($pid, $tree);
                    if ($indi && $indi->canEdit()) {
                        $indi->updateFact($fact_id, $newged, !$keep_chan);
                    }
                }
            }
        }

        return redirect($record->url());
    }

    /**
     * Remove all links from $gedrec to $xref, and any sub-tags.
     *
     * @param string $gedrec
     * @param string $xref
     *
     * @return string
     */
    private function removeLinks($gedrec, $xref): string
    {
        $gedrec = preg_replace('/\n1 ' . Gedcom::REGEX_TAG . ' @' . $xref . '@(\n[2-9].*)*/', '', $gedrec);
        $gedrec = preg_replace('/\n2 ' . Gedcom::REGEX_TAG . ' @' . $xref . '@(\n[3-9].*)*/', '', $gedrec);
        $gedrec = preg_replace('/\n3 ' . Gedcom::REGEX_TAG . ' @' . $xref . '@(\n[4-9].*)*/', '', $gedrec);
        $gedrec = preg_replace('/\n4 ' . Gedcom::REGEX_TAG . ' @' . $xref . '@(\n[5-9].*)*/', '', $gedrec);
        $gedrec = preg_replace('/\n5 ' . Gedcom::REGEX_TAG . ' @' . $xref . '@(\n[6-9].*)*/', '', $gedrec);

        return $gedrec;
    }
}

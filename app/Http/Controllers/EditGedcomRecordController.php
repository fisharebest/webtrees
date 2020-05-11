<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\CensusAssistantModule;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function assert;
use function is_string;

/**
 * Controller for edit forms and responses.
 */
class EditGedcomRecordController extends AbstractEditController
{
    /** @var ModuleService */
    private $module_service;

    /**
     * EditGedcomRecordController constructor.
     *
     * @param ModuleService $module_service
     */
    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function updateFact(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $params = (array) $request->getParsedBody();

        $fact_id = $params['fact_id'] ?? '';

        $record = Factory::gedcomRecord()->make($xref, $tree);
        $record = Auth::checkRecordAccess($record, true);

        $keep_chan = (bool) ($params['keep_chan'] ?? false);

        $this->glevels = $params['glevels'];
        $this->tag     = $params['tag'];
        $this->text    = $params['text'];
        $this->islink  = $params['islink'];

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

        $NAME = $params['NAME'] ?? '';

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
                $NAME_FACT = $params[$name_fact] ?? '';
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
                $NAME_FACT = $params[$name_fact] ?? '';
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
        $pid_array = $params['pid_array'] ?? '';
        if ($pid_array !== '') {
            foreach (explode(',', $pid_array) as $pid) {
                if ($pid !== $xref) {
                    $indi = Factory::individual()->make($pid, $tree);
                    if ($indi && $indi->canEdit()) {
                        $indi->updateFact($fact_id, $newged, !$keep_chan);
                    }
                }
            }
        }

        return redirect($record->url());
    }
}

<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\CensusAssistantModule;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

readonly class EditFactAction implements RequestHandlerInterface
{
    public function __construct(
        private GedcomEditService $gedcom_edit_service,
        private ModuleService $module_service
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree    = Validator::attributes($request)->tree();
        $xref    = Validator::attributes($request)->isXref()->string('xref');
        $fact_id = Validator::attributes($request)->string('fact_id');

        $record = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record = Auth::checkRecordAccess($record, true);

        $keep_chan = Validator::parsedBody($request)->boolean('keep_chan', false);
        $levels    = Validator::parsedBody($request)->array('levels');
        $tags      = Validator::parsedBody($request)->array('tags');
        $values    = Validator::parsedBody($request)->array('values');
        $gedcom    = $this->gedcom_edit_service->editLinesToGedcom($record::RECORD_TYPE, $levels, $tags, $values, false);

        $census_assistant = $this->module_service->findByInterface(CensusAssistantModule::class)->first();

        if ($census_assistant instanceof CensusAssistantModule && $record instanceof Individual) {
            $ca_individuals = Validator::parsedBody($request)->array('ca_individuals')['xref'] ?? [];

            if ($ca_individuals !== []) {
                $gedcom = $census_assistant->updateCensusAssistant($request, $record, $fact_id, $gedcom, $keep_chan);

                // Don't copy the AGE/OCCU fields to other individuals
                $gedcom2 = preg_replace('/\n2 (?:AGE|OCCU) .*/', '', $gedcom);

                foreach ($ca_individuals as $pid) {
                    if ($pid !== $xref) {
                        $individual = Registry::individualFactory()->make($pid, $tree);
                        if ($individual instanceof Individual && $individual->canEdit()) {
                            $individual->createFact($gedcom2, !$keep_chan);
                        }
                    }
                }
            }
        }

        if ($fact_id === 'new') {
            $record->createFact($gedcom, !$keep_chan);
        } else {
            $record->updateFact($fact_id, $gedcom, !$keep_chan);
        }

        $url = Validator::parsedBody($request)->isLocalUrl()->string('url', $record->url());

        return redirect($url);
    }
}

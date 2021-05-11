<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function explode;
use function is_string;
use function redirect;

/**
 * Save an updated GEDCOM fact.
 */
class EditFactAction implements RequestHandlerInterface
{
    /** @var GedcomEditService */
    private $gedcom_edit_service;

    /** @var ModuleService */
    private $module_service;

    /**
     * EditFactAction constructor.
     *
     * @param GedcomEditService $gedcom_edit_service
     * @param ModuleService     $module_service
     */
    public function __construct(GedcomEditService $gedcom_edit_service, ModuleService $module_service)
    {
        $this->gedcom_edit_service = $gedcom_edit_service;
        $this->module_service      = $module_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $fact_id = $request->getAttribute('fact_id') ?? '';
        assert(is_string($fact_id));

        $record = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record = Auth::checkRecordAccess($record, true);

        $params    = (array) $request->getParsedBody();
        $keep_chan = (bool) ($params['keep_chan'] ?? false);
        $levels    = $params['levels'];
        $tags      = $params['tags'];
        $values    = $params['values'];

        $gedcom = $this->gedcom_edit_service->editLinesToGedcom($record::RECORD_TYPE, $levels, $tags, $values);

        $census_assistant = $this->module_service->findByInterface(CensusAssistantModule::class)->first();

        if ($census_assistant instanceof CensusAssistantModule && $record instanceof Individual) {
            $gedcom = $census_assistant->updateCensusAssistant($request, $record, $fact_id, $gedcom, $keep_chan);
            $pid_array = $params['pid_array'] ?? '';
            if ($pid_array !== '') {
                foreach (explode(',', $pid_array) as $pid) {
                    if ($pid !== $xref) {
                        $individual = Registry::individualFactory()->make($pid, $tree);
                        if ($individual instanceof Individual && $individual->canEdit()) {
                            $individual->updateFact('', $gedcom, !$keep_chan);
                        }
                    }
                }
            }
        }

        if ($fact_id === 'new') {
            // Add a new fact
            $record->updateFact('', $gedcom, !$keep_chan);
        } else {
            // Update (only the first copy of) an existing fact
            foreach ($record->facts([], false, null, true) as $fact) {
                if ($fact->id() === $fact_id && $fact->canEdit()) {
                    $record->updateFact($fact_id, $gedcom, !$keep_chan);
                    break;
                }
            }
        }

        return redirect($params['url'] ?? $record->url());
    }
}

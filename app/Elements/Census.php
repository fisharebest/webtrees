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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\Census\Census as Censuses;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\CensusAssistantModule;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Census
 */
class Census extends AbstractElement
{
    protected const SUBTAGS = [
        'DATE' => '0:1',
        'AGE'  => '0:1',
        'PLAC' => '0:1',
        'ADDR' => '0:1',
        'NOTE' => '0:M',
        'OBJE' => '0:M',
        'SOUR' => '0:M',
        'RESN' => '0:1',
    ];

    /**
     * An edit control for this data.
     *
     * @param string $id
     * @param string $name
     * @param string $value
     * @param Tree   $tree
     *
     * @return string
     */
    public function edit(string $id, string $name, string $value, Tree $tree): string
    {
        $html = $this->editHidden($id, $name, $value);

        $html .= view('modules/GEDFact_assistant/select-census', [
            'census_places' => Censuses::censusPlaces(I18N::languageTag()),
        ]);

        $xref = app(ServerRequestInterface::class)->getAttribute('xref', '');

        $census_assistant = app(ModuleService::class)->findByInterface(CensusAssistantModule::class)->first();
        $record           = Registry::individualFactory()->make($xref, $tree);

        if ($census_assistant instanceof CensusAssistantModule && $record instanceof Individual) {
            $html .= $census_assistant->createCensusAssistant($record);
        }

        return $html;
    }
}

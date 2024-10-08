<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Illuminate\Support\Collection;

use function array_map;

/**
 * Class ExtraInformationModule
 * A sidebar to show non-genealogy information about an individual
 */
class IndividualMetadataModule extends AbstractModule implements ModuleSidebarInterface
{
    use ModuleSidebarTrait;

    // A list of facts that are handled by this module.
    protected const HANDLED_FACTS = [
        'AFN',
        'CHAN',
        'IDNO',
        'REFN',
        'RESN',
        'RFN',
        'RIN',
        'SSN',
        '_UID',
        '_FSFTID',
        '_WEBTAG',
    ];

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/sidebar */
        return I18N::translate('Extra information');
    }

    public function description(): string
    {
        /* I18N: Description of the “Extra information” module */
        return I18N::translate('A sidebar showing non-genealogy information about an individual.');
    }

    /**
     * The default position for this sidebar.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultSidebarOrder(): int
    {
        return 1;
    }

    /**
     * @param Individual $individual
     *
     * @return bool
     */
    public function hasSidebarContent(Individual $individual): bool
    {
        return $individual->facts(static::HANDLED_FACTS)->isNotEmpty();
    }

    /**
     * Load this sidebar synchronously.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function getSidebarContent(Individual $individual): string
    {
        $html = $individual->facts(static::HANDLED_FACTS)
            ->map(static fn (Fact $fact): string => view('fact', ['fact' => $fact, 'record' => $individual]))
            ->implode('<hr>');

        return strip_tags($html, ['a', 'div', 'span', 'i', 'hr', 'br']);
    }

    /**
     * This module handles the following facts - so don't show them on the "Facts and events" tab.
     *
     * @return Collection<int,string>
     */
    public function supportedFacts(): Collection
    {
        return new Collection(array_map(static fn (string $tag): string => 'INDI:' . $tag, static::HANDLED_FACTS));
    }
}

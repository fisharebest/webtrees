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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Illuminate\Support\Collection;

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

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
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

    /** {@inheritdoc} */
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
        ob_start();

        foreach ($individual->facts(static::HANDLED_FACTS) as $fact) {
            FunctionsPrintFacts::printFact($fact, $individual);
        }

        $html = ob_get_clean();

        return strip_tags($html, '<a><div><span>');
    }

    /**
     * This module handles the following facts - so don't show them on the "Facts and events" tab.
     *
     * @return Collection
     */
    public function supportedFacts(): Collection
    {
        return new Collection(static::HANDLED_FACTS);
    }
}

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

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Functions\FunctionsPrintFacts;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;

/**
 * Class ExtraInformationModule
 * A sidebar to show non-genealogy information about an individual
 */
class ExtraInformationModule extends AbstractModule implements ModuleSidebarInterface
{
    use ModuleSidebarTrait;

    /**
     * How should this module be labelled on tabs, menus, etc.?
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
        return true;
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
        $indifacts = [];
        // The individual’s own facts
        foreach ($individual->facts() as $fact) {
            if (self::showFact($fact)) {
                $indifacts[] = $fact;
            }
        }

        ob_start();
        if (!$indifacts) {
            echo I18N::translate('There are no facts for this individual.');
        } else {
            foreach ($indifacts as $fact) {
                FunctionsPrintFacts::printFact($fact, $individual);
            }
        }

        return strip_tags(ob_get_clean(), '<a><div><span>');
    }

    /**
     * Does this module display a particular fact
     *
     * @param Fact $fact
     *
     * @return bool
     */
    public static function showFact(Fact $fact)
    {
        switch ($fact->getTag()) {
            case 'AFN':
            case 'CHAN':
            case 'IDNO':
            case 'REFN':
            case 'RFN':
            case 'RIN':
            case 'SSN':
            case '_UID':
                return true;
            default:
                return false;
        }
    }
}

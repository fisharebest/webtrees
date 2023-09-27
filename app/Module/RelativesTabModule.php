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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Illuminate\Support\Collection;

/**
 * Class RelativesTabModule
 */
class RelativesTabModule extends AbstractModule implements ModuleTabInterface
{
    use ModuleTabTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Families');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Families” module */
        return I18N::translate('A tab showing the close relatives of an individual.');
    }

    /**
     * The default position for this tab.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultTabOrder(): int
    {
        return 2;
    }

    /**
     * Generate the HTML content of this tab.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function getTabContent(Individual $individual): string
    {
        $tree = $individual->tree();
        if ($tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS') === '1') {
            $fam_access_level = Auth::PRIV_HIDE;
        } else {
            $fam_access_level = Auth::accessLevel($tree);
        }

        return view('modules/relatives/tab', [
            'fam_access_level'     => $fam_access_level,
            'can_edit'             => $individual->canEdit(),
            'individual'           => $individual,
            'parent_families'      => $individual->childFamilies(),
            'spouse_families'      => $individual->spouseFamilies(),
            'step_child_families'  => $individual->spouseStepFamilies(),
            'step_parent_families' => $individual->childStepFamilies(),
        ]);
    }

    /**
     * Is this tab empty? If so, we don't always need to display it.
     *
     * @param Individual $individual
     *
     * @return bool
     */
    public function hasTabContent(Individual $individual): bool
    {
        return $individual->canEdit() || $individual->facts(['FAMC', 'FAMS'], false, null, true)->isNotEmpty();
    }

    /**
     * A greyed out tab has no actual content, but may perhaps have
     * options to create content.
     *
     * @param Individual $individual
     *
     * @return bool
     */
    public function isGrayedOut(Individual $individual): bool
    {
        return false;
    }

    /**
     * Can this tab load asynchronously?
     *
     * @return bool
     */
    public function canLoadAjax(): bool
    {
        return false;
    }

    /**
     * This module handles the following facts - so don't show them on the "Facts and events" tab.
     *
     * @return Collection<int,string>
     */
    public function supportedFacts(): Collection
    {
        return new Collection(['INDI:FAMC', 'INDI:FAMS', 'FAM:HUSB', 'FAM:WIFE', 'FAM:CHIL']);
    }
}

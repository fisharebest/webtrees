<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;

/**
 * Class RelativesTabModule
 */
class RelativesTabModule extends AbstractModule implements ModuleTabInterface
{
    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Families');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function getDescription(): string
    {
        /* I18N: Description of the “Families” module */
        return I18N::translate('A tab showing the close relatives of an individual.');
    }

    /**
     * The user can re-arrange the tab order, but until they do, this
     * is the order in which tabs are shown.
     *
     * @return int
     */
    public function defaultTabOrder(): int
    {
        return 20;
    }

    /** {@inheritdoc} */
    public function getTabContent(Individual $individual): string
    {
        $tree = $individual->tree();
        if ($tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS')) {
            $fam_access_level = Auth::PRIV_HIDE;
        } else {
            $fam_access_level = Auth::accessLevel($tree);
        }

        return view('modules/relatives/tab', [
            'fam_access_level'     => $fam_access_level,
            'can_edit'             => $individual->canEdit(),
            'individual'           => $individual,
            'parent_families'      => $individual->getChildFamilies(),
            'spouse_families'      => $individual->getSpouseFamilies(),
            'step_child_familiess' => $individual->getSpouseStepFamilies(),
            'step_parent_families' => $individual->getChildStepFamilies(),
        ]);
    }

    /** {@inheritdoc} */
    public function hasTabContent(Individual $individual): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isGrayedOut(Individual $individual): bool
    {
        return false;
    }

    /** {@inheritdoc} */
    public function canLoadAjax(): bool
    {
        return false;
    }
}

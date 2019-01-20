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
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;

/**
 * Class SourcesTabModule
 */
class SourcesTabModule extends AbstractModule implements ModuleInterface, ModuleTabInterface
{
    use ModuleTabTrait;

    /** @var Fact[] All facts belonging to this source. */
    private $facts;

    /** {@inheritdoc} */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Sources');
    }

    /** {@inheritdoc} */
    public function description(): string
    {
        /* I18N: Description of the “Sources” module */
        return I18N::translate('A tab showing the sources linked to an individual.');
    }

    /**
     * The default position for this tab.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultTabOrder(): int {
        return 40;
    }

    /** {@inheritdoc} */
    public function hasTabContent(Individual $individual): bool
    {
        return $individual->canEdit() || $this->getFactsWithSources($individual);
    }

    /** {@inheritdoc} */
    public function isGrayedOut(Individual $individual): bool
    {
        return !$this->getFactsWithSources($individual);
    }

    /** {@inheritdoc} */
    public function getTabContent(Individual $individual): string
    {
        return view('modules/sources_tab/tab', [
            'can_edit'   => $individual->canEdit(),
            'individual' => $individual,
            'facts'      => $this->getFactsWithSources($individual),
        ]);
    }

    /**
     * Get all the facts for an individual which contain sources.
     *
     * @param Individual $individual
     *
     * @return Fact[]
     */
    private function getFactsWithSources(Individual $individual): array
    {
        if ($this->facts === null) {
            $facts = $individual->facts();
            foreach ($individual->getSpouseFamilies() as $family) {
                if ($family->canShow()) {
                    foreach ($family->facts() as $fact) {
                        $facts[] = $fact;
                    }
                }
            }
            $this->facts = [];
            foreach ($facts as $fact) {
                if (preg_match('/(?:^1|\n\d) SOUR/', $fact->gedcom())) {
                    $this->facts[] = $fact;
                }
            }
            Functions::sortFacts($this->facts);
        }

        return $this->facts;
    }

    /** {@inheritdoc} */
    public function canLoadAjax(): bool
    {
        return false;
    }
}

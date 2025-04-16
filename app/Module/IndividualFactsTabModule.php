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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Elements\AdoptedByWhichParent;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\XrefFamily;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\IndividualFactsService;
use Fisharebest\Webtrees\Services\ModuleService;
use Illuminate\Support\Collection;

use function view;

class IndividualFactsTabModule extends AbstractModule implements ModuleTabInterface
{
    use ModuleTabTrait;

    private ClipboardService $clipboard_service;

    private IndividualFactsService $individual_facts_service;

    private ModuleService $module_service;

    /**
     * @param ClipboardService       $clipboard_service
     * @param IndividualFactsService $individual_facts_service
     * @param ModuleService          $module_service
     */
    public function __construct(
        ClipboardService $clipboard_service,
        IndividualFactsService $individual_facts_service,
        ModuleService $module_service
    ) {
        $this->clipboard_service        = $clipboard_service;
        $this->individual_facts_service = $individual_facts_service;
        $this->module_service           = $module_service;
    }

    public function title(): string
    {
        /* I18N: Name of a module/tab on the individual page. */
        return I18N::translate('Facts and events');
    }

    public function description(): string
    {
        /* I18N: Description of the “Facts and events” module */
        return I18N::translate('A tab showing the facts and events of an individual.');
    }

    /**
     * The default position for this tab.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultTabOrder(): int
    {
        return 1;
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
     * Generate the HTML content of this tab.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function getTabContent(Individual $individual): string
    {
        // Which facts and events are handled by other modules?
        $sidebar_facts = $this->module_service
            ->findByComponent(ModuleSidebarInterface::class, $individual->tree(), Auth::user())
            ->map(fn (ModuleSidebarInterface $sidebar): Collection => $sidebar->supportedFacts())
            ->flatten();

        $tab_facts = $this->module_service
            ->findByComponent(ModuleTabInterface::class, $individual->tree(), Auth::user())
            ->map(fn (ModuleTabInterface $tab): Collection => $tab->supportedFacts())
            ->flatten();

        // Don't show family meta-data tags
        $exclude_facts  = new Collection(['FAM:CHAN', 'FAM:_UID', 'FAM:UID', 'FAM:SUBM']);
        // Don't show tags that are shown in tabs or sidebars
        $exclude_facts = $exclude_facts->merge($sidebar_facts)->merge($tab_facts);

        $individual_facts = $this->individual_facts_service->individualFacts($individual, $exclude_facts);
        $family_facts     = $this->individual_facts_service->familyFacts($individual, $exclude_facts);
        $relative_facts   = $this->individual_facts_service->relativeFacts($individual);
        $associate_facts  = $this->individual_facts_service->associateFacts($individual);
        $historic_facts   = $this->individual_facts_service->historicFacts($individual);

        $individual_facts = $individual_facts
            ->merge($family_facts)
            ->merge($relative_facts)
            ->merge($associate_facts)
            ->merge($historic_facts);

        $individual_facts = Fact::sortFacts($individual_facts);

        // Facts of relatives take the form 1 EVEN / 2 TYPE Event of Individual
        // Ensure custom tags from there are recognised
        Registry::elementFactory()->registerTags([
            'INDI:EVEN:CEME'      => new CustomElement('Cemetery'),
            'INDI:EVEN:_GODP'     => new CustomElement('Godparent'),
            'INDI:EVEN:FAMC'      => new XrefFamily(I18N::translate('Adoptive parents')),
            'INDI:EVEN:FAMC:ADOP' => new AdoptedByWhichParent(I18N::translate('Adoption')),
        ]);

        return view('modules/personal_facts/tab', [
            'can_edit'            => $individual->canEdit(),
            'clipboard_facts'     => $this->clipboard_service->pastableFacts($individual),
            'has_associate_facts' => $associate_facts->isNotEmpty(),
            'has_historic_facts'  => $historic_facts->isNotEmpty(),
            'has_relative_facts'  => $relative_facts->isNotEmpty(),
            'individual'          => $individual,
            'facts'               => $individual_facts,
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
        return true;
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
        // We don't actually displaye these facts, but they are displayed
        // outside the tabs/sidebar systems. This just forces them to be excluded here.
        return new Collection(['INDI:NAME', 'INDI:SEX', 'INDI:OBJE']);
    }
}

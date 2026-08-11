<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\FactSortService;
use Illuminate\Support\Collection;

use function preg_match;

class MediaTabModule extends AbstractModule implements ModuleTabInterface
{
    use ModuleTabTrait;

    public function __construct(
        private ClipboardService $clipboard_service,
        private FactSortService $fact_sort_service,
    ) {
    }

    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Media');
    }

    public function description(): string
    {
        /* I18N: Description of the “Media” module */
        return I18N::translate('A tab showing the media objects linked to an individual.');
    }

    /**
     * The default position for this tab.  It can be changed in the control panel.
     */
    public function defaultTabOrder(): int
    {
        return 5;
    }

    /**
     * Is this tab empty? If so, we don't always need to display it.
     */
    public function hasTabContent(Individual $individual): bool
    {
        return $individual->canEdit() || $this->getFactsWithMedia($individual)->isNotEmpty();
    }

    /**
     * A greyed out tab has no actual content, but may perhaps have
     * options to create content.
     */
    public function isGrayedOut(Individual $individual): bool
    {
        return $this->getFactsWithMedia($individual)->isEmpty();
    }

    /**
     * Generate the HTML content of this tab.
     */
    public function getTabContent(Individual $individual): string
    {
        return view('modules/media/tab', [
            'can_edit'   => $individual->canEdit(),
            'clipboard_facts' => $this->clipboard_service->pastableFactsOfType($individual, $this->supportedFacts()),
            'individual' => $individual,
            'facts'      => $this->getFactsWithMedia($individual),
        ]);
    }

    /**
     * Get all the facts for an individual which contain media objects.
     *
     *
     * @return Collection<int,Fact>
     */
    protected function getFactsWithMedia(Individual $individual): Collection
    {
        return Registry::cache()->array()->remember(self::class . ':' . __METHOD__, function () use ($individual): Collection {
            $facts = $individual->facts();

            foreach ($individual->spouseFamilies() as $family) {
                if ($family->canShow()) {
                    $facts = $facts->concat($family->facts());
                }
            }

            $facts = $facts->filter(static fn (Fact $fact): bool => preg_match('/(?:^1|\n\d) OBJE @' . Gedcom::REGEX_XREF . '@/', $fact->gedcom()) === 1);

            return $this->fact_sort_service->sort($facts);
        });
    }

    /**
     * Can this tab load asynchronously?
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
        return new Collection(['INDI:OBJE', 'FAM:OBJE']);
    }
}

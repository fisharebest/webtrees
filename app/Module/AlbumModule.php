<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;

/**
 * Class AlbumModule
 */
class AlbumModule extends AbstractModule implements ModuleTabInterface
{
    use ModuleTabTrait;

    /** @var Media[] List of media objects. */
    private $media_list;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Album');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Album” module */
        return I18N::translate('An alternative to the “media” tab, and an enhanced image viewer.');
    }

    /**
     * The default position for this tab.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultTabOrder(): int
    {
        return 6;
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
        return $individual->canEdit() || $this->getMedia($individual);
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
        return !$this->getMedia($individual);
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
        return view('modules/lightbox/tab', [
            'media_list' => $this->getMedia($individual),
        ]);
    }

    /**
     * Get all facts containing media links for this person and their spouse-family records
     *
     * @param Individual $individual
     *
     * @return Media[]
     */
    private function getMedia(Individual $individual): array
    {
        if ($this->media_list === null) {
            // Use facts from this individual and all their spouses
            $facts = $individual->facts();
            foreach ($individual->spouseFamilies() as $family) {
                foreach ($family->facts() as $fact) {
                    $facts->push($fact);
                }
            }
            // Use all media from each fact
            $this->media_list = [];
            foreach ($facts as $fact) {
                // Don't show pending edits, as the user just sees duplicates
                if (!$fact->isPendingDeletion()) {
                    preg_match_all('/(?:^1|\n\d) OBJE @(' . Gedcom::REGEX_XREF . ')@/', $fact->gedcom(), $matches);
                    foreach ($matches[1] as $match) {
                        $media = Factory::media()->make($match, $individual->tree());
                        if ($media && $media->canShow()) {
                            $this->media_list[] = $media;
                        }
                    }
                }
            }
            // If a media object is linked twice, only show it once
            $this->media_list = array_unique($this->media_list);
            // Sort these using _WT_OBJE_SORT
            $wt_obje_sort = [];
            foreach ($individual->facts(['_WT_OBJE_SORT']) as $fact) {
                $wt_obje_sort[] = trim($fact->value(), '@');
            }
            usort($this->media_list, static function (Media $x, Media $y) use ($wt_obje_sort): int {
                return array_search($x->xref(), $wt_obje_sort, true) - array_search($y->xref(), $wt_obje_sort, true);
            });
        }

        return $this->media_list;
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
}

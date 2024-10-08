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

use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Registry;
use Illuminate\Support\Collection;

/**
 * Class AlbumModule
 */
class AlbumModule extends MediaTabModule
{
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
     * Get the linked media objects.
     *
     * @param Individual $individual
     *
     * @return Collection<int,Media>
     */
    private function getMedia(Individual $individual): Collection
    {
        $media = new Collection();

        foreach ($this->getFactsWithMedia($individual) as $fact) {
            preg_match_all('/(?:^1|\n\d) OBJE @(' . Gedcom::REGEX_XREF . ')@/', $fact->gedcom(), $matches);

            foreach ($matches[1] as $xref) {
                if (!$media->has($xref)) {
                    $media->put($xref, Registry::mediaFactory()->make($xref, $individual->tree()));
                }
            }
        }

        return $media->filter()->filter(Media::accessFilter());
    }
}

<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\Contracts\SlugFactoryInterface;
use Fisharebest\Webtrees\GedcomRecord;
use Transliterator;

use function extension_loaded;
use function in_array;
use function preg_replace;
use function strip_tags;
use function trim;

/**
 * Make a slug to be used in the URL of a GedcomRecord.
 */
class SlugFactory implements SlugFactoryInterface
{
    private ?Transliterator $transliterator = null;

    public function __construct()
    {
        if (extension_loaded('intl')) {
            $ids = Transliterator::listIDs();

            if (in_array('Any-Latin', $ids, true) && in_array('Latin-ASCII', $ids, true)) {
                $this->transliterator = Transliterator::create('Any-Latin;Latin-ASCII');
            }
        }
    }

    /**
     * @param GedcomRecord $record
     *
     * @return string
     */
    public function make(GedcomRecord $record): string
    {
        $slug = strip_tags($record->fullName());

        if ($this->transliterator instanceof Transliterator) {
            $slug = $this->transliterator->transliterate($slug);

            if ($slug === false) {
                return '';
            }
        }

        $slug = preg_replace('/[^A-Za-z0-9]+/', '-', $slug);

        return trim($slug, '-');
    }
}

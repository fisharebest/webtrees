<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\I18N;

use function strtolower;
use function uasort;

/**
 * SOURCE_MEDIA_TYPE := {Size=1:15}
 * [ audio | book | card | electronic | fiche | film | magazine |
 * manuscript | map | newspaper | photo | tombstone | video ]
 * A code, selected from one of the media classifications choices above, that indicates the type of
 * material in which the referenced source is stored.
 */
class SourceMediaType extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 15;

    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        return strtolower(parent::canonical($value));
    }

    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        // *** indicates custom values
        $values = [
            ''            => '',
            'audio'       => /* I18N: Type of media object */ I18N::translate('Audio'),
            'book'        => /* I18N: Type of media object */ I18N::translate('Book'),
            'card'        => /* I18N: Type of media object */ I18N::translate('Card'),
            'certificate' => /* I18N: Type of media object */ I18N::translate('Certificate'),
            'coat'        => /* I18N: Type of media object */ I18N::translate('Coat of arms'),
            'document'    => /* I18N: Type of media object */ I18N::translate('Document'),
            'electronic'  => /* I18N: Type of media object */ I18N::translate('Electronic'),
            'fiche'       => /* I18N: Type of media object */ I18N::translate('Microfiche'),
            'film'        => /* I18N: Type of media object */ I18N::translate('Microfilm'),
            'magazine'    => /* I18N: Type of media object */ I18N::translate('Magazine'),
            'manuscript'  => /* I18N: Type of media object */ I18N::translate('Manuscript'),
            'map'         => /* I18N: Type of media object */ I18N::translate('Map'),
            'newspaper'   => /* I18N: Type of media object */ I18N::translate('Newspaper'),
            'other'       => /* I18N: Type of media object */ I18N::translate('Other'),
            'photo'       => /* I18N: Type of media object */ I18N::translate('Photo'),
            'painting'    => /* I18N: Type of media object */ I18N::translate('Painting'),
            'tombstone'   => /* I18N: Type of media object */ I18N::translate('Tombstone'),
            'video'       => /* I18N: Type of media object */ I18N::translate('Video'),
        ];

        uasort($values, I18N::comparator());

        return $values;
    }
}

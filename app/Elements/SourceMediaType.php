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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\I18N;

use function strtoupper;
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
    public const VALUE_AUDIO       = 'AUDIO';
    public const VALUE_BOOK        = 'BOOK';
    public const VALUE_CARD        = 'CARD';
    public const VALUE_CERTIFICATE = 'CERTIFICATE';
    public const VALUE_COAT        = 'COAT';
    public const VALUE_DOCUMENT    = 'DOCUMENT';
    public const VALUE_ELECTRONIC  = 'ELECTRONIC';
    public const VALUE_FICHE       = 'FICHE';
    public const VALUE_FILM        = 'FILM';
    public const VALUE_MAGAZINE    = 'MAGAZINE';
    public const VALUE_MANUSCRIPT  = 'MANUSCRIPT';
    public const VALUE_MAP         = 'MAP';
    public const VALUE_NEWSPAPER   = 'NEWSPAPER';
    public const VALUE_OTHER       = 'OTHER';
    public const VALUE_PAINTING    = 'PAINTING';
    public const VALUE_PHOTO       = 'PHOTO';
    public const VALUE_TOMBSTONE   = 'TOMBSTONE';
    public const VALUE_VIDEO       = 'VIDEO';

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
        return strtoupper(parent::canonical($value));
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
            ''                      => '',
            self::VALUE_AUDIO       => /* I18N: Type of media object */ I18N::translate('Audio'),
            self::VALUE_BOOK        => /* I18N: Type of media object */ I18N::translate('Book'),
            self::VALUE_CARD        => /* I18N: Type of media object */ I18N::translate('Card'),
            self::VALUE_CERTIFICATE => /* I18N: Type of media object */ I18N::translate('Certificate'),
            self::VALUE_COAT       => /* I18N: Type of media object */ I18N::translate('Coat of arms'),
            self::VALUE_DOCUMENT   => /* I18N: Type of media object */ I18N::translate('Document'),
            self::VALUE_ELECTRONIC => /* I18N: Type of media object */ I18N::translate('Electronic'),
            self::VALUE_FICHE      => /* I18N: Type of media object */ I18N::translate('Microfiche'),
            self::VALUE_FILM       => /* I18N: Type of media object */ I18N::translate('Microfilm'),
            self::VALUE_MAGAZINE   => /* I18N: Type of media object */ I18N::translate('Magazine'),
            self::VALUE_MANUSCRIPT => /* I18N: Type of media object */ I18N::translate('Manuscript'),
            self::VALUE_MAP        => /* I18N: Type of media object */ I18N::translate('Map'),
            self::VALUE_NEWSPAPER  => /* I18N: Type of media object */ I18N::translate('Newspaper'),
            self::VALUE_OTHER      => /* I18N: Type of media object */ I18N::translate('Other'),
            self::VALUE_PAINTING   => /* I18N: Type of media object */ I18N::translate('Painting'),
            self::VALUE_PHOTO      => /* I18N: Type of media object */ I18N::translate('Photo'),
            self::VALUE_TOMBSTONE  => /* I18N: Type of media object */ I18N::translate('Tombstone'),
            self::VALUE_VIDEO      => /* I18N: Type of media object */ I18N::translate('Video'),
        ];

        uasort($values, I18N::comparator());

        return $values;
    }
}

<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
    public const TYPE_AUDIO       = 'audio';
    public const TYPE_BOOK        = 'book';
    public const TYPE_CARD        = 'card';
    public const TYPE_CERTIFICATE = 'certificate';
    public const TYPE_COAT        = 'coat';
    public const TYPE_DOCUMENT    = 'document';
    public const TYPE_ELECTRONIC  = 'electronic';
    public const TYPE_FICHE       = 'fiche';
    public const TYPE_FILM        = 'film';
    public const TYPE_MAGAZINE    = 'magazine';
    public const TYPE_MANUSCRIPT  = 'manuscript';
    public const TYPE_MAP         = 'map';
    public const TYPE_NEWSPAPER   = 'newspaper';
    public const TYPE_OTHER       = 'other';
    public const TYPE_PAINTING    = 'painting';
    public const TYPE_PHOTO       = 'photo';
    public const TYPE_TOMBSTONE   = 'tombstone';
    public const TYPE_VIDEO       = 'video';

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
            self::TYPE_AUDIO       => /* I18N: Type of media object */ I18N::translate('Audio'),
            self::TYPE_BOOK        => /* I18N: Type of media object */ I18N::translate('Book'),
            self::TYPE_CARD        => /* I18N: Type of media object */ I18N::translate('Card'),
            self::TYPE_CERTIFICATE => /* I18N: Type of media object */ I18N::translate('Certificate'),
            self::TYPE_COAT        => /* I18N: Type of media object */ I18N::translate('Coat of arms'),
            self::TYPE_DOCUMENT    => /* I18N: Type of media object */ I18N::translate('Document'),
            self::TYPE_ELECTRONIC  => /* I18N: Type of media object */ I18N::translate('Electronic'),
            self::TYPE_FICHE       => /* I18N: Type of media object */ I18N::translate('Microfiche'),
            self::TYPE_FILM        => /* I18N: Type of media object */ I18N::translate('Microfilm'),
            self::TYPE_MAGAZINE    => /* I18N: Type of media object */ I18N::translate('Magazine'),
            self::TYPE_MANUSCRIPT  => /* I18N: Type of media object */ I18N::translate('Manuscript'),
            self::TYPE_MAP         => /* I18N: Type of media object */ I18N::translate('Map'),
            self::TYPE_NEWSPAPER   => /* I18N: Type of media object */ I18N::translate('Newspaper'),
            self::TYPE_OTHER       => /* I18N: Type of media object */ I18N::translate('Other'),
            self::TYPE_PAINTING    => /* I18N: Type of media object */ I18N::translate('Painting'),
            self::TYPE_PHOTO       => /* I18N: Type of media object */ I18N::translate('Photo'),
            self::TYPE_TOMBSTONE   => /* I18N: Type of media object */ I18N::translate('Tombstone'),
            self::TYPE_VIDEO       => /* I18N: Type of media object */ I18N::translate('Video'),
        ];

        uasort($values, I18N::comparator());

        return $values;
    }
}

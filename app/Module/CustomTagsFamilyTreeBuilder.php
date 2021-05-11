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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\DateValue;
use Fisharebest\Webtrees\Elements\NamePersonal;
use Fisharebest\Webtrees\Elements\PlaceName;
use Fisharebest\Webtrees\I18N;

/**
 * Class CustomTagsFTB
 */
class CustomTagsFamilyTreeBuilder extends AbstractModule implements ModuleConfigInterface, ModuleCustomTagsInterface
{
    use ModuleConfigTrait;
    use ModuleCustomTagsTrait;

    /**
     * Should this module be enabled when it is first installed?
     *
     * @return bool
     */
    public function isEnabledByDefault(): bool
    {
        return false;
    }

    /**
     * @return array<string,ElementInterface>
     */
    public function customTags(): array
    {
        return [
            '*:_UPD'              => new CustomElement(I18N::translate('Last change')), // e.g. "1 _UPD 14 APR 2012 00:14:10 GMT-5"
            'INDI:NAME:_AKA'      => new NamePersonal(I18N::translate('Also known as'), []),
            'OBJE:_ALBUM'         => new CustomElement(I18N::translate('Album')), // XREF to an album
            'OBJE:_DATE'          => new DateValue(I18N::translate('Date')),
            'OBJE:_FILESIZE'      => new CustomElement(I18N::translate('File size')),
            'OBJE:_PHOTO_RIN'     => new CustomElement(I18N::translate('Photo')),
            'OBJE:_PLACE'         => new PlaceName(I18N::translate('Place')),
            '_ALBUM:_PHOTO'       => new CustomElement(I18N::translate('Photo')),
            '_ALBUM:_PHOTO:_PRIN' => new CustomElement(I18N::translate('Highlighted image')),
        ];
    }

    /**
     * The application for which we are supporting custom tags.
     *
     * @return string
     */
    public function customTagApplication(): string
    {
        return 'Family Tree Builder™';
    }
}

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

namespace Fisharebest\Webtrees\CustomTags;

use Fisharebest\Webtrees\Contracts\CustomTagInterface;
use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\Elements\AutomatedRecordId;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\PafUid;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by MyHeritage
 *
 * @see https://www.myheritage.com
 */
class MyHeritage implements CustomTagInterface
{
    /**
     * The name of the application.
     *
     * @return string
     */
    public function name(): string
    {
        return 'MyHeritage';
    }

    /**
     * Tags created by this application.
     *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'FAM:*:_UID'                  => new PafUid(I18N::translate('Unique identifier')),
            'FAM:*:RIN'                   => new AutomatedRecordId(I18N::translate('Record ID number')),
            'HEAD:DATE:_TIMEZONE'         => new CustomElement(I18N::translate('Time zone')),
            'HEAD:SOUR:_RTLSAVE'          => new CustomElement(I18N::translate('Text direction')), // ?
            'HEAD:_RINS'                  => new CustomElement(I18N::translate('Record ID number')), // ?
            'HEAD:_UID'                   => new PafUid(I18N::translate('Unique identifier')),
            'HEAD:_PROJECT_GUID'          => new PafUid(I18N::translate('Unique identifier')),
            'HEAD:_EXPORTED_FROM_SITE_ID' => new CustomElement(I18N::translate('Site identification code')),
            'HEAD:_DESCRIPTION_AWARE'     => new CustomElement(I18N::translate('Description')), // ?
            'INDI:PERSONALPHOTO'          => new CustomElement(I18N::translate('Photograph')),
            'INDI:*:_UID'                 => new PafUid(I18N::translate('Unique identifier')),
            'INDI:*:RIN'                  => new AutomatedRecordId(I18N::translate('Record ID number')),
            '*:_UPD'                      => new CustomElement(I18N::translate('Updated at')),
        ];
    }
}

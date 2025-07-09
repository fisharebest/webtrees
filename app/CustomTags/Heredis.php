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

namespace Fisharebest\Webtrees\CustomTags;

use Fisharebest\Webtrees\Contracts\CustomTagInterface;
use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\Elements\CertaintyAssessment;
use Fisharebest\Webtrees\Elements\Creation;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\TimeValue;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by Heredis
 *
 * @see https://www.heredis.com
 */
class Heredis implements CustomTagInterface
{
    /**
     * The name of the application.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Heredis';
    }

    /**
     * Tags created by this application.
     *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'FAM:_CREA'                   => new Creation(I18N::translate('Created at')),
            'FAM:*:DATE:_TIME'            => new TimeValue(I18N::translate('Time')),
            'INDI:_CREA'                  => new Creation(I18N::translate('Created at')),
            'INDI:SIGN'                   => new CustomElement(I18N::translate('Signature')),
            'INDI:*:DATE:_TIME'           => new TimeValue(I18N::translate('Time')),
            'SOUR:QUAY'                   => new CertaintyAssessment(I18N::translate('Quality of data')),
            'SOUR:TYPE'                   => new CustomElement(I18N::translate('Type')),
            /* Reported on the forum - but what do they mean?
            'INDI:_FIL'                   => new CustomElement(I18N::translate('???')),
            'INDI:*:_FNA'                 => new CustomElement(I18N::translate('???')),
            'INDI:????:????:_SUBMAP'      => new EmptyElement(I18N::translate('Coordinates'), ['INDI' => '1:1', 'LONG' => '1:1']),
            'INDI:????:????:_SUBMAP:LATI' => new PlaceLatitude(I18N::translate('Latitude')),
            'INDI:????:????:_SUBMAP:LONG' => new PlaceLongtitude(I18N::translate('Longitude')),
            */
        ];
    }
}

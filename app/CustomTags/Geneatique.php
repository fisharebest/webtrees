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
use Fisharebest\Webtrees\Elements\DescriptiveTitle;
use Fisharebest\Webtrees\Elements\MultimediaFormat;
use Fisharebest\Webtrees\Elements\NamePersonal;
use Fisharebest\Webtrees\Elements\TimeValue;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by Généatique
 *
 * @see https://www.geneatique.com/
 */
class Geneatique implements CustomTagInterface
{
    /**
     * The name of the application.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Généatique';
    }

    /**
     * Tags created by this application.
     *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'INDI:DEAT:DATE:TIME' => new TimeValue(I18N::translate('Time of death')),
            'OBJE:FORM'           => new MultimediaFormat(I18N::translate('Format')),
            'OBJE:TITL'           => new DescriptiveTitle(I18N::translate('Title')),
            'INDI:NAME:_AKA'      => new NamePersonal(I18N::translate('Also known as'), []),
            'INDI:NAME:_MARNM'    => new NamePersonal(I18N::translate('Also known as'), []),

            /*
            Pour déclarer les témoins dans les actes de naissance

            Balise GEDCOM non valide. INDI:BIRT:ASSO
            INDI:BIRT:ASSO:TYPE
            INDI:BIRT:ASSO:RELA
            INDI:DEAT:PLAC:QUAY
            INDI:BIRT:OBJE:QUAY
            INDI:BIRT:SOUR:TEXT

            Dans les mariages

            FAM:MARR:ASSO
            FAM:MARR:ASSO:TYPE
            FAM:MARR:ASSO:RELA
            FAM:MARR:WWW:QUAY
            OBJE:WWW
            OBJE:SOUR:TEXTHTTPS
            OBJE:NOTE:WWW
            SOUR:QUAY
            SOUR:TYPE
            */
        ];
    }
}

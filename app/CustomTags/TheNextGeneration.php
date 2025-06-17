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
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by TheNextGeneration
 */
class TheNextGeneration implements CustomTagInterface
{
    /**
     * The name of the application.
     *
     * @return string
     */
    public function name(): string
    {
        return 'TheNextGeneration';
    }

    /**
     * Tags created by this application.
     *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'FAM:CHIL:_FREL'  => new CustomElement(I18N::translate('Relationship to father')),
            'FAM:CHIL:_MREL'  => new CustomElement(I18N::translate('Relationship to mother')),
            'INDI:OBJE:_PRIM' => new CustomElement(I18N::translate('Highlighted image')),
            'INDI:_LIVING'    => new CustomElement(I18N::translate('Living')),
            'INDI:_PRIVATE'   => new CustomElement(I18N::translate('Private')),
        ];
    }
}

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

namespace Fisharebest\Webtrees\Elements;

/**
 * ATTRIBUTE_DESCRIPTOR := {Size=1:90}
 * Text describing a particular characteristic or attribute assigned to an
 * individual. This attribute value is assigned to the FACT tag. The
 * classification of this specific attribute or fact is specified by the value
 * of the subordinate TYPE tag selected from the EVENT_DETAIL structure. For
 * example if you were classifying the skills a person had obtained;
 * 1 FACT Woodworking
 * 2 TYPE Skills
 */
class AttributeDescriptor extends AbstractElement
{
    protected const SUBTAGS = [
        'TYPE' => '0:1',
        'DATE' => '0:1',
        'PLAC' => '0:1',
        'NOTE' => '0:M',
        'OBJE' => '0:M',
        'SOUR' => '0:M',
        'RESN' => '0:1',
    ];
}
